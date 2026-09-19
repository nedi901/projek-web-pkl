<?php

namespace App\Http\Controllers\SuperUser;

use App\Http\Controllers\Controller;
use App\Models\NeracaMineralBukanLogam;
use App\Models\Provinsi;
use App\Models\Kabupaten;
use App\Models\KomoditasBukanLogam;
use App\Models\StatDikBb;
use App\Models\IdInstansi;
use App\Imports\MineralBukanLogamImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class MineralBukanLogamController extends Controller
{
    public function index(Request $request)
    {
        $data = NeracaMineralBukanLogam::with(['provinsi', 'kabupaten', 'komoditasBukanLogam.kelompokKomoditasBukanLogam', 'statDikBb'])
            ->when($request->search, fn($q) => $q->where('nama_objek', 'like', "%{$request->search}%"))
            ->paginate(15);

        return view('superuser.mineral-bukan-logam.index', compact('data'));
    }

    public function create()
    {
        $provinsis = Provinsi::orderBy('nama_provinsi')->get();
        $komoditasBukanLogams = KomoditasBukanLogam::with('kelompokKomoditasBukanLogam')->get();
        $statDikBbs = StatDikBb::get();
        $idInstansis = IdInstansi::get();

        return view('superuser.mineral-bukan-logam.create', compact('provinsis', 'komoditasBukanLogams', 'statDikBbs', 'idInstansis'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'idbl' => 'required|integer|unique:neraca_mineral_bukan_logams,idbl',
            'nama_objek' => 'required|string',
            'tahun_data' => 'required|integer',
            'tahun_neraca' => 'required|integer',
            'provinsi_id' => 'required|exists:provinsis,id',
            'kabupaten_id' => 'required|exists:kabupatens,id',
            'komoditas_bukan_logam_id' => 'required|exists:komoditas_bukan_logams,id',
            'stat_dik_bb_id' => 'required|exists:stat_dik_bbs,id',
            'id_instansi_id' => 'nullable|exists:id_instansis,id',
            'hipotetik' => 'nullable|numeric',
            'tereka' => 'nullable|numeric',
            'tertunjuk' => 'nullable|numeric',
            'terukur' => 'nullable|numeric',
            'terkira' => 'nullable|numeric',
            'terbukti' => 'nullable|numeric',
            'bujur' => 'nullable|numeric',
            'lintang' => 'nullable|numeric',
            'remark' => 'nullable|string',
        ]);

        $validated['total_sd'] = ($validated['tereka'] ?? 0) + ($validated['tertunjuk'] ?? 0) + ($validated['terukur'] ?? 0);
        $validated['total_cad'] = ($validated['terkira'] ?? 0) + ($validated['terbukti'] ?? 0);

        NeracaMineralBukanLogam::create($validated);

        return redirect()->route('superuser.mineral-bukan-logam.index')->with('success', 'Data berhasil ditambahkan.');
    }

    public function edit(NeracaMineralBukanLogam $mineralBukanLogam)
    {
        $provinsis = Provinsi::orderBy('nama_provinsi')->get();
        $kabupatens = Kabupaten::where('provinsi_id', $mineralBukanLogam->provinsi_id)->get();
        $komoditasBukanLogams = KomoditasBukanLogam::with('kelompokKomoditasBukanLogam')->get();
        $statDikBbs = StatDikBb::get();
        $idInstansis = IdInstansi::get();

        return view('superuser.mineral-bukan-logam.edit', compact('mineralBukanLogam', 'provinsis', 'kabupatens', 'komoditasBukanLogams', 'statDikBbs', 'idInstansis'));
    }

    public function update(Request $request, NeracaMineralBukanLogam $mineralBukanLogam)
    {
        $validated = $request->validate([
            'idbl' => 'required|integer|unique:neraca_mineral_bukan_logams,idbl,' . $mineralBukanLogam->id,
            'nama_objek' => 'required|string',
            'tahun_data' => 'required|integer',
            'tahun_neraca' => 'required|integer',
            'provinsi_id' => 'required|exists:provinsis,id',
            'kabupaten_id' => 'required|exists:kabupatens,id',
            'komoditas_bukan_logam_id' => 'required|exists:komoditas_bukan_logams,id',
            'stat_dik_bb_id' => 'required|exists:stat_dik_bbs,id',
            'id_instansi_id' => 'nullable|exists:id_instansis,id',
            'hipotetik' => 'nullable|numeric',
            'tereka' => 'nullable|numeric',
            'tertunjuk' => 'nullable|numeric',
            'terukur' => 'nullable|numeric',
            'terkira' => 'nullable|numeric',
            'terbukti' => 'nullable|numeric',
            'bujur' => 'nullable|numeric',
            'lintang' => 'nullable|numeric',
            'remark' => 'nullable|string',
        ]);

        $validated['total_sd'] = ($validated['tereka'] ?? 0) + ($validated['tertunjuk'] ?? 0) + ($validated['terukur'] ?? 0);
        $validated['total_cad'] = ($validated['terkira'] ?? 0) + ($validated['terbukti'] ?? 0);

        $mineralBukanLogam->update($validated);

        return redirect()->route('superuser.mineral-bukan-logam.index')->with('success', 'Data berhasil diubah.');
    }

    public function destroy(NeracaMineralBukanLogam $mineralBukanLogam)
    {
        $mineralBukanLogam->delete();

        return redirect()->route('superuser.mineral-bukan-logam.index')->with('success', 'Data berhasil dihapus.');
    }

    /**
     * AJAX endpoint dipakai oleh <select id="provinsi_id"> di create/edit
     * buat ngisi dropdown kabupaten sesuai provinsi yang dipilih.
     */
    public function kabupaten($provinsiId)
    {
        return Kabupaten::where('provinsi_id', $provinsiId)
            ->orderBy('nama_kabupaten')
            ->get(['id', 'nama_kabupaten']);
    }

    public function importForm()
    {
        return view('superuser.mineral-bukan-logam.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        Excel::import(new MineralBukanLogamImport, $request->file('file'));

        return redirect()->route('superuser.mineral-bukan-logam.index')->with('success', 'Import selesai.');
    }
}
