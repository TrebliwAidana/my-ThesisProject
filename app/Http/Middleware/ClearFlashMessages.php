<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ClearFlashMessages
{
    public function handle(Request $request, Closure $next)
    {
        // Clear flash messages before processing the request
        if (!in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            session()->forget('_flash');
        }
        
        return $next($request);
    }
}