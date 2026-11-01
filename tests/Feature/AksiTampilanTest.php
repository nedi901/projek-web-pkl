<?php

/*
| Gaya tampilan bersama (font, kartu, tabel, sidebar) terpasang di layout admin dan user.
| Jalankan:  php artisan test --filter=AksiTampilan
| Catatan: memeriksa KODE gaya, bukan hasil gambar. Lihat hasilnya langsung di browser (Ctrl+F5).
*/

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
});

it('layout admin dan user memasang gaya tampilan bersama', function (string $email, string $rute) {
    $this->actingAs(User::where('email', $email)->firstOrFail())
        ->get(route($rute))
        ->assertOk()
        ->assertSee('id="gaya-tampilan"', false)
        ->assertSee('IBM Plex Sans', false)
        ->assertSee('id="sidebar"', false);
})->with([
    'admin (super user)' => ['admin@test.com', 'superuser.dashboard'],
    'admin domain' => ['superbatubara@test.com', 'admin.batubara.grafik.index'],
    'user' => ['user@test.com', 'user.dashboard'],
]);

it('menu aktif di sidebar berwarna kuning muda, bukan kuning pekat', function () {
    $html = $this->actingAs(User::where('email', 'admin@test.com')->firstOrFail())
        ->get(route('superuser.dashboard'))->assertOk()->getContent();

    expect($html)->toContain('#sidebar a.bg-\[\#F2C230\] { background-color: #FBF3D1;');
});

it('tidak ada direktif Blade yang menempel ke huruf (Blade mengabaikannya, lalu @endif jadi tanpa pasangan)', function () {
    $salah = [];

    $iter = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(resource_path('views'), FilesystemIterator::SKIP_DOTS));
    foreach ($iter as $berkas) {
        if (! str_ends_with($berkas->getFilename(), '.blade.php')) {
            continue;
        }

        $isi = file_get_contents($berkas->getPathname());
        if (preg_match_all('/(?<=\w)@(?:if|elseif|else|endif|foreach|endforeach|forelse|empty|endforelse|php|endphp|unless|endunless|isset|endisset|include|selected|checked)\b/', $isi, $cocok)) {
            $salah[] = $berkas->getFilename().': '.implode(', ', $cocok[0]);
        }
    }

    expect($salah)->toBe([], "Direktif Blade menempel ke huruf (beri spasi di depannya):\n".implode("\n", $salah));
});
