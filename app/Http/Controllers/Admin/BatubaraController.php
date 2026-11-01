<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\BatubaraImport;
use App\Models\IdInstansi;
use App\Models\Kabupaten;
use App\Models\KelasKalori;
use App\Models\NeracaBatubara;
use App\Models\Provinsi;
use App\Models\StatDikBb;
use App\Support\JalankanImport;
use App\Support\NolkanAngka;
use Illuminate\Http\Request;

class BatubaraController extends Controller
{
    /** Kolom angka NOT NULL di database: isian kosong dari form dijadikan 0 (lihat NolkanAngka). */
    private const ANGKA_WAJIB = ['tereka', 'tertunjuk', 'terukur', 'terkira', 'terbukti'];

    public function index(Request $request)
    {
        $data = NeracaBatubara::with(['provinsi', 'kabupaten', 'kelasKalori', 'statDikBb'])
            ->when($request->search, fn ($q) => $q->where('nama_objek', 'like', "%{$request->search}%"))
            ->paginate(15);

        return view('admin.batubara.index', compact('data'));
    }

    public function create()
    {
        $provinsis = Provinsi::orderBy('nama_provinsi')->get();
        $kelasKaloris = KelasKalori::get();
        $statDikBbs = StatDikBb::get();
        $idInstansis = IdInstansi::get();

        return view('admin.batubara.create', compact('provinsis', 'kelasKaloris', 'statDikBbs', 'idInstansis'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'idbb' => 'required|integer|unique:neraca_batubaras,idbb',
            'nama_objek' => 'required|string',
            'tahun_data' => 'required|integer',
            'tahun_neraca' => 'required|integer',
            'provinsi_id' => 'required|exists:provinsis,id',
            'kabupaten_id' => 'required|exists:kabupatens,id',
            'kelas_kalori_id' => 'required|exists:kelas_kaloris,id',
            'stat_dik_bb_id' => 'required|exists:stat_dik_bbs,id',
            'id_instansi_id' => 'nullable|exists:id_instansis,id',
            'tereka' => 'nullable|numeric',
            'tertunjuk' => 'nullable|numeric',
            'terukur' => 'nullable|numeric',
            'terkira' => 'nullable|numeric',
            'terbukti' => 'nullable|numeric',
            'remark' => 'nullable|string',
        ]);

        $validated = NolkanAngka::terapkan($validated, self::ANGKA_WAJIB);

        $validated['total_sd'] = ($validated['tereka'] ?? 0) + ($validated['tertunjuk'] ?? 0) + ($validated['terukur'] ?? 0);
        $validated['total_cad'] = ($validated['terkira'] ?? 0) + ($validated['terbukti'] ?? 0);

        NeracaBatubara::create($validated);

        return redirect()->route('admin.batubara.index')->with('success', 'Data berhasil ditambahkan.');
    }

    public function edit(NeracaBatubara $batubara)
    {
        $provinsis = Provinsi::orderBy('nama_provinsi')->get();
        $kabupatens = Kabupaten::where('provinsi_id', $batubara->provinsi_id)->get();
        $kelasKaloris = KelasKalori::get();
        $statDikBbs = StatDikBb::get();
        $idInstansis = IdInstansi::get();

        return view('admin.batubara.edit', compact('batubara', 'provinsis', 'kabupatens', 'kelasKaloris', 'statDikBbs', 'idInstansis'));
    }

    public function update(Request $request, NeracaBatubara $batubara)
    {
        $validated = $request->validate([
            'idbb' => 'required|integer|unique:neraca_batubaras,idbb,'.$batubara->id,
            'nama_objek' => 'required|string',
            'tahun_data' => 'required|integer',
            'tahun_neraca' => 'required|integer',
            'provinsi_id' => 'required|exists:provinsis,id',
            'kabupaten_id' => 'required|exists:kabupatens,id',
            'kelas_kalori_id' => 'required|exists:kelas_kaloris,id',
            'stat_dik_bb_id' => 'required|exists:stat_dik_bbs,id',
            'id_instansi_id' => 'nullable|exists:id_instansis,id',
            'tereka' => 'nullable|numeric',
            'tertunjuk' => 'nullable|numeric',
            'terukur' => 'nullable|numeric',
            'terkira' => 'nullable|numeric',
            'terbukti' => 'nullable|numeric',
            'remark' => 'nullable|string',
        ]);

        $validated = NolkanAngka::terapkan($validated, self::ANGKA_WAJIB);

        $validated['total_sd'] = ($validated['tereka'] ?? 0) + ($validated['tertunjuk'] ?? 0) + ($validated['terukur'] ?? 0);
        $validated['total_cad'] = ($validated['terkira'] ?? 0) + ($validated['terbukti'] ?? 0);

        $batubara->update($validated);

        return redirect()->route('admin.batubara.index')->with('success', 'Data berhasil diubah.');
    }

    public function destroy(NeracaBatubara $batubara)
    {
        $batubara->delete();

        return redirect()->route('admin.batubara.index')->with('success', 'Data berhasil dihapus.');
    }

    public function importForm()
    {
        return view('admin.batubara.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        $hasil = JalankanImport::jalankan(new BatubaraImport, $request->file('file'));

        if ($hasil['galat']) {
            return back()->withErrors(['file' => $hasil['galat']]);
        }

        return redirect()->route('admin.batubara.index')->with('success', $hasil['ringkasan']);
    }
}
