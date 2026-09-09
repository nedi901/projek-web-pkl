<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\NeracaMineralLogam;
use Illuminate\Http\Request;

class MineralLogamController extends Controller
{
    public function index(Request $request)
    {
        $data = NeracaMineralLogam::with(['provinsi', 'kabupaten', 'komoditasLogam.kelompokKomoditasLogam', 'statDikBb'])
            ->when($request->search, fn($q) => $q->where('nama_objek', 'like', "%{$request->search}%"))
            ->when($request->provinsi_id, fn($q) => $q->where('provinsi_id', $request->provinsi_id))
            ->when($request->komoditas_logam_id, fn($q) => $q->where('komoditas_logam_id', $request->komoditas_logam_id))
            ->paginate(15);

        return view('user.mineral-logam.index', compact('data'));
    }

    public function show(NeracaMineralLogam $mineralLogam)
    {
        $mineralLogam->load(['provinsi', 'kabupaten', 'komoditasLogam.kelompokKomoditasLogam', 'statDikBb', 'idInstansi']);
        return view('user.mineral-logam.show', compact('mineralLogam'));
    }
}