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
 * - 2021-2023: DIBUANG. Sesuai arahan pembimbing, semua data cuma dipakai 2024-2025.
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
                2024 => [250213581859, 27358862496, 1106],
                2025 => [260919454016, 28794522974, 1140],
            ],
            'Andesit' => [
                2024 => [38685900000, 9052610000, 1031],
                2025 => [38754711726, 9047631231, 1045],
            ],
            'Lempung' => [
                2024 => [13103100000, 1384350000, 602],
                2025 => [13184611223, 1357626169, 619],
            ],
            'Felspar' => [
                2024 => [5288965136, 180863149, 186],
                2025 => [5288965136, 180863149, 186],
            ],
            'Pasir Kuarsa' => [
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
                2024 => [12807267931, 327111, 3214862972, 42592],
                2025 => [12148266659, 195609, 4664050834, 51697],
            ],
            'Nikel' => [
                2024 => [19157504227, 193543479, 5913865814, 62029856],
                2025 => [17376084667, 188098701, 5309622866, 52472310],
            ],
            'Besi Laterit' => [
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
