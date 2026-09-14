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

        // Trend per tahun_neraca — beneran dari database
        $trendPerTahun = NeracaBatubara::selectRaw('tahun_data, SUM(total_sd) as total')
    ->groupBy('tahun_data')
    ->orderBy('tahun_data')
    ->get();

$trendMax = $trendPerTahun->max('total') ?: 1;

$tahunMin = NeracaBatubara::min('tahun_data');
$tahunMax = NeracaBatubara::max('tahun_data');


        $provinsis = Provinsi::orderBy('nama_provinsi')->get();

       return view('user.grafik.index', compact(
    'perProvinsi', 'totalTereka', 'totalTertunjuk', 'totalTerukur', 'totalKlasifikasi',
    'trendPerTahun', 'trendMax', 'tahunMin', 'tahunMax', 'provinsis', 'domain'
));
    }
}