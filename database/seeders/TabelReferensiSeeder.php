<?php

namespace Database\Seeders;

use App\Models\TabelReferensi;
use Illuminate\Database\Seeder;

/**
 * Mengisi tabel rekapitulasi dari Buku Neraca 2025 & 2026 (16 tabel, semua domain).
 * Data ada di database/seeders/data/tabel_referensi.json. Aman dijalankan berulang (updateOrCreate per kode).
 */
class TabelReferensiSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('seeders/data/tabel_referensi.json');
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

        $this->command?->info(count($tabels) . ' tabel referensi dimuat.');
    }
}
