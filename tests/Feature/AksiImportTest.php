<?php

/*
|--------------------------------------------------------------------------
| Tes import Excel (Batubara, Mineral Logam, Mineral Bukan Logam, Panas Bumi, Gambut).
|--------------------------------------------------------------------------
| Jalankan:  php artisan test --filter=AksiImport
| File Excel dibuat otomatis di dalam tes (PhpSpreadsheet), jadi tidak butuh file contoh.
| Berdiri sendiri (tidak butuh AksiTest.php / AksiDomainTest.php).
|
| Yang diuji : baris masuk + referensi dibuat + total benar, import ulang tidak menggandakan, sel kosong / "-" / angka
|              format Indonesia, teks bukan angka dan judul kolom salah (dibatalkan semua dengan pesan jelas),
|              baris tanpa ID dan referensi tak dikenal (dilewati + dilaporkan), hak akses.
| Catatan    : memakai template kolom persis seperti di app/Imports/*Import.php, BUKAN file Excel asli KUGI.
*/

use App\Models\IdInstansi;
use App\Models\KlasifikasiTemperatur;
use App\Models\KomoditasBukanLogam;
use App\Models\NeracaBatubara;
use App\Models\NeracaGambut;
use App\Models\NeracaMineralBukanLogam;
use App\Models\NeracaMineralLogam;
use App\Models\NeracaPanasBumi;
use App\Models\Provinsi;
use App\Models\StatDikBb;
use App\Models\StatDikPb;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

uses(RefreshDatabase::class);

beforeEach(function () {
    if (! class_exists(Spreadsheet::class)) {
        $this->markTestSkipped('PhpSpreadsheet tidak terpasang, tes import dilewati.');
    }

    $this->seed();
});

/* ------------------------------------------------------------------ */
/* Peta tiap domain */
/* ------------------------------------------------------------------ */

/**
 *  idHeading   : judul kolom ID                  namaHeading : judul kolom nama objek
 *  angka       : judul kolom angka NOT NULL      cek         : kolom database => nilai yang diharapkan (baris contoh)
 *  kolomWajib  : satu judul kolom yang kalau hilang berarti template salah
 *  uji         : [judul kolom, kolom database, nilai yang diketik sebagai teks, nilai yang diharapkan]
 */
function impCfg(): array
{
    return [
        'batubara' => [
            'rute' => 'admin.batubara', 'email' => 'superbatubara@test.com', 'model' => NeracaBatubara::class,
            'id' => 'idbb', 'idHeading' => 'idbb', 'namaHeading' => 'namobj', 'kolomWajib' => 'klsbb',
            'angka' => ['hipbb', 'tereka', 'tertunjuk', 'terukur', 'total_sd', 'terkira', 'terbukti', 'total_cad'],
            'cek' => ['tereka' => 10.0, 'total_sd' => 35.0, 'total_cad' => 10.0],
            'uji' => ['tereka', 'tereka', '1.234,5', 1234.5],
        ],
        'mineral logam' => [
            'rute' => 'admin.mineral-logam', 'email' => 'superminerallogam@test.com', 'model' => NeracaMineralLogam::class,
            'id' => 'idml', 'idHeading' => 'idlgm', 'namaHeading' => 'namobj', 'kolomWajib' => 'kellgm',
            'angka' => ['bjhhip', 'loghip', 'bjhtrka', 'logtrka', 'bjhtjuk', 'logtjuk', 'bjhtkur', 'logtkur', 'bjhtkira', 'logtkira', 'bjhtbkt', 'logtbkt'],
            'cek' => ['tereka_bijih' => 10.0, 'total_sd_bijih' => 35.0, 'total_sd_logam' => 3.5, 'total_cad_bijih' => 10.0],
            'uji' => ['bjhtrka', 'tereka_bijih', '1.234,5', 1234.5],
        ],
        'mineral bukan logam' => [
            'rute' => 'admin.mineral-bukan-logam', 'email' => 'superminerallogambukan@test.com', 'model' => NeracaMineralBukanLogam::class,
            'id' => 'idbl', 'idHeading' => 'idbl', 'namaHeading' => 'nama_objek', 'kolomWajib' => 'komoditas',
            'angka' => ['hipotetik', 'tereka', 'tertunjuk', 'terukur', 'terkira', 'terbukti'],
            'cek' => ['tereka' => 10.0, 'total_sd' => 35.0, 'total_cad' => 10.0],
            'uji' => ['tereka', 'tereka', '1.234,5', 1234.5],
        ],
        'panas bumi' => [
            'rute' => 'admin.panas-bumi', 'email' => 'superpanasbumi@test.com', 'model' => NeracaPanasBumi::class,
            'id' => 'idpb', 'idHeading' => 'idpb', 'namaHeading' => 'nama_objek', 'kolomWajib' => 'stat_dik',
            'angka' => ['spekulatif', 'hipotetik', 'terduga', 'mungkin', 'terbukti', 'kapasitas_terpasang'],
            'cek' => ['spekulatif' => 100.0, 'total_sd' => 150.0, 'total_cad' => 60.0],
            'uji' => ['spekulatif', 'spekulatif', '1.234,5', 1234.5],
        ],
        'gambut' => [
            'rute' => 'admin.gambut', 'email' => 'supergambut@test.com', 'model' => NeracaGambut::class,
            'id' => 'idgb', 'idHeading' => 'idgb', 'namaHeading' => 'nama_objek', 'kolomWajib' => 'kabupaten',
            'angka' => ['luas_ha', 'volume_juta_m3', 'total_sd'],
            'cek' => ['luas_ha' => 123.45, 'total_sd' => 5.5],
            'uji' => ['luas_ha', 'luas_ha', '1.234,5', 1234.5],
        ],
    ];
}

