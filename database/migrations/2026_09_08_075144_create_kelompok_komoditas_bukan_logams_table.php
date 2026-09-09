<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('kelompok_komoditas_bukan_logams', function (Blueprint $table) {
        $table->id();
        $table->string('nama_kelompok');
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('kelompok_komoditas_bukan_logams');
}
};
