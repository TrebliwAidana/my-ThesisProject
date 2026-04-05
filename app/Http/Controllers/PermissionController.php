<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermissionController extends Controller
{
    public function __construct()
    {
        // FIX: Removed duplicate auth.custom + manual level-1 check that was
        // inside every method. The route is already gated with:
        //   middleware('role:System Administrator')
        // so auth + role enforcement happens before the controller is reached.
        $this->middleware('auth.custom');
    }

    public function index(Request $request)
    {
        $roles       = Role::with('permissions')->orderBy('level')->get();
        $permissions = Permission::all()->groupBy('module');
        $modules     = $permissions->keys();

        $selectedRoleId = $request->get('role_id', $roles->first()?->id);
        $selectedRole   = $roles->find($selectedRoleId);

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

        // Bust cached permission sets for every user with this role.
        // Role::users() is hasMany so each() works correctly.
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