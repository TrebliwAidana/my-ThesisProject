<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Financial\FinancialHelperTrait;
use App\Models\FinancialTransaction;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Services\AuditLogger;

class FinancialController extends Controller
{
    use FinancialHelperTrait;

    public function __construct()
    {
        $this->middleware('auth.custom');
    }

    // ── Index ──────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $user = Auth::user();

        if ($user->email === 'guest@gmail.com') {
            return redirect()->route('dashboard')
                ->with('error', 'Guest accounts cannot view financial records.');
        }

        if ($user->role->level !== 1 && !$user->hasPermission('view_financial_transactions')) {
            abort(403, 'You do not have permission to view financial records.');
        }

        $query = FinancialTransaction::with([
            'user:id,full_name,email',
            'approver:id,full_name,email',
            'auditor:id,full_name,email',
        ])->latest('transaction_date');

        $showApproved = $request->boolean('show_approved');
        if (!$showApproved) {
            // For receivables: hide only when 'paid' (equivalent of approved+collected)
            // For income/expense: hide when 'approved'
            $query->where(function ($q) {
                $q->where(function ($inner) {
                    $inner->where('type', 'receivable')
                          ->where('status', '!=', 'paid');
                })->orWhere(function ($inner) {
                    $inner->whereIn('type', ['income', 'expense'])
                          ->where('status', '!=', 'approved');
                });
            });
        }

        if ($request->filled('type'))      $query->where('type', $request->type);
        if ($request->filled('status'))    $query->where('status', $request->status);
        if ($request->filled('category'))  $query->where('category', $request->category);
        if ($request->filled('search'))    $query->where('description', 'like', '%' . $request->search . '%');
        if ($request->filled('date_from')) $query->whereDate('transaction_date', '>=', $request->date_from);
        if ($request->filled('date_to'))   $query->whereDate('transaction_date', '<=', $request->date_to);

        $transactions = $query->paginate(15)->appends($request->except('page'));

        // Income total: approved income transactions only
        $incomeTotal = FinancialTransaction::income()->approved()->sum('amount');

        // Receivables total: only paid receivables count toward income
        $receivablePaidTotal = FinancialTransaction::receivable()->paid()->sum('amount');

        // Combined income displayed on the card
        $totalIncome  = $incomeTotal + $receivablePaidTotal;
        $expenseTotal = FinancialTransaction::expense()->approved()->sum('amount');
        $balance      = $totalIncome - $expenseTotal;

        $pendingCount = FinancialTransaction::pending()->count();
        $auditedCount = FinancialTransaction::audited()->count();

        $categories = FinancialTransaction::select('category')
            ->distinct()->whereNotNull('category')->orderBy('category')->pluck('category');

