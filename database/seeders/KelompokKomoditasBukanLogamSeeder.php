<?php

namespace Database\Seeders;

use App\Models\KelompokKomoditasBukanLogam;
use Illuminate\Database\Seeder;

/**
 * ASUMSI STRUKTUR: kolom 'nama_kelompok' di tabel kelompok_komoditas_bukan_logams,
 * ngikutin pola persis KelompokKomoditasLogamSeeder (kelompok_komoditas_logams).
 * Kalau nama kolom/model kamu beda, tinggal sesuaikan di sini.
 *
 * Kelompok resmi sesuai Buku Neraca Minerba 2026 (Tabel 24 & 26):
 * - Mineral Industri
 * - Bahan Keramik
 * - Batuan
 * (masih ada beberapa kelompok lain di buku - Batu Mulia dll - belum ditambah
 * karena belum ada komoditas dari kelompok itu yang di-seed datanya)
 */
class KelompokKomoditasBukanLogamSeeder extends Seeder
{
    public function run(): void
    {
        $kelompoks = [
            'Mineral Industri',
            'Bahan Keramik',
            'Batuan',
        ];

        foreach ($kelompoks as $nama) {
            KelompokKomoditasBukanLogam::firstOrCreate(['nama_kelompok' => $nama]);
        }
    }
}
