<?php

namespace Database\Seeders;

use App\Models\KelasKalori;
use Illuminate\Database\Seeder;

/**
 * Nambah 1 opsi kelas kalori baru: "Campuran / Tidak Dipilah" - dipakai khusus buat data
 * Batubara per-provinsi (Tabel 33, Buku Neraca Minerba 2025) yang totalnya gabungan semua
 * kelas kalori, gak dipecah per kelas seperti breakdown nasional di buku 2026.
 *
 * Pakai firstOrCreate (bukan insert() polos kayak KelasKaloriSeeder asli) supaya AMAN
 * dijalankan kapan aja tanpa bikin baris duplikat, walau KelasKaloriSeeder asli udah pernah jalan.
 */
class KelasKaloriCampuranSeeder extends Seeder
{
    public function run(): void
    {
        KelasKalori::firstOrCreate(
            ['label' => 'Campuran / Tidak Dipilah'],
            ['rentang' => 'Gabungan seluruh kelas kalori (data belum dipilah per kelas)']
        );
    }
}
