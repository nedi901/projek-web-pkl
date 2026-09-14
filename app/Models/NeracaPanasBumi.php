<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NeracaPanasBumi extends Model
{
    protected $fillable = [
        'idpb', 'tahun_data', 'tahun_neraca', 'nama_objek',
        'stat_dik_pb_id', 'id_instansi_id', 'klasifikasi_temperatur_id', 'temperatur_reservoir',
        'spekulatif', 'hipotetik', 'total_sd',
        'terduga', 'mungkin', 'terbukti', 'total_cad',
        'kapasitas_terpasang', 'remark',
        'provinsi_id', 'kabupaten_id', 'bujur', 'lintang',
    ];

    public function statDikPb() { return $this->belongsTo(StatDikPb::class); }
    public function klasifikasiTemperatur() { return $this->belongsTo(KlasifikasiTemperatur::class); }
    public function idInstansi() { return $this->belongsTo(IdInstansi::class); }
    public function provinsi() { return $this->belongsTo(Provinsi::class); }
    public function kabupaten() { return $this->belongsTo(Kabupaten::class); }
}