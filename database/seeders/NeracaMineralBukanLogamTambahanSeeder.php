<?php

namespace Database\Seeders;

use App\Models\Kabupaten;
use App\Models\KelompokKomoditasBukanLogam;
use App\Models\KomoditasBukanLogam;
use App\Models\NeracaMineralBukanLogam;
use App\Models\Provinsi;
use App\Models\StatDikBb;
use Illuminate\Database\Seeder;

/**
 * Tambahan 3 komoditas Mineral Bukan Logam yang belum ada di seeder sebelumnya, per provinsi,
 * dari Buku Neraca Sumber Daya dan Cadangan Mineral dan Batubara Indonesia Tahun 2025 (data Desember 2024):
 * Batuan Pembawa Kalium (Tabel 25), Dolomit (Tabel 26), Kuarsit (Tabel 29).
 * Tiap komoditas sudah dicocokkan 1:1 (jumlah lokasi + 6 kolom) dengan baris TOTAL resmi buku.
 * Idempotent (updateOrCreate per idbl). Jalankan setelah KelompokKomoditasBukanLogamSeeder.
 */
class NeracaMineralBukanLogamTambahanSeeder extends Seeder
{
    public function run(): void
    {
        $kelompok = KelompokKomoditasBukanLogam::where('nama_kelompok', 'Mineral Industri')->firstOrFail();
        $statOperasi = StatDikBb::where('label', 'Operasi Produksi')->firstOrFail();
        $idbl = 2000;

        // kolom tiap baris: [provinsi, jumlah_lokasi, hipotetik, tereka, tertunjuk, terukur, terkira, terbukti]
        $data = [
            'Batuan Pembawa Kalium' => ['Tabel 25', [
                ['Jawa Tengah', 24, 0, 11580024064, 12923185793, 1407982985, 0, 0],
                ['Jawa Timur', 1, 0, 117500000, 0, 31453963, 0, 0],
                ['Sulawesi Barat', 4, 0, 44324799193, 0, 0, 0, 0],
                ['Sulawesi Selatan', 14, 1086652000, 4905633034, 306250000, 0, 0, 0],
            ]],
            'Dolomit' => ['Tabel 26', [
                ['Aceh', 13, 187500000, 659160000, 57327000, 0, 607595, 0],
                ['Banten', 2, 0, 350195826, 0, 0, 44878150, 0],
                ['Jawa Tengah', 2, 10000000, 0, 156000, 0, 0, 0],
                ['Jawa Timur', 29, 551531000, 532056942, 806244347, 469951801, 313900452, 451921796],
                ['Maluku Utara', 3, 114520000, 0, 0, 0, 0, 0],
                ['Nusa Tenggara Timur', 8, 825750000, 0, 691350000, 0, 0, 0],
                ['Sulawesi Tengah', 3, 262818000, 0, 0, 0, 0, 0],
                ['Sulawesi Tenggara', 1, 324000000, 0, 0, 0, 0, 0],
                ['Sumatera Utara', 11, 114724480, 894759207, 312183086, 0, 6817086, 0],
                ['Sumatera Barat', 7, 59800000, 372873527, 3399300, 3399300, 3399300, 3399300],
            ]],
            'Kuarsit' => ['Tabel 29', [
                ['Aceh', 6, 50000000, 4610700, 237154899, 0, 0, 0],
                ['Lampung', 2, 0, 54244, 0, 0, 0, 0],
                ['Nusa Tenggara Timur', 2, 0, 515000, 0, 0, 0, 0],
                ['Papua Barat', 1, 0, 2650000, 0, 0, 0, 0],
                ['Riau', 2, 9695700, 0, 0, 0, 0, 0],
                ['Sumatera Barat', 15, 291267000, 1410461967, 0, 0, 0, 0],
            ]],
        ];

        foreach ($data as $namaKomoditas => [$tabelBuku, $rows]) {
            $komoditas = KomoditasBukanLogam::firstOrCreate(
                ['nama_komoditas' => $namaKomoditas],
                ['kelompok_komoditas_bukan_logam_id' => $kelompok->id]
            );

            foreach ($rows as [$namaProvinsi, $jumlahLokasi, $hip, $trk, $tjk, $tkr, $tkira, $tbkt]) {
                $provinsi = Provinsi::firstOrCreate(['nama_provinsi' => $namaProvinsi]);
                $kabupaten = Kabupaten::firstOrCreate([
                    'provinsi_id' => $provinsi->id,
                    'nama_kabupaten' => 'Rekap Provinsi',
                ]);

                // withTrashed: kalau barisnya pernah dihapus ke Sampah, jangan dibuat duplikat (UNIQUE idbl)
                NeracaMineralBukanLogam::withTrashed()->updateOrCreate(
                    ['idbl' => $idbl++],
                    [
                        'tahun_data' => 2024,
                        'tahun_neraca' => 2025,
                        'nama_objek' => 'Rekap ' . $namaKomoditas . ' ' . $namaProvinsi . ' (' . $jumlahLokasi . ' lokasi)',
                        'komoditas_bukan_logam_id' => $komoditas->id,
                        'stat_dik_bb_id' => $statOperasi->id,
                        'hipotetik' => $hip,
                        'tereka' => $trk,
                        'tertunjuk' => $tjk,
                        'terukur' => $tkr,
                        'total_sd' => $trk + $tjk + $tkr,
                        'terkira' => $tkira,
                        'terbukti' => $tbkt,
                        'total_cad' => $tkira + $tbkt,
                        'provinsi_id' => $provinsi->id,
                        'kabupaten_id' => $kabupaten->id,
                        'remark' => "Data agregat resmi per provinsi (Buku Neraca Minerba 2025, {$tabelBuku}, data Desember 2024) — bukan per titik lokasi individual.",
                    ]
                );
            }
        }
    }
}
