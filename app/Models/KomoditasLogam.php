<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KomoditasLogam extends Model
{
    protected $fillable = ['kelompok_komoditas_logam_id', 'nama_komoditas'];

    public function kelompokKomoditasLogam() { return $this->belongsTo(KelompokKomoditasLogam::class); }
}