<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

/**
 * Menjalankan import Excel "semua atau tidak sama sekali" dan menyusun pesan untuk pengguna.
 * Import harus memakai trait App\Imports\Concerns\MelaporkanHasil (menyediakan masuk, dilewati, alasan).
 */
class JalankanImport
{
    /** @return array{galat: ?string, ringkasan: string} */
    public static function jalankan(object $import, mixed $file): array
    {
        try {
            DB::transaction(fn () => Excel::import($import, $file));
        } catch (Throwable $e) {
            report($e);

            return ['galat' => self::pesanGalat($e), 'ringkasan' => ''];
        }

        $ringkasan = "Import selesai: {$import->masuk} baris masuk, {$import->dilewati} dilewati.";

        if ($import->alasan) {
            $unik = array_values(array_unique($import->alasan));
            $ringkasan .= ' Dilewati karena: '.implode('; ', array_slice($unik, 0, 5))
                .(count($unik) > 5 ? '; dan lainnya' : '').'.';
        }

        return ['galat' => null, 'ringkasan' => $ringkasan];
    }

    private static function pesanGalat(Throwable $e): string
    {
        $awal = 'Import dibatalkan, tidak ada data yang tersimpan. ';

        if ($e instanceof InvalidArgumentException) {
            return $awal.$e->getMessage().'.';
        }

        if (preg_match('/Undefined array key "([^"]+)"/', $e->getMessage(), $m)) {
            return $awal."Kolom \"{$m[1]}\" tidak ditemukan di baris judul Excel. Cek nama kolom sesuai template.";
        }

        return $awal.'Pastikan file sesuai template dan isinya benar (rincian teknis dicatat di log).';
    }
}