        return view('financial.index', compact(
            'transactions', 'incomeTotal', 'expenseTotal', 'balance',
            'pendingCount', 'auditedCount', 'categories', 'showApproved',
            'receivablePaidTotal', 'totalIncome'
        ));
    }

    // ── Show ───────────────────────────────────────────────────────────────

    public function show(int $id)
    {
        $user = Auth::user();

        if ($user->email === 'guest@gmail.com') {
            return redirect()->route('dashboard')
                ->with('error', 'Guest accounts cannot view financial records.');
        }

        if ($user->role->level !== 1 && !$user->hasPermission('view_financial_transactions')) {
            abort(403);
        }

        $transaction = FinancialTransaction::with([
            'user', 'approver', 'auditor', 'documents',
        ])->findOrFail($id);

        return view('financial.show', compact('transaction'));
    }

    // ── Edit / Update ──────────────────────────────────────────────────────

    public function edit(int $id)
    {
        if ($response = $this->authorizeFinancialAction('edit')) return $response;

        $transaction = FinancialTransaction::with('documents')->findOrFail($id);

        if ($transaction->status !== 'pending') {
            return back()->with('error', 'Only pending transactions can be edited.');
        }

        return view('financial.edit', compact('transaction'));
    }

    public function update(Request $request, int $id)
    {
        if ($response = $this->authorizeFinancialAction('edit')) return $response;

        $transaction = FinancialTransaction::with('documents')->findOrFail($id);

        if ($transaction->status !== 'pending') {
            return back()->with('error', 'Only pending transactions can be edited.');
        }

        $validated = $this->validateTransaction($request);
        $oldData   = $transaction->getOriginal();

        DB::transaction(function () use ($request, $transaction, $validated, $oldData) {
            $transaction->update($validated);

            if ($request->hasFile('receipt')) {
                foreach ($transaction->documents as $doc) {
                    $transaction->documents()->detach($doc->id);
                    $doc->delete();
                }
                $this->attachReceiptDocument($transaction, $request->file('receipt'), 'Updated receipt');
            }

            AuditLogger::log(
                'updated', $transaction,
                "Transaction: {$transaction->description}",
                $oldData,
                $transaction->getChanges()
            );
        });

        return redirect()->route('financial.index')
            ->with('success', 'Transaction updated successfully.');
    }

    // ── Audit ──────────────────────────────────────────────────────────────

    public function audit(Request $request, int $id)
    {
        $user = Auth::user();

        if ($user->email === 'guest@gmail.com') {
            return redirect()->route('dashboard')
                ->with('error', 'Guest accounts cannot perform this action.');
        }

        if (!$user->hasPermission('view_financial_transactions') && $user->role->level !== 1) {
            return back()->with('error', 'You do not have permission to audit transactions.');
        }

        $transaction = FinancialTransaction::findOrFail($id);

        if ($transaction->status !== 'pending') {
            return back()->with('error', 'Only pending transactions can be audited.');
        }

        $oldStatus = $transaction->status;

        $transaction->update([
            'status'     => 'audited',
            'audited_by' => $user->id,
            'audited_at' => now(),
        ]);

        AuditLogger::log(
            'audited', $transaction,
            "Transaction audited: {$transaction->description}",
            ['status' => $oldStatus],
            ['status' => 'audited']
        );

        return back()->with('success', 'Transaction marked as audited. Awaiting final approval.');
    }

    // ── Approve ────────────────────────────────────────────────────────────

    public function approve(int $id)
    {
        if ($response = $this->authorizeFinancialAction('approve')) return $response;

        $transaction = FinancialTransaction::with(['user', 'auditor', 'documents'])
            ->findOrFail($id);

        if ($transaction->status !== 'audited') {
            return back()->with('error', 'Transaction must be audited before final approval.');
        }

        $oldStatus = $transaction->status;

        DB::transaction(function () use ($transaction, $oldStatus) {
            $transaction->update([
                'status'      => 'approved',
                'approved_by' => Auth::id(),
                'approved_at' => now(),
            ]);
            $transaction->refresh();

            // Income and expense: save approval PDF immediately to Documents
            // Receivable: NOT saved to Documents yet — only happens on Mark as Paid
            if ($transaction->type !== 'receivable') {
                $this->saveApprovedTransactionAsDocument($transaction);
            }

            AuditLogger::log(
                'approved', $transaction,
                "Transaction approved: {$transaction->description}",
                ['status' => $oldStatus],
                ['status' => 'approved']
            );
        });

        $msg = $transaction->type === 'receivable'
            ? 'Receivable approved. Click "Mark as Paid" on the detail page once the customer settles — that will add it to income and save the document.'
            : 'Transaction approved and saved to Documents successfully.';

        return back()->with('success', $msg);
    }

    // ── Reject ─────────────────────────────────────────────────────────────

    public function reject(int $id)
    {
        if ($response = $this->authorizeFinancialAction('approve')) return $response;

        $transaction = FinancialTransaction::findOrFail($id);

        if (in_array($transaction->status, ['approved', 'paid', 'rejected'])) {
            return back()->with('error', 'This transaction has already been finalized.');
        }

        $oldStatus = $transaction->status;
        $transaction->update(['status' => 'rejected']);

        AuditLogger::log(
            'rejected', $transaction,
            "Transaction rejected: {$transaction->description}",
            ['status' => $oldStatus],
            ['status' => 'rejected']
        );

        return back()->with('success', 'Transaction rejected.');
    }

    // ── Mark as Paid (receivables only) ────────────────────────────────────

    public function markAsPaid(int $id)
    {
        if ($response = $this->authorizeFinancialAction('approve')) return $response;

        $transaction = FinancialTransaction::with(['user', 'approver', 'auditor', 'documents'])
            ->findOrFail($id);

        if ($transaction->type !== 'receivable') {
            return back()->with('error', 'Only receivables can be marked as paid.');
        }

        if ($transaction->status !== 'approved') {
            return back()->with('error', 'Receivable must be approved before it can be marked as paid.');
        }

        DB::transaction(function () use ($transaction) {
            $transaction->update([
                'status'      => 'paid',
                'approved_by' => Auth::id(), // person who confirmed payment
                'approved_at' => now(),
            ]);
            $transaction->refresh();

            // NOW save the approval PDF and add to Documents
            $this->saveApprovedTransactionAsDocument($transaction);

            AuditLogger::log(
                'paid', $transaction,
                "Receivable marked as paid: {$transaction->description} — Customer: {$transaction->customer_name}",
                ['status' => 'approved'],
                ['status' => 'paid']
            );
        });

        return back()->with('success', 'Receivable marked as paid. Amount added to total income and approval slip saved to Documents.');
    }

    // ── Destroy ────────────────────────────────────────────────────────────

    public function destroy(int $id)
    {
        if ($response = $this->authorizeFinancialAction('delete')) return $response;

        $transaction = FinancialTransaction::with('documents')->findOrFail($id);

        AuditLogger::log(
            'deleted', $transaction,
            "Transaction: {$transaction->description}",
            $transaction->toArray(),
            []
        );

        DB::transaction(function () use ($transaction) {
            foreach ($transaction->documents as $doc) {
                $doc->delete();
            }
            $transaction->delete();
        });

        return redirect()->route('financial.index')
            ->with('success', 'Transaction deleted. It will now appear in the Trash.');
    }

    // ── Trash / Restore / Force Delete ─────────────────────────────────────

    public function trash()
    {
        $user = Auth::user();

        if ($user->role->level !== 1 && !$user->hasPermission('manage_all')) {
            abort(403);
        }

        $transactions = FinancialTransaction::onlyTrashed()
            ->with(['user', 'approver', 'auditor', 'documents'])
            ->latest('deleted_at')
            ->paginate(15);

        return view('financial.trash', compact('transactions'));
    }

    public function restore($id)
    {
        $user = Auth::user();

        if ($user->role->level !== 1 && !$user->hasPermission('manage_all')) {
            abort(403);
        }

        $transaction = FinancialTransaction::onlyTrashed()->findOrFail($id);
        $transaction->restore();

        AuditLogger::log(
            'restored', $transaction,
            "Financial transaction restored: {$transaction->description}"
        );

        return redirect()->route('financial.trash')
            ->with('success', 'Transaction restored successfully.');
    }

    public function forceDelete($id)
    {
        $user = Auth::user();

        if ($user->role->level !== 1 && !$user->hasPermission('manage_all')) {
            abort(403);
        }

        $transaction = FinancialTransaction::onlyTrashed()->with('documents')->findOrFail($id);

        foreach ($transaction->documents as $doc) {
            $doc->forceDelete();
        }

        AuditLogger::log(
            'force_deleted', $transaction,
            "Financial transaction permanently deleted: {$transaction->description}",
            $transaction->toArray(),
            []
        );

        $transaction->forceDelete();

        return redirect()->route('financial.trash')
            ->with('success', 'Transaction permanently deleted.');
    }

    // ── Report ─────────────────────────────────────────────────────────────

    public function reportForm()
    {
        $this->checkGuest();

        if (!auth()->user()->hasPermission('approve_financial_transactions') && auth()->user()->role->level !== 1) {
            abort(403);
        }

        return view('financial.report-form');
    }

    public function generateReport(Request $request)
    {
        $this->checkGuest();

        if (!auth()->user()->hasPermission('approve_financial_transactions') && auth()->user()->role->level !== 1) {
            abort(403);
        }

        $validated = $request->validate([
            'start_date'    => 'required|date',
            'end_date'      => 'required|date|after_or_equal:start_date',
            'organization'  => 'nullable|string|max:255',
            'previous_cash' => 'nullable|numeric|min:0',
        ]);

        $pdf  = $this->buildReportPdf($validated);
        $from = $validated['start_date'];
        $to   = $validated['end_date'];

        return $pdf->download("financial_report_{$from}_to_{$to}.pdf");
    }

    public function preview(Request $request)
    {
        $this->checkGuest();

        if (!auth()->user()->hasPermission('approve_financial_transactions') && auth()->user()->role->level !== 1) {
            abort(403);
        }

        $validated = $request->validate([
            'start_date'    => 'required|date',
            'end_date'      => 'required|date|after_or_equal:start_date',
            'organization'  => 'nullable|string|max:255',
            'previous_cash' => 'nullable|numeric|min:0',
        ]);

        return $this->buildReportPdf($validated)->stream('financial_report_preview.pdf');
    }

    // ── Report Builder ─────────────────────────────────────────────────────

    private function buildReportPdf(array $validated): \Barryvdh\DomPDF\PDF
    {
        $startDate = $validated['start_date'];
        $endDate   = $validated['end_date'];
        $orgName   = $validated['organization'] ?? '_________________________';
        $prevCash  = (float) ($validated['previous_cash'] ?? 0);

        // Approved cash income only
        $incomeTotal = FinancialTransaction::where('type', 'income')
            ->where('status', 'approved')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('amount');

        // Paid receivables count as collected income
        $receivablePaidTotal = FinancialTransaction::where('type', 'receivable')
            ->where('status', 'paid')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('amount');

        $incomeTotal += $receivablePaidTotal;

        $expenseTotal = FinancialTransaction::where('type', 'expense')
            ->where('status', 'approved')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('amount');

        $netFromOps = $incomeTotal - $expenseTotal;
        $netFinal   = $netFromOps + $prevCash;

        // Outstanding receivables — approved but not yet paid
        $receivablesTotal = FinancialTransaction::where('type', 'receivable')
            ->where('status', 'approved')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('amount');

        $get = fn($role) => User::whereHas('role', fn($q) => $q->where('name', $role))
            ->where('is_active', true)->first();

        $data = [
            'org_name'       => $orgName,
            'start_date'     => $startDate,
            'end_date'       => $endDate,
            'income_total'   => $incomeTotal,
            'expense_total'  => $expenseTotal,
            'net_from_ops'   => $netFromOps,
            'prev_cash'      => $prevCash,
            'net_final'      => $netFinal,
            'receivables'    => $receivablesTotal,
            'generated_at'   => now(),
            'auditor_name'   => optional($get('Auditor'))->full_name   ?? '_________________________',
            'president_name' => optional($get('President'))->full_name ?? '_________________________',
            'adviser_name'   => optional($get('Club Adviser'))->full_name ?? '_________________________',
            'treasurer_name' => optional($get('Treasurer'))->full_name ?? 'SHEERWINA MAE G. BALOTITE',
            'guidance_name'  => optional($get('Guidance Facilitator'))->full_name ?? 'NOEMI ELISA L. OQUIAS',
        ];

        return Pdf::loadView('financial.report-pdf', $data)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont'          => 'sans-serif',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => true,
                'dpi'                  => 96,
                'margin_left'          => 15,
                'margin_right'         => 15,
                'margin_top'           => 15,
                'margin_bottom'        => 15,
            ]);
    }
}