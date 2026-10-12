<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NeracaBatubara;
use App\Models\NeracaGambut;
use App\Models\NeracaMineralBukanLogam;
use App\Models\NeracaMineralLogam;
use App\Models\NeracaPanasBumi;
use App\Models\RiwayatData;
use App\Models\TabelReferensi;
use App\Models\User;
use Illuminate\Http\Request;

/** Dashboard Super User: ringkasan lintas domain, akun, dan aktivitas terbaru. */
class SuperDashboardController extends Controller
{
    public function index(Request $request)
    {
        // aturan pembimbing: data tahun 2024-2025 (bisa dipersempit ke satu tahun)
        $tahun = in_array($request->query('tahun'), ['2024', '2025'], true) ? (int) $request->query('tahun') : null;
        $scope = fn ($q) => $tahun ? $q->where('tahun_data', $tahun) : $q->whereBetween('tahun_data', [2024, 2025]);

        $definisi = [
            ['label' => 'Batubara', 'model' => NeracaBatubara::class, 'sd' => 'total_sd', 'cad' => 'total_cad', 'satuan' => 'ton', 'faktor' => 1, 'prefix' => 'admin.batubara'],
            ['label' => 'Mineral Logam', 'model' => NeracaMineralLogam::class, 'sd' => 'total_sd_bijih', 'cad' => 'total_cad_bijih', 'satuan' => 'ton', 'faktor' => 1, 'prefix' => 'admin.mineral-logam'],
            ['label' => 'Mineral Bukan Logam', 'model' => NeracaMineralBukanLogam::class, 'sd' => 'total_sd', 'cad' => 'total_cad', 'satuan' => 'ton', 'faktor' => 1, 'prefix' => 'admin.mineral-bukan-logam'],
            ['label' => 'Panas Bumi', 'model' => NeracaPanasBumi::class, 'sd' => 'total_sd', 'cad' => 'total_cad', 'satuan' => 'MWe', 'faktor' => 1, 'prefix' => 'admin.panas-bumi'],
            ['label' => 'Gambut', 'model' => NeracaGambut::class, 'sd' => 'total_sd', 'cad' => null, 'satuan' => 'ton', 'faktor' => 1_000_000, 'prefix' => 'admin.gambut'],
        ];

        $domains = [];
        foreach ($definisi as $d) {
            $q = $scope($d['model']::query());

            $domains[] = $d + [
                'total_sd' => (float) (clone $q)->sum($d['sd']) * $d['faktor'],
                'total_cad' => $d['cad'] ? (float) (clone $q)->sum($d['cad']) * $d['faktor'] : null,
                'jumlah' => (clone $q)->count(),
                'terhapus' => $d['model']::onlyTrashed()->count(),
            ];
        }

        $akun = [
            'total' => User::count(),
            'super_user' => User::where('role', 'super_user')->count(),
            'admin' => User::where('role', 'admin')->count(),
            'user' => User::where('role', 'user')->count(),
            'nonaktif' => User::where('is_active', false)->count(),
        ];

        $aktivitas = RiwayatData::latest()->limit(8)->get();

        return view('admin.super.dashboard', [
            'domains' => $domains,
            'tahun' => $tahun,
            'akun' => $akun,
            'aktivitas' => $aktivitas,
            'jumlahTabelBuku' => TabelReferensi::count(),
            'totalTerhapus' => array_sum(array_column($domains, 'terhapus')),
        ]);
    }
}
