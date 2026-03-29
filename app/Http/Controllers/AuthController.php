<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        \Log::info('=== LOGIN ATTEMPT ===');
        \Log::info('Email: ' . $request->email);
        
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);
        
        $user = \App\Models\User::where('email', $request->email)->first();
        
        if ($user) {
            \Log::info('User found: ' . $user->id);
            \Log::info('Password check: ' . (\Hash::check($request->password, $user->password) ? 'OK' : 'FAILED'));
            \Log::info('Is active: ' . ($user->is_active ? 'Yes' : 'No'));
            \Log::info('Email verified: ' . ($user->email_verified_at ? 'Yes' : 'No'));
            
            if (\Hash::check($request->password, $user->password) && !$user->hasVerifiedEmail()) {
                session(['verification_email' => $user->email]);
                \Log::info('Unverified user, redirecting to verification page');
                return redirect()->route('verification.notice')
                    ->with('warning', 'Please verify your email address before logging in.');
            }
        }
        
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $user = Auth::user();
            
            if (!$user->is_active) {
                Auth::logout();
                return back()
                    ->withErrors(['email' => 'Your account is inactive.'])
                    ->onlyInput('email');
            }
            
            $request->session()->regenerate();
            $user->updateLastLogin();
            \Log::info('Login successful for verified user: ' . $user->email);
            return redirect()->intended(route('dashboard'));
        }

        \Log::info('Login failed - invalid credentials');
        return back()
            ->withErrors(['email' => 'These credentials do not match our records.'])
            ->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}