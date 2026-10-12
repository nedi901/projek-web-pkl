<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Kolom tambahan untuk fitur Kelola Akun:
 *  - username      : unik, bisa dipakai login (selain email)
 *  - is_active     : akun bisa dinonaktifkan tanpa dihapus (default aktif)
 *  - last_login_at : waktu login terakhir (disimpan UTC, ditampilkan WIB)
 * Akun yang sudah ada otomatis diberi username dari bagian depan emailnya.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 30)->nullable()->unique()->after('name');
            $table->boolean('is_active')->default(true)->after('domain_akses');
            $table->timestamp('last_login_at')->nullable()->after('is_active');
        });

        $dipakai = [];
        foreach (DB::table('users')->orderBy('id')->get(['id', 'email', 'username']) as $u) {
            if ($u->username) {
                $dipakai[] = $u->username;
                continue;
            }

            $dasar = Str::limit(Str::slug(Str::before($u->email, '@'), '_'), 25, '') ?: 'user';
            $nama = $dasar;
            $i = 2;
            while (in_array($nama, $dipakai, true)) {
                $nama = $dasar . $i++;
            }

            $dipakai[] = $nama;
            DB::table('users')->where('id', $u->id)->update(['username' => $nama]);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['username']);
            $table->dropColumn(['username', 'is_active', 'last_login_at']);
        });
    }
};
