<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\User\BatubaraController;
use App\Http\Controllers\User\MineralLogamController;
use App\Http\Controllers\User\MineralBukanLogamController;
use App\Http\Controllers\User\PanasBumiController;
use App\Http\Controllers\User\GambutController;
use App\Http\Controllers\Admin\AkunController;
use App\Http\Controllers\Auth\ProfilController;
use App\Http\Controllers\Admin\SuperDashboardController;
use App\Http\Controllers\Admin\ReferensiController;
use App\Http\Controllers\Admin\TabelBukuController;
use App\Http\Controllers\Admin\HistoriSemuaController;
use App\Http\Controllers\User\GrafikController;
use App\Http\Controllers\Admin\BatubaraController as AdminBatubaraController;
use App\Http\Controllers\Admin\GrafikController as AdminGrafikController;
use App\Http\Controllers\Admin\MineralLogamController as AdminMineralLogamController;
use App\Http\Controllers\Admin\MineralBukanLogamController as AdminMineralBukanLogamController;
use App\Http\Controllers\Admin\MineralBukanLogamGrafikController as AdminMineralBukanLogamGrafikController;
use App\Http\Controllers\Admin\PanasBumiController as AdminPanasBumiController;
use App\Http\Controllers\Admin\PanasBumiGrafikController as AdminPanasBumiGrafikController;
use App\Http\Controllers\Admin\GambutController as AdminGambutController;
use App\Http\Controllers\Admin\GambutGrafikController as AdminGambutGrafikController;

