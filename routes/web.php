<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/dashboard', function () {
    return '<h1>Dashboard</h1><p>Placeholder — nanti dibuatin beneran.</p>';
})->name('user.dashboard');
Route::get('/login', [LoginController::class, 'create'])->name('login');
Route::post('/login', [LoginController::class, 'store'])->name('login.store');
Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');