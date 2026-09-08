<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            ProvinsiSeeder::class,
            KabupatenSeeder::class,
            KelasKaloriSeeder::class,
            StatDikBbSeeder::class,
            IdInstansiSeeder::class,
        ]);
    }
}