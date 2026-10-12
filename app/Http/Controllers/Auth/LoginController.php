<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required'],
        ]);

        // bisa login pakai email ATAU username
        $field = filter_var($data['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $login = Str::lower(trim($data['login']));

        if (! Auth::attempt([$field => $login, 'password' => $data['password']], $request->boolean('remember'))) {
            return back()->withErrors([
                'login' => 'Email/username atau password salah.',
            ])->onlyInput('login');
        }

        if (Auth::user()->is_active === false) {
            Auth::logout();

            return back()->withErrors([
                'login' => 'Akun kamu dinonaktifkan. Hubungi Super User.',
            ])->onlyInput('login');
        }

        $request->session()->regenerate();

        Auth::user()->forceFill(['last_login_at' => now()])->saveQuietly();

        $user = Auth::user();

        // Hierarki: super_user (1 orang, kendali penuh) > admin (banyak, per domain) > user (read-only)

        if ($user->role === 'admin') {
            // Admin domain -> langsung ke Grafik domain miliknya sendiri
            $grafikRoute = match ($user->domain_akses) {
                'batubara' => 'admin.batubara.grafik.index',
                'mineral_logam' => 'admin.mineral-logam.grafik.index',
                'mineral_bukan_logam' => 'admin.mineral-bukan-logam.grafik.index',
                'panas_bumi' => 'admin.panas-bumi.grafik.index',
                'gambut' => 'admin.gambut.grafik.index',
                default => null,
            };

            // redirect()->to(), BUKAN intended() - biar gak dibajak URL tersimpan di session
            if ($grafikRoute && Route::has($grafikRoute)) {
                return redirect()->to(route($grafikRoute));
            }

            return redirect()->to('/dashboard');
        }

        if ($user->role === 'super_user') {
            // Super User -> Dashboard (ringkasan semua domain, akun, aktivitas terbaru)
            return redirect()->to(route('superuser.dashboard'));
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
