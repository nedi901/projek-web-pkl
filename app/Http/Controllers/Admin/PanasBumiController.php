<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NeracaPanasBumi;
use App\Models\Provinsi;
use App\Models\Kabupaten;
use App\Models\StatDikPb;
use App\Models\KlasifikasiTemperatur;
use App\Models\IdInstansi;
use App\Imports\PanasBumiImport;
use App\Support\NolkanAngka;
use App\Support\JalankanImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class PanasBumiController extends Controller
{
    /** Kolom angka NOT NULL di database: isian kosong dari form dijadikan 0 (lihat NolkanAngka). */
    private const ANGKA_WAJIB = ['spekulatif', 'hipotetik', 'terduga', 'mungkin', 'terbukti', 'kapasitas_terpasang'];

    public function index(Request $request)
    {
        $data = NeracaPanasBumi::with(['provinsi', 'kabupaten', 'statDikPb', 'klasifikasiTemperatur'])
            ->when($request->search, fn($q) => $q->where('nama_objek', 'like', "%{$request->search}%"))
            ->paginate(15);

        return view('admin.panas-bumi.index', compact('data'));
    }

    public function create()
    {
        $provinsis = Provinsi::orderBy('nama_provinsi')->get();
        $statDikPbs = StatDikPb::get();
        $klasifikasiTemperaturs = KlasifikasiTemperatur::get();
        $idInstansis = IdInstansi::get();

        return view('admin.panas-bumi.create', compact('provinsis', 'statDikPbs', 'klasifikasiTemperaturs', 'idInstansis'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'idpb' => 'required|integer|unique:neraca_panas_bumis,idpb',
            'nama_objek' => 'required|string',
            'tahun_data' => 'required|integer',
            'tahun_neraca' => 'required|integer',
            'provinsi_id' => 'required|exists:provinsis,id',
            'kabupaten_id' => 'required|exists:kabupatens,id',
            'stat_dik_pb_id' => 'required|exists:stat_dik_pbs,id',
            'id_instansi_id' => 'nullable|exists:id_instansis,id',
            'klasifikasi_temperatur_id' => 'nullable|exists:klasifikasi_temperaturs,id',
            'temperatur_reservoir' => 'nullable|numeric',
            'spekulatif' => 'nullable|numeric',
            'hipotetik' => 'nullable|numeric',
            'terduga' => 'nullable|numeric',
            'mungkin' => 'nullable|numeric',
            'terbukti' => 'nullable|numeric',
            'kapasitas_terpasang' => 'nullable|numeric',
            'bujur' => 'nullable|numeric',
            'lintang' => 'nullable|numeric',
            'remark' => 'nullable|string',
        ]);

        $validated = NolkanAngka::terapkan($validated, self::ANGKA_WAJIB);

        $validated['total_sd'] = ($validated['spekulatif'] ?? 0) + ($validated['hipotetik'] ?? 0);
        $validated['total_cad'] = ($validated['terduga'] ?? 0) + ($validated['mungkin'] ?? 0) + ($validated['terbukti'] ?? 0);

        NeracaPanasBumi::create($validated);

        return redirect()->route('admin.panas-bumi.index')->with('success', 'Data berhasil ditambahkan.');
    }

    public function edit(NeracaPanasBumi $panasBumi)
    {
        $provinsis = Provinsi::orderBy('nama_provinsi')->get();
        $kabupatens = Kabupaten::where('provinsi_id', $panasBumi->provinsi_id)->get();
        $statDikPbs = StatDikPb::get();
        $klasifikasiTemperaturs = KlasifikasiTemperatur::get();
        $idInstansis = IdInstansi::get();

        return view('admin.panas-bumi.edit', compact('panasBumi', 'provinsis', 'kabupatens', 'statDikPbs', 'klasifikasiTemperaturs', 'idInstansis'));
    }

    public function update(Request $request, NeracaPanasBumi $panasBumi)
    {
        $validated = $request->validate([
            'idpb' => 'required|integer|unique:neraca_panas_bumis,idpb,' . $panasBumi->id,
            'nama_objek' => 'required|string',
            'tahun_data' => 'required|integer',
            'tahun_neraca' => 'required|integer',
            'provinsi_id' => 'required|exists:provinsis,id',
            'kabupaten_id' => 'required|exists:kabupatens,id',
            'stat_dik_pb_id' => 'required|exists:stat_dik_pbs,id',
            'id_instansi_id' => 'nullable|exists:id_instansis,id',
            'klasifikasi_temperatur_id' => 'nullable|exists:klasifikasi_temperaturs,id',
            'temperatur_reservoir' => 'nullable|numeric',
            'spekulatif' => 'nullable|numeric',
            'hipotetik' => 'nullable|numeric',
            'terduga' => 'nullable|numeric',
            'mungkin' => 'nullable|numeric',
            'terbukti' => 'nullable|numeric',
            'kapasitas_terpasang' => 'nullable|numeric',
            'bujur' => 'nullable|numeric',
            'lintang' => 'nullable|numeric',
            'remark' => 'nullable|string',
        ]);

        $validated = NolkanAngka::terapkan($validated, self::ANGKA_WAJIB);

        $validated['total_sd'] = ($validated['spekulatif'] ?? 0) + ($validated['hipotetik'] ?? 0);
        $validated['total_cad'] = ($validated['terduga'] ?? 0) + ($validated['mungkin'] ?? 0) + ($validated['terbukti'] ?? 0);

        $panasBumi->update($validated);

        return redirect()->route('admin.panas-bumi.index')->with('success', 'Data berhasil diubah.');
    }

    public function destroy(NeracaPanasBumi $panasBumi)
    {
        $panasBumi->delete();

        return redirect()->route('admin.panas-bumi.index')->with('success', 'Data berhasil dihapus.');
    }

    public function kabupaten($provinsiId)
    {
        return Kabupaten::where('provinsi_id', $provinsiId)
            ->orderBy('nama_kabupaten')
            ->get(['id', 'nama_kabupaten']);
    }

    public function importForm()
    {
        return view('admin.panas-bumi.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        $hasil = JalankanImport::jalankan(new PanasBumiImport, $request->file('file'));

        if ($hasil['galat']) {
            return back()->withErrors(['file' => $hasil['galat']]);
        }

        return redirect()->route('admin.panas-bumi.index')->with('success', $hasil['ringkasan']);
    }
}
