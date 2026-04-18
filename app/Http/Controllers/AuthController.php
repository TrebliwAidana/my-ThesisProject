<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\AuditLogger;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        // If already logged in, redirect to dashboard
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        
        return view('auth.login');
    }

    public function login(Request $request)
    {
        \Log::info('=== LOGIN ATTEMPT ===');
        \Log::info('Email: ' . $request->email);
        \Log::info('Remember me checked: ' . ($request->boolean('remember') ? 'YES' : 'NO'));

        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);
        
        $user = User::where('email', $request->email)->first();
        
        if ($user) {
            \Log::info('User found: ' . $user->id);
            \Log::info('User role: ' . ($user->role->name ?? 'No role assigned'));
            \Log::info('Password check: ' . (Hash::check($request->password, $user->password) ? 'OK' : 'FAILED'));
            \Log::info('Is active: ' . ($user->is_active ? 'Yes' : 'No'));
            \Log::info('Email verified: ' . ($user->email_verified_at ? 'Yes' : 'No'));
            \Log::info('Current remember_token: ' . ($user->remember_token ?? 'null'));
            
            if (Hash::check($request->password, $user->password) && !$user->hasVerifiedEmail()) {
                session(['verification_email' => $user->email]);
                \Log::info('Unverified user, redirecting to verification page');
                return redirect()->route('verification.notice')
                    ->with('warning', '⚠️ Please verify your email address before logging in. Check your inbox or spam folder.');
            }
        }
        
        $remember = $request->boolean('remember');
        \Log::info('Attempting Auth::attempt with remember = ' . ($remember ? 'true' : 'false'));
        
        if (Auth::attempt($credentials, $remember)) {
            $user = Auth::user();
            \Log::info('Auth::attempt successful');
            \Log::info('User after login - remember_token: ' . ($user->remember_token ?? 'null'));
            \Log::info('Auth::viaRemember(): ' . (Auth::viaRemember() ? 'true' : 'false'));
            
            if (!$user->is_active) {
                Auth::logout();
                \Log::info('User inactive, logged out');
                return back()
                    ->withErrors(['email' => '❌ Your account is inactive. Please contact the administrator.'])
                    ->onlyInput('email');
            }
            
            $request->session()->regenerate();
            $user->updateLastLogin();
            
            \Log::info('Login successful for user: ' . $user->email);
            \Log::info('Session ID: ' . session()->getId());
            \Log::info('Remember token after login: ' . ($user->fresh()->remember_token ?? 'null'));
        
             AuditLogger::log('login');
            return redirect()->intended(route('dashboard'));
        }

        \Log::info('Login failed - invalid credentials');
        return back()
            ->withErrors(['email' => '❌ These credentials do not match our records.'])
            ->onlyInput('email');
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            \Log::info('Logging out user: ' . $user->email);
            
            // Optional: Log logout to audit log
            // \App\Models\AuditLog::create([
            //     'user_id' => $user->id,
            //     'action' => 'logout',
            //     'ip_address' => $request->ip(),
            // ]);
        }

        AuditLogger::log('logout');
        
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        // ✅ Redirect to landing page instead of login
        return redirect('/')->with('success', '✅ You have been logged out successfully.');
    }
    
    /**
     * Show the email verification notice
     */
    public function verificationNotice()
    {
        return view('auth.verify-email');
    }
    
    /**
     * Resend the email verification notification
     */
    public function resendVerification(Request $request)
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }
        
        if ($user->hasVerifiedEmail()) {
            return redirect()->route('dashboard')
                ->with('success', '✅ Your email is already verified.');
        }
        
        $user->sendEmailVerificationNotification();
        
        return back()->with('success', '📧 A new verification link has been sent to your email address.');
    }
    public function guestLogin(Request $request)
    {
        $guestUser = User::where('email', 'guest@gmail.com')->firstOrFail();
        
        Auth::login($guestUser);
        $request->session()->regenerate();
        
        return redirect()->route('dashboard')
            ->with('success', 'You are now browsing as a guest. Limited access.');
    }
}