<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('neraca_gambuts', function (Blueprint $table) {
            $table->id();
            $table->integer('idgb')->unique();
            $table->year('tahun_data');
            $table->year('tahun_neraca');
            $table->string('nama_objek');

            // Nilai kalori dicetak di buku sebagai RANGE (mis. "1545 - 5035 kal/gr"),
            // jadi disimpan sebagai 2 kolom angka (min/max), bukan 1 field bebas kayak KLSBB
            // punya Batubara dulu (yang akhirnya dinormalisasi jadi tabel kelas_kaloris).
            // Gambut cuma 1 domain data tanpa banyak variasi tabel referensi, jadi range
            // langsung disimpan di sini aja, gak perlu tabel kelas_kalori terpisah.
            $table->decimal('nilai_kalori_min', 8, 2)->nullable();
            $table->decimal('nilai_kalori_max', 8, 2)->nullable();

            $table->decimal('luas_ha', 15, 2)->default(0);
            $table->decimal('volume_juta_m3', 15, 2)->default(0);

            // Gambut CUMA punya Sumber Daya, TIDAK ada kategori Cadangan sama sekali di buku
            // (beda dari semua domain lain) - makanya tabel ini gak punya kolom
            // terkira/terbukti/total_cad. Satu kolom 'total_sd' aja.
            $table->decimal('total_sd', 15, 2)->default(0);

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
        Schema::dropIfExists('neraca_gambuts');
    }
};
