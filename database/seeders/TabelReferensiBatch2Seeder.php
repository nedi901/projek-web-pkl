<?php

namespace Database\Seeders;

use App\Models\TabelReferensi;
use Illuminate\Database\Seeder;

/**
 * Batch 2 tabel Mineral Logam: Neraca Logam Strategis (Tabel 4), Nikel per kadar & tipe bijih (Tabel 9, 10),
 * Bauksit per kadar Al2O3 (Tabel 13, 14). Butuh tabel `tabel_referensis` dari paket sebelumnya (tabel-package).
 * Aman diulang (updateOrCreate per kode).
 */
class TabelReferensiBatch2Seeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/tabel_referensi_batch2.json');
        $tabels = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);

        foreach ($tabels as $t) {
            TabelReferensi::updateOrCreate(
                ['kode' => $t['kode']],
                [
                    'domain' => $t['domain'],
                    'judul' => $t['judul'],
                    'tahun_data' => $t['tahun_data'],
                    'sumber' => $t['sumber'],
                    'urutan' => $t['urutan'],
                    'kolom' => $t['kolom'],
                    'baris' => $t['baris'],
                    'catatan' => $t['catatan'],
                ]
            );
        }

        $this->command?->info(count($tabels) . ' tabel referensi batch 2 dimuat.');
    }
}
