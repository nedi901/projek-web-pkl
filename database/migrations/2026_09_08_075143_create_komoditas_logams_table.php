<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('komoditas_logams', function (Blueprint $table) {
        $table->id();
        $table->foreignId('kelompok_komoditas_logam_id')->constrained('kelompok_komoditas_logams');
        $table->string('nama_komoditas');
        $table->timestamps();
    });
}

public function down(): void
{
    Schema::dropIfExists('komoditas_logams');
}
};
