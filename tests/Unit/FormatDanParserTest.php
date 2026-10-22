<?php

/*
|--------------------------------------------------------------------------
| Tes unit murni (tanpa database): pembaca angka/tabel dari Excel dan pemformat angka Indonesia.
|--------------------------------------------------------------------------
| Jalankan:  php artisan test --filter=FormatDanParser
|            (atau seluruh file:  php artisan test tests/Unit/FormatDanParserTest.php)
*/

use App\Support\Angka;
use App\Support\TabelParser;

/* ------------------------------------------------------------------ */
/* TabelParser::keAngka                                                */
/* ------------------------------------------------------------------ */

it('keAngka membaca angka format Indonesia maupun titik-desimal', function (string $teks, float $angka) {
    expect(TabelParser::keAngka($teks))->toEqualWithDelta($angka, 0.0000001);
})->with([
    'koma desimal' => ['436,55', 436.55],
    'ribuan + koma desimal' => ['1.234,56', 1234.56],
    'ribuan saja (1.234 = seribu dua ratus tiga puluh empat)' => ['1.234', 1234.0],
    'ribuan bertingkat' => ['12.345.678', 12345678.0],
    'titik desimal' => ['436.55', 436.55],
    'bilangan bulat' => ['42', 42.0],
    'negatif' => ['-5,5', -5.5],
    'nol' => ['0', 0.0],
    'ada spasi' => [' 7 ', 7.0],
]);

it('keAngka mengembalikan null untuk yang bukan angka', function (string $teks) {
    expect(TabelParser::keAngka($teks))->toBeNull();
})->with(['abc', '', '-', '1,2,3', '1.2.3', '12,', 'n/a']);

/* ------------------------------------------------------------------ */
/* TabelParser::dariTeks / keTeks                                      */
/* ------------------------------------------------------------------ */

function unitTeksTabel(): string
{
    return implode("\n", [
        implode("\t", ['Provinsi', 'SD', 'Cadangan']),
        implode("\t", ['Kalimantan Timur', '1.234,56', '10,5']),
        implode("\t", ['TOTAL', '1.234,56', '10,5']),
    ]);
}

it('dariTeks menentukan tipe kolom, jumlah desimal, dan gaya baris total', function () {
    $hasil = TabelParser::dariTeks(unitTeksTabel());

    expect(array_column($hasil['kolom'], 't'))->toBe(['text', 'num', 'num']);
    expect($hasil['kolom'][1]['d'])->toBe(2);
    expect($hasil['kolom'][2]['d'])->toBe(1);
    expect($hasil['baris'][0]['c'][1])->toBe(1234.56);
    expect($hasil['baris'][0]['s'])->toBe('');
    expect($hasil['baris'][1]['s'])->toBe('total');
});

it('keTeks mengembalikan teks yang sama (bolak-balik tanpa perubahan)', function () {
    $hasil = TabelParser::dariTeks(unitTeksTabel());

    expect(TabelParser::keTeks($hasil['kolom'], $hasil['baris']))->toBe(unitTeksTabel());
});

it('"-" dan sel kosong pada kolom angka dibaca 0, lalu ditulis kembali sebagai "-"', function () {
    $teks = "Nama\tNilai\nA\t-\nB\t5,5";
    $hasil = TabelParser::dariTeks($teks);

    expect($hasil['kolom'][1]['t'])->toBe('num');
    expect($hasil['baris'][0]['c'][1])->toBe(0.0);
    expect(TabelParser::keTeks($hasil['kolom'], $hasil['baris']))->toBe($teks);
});

it('kolom yang ada isi non-angka dianggap teks', function () {
    $hasil = TabelParser::dariTeks("Nama\tKet\nA\tbaik\nB\t5");

    expect($hasil['kolom'][1]['t'])->toBe('text');
});

it('baris berawalan TOTAL / JUMLAH / GRAND TOTAL otomatis bergaya total, tapi bukan kata lain', function (string $label, string $gaya) {
    $hasil = TabelParser::dariTeks("Nama\tNilai\n{$label}\t1");

    expect($hasil['baris'][0]['s'])->toBe($gaya);
})->with([
    ['TOTAL', 'total'],
    ['Total Nasional', 'total'],
    ['Jumlah', 'total'],
    ['GRAND TOTAL', 'total'],
    ['Totalitas', ''],
    ['Kalimantan', ''],
]);

it('gaya sub/child dipertahankan kalau label baris di posisi yang sama tidak berubah', function () {
    $lama = [
        ['c' => ['A', 1], 's' => 'sub'],
        ['c' => ['X', 2], 's' => 'child'],
    ];

    $hasil = TabelParser::dariTeks("Nama\tNilai\nA\t1\nB\t2", $lama);

    expect($hasil['baris'][0]['s'])->toBe('sub');   // label A sama -> gaya tetap
    expect($hasil['baris'][1]['s'])->toBe('');      // label berubah X -> B -> gaya dilepas
});

