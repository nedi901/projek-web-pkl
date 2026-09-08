<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kabupaten extends Model
{
    use HasFactory;

    protected $fillable = [
        'provinsi_id',
        'nama_kabupaten',
    ];

    /**
     * Relasi ke model Provinsi (Many-to-One).
     */
    public function provinsi(): BelongsTo
    {
        return $this->belongsTo(Provinsi::class);
    }
}