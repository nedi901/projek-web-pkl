<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NeracaBatubara extends Model
{
    use HasFactory;

    protected $fillable = [
        'idbb',
        'tahun_data',
        'tahun_neraca',
        'nama_objek',
        'kelas_kalori_id',
        'stat_dik_bb_id',
        'id_instansi_id',
        'hipbb',
        'tereka',
        'tertunjuk',
        'terukur',
        'total_sd',
        'terkira',
        'terbukti',
        'total_cad',
        'remark',
        'provinsi_id',
        'kabupaten_id',
        'bujur',
        'lintang',
    ];

    public function kelasKalori(): BelongsTo
    {
        return $this->belongsTo(KelasKalori::class);
    }

    public function statDikBb(): BelongsTo
    {
        return $this->belongsTo(StatDikBb::class);
    }

    public function idInstansi(): BelongsTo
    {
        return $this->belongsTo(IdInstansi::class);
    }

    public function provinsi(): BelongsTo
    {
        return $this->belongsTo(Provinsi::class);
    }

    public function kabupaten(): BelongsTo
    {
        return $this->belongsTo(Kabupaten::class);
    }
}