it('kolom jumlah (int) yang nilainya >= 1.000 tetap tersimpan sebagai ANGKA, bukan teks "1.140"', function () {
    $kolomLama = [['l' => 'Provinsi', 't' => 'text'], ['l' => 'Jumlah Neraca', 't' => 'int']];

    $hasil = TabelParser::dariTeks("Provinsi\tJumlah Neraca\nA\t1.140\nB\t3\nC\t-", null, $kolomLama);

    expect($hasil['kolom'][1]['t'])->toBe('int');
    expect($hasil['baris'][0]['c'][1])->toBe(1140.0);
    expect($hasil['baris'][1]['c'][1])->toBe(3.0);
});

it('keTeks dan dariTeks tidak merusak kolom jumlah (int) yang >= 1.000', function () {
    $kolom = [['k' => 'c1', 'l' => 'Provinsi', 't' => 'text'], ['k' => 'c2', 'l' => 'Jumlah Neraca', 't' => 'int']];
    $baris = [['c' => ['A', 1140.0], 's' => ''], ['c' => ['B', 1683.0], 's' => '']];

    $teks = TabelParser::keTeks($kolom, $baris);
    expect($teks)->toBe("Provinsi\tJumlah Neraca\nA\t1.140\nB\t1.683");

    $hasil = TabelParser::dariTeks($teks, $baris, $kolom);
    expect($hasil['baris'][0]['c'][1])->toBe(1140.0);
    expect($hasil['baris'][1]['c'][1])->toBe(1683.0);
});

it('dariTeks menerima akhir baris Windows (CRLF)', function () {
    $hasil = TabelParser::dariTeks("A\tB\r\nx\t1\r\ny\t2");

    expect($hasil['baris'])->toHaveCount(2);
});

it('dariTeks menolak teks yang tidak layak jadi tabel', function (string $teks) {
    expect(fn () => TabelParser::dariTeks($teks))->toThrow(InvalidArgumentException::class);
})->with([
    'kosong' => [''],
    'hanya judul kolom' => ["A\tB"],
    'satu kolom saja (bukan TAB)' => ["Judul\nA"],
    'baris lebih lebar dari judul' => ["A\tB\nx\t1\t2"],
]);

/* ------------------------------------------------------------------ */
/* Angka: format Indonesia (koma desimal, titik ribuan)                */
/* ------------------------------------------------------------------ */

it('Angka::lengkap memakai koma desimal dan titik ribuan', function (mixed $masuk, int $desimal, string $keluar) {
    expect(Angka::lengkap($masuk, $desimal))->toBe($keluar);
})->with([
    'contoh dari pembimbing' => [436.55, 2, '436,55'],
    'angka besar' => [17459896902, 2, '17.459.896.902,00'],
    'teks angka' => ['1234.5', 2, '1.234,50'],
    'null jadi nol' => [null, 2, '0,00'],
    'tanpa desimal' => [7, 0, '7'],
]);

it('Angka::format meringkas ton ke satuan Indonesia, satuan lain ditulis penuh', function (float $angka, string $satuan, array $harapan) {
    expect(Angka::format($angka, $satuan))->toBe($harapan);
})->with([
    'miliar' => [98_000_000_000.0, 'ton', ['98,00', 'Miliar ton']],
    'triliun' => [2_500_000_000_000.0, 'ton', ['2,50', 'Triliun ton']],
    'juta' => [4_250_000.0, 'ton', ['4,25', 'Juta ton']],
    'ribu' => [1_500.0, 'ton', ['1,50', 'Ribu ton']],
    'di bawah seribu' => [500.0, 'ton', ['500,00', 'ton']],
    'satuan bukan ton' => [9144.5, 'MWe', ['9.144,50', 'MWe']],
]);

it('Angka::sumbu menulis label sumbu Y dengan ringkas dan koma desimal', function (float|int $angka, string $label) {
    expect(Angka::sumbu($angka))->toBe($label);
})->with([
    'nol' => [0, '0'],
    'ribuan penuh' => [1000, '1.000'],
    'desimal kecil' => [0.5, '0,5'],
    'desimal dua digit' => [12.25, '12,25'],
    'juta bulat' => [250_000_000, '250 Juta'],
    'juta pecahan' => [1_500_000, '1,5 Juta'],
    'miliar pecahan' => [2_500_000_000, '2,5 Miliar'],
    'triliun bulat' => [3_000_000_000_000, '3 Triliun'],
]);
