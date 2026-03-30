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

        $totalMembers = User::count();
        $activeMembers = User::where('is_active', true)->count();
        
        $officersCount = User::whereHas('role', function($q) {
            $q->whereIn('name', [
                'System Administrator', 
                'Supreme Admin', 
                'Supreme Officer', 
                'Org Admin', 
                'Org Officer', 
                'Adviser'
            ]);
        })->count();
        
        $newMembersThisMonth = User::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $members = User::with('role')->latest()->paginate(10);

        $roleColors = [
            'System Administrator' => 'bg-purple-100 text-purple-700 dark:bg-purple-900 dark:text-purple-300',
            'Supreme Admin' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300',
            'Supreme Officer' => 'bg-blue-100 text-blue-700 dark:bg-blue-900 dark:text-blue-300',
            'Org Admin' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900 dark:text-emerald-300',
            'Org Officer' => 'bg-sky-100 text-sky-700 dark:bg-sky-900 dark:text-sky-300',
            'Adviser' => 'bg-amber-100 text-amber-700 dark:bg-amber-900 dark:text-amber-300',
            'Org Member' => 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300',
            'Guest' => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400',
        ];
        
        $statusColors = [
            'Active'   => 'bg-green-100 text-green-700 dark:bg-green-900 dark:text-green-300',
            'Inactive' => 'bg-slate-100 text-slate-500 dark:bg-slate-700 dark:text-slate-400',
        ];

        $recentDocuments = Document::with('uploader')
            ->latest('created_at')
            ->take(5)
            ->get();
            
        $recentBudgets = Budget::with('requester')
            ->latest()
            ->take(5)
            ->get();

        $totalBudget = Budget::where('status', 'approved')->sum('amount');

        $userBadges = [];
        
        if ($user->role->name === 'System Administrator') {
            $userBadges[] = ['color' => 'purple', 'text' => 'System Admin'];
        }
        if ($user->role->name === 'Supreme Admin') {
            $userBadges[] = ['color' => 'indigo', 'text' => 'Supreme Admin'];
        }
        if ($user->role->name === 'Adviser') {
            $userBadges[] = ['color' => 'amber', 'text' => 'Adviser'];
        }
        if (in_array($user->role->abbreviation, ['OA', 'OO'])) {
            $userBadges[] = ['color' => 'emerald', 'text' => 'Org Leader'];
        }

        return view('dashboard.index', compact(
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