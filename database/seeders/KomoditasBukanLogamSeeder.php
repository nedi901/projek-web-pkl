<?php

namespace Database\Seeders;

use App\Models\KomoditasBukanLogam;
use App\Models\KelompokKomoditasBukanLogam;
use Illuminate\Database\Seeder;

/**
 * ASUMSI STRUKTUR: kolom 'nama_komoditas' + 'kelompok_id' (FK) di tabel
 * komoditas_bukan_logams, ngikutin pola persis KomoditasLogamSeeder.
 * Kalau nama kolom/model kamu beda, tinggal sesuaikan di sini.
 *
 * WAJIB jalan SETELAH KelompokKomoditasBukanLogamSeeder (butuh kelompok_id-nya).
 *
 * Cuma 5 komoditas yang udah ada data Neraca-nya (Tabel 27,28,29,30,31 buku 2026).
 * Kalau nanti nambah komoditas lain (Dolomit, Fosfat, Grafit, dst dari Tabel 23/25),
 * tinggal tambahin baris baru di $data di bawah - kelompoknya udah kesedia di seeder kelompok.
 */
class KomoditasBukanLogamSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['Batugamping', 'Mineral Industri'],
            ['Andesit', 'Batuan'],
            ['Lempung', 'Bahan Keramik'],
            ['Felspar', 'Bahan Keramik'],
            ['Pasir Kuarsa', 'Mineral Industri'],
        ];

        foreach ($data as [$namaKomoditas, $namaKelompok]) {
            $kelompok = KelompokKomoditasBukanLogam::where('nama_kelompok', $namaKelompok)->firstOrFail();

            KomoditasBukanLogam::firstOrCreate(
                ['nama_komoditas' => $namaKomoditas],
                ['kelompok_komoditas_bukan_logam_id' => $kelompok->id]
            );
        }
    }
}
