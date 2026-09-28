<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\NeracaBatubara;
use App\Models\NeracaMineralLogam;
use App\Models\NeracaMineralBukanLogam;
use App\Models\NeracaPanasBumi;
use App\Models\Provinsi;
use Illuminate\Http\Request;

class GrafikController extends Controller
{
    public function index(Request $request)
    {
        $domain = $request->get('domain', 'batubara');
        $provinsiId = $request->get('provinsi_id');

        // Peta domain -> model + nama kolom yang dipakai, karena tiap domain beda skema:
        // Batubara & Bukan Logam: tereka/tertunjuk/terukur/total_sd (single value)
        // Mineral Logam: *_bijih (di-split bijih/logam, di sini dipetakan ke komponen bijih)
        // Panas Bumi: TIDAK PUNYA tereka/tertunjuk/terukur sama sekali (kategorinya beda:
        //   Spekulatif/Hipotetik buat Sumber Daya). Dipetakan sementara ke slot yang sama
        //   biar donut "Proporsi Klasifikasi SNI" gak error, TAPI label di tampilan
        //   ("Tereka/Tertunjuk/Terukur") jadi gak akurat secara konsep buat domain ini -
        //   kalau mau dibenerin biar nunjukin "Spekulatif/Hipotetik" yang bener, kabari,
        //   nanti view-nya disesuaikan juga per-domain.
        [$model, $fields] = match ($domain) {
            'mineral-logam' => [NeracaMineralLogam::class, [
                'tereka' => 'tereka_bijih', 'tertunjuk' => 'tertunjuk_bijih',
                'terukur' => 'terukur_bijih', 'total_sd' => 'total_sd_bijih',
            ]],
            'mineral-bukan-logam' => [NeracaMineralBukanLogam::class, [
                'tereka' => 'tereka', 'tertunjuk' => 'tertunjuk',
                'terukur' => 'terukur', 'total_sd' => 'total_sd',
            ]],
            'panas-bumi' => [NeracaPanasBumi::class, [
                'tereka' => 'spekulatif', 'tertunjuk' => 'hipotetik',
                'terukur' => null, 'total_sd' => 'total_sd',
            ]],
            default => [NeracaBatubara::class, [
                'tereka' => 'tereka', 'tertunjuk' => 'tertunjuk',
                'terukur' => 'terukur', 'total_sd' => 'total_sd',
            ]],
        };

        $query = $model::query();
        if ($provinsiId) {
            $query->where('provinsi_id', $provinsiId);
        }

        $perProvinsi = (clone $query)
            ->selectRaw("provinsi_id, SUM({$fields['total_sd']}) as total")
            ->with('provinsi')
            ->groupBy('provinsi_id')
            ->orderByDesc('total')
            ->take(6)
            ->get();

        $totalTereka = (clone $query)->sum($fields['tereka']);
        $totalTertunjuk = (clone $query)->sum($fields['tertunjuk']);
        $totalTerukur = $fields['terukur'] ? (clone $query)->sum($fields['terukur']) : 0;
        $totalKlasifikasi = max($totalTereka + $totalTertunjuk + $totalTerukur, 1);

        $trendPerTahun = (clone $query)
            ->selectRaw("tahun_data, SUM({$fields['total_sd']}) as total")
            ->groupBy('tahun_data')
            ->orderBy('tahun_data')
            ->get();

        $trendMax = $trendPerTahun->max('total') ?: 1;

        $provinsis = Provinsi::orderBy('nama_provinsi')->get();

        return view('user.grafik.index', compact(
            'perProvinsi', 'totalTereka', 'totalTertunjuk', 'totalTerukur', 'totalKlasifikasi',
            'trendPerTahun', 'trendMax', 'provinsis', 'domain'
        ));
    }
}
