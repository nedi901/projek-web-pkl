<?php

/*
|--------------------------------------------------------------------------
| Tampilan HP dan tablet: pagar pengaman supaya tampilan responsif tidak rusak lagi.
|--------------------------------------------------------------------------
| Jalankan:  php artisan test --filter=AksiResponsif
| Catatan: tes ini memeriksa KODE tampilan (kelas CSS dan struktur), bukan hasil gambar. Cara melihat hasilnya:
| di Chrome tekan F12, lalu Ctrl+Shift+M (mode perangkat), pilih ukuran HP (mis. 360 px) dan tablet (mis. 768 px).
*/

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
});

it('layout admin dan user punya menu samping yang bisa dibuka di layar kecil', function (string $email, string $rute) {
    $this->actingAs(User::where('email', $email)->firstOrFail())
        ->get(route($rute))
        ->assertOk()
        ->assertSee('name="viewport"', false)
        ->assertSee('id="sidebar"', false)
        ->assertSee('id="sidebar-buka"', false)
        ->assertSee('id="sidebar-overlay"', false)
        ->assertSee('md:static', false)
        ->assertDontSee('<div class="w-56 bg-white', false); // sidebar lama yang lebarnya selalu 224px
})->with([
    'layout admin (super user)' => ['admin@test.com', 'superuser.dashboard'],
    'layout admin (admin domain)' => ['superbatubara@test.com', 'admin.batubara.grafik.index'],
    'layout user' => ['user@test.com', 'user.dashboard'],
]);

it('halaman login tidak lebih lebar dari layar HP', function () {
    $this->get(route('login'))
        ->assertOk()
        ->assertSee('w-full max-w-[340px]', false)
        ->assertDontSee('<div class="w-[340px]', false); // bentuk lama yang selebar tetap (max-w-[340px] boleh)
});

it('tidak ada grid multi-kolom yang kaku (selalu 2+ kolom di HP) di seluruh tampilan', function () {
    $salah = [];

    $iter = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(resource_path('views'), FilesystemIterator::SKIP_DOTS));
    foreach ($iter as $berkas) {
        $nama = $berkas->getFilename();
        if (! str_ends_with($nama, '.blade.php') || $nama === 'welcome.blade.php') {
            continue;
        }

        preg_match_all('/class="([^"]*)"/', file_get_contents($berkas->getPathname()), $cocok);
        foreach ($cocok[1] as $kelas) {
            $kaku = preg_match('/(^|\s)grid-cols-([2-9])\b/', $kelas);
            $responsif = preg_match('/\b(sm|md|lg|xl):grid-cols-/', $kelas);
            if ($kaku && ! $responsif) {
                $salah[] = str_replace(resource_path('views').DIRECTORY_SEPARATOR, '', $berkas->getPathname()).': '.$kelas;
            }
        }
    }

    expect($salah)->toBe([], "Grid yang kaku di layar kecil:\n".implode("\n", $salah));
});

it('baris judul + aksi boleh turun baris di layar sempit (tidak memaksa satu baris)', function () {
    $salah = [];

    $iter = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(resource_path('views'), FilesystemIterator::SKIP_DOTS));
    foreach ($iter as $berkas) {
        if (! str_ends_with($berkas->getFilename(), '.blade.php')) {
            continue;
        }
        $isi = file_get_contents($berkas->getPathname());
        foreach (['class="flex justify-between items-end mb-4"', 'class="flex justify-between items-center mb-3"'] as $lama) {
            if (str_contains($isi, $lama)) {
                $salah[] = $berkas->getFilename()." masih memakai {$lama}";
            }
        }
    }

    expect($salah)->toBe([], implode("\n", $salah));
});

it('grafik batang tidak mendorong halaman melebar di layar kecil (bisa digeser di dalam kartu)', function () {
    $html = $this->actingAs(User::where('email', 'superbatubara@test.com')->firstOrFail())
        ->get(route('admin.batubara.grafik.index'))
        ->assertOk()
        ->assertSee('overflow-x-auto pt-4', false)
        ->assertSee('min-w-[var(--mw)] lg:min-w-0', false)
        ->assertSee('[overflow-wrap:anywhere]', false)
        ->assertDontSee('break-normal', false)
        ->getContent();

    preg_match_all('/--mw: (\d+)px/', $html, $cocok);

    expect($cocok[1])->not->toBeEmpty();
    foreach ($cocok[1] as $lebar) {
        // rumus: 112 px (sumbu Y) + 72 px per batang
        expect(((int) $lebar - 112) % 72)->toBe(0);
        expect((int) $lebar)->toBeGreaterThanOrEqual(184);
    }
});

it('komponen grafik batang tidak lagi memakai label yang kata panjangnya tidak boleh dipotong', function () {
    $isi = file_get_contents(resource_path('views/components/grafik-bar.blade.php'));

    expect($isi)->not->toContain('break-normal');
    expect($isi)->toContain('lg:min-w-0');
});
