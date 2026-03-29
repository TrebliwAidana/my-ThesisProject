<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\User;
use App\Models\Role;
use App\Models\PositionChangeLog;
use App\Helpers\UserHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class MemberController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.custom');
    }

    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        if (!$user->role) {
            abort(403, 'User role not assigned. Please contact administrator.');
        }

        // Get statistics for dashboard
        $totalMembers = User::count();
        $activeMembers = User::where('is_active', true)->count();
        $totalRoles = Role::count();
        $verifiedEmails = User::whereNotNull('email_verified_at')->count();
        $recentLogins = User::whereNotNull('last_login_at')
            ->where('last_login_at', '>=', now()->subDays(7))
            ->count();

        // For Adviser/Officer/Auditor - show ALL users
        if ($user->role->name === 'Member') {
            // Members can only see themselves
            $users = User::with('role')
                ->where('id', $user->id)
                ->paginate(10);
        } else {
            // Advisers, Officers, and Auditors can see all users
            $users = User::with('role')
                ->latest()
                ->paginate(10);
        }

        // Get all roles for filter
        $roles = Role::all();

        return view('members.index', compact(
            'users', 
            'roles', 
            'totalMembers', 
            'activeMembers', 
            'totalRoles', 
            'recentLogins',
            'verifiedEmails'
        ));
    }

    public function create()
    {
        $user = Auth::user();

        if (!$user || !$user->role) {
            abort(403, 'Unauthorized.');
        }

        if (!in_array($user->role->name, ['Adviser', 'Officer'])) {
            abort(403, 'Unauthorized. Only Advisers and Officers can create members.');
        }

        if ($user->role->name === 'Adviser') {
            $roles = Role::all();
        } else {
            $roles = Role::where('name', 'Member')->get();
        }

        return view('members.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user || !$user->role) {
            abort(403, 'Unauthorized.');
        }

        if (!in_array($user->role->name, ['Adviser', 'Officer'])) {
            abort(403, 'Unauthorized. Only Advisers and Officers can create members.');
        }

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'unique:users,email'],
            'student_id' => ['nullable', 'string', 'unique:users,student_id'],
            'year_level' => ['nullable', 'string'],
            'role_id' => ['required', 'exists:roles,id'],
            'position' => ['required', 'string', 'max:255'],
            'is_active' => ['boolean'],
            'send_welcome_email' => ['nullable', 'boolean'],
        ]);

        if ($user->role->name === 'Officer') {
            $selectedRole = Role::find($validated['role_id']);
            if ($selectedRole->name !== 'Member') {
                return redirect()->route('members.index')
                    ->with('error', '❌ ACCESS DENIED: Officers can only create Member accounts.');
            }
        }

        $role = Role::find($validated['role_id']);
        $allowedPositions = $this->getAllowedPositions($role->name);

        if (!in_array($validated['position'], $allowedPositions)) {
            return back()->withErrors(['position' => "Position '{$validated['position']}' is not valid for role '{$role->name}'. Allowed positions: " . implode(', ', $allowedPositions)])
                ->withInput();
        }

        DB::beginTransaction();

        try {
            // Generate full name
            $fullName = $validated['first_name'] . ' ' . $validated['last_name'];
            if (!empty($validated['middle_name'])) {
                $fullName = $validated['first_name'] . ' ' . $validated['middle_name'] . ' ' . $validated['last_name'];
            }
            
            // Generate email if not provided
            $email = $validated['email'] ?? UserHelper::generateUniqueMemberEmail(
                $validated['first_name'], 
                $validated['last_name']
            );
            
            // Default password
            $password = 'password';
            
            $newUser = User::create([
                'full_name' => $fullName,
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'middle_name' => $validated['middle_name'] ?? null,
                'email' => $email,
                'password' => Hash::make($password),
                'role_id' => $validated['role_id'],
                'position' => $validated['position'],
                'student_id' => $validated['student_id'] ?? null,
                'year_level' => $validated['year_level'] ?? null,
                'is_active' => $validated['is_active'] ?? true,
                'email_verified_at' => now(),
            ]);

            // Create member record
            Member::create([
                'user_id' => $newUser->id,
                'position' => $validated['position'],
                'joined_at' => now(),
                'term_start' => now(),
                'term_end' => null,
            ]);

            DB::commit();

            return redirect()->route('members.index')
                ->with('success', "✅ Member created successfully! Email: {$email}, Password: {$password}");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', '❌ Failed to create member: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $roles = Role::all();
        return view('members.edit', compact('user', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $currentUser = Auth::user();

        if (!$currentUser || !$currentUser->role) {
            abort(403, 'Unauthorized.');
        }

        if (!in_array($currentUser->role->name, ['Adviser', 'Officer'])) {
            abort(403, 'Unauthorized. Only Advisers and Officers can update members.');
        }

        if ($currentUser->role->name === 'Officer') {
            $targetRole = $user->role->name;
            if ($targetRole !== 'Member') {
                return redirect()->route('members.index')
                    ->with('error', '❌ ACCESS DENIED: You are not authorized to edit this member.');
            }
        }

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,' . $user->id],
            'student_id' => ['nullable', 'string', 'unique:users,student_id,' . $user->id],
            'year_level' => ['nullable', 'string'],
            'role_id' => ['required', 'exists:roles,id'],
            'position' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
        ]);

        // Prevent changing role of last adviser
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
            if (!empty($validated['middle_name'])) {
                $fullName = $validated['first_name'] . ' ' . $validated['middle_name'] . ' ' . $validated['last_name'];
            }
            
            $user->full_name = $fullName;
            $user->first_name = $validated['first_name'];
            $user->last_name = $validated['last_name'];
            $user->middle_name = $validated['middle_name'] ?? null;
            $user->email = $validated['email'];
            $user->student_id = $validated['student_id'] ?? null;
            $user->year_level = $validated['year_level'] ?? null;
            $user->role_id = $validated['role_id'];
            $user->position = $validated['position'] ?? $user->position;
            $user->is_active = $validated['is_active'] ?? $user->is_active;
            
            // Only update password if provided
            if ($request->filled('password')) {
                $request->validate([
                    'password' => ['required', 'confirmed', 'min:8'],
                ]);
                $user->password = Hash::make($request->password);
            }
            
            $user->save();
            
            // Update or create member record
            $member = $user->member;
            if ($member) {
                $member->position = $validated['position'] ?? $member->position;
                $member->save();
            }

            return redirect()->route('members.index')
                ->with('success', "✅ Member {$fullName} updated successfully.");
                
        } catch (\Exception $e) {
            return back()->with('error', '❌ Failed to update member: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        $userToDelete = User::findOrFail($id);
        $currentUser = Auth::user();

        if (!$currentUser || !$currentUser->role) {
            return back()->with('error', '❌ Unauthorized.');
        }

        if (!in_array($currentUser->role->name, ['Adviser', 'Officer'])) {
            return back()->with('error', '❌ Unauthorized. Only Advisers and Officers can delete members.');
        }

        if ($currentUser->role->name === 'Officer') {
            $userRole = $userToDelete->role->name;
            if ($userRole !== 'Member') {
                return redirect()->route('members.index')
                    ->with('error', '❌ ACCESS DENIED: Officers can only delete regular Members.');
            }
        }

        if ($userToDelete->role && $userToDelete->role->name === 'Adviser') {
            $adviserCount = User::whereHas('role', function($q) {
                $q->where('name', 'Adviser');
            })->count();

            if ($adviserCount <= 1) {
                return back()->with('error', '⚠️ Cannot delete the last adviser in the system.');
            }
        }

        try {
            $userName = $userToDelete->full_name;
            $userRole = $userToDelete->role->name;
            
            if ($userToDelete->member) {
                $userToDelete->member->delete();
            }
            
            $userToDelete->delete();

            return redirect()->route('members.index')
                ->with('success', "✅ {$userName} ({$userRole}) has been removed from the system.");
                
        } catch (\Exception $e) {
            return back()->with('error', '❌ Failed to delete member: ' . $e->getMessage());
        }
    }

    private function getAllowedPositions($roleName)
    {
        $positions = [
            'Adviser' => ['Adviser'],
            'Officer' => ['President', 'Vice President', 'Secretary', 'Treasurer', 'Auditor'],
            'Auditor' => ['Auditor'],
            'Member' => ['Member'],
        ];

        return $positions[$roleName] ?? [];
    }
}