// Halaman awal: belum login -> halaman login; sudah login -> halaman sesuai role (bukan halaman bawaan Laravel)
Route::get('/', function () {
    $user = auth()->user();

    if (! $user) {
        return redirect()->route('login');
    }

    if ($user->role === 'super_user') {
        return redirect()->route('superuser.dashboard');
    }

    if ($user->role === 'admin') {
        $rute = match ($user->domain_akses) {
            'batubara' => 'admin.batubara.grafik.index',
            'mineral_logam' => 'admin.mineral-logam.grafik.index',
            'mineral_bukan_logam' => 'admin.mineral-bukan-logam.grafik.index',
            'panas_bumi' => 'admin.panas-bumi.grafik.index',
            'gambut' => 'admin.gambut.grafik.index',
            default => null,
        };

        return ($rute && Route::has($rute)) ? redirect()->route($rute) : redirect('/dashboard');
    }

    return redirect('/dashboard');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->name('user.dashboard');
Route::get('/login', [LoginController::class, 'create'])->name('login');
Route::post('/login', [LoginController::class, 'store'])->name('login.store');
Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');



Route::prefix('batubara')->name('user.batubara.')->group(function () {
    Route::get('/', [BatubaraController::class, 'index'])->name('index');
    Route::get('/{batubara}', [BatubaraController::class, 'show'])->name('show');
});
Route::prefix('mineral-logam')->name('user.mineral-logam.')->group(function () {
    Route::get('/', [MineralLogamController::class, 'index'])->name('index');
    Route::get('/{mineralLogam}', [MineralLogamController::class, 'show'])->name('show');
});

Route::prefix('mineral-bukan-logam')->name('user.mineral-bukan-logam.')->group(function () {
    Route::get('/', [MineralBukanLogamController::class, 'index'])->name('index');
    Route::get('/{mineralBukanLogam}', [MineralBukanLogamController::class, 'show'])->name('show');
});

Route::prefix('panas-bumi')->name('user.panas-bumi.')->group(function () {
    Route::get('/', [PanasBumiController::class, 'index'])->name('index');
    Route::get('/{panasBumi}', [PanasBumiController::class, 'show'])->name('show');
});
Route::prefix('gambut')->name('user.gambut.')->group(function () {
    Route::get('/', [GambutController::class, 'index'])->name('index');
    Route::get('/{gambut}', [GambutController::class, 'show'])->name('show');
});
Route::get('/grafik', [GrafikController::class, 'index'])->name('user.grafik.index');

Route::middleware(['auth', 'admin.batubara'])->prefix('admin/batubara')->name('admin.batubara.')->group(function () {
    Route::get('/', [AdminBatubaraController::class, 'index'])->name('index');
    Route::get('/create', [AdminBatubaraController::class, 'create'])->name('create');
    Route::post('/', [AdminBatubaraController::class, 'store'])->name('store');
    Route::get('/{batubara}/edit', [AdminBatubaraController::class, 'edit'])->name('edit');
    Route::put('/{batubara}', [AdminBatubaraController::class, 'update'])->name('update');
    Route::delete('/{batubara}', [AdminBatubaraController::class, 'destroy'])->name('destroy');
    Route::get('/import', [AdminBatubaraController::class, 'importForm'])->name('import.form');
    Route::post('/import', [AdminBatubaraController::class, 'import'])->name('import');
    Route::get('/kabupaten/{provinsi}', function (\App\Models\Provinsi $provinsi) {
        return $provinsi->kabupatens;
    })->name('kabupaten');
    Route::get('/grafik', [AdminGrafikController::class, 'index'])->name('grafik.index');
});



Route::middleware(['auth', 'admin.minerallogam'])->prefix('admin/mineral-logam')->name('admin.mineral-logam.')->group(function () {
    Route::get('/', [AdminMineralLogamController::class, 'index'])->name('index');
    Route::get('/create', [AdminMineralLogamController::class, 'create'])->name('create');
    Route::post('/', [AdminMineralLogamController::class, 'store'])->name('store');
    Route::get('/{mineralLogam}/edit', [AdminMineralLogamController::class, 'edit'])->name('edit');
    Route::put('/{mineralLogam}', [AdminMineralLogamController::class, 'update'])->name('update');
    Route::delete('/{mineralLogam}', [AdminMineralLogamController::class, 'destroy'])->name('destroy');
    Route::get('/kabupaten/{provinsi}', function (\App\Models\Provinsi $provinsi) {
        return $provinsi->kabupatens;
    })->name('kabupaten');
    Route::get('/grafik', [\App\Http\Controllers\Admin\MineralLogamGrafikController::class, 'index'])->name('grafik.index');
    Route::get('/import', [AdminMineralLogamController::class, 'importForm'])->name('import.form');
    Route::post('/import', [AdminMineralLogamController::class, 'import'])->name('import');
});

Route::middleware(['auth', 'admin.mineral-bukan-logam'])
    ->prefix('admin/mineral-bukan-logam')
    ->name('admin.mineral-bukan-logam.')
    ->group(function () {
        Route::get('/', [AdminMineralBukanLogamController::class, 'index'])->name('index');
        Route::get('/create', [AdminMineralBukanLogamController::class, 'create'])->name('create');
        Route::post('/', [AdminMineralBukanLogamController::class, 'store'])->name('store');
        Route::get('/{mineralBukanLogam}/edit', [AdminMineralBukanLogamController::class, 'edit'])->name('edit');
        Route::put('/{mineralBukanLogam}', [AdminMineralBukanLogamController::class, 'update'])->name('update');
        Route::delete('/{mineralBukanLogam}', [AdminMineralBukanLogamController::class, 'destroy'])->name('destroy');

        Route::get('/import', [AdminMineralBukanLogamController::class, 'importForm'])->name('import.form');
        Route::post('/import', [AdminMineralBukanLogamController::class, 'import'])->name('import');

        Route::get('/kabupaten/{provinsiId}', [AdminMineralBukanLogamController::class, 'kabupaten'])->name('kabupaten');

        Route::get('/grafik', [AdminMineralBukanLogamGrafikController::class, 'index'])->name('grafik.index');
    });

    Route::middleware(['auth', 'admin.panas-bumi'])
    ->prefix('admin/panas-bumi')
    ->name('admin.panas-bumi.')
    ->group(function () {
        Route::get('/', [AdminPanasBumiController::class, 'index'])->name('index');
        Route::get('/create', [AdminPanasBumiController::class, 'create'])->name('create');
        Route::post('/', [AdminPanasBumiController::class, 'store'])->name('store');
        Route::get('/{panasBumi}/edit', [AdminPanasBumiController::class, 'edit'])->name('edit');
        Route::put('/{panasBumi}', [AdminPanasBumiController::class, 'update'])->name('update');
        Route::delete('/{panasBumi}', [AdminPanasBumiController::class, 'destroy'])->name('destroy');

        Route::get('/import', [AdminPanasBumiController::class, 'importForm'])->name('import.form');
        Route::post('/import', [AdminPanasBumiController::class, 'import'])->name('import');

        Route::get('/kabupaten/{provinsiId}', [AdminPanasBumiController::class, 'kabupaten'])->name('kabupaten');

        Route::get('/grafik', [AdminPanasBumiGrafikController::class, 'index'])->name('grafik.index');
    });

    Route::middleware(['auth', 'admin.gambut'])
    ->prefix('admin/gambut')
    ->name('admin.gambut.')
    ->group(function () {
        Route::get('/', [AdminGambutController::class, 'index'])->name('index');
        Route::get('/create', [AdminGambutController::class, 'create'])->name('create');
        Route::post('/', [AdminGambutController::class, 'store'])->name('store');
        Route::get('/{gambut}/edit', [AdminGambutController::class, 'edit'])->name('edit');
        Route::put('/{gambut}', [AdminGambutController::class, 'update'])->name('update');
        Route::delete('/{gambut}', [AdminGambutController::class, 'destroy'])->name('destroy');

        Route::get('/import', [AdminGambutController::class, 'importForm'])->name('import.form');
        Route::post('/import', [AdminGambutController::class, 'import'])->name('import');

        Route::get('/kabupaten/{provinsiId}', [AdminGambutController::class, 'kabupaten'])->name('kabupaten');

        Route::get('/grafik', [AdminGambutGrafikController::class, 'index'])->name('grafik.index');
    });


/*
|--------------------------------------------------------------------------
| Histori (soft delete) & Log Aktivitas - semua domain
|--------------------------------------------------------------------------
| Tiap domain dapat 4 route dengan middleware akses domain masing-masing:
|   admin.<domain>.histori.index   GET     daftar data terhapus
|   admin.<domain>.histori.restore POST    pulihkan
|   admin.<domain>.histori.force   DELETE  hapus permanen (cuma super_user, dicek di controller)
|   admin.<domain>.histori.log     GET     log siapa menghapus/memulihkan
*/
$historiDomains = [
    // slug domain_akses  => [alias middleware, prefix URL/route]
    'batubara'            => ['admin.batubara',            'batubara'],
    'mineral_logam'       => ['admin.minerallogam',        'mineral-logam'],
    'mineral_bukan_logam' => ['admin.mineral-bukan-logam', 'mineral-bukan-logam'],
    'panas_bumi'          => ['admin.panas-bumi',          'panas-bumi'],
    'gambut'              => ['admin.gambut',              'gambut'],
];

foreach ($historiDomains as $slug => [$middlewareAlias, $prefix]) {
    Route::middleware(['auth', $middlewareAlias])
        ->prefix("admin/{$prefix}/histori")
        ->name("admin.{$prefix}.histori.")
        ->group(function () use ($slug) {
            Route::get('/', [\App\Http\Controllers\Admin\HistoriController::class, 'index'])
                ->defaults('domain', $slug)->name('index');
            Route::get('/log', [\App\Http\Controllers\Admin\HistoriController::class, 'log'])
                ->defaults('domain', $slug)->name('log');
            Route::post('/{id}/pulihkan', [\App\Http\Controllers\Admin\HistoriController::class, 'restore'])
                ->defaults('domain', $slug)->whereNumber('id')->name('restore');
            Route::delete('/{id}/permanen', [\App\Http\Controllers\Admin\HistoriController::class, 'forceDestroy'])
                ->defaults('domain', $slug)->whereNumber('id')->name('force');
        });
}

/*
|--------------------------------------------------------------------------
| Akun
|--------------------------------------------------------------------------
| Kelola Akun : hanya super_user (middleware super.user)
| Akun Saya   : semua yang sudah login (ubah profil & ganti password sendiri)
*/
Route::middleware(['auth', 'super.user'])->prefix('superuser/akun')->name('superuser.akun.')->group(function () {
    Route::get('/', [AkunController::class, 'index'])->name('index');
    Route::get('/create', [AkunController::class, 'create'])->name('create');
    Route::post('/', [AkunController::class, 'store'])->name('store');
    Route::get('/{akun}/edit', [AkunController::class, 'edit'])->name('edit');
    Route::put('/{akun}', [AkunController::class, 'update'])->name('update');
    Route::put('/{akun}/password', [AkunController::class, 'resetPassword'])->name('password');
    Route::patch('/{akun}/status', [AkunController::class, 'toggleStatus'])->name('status');
    Route::delete('/{akun}', [AkunController::class, 'destroy'])->name('destroy');
});

Route::middleware('auth')->prefix('akun-saya')->name('akun-saya.')->group(function () {
    Route::get('/', [ProfilController::class, 'edit'])->name('edit');
    Route::put('/', [ProfilController::class, 'update'])->name('update');
    Route::put('/password', [ProfilController::class, 'password'])->name('password');
});

/*
|--------------------------------------------------------------------------
| Panel Super User (hanya super_user) - URL /superuser/...
|--------------------------------------------------------------------------
| Dashboard, Kelola Referensi, Kelola Tabel Buku, Histori Semua Domain. (Kelola Akun ada di blok Akun.)
*/
Route::middleware(['auth', 'super.user'])->prefix('superuser')->name('superuser.')->group(function () {
    Route::get('/dashboard', [SuperDashboardController::class, 'index'])->name('dashboard');

    Route::get('/histori', [HistoriSemuaController::class, 'index'])->name('histori-semua.index');

    Route::prefix('referensi')->name('referensi.')->group(function () {
        $jenis = 'provinsi|kabupaten|kelompok-logam|komoditas-logam|kelompok-bukan-logam|komoditas-bukan-logam|kelas-kalori|status-penyelidikan|instansi|status-penyelidikan-pb|klasifikasi-temperatur';

        Route::get('/', [ReferensiController::class, 'index'])->name('index');
        Route::get('/{jenis}', [ReferensiController::class, 'daftar'])->where('jenis', $jenis)->name('daftar');
        Route::get('/{jenis}/create', [ReferensiController::class, 'create'])->where('jenis', $jenis)->name('create');
        Route::post('/{jenis}', [ReferensiController::class, 'store'])->where('jenis', $jenis)->name('store');
        Route::get('/{jenis}/{id}/edit', [ReferensiController::class, 'edit'])->where('jenis', $jenis)->whereNumber('id')->name('edit');
        Route::put('/{jenis}/{id}', [ReferensiController::class, 'update'])->where('jenis', $jenis)->whereNumber('id')->name('update');
        Route::delete('/{jenis}/{id}', [ReferensiController::class, 'destroy'])->where('jenis', $jenis)->whereNumber('id')->name('destroy');
    });

    Route::prefix('tabel-buku')->name('tabel-buku.')->group(function () {
        Route::get('/', [TabelBukuController::class, 'index'])->name('index');
        Route::get('/create', [TabelBukuController::class, 'create'])->name('create');
        Route::post('/', [TabelBukuController::class, 'store'])->name('store');
        Route::get('/{tabel}/edit', [TabelBukuController::class, 'edit'])->name('edit');
        Route::put('/{tabel}', [TabelBukuController::class, 'update'])->name('update');
        Route::delete('/{tabel}', [TabelBukuController::class, 'destroy'])->name('destroy');
    });
});

// /superuser langsung ke Dashboard Super User
Route::redirect('/superuser', '/superuser/dashboard');
