<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

/**
 * UserController
 * Access: Admin only — no other role can manage users.
 */
class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth.custom', 'role:Admin']);
    }

    public function index()
    {
        $users = User::with('role')->latest()->paginate(10);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email', 'unique:users,email'],
            'password'  => ['required', 'confirmed', Password::min(8)],
            'role_id'   => ['required', 'exists:roles,id'],
        ]);

        User::create([
            'full_name' => $validated['full_name'],
            'email'     => $validated['email'],
            'password'  => Hash::make($validated['password']),
            'role_id'   => $validated['role_id'],
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User created successfully.');
    }

    public function show(User $user)
    {
        $user->load('role', 'member');
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email', 'unique:users,email,' . $user->id],
            'password'  => ['nullable', 'confirmed', Password::min(8)],
            'role_id'   => ['required', 'exists:roles,id'],
        ]);

        $user->full_name = $validated['full_name'];
        $user->email     = $validated['email'];
        $user->role_id   = $validated['role_id'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully.');
    }
}
