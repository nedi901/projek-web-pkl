<?php

/*
|--------------------------------------------------------------------------
| Tes ANGKA di Dashboard dan Grafik: apakah yang tampil sama dengan isi database?
|--------------------------------------------------------------------------
| Jalankan:  php artisan test --filter=AksiAngka
| Berdiri sendiri (tidak butuh tes lain). Angka "yang seharusnya" dihitung langsung dengan DB::table(...)->sum(...)
| (tanpa melewati model/controller aplikasi), lalu dibandingkan dengan yang dikirim aplikasi ke halamannya.
|
| Yang diuji : total tiap domain di Dashboard Super User & User (aturan tahun 2024-2025), data terhapus tidak dihitung,
|              total/klasifikasi/chart/tren di Grafik admin tiap domain, filter Grafik, Grafik user (+filter provinsi),
|              dan "tambah data lewat form -> angka di Dashboard & Grafik naik persis sebesar yang diinput".
| Belum diuji: tampilan visual grafik (bentuk batang/donut) - itu harus dicek di browser.
*/

use App\Models\IdInstansi;
use App\Models\Kabupaten;
use App\Models\KelasKalori;
use App\Models\KlasifikasiTemperatur;
use App\Models\NeracaBatubara;
use App\Models\NeracaGambut;
use App\Models\NeracaMineralBukanLogam;
use App\Models\NeracaMineralLogam;
use App\Models\NeracaPanasBumi;
use App\Models\Provinsi;
use App\Models\StatDikBb;
use App\Models\StatDikPb;
use App\Models\User;
use App\Support\Angka;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
});

/**
 *  label  : nama di Dashboard             sd/cad/faktor : kolom total & pengali satuan di Dashboard
 *  grafik : variabel di halaman Grafik admin => kolom database
 *  grup/batas : pengelompokan & batas baris chart batang di Grafik admin
 *  user   : kode ?domain= di Grafik user
 */
function angCfg(): array
{
    return [
        'batubara' => [
            'label' => 'Batubara', 'tabel' => 'neraca_batubaras', 'model' => NeracaBatubara::class,
            'rute' => 'admin.batubara', 'email' => 'superbatubara@test.com', 'user' => 'batubara',
            'sd' => 'total_sd', 'cad' => 'total_cad', 'faktor' => 1,
            'grup' => 'provinsi_id', 'batas' => 6, 'jumlah' => 'jumlahData',
            'grafik' => ['totalSd' => 'total_sd', 'totalCad' => 'total_cad', 'totalTereka' => 'tereka', 'totalTertunjuk' => 'tertunjuk', 'totalTerukur' => 'terukur'],
        ],
        'mineral logam' => [
            'label' => 'Mineral Logam', 'tabel' => 'neraca_mineral_logams', 'model' => NeracaMineralLogam::class,
            'rute' => 'admin.mineral-logam', 'email' => 'superminerallogam@test.com', 'user' => 'mineral-logam',
            'sd' => 'total_sd_bijih', 'cad' => 'total_cad_bijih', 'faktor' => 1,
            'grup' => 'komoditas_logam_id', 'batas' => 6, 'jumlah' => null,
            'grafik' => ['totalSd' => 'total_sd_bijih', 'totalCad' => 'total_cad_bijih', 'totalHipotetik' => 'hipotetik_bijih', 'totalTereka' => 'tereka_bijih', 'totalTertunjuk' => 'tertunjuk_bijih', 'totalTerukur' => 'terukur_bijih'],
        ],
        'mineral bukan logam' => [
            'label' => 'Mineral Bukan Logam', 'tabel' => 'neraca_mineral_bukan_logams', 'model' => NeracaMineralBukanLogam::class,
            'rute' => 'admin.mineral-bukan-logam', 'email' => 'superminerallogambukan@test.com', 'user' => 'mineral-bukan-logam',
            'sd' => 'total_sd', 'cad' => 'total_cad', 'faktor' => 1,
            'grup' => 'komoditas_bukan_logam_id', 'batas' => null, 'jumlah' => null,
            'grafik' => ['totalSd' => 'total_sd', 'totalCad' => 'total_cad', 'totalHipotetik' => 'hipotetik', 'totalTereka' => 'tereka', 'totalTertunjuk' => 'tertunjuk', 'totalTerukur' => 'terukur'],
        ],
        'panas bumi' => [
            'label' => 'Panas Bumi', 'tabel' => 'neraca_panas_bumis', 'model' => NeracaPanasBumi::class,
            'rute' => 'admin.panas-bumi', 'email' => 'superpanasbumi@test.com', 'user' => 'panas-bumi',
            'sd' => 'total_sd', 'cad' => 'total_cad', 'faktor' => 1,
            'grup' => 'provinsi_id', 'batas' => null, 'jumlah' => null,
            'grafik' => ['totalSd' => 'total_sd', 'totalCad' => 'total_cad', 'totalSpekulatif' => 'spekulatif', 'totalHipotetik' => 'hipotetik', 'totalTerduga' => 'terduga', 'totalMungkin' => 'mungkin', 'totalTerbukti' => 'terbukti', 'totalKapasitas' => 'kapasitas_terpasang'],
        ],
        'gambut' => [
            'label' => 'Gambut', 'tabel' => 'neraca_gambuts', 'model' => NeracaGambut::class,
            'rute' => 'admin.gambut', 'email' => 'supergambut@test.com', 'user' => 'gambut',
            'sd' => 'total_sd', 'cad' => null, 'faktor' => 1_000_000,
            'grup' => 'provinsi_id', 'batas' => null, 'jumlah' => null,
            'grafik' => ['totalSd' => 'total_sd', 'totalLuas' => 'luas_ha', 'totalVolume' => 'volume_juta_m3'],
        ],
    ];
}

