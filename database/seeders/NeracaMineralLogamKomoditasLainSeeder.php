<?php

namespace Database\Seeders;

use App\Models\NeracaMineralLogam;
use App\Models\Provinsi;
use App\Models\Kabupaten;
use App\Models\KomoditasLogam;
use App\Models\StatDikBb;
use Illuminate\Database\Seeder;

class NeracaMineralLogamKomoditasLainSeeder extends Seeder
{
    /**
     * Data resmi Buku Neraca Sumber Daya dan Cadangan Mineral dan Batubara Indonesia Tahun 2026
     * (data termutakhirkan Desember 2025), diextract & divalidasi dari Tabel 5,6,7,11,12,15,16,17,19.
     * Setiap total per komoditas/provinsi sudah dicocokkan 1:1 dengan baris "Jumlah/Total" resmi buku.
     *
     * PENTING: nama komoditas di bawah HARUS persis sama dengan `nama_komoditas` di tabel
     * komoditas_logams (cek KomoditasLogamSeeder kamu). Kalau beda penulisan (mis. "Emas" vs
     * "Emas Primer"), where(...)->firstOrFail() di bawah akan melempar exception - sesuaikan dulu.
     */
    public function run(): void
    {
        $statOperasi = StatDikBb::where('label', 'Operasi Produksi')->firstOrFail();
        $idml = 2000; // lanjut dari range Nikel (1000-1999) biar ga bentrok

        $idml = $this->seedStandard($idml, $statOperasi);
        $idml = $this->seedLogamTanahJarang($idml, $statOperasi);
    }

