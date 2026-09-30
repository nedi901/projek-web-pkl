<?php

namespace App\Models;

use App\Models\Concerns\TracksDeletion;
use Illuminate\Database\Eloquent\Model;

class NeracaGambut extends Model
{
    use TracksDeletion;

    protected string $riwayatDomain = 'gambut';

    protected $fillable = [
        'idgb', 'tahun_data', 'tahun_neraca', 'nama_objek',
        'nilai_kalori_min', 'nilai_kalori_max',
        'luas_ha', 'volume_juta_m3', 'total_sd',
        'remark', 'provinsi_id', 'kabupaten_id', 'bujur', 'lintang',
    ];

    public function provinsi() { return $this->belongsTo(Provinsi::class); }
    public function kabupaten() { return $this->belongsTo(Kabupaten::class); }

    public function getNilaiKaloriLabelAttribute(): string
    {
        if ($this->nilai_kalori_min === null || $this->nilai_kalori_max === null) {
            return '-';
        }
        return number_format($this->nilai_kalori_min, 0) . ' - ' . number_format($this->nilai_kalori_max, 0) . ' kal/gr';
    }
}
