<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Member;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * AdminController
 * Handles: User management, Role management, Permission management.
 * Access: System Administrator, Supreme Admin, Adviser only.
 */
class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth.custom']);
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            $allowedRoles = ['System Administrator', 'Supreme Admin', 'Adviser'];
            $allowedAbbreviations = ['SysAdmin', 'SA', 'AD'];
            
            if (!$user || !$user->role) {
                abort(403, 'Unauthorized.');
            }
            
            if (!in_array($user->role->name, $allowedRoles) && !in_array($user->role->abbreviation, $allowedAbbreviations)) {
                abort(403, 'Unauthorized. Only System Administrators, Supreme Admins, and Advisers can access admin functions.');
            }
            
            return $next($request);
        });
    }

    // =========================================================================
    // USERS
    // =========================================================================

    public function users()
    {
        $user = auth()->user();
        $totalUsers = User::count();
        $activeUsers = User::where('is_active', true)->count();
        $totalRoles = Role::count();
        $verifiedEmails = User::whereNotNull('email_verified_at')->count();
        $recentLogins = User::whereNotNull('last_login_at')
            ->where('last_login_at', '>=', now()->subDays(7))
            ->count();
        
        if ($user->role->name === 'Adviser' || $user->role->abbreviation === 'AD') {
            $users = User::with('role')
                ->whereHas('role', function($query) {
                    $query->whereIn('name', ['Org Admin', 'Org Officer', 'Org Member', 'Guest']);
                })
                ->latest()
                ->paginate(10);
        } else {
            $users = User::with('role')->latest()->paginate(10);
        }
        
        $roles = Role::all();
        
        return view('admin.users.index', compact(
            'users', 
            'roles', 
            'totalUsers', 
            'activeUsers', 
            'totalRoles', 
            'recentLogins',
            'verifiedEmails',
        ));
    }

    public function createUser()
    {
        $user = auth()->user();
        
        if ($user->role->name === 'Adviser' || $user->role->abbreviation === 'AD') {
            $roles = Role::whereIn('name', ['Org Admin', 'Org Officer', 'Org Member', 'Guest'])->get();
        } else {
            $roles = Role::all();
        }
        
        return view('admin.users.create', compact('roles'));
    }

    public function storeUser(Request $request)
    {
        $currentUser = auth()->user();
        
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'unique:users,email'],
            'student_id' => ['nullable', 'string', 'unique:users,student_id'],
            'year_level' => ['nullable', 'string'],
            'role_id' => ['required', 'exists:roles,id'],
            'position' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
            'send_welcome_email' => ['nullable', 'boolean'],
        ]);
        
        $selectedRole = Role::find($validated['role_id']);
        
        if ($currentUser->role->name === 'Adviser' || $currentUser->role->abbreviation === 'AD') {
            $allowedRoles = ['Org Admin', 'Org Officer', 'Org Member', 'Guest'];
            if (!in_array($selectedRole->name, $allowedRoles)) {
                return redirect()->route('admin.users.index')
                    ->with('error', '❌ ACCESS DENIED: Advisers can only create Org Admin, Org Officer, Org Member, and Guest accounts.');
            }
        }

        DB::beginTransaction();
        
        try {
            $fullName = $validated['first_name'] . ' ' . $validated['last_name'];
            if (!empty($validated['middle_name'])) {
                $fullName = $validated['first_name'] . ' ' . $validated['middle_name'] . ' ' . $validated['last_name'];
            }
            
            $email = $validated['email'] ?? \App\Helpers\UserHelper::generateUniqueMemberEmail(
                $validated['first_name'], 
                $validated['last_name']
            );
            
            $password = 'password';
            
            $user = User::create([
                'full_name' => $fullName,
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'middle_name' => $validated['middle_name'] ?? null,
                'email' => $email,
                'password' => Hash::make($password),
                'role_id' => $validated['role_id'],
                'position' => $validated['position'] ?? null,
                'student_id' => $validated['student_id'] ?? null,
                'year_level' => $validated['year_level'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
                'email_verified_at' => now(),
            ]);
            
            Member::create([
                'user_id' => $user->id,
                'position' => $validated['position'] ?? 'Member',
                'joined_at' => now(),
                'term_start' => now(),
                'term_end' => null,
            ]);
            
            DB::commit();

            $message = "✅ User {$fullName} created successfully!\n";
            $message .= "Email: {$email}\n";
            $message .= "Password: {$password}";

            if ($request->send_welcome_email) {
                // Mail::to($user->email)->send(new WelcomeEmail($user, $password));
            }

            return redirect()->route('admin.users.index')
                ->with('success', nl2br($message));
                
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', '❌ Failed to create user: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function editUser(int $id)
    {
        $user = User::findOrFail($id);
        $currentUser = auth()->user();
        
        if ($currentUser->role->name === 'Adviser' || $currentUser->role->abbreviation === 'AD') {
            $allowedRoles = ['Org Admin', 'Org Officer', 'Org Member', 'Guest'];
            if (!in_array($user->role->name, $allowedRoles)) {
                abort(403, 'Unauthorized to edit this user.');
            }
        }
        
        if ($currentUser->role->name === 'Adviser' || $currentUser->role->abbreviation === 'AD') {
            $roles = Role::whereIn('name', ['Org Admin', 'Org Officer', 'Org Member', 'Guest'])->get();
        } else {
            $roles = Role::all();
        }
        
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function updateUser(Request $request, int $id)
    {
        $user = User::findOrFail($id);
        $currentUser = auth()->user();

        if ($currentUser->role->name === 'Adviser' || $currentUser->role->abbreviation === 'AD') {
            $allowedRoles = ['Org Admin', 'Org Officer', 'Org Member', 'Guest'];
            if (!in_array($user->role->name, $allowedRoles)) {
                abort(403, 'Unauthorized to update this user.');
            }
        }

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,' . $id],
            'role_id' => ['required', 'exists:roles,id'],
            'position' => ['nullable', 'string', 'max:255'],
            'student_id' => ['nullable', 'string', 'unique:users,student_id,' . $id],
            'year_level' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ]);
        
        $selectedRole = Role::find($validated['role_id']);
        
        if ($currentUser->role->name === 'Adviser' || $currentUser->role->abbreviation === 'AD') {
            $allowedRoles = ['Org Admin', 'Org Officer', 'Org Member', 'Guest'];
            if (!in_array($selectedRole->name, $allowedRoles)) {
                return redirect()->route('admin.users.index')
                    ->with('error', '❌ ACCESS DENIED: Advisers can only assign Org Admin, Org Officer, Org Member, and Guest roles.');
            }
        }

        if ($user->role && $user->role->name === 'Adviser' && $validated['role_id'] != $user->role_id) {
            $adviserCount = User::whereHas('role', function($q) {
                $q->where('name', 'Adviser');
            })->count();
            
            if ($adviserCount <= 1) {
                return back()->with('error', 'Cannot change role of the last adviser in the system.');
            }
        }

        try {
            $fullName = $validated['first_name'] . ' ' . $validated['last_name'];
            
            $user->full_name = $fullName;
            $user->first_name = $validated['first_name'];
            $user->last_name = $validated['last_name'];
            $user->email = $validated['email'];
            $user->role_id = $validated['role_id'];
            $user->position = $validated['position'] ?? null;
            $user->student_id = $validated['student_id'] ?? null;
            $user->year_level = $validated['year_level'] ?? null;
            $user->is_active = $validated['is_active'] ?? $user->is_active;
            
            if ($request->filled('password')) {
                $request->validate([
                    'password' => ['required', 'confirmed', 'min:8'],
                ]);
                $user->password = Hash::make($request->password);
            }
            
            $user->save();
            
            $member = $user->member;
            if ($member) {
                $member->position = $validated['position'] ?? $member->position;
                $member->save();
            }

            return redirect()->route('admin.users.index')
                ->with('success', "✅ User {$fullName} updated successfully.");
                
        } catch (\Exception $e) {
            return back()->with('error', '❌ Failed to update user: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroyUser(int $id)
    {
        $user = User::findOrFail($id);
        $currentUser = auth()->user();
        
        if ($user->id === $currentUser->id) {
            return back()->with('error', 'You cannot delete your own account.');
        }
        
        if ($currentUser->role->name === 'Adviser' || $currentUser->role->abbreviation === 'AD') {
            $allowedRoles = ['Org Admin', 'Org Officer', 'Org Member', 'Guest'];
            if (!in_array($user->role->name, $allowedRoles)) {
                return back()->with('error', '❌ ACCESS DENIED: Advisers can only delete Org Admin, Org Officer, Org Member, and Guest accounts.');
            }
        }
        
        if ($user->role && $user->role->name === 'Adviser') {
            $adviserCount = User::whereHas('role', function($q) {
                $q->where('name', 'Adviser');
            })->count();
            
            if ($adviserCount <= 1) {
                return back()->with('error', 'Cannot delete the last adviser in the system.');
            }
        }
        
        try {
            $userName = $user->full_name;
            
            if ($user->member) {
                $user->member->delete();
            }
            
            $user->delete();
            
            return redirect()->route('admin.users.index')
                ->with('success', "✅ User {$userName} deleted successfully.");
                
        } catch (\Exception $e) {
            return back()->with('error', '❌ Failed to delete user: ' . $e->getMessage());
        }
    }
    
    public function resetPassword(int $id)
    {
        $user = User::findOrFail($id);
        
        $newPassword = Str::random(10);
        $user->password = Hash::make($newPassword);
        $user->save();
        
        return back()->with('success', "Password reset for {$user->full_name}. New password: {$newPassword}");
    }

    // =========================================================================
    // ROLES
    // =========================================================================

    public function roles()
    {
        $roles = Role::with('permissions', 'users')->get();
        $permissions = Permission::all();
        return view('admin.roles.index', compact('roles', 'permissions'));
    }

    public function storeRole(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:roles,name'],
            'description' => ['nullable', 'string'],
        ]);

        try {
            $role = Role::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
            ]);
            
            return redirect()->route('admin.roles.index')
                ->with('success', "Role {$role->name} created successfully.");
                
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to create role: ' . $e->getMessage());
        }
    }
    
    public function updateRole(Request $request, int $id)
    {
        $role = Role::findOrFail($id);
        
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:roles,name,' . $id],
            'description' => ['nullable', 'string'],
        ]);
        
        $defaultRoles = ['System Administrator', 'Supreme Admin', 'Supreme Officer', 'Org Admin', 'Org Officer', 'Adviser', 'Org Member', 'Guest'];
        if (in_array($role->name, $defaultRoles)) {
            return back()->with('error', 'Cannot edit default system roles.');
        }
        
        try {
            $role->update([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
            ]);
            
            return redirect()->route('admin.roles.index')
                ->with('success', "Role {$role->name} updated successfully.");
                
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to update role: ' . $e->getMessage());
        }
    }

    public function destroyRole(int $id)
    {
        $role = Role::findOrFail($id);
        
        $defaultRoles = ['System Administrator', 'Supreme Admin', 'Supreme Officer', 'Org Admin', 'Org Officer', 'Adviser', 'Org Member', 'Guest'];
        if (in_array($role->name, $defaultRoles)) {
            return back()->with('error', 'Cannot delete default system roles.');
        }
        
        if ($role->users()->count() > 0) {
            return back()->with('error', 'Cannot delete role that has users assigned.');
        }
        
        try {
            $roleName = $role->name;
            $role->delete();
            
            return redirect()->route('admin.roles.index')
                ->with('success', "Role {$roleName} deleted successfully.");
                
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete role: ' . $e->getMessage());
        }
    }

    // =========================================================================
    // PERMISSIONS
    // =========================================================================

    public function permissions()
    {
        $permissions = Permission::with('roles')->get();
        $roles = Role::with('permissions')->get();
        return view('admin.permissions.index', compact('permissions', 'roles'));
    }

    public function syncPermissions(Request $request)
    {
        $validated = $request->validate([
            'role_id' => ['required', 'exists:roles,id'],
            'permission_ids' => ['nullable', 'array'],
            'permission_ids.*' => ['exists:permissions,id'],
        ]);

        try {
            $role = Role::findOrFail($validated['role_id']);
            $role->permissions()->sync($validated['permission_ids'] ?? []);
            
            return redirect()->route('admin.permissions.index')
                ->with('success', "Permissions for {$role->name} synced successfully.");
                
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to sync permissions: ' . $e->getMessage());
        }
    }
    
    public function getStatistics()
    {
        return [
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'total_roles' => Role::count(),
            'recent_logins' => User::whereNotNull('last_login_at')
                ->where('last_login_at', '>=', now()->subDays(7))
                ->count(),
            'users_by_role' => Role::withCount('users')->get()->map(function($role) {
                return [
                    'name' => $role->name,
                    'abbreviation' => $role->abbreviation,
                    'count' => $role->users_count
                ];
            }),
        ];
    }

    public function sendVerificationEmail($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'success' => false, 
                'message' => 'Email already verified'
            ], 400);
        }
        
        $user->sendEmailVerificationNotification();
        
        return response()->json([
            'success' => true, 
            'message' => 'Verification email sent successfully'
        ]);
    }

    public function verifyEmailManually($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->hasVerifiedEmail()) {
            return back()->with('warning', 'Email already verified for ' . $user->full_name);
        }
        
        $user->markEmailAsVerified();
        
        return back()->with('success', 'Email verified successfully for ' . $user->full_name);
    }
}