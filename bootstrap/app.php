<?php

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
        'admin.batubara' => \App\Http\Middleware\CheckAdminBatubara::class,
        'admin.minerallogam' => \App\Http\Middleware\CheckAdminMineralLogam::class,
        'admin.mineral-bukan-logam' => \App\Http\Middleware\CheckAdminMineralBukanLogam::class,
        'admin.panas-bumi' => \App\Http\Middleware\CheckAdminPanasBumi::class,
        'admin.gambut' => \App\Http\Middleware\CheckAdminGambut::class,
    ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();

    