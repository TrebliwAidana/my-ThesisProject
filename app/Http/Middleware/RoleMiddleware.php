<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Maps route name prefixes to the permission slug required to access them.
     * If a user's role is not in the allowed list, we check this map as a
     * fallback — granting access if they have the corresponding permission.
     */
    protected array $routePermissionMap = [
        'members.'   => 'members.view',
        'documents.' => 'documents.view',
        'budgets.'   => 'budgets.view',
        'reports.'   => 'reports.view',
        'admin.users.'   => 'admin.users',
        'admin.roles.'   => 'admin.roles',
        'admin.permissions.' => 'admin.permissions',
        'audit.'     => 'admin.audit',
        'settings.'  => 'admin.users',
    ];

    public function handle(Request $request, Closure $next, ...$allowed): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'You must be logged in to access this page.');
        }

        $user = Auth::user();

        if (!$user->role) {
            abort(403, 'User role not assigned. Please contact administrator.');
        }

        // No restrictions on this route — allow through immediately
        if (empty($allowed)) {
            return $next($request);
        }

        // ── Check 1: Role name / level (existing behaviour, unchanged) ────────
        $allowedLevels = [];
        $allowedRoles  = [];

        foreach ($allowed as $item) {
            if (is_numeric($item)) {
                $allowedLevels[] = (int) $item;
            } else {
                $allowedRoles[] = $item;
            }
        }

        if (!empty($allowedRoles)) {
            foreach ($allowedRoles as $roleName) {
                if ($user->hasRole($roleName)) {
                    return $next($request);
                }
            }
        }

        if (!empty($allowedLevels)) {
            if ((int) $user->role->level <= max($allowedLevels)) {
                return $next($request);
            }
        }

        // ── Check 2: Permission-based fallback ────────────────────────────────
        // If the user's role is not in the allowed list, check whether they
        // have a permission that grants access to this route. This means an
        // Org Member with 'members.view' can access /members even though
        // 'Org Member' is not in the route's role list.
        $routeName = $request->route()?->getName() ?? '';

        foreach ($this->routePermissionMap as $prefix => $permissionSlug) {
            if (str_starts_with($routeName, $prefix)) {
                if ($user->hasPermission($permissionSlug)) {
                    return $next($request);
                }
                // Matched a prefix but no permission — stop checking
                break;
            }
        }

        abort(403, 'Unauthorized. You do not have permission to perform this action.');
    }
}