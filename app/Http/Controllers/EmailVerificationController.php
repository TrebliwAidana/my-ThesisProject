<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
    /**
     * Show email verification notice.
     * Accessible even when not authenticated.
     */
    public function notice(Request $request)
    {
        $email = session('verification_email');

        if (!$email) {
            return redirect()->route('login')
                ->with('info', 'Please login first.');
        }

        return view('auth.verify-email', compact('email'));
    }

    /**
     * Verify email address via signed URL link.
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
                ->with('info', 'Email already verified. Please log in.');
        }

        $user->markEmailAsVerified();

        // Clear the verification email from session
        session()->forget('verification_email');

        return redirect()->route('login')
            ->with('success', 'Email verified successfully! You can now log in with your credentials.');
    }

    /**
     * Resend verification-only email (no password — just the verify link).
     */
    public function resend(Request $request)
    {
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
                ->with('info', 'Email already verified. Please log in.');
        }

        // Sends VerifyEmailOnly notification (verify link, no password)
        $user->sendEmailVerificationNotification();

        return back()->with('success', 'Verification link resent to ' . $email);
    }
}