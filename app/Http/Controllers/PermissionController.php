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
            $selectedRole = $roles->first();
            $selectedRoleId = $selectedRole->id;
        }

        // Define logical display order — core features first, system last.
        $moduleOrder = [
            'members',
            'documents',
            'financial',    
            'reports',
            'organization',
            'users',
            'roles',
            'permissions',
            'admin',
            'audit',          
            'activities', 
        ];

        $allPerms = Permission::all();
        $grouped  = $allPerms->groupBy('module')->toArray();

        $sorted = [];
        foreach ($moduleOrder as $module) {
            if (isset($grouped[$module])) {
                $sorted[$module] = $grouped[$module];
                unset($grouped[$module]);
            }
        }
        ksort($grouped);
        foreach ($grouped as $module => $perms) {
            $sorted[$module] = $perms;
        }

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