<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $tables = [
        'neraca_batubaras',
        'neraca_mineral_logams',
        'neraca_mineral_bukan_logams',
        'neraca_panas_bumis',
        'neraca_gambuts',
    ];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->softDeletes();                          // kolom deleted_at
                // Sengaja TANPA foreign key ke users: ALTER TABLE + FK di SQLite ribet, dan
                // nama penghapus juga disnapshot di tabel riwayat_data, jadi aman.
                $t->unsignedBigInteger('deleted_by')->nullable()->index();
            });
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            Schema::table($table, function (Blueprint $t) {
                $t->dropIndex(['deleted_by']);
                $t->dropColumn(['deleted_at', 'deleted_by']);
            });
        }
    }
};
