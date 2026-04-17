<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Notifications\NewUserWelcomeNotification;
use App\Notifications\PasswordResetNotification;

class AdminController extends Controller
{
    // =========================================================================
    // ROLES
    // =========================================================================

    public function roles(Request $request)
    {
        $query = Role::withCount('users')->with('permissions', 'users');
        if (!$request->boolean('show_hidden')) {
            $query->where('is_visible', true);
        }
        $roles = $query->orderBy('level')->get();
        $showHidden = $request->boolean('show_hidden');
        $permissions = Permission::all();

        return view('admin.roles.index', compact('roles', 'permissions', 'showHidden'));
    }

    public function createRole()
    {
        $roles = Role::where('is_visible', true)->orderBy('level')->get();
        return view('admin.roles.create', compact('roles'));
    }

    public function editRole(int $id)
    {
        $role = Role::with('permissions')->findOrFail($id);
        if (!$role->is_visible) {
            return redirect()->route('admin.roles.index')
                ->with('error', 'Cannot edit a hidden role.');
        }
        $permissions = Permission::all();
        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    public function storeRole(Request $request)
    {
        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:100', 'unique:roles,name'],
            'abbreviation' => ['nullable', 'string', 'max:10'],
            'description'  => ['nullable', 'string', 'max:500'],
            'level'        => ['required', 'integer', 'min:1', 'max:10'],
            'is_system'    => ['nullable', 'boolean'],
            'parent_id'    => ['nullable', 'exists:roles,id'],
        ]);

        if ($validated['level'] == 1 && !($validated['is_system'] ?? false)) {
            return back()->with('error', 'Level 1 is reserved for system roles only.')
                         ->withInput();
        }

