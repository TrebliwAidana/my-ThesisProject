<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Document;
use App\Models\FinancialTransaction;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.custom');
    }

    public function index()
    {
        
            if (auth()->user()->email === 'guest@gmail.com') {
        abort(403, 'Guest accounts cannot access profile settings.');
    }
        $user = Auth::user()->load('role');

        $documentsCount = Document::where('owner_id', $user->id)->count();
        $transactionsCount = FinancialTransaction::where('user_id', $user->id)->count();

        return view('profile.index', compact('user', 'documentsCount', 'transactionsCount'));
    }

    public function updateProfile(Request $request)
    {
         if (auth()->user()->email === 'guest@gmail.com') {
            abort(403, 'Guest accounts cannot be modified.');
        }
            $user = Auth::user();

        $validated = $request->validate([
            'full_name'  => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email,' . $user->id,
            'first_name' => 'nullable|string|max:255',
            'last_name'  => 'nullable|string|max:255',
            'middle_name'=> 'nullable|string|max:255',
            'student_id' => 'nullable|string|max:50|unique:users,student_id,' . $user->id,
            'year_level' => 'nullable|string|max:50',
            'gender'     => 'nullable|string|in:Male,Female,Other',
            'phone'      => 'nullable|string|max:20|unique:users,phone,' . $user->id,
            'birthday'   => 'nullable|date',
            'avatar'     => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
        ]);

        // Handle avatar upload
        if ($request->hasFile('avatar') && $request->file('avatar')->isValid()) {
            // Delete old avatar if it exists and isn't the default
            if ($user->avatar && !str_starts_with($user->avatar, 'http')) {
                Storage::disk('public')->delete($user->avatar);
            }
            $path = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        // Year level clearing based on role and position
        if ($this->shouldClearYearLevel($user->role_id, $user->position)) {
            $validated['year_level'] = null;
        }

        $user->full_name = $validated['full_name'];
        $user->email     = $validated['email'];

        if (isset($validated['first_name']))  $user->first_name  = $validated['first_name'];
        if (isset($validated['last_name']))   $user->last_name   = $validated['last_name'];
        if (isset($validated['middle_name'])) $user->middle_name = $validated['middle_name'];
        if (isset($validated['student_id']))  $user->student_id  = $validated['student_id'];
        if (isset($validated['year_level']))  $user->year_level  = $validated['year_level'];
        if (isset($validated['gender']))      $user->gender      = $validated['gender'];
        if (isset($validated['birthday']))    $user->birthday    = $validated['birthday'];

        if (isset($validated['phone'])) {
            $phone = preg_replace('/[^0-9]/', '', $validated['phone']);
            if (str_starts_with($phone, '63')) $phone = substr($phone, 2);
            if (str_starts_with($phone, '0'))  $phone = substr($phone, 1);
            $user->phone = '+63' . substr($phone, 0, 10);
        }

        $user->save();

        return redirect()->route('profile.index')
            ->with('success', '✅ Profile updated successfully!');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'current_password' => 'required',
            'new_password'     => 'required|min:8|confirmed',
        ], [
            'current_password.required' => 'Current password is required.',
            'new_password.required'     => 'New password is required.',
            'new_password.min'          => 'New password must be at least 8 characters.',
            'new_password.confirmed'    => 'New password confirmation does not match.',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.'])->withInput();
        }

        if (Hash::check($request->new_password, $user->password)) {
            return back()
                ->withErrors(['new_password' => 'New password cannot be the same as your current password.'])
                ->withInput()
                ->with('password_error', 'same_password');
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->route('profile.index')
            ->with('password_success', '🔒 Your password has been changed successfully!');
    }

    public function updateTheme(Request $request)
    {
         if (auth()->user()->email === 'guest@gmail.com') {
            abort(403, 'Guest accounts cannot be modified.');
        }
        $user = Auth::user();

        $validated = $request->validate([
            'theme' => 'required|in:light,dark',
        ]);

        $user->theme = $validated['theme'];
        $user->save();

        return response()->json(['success' => true]);
    }

    public function getProfileStats()
    {
        $user = Auth::user();

        $stats = [
            'member_since'       => $user->created_at?->format('F d, Y'),
            'last_login'         => $user->last_login_at?->format('F d, Y H:i'),
            'documents_uploaded' => $user->documents()->count(),
            'role_name'          => $user->role->name ?? 'N/A',
            'role_abbreviation'  => $user->role->abbreviation ?? 'N/A',
            'is_verified'        => $user->hasVerifiedEmail(),
        ];

        return response()->json($stats);
    }

    // -------------------------------------------------------------------------
    // Helper
    // -------------------------------------------------------------------------

    private function shouldClearYearLevel($roleId, $position)
    {
        $alwaysNonStudent = [1, 6, 8];
        if (in_array($roleId, $alwaysNonStudent)) return true;
        if ($roleId == 2 && $position !== 'SSLG President') return true;
        return false;
    }
}