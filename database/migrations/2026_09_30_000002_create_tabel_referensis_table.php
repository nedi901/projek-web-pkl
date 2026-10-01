<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tabel_referensis', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();          // mis. ml-rekap-2025
            $table->string('domain')->index();         // slug = domain_akses: batubara, mineral_logam, ...
            $table->string('judul');
            $table->unsignedSmallInteger('tahun_data')->nullable();
            $table->string('sumber')->nullable();      // mis. "Buku Neraca 2026, Tabel 2"
            $table->unsignedSmallInteger('urutan')->default(0);
            $table->json('kolom');                     // [{k,l,t(text|int|num),d(desimal)}]
            $table->json('baris');                     // [{c:[...sel...], s:''|child|sub|total}]
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tabel_referensis');
    }
};
