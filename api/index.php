<?php

// 1. Load Composer's autoloader (required for all Laravel classes)
require __DIR__ . '/../vendor/autoload.php';

// 2. Bootstrap Laravel (creates the application container)
$app = require_once __DIR__ . '/../bootstrap/app.php';

// 3. Handle the incoming request via Laravel's HTTP kernel
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);
$response->send();

// 4. Terminate the kernel (calls middleware terminators)
$kernel->terminate($request, $response);