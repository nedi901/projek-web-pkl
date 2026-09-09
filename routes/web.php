<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\User\BatubaraController;
use App\Http\Controllers\User\MineralLogamController;
use App\Http\Controllers\User\MineralBukanLogamController;
use App\Http\Controllers\User\PanasBumiController;


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