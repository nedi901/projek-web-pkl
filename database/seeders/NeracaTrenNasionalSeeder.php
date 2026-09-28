<?php

namespace Database\Seeders;

use App\Models\NeracaTrenNasional;
use Illuminate\Database\Seeder;

/**
 * Tren tahunan NASIONAL (bukan per-provinsi) 2021-2025, buat chart "Tren per Tahun Data"
 * di halaman Grafik. Sengaja dipisah dari tabel neraca utama (per-provinsi) supaya gak
 * ganggu perhitungan SUM() di chart lain (proporsi SNI, SD vs Cadangan, dst).
 *
 * Sumber & tingkat presisi per titik tahun:
 * - 2025: EXACT, dihitung dari data per-provinsi yang udah di-seed ke tabel neraca utama
 *   (Buku Neraca Minerba 2026, Tabel per-komoditas)
 * - 2024: EXACT dari Buku Neraca Minerba 2025 (Tabel per-komoditas edisi itu) - KECUALI
 *   Andesit & Lempung yang gak ada di buku 2025 sama sekali (baru masuk laporan mulai 2026),
 *   jadi masih approx dari chart.
 * - 2021-2023: APPROX, dibaca dari grafik statistik tren yang nempel di buku 2026
 *   (jadi ada pembulatan kecil, bukan data mentah per-lokasi).
 *
 * Semua angka Ton. Unit asli di buku macem-macem (Juta Ton, Ribu Ton, Miliar wmt) - udah
 * dikonversi ke Ton di sini.
 */
class NeracaTrenNasionalSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedBukanLogam();
        $this->seedMineralLogam();
    }

    private function seedBukanLogam(): void
    {
        // format: 'Komoditas' => [tahun => [total_sd, total_cad, jumlah_data]]
        $data = [
            'Batugamping' => [
                2021 => [177537000000, 12016000000, 865],
                2022 => [210910000000, 14066000000, 930],
                2023 => [227602000000, 21072000000, 1000],
                2024 => [250213581859, 27358862496, 1106],
                2025 => [260919454016, 28794522974, 1140],
            ],
            'Andesit' => [
                2021 => [21056890000, 3161690000, 601],
                2022 => [22328580000, 3717820000, 665],
                2023 => [26474120000, 5029550000, 804],
                2024 => [38685900000, 9052610000, 1031],
                2025 => [38754711726, 9047631231, 1045],
            ],
            'Lempung' => [
                2021 => [9955300000, 291020000, 547],
                2022 => [10851540000, 1380350000, 559],
                2023 => [11452600000, 783620000, 566],
                2024 => [13103100000, 1384350000, 602],
                2025 => [13184611223, 1357626169, 619],
            ],
            'Felspar' => [
                2021 => [4820060000, 34570000, 166],
                2022 => [4920430000, 55370000, 168],
                2023 => [5090260000, 108970000, 175],
                2024 => [5288965136, 180863149, 186],
                2025 => [5288965136, 180863149, 186],
            ],
            'Pasir Kuarsa' => [
                2021 => [2111230000, 330890000, 340],
                2022 => [3115030000, 1122520000, 370],
                2023 => [13477560000, 3402350000, 478],
                2024 => [26402954403, 7811621700, 611],
                2025 => [27426132793, 7889435191, 644],
            ],
        ];

        foreach ($data as $namaKomoditas => $years) {
            foreach ($years as $tahun => [$totalSd, $totalCad, $jumlahData]) {
                NeracaTrenNasional::updateOrCreate(
                    ['domain' => 'mineral_bukan_logam', 'nama_komoditas' => $namaKomoditas, 'tahun' => $tahun],
                    ['total_sd' => $totalSd, 'total_cad' => $totalCad, 'jumlah_data' => $jumlahData]
                );
            }
        }
    }

    private function seedMineralLogam(): void
    {
        // format: 'Komoditas' => [tahun => [total_sd_bijih, total_sd_logam, total_cad_bijih, total_cad_logam]]
        $data = [
            'Perak' => [
                2021 => [10545000000, 78624, 3116000000, 11541],
                2022 => [11139000000, 164478, 3338000000, 44287],
                2023 => [11402000000, 166325, 3231000000, 42657],
                2024 => [12807267931, 327111, 3214862972, 42592],
                2025 => [12148266659, 195609, 4664050834, 51697],
            ],
            'Nikel' => [
                2021 => [17686000000, 177815000, 5244000000, 57112000],
                2022 => [17336000000, 174210000, 5029000000, 55064000],
                2023 => [18550000000, 184607000, 5326000000, 56117000],
                2024 => [19157504227, 193543479, 5913865814, 62029856],
                2025 => [17376084667, 188098701, 5309622866, 52472310],
            ],
            'Besi Laterit' => [
                2021 => [7750000000, 1164000000, 1530000000, 318000000],
                2022 => [7530000000, 1145000000, 1390000000, 310000000],
                2023 => [7870000000, 1205000000, 1650000000, 359000000],
                2024 => [8395872604, 1322799223, 1967103001, 437111261],
                2025 => [9972435727, 1924670219, 3676601572, 655268999],
            ],
        ];

        foreach ($data as $namaKomoditas => $years) {
            foreach ($years as $tahun => [$sdBijih, $sdLogam, $cadBijih, $cadLogam]) {
                NeracaTrenNasional::updateOrCreate(
                    ['domain' => 'mineral_logam', 'nama_komoditas' => $namaKomoditas, 'tahun' => $tahun],
                    [
                        'total_sd_bijih' => $sdBijih,
                        'total_sd_logam' => $sdLogam,
                        'total_cad_bijih' => $cadBijih,
                        'total_cad_logam' => $cadLogam,
                    ]
                );
            }
        }
    }
}
