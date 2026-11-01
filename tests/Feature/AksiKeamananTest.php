<?php

/*
|--------------------------------------------------------------------------
| Keamanan & kerapian: halaman error.
| (Pembatasan percobaan login dihapus: login password sudah dimatikan, login hanya lewat Google.)
|--------------------------------------------------------------------------
| Jalankan:  php artisan test --filter=AksiKeamanan
| Berdiri sendiri.
*/

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
});

/* ------------------------------------------------------------------ */
/* HALAMAN ERROR */
/* ------------------------------------------------------------------ */

it('setiap kode error punya halaman yang rapi', function (int $status, string $judul) {
    Route::get("/_uji/galat-{$status}", fn () => abort($status));

    $this->get("/_uji/galat-{$status}")
        ->assertStatus($status)
        ->assertSee($judul)
        ->assertSee('Ke Beranda')
        ->assertSee('Kementerian ESDM');
})->with([
    '403' => [403, 'Akses ditolak'],
    '404' => [404, 'Halaman tidak ditemukan'],
    '419' => [419, 'Sesi berakhir'],
    '429' => [429, 'Terlalu banyak permintaan'],
    '503' => [503, 'Sedang dalam pemeliharaan'],
]);

it('alamat yang tidak ada menampilkan halaman 404 yang rapi', function () {
    $this->get('/alamat-yang-tidak-ada')
        ->assertNotFound()
        ->assertSee('Halaman tidak ditemukan');
});

it('akses ditolak karena peran menampilkan halaman 403 yang rapi', function () {
    $this->actingAs(User::where('email', 'user@test.com')->firstOrFail())
        ->get(route('superuser.akun.index'))
        ->assertForbidden()
        ->assertSee('Akses ditolak');
});

it('halaman 403 menampilkan alasan kalau ada, dan tidak menampilkan pesan bawaan Laravel', function () {
    Route::get('/_uji/dengan-alasan', fn () => abort(403, 'Hanya Super User yang boleh membuka ini.'));
    Route::get('/_uji/tanpa-alasan', fn () => abort(403));

    $this->get('/_uji/dengan-alasan')->assertForbidden()->assertSee('Hanya Super User yang boleh membuka ini.');
    $this->get('/_uji/tanpa-alasan')->assertForbidden()->assertDontSee('This action is unauthorized');
});

it('error server (500) tampil rapi dan TIDAK membocorkan detail teknis', function () {
    config(['app.debug' => false]);
    Route::get('/_uji/meledak', fn () => throw new RuntimeException('detail-rahasia-internal'));

    $this->get('/_uji/meledak')
        ->assertStatus(500)
        ->assertSee('Terjadi kesalahan di server')
        ->assertDontSee('detail-rahasia-internal');
});
