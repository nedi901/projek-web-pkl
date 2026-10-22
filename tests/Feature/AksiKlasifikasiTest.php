<?php

/*
|--------------------------------------------------------------------------
| Dropdown klasifikasi temperatur di Grafik Panas Bumi.
|--------------------------------------------------------------------------
| Jalankan:  php artisan test --filter=AksiKlasifikasi
| Berdiri sendiri. Latar belakang: data panas bumi dari buku TIDAK dibagi per kelas suhu, jadi semua baris di seeder
| punya klasifikasi kosong. Dropdown-nya disembunyikan selama begitu, dan muncul otomatis kalau ada data berklasifikasi.
| Tes di sini MENGISI klasifikasi pada satu baris supaya filternya benar-benar teruji (bukan sekadar "semua baris").
*/

use App\Models\KlasifikasiTemperatur;
use App\Models\NeracaPanasBumi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
});

function klsAdmin(): User
{
    return User::where('email', 'superpanasbumi@test.com')->firstOrFail();
}

/** Tambahkan satu baris panas bumi berklasifikasi (salinan baris yang ada). Mengembalikan [salinan, id kelas lain]. */
function klsTambahBerklasifikasi(): array
{
    $kelas = KlasifikasiTemperatur::orderBy('id')->pluck('id')->all();
    $asli = NeracaPanasBumi::query()->where('total_sd', '>', 0)->first();

    $salinan = $asli->replicate();
    $salinan->idpb = (int) NeracaPanasBumi::withTrashed()->max('idpb') + 1;
    $salinan->klasifikasi_temperatur_id = $kelas[0];
    $salinan->save();

    return [$salinan, $kelas[1]];
}

it('data seeder memang tidak punya klasifikasi temperatur, dan dropdown-nya disembunyikan', function () {
    expect(NeracaPanasBumi::whereNotNull('klasifikasi_temperatur_id')->exists())->toBeFalse();

    $respon = $this->actingAs(klsAdmin())->get(route('admin.panas-bumi.grafik.index'))->assertOk();

    expect($respon->viewData('adaKlasifikasi'))->toBeFalse();
    $respon->assertDontSee('name="klasifikasi_id"', false);
});

it('dropdown muncul begitu ada baris berklasifikasi', function () {
    klsTambahBerklasifikasi();

    $respon = $this->actingAs(klsAdmin())->get(route('admin.panas-bumi.grafik.index'))->assertOk();

    expect($respon->viewData('adaKlasifikasi'))->toBeTrue();
    $respon->assertSee('name="klasifikasi_id"', false);
});

it('filter klasifikasi menyaring dengan benar: kelas yang ada berisi, kelas lain nol, tanpa filter = semua', function () {
    [$salinan, $kelasLain] = klsTambahBerklasifikasi();
    $admin = klsAdmin();
    $semua = (float) NeracaPanasBumi::sum('total_sd');

    $tanpaFilter = $this->actingAs($admin)->get(route('admin.panas-bumi.grafik.index'))->assertOk();
    $kelasIni = $this->actingAs($admin)->get(route('admin.panas-bumi.grafik.index', ['klasifikasi_id' => $salinan->klasifikasi_temperatur_id]))->assertOk();
    $kelasKosong = $this->actingAs($admin)->get(route('admin.panas-bumi.grafik.index', ['klasifikasi_id' => $kelasLain]))->assertOk();

    expect((float) $tanpaFilter->viewData('totalSd'))->toEqualWithDelta($semua, 0.01);
    expect((float) $kelasIni->viewData('totalSd'))->toEqualWithDelta((float) $salinan->total_sd, 0.01);
    expect((float) $kelasIni->viewData('totalSd'))->toBeLessThan($semua);
    expect((float) $kelasKosong->viewData('totalSd'))->toEqualWithDelta(0.0, 0.0001);
});

it('dropdown tetap tampil kalau URL sudah memakai klasifikasi_id, supaya bisa dipilih balik', function () {
    // belum ada data berklasifikasi, tapi alamat lama (bookmark) memakai filter
    $respon = $this->actingAs(klsAdmin())->get(route('admin.panas-bumi.grafik.index', ['klasifikasi_id' => 1]))->assertOk();

    $respon->assertSee('name="klasifikasi_id"', false);
    $respon->assertSee('Semua Klasifikasi Temperatur');
    expect((float) $respon->viewData('totalSd'))->toEqualWithDelta(0.0, 0.0001);
});