        try {
            $role = Role::create([
                'name'         => $validated['name'],
                'abbreviation' => $validated['abbreviation'] ?? null,
                'description'  => $validated['description'] ?? null,
                'level'        => $validated['level'],
                'is_system'    => $validated['is_system'] ?? false,
                'parent_id'    => $validated['parent_id'] ?? null,
                'is_visible'   => true,
            ]);

            if ($request->has('permissions')) {
                $role->permissions()->sync($request->permissions);
            }

            return redirect()->route('admin.roles.index')
                ->with('success', "✅ Role '{$role->name}' created successfully.");
        } catch (\Exception $e) {
            return back()->with('error', '❌ Failed to create role: ' . $e->getMessage())->withInput();
        }
    }

    public function updateRole(Request $request, int $id)
    {
        $role = Role::findOrFail($id);

        if (!$role->is_visible) {
            return redirect()->route('admin.roles.index')
                ->with('error', 'Cannot update a hidden role.');
        }

        if ($role->is_predefined) {
            $validated = $request->validate([
                'abbreviation' => ['nullable', 'string', 'max:10'],
                'description'  => ['nullable', 'string', 'max:500'],
                'level'        => ['required', 'integer', 'min:1', 'max:10'],
            ]);
            $role->update($validated);
            if ($request->has('permissions')) {
                $role->permissions()->sync($request->permissions);
            }
            return redirect()->route('admin.roles.index')
                ->with('success', "✅ Predefined role '{$role->name}' updated successfully.");
        }

        $validated = $request->validate([
            'name'         => ['required', 'string', 'max:100', 'unique:roles,name,' . $id],
            'abbreviation' => ['nullable', 'string', 'max:10'],
            'description'  => ['nullable', 'string', 'max:500'],
            'level'        => ['required', 'integer', 'min:1', 'max:10'],
            'is_system'    => ['nullable', 'boolean'],
        ]);

        if ($validated['level'] == 1 && !($validated['is_system'] ?? false)) {
            return back()->with('error', 'Level 1 is reserved for system roles only.')
                         ->withInput();
        }

        try {
            $role->update([
                'name'         => $validated['name'],
                'abbreviation' => $validated['abbreviation'] ?? null,
                'description'  => $validated['description'] ?? null,
                'level'        => $validated['level'],
                'is_system'    => $validated['is_system'] ?? $role->is_system,
            ]);
            $role->permissions()->sync($request->permissions ?? []);

            return redirect()->route('admin.roles.index')
                ->with('success', "✅ Role '{$role->name}' updated successfully.");
        } catch (\Exception $e) {
            return back()->with('error', '❌ Failed to update role: ' . $e->getMessage())->withInput();
        }
    }

    public function destroyRole(int $id)
    {
        $role = Role::findOrFail($id);

        if (!$role->is_visible) {
            return back()->with('error', 'Cannot delete a hidden role.');
        }
        if ($role->is_predefined) {
            return back()->with('error', '⚠️ Cannot delete a predefined system role.');
        }
        if ($role->users()->count() > 0) {
            return back()->with('error', '⚠️ Cannot delete a role that has users assigned to it.');
        }

        try {
            $roleName = $role->name;
            $role->delete();
            return redirect()->route('admin.roles.index')
                ->with('success', "✅ Role '{$roleName}' deleted successfully.");
        } catch (\Exception $e) {
            return back()->with('error', '❌ Failed to delete role: ' . $e->getMessage());
        }
    }

    /**
     * Toggle the visibility of a role (hide/unhide).
     * Hidden roles are excluded from all user-facing role selectors.
     *
     * Only two things are blocked:
     *   1. Hiding the System Administrator role (id = 1) — never allowed.
     *   2. Hiding your own role — would lock yourself out.
     * Everything else, including predefined roles with users, can be hidden freely.
     */
    public function toggleRoleVisibility(Role $role)
    {
        // System Administrator can never be hidden
        if ($role->id === 1) {
            return redirect()->route('admin.roles.index', ['show_hidden' => request()->boolean('show_hidden')])
                ->with('error', '⚠️ The System Administrator role cannot be hidden.');
        }

        // Prevent hiding your own role
        if (auth()->user()->role_id == $role->id && $role->is_visible) {
            return redirect()->route('admin.roles.index', ['show_hidden' => request()->boolean('show_hidden')])
                ->with('error', '⚠️ You cannot hide your own role.');
        }

        $role->update(['is_visible' => !$role->is_visible]);

        $status = $role->is_visible ? 'visible' : 'hidden';
        return redirect()->route('admin.roles.index', ['show_hidden' => request()->boolean('show_hidden')])
            ->with('success', "✅ Role '{$role->name}' is now {$status}.");
    }

    // =========================================================================
    // USERS
    // =========================================================================

    public function users(Request $request)
    {
        $query = User::with('role');

        if ($request->boolean('trashed')) {
            $query->withTrashed();
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->whereHas('role', fn($q) => $q->where('name', $request->role));
        }

        if ($request->filled('status')) {
            $isActive = $request->status === 'active';
            $query->where('is_active', $isActive);
        }

        if ($request->filled('verification')) {
            if ($request->verification === 'verified') {
                $query->whereNotNull('email_verified_at');
            } elseif ($request->verification === 'unverified') {
                $query->whereNull('email_verified_at');
            }
        }

        $users = $query->paginate(20)->appends($request->except('page'));

        $totalUsers     = User::count();
        $activeUsers    = User::where('is_active', true)->count();
        $verifiedEmails = User::whereNotNull('email_verified_at')->count();
        $recentLogins   = User::where('last_login_at', '>=', now()->subDays(7))->count();

        // Only visible roles appear in filter dropdowns
        $roles = Role::where('is_visible', true)->get();

        return view('admin.users.index', compact(
            'totalUsers', 'activeUsers', 'verifiedEmails',
            'recentLogins', 'users', 'roles'
        ));
    }

    public function createUser()
    {
        // Only visible roles are offered when creating a user
        $roles = Role::where('is_visible', true)->orderBy('level')->get();
        return view('admin.users.create', compact('roles'));
    }

    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'first_name'  => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name'   => ['required', 'string', 'max:255'],
            'email'       => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'role_id'     => ['required', 'exists:roles,id'],
            'position'    => ['nullable', 'string', 'max:255'],
            'student_id'  => ['nullable', 'string', 'max:20', 'unique:users,student_id'],
            'year_level'  => ['nullable', 'string', 'max:20'],
            'gender'      => ['required', 'string', 'in:Male,Female,Other'],
            'phone'       => ['nullable', 'string', 'max:20', 'unique:users,phone'],
            'birthday'    => ['nullable', 'date'],
            'password'    => ['nullable', 'string', 'min:8', 'confirmed'],
            'is_active'   => ['boolean'],
        ], [
            'email.unique'      => 'This email is already registered.',
            'student_id.unique' => 'This student ID is already assigned.',
            'phone.unique'      => 'This phone number is already in use.',
        ]);

        // Ensure the selected role is visible (guards against tampered form submissions)
        $selectedRole = Role::where('id', $validated['role_id'])
            ->where('is_visible', true)
            ->first();
        if (!$selectedRole) {
            return back()->withErrors(['role_id' => 'The selected role is not available.'])->withInput();
        }

        if (!empty($validated['phone'])) {
            $phone = preg_replace('/[^0-9]/', '', $validated['phone']);
            if (substr($phone, 0, 2) == '63') $phone = substr($phone, 2);
            if (substr($phone, 0, 1) == '0')  $phone = substr($phone, 1);
            $validated['phone'] = '+63' . substr($phone, 0, 10);
        }

        if ($this->shouldClearYearLevel($selectedRole->id, $validated['position'] ?? '')) {
            $validated['year_level'] = null;
        }

        $allowedPositions = $this->getAllowedPositions($selectedRole->name, $selectedRole->abbreviation);
        if (!empty($allowedPositions)) {
            if (empty($validated['position'])) {
                return back()->withErrors(['position' => 'Position is required for this role.'])->withInput();
            }
            if (!in_array($validated['position'], $allowedPositions)) {
                return back()->withErrors(['position' => "Invalid position for role '{$selectedRole->name}'."])->withInput();
            }
        } else {
            $validated['position'] = null;
        }

        if ($selectedRole->id == 1) {
            $validated['position'] = null;
        }

        $fullName = trim(
            $validated['first_name'] . ' ' .
            ($validated['middle_name'] ? $validated['middle_name'] . ' ' : '') .
            $validated['last_name']
        );

        $password = $validated['password'] ?? Str::random(10);

        $user = User::create([
            'first_name'        => $validated['first_name'],
            'middle_name'       => $validated['middle_name'] ?? null,
            'last_name'         => $validated['last_name'],
            'full_name'         => $fullName,
            'email'             => $validated['email'],
            'password'          => Hash::make($password),
            'role_id'           => $validated['role_id'],
            'position'          => $validated['position'],
            'student_id'        => $validated['student_id'] ?? null,
            'year_level'        => $validated['year_level'] ?? null,
            'gender'            => $validated['gender'],
            'phone'             => $validated['phone'] ?? null,
            'birthday'          => $validated['birthday'] ?? null,
            'is_active'         => $validated['is_active'] ?? true,
            'email_verified_at' => now(),
        ]);

        $user->notify(new NewUserWelcomeNotification($password));

        return redirect()->route('admin.users.index')
            ->with('success', "User '{$user->full_name}' created successfully. A welcome email has been sent.");
    }

    public function editUser(int $id)
    {
        $user  = User::findOrFail($id);
        // Only visible roles offered when editing a user
        $roles = Role::where('is_visible', true)->orderBy('level')->get();
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function updateUser(Request $request, int $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'first_name'  => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name'   => ['required', 'string', 'max:255'],
            'email'       => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $id],
            'role_id'     => ['required', 'exists:roles,id'],
            'position'    => ['nullable', 'string', 'max:255'],
            'student_id'  => ['nullable', 'string', 'max:20', 'unique:users,student_id,' . $id],
            'year_level'  => ['nullable', 'string', 'max:20'],
            'gender'      => ['required', 'string', 'in:Male,Female,Other'],
            'phone'       => ['nullable', 'string', 'max:20', 'unique:users,phone,' . $id],
            'birthday'    => ['nullable', 'date'],
            'password'    => ['nullable', 'string', 'min:8', 'confirmed'],
            'is_active'   => ['boolean'],
        ], [
            'email.unique'      => 'This email is already registered.',
            'student_id.unique' => 'This student ID is already assigned.',
            'phone.unique'      => 'This phone number is already in use.',
        ]);

        // Ensure the new role is visible (guards against tampered form submissions)
        $selectedRole = Role::where('id', $validated['role_id'])
            ->where('is_visible', true)
            ->first();
        if (!$selectedRole) {
            return back()->withErrors(['role_id' => 'The selected role is not available.'])->withInput();
        }

        if (!empty($validated['phone'])) {
            $phone = preg_replace('/[^0-9]/', '', $validated['phone']);
            if (substr($phone, 0, 2) == '63') $phone = substr($phone, 2);
            if (substr($phone, 0, 1) == '0')  $phone = substr($phone, 1);
            $validated['phone'] = '+63' . substr($phone, 0, 10);
        }

        if ($this->shouldClearYearLevel($selectedRole->id, $validated['position'] ?? '')) {
            $validated['year_level'] = null;
        }

        $allowedPositions = $this->getAllowedPositions($selectedRole->name, $selectedRole->abbreviation);
        if (!empty($allowedPositions)) {
            if (empty($validated['position'])) {
                return back()->withErrors(['position' => 'Position is required for this role.'])->withInput();
            }
            if (!in_array($validated['position'], $allowedPositions)) {
                return back()->withErrors(['position' => "Invalid position for role '{$selectedRole->name}'."])->withInput();
            }
        } else {
            $validated['position'] = null;
        }

        // Only protect System Administrator (id = 1) from losing its last user.
        // All other roles, including predefined ones, can have their last user
        // moved freely so the role can then be hidden.
        if ($user->role_id != $validated['role_id']) {
            $oldRole = Role::find($user->role_id);
            if ($oldRole && $oldRole->id === 1) {
                $count = User::where('role_id', 1)->count();
                if ($count <= 1) {
                    return back()->with('error', '⚠️ Cannot change the role of the last System Administrator.');
                }
            }
        }

        $fullName = trim(
            $validated['first_name'] . ' ' .
            ($validated['middle_name'] ? $validated['middle_name'] . ' ' : '') .
            $validated['last_name']
        );

        $data = [
            'first_name'  => $validated['first_name'],
            'middle_name' => $validated['middle_name'] ?? null,
            'last_name'   => $validated['last_name'],
            'full_name'   => $fullName,
            'email'       => $validated['email'],
            'role_id'     => $validated['role_id'],
            'student_id'  => $validated['student_id'] ?? null,
            'year_level'  => $validated['year_level'] ?? null,
            'gender'      => $validated['gender'],
            'phone'       => $validated['phone'] ?? null,
            'birthday'    => $validated['birthday'] ?? null,
            'is_active'   => $validated['is_active'] ?? $user->is_active,
            'position'    => $selectedRole->id == 1 ? null : $validated['position'],
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')
            ->with('success', "User '{$user->full_name}' updated successfully.");
    }

    public function destroyUser(int $id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        // Only protect System Administrator (id = 1) from losing its last user.
        // All other roles, including predefined ones, can have their last user
        // deleted freely so the role can then be hidden.
        if ($user->role && $user->role->id === 1) {
            $count = User::where('role_id', 1)->count();
            if ($count <= 1) {
                return back()->with('error', '⚠️ Cannot delete the last System Administrator.');
            }
        }

        $userName = $user->full_name;
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "User '{$userName}' deleted successfully.");
    }

    public function resetPassword(int $id)
    {
        $user        = User::findOrFail($id);
        $newPassword = Str::random(10);
        $user->password = Hash::make($newPassword);
        $user->save();

        $user->notify(new PasswordResetNotification($newPassword));

        return redirect()->route('admin.users.index')
            ->with('success', "Password for '{$user->full_name}' reset. An email has been sent.");
    }

    public function sendVerificationEmail(int $id)
    {
        $user = User::findOrFail($id);

        if ($user->hasVerifiedEmail()) {
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Email already verified.'], 400);
            }
            return back()->with('error', 'Email already verified.');
        }

        $user->sendEmailVerificationNotification();

        if (request()->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Verification email sent.']);
        }

        return back()->with('success', 'Verification email sent.');
    }

    public function verifyEmailManually($id)
    {
        $user = User::findOrFail($id);

        if (!in_array(auth()->user()->role->level, [1, 2])) {
            return back()->with('error', 'Unauthorized.');
        }

        if ($user->hasVerifiedEmail()) {
            return back()->with('info', 'User already verified.');
        }

        $user->markEmailAsVerified();

        return back()->with('success', "Email for {$user->full_name} has been verified.");
    }

    public function restoreUser(int $id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();
        return redirect()->route('admin.users.index')
            ->with('success', "User '{$user->full_name}' restored successfully.");
    }

    public function forceDeleteUser(int $id)
    {
        $user     = User::withTrashed()->findOrFail($id);
        $userName = $user->full_name;
        $user->forceDelete();
        return redirect()->route('admin.users.index')
            ->with('success', "User '{$userName}' permanently deleted.");
    }

    // =========================================================================
    // Helpers
    // =========================================================================

    private function shouldClearYearLevel($roleId, $position): bool
    {
        $alwaysNonStudent = [1, 6, 8]; // System Admin, Club Adviser, Guest
        if (in_array($roleId, $alwaysNonStudent)) {
            return true;
        }
        // Supreme Admin without SSLG President position also clears year level
        if ($roleId == 2 && $position !== 'SSLG President') {
            return true;
        }
        return false;
    }

    private function getAllowedPositions(string $roleName, ?string $abbreviation = null): array
    {
        $byAbbrev = [
            'SysAdmin' => [],
            'SA'       => ['SSLG President', 'SSLG Adviser', 'Student Affairs Head'],
            'SO'       => ['SSLG Secretary', 'SSLG Treasurer', 'SSLG PIO'],
            'CA'       => ['Club Adviser'],
            'OA'       => ['Organization President', 'Organization Vice President'],
            'OO'       => ['Organization Secretary', 'Organization Treasurer', 'Organization Auditor', 'Organization PIO'],
            'OM'       => ['Regular Member'],
        ];

        if ($abbreviation && isset($byAbbrev[$abbreviation])) {
            return $byAbbrev[$abbreviation];
        }

        $byName = [
            'System Administrator' => [],
            'Supreme Admin'        => ['SSLG President', 'SSLG Adviser', 'Student Affairs Head'],
            'Supreme Officer'      => ['SSLG Secretary', 'SSLG Treasurer', 'SSLG PIO'],
            'Club Adviser'         => ['Club Adviser'],
            'Org Admin'            => ['Organization President', 'Organization Vice President'],
            'Org Officer'          => ['Organization Secretary', 'Organization Treasurer', 'Organization Auditor', 'Organization PIO'],
            'Org Member'           => ['Regular Member'],
        ];

        return $byName[$roleName] ?? [];
    }
}