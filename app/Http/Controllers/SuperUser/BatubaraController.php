<?php

namespace App\Http\Controllers\SuperUser;

use App\Http\Controllers\Controller;
use App\Models\NeracaBatubara;
use App\Models\Provinsi;
use App\Models\Kabupaten;
use App\Models\KelasKalori;
use App\Models\StatDikBb;
use App\Models\IdInstansi;
use Illuminate\Http\Request;
use App\Imports\BatubaraImport;
use Maatwebsite\Excel\Facades\Excel;

class BatubaraController extends Controller
{
    public function index(Request $request)
    {
        $data = NeracaBatubara::with(['provinsi', 'kabupaten', 'kelasKalori', 'statDikBb'])
            ->when($request->search, fn($q) => $q->where('nama_objek', 'like', "%{$request->search}%"))
            ->paginate(15);

        return view('superuser.batubara.index', compact('data'));
    }

    public function create()
    {
        $provinsis = Provinsi::orderBy('nama_provinsi')->get();
        $kelasKaloris = KelasKalori::get();
        $statDikBbs = StatDikBb::get();
        $idInstansis = IdInstansi::get();

        return view('superuser.batubara.create', compact('provinsis', 'kelasKaloris', 'statDikBbs', 'idInstansis'));
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

        $validated['total_sd'] = ($validated['tereka'] ?? 0) + ($validated['tertunjuk'] ?? 0) + ($validated['terukur'] ?? 0);
        $validated['total_cad'] = ($validated['terkira'] ?? 0) + ($validated['terbukti'] ?? 0);

        NeracaBatubara::create($validated);

        return redirect()->route('superuser.batubara.index')->with('success', 'Data berhasil ditambahkan.');
    }

    public function edit(NeracaBatubara $batubara)
    {
        $provinsis = Provinsi::orderBy('nama_provinsi')->get();
        $kabupatens = Kabupaten::where('provinsi_id', $batubara->provinsi_id)->get();
        $kelasKaloris = KelasKalori::get();
        $statDikBbs = StatDikBb::get();
        $idInstansis = IdInstansi::get();

        return view('superuser.batubara.edit', compact('batubara', 'provinsis', 'kabupatens', 'kelasKaloris', 'statDikBbs', 'idInstansis'));
    }

    public function update(Request $request, NeracaBatubara $batubara)
    {
        $validated = $request->validate([
            'idbb' => 'required|integer|unique:neraca_batubaras,idbb,' . $batubara->id,
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

        $validated['total_sd'] = ($validated['tereka'] ?? 0) + ($validated['tertunjuk'] ?? 0) + ($validated['terukur'] ?? 0);
        $validated['total_cad'] = ($validated['terkira'] ?? 0) + ($validated['terbukti'] ?? 0);

        $batubara->update($validated);

        return redirect()->route('superuser.batubara.index')->with('success', 'Data berhasil diubah.');
    }

    public function destroy(NeracaBatubara $batubara)
    {
        $batubara->delete();

        return redirect()->route('superuser.batubara.index')->with('success', 'Data berhasil dihapus.');
    }
    public function importForm()
{
    return view('superuser.batubara.import');
}

public function import(Request $request)
{
    $request->validate([
        'file' => 'required|mimes:xlsx,xls,csv',
    ]);

    Excel::import(new BatubaraImport, $request->file('file'));

    return redirect()->route('superuser.batubara.index')->with('success', 'Data berhasil diimport.');
}
}