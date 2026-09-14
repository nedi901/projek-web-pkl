<?php

namespace Database\Seeders;

use App\Models\StatDikPb;
use Illuminate\Database\Seeder;

class StatDikPbSeeder extends Seeder
{
    public function run(): void
    {
        StatDikPb::insert([
            ['label' => 'Survei Pendahuluan Awal'],
            ['label' => 'Survei Pendahuluan'],
            ['label' => 'Survei Rinci'],
            ['label' => 'Eksplorasi / Siap Dikembangkan'],
            ['label' => 'Eksploitasi / Terpasang'],
        ]);
    }
}
