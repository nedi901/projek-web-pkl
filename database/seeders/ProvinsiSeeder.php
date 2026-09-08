<?php

namespace Database\Seeders;

use App\Models\Provinsi;
use Illuminate\Database\Seeder;

class ProvinsiSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        Provinsi::insert([
            ['nama_provinsi' => 'Aceh', 'pulau' => 'Sumatera', 'created_at' => $now, 'updated_at' => $now],
            ['nama_provinsi' => 'Bengkulu', 'pulau' => 'Sumatera', 'created_at' => $now, 'updated_at' => $now],
            ['nama_provinsi' => 'Kalimantan Selatan', 'pulau' => 'Kalimantan', 'created_at' => $now, 'updated_at' => $now],
            ['nama_provinsi' => 'Kalimantan Timur', 'pulau' => 'Kalimantan', 'created_at' => $now, 'updated_at' => $now],
            ['nama_provinsi' => 'Sumatra Barat', 'pulau' => 'Sumatera', 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}