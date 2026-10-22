<?php

/*
| Form login tidak boleh diisi otomatis oleh browser dari akun login sebelumnya.
| Jalankan:  php artisan test --filter=AksiLoginForm
| Catatan: tes ini memeriksa atribut HTML yang mencegah isian otomatis. Browser tetap bisa menawarkan saran
| (dropdown) kalau pengguna menyimpan password di browser; itu pengaturan browser, bukan aplikasi.
*/

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('form login mencegah isian otomatis browser (email dan password harus diketik ulang)', function () {
    $this->get(route('login'))
        ->assertOk()
        ->assertSee('<form method="POST" action="' . route('login.store') . '" autocomplete="off">', false)
        ->assertSee('autocomplete="new-password"', false)
        ->assertDontSee('autocomplete="username"', false)
        ->assertSee("onfocus=\"this.removeAttribute('readonly')\"", false);
});

it('halaman login kosong saat dibuka lagi setelah logout', function () {
    $this->seed();
    $user = \App\Models\User::where('email', 'user@test.com')->firstOrFail();

    $this->actingAs($user)->post(route('logout'));

    $this->get(route('login'))
        ->assertOk()
        ->assertDontSee('user@test.com');
});

it('setelah salah password, email tetap terisi supaya tidak mengetik ulang, tapi password tidak', function () {
    $this->seed();

    $this->from(route('login'))
        ->post(route('login.store'), ['login' => 'user@test.com', 'password' => 'salah'])
        ->assertRedirect(route('login'));

    $this->get(route('login'))
        ->assertSee('value="user@test.com"', false)
        ->assertDontSee('value="salah"', false); // password tidak pernah dikembalikan ke form
});