function angSuper(): User
{
    return User::where('email', 'admin@test.com')->firstOrFail();
}

function angUser(): User
{
    return User::where('email', 'user@test.com')->firstOrFail();
}

/** Query dasar ke tabel mentah: baris yang sudah dihapus (Histori) tidak dihitung. */
function angTabel(string $tabel)
{
    return DB::table($tabel)->whereNull('deleted_at');
}

/** Aturan pembimbing di Dashboard: hanya tahun 2024-2025 (atau satu tahun yang dipilih). */
function angScope($q, ?string $tahun)
{
    return $tahun ? $q->where('tahun_data', (int) $tahun) : $q->whereBetween('tahun_data', [2024, 2025]);
}

/** Buka Dashboard (sebagai akun yang SUDAH di-actingAs) dan kembalikan domains[] berindeks label. */
function angDashboard($test, string $rute, ?string $tahun = null): array
{
    $respon = $test->get(route($rute, $tahun ? ['tahun' => $tahun] : []))->assertOk();

    return collect($respon->viewData('domains'))->keyBy('label')->all();
}

function angSama(float $a, float $b, float $toleransi = 0.01): bool
{
    return abs($a - $b) <= $toleransi;
}

$semuaDomain = array_keys(angCfg());
$semuaTahun = ['semua tahun' => [null], 'tahun 2024' => ['2024'], 'tahun 2025' => ['2025']];

/* ------------------------------------------------------------------ */
/* DASHBOARD                                                           */
/* ------------------------------------------------------------------ */

