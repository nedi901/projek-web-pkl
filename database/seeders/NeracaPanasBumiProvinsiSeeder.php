<?php

namespace Database\Seeders;

use App\Models\NeracaPanasBumi;
use App\Models\Provinsi;
use App\Models\Kabupaten;
use App\Models\StatDikPb;
use Illuminate\Database\Seeder;

/**
 * VERSI 2 - gantiin NeracaPanasBumiProvinsiSeeder yang lama.
 *
 * Sumber: Buku Neraca Minerba 2025, Tabel 47 (rekap per provinsi, "Tahun 2023") + Tabel 45
 * (data per-lapangan/WKP individual dari "Hasil Pemutakhiran Basis Data Lokasi... 2024").
 *
 * 13 provinsi yang punya lapangan WKP terkenal (Kamojang, Sarulla, Ulubelu, Lahendong, dst)
 * dipecah jadi: [row per lapangan asli] + [1 row "Lokasi Lainnya" = sisa dari total resmi
 * provinsi dikurangi jumlah semua lapangan yang udah dipecah]. Ini supaya TOTAL per provinsi
 * TETEP SAMA PERSIS kayak angka resmi Tabel 47 (udah divalidasi, remainder-nya dicek semua
 * positif, gak ada yang minus).
 *
 * 18 provinsi sisanya (gak ada lapangan terkenal yang ke-extract) tetap 1 row rekap kayak
 * versi sebelumnya.
 *
 * CATATAN TAHUN: Tabel 47 berlabel "Tahun 2023" di buku, tapi di-set tahun_data=2024 (keputusan
 * user, sesuai aturan pembimbing data 2024-2025; edisi buku = data Desember 2024, Tabel 46 sebelahnya
 * berlabel 2024). Label asli 2023 dicatat di kolom remark tiap baris.
 *
 * CATATAN JUJUR: Tabel 45 di buku SAMA SEKALI GAK PUNYA KOLOM PROVINSI - provinsi tiap
 * lapangan di bawah ini DIPETAKAN MANUAL dari pengetahuan geografi umum (WKP-WKP yang udah
 * dikenal luas di publikasi energi Indonesia), BUKAN dari buku. Kalau ada yang salah,
 * gampang dikoreksi - tinggal edit array $lapangan di bawah.
 */
