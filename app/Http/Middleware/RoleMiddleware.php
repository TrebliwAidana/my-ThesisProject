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
     * Admin routes (users, roles, permissions) are intentionally excluded here.
     * Their controllers enforce access via hasPermission() directly — no
     * middleware fallback needed and the old slugs (admin.users, admin.roles,
     * admin.permissions) never existed in the permissions table anyway.
     *
     * FIX: 'financial.' was mapped to 'financial_transactions.view' which does
     *      not exist in the permissions table. Corrected to 'financial.view'.
     *
     * FIX: removed 'admin.users.', 'admin.roles.', 'admin.permissions.',
     *      'admin.auditlogs.', 'admin.document-categories.',
     *      'admin.document-backups.' — all mapped to non-existent slugs and
     *      were silently blocking every non-SysAdmin user. Controllers handle
     *      these via hasPermission() with correct slugs.
     */
    protected array $routePermissionMap = [
        'members.'                    => 'members.view',
        'documents.'                  => 'documents.view',
        'financial.'                  => 'financial.view',           // FIX: was 'financial_transactions.view'
        'reports.'                    => 'reports.view',
        'admin.financial-categories.' => 'financial_categories.manage',
    ];

    public function handle(Request $request, Closure $next, string ...$roles): mixed
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->route('landing')
                ->with('error', 'You must be logged in to access this page.');
        }

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
        return redirect()->route('dashboard')
            ->with('error', 'You do not have permission to access that page.');
    }
}