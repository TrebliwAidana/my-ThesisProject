<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.custom');
    }

    public function index()
    {
        $user = Auth::user()->load('role');
        return view('profile.index', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'first_name' => 'nullable|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'student_id' => 'nullable|string|max:50|unique:users,student_id,' . $user->id,
            'year_level' => 'nullable|string|max:50',
        ]);

        $user->full_name = $validated['full_name'];
        $user->email = $validated['email'];
        
        if (isset($validated['first_name'])) {
            $user->first_name = $validated['first_name'];
        }
        if (isset($validated['last_name'])) {
            $user->last_name = $validated['last_name'];
        }
        if (isset($validated['middle_name'])) {
            $user->middle_name = $validated['middle_name'];
        }
        if (isset($validated['student_id'])) {
            $user->student_id = $validated['student_id'];
        }
        if (isset($validated['year_level'])) {
            $user->year_level = $validated['year_level'];
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
            'new_password' => 'required|min:8|confirmed',
        ], [
            'current_password.required' => 'Current password is required.',
            'new_password.required' => 'New password is required.',
            'new_password.min' => 'New password must be at least 8 characters.',
            'new_password.confirmed' => 'New password confirmation does not match.',
        ]);
        
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.'])
                ->withInput();
        }
        
        if (Hash::check($request->new_password, $user->password)) {
            return back()->withErrors(['new_password' => 'New password cannot be the same as your current password.'])
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
            'member_since' => $user->created_at?->format('F d, Y'),
            'last_login' => $user->last_login_at?->format('F d, Y H:i'),
            'documents_uploaded' => $user->documents()->count(),
            'budgets_created' => $user->budgets()->count(),
            'role_name' => $user->role->name ?? 'N/A',
            'role_abbreviation' => $user->role->abbreviation ?? 'N/A',
            'is_verified' => $user->hasVerifiedEmail(),
        ];
        
        return response()->json($stats);
    }
}