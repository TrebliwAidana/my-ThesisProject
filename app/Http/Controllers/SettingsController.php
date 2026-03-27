<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    /** Show the settings page */
    public function index()
    {
        return view('admin.settings');
    }

    /** Save the chosen theme for the authenticated user */
    public function updateTheme(Request $request)
    {
        $validated = $request->validate([
            'theme' => ['required', 'string', 'in:navy,forest,crimson,slate,amber,rose,light'],
        ]);

        Auth::user()->update(['theme' => $validated['theme']]);

        return response()->json(['theme' => $validated['theme']]);
    }
}