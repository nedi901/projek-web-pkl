<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Dipasang di grup "web": kalau akun yang sedang login dinonaktifkan Super User,
 * sesinya langsung diakhiri pada request berikutnya.
 */
class EnsureAccountActive
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // === false: aman kalau kolom is_active belum ada (migration belum dijalankan)
        if ($user && $user->is_active === false) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->withErrors(['login' => 'Akun kamu sudah dinonaktifkan. Hubungi Super User.']);
        }

        return $next($request);
    }
}
