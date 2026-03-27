<?php

namespace App\Services;

class ThemeService
{
    // Example: determine the current theme for the school/user
    public static function current(): string
    {
        // You can replace this logic with DB or config
        // Possible values: 'red', 'blue', 'green', 'yellow'
        return session('school_theme', 'blue');
    }
}