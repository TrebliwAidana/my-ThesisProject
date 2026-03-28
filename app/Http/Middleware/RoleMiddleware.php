<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'You must be logged in to access this page.');
        }

        $user = Auth::user();
        
        // Check if user has a role
        if (!$user->role) {
            abort(403, 'User role not assigned. Please contact administrator.');
        }

        $userRole = $user->role->name;

        // If no roles are specified, just pass through
        if (empty($roles)) {
            return $next($request);
        }

        if (!in_array($userRole, $roles)) {
            abort(403, 'Unauthorized. You do not have permission to perform this action.');
        }

        return $next($request);
    }
}