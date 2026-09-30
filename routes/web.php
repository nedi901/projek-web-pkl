<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\User\BatubaraController;
use App\Http\Controllers\User\MineralLogamController;
use App\Http\Controllers\User\MineralBukanLogamController;
use App\Http\Controllers\User\PanasBumiController;
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

Route::get('/', function () {
    return view('welcome');
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
    Route::get('/grafik', [AdminGrafikController::class, 'index'])->name('grafik.index');  // ← ini harus ada
});


Route::get('/admin/batubara/kabupaten/{provinsi}', function (\App\Models\Provinsi $provinsi) {
    return $provinsi->kabupatens;
})->middleware(['auth', 'admin.batubara']);

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
| Sampah (soft delete) & Riwayat - semua domain
|--------------------------------------------------------------------------
| Tiap domain dapat 4 route dengan middleware akses domain masing-masing:
|   admin.<domain>.sampah.index    GET     daftar data terhapus
|   admin.<domain>.sampah.restore  POST    pulihkan
|   admin.<domain>.sampah.force    DELETE  hapus permanen (cuma super_user, dicek di controller)
|   admin.<domain>.sampah.riwayat  GET     log siapa menghapus/memulihkan
*/
$sampahDomains = [
    // slug domain_akses  => [alias middleware, prefix URL/route]
    'batubara'            => ['admin.batubara',            'batubara'],
    'mineral_logam'       => ['admin.minerallogam',        'mineral-logam'],
    'mineral_bukan_logam' => ['admin.mineral-bukan-logam', 'mineral-bukan-logam'],
    'panas_bumi'          => ['admin.panas-bumi',          'panas-bumi'],
    'gambut'              => ['admin.gambut',              'gambut'],
];

foreach ($sampahDomains as $slug => [$middlewareAlias, $prefix]) {
    Route::middleware(['auth', $middlewareAlias])
        ->prefix("admin/{$prefix}/sampah")
        ->name("admin.{$prefix}.sampah.")
        ->group(function () use ($slug) {
            Route::get('/', [\App\Http\Controllers\Admin\SampahController::class, 'index'])
                ->defaults('domain', $slug)->name('index');
            Route::get('/riwayat', [\App\Http\Controllers\Admin\SampahController::class, 'riwayat'])
                ->defaults('domain', $slug)->name('riwayat');
            Route::post('/{id}/pulihkan', [\App\Http\Controllers\Admin\SampahController::class, 'restore'])
                ->defaults('domain', $slug)->whereNumber('id')->name('restore');
            Route::delete('/{id}/permanen', [\App\Http\Controllers\Admin\SampahController::class, 'forceDestroy'])
                ->defaults('domain', $slug)->whereNumber('id')->name('force');
        });
}
