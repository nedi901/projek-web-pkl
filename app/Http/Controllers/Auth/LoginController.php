<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class LoginController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()->withErrors([
                'email' => 'Email atau password salah.',
            ])->onlyInput('email');
        }

        $request->session()->regenerate();

        $user = Auth::user();

        // Hierarki: super_user (1 orang, kendali penuh) > admin (banyak, per domain) > user (read-only)

        if ($user->role === 'admin') {
            // Admin domain -> langsung ke Grafik domain miliknya sendiri
            $grafikRoute = match ($user->domain_akses) {
                'batubara' => 'superuser.batubara.grafik.index',
                'mineral_logam' => 'superuser.mineral-logam.grafik.index',
                'mineral_bukan_logam' => 'superuser.mineral-bukan-logam.grafik.index',
                'panas_bumi' => 'superuser.panas-bumi.grafik.index',
                'gambut' => 'superuser.gambut.grafik.index',
                default => null,
            };

            // redirect()->to(), BUKAN intended() - biar gak dibajak URL tersimpan di session
            if ($grafikRoute && Route::has($grafikRoute)) {
                return redirect()->to(route($grafikRoute));
            }

            return redirect()->to('/dashboard');
        }

        if ($user->role === 'super_user') {
            // Super user belum punya halaman khusus (manajemen admin & user belum dibuat),
            // sementara dimasukin ke Grafik Batubara - sidebar-nya nampilin SEMUA domain.
            return redirect()->to(route('superuser.batubara.grafik.index'));
        }

        return redirect()->intended('/dashboard');
    }

    public function destroy(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
