<?php

namespace App\Services;

class ThemeService
{
    /**
     * Get the current theme (light/dark).
     */
    public function getTheme(): string
    {
        // Default to 'light' or retrieve from session/cookie
        return session('theme', 'light');
    }

    /**
     * Set the theme.
     */
    public function setTheme(string $theme): void
    {
        session(['theme' => $theme]);
    }
}