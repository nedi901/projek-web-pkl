<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RiwayatData extends Model
{
    // Ditulis eksplisit supaya Laravel tidak menebak jadi "riwayat_datas".
    protected $table = 'riwayat_data';

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
