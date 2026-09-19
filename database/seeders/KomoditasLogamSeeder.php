<?php

namespace Database\Seeders;

use App\Models\KomoditasLogam;
use Illuminate\Database\Seeder;

class KomoditasLogamSeeder extends Seeder
{
    public function run(): void
    {
        KomoditasLogam::insert([
            ['kelompok_komoditas_logam_id' => 1, 'nama_komoditas' => 'Emas Primer'],
            ['kelompok_komoditas_logam_id' => 1, 'nama_komoditas' => 'Perak'],
            ['kelompok_komoditas_logam_id' => 2, 'nama_komoditas' => 'Tembaga'],
            ['kelompok_komoditas_logam_id' => 2, 'nama_komoditas' => 'Timah'],
            ['kelompok_komoditas_logam_id' => 2, 'nama_komoditas' => 'Kobal'],
            ['kelompok_komoditas_logam_id' => 3, 'nama_komoditas' => 'Bauksit'],
            ['kelompok_komoditas_logam_id' => 4, 'nama_komoditas' => 'Nikel'],
            ['kelompok_komoditas_logam_id' => 4, 'nama_komoditas' => 'Besi Laterit'],
            ['kelompok_komoditas_logam_id' => 4, 'nama_komoditas' => 'Besi Primer'],
            ['kelompok_komoditas_logam_id' => 5, 'nama_komoditas' => 'Logam Tanah Jarang'],
        ]);
    }
}
