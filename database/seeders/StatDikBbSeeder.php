<?php

namespace Database\Seeders;

use App\Models\StatDikBb;
use Illuminate\Database\Seeder;

class StatDikBbSeeder extends Seeder
{
    public function run(): void
    {
        StatDikBb::insert([
            ['label' => 'Survei Tinjau'],
            ['label' => 'Prospeksi'],
            ['label' => 'Eksplorasi'],
            ['label' => 'Eksplorasi Rinci'],
            ['label' => 'Studi Kelayakan'],
            ['label' => 'Eksploitasi'],
            ['label' => 'Operasi Produksi'],
            ['label' => 'Lainnya'],
        ]);
    }
}