/** Satu baris Excel contoh yang valid: [judul kolom => isi], urutan = urutan kolom di file. */
function impBaris(string $d, int $id, string $nama, array $ubah = []): array
{
    $dasar = match ($d) {
        'batubara' => [
            'provinsi' => 'Provinsi Impor', 'pulau' => 'Jawa', 'kabupaten' => 'Kabupaten Impor',
            'klsbb' => 'Kelas Impor', 'statdikbb' => 'Status Impor', 'idinstansi' => 'Instansi Impor',
            'idbb' => $id, 'tahun_data' => 2025, 'tahun_neraca' => 2025, 'namobj' => $nama,
            'hipbb' => 1, 'tereka' => 10, 'tertunjuk' => 20, 'terukur' => 5, 'total_sd' => 35,
            'terkira' => 4, 'terbukti' => 6, 'total_cad' => 10,
            'remark' => 'impor uji', 'bujur' => 117.5, 'lintang' => -1.25,
        ],
        'mineral logam' => [
            'provinsi' => 'Provinsi Impor', 'pulau' => 'Jawa', 'kabupaten' => 'Kabupaten Impor',
            'kellgm' => 'Kelompok Impor', 'jnskom' => 'Komoditas Impor', 'statdiklgm' => 'Status Impor', 'idinstansi' => 'Instansi Impor',
            'idlgm' => $id, 'tahun_data' => 2025, 'tahun_neraca' => 2025, 'namobj' => $nama,
            'bjhhip' => 1, 'loghip' => 1, 'bjhtrka' => 10, 'logtrka' => 1, 'bjhtjuk' => 20, 'logtjuk' => 2,
            'bjhtkur' => 5, 'logtkur' => 0.5, 'bjhtkira' => 4, 'logtkira' => 0.4, 'bjhtbkt' => 6, 'logtbkt' => 0.6,
            'remark' => 'impor uji', 'bujur' => 117.5, 'lintang' => -1.25,
        ],
        'mineral bukan logam' => [
            'idbl' => $id, 'nama_objek' => $nama, 'tahun_data' => 2025, 'tahun_neraca' => 2025,
            'komoditas' => KomoditasBukanLogam::query()->value('nama_komoditas'),
            'provinsi' => 'Provinsi Impor', 'kabupaten' => 'Kabupaten Impor',
            'stat_dik' => StatDikBb::query()->value('label'), 'instansi' => IdInstansi::query()->value('label'),
            'hipotetik' => 1, 'tereka' => 10, 'tertunjuk' => 20, 'terukur' => 5, 'terkira' => 4, 'terbukti' => 6,
            'bujur' => 117.5, 'lintang' => -1.25, 'remark' => 'impor uji',
        ],
        'panas bumi' => [
            'idpb' => $id, 'nama_objek' => $nama, 'tahun_data' => 2025, 'tahun_neraca' => 2025,
            'provinsi' => 'Provinsi Impor', 'kabupaten' => 'Kabupaten Impor',
            'stat_dik' => StatDikPb::query()->value('label'), 'instansi' => IdInstansi::query()->value('label'),
            'klasifikasi_temperatur' => KlasifikasiTemperatur::query()->value('label'),
            'temperatur_reservoir' => 250.5, 'spekulatif' => 100, 'hipotetik' => 50,
            'terduga' => 30, 'mungkin' => 20, 'terbukti' => 10, 'kapasitas_terpasang' => 55,
            'bujur' => 117.5, 'lintang' => -1.25, 'remark' => 'impor uji',
        ],
        'gambut' => [
            'idgb' => $id, 'nama_objek' => $nama, 'tahun_data' => 2025, 'tahun_neraca' => 2025,
            'provinsi' => 'Provinsi Impor', 'kabupaten' => 'Kabupaten Impor',
            'nilai_kalori_min' => 1000, 'nilai_kalori_max' => 2000, 'luas_ha' => 123.45,
            'volume_juta_m3' => 67.89, 'total_sd' => 5.5, 'bujur' => 117.5, 'lintang' => -1.25, 'remark' => 'impor uji',
        ],
    };

    return array_merge($dasar, $ubah);
}

/** ID baru yang belum dipakai di domain ini ($urut = 0, 1, 2 untuk beberapa baris). */
function impId(string $d, int $urut = 0): int
{
    $c = impCfg()[$d];

    return (int) $c['model']::withTrashed()->max($c['id']) + 1 + $urut;
}

/** Tulis baris-baris (list of [judul => isi]) menjadi file .xlsx sementara. */
function impXlsx(array $baris): string
{
    $buku = new Spreadsheet;
    $data = [array_keys($baris[0])];
    foreach ($baris as $b) {
        $data[] = array_values($b);
    }

    // strictNullComparison = true: hanya null yang dikosongkan, angka 0 tetap ditulis
    $buku->getActiveSheet()->fromArray($data, null, 'A1', true);

    $sementara = tempnam(sys_get_temp_dir(), 'impor');
    $path = $sementara.'.xlsx';
    rename($sementara, $path);
    (new Xlsx($buku))->save($path);

    return $path;
}

function impUnggah(string $path): UploadedFile
{
    return new UploadedFile($path, 'impor.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);
}

function impAdmin(array $cfg): User
{
    return User::where('email', $cfg['email'])->firstOrFail();
}

/** Kirim file ke route import domain sebagai admin domain tsb. */
function impKirim($test, string $d, array $baris)
{
    $c = impCfg()[$d];

    return $test->actingAs(impAdmin($c))->post(route("{$c['rute']}.import"), ['file' => impUnggah(impXlsx($baris))]);
}

$semuaDomain = array_keys(impCfg());

/* ------------------------------------------------------------------ */
/* JALUR NORMAL */
/* ------------------------------------------------------------------ */

it('import: baris masuk, referensi dibuat, total benar, dan hasilnya dilaporkan', function (string $d) {
    $this->withoutExceptionHandling();

    $c = impCfg()[$d];
    $id = impId($d);

    impKirim($this, $d, [impBaris($d, $id, "Objek Impor {$d}")])
        ->assertRedirect(route("{$c['rute']}.index"))
        ->assertSessionHas('success', fn ($m) => str_contains($m, '1 baris masuk') && str_contains($m, '0 dilewati'));

    $row = $c['model']::where($c['id'], $id)->first();

    expect($row)->not->toBeNull();
    expect($row->nama_objek)->toBe("Objek Impor {$d}");

    foreach ($c['cek'] as $kolom => $harapan) {
        expect((float) $row->{$kolom})->toEqualWithDelta($harapan, 0.001);
    }

    $provinsi = Provinsi::where('nama_provinsi', 'Provinsi Impor')->first();
    expect($provinsi)->not->toBeNull();
    expect((int) $row->provinsi_id)->toBe((int) $provinsi->id);
    expect($row->kabupaten->nama_kabupaten)->toBe('Kabupaten Impor');
})->with($semuaDomain);

it('import ulang file yang sama tidak menggandakan data', function (string $d) {
    $this->withoutExceptionHandling();

    $c = impCfg()[$d];
    $id = impId($d);
    $baris = [impBaris($d, $id, "Objek Impor {$d}")];

    impKirim($this, $d, $baris)->assertSessionHas('success');
    $jumlah = $c['model']::count();

    impKirim($this, $d, $baris)
        ->assertSessionHas('success', fn ($m) => str_contains($m, '0 baris masuk') && str_contains($m, '1 dilewati'));

    expect($c['model']::count())->toBe($jumlah);
    expect($c['model']::where($c['id'], $id)->count())->toBe(1);
})->with($semuaDomain);

it('import: beberapa baris sekaligus masuk semua', function (string $d) {
    $this->withoutExceptionHandling();

    $c = impCfg()[$d];
    $awal = $c['model']::count();
    $baris = [];
    foreach ([0, 1, 2] as $i) {
        $baris[] = impBaris($d, impId($d, $i), "Objek Impor {$d} {$i}");
    }

    impKirim($this, $d, $baris)
        ->assertSessionHas('success', fn ($m) => str_contains($m, '3 baris masuk'));

    expect($c['model']::count())->toBe($awal + 3);
})->with($semuaDomain);

/* ------------------------------------------------------------------ */
/* ISI SEL ANGKA */
/* ------------------------------------------------------------------ */

it('sel angka kosong atau berisi "-" dibaca 0 (tidak error)', function (string $d, mixed $isi) {
    $this->withoutExceptionHandling();

    $c = impCfg()[$d];
    $id = impId($d);
    $ubah = [];
    foreach ($c['angka'] as $judul) {
        $ubah[$judul] = $isi;
    }

    impKirim($this, $d, [impBaris($d, $id, "Objek Kosong {$d}", $ubah)])
        ->assertSessionHas('success', fn ($m) => str_contains($m, '1 baris masuk'));

    $row = $c['model']::where($c['id'], $id)->firstOrFail();

    foreach (array_keys($c['cek']) as $kolom) {
        expect(is_numeric($row->{$kolom}))->toBeTrue("Kolom {$kolom} berisi bukan angka: ".var_export($row->{$kolom}, true));
        expect((float) $row->{$kolom})->toEqualWithDelta(0.0, 0.0001);
    }
})->with(function () {
    $hasil = [];
    foreach (array_keys(impCfg()) as $d) {
        $hasil["{$d}: sel kosong"] = [$d, null];
        $hasil["{$d}: sel \"-\""] = [$d, '-'];
    }

    return $hasil;
});

it('angka format Indonesia yang tertulis sebagai teks ("1.234,5") terbaca benar', function (string $d) {
    $this->withoutExceptionHandling();

    $c = impCfg()[$d];
    [$judul, $kolom, $teks, $harapan] = $c['uji'];
    $id = impId($d);

    impKirim($this, $d, [impBaris($d, $id, "Objek Teks {$d}", [$judul => $teks])])
        ->assertSessionHas('success', fn ($m) => str_contains($m, '1 baris masuk'));

    expect((float) $c['model']::where($c['id'], $id)->firstOrFail()->{$kolom})->toEqualWithDelta($harapan, 0.001);
})->with($semuaDomain);

/* ------------------------------------------------------------------ */
/* FILE BERMASALAH: dibatalkan semua, pesan jelas, tidak ada data setengah jadi */
/* ------------------------------------------------------------------ */

it('teks bukan angka di kolom angka: import dibatalkan semua dengan pesan jelas', function (string $d) {
    $c = impCfg()[$d];
    $idA = impId($d, 0);
    $idB = impId($d, 1);
    $judulAngka = $c['angka'][1] ?? $c['angka'][0];

    impKirim($this, $d, [
        impBaris($d, $idA, "Objek Baik {$d}"),
        impBaris($d, $idB, "Objek Rusak {$d}", [$judulAngka => 'abc']),
    ])->assertSessionHasErrors('file');

    expect(session('errors')->first('file'))->toContain('abc');
    expect($c['model']::whereIn($c['id'], [$idA, $idB])->count())->toBe(0);
})->with($semuaDomain);

it('judul kolom hilang (template salah): pesan menyebut kolomnya, tidak ada data tersimpan', function (string $d) {
    $c = impCfg()[$d];
    $id = impId($d);
    $awal = $c['model']::count();

    $baris = impBaris($d, $id, "Objek Template {$d}");
    unset($baris[$c['kolomWajib']]);

    impKirim($this, $d, [$baris])->assertSessionHasErrors('file');

    expect(session('errors')->first('file'))->toContain($c['kolomWajib']);
    expect($c['model']::count())->toBe($awal);
})->with($semuaDomain);

/* ------------------------------------------------------------------ */
/* BARIS YANG DILEWATI: dilaporkan, baris lain tetap masuk */
/* ------------------------------------------------------------------ */

it('baris tanpa ID dilewati, baris lain tetap masuk, dan dilaporkan', function (string $d) {
    $this->withoutExceptionHandling();

    $c = impCfg()[$d];
    $idA = impId($d);

    impKirim($this, $d, [
        impBaris($d, $idA, "Objek Baik {$d}"),
        impBaris($d, 0, "Objek Tanpa ID {$d}", [$c['idHeading'] => null]),
    ])->assertSessionHas('success', fn ($m) => str_contains($m, '1 baris masuk') && str_contains($m, '1 dilewati'));

    expect($c['model']::where($c['id'], $idA)->exists())->toBeTrue();
    expect($c['model']::where('nama_objek', "Objek Tanpa ID {$d}")->exists())->toBeFalse();
})->with($semuaDomain);

it('referensi yang tidak dikenal: baris dilewati dan dilaporkan, bukan error server', function (string $d, string $judul) {
    $c = impCfg()[$d];
    $idA = impId($d, 0);
    $idB = impId($d, 1);

    impKirim($this, $d, [
        impBaris($d, $idA, "Objek Baik {$d}"),
        impBaris($d, $idB, "Objek Asing {$d}", [$judul => 'Nilai Tidak Ada']),
    ])
        ->assertRedirect(route("{$c['rute']}.index"))
        ->assertSessionHas('success', fn ($m) => str_contains($m, '1 baris masuk') && str_contains($m, '1 dilewati') && str_contains($m, 'Nilai Tidak Ada'));

    expect($c['model']::where($c['id'], $idA)->exists())->toBeTrue();
    expect($c['model']::where($c['id'], $idB)->exists())->toBeFalse();
})->with([
    'mineral bukan logam: komoditas' => ['mineral bukan logam', 'komoditas'],
    'mineral bukan logam: stat_dik' => ['mineral bukan logam', 'stat_dik'],
    'panas bumi: stat_dik' => ['panas bumi', 'stat_dik'],
]);

/* ------------------------------------------------------------------ */
/* HAK AKSES */
/* ------------------------------------------------------------------ */

it('import hanya boleh Super User dan admin domain yang bersangkutan', function (string $d) {
    $c = impCfg()[$d];
    $awal = $c['model']::count();
    $adminLain = $d === 'batubara' ? 'superminerallogam@test.com' : 'superbatubara@test.com';
    $file = fn () => ['file' => impUnggah(impXlsx([impBaris($d, impId($d), "Objek Akses {$d}")]))];

    // tamu dulu (sebelum ada akun yang "login" di tes ini)
    $this->post(route("{$c['rute']}.import"), $file())->assertRedirect(route('login'));

    $this->actingAs(User::where('email', $adminLain)->firstOrFail())
        ->post(route("{$c['rute']}.import"), $file())->assertForbidden();
    $this->actingAs(User::where('email', 'user@test.com')->firstOrFail())
        ->post(route("{$c['rute']}.import"), $file())->assertForbidden();

    expect($c['model']::count())->toBe($awal);

    $this->actingAs(User::where('email', 'admin@test.com')->firstOrFail())
        ->post(route("{$c['rute']}.import"), $file())
        ->assertSessionHas('success', fn ($m) => str_contains($m, '1 baris masuk'));

    expect($c['model']::count())->toBe($awal + 1);
})->with($semuaDomain);
