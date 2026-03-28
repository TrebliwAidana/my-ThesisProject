<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Member;
use App\Models\Document;
use App\Models\Budget;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.custom');
    }

    public function index()
    {
        $user = Auth::user()->load('role');

        // Stats
        $totalMembers        = Member::count();
        $activeMembers       = Member::where(function ($q) {
                                    $q->whereNull('term_end')
                                      ->orWhere('term_end', '>=', now());
                                })->count();
        // FIXED: Changed 'Admin' to 'Adviser'
        $officersCount       = User::whereHas('role', fn($q) =>
                                    $q->whereIn('name', ['Adviser', 'Officer'])
                               )->count();
        $newMembersThisMonth = Member::whereMonth('created_at', now()->month)
                                     ->whereYear('created_at', now()->year)
                                     ->count();

        // Members table with pagination
        $members = Member::with('user.role')
            ->latest()
            ->paginate(10);

        // Role & status color maps - FIXED: Changed 'Admin' to 'Adviser'
        $roleColors = [
            'Adviser' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300',
            'Officer' => 'bg-sky-100 text-sky-700 dark:bg-sky-900 dark:text-sky-300',
            'Auditor' => 'bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-300',
            'Member'  => 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300',
        ];
        $statusColors = [
            'Active'   => 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300',
            'Inactive' => 'bg-slate-100 text-slate-500 dark:bg-slate-700 dark:text-slate-400',
        ];

        // Recent activity - FIXED: Changed 'reviewer.user' to 'requester'
        $recentDocuments = Document::with('uploader')
            ->latest('uploaded_at')
            ->take(5)
            ->get();
        $recentBudgets   = Budget::with('requester')  // Changed from 'reviewer.user'
            ->latest()
            ->take(5)
            ->get();

        // Total approved budget
        $totalBudget = Budget::where('status', 'approved')->sum('amount');

        // Badges
        $userBadges = [];

        // Use dashboard.dashboard (folder.file)
        return view('dashboard.dashboard', compact(
            'user',
            'totalMembers',
            'activeMembers',
            'officersCount',
            'newMembersThisMonth',
            'members',
            'roleColors',
            'statusColors',
            'recentDocuments',
            'recentBudgets',
            'totalBudget',
            'userBadges'
        ));
    }
}