it('Dashboard Super User: total tiap domain sama dengan jumlah di database', function (?string $tahun) {
    $this->actingAs(angSuper());
    $domains = angDashboard($this, 'superuser.dashboard', $tahun);
    $salah = [];

    foreach (angCfg() as $c) {
        $q = angScope(angTabel($c['tabel']), $tahun);
        $d = $domains[$c['label']];

        $sd = (float) (clone $q)->sum($c['sd']) * $c['faktor'];
        if (! angSama($d['total_sd'], $sd)) {
            $salah[] = "{$c['label']}: Sumber Daya halaman {$d['total_sd']}, database {$sd}";
        }

        if ($c['cad']) {
            $cad = (float) (clone $q)->sum($c['cad']) * $c['faktor'];
            if (! angSama((float) $d['total_cad'], $cad)) {
                $salah[] = "{$c['label']}: Cadangan halaman {$d['total_cad']}, database {$cad}";
            }
        } elseif ($d['total_cad'] !== null) {
            $salah[] = "{$c['label']}: seharusnya tidak punya Cadangan";
        }

        $jumlah = (clone $q)->count();
        if ($d['jumlah'] !== $jumlah) {
            $salah[] = "{$c['label']}: jumlah data halaman {$d['jumlah']}, database {$jumlah}";
        }
    }

    expect($salah)->toBe([], "Angka Dashboard tidak cocok dengan database:\n" . implode("\n", $salah));
})->with($semuaTahun);

it('Dashboard User dan Dashboard Super User menampilkan total yang sama', function (?string $tahun) {
    $super = angDashboard($this->actingAs(angSuper()), 'superuser.dashboard', $tahun);
    $user = angDashboard($this->actingAs(angUser()), 'user.dashboard', $tahun);
    $salah = [];

    foreach (angCfg() as $c) {
        $a = $super[$c['label']];
        $b = $user[$c['label']];

        if (! angSama($a['total_sd'], $b['total_sd']) || ! angSama((float) $a['total_cad'], (float) $b['total_cad']) || $a['jumlah'] !== $b['jumlah']) {
            $salah[] = "{$c['label']}: super user {$a['total_sd']}/{$a['total_cad']}/{$a['jumlah']} vs user {$b['total_sd']}/{$b['total_cad']}/{$b['jumlah']}";
        }
    }

    expect($salah)->toBe([], "Dashboard Super User dan User beda angka:\n" . implode("\n", $salah));
})->with($semuaTahun);

it('Dashboard Super User: angka benar-benar tertulis di halaman (format koma desimal)', function () {
    $respon = $this->actingAs(angSuper())->get(route('superuser.dashboard'))->assertOk();

    foreach (angCfg() as $c) {
        $sd = (float) angScope(angTabel($c['tabel']), null)->sum($c['sd']) * $c['faktor'];
        $respon->assertSee(Angka::lengkap($sd));

        if ($c['cad']) {
            $respon->assertSee(Angka::lengkap((float) angScope(angTabel($c['tabel']), null)->sum($c['cad']) * $c['faktor']));
        }
    }
});

it('Dashboard: data yang dihapus tidak dihitung, dan kembali setelah dipulihkan', function (string $nama) {
    $c = angCfg()[$nama];
    $row = $c['model']::query()->whereBetween('tahun_data', [2024, 2025])->where($c['sd'], '>', 0)->first();

    if (! $row) {
        $this->markTestSkipped("Seeder tidak punya data {$nama} bernilai > 0 pada 2024-2025.");
    }

    $nilai = (float) $row->{$c['sd']} * $c['faktor'];
    $this->actingAs(angSuper());

    $sebelum = angDashboard($this, 'superuser.dashboard')[$c['label']];

    $row->delete();
    $sesudah = angDashboard($this, 'superuser.dashboard')[$c['label']];

    expect($sesudah['total_sd'])->toEqualWithDelta($sebelum['total_sd'] - $nilai, 0.01);
    expect($sesudah['jumlah'])->toBe($sebelum['jumlah'] - 1);
    expect($sesudah['terhapus'])->toBe($sebelum['terhapus'] + 1);

    $row->restore();
    $akhir = angDashboard($this, 'superuser.dashboard')[$c['label']];

    expect($akhir['total_sd'])->toEqualWithDelta($sebelum['total_sd'], 0.01);
    expect($akhir['jumlah'])->toBe($sebelum['jumlah']);
    expect($akhir['terhapus'])->toBe($sebelum['terhapus']);
})->with($semuaDomain);

