<?php

namespace App\Http\Controllers\SuperUser;

use App\Http\Controllers\Controller;
use App\Models\NeracaPanasBumi;
use App\Models\KlasifikasiTemperatur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PanasBumiGrafikController extends Controller
{
    public function index(Request $request)
    {
        $klasifikasiList = KlasifikasiTemperatur::orderBy('id')->get();
        $klasifikasiId = $request->klasifikasi_id;
        $klasifikasiTerpilih = $klasifikasiId ? KlasifikasiTemperatur::find($klasifikasiId) : null;

        $base = NeracaPanasBumi::query()
            ->when($klasifikasiId, fn($q) => $q->where('klasifikasi_temperatur_id', $klasifikasiId));

        // Bar per provinsi
        $perProvinsi = (clone $base)
            ->select('provinsi_id', DB::raw('SUM(total_sd) as total'))
            ->groupBy('provinsi_id')
            ->with('provinsi')
            ->orderByDesc('total')
            ->get();

        // Donut Sumber Daya vs Cadangan
        $totalSd = (clone $base)->sum('total_sd');
        $totalCad = (clone $base)->sum('total_cad');
        $totalGabungan = ($totalSd + $totalCad) ?: 1;

        // Donut Sumber Daya: Spekulatif vs Hipotetik (Panas Bumi cuma 2 kategori SD,
        // BEDA dari domain lain yang punya Tereka/Tertunjuk/Terukur)
        $totalSpekulatif = (clone $base)->sum('spekulatif');
        $totalHipotetik = (clone $base)->sum('hipotetik');
        $totalSdKategori = ($totalSpekulatif + $totalHipotetik) ?: 1;

        // Donut Cadangan: Terduga / Mungkin / Terbukti (Panas Bumi 3 kategori cadangan,
        // BEDA dari domain lain yang cuma Terkira/Terbukti - koreksi penting: Terduga &
        // Mungkin itu CADANGAN buat Panas Bumi, bukan Sumber Daya - JANGAN diubah balik)
        $totalTerduga = (clone $base)->sum('terduga');
        $totalMungkin = (clone $base)->sum('mungkin');
        $totalTerbukti = (clone $base)->sum('terbukti');
        $totalCadKategori = ($totalTerduga + $totalMungkin + $totalTerbukti) ?: 1;

        // Kapasitas terpasang total (MW) - metrik unik Panas Bumi
        $totalKapasitas = (clone $base)->sum('kapasitas_terpasang');

        // Tren per tahun data
        $trendPerTahun = (clone $base)
            ->select('tahun_data', DB::raw('SUM(total_sd) as total'))
            ->groupBy('tahun_data')
            ->orderBy('tahun_data')
            ->get();
        $trendMax = $trendPerTahun->max('total') ?: 1;

        return view('superuser.panas-bumi.grafik.index', compact(
            'klasifikasiList', 'klasifikasiId', 'klasifikasiTerpilih',
            'perProvinsi', 'totalSd', 'totalCad', 'totalGabungan',
            'totalSpekulatif', 'totalHipotetik', 'totalSdKategori',
            'totalTerduga', 'totalMungkin', 'totalTerbukti', 'totalCadKategori',
            'totalKapasitas',
            'trendPerTahun', 'trendMax'
        ));
    }
}
