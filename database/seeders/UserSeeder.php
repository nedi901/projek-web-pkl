<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Hierarki:
     *   super_user = 1 orang, kendali penuh semua domain
     *   admin      = banyak orang, tiap orang dibatasi 1 domain (domain_akses)
     *   user       = read-only
     */
    public function run(): void
    {
        $accounts = [
            ['name' => 'User Biasa',          'email' => 'user@test.com',                   'role' => 'user',       'domain_akses' => null],
            ['name' => 'Super User',          'email' => 'admin@test.com',                  'role' => 'super_user', 'domain_akses' => null],
            ['name' => 'Admin Batubara',      'email' => 'superbatubara@test.com',          'role' => 'admin',      'domain_akses' => 'batubara'],
            ['name' => 'Admin Mineral Logam', 'email' => 'superminerallogam@test.com',      'role' => 'admin',      'domain_akses' => 'mineral_logam'],
            ['name' => 'Admin Bukan Logam',   'email' => 'superminerallogambukan@test.com', 'role' => 'admin',      'domain_akses' => 'mineral_bukan_logam'],
            ['name' => 'Admin Panas Bumi',    'email' => 'superpanasbumi@test.com',         'role' => 'admin',      'domain_akses' => 'panas_bumi'],
            ['name' => 'Admin Gambut',        'email' => 'supergambut@test.com',            'role' => 'admin',      'domain_akses' => 'gambut'],
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