it('Dashboard: data di luar tahun 2024-2025 tidak ikut dihitung (aturan pembimbing)', function (string $nama) {
    $c = angCfg()[$nama];
    $row = $c['model']::query()->whereBetween('tahun_data', [2024, 2025])->where($c['sd'], '>', 0)->first();

    if (! $row) {
        $this->markTestSkipped("Seeder tidak punya data {$nama} bernilai > 0 pada 2024-2025.");
    }

    $nilai = (float) $row->{$c['sd']} * $c['faktor'];
    $this->actingAs(angSuper());

    $sebelum = angDashboard($this, 'superuser.dashboard')[$c['label']];

    $c['model']::whereKey($row->getKey())->update(['tahun_data' => 2026]);
    $sesudah = angDashboard($this, 'superuser.dashboard')[$c['label']];

    expect($sesudah['total_sd'])->toEqualWithDelta($sebelum['total_sd'] - $nilai, 0.01);
    expect($sesudah['jumlah'])->toBe($sebelum['jumlah'] - 1);
})->with($semuaDomain);

/* ------------------------------------------------------------------ */
/* GRAFIK ADMIN                                                        */
/* ------------------------------------------------------------------ */

it('Grafik admin: total, klasifikasi, dan chart batang sama dengan database', function (string $nama) {
    $c = angCfg()[$nama];
    $respon = $this->actingAs(User::where('email', $c['email'])->firstOrFail())
        ->get(route("{$c['rute']}.grafik.index"))
        ->assertOk();
    $salah = [];

    foreach ($c['grafik'] as $variabel => $kolom) {
        $harapan = (float) angTabel($c['tabel'])->sum($kolom);
        $nyata = (float) $respon->viewData($variabel);
        if (! angSama($nyata, $harapan)) {
            $salah[] = "{$variabel}: halaman {$nyata}, database {$harapan}";
        }
    }

    if ($c['jumlah']) {
        $harapan = angTabel($c['tabel'])->count();
        if ((int) $respon->viewData($c['jumlah']) !== $harapan) {
            $salah[] = "{$c['jumlah']}: halaman " . $respon->viewData($c['jumlah']) . ", database {$harapan}";
        }
    }

    // chart batang: urutan & nilai (dibandingkan lewat daftar nilainya supaya tidak terganggu seri yang nilainya sama)
    $query = angTabel($c['tabel'])
        ->selectRaw("{$c['grup']} as g, SUM({$c['sd']}) as total")
        ->groupBy($c['grup'])
        ->orderByDesc('total');
    if ($c['batas']) {
        $query->limit($c['batas']);
    }
    $baris = $query->get();

    $harapanBar = $baris->pluck('total')->map(fn ($v) => round((float) $v, 2))->all();
    $nyataBar = $respon->viewData('perProvinsi')->pluck('total')->map(fn ($v) => round((float) $v, 2))->all();
    if ($harapanBar !== $nyataBar) {
        $salah[] = 'chart batang: halaman [' . implode(', ', $nyataBar) . '], database [' . implode(', ', $harapanBar) . ']';
    }

    // label batang pertama harus milik provinsi/komoditas yang benar (kalau nilai tertinggi tidak kembar)
    if ($baris->count() > 0 && ($baris->count() < 2 || (float) $baris[0]->total !== (float) $baris[1]->total)) {
        $teratas = $respon->viewData('perProvinsi')->first();
        if ((int) $teratas->{$c['grup']} !== (int) $baris[0]->g) {
            $salah[] = "batang tertinggi milik {$c['grup']} " . $teratas->{$c['grup']} . ", seharusnya " . $baris[0]->g;
        }
    }

    expect($salah)->toBe([], "Angka Grafik {$nama} tidak cocok dengan database:\n" . implode("\n", $salah));
})->with($semuaDomain);

