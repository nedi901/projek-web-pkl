<?php

use App\Support\SelAngka;

it('SelAngka::ke membaca isi sel Excel sebagai angka', function (mixed $isi, float $harapan) {
    expect(SelAngka::ke($isi))->toEqualWithDelta($harapan, 0.0000001);
})->with([
    'null' => [null, 0.0],
    'kosong' => ['', 0.0],
    'spasi' => ['   ', 0.0],
    'strip' => ['-', 0.0],
    'bilangan bulat' => [5, 5.0],
    'desimal' => [2.5, 2.5],
    'teks angka' => ['42', 42.0],
    'format Indonesia' => ['1.234,5', 1234.5],
    'koma desimal' => ['436,55', 436.55],
    'titik desimal' => ['436.55', 436.55],
]);

it('SelAngka::keAtauNull mengembalikan null untuk sel kosong atau "-"', function (mixed $isi) {
    expect(SelAngka::keAtauNull($isi))->toBeNull();
})->with([null, '', '-', '  ']);

it('SelAngka::keAtauNull tetap membaca angka', function () {
    expect(SelAngka::keAtauNull(117.5))->toBe(117.5);
    expect(SelAngka::keAtauNull('-1,25'))->toEqualWithDelta(-1.25, 0.0000001);
});

it('SelAngka menolak teks yang bukan angka dengan pesan yang menyebut isinya', function (string $isi) {
    expect(fn () => SelAngka::ke($isi))->toThrow(InvalidArgumentException::class);
})->with(['abc', 'n/a', '1,2,3', '12 ton']);
