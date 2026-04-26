<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * RoleMiddleware
 *
 * Usage in routes:
 *   Route::middleware('role:System Administrator,Club Adviser')
 *
 * Role names in this app (must match DB exactly):
 *   System Administrator  — full access
 *   Club Adviser          — management, no admin panel
 *   Treasurer             — financial + members
 *   Auditor               — financial + members (read/audit)
 *   Guest                 — documents + financial (read-only)
 *
 * How it works:
 *   1. Named role check  — pass if $user->role->name is in the allowed list.
 *   2. Permission fallback — if the role is not in the list, check whether
 *      the user has a permission slug that grants access to this route prefix.
 *      This lets fine-grained per-user overrides work without touching the
 *      route definitions.
 *   3. Denied — redirect to dashboard with an error toast (no raw 403 page).
 */
class RoleMiddleware
{
    /**
     * Maps route-name prefixes → the permission slug required for fallback access.
     *
     * Only define prefixes that need a permission fallback. Routes without an
     * entry here are either open-to-all or rely solely on the named-role check.
     *
     * FIX: was 'FinancialTransaction.' (wrong — never matched financial.* routes)
     *      Corrected to 'financial.' to match actual route names.
     */
    protected array $routePermissionMap = [
        'members.'                   => 'members.view',
        'documents.'                 => 'documents.view',
        'financial.'                 => 'financial_transactions.view',  // FIX: was 'FinancialTransaction.'
        'reports.'                   => 'reports.view',
        'admin.users.'               => 'admin.users',
        'admin.roles.'               => 'admin.roles',
        'admin.permissions.'         => 'admin.permissions',
        'admin.auditlogs.'           => 'admin.audit',
        'admin.document-categories.' => 'admin.document-categories',
        'admin.document-backups.'    => 'admin.document-categories',
        'admin.financial-categories.'=> 'financial_categories.manage',
    ];

    public function handle(Request $request, Closure $next, string ...$roles): mixed
    {
        // FIX: use $request->user() consistently — respects the active guard.
        //      Removed Auth facade (was mixed with $request->user() inconsistently).
        $user = $request->user();

        // FIX: redirect to 'landing' directly — 'login' route just bounces to landing anyway.
        if (! $user) {
            return redirect()->route('landing')
                ->with('error', 'You must be logged in to access this page.');
        }

        // FIX: redirect to dashboard with toast instead of raw abort(403).
        if (! $user->role) {
            return redirect()->route('dashboard')
                ->with('error', 'Your account has no role assigned. Please contact the administrator.');
        }

        // No role restriction on this route — allow through immediately.
        if (empty($roles)) {
            return $next($request);
        }

        // ── Check 1: Named role ───────────────────────────────────────────────
        // Pass immediately if the user's role name is in the allowed list.
        if (in_array($user->role->name, $roles, true)) {
            return $next($request);
        }

        // ── Check 2: Permission-based fallback ────────────────────────────────
        // Allows fine-grained per-user overrides: a role not in the named list
        // can still get through if they hold the relevant permission slug.
        $routeName = $request->route()?->getName() ?? '';

        foreach ($this->routePermissionMap as $prefix => $permissionSlug) {
            if (str_starts_with($routeName, $prefix)) {
                if ($user->hasPermission($permissionSlug)) {
                    return $next($request);
                }
                // Matched the prefix but no permission — stop checking further.
                break;
            }
        }

        // ── Denied ────────────────────────────────────────────────────────────
        // FIX: redirect with toast instead of abort(403) raw error page.
        return redirect()->route('dashboard')
            ->with('error', 'You do not have permission to access that page.');
    }
}