<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleController extends Controller
{
    /**
     * Mengarahkan pengguna ke halaman autentikasi Google.
     */
    public function redirectToGoogle(): RedirectResponse
    {
        if (blank(config('services.google.client_id')) || blank(config('services.google.client_secret'))) {
            return redirect()->route('login')->withErrors([
                'login' => 'Google Login belum dikonfigurasi. Harap isi GOOGLE_CLIENT_ID dan GOOGLE_CLIENT_SECRET di file .env.',
            ]);
        }

        return Socialite::driver('google')->redirect();
    }

    /**
     * Menangani callback autentikasi dari Google.
     */
    public function handleGoogleCallback(Request $request): RedirectResponse
    {
        if ($request->has('error')) {
            return redirect()->route('login')->withErrors([
                'login' => 'Login dengan Google dibatalkan atau ditolak.',
            ]);
        }

        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (Throwable $e) {
            Log::warning('Gagal autentikasi Google OAuth', ['error' => $e->getMessage()]);

            return redirect()->route('login')->withErrors([
                'login' => 'Gagal menghubungkan ke akun Google. Silakan coba beberapa saat lagi.',
            ]);
        }

        $email = Str::lower(trim((string) $googleUser->getEmail()));
        $googleId = (string) $googleUser->getId();
        $avatar = $googleUser->getAvatar();
        $name = $googleUser->getName() ?: ($googleUser->getNickname() ?: 'Pengguna Google');

        if (blank($email)) {
            return redirect()->route('login')->withErrors([
                'login' => 'Akun Google tidak menyediakan alamat email yang valid.',
            ]);
        }

        // Cari berdasarkan google_id terlebih dahulu, atau cari berdasarkan email yang sudah terdaftar
        $user = User::where('google_id', $googleId)->first();

        if (! $user) {
            $user = User::where('email', $email)->first();

            if ($user) {
                // Tautkan akun Google ke user yang sudah ada
                $user->forceFill([
                    'google_id' => $googleId,
                    'avatar' => $avatar,
                ])->save();
            } else {
                // Buat akun baru otomatis jika belum ada (role default: user read-only)
                $username = $this->generateUniqueUsername($email);

                $user = User::create([
                    'name' => $name,
                    'username' => $username,
                    'email' => $email,
                    'google_id' => $googleId,
                    'avatar' => $avatar,
                    'password' => null,
                    'role' => 'user',
                    'domain_akses' => null,
                    'is_active' => true,
                ]);
            }
        } else {
            // Update avatar terbaru jika berubah
            if ($avatar && $user->avatar !== $avatar) {
                $user->forceFill(['avatar' => $avatar])->saveQuietly();
            }
        }

        if ($user->is_active === false) {
            return redirect()->route('login')->withErrors([
                'login' => 'Akun kamu dinonaktifkan. Hubungi Super User.',
            ]);
        }

        Auth::login($user, remember: true);

        $request->session()->regenerate();

        $user->forceFill(['last_login_at' => now()])->saveQuietly();

        return redirect()->to($user->getRedirectUrlAfterLogin());
    }

    /**
     * Menghasilkan username unik dari alamat email.
     */
    private function generateUniqueUsername(string $email): string
    {
        $base = Str::limit(Str::slug(Str::before($email, '@'), '_'), 25, '') ?: 'user';
        $username = $base;
        $counter = 1;

        while (User::where('username', $username)->exists()) {
            $username = $base.$counter++;
        }

        return $username;
    }
}
