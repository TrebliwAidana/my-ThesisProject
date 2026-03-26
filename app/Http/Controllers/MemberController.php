<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\User;
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
        $this->middleware('role:Admin,Officer')
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
        $users = User::whereDoesntHave('member')->get();
        return view('members.create', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'    => ['required', 'exists:users,id', 'unique:members,user_id'],
            'position'   => ['required', 'string', 'max:150'],
            'term_start' => ['required', 'date'],
            'term_end'   => ['nullable', 'date', 'after_or_equal:term_start'],
        ]);

        Member::create($validated);

        return redirect()->route('members.index')
            ->with('success', 'Member added successfully.');
    }

    public function show(Member $member)
    {
        $this->authorizeMemberAccess($member);

        $member->load('user', 'budgets');
        return view('members.show', compact('member'));
    }

    public function edit(Member $member)
    {
        $users = User::all();
        return view('members.edit', compact('member', 'users'));
    }

    public function update(Request $request, Member $member)
    {
        $validated = $request->validate([
            'user_id'    => ['required', 'exists:users,id', 'unique:members,user_id,' . $member->id],
            'position'   => ['required', 'string', 'max:150'],
            'term_start' => ['required', 'date'],
            'term_end'   => ['nullable', 'date', 'after_or_equal:term_start'],
        ]);

        $member->update($validated);

        return redirect()->route('members.index')
            ->with('success', 'Member updated successfully.');
    }

    public function destroy(Member $member)
    {
        $member->delete();
        return redirect()->route('members.index')
            ->with('success', 'Member removed successfully.');
    }

    /**
     * Members can only view their own record.
     * Admins, Officers, and Auditors can view any record.
     */
    private function authorizeMemberAccess(Member $member): void
    {
        $user = Auth::user();

        if ($user->role->name === 'Member' && $member->user_id !== $user->id) {
            abort(403, 'You can only view your own member record.');
        }
    }
}
