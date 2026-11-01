<?php

namespace App\Support;

/**
 * Kolom angka di database bersifat NOT NULL DEFAULT 0. Form yang kolom angkanya dikosongkan mengirim teks kosong,
 * yang oleh Laravel diubah jadi null, lalu ditulis sebagai NULL (default 0 TIDAK terpakai) dan database menolaknya.
 * Fungsi ini mengubah null pada kolom-kolom itu jadi 0. Kolom yang tidak dikirim sama sekali dibiarkan.
 */
class NolkanAngka
{
    /**
     * @param  array<string, mixed>  $data  hasil $request->validate()
     * @param  array<int, string>  $kolom  kolom angka yang NOT NULL
     */
    public static function terapkan(array $data, array $kolom): array
    {
        foreach ($kolom as $k) {
            if (array_key_exists($k, $data) && $data[$k] === null) {
                $data[$k] = 0;
            }
        }

        return $data;
    }
}
