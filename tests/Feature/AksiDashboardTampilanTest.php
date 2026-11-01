<?php

/*
| Tampilan Dashboard dan kartu ringkasan Grafik (tahap 2): angka lengkap tetap tampil, proporsi sumber daya vs cadangan,
| legenda, dan kolom responsif. Jalankan:  php artisan test --filter=AksiDashboardTampilan
| Catatan: memeriksa isi HTML, bukan hasil gambar.
*/

use App\Models\NeracaBatubara;
use App\Models\User;
use App\Support\Angka;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
});

it('Dashboard: kartu tiap domain memuat angka lengkap, bilah proporsi, dan legenda', function (string $email, string $rute) {
    $sd = (float) NeracaBatubara::query()->sum('total_sd');
    $cad = (float) NeracaBatubara::query()->sum('total_cad');
    $persenSd = (int) round($sd / ($sd + $cad) * 100);

    $respon = $this->actingAs(User::where('email', $email)->firstOrFail())
        ->get(route($rute))
        ->assertOk()
        ->assertSee('2xl:grid-cols-5', false)
        ->assertSee('bg-[#F2C230]', false)
        ->assertSee('bg-[#4A6FE3]', false)
        ->assertSee('tabular-nums', false)
        ->assertSee(Angka::lengkap($sd))
        ->assertSee(Angka::lengkap($cad))
        ->assertSee('style="width: '.$persenSd.'%"', false);

    foreach (['Batubara', 'Mineral Logam', 'Mineral Bukan Logam', 'Panas Bumi', 'Gambut'] as $label) {
        $respon->assertSee($label);
    }
})->with([
    'Dashboard Super User' => ['admin@test.com', 'superuser.dashboard'],
    'Dashboard User' => ['user@test.com', 'user.dashboard'],
]);

it('Dashboard Super User: legenda sumber daya dan cadangan tampil di atas kartu', function () {
    $this->actingAs(User::where('email', 'admin@test.com')->firstOrFail())
        ->get(route('superuser.dashboard'))
        ->assertOk()
        ->assertSee('Sumber daya')
        ->assertSee('Cadangan');
});

it('kartu ringkasan di halaman Grafik memakai angka besar yang rapi dan tetap lengkap', function () {
    $sd = (float) NeracaBatubara::query()->sum('total_sd');

    $this->actingAs(User::where('email', 'superbatubara@test.com')->firstOrFail())
        ->get(route('admin.batubara.grafik.index'))
        ->assertOk()
        ->assertSee('text-2xl font-semibold', false)
        ->assertSee(Angka::lengkap($sd));
});
