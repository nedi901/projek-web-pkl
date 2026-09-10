<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSuperUserBatubara
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (! $user || !in_array($user->role, ['super_user', 'admin'])) {
            abort(403, 'Kamu gak punya akses ke halaman ini.');
        }

        if ($user->role === 'super_user' && $user->domain_akses !== 'batubara') {
            abort(403, 'Kamu cuma bisa kelola domain: ' . $user->domain_akses);
        }

        return $next($request);
    }
}