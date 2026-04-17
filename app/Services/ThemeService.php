<?php

namespace App\Services;

class ThemeService
{
    /**
     * Get the current theme (light/dark/navy).
     */
    public function getTheme(): string
    {
        // Retrieve from session or default to 'light'
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