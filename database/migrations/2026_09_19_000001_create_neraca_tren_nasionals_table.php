<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('neraca_tren_nasionals', function (Blueprint $table) {
            $table->id();
            // 'mineral_logam' | 'mineral_bukan_logam' | (nanti bisa 'batubara', 'panas_bumi', dst)
            $table->string('domain');
            // nama komoditas apa adanya (string biasa, BUKAN foreign key) - supaya 1 tabel ini
            // bisa dipakai lintas domain tanpa polymorphic FK yang ribet. Cuma dipakai buat
            // chart tren, jadi gak perlu integritas relasional seketat tabel neraca utama.
            $table->string('nama_komoditas');
            $table->year('tahun');

            // dipakai domain yang TIDAK di-split bijih/logam (mis. Mineral Bukan Logam)
            $table->decimal('total_sd', 20, 3)->nullable();
            $table->decimal('total_cad', 20, 3)->nullable();

            // dipakai domain yang DI-split bijih/logam (mis. Mineral Logam)
            $table->decimal('total_sd_bijih', 20, 3)->nullable();
            $table->decimal('total_sd_logam', 20, 3)->nullable();
            $table->decimal('total_cad_bijih', 20, 3)->nullable();
            $table->decimal('total_cad_logam', 20, 3)->nullable();

            // "Jumlah Data" dari chart buku (jumlah titik/lokasi neraca tahun itu) - opsional, buat referensi
            $table->integer('jumlah_data')->nullable();

            $table->string('sumber')->default('Buku Neraca Minerba 2026 - grafik statistik tren tahunan');
            $table->timestamps();

            $table->unique(['domain', 'nama_komoditas', 'tahun']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('neraca_tren_nasionals');
    }
};
