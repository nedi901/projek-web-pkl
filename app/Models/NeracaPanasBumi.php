<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NeracaPanasBumi extends Model
{
    protected $fillable = [
        'idpb', 'tahun_data', 'tahun_neraca', 'nama_objek',
        'stat_dik_bb_id', 'id_instansi_id',
        'spekulatif', 'hipotetis', 'terduga', 'total_sd',
        'terbukti', 'kapasitas_terpasang', 'remark',
        'provinsi_id', 'kabupaten_id', 'bujur', 'lintang',
    ];

    public function statDikBb() { return $this->belongsTo(StatDikBb::class); }
    public function idInstansi() { return $this->belongsTo(IdInstansi::class); }
    public function provinsi() { return $this->belongsTo(Provinsi::class); }
    public function kabupaten() { return $this->belongsTo(Kabupaten::class); }
}