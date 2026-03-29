<?php

class VerificationController extends Controller
{
    public function verify(Request $request, $id, $hash)
    {
        $user = User::findOrFail($id);
        
        // Verify hash matches user's email
        if (!hash_equals($hash, sha1($user->getEmailForVerification()))) {
            return redirect()->route('login')
                ->with('error', 'Invalid verification link.');
        }
        
        // Mark as verified
        $user->markEmailAsVerified();
        
        return redirect()->route('login')
            ->with('success', 'Email verified successfully! You can now log in.');
    }
}