it('Grafik admin: tren per tahun sama dengan jumlah per tahun di database', function (string $nama) {
    $c = angCfg()[$nama];

    if ($nama === 'mineral bukan logam') {
        // tren nasional diambil dari tabel neraca_tren_nasionals, bukan dari tabel neraca
        $harapan = DB::table('neraca_tren_nasionals')->where('domain', 'mineral_bukan_logam')
            ->selectRaw('tahun as t, SUM(total_sd) as total')->groupBy('tahun')->orderBy('tahun')->get();
    } else {
        $harapan = angTabel($c['tabel'])
            ->selectRaw("tahun_data as t, SUM({$c['sd']}) as total")->groupBy('tahun_data')->orderBy('tahun_data')->get();
    }

    $respon = $this->actingAs(User::where('email', $c['email'])->firstOrFail())
        ->get(route("{$c['rute']}.grafik.index"))->assertOk();

    $bentuk = fn ($koleksi, $kunciTahun) => collect($koleksi)
        ->mapWithKeys(fn ($r) => [(int) $r->{$kunciTahun} => round((float) $r->total, 2)])->all();

    expect($bentuk($respon->viewData('trendPerTahun'), 'tahun_data'))->toBe($bentuk($harapan, 't'));
})->with(['batubara', 'mineral logam', 'mineral bukan logam', 'panas bumi']);

it('Grafik admin: filter komoditas / klasifikasi mengubah total sesuai database', function (string $nama, string $parameter, string $kolomFilter) {
    $c = angCfg()[$nama];
    $nilai = angTabel($c['tabel'])->whereNotNull($kolomFilter)->value($kolomFilter);

    $respon = $this->actingAs(User::where('email', $c['email'])->firstOrFail())
        ->get(route("{$c['rute']}.grafik.index", [$parameter => $nilai]))
        ->assertOk();

    $salah = [];
    foreach (['totalSd', 'totalCad'] as $variabel) {
        $harapan = (float) angTabel($c['tabel'])->where($kolomFilter, $nilai)->sum($c['grafik'][$variabel]);
        $nyata = (float) $respon->viewData($variabel);
        if (! angSama($nyata, $harapan)) {
            $salah[] = "{$variabel} (filter {$kolomFilter}={$nilai}): halaman {$nyata}, database {$harapan}";
        }
    }

    expect($salah)->toBe([], "Filter Grafik {$nama} tidak cocok dengan database:\n" . implode("\n", $salah));
})->with([
    'mineral logam: komoditas' => ['mineral logam', 'komoditas_id', 'komoditas_logam_id'],
    'mineral bukan logam: komoditas' => ['mineral bukan logam', 'komoditas_id', 'komoditas_bukan_logam_id'],
    'panas bumi: klasifikasi temperatur' => ['panas bumi', 'klasifikasi_id', 'klasifikasi_temperatur_id'],
]);

it('Grafik Gambut: filter pulau mengubah total sesuai database', function () {
    $pulau = DB::table('neraca_gambuts')->join('provinsis', 'provinsis.id', '=', 'neraca_gambuts.provinsi_id')
        ->whereNull('neraca_gambuts.deleted_at')->whereNotNull('provinsis.pulau')->value('provinsis.pulau');

    if (! $pulau) {
        $this->markTestSkipped('Seeder tidak punya data Gambut dengan pulau terisi.');
    }

    $dasar = fn () => DB::table('neraca_gambuts')->join('provinsis', 'provinsis.id', '=', 'neraca_gambuts.provinsi_id')
        ->whereNull('neraca_gambuts.deleted_at')->where('provinsis.pulau', $pulau);

    $respon = $this->actingAs(User::where('email', 'supergambut@test.com')->firstOrFail())
        ->get(route('admin.gambut.grafik.index', ['pulau' => $pulau]))->assertOk();

    expect((float) $respon->viewData('totalSd'))->toEqualWithDelta((float) $dasar()->sum('neraca_gambuts.total_sd'), 0.01);
    expect((float) $respon->viewData('totalLuas'))->toEqualWithDelta((float) $dasar()->sum('neraca_gambuts.luas_ha'), 0.01);
    expect((float) $respon->viewData('totalVolume'))->toEqualWithDelta((float) $dasar()->sum('neraca_gambuts.volume_juta_m3'), 0.01);
});

