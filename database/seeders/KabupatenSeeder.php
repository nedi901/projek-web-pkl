<?php

namespace Database\Seeders;

use App\Models\Kabupaten;
use Illuminate\Database\Seeder;

class KabupatenSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        Kabupaten::insert([
            ['provinsi_id' => 1, 'nama_kabupaten' => 'Aceh Barat', 'created_at' => $now, 'updated_at' => $now],
            ['provinsi_id' => 1, 'nama_kabupaten' => 'Nagan Raya', 'created_at' => $now, 'updated_at' => $now],
            ['provinsi_id' => 2, 'nama_kabupaten' => 'Seluma', 'created_at' => $now, 'updated_at' => $now],
            ['provinsi_id' => 3, 'nama_kabupaten' => 'Tapin', 'created_at' => $now, 'updated_at' => $now],
            ['provinsi_id' => 4, 'nama_kabupaten' => 'Kutai Kartanegara', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}