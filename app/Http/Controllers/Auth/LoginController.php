<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class LoginController extends Controller
{
    /** Pembatasan tebak-tebakan password: maksimal per akun (+IP) dan per IP, dalam satu jeda. */
    private const MAKS_PER_AKUN = 5;

    private const MAKS_PER_IP = 20;

    private const JEDA_DETIK = 60;

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

        $kunciAkun = 'login|'.$login.'|'.$request->ip();
        $kunciIp = 'login-ip|'.$request->ip();

        foreach ([[$kunciAkun, self::MAKS_PER_AKUN], [$kunciIp, self::MAKS_PER_IP]] as [$kunci, $maks]) {
            if (RateLimiter::tooManyAttempts($kunci, $maks)) {
                $detik = RateLimiter::availableIn($kunci);

                return back()->withErrors([
                    'login' => "Terlalu banyak percobaan login. Coba lagi dalam {$detik} detik.",
                ])->onlyInput('login');
            }
        }

        if (! Auth::attempt([$field => $login, 'password' => $data['password']], $request->boolean('remember'))) {
            RateLimiter::hit($kunciAkun, self::JEDA_DETIK);
            RateLimiter::hit($kunciIp, self::JEDA_DETIK);

            return back()->withErrors([
                'login' => 'Email/username atau password salah.',
            ])->onlyInput('login');
        }

        if (Auth::user()->is_active === false) {
            Auth::logout();
            RateLimiter::hit($kunciAkun, self::JEDA_DETIK);
            RateLimiter::hit($kunciIp, self::JEDA_DETIK);

            return back()->withErrors([
                'login' => 'Akun kamu dinonaktifkan. Hubungi Super User.',
            ])->onlyInput('login');
        }

        $request->session()->regenerate();
        RateLimiter::clear($kunciAkun);

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
