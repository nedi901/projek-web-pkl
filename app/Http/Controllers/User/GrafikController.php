<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\NeracaBatubara;
use App\Models\Provinsi;
use Illuminate\Http\Request;

class GrafikController extends Controller
{
    public function index(Request $request)
    {
        $domain = $request->get('domain', 'batubara');

        $perProvinsi = NeracaBatubara::selectRaw('provinsi_id, SUM(total_sd) as total')
            ->with('provinsi')
            ->groupBy('provinsi_id')
            ->orderByDesc('total')
            ->take(6)
            ->get();

        $totalTereka = NeracaBatubara::sum('tereka');
        $totalTertunjuk = NeracaBatubara::sum('tertunjuk');
        $totalTerukur = NeracaBatubara::sum('terukur');
        $totalKlasifikasi = max($totalTereka + $totalTertunjuk + $totalTerukur, 1);

        $provinsis = Provinsi::orderBy('nama_provinsi')->get();

        return view('user.grafik.index', compact(
            'perProvinsi', 'totalTereka', 'totalTertunjuk', 'totalTerukur', 'totalKlasifikasi', 'provinsis', 'domain'
        ));
    }
}