<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// Vercel: use /tmp for storage (writable)
if (getenv('VERCEL') === '1' || getenv('APP_STORAGE') === '/tmp') {
    $_ENV['APP_STORAGE'] = '/tmp';
    putenv('APP_STORAGE=/tmp');
}

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Trust all proxies for Railway / Vercel
        $middleware->trustProxies(at: '*');

        $middleware->alias([
            'auth.custom' => \App\Http\Middleware\AuthMiddleware::class,
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'prevent-back-history' => \App\Http\Middleware\PreventBackHistory::class,
            'verified' => \App\Http\Middleware\EnsureEmailIsVerified::class,
        ]);
        
        $middleware->append(\App\Http\Middleware\ClearOldFlashMessages::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->create();

// Apply the writable storage path for Vercel
if (getenv('VERCEL') === '1' || getenv('APP_STORAGE') === '/tmp') {
    $app->useStoragePath('/tmp');
}

return $app;