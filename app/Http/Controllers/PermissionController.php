<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.custom');
    }

    public function index(Request $request)
    {
        $roles = Role::with('permissions')->orderBy('level')->get();

        // FIX: Cast to int and use firstWhere instead of find() —
        // Laravel 13 Collection::find() throws BadMethodCallException
        // when passed a string from $request->input().
        $selectedRoleId = (int) $request->input('role_id', $roles->first()?->id);
        $selectedRole   = $roles->firstWhere('id', $selectedRoleId);

        // Define logical display order — core features first, system last.
        $moduleOrder = [
            'members',
            'documents',
            'budgets',
            'reports',
            'organization',
            'users',
            'roles',
            'permissions',
            'admin',
        ];

        // FIX: Avoid chaining merge() between a plain Collection and an
        // Eloquent Collection — Laravel 13 is strict about collection types
        // and throws BadMethodCallException on the merge.
        // Instead, build a plain array sorted by $moduleOrder, then wrap once.
        $allPerms = Permission::all();
        $grouped  = $allPerms->groupBy('module')->toArray(); // plain array keyed by module

        // This is now just plain PHP array manipulation — no Collection conflicts.
        $sorted = [];

        // 1. Add modules in the defined order first
        foreach ($moduleOrder as $module) {
            if (isset($grouped[$module])) {
                $sorted[$module] = $grouped[$module];
                unset($grouped[$module]);
            }
        }

        // 2. Append any remaining modules alphabetically
        ksort($grouped);
        foreach ($grouped as $module => $perms) {
            $sorted[$module] = $perms;
        }

        // 3. Re-wrap as a Collection of Collections so the view works as before
        $permissions = collect($sorted)->map(fn($perms) => collect($perms)->map(
            fn($perm) => (object) $perm
        ));

        $modules = $permissions->keys();

        return view('permissions.index', compact(
            'roles', 'permissions', 'modules', 'selectedRole', 'selectedRoleId'
        ));
    }

    public function update(Request $request, Role $role)
    {
        $validated = $request->validate([
            'permissions'   => 'nullable|array',
            'permissions.*' => 'integer|exists:permissions,id',
        ]);

        $grantedIds = $validated['permissions'] ?? [];

        $role->permissions()->sync($grantedIds);

        $role->users()->each(fn($u) => cache()->forget("user_perms_{$u->id}"));

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