/* ------------------------------------------------------------------ */
/* GRAFIK USER (semua domain dalam satu halaman, filter provinsi)       */
/* ------------------------------------------------------------------ */

$kasusUser = [];
foreach (array_keys(angCfg()) as $d) {
    $kasusUser["{$d}: semua provinsi"] = [$d, false];
    $kasusUser["{$d}: filter satu provinsi"] = [$d, true];
}

it('Grafik user: total dan jumlah data sama dengan database', function (string $nama, bool $filter) {
    $c = angCfg()[$nama];
    $parameter = ['domain' => $c['user']];
    $dasar = fn () => angTabel($c['tabel']);

    if ($filter) {
        $provinsiId = $dasar()->whereNotNull('provinsi_id')->value('provinsi_id');
        $parameter['provinsi_id'] = $provinsiId;
        $dasar = fn () => angTabel($c['tabel'])->where('provinsi_id', $provinsiId);
    }

    $respon = $this->actingAs(angUser())->get(route('user.grafik.index', $parameter))->assertOk();
    $salah = [];

    $harapanSd = (float) $dasar()->sum($c['sd']);
    if (! angSama((float) $respon->viewData('totalSd'), $harapanSd)) {
        $salah[] = 'Sumber Daya halaman ' . $respon->viewData('totalSd') . ", database {$harapanSd}";
    }

    $harapanCad = $c['cad'] ? (float) $dasar()->sum($c['cad']) : 0.0;
    if (! angSama((float) $respon->viewData('totalCad'), $harapanCad)) {
        $salah[] = 'Cadangan halaman ' . $respon->viewData('totalCad') . ", database {$harapanCad}";
    }

    $harapanJumlah = $dasar()->count();
    if ((int) $respon->viewData('jumlahData') !== $harapanJumlah) {
        $salah[] = 'jumlah data halaman ' . $respon->viewData('jumlahData') . ", database {$harapanJumlah}";
    }

    expect($salah)->toBe([], "Grafik user {$nama} tidak cocok dengan database:\n" . implode("\n", $salah));
})->with($kasusUser);

/* ------------------------------------------------------------------ */
/* DARI FORM KE ANGKA: tambah data -> Dashboard & Grafik naik persis sebesar input */
/* ------------------------------------------------------------------ */

