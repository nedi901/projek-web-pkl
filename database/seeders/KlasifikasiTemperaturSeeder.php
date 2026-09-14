<?php

namespace Database\Seeders;

use App\Models\KlasifikasiTemperatur;
use Illuminate\Database\Seeder;

class KlasifikasiTemperaturSeeder extends Seeder
{
    public function run(): void
    {
        KlasifikasiTemperatur::insert([
            ['label' => 'Rendah <125°C'],
            ['label' => 'Sedang 125-225°C'],
            ['label' => 'Tinggi >225°C'],
        ]);
    }
}