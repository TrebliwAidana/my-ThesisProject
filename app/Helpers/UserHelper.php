<?php

namespace App\Helpers;

class UserHelper
{
    /**
     * Generate email for new member
     * Format: firstname@vsulhs-sslg.com
     */
    public static function generateMemberEmail($fullName)
    {
        // Get first name (first part of full name)
        $firstName = explode(' ', trim($fullName))[0];
        
        // Convert to lowercase and remove special characters
        $firstName = strtolower(preg_replace('/[^a-z0-9]/i', '', $firstName));
        
        return $firstName . '@vsulhs-sslg.com';
    }
    
    /**
     * Check if email already exists
     */
    public static function emailExists($email)
    {
        return \App\Models\User::where('email', $email)->exists();
    }
    
    /**
     * Generate unique email if duplicate exists
     */
    public static function generateUniqueMemberEmail($fullName)
    {
        $baseEmail = self::generateMemberEmail($fullName);
        $email = $baseEmail;
        $counter = 1;
        
        // If email exists, add number suffix
        while (self::emailExists($email)) {
            $name = explode(' ', trim($fullName))[0];
            $name = strtolower(preg_replace('/[^a-z0-9]/i', '', $name));
            $email = $name . $counter . '@vsulhs-sslg.com';
            $counter++;
        }
        
        return $email;
    }
}