<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NeracaGambut;
use App\Models\Provinsi;
use App\Models\Kabupaten;
use App\Imports\GambutImport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class GambutController extends Controller
{
    public function index(Request $request)
    {
        $data = NeracaGambut::with(['provinsi', 'kabupaten'])
            ->when($request->search, fn($q) => $q->where('nama_objek', 'like', "%{$request->search}%"))
            ->paginate(15);

        return view('admin.gambut.index', compact('data'));
    }

    public function create()
    {
        $provinsis = Provinsi::orderBy('nama_provinsi')->get();

        return view('admin.gambut.create', compact('provinsis'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'idgb' => 'required|integer|unique:neraca_gambuts,idgb',
            'nama_objek' => 'required|string',
            'tahun_data' => 'required|integer',
            'tahun_neraca' => 'required|integer',
            'provinsi_id' => 'required|exists:provinsis,id',
            'kabupaten_id' => 'required|exists:kabupatens,id',
            'nilai_kalori_min' => 'nullable|numeric',
            'nilai_kalori_max' => 'nullable|numeric',
            'luas_ha' => 'nullable|numeric',
            'volume_juta_m3' => 'nullable|numeric',
            'total_sd' => 'nullable|numeric',
            'bujur' => 'nullable|numeric',
            'lintang' => 'nullable|numeric',
            'remark' => 'nullable|string',
        ]);

        NeracaGambut::create($validated);

        return redirect()->route('admin.gambut.index')->with('success', 'Data berhasil ditambahkan.');
    }

    public function edit(NeracaGambut $gambut)
    {
        $provinsis = Provinsi::orderBy('nama_provinsi')->get();
        $kabupatens = Kabupaten::where('provinsi_id', $gambut->provinsi_id)->get();

        return view('admin.gambut.edit', compact('gambut', 'provinsis', 'kabupatens'));
    }

    public function update(Request $request, NeracaGambut $gambut)
    {
        $validated = $request->validate([
            'idgb' => 'required|integer|unique:neraca_gambuts,idgb,' . $gambut->id,
            'nama_objek' => 'required|string',
            'tahun_data' => 'required|integer',
            'tahun_neraca' => 'required|integer',
            'provinsi_id' => 'required|exists:provinsis,id',
            'kabupaten_id' => 'required|exists:kabupatens,id',
            'nilai_kalori_min' => 'nullable|numeric',
            'nilai_kalori_max' => 'nullable|numeric',
            'luas_ha' => 'nullable|numeric',
            'volume_juta_m3' => 'nullable|numeric',
            'total_sd' => 'nullable|numeric',
            'bujur' => 'nullable|numeric',
            'lintang' => 'nullable|numeric',
            'remark' => 'nullable|string',
        ]);

        $gambut->update($validated);

        return redirect()->route('admin.gambut.index')->with('success', 'Data berhasil diubah.');
    }

    public function destroy(NeracaGambut $gambut)
    {
        $gambut->delete();

        return redirect()->route('admin.gambut.index')->with('success', 'Data berhasil dihapus.');
    }

    public function kabupaten($provinsiId)
    {
        return Kabupaten::where('provinsi_id', $provinsiId)
            ->orderBy('nama_kabupaten')
            ->get(['id', 'nama_kabupaten']);
    }

    public function importForm()
    {
        return view('admin.gambut.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        Excel::import(new GambutImport, $request->file('file'));

        return redirect()->route('admin.gambut.index')->with('success', 'Import selesai.');
    }
}
