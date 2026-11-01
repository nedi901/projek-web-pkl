<?php

/*
|--------------------------------------------------------------------------
| Keamanan & kerapian: pembatasan percobaan login dan halaman error.
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

function kemLogin($test, string $login, string $password)
{
    return $test->post(route('login.store'), ['login' => $login, 'password' => $password]);
}

/** Pesan error login dari session. Tahan terhadap bentuk datanya (objek MessageBag atau array hasil serialisasi). */
function kemPesanLogin(): string
{
    $galat = session('errors');

    return is_object($galat)
        ? (string) $galat->first('login')
        : (string) json_encode($galat, JSON_UNESCAPED_UNICODE);
}

/* ------------------------------------------------------------------ */
/* PEMBATASAN PERCOBAAN LOGIN */
/* ------------------------------------------------------------------ */

it('login dibatasi: setelah 5 kali salah, percobaan berikutnya ditolak walau password benar', function () {
    for ($i = 0; $i < 5; $i++) {
        kemLogin($this, 'user@test.com', 'salah')->assertSessionHasErrors('login');
    }

    kemLogin($this, 'user@test.com', 'password')->assertSessionHasErrors('login');

    expect(kemPesanLogin())->toContain('Terlalu banyak percobaan');
    $this->assertGuest();
});

it('pembatasan berlaku per akun: akun lain tidak ikut terblokir', function () {
    for ($i = 0; $i < 5; $i++) {
        kemLogin($this, 'user@test.com', 'salah');
    }

    kemLogin($this, 'admin@test.com', 'password');

    $this->assertAuthenticated();
});

it('login berhasil mengulang hitungan percobaan gagal', function () {
    for ($i = 0; $i < 3; $i++) {
        kemLogin($this, 'user@test.com', 'salah');
    }

    kemLogin($this, 'user@test.com', 'password');
    $this->assertAuthenticated();

    $this->post(route('logout'));
    $this->assertGuest();

    // tanpa reset, 3 + 3 = 6 percobaan gagal akan memblokir login berikutnya
    for ($i = 0; $i < 3; $i++) {
        kemLogin($this, 'user@test.com', 'salah');
    }

    kemLogin($this, 'user@test.com', 'password');
    $this->assertAuthenticated();
});

it('login dibatasi per alamat IP: 20 tebakan ke akun berbeda memblokir percobaan berikutnya', function () {
    for ($i = 0; $i < 20; $i++) {
        kemLogin($this, "hantu{$i}", 'salah');
    }

    kemLogin($this, 'admin@test.com', 'password')->assertSessionHasErrors('login');

    expect(kemPesanLogin())->toContain('Terlalu banyak percobaan');
    $this->assertGuest();
});

it('percobaan ke akun nonaktif juga dihitung', function () {
    User::where('email', 'user@test.com')->update(['is_active' => false]);

    for ($i = 0; $i < 5; $i++) {
        kemLogin($this, 'user@test.com', 'password');
    }

    kemLogin($this, 'user@test.com', 'password');

    expect(kemPesanLogin())->toContain('Terlalu banyak percobaan');
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
