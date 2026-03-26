<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * Usage in routes or constructors:
     *   middleware('role:Admin')
     *   middleware('role:Admin,Officer')
     *   middleware('role:Admin,Officer,Auditor')
     *
     * @param string ...$roles  One or more allowed role names
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'You must be logged in to access this page.');
        }

        $userRole = Auth::user()->role->name ?? null;

        if (!in_array($userRole, $roles)) {
            abort(403, 'Unauthorized. You do not have permission to perform this action.');
        }

        return $next($request);
    }
}
