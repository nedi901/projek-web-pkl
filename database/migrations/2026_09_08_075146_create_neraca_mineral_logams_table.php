<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('neraca_mineral_logams', function (Blueprint $table) {
        $table->id();
        $table->integer('idml')->unique();
        $table->year('tahun_data');
        $table->year('tahun_neraca');
        $table->string('nama_objek');
        $table->foreignId('komoditas_logam_id')->constrained('komoditas_logams');
        $table->foreignId('stat_dik_bb_id')->constrained('stat_dik_bbs');
        $table->foreignId('id_instansi_id')->nullable()->constrained('id_instansis');
        $table->decimal('tereka_bijih', 15, 3)->default(0);
        $table->decimal('tereka_logam', 15, 3)->default(0);
        $table->decimal('tertunjuk_bijih', 15, 3)->default(0);
        $table->decimal('tertunjuk_logam', 15, 3)->default(0);
        $table->decimal('terukur_bijih', 15, 3)->default(0);
        $table->decimal('terukur_logam', 15, 3)->default(0);
        $table->decimal('total_sd_bijih', 15, 3)->default(0);
        $table->decimal('total_sd_logam', 15, 3)->default(0);
        $table->decimal('terkira_bijih', 15, 3)->default(0);
        $table->decimal('terkira_logam', 15, 3)->default(0);
        $table->decimal('terbukti_bijih', 15, 3)->default(0);
        $table->decimal('terbukti_logam', 15, 3)->default(0);
        $table->decimal('total_cad_bijih', 15, 3)->default(0);
        $table->decimal('total_cad_logam', 15, 3)->default(0);
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
    Schema::dropIfExists('neraca_mineral_logams');
}
};
