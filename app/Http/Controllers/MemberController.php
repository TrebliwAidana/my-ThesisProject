<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\User;
use App\Models\Role;
use App\Models\PositionChangeLog;  // Add this
use App\Helpers\UserHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;  // Add this

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
            $members = Member::with('user', 'positionChangedBy')  // Add positionChangedBy
                ->where('user_id', $user->id)
                ->paginate(10);
        } else {
            $members = Member::with('user', 'positionChangedBy')  // Add positionChangedBy
                ->latest()
                ->paginate(10);
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

        // Filter roles based on user's permission
        if ($user->role->name === 'Adviser') {
            // Adviser can create all roles
            $roles = Role::all();
        } else {
            // Officer can only create Member role
            $roles = Role::where('name', 'Member')->get();
        }

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

        // RESTRICTION: Officers cannot create Advisers, Officers, or Auditors
        if ($user->role->name === 'Officer') {
            $selectedRole = Role::find($validated['role_id']);

            // Officers can only create Members
            if ($selectedRole->name !== 'Member') {
                return redirect()->route('members.index')
                    ->with('error', '❌ ACCESS DENIED\n\nYou are not authorized to create this role.\n\nAs an Officer, you can only create regular Members.\n\nPlease contact an Adviser if you need to create higher-level accounts.');
            }
        }

        // Check if position is valid for the selected role
        $role = Role::find($validated['role_id']);
        $allowedPositions = $this->getAllowedPositions($role->name);

        if (!in_array($validated['position'], $allowedPositions)) {
            return back()->withErrors(['position' => "Position '{$validated['position']}' is not valid for role '{$role->name}'. Allowed positions: " . implode(', ', $allowedPositions)])
                ->withInput();
        }

        // Generate email for member
        $email = UserHelper::generateUniqueMemberEmail($validated['full_name']);

        DB::beginTransaction();

        try {
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

            DB::commit();

            return redirect()->route('members.index')
                ->with('success', "✅ Member created successfully! Email: {$email}, Password: password");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', '❌ Failed to create member: ' . $e->getMessage());
        }
    }

    public function show(Member $member)
    {
        $this->authorizeMemberAccess($member);

        $member->load('user', 'budgets', 'positionChangeHistory.changer');  // Add position history
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

        // RESTRICTION: Officers cannot edit Advisers, Officers, or Auditors
        if ($user->role->name === 'Officer') {
            $targetRole = $member->user->role->name;
            if ($targetRole !== 'Member') {
                return redirect()->route('members.index')
                    ->with('error', '❌ ACCESS DENIED\n\nYou are not authorized to edit this member.\n\nAs an Officer, you can only edit regular Members.\n\nAdvisers, Officers, and Auditors can only be edited by an Adviser.');
            }
        }

        // Filter roles based on user's permission for editing
        if ($user->role->name === 'Adviser') {
            $roles = Role::all();
        } else {
            // Officers can only change role to Member
            $roles = Role::where('name', 'Member')->get();
        }

        $positions = Member::POSITIONS;  // Add positions array

        return view('members.edit', compact('member', 'roles', 'positions'));
    }

    public function update(Request $request, $id)
    {
        $member = Member::find($id);

        if (!$member) {
            return back()->with('error', '❌ Member not found.');
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

        // RESTRICTION: Officers cannot edit Advisers, Officers, or Auditors
        if ($user->role->name === 'Officer') {
            $targetRole = $member->user->role->name;
            if ($targetRole !== 'Member') {
                return redirect()->route('members.index')
                    ->with('error', '❌ ACCESS DENIED\n\nYou are not authorized to edit this member.\n\nAs an Officer, you can only edit regular Members.\n\nAdvisers, Officers, and Auditors can only be edited by an Adviser.');
            }
        }

        $validated = $request->validate([
            'position' => ['required', 'string', 'max:150'],
            'joined_at' => ['required', 'date'],
            'term_start' => ['required', 'date'],
            'term_end' => ['nullable', 'date', 'after_or_equal:term_start'],
            'role_id' => ['nullable', 'exists:roles,id'],
            'position_change_reason' => 'nullable|string|max:500',  // Add this
        ]);

        DB::beginTransaction();

        try {
            $oldPosition = $member->position;
            $newPosition = $validated['position'];
            $newRoleId = $request->input('role_id');

            // Check if position is being changed
            if ($oldPosition !== $newPosition) {
                // Validate if position change is allowed
                $currentUserMember = $user->member;
                
                if (!$currentUserMember) {
                    return redirect()->back()
                        ->with('error', 'Your account is not linked to a member record.')
                        ->withInput();
                }

                // Validate business rules for position change
                if (!$this->validatePositionChangeRules($member, $oldPosition, $newPosition, $user)) {
                    return redirect()->back()
                        ->with('error', 'This position change violates business rules.')
                        ->withInput();
                }

                // Validate reason is provided for position change
                if (empty($request->position_change_reason)) {
                    return redirect()->back()
                        ->with('error', 'Please provide a reason for changing the position.')
                        ->withInput();
                }

                // Log the position change
                PositionChangeLog::create([
                    'member_id' => $member->id,
                    'changed_by' => Auth::id(),
                    'old_position' => $oldPosition,
                    'new_position' => $newPosition,
                    'reason' => $request->position_change_reason,
                    'ip_address' => $request->ip()
                ]);

                // Update member with position change tracking
                $member->position = $newPosition;
                $member->position_changed_at = now();
                $member->position_changed_by = Auth::id();
            }

            // RESTRICTION: Officers cannot upgrade roles
            if ($user->role->name === 'Officer') {
                // If trying to change role
                if ($newRoleId && $newRoleId != $member->user->role_id) {
                    $newRole = Role::find($newRoleId);

                    // Officers cannot promote anyone to Adviser, Officer, or Auditor
                    if ($newRole->name !== 'Member') {
                        return redirect()->route('members.index')
                            ->with('error', '❌ ACCESS DENIED\n\nYou are not authorized to promote members to higher roles.\n\nOnly Advisers can create or promote to Adviser, Officer, or Auditor roles.\n\nPlease contact an Adviser if you need to create higher-level accounts.');
                    }
                }
            }

            // Safety Check: Prevent changing the last adviser's role
            $currentUserRole = $member->user->role->name;

            if ($currentUserRole === 'Adviser') {
                $adviserCount = User::whereHas('role', function($q) {
                    $q->where('name', 'Adviser');
                })->count();

                if ($adviserCount <= 1 && $newRoleId && $newRoleId != $member->user->role_id) {
                    return back()->with('error', '⚠️⚠️⚠️ CANNOT CHANGE ROLE ⚠️⚠️⚠️\n\nThis is the LAST ADVISER in the system.\n\nYou cannot change the role of the last adviser. There must be at least one adviser to manage the system.\n\nPlease create another adviser first, then you can change this one.');
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

            // Update member record (only if position wasn't already updated)
            if ($oldPosition === $newPosition) {
                $member->update($validated);
            } else {
                $member->joined_at = $validated['joined_at'];
                $member->term_start = $validated['term_start'];
                $member->term_end = $validated['term_end'];
                $member->save();
            }

            // Also update the user's position if it changed
            if ($member->user && $member->user->position !== $validated['position']) {
                $member->user->update([
                    'position' => $validated['position']
                ]);
            }

            // Also update the user's role if changed (and allowed)
            if ($newRoleId && $newRoleId != $member->user->role_id) {
                // Double-check for officer trying to upgrade
                if ($user->role->name === 'Officer') {
                    $newRole = Role::find($newRoleId);
                    if ($newRole->name !== 'Member') {
                        return redirect()->route('members.index')
                            ->with('error', '❌ ACCESS DENIED\n\nYou are not authorized to change roles.\n\nOnly Advisers can change roles to Adviser, Officer, or Auditor.');
                    }
                }
                $member->user->update([
                    'role_id' => $newRoleId
                ]);
            }

            DB::commit();

            $message = ($oldPosition !== $newPosition) 
                ? "✅ {$member->user->full_name} has been updated successfully. Position changed from {$oldPosition} to {$newPosition}."
                : "✅ {$member->user->full_name} has been updated successfully.";

            return redirect()->route('members.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', '❌ Failed to update member: ' . $e->getMessage());
        }
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

        // RESTRICTION: Officers cannot delete Advisers, Officers, or Auditors
        if ($user->role->name === 'Officer') {
            $userRole = $member->user->role->name;
            if ($userRole !== 'Member') {
                return redirect()->route('members.index')
                    ->with('error', '❌ ACCESS DENIED\n\nYou are not authorized to delete this member.\n\nAs an Officer, you can only delete regular Members.\n\nAdvisers, Officers, and Auditors can only be deleted by an Adviser.');
            }
        }

        // Check if trying to delete an adviser
        if ($member->user->role && $member->user->role->name === 'Adviser') {
            $adviserCount = User::whereHas('role', function($q) {
                $q->where('name', 'Adviser');
            })->count();

            if ($adviserCount <= 1) {
                return back()->with('error', '⚠️⚠️⚠️ CANNOT DELETE ⚠️⚠️⚠️\n\nThis is the LAST ADVISER in the system.\n\nThere must be at least one adviser to manage the system.\n\nPlease create another adviser first before deleting this one.');
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
     * Display position change history for a member.
     */
    public function positionHistory($id)
    {
        $member = Member::with('user')->findOrFail($id);
        
        // Check authorization
        $user = Auth::user();
        if ($user->role->name === 'Member' && $member->user_id !== $user->id) {
            abort(403, 'You can only view your own position history.');
        }
        
        $history = PositionChangeLog::with('changer')
            ->where('member_id', $id)
            ->orderBy('created_at', 'desc')
            ->get();
            
        return view('members.position-history', compact('member', 'history'));
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

    /**
     * Validate business rules for position changes
     */
    private function validatePositionChangeRules($member, $oldPosition, $newPosition, $changingUser)
    {
        // Rule 1: Cannot demote the last officer if no other officers exist
        if ($oldPosition === 'Officer' && $newPosition !== 'Officer') {
            $officerCount = Member::where('position', 'Officer')->count();
            if ($officerCount <= 1) {
                return false;
            }
        }
        
        // Rule 2: Cannot demote the last adviser
        if ($oldPosition === 'Adviser' && $newPosition !== 'Adviser') {
            $adviserCount = Member::where('position', 'Adviser')->count();
            if ($adviserCount <= 1) {
                return false;
            }
        }
        
        // Rule 3: Cannot demote yourself if you're the only admin/adviser
        if ($changingUser->id === $member->user_id) {
            if ($oldPosition === 'Adviser' && $newPosition !== 'Adviser') {
                return false;
            }
        }
        
        return true;
    }
}