class NeracaPanasBumiProvinsiSeeder extends Seeder
{
    public function run(): void
    {
        $statDefault = StatDikPb::where('label', 'Survei Pendahuluan')->firstOrFail();
        $idpb = 3000;

        // ===== 13 provinsi dengan breakdown per-lapangan =====
        $lapangan = [
            ['Seulawah Agam', 'Aceh', 0, 0, 320, 0, 0, 0],
            ['Lau Debukdebuk - Sibayak', 'Sumatera Utara', 0, 0, 30, 16, 30, 13.3],
            ['Sarulla-Silangkitang', 'Sumatera Utara', 0, 0, 0, 0, 110, 0],
            ['Sibual-Buali', 'Sumatera Utara', 0, 0, 229, 0, 51, 418.13],
            ['Namora Ilangit', 'Sumatera Utara', 0, 0, 0, 0, 220, 0],
            ['Sorik Merapi - Sibangor', 'Sumatera Utara', 0, 0, 30, 20, 190, 237.5],
            ['Panti', 'Sumatera Barat', 120, 0, 11, 0, 0, 0],
            ['Muaralaboh', 'Sumatera Barat', 35, 0, 0, 0, 0, 89.25],
            ['Liki - Pinangawan', 'Sumatera Barat', 0, 86, 54, 50, 85, 0],
            ['Kerinci', 'Jambi', 0, 0, 13, 17, 10, 0],
            ['Tambang Sawah', 'Bengkulu', 0, 0, 27, 0, 0, 0],
            ['Bukit Gedung - Hulu Lais', 'Bengkulu', 0, 0, 119, 114, 170, 0],
            ['Bukit Daun - Lebong Simpang', 'Bengkulu', 0, 0, 29, 45, 20, 0],
            ['Rantau Dedap - Segamit', 'Sumatera Selatan', 0, 160, 49, 119, 91, 98.4],
            ['Bukit Lumut Balai', 'Sumatera Selatan', 0, 0, 170, 133, 150, 59.93],
            ['Wai Selabung', 'Sumatera Selatan', 0, 64, 38, 0, 0, 0],
            ['Ulubelu', 'Lampung', 0, 0, 131, 48, 260, 229],
            ['Kawah Ratu - Salak', 'Jawa Barat', 0, 0, 38, 5, 0, 0],
            ['Awibengkok', 'Jawa Barat', 0, 0, 162, 15, 497, 381.97],
            ['Gunung Patuha', 'Jawa Barat', 0, 0, 65, 0, 110, 59.88],
            ['Gunung Papandayan', 'Jawa Barat', 120, 0, 64, 0, 0, 0],
            ['Gunung Masigit - Guntur', 'Jawa Barat', 0, 0, 69, 0, 0, 0],
            ['Kamojang', 'Jawa Barat', 0, 0, 106, 77, 240, 239],
            ['Darajat', 'Jawa Barat', 0, 0, 64, 8, 291, 293.21],
            ['Cigunung', 'Jawa Barat', 0, 0, 110, 0, 0, 0],
            ['Candradimuka', 'Jawa Tengah', 0, 0, 45, 0, 0, 0],
            ['Wae Sano', 'Nusa Tenggara Timur', 0, 106, 46, 0, 0, 0],
            ['Lesugolo', 'Nusa Tenggara Timur', 0, 0, 37, 0, 0, 0],
            ['Lahendong', 'Sulawesi Utara', 0, 0, 67, 32, 95, 123.72],
            ['Tompaso', 'Sulawesi Utara', 0, 0, 123, 76, 55, 0],
            ['Kotamobagu', 'Sulawesi Utara', 0, 0, 160, 0, 0, 0],
            ['Bittuang', 'Sulawesi Selatan', 0, 29, 46, 0, 0, 0],
            ['Kampala - Sinjai', 'Sulawesi Selatan', 0, 3, 0, 0, 0, 0],
            ['Wapsalit - Waeapo', 'Maluku', 0, 0, 31, 30, 0, 0],
        ];

        $sisaLainnya = [
            ['Aceh', 324, 222, 355, 25, 0, 0],
            ['Sumatera Utara', 225, 388, 324, 3, 0, 0],
            ['Sumatera Barat', 290.5, 509, 408, 0, 0, 0],
            ['Jambi', 352, 87, 265, 0, 0, 0],
            ['Bengkulu', 134, 0, 180, 0, 0, 0],
            ['Sumatera Selatan', 225, 6, 245, 0, 0, 0],
            ['Lampung', 375, 28, 832, 225, 0, 0],
            ['Jawa Barat', 720, 507, 910, 5, 317, 257.0],
            ['Jawa Tengah', 85, 271, 665, 130, 275, 72.8],
            ['Nusa Tenggara Timur', 213, 28, 561, 138, 33.5, 24.08],
            ['Sulawesi Utara', 84, 51, 269, 0, 0, 0],
            ['Sulawesi Selatan', 259, 80, 108, 0, 0, 0],
            ['Maluku', 325, 73, 97, 6, 2, 0],
        ];
        // kolom lapangan: [nama, provinsi, spekulatif, hipotetis, mungkin, terduga, terbukti, kapasitas]
        // kolom sisaLainnya: [provinsi, spekulatif, hipotetis, mungkin, terduga, terbukti, kapasitas]

        foreach ($lapangan as [$nama, $namaProvinsi, $spek, $hip, $mungkin, $terduga, $terbukti, $kap]) {
            $provinsi = Provinsi::firstOrCreate(['nama_provinsi' => $namaProvinsi]);
            $kabupaten = Kabupaten::firstOrCreate([
                'provinsi_id' => $provinsi->id,
                'nama_kabupaten' => 'Rekap Provinsi',
            ]);

            NeracaPanasBumi::create([
                'idpb' => $idpb++,
                'tahun_data' => 2024,
                'tahun_neraca' => 2025,
                'nama_objek' => $nama,
                'stat_dik_pb_id' => $statDefault->id,
                'klasifikasi_temperatur_id' => null,
                'spekulatif' => $spek,
                'hipotetik' => $hip,
                'total_sd' => $spek + $hip,
                'terduga' => $terduga,
                'mungkin' => $mungkin,
                'terbukti' => $terbukti,
                'total_cad' => $terduga + $mungkin + $terbukti,
                'kapasitas_terpasang' => $kap,
                'provinsi_id' => $provinsi->id,
                'kabupaten_id' => $kabupaten->id,
                'remark' => 'Data lapangan/WKP individual (Tabel 45, Buku Neraca Minerba 2025, pemutakhiran 2024). Provinsi dipetakan manual dari nama lapangan - cek ulang kalau ragu.',
            ]);
        }

        foreach ($sisaLainnya as [$namaProvinsi, $spek, $hip, $mungkin, $terduga, $terbukti, $kap]) {
            if ($spek + $hip + $mungkin + $terduga + $terbukti <= 0) {
                continue; // provinsi ini semua datanya udah kepake abis di lapangan bernama
            }

            $provinsi = Provinsi::firstOrCreate(['nama_provinsi' => $namaProvinsi]);
            $kabupaten = Kabupaten::firstOrCreate([
                'provinsi_id' => $provinsi->id,
                'nama_kabupaten' => 'Rekap Provinsi',
            ]);

            NeracaPanasBumi::create([
                'idpb' => $idpb++,
                'tahun_data' => 2024, // label asli di buku: 2023 (lihat catatan di remark)
                'tahun_neraca' => 2025,
                'nama_objek' => 'Panas Bumi ' . $namaProvinsi . ' - Lokasi Lainnya',
                'stat_dik_pb_id' => $statDefault->id,
                'klasifikasi_temperatur_id' => null,
                'spekulatif' => $spek,
                'hipotetik' => $hip,
                'total_sd' => $spek + $hip,
                'terduga' => $terduga,
                'mungkin' => $mungkin,
                'terbukti' => $terbukti,
                'total_cad' => $terduga + $mungkin + $terbukti,
                'kapasitas_terpasang' => $kap,
                'provinsi_id' => $provinsi->id,
                'kabupaten_id' => $kabupaten->id,
                'remark' => 'Selisih dari total resmi provinsi (Tabel 47) dikurangi lapangan-lapangan yang udah dipecah di atas - lokasi WKP lain yang belum teridentifikasi namanya secara spesifik. CATATAN TAHUN: tabel sumber berlabel "Tahun 2023" di buku, di-set 2024 agar masuk rentang data 2024-2025 (edisi buku: data termutakhirkan Desember 2024).',
            ]);
        }

        // ===== 18 provinsi lainnya - tetap 1 row rekap (gak ada lapangan terkenal) =====
        $rekapProvinsiLain = [
            ['Riau', 4, 45, 0, 0, 0, 0, 0],
            ['Kepulauan Bangka Belitung', 7, 35, 11, 0, 0, 0, 0],
            ['Banten', 9, 134, 161, 323, 0, 0, 0],
            ['Daerah Istimewa Yogyakarta', 1, 0, 0, 10, 0, 0, 0],
            ['Jawa Timur', 11, 70, 365, 770, 59, 35, 0],
            ['Bali', 6, 70, 21, 104, 110, 30, 0],
            ['Nusa Tenggara Barat', 3, 6, 0, 11, 61.35, 0, 0],
            ['Kalimantan Barat', 5, 65, 0, 0, 0, 0, 0],
            ['Kalimantan Selatan', 3, 49, 1, 0, 0, 0, 0],
            ['Kalimantan Utara', 4, 20, 17, 6, 0, 0, 0],
            ['Kalimantan Timur', 4, 32, 0, 0, 0, 0, 0],
            ['Gorontalo', 5, 129, 11, 20, 0, 0, 0],
            ['Sulawesi Tengah', 30, 391, 70, 227, 0, 0, 0],
            ['Sulawesi Barat', 12, 296, 53, 32, 0, 0, 0],
            ['Sulawesi Tenggara', 13, 200, 36, 78, 0, 0, 0],
            ['Maluku Utara', 15, 190, 7, 379, 0, 0, 0],
            ['Papua Barat', 2, 50, 0, 0, 0, 0, 0],
            ['Papua Barat Daya', 1, 25, 0, 0, 0, 0, 0],
        ];
        // kolom: [provinsi, jumlah_titik, spekulatif, hipotetis, mungkin, terduga, terbukti, kapasitas]

        foreach ($rekapProvinsiLain as [$namaProvinsi, $jumlahTitik, $spek, $hip, $mungkin, $terduga, $terbukti, $kap]) {
            if ($spek + $hip + $mungkin + $terduga + $terbukti == 0) {
                continue;
            }

            $provinsi = Provinsi::firstOrCreate(['nama_provinsi' => $namaProvinsi]);
            $kabupaten = Kabupaten::firstOrCreate([
                'provinsi_id' => $provinsi->id,
                'nama_kabupaten' => 'Rekap Provinsi',
            ]);

            NeracaPanasBumi::create([
                'idpb' => $idpb++,
                'tahun_data' => 2024, // label asli di buku: 2023 (lihat catatan di remark)
                'tahun_neraca' => 2025,
                'nama_objek' => 'Panas Bumi ' . $namaProvinsi . ' (' . $jumlahTitik . ' titik WKP)',
                'stat_dik_pb_id' => $statDefault->id,
                'klasifikasi_temperatur_id' => null,
                'spekulatif' => $spek,
                'hipotetik' => $hip,
                'total_sd' => $spek + $hip,
                'terduga' => $terduga,
                'mungkin' => $mungkin,
                'terbukti' => $terbukti,
                'total_cad' => $terduga + $mungkin + $terbukti,
                'kapasitas_terpasang' => $kap,
                'provinsi_id' => $provinsi->id,
                'kabupaten_id' => $kabupaten->id,
                'remark' => 'Data agregat resmi per provinsi (Tabel 47, Buku Neraca Minerba 2025) - belum ada breakdown per-lapangan yang teridentifikasi. CATATAN TAHUN: tabel sumber berlabel "Tahun 2023" di buku, di-set 2024 agar masuk rentang data 2024-2025 (edisi buku: data termutakhirkan Desember 2024).',
            ]);
        }
    }
}
