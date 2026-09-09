<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\NeracaBatubara;
use Illuminate\Http\Request;

class BatubaraController extends Controller
{
    public function index(Request $request)
    {
        $data = NeracaBatubara::with(['provinsi', 'kabupaten', 'kelasKalori', 'statDikBb'])
            ->when($request->search, fn($q) => $q->where('nama_objek', 'like', "%{$request->search}%"))
            ->when($request->provinsi_id, fn($q) => $q->where('provinsi_id', $request->provinsi_id))
            ->when($request->kelas_kalori_id, fn($q) => $q->where('kelas_kalori_id', $request->kelas_kalori_id))
            ->paginate(15);

        return view('user.batubara.index', compact('data'));
    }

    public function show(NeracaBatubara $batubara)
    {
        $batubara->load(['provinsi', 'kabupaten', 'kelasKalori', 'statDikBb', 'idInstansi']);
        return view('user.batubara.show', compact('batubara'));
    }
}