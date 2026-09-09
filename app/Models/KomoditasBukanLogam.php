<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KomoditasBukanLogam extends Model
{
    protected $fillable = ['kelompok_komoditas_bukan_logam_id', 'nama_komoditas'];

    public function kelompokKomoditasBukanLogam() { return $this->belongsTo(KelompokKomoditasBukanLogam::class); }
}