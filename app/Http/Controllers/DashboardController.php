<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\FinancialTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.custom');
    }

    public function index(Request $request)
    {
        $user = Auth::user()->load(['role', 'member']);

        $user->avatar_url = $user->avatar
            ? asset('storage/' . $user->avatar)
            : asset('images/default-avatar.png');

        $range = $request->get('range', 'monthly');

        // ── Member Statistics ───────────────────────────────────────────────
        $totalMembers        = User::count();
        $activeMembers       = User::where('is_active', true)->count();
        $officersCount       = User::join('roles', 'users.role_id', '=', 'roles.id')
            ->whereIn('roles.name', ['System Administrator', 'treasurer', 'auditor', 'Club Adviser'])
            ->count();
        $newMembersThisMonth = User::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // ── Recent Documents ────────────────────────────────────────────────
        $recentDocuments = null;
        if ($user->hasPermission('documents.view')) {
            $recentDocuments = Document::with('uploader')
                ->latest('created_at')
                ->take(5)
                ->get();
        }

        // ── Financial Summaries ─────────────────────────────────────────────
        $incomeTotal         = (float) FinancialTransaction::income()->approved()->sum('amount');
        $receivablePaidTotal = (float) FinancialTransaction::receivable()->paid()->sum('amount');
        $totalIncome         = $incomeTotal + $receivablePaidTotal;
        $expenseTotal        = (float) FinancialTransaction::expense()->approved()->sum('amount');
        $balance             = $totalIncome - $expenseTotal;
        $pendingTransactions = FinancialTransaction::pending()->count();

        // ── Recent Transactions ─────────────────────────────────────────────
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

        // ── Pending Approvals ───────────────────────────────────────────────
        $pendingApprovals  = [];
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

        // ── Role Description ────────────────────────────────────────────────
        $roleDescription = $user->role->description
            ?? 'Manage your account and participate in organization activities.';

        // ── User Badges ─────────────────────────────────────────────────────
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

        // ── Chart Data ──────────────────────────────────────────────────────
        $chartData    = $this->getChartData($range);
        $totalExpense = $expenseTotal;
        $netBalance   = $balance;

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
            'totalExpense',
            'balance',
            'totalIncome',
            'receivablePaidTotal',
            'netBalance',
            'pendingTransactions',
            'pendingApprovals',
            'pendingTasksCount',
            'roleDescription',
            'userBadges',
            'chartData',
            'range'
        ));
    }

    private function getChartData(string $range): array
    {
        $isPostgres = DB::getDriverName() === 'pgsql';

        if ($range === 'weekly') {
            $rows = FinancialTransaction::whereIn('status', ['approved', 'paid'])
                ->selectRaw("type, status, DATE(transaction_date) as period, SUM(amount) as total")
                ->whereBetween('transaction_date', [now()->subDays(6)->startOfDay(), now()->endOfDay()])
                ->groupByRaw("type, status, DATE(transaction_date)")
                ->get();

            $labels = [];
            $keys   = [];
            for ($i = 6; $i >= 0; $i--) {
                $labels[] = now()->subDays($i)->format('D, M d');
                $keys[]   = now()->subDays($i)->format('Y-m-d');
            }

        } elseif ($range === 'yearly') {

            // ✅ Fixed: DATE_FORMAT → TO_CHAR for PostgreSQL
            $periodExpr = $isPostgres
                ? "TO_CHAR(transaction_date, 'YYYY-MM') as period"
                : "DATE_FORMAT(transaction_date, '%Y-%m') as period";

            $groupExpr = $isPostgres
                ? "type, status, TO_CHAR(transaction_date, 'YYYY-MM')"
                : "type, status, DATE_FORMAT(transaction_date, '%Y-%m')";

            $rows = FinancialTransaction::whereIn('status', ['approved', 'paid'])
                ->selectRaw("type, status, $periodExpr, SUM(amount) as total")
                ->where('transaction_date', '>=', now()->subMonths(11)->startOfMonth())
                ->groupByRaw($groupExpr)
                ->get();

            $labels = [];
            $keys   = [];
            for ($i = 11; $i >= 0; $i--) {
                $d        = now()->subMonths($i);
                $labels[] = $d->format('M Y');
                $keys[]   = $d->format('Y-m');
            }

        } else {
            // monthly (default)
            // ✅ Fixed: MONTH() → EXTRACT(MONTH FROM) for PostgreSQL
            $periodExpr = $isPostgres
                ? "EXTRACT(MONTH FROM transaction_date)::integer as period"
                : "MONTH(transaction_date) as period";

            $groupExpr = $isPostgres
                ? "type, status, EXTRACT(MONTH FROM transaction_date)"
                : "type, status, MONTH(transaction_date)";

            $rows = FinancialTransaction::whereIn('status', ['approved', 'paid'])
                ->selectRaw("type, status, $periodExpr, SUM(amount) as total")
                ->whereYear('transaction_date', now()->year)
                ->groupByRaw($groupExpr)
                ->get();

            $labels = [];
            $keys   = [];
            for ($i = 1; $i <= 12; $i++) {
                $labels[] = now()->setMonth($i)->format('M');
                $keys[]   = $i;
            }
        }

        // Build indexed totals
        $indexed = [];
        foreach ($rows as $row) {
            $key = (string) $row->period;

            if ($row->type === 'income' && $row->status === 'approved') {
                $indexed['income'][$key] = ($indexed['income'][$key] ?? 0) + (float) $row->total;
            } elseif ($row->type === 'receivable' && $row->status === 'paid') {
                $indexed['income'][$key] = ($indexed['income'][$key] ?? 0) + (float) $row->total;
            } elseif ($row->type === 'expense' && $row->status === 'approved') {
                $indexed['expense'][$key] = ($indexed['expense'][$key] ?? 0) + (float) $row->total;
            }
        }

        return [
            'labels'  => $labels,
            'income'  => array_map(fn($k) => $indexed['income'][(string)$k]  ?? 0, $keys),
            'expense' => array_map(fn($k) => $indexed['expense'][(string)$k] ?? 0, $keys),
        ];
    }
}