<?php

namespace App\Http\Controllers;

use App\Models\User;
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

        // Statistics
        $totalMembers = User::count();
        $activeMembers = User::where('is_active', true)->count();
        $officersCount = User::whereHas('role', function ($q) {
            $q->whereIn('name', [
                'System Administrator',
                'Supreme Admin',
                'Supreme Officer',
                'Org Admin',
                'Org Officer',
                'Club Adviser'
            ]);
        })->count();
        $newMembersThisMonth = User::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Recent documents (only if user has permission)
        $recentDocuments = null;
        if ($user->hasPermission('documents.view')) {
            $recentDocuments = Document::with('uploader')
                ->latest('created_at')
                ->take(5)
                ->get();
        }

        // Recent budgets (only if user has permission)
        $recentBudgets = null;
        $totalBudget = null;
        if ($user->hasPermission('budgets.view')) {
            $recentBudgets = Budget::with('requester')
                ->latest()
                ->take(5)
                ->get();
            $totalBudget = Budget::where('status', 'approved')->sum('amount');
        }

        // Pending approvals (only for users who can approve)
        $pendingApprovals = [];
        if ($user->hasPermission('budgets.approve')) {
            $pendingBudgets = Budget::where('status', 'pending')
                ->with('requester')
                ->take(3)
                ->get()
                ->map(function ($b) {
                    return [
                        'title' => $b->description ?? $b->title ?? 'Budget Request',
                        'type' => 'Budget Request',
                        'submitter' => $b->requester->full_name ?? 'Unknown',
                        'link' => route('budgets.review', $b->id)
                    ];
                });
            $pendingApprovals = $pendingBudgets->toArray();
        }

        // Pending tasks count (e.g., pending budgets)
        $pendingTasksCount = Budget::where('status', 'pending')->count();

        // Role description for the welcome header
        $roleDescriptions = [
            'System Administrator' => 'Full system control – manage users, roles, budgets, documents, and settings.',
            'Supreme Admin' => 'Oversee all organization activities, approve budgets, and manage key members.',
            'Supreme Officer' => 'Review budgets, upload documents, and manage member records.',
            'Org Admin' => 'Manage your organization’s members, budgets, and documents.',
            'Org Officer' => 'Submit budgets, upload documents, and view member lists.',
            'Club Adviser' => 'Guide the organization, approve budgets, and oversee activities.',
            'Org Member' => 'View your profile, documents, and budget status.',
        ];
        $roleDescription = $roleDescriptions[$user->role->name] ?? 'Manage your account and participate in organization activities.';

        // User badges (optional)
        $userBadges = [];
        if ($user->role->name === 'System Administrator') {
            $userBadges[] = ['color' => 'purple', 'text' => 'System Admin'];
        }
        if ($user->role->name === 'Supreme Admin') {
            $userBadges[] = ['color' => 'indigo', 'text' => 'Supreme Admin'];
        }
        if ($user->role->name === 'Club Adviser') {
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
            'recentDocuments',
            'recentBudgets',
            'totalBudget',
            'pendingApprovals',
            'pendingTasksCount',
            'roleDescription',
            'userBadges'
        ));
    }
}