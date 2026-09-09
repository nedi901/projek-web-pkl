<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KelompokKomoditasLogam extends Model
{
    protected $fillable = ['nama_kelompok'];

    public function komoditasLogams() { return $this->hasMany(KomoditasLogam::class); }
}