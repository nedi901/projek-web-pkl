<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminPanasBumi
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // Hierarki BARU (hasil tukar posisi):
        // - super_user : cuma 1, kendali penuh atas SEMUA domain & fitur
        // - admin      : banyak, masing-masing dibatasi ke 1 domain lewat domain_akses
        if (! $user || ! in_array($user->role, ['super_user', 'admin'])) {
            abort(403, 'Kamu gak punya akses ke halaman ini.');
        }

        if ($user->role === 'admin' && $user->domain_akses !== 'panas_bumi') {
            abort(403, 'Kamu cuma bisa kelola domain: ' . $user->domain_akses);
        }

        return $next($request);
    }
}
