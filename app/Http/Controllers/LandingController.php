<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Document;
use App\Models\FinancialTransaction;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;

class LandingController extends Controller
{
    public function index()
    {
        // Redirect authenticated users to the dashboard
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        // Real statistics for the landing page
        $activeMembersCount = User::where('is_active', true)->count();
        $totalFunds = FinancialTransaction::income()->approved()->sum('amount');
        $documentsCount = Document::count();

        // Recent documents (all documents are public now, so no is_public filter)
        $recentPublicDocs = Document::with('category', 'owner')
            ->latest()
            ->take(3)
            ->get();

        // Featured members, ordered by role level (highest first)
        $featuredMembers = User::with('role')
            ->where('is_active', true)
            ->whereHas('role', function ($q) {
                $q->whereIn('name', [
                    'System Administrator',
                    'Supreme Admin',
                    'Supreme Officer',
                    'Club Adviser',
                    'Org Admin',
                    'Org Officer',
                    'Org Member',
                ]);
            })
            ->join('roles', 'users.role_id', '=', 'roles.id')
            ->orderBy('roles.level', 'asc')
            ->select('users.*')
            ->take(10)
            ->get();

        // Dynamic roles from DB with active member counts
        $roles = Role::where('is_visible', true)
            ->withCount(['users' => fn($q) => $q->where('is_active', true)])
            ->orderBy('level', 'asc')
            ->get();

        return view('landing', compact(
            'activeMembersCount',
            'totalFunds',
            'documentsCount',
            'recentPublicDocs',
            'featuredMembers',
            'roles'
        ));
    }
}