<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

        if ($user->role === 'admin') {
            return redirect()->intended('/admin/dashboard');
        }

        if ($user->role === 'super_user') {
            // Arahin langsung ke halaman Grafik domain masing-masing, bukan ke /dashboard generic.
            // Kalau domain_akses-nya belum punya route grafik (mis. panas_bumi, gambut - belum
            // dibikin), fallback ke /dashboard biar gak error "route not defined".
            $grafikRoute = match ($user->domain_akses) {
                'batubara' => 'superuser.batubara.grafik.index',
                'mineral_logam' => 'superuser.mineral-logam.grafik.index',
                'mineral_bukan_logam' => 'superuser.mineral-bukan-logam.grafik.index',
                'panas_bumi' => 'superuser.panas-bumi.grafik.index',
                default => null,
            };

            if ($grafikRoute && \Illuminate\Support\Facades\Route::has($grafikRoute)) {
                // Sengaja PAKE redirect() biasa, BUKAN ->intended() - soalnya intended()
                // bakal ngutamain URL yang ke-simpen di session (halaman yang dicoba diakses
                // sebelum login), bukan tujuan yang kita paksa di sini. Kalau kepake
                // intended(), super_user bisa nyasar balik ke halaman Data alih-alih Grafik.
                return redirect()->to(route($grafikRoute));
            }

            return redirect()->to('/dashboard');
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