/** Isian form tambah data + kenaikan yang HARUS terjadi: [payload, naik Dashboard, naik Grafik]. */
function angInput(string $nama): array
{
    $dasar = fn (string $kolomId, int $id) => [
        $kolomId => $id,
        'nama_objek' => "Objek Angka {$nama}",
        'tahun_data' => 2025,
        'tahun_neraca' => 2025,
        'provinsi_id' => Provinsi::query()->value('id'),
        'kabupaten_id' => Kabupaten::query()->value('id'),
    ];

    return match ($nama) {
        'batubara' => [
            array_merge($dasar('idbb', (int) NeracaBatubara::max('idbb') + 1), [
                'kelas_kalori_id' => KelasKalori::query()->value('id'), 'stat_dik_bb_id' => StatDikBb::query()->value('id'),
                'id_instansi_id' => IdInstansi::query()->value('id'),
                'tereka' => '10', 'tertunjuk' => '20', 'terukur' => '5.5', 'terkira' => '4', 'terbukti' => '6',
            ]),
            ['total_sd' => 35.5, 'total_cad' => 10.0, 'jumlah' => 1],
            ['totalSd' => 35.5, 'totalCad' => 10.0, 'totalTereka' => 10.0, 'totalTertunjuk' => 20.0, 'totalTerukur' => 5.5],
        ],
        'panas bumi' => [
            array_merge($dasar('idpb', (int) NeracaPanasBumi::max('idpb') + 1), [
                'stat_dik_pb_id' => StatDikPb::query()->value('id'), 'klasifikasi_temperatur_id' => KlasifikasiTemperatur::query()->value('id'),
                'id_instansi_id' => IdInstansi::query()->value('id'),
                'spekulatif' => '100', 'hipotetik' => '50', 'terduga' => '30', 'mungkin' => '20', 'terbukti' => '10', 'kapasitas_terpasang' => '55',
            ]),
            ['total_sd' => 150.0, 'total_cad' => 60.0, 'jumlah' => 1],
            ['totalSd' => 150.0, 'totalCad' => 60.0, 'totalSpekulatif' => 100.0, 'totalHipotetik' => 50.0, 'totalTerduga' => 30.0, 'totalMungkin' => 20.0, 'totalTerbukti' => 10.0, 'totalKapasitas' => 55.0],
        ],
        'gambut' => [
            array_merge($dasar('idgb', (int) NeracaGambut::max('idgb') + 1), [
                'nilai_kalori_min' => '1000', 'nilai_kalori_max' => '2000',
                'luas_ha' => '100', 'volume_juta_m3' => '50', 'total_sd' => '5.5',
            ]),
            // Dashboard menampilkan Gambut dalam ton (disimpan juta ton): 5,5 juta ton = 5.500.000 ton
            ['total_sd' => 5_500_000.0, 'total_cad' => null, 'jumlah' => 1],
            ['totalSd' => 5.5, 'totalLuas' => 100.0, 'totalVolume' => 50.0],
        ],
    };
}

it('tambah data lewat form: angka di Dashboard dan Grafik naik persis sebesar yang diinput', function (string $nama) {
    $c = angCfg()[$nama];
    [$payload, $naikDashboard, $naikGrafik] = angInput($nama);
    $admin = User::where('email', $c['email'])->firstOrFail();

    $ambil = function () use ($c, $admin) {
        $dashboard = angDashboard($this->actingAs(angSuper()), 'superuser.dashboard')[$c['label']];
        $grafik = $this->actingAs($admin)->get(route("{$c['rute']}.grafik.index"))->assertOk();

        return [$dashboard, $grafik];
    };

    [$dashSebelum, $grafikSebelum] = $ambil();

    $this->actingAs($admin)
        ->post(route("{$c['rute']}.store"), $payload)
        ->assertSessionHasNoErrors()
        ->assertRedirect(route("{$c['rute']}.index"));

    [$dashSesudah, $grafikSesudah] = $ambil();
    $salah = [];

    foreach ($naikDashboard as $kunci => $naik) {
        if ($naik === null) {
            continue;
        }
        $selisih = (float) $dashSesudah[$kunci] - (float) $dashSebelum[$kunci];
        if (! angSama($selisih, (float) $naik)) {
            $salah[] = "Dashboard {$kunci}: naik {$selisih}, seharusnya {$naik}";
        }
    }

    foreach ($naikGrafik as $variabel => $naik) {
        $selisih = (float) $grafikSesudah->viewData($variabel) - (float) $grafikSebelum->viewData($variabel);
        if (! angSama($selisih, $naik)) {
            $salah[] = "Grafik {$variabel}: naik {$selisih}, seharusnya {$naik}";
        }
    }

    expect($salah)->toBe([], "Angka tidak ikut berubah dengan benar setelah tambah data {$nama}:\n" . implode("\n", $salah));
})->with(['batubara', 'panas bumi', 'gambut']);
