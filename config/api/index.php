<?php
// This file routes all requests to your actual PHP application
$requestPath = $_SERVER['REQUEST_URI'];
$filePath = __DIR__ . '/../public' . $requestPath;

// If the request maps to an existing file in /public, serve it directly
if (file_exists($filePath) && !is_dir($filePath)) {
    // Let Vercel serve static files normally
    return false;
}

// Otherwise, bootstrap your main PHP application (e.g., index.php in /public)
$frontController = __DIR__ . '/../public/index.php';
if (file_exists($frontController)) {
    require $frontController;
} else {
    http_response_code(500);
    echo "Front controller not found. Make sure public/index.php exists.";
}