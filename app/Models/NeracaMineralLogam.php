<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NeracaMineralLogam extends Model
{
    protected $fillable = [
        'idml', 'tahun_data', 'tahun_neraca', 'nama_objek',
        'komoditas_logam_id', 'stat_dik_bb_id', 'id_instansi_id',
        'tereka_bijih', 'tereka_logam', 'tertunjuk_bijih', 'tertunjuk_logam',
        'terukur_bijih', 'terukur_logam', 'total_sd_bijih', 'total_sd_logam',
        'terkira_bijih', 'terkira_logam', 'terbukti_bijih', 'terbukti_logam',
        'total_cad_bijih', 'total_cad_logam', 'remark',
        'provinsi_id', 'kabupaten_id', 'bujur', 'lintang',
    ];

    public function komoditasLogam() { return $this->belongsTo(KomoditasLogam::class); }
    public function statDikBb() { return $this->belongsTo(StatDikBb::class); }
    public function idInstansi() { return $this->belongsTo(IdInstansi::class); }
    public function provinsi() { return $this->belongsTo(Provinsi::class); }
    public function kabupaten() { return $this->belongsTo(Kabupaten::class); }
}