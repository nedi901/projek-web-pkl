<?php

/*
|--------------------------------------------------------------------------
| Tes asap (smoke test): membuka SEMUA halaman GET sebagai tiap role dan mencari error 500.
|--------------------------------------------------------------------------
| Jalankan:  php artisan test --filter=Smoke
| Memakai database SQLite di memori (terpisah dari database.sqlite asli, aman) yang diisi dari nol oleh
| DatabaseSeeder — jadi sekaligus menguji bahwa `migrate:fresh --seed` menghasilkan data lengkap.
*/

use App\Models\NeracaBatubara;
use App\Models\NeracaGambut;
use App\Models\NeracaMineralBukanLogam;
use App\Models\NeracaMineralLogam;
use App\Models\NeracaPanasBumi;
use App\Models\TabelReferensi;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
});

/** Semua URL GET yang bisa dibuka (rute berparameter diisi dengan data pertama di database). */
function daftarUrlGet(): array
{
    $urls = [];

    foreach (Route::getRoutes() as $route) {
        if (! in_array('GET', $route->methods(), true)) {
            continue;
        }

        $uri = $route->uri();
        if ($uri === 'up' || str_contains($uri, 'storage') || str_starts_with($uri, '_')) {
            continue;
        }

        // tanpa parameter
        if (! str_contains($uri, '{')) {
            $urls[] = '/' . ltrim($uri, '/');
            continue;
        }

        // parameter {jenis} (Kelola Referensi): coba semua jenis; lewati rute yang butuh {id}
        if (str_contains($uri, '{jenis}')) {
            if (str_contains($uri, '{id}')) {
                continue;
            }
            foreach (explode('|', $route->wheres['jenis'] ?? '') as $jenis) {
                $urls[] = '/' . ltrim(str_replace('{jenis}', $jenis, $uri), '/');
            }
            continue;
        }

        // parameter model (route model binding): pakai data pertama
        $ganti = [];
        foreach ($route->signatureParameters(['subClass' => Model::class]) as $p) {
            $kelas = $p->getType()->getName();
            $data = $kelas::query()->first();
            if ($data) {
                $ganti['{' . $p->getName() . '}'] = $data->getKey();
            }
        }

        $url = str_replace(array_keys($ganti), array_values($ganti), $uri);
        if (! str_contains($url, '{')) {
            $urls[] = '/' . ltrim($url, '/');
        }
    }

    return array_values(array_unique($urls));
}

/** Buka satu URL; kembalikan [status, pesan error|null]. Exception asli ditampilkan agar mudah dilacak. */
function bukaUrl($test, ?User $user, string $url): array
{
    try {
        $request = $user ? $test->actingAs($user) : $test;
        $respon = $request->get($url);

        return [$respon->getStatusCode(), null];
    } catch (HttpExceptionInterface $e) {
        return [$e->getStatusCode(), null];
    } catch (AuthenticationException $e) {
        return [302, null]; // belum login -> biasanya diarahkan ke halaman login
    } catch (ModelNotFoundException $e) {
        return [404, null];
    } catch (Throwable $e) {
        return [500, get_class($e) . ': ' . $e->getMessage() . ' (' . basename($e->getFile()) . ':' . $e->getLine() . ')'];
    }
}

it('database bisa diisi dari nol (migrate:fresh --seed) dan datanya lengkap', function () {
    expect(NeracaBatubara::count())->toBeGreaterThan(0);
    expect(NeracaMineralLogam::count())->toBeGreaterThan(0);
    expect(NeracaMineralBukanLogam::count())->toBeGreaterThan(0);
    expect(NeracaPanasBumi::count())->toBeGreaterThan(0);
    expect(NeracaGambut::count())->toBeGreaterThan(0);
    expect(TabelReferensi::count())->toBe(22);
    expect(User::where('role', 'super_user')->count())->toBeGreaterThanOrEqual(1);
    expect(User::where('role', 'admin')->count())->toBe(5);
});

it('semua halaman terbuka tanpa error 500 dan hak aksesnya benar', function (string $peran) {
    $this->withoutExceptionHandling();

    $user = match ($peran) {
        'super_user' => User::where('email', 'admin@test.com')->firstOrFail(),
        'admin' => User::where('email', 'superbatubara@test.com')->firstOrFail(), // admin domain Batubara
        'user' => User::where('email', 'user@test.com')->firstOrFail(),
        default => null, // tamu
    };

    $urls = daftarUrlGet();
    expect(count($urls))->toBeGreaterThan(30);

    $error500 = [];
    $salahAkses = [];

    foreach ($urls as $url) {
        [$status, $pesan] = bukaUrl($this, $user, $url);

        if ($status >= 500) {
            $error500[] = "{$status}  {$url}  →  {$pesan}";
            continue;
        }

        if ($url === '/superuser') {
            continue; // rute pintasan biasa (redirect ke dashboard), tanpa pembatasan role
        }

        $areaSuper = str_starts_with($url, '/superuser');
        $areaAdmin = str_starts_with($url, '/admin');
        $areaAkun = str_starts_with($url, '/akun-saya');
        $areaBatubara = str_starts_with($url, '/admin/batubara');

        $diharapkan = match ($peran) {
            'super_user' => $status < 400,
            'admin' => match (true) {
                $areaSuper => $status === 403,
                $areaAdmin && ! $areaBatubara => $status === 403,
                default => $status < 400,
            },
            'user' => ($areaSuper || $areaAdmin) ? $status === 403 : $status < 400,
            default => ($areaSuper || $areaAdmin || $areaAkun) ? $status === 302 : $status < 400,
        };

        if (! $diharapkan) {
            $salahAkses[] = "{$status}  {$url}";
        }
    }

    expect($error500)->toBe([], "Halaman yang ERROR (peran: {$peran}):\n" . implode("\n", $error500));
    expect($salahAkses)->toBe([], "Status tidak sesuai harapan (peran: {$peran}):\n" . implode("\n", $salahAkses));
})->with(['super_user', 'admin', 'user', 'tamu']);
