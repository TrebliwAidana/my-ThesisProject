<?php

// 1. Load Composer's autoloader
require __DIR__ . '/../vendor/autoload.php';

// 2. For Vercel, ensure the storage directory exists and is writable
if (getenv('VERCEL') === '1') {
    $storagePath = '/tmp';
    $dirs = [
        $storagePath . '/framework',
        $storagePath . '/framework/views',
        $storagePath . '/framework/cache',
        $storagePath . '/framework/sessions',
        $storagePath . '/logs',
    ];
    foreach ($dirs as $dir) {
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }
    // Set the environment variable for Laravel
    putenv("APP_STORAGE=$storagePath");
    $_ENV['APP_STORAGE'] = $storagePath;
}

// 3. Bootstrap Laravel
$app = require_once __DIR__ . '/../bootstrap/app.php';

// 4. Force storage path to /tmp if on Vercel (overrides the one set in bootstrap/app.php)
if (getenv('VERCEL') === '1') {
    $app->useStoragePath('/tmp');
}

// 5. Handle the request
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);
$response->send();
$kernel->terminate($request, $response);