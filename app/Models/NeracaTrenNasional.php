<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NeracaTrenNasional extends Model
{
    protected $fillable = [
        'domain', 'nama_komoditas', 'tahun',
        'total_sd', 'total_cad',
        'total_sd_bijih', 'total_sd_logam', 'total_cad_bijih', 'total_cad_logam',
        'jumlah_data', 'sumber',
    ];

    public function scopeDomain($query, string $domain)
    {
        return $query->where('domain', $domain);
    }

    public function scopeKomoditas($query, string $nama)
    {
        return $query->where('nama_komoditas', $nama);
    }
}
