<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Document;
use App\Models\FinancialTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.custom');
    }

    public function index(Request $request)
    {
        $user = Auth::user()->load('role');

        $user->avatar_url = $user->avatar
            ? asset('storage/' . $user->avatar)
            : asset('images/default-avatar.png');

        // Get the selected range (default: monthly)
        $range = $request->get('range', 'monthly');

        // ── Member Statistics ───────────────────────────────────────────────────
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

        // ── Recent Documents ────────────────────────────────────────────────────
        $recentDocuments = null;
        if ($user->hasPermission('documents.view')) {
            $recentDocuments = Document::with('uploader')
                ->latest('created_at')
                ->take(5)
                ->get();
        }

        // ── Financial Summaries (approved only) ─────────────────────────────────
        $incomeTotal   = (float) FinancialTransaction::income()->approved()->sum('amount');
        $expenseTotal  = (float) FinancialTransaction::expense()->approved()->sum('amount');
        $balance       = $incomeTotal - $expenseTotal;
        $pendingTransactions = FinancialTransaction::pending()->count();

        // ── Recent Transactions ─────────────────────────────────────────────────
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

        // ── Pending Approvals ───────────────────────────────────────────────────
        $pendingApprovals = [];
        $pendingTasksCount = 0;
        if ($user->hasPermission('financial.approve')) {
            $pendingTransactionsForApproval = FinancialTransaction::pending()
                ->with('user')
                ->take(3)
                ->get();
            foreach ($pendingTransactionsForApproval as $tx) {
                $pendingApprovals[] = [
                    'title'     => $tx->description,
                    'type'      => ucfirst($tx->type) . ' Transaction',
                    'submitter' => $tx->user->full_name ?? $tx->user->email,
                    'link'      => route('financial.index', ['filter' => 'pending']),
                ];
            }
            $pendingTasksCount = FinancialTransaction::pending()->count();
        }

        // ── Role Description ────────────────────────────────────────────────────
        $roleDescription = $user->role->description
            ?? 'Manage your account and participate in organization activities.';

        // ── User Badges ─────────────────────────────────────────────────────────
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

        // ── Chart Data (with caching) ───────────────────────────────────────────
        $chartData = $this->getChartData($range);

        // Return the view with all required variables
        return view('dashboard.index', compact(
            'user',
            'totalMembers',
            'activeMembers',
            'officersCount',
            'newMembersThisMonth',
            'recentDocuments',
            'recentTransactions',
            'incomeTotal',
            'expenseTotal',
            'balance',
            'pendingTransactions',
            'pendingApprovals',
            'pendingTasksCount',
            'roleDescription',
            'userBadges',
            'chartData',
            'range'
        ));
    }

    /**
     * Get chart data based on the selected range (weekly, monthly, yearly).
     * Results are cached for 1 hour to improve performance.
     */
    private function getChartData(string $range): array
    {
        $cacheKey = "dashboard_chart_{$range}";

        return Cache::remember($cacheKey, 3600, function () use ($range) {
            $labels = [];
            $incomeData = [];
            $expenseData = [];

            if ($range === 'weekly') {
                // Last 7 days
                for ($i = 6; $i >= 0; $i--) {
                    $date = now()->subDays($i)->format('Y-m-d');
                    $labels[] = now()->subDays($i)->format('D, M d');

                    $incomeData[] = FinancialTransaction::income()->approved()
                        ->whereDate('transaction_date', $date)
                        ->sum('amount');

                    $expenseData[] = FinancialTransaction::expense()->approved()
                        ->whereDate('transaction_date', $date)
                        ->sum('amount');
                }
            } elseif ($range === 'yearly') {
                // Last 12 months
                for ($i = 11; $i >= 0; $i--) {
                    $date = now()->subMonths($i);
                    $labels[] = $date->format('M Y');

                    $incomeData[] = FinancialTransaction::income()->approved()
                        ->whereYear('transaction_date', $date->year)
                        ->whereMonth('transaction_date', $date->month)
                        ->sum('amount');

                    $expenseData[] = FinancialTransaction::expense()->approved()
                        ->whereYear('transaction_date', $date->year)
                        ->whereMonth('transaction_date', $date->month)
                        ->sum('amount');
                }
            } else {
                // Default: monthly (current year)
                $currentYear = now()->year;
                for ($i = 1; $i <= 12; $i++) {
                    $labels[] = now()->setMonth($i)->format('M');

                    $incomeData[] = FinancialTransaction::income()->approved()
                        ->whereYear('transaction_date', $currentYear)
                        ->whereMonth('transaction_date', $i)
                        ->sum('amount');

                    $expenseData[] = FinancialTransaction::expense()->approved()
                        ->whereYear('transaction_date', $currentYear)
                        ->whereMonth('transaction_date', $i)
                        ->sum('amount');
                }
            }

            return [
                'labels'  => $labels,
                'income'  => $incomeData,
                'expense' => $expenseData,
            ];
        });
    }
}