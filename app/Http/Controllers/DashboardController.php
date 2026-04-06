<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Document;
use App\Models\Budget;
use App\Models\Income;
use App\Models\Expense;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.custom');
    }

    public function index()
    {
        $user = Auth::user()->load('role');

        $user->avatar_url = $user->avatar
            ? asset('storage/' . $user->avatar)
            : asset('images/default-avatar.png');

        // Statistics (unfiltered, global counts – you may keep as is)
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

        // Recent documents (organisation scoped)
        $recentDocuments = null;
        if ($user->hasPermission('documents.view')) {
            $docQuery = Document::with('uploader')->latest('created_at');
            if (!in_array($user->role->level, [1,2])) {
                $docQuery->where('organization_id', $user->organization_id);
            }
            $recentDocuments = $docQuery->take(5)->get();
        }

        // Recent budgets & total approved (organisation scoped)
        $recentBudgets = null;
        $totalBudget = null;
        if ($user->hasPermission('budgets.view')) {
            $budgetQuery = Budget::with('requester');
            if (!in_array($user->role->level, [1,2])) {
                $budgetQuery->where('organization_id', $user->organization_id);
            }
            $recentBudgets = (clone $budgetQuery)->latest()->take(5)->get();
            $totalBudget = (clone $budgetQuery)->where('status', 'approved')->sum('amount');
        }

        // Pending approvals (organisation scoped)
        $pendingApprovals = [];
        if ($user->hasPermission('budgets.approve')) {
            $pendingQuery = Budget::where('status', 'pending')->with('requester');
            if (!in_array($user->role->level, [1,2])) {
                $pendingQuery->where('organization_id', $user->organization_id);
            }
            $pendingBudgets = $pendingQuery->take(3)->get()->map(function ($b) {
                return [
                    'title' => $b->description ?? $b->title ?? 'Budget Request',
                    'type' => 'Budget Request',
                    'submitter' => $b->requester->full_name ?? 'Unknown',
                    'link' => route('budgets.review', $b->id)
                ];
            });
            $pendingApprovals = $pendingBudgets->toArray();
        }

        $pendingTasksCount = Budget::where('status', 'pending')->count(); // global, but you can scope if needed

        // Role description (unchanged)
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

        // User badges (unchanged)
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

        // ── CHART DATA (organisation scoped) ──────────────────────────────
        $currentYear = now()->year;
        $months = collect(range(1,12))->map(fn($m) => date('M', mktime(0,0,0,$m,1)))->toArray();

        // Base query for budgets (organisation scoped for non‑level‑1/2)
        $budgetQuery = Budget::query();
        if (!in_array($user->role->level, [1,2])) {
            $budgetQuery->where('organization_id', $user->organization_id);
        }

        // Monthly totals (all budgets)
        $monthlyTotals = (clone $budgetQuery)
            ->selectRaw('MONTH(created_at) as month, SUM(amount) as total')
            ->whereYear('created_at', $currentYear)
            ->groupBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // Monthly approved amounts
        $monthlyApproved = (clone $budgetQuery)
            ->where('status', 'approved')
            ->selectRaw('MONTH(created_at) as month, SUM(amount) as approved_total')
            ->whereYear('created_at', $currentYear)
            ->groupBy('month')
            ->pluck('approved_total', 'month')
            ->toArray();

        $chartTotals = [];
        $chartApproved = [];
        for ($i=1; $i<=12; $i++) {
            $chartTotals[] = $monthlyTotals[$i] ?? 0;
            $chartApproved[] = $monthlyApproved[$i] ?? 0;
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
            'userBadges',
            'months',
            'chartTotals',
            'chartApproved'
        ));
    }
}