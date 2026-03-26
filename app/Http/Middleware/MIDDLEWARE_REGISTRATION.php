<?php

// ================================================================
// MIDDLEWARE REGISTRATION — bootstrap/app.php  (Laravel 11)
// ================================================================
// Open your existing bootstrap/app.php and add the
// ->withMiddleware() block shown below.
// ================================================================

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )

    // ↓ ADD THIS BLOCK ↓
    ->withMiddleware(function (Middleware $middleware) {

        $middleware->alias([
            'auth.custom' => \App\Http\Middleware\AuthMiddleware::class,
            'role'        => \App\Http\Middleware\RoleMiddleware::class,
        ]);

    })
    // ↑ END OF BLOCK ↑

    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
