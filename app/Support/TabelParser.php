<?php

namespace App\Support;

use InvalidArgumentException;

/**
 * Konversi dua arah antara teks (tempel dari Excel, dipisah TAB) dan struktur tabel_referensis
 * ({kolom: [{k,l,t,d}], baris: [{c:[...], s:''|child|sub|total}]}).
 *
 *  - Baris pertama = judul kolom.
 *  - Kolom pertama selalu teks. Kolom lain otomatis angka kalau SEMUA isinya angka (format Indonesia: 1.234,56).
 *  - "-" atau kosong pada kolom angka = 0.
 *  - Baris yang diawali TOTAL / JUMLAH otomatis bergaya total (tebal).
 */
class TabelParser
{
    public const MAKS_KOLOM = 40;
    public const MAKS_BARIS = 500;

    /** @return array{kolom: array<int, array>, baris: array<int, array>} */
    public static function dariTeks(string $teks, ?array $barisLama = null, ?array $kolomLama = null): array
    {
        $teks = str_replace(["\r\n", "\r"], "\n", trim($teks));
        $garis = array_values(array_filter(explode("\n", $teks), fn ($l) => trim($l) !== ''));

        if (count($garis) < 2) {
            throw new InvalidArgumentException('Isi minimal 1 baris judul kolom dan 1 baris data.');
        }
        if (count($garis) - 1 > self::MAKS_BARIS) {
            throw new InvalidArgumentException('Maksimal ' . self::MAKS_BARIS . ' baris data.');
        }

        $sel = array_map(fn ($l) => array_map('trim', explode("\t", $l)), $garis);
        $judul = array_shift($sel);
        $n = count($judul);

        if ($n < 2) {
            throw new InvalidArgumentException('Data harus punya minimal 2 kolom dan dipisahkan TAB (tempel langsung dari Excel).');
        }
        if ($n > self::MAKS_KOLOM) {
            throw new InvalidArgumentException('Maksimal ' . self::MAKS_KOLOM . ' kolom.');
        }

        foreach ($sel as $i => $baris) {
            if (count($baris) > $n) {
                throw new InvalidArgumentException('Baris data ke-' . ($i + 1) . ' punya ' . count($baris) . " kolom, lebih banyak dari judul kolom ($n).");
            }
            $sel[$i] = array_pad($baris, $n, '');
        }

        // tentukan tipe tiap kolom
        $kolom = [];
        for ($j = 0; $j < $n; $j++) {
            $label = $judul[$j] !== '' ? $judul[$j] : 'Kolom ' . ($j + 1);
            $def = ['k' => 'c' . ($j + 1), 'l' => $label, 't' => 'text'];

            if ($j > 0) {
                $adaAngka = false;
                $semuaAngka = true;
                $desimal = 0;
                foreach ($sel as $baris) {
                    $v = $baris[$j];
                    if ($v === '' || $v === '-') {
                        continue;
                    }
                    if (self::keAngka($v) === null) {
                        $semuaAngka = false;
                        break;
                    }
                    $adaAngka = true;
                    $desimal = max($desimal, self::jumlahDesimal($v));
                }
                if ($adaAngka && $semuaAngka) {
                    $def['t'] = 'num';
                    $def['d'] = min($desimal, 4);

                    // kolom jumlah/hitungan yang sebelumnya bertipe 'int' tetap 'int' (0 tampil "0", bukan "-")
                    if ($def['d'] === 0 && $kolomLama && ($kolomLama[$j]['l'] ?? null) === $label && ($kolomLama[$j]['t'] ?? null) === 'int') {
                        $def['t'] = 'int';
                        unset($def['d']);
                    }
                }
            }
            $kolom[] = $def;
        }

        // susun baris
        $baris = [];
        foreach ($sel as $i => $row) {
            $sel_ = [];
            foreach ($kolom as $j => $def) {
                if ($def['t'] === 'num') {
                    $sel_[] = ($row[$j] === '' || $row[$j] === '-') ? 0.0 : self::keAngka($row[$j]);
                } else {
                    $sel_[] = $row[$j];
                }
            }

            $gaya = preg_match('/^(grand\s+total|total|jumlah)\b/i', $row[0]) ? 'total' : '';
            // pertahankan gaya lama (sub/child) kalau baris di posisi yang sama masih berlabel sama
            if ($gaya === '' && $barisLama && isset($barisLama[$i]) && (string) ($barisLama[$i]['c'][0] ?? '') === (string) $row[0]) {
                $gaya = $barisLama[$i]['s'] ?? '';
            }
            $baris[] = ['c' => $sel_, 's' => $gaya];
        }

        return ['kolom' => $kolom, 'baris' => $baris];
    }

    /** Struktur tabel -> teks TAB (untuk diedit di textarea). */
    public static function keTeks(array $kolom, array $baris): string
    {
        $out = [implode("\t", array_map(fn ($k) => $k['l'], $kolom))];

        foreach ($baris as $row) {
            $sel = [];
            foreach ($kolom as $j => $k) {
                $v = $row['c'][$j] ?? '';
                if (($k['t'] ?? 'text') === 'text' || ! is_numeric($v)) {
                    $sel[] = str_replace(["\t", "\n", "\r"], ' ', (string) $v);
                } elseif ((float) $v == 0.0) {
                    $sel[] = '-';
                } else {
                    $sel[] = number_format((float) $v, (int) ($k['d'] ?? 0), ',', '.');
                }
            }
            $out[] = implode("\t", $sel);
        }

        return implode("\n", $out);
    }

    /** Angka format Indonesia (1.234,56) atau titik-desimal (436.55); null kalau bukan angka. */
    public static function keAngka(string $s): ?float
    {
        $s = trim(str_replace(' ', '', $s));

        if (preg_match('/^-?\d{1,3}(\.\d{3})+(,\d+)?$/', $s) || preg_match('/^-?\d+,\d+$/', $s)) {
            return (float) str_replace(',', '.', str_replace('.', '', $s));
        }
        if (preg_match('/^-?\d+(\.\d+)?$/', $s)) {
            return (float) $s;
        }

        return null;
    }

    private static function jumlahDesimal(string $s): int
    {
        $s = trim($s);
        if (str_contains($s, ',')) {
            return strlen(substr($s, strrpos($s, ',') + 1));
        }
        // "436.55" (titik desimal, bukan pemisah ribuan "1.234")
        if (preg_match('/^-?\d+\.(\d+)$/', $s, $m) && ! preg_match('/^-?\d{1,3}(\.\d{3})+$/', $s)) {
            return strlen($m[1]);
        }

        return 0;
    }
}
