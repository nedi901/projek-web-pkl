<?php

namespace Database\Seeders;

use App\Models\NeracaBatubara;
use App\Models\Provinsi;
use App\Models\Kabupaten;
use App\Models\KelasKalori;
use App\Models\StatDikBb;
use Illuminate\Database\Seeder;

/**
 * Data resmi Buku Neraca Sumber Daya dan Cadangan Mineral dan Batubara Indonesia Tahun 2025
 * (edisi LAMA, bukan 2026 - "Data Termutakhirkan Desember 2024"), Tabel 33: Sumber Daya dan
 * Cadangan Batubara Indonesia per Provinsi Tahun 2024.
 *
 * Ini SATU-SATUNYA sumber breakdown batubara PER PROVINSI yang ditemukan - buku edisi 2026
 * cuma nerbitin agregat nasional per kelas kalori (lihat kartu statis di halaman Grafik),
 * jadi datanya sengaja dari edisi 2025, bukan 2026.
 *
 * VALIDASI: total tiap kolom (Tereka/Tertunjuk/Terukur/Terkira/Terbukti) dijumlah lintas
 * provinsi dan dicocokkan ke baris "TOTAL INDONESIA" resmi tabel - cocok 100% KECUALI baris
 * Aceh, yang kolom TOTAL SD tercetak di buku (1.042,75) sendiri gak sinkron sama penjumlahan
 * TEREKA+TERTUNJUK+TERUKUR-nya (314,58+401,94+362,23=1.078,75, beda 36) - ini kesalahan
 * cetak/kompilasi DI BUKU ASLINYA. Gak masalah buat kita karena total_sd/total_cad di bawah
 * dihitung sendiri dari komponen (pola yang sama persis kayak NeracaMineralLogamNikelSeeder),
 * bukan disalin dari kolom TOTAL yang tercetak.
 *
 * KETERBATASAN: tabel ini TIDAK dipecah per kelas kalori (beda dari breakdown nasional di
 * buku 2026) - jadi kelas_kalori_id pakai opsi baru "Campuran / Tidak Dipilah"
 * (KelasKaloriCampuranSeeder, WAJIB dijalankan sebelum seeder ini). Kolom HIPOTETIK juga
 * tidak ada di tabel ini (diisi 0, sama seperti Nikel).
 */
class NeracaBatubaraProvinsiSeeder extends Seeder
{
    public function run(): void
    {
        // provinsi, tereka, tertunjuk, terukur, terkira, terbukti (dalam TON, sudah dikonversi dari Juta Ton)
        $data = [
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
            // Banten, Jawa Tengah, Jawa Timur, Sulawesi Selatan, Sulawesi Tengah,
            // Sulawesi Tenggara, Maluku Utara, Papua: semua nol di buku (baru area target
            // eksplorasi, belum ada sumber daya/cadangan terverifikasi) - di-skip di loop bawah
        ];

        $kelasKalori = KelasKalori::where('label', 'Campuran / Tidak Dipilah')->firstOrFail();
        $statOperasi = StatDikBb::where('label', 'Operasi Produksi')->firstOrFail();

        $idbb = 5000; // range terpisah dari bb_sample.xlsx / data manual lain

        foreach ($data as [$namaProvinsi, $trk, $tjk, $tkr, $tkira, $tbkt]) {
            if ($trk + $tjk + $tkr + $tkira + $tbkt == 0) {
                continue;
            }

            $provinsi = Provinsi::firstOrCreate(['nama_provinsi' => $namaProvinsi]);
            $kabupaten = Kabupaten::firstOrCreate([
                'provinsi_id' => $provinsi->id,
                'nama_kabupaten' => 'Rekap Provinsi',
            ]);

            NeracaBatubara::create([
                'idbb' => $idbb++,
                'tahun_data' => 2024,
                'tahun_neraca' => 2025,
                'nama_objek' => 'Batubara ' . $namaProvinsi,
                'kelas_kalori_id' => $kelasKalori->id,
                'stat_dik_bb_id' => $statOperasi->id,
                'hipbb' => 0,
                'tereka' => $trk,
                'tertunjuk' => $tjk,
                'terukur' => $tkr,
                'total_sd' => $trk + $tjk + $tkr,
                'terkira' => $tkira,
                'terbukti' => $tbkt,
                'total_cad' => $tkira + $tbkt,
                'provinsi_id' => $provinsi->id,
                'kabupaten_id' => $kabupaten->id,
                'remark' => 'Data agregat resmi per provinsi (Tabel 33, Buku Neraca Minerba 2025, data Des 2024) - gabungan seluruh kelas kalori, bukan per titik lokasi individual.',
            ]);
        }
    }
}