    /**
     * Handle 8 komoditas dengan struktur standar (Tereka/Tertunjuk/Terukur -> SD,
     * Terkira/Terbukti -> Cadangan, semua di-split Bijih/Logam).
     * Baris: [provinsi, jumlah_lokasi, trkB, trkL, tjkB, tjkL, tkrB, tkrL, tkiraB, tkiraL, tbktB, tbktL]
     */
    private function seedStandard(int $idml, StatDikBb $statOperasi): int
    {
        $komoditasData = [
            'Tembaga' => [
                ['Aceh', 5, 958400000, 2907600, 37000000, 244800, 28000000, 201600, 24700000, 87738, 28600000, 151650],
                ['Banten', 5, 2043119, 46, 1093041, 26, 90712000, 482, 960072, 14, 0, 0],
                ['Bengkulu', 2, 0, 0, 0, 0, 778000, 3039, 0, 0, 0, 0],
                ['Gorontalo', 13, 218530661, 911786, 32824385, 148333, 334825954, 1259463, 105400000, 737800, 0, 0],
                ['Jawa Barat', 7, 8741039, 29182, 11250000, 41625, 0, 0, 0, 0, 0, 0],
                ['Jawa Tengah', 2, 61915000, 86527, 3080000, 2772, 21590000, 32385, 0, 0, 0, 0],
                ['Jawa Timur', 4, 1417200000, 5940800, 372100000, 2269810, 4915015, 66360, 11797000, 90095, 662000, 15491],
                ['Kalimantan Barat', 1, 0, 0, 8731198, 0, 0, 0, 0, 0, 0, 0],
                ['Kalimantan Tengah', 11, 2841803837, 3527147, 35837305, 173005, 21642000, 162230, 27767000, 202699, 0, 0],
                ['Maluku', 4, 45320000, 47420, 43248000, 131442, 8700000, 188400, 3940000, 58200, 4900000, 61200],
                ['Maluku Utara', 6, 116596250, 312587, 2410000, 809, 71632090, 117514, 2340000, 802, 0, 0],
                ['Nusa Tenggara Barat', 5, 2693750000, 11188125, 2687000000, 14856600, 661000000, 2192500, 755000000, 2265100, 1485000000, 5462300],
                ['Nusa Tenggara Timur', 2, 0, 0, 1248000, 28656, 0, 0, 0, 0, 0, 0],
                ['Papua Tengah', 16, 862742000, 2634153, 2575077000, 17901108, 479057000, 3228066, 1100648000, 10298701, 448197000, 5441003],
                ['Sulawesi Barat', 1, 47366000, 0, 281254000, 0, 214714000, 0, 0, 0, 214714000, 0],
                ['Sulawesi Selatan', 6, 6050000, 192500, 48320284, 300607, 8279133, 32828, 46250500, 292400, 8215152, 40944],
                ['Sulawesi Tengah', 3, 0, 0, 8000000, 40000, 0, 0, 0, 0, 0, 0],
                ['Sumatera Barat', 9, 81647148, 118968, 635443, 4702, 0, 0, 0, 0, 0, 0],
                ['Sumatera Selatan', 2, 100000, 29, 0, 0, 1760000, 14080, 0, 0, 0, 0],
                ['Sumatera Utara', 3, 178000, 551, 800000, 3600, 0, 0, 0, 0, 0, 0],
            ],
            'Emas Primer' => [
                ['Aceh', 10, 493110000, 335, 69009414, 67, 38298448, 53, 26885585, 5, 29258308, 4],
                ['Banten', 18, 655608009, 116, 462534377, 30, 1238000, 9, 74558405, 20, 96800, 2],
                ['Bengkulu', 10, 18115539, 14, 48349711, 62, 3813900, 54, 1314440, 3, 0, 0],
                ['Gorontalo', 28, 478473661, 288, 218065072, 187, 228968577, 98, 156790000, 72, 48045969, 123],
                ['Jambi', 4, 24453643, 28, 0, 0, 0, 0, 0, 0, 0, 0],
                ['Jawa Barat', 25, 4727108, 17, 21507597, 65, 5910233, 19, 7119497, 40, 89599, 1],
                ['Jawa Tengah', 3, 8025000, 7, 10600000, 10, 13500000, 12, 8600000, 7, 0, 0],
                ['Jawa Timur', 14, 232883187, 872, 155514327, 54, 21319416, 10, 43883110, 25, 10971056, 6],
                ['Kalimantan Barat', 16, 3217898, 4, 6490606, 6, 24088597, 50, 4765116, 5, 4191038, 4],
                ['Kalimantan Selatan', 3, 7486755, 14, 2156129, 4, 4581164, 10, 1106000, 3, 3814000, 8],
                ['Kalimantan Tengah', 56, 101277947, 202, 253047198, 209, 26422037, 42, 8391841, 12, 5580985, 6],
                ['Kalimantan Timur', 5, 3862664, 10, 4046585, 10, 303678713, 699, 957078, 2, 2322256, 6],
                ['Kalimantan Utara', 6, 16058216, 8, 18796259, 10, 41420686, 63, 49074594, 66, 1816034, 1],
                ['Lampung', 16, 8220309, 10, 9749702, 22, 1585505, 9, 5386278, 5, 76000, 1],
                ['Maluku', 8, 85516174, 24, 66050117, 28, 10800000, 5, 13030000, 11, 0, 0],
                ['Maluku Utara', 20, 238312293, 70, 66023341, 291, 72582310, 8, 11965518, 34, 803111, 5],
                ['Nusa Tenggara Barat', 23, 3178495743, 849, 2962659982, 1137, 687659185, 256, 761746327, 179, 1497048356, 587],
                ['Nusa Tenggara Timur', 5, 2650835, 2, 2670280, 5, 1200000, 1, 0, 0, 0, 0],
                ['Papua', 1, 1000000, 5, 0, 0, 0, 0, 0, 0, 0, 0],
                ['Papua Tengah', 19, 671928357, 354, 2575077000, 1493, 482526000, 455, 1100648000, 756, 448197000, 391],
                ['Sulawesi Barat', 2, 0, 0, 3278429, 0, 0, 0, 0, 0, 0, 0],
                ['Sulawesi Selatan', 5, 331874726, 2089, 283247623, 1681, 85328463, 464, 277313445, 1611, 600000, 1],
                ['Sulawesi Tengah', 3, 1888000, 5, 13502000, 27, 2591000, 9, 10189000, 24, 1085127, 1],
                ['Sulawesi Tenggara', 4, 2103484, 14, 491614, 1, 491614, 1, 491123, 1, 491123, 1],
                ['Sulawesi Utara', 44, 89879335, 103, 164220467, 169, 53119268, 68, 81348028, 86, 32162467, 60],
                ['Sumatera Barat', 8, 5429750, 15, 6679250, 15, 860170, 5, 203400, 0, 0, 0],
                ['Sumatera Selatan', 17, 4857000, 9, 3386000, 7, 1817000, 1, 9339000, 11, 33000, 0],
                ['Sumatera Utara', 13, 20383375, 43, 92219700, 187, 108520000, 282, 27783730, 60, 59143200, 99],
            ],
            'Perak' => [
                ['Aceh', 5, 197020000, 457, 44350000, 116, 28840000, 55, 26362177, 62, 29258308, 32],
                ['Banten', 16, 245794009, 17580, 61239377, 827, 40807000, 350, 68480186, 533, 96800, 10],
                ['Bengkulu', 7, 1444088, 30, 3105563, 47, 4414900, 469, 3105563, 47, 0, 0],
                ['Gorontalo', 17, 194925106, 515, 91814385, 416, 161529195, 228, 130190000, 186, 44661572, 125],
                ['Jambi', 2, 21271538, 322, 0, 0, 0, 0, 0, 0, 0, 0],
                ['Jawa Barat', 12, 5231691, 162, 6385353, 263, 3140837, 117, 2874587, 113, 752923, 25],
                ['Jawa Tengah', 1, 5025000, 1025, 0, 0, 0, 0, 0, 0, 0, 0],
                ['Jawa Timur', 10, 79415559, 219, 89087642, 1414, 20440944, 527, 14366197, 297, 9773078, 325],
                ['Kalimantan Barat', 2, 0, 0, 7604113, 76, 0, 5, 7604113, 76, 0, 0],
                ['Kalimantan Selatan', 2, 686756, 2, 2156129, 7, 4581164, 21, 0, 0, 0, 0],
                ['Kalimantan Tengah', 40, 273404784, 28699, 35912043, 1132, 2554250, 35, 5654157, 155, 1000865, 7],
                ['Kalimantan Utara', 4, 980000, 2, 1360000, 4, 1220000, 6, 370000, 3, 0, 0],
                ['Kepulauan Bangka Belitung', 1, 13380000, 800, 12230000, 731, 0, 0, 0, 0, 0, 0],
                ['Lampung', 11, 5114958, 48, 8657326, 92, 1113799, 12, 755701, 9, 5922671, 39],
                ['Maluku', 4, 75926164, 7113, 38430117, 845, 0, 0, 5330000, 178, 0, 0],
                ['Maluku Utara', 12, 2098496, 98, 9215831, 308, 71632090, 3, 7921859, 293, 0, 0],
                ['Nusa Tenggara Barat', 5, 2565125021, 3769, 2361513126, 5544, 671059406, 982, 757677193, 679, 1488703735, 1702],
                ['Nusa Tenggara Timur', 3, 2650835, 160, 2670280, 95, 1200000, 61, 0, 0, 0, 0],
                ['Papua Tengah', 14, 353102000, 951, 2575077000, 10065, 482526000, 1716, 1100648000, 4558, 448197000, 1818],
                ['Sulawesi Selatan', 1, 325174726, 51422, 242647623, 40829, 82527103, 10593, 244913445, 38730, 0, 0],
                ['Sulawesi Tengah', 1, 7388000, 44, 47492000, 110, 6091000, 41, 38629000, 292, 4685127, 26],
                ['Sulawesi Utara', 26, 101732300, 347, 188342600, 758, 53627000, 203, 103268087, 408, 32427126, 141],
                ['Sumatera Barat', 8, 1629000, 138, 1090600, 131, 1759574, 214, 576008, 47, 0, 0],
                ['Sumatera Selatan', 9, 4757000, 71, 3204257, 89, 1817000, 251, 1380469, 37, 14887, 1],
                ['Sumatera Utara', 14, 17023000, 139, 78200000, 591, 95300000, 1117, 22800000, 145, 55650000, 599],
            ],
            'Besi Laterit' => [
                ['Aceh', 1, 8295040, 2058299, 0, 0, 0, 0, 0, 0, 0, 0],
                ['Jawa Barat', 1, 500000, 225000, 0, 0, 0, 0, 0, 0, 0, 0],
                ['Kalimantan Barat', 1, 6897375, 598692, 16804938, 1939794, 69351889, 6874159, 69351889, 6874159, 0, 0],
                ['Kalimantan Selatan', 13, 65250997, 18006141, 170107304, 51844819, 224097515, 64010000, 215616391, 69687925, 153841144, 51174724],
                ['Kalimantan Tengah', 7, 0, 0, 82860000, 6884410, 0, 0, 36050000, 2798177, 0, 0],
                ['Lampung', 3, 8000, 5819, 0, 0, 0, 0, 0, 0, 0, 0],
                ['Maluku', 1, 250000, 23419, 600000, 57680, 2290000, 242270.7, 2370000, 250012, 0, 0],
                ['Maluku Utara', 78, 1610633274, 262622186, 949108235, 166383421, 759194840, 129777355, 698681195, 104447022, 451636078, 69402150],
                ['Papua', 5, 0, 0, 0, 0, 40733000, 10757770, 0, 0, 0, 0],
                ['Papua Barat', 13, 354570000, 72958325, 77410000, 15252730, 74108000, 17597791, 49170000, 6900187, 11280000, 1681587],
                ['Sulawesi Selatan', 8, 602996697, 204970502, 48575977, 5976346, 35650000, 4978333, 151619401, 10648324, 163711375, 12369784],
                ['Sulawesi Tengah', 96, 672601339, 118138986, 518820939, 86942530, 327685670, 48455806, 356563560, 63303496, 212067402, 29616336],
                ['Sulawesi Tenggara', 197, 1073482781, 199797190, 1097948974, 209808538, 1081602943, 217481909, 653564307, 133594327, 451078829, 92520789],
            ],
            'Kobal' => [
                ['Aceh', 1, 8295040, 8311, 0, 0, 0, 0, 0, 0, 0, 0],
                ['Kalimantan Selatan', 3, 24128000, 11265, 46305000, 28943, 0, 0, 121655000, 101623, 0, 0],
                ['Maluku', 2, 250000, 53, 600000, 126, 2290000, 641, 2370000, 580, 0, 0],
                ['Maluku Utara', 47, 1152360583, 549429, 723445185, 626723, 624535495, 312890, 542583368, 301566, 483111114, 258120],
                ['Papua', 7, 81000000, 39690, 0, 0, 43613000, 33569, 0, 0, 0, 0],
                ['Papua Barat Daya', 15, 315610000, 107289, 76530000, 32654, 81448000, 64577, 49170000, 23086, 11280000, 6116],
                ['Sulawesi Selatan', 2, 186300000, 11636, 27200000, 2067, 8700000, 536, 147540000, 8353, 146750000, 8330],
                ['Sulawesi Tengah', 32, 157206900, 35849, 91374374, 130789, 38955209, 104312, 115419105, 95147, 41078934, 19344],
                ['Sulawesi Tenggara', 71, 562716325, 404096, 560550971, 1735511, 655243188, 426938, 340401224, 257528, 188459728, 105718],
            ],
            'Bauksit' => [
                ['Kalimantan Barat', 136, 1587362183, 307131070, 2196846678, 400797115, 1733007026, 346342789, 1500093076, 294993155, 806315411, 161055895],
                ['Kalimantan Tengah', 11, 117968109, 18247089, 200237352, 43564790, 83615268, 14585393, 110053567, 24644654, 34090401, 5855803],
                ['Kepulauan Bangka Belitung', 1, 0, 0, 3100000, 368900, 0, 0, 0, 0, 0, 0],
                ['Kepulauan Riau', 46, 592103300, 60589085, 458133556, 52297387, 313376274, 60551880, 191208024, 34385444, 128143243, 25633020],
            ],
            'Timah' => [
                ['Kalimantan Barat', 5, 28261997, 6521, 981262, 6124, 0, 8028, 0, 0, 0, 0],
                ['Kepulauan Bangka Belitung', 359, 1997182830, 516377, 1966730757, 688169, 3159811329, 1203193, 2838055571, 606179, 866036938, 542281],
                ['Kepulauan Riau', 28, 492867316, 52994, 197989257, 57469, 542329599, 131957, 375547611, 66763, 222675532, 62678],
                ['Riau', 5, 4353000, 169, 208269, 5207, 10000, 250, 0, 0, 0, 0],
            ],
            'Besi Primer' => [
                ['Aceh', 13, 992122678, 385597653, 2442825644, 912072679, 86184280, 21301120, 47394481, 22241751, 87188147, 34424786],
                ['Jambi', 9, 2347363, 1455365, 20580054, 10940978, 6025598, 5194902, 10991083, 2158001, 5735001, 1373055],
                ['Jawa Barat', 2, 18000000, 0, 37427111, 0, 0, 0, 0, 0, 0, 0],
                ['Kalimantan Barat', 12, 377700, 35970, 44381728, 13617188, 72908, 43016, 0, 0, 49391366, 27964689],
                ['Kalimantan Selatan', 21, 23962458, 13137711, 23949581, 14036820, 33908174, 17624668, 1903904, 1086549, 1848300, 1054825],
                ['Kalimantan Tengah', 18, 29691241, 12106942, 56040902, 13455075, 49871114, 26225289, 94895101, 49304427, 37229381, 13248605],
                ['Kalimantan Timur', 2, 0, 0, 409659, 0, 18000000, 9900000, 0, 0, 0, 0],
                ['Kepulauan Bangka Belitung', 3, 0, 0, 35905494, 18524953, 0, 0, 0, 0, 0, 0],
                ['Kepulauan Riau', 5, 10422920, 30250, 24114934, 0, 0, 0, 0, 0, 0, 0],
                ['Lampung', 7, 891185, 468942, 788612, 473533, 1261509, 638753, 479869, 240824, 228509, 112433],
                ['Maluku Utara', 41, 541421131, 30219476, 54261495, 20169420, 254371091, 4016465, 349065770, 29310487, 709695, 306737],
                ['Nusa Tenggara Barat', 3, 93506, 0, 1271204, 9554, 523450, 0, 0, 0, 658250, 0],
                ['Nusa Tenggara Timur', 2, 50563280, 28441251, 31425960, 18227695, 18208640, 10947109, 4709528, 2728919, 1748264, 1048958],
                ['Sulawesi Barat', 4, 6372, 190, 0, 0, 0, 0, 0, 0, 0, 0],
                ['Sulawesi Selatan', 6, 50000000, 27590000, 161510060, 21898425, 1648578223, 670559, 3467912, 2046068, 4732000, 0],
                ['Sulawesi Tengah', 2, 3900000, 2145000, 368487933, 0, 0, 0, 368487933, 0, 0, 0],
                ['Sulawesi Utara', 1, 0, 0, 61936951, 25394150, 328092049, 134517740, 40245890, 16500815, 6594520, 2703753],
                ['Sumatera Barat', 22, 55845197, 8517109, 101078033, 6494687, 11146223, 3024435, 55735950, 715954, 28811519, 879200],
                ['Sumatera Selatan', 3, 2400000, 1131840, 5764275, 0, 900000, 0, 1792000, 0, 739200, 0],
            ],
        ];
        // kolom tiap baris: [provinsi, jumlah_lokasi, tereka_bijih, tereka_logam, tertunjuk_bijih, tertunjuk_logam,
        //                     terukur_bijih, terukur_logam, terkira_bijih, terkira_logam, terbukti_bijih, terbukti_logam]

        foreach ($komoditasData as $namaKomoditas => $rows) {
            $komoditas = KomoditasLogam::where('nama_komoditas', $namaKomoditas)->firstOrFail();

            foreach ($rows as $row) {
                [$namaProvinsi, $jumlahLokasi, $trkB, $trkL, $tjkB, $tjkL, $tkrB, $tkrL, $tkiraB, $tkiraL, $tbktB, $tbktL] = $row;

                if ($jumlahLokasi == 0 && $trkB + $tjkB + $tkrB == 0) {
                    continue; // skip provinsi yang datanya kosong
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
                    'nama_objek' => 'Rekap ' . $namaKomoditas . ' ' . $namaProvinsi . ' (' . $jumlahLokasi . ' lokasi)',
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
                    'remark' => 'Data agregat resmi per provinsi (Buku Neraca Minerba 2026) — bukan per titik lokasi individual.',
                ]);
            }
        }

