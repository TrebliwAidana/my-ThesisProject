<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        return view('settings.index'); // you can change this later
    }

    public function updateTheme(Request $request)
    {
        return back()->with('success', 'Theme updated successfully.');
    }
}