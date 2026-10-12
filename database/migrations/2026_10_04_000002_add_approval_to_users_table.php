<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Pendaftaran akun dari halaman login memakai persetujuan Super User.
 *  - status_akun    : 'disetujui' (default, semua akun lama) atau 'menunggu'
 *  - role_diminta   : role yang diminta saat daftar (role asli akun tetap 'user' sampai disetujui)
 *  - domain_diminta : domain yang diminta (khusus role Admin)
 *  - keterangan     : instansi / keperluan pendaftar
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('status_akun', 20)->default('disetujui')->after('is_active');
            $table->string('role_diminta', 20)->nullable()->after('status_akun');
            $table->string('domain_diminta', 50)->nullable()->after('role_diminta');
            $table->string('keterangan')->nullable()->after('domain_diminta');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['status_akun', 'role_diminta', 'domain_diminta', 'keterangan']);
        });
    }
};