        return $idml;
    }

    /**
     * Logam Tanah Jarang (LTJ) punya struktur BEDA dari 8 komoditas lain:
     * - TIDAK ada kolom jumlah lokasi di buku (Tabel 19), jadi nama_objek tanpa "(N lokasi)"
     * - TIDAK ada data cadangan sama sekali (baru sebatas sumber daya) - field cadangan diisi 0
     * - Nilainya desimal (bukan bulat), disimpan apa adanya
     * Baris: [provinsi, trkB, trkL, tjkB, tjkL, tkrB, tkrL]
     */
    private function seedLogamTanahJarang(int $idml, StatDikBb $statOperasi): int
    {
        $data = [
            ['Sumatera Utara', 1501917, 2.39, 0, 0, 0, 0],
            ['Kepulauan Bangka Belitung', 59807187.5, 28792.78, 5498750, 3316.6, 1821875, 1097.07],
            ['Sulawesi Barat', 67698085.56, 85711.14, 0, 0, 0, 0],
        ];
        // kolom: [provinsi, tereka_bijih, tereka_logam, tertunjuk_bijih, tertunjuk_logam, terukur_bijih, terukur_logam]

        $komoditas = KomoditasLogam::where('nama_komoditas', 'Logam Tanah Jarang')->firstOrFail();

        foreach ($data as $row) {
            [$namaProvinsi, $trkB, $trkL, $tjkB, $tjkL, $tkrB, $tkrL] = $row;

            $provinsi = Provinsi::firstOrCreate(['nama_provinsi' => $namaProvinsi]);
            $kabupaten = Kabupaten::firstOrCreate([
                'provinsi_id' => $provinsi->id,
                'nama_kabupaten' => 'Rekap Provinsi',
            ]);

            NeracaMineralLogam::create([
                'idml' => $idml++,
                'tahun_data' => 2025,
                'tahun_neraca' => 2026,
                'nama_objek' => 'Rekap Logam Tanah Jarang ' . $namaProvinsi,
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
                'terkira_bijih' => 0,
                'terkira_logam' => 0,
                'terbukti_bijih' => 0,
                'terbukti_logam' => 0,
                'total_cad_bijih' => 0,
                'total_cad_logam' => 0,
                'provinsi_id' => $provinsi->id,
                'kabupaten_id' => $kabupaten->id,
                'remark' => 'Data agregat resmi per provinsi (Tabel 19, Buku Neraca Minerba 2026). Baru tersedia data Sumber Daya, belum ada data Cadangan untuk komoditas ini di buku 2026.',
            ]);
        }

        return $idml;
    }
}