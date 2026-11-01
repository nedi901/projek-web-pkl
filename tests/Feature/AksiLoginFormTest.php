<?php

/*
| Halaman login: HANYA tombol Google (tanpa form email/username + password).
| Jalankan:  php artisan test --filter=AksiLoginForm
*/

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('halaman login hanya menampilkan tombol Google, tanpa form password', function () {
    $this->get(route('login'))
        ->assertOk()
        ->assertSee('Masuk dengan Google')
        ->assertSee(route('auth.google'), false)
        ->assertDontSee('name="password"', false)
        ->assertDontSee('name="login"', false);
});

it('halaman login kosong saat dibuka lagi setelah logout', function () {
    $this->seed();
    $user = User::where('email', 'user@test.com')->firstOrFail();

    $this->actingAs($user)->post(route('logout'));

    $this->get(route('login'))
        ->assertOk()
        ->assertDontSee('user@test.com');
});
