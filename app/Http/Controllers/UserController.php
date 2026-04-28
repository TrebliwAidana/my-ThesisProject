<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

/**
 * UserController
 * Access: System Administrator, Supreme Admin, and Adviser can manage users
 */
class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth.custom']);
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            $allowedRoles = ['System Administrator', 'Supreme Admin', 'Adviser'];
            $allowedAbbreviations = ['SysAdmin', 'SA', 'AD'];

            if (! $user || ! $user->role) {
                abort(403, 'Unauthorized.');
            }

            if (! in_array($user->role->name, $allowedRoles) && ! in_array($user->role->abbreviation, $allowedAbbreviations)) {
                abort(403, 'Unauthorized. Only System Administrator and Adviser can manage users.');
            }

            return $next($request);
        });
    }

    public function index()
    {
        $user = auth()->user();

        if ($user->role->name === 'Adviser' || $user->role->abbreviation === 'AD') {
            $users = User::with('role')
                ->whereHas('role', function ($query) {
                    $query->whereIn('name', ['Org Admin', 'Org Officer', 'Org Member', 'Guest']);
                })
                ->latest()
                ->paginate(10);
        } else {
            $users = User::with('role')->latest()->paginate(10);
        }

        return view('users.index', compact('users'));
    }

    public function create()
    {
        $user = auth()->user();
        // OPTIMIZED: cache all roles
        $roles = $this->getAllRoles();

        if ($user->role->name === 'Adviser' || $user->role->abbreviation === 'AD') {
            // OPTIMIZED: cache adviser-allowable roles
            $roles = $this->getAdviserAllowableRoles();
        }

        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $currentUser = auth()->user();

        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email', 'not_in:guest@gmail.com'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'role_id' => ['required', 'exists:roles,id'],
        ]);

        $selectedRole = Role::find($validated['role_id']);

        if ($currentUser->role->name === 'Adviser' || $currentUser->role->abbreviation === 'AD') {
            $allowedRoles = ['Org Admin', 'Org Officer', 'Org Member', 'Guest'];
            if (! in_array($selectedRole->name, $allowedRoles)) {
                return redirect()->route('users.index')
                    ->with('error', '❌ ACCESS DENIED: Advisers can only create Org Admin, Org Officer, Org Member, and Guest accounts.');
            }
        }

        User::create([
            'full_name' => $validated['full_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => $validated['role_id'],
            'email_verified_at' => now(),
        ]);

        return redirect()->route('users.index')
            ->with('success', '✅ User created successfully.');
    }

    public function show(User $user)
    {
        $currentUser = auth()->user();

        if ($currentUser->role->name === 'Adviser' || $currentUser->role->abbreviation === 'AD') {
            $allowedRoles = ['Org Admin', 'Org Officer', 'Org Member', 'Guest'];
            if (! in_array($user->role->name, $allowedRoles)) {
                abort(403, 'Unauthorized to view this user.');
            }
        }

        // OPTIMIZED: Remove unnecessary reload - use eager loading in index/beforehand instead
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $currentUser = auth()->user();

        if ($currentUser->role->name === 'Adviser' || $currentUser->role->abbreviation === 'AD') {
            $allowedRoles = ['Org Admin', 'Org Officer', 'Org Member', 'Guest'];
            if (! in_array($user->role->name, $allowedRoles)) {
                abort(403, 'Unauthorized to edit this user.');
            }
        }

        // OPTIMIZED: cache all roles
        $roles = $this->getAllRoles();

        if ($currentUser->role->name === 'Adviser' || $currentUser->role->abbreviation === 'AD') {
            // OPTIMIZED: cache adviser-allowable roles
            $roles = $this->getAdviserAllowableRoles();
        }

        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $currentUser = auth()->user();

        // CRITICAL: Early guard - prevent modification of guest account
        if ($user->email === 'guest@gmail.com') {
            return back()->with('error', 'The shared guest account cannot be modified.');
        }

        // Early guard - prevent admin who is guest from making modifications
        if ($currentUser->email === 'guest@gmail.com') {
            abort(403, 'Guest account cannot make modifications.');
        }

        // Check adviser permissions only for advisers
        if ($currentUser->role->name === 'Adviser' || $currentUser->role->abbreviation === 'AD') {
            $allowedRoles = ['Org Admin', 'Org Officer', 'Org Member', 'Guest'];
            if (! in_array($user->role->name, $allowedRoles)) {
                abort(403, 'Unauthorized to update this user.');
            }
        }

        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,'.$user->id],
            'password' => ['nullable', 'confirmed', Password::min(8)],
            'role_id' => ['required', 'exists:roles,id'],
        ]);

        $selectedRole = Role::find($validated['role_id']);

        if ($currentUser->role->name === 'Adviser' || $currentUser->role->abbreviation === 'AD') {
            $allowedRoles = ['Org Admin', 'Org Officer', 'Org Member', 'Guest'];
            if (! in_array($selectedRole->name, $allowedRoles)) {
                return redirect()->route('users.index')
                    ->with('error', '❌ ACCESS DENIED: Advisers can only assign Org Admin, Org Officer, Org Member, and Guest roles.');
            }
        }

        $user->full_name = $validated['full_name'];
        $user->email = $validated['email'];
        $user->role_id = $validated['role_id'];

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('users.index')
            ->with('success', '✅ User updated successfully.');
    }

    public function destroy(User $user)
    {
        $currentUser = auth()->user();

        // CRITICAL: Prevent deletion of guest account (early guard)
        if ($user->email === 'guest@gmail.com') {
            return back()->with('error', 'The shared guest account cannot be deleted.');
        }

        // OPTIMIZED: Check if current user is an adviser with limited delete scope
        if ($currentUser->role->name === 'Adviser' || $currentUser->role->abbreviation === 'AD') {
            $allowedRoles = ['Org Admin', 'Org Officer', 'Org Member', 'Guest'];
            if (! in_array($user->role->name, $allowedRoles)) {
                return redirect()->route('users.index')
                    ->with('error', '❌ ACCESS DENIED: Advisers can only delete Org Admin, Org Officer, Org Member, and Guest accounts.');
            }
        }

        // Check if this is the last adviser before allowing deletion
        if ($user->role && $user->role->name === 'Adviser') {
            $otherAdvisers = User::whereHas('role', fn ($q) => $q->where('name', 'Adviser'))
                ->where('id', '!=', $user->id)
                ->exists();

            if (! $otherAdvisers) {
                return back()->with('error', '⚠️ Cannot delete the last adviser in the system.');
            }
        }

        $userName = $user->full_name;
        $userRole = $user->role->name ?? 'Unknown';
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', "✅ {$userName} ({$userRole}) has been deleted successfully.");
    }

    // =========================================================================
    // Private Helpers
    // =========================================================================

    /**
     * Get cached adviser-allowable roles for Adviser users.
     * Cache for 1 hour since roles rarely change.
     */
    private function getAdviserAllowableRoles()
    {
        return Cache::remember('adviser_allowable_roles', 3600, fn () => Role::whereIn('name', ['Org Admin', 'Org Officer', 'Org Member', 'Guest'])->get());
    }

    /**
     * Get all roles from cache.
     * Cache for 1 hour since roles rarely change.
     */
    private function getAllRoles()
    {
        return Cache::remember('all_roles', 3600, fn () => Role::all());
    }
}
