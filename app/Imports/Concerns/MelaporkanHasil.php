<?php

namespace App\Imports\Concerns;

use Illuminate\Database\Eloquent\Model;

/** Menghitung baris yang masuk/dilewati selama import, untuk dilaporkan ke pengguna (lihat JalankanImport). */
trait MelaporkanHasil
{
    public int $masuk = 0;

    public int $dilewati = 0;

    /** @var array<int, string> */
    public array $alasan = [];

    /** Dipakai di model(): `return $this->lewati('...');` */
    protected function lewati(string $alasan)
    {
        $this->dilewati++;
        $this->alasan[] = $alasan;

        return null;
    }

    protected function terima(Model $model): Model
    {
        $this->masuk++;

        return $model;
    }
}
