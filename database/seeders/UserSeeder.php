<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            [
                'name' => 'User Biasa',
                'email' => 'user@test.com',
                'role' => 'user',
                'domain_akses' => null,
            ],
            [
                'name' => 'Super Batubara',
                'email' => 'superbatubara@test.com',
                'role' => 'super_user',
                'domain_akses' => 'batubara',
            ],
            [
                'name' => 'Super Mineral Logam',
                'email' => 'superminerallogam@test.com',
                'role' => 'super_user',
                'domain_akses' => 'mineral_logam',
            ],
            [
                'name' => 'Super User Bukan Logam',
                'email' => 'superminerallogambukan@test.com',
                'role' => 'super_user',
                'domain_akses' => 'mineral_bukan_logam',
            ],
            [
                'name' => 'Super User Panas Bumi',
                'email' => 'superpanasbumi@test.com',
                'role' => 'super_user',
                'domain_akses' => 'panas_bumi',
            ],
            [
                'name' => 'Admin',
                'email' => 'admin@test.com',
                'role' => 'admin',
                'domain_akses' => null,
            ],
        ];

        foreach ($accounts as $account) {
            User::updateOrCreate(
                ['email' => $account['email']],
                [
                    'name' => $account['name'],
                    'password' => 'password',
                    'role' => $account['role'],
                    'domain_akses' => $account['domain_akses'],
                ]
            );
        }
    }
}
