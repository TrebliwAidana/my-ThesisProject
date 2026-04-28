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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Models\FinancialTransaction;
use App\Services\AuditLogger;

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

    private function isSysAdmin(User $user): bool
    {
        return in_array($user->role->abbreviation, ['SysAdmin', 'SA'])
            || (int) $user->role->level === 1;
    }

    private function getAllowedRolesForCurrentUser(): \Illuminate\Database\Eloquent\Collection
    {
        $user = Auth::user();

        // System Administrator can assign any visible role
        if ($this->isSysAdmin($user)) {
            return Role::where('is_visible', true)->get();
        }

        // Club Adviser can assign any visible role except System Administrator (role_id 1)
        if ($user->role->name === 'Club Adviser') {
            return Role::where('is_visible', true)
                ->where('id', '!=', 1)
                ->get();
        }

        // Treasurer, Auditor, Guest, and any custom role: no role assignment rights.
        // Use whereRaw('0') to return a genuine empty Eloquent Collection so that
        // ->pluck() and ->contains() callers (e.g. assertRoleIsAllowed) stay safe.
        return Role::whereRaw('0')->get();
    }

    /** Uses Member::VALID_POSITIONS as the single source of truth. */
    private function getAllowedPositions(Role $role): array
    {
        return Member::VALID_POSITIONS[$role->id] ?? [];
    }

    private function buildPositionMapping(iterable $roles): array
    {
        $map = [];
        foreach ($roles as $role) {
            $map[$role->id] = $this->getAllowedPositions($role);
        }
        return $map;
    }

    private function canManageMember(User $target, string $action = 'view'): bool
    {
        $current = Auth::user();

        if ($this->isSysAdmin($current)) return true;

        // Any user may view their own profile
        if ($action === 'view' && $current->id === $target->id) return true;

        // Club Adviser can manage all non-SysAdmin members
        if ($current->role->name === 'Club Adviser') {
            return !$this->isSysAdmin($target);
        }

        // Treasurer and Auditor can only view — no manage rights over other members
        return false;
    }

    private function requiresPermission(string $permission): void
    {
        $user = Auth::user();
        if (!$this->isSysAdmin($user) && !$user->hasPermission($permission)) {
            abort(403, "You are not authorized to perform this action.");
        }
    }

    // -------------------------------------------------------------------------
    // Index
    // -------------------------------------------------------------------------

    public function index(Request $request)
    {
        $this->requiresPermission('members.view');

        $query = User::with(['role:id,name,abbreviation,level']);

        if ($search = $request->input('search')) {
            $query->where(fn($q) =>
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
            );
        }

        $roleFilter = $request->input('role', 'all');
        match ($roleFilter) {
            'admin'   => $query->whereHas('role', fn($q) => $q->where('name', 'System Administrator')),
            'adviser' => $query->whereHas('role', fn($q) => $q->where('name', 'Club Adviser')),
            'treasurer' => $query->whereHas('role', fn($q) => $q->where('name', 'Treasurer')),
            'auditor'   => $query->whereHas('role', fn($q) => $q->where('name', 'Auditor')),
            'guest'     => $query->whereHas('role', fn($q) => $q->where('name', 'Guest')),
            'custom'    => $query->whereHas('role', fn($q) => $q->whereNotIn('id', array_keys(Member::VALID_POSITIONS))),
            default     => null,
        };

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        if ($request->filled('verification')) {
            $request->verification === 'verified'
                ? $query->whereNotNull('email_verified_at')
                : $query->whereNull('email_verified_at');
        }

        $statsBase     = clone $query;
        $predefinedIds = array_keys(Member::VALID_POSITIONS);
        $filteredStats = [
            'admin'     => (clone $statsBase)->whereHas('role', fn($q) => $q->where('name', 'System Administrator'))->count(),
            'adviser'   => (clone $statsBase)->whereHas('role', fn($q) => $q->where('name', 'Club Adviser'))->count(),
            'treasurer' => (clone $statsBase)->whereHas('role', fn($q) => $q->where('name', 'Treasurer'))->count(),
            'auditor'   => (clone $statsBase)->whereHas('role', fn($q) => $q->where('name', 'Auditor'))->count(),
            'guest'     => (clone $statsBase)->whereHas('role', fn($q) => $q->where('name', 'Guest'))->count(),
            'custom'    => (clone $statsBase)->whereHas('role', fn($q) => $q->whereNotIn('id', $predefinedIds))->count(),
            'all'       => (clone $statsBase)->count(),
        ];

        $users = $query->paginate(15)->appends($request->except('page'));

        $roleColors = [
            'System Administrator' => 'purple',
            'Club Adviser'         => 'amber',
            'Treasurer'            => 'emerald',
            'Auditor'              => 'blue',
            'Guest'                => 'gray',
        ];

        return view('members.index', compact('users', 'filteredStats', 'roleColors', 'roleFilter'));
    }

    // -------------------------------------------------------------------------
    // Create / Store
    // -------------------------------------------------------------------------

    public function create()
    {
        $this->requiresPermission('members.create');
 
        $roles               = $this->getAllowedRolesForCurrentUser();
        $positionMapping     = $this->buildPositionMapping($roles);
        // Passed so the view can derive "student role" client-side without
        // hardcoding any role IDs or position strings.
        $nonStudentPositions = Member::NON_STUDENT_POSITIONS;
 
        return view('members.create', compact('roles', 'positionMapping', 'nonStudentPositions'));
    }
    public function store(Request $request)
    {
        $this->requiresPermission('members.create');

        $validated    = $this->validateMemberRequest($request);
        $selectedRole = Role::findOrFail($validated['role_id']);

        $this->assertRoleIsVisible($selectedRole);
        $this->authorize('assignRole', [User::class, $selectedRole]);
        $this->assertRoleIsAllowed($selectedRole);

        $position = $this->resolvePositionForRole($selectedRole, $request->input('position'));

        if ($this->shouldClearYearLevel($selectedRole->id, $position)) {
            $validated['year_level'] = null;
        }

        DB::beginTransaction();
        try {
            $fullName = $this->buildFullName($validated);
            $password = $validated['password'] ?? Str::random(10);

            $user = User::create([
                'full_name'   => $fullName,
                'first_name'  => $validated['first_name'],
                'middle_name' => $validated['middle_name'] ?? null,
                'last_name'   => $validated['last_name'],
                'email'       => $validated['email'],
                'password'    => Hash::make($password),
                'role_id'     => $validated['role_id'],
                'position'    => $position,
                'student_id'  => $validated['student_id'] ?? null,
                'year_level'  => $validated['year_level'] ?? null,
                'gender'      => $validated['gender'],
                'phone'       => $validated['phone'] ?? null,
                'birthday'    => $validated['birthday'] ?? null,
                'is_active'   => $validated['is_active'] ?? true,
                'avatar'      => $request->hasFile('avatar') ? $this->uploadAvatar($request->file('avatar')) : null,
            ]);

            Member::create([
                'user_id'    => $user->id,
                'position'   => $position,
                'joined_at'  => $request->input('joined_at', now()),
                'term_start' => $request->input('term_start', now()),
                'term_end'   => $request->input('term_end') ?: null,
            ]);

            DB::commit();

            AuditLogger::log('created', $user, "Member: {$user->full_name}", [], $user->toArray());
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
        $user        = User::findOrFail($id);
        $currentUser = Auth::user();
 
        if (!$this->isSysAdmin($currentUser)) {
            if (!$currentUser->hasPermission('members.edit') || !$this->canManageMember($user, 'edit')) {
                abort(403, 'Unauthorized to edit this member.');
            }
        }
 
        $roles               = $this->getAllowedRolesForCurrentUser();
        $positionMapping     = $this->buildPositionMapping($roles);
        $nonStudentPositions = Member::NON_STUDENT_POSITIONS;
        $memberRecord        = $user->member;
 
        $positionLogs = $memberRecord
            ? PositionChangeLog::forMember($memberRecord->id)
                ->with('changer')
                ->orderByDesc('created_at')
                ->get()
            : collect();
 
        return view('members.edit', compact(
            'user', 'roles', 'positionMapping', 'nonStudentPositions', 'positionLogs'
        ))->with('member', $user);
    }

    public function update(Request $request, int $id)
    {
        $user         = User::findOrFail($id);
        $memberRecord = $user->member;
        $currentUser  = Auth::user();

        // Guest account: only avatar updates allowed (SysAdmin only for anything else)
        if ($user->email === 'guest@gmail.com') {
            return $this->handleGuestUpdate($request, $user, $currentUser);
        }

        if (!$this->isSysAdmin($currentUser)) {
            if (!$currentUser->hasPermission('members.edit') || !$this->canManageMember($user, 'edit')) {
                abort(403, 'Unauthorized to edit this member.');
            }
        }

        $oldRoleId    = $user->role_id;
        $newRoleId    = (int) $request->role_id;
        $selectedRole = Role::findOrFail($newRoleId);

        $this->assertRoleIsVisible($selectedRole);
        $this->guardLastUserInRole($oldRoleId, $newRoleId, $currentUser);

        // Resolve once — passed into assertRoleIsAllowed to avoid a redundant DB query
        $allowedRoles = $this->getAllowedRolesForCurrentUser();
        $this->assertRoleIsAllowed($selectedRole, $allowedRoles);

        $validated = $this->validateMemberRequest($request, $user->id);

        [$newPosition, $positionChanged, $autoChanged] = $this->resolvePositionChange(
            $user, $selectedRole, $oldRoleId, $newRoleId, $request->input('position')
        );

        if ($positionChanged && $request->boolean('position_manually_changed')) {
            $request->validate([
                'position_change_reason' => ['required', 'string', 'max:1000'],
            ], [
                'position_change_reason.required' => 'A reason is required when changing position.',
            ]);
        }

        if ($this->shouldClearYearLevel($selectedRole->id, $newPosition)) {
            $validated['year_level'] = null;
        }

        DB::beginTransaction();
        try {
            $fullName    = $this->buildFullName($validated);
            $oldData     = $user->getOriginal();
            // Must be captured before save() — Laravel clears dirty/original state after persist
            $oldPosition = (string) ($user->position ?? '');

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

            if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
                $this->deleteAvatar($user->avatar);
                $user->avatar = $this->uploadAvatar($request->file('avatar'));
            }

            $user->save();

            if ($memberRecord) {
                $memberRecord->position = $newPosition;
                $memberRecord->save();

                if ($positionChanged) {
                    $reason = $autoChanged
                        ? "Auto-updated due to role change from role ID {$oldRoleId} to {$newRoleId}"
                        : ($request->input('position_change_reason') ?? null);

                    $this->logPositionChange($memberRecord, $oldPosition, $newPosition, $reason);
                }
            }

            DB::commit();

            AuditLogger::log('updated', $user, "Member: {$user->full_name}", $oldData, $user->getChanges());

            return redirect()->route('members.index')
                ->with('success', $this->buildUpdateSuccessMessage($fullName, $positionChanged, $autoChanged, $oldPosition, $newPosition));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('MemberController@update error: ' . $e->getMessage());
            return back()->with('error', 'Failed to update member: ' . $e->getMessage())->withInput();
        }
    }

    // -------------------------------------------------------------------------
    // Show
    // -------------------------------------------------------------------------

    public function show(int $id)
    {
        $this->requiresPermission('members.view');

        $user        = User::with('role', 'member')->findOrFail($id);
        $memberSince = optional($user->member)->joined_at ?? $user->created_at;

        $documentsCount             = $user->documents()->count();
        $financialTransactionsCount = FinancialTransaction::where('user_id', $user->id)->count();

        $recentFinancialTransactions = FinancialTransaction::where('user_id', $user->id)
            ->latest('transaction_date')
            ->take(5)
            ->get()
            ->map(fn($tx) => [
                'type'        => 'financial',
                'description' => ucfirst($tx->type) . ": {$tx->description} (₱" . number_format($tx->amount, 2) . ")",
                'time'        => $tx->transaction_date->diffForHumans(),
            ]);

        return view('members.show', compact(
            'user',
            'memberSince',
            'documentsCount',
            'financialTransactionsCount',
            'recentFinancialTransactions'
        ));
    }

    // -------------------------------------------------------------------------
    // Destroy
    // -------------------------------------------------------------------------

    public function destroy(int $id)
    {
        $target      = User::findOrFail($id);
        $currentUser = Auth::user();

        if ($target->email === 'guest@gmail.com') {
            return back()->with('error', 'The shared guest account cannot be deleted.');
        }

        $this->requiresPermission('members.delete');

        if (!$this->isSysAdmin($currentUser) && $target->role->level >= $currentUser->role->level) {
            return back()->with('error', 'You cannot delete a user with a role level equal or higher than yours.');
        }

        if ($target->id === $currentUser->id) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        if ($target->role?->is_predefined && User::where('role_id', $target->role_id)->count() <= 1) {
            return back()->with('error', "Cannot delete the last user with the role '{$target->role->name}'. This role is required for system functionality.");
        }

        try {
            AuditLogger::log('deleted', $target, "Member: {$target->full_name}", $target->toArray(), []);

            $target->member?->delete();
            $target->delete();

            return redirect()->route('members.index')
                ->with('success', "{$target->full_name} ({$target->role->name}) has been removed from the system.");

        } catch (\Exception $e) {
            return back()->with('error', 'Failed to delete member: ' . $e->getMessage());
        }
    }

    // -------------------------------------------------------------------------
    // Activate / Deactivate
    // -------------------------------------------------------------------------

    public function deactivate(int $id)
    {
        $user        = User::findOrFail($id);
        $currentUser = Auth::user();

        if ($user->id === $currentUser->id) {
            return response()->json(['error' => 'You cannot deactivate your own account.'], 403);
        }

        if (!$this->isSysAdmin($currentUser) && !$this->canManageMember($user, 'edit')) {
            return response()->json(['error' => 'Unauthorized.'], 403);
        }

        $oldStatus = $user->is_active;
        $user->update(['is_active' => false]);

        AuditLogger::log('deactivated', $user, "Member: {$user->full_name}", ['is_active' => $oldStatus], ['is_active' => false]);

        return response()->json(['success' => true, 'message' => 'Account deactivated successfully.']);
    }

    public function activate(int $id)
    {
        $user        = User::findOrFail($id);
        $currentUser = Auth::user();

        if (!$this->isSysAdmin($currentUser) && !$this->canManageMember($user, 'edit')) {
            return response()->json(['error' => 'Unauthorized.'], 403);
        }

        $oldStatus = $user->is_active;
        $user->update(['is_active' => true]);

        AuditLogger::log('activated', $user, "Member: {$user->full_name}", ['is_active' => $oldStatus], ['is_active' => true]);

        return response()->json(['success' => true, 'message' => 'Account activated successfully.']);
    }

    // -------------------------------------------------------------------------
    // Position Change Log (AJAX)
    // -------------------------------------------------------------------------

    public function getPositionHistoryData(int $id)
    {
        try {
            $user         = User::findOrFail($id);
            $currentUser  = Auth::user();
            $memberRecord = $user->member;

            if (!$currentUser?->role) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            if (!$this->isSysAdmin($currentUser) && !$this->canManageMember($user, 'view')) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            if (!$memberRecord) {
                return response()->json([]);
            }

            $logs = PositionChangeLog::forMember($memberRecord->id)
                ->with('changer')
                ->orderByDesc('created_at')
                ->get()
                ->map(fn($log) => [
                    'id'           => $log->id,
                    'old_position' => $log->old_position,
                    'new_position' => $log->new_position,
                    'reason'       => $log->reason,
                    'created_at'   => $log->created_at,
                    'ip_address'   => $log->ip_address,
                    'changer'      => $log->changer
                        ? ['id' => $log->changer->id, 'name' => $log->changer->full_name]
                        : null,
                ]);

            return response()->json($logs);

        } catch (\Exception $e) {
            Log::error('MemberController@getPositionHistoryData: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load position change log.'], 500);
        }
    }

    // -------------------------------------------------------------------------
    // Edit History Page
    // -------------------------------------------------------------------------

    public function editHistory(int $id)
    {
        $user         = User::with('role', 'member')->findOrFail($id);
        $memberRecord = $user->member ?? abort(404, 'Member record not found.');
        $currentUser  = Auth::user();

        if (!$currentUser?->role) abort(403);

        if (!$this->isSysAdmin($currentUser) && !$this->canManageMember($user, 'view')) {
            abort(403, "Unauthorized to view this member's history.");
        }

        $positionLogs = PositionChangeLog::forMember($memberRecord->id)
            ->with('changer')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('members.edit-history', compact('user', 'memberRecord', 'positionLogs'));
    }

    // -------------------------------------------------------------------------
    // Private Helpers
    // -------------------------------------------------------------------------

    private function handleGuestUpdate(Request $request, User $user, User $currentUser): \Illuminate\Http\RedirectResponse
    {
        if (!$this->isSysAdmin($currentUser)) {
            return back()->with('error', 'Only a System Administrator can modify the shared guest account.');
        }

        if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
            $this->deleteAvatar($user->avatar);
            $user->avatar = $this->uploadAvatar($request->file('avatar'));
            $user->save();

            AuditLogger::log('updated', $user, "Guest avatar updated", [], ['avatar' => $user->avatar]);

            return redirect()->route('members.index')->with('success', 'Guest avatar updated successfully.');
        }

        return back()->with('error', 'Only the avatar can be updated for the guest account.');
    }

    private function guardLastUserInRole(int $oldRoleId, int $newRoleId, User $currentUser): void
    {
        if ($oldRoleId === $newRoleId) return;

        $oldRole = Role::find($oldRoleId);
        if (!$oldRole) return;

        if (User::where('role_id', $oldRoleId)->count() > 1) return;

        if (!$this->isSysAdmin($currentUser)) {
            abort(403, "Only a System Administrator can change the role of the last user with the role '{$oldRole->name}'.");
        }

        if ($oldRole->id === 1) {
            session()->flash('warning', '⚠️ You are changing the role of the last System Administrator. Make sure another user has this role to avoid lockout.');
        }
    }

    private function assertRoleIsVisible(Role $role): void
    {
        if (!$role->is_visible) {
            abort(422, 'The selected role is not available.');
        }
    }

    private function assertRoleIsAllowed(Role $role, ?\Illuminate\Database\Eloquent\Collection $allowedRoles = null): void
    {
        $allowedIds = ($allowedRoles ?? $this->getAllowedRolesForCurrentUser())->pluck('id');

        if (!$allowedIds->contains($role->id)) {
            abort(403, "ACCESS DENIED: You cannot assign the role '{$role->name}'.");
        }
    }

    /**
     * Resolve a valid position for the given role.
     * Returns null when the role has no defined positions (e.g. System Administrator).
     */
    private function resolvePositionForRole(Role $role, ?string $submitted): ?string
    {
        $allowed = $this->getAllowedPositions($role);

        if (empty($allowed)) {
            return null;
        }

        if ($submitted && in_array($submitted, $allowed, true)) {
            return $submitted;
        }

        // Validate that the submitted value is present and valid
        request()->validate([
            'position' => ['required', Rule::in($allowed)],
        ], [
            'position.required' => 'Position is required for this role.',
            'position.in'       => 'Invalid position for this role. Valid options: ' . implode(', ', $allowed),
        ]);

        return $submitted;
    }

    /**
     * Returns [newPosition, positionChanged, autoChanged].
     *
     * @return array{string|null, bool, bool}
     */
    private function resolvePositionChange(User $user, Role $selectedRole, int $oldRoleId, int $newRoleId, ?string $submitted): array
    {
        $allowed     = $this->getAllowedPositions($selectedRole);
        $oldPosition = (string) ($user->position ?? '');
        $autoChanged = false;

        if (empty($allowed)) {
            return [null, $oldPosition !== '', false];
        }

        if ($oldRoleId !== $newRoleId || !in_array($oldPosition, $allowed, true)) {
            // Auto-assign first valid position when role changes or current is invalid
            return [$allowed[0], $oldPosition !== $allowed[0], true];
        }

        if ($submitted && in_array($submitted, $allowed, true)) {
            return [$submitted, $oldPosition !== $submitted, false];
        }

        return [$oldPosition, false, false];
    }

    private function buildUpdateSuccessMessage(string $fullName, bool $positionChanged, bool $autoChanged, string $oldPosition, ?string $newPosition): string
    {
        $message = "Member {$fullName} updated successfully.";

        if ($positionChanged) {
            $from     = $oldPosition !== '' ? "'{$oldPosition}'" : 'none';
            $message .= " Position changed from {$from} to '{$newPosition}'.";
            if ($autoChanged) {
                $message .= ' (auto-assigned for new role)';
            }
        }

        return $message;
    }

    private function buildFullName(array $validated): string
    {
        return preg_replace('/\s+/', ' ', trim(
            $validated['first_name'] . ' ' .
            ($validated['middle_name'] ?? '') . ' ' .
            $validated['last_name']
        ));
    }

    /**
     * Normalises a PH phone number to +63XXXXXXXXXX format.
     */
    private function normalizePhone(string $phone): string
    {
        $digits = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($digits, '63')) $digits = substr($digits, 2);
        if (str_starts_with($digits, '0'))  $digits = substr($digits, 1);
        return '+63' . substr($digits, 0, 10);
    }

    /**
     * Whether year_level should be cleared for the given role + position.
     *
     * Non-student logic is derived entirely from Member::NON_STUDENT_POSITIONS
     * and Member::VALID_POSITIONS — no hardcoded role IDs or position strings here.
     *
     * Clears year_level when:
     *   (a) the role has no positions defined at all, OR
     *   (b) every position the role allows is a non-student position, OR
     *   (c) the specific resolved position is itself a non-student position.
     */
    private function shouldClearYearLevel(int $roleId, ?string $position): bool
    {
        $positionsForRole = Member::VALID_POSITIONS[$roleId] ?? [];

        if (empty($positionsForRole)) {
            return true;
        }

        $nonStudent = Member::NON_STUDENT_POSITIONS;

        // All positions for this role are non-student
        if (array_diff($positionsForRole, $nonStudent) === []) {
            return true;
        }

        // The specific resolved position is non-student
        return $position !== null && in_array($position, $nonStudent, true);
    }

    /**
     * Validate a create or update member request.
     * Pass $userId on updates to exclude the current record from unique checks.
     */
    private function validateMemberRequest(Request $request, ?int $userId = null): array
    {
        $uniqueEmail     = $userId ? "unique:users,email,{$userId}"     : 'unique:users,email';
        $uniqueStudentId = $userId ? "unique:users,student_id,{$userId}" : 'unique:users,student_id';
        $uniquePhone     = $userId ? "unique:users,phone,{$userId}"      : 'unique:users,phone';

        $validated = $request->validate([
            'first_name'  => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name'   => ['required', 'string', 'max:255'],
            'email'       => ['required', 'email', 'ends_with:@gmail.com', $uniqueEmail, 'not_in:guest@gmail.com'],
            'student_id'  => ['nullable', 'string', $uniqueStudentId, function ($attr, $val, $fail) {
                if (!empty($val) && !preg_match('/^\d{4}-\d{5}$/', $val)) {
                    $fail('Student ID must be in format: YYYY-XXXXX');
                }
            }],
            'year_level'  => ['nullable', 'string', 'in:Grade 7,Grade 8,Grade 9,Grade 10,Grade 11,Grade 12'],
            'gender'      => ['required', 'string', 'in:Male,Female,Other'],
            'phone'       => ['nullable', 'string', 'max:20', $uniquePhone],
            'birthday'    => ['nullable', 'date'],
            'role_id'     => ['required', 'exists:roles,id'],
            'is_active'   => ['boolean'],
            'password'    => ['nullable', 'string', 'min:8', 'confirmed'],
            'avatar'      => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:2048'],
        ], [
            'email.unique'      => 'This email is already registered.',
            'student_id.unique' => 'This student ID is already assigned.',
            'phone.unique'      => 'This phone number is already in use.',
        ]);

        if (!empty($validated['phone'])) {
            $validated['phone'] = $this->normalizePhone($validated['phone']);
        }

        return $validated;
    }

    // -------------------------------------------------------------------------
    // Avatar Helpers — local disk in development, Cloudinary in production
    // -------------------------------------------------------------------------

    private function getCloudinaryInstance(): \Cloudinary\Cloudinary
    {
        return new \Cloudinary\Cloudinary(
            \Cloudinary\Configuration\Configuration::instance([
                'cloud' => [
                    'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                    'api_key'    => env('CLOUDINARY_API_KEY'),
                    'api_secret' => env('CLOUDINARY_API_SECRET'),
                ],
                'url' => ['secure' => true],
            ])
        );
    }

    private function uploadAvatar($file): string
    {
        if ($this->useCloudinary()) {
            $result = $this->getCloudinaryInstance()
                ->uploadApi()
                ->upload($file->getRealPath(), ['folder' => 'vsulhs-sslg/avatars']);

            return $result['secure_url'];
        }

        return $file->store('avatars', 'public');
    }

    private function deleteAvatar(?string $avatar): void
    {
        if (!$avatar) return;

        if (str_starts_with($avatar, 'https://res.cloudinary.com')) {
            preg_match('/\/v\d+\/(.+)\.[a-z]+$/i', parse_url($avatar, PHP_URL_PATH), $matches);
            if (!empty($matches[1])) {
                $this->getCloudinaryInstance()->uploadApi()->destroy($matches[1]);
            }
            return;
        }

        if (!str_starts_with($avatar, 'http')) {
            Storage::disk('public')->delete($avatar);
        }
    }

    private function useCloudinary(): bool
    {
        $hasCredentials = !empty(env('CLOUDINARY_CLOUD_NAME'))
                       && !empty(env('CLOUDINARY_API_KEY'))
                       && !empty(env('CLOUDINARY_API_SECRET'));

        return $hasCredentials
            && (app()->environment('production') || env('FORCE_CLOUDINARY', false));
    }
}