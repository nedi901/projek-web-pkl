<?php

namespace Database\Seeders;

use App\Models\KelasKalori;
use Illuminate\Database\Seeder;

class KelasKaloriSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        KelasKalori::insert([
            ['label' => 'Kalori Rendah', 'rentang' => '<4200 kkal/kg gar', 'created_at' => $now, 'updated_at' => $now],
            ['label' => 'Kalori Sedang', 'rentang' => '4200-5200 kkal/kg gar', 'created_at' => $now, 'updated_at' => $now],
            ['label' => 'Kalori Tinggi', 'rentang' => '>5200 kkal/kg gar', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}