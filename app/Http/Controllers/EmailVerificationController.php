<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmailVerificationController extends Controller
{
    /**
     * Show email verification notice.
     * This page should be accessible even when not authenticated.
     */
    public function notice(Request $request)
    {
        // Check if there's a verification email in session
        $email = session('verification_email');
        
        if (!$email) {
            // If no email in session, redirect to login
            return redirect()->route('login')
                ->with('info', 'Please login first.');
        }
        
        return view('auth.verify-email', compact('email'));
    }

    /**
     * Verify email address.
     */
    public function verify(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);

        if (!hash_equals(sha1($user->getEmailForVerification()), $hash)) {
            return redirect()->route('login')
                ->with('error', 'Invalid verification link.');
        }

        if ($user->hasVerifiedEmail()) {
            return redirect()->route('login')
                ->with('info', 'Email already verified.');
        }

        $user->markEmailAsVerified();
        
        // Clear the verification email from session
        session()->forget('verification_email');

        return redirect()->route('login')
            ->with('success', 'Email verified successfully! You can now log in.');
    }

    /**
     * Resend verification email.
     */
    public function resend(Request $request)
    {
        // Get email from request or session
        $email = $request->input('email') ?? session('verification_email');
        
        if (!$email) {
            return redirect()->route('login')
                ->with('error', 'Unable to resend verification email.');
        }
        
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            return redirect()->route('login')
                ->with('error', 'User not found.');
        }
        
        if ($user->hasVerifiedEmail()) {
            return redirect()->route('login')
                ->with('info', 'Email already verified.');
        }
        
        // Send verification email
        $user->sendEmailVerificationNotification();
        
        return back()->with('success', 'Verification link sent to ' . $email);
    }
}