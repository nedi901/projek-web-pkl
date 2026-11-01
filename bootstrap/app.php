<?php

use App\Http\Middleware\CheckAdminBatubara;
use App\Http\Middleware\CheckAdminGambut;
use App\Http\Middleware\CheckAdminMineralBukanLogam;
use App\Http\Middleware\CheckAdminMineralLogam;
use App\Http\Middleware\CheckAdminPanasBumi;
use App\Http\Middleware\EnsureAccountActive;
use App\Http\Middleware\EnsureSuperUser;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'super.user' => EnsureSuperUser::class,
            'admin.batubara' => CheckAdminBatubara::class,
            'admin.minerallogam' => CheckAdminMineralLogam::class,
            'admin.mineral-bukan-logam' => CheckAdminMineralBukanLogam::class,
            'admin.panas-bumi' => CheckAdminPanasBumi::class,
            'admin.gambut' => CheckAdminGambut::class,
        ]);

        // akun yang dinonaktifkan otomatis dikeluarkan dari sesi yang sedang berjalan
        $middleware->appendToGroup('web', EnsureAccountActive::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();
