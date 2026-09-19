<?php

namespace Database\Seeders;

use App\Models\KelompokKomoditasLogam;
use Illuminate\Database\Seeder;

class KelompokKomoditasLogamSeeder extends Seeder
{
    public function run(): void
    {
        KelompokKomoditasLogam::insert([
            ['nama_kelompok' => 'Logam Mulia'],
            ['nama_kelompok' => 'Logam Dasar'],
            ['nama_kelompok' => 'Logam Ringan'],
            ['nama_kelompok' => 'Logam Besi dan Paduan Besi'],
            ['nama_kelompok' => 'Logam Langka'],
        ]);
    }
}