<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\User;
use App\Helpers\UserHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * MemberController
 * - Admin, Officer : full CRUD
 * - Auditor        : index + show only
 * - Member         : show own record only
 */
class MemberController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.custom');

        // Only Admin and Officer can create, edit, or delete
        $this->middleware('role:Adviser,Officer')
             ->only(['create', 'store', 'edit', 'update', 'destroy']);
    }

    public function index()
    {
        $user = Auth::user();

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
        return view('members.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'term_start' => 'required|date',
            'term_end' => 'nullable|date|after_or_equal:term_start',
        ]);
        
        // Generate email for member
        $email = UserHelper::generateUniqueMemberEmail($validated['full_name']);
        
        // Create user with member role (role_id = 4)
        $user = User::create([
            'full_name' => $validated['full_name'],
            'email' => $email,
            'password' => bcrypt('password'), // Default password
            'role_id' => 4, // Member role
            'position' => $validated['position'],
        ]);
        
        // Create member record
        $member = Member::create([
            'user_id' => $user->id,
            'position' => $validated['position'],
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

    public function edit(Member $member)
    {
        return view('members.edit', compact('member'));
    }

    public function update(Request $request, Member $member)
    {
        $validated = $request->validate([
            'position' => ['required', 'string', 'max:150'],
            'term_start' => ['required', 'date'],
            'term_end' => ['nullable', 'date', 'after_or_equal:term_start'],
        ]);

        $member->update($validated);

        return redirect()->route('members.index')
            ->with('success', 'Member updated successfully.');
    }

    public function destroy(Member $member)
    {
        // Also delete the associated user
        $user = $member->user;
        $member->delete();
        
        if ($user && $user->role_id == 4) { // Only delete if it's a member role
            $user->delete();
        }
        
        return redirect()->route('members.index')
            ->with('success', 'Member removed successfully.');
    }

    /**
     * Members can only view their own record.
     * Advisers, Officers, and Auditors can view any record.
     */
    private function authorizeMemberAccess(Member $member): void
    {
        $user = Auth::user();

        if ($user->role->name === 'Member' && $member->user_id !== $user->id) {
            abort(403, 'You can only view your own member record.');
        }
    }
}