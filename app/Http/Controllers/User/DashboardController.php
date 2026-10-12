<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\NeracaBatubara;
use App\Models\NeracaGambut;
use App\Models\NeracaMineralBukanLogam;
use App\Models\NeracaMineralLogam;
use App\Models\NeracaPanasBumi;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Aturan pembimbing: data yang dipakai hanya tahun 2024-2025.
        // ?tahun=2024 / ?tahun=2025 untuk satu tahun; selain itu = keduanya.
        $tahun = in_array($request->query('tahun'), ['2024', '2025'], true) ? (int) $request->query('tahun') : null;

        $scope = fn ($q) => $tahun
            ? $q->where('tahun_data', $tahun)
            : $q->whereBetween('tahun_data', [2024, 2025]);

        // satuan beda tiap domain, jadi TIDAK dibandingkan antar domain dalam satu angka/donut.
        // $sd / $cad = nama kolom; $faktor = pengali ke satuan tampil (Gambut disimpan dalam juta ton).
        $definisi = [
            ['key' => 'mineral_logam', 'label' => 'Mineral Logam', 'model' => NeracaMineralLogam::class,
                'sd' => 'total_sd_bijih', 'cad' => 'total_cad_bijih', 'satuan' => 'ton', 'faktor' => 1, 'route' => 'user.mineral-logam.index'],
            ['key' => 'mineral_bukan_logam', 'label' => 'Mineral Bukan Logam', 'model' => NeracaMineralBukanLogam::class,
                'sd' => 'total_sd', 'cad' => 'total_cad', 'satuan' => 'ton', 'faktor' => 1, 'route' => 'user.mineral-bukan-logam.index'],
            ['key' => 'batubara', 'label' => 'Batubara', 'model' => NeracaBatubara::class,
                'sd' => 'total_sd', 'cad' => 'total_cad', 'satuan' => 'ton', 'faktor' => 1, 'route' => 'user.batubara.index'],
            ['key' => 'panas_bumi', 'label' => 'Panas Bumi', 'model' => NeracaPanasBumi::class,
                'sd' => 'total_sd', 'cad' => 'total_cad', 'satuan' => 'MWe', 'faktor' => 1, 'route' => 'user.panas-bumi.index'],
            ['key' => 'gambut', 'label' => 'Gambut', 'model' => NeracaGambut::class,
                'sd' => 'total_sd', 'cad' => null, 'satuan' => 'ton', 'faktor' => 1_000_000, 'route' => 'user.gambut.index'],
        ];

        $domains = [];
        foreach ($definisi as $d) {
            $q = $scope($d['model']::query());

            $domains[] = $d + [
                'total_sd' => (float) (clone $q)->sum($d['sd']) * $d['faktor'],
                'total_cad' => $d['cad'] ? (float) (clone $q)->sum($d['cad']) * $d['faktor'] : null,
                'jumlah' => (clone $q)->count(),
            ];
        }

        $jumlahTotal = array_sum(array_column($domains, 'jumlah'));
        $jumlahMax = max(array_column($domains, 'jumlah')) ?: 1;

        return view('user.dashboard', compact('domains', 'tahun', 'jumlahTotal', 'jumlahMax'));
    }
}
