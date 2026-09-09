<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NeracaMineralBukanLogam extends Model
{
    protected $fillable = [
        'idbl', 'tahun_data', 'tahun_neraca', 'nama_objek',
        'komoditas_bukan_logam_id', 'stat_dik_bb_id', 'id_instansi_id',
        'tereka', 'tertunjuk', 'terukur', 'total_sd',
        'terkira', 'terbukti', 'total_cad', 'remark',
        'provinsi_id', 'kabupaten_id', 'bujur', 'lintang',
    ];

    public function komoditasBukanLogam() { return $this->belongsTo(KomoditasBukanLogam::class); }
    public function statDikBb() { return $this->belongsTo(StatDikBb::class); }
    public function idInstansi() { return $this->belongsTo(IdInstansi::class); }
    public function provinsi() { return $this->belongsTo(Provinsi::class); }
    public function kabupaten() { return $this->belongsTo(Kabupaten::class); }
}