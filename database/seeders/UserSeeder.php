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
     *
     * Login bisa pakai email ATAU username. Semua password akun testing: "password".
     */
    public function run(): void
    {
        $accounts = [
            ['name' => 'User Biasa',          'username' => 'user',           'email' => 'user@test.com',                   'role' => 'user',       'domain_akses' => null],
            ['name' => 'Super User',          'username' => 'superuser',      'email' => 'admin@test.com',                  'role' => 'super_user', 'domain_akses' => null],
            ['name' => 'Admin Batubara',      'username' => 'admin_batubara', 'email' => 'superbatubara@test.com',          'role' => 'admin',      'domain_akses' => 'batubara'],
            ['name' => 'Admin Mineral Logam', 'username' => 'admin_logam',    'email' => 'superminerallogam@test.com',      'role' => 'admin',      'domain_akses' => 'mineral_logam'],
            ['name' => 'Admin Bukan Logam',   'username' => 'admin_bukanlogam', 'email' => 'superminerallogambukan@test.com', 'role' => 'admin',    'domain_akses' => 'mineral_bukan_logam'],
            ['name' => 'Admin Panas Bumi',    'username' => 'admin_panasbumi', 'email' => 'superpanasbumi@test.com',        'role' => 'admin',      'domain_akses' => 'panas_bumi'],
            ['name' => 'Admin Gambut',        'username' => 'admin_gambut',   'email' => 'supergambut@test.com',            'role' => 'admin',      'domain_akses' => 'gambut'],
        ];

        foreach ($accounts as $account) {
            User::updateOrCreate(
                ['email' => $account['email']],
                [
                    'name' => $account['name'],
                    'username' => $account['username'],
                    'password' => 'password',
                    'role' => $account['role'],
                    'domain_akses' => $account['domain_akses'],
                    'is_active' => true,
                ]
            );
        }
    }
}
