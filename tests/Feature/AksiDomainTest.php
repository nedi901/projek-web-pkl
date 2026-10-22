<?php

/*
|--------------------------------------------------------------------------
| Tes form tambah/ubah data per domain (Mineral Logam, Bukan Logam, Panas Bumi, Gambut + Batubara sebagai pembanding).
|--------------------------------------------------------------------------
| Jalankan:  php artisan test --filter=AksiDomain
| File ini berdiri sendiri (tidak butuh AksiTest.php).
|
| Yang diuji : tambah data + total otomatis, ubah data + total dihitung ulang, validasi, kolom angka dikosongkan
|              (yang biasa terjadi di form asli), hak akses, dan dropdown kabupaten.
| Belum diuji: import Excel, tampilan di browser.
*/

use App\Models\IdInstansi;
use App\Models\Kabupaten;
use App\Models\KelasKalori;
use App\Models\KlasifikasiTemperatur;
use App\Models\KomoditasBukanLogam;
use App\Models\KomoditasLogam;
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

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
});

/**
 * Peta tiap domain.
 *  id     : nama kolom ID unik dari buku           ref   : kolom referensi => model (id pertama dipakai)
 *  wajib  : referensi yang WAJIB diisi             angka : isian angka (teks, seperti dikirim form)
 *  total  : kolom => nilai yang diharapkan setelah TAMBAH
 *  ubah   : isian yang diganti saat UBAH           total_ubah : kolom => nilai yang diharapkan setelah UBAH
 */
function domCfg(): array
{
    return [
        'batubara' => [
            'rute' => 'admin.batubara', 'email' => 'superbatubara@test.com', 'model' => NeracaBatubara::class,
            'id' => 'idbb', 'wajib' => 'kelas_kalori_id',
            'ref' => ['kelas_kalori_id' => KelasKalori::class, 'stat_dik_bb_id' => StatDikBb::class, 'id_instansi_id' => IdInstansi::class],
            'angka' => ['tereka' => '10', 'tertunjuk' => '20', 'terukur' => '5.5', 'terkira' => '4', 'terbukti' => '6'],
            'total' => ['total_sd' => 35.5, 'total_cad' => 10.0],
            'ubah' => ['tereka' => '100'], 'total_ubah' => ['total_sd' => 125.5, 'total_cad' => 10.0],
        ],
        'mineral logam' => [
            'rute' => 'admin.mineral-logam', 'email' => 'superminerallogam@test.com', 'model' => NeracaMineralLogam::class,
            'id' => 'idml', 'wajib' => 'komoditas_logam_id',
            'ref' => ['komoditas_logam_id' => KomoditasLogam::class, 'stat_dik_bb_id' => StatDikBb::class, 'id_instansi_id' => IdInstansi::class],
            'angka' => [
                'hipotetik_bijih' => '1', 'hipotetik_logam' => '1',
                'tereka_bijih' => '10', 'tereka_logam' => '1',
                'tertunjuk_bijih' => '20', 'tertunjuk_logam' => '2',
                'terukur_bijih' => '5.5', 'terukur_logam' => '0.5',
                'terkira_bijih' => '4', 'terkira_logam' => '0.4',
                'terbukti_bijih' => '6', 'terbukti_logam' => '0.6',
            ],
            'total' => ['total_sd_bijih' => 35.5, 'total_sd_logam' => 3.5, 'total_cad_bijih' => 10.0, 'total_cad_logam' => 1.0],
            'ubah' => ['tereka_bijih' => '100'],
            'total_ubah' => ['total_sd_bijih' => 125.5, 'total_sd_logam' => 3.5, 'total_cad_bijih' => 10.0],
        ],
        'mineral bukan logam' => [
            'rute' => 'admin.mineral-bukan-logam', 'email' => 'superminerallogambukan@test.com', 'model' => NeracaMineralBukanLogam::class,
            'id' => 'idbl', 'wajib' => 'komoditas_bukan_logam_id',
            'ref' => ['komoditas_bukan_logam_id' => KomoditasBukanLogam::class, 'stat_dik_bb_id' => StatDikBb::class, 'id_instansi_id' => IdInstansi::class],
            'angka' => [
                'hipotetik' => '1', 'tereka' => '10', 'tertunjuk' => '20', 'terukur' => '5.5',
                'terkira' => '4', 'terbukti' => '6', 'bujur' => '117.5', 'lintang' => '-1.25',
            ],
            'total' => ['total_sd' => 35.5, 'total_cad' => 10.0],
            'ubah' => ['tereka' => '100'], 'total_ubah' => ['total_sd' => 125.5, 'total_cad' => 10.0],
        ],
        'panas bumi' => [
            'rute' => 'admin.panas-bumi', 'email' => 'superpanasbumi@test.com', 'model' => NeracaPanasBumi::class,
            'id' => 'idpb', 'wajib' => 'stat_dik_pb_id',
            'ref' => ['stat_dik_pb_id' => StatDikPb::class, 'klasifikasi_temperatur_id' => KlasifikasiTemperatur::class, 'id_instansi_id' => IdInstansi::class],
            'angka' => [
                'temperatur_reservoir' => '250.5', 'spekulatif' => '100', 'hipotetik' => '50',
                'terduga' => '30', 'mungkin' => '20', 'terbukti' => '10',
                'kapasitas_terpasang' => '55', 'bujur' => '117.5', 'lintang' => '-1.25',
            ],
            'total' => ['total_sd' => 150.0, 'total_cad' => 60.0],
            'ubah' => ['spekulatif' => '200'], 'total_ubah' => ['total_sd' => 250.0, 'total_cad' => 60.0],
        ],
        'gambut' => [
            'rute' => 'admin.gambut', 'email' => 'supergambut@test.com', 'model' => NeracaGambut::class,
            'id' => 'idgb', 'wajib' => null,
            'ref' => [],
            'angka' => [
                'nilai_kalori_min' => '1000', 'nilai_kalori_max' => '2000', 'luas_ha' => '123.45',
                'volume_juta_m3' => '67.89', 'total_sd' => '5.5', 'bujur' => '117.5', 'lintang' => '-1.25',
            ],
            // gambut tidak punya total otomatis: nilai yang diketik yang tersimpan
            'total' => ['luas_ha' => 123.45, 'volume_juta_m3' => 67.89, 'total_sd' => 5.5],
            'ubah' => ['luas_ha' => '200'], 'total_ubah' => ['luas_ha' => 200.0, 'total_sd' => 5.5],
        ],
    ];
}

