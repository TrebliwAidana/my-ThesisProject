<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

/**
 * AdminController
 * Handles: User management, Role management, Permission management.
 * Access: Admin only (enforced in routes/web.php via role:Admin middleware).
 */
class AdminController extends Controller
{
    // =========================================================================
    // USERS
    // =========================================================================

    public function users()
    {
        $users = User::with('role')->latest()->paginate(10);
        return view('admin.users.index', compact('users'));
    }

    public function createUser()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email', 'unique:users,email'],
            'password'  => ['required', 'confirmed', Password::min(8)],
            'role_id'   => ['required', 'exists:roles,id'],
        ]);

        User::create([
            'full_name' => $validated['full_name'],
            'email'     => $validated['email'],
            'password'  => Hash::make($validated['password']),
            'role_id'   => $validated['role_id'],
        ]);

        return redirect()->route('admin.users')
            ->with('success', 'User created successfully.');
    }

    public function editUser(int $id)
    {
        $user  = User::findOrFail($id);
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function updateUser(Request $request, int $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email', 'unique:users,email,' . $user->id],
            'password'  => ['nullable', 'confirmed', Password::min(8)],
            'role_id'   => ['required', 'exists:roles,id'],
        ]);

        $user->full_name = $validated['full_name'];
        $user->email     = $validated['email'];
        $user->role_id   = $validated['role_id'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('admin.users')
            ->with('success', 'User updated successfully.');
    }

    public function destroyUser(int $id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->route('admin.users')
            ->with('success', 'User deleted successfully.');
    }

    // =========================================================================
    // ROLES
    // =========================================================================

    public function roles()
    {
        $roles = Role::with('permissions')->get();
        return view('admin.roles.index', compact('roles'));
    }

    public function storeRole(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:roles,name'],
            'desc' => ['nullable', 'string'],
        ]);

        Role::create($validated);

        return redirect()->route('admin.roles')
            ->with('success', 'Role created successfully.');
    }

    public function destroyRole(int $id)
    {
        $role = Role::findOrFail($id);
        $role->delete();

        return redirect()->route('admin.roles')
            ->with('success', 'Role deleted successfully.');
    }

    // =========================================================================
    // PERMISSIONS
    // =========================================================================

    public function permissions()
    {
        $permissions = Permission::all();
        $roles       = Role::with('permissions')->get();
        return view('admin.permissions.index', compact('permissions', 'roles'));
    }

    public function syncPermissions(Request $request)
    {
        $validated = $request->validate([
            'role_id'        => ['required', 'exists:roles,id'],
            'permission_ids' => ['nullable', 'array'],
            'permission_ids.*' => ['exists:permissions,id'],
        ]);

        $role = Role::findOrFail($validated['role_id']);
        $role->permissions()->sync($validated['permission_ids'] ?? []);

        return redirect()->route('admin.permissions')
            ->with('success', 'Permissions synced successfully.');
    }
}
