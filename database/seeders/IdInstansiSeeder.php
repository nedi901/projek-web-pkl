<?php

namespace Database\Seeders;

use App\Models\IdInstansi;
use Illuminate\Database\Seeder;

class IdInstansiSeeder extends Seeder
{
    public function run(): void
    {
        IdInstansi::insert([
            ['label' => 'Pemerintah'],
            ['label' => 'Pemerintah Joint Study'],
            ['label' => 'Swasta'],
        ]);
    }
}
