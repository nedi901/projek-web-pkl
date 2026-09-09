<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KelompokKomoditasBukanLogam extends Model
{
    protected $fillable = ['nama_kelompok'];

    public function komoditasBukanLogams() { return $this->hasMany(KomoditasBukanLogam::class); }
}
