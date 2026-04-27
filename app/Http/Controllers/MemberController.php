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
use App\Models\FinancialTransaction;
use App\Services\AuditLogger;
use Illuminate\Support\Facades\Storage;
use Cloudinary\Cloudinary;


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
        $map  = config('roles.hierarchy');

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

        // Default: only Org Member role
        return Role::where('abbreviation', 'OM')
                   ->where('is_visible', true)
                   ->get();
    }

    /**
     * Get allowed positions for a role using Member::VALID_POSITIONS as
     * the single source of truth.
     *
     * Accepts a Role model, stdClass, or array with 'id' field.
     */
    private function getAllowedPositions($role): array
    {
        // Normalize to a Role model instance
        if (is_array($role) && isset($role['id'])) {
            $role = Role::find($role['id']);
        } elseif (is_object($role) && !($role instanceof Role) && isset($role->id)) {
            $role = Role::find($role->id);
        }

        if (!($role instanceof Role)) {
            return [];
        }

        // Single source of truth: Member::VALID_POSITIONS keyed by role_id
        return Member::VALID_POSITIONS[$role->id] ?? [];
    }

    /**
     * Kept for backward compatibility — delegates to Member::VALID_POSITIONS.
     */
    private function getPositionMapping(): array
    {
        return Member::VALID_POSITIONS;
    }

    private function canManageMember(User $targetUser, string $action = 'view'): bool
    {
        $currentUser = Auth::user();

        if (in_array($currentUser->role->abbreviation, ['SysAdmin', 'SA'])) return true;

        if ($action === 'view' && $currentUser->id === $targetUser->id) return true;

        if ($currentUser->role->abbreviation === 'CA') {
            return true;
        }

        if (in_array($currentUser->role->abbreviation, ['OA', 'OO'])) {
            $forbidden = ['OA', 'OO', 'CA', 'SA', 'SysAdmin'];
            if (in_array($targetUser->role->abbreviation ?? '', $forbidden)) {
                return false;
            }
            return true;
        }

        return false;
    }

    // -------------------------------------------------------------------------
    // Index
    // -------------------------------------------------------------------------

    public function index(Request $request)
    {
        $currentUser = auth()->user();

        if ((int) $currentUser->role->level !== 1 && !$currentUser->hasPermission('members.view')) {
            abort(403, 'You are not authorized to view members.');
        }

        $query = User::with(['role:id,name,abbreviation,level']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $roleFilter = $request->input('role', 'all');
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

        $statsQuery    = clone $query;
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
        $currentUser = Auth::user();
        if (!$currentUser || !$currentUser->role) abort(403);

        if ((int) $currentUser->role->level !== 1 && !$currentUser->hasPermission('members.create')) {
            abort(403, 'You do not have permission to create members.');
        }

        $roles = $this->getAllowedRolesForCurrentUser();

        // Build positionMapping keyed by role ID using Member::VALID_POSITIONS
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

        if ((int) $currentUser->role->level !== 1 && !$currentUser->hasPermission('members.create')) {
            abort(403, 'You do not have permission to create members.');
        }

        $validated = $request->validate([
            'first_name'  => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name'   => ['required', 'string', 'max:255'],
            'email'       => ['required', 'email', 'ends_with:@gmail.com', 'unique:users,email', 'not_in:guest@gmail.com'],
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
            if (substr($phone, 0, 1) == '0')  $phone = substr($phone, 1);
            $validated['phone'] = '+63' . substr($phone, 0, 10);
        }

        $selectedRole = Role::findOrFail($validated['role_id']);

        if (!$selectedRole->is_visible) {
            return back()->withErrors(['role_id' => 'The selected role is not available.'])->withInput();
        }

        // Policy-based authorization — relies on fixed UserPolicy (int cast on level)
        $this->authorize('assignRole', [User::class, $selectedRole]);

        $allowedRoleIds = $this->getAllowedRolesForCurrentUser()->pluck('id')->toArray();
        if (!in_array($selectedRole->id, $allowedRoleIds)) {
            return redirect()->route('members.index')
                ->with('error', "ACCESS DENIED: You cannot create users with the role '{$selectedRole->name}'.");
        }

        // Resolve and validate position from Member::VALID_POSITIONS
        $allowedPositions = Member::VALID_POSITIONS[$selectedRole->id] ?? [];

        if (!empty($allowedPositions)) {
            $request->validate([
                'position' => ['required', Rule::in($allowedPositions)],
            ], [
                'position.required' => 'Position is required for this role.',
                'position.in'       => 'Invalid position for this role. Valid options: ' . implode(', ', $allowedPositions),
            ]);
            $position = $request->position;
        } else {
            // No positions defined for this role (e.g. System Administrator)
            $position = null;
        }

        if ($this->shouldClearYearLevel($selectedRole->id, $position)) {
            $validated['year_level'] = null;
        }

        DB::beginTransaction();
        try {
            $fullName       = $this->buildFullName($validated);
            $password       = $validated['password'] ?? Str::random(10);
            $hashedPassword = Hash::make($password);

            // Upload avatar — Cloudinary in production, local disk in local/dev
            $avatarUrl = null;
            if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
                $avatarUrl = $this->uploadAvatar($request->file('avatar'));
            }

            $user = User::create([
                'full_name'         => $fullName,
                'first_name'        => $validated['first_name'],
                'middle_name'       => $validated['middle_name'] ?? null,
                'last_name'         => $validated['last_name'],
                'email'             => $validated['email'],
                'password'          => $hashedPassword,
                'role_id'           => $validated['role_id'],
                'position'          => $position,
                'student_id'        => $validated['student_id'] ?? null,
                'year_level'        => $validated['year_level'] ?? null,
                'gender'            => $validated['gender'],
                'phone'             => $validated['phone'] ?? null,
                'birthday'          => $validated['birthday'] ?? null,
                'is_active'         => $validated['is_active'] ?? true,
                'avatar'            => $avatarUrl,
                // 'email_verified_at' => now(),
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
        if (!$currentUser || !$currentUser->role) abort(403);

        if ((int) $currentUser->role->level !== 1) {
            if (!$currentUser->hasPermission('members.edit') || !$this->canManageMember($user, 'edit')) {
                abort(403, 'Unauthorized to edit this member.');
            }
        }

        $roles = $this->getAllowedRolesForCurrentUser();

        $positionMapping = [];
        foreach ($roles as $role) {
            $positionMapping[$role->id] = $this->getAllowedPositions($role);
        }

        $memberRecord = $user->member;
        $positionLogs = $memberRecord
            ? PositionChangeLog::forMember($memberRecord->id)->with('changer')->orderBy('created_at', 'desc')->get()
            : collect();

        return view('members.edit', compact('user', 'roles', 'positionMapping', 'positionLogs'))
            ->with('member', $user);
    }

    public function update(Request $request, int $id)
    {
        $user         = User::findOrFail($id);
        $memberRecord = $user->member;
        $currentUser  = Auth::user();
        if (!$currentUser || !$currentUser->role) abort(403);

        // Guest account protection
        if ($user->email === 'guest@gmail.com') {
            if ((int) $currentUser->role->level !== 1) {
                return back()->with('error', 'Only a System Administrator can modify the shared guest account.');
            }

            if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
                if ($user->avatar && !str_starts_with($user->avatar, 'http')) {
                    Storage::disk('public')->delete($user->avatar);
                }
                $path         = $request->file('avatar')->store('avatars', 'public');
                $user->avatar = $path;
                $user->save();

                AuditLogger::log('updated', $user, "Guest avatar updated", [], ['avatar' => $path]);

                return redirect()->route('members.index')
                    ->with('success', 'Guest avatar updated successfully.');
            }

            return back()->with('error', 'Only the avatar can be updated for the guest account.');
        }

        if ((int) $currentUser->role->level !== 1 && (!$currentUser->hasPermission('members.edit') || !$this->canManageMember($user, 'edit'))) {
            abort(403, 'Unauthorized to edit this member.');
        }

        $oldRoleId = $user->role_id;
        $newRoleId = (int) $request->role_id;

        // Prevent removing the last user from a role
        if ($oldRoleId != $newRoleId) {
            $oldRole = Role::find($oldRoleId);
            if ($oldRole) {
                $usersWithOldRole = User::where('role_id', $oldRoleId)->count();
                if ($usersWithOldRole <= 1) {
                    if ((int) $currentUser->role->level !== 1) {
                        return back()->with('error', "Only a System Administrator can change the role of the last user with the role '{$oldRole->name}'.");
                    }
                    if ($oldRole->id == 1) {
                        session()->flash('warning', '⚠️ You are changing the role of the last System Administrator. Make sure another user has this role to avoid lockout.');
                    }
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
            'avatar'      => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:2048'],
        ], [
            'email.unique'      => 'This email is already registered.',
            'student_id.unique' => 'This student ID is already assigned.',
            'phone.unique'      => 'This phone number is already in use.',
        ]);

        if (!empty($validated['phone'])) {
            $phone = preg_replace('/[^0-9]/', '', $validated['phone']);
            if (substr($phone, 0, 2) == '63') $phone = substr($phone, 2);
            if (substr($phone, 0, 1) == '0')  $phone = substr($phone, 1);
            $validated['phone'] = '+63' . substr($phone, 0, 10);
        }

        // -----------------------------------------------------------------
        // Resolve position using Member::VALID_POSITIONS
        // -----------------------------------------------------------------
        // Cast to string so logPositionChange() never receives null.
        // A member with no previous position is treated as an empty string.
        $allowedPositions = Member::VALID_POSITIONS[$selectedRole->id] ?? [];
        $oldPosition      = (string) ($user->position ?? '');
        $newPosition      = $oldPosition;
        $autoChanged      = false;

        if (!empty($allowedPositions)) {
            if ($oldRoleId != $newRoleId || !in_array($oldPosition, $allowedPositions)) {
                // Auto-assign first valid position when role changes or current position is invalid
                $newPosition = $allowedPositions[0];
                $autoChanged = true;
            } else {
                // Keep submitted position if it is valid for the role
                $submittedPosition = $request->input('position');
                if ($submittedPosition && in_array($submittedPosition, $allowedPositions)) {
                    $newPosition = $submittedPosition;
                }
            }
        } else {
            // No positions for this role (e.g. System Administrator)
            $newPosition = null;
        }

        $positionChanged = ($oldPosition != $newPosition);
        $manuallyChanged = $request->boolean('position_manually_changed');

        if ($positionChanged && $manuallyChanged) {
            $request->validate([
                'position_change_reason' => ['required', 'string', 'max:1000'],
            ], [
                'position_change_reason.required' => 'A reason is required when changing position.',
            ]);
        }

        if ($this->shouldClearYearLevel($selectedRole->id, $newPosition)) {
            $validated['year_level'] = null;
        }

        $allowedRoleIds = $this->getAllowedRolesForCurrentUser()->pluck('id')->toArray();
        if (!in_array($selectedRole->id, $allowedRoleIds)) {
            return redirect()->route('members.index')
                ->with('error', "ACCESS DENIED: You cannot assign the role '{$selectedRole->name}'.");
        }

        DB::beginTransaction();
        try {
            $fullName = $this->buildFullName($validated);
            $oldData  = $user->getOriginal();

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
                // Delete old avatar before replacing
                $this->deleteAvatar($user->avatar);

                // Upload new avatar — Cloudinary in production, local disk in local/dev
                $user->avatar = $this->uploadAvatar($request->file('avatar'));
            }

            $user->save();

            if ($memberRecord) {
                $memberRecord->position = $newPosition;
                $memberRecord->save();

                if ($positionChanged) {
                    $reason = $autoChanged
                        ? "Auto-updated due to role change from role ID {$oldRoleId} to {$newRoleId}"
                        : ($request->position_change_reason ?? null);
                    $this->logPositionChange($memberRecord, $oldPosition, $newPosition, $reason);
                }
            }

            DB::commit();

            AuditLogger::log('updated', $user, "Member: {$user->full_name}", $oldData, $user->getChanges());

            $message = "Member {$fullName} updated successfully.";
            if ($positionChanged) {
                $from     = $oldPosition !== '' ? "'{$oldPosition}'" : 'none';
                $message .= " Position changed from {$from} to '{$newPosition}'.";
                if ($autoChanged) $message .= " (auto-assigned for new role)";
            }

            return redirect()->route('members.index')->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('MemberController@update error: ' . $e->getMessage());
            return back()->with('error', 'Failed to update member: ' . $e->getMessage())->withInput();
        }
    }

    // -------------------------------------------------------------------------
    // Show
    // -------------------------------------------------------------------------

    public function show(int $id)
    {
        $user        = User::with('role', 'member')->findOrFail($id);
        $currentUser = Auth::user();

        if ((int) $currentUser->role->level !== 1 && !$currentUser->hasPermission('members.view')) {
            abort(403);
        }

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
        $targetUser  = User::findOrFail($id);
        $currentUser = Auth::user();

        if ($targetUser->email === 'guest@gmail.com') {
            return back()->with('error', 'The shared guest account cannot be deleted.');
        }

        if ((int) $currentUser->role->level !== 1 && !$currentUser->hasPermission('members.delete')) {
            abort(403);
        }

        if ($targetUser->role->level >= $currentUser->role->level && (int) $currentUser->role->level !== 1) {
            return back()->with('error', 'You cannot delete a user with a role level equal or higher than yours.');
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

            AuditLogger::log('deleted', $targetUser, "Member: {$targetUser->full_name}", $targetUser->toArray(), []);

            $targetUser->member?->delete();
            $targetUser->delete();

            return redirect()->route('members.index')
                ->with('success', "{$userName} ({$userRole}) has been removed from the system.");
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

        if ($user->id === auth()->id()) {
            return response()->json(['error' => 'You cannot deactivate your own account.'], 403);
        }

        if ((int) $currentUser->role->level !== 1 && !$this->canManageMember($user, 'edit')) {
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

        if ((int) $currentUser->role->level !== 1 && !$this->canManageMember($user, 'edit')) {
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
            $memberRecord = $user->member;
            $currentUser  = Auth::user();

            if (!$currentUser || !$currentUser->role) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            if ((int) $currentUser->role->level !== 1 && !$this->canManageMember($user, 'view')) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            if (!$memberRecord) return response()->json([]);

            $logs = PositionChangeLog::forMember($memberRecord->id)
                ->with('changer')
                ->orderBy('created_at', 'desc')
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
            \Log::error('MemberController@getPositionHistoryData: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load position change log.'], 500);
        }
    }

    // -------------------------------------------------------------------------
    // Edit History Page
    // -------------------------------------------------------------------------

    public function editHistory(int $id)
    {
        $user         = User::with('role', 'member')->findOrFail($id);
        $memberRecord = $user->member;
        if (!$memberRecord) abort(404, 'Member record not found.');

        $currentUser = Auth::user();
        if (!$currentUser || !$currentUser->role) abort(403);

        if ((int) $currentUser->role->level !== 1 && !$this->canManageMember($user, 'view')) {
            abort(403, 'Unauthorized to view this member\'s history.');
        }

        $positionLogs = PositionChangeLog::forMember($memberRecord->id)
            ->with('changer')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

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

    /**
     * Determine whether year_level should be cleared for the given role/position.
     *
     * Non-student roles are derived from Member::VALID_POSITIONS:
     *   - Roles with an empty positions array (e.g. role_id 1 = System Administrator)
     *   - Roles whose positions include non-student titles (Club Adviser, Guest)
     *
     * If you add new non-student roles, update Member::VALID_POSITIONS and they
     * will automatically be excluded here — no need to touch this method.
     */
    private function shouldClearYearLevel($roleId, $position): bool
    {
        $nonStudentPositions = ['System Administrator', 'Club Adviser', 'Guest'];

        $positionsForRole = Member::VALID_POSITIONS[$roleId] ?? [];

        // Roles with no positions at all (e.g. System Administrator role_id=1)
        if (empty($positionsForRole)) {
            return true;
        }

        // Roles whose entire position list is non-student titles
        $allNonStudent = count(array_filter(
            $positionsForRole,
            fn($p) => in_array($p, $nonStudentPositions)
        )) === count($positionsForRole);

        if ($allNonStudent) {
            return true;
        }

        // Specific position override (e.g. a mixed role where only some positions are non-student)
        if ($position && in_array($position, $nonStudentPositions)) {
            return true;
        }

        return false;
    }

    // -------------------------------------------------------------------------
    // Avatar Helpers — local disk in development, Cloudinary in production
    // -------------------------------------------------------------------------

    /**
     * Upload an avatar file.
     * Uses Cloudinary when CLOUDINARY_URL is set and APP_ENV is production.
     * Falls back to local public disk otherwise.
     */
    private function uploadAvatar($file): string
    {
        if ($this->useCloudinary()) {
            $cloudinary = new Cloudinary(env('CLOUDINARY_URL'));
            $result     = $cloudinary->uploadApi()->upload(
                $file->getRealPath(),
                ['folder' => 'vsulhs-sslg/avatars']
            );
            return $result['secure_url'];
        }

        // Local storage (development)
        return $file->store('avatars', 'public');
    }

    /**
     * Delete an existing avatar.
     * Handles both Cloudinary URLs and local file paths.
     */
    private function deleteAvatar(?string $avatar): void
    {
        if (!$avatar) return;

        if (str_starts_with($avatar, 'https://res.cloudinary.com')) {
            // Extract public_id from Cloudinary URL and destroy
            preg_match('/\/v\d+\/(.+)\.[a-z]+$/i', parse_url($avatar, PHP_URL_PATH), $matches);
            if (!empty($matches[1])) {
                $cloudinary = new Cloudinary(env('CLOUDINARY_URL'));
                $cloudinary->uploadApi()->destroy($matches[1]);
            }
            return;
        }

        // Local file
        if (!str_starts_with($avatar, 'http')) {
            Storage::disk('public')->delete($avatar);
        }
    }

    /**
     * Determine whether to use Cloudinary based on environment.
     * Uses Cloudinary when:
     *   - APP_ENV is production, OR
     *   - CLOUDINARY_URL is explicitly set and FORCE_CLOUDINARY=true
     */
    private function useCloudinary(): bool
    {
        $hasCredentials = !empty(env('CLOUDINARY_URL'));

        if (!$hasCredentials) return false;

        // Always use Cloudinary in production
        if (app()->environment('production')) return true;

        // Optionally force Cloudinary in local for testing
        if (env('FORCE_CLOUDINARY', false)) return true;

        return false;
    }

}