<?php

namespace App\Http\Controllers\SuperUser;

use App\Http\Controllers\Controller;
use App\Models\NeracaGambut;
use App\Models\Provinsi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GambutGrafikController extends Controller
{
    public function index(Request $request)
    {
        // Filter per Pulau - Gambut di buku sumbernya emang dikelompokkan per pulau
        // (Sumatera/Kalimantan/Sulawesi), jadi ini filter yang paling nyambung buat
        // domain ini - beda dari domain lain yang filter per komoditas.
        $pulauList = Provinsi::whereNotNull('pulau')->distinct()->orderBy('pulau')->pluck('pulau');
        $pulau = $request->pulau;

        $base = NeracaGambut::query()
            ->when($pulau, fn($q) => $q->whereHas('provinsi', fn($qq) => $qq->where('pulau', $pulau)));

        $perProvinsi = (clone $base)
            ->select('provinsi_id', DB::raw('SUM(total_sd) as total'))
            ->groupBy('provinsi_id')
            ->with('provinsi')
            ->orderByDesc('total')
            ->get();

        $totalSd = (clone $base)->sum('total_sd');
        $totalLuas = (clone $base)->sum('luas_ha');
        $totalVolume = (clone $base)->sum('volume_juta_m3');

        return view('superuser.gambut.grafik.index', compact(
            'pulauList', 'pulau', 'perProvinsi', 'totalSd', 'totalLuas', 'totalVolume'
        ));
    }
}
