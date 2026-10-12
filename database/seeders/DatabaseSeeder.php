<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Urutan penting: referensi dulu, baru data neraca, terakhir tabel buku, perapihan, dan akun.
 * `php artisan migrate:fresh --seed` sekarang menghasilkan database LENGKAP (semua domain + tabel + akun testing).
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // --- referensi ---
            ProvinsiSeeder::class,
            KabupatenSeeder::class,
            KelasKaloriSeeder::class,
            KelasKaloriCampuranSeeder::class,
            StatDikBbSeeder::class,
            IdInstansiSeeder::class,
            KlasifikasiTemperaturSeeder::class,
            StatDikPbSeeder::class,
            KelompokKomoditasLogamSeeder::class,
            KomoditasLogamSeeder::class,
            KelompokKomoditasBukanLogamSeeder::class,
            KomoditasBukanLogamSeeder::class,

            // --- data neraca per domain ---
            NeracaBatubaraProvinsiSeeder::class,
            NeracaMineralLogamNikelSeeder::class,
            NeracaMineralLogamKomoditasLainSeeder::class,
            NeracaMineralBukanLogamSeeder::class,
            NeracaMineralBukanLogamTambahanSeeder::class,
            NeracaPanasBumiProvinsiSeeder::class,
            NeracaGambutSeeder::class,
            NeracaTrenNasionalSeeder::class,

            // --- tabel rekapitulasi dari buku ---
            TabelReferensiSeeder::class,
            TabelReferensiBatch2Seeder::class,

            // --- perapihan & akun ---
            RapikanDataSeeder::class,
            UserSeeder::class,
        ]);
    }
}
