<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.custom');
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        // Permission check: System Admin bypasses, otherwise requires permissions.view
        if ($user->role->level !== 1 && !$user->hasPermission('permissions.view')) {
            abort(403, 'You do not have permission to view the permission matrix.');
        }

        // Only fetch visible roles for the permission matrix
        $roles = Role::where('is_visible', true)
            ->with('permissions')
            ->orderBy('level')
            ->get();

        // If no visible roles, abort or show empty state
        if ($roles->isEmpty()) {
            abort(403, 'No visible roles found. Please unhide a role first.');
        }

        $selectedRoleId = (int) $request->input('role_id', $roles->first()?->id);
        $selectedRole   = $roles->firstWhere('id', $selectedRoleId);

        // If the requested role_id is not among visible roles, fallback to the first visible role
        if (!$selectedRole && $roles->isNotEmpty()) {
            $selectedRole   = $roles->first();
            $selectedRoleId = $selectedRole->id;
        }

        /*
         | ─────────────────────────────────────────────────────────────────────
         | MODULE DISPLAY ORDER
         | ─────────────────────────────────────────────────────────────────────
         | Defines the order modules appear in the Permission Matrix UI.
         | Any module slug NOT listed here is appended alphabetically at the end.
         |
         | FIX: Added 'categories', 'financial_categories', and 'backups' which
         | were present in the seeder's $matrix but missing here, causing those
         | modules to be sorted alphabetically at the bottom instead of grouped
         | logically — and making them easy to overlook when assigning perms.
         | ─────────────────────────────────────────────────────────────────────
         */
        $moduleOrder = [
            // ── Core records ──────────────────────────────
            'members',
            'documents',
            'categories',           // document categories
            // ── Financial ─────────────────────────────────
            'financial',
            'financial_categories',
            // ── Reporting ─────────────────────────────────
            'reports',
            // ── System / Infrastructure ───────────────────
            'backups',              // Backup & Restore
            'users',
            'roles',
            'permissions',
            // ── Monitoring ────────────────────────────────
            'audit',
            'activities',
        ];

        $allPerms = Permission::all();
        $grouped  = $allPerms->groupBy('module')->toArray();

        // Build sorted map: listed modules first (in order), then any unlisted ones alphabetically
        $sorted = [];
        foreach ($moduleOrder as $module) {
            if (isset($grouped[$module])) {
                $sorted[$module] = $grouped[$module];
                unset($grouped[$module]);
            }
        }
        // Append any remaining modules not in $moduleOrder, sorted alphabetically
        ksort($grouped);
        foreach ($grouped as $module => $perms) {
            $sorted[$module] = $perms;
        }

        $permissions = collect($sorted)->map(
            fn($perms) => collect($perms)->map(fn($perm) => (object) $perm)
        );

        $modules = $permissions->keys();

        return view('permissions.index', compact(
            'roles', 'permissions', 'modules', 'selectedRole', 'selectedRoleId'
        ));
    }

    public function update(Request $request, Role $role)
    {
        $user = Auth::user();

        // Permission check: System Admin bypasses, otherwise requires permissions.edit
        if ($user->role->level !== 1 && !$user->hasPermission('permissions.edit')) {
            abort(403, 'You do not have permission to edit permissions.');
        }

        // Ensure the role being updated is visible (prevent tampering)
        if (!$role->is_visible) {
            return redirect()->route('admin.permissions.index')
                ->with('error', 'Cannot update permissions for a hidden role.');
        }

        $validated = $request->validate([
            'permissions'   => 'nullable|array',
            'permissions.*' => 'integer|exists:permissions,id',
        ]);

        $grantedIds = $validated['permissions'] ?? [];

        $role->permissions()->sync($grantedIds);

        /*
         | ─────────────────────────────────────────────────────────────────────
         | CACHE INVALIDATION
         | ─────────────────────────────────────────────────────────────────────
         | CRITICAL: The cache key used here MUST exactly match the key used
         | inside User::hasPermission(). A mismatch means saved permission
         | changes are ignored until the cache naturally expires, causing users
         | to get stale 403s even after being granted access.
         |
         | Common mismatch example:
         |   hasPermission() stores  → "user_permissions_{id}"
         |   This line cleared       → "user_perms_{id}"          ← BUG
         |
         | Check your User model's hasPermission() method and align the key
         | below to match it exactly. Both patterns are shown — keep only one:
         |
         |   Pattern A (short key):  "user_perms_{$u->id}"
         |   Pattern B (long key):   "user_permissions_{$u->id}"
         |
         | If you are still getting 403s after saving permissions, this cache
         | mismatch is almost certainly the cause.
         | ─────────────────────────────────────────────────────────────────────
         */
        $role->users()->each(function ($u) {
            // ── Adjust this key to match User::hasPermission() exactly ──
            cache()->forget("user_perms_{$u->id}");        // Pattern A
            cache()->forget("user_permissions_{$u->id}");  // Pattern B (belt-and-suspenders)
        });

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Permissions updated for {$role->name}.",
                'granted' => count($grantedIds),
            ]);
        }

        return redirect()
            ->route('admin.permissions.index', ['role_id' => $role->id])
            ->with('success', "Permissions for {$role->name} updated successfully!");
    }
}