<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Provinsi extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_provinsi',
        'pulau',
    ];

    /**
     * Relasi ke model Kabupaten (One-to-Many).
     */
    public function kabupatens(): HasMany
    {
        return $this->hasMany(Kabupaten::class);
    }
}