<?php

namespace App\Support;

/**
 * Pemformat angka besar untuk tampilan (Beranda).
 * Skala Indonesia: Ribu (10^3), Juta (10^6), Miliar (10^9), Triliun (10^12).
 */
class Angka
{
    /**
     * @return array{0: string, 1: string} [angka berformat, satuan lengkap]
     *  - satuan 'ton'  -> diringkas, mis. ['98,00', 'Miliar ton']
     *  - satuan lain   -> angka penuh, mis. ['9.144,50', 'MWe']
     */
    public static function format(float $n, string $satuan = 'ton'): array
    {
        if ($satuan !== 'ton') {
            return [number_format($n, 2, ',', '.'), $satuan];
        }

        $abs = abs($n);
        foreach ([[1e12, 'Triliun'], [1e9, 'Miliar'], [1e6, 'Juta'], [1e3, 'Ribu']] as [$batas, $nama]) {
            if ($abs >= $batas) {
                return [number_format($n / $batas, 2, ',', '.'), $nama . ' ton'];
            }
        }

        return [number_format($n, 2, ',', '.'), 'ton'];
    }

    /**
     * Angka lengkap gaya Indonesia: titik pemisah ribuan, koma desimal. Contoh: 436,55 / 17.459.896.902,00
     */
    public static function lengkap(float|int|string|null $n, int $desimal = 2): string
    {
        return number_format((float) $n, $desimal, ',', '.');
    }

    /**
     * Label ringkas untuk sumbu Y. Contoh: 0 / 2,5 Miliar / 250 Juta / 1.000
     * Angka < 1 juta ditulis penuh supaya satuan MWe / ha / juta ton tidak jadi aneh.
     */
    public static function sumbu(float|int $n): string
    {
        $abs = abs((float) $n);
        foreach ([[1e12, 'Triliun'], [1e9, 'Miliar'], [1e6, 'Juta']] as [$batas, $nama]) {
            if ($abs >= $batas) {
                $v = $n / $batas;
                $teks = floor($v) == $v ? number_format($v, 0, ',', '.') : rtrim(rtrim(number_format($v, 2, ',', '.'), '0'), ',');

                return $teks . ' ' . $nama;
            }
        }

        if (floor($n) == $n) {
            return number_format($n, 0, ',', '.');
        }

        return rtrim(rtrim(number_format($n, 2, ',', '.'), '0'), ',');
    }
}
