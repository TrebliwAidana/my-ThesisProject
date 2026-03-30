<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Register route-level middleware aliases
        $middleware->alias([
            'auth.custom' => \App\Http\Middleware\AuthMiddleware::class,
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'prevent-back-history' => \App\Http\Middleware\PreventBackHistory::class,
            'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,
        ]);
        
        // Add global middleware (applied to all routes)
        $middleware->append(\App\Http\Middleware\ClearOldFlashMessages::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();