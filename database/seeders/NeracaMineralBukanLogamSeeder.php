<?php

namespace Database\Seeders;

use App\Models\NeracaMineralBukanLogam;
use App\Models\Provinsi;
use App\Models\Kabupaten;
use App\Models\KomoditasBukanLogam;
use App\Models\StatDikBb;
use Illuminate\Database\Seeder;

class NeracaMineralBukanLogamSeeder extends Seeder
{
    /**
     * Data resmi Buku Neraca Sumber Daya dan Cadangan Mineral dan Batubara Indonesia Tahun 2026
     * (data termutakhirkan Desember 2025), diextract & divalidasi dari Tabel 27,28,29,30,31.
     * Semua total per komoditas sudah dicocokkan 1:1 dengan baris "TOTAL" resmi buku.
     *
     * PENTING: nama komoditas di bawah HARUS persis sama dengan `nama_komoditas` di tabel
     * komoditas_bukan_logams (cek KomoditasBukanLogamSeeder kamu) - kalau beda ejaan,
     * where(...)->firstOrFail() bakal melempar exception.
     */
    public function run(): void
    {
        $statOperasi = StatDikBb::where('label', 'Operasi Produksi')->firstOrFail();
        $idbl = 1000;

        $komoditasData = [
            'Batugamping' => [
                ['Aceh', 65, 10477864000, 6099010932, 127255579, 1158331583, 589535122, 1034771509],
                ['Bali', 9, 4982737000, 0, 879551000, 1329500000, 0, 0],
                ['Banten', 14, 60000000, 3149707866, 815761104, 1145333726, 2502272127, 396087549],
                ['Bengkulu', 5, 837088000, 0, 0, 0, 0, 0],
                ['Daerah Istimewa Yogyakarta', 13, 365602000, 15901754, 2660328, 9526772, 8624513, 3746008],
                ['Gorontalo', 26, 0, 32983162690, 0, 0, 0, 0],
                ['Jambi', 4, 8100000, 646380000, 307800000, 288025200, 328792225, 328792225],
                ['Jawa Barat', 46, 431195000, 397352488, 1456049983, 2751854446, 169343521, 1875657112],
                ['Jawa Tengah', 59, 625302000, 4925661757, 2214911568, 1808071898, 469967430, 702320774],
                ['Jawa Timur', 139, 1227338136, 2297162397, 2076743172, 3282593490, 120990433, 882387743],
                ['Kalimantan Selatan', 59, 24815810000, 2195307530, 2490020151, 682037248, 603074483, 657530217],
                ['Kalimantan Tengah', 10, 448775000, 0, 0, 0, 0, 0],
                ['Kalimantan Timur', 32, 5494901000, 13010506709, 2768124487, 269057802, 296467544, 187771250],
                ['Kalimantan Utara', 6, 1109500000, 0, 0, 239424768, 0, 63673986],
                ['Lampung', 10, 15141000, 228933845, 8835323, 23226210, 10100874, 11810154],
                ['Maluku', 1, 65250000000, 0, 0, 0, 0, 0],
                ['Maluku Utara', 30, 11273072800, 19480239074, 2037287120, 1540688524, 1496953198, 13373674],
                ['Nusa Tenggara Barat', 30, 1116263000, 62110015, 81918000, 66034648, 32514499, 29117246],
                ['Nusa Tenggara Timur', 106, 32504948000, 30462126000, 1519388750, 613861, 0, 604560],
                ['Papua', 38, 19668100000, 168832034, 0, 147142000, 0, 0],
                ['Papua Barat', 60, 271599830000, 5559083000, 0, 0, 0, 0],
                ['Papua Barat Daya', 1, 0, 0, 0, 698210190, 0, 230440190],
                ['Riau', 3, 42986000, 18706756, 0, 0, 0, 0],
                ['Sulawesi Barat', 12, 616375000, 0, 119700000, 0, 581558, 0],
                ['Sulawesi Selatan', 58, 11917791453, 6482484204, 2290727067, 347669439, 2025410249, 77805490],
                ['Sulawesi Tengah', 82, 19898040804, 7424034742, 6007962079, 8729070690, 3649374379, 5550975591],
                ['Sulawesi Tenggara', 55, 34275884000, 37508148448, 3318514363, 2649091319, 1153347917, 829734756],
                ['Sulawesi Utara', 15, 2728715000, 0, 132490000, 0, 34200000, 0],
                ['Sumatera Barat', 102, 83038747000, 23364857569, 271069926, 357100325, 452315931, 1694337786],
                ['Sumatera Selatan', 16, 425707000, 863963815, 687802529, 803088657, 0, 234313076],
                ['Sumatera Utara', 34, 1938406667, 5559206977, 46217436, 30090655, 32792111, 12613965],
            ],
            'Andesit' => [
                ['Aceh', 32, 957700000, 0, 0, 0, 7133950, 0],
                ['Bali', 14, 174590600, 67691890, 0, 0, 0, 0],
                ['Banten', 88, 154172000, 2303440657, 3204366501, 1985686594, 1974539128, 1402906802],
                ['Bengkulu', 2, 26000000, 0, 0, 472950, 0, 398100],
                ['Daerah Istimewa Yogyakarta', 23, 0, 11844266, 24411921, 168497068, 53914716, 28776544],
                ['Gorontalo', 30, 192369876, 2544970665, 52191281, 4438899, 92843846, 4438899],
                ['Jambi', 13, 493765000, 204983837, 153813374, 9702345, 647918, 7307055],
                ['Jawa Barat', 195, 503643500, 1368923718, 1325751346, 3307266962, 752852934, 1079842818],
                ['Jawa Tengah', 84, 1370740000, 468253622, 649131091, 353227907, 84202382, 49578439],
                ['Jawa Timur', 60, 1377945000, 72308734, 81608424, 241762119, 62578896, 95246438],
                ['Kalimantan Barat', 47, 19806125000, 7137100000, 141709279, 0, 74970673, 12699463],
                ['Kalimantan Selatan', 61, 9375560000, 279670955, 186029109, 221115151, 158109591, 134743650],
                ['Kalimantan Tengah', 22, 156300000, 107235114, 41736183, 86560876, 36679536, 74596197],
                ['Kalimantan Timur', 6, 1063255000, 33649000, 176921968, 86002081, 42708557, 44278988],
                ['Kalimantan Utara', 12, 1227000, 162480683, 121815211, 73247280, 12414600, 66270046],
                ['Kepulauan Riau', 2, 134000000, 0, 348400000, 0, 0, 0],
                ['Lampung', 59, 1789532000, 816367000, 309958260, 414277268, 269183838, 178935552],
                ['Maluku Utara', 9, 279170000, 2468700, 2468700, 2643900, 16374636, 16374636],
                ['Nusa Tenggara Barat', 59, 218142000, 23830837, 1016254697, 264110534, 24516364, 14930494],
                ['Nusa Tenggara Timur', 39, 8934284000, 92935000, 68456281, 1582758, 325281, 1335389],
                ['Papua Barat', 2, 13000000, 0, 0, 521388, 156188, 0],
                ['Papua Barat Daya', 3, 0, 0, 47016385, 41741999, 46318391, 8001915],
                ['Riau', 5, 0, 0, 714000000, 112398283, 0, 40174620],
                ['Sulawesi Barat', 27, 446987500, 765660754, 817028763, 694834823, 405376342, 295387973],
                ['Sulawesi Selatan', 33, 1694400000, 127591228, 106529072, 19545714, 94000912, 256050],
                ['Sulawesi Tengah', 11, 1050000, 225477726, 222068192, 716971031, 408951712, 143051261],
                ['Sulawesi Tenggara', 1, 10000000, 0, 0, 0, 0, 0],
                ['Sulawesi Utara', 42, 1957484000, 103389859, 22658172, 10082406, 50072485, 13492658],
                ['Sumatera Barat', 29, 330760000, 977649073, 817486740, 458163234, 161968284, 292530277],
                ['Sumatera Selatan', 19, 6199668000, 523595265, 213670591, 161448774, 159469079, 34677949],
                ['Sumatera Utara', 16, 384250000, 15000000, 6198779, 10210478, 5505505, 11583274],
            ],
            'Lempung' => [
                ['Aceh', 83, 1047274000, 159631971, 134104981, 132075229, 144419842, 65728500],
                ['Bali', 2, 125000, 14075250, 0, 0, 0, 0],
                ['Banten', 6, 420000000, 212874729, 580273482, 117726983, 373634856, 0],
                ['Bengkulu', 1, 20000000, 0, 0, 0, 0, 0],
                ['Daerah Istimewa Yogyakarta', 1, 18500000, 0, 0, 0, 0, 0],
                ['Gorontalo', 1, 0, 750000000, 0, 0, 0, 0],
                ['Jambi', 8, 1646000000, 183643810, 0, 22506000, 0, 0],
                ['Jawa Barat', 9, 44000000, 35000000, 13968089, 451044780, 17755183, 278669165],
                ['Jawa Tengah', 35, 137752000, 521509434, 177522636, 116665320, 9483487, 26399590],
                ['Jawa Timur', 41, 3695620, 155616746, 155970045, 315558369, 3284884, 66663795],
                ['Kalimantan Barat', 32, 671525000, 0, 25000000, 0, 0, 0],
                ['Kalimantan Selatan', 34, 4167934000, 405964183, 619600023, 119488738, 79541682, 58971728],
                ['Kalimantan Tengah', 15, 185933000, 0, 0, 0, 0, 0],
                ['Kalimantan Timur', 31, 1065143000, 52920000, 0, 0, 0, 0],
                ['Kalimantan Utara', 9, 157325000, 0, 0, 0, 0, 0],
                ['Kepulauan Bangka Belitung', 8, 19800000, 24000, 1506940, 25685874, 12000975, 17874707],
                ['Kepulauan Riau', 5, 53391000, 0, 0, 0, 0, 0],
                ['Lampung', 14, 23350000, 0, 0, 0, 0, 0],
                ['Maluku Utara', 4, 345800000, 0, 0, 0, 0, 0],
                ['Nusa Tenggara Barat', 11, 503142000, 0, 8361000, 496855, 0, 109458],
                ['Nusa Tenggara Timur', 28, 2330616895, 1581827000, 0, 0, 0, 0],
                ['Papua', 13, 4080150000, 0, 0, 319000, 0, 0],
                ['Papua Barat', 16, 5316806000, 1625000000, 0, 0, 0, 0],
                ['Riau', 27, 114950000, 45045000, 0, 0, 0, 0],
                ['Sulawesi Barat', 10, 414440000, 0, 0, 0, 0, 0],
                ['Sulawesi Selatan', 40, 327458000, 1283149213, 274825803, 101357317, 95825620, 16887053],
                ['Sulawesi Tengah', 12, 1891508600, 0, 0, 0, 0, 0],
                ['Sulawesi Tenggara', 10, 5810116000, 0, 0, 0, 0, 0],
                ['Sulawesi Utara', 8, 980200000, 6262000, 0, 0, 0, 0],
                ['Sumatera Barat', 43, 10387209000, 5191957, 42178757, 62788019, 10412796, 41708382],
                ['Sumatera Selatan', 50, 47670967350, 2604409279, 11299996, 32142415, 21635578, 16618887],
                ['Sumatera Utara', 12, 1147610000, 0, 0, 0, 0, 0],
            ],
            'Felspar' => [
                ['Aceh', 15, 552010000, 1376524500, 0, 0, 0, 0],
                ['Bali', 1, 25000000, 0, 0, 0, 0, 0],
                ['Banten', 6, 80000, 0, 2800000, 0, 0, 0],
                ['Daerah Istimewa Yogyakarta', 1, 500000, 0, 0, 0, 0, 0],
                ['Gorontalo', 1, 0, 2500000, 0, 0, 0, 0],
                ['Jambi', 9, 0, 481100000, 0, 0, 0, 0],
                ['Jawa Barat', 5, 0, 13000000, 0, 3829241, 0, 515186],
                ['Jawa Tengah', 27, 450000, 128423089, 169535876, 138211428, 51021571, 65037980],
                ['Jawa Timur', 21, 866542000, 29673503, 20453493, 12625904, 3700446, 5995010],
                ['Kalimantan Barat', 4, 1011600000, 0, 1692000, 0, 0, 0],
                ['Lampung', 9, 4200000, 20390000, 54221679, 30100341, 24051924, 17079483],
                ['Maluku Utara', 8, 1362862286, 0, 0, 0, 0, 0],
                ['Nusa Tenggara Barat', 1, 0, 0, 24675300, 0, 0, 0],
                ['Nusa Tenggara Timur', 12, 264975000, 22932800, 155625000, 0, 0, 0],
                ['Sulawesi Barat', 13, 727540000, 0, 0, 0, 0, 0],
                ['Sulawesi Selatan', 5, 105964000, 6667199, 0, 1500000, 0, 0],
                ['', 3, 40822000, 0, 215000000, 0, 0, 0],
                ['Sumatera Barat', 8, 1144530000, 0, 0, 0, 0, 0],
                ['Sumatera Selatan', 19, 36268000, 2216000, 1484800, 1299200, 1484800, 1299200],
                ['Sumatera Utara', 18, 292337000, 2361450000, 11033783, 0, 10677548, 0],
            ],
            'Pasir Kuarsa' => [
                ['Aceh', 16, 57350000, 1248000, 0, 0, 0, 0],
                ['Banten', 29, 50000000, 318625871, 328523220, 321478736, 333990506, 157637000],
                ['Jawa Barat', 12, 4184000, 65698578, 51764328, 52639611, 39535815, 39446277],
                ['Jawa Tengah', 12, 20000000, 19006962, 67071726, 3272790, 17995608, 2047595],
                ['Jawa Timur', 31, 13513400, 434024307, 18830446, 18849417, 8464969, 5147257],
                ['Kalimantan Barat', 47, 1722587500, 0, 284250000, 56600000, 0, 0],
                ['Kalimantan Selatan', 37, 134128000, 316042982, 571465946, 365379553, 86638343, 676002605],
                ['Kalimantan Tengah', 117, 220767400, 8642993544, 6481969118, 4813996854, 3251503826, 2079700136],
                ['Kalimantan Timur', 56, 896857000, 64050000, 0, 0, 0, 0],
                ['Kalimantan Utara', 14, 61160000, 0, 0, 0, 0, 0],
                ['Kepulauan Bangka Belitung', 78, 569310100, 44863620, 125024025, 296068442, 200975228, 201319370],
                ['Kepulauan Riau', 60, 231700000, 762013956, 548255371, 125360579, 183438666, 23019970],
                ['Lampung', 18, 98950000, 5737153, 935510, 9499250, 479870, 8889054],
                ['Nusa Tenggara Barat', 1, 83000, 0, 0, 0, 0, 0],
                ['Nusa Tenggara Timur', 3, 447500000, 0, 0, 0, 0, 0],
                ['Papua Barat', 2, 1100000, 0, 0, 0, 0, 0],
                ['Riau', 25, 221235500, 65450000, 58850000, 5405000, 0, 0],
                ['Sulawesi Selatan', 20, 101030000, 262875614, 287674501, 73199826, 127317725, 3139966],
                ['Sulawesi Tengah', 5, 34370000, 0, 0, 0, 0, 0],
                ['Sulawesi Tenggara', 26, 4950812000, 435645048, 334033018, 629411978, 140756853, 208573767],
                ['Sumatera Barat', 14, 11903500000, 1300000, 780000, 53331235, 0, 46590760],
                ['Sumatera Selatan', 10, 100200000, 1650000, 0, 0, 45300937, 0],
                ['Sumatera Utara', 11, 1474940000, 986677, 0, 0, 1523088, 0],
            ],
        ];
        // kolom tiap baris: [provinsi, jumlah_lokasi, hipotetik, tereka, tertunjuk, terukur, terkira, terbukti]

        foreach ($komoditasData as $namaKomoditas => $rows) {
            $komoditas = KomoditasBukanLogam::where('nama_komoditas', $namaKomoditas)->firstOrFail();

            foreach ($rows as $row) {
                [$namaProvinsi, $jumlahLokasi, $hip, $trk, $tjk, $tkr, $tkira, $tbkt] = $row;

                if ($jumlahLokasi == 0 && $trk + $tjk + $tkr == 0) {
                    continue; // skip provinsi yang datanya kosong
                }

                $provinsi = Provinsi::firstOrCreate(['nama_provinsi' => $namaProvinsi]);
                $kabupaten = Kabupaten::firstOrCreate([
                    'provinsi_id' => $provinsi->id,
                    'nama_kabupaten' => 'Rekap Provinsi',
                ]);

                NeracaMineralBukanLogam::create([
                    'idbl' => $idbl++,
                    'tahun_data' => 2025,
                    'tahun_neraca' => 2026,
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
                    'remark' => 'Data agregat resmi per provinsi (Buku Neraca Minerba 2026) — bukan per titik lokasi individual.',
                ]);
            }
        }
    }
}
