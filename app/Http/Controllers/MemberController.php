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
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;

class MemberController extends Controller
{
    use LogsMemberEdits;

    public function __construct()
    {
        $this->middleware('auth.custom');
    }

    // Permission helpers (same as before, unchanged)
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
            return Role::all();
        }

        if (isset($map[$user->role->abbreviation])) {
            $allowed = $map[$user->role->abbreviation];
            if (in_array('all', $allowed)) {
                return Role::all();
            }
            return Role::whereIn('abbreviation', $allowed)->get();
        }

        return Role::where('abbreviation', 'OM')->get();
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

        return $byName[$roleName] ?? ['Regular Member'];
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

        if (in_array($current->role->abbreviation, ['SysAdmin', 'SA'])) {
            return true;
        }

        if ($action === 'view' && $current->id === $target->id) {
            return true;
        }

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

    // Index with server-side filtering (as before)
    public function index(Request $request)
    {
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

        $users = $query->paginate(15)->appends($request->except('page'));

        $totalStats = [
            'system_admin' => User::whereHas('role', fn($q) => $q->where('name', 'System Administrator'))->count(),
            'supreme'      => User::whereHas('role', fn($q) => $q->where('level', '<=', 3))->count(),
            'leaders'      => User::whereHas('role', fn($q) => $q->whereIn('name', ['Org Admin', 'Org Officer']))->count(),
            'members'      => User::whereHas('role', fn($q) => $q->where('name', 'Org Member'))->count(),
            'advisers'     => User::whereHas('role', fn($q) => $q->where('name', 'Club Adviser'))->count(),
            'all'          => User::count(),
        ];

        $roleColors = [
            'System Administrator' => 'purple',
            'Supreme Admin'        => 'indigo',
            'Supreme Officer'      => 'blue',
            'Org Admin'            => 'emerald',
            'Org Officer'          => 'sky',
            'Club Adviser'         => 'amber',
            'Org Member'           => 'gray',
        ];

        return view('members.index', compact('users', 'totalStats', 'roleColors', 'roleFilter'));
    }

    // Create / Store
    public function create()
    {
        $user = Auth::user();

        if (! $user || ! $user->role) {
            abort(403, 'Unauthorized.');
        }

        if (! in_array($user->role->abbreviation, ['SysAdmin', 'SA', 'CA', 'OA', 'OO'])) {
            abort(403, 'You do not have permission to create members.');
        }

        $roles = $this->getAllowedRolesForCurrentUser();

        return view('members.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $currentUser = Auth::user();

        if (! $currentUser || ! $currentUser->role) {
            abort(403, 'Unauthorized.');
        }

        if (! in_array($currentUser->role->abbreviation, ['SysAdmin', 'SA', 'CA', 'OA', 'OO'])) {
            abort(403, 'You do not have permission to create members.');
        }

        $validated = $request->validate([
            'first_name'  => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name'   => ['required', 'string', 'max:255'],
            'email'       => ['required', 'email', 'ends_with:@gmail.com', 'unique:users,email'],
            'student_id'  => [
                'nullable', 'string', 'unique:users,student_id',
                fn($attr, $val, $fail) => (!empty($val) && ! preg_match('/^\d{4}-\d{5}$/', $val))
                    ? $fail('Student ID must be in format: YYYY-XXXXX') : null,
            ],
            'year_level'  => ['nullable', 'string', 'in:Grade 7,Grade 8,Grade 9,Grade 10,Grade 11,Grade 12'],
            'gender'      => ['required', 'string', 'in:Male,Female,Other'],
            'phone'       => ['nullable', 'string', 'max:20', 'unique:users,phone'],
            'birthday'    => ['nullable', 'date'],
            'role_id'     => ['required', 'exists:roles,id'],
            'position'    => ['nullable', 'string', 'max:255'],
            'is_active'   => ['boolean'],
            'password'    => ['nullable', 'string', 'min:8', 'confirmed'],
        ], [
            'email.unique' => 'This email is already registered.',
            'student_id.unique' => 'This student ID is already assigned.',
            'phone.unique' => 'This phone number is already in use.',
        ]);

        // Format phone
        if (!empty($validated['phone'])) {
            $phone = preg_replace('/[^0-9]/', '', $validated['phone']);
            if (substr($phone, 0, 2) == '63') $phone = substr($phone, 2);
            if (substr($phone, 0, 1) == '0') $phone = substr($phone, 1);
            $validated['phone'] = '+63' . substr($phone, 0, 10);
        }

        $selectedRole = Role::findOrFail($validated['role_id']);
        $allowedRoleIds = $this->getAllowedRolesForCurrentUser()->pluck('id')->toArray();

        if (! in_array($selectedRole->id, $allowedRoleIds)) {
            return redirect()->route('members.index')
                ->with('error', "❌ ACCESS DENIED: You cannot create users with the role '{$selectedRole->name}'.");
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

        DB::beginTransaction();

        try {
            $fullName = $this->buildFullName($validated);

            if ($selectedRole->id == 1) {
                $validated['position'] = null;
            }

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
                'position'        => $validated['position'],
                'student_id'      => $validated['student_id'] ?? null,
                'year_level'      => $validated['year_level'] ?? null,
                'gender'          => $validated['gender'],
                'phone'           => $validated['phone'] ?? null,
                'birthday'        => $validated['birthday'] ?? null,
                'is_active'       => $validated['is_active'] ?? true,
                'organization_id' => $currentUser->organization_id ?? null,
            ]);

            Member::create([
                'user_id'    => $user->id,
                'position'   => $validated['position'],
                'joined_at'  => now(),
                'term_start' => now(),
            ]);

            DB::commit();

            $user->sendEmailVerificationNotification();
            $user->notify(new \App\Notifications\NewUserWelcomeNotification($password));

            return redirect()->route('members.index')
                ->with('success', "✅ Member {$fullName} created successfully! A welcome email has been sent.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', '❌ Failed to create member: ' . $e->getMessage())->withInput();
        }
    }

    // Edit / Update
    public function edit(int $id)
    {
        $member      = User::findOrFail($id);
        $currentUser = Auth::user();

        if (! $currentUser || ! $currentUser->role) {
            abort(403, 'Unauthorized.');
        }

        if (! $this->canManageMember($member, 'edit')) {
            abort(403, 'Unauthorized to edit this member.');
        }

        $roles           = $this->getAllowedRolesForCurrentUser()->load('positions');
        $positionMapping = $this->getPositionMapping();

        $memberRecord = $member->member;
        $positionLogs = $memberRecord
            ? PositionChangeLog::forMember($memberRecord->id)
                ->with('changer')
                ->orderBy('created_at', 'desc')
                ->get()
            : collect();

        return view('members.edit', compact('member', 'roles', 'positionMapping', 'positionLogs'));
    }

    public function update(Request $request, int $id)
    {
        $user         = User::findOrFail($id);
        $memberRecord = $user->member;
        $currentUser  = Auth::user();

        if (! $currentUser || ! $currentUser->role) {
            abort(403, 'Unauthorized.');
        }

        if (! $this->canManageMember($user, 'edit')) {
            abort(403, 'Unauthorized to edit this member.');
        }

        $oldRoleId = $user->role_id;
        $newRoleId = $request->role_id;

        // Protect predefined roles: cannot change role of last user
        if ($oldRoleId != $newRoleId) {
            $oldRole = Role::find($oldRoleId);
            if ($oldRole && $oldRole->is_predefined) {
                $count = User::where('role_id', $oldRoleId)->count();
                if ($count <= 1) {
                    return back()->with('error', "⚠️ Cannot change role of the last user with the role '{$oldRole->name}'. This role is required for system functionality.");
                }
            }
        }

        $selectedRole = Role::findOrFail($newRoleId);
        $allowedPositions = $this->getAllowedPositions($selectedRole->name, $selectedRole->abbreviation);

        $validated = $request->validate([
            'first_name'             => ['required', 'string', 'max:255'],
            'middle_name'            => ['nullable', 'string', 'max:255'],
            'last_name'              => ['required', 'string', 'max:255'],
            'email'                  => ['required', 'email', 'ends_with:@gmail.com', 'unique:users,email,' . $user->id],
            'student_id'             => [
                'nullable', 'string', 'unique:users,student_id,' . $user->id,
                fn($attr, $val, $fail) => (!empty($val) && ! preg_match('/^\d{4}-\d{5}$/', $val))
                    ? $fail('Student ID must be in format: YYYY-XXXXX') : null,
            ],
            'year_level'             => ['nullable', 'string', 'in:Grade 7,Grade 8,Grade 9,Grade 10,Grade 11,Grade 12'],
            'gender'                 => ['required', 'string', 'in:Male,Female,Other'],
            'phone'                  => ['nullable', 'string', 'max:20', 'unique:users,phone,' . $user->id],
            'birthday'               => ['nullable', 'date'],
            'role_id'                => ['required', 'exists:roles,id'],
            'position'               => ['required', Rule::in($allowedPositions)],
            'is_active'              => ['boolean'],
            'password'               => ['nullable', 'string', 'min:8', 'confirmed'],
            'position_change_reason' => [
                'nullable', 'string',
                function ($attr, $val, $fail) use ($request, $user) {
                    if (trim($request->position ?? '') !== trim($user->position) && empty($val)) {
                        $fail('A reason is required when changing position.');
                    }
                },
            ],
        ], [
            'email.unique' => 'This email is already registered.',
            'student_id.unique' => 'This student ID is already assigned.',
            'phone.unique' => 'This phone number is already in use.',
        ]);

        // Format phone
        if (!empty($validated['phone'])) {
            $phone = preg_replace('/[^0-9]/', '', $validated['phone']);
            if (substr($phone, 0, 2) == '63') $phone = substr($phone, 2);
            if (substr($phone, 0, 1) == '0') $phone = substr($phone, 1);
            $validated['phone'] = '+63' . substr($phone, 0, 10);
        }

        $allowedRoleIds = $this->getAllowedRolesForCurrentUser()->pluck('id')->toArray();
        if (! in_array($selectedRole->id, $allowedRoleIds)) {
            return redirect()->route('members.index')
                ->with('error', "❌ ACCESS DENIED: You cannot assign the role '{$selectedRole->name}'.");
        }

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

        DB::beginTransaction();

        try {
            $oldPosition = $user->position;
            $newPosition = $validated['position'];
            if ($selectedRole->id == 1) {
                $newPosition = null;
            }
            $fullName    = $this->buildFullName($validated);

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
            }

            if ($memberRecord) {
                $this->logPositionChange(
                    $memberRecord,
                    $oldPosition,
                    $newPosition,
                    $validated['position_change_reason'] ?? null
                );
            }

            DB::commit();

            $message = "✅ Member {$fullName} updated successfully.";
            if (trim($oldPosition) !== trim($newPosition)) {
                $message .= " Position changed from '{$oldPosition}' to '{$newPosition}'.";
            }

            return redirect()->route('members.index')->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('MemberController@update error: ' . $e->getMessage());
            return back()->with('error', '❌ Failed to update member: ' . $e->getMessage())->withInput();
        }
    }

    // Show, Destroy, Activate/Deactivate, Logs, etc. remain unchanged (not shown for brevity, but keep them)

    private function buildFullName(array $validated): string
    {
        return preg_replace('/\s+/', ' ', trim(
            $validated['first_name'] . ' ' .
            ($validated['middle_name'] ?? '') . ' ' .
            $validated['last_name']
        ));
    }

    // The rest of the methods (show, destroy, activate, deactivate, getPositionHistoryData, editHistory) are unchanged.
    // They can be copied from your existing MemberController.
}