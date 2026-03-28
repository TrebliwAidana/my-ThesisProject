<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\User;
use App\Models\Role;
use App\Helpers\UserHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * MemberController
 * - Adviser, Officer : full CRUD
 * - Auditor        : index + show only
 * - Member         : show own record only
 */
class MemberController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.custom');
        // Role middleware removed - we'll check permissions inside methods
    }

    public function index()
    {
        $user = Auth::user();
        
        // Check if user is authenticated
        if (!$user) {
            return redirect()->route('login');
        }
        
        // Check if user has a role
        if (!$user->role) {
            abort(403, 'User role not assigned. Please contact administrator.');
        }

        // Member role: only see their own member record
        if ($user->role->name === 'Member') {
            $members = Member::with('user')
                ->where('user_id', $user->id)
                ->paginate(10);
        } else {
            $members = Member::with('user')->latest()->paginate(10);
        }

        return view('members.index', compact('members'));
    }

    public function create()
    {
        $user = Auth::user();
        
        // Check if user is authenticated
        if (!$user || !$user->role) {
            abort(403, 'Unauthorized.');
        }
        
        // Only Adviser and Officer can create members
        if (!in_array($user->role->name, ['Adviser', 'Officer'])) {
            abort(403, 'Unauthorized. Only Advisers and Officers can create members.');
        }
        
        $roles = Role::all();
        return view('members.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        
        // Check if user is authenticated
        if (!$user || !$user->role) {
            abort(403, 'Unauthorized.');
        }
        
        // Only Adviser and Officer can create members
        if (!in_array($user->role->name, ['Adviser', 'Officer'])) {
            abort(403, 'Unauthorized. Only Advisers and Officers can create members.');
        }
        
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'role_id' => 'required|exists:roles,id',
            'position' => 'required|string|max:255',
            'joined_at' => 'required|date',
            'term_start' => 'required|date',
            'term_end' => 'nullable|date|after_or_equal:term_start',
        ]);
        
        // Check if position is valid for the selected role
        $role = Role::find($validated['role_id']);
        $allowedPositions = $this->getAllowedPositions($role->name);
        
        if (!in_array($validated['position'], $allowedPositions)) {
            return back()->withErrors(['position' => "Position '{$validated['position']}' is not valid for role '{$role->name}'. Allowed positions: " . implode(', ', $allowedPositions)])
                ->withInput();
        }
        
        // Generate email for member
        $email = UserHelper::generateUniqueMemberEmail($validated['full_name']);
        
        // Create user with selected role
        $newUser = User::create([
            'full_name' => $validated['full_name'],
            'email' => $email,
            'password' => bcrypt('password'),
            'role_id' => $validated['role_id'],
            'position' => $validated['position'],
        ]);
        
        // Create member record
        $member = Member::create([
            'user_id' => $newUser->id,
            'position' => $validated['position'],
            'joined_at' => $validated['joined_at'],
            'term_start' => $validated['term_start'],
            'term_end' => $validated['term_end'],
        ]);
        
        return redirect()->route('members.index')
            ->with('success', "Member created successfully! Email: {$email}, Password: password");
    }

    public function show(Member $member)
    {
        $this->authorizeMemberAccess($member);

        $member->load('user', 'budgets');
        return view('members.show', compact('member'));
    }

    public function edit($id)
    {
        $member = Member::find($id);
        
        if (!$member) {
            abort(404, 'Member not found');
        }
        
        $user = Auth::user();
        
        // Check if user is authenticated
        if (!$user || !$user->role) {
            abort(403, 'Unauthorized.');
        }
        
        // Only Adviser and Officer can edit members
        if (!in_array($user->role->name, ['Adviser', 'Officer'])) {
            abort(403, 'Unauthorized. Only Advisers and Officers can edit members.');
        }
        
        $roles = Role::all();
        return view('members.edit', compact('member', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $member = Member::find($id);
        
        if (!$member) {
            return back()->with('error', 'Member not found.');
        }
        
        $user = Auth::user();
        
        // Check if user is authenticated
        if (!$user || !$user->role) {
            abort(403, 'Unauthorized.');
        }
        
        // Only Adviser and Officer can update members
        if (!in_array($user->role->name, ['Adviser', 'Officer'])) {
            abort(403, 'Unauthorized. Only Advisers and Officers can update members.');
        }
        
        $validated = $request->validate([
            'position' => ['required', 'string', 'max:150'],
            'joined_at' => ['required', 'date'],
            'term_start' => ['required', 'date'],
            'term_end' => ['nullable', 'date', 'after_or_equal:term_start'],
            'role_id' => ['nullable', 'exists:roles,id'],
        ]);
        
        // Safety Check: Prevent changing the last adviser's role
        $currentUserRole = $member->user->role->name;
        $newRoleId = $request->input('role_id');
        
        if ($currentUserRole === 'Adviser') {
            $adviserCount = User::whereHas('role', function($q) {
                $q->where('name', 'Adviser');
            })->count();
            
            if ($adviserCount <= 1 && $newRoleId && $newRoleId != $member->user->role_id) {
                return back()->with('error', '⚠️⚠️⚠️ CANNOT CHANGE ROLE ⚠️⚠️⚠️<br><br>This is the LAST ADVISER in the system.');
            }
        }
        
        // Check if position is valid for the new role (if role is changing)
        if ($newRoleId && $newRoleId != $member->user->role_id) {
            $newRole = Role::find($newRoleId);
            $allowedPositions = $this->getAllowedPositions($newRole->name);
            
            if (!in_array($validated['position'], $allowedPositions)) {
                return back()->withErrors(['position' => "Position '{$validated['position']}' is not valid for role '{$newRole->name}'. Allowed positions: " . implode(', ', $allowedPositions)])
                    ->withInput();
            }
        }
        
        // Update member record
        $member->update($validated);
        
        // Also update the user's position if it changed
        if ($member->user && $member->user->position !== $validated['position']) {
            $member->user->update([
                'position' => $validated['position']
            ]);
        }
        
        // Also update the user's role if changed
        if ($newRoleId && $newRoleId != $member->user->role_id) {
            $member->user->update([
                'role_id' => $newRoleId
            ]);
        }

        return redirect()->route('members.index')
            ->with('success', "✅ {$member->user->full_name} has been updated successfully.");
    }

    public function destroy($id)
    {
        $member = Member::find($id);
        
        if (!$member) {
            return back()->with('error', '❌ Member not found.');
        }
        
        $user = Auth::user();
        
        // Check if user is authenticated
        if (!$user || !$user->role) {
            return back()->with('error', '❌ Unauthorized. Please login again.');
        }
        
        // Only Adviser and Officer can delete members
        if (!in_array($user->role->name, ['Adviser', 'Officer'])) {
            return back()->with('error', '❌ Unauthorized. Only Advisers and Officers can delete members.');
        }
        
        // Check if member has a user
        if (!$member->user) {
            return back()->with('error', '❌ Member record is invalid.');
        }
        
        // Check if trying to delete an adviser
        if ($member->user->role && $member->user->role->name === 'Adviser') {
            $adviserCount = User::whereHas('role', function($q) {
                $q->where('name', 'Adviser');
            })->count();
            
            if ($adviserCount <= 1) {
                return back()->with('error', '⚠️⚠️⚠️ CANNOT DELETE ⚠️⚠️⚠️<br><br>This is the LAST ADVISER in the system.<br><br>There must be at least one adviser to manage the system.');
            }
        }
        
        try {
            $userName = $member->user->full_name;
            $userRole = $member->user->role->name;
            $userId = $member->user_id;
            
            // Delete the member record
            $member->delete();
            
            // Delete the associated user
            if ($userId && $userRecord = User::find($userId)) {
                $userRecord->delete();
            }
            
            return redirect()->route('members.index')
                ->with('success', "✅ {$userName} ({$userRole}) has been successfully removed from the system.");
                
        } catch (\Exception $e) {
            return back()->with('error', '❌ Failed to delete member: ' . $e->getMessage());
        }
    }

    /**
     * Members can only view their own record.
     * Advisers, Officers, and Auditors can view any record.
     */
    private function authorizeMemberAccess(Member $member): void
    {
        $user = Auth::user();
        
        if (!$user || !$user->role) {
            abort(403, 'Unauthorized.');
        }

        if ($user->role->name === 'Member' && $member->user_id !== $user->id) {
            abort(403, 'You can only view your own member record.');
        }
    }

    /**
     * Get allowed positions for a given role
     */
    private function getAllowedPositions($roleName)
    {
        $positions = [
            'Adviser' => ['Adviser'],
            'Officer' => ['President', 'Secretary', 'Treasurer', 'Auditor'],
            'Auditor' => ['Auditor'],
            'Member' => ['Member'],
        ];
        
        return $positions[$roleName] ?? [];
    }
}