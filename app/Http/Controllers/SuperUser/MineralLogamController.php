<?php

namespace App\Http\Controllers\SuperUser;

use App\Http\Controllers\Controller;
use App\Models\NeracaMineralLogam;
use App\Models\Provinsi;
use App\Models\Kabupaten;
use App\Models\KomoditasLogam;
use App\Models\StatDikBb;
use App\Models\IdInstansi;
use Illuminate\Http\Request;

class MineralLogamController extends Controller
{
    public function index(Request $request)
    {
        $data = NeracaMineralLogam::with(['provinsi', 'kabupaten', 'komoditasLogam.kelompokKomoditasLogam', 'statDikBb'])
            ->when($request->search, fn($q) => $q->where('nama_objek', 'like', "%{$request->search}%"))
            ->paginate(15);

        return view('superuser.mineral-logam.index', compact('data'));
    }

    public function create()
    {
        $provinsis = Provinsi::orderBy('nama_provinsi')->get();
        $komoditasLogams = KomoditasLogam::with('kelompokKomoditasLogam')->get();
        $statDikBbs = StatDikBb::get();
        $idInstansis = IdInstansi::get();

        return view('superuser.mineral-logam.create', compact('provinsis', 'komoditasLogams', 'statDikBbs', 'idInstansis'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'idml' => 'required|integer|unique:neraca_mineral_logams,idml',
            'nama_objek' => 'required|string',
            'tahun_data' => 'required|integer',
            'tahun_neraca' => 'required|integer',
            'provinsi_id' => 'required|exists:provinsis,id',
            'kabupaten_id' => 'required|exists:kabupatens,id',
            'komoditas_logam_id' => 'required|exists:komoditas_logams,id',
            'stat_dik_bb_id' => 'required|exists:stat_dik_bbs,id',
            'id_instansi_id' => 'nullable|exists:id_instansis,id',
            'hipotetik_bijih' => 'nullable|numeric',
            'hipotetik_logam' => 'nullable|numeric',
            'tereka_bijih' => 'nullable|numeric',
            'tereka_logam' => 'nullable|numeric',
            'tertunjuk_bijih' => 'nullable|numeric',
            'tertunjuk_logam' => 'nullable|numeric',
            'terukur_bijih' => 'nullable|numeric',
            'terukur_logam' => 'nullable|numeric',
            'terkira_bijih' => 'nullable|numeric',
            'terkira_logam' => 'nullable|numeric',
            'terbukti_bijih' => 'nullable|numeric',
            'terbukti_logam' => 'nullable|numeric',
            'remark' => 'nullable|string',
        ]);

        $validated['total_sd_bijih'] = ($validated['tereka_bijih'] ?? 0) + ($validated['tertunjuk_bijih'] ?? 0) + ($validated['terukur_bijih'] ?? 0);
        $validated['total_sd_logam'] = ($validated['tereka_logam'] ?? 0) + ($validated['tertunjuk_logam'] ?? 0) + ($validated['terukur_logam'] ?? 0);
        $validated['total_cad_bijih'] = ($validated['terkira_bijih'] ?? 0) + ($validated['terbukti_bijih'] ?? 0);
        $validated['total_cad_logam'] = ($validated['terkira_logam'] ?? 0) + ($validated['terbukti_logam'] ?? 0);

        NeracaMineralLogam::create($validated);

        return redirect()->route('superuser.mineral-logam.index')->with('success', 'Data berhasil ditambahkan.');
    }

    public function edit(NeracaMineralLogam $mineralLogam)
    {
        $provinsis = Provinsi::orderBy('nama_provinsi')->get();
        $kabupatens = Kabupaten::where('provinsi_id', $mineralLogam->provinsi_id)->get();
        $komoditasLogams = KomoditasLogam::with('kelompokKomoditasLogam')->get();
        $statDikBbs = StatDikBb::get();
        $idInstansis = IdInstansi::get();

        return view('superuser.mineral-logam.edit', compact('mineralLogam', 'provinsis', 'kabupatens', 'komoditasLogams', 'statDikBbs', 'idInstansis'));
    }

    public function update(Request $request, NeracaMineralLogam $mineralLogam)
    {
        $validated = $request->validate([
            'idml' => 'required|integer|unique:neraca_mineral_logams,idml,' . $mineralLogam->id,
            'nama_objek' => 'required|string',
            'tahun_data' => 'required|integer',
            'tahun_neraca' => 'required|integer',
            'provinsi_id' => 'required|exists:provinsis,id',
            'kabupaten_id' => 'required|exists:kabupatens,id',
            'komoditas_logam_id' => 'required|exists:komoditas_logams,id',
            'stat_dik_bb_id' => 'required|exists:stat_dik_bbs,id',
            'id_instansi_id' => 'nullable|exists:id_instansis,id',
            'hipotetik_bijih' => 'nullable|numeric',
            'hipotetik_logam' => 'nullable|numeric',
            'tereka_bijih' => 'nullable|numeric',
            'tereka_logam' => 'nullable|numeric',
            'tertunjuk_bijih' => 'nullable|numeric',
            'tertunjuk_logam' => 'nullable|numeric',
            'terukur_bijih' => 'nullable|numeric',
            'terukur_logam' => 'nullable|numeric',
            'terkira_bijih' => 'nullable|numeric',
            'terkira_logam' => 'nullable|numeric',
            'terbukti_bijih' => 'nullable|numeric',
            'terbukti_logam' => 'nullable|numeric',
            'remark' => 'nullable|string',
        ]);

        $validated['total_sd_bijih'] = ($validated['tereka_bijih'] ?? 0) + ($validated['tertunjuk_bijih'] ?? 0) + ($validated['terukur_bijih'] ?? 0);
        $validated['total_sd_logam'] = ($validated['tereka_logam'] ?? 0) + ($validated['tertunjuk_logam'] ?? 0) + ($validated['terukur_logam'] ?? 0);
        $validated['total_cad_bijih'] = ($validated['terkira_bijih'] ?? 0) + ($validated['terbukti_bijih'] ?? 0);
        $validated['total_cad_logam'] = ($validated['terkira_logam'] ?? 0) + ($validated['terbukti_logam'] ?? 0);

        $mineralLogam->update($validated);

        return redirect()->route('superuser.mineral-logam.index')->with('success', 'Data berhasil diubah.');
    }

    public function destroy(NeracaMineralLogam $mineralLogam)
    {
        $mineralLogam->delete();

        return redirect()->route('superuser.mineral-logam.index')->with('success', 'Data berhasil dihapus.');
    }
}