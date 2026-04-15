<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\User;
use App\Models\Role;
use App\Models\PositionChangeLog;
use App\Traits\LogsMemberEdits;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class MemberController extends Controller
{
    use LogsMemberEdits;

    public function __construct()
    {
        $this->middleware('auth.custom');
    }

    // -------------------------------------------------------------------------
    // Permission Helpers
    // -------------------------------------------------------------------------

    private function getAllowedRolesForCurrentUser()
    {
        $user = Auth::user();

        $map = [
            'SysAdmin' => ['all'],
            'SA'       => ['all'],
            'CA'       => ['OA', 'OO', 'OM'],
            'OA'       => ['OM'],
            'OO'       => ['OM'],
        ];

        if (in_array($user->role->abbreviation, ['SysAdmin', 'SA'])) {
            return Role::where('is_visible', true)->get();
        }

        if (isset($map[$user->role->abbreviation])) {
            $allowed = $map[$user->role->abbreviation];
            if (in_array('all', $allowed)) {
                return Role::where('is_visible', true)->get();
            }
            return Role::whereIn('abbreviation', $allowed)
                       ->where('is_visible', true)
                       ->get();
        }

        return Role::where('abbreviation', 'OM')
                   ->where('is_visible', true)
                   ->get();
    }

    /**
     * Get allowed positions for a role.
     * Uses the role's allowed_positions column if set, otherwise falls back to hardcoded mappings.
     */
    private function getAllowedPositions(Role $role): array
    {
        // 1. Use custom allowed_positions if stored
        if (!empty($role->allowed_positions) && is_array($role->allowed_positions)) {
            return $role->allowed_positions;
        }

        // 2. Fallback to hardcoded mappings (for predefined roles)
        $byAbbrev = [
            'SysAdmin' => [],
            'SA'       => ['SSLG President', 'SSLG Adviser', 'Student Affairs Head'],
            'SO'       => ['SSLG Secretary', 'SSLG Treasurer', 'SSLG PIO'],
            'CA'       => ['Club Adviser'],
            'OA'       => ['Organization President', 'Organization Vice President'],
            'OO'       => ['Organization Secretary', 'Organization Treasurer', 'Organization Auditor', 'Organization PIO'],
            'OM'       => ['Organization Member'],
            'G'        => ['Guest'],
        ];

        if ($role->abbreviation && isset($byAbbrev[$role->abbreviation])) {
            return $byAbbrev[$role->abbreviation];
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

        return $byName[$role->name] ?? [];
    }

    private function getPositionMapping(): array
    {
        return [
            1 => [],
            2 => ['SSLG President', 'SSLG Adviser', 'Student Affairs Head'],
            3 => ['SSLG Secretary', 'SSLG Treasurer', 'SSLG PIO'],
            4 => ['Organization President', 'Organization Vice President'],
            5 => ['Organization Secretary', 'Organization Treasurer', 'Organization Auditor', 'Organization PIO'],
            6 => ['Club Adviser'],
            7 => ['Regular Member'],
            8 => ['Guest'],
        ];
    }

    private function canManageMember(User $target, string $action = 'view'): bool
    {
        $current = Auth::user();

        if (in_array($current->role->abbreviation, ['SysAdmin', 'SA'])) return true;
        if ($action === 'view' && $current->id === $target->id) return true;

        if ($current->role->abbreviation === 'CA') {
            return $target->organization_id === $current->organization_id;
        }

        if (in_array($current->role->abbreviation, ['OA', 'OO'])) {
            if (in_array($target->role->abbreviation ?? '', ['OA', 'OO', 'CA', 'SA', 'SysAdmin'])) {
                return false;
            }
            return $target->organization_id === $current->organization_id;
        }

        return false;
    }

    // -------------------------------------------------------------------------
    // Index
    // -------------------------------------------------------------------------

    public function index(Request $request)
    {
        $user = auth()->user();

        if ($user->role->level !== 1 && !$user->hasPermission('members.view')) {
            abort(403, 'You are not authorized to view members.');
        }

        $query = User::with('role');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $roleFilter = $request->get('role', 'all');
        if ($roleFilter !== 'all') {
            switch ($roleFilter) {
                case 'admin':
                    $query->whereHas('role', fn($q) => $q->where('name', 'System Administrator'));
                    break;
                case 'supreme':
                    $query->whereHas('role', fn($q) => $q->where('level', '<=', 3));
                    break;
                case 'org-leader':
                    $query->whereHas('role', fn($q) => $q->whereIn('name', ['Org Admin', 'Org Officer']));
                    break;
                case 'adviser':
                    $query->whereHas('role', fn($q) => $q->where('name', 'Club Adviser'));
                    break;
                case 'member':
                    $query->whereHas('role', fn($q) => $q->where('name', 'Org Member'));
                    break;
            }
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->filled('verification')) {
            if ($request->verification === 'verified') {
                $query->whereNotNull('email_verified_at');
            } elseif ($request->verification === 'unverified') {
                $query->whereNull('email_verified_at');
            }
        }

        $statsQuery = clone $query;
        $filteredStats = [
            'system_admin' => (clone $statsQuery)->whereHas('role', fn($q) => $q->where('name', 'System Administrator'))->count(),
            'supreme'      => (clone $statsQuery)->whereHas('role', fn($q) => $q->where('level', '<=', 3))->count(),
            'leaders'      => (clone $statsQuery)->whereHas('role', fn($q) => $q->whereIn('name', ['Org Admin', 'Org Officer']))->count(),
            'members'      => (clone $statsQuery)->whereHas('role', fn($q) => $q->where('name', 'Org Member'))->count(),
            'advisers'     => (clone $statsQuery)->whereHas('role', fn($q) => $q->where('name', 'Club Adviser'))->count(),
            'all'          => (clone $statsQuery)->count(),
        ];

        $users = $query->paginate(15)->appends($request->except('page'));

        $roleColors = [
            'System Administrator' => 'purple',
            'Supreme Admin'        => 'indigo',
            'Supreme Officer'      => 'blue',
            'Org Admin'            => 'emerald',
            'Org Officer'          => 'sky',
            'Club Adviser'         => 'amber',
            'Org Member'           => 'gray',
        ];

        return view('members.index', compact('users', 'filteredStats', 'roleColors', 'roleFilter'));
    }

    // -------------------------------------------------------------------------
    // Create / Store
    // -------------------------------------------------------------------------

    public function create()
    {
        $user = Auth::user();
        if (!$user || !$user->role) abort(403);

        if ($user->role->level !== 1 && !$user->hasPermission('members.create')) {
            abort(403, 'You do not have permission to create members.');
        }

        $roles = $this->getAllowedRolesForCurrentUser();
        $positionMapping = [];
        foreach ($roles as $role) {
            $positionMapping[$role->id] = $this->getAllowedPositions($role);
        }

        return view('members.create', compact('roles', 'positionMapping'));
    }

    public function store(Request $request)
    {
        $currentUser = Auth::user();
        if (!$currentUser || !$currentUser->role) abort(403);

        if ($currentUser->role->level !== 1 && !$currentUser->hasPermission('members.create')) {
            abort(403, 'You do not have permission to create members.');
        }

        $validated = $request->validate([
            'first_name'  => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name'   => ['required', 'string', 'max:255'],
            'email'       => ['required', 'email', 'ends_with:@gmail.com', 'unique:users,email'],
            'student_id'  => ['nullable', 'string', 'unique:users,student_id', function ($attr, $val, $fail) {
                if (!empty($val) && !preg_match('/^\d{4}-\d{5}$/', $val)) {
                    $fail('Student ID must be in format: YYYY-XXXXX');
                }
            }],
            'year_level'  => ['nullable', 'string', 'in:Grade 7,Grade 8,Grade 9,Grade 10,Grade 11,Grade 12'],
            'gender'      => ['required', 'string', 'in:Male,Female,Other'],
            'phone'       => ['nullable', 'string', 'max:20', 'unique:users,phone'],
            'birthday'    => ['nullable', 'date'],
            'role_id'     => ['required', 'exists:roles,id'],
            'is_active'   => ['boolean'],
            'password'    => ['nullable', 'string', 'min:8', 'confirmed'],
        ], [
            'email.unique'      => 'This email is already registered.',
            'student_id.unique' => 'This student ID is already assigned.',
            'phone.unique'      => 'This phone number is already in use.',
        ]);

        if (!empty($validated['phone'])) {
            $phone = preg_replace('/[^0-9]/', '', $validated['phone']);
            if (substr($phone, 0, 2) == '63') $phone = substr($phone, 2);
            if (substr($phone, 0, 1) == '0') $phone = substr($phone, 1);
            $validated['phone'] = '+63' . substr($phone, 0, 10);
        }

        $selectedRole = Role::findOrFail($validated['role_id']);
        
        if (!$selectedRole->is_visible) {
            return back()->withErrors(['role_id' => 'The selected role is not available.'])->withInput();
        }

        $allowedRoleIds = $this->getAllowedRolesForCurrentUser()->pluck('id')->toArray();
        if (!in_array($selectedRole->id, $allowedRoleIds)) {
            return redirect()->route('members.index')
                ->with('error', "ACCESS DENIED: You cannot create users with the role '{$selectedRole->name}'.");
        }

        $allowedPositions = $this->getAllowedPositions($selectedRole);
        
        // Position handling: empty string for custom roles without restrictions
        if (!empty($allowedPositions)) {
            $request->validate([
                'position' => ['required', Rule::in($allowedPositions)],
            ], [
                'position.required' => 'Position is required for this role.',
                'position.in'       => 'Invalid position for this role.',
            ]);
            $position = $request->position;
        } else {
            $position = '';
        }

        if ($this->shouldClearYearLevel($selectedRole->id, $position)) {
            $validated['year_level'] = null;
        }

        if ($selectedRole->id == 1) {
            $position = '';
        }

        DB::beginTransaction();
        try {
            $fullName = $this->buildFullName($validated);
            $password = $validated['password'] ?? Str::random(10);
            $hashedPassword = Hash::make($password);

            $user = User::create([
                'full_name'       => $fullName,
                'first_name'      => $validated['first_name'],
                'middle_name'     => $validated['middle_name'] ?? null,
                'last_name'       => $validated['last_name'],
                'email'           => $validated['email'],
                'password'        => $hashedPassword,
                'role_id'         => $validated['role_id'],
                'position'        => $position,
                'student_id'      => $validated['student_id'] ?? null,
                'year_level'      => $validated['year_level'] ?? null,
                'gender'          => $validated['gender'],
                'phone'           => $validated['phone'] ?? null,
                'birthday'        => $validated['birthday'] ?? null,
                'is_active'       => $validated['is_active'] ?? true,
                'organization_id' => $currentUser->organization_id ?? null,
                'email_verified_at' => now(),
            ]);

            Member::create([
                'user_id'    => $user->id,
                'position'   => $position,
                'joined_at'  => now(),
                'term_start' => now(),
            ]);

            DB::commit();

            $user->notify(new \App\Notifications\NewUserWelcomeNotification($password));

            return redirect()->route('members.index')
                ->with('success', "Member {$fullName} created successfully! A welcome email has been sent.");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create member: ' . $e->getMessage())->withInput();
        }
    }

    // -------------------------------------------------------------------------
    // Edit / Update
    // -------------------------------------------------------------------------

    public function edit(int $id)
    {
        $member      = User::findOrFail($id);
        $currentUser = Auth::user();
        if (!$currentUser || !$currentUser->role) abort(403);

        if ($currentUser->role->level !== 1) {
            if (!$currentUser->hasPermission('members.edit') || !$this->canManageMember($member, 'edit')) {
                abort(403, 'Unauthorized to edit this member.');
            }
        }

        $roles = $this->getAllowedRolesForCurrentUser();
        $positionMapping = [];
        foreach ($roles as $role) {
            $positionMapping[$role->id] = $this->getAllowedPositions($role);
        }

        $memberRecord = $member->member;
        $positionLogs = $memberRecord
            ? PositionChangeLog::forMember($memberRecord->id)->with('changer')->orderBy('created_at', 'desc')->get()
            : collect();

        return view('members.edit', compact('member', 'roles', 'positionMapping', 'positionLogs'));
    }

    public function update(Request $request, int $id)
    {
        $user         = User::findOrFail($id);
        $memberRecord = $user->member;
        $currentUser  = Auth::user();
        if (!$currentUser || !$currentUser->role) abort(403);

        if ($currentUser->role->level !== 1) {
            if (!$currentUser->hasPermission('members.edit') || !$this->canManageMember($user, 'edit')) {
                abort(403, 'Unauthorized to edit this member.');
            }
        }

        $oldRoleId = $user->role_id;
        $newRoleId = $request->role_id;

        if ($oldRoleId != $newRoleId) {
            $oldRole = Role::find($oldRoleId);
            if ($oldRole && $oldRole->is_predefined) {
                $count = User::where('role_id', $oldRoleId)->count();
                if ($count <= 1) {
                    return back()->with('error', "Cannot change role of the last user with the role '{$oldRole->name}'. This role is required for system functionality.");
                }
            }
        }

        $selectedRole = Role::findOrFail($newRoleId);
        
        if (!$selectedRole->is_visible) {
            return back()->withErrors(['role_id' => 'The selected role is not available.'])->withInput();
        }

        $validated = $request->validate([
            'first_name'  => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name'   => ['required', 'string', 'max:255'],
            'email'       => ['required', 'email', 'ends_with:@gmail.com', 'unique:users,email,' . $user->id],
            'student_id'  => ['nullable', 'string', 'unique:users,student_id,' . $user->id, function ($attr, $val, $fail) {
                if (!empty($val) && !preg_match('/^\d{4}-\d{5}$/', $val)) $fail('Student ID must be in format: YYYY-XXXXX');
            }],
            'year_level'  => ['nullable', 'string', 'in:Grade 7,Grade 8,Grade 9,Grade 10,Grade 11,Grade 12'],
            'gender'      => ['required', 'string', 'in:Male,Female,Other'],
            'phone'       => ['nullable', 'string', 'max:20', 'unique:users,phone,' . $user->id],
            'birthday'    => ['nullable', 'date'],
            'role_id'     => ['required', 'exists:roles,id'],
            'is_active'   => ['boolean'],
            'password'    => ['nullable', 'string', 'min:8', 'confirmed'],
        ], [
            'email.unique'      => 'This email is already registered.',
            'student_id.unique' => 'This student ID is already assigned.',
            'phone.unique'      => 'This phone number is already in use.',
        ]);

        if (!empty($validated['phone'])) {
            $phone = preg_replace('/[^0-9]/', '', $validated['phone']);
            if (substr($phone, 0, 2) == '63') $phone = substr($phone, 2);
            if (substr($phone, 0, 1) == '0') $phone = substr($phone, 1);
            $validated['phone'] = '+63' . substr($phone, 0, 10);
        }

        $allowedPositions = $this->getAllowedPositions($selectedRole);
        
        // Position handling
        $position = '';
        if (!empty($allowedPositions)) {
            $request->validate([
                'position' => ['required', Rule::in($allowedPositions)],
            ], [
                'position.required' => 'Position is required for this role.',
                'position.in'       => 'Invalid position for this role.',
            ]);
            $position = $request->position;
        }

        if ($this->shouldClearYearLevel($selectedRole->id, $position)) {
            $validated['year_level'] = null;
        }

        $allowedRoleIds = $this->getAllowedRolesForCurrentUser()->pluck('id')->toArray();
        if (!in_array($selectedRole->id, $allowedRoleIds)) {
            return redirect()->route('members.index')
                ->with('error', "ACCESS DENIED: You cannot assign the role '{$selectedRole->name}'.");
        }

        if ($selectedRole->id == 1) {
            $position = '';
        }

        // Validate position change reason only if allowed positions exist and position changed
        if (!empty($allowedPositions) && trim($position) !== trim($user->position ?? '')) {
            $request->validate([
                'position_change_reason' => ['required', 'string', 'max:1000'],
            ], [
                'position_change_reason.required' => 'A reason is required when changing position.',
            ]);
        }

        DB::beginTransaction();
        try {
            $oldPosition = $user->position;
            $newPosition = $position;
            $fullName = $this->buildFullName($validated);

            $user->fill([
                'full_name'   => $fullName,
                'first_name'  => $validated['first_name'],
                'middle_name' => $validated['middle_name'] ?? null,
                'last_name'   => $validated['last_name'],
                'email'       => $validated['email'],
                'student_id'  => $validated['student_id'] ?? null,
                'year_level'  => $validated['year_level'] ?? null,
                'gender'      => $validated['gender'],
                'phone'       => $validated['phone'] ?? null,
                'birthday'    => $validated['birthday'] ?? null,
                'role_id'     => $validated['role_id'],
                'position'    => $newPosition,
                'is_active'   => $validated['is_active'] ?? $user->is_active,
            ]);

            if (!empty($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }
            $user->save();

            if ($memberRecord) {
                $memberRecord->position = $newPosition;
                $memberRecord->save();
                if (trim($oldPosition) !== trim($newPosition)) {
                    $reason = $request->position_change_reason ?? null;
                    $this->logPositionChange($memberRecord, $oldPosition, $newPosition, $reason);
                }
            }

            DB::commit();

            $message = "Member {$fullName} updated successfully.";
            if (trim($oldPosition) !== trim($newPosition)) {
                $message .= " Position changed from '{$oldPosition}' to '{$newPosition}'.";
            }
            return redirect()->route('members.index')->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('MemberController@update error: ' . $e->getMessage());
            return back()->with('error', 'Failed to update member: ' . $e->getMessage())->withInput();
        }
    }

    // -------------------------------------------------------------------------
    // Show, Destroy, Activate, Deactivate, History methods (unchanged)
    // -------------------------------------------------------------------------

    public function show(int $id)
    {
        // ... keep as in your original ...
        $user        = User::with('role', 'member')->findOrFail($id);
        $currentUser = Auth::user();
        if (!$currentUser || !$currentUser->role) abort(403);
        if ($currentUser->role->level !== 1 && !$this->canManageMember($user, 'view')) {
            abort(403, 'Unauthorized to view this member.');
        }
        $member          = $user;
        $memberSince     = $member->created_at->format('F d, Y');
        $documentsCount  = $member->documents()->count();
        $budgetsCount    = $member->budgets()->count();
        $recentActivity  = collect();
        $recentDocuments = $member->documents()->latest()->take(5)->get();
        $recentBudgets   = $member->budgets()->latest()->take(5)->get();
        foreach ($recentDocuments as $doc) {
            $recentActivity->push(['type' => 'document', 'description' => "Uploaded document: {$doc->title}", 'time' => $doc->created_at->diffForHumans()]);
        }
        foreach ($recentBudgets as $budget) {
            $recentActivity->push(['type' => 'budget', 'description' => "Submitted budget request for ₱" . number_format($budget->amount, 2), 'time' => $budget->created_at->diffForHumans()]);
        }
        $recentActivity = $recentActivity->sortByDesc(fn($a) => $a['time'])->take(5);
        return view('members.show', compact('member', 'memberSince', 'documentsCount', 'budgetsCount', 'recentActivity'));
    }

    public function destroy(int $id)
    {
        // ... keep as in your original ...
        $targetUser  = User::findOrFail($id);
        $currentUser = Auth::user();
        if ($currentUser->role->level !== 1 && !$currentUser->hasPermission('members.delete')) abort(403);
        if ($targetUser->role->level >= $currentUser->role->level && $currentUser->role->level !== 1) {
            return back()->with('error', 'You cannot delete a user with a role level equal or higher than yours.');
        }
        if ($currentUser->organization_id && $targetUser->organization_id !== $currentUser->organization_id) {
            return back()->with('error', 'You cannot delete members from another organisation.');
        }
        if ($targetUser->id === $currentUser->id) {
            return back()->with('error', 'You cannot delete your own account.');
        }
        if ($targetUser->role && $targetUser->role->is_predefined) {
            $count = User::where('role_id', $targetUser->role_id)->count();
            if ($count <= 1) {
                return back()->with('error', "Cannot delete the last user with the role '{$targetUser->role->name}'. This role is required for system functionality.");
            }
        }
        try {
            $userName = $targetUser->full_name;
            $userRole = $targetUser->role->name ?? 'Unknown';
            $targetUser->member?->delete();
            $targetUser->delete();
            return redirect()->route('members.index')->with('success', "{$userName} ({$userRole}) has been removed from the system.");
        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete member: ' . $e->getMessage());
        }
    }

    public function deactivate(int $id)
    {
        // ... keep as in your original ...
        $user = User::findOrFail($id);
        if ($user->id === auth()->id()) return response()->json(['error' => 'You cannot deactivate your own account.'], 403);
        $currentUser = Auth::user();
        if ($currentUser->role->level !== 1 && !$this->canManageMember($user, 'edit')) {
            return response()->json(['error' => 'Unauthorized.'], 403);
        }
        $user->update(['is_active' => false]);
        return response()->json(['success' => true, 'message' => 'Account deactivated successfully.']);
    }

    public function activate(int $id)
    {
        $user = User::findOrFail($id);
        $currentUser = Auth::user();
        if ($currentUser->role->level !== 1 && !$this->canManageMember($user, 'edit')) {
            return response()->json(['error' => 'Unauthorized.'], 403);
        }
        $user->update(['is_active' => true]);
        return response()->json(['success' => true, 'message' => 'Account activated successfully.']);
    }

    public function getPositionHistoryData(int $id)
    {
        // ... keep as in your original ...
        try {
            $user = User::findOrFail($id);
            $memberRecord = $user->member;
            $currentUser = Auth::user();
            if (!$currentUser || !$currentUser->role) return response()->json(['error' => 'Unauthorized'], 403);
            if ($currentUser->role->level !== 1 && !$this->canManageMember($user, 'view')) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }
            if (!$memberRecord) return response()->json([]);
            $logs = PositionChangeLog::forMember($memberRecord->id)->with('changer')->orderBy('created_at', 'desc')->get()
                ->map(fn($log) => [
                    'id' => $log->id,
                    'old_position' => $log->old_position,
                    'new_position' => $log->new_position,
                    'reason' => $log->reason,
                    'created_at' => $log->created_at,
                    'ip_address' => $log->ip_address,
                    'changer' => $log->changer ? ['id' => $log->changer->id, 'name' => $log->changer->full_name] : null,
                ]);
            return response()->json($logs);
        } catch (\Exception $e) {
            \Log::error('MemberController@getPositionHistoryData: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load position change log.'], 500);
        }
    }

    public function editHistory(int $id)
    {
        // ... keep as in your original ...
        $user = User::with('role', 'member')->findOrFail($id);
        $memberRecord = $user->member;
        if (!$memberRecord) abort(404, 'Member record not found.');
        $currentUser = Auth::user();
        if (!$currentUser || !$currentUser->role) abort(403);
        if ($currentUser->role->level !== 1 && !$this->canManageMember($user, 'view')) {
            abort(403, 'Unauthorized to view this member\'s history.');
        }
        $positionLogs = PositionChangeLog::forMember($memberRecord->id)->with('changer')->orderBy('created_at', 'desc')->paginate(20);
        return view('members.edit-history', compact('user', 'memberRecord', 'positionLogs'));
    }

    // -------------------------------------------------------------------------
    // Private Helpers
    // -------------------------------------------------------------------------

    private function buildFullName(array $validated): string
    {
        return preg_replace('/\s+/', ' ', trim(
            $validated['first_name'] . ' ' .
            ($validated['middle_name'] ?? '') . ' ' .
            $validated['last_name']
        ));
    }

    private function shouldClearYearLevel($roleId, $position): bool
    {
        $alwaysNonStudent = [1, 6, 8];
        if (in_array($roleId, $alwaysNonStudent)) return true;
        if ($roleId == 2 && $position !== 'SSLG President') return true;
        return false;
    }
}