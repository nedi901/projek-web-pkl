<?php
namespace App\Http\Controllers\SuperUser;

use App\Http\Controllers\Controller;
use App\Models\NeracaMineralLogam;
use App\Models\KomoditasLogam;
use Illuminate\Http\Request;

class MineralLogamGrafikController extends Controller
{
    public function index(Request $request)
    {
        $komoditasId = $request->get('komoditas_id');

        $query = NeracaMineralLogam::query();
        if ($komoditasId) {
            $query->where('komoditas_logam_id', $komoditasId);
        }

        // Bar per provinsi (kalo pilih komoditas, difilter; kalo "semua", tetep per komoditas rekap)
        if ($komoditasId) {
            $perProvinsi = (clone $query)
                ->selectRaw('provinsi_id, SUM(total_sd_bijih) as total')
                ->with('provinsi')
                ->groupBy('provinsi_id')
                ->orderByDesc('total')
                ->take(6)
                ->get();
        } else {
            $perProvinsi = NeracaMineralLogam::selectRaw('komoditas_logam_id, SUM(total_sd_bijih) as total')
                ->with('komoditasLogam')
                ->groupBy('komoditas_logam_id')
                ->orderByDesc('total')
                ->take(6)
                ->get();
        }

        $totalHipotetik = (clone $query)->sum('hipotetik_bijih');
        $totalTereka = (clone $query)->sum('tereka_bijih');
        $totalTertunjuk = (clone $query)->sum('tertunjuk_bijih');
        $totalTerukur = (clone $query)->sum('terukur_bijih');
        $totalKlasifikasi = max($totalHipotetik + $totalTereka + $totalTertunjuk + $totalTerukur, 1);

        // Donut Sumber Daya vs Cadangan (sesuai pola buku)
        $totalSd = (clone $query)->sum('total_sd_bijih');
        $totalCad = (clone $query)->sum('total_cad_bijih');
        $totalGabungan = max($totalSd + $totalCad, 1);

        $trendPerTahun = (clone $query)
            ->selectRaw('tahun_data, SUM(total_sd_bijih) as total')
            ->groupBy('tahun_data')
            ->orderBy('tahun_data')
            ->get();

        $trendMax = $trendPerTahun->max('total') ?: 1;

        $komoditasList = KomoditasLogam::orderBy('nama_komoditas')->get();
        $komoditasTerpilih = $komoditasId ? KomoditasLogam::find($komoditasId) : null;

        return view('superuser.mineral-logam.grafik.index', compact(
            'perProvinsi', 'totalHipotetik', 'totalTereka', 'totalTertunjuk', 'totalTerukur', 'totalKlasifikasi',
            'totalSd', 'totalCad', 'totalGabungan',
            'trendPerTahun', 'trendMax', 'komoditasList', 'komoditasTerpilih', 'komoditasId'
        ));
    }
}