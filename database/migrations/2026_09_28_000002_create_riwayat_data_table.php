<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_data', function (Blueprint $table) {
            $table->id();
            $table->string('domain');                 // slug: batubara, mineral_logam, ... (sama dgn domain_akses)
            $table->string('aksi');                   // dihapus | dipulihkan | dihapus_permanen
            $table->unsignedBigInteger('data_id');    // id baris yang dihapus/dipulihkan
            $table->string('nama_data');              // nama_objek saat kejadian
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('user_nama')->nullable();  // snapshot, biar tetap ada walau akun dihapus
            $table->string('user_role')->nullable();
            $table->longText('snapshot')->nullable(); // isi lengkap data (JSON) saat kejadian
            $table->timestamps();

            $table->index(['domain', 'aksi']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_data');
    }
};
