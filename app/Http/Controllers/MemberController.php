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

        $totalMembers = User::count();
        $activeMembers = User::where('is_active', true)->count();
        $totalRoles = Role::count();
        $verifiedEmails = User::whereNotNull('email_verified_at')->count();
        $recentLogins = User::whereNotNull('last_login_at')
            ->where('last_login_at', '>=', now()->subDays(7))
            ->count();

        if ($user->role->name === 'Org Member' || $user->role->abbreviation === 'OM') {
            $users = User::with('role')
                ->where('id', $user->id)
                ->paginate(10);
        } else {
            $users = User::with('role')
                ->latest()
                ->paginate(10);
        }

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

        $allowedRoles = ['Adviser', 'Org Admin', 'Org Officer'];
        if (!in_array($user->role->name, $allowedRoles) && !in_array($user->role->abbreviation, ['AD', 'OA', 'OO'])) {
            abort(403, 'Unauthorized. Only Advisers, Org Admins, and Org Officers can create members.');
        }

        if ($user->role->name === 'Adviser' || $user->role->abbreviation === 'AD') {
            $roles = Role::all();
        } else {
            $roles = Role::where('name', 'Org Member')->orWhere('abbreviation', 'OM')->get();
        }

        return view('members.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user || !$user->role) {
            abort(403, 'Unauthorized.');
        }

        $allowedRoles = ['Adviser', 'Org Admin', 'Org Officer'];
        if (!in_array($user->role->name, $allowedRoles) && !in_array($user->role->abbreviation, ['AD', 'OA', 'OO'])) {
            abort(403, 'Unauthorized. Only Advisers, Org Admins, and Org Officers can create members.');
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

        if (in_array($user->role->abbreviation, ['OO', 'OA']) || $user->role->name === 'Org Officer') {
            $selectedRole = Role::find($validated['role_id']);
            if (!in_array($selectedRole->name, ['Org Member', 'Org Officer']) && $selectedRole->abbreviation !== 'OM') {
                return redirect()->route('members.index')
                    ->with('error', '❌ ACCESS DENIED: Officers can only create Org Member and Org Officer accounts.');
            }
        }

        $role = Role::find($validated['role_id']);
        $allowedPositions = $this->getAllowedPositions($role->name, $role->abbreviation);

        if (!in_array($validated['position'], $allowedPositions)) {
            return back()->withErrors(['position' => "Position '{$validated['position']}' is not valid for role '{$role->name}'. Allowed positions: " . implode(', ', $allowedPositions)])
                ->withInput();
        }

        DB::beginTransaction();

        try {
            $fullName = $validated['first_name'] . ' ' . $validated['last_name'];
            if (!empty($validated['middle_name'])) {
                $fullName = $validated['first_name'] . ' ' . $validated['middle_name'] . ' ' . $validated['last_name'];
            }
            
            $email = $validated['email'] ?? UserHelper::generateUniqueMemberEmail(
                $validated['first_name'], 
                $validated['last_name']
            );
            
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

        $allowedRoles = ['Adviser', 'Org Admin', 'Org Officer'];
        if (!in_array($currentUser->role->name, $allowedRoles) && !in_array($currentUser->role->abbreviation, ['AD', 'OA', 'OO'])) {
            abort(403, 'Unauthorized. Only Advisers, Org Admins, and Org Officers can update members.');
        }

        if (in_array($currentUser->role->abbreviation, ['OO', 'OA']) || $currentUser->role->name === 'Org Officer') {
            $targetRole = $user->role->name;
            if (!in_array($targetRole, ['Org Member', 'Org Officer']) && $user->role->abbreviation !== 'OM') {
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

            // Clear any existing flash data before setting new one
            session()->forget('success');
            session()->forget('error');
            
            return redirect()->route('members.index')
                ->with('success', "✅ Member {$fullName} updated successfully.")
                ->with('_flash', ['success' => "✅ Member {$fullName} updated successfully."]);
                
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

        $allowedRoles = ['Adviser', 'Org Admin', 'Org Officer'];
        if (!in_array($currentUser->role->name, $allowedRoles) && !in_array($currentUser->role->abbreviation, ['AD', 'OA', 'OO'])) {
            return back()->with('error', '❌ Unauthorized. Only Advisers, Org Admins, and Org Officers can delete members.');
        }

        if (in_array($currentUser->role->abbreviation, ['OO', 'OA']) || $currentUser->role->name === 'Org Officer') {
            $userRole = $userToDelete->role->name;
            if (!in_array($userRole, ['Org Member', 'Org Officer']) && $userToDelete->role->abbreviation !== 'OM') {
                return redirect()->route('members.index')
                    ->with('error', '❌ ACCESS DENIED: Officers can only delete regular Org Members.');
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

    /**
     * Display the specified member.
     */
    public function show($id)
    {
        $user = Auth::user();

        if (!$user || !$user->role) {
            abort(403, 'Unauthorized.');
        }

        // Find the member with role and member relationship
        $member = User::with('role', 'member')->findOrFail($id);

        // Check if user has permission to view this member
        // System Administrator (role_id = 1) can view any member
        if ($user->role_id == 1) {
            // Allow full access
        } 
        // Users can view their own profile
        elseif ($user->id == $member->id) {
            // Allow access to own profile
        }
        // Org Admins and Org Officers can view members in their organization
        elseif (in_array($user->role->abbreviation, ['OA', 'OO'])) {
            // Check if member belongs to same organization
            if ($member->organization_id != ($user->organization_id ?? null)) {
                abort(403, 'Unauthorized to view this member.');
            }
        }
        else {
            abort(403, 'Unauthorized to view this member.');
        }

        // Get member statistics
        $documentsCount = $member->documents()->count();
        $budgetsCount = $member->budgets()->count();
        $memberSince = $member->created_at->format('F d, Y');

        return view('members.show', compact('member', 'documentsCount', 'budgetsCount', 'memberSince'));
    }

    private function getAllowedPositions($roleName, $abbreviation = null)
    {
        $positions = [
            'System Administrator' => ['System Administrator'],
            'Supreme Admin' => ['Supreme Admin'],
            'Supreme Officer' => ['Supreme Officer'],
            'Org Admin' => ['President', 'Chairperson'],
            'Org Officer' => ['Secretary', 'Treasurer', 'Auditor', 'PIO', 'Vice President'],
            'Adviser' => ['Adviser'],
            'Org Member' => ['Member'],
        ];

        if ($abbreviation) {
            $abbrevPositions = [
                'SysAdmin' => ['System Administrator'],
                'SA' => ['Supreme Admin'],
                'SO' => ['Supreme Officer'],
                'OA' => ['President', 'Chairperson'],
                'OO' => ['Secretary', 'Treasurer', 'Auditor', 'PIO', 'Vice President'],
                'AD' => ['Adviser'],
                'OM' => ['Member'],
            ];
            return $abbrevPositions[$abbreviation] ?? $positions[$roleName] ?? ['Member'];
        }

        return $positions[$roleName] ?? ['Member'];
    }

        /**
     * Deactivate a member account (Admin only)
     */
    public function deactivate($id)
    {
        $user = User::findOrFail($id);
        
        // Prevent deactivating yourself
        if ($user->id == auth()->id()) {
            return response()->json(['error' => 'You cannot deactivate your own account.'], 403);
        }
        
        $user->is_active = false;
        $user->save();
        
        return response()->json(['success' => true, 'message' => 'Account deactivated successfully.']);
    }

    /**
     * Activate a member account (Admin only)
     */
    public function activate($id)
    {
        $user = User::findOrFail($id);
        
        $user->is_active = true;
        $user->save();
        
        return response()->json(['success' => true, 'message' => 'Account activated successfully.']);
    }
}