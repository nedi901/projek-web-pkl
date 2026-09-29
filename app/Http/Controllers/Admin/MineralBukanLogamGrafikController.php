<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NeracaMineralBukanLogam;
use App\Models\NeracaTrenNasional;
use App\Models\KomoditasBukanLogam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MineralBukanLogamGrafikController extends Controller
{
    public function index(Request $request)
    {
        $komoditasList = KomoditasBukanLogam::orderBy('nama_komoditas')->get();
        $komoditasId = $request->komoditas_id;
        $komoditasTerpilih = $komoditasId ? KomoditasBukanLogam::find($komoditasId) : null;

        $base = NeracaMineralBukanLogam::query()
            ->when($komoditasId, fn($q) => $q->where('komoditas_bukan_logam_id', $komoditasId));

        // Grafik utama: per provinsi kalau komoditas dipilih, per komoditas kalau belum
        if ($komoditasTerpilih) {
            $perProvinsi = (clone $base)
                ->select('provinsi_id', DB::raw('SUM(total_sd) as total'))
                ->groupBy('provinsi_id')
                ->with('provinsi')
                ->orderByDesc('total')
                ->get();
        } else {
            $perProvinsi = NeracaMineralBukanLogam::query()
                ->select('komoditas_bukan_logam_id', DB::raw('SUM(total_sd) as total'))
                ->groupBy('komoditas_bukan_logam_id')
                ->with('komoditasBukanLogam')
                ->orderByDesc('total')
                ->get();
        }

        // Donut Sumber Daya vs Cadangan
        $totalSd = (clone $base)->sum('total_sd');
        $totalCad = (clone $base)->sum('total_cad');
        $totalGabungan = ($totalSd + $totalCad) ?: 1;

        // Donut proporsi klasifikasi SNI (Hipotetik / Tereka / Tertunjuk / Terukur)
        $totalHipotetik = (clone $base)->sum('hipotetik');
        $totalTereka = (clone $base)->sum('tereka');
        $totalTertunjuk = (clone $base)->sum('tertunjuk');
        $totalTerukur = (clone $base)->sum('terukur');
        $totalKlasifikasi = ($totalHipotetik + $totalTereka + $totalTertunjuk + $totalTerukur) ?: 1;

        // Tren per tahun NASIONAL - dari tabel neraca_tren_nasionals (2021-2025), BUKAN dari
        // grouping baris per-provinsi di neraca_mineral_bukan_logams (yang cuma punya 1 tahun,
        // yaitu tahun snapshot data waktu di-seed - lihat NeracaTrenNasionalSeeder buat detail sumbernya)
        $trendQuery = NeracaTrenNasional::query()->domain('mineral_bukan_logam');
        if ($komoditasTerpilih) {
            $trendQuery->komoditas($komoditasTerpilih->nama_komoditas);
        } else {
            // gabungan semua komoditas: jumlahkan per tahun
            $trendQuery = NeracaTrenNasional::query()
                ->domain('mineral_bukan_logam')
                ->select('tahun', DB::raw('SUM(total_sd) as total'))
                ->groupBy('tahun');
        }
        if ($komoditasTerpilih) {
            $trendPerTahun = $trendQuery->orderBy('tahun')
                ->get()
                ->map(fn ($row) => (object) ['tahun_data' => $row->tahun, 'total' => $row->total_sd]);
        } else {
            $trendPerTahun = $trendQuery->orderBy('tahun')
                ->get()
                ->map(fn ($row) => (object) ['tahun_data' => $row->tahun, 'total' => $row->total]);
        }
        $trendMax = $trendPerTahun->max('total') ?: 1;

        return view('admin.mineral-bukan-logam.grafik.index', compact(
            'komoditasList', 'komoditasId', 'komoditasTerpilih',
            'perProvinsi', 'totalSd', 'totalCad', 'totalGabungan',
            'totalHipotetik', 'totalTereka', 'totalTertunjuk', 'totalTerukur', 'totalKlasifikasi',
            'trendPerTahun', 'trendMax'
        ));
    }
}
