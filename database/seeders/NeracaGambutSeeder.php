<?php

namespace Database\Seeders;

use App\Models\NeracaGambut;
use App\Models\Provinsi;
use App\Models\Kabupaten;
use Illuminate\Database\Seeder;

/**
 * Data resmi Buku Neraca Sumber Daya dan Cadangan Mineral dan Batubara Indonesia Tahun 2026
 * (Tabel 34, "Data Termutakhirkan Desember 2025"). 10 provinsi, tervalidasi 100% terhadap
 * baris "Sumber Daya Gambut Indonesia" (grand total) dan subtotal per pulau di buku.
 *
 * Gambut CUMA punya Sumber Daya (gak ada Cadangan) - beda dari semua domain lain.
 */
class NeracaGambutSeeder extends Seeder
{
    public function run(): void
    {
        // provinsi, kalori_min, kalori_max, luas_ha, volume_juta_m3, total_sd_juta_ton
        $data = [
            ['Aceh', 1545, 5035, 57700.00, 2260.00, 239.82],
            ['Sumatera Utara', 4455, 5540, 27040.63, 30966.00, 166.76],
            ['Riau', 4395, 5950, 1311155.50, 50050.84, 5242.69],
            ['Jambi', 1405, 5220, 260407.00, 13393.00, 1648.68],
            ['Sumatera Selatan', 3018, 5540, 447615.94, 14973.80, 1396.07],
            ['Kalimantan Barat', 3210, 5670, 1114694.39, 11886.35, 1454.15],
            ['Kalimantan Tengah', 3395, 5330, 654519.62, 26154.32, 3557.58],
            ['Kalimantan Selatan', 2362, 5320, 250963.00, 1267.83, 223.07],
            ['Kalimantan Timur', 3400, 5480, 16579.00, 442.37, 42.48],
            ['Sulawesi Selatan', 4680, 5220, 1250.00, 9.50, 1.25],
        ];

        $idgb = 1000;

        foreach ($data as [$namaProvinsi, $kaloriMin, $kaloriMax, $luas, $volume, $sd]) {
            $provinsi = Provinsi::firstOrCreate(['nama_provinsi' => $namaProvinsi]);
            $kabupaten = Kabupaten::firstOrCreate([
                'provinsi_id' => $provinsi->id,
                'nama_kabupaten' => 'Rekap Provinsi',
            ]);

            NeracaGambut::create([
                'idgb' => $idgb++,
                'tahun_data' => 2025,
                'tahun_neraca' => 2026,
                'nama_objek' => 'Gambut ' . $namaProvinsi,
                'nilai_kalori_min' => $kaloriMin,
                'nilai_kalori_max' => $kaloriMax,
                'luas_ha' => $luas,
                'volume_juta_m3' => $volume,
                'total_sd' => $sd,
                'provinsi_id' => $provinsi->id,
                'kabupaten_id' => $kabupaten->id,
                'remark' => 'Data agregat resmi per provinsi (Tabel 34, Buku Neraca Minerba 2026) - bukan per titik lokasi individual.',
            ]);
        }
    }
}
