<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Notifications\NewUserWelcomeNotification;
use App\Notifications\PasswordResetNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AdminController extends Controller
{
    // =========================================================================
    // ROLES
    // =========================================================================

    public function roles(Request $request)
    {
        $user = Auth::user();
        if ($user->role->level !== 1 && ! $user->hasPermission('roles.view')) {
            abort(403, 'You do not have permission to view roles.');
        }

        $query = Role::withCount('users')
            ->with('permissions')
            ->when(! $request->boolean('show_hidden'), fn ($q) => $q->where('is_visible', true));

        if ($request->boolean('show_trashed')) {
            $query->onlyTrashed();
        } else {
            $query->when(
                ! $request->boolean('show_hidden'),
                fn ($q) => $q->where('is_visible', true)
            );
        }

        $roles = $query->orderBy('level')->paginate(50);
        $showHidden = $request->boolean('show_hidden');
        $permissions = $this->getCachedPermissions();

        return view('admin.roles.index', compact('roles', 'permissions', 'showHidden'));
    }

    public function createRole()
    {
        $user = Auth::user();
        if ($user->role_id !== 1 && ! $user->hasPermission('roles.create')) {
            abort(403, 'You do not have permission to create roles.');
        }

        $roles = $this->getCachedVisibleRoles();

        return view('admin.roles.create', compact('roles'));
    }

    public function editRole(int $id)
    {
        $user = Auth::user();
        if ($user->role->level !== 1 && ! $user->hasPermission('roles.edit')) {
            abort(403, 'You do not have permission to edit roles.');
        }

        $role = Role::with('permissions')->findOrFail($id);
        if (! $role->is_visible) {
            return redirect()->route('admin.roles.index')->with('error', 'This role is hidden.');
        }

        $permissions = $this->getCachedPermissions();
        $rolePermissionIds = $role->permissions->pluck('id')->toArray();

        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissionIds'));
    }

    public function storeRole(Request $request)
    {
        $user = Auth::user();
        if ($user->role_id !== 1 && ! $user->hasPermission('roles.create')) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:roles,name'],
            'abbreviation' => ['nullable', 'string', 'max:10'],
            'desc' => ['nullable', 'string', 'max:500'],
            'level' => ['required', 'integer', 'min:1', 'max:10'],
            'is_system' => ['nullable', 'boolean'],
            'parent_id' => ['nullable', 'exists:roles,id'],
        ]);

        if ($validated['level'] == 1 && ! ($validated['is_system'] ?? false)) {
            return back()->with('error', 'Level 1 is reserved for system roles only.')->withInput();
        }

        try {
            $role = Role::create([
                'name' => $validated['name'],
                'abbreviation' => $validated['abbreviation'] ?? null,
                'desc' => $validated['desc'] ?? null,
                'level' => $validated['level'],
                'is_system' => $validated['is_system'] ?? false,
                'parent_id' => $validated['parent_id'] ?? null,
                'is_visible' => true,
            ]);

            if ($request->has('permissions')) {
                $role->permissions()->sync($request->input('permissions', []));
            }

            $this->flushPermissionCaches($role);

            return redirect()->route('admin.roles.index')
                ->with('success', "✅ Role '{$role->name}' created successfully.");
        } catch (\Exception $e) {
            return back()->with('error', '❌ Failed to create role: '.$e->getMessage())->withInput();
        }
    }

    public function updateRole(Request $request, int $id)
    {
        $user = Auth::user();
        if ($user->role->level !== 1 && ! $user->hasPermission('roles.edit')) {
            abort(403);
        }

        $role = Role::findOrFail($id);

        if (! $role->is_visible) {
            return redirect()->route('admin.roles.index')->with('error', 'Cannot edit hidden role.');
        }

        if ($role->is_predefined) {
            $validated = $request->validate([
                'level' => ['required', 'integer', 'min:1', 'max:10'],
            ]);
            $role->update($validated);

            if ($request->has('permissions')) {
                $role->permissions()->sync($request->input('permissions', []));
                $role->users()->each(fn ($u) => cache()->forget("user_perms_{$u->id}"));
            }

            $this->flushPermissionCaches($role);

            return redirect()->route('admin.roles.index')->with('success', 'Role updated.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('roles', 'name')->ignore($id)],
            'abbreviation' => ['nullable', 'string', 'max:10'],
            'desc' => ['nullable', 'string', 'max:500'],
            'level' => ['required', 'integer', 'min:1', 'max:10'],
            'is_system' => ['nullable', 'boolean'],
        ]);

        if ($validated['level'] == 1 && ! ($validated['is_system'] ?? false)) {
            return back()->with('error', 'Level 1 is reserved for system roles only.')->withInput();
        }

        try {
            $role->update($validated);
            $role->permissions()->sync($request->input('permissions', []));
            $role->users()->each(fn ($u) => cache()->forget("user_perms_{$u->id}"));

            $this->flushPermissionCaches($role);

            return redirect()->route('admin.roles.index')
                ->with('success', "✅ Role '{$role->name}' updated successfully.");
        } catch (\Exception $e) {
            return back()->with('error', '❌ Failed to update role: '.$e->getMessage())->withInput();
        }
    }

    public function destroyRole(int $id)
    {
        $user = Auth::user();
        if ($user->role->level !== 1 && ! $user->hasPermission('roles.delete')) {
            abort(403);
        }

        $role = Role::findOrFail($id);

        if ($role->id === 1) {
            return back()->with('error', '⚠️ Cannot delete the System Administrator role.');
        }
        if (! $role->is_visible) {
            return back()->with('error', 'Cannot delete a hidden role.');
        }
        if ($role->is_predefined) {
            return back()->with('error', '⚠️ Cannot delete a predefined system role.');
        }
        if ($role->users()->withTrashed()->exists()) {
            return back()->with('error', '⚠️ Cannot delete a role that has users assigned to it.');
        }

        try {
            $roleName = $role->name;
            $role->delete();

            $this->flushPermissionCaches();

            return redirect()->route('admin.roles.index')
                ->with('success', "✅ Role '{$roleName}' deleted successfully.");
        } catch (\Exception $e) {
            return back()->with('error', '❌ Failed to delete role: '.$e->getMessage());
        }
    }

    public function restoreRole(int $id)
    {
        $user = Auth::user();
        if ($user->role_id !== 1 && ! $user->hasPermission('roles.delete')) {
            abort(403);
        }

        $role = Role::withTrashed()->findOrFail($id);
        $role->restore();

        $this->flushPermissionCaches();

        return redirect()->route('admin.roles.index')
            ->with('success', "Role '{$role->name}' restored successfully.");
    }

    public function forceDeleteRole(int $id)
    {
        $user = Auth::user();
        if ($user->role_id !== 1 && ! $user->hasPermission('roles.delete')) {
            abort(403);
        }

        $role = Role::withTrashed()->findOrFail($id);

        if ($role->users()->withTrashed()->exists()) {
            return back()->with('error', '⚠️ Cannot permanently delete a role that has users assigned to it.');
        }

        $roleName = $role->name;
        $role->forceDelete();

        $this->flushPermissionCaches();

        return redirect()->route('admin.roles.index')
            ->with('success', "Role '{$roleName}' permanently deleted.");
    }

    public function toggleRoleVisibility(Role $role)
    {
        $user = Auth::user();
        if ($user->role->level !== 1 && ! $user->hasPermission('roles.edit')) {
            abort(403, 'You do not have permission to edit roles.');
        }

        if ($role->id === 1) {
            return back()->with('error', 'Cannot toggle visibility of System Administrator role.');
        }

        if ($user->role_id == $role->id && $role->is_visible) {
            return back()->with('error', 'Cannot hide your own current role.');
        }

        $role->update(['is_visible' => ! $role->is_visible]);
        $status = $role->is_visible ? 'visible' : 'hidden';

        $this->flushPermissionCaches();

        return redirect()->route('admin.roles.index', ['show_hidden' => request()->boolean('show_hidden')])
            ->with('success', "Role is now {$status}.");
    }

    // =========================================================================
    // USERS
    // =========================================================================

    public function users(Request $request)
    {
        $authUser = Auth::user();
        if ($authUser->role->level !== 1 && ! $authUser->hasPermission('users.view')) {
            abort(403, 'You do not have permission to view users.');
        }

        $query = User::with('role:id,name');

        if ($request->boolean('trashed')) {
            $query->withTrashed();
        }

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->whereHas('role', fn ($q) => $q->where('name', $request->input('role')));
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->input('status') === 'active');
        }

        if ($request->filled('verification')) {
            if ($request->input('verification') === 'verified') {
                $query->whereNotNull('email_verified_at');
            } elseif ($request->input('verification') === 'unverified') {
                $query->whereNull('email_verified_at');
            }
        }

        $users = $query->paginate(20)->appends($request->except('page'));

        $stats = DB::table('users')
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN is_active = true THEN 1 ELSE 0 END) as active,
                SUM(CASE WHEN email_verified_at IS NOT NULL THEN 1 ELSE 0 END) as verified,
                SUM(CASE WHEN last_login_at >= ? THEN 1 ELSE 0 END) as recent_logins
            ', [now()->subDays(7)])
            ->first();

        $roles = $this->getCachedVisibleRoles();

        return view('admin.users.index', compact('users', 'roles', 'stats'));
    }

    public function createUser()
    {
        $authUser = Auth::user();
        if ($authUser->role->level !== 1 && ! $authUser->hasPermission('users.create')) {
            abort(403, 'You do not have permission to create users.');
        }

        // FIX: was inline Cache::remember with 'roles_visible_ordered' key storing
        // Eloquent models. Now uses getCachedVisibleRoles() so the shape is consistent
        // with editUser() and the cache key is managed in one place.
        $roles = $this->getCachedVisibleRoles();

        return view('admin.users.create', compact('roles'));
    }

    public function storeUser(Request $request)
    {
        $authUser = Auth::user();
        if ($authUser->role->level !== 1 && ! $authUser->hasPermission('users.create')) {
            abort(403);
        }

        $validated = $this->validateUserRequest($request);

        $selectedRole = $this->resolveVisibleRole($validated['role_id']);
        if (! $selectedRole) {
            return back()->withErrors(['role_id' => 'The selected role is not available.'])->withInput();
        }

        if (! empty($validated['phone'])) {
            $validated['phone'] = $this->normalizePhone($validated['phone']);
        }

        $validated['position'] = $this->resolvePosition($selectedRole, $validated['position'] ?? '');
        if ($validated['position'] === false) {
            return back()->withErrors(['position' => 'Position is required for this role.'])->withInput();
        }

        if ($this->shouldClearYearLevel($selectedRole->id, $validated['position'] ?? '')) {
            $validated['year_level'] = null;
        }

        $password = ! empty($validated['password']) ? $validated['password'] : Str::random(10);

        $user = User::create([
            'first_name' => $validated['first_name'],
            'middle_name' => $validated['middle_name'] ?? null,
            'last_name' => $validated['last_name'],
            'full_name' => $this->buildFullName($validated),
            'email' => $validated['email'],
            'password' => Hash::make($password),
            'role_id' => $validated['role_id'],
            'position' => $validated['position'],
            'student_id' => $validated['student_id'] ?? null,
            'year_level' => $validated['year_level'] ?? null,
            'gender' => $validated['gender'],
            'phone' => $validated['phone'] ?? null,
            'birthday' => $validated['birthday'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
            'email_verified_at' => now(),
        ]);

        $emailSent = false;
        try {
            $user->notify(new NewUserWelcomeNotification($password));
            $emailSent = true;
        } catch (\Exception $e) {
            \Log::warning("Welcome email failed for user {$user->id}: ".$e->getMessage());
        }

        $message = "User '{$user->full_name}' created successfully.";
        $message .= $emailSent
            ? ' A welcome email has been sent.'
            : ' ⚠️ Welcome email could not be sent — please share credentials manually or resend from the user list.';

        return redirect()->route('admin.users.index')->with('success', $message);
    }

    public function editUser(int $id)
    {
        $authUser = Auth::user();
        if ($authUser->role->level !== 1 && ! $authUser->hasPermission('users.edit')) {
            abort(403);
        }

        $user = User::findOrFail($id);

        // FIX: was Cache::remember('roles_visible_ordered', ..., fn() => ->get())
        // which stored raw Eloquent models under a key also written by other paths
        // as plain arrays — causing "Attempt to read property id on string" in the
        // view. Now delegates to getCachedVisibleRoles() which always normalises
        // the stored value to a plain array and casts each item back to stdClass,
        // giving $role->id a consistent shape regardless of which method primed
        // the cache. Also drops the stale 'roles_visible_ordered' key on first hit.
        Cache::forget('roles_visible_ordered');
        $roles = $this->getCachedVisibleRoles();

        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function updateUser(Request $request, int $id)
    {
        $authUser = Auth::user();
        if ($authUser->role->level !== 1 && ! $authUser->hasPermission('users.edit')) {
            abort(403);
        }

        $user = User::findOrFail($id);
        $validated = $this->validateUserRequest($request, $id);

        $selectedRole = $this->resolveVisibleRole($validated['role_id']);
        if (! $selectedRole) {
            return back()->withErrors(['role_id' => 'The selected role is not available.'])->withInput();
        }

        if (! empty($validated['phone'])) {
            $validated['phone'] = $this->normalizePhone($validated['phone']);
        }

        $validated['position'] = $this->resolvePosition($selectedRole, $validated['position'] ?? '');
        if ($validated['position'] === false) {
            return back()->withErrors(['position' => 'Position is required for this role.'])->withInput();
        }

        if ($this->shouldClearYearLevel($selectedRole->id, $validated['position'] ?? '')) {
            $validated['year_level'] = null;
        }

        if ($user->role_id != $validated['role_id'] && $user->role_id === 1) {
            if (User::where('role_id', 1)->count() <= 1) {
                return back()->with('error', '⚠️ Cannot change the role of the last System Administrator.');
            }
        }

        $data = [
            'first_name' => $validated['first_name'],
            'middle_name' => $validated['middle_name'] ?? null,
            'last_name' => $validated['last_name'],
            'full_name' => $this->buildFullName($validated),
            'email' => $validated['email'],
            'student_id' => $validated['student_id'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'gender' => $validated['gender'],
            'birthday' => $validated['birthday'] ?? null,
            'role_id' => $validated['role_id'],
            'position' => $validated['position'] ?? null,
            'year_level' => $validated['year_level'] ?? null,
            'is_active' => $validated['is_active'] ?? true,
        ];

        if (! empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);

        cache()->forget("user_perms_{$user->id}");

        return redirect()->route('admin.users.index')
            ->with('success', "✅ User '{$user->full_name}' updated successfully.");
    }

    public function destroyUser(int $id)
    {
        $authUser = Auth::user();
        if ($authUser->role_id !== 1 && ! $authUser->hasPermission('users.delete')) {
            abort(403);
        }

        $user = User::with('role')->findOrFail($id);

        if ($user->id === $authUser->id) {
            return back()->with('error', 'You cannot delete your own account.');
        }
        if ($user->email === 'guest@gmail.com') {
            return back()->with('error', 'The shared guest account cannot be deleted.');
        }
        if ($user->role && $user->role->id === 1 && User::where('role_id', 1)->count() <= 1) {
            return back()->with('error', '⚠️ Cannot delete the last System Administrator.');
        }

        $userName = $user->full_name;
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "User '{$userName}' deleted successfully.");
    }

    public function resetPassword(int $id)
    {
        $authUser = Auth::user();
        if ($authUser->role->level !== 1 && ! $authUser->hasPermission('users.edit')) {
            abort(403);
        }

        $user = User::findOrFail($id);
        $newPassword = Str::random(10);
        $user->password = Hash::make($newPassword);
        $user->save();

        $user->notify(new PasswordResetNotification($newPassword));

        return redirect()->route('admin.users.index')
            ->with('success', "Password for '{$user->full_name}' reset. An email has been sent.");
    }

    public function sendVerificationEmail(int $id)
    {
        $authUser = Auth::user();
        if ($authUser->role->level !== 1 && ! $authUser->hasPermission('users.edit')) {
            abort(403);
        }

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

    public function verifyEmailManually(int $id)
    {
        $authUser = Auth::user();
        if ($authUser->role->level !== 1 && ! $authUser->hasPermission('users.edit')) {
            abort(403);
        }

        $user = User::findOrFail($id);

        if ($user->hasVerifiedEmail()) {
            return back()->with('info', 'User already verified.');
        }

        $user->markEmailAsVerified();

        return back()->with('success', "Email for {$user->full_name} has been verified.");
    }

    public function restoreUser(int $id)
    {
        $authUser = Auth::user();
        if ($authUser->role->level !== 1 && ! $authUser->hasPermission('users.delete')) {
            abort(403);
        }

        $user = User::withTrashed()->findOrFail($id);
        $user->restore();

        return redirect()->route('admin.users.index')
            ->with('success', "User '{$user->full_name}' restored successfully.");
    }

    public function forceDeleteUser(int $id)
    {
        $authUser = Auth::user();
        if ($authUser->role->level !== 1 && ! $authUser->hasPermission('users.delete')) {
            abort(403);
        }

        $user = User::withTrashed()->findOrFail($id);
        $userName = $user->full_name;
        $user->forceDelete();

        return redirect()->route('admin.users.index')
            ->with('success', "User '{$userName}' permanently deleted.");
    }

    // =========================================================================
    // Private Helpers
    // =========================================================================

    private function validateUserRequest(Request $request, ?int $userId = null): array
    {
        $emailRule = $userId
            ? Rule::unique('users', 'email')->whereNot('id', $userId)
            : Rule::unique('users', 'email');

        $studentIdRule = $userId
            ? Rule::unique('users', 'student_id')->whereNot('id', $userId)
            : Rule::unique('users', 'student_id');

        $phoneRule = $userId
            ? Rule::unique('users', 'phone')->whereNot('id', $userId)
            : Rule::unique('users', 'phone');

        return $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', $emailRule],
            'role_id' => ['required', 'exists:roles,id'],
            'position' => ['nullable', 'string', 'max:255'],
            'student_id' => ['nullable', 'string', 'max:20', $studentIdRule],
            'year_level' => ['nullable', 'string', 'max:20'],
            'gender' => ['required', 'string', 'in:Male,Female,Other'],
            'phone' => ['nullable', 'string', 'max:20', $phoneRule],
            'birthday' => ['nullable', 'date'],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'is_active' => ['boolean'],
        ], [
            'email.unique' => 'This email is already registered.',
            'student_id.unique' => 'This student ID is already assigned.',
            'phone.unique' => 'This phone number is already in use.',
        ]);
    }

    private function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($phone, '63')) {
            $phone = substr($phone, 2);
        }
        if (str_starts_with($phone, '0')) {
            $phone = substr($phone, 1);
        }

        return '+63'.substr($phone, 0, 10);
    }

    private function resolveVisibleRole(int $roleId): ?Role
    {
        return Role::where('id', $roleId)->where('is_visible', true)->first();
    }

    private function buildFullName(array $validated): string
    {
        return trim(
            $validated['first_name'].' '.
            (! empty($validated['middle_name']) ? $validated['middle_name'].' ' : '').
            $validated['last_name']
        );
    }

    private function getCachedPermissions(): Collection
    {
        $array = Cache::remember(
            'permissions_all',
            3600,
            fn () => Permission::all()->toArray()
        );

        return collect($array);
    }

    private function getCachedVisibleRoles(): Collection
    {
        $array = Cache::remember(
            'roles_visible',
            3600,
            fn () => Role::where('is_visible', true)->orderBy('level')->get(['id', 'name'])->toArray()
        );

        return collect($array)->map(fn ($r) => (object) $r);
    }

    /**
     * Central cache flush for all role/permission write operations.
     */
    private function flushPermissionCaches(?Role $role = null): void
    {
        Cache::forget('permissions_all');
        Cache::forget('roles_visible');
        Cache::forget('roles_visible_ordered');

        if ($role) {
            $role->users()->each(fn ($u) => cache()->forget("user_perms_{$u->id}"));
        }
    }

    private function resolvePosition(Role $role, string $position): string|null|false
    {
        $allowed = Member::VALID_POSITIONS[$role->id] ?? [];

        if (empty($allowed)) {
            return null;
        }

        if (empty($position)) {
            return false;
        }

        if (! in_array($position, $allowed, true)) {
            return null;
        }

        return $position;
    }

    private function shouldClearYearLevel(int $roleId, ?string $position): bool
    {
        $positionsForRole = Member::VALID_POSITIONS[$roleId] ?? [];

        if (empty($positionsForRole)) {
            return true;
        }

        $nonStudent = Member::NON_STUDENT_POSITIONS;

        if (array_diff($positionsForRole, $nonStudent) === []) {
            return true;
        }

        return $position !== null && in_array($position, $nonStudent, true);
    }
}