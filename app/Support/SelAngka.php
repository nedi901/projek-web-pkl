<?php

namespace App\Support;

use InvalidArgumentException;

/**
 * Membaca isi sel Excel sebagai angka.
 *  - kosong atau "-"                 -> kosong (0 untuk ke(), null untuk keAtauNull())
 *  - angka asli Excel                -> apa adanya
 *  - teks format Indonesia "1.234,5" -> 1234.5
 *  - teks lain ("abc")               -> InvalidArgumentException (import dibatalkan dengan pesan jelas)
 */
class SelAngka
{
    public static function ke(mixed $nilai): float
    {
        return self::baca($nilai) ?? 0.0;
    }

    public static function keAtauNull(mixed $nilai): ?float
    {
        return self::baca($nilai);
    }

    private static function baca(mixed $nilai): ?float
    {
        if ($nilai === null) {
            return null;
        }
        if (is_int($nilai) || is_float($nilai)) {
            return (float) $nilai;
        }

        $teks = trim((string) $nilai);
        if ($teks === '' || $teks === '-') {
            return null;
        }

        $angka = TabelParser::keAngka($teks);
        if ($angka === null) {
            throw new InvalidArgumentException("Isi sel \"{$teks}\" bukan angka");
        }

        return $angka;
    }
}
