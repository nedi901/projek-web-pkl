<?php

namespace Database\Seeders;

use App\Models\StatDikBb;
use Illuminate\Database\Seeder;

class StatDikBbSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        StatDikBb::insert([
            ['label' => 'Eksplorasi Umum', 'created_at' => $now, 'updated_at' => $now],
            ['label' => 'Eksplorasi Rinci', 'created_at' => $now, 'updated_at' => $now],
            ['label' => 'Operasi Produksi', 'created_at' => $now, 'updated_at' => $now],
            ['label' => 'Survei Tinjau', 'created_at' => $now, 'updated_at' => $now],
            ['label' => 'Prospeksi', 'created_at' => $now, 'updated_at' => $now],
            ['label' => 'Studi Kelayakan', 'created_at' => $now, 'updated_at' => $now],
            ['label' => 'Lainnya', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}