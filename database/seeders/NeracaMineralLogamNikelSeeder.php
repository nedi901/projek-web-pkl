<?php

namespace Database\Seeders;

use App\Models\NeracaMineralLogam;
use App\Models\Provinsi;
use App\Models\Kabupaten;
use App\Models\KomoditasLogam;
use App\Models\StatDikBb;
use Illuminate\Database\Seeder;

class NeracaMineralLogamNikelSeeder extends Seeder
{
    public function run(): void
    {
        // Data resmi: Tabel 8, Buku Neraca Minerba Indonesia 2026 (data per Des 2025)
        $data = [
            ['Aceh', 1, 8295040, 51204, 0, 0, 0, 0, 0, 0, 0, 0],
            ['Kalimantan Selatan', 3, 24128000, 180181, 46305000, 303184, 0, 0, 121655000, 651582, 0, 0],
            ['Kalimantan Tengah', 1, 0, 0, 21730643, 275327, 0, 0, 9780719, 117075, 0, 0],
            ['Kalimantan Timur', 1, 0, 0, 1202428, 11784, 0, 0, 0, 0, 0, 0],
            ['Maluku', 1, 250000, 2803, 600000, 6826, 2290000, 27319, 2370000, 28537, 0, 0],
            ['Maluku Utara', 91, 2470729612, 38238945, 1551433769, 15315687, 1310174948, 13394400, 981844944, 10055785, 712048201, 7439511],
            ['Papua', 6, 216000000, 1663740, 93000000, 814680, 49240000, 386154, 0, 0, 0, 0],
            ['Papua Barat Daya', 13, 273600000, 3661502, 105715600, 915327, 98979600, 865546, 53470000, 631777, 14780000, 136508],
            ['Papua Tengah', 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
            ['Sulawesi Selatan', 11, 284998955, 1876329, 82267215, 602374, 72662647, 608220, 182158356, 950672, 172325504, 997334],
            ['Sulawesi Tengah', 135, 1772757243, 16700934, 1709766900, 17838054, 512328871, 5688169, 732084567, 7699384, 311835875, 3334144],
            ['Sulawesi Tenggara', 251, 3091482934, 31583393, 2063167143, 21419593, 1512978119, 15667026, 1449569955, 14016075, 565699745, 6413926],
        ];
        // kolom: [provinsi, jumlah_lokasi, tereka_bijih, tereka_logam, tertunjuk_bijih, tertunjuk_logam,
        //          terukur_bijih, terukur_logam, terkira_bijih, terkira_logam, terbukti_bijih, terbukti_logam]

        $komoditas = KomoditasLogam::where('nama_komoditas', 'Nikel')->firstOrFail();
        $statOperasi = StatDikBb::where('label', 'Operasi Produksi')->firstOrFail();

        $idml = 1000; // mulai dari 1000 biar ga bentrok sama data manual lain

        foreach ($data as $row) {
            [$namaProvinsi, $jumlahLokasi, $trkB, $trkL, $tjkB, $tjkL, $tkrB, $tkrL, $tkiraB, $tkiraL, $tbktB, $tbktL] = $row;

            if ($jumlahLokasi == 0 && $trkB + $tjkB + $tkrB == 0) {
                continue; // skip provinsi yang datanya kosong (Papua Tengah)
            }

            $provinsi = Provinsi::firstOrCreate(['nama_provinsi' => $namaProvinsi]);
            $kabupaten = Kabupaten::firstOrCreate([
                'provinsi_id' => $provinsi->id,
                'nama_kabupaten' => 'Rekap Provinsi',
            ]);

            NeracaMineralLogam::create([
                'idml' => $idml++,
                'tahun_data' => 2025,
                'tahun_neraca' => 2026,
                'nama_objek' => 'Rekap Nikel ' . $namaProvinsi . ' (' . $jumlahLokasi . ' lokasi)',
                'komoditas_logam_id' => $komoditas->id,
                'stat_dik_bb_id' => $statOperasi->id,
                'hipotetik_bijih' => 0,
                'hipotetik_logam' => 0,
                'tereka_bijih' => $trkB,
                'tereka_logam' => $trkL,
                'tertunjuk_bijih' => $tjkB,
                'tertunjuk_logam' => $tjkL,
                'terukur_bijih' => $tkrB,
                'terukur_logam' => $tkrL,
                'total_sd_bijih' => $trkB + $tjkB + $tkrB,
                'total_sd_logam' => $trkL + $tjkL + $tkrL,
                'terkira_bijih' => $tkiraB,
                'terkira_logam' => $tkiraL,
                'terbukti_bijih' => $tbktB,
                'terbukti_logam' => $tbktL,
                'total_cad_bijih' => $tkiraB + $tbktB,
                'total_cad_logam' => $tkiraL + $tbktL,
                'provinsi_id' => $provinsi->id,
                'kabupaten_id' => $kabupaten->id,
                'remark' => 'Data agregat resmi per provinsi (Tabel 8, Buku Neraca Minerba 2026) — bukan per titik lokasi individual.',
            ]);
        }
    }
}