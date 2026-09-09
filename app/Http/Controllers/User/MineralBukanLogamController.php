<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\NeracaMineralBukanLogam;
use Illuminate\Http\Request;

class MineralBukanLogamController extends Controller
{
    public function index(Request $request)
    {
        $data = NeracaMineralBukanLogam::with(['provinsi', 'kabupaten', 'komoditasBukanLogam.kelompokKomoditasBukanLogam', 'statDikBb'])
            ->when($request->search, fn($q) => $q->where('nama_objek', 'like', "%{$request->search}%"))
            ->when($request->provinsi_id, fn($q) => $q->where('provinsi_id', $request->provinsi_id))
            ->when($request->komoditas_bukan_logam_id, fn($q) => $q->where('komoditas_bukan_logam_id', $request->komoditas_bukan_logam_id))
            ->paginate(15);

        return view('user.mineral-bukan-logam.index', compact('data'));
    }

    public function show(NeracaMineralBukanLogam $mineralBukanLogam)
    {
        $mineralBukanLogam->load(['provinsi', 'kabupaten', 'komoditasBukanLogam.kelompokKomoditasBukanLogam', 'statDikBb', 'idInstansi']);
        return view('user.mineral-bukan-logam.show', compact('mineralBukanLogam'));
    }
}