/** Kolom angka NOT NULL (harus jadi 0 kalau dikosongkan) dan kolom opsional (boleh tetap kosong/null). */
function domKolomKosong(string $domain): array
{
    return match ($domain) {
        'batubara' => [['tereka', 'tertunjuk', 'terukur', 'terkira', 'terbukti'], []],
        'mineral logam' => [['hipotetik_bijih', 'hipotetik_logam', 'tereka_bijih', 'tereka_logam', 'tertunjuk_bijih', 'tertunjuk_logam', 'terukur_bijih', 'terukur_logam', 'terkira_bijih', 'terkira_logam', 'terbukti_bijih', 'terbukti_logam'], []],
        'mineral bukan logam' => [['hipotetik', 'tereka', 'tertunjuk', 'terukur', 'terkira', 'terbukti'], ['bujur', 'lintang']],
        'panas bumi' => [['spekulatif', 'hipotetik', 'terduga', 'mungkin', 'terbukti', 'kapasitas_terpasang'], ['temperatur_reservoir', 'bujur', 'lintang']],
        'gambut' => [['luas_ha', 'volume_juta_m3', 'total_sd'], ['nilai_kalori_min', 'nilai_kalori_max', 'bujur', 'lintang']],
    };
}

/** Setelah dikosongkan: kolom wajib = 0, kolom opsional = null. */
function domPeriksaKosong(string $domain, $row): void
{
    [$wajib, $opsional] = domKolomKosong($domain);

    foreach ($wajib as $kolom) {
        expect((float) $row->{$kolom})->toEqualWithDelta(0.0, 0.0001);
    }
    foreach ($opsional as $kolom) {
        expect($row->{$kolom})->toBeNull();
    }
}

function domAdmin(array $cfg): User
{
    return User::where('email', $cfg['email'])->firstOrFail();
}

function domSuper(): User
{
    return User::where('email', 'admin@test.com')->firstOrFail();
}

function domPayload(string $domain, array $ubah = []): array
{
    $c = domCfg()[$domain];

    $dasar = [
        $c['id'] => (int) $c['model']::max($c['id']) + 1,
        'nama_objek' => "Objek Uji {$domain}",
        'tahun_data' => 2025,
        'tahun_neraca' => 2025,
        'provinsi_id' => Provinsi::query()->value('id'),
        'kabupaten_id' => Kabupaten::query()->value('id'),
        'remark' => 'data uji',
    ];

    foreach ($c['ref'] as $kolom => $model) {
        $dasar[$kolom] = $model::query()->value('id');
    }

    return array_merge($dasar, $c['angka'], $ubah);
}

/** @return array{0: array, 1: string} [isian yang diganti, kolom yang harus error] */
function domKasus(string $domain, string $kasus): array
{
    $c = domCfg()[$domain];
    $angkaPertama = array_key_first($c['angka']);

    return match ($kasus) {
        'nama objek kosong' => [['nama_objek' => ''], 'nama_objek'],
        'id sudah ada' => [[$c['id'] => $c['model']::query()->value($c['id'])], $c['id']],
        'tahun bukan angka' => [['tahun_data' => 'dua ribu'], 'tahun_data'],
        'provinsi tidak ada' => [['provinsi_id' => 999999], 'provinsi_id'],
        'kabupaten tidak ada' => [['kabupaten_id' => 999999], 'kabupaten_id'],
        'referensi wajib tidak ada' => [[$c['wajib'] => 999999], $c['wajib']],
        'angka bukan angka' => [[$angkaPertama => 'abc'], $angkaPertama],
    };
}

$semuaDomain = array_keys(domCfg());

$kasusInvalid = [];
foreach ($semuaDomain as $d) {
    foreach (['nama objek kosong', 'id sudah ada', 'tahun bukan angka', 'provinsi tidak ada', 'kabupaten tidak ada', 'referensi wajib tidak ada', 'angka bukan angka'] as $k) {
        if ($k === 'referensi wajib tidak ada' && domCfg()[$d]['wajib'] === null) {
            continue; // Gambut tidak punya referensi wajib
        }
        $kasusInvalid["{$d}: {$k}"] = [$d, $k];
    }
}

/* ------------------------------------------------------------------ */
/* TAMBAH & UBAH                                                       */
/* ------------------------------------------------------------------ */

it('tambah data: tersimpan, total dihitung otomatis, dan tampil di daftar', function (string $d) {
    $c = domCfg()[$d];
    $payload = domPayload($d);

    $this->actingAs(domAdmin($c))
        ->post(route("{$c['rute']}.store"), $payload)
        ->assertSessionHasNoErrors()
        ->assertRedirect(route("{$c['rute']}.index"));

    $row = $c['model']::where('nama_objek', $payload['nama_objek'])->first();

    expect($row)->not->toBeNull();
    expect((int) $row->{$c['id']})->toBe($payload[$c['id']]);

    foreach ($c['total'] as $kolom => $harapan) {
        expect((float) $row->{$kolom})->toEqualWithDelta($harapan, 0.001);
    }

    $this->actingAs(domAdmin($c))
        ->get(route("{$c['rute']}.index", ['search' => $payload['nama_objek']]))
        ->assertOk()
        ->assertSee($payload['nama_objek']);
})->with($semuaDomain);

it('ubah data: perubahan tersimpan dan total dihitung ulang', function (string $d) {
    $c = domCfg()[$d];
    $payload = domPayload($d);
    $admin = domAdmin($c);

    $this->actingAs($admin)->post(route("{$c['rute']}.store"), $payload)->assertSessionHasNoErrors();
    $row = $c['model']::where('nama_objek', $payload['nama_objek'])->firstOrFail();

    $ubah = array_merge($payload, $c['ubah'], ['nama_objek' => $payload['nama_objek'] . ' Diubah']);

    $this->actingAs($admin)
        ->put(route("{$c['rute']}.update", $row), $ubah)
        ->assertSessionHasNoErrors()
        ->assertRedirect(route("{$c['rute']}.index"));

    $row->refresh();
    expect($row->nama_objek)->toBe($payload['nama_objek'] . ' Diubah');

    foreach ($c['total_ubah'] as $kolom => $harapan) {
        expect((float) $row->{$kolom})->toEqualWithDelta($harapan, 0.001);
    }
})->with($semuaDomain);

it('tambah/ubah ditolak kalau isian tidak valid', function (string $d, string $kasus) {
    $c = domCfg()[$d];
    [$ubah, $kolomError] = domKasus($d, $kasus);
    $awal = $c['model']::count();

    $this->actingAs(domAdmin($c))
        ->post(route("{$c['rute']}.store"), domPayload($d, $ubah))
        ->assertSessionHasErrors($kolomError);

    expect($c['model']::count())->toBe($awal);
})->with($kasusInvalid);

it('ubah data dengan ID yang sama dengan miliknya sendiri tidak dianggap dobel', function (string $d) {
    $c = domCfg()[$d];
    $row = $c['model']::query()->firstOrFail();

    $payload = domPayload($d, [
        $c['id'] => $row->{$c['id']},
        'nama_objek' => $row->nama_objek,
        'provinsi_id' => $row->provinsi_id,
        'kabupaten_id' => $row->kabupaten_id,
    ]);

    $this->actingAs(domAdmin($c))
        ->put(route("{$c['rute']}.update", $row), $payload)
        ->assertSessionDoesntHaveErrors($c['id']);
})->with($semuaDomain);

/* ------------------------------------------------------------------ */
/* KOLOM ANGKA DIKOSONGKAN (form asli bisa mengirim teks kosong)       */
/* ------------------------------------------------------------------ */

it('tambah data dengan kolom angka dikosongkan tidak boleh error server', function (string $d) {
    // exception asli (mis. "NOT NULL constraint failed") ditampilkan apa adanya kalau gagal
    $this->withoutExceptionHandling();

    $c = domCfg()[$d];
    $payload = domPayload($d);
    foreach (array_keys($c['angka']) as $kolom) {
        $payload[$kolom] = '';
    }

    $this->actingAs(domAdmin($c))
        ->post(route("{$c['rute']}.store"), $payload)
        ->assertRedirect(route("{$c['rute']}.index"));

    $row = $c['model']::where('nama_objek', $payload['nama_objek'])->first();
    expect($row)->not->toBeNull();
    domPeriksaKosong($d, $row);
})->with($semuaDomain);

it('ubah data dengan kolom angka dikosongkan tidak boleh error server', function (string $d) {
    $this->withoutExceptionHandling();

    $c = domCfg()[$d];
    $payload = domPayload($d);
    $admin = domAdmin($c);

    $this->actingAs($admin)->post(route("{$c['rute']}.store"), $payload);
    $row = $c['model']::where('nama_objek', $payload['nama_objek'])->firstOrFail();

    foreach (array_keys($c['angka']) as $kolom) {
        $payload[$kolom] = '';
    }

    $this->actingAs($admin)
        ->put(route("{$c['rute']}.update", $row), $payload)
        ->assertRedirect(route("{$c['rute']}.index"));

    domPeriksaKosong($d, $row->refresh());
})->with($semuaDomain);

/* ------------------------------------------------------------------ */
/* HAK AKSES & DROPDOWN                                                */
/* ------------------------------------------------------------------ */

it('menambah data hanya boleh Super User dan admin domain yang bersangkutan', function (string $d) {
    $c = domCfg()[$d];
    $awal = $c['model']::count();
    $adminLain = $d === 'batubara' ? 'superminerallogam@test.com' : 'superbatubara@test.com';

    // tamu dulu (sebelum ada akun yang "login" di tes ini)
    $this->post(route("{$c['rute']}.store"), domPayload($d))->assertRedirect(route('login'));
    expect($c['model']::count())->toBe($awal);

    $this->actingAs(User::where('email', $adminLain)->firstOrFail())
        ->post(route("{$c['rute']}.store"), domPayload($d))->assertForbidden();
    $this->actingAs(User::where('email', 'user@test.com')->firstOrFail())
        ->post(route("{$c['rute']}.store"), domPayload($d))->assertForbidden();
    expect($c['model']::count())->toBe($awal);

    // Super User boleh ke semua domain
    $this->actingAs(domSuper())
        ->post(route("{$c['rute']}.store"), domPayload($d))
        ->assertSessionHasNoErrors()
        ->assertRedirect(route("{$c['rute']}.index"));
    expect($c['model']::count())->toBe($awal + 1);
})->with($semuaDomain);

it('dropdown kabupaten hanya berisi kabupaten dari provinsi yang dipilih', function (string $d) {
    $c = domCfg()[$d];
    $provinsiId = Kabupaten::query()->value('provinsi_id');

    $respon = $this->actingAs(domAdmin($c))
        ->get(route("{$c['rute']}.kabupaten", $provinsiId))
        ->assertOk();

    expect(count($respon->json()))->toBe(Kabupaten::where('provinsi_id', $provinsiId)->count());
})->with($semuaDomain);
