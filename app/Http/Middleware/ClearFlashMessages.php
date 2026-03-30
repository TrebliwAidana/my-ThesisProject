<?php
// app/Http/Middleware/ClearOldFlashMessages.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ClearOldFlashMessages
{
    public function handle(Request $request, Closure $next)
    {
        // Get current page URL
        $currentUrl = $request->fullUrl();
        $pageKey = 'flash_shown_' . md5($currentUrl);
        
        // Check if this page has already shown flash messages
        if (session()->has($pageKey)) {
            // Clear all flash messages for this request
            session()->forget('success');
            session()->forget('error');
            session()->forget('warning');
            session()->forget('info');
            session()->forget('_flash');
        }
        
        return $next($request);
    }
}