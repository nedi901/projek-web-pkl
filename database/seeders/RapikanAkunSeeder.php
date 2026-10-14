<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Merapikan NAMA tampilan akun testing agar sesuai role setelah role dibalik
 * (mis. akun admin domain masih bernama "Super Batubara"). Aman diulang.
 * Yang diubah hanya kolom name. Email, username, dan password TIDAK berubah.
 *
 *   php artisan db:seed --class=RapikanAkunSeeder
 */
class RapikanAkunSeeder extends Seeder
{
    public function run(): void
    {
        $nama = [
            'admin@test.com' => 'Super User',
            'superbatubara@test.com' => 'Admin Batubara',
            'superminerallogam@test.com' => 'Admin Mineral Logam',
            'superminerallogambukan@test.com' => 'Admin Mineral Bukan Logam',
            'superpanasbumi@test.com' => 'Admin Panas Bumi',
            'supergambut@test.com' => 'Admin Gambut',
            'user@test.com' => 'User Biasa',
        ];

        foreach ($nama as $email => $name) {
            User::where('email', $email)->update(['name' => $name]);
        }
    }
}
