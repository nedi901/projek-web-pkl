<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Menukar arti role sesuai koreksi pembimbing:
 *   super_user = 1 orang, kendali penuh semua domain
 *   admin      = banyak orang, masing-masing 1 domain (domain_akses)
 *
 * Satu UPDATE dengan CASE, jadi tidak ada momen di mana semua akun "sama-sama" satu role.
 * Migration ini cuma jalan SEKALI (dicatat tabel migrations). Kalau database-nya di-migrate:fresh,
 * users kosong dulu lalu diisi UserSeeder yang sudah pakai arti baru, jadi aman.
 */
return new class extends Migration
{
    public function up(): void
    {
        $this->swap();
    }

    public function down(): void
    {
        $this->swap();
    }

    private function swap(): void
    {
        DB::statement("
            UPDATE users
            SET role = CASE role
                WHEN 'admin' THEN 'super_user'
                WHEN 'super_user' THEN 'admin'
                ELSE role
            END
            WHERE role IN ('admin', 'super_user')
        ");
    }
};
