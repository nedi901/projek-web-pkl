<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\User\BatubaraController;
use App\Http\Controllers\User\MineralLogamController;
use App\Http\Controllers\User\MineralBukanLogamController;
use App\Http\Controllers\User\PanasBumiController;
use App\Http\Controllers\User\GrafikController;
use App\Http\Controllers\SuperUser\BatubaraController as SuperUserBatubaraController;


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

Route::middleware(['auth', 'superuser.batubara'])->prefix('superuser/batubara')->name('superuser.batubara.')->group(function () {
    Route::get('/', [SuperUserBatubaraController::class, 'index'])->name('index');
    Route::get('/create', [SuperUserBatubaraController::class, 'create'])->name('create');
    Route::post('/', [SuperUserBatubaraController::class, 'store'])->name('store');
    Route::get('/{batubara}/edit', [SuperUserBatubaraController::class, 'edit'])->name('edit');
    Route::put('/{batubara}', [SuperUserBatubaraController::class, 'update'])->name('update');
    Route::delete('/{batubara}', [SuperUserBatubaraController::class, 'destroy'])->name('destroy');
    Route::get('/import', [SuperUserBatubaraController::class, 'importForm'])->name('import.form');
Route::post('/import', [SuperUserBatubaraController::class, 'import'])->name('import');
});

Route::get('/superuser/batubara/kabupaten/{provinsi}', function (\App\Models\Provinsi $provinsi) {
    return $provinsi->kabupatens;
})->middleware(['auth', 'superuser.batubara']);