<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/** Hanya Super User (alias: super.user). */
class EnsureSuperUser
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->user()?->role !== 'super_user') {
            abort(403, 'Halaman ini hanya untuk Super User.');
        }

        return $next($request);
    }
}
