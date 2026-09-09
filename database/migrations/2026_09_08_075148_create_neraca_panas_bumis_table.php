<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('neraca_panas_bumis', function (Blueprint $table) {
        $table->id();
        $table->integer('idpb')->unique();
        $table->year('tahun_data');
        $table->year('tahun_neraca');
        $table->string('nama_objek');
        $table->foreignId('stat_dik_bb_id')->constrained('stat_dik_bbs');
        $table->foreignId('id_instansi_id')->nullable()->constrained('id_instansis');
        $table->decimal('spekulatif', 15, 3)->default(0);
        $table->decimal('hipotetis', 15, 3)->default(0);
        $table->decimal('terduga', 15, 3)->default(0);
        $table->decimal('total_sd', 15, 3)->default(0);
        $table->decimal('terbukti', 15, 3)->default(0);
        $table->decimal('kapasitas_terpasang', 15, 3)->default(0);
        $table->text('remark')->nullable();
        $table->foreignId('provinsi_id')->constrained('provinsis');
        $table->foreignId('kabupaten_id')->constrained('kabupatens');
        $table->decimal('bujur', 10, 6)->nullable();
        $table->decimal('lintang', 10, 6)->nullable();
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('neraca_panas_bumis');
}
};
