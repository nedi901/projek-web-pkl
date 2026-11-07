<?php

namespace Database\Seeders;

use App\Models\Kabupaten;
use App\Models\KelasKalori;
use App\Models\NeracaBatubara;
use App\Models\Provinsi;
use App\Models\StatDikBb;
use Illuminate\Database\Seeder;

/**
 * Seeder data dummy tren batubara per provinsi untuk tahun 2022, 2023, dan 2025
 * agar grafik tren per tahun tidak timpang tajam dibanding data 2024 (98 Miliar ton).
 *
 * Rasio skala:
 * - 2022 : ~91.2% dari 2024 (Total ~89 Miliar ton)
 * - 2023 : ~95.6% dari 2024 (Total ~94 Miliar ton)
 * - 2024 : 100% (Data resmi buku, ~98 Miliar ton)
 * - 2025 : ~104.5% dari 2024 (Total ~102 Miliar ton)
 */
class DummyBatubaraTrenSeeder extends Seeder
{
    public function run(): void
    {
        $baseData = [
            ['Aceh', 314580000, 401940000, 362230000, 313700000, 193360000],
            ['Sumatera Utara', 10240000, 8480000, 7550000, 0, 7120000],
            ['Riau', 271520000, 282000000, 303270000, 185520000, 173750000],
            ['Sumatera Barat', 26300000, 19540000, 33090000, 11090000, 18110000],
            ['Jambi', 1042780000, 1140650000, 1995480000, 632010000, 1068290000],
            ['Bengkulu', 137740000, 106060000, 171330000, 42200000, 67860000],
            ['Sumatera Selatan', 7541710000, 9795500000, 8325820000, 4586900000, 4348170000],
            ['Lampung', 10250000, 24280000, 60320000, 60320000, 0],
            ['Kalimantan Barat', 19280000, 13150000, 38550000, 3310000, 7910000],
            ['Kalimantan Tengah', 3853660000, 3138080000, 3050540000, 1532570000, 1417400000],
            ['Kalimantan Selatan', 3481900000, 3414040000, 6635730000, 1310690000, 2756260000],
            ['Kalimantan Timur', 8377750000, 13055480000, 17892130000, 5155770000, 7101240000],
            ['Kalimantan Utara', 857890000, 826560000, 927340000, 580700000, 377170000],
            ['Sulawesi Barat', 1300000, 1000000, 850000, 0, 0],
            ['Papua Barat', 6000000, 5700000, 7200000, 4090000, 0],
        ];

        $kelasKalori = KelasKalori::where('label', 'Campuran / Tidak Dipilah')->first()
            ?? KelasKalori::first();
        $statOperasi = StatDikBb::where('label', 'Operasi Produksi')->first()
            ?? StatDikBb::first();

        $yearsConfig = [
            2022 => ['factor' => 0.912, 'tahun_neraca' => 2023, 'start_idbb' => 5100],
            2023 => ['factor' => 0.956, 'tahun_neraca' => 2024, 'start_idbb' => 5200],
            2025 => ['factor' => 1.045, 'tahun_neraca' => 2026, 'start_idbb' => 5300],
        ];

        foreach ($yearsConfig as $tahunData => $cfg) {
            $factor = $cfg['factor'];
            $idbb = $cfg['start_idbb'];

            foreach ($baseData as [$namaProvinsi, $trk, $tjk, $tkr, $tkira, $tbkt]) {
                $provinsi = Provinsi::firstOrCreate(['nama_provinsi' => $namaProvinsi]);
                $kabupaten = Kabupaten::firstOrCreate([
                    'provinsi_id' => $provinsi->id,
                    'nama_kabupaten' => 'Rekap Provinsi',
                ]);

                $adjTrk = round($trk * $factor);
                $adjTjk = round($tjk * $factor);
                $adjTkr = round($tkr * $factor);
                $adjTkira = round($tkira * $factor);
                $adjTbkt = round($tbkt * $factor);

                NeracaBatubara::updateOrCreate(
                    [
                        'tahun_data' => $tahunData,
                        'provinsi_id' => $provinsi->id,
                        'nama_objek' => 'Batubara '.$namaProvinsi,
                    ],
                    [
                        'idbb' => $idbb++,
                        'tahun_neraca' => $cfg['tahun_neraca'],
                        'kelas_kalori_id' => $kelasKalori->id,
                        'stat_dik_bb_id' => $statOperasi->id,
                        'hipbb' => 0,
                        'tereka' => $adjTrk,
                        'tertunjuk' => $adjTjk,
                        'terukur' => $adjTkr,
                        'total_sd' => $adjTrk + $adjTjk + $adjTkr,
                        'terkira' => $adjTkira,
                        'terbukti' => $adjTbkt,
                        'total_cad' => $adjTkira + $adjTbkt,
                        'kabupaten_id' => $kabupaten->id,
                        'remark' => "Data estimasi tren tahunan batubara provinsi {$namaProvinsi} ({$tahunData})",
                    ]
                );
            }
        }
    }
}
