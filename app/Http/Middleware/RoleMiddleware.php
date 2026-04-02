<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$allowed): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'You must be logged in to access this page.');
        }

        $user = Auth::user();

        if (!$user->role) {
            abort(403, 'User role not assigned. Please contact administrator.');
        }

        // No restrictions – allow access
        if (empty($allowed)) {
            return $next($request);
        }

        // Determine if the allowed values are role names or level numbers
        $allowedLevels = [];
        $allowedRoles = [];

        foreach ($allowed as $item) {
            if (is_numeric($item)) {
                $allowedLevels[] = (int) $item;
            } else {
                $allowedRoles[] = $item;
            }
        }

        // Check by role name (if any)
        if (!empty($allowedRoles)) {
            foreach ($allowedRoles as $roleName) {
                if ($user->hasRole($roleName)) {
                    return $next($request);
                }
            }
        }

        // Check by level (if any)
        if (!empty($allowedLevels)) {
            $userLevel = (int) $user->role->level;
            // If any allowed level is >= user level? Depends on your logic.
            // With your current "lower = higher authority", a user with level 1 can do everything.
            // So allow if user level is <= any allowed level? Or <= max allowed?
            // Let's use: user level <= max allowed level (higher number = less authority)
            if ($userLevel <= max($allowedLevels)) {
                return $next($request);
            }
        }

        abort(403, 'Unauthorized. You do not have permission to perform this action.');
    }
}