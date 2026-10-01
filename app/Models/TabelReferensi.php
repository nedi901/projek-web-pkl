<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Tabel rekapitulasi nasional dari buku (bentuknya beda-beda per tabel), disimpan generik
 * sebagai JSON supaya tidak perlu 1 migration per tabel. Dipakai halaman Grafik dan Visualisasi.
 */
class TabelReferensi extends Model
{
    protected $table = 'tabel_referensis';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'kolom' => 'array',
            'baris' => 'array',
        ];
    }
}
