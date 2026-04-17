<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Document;
use App\Models\FinancialTransaction;
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

        $user->avatar_url = $user->avatar
            ? asset('storage/' . $user->avatar)
            : asset('images/default-avatar.png');

        // ── Statistics ──────────────────────────────────────────────────────────
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

        // ── Recent documents ────────────────────────────────────────────────────
        $recentDocuments = null;
        if ($user->hasPermission('documents.view')) {
            $recentDocuments = Document::with('uploader')
                ->latest('created_at')
                ->take(5)
                ->get();
        }

        // ── FINANCIAL DATA (replaces budgets) ───────────────────────────────────
        // Cast to float to avoid number_format() errors
        $totalIncome   = (float) FinancialTransaction::income()->approved()->sum('amount');
        $totalExpense  = (float) FinancialTransaction::expense()->approved()->sum('amount');
        $netBalance    = $totalIncome - $totalExpense;

        $recentTransactions = null;
        if ($user->hasPermission('financial.view')) {
            $recentTransactions = FinancialTransaction::with('user')
                ->latest('transaction_date')
                ->take(5)
                ->get()
                ->map(function ($tx) {
                    $tx->amount = (float) $tx->amount;
                    return $tx;
                });
        }

        // ── Pending approvals (for financial transactions) ──────────────────────
        $pendingApprovals = [];
        $pendingTasksCount = 0;
        if ($user->hasPermission('financial.approve')) {
            $pendingTransactions = FinancialTransaction::pending()
                ->with('user')
                ->take(3)
                ->get();
            foreach ($pendingTransactions as $tx) {
                $pendingApprovals[] = [
                    'title' => $tx->description,
                    'type' => ucfirst($tx->type) . ' Transaction',
                    'submitter' => $tx->user->full_name ?? $tx->user->email,
                    'link' => route('financial.index', ['filter' => 'pending']),
                ];
            }
            $pendingTasksCount = FinancialTransaction::pending()->count();
        }

        // ── ROLE DESCRIPTION – dynamic from database ───────────────────────────
        $roleDescription = $user->role->description
            ?? 'Manage your account and participate in organization activities.';

        // ── User badges (unchanged) ────────────────────────────────────────────
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

        // ── CHART DATA (monthly income vs expenses) ────────────────────────────
        $currentYear = now()->year;
        $months = [];
        $monthlyIncome = [];
        $monthlyExpense = [];

        for ($i = 1; $i <= 12; $i++) {
            $months[] = date('M', mktime(0, 0, 0, $i, 1));

            $income = (float) FinancialTransaction::income()
                ->approved()
                ->whereYear('transaction_date', $currentYear)
                ->whereMonth('transaction_date', $i)
                ->sum('amount');

            $expense = (float) FinancialTransaction::expense()
                ->approved()
                ->whereYear('transaction_date', $currentYear)
                ->whereMonth('transaction_date', $i)
                ->sum('amount');

            $monthlyIncome[] = $income;
            $monthlyExpense[] = $expense;
        }

        // ── Return view with all required variables ────────────────────────────
        return view('dashboard.index', compact(
            'user',
            'totalMembers',
            'activeMembers',
            'officersCount',
            'newMembersThisMonth',
            'recentDocuments',
            'recentTransactions',
            'totalIncome',
            'totalExpense',
            'netBalance',
            'pendingApprovals',
            'pendingTasksCount',
            'roleDescription',
            'userBadges',
            'months',
            'monthlyIncome',
            'monthlyExpense'
        ));
    }
}