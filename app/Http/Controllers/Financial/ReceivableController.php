<?php

namespace App\Http\Controllers\Financial;

use App\Http\Controllers\Controller;
use App\Models\FinancialTransaction;
use App\Models\Receivable;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReceivableController extends Controller
{
    use FinancialHelperTrait;

    public function __construct()
    {
        $this->middleware('auth.custom');
    }

    // ── Index ──────────────────────────────────────────────────────────────

    public function index()
    {
        $this->checkGuest();

        if (!auth()->user()->hasPermission('financial.view') && auth()->user()->role->level !== 1) {
            abort(403);
        }

        $receivables = Receivable::with(['payments', 'creator'])
            ->orderByRaw("FIELD(status, 'overdue', 'pending', 'partial', 'paid')")
            ->orderBy('due_date')
            ->paginate(20);

        $totalOutstanding = Receivable::whereIn('status', ['pending', 'partial', 'overdue'])
            ->sum(DB::raw('total_amount - paid_amount'));

        return view('financial.receivables.index', compact('receivables', 'totalOutstanding'));
    }

    // ── Show ───────────────────────────────────────────────────────────────

    public function show(Receivable $receivable)
    {
        $this->checkGuest();

        if (!auth()->user()->hasPermission('financial.view') && auth()->user()->role->level !== 1) {
            abort(403);
        }

        $receivable->load(['payments', 'creator', 'incomeTransaction.documents.latestVersion']);

        return view('financial.receivables.show', compact('receivable'));
    }

    // ── Mark as Paid ───────────────────────────────────────────────────────

    public function markPaid(Receivable $receivable)
    {
        $this->checkGuest();

        if (!auth()->user()->hasPermission('financial.approve') && auth()->user()->role->level !== 1) {
            return back()->with('error', 'Permission denied.');
        }

        if ($receivable->status === 'paid') {
            return back()->with('info', 'This receivable is already marked as paid.');
        }

        // Load the ORIGINAL income transaction before we do anything
        $receivable->load('incomeTransaction');
        $originalTransaction = $receivable->incomeTransaction;

        DB::transaction(function () use ($receivable, $originalTransaction) {
            // 1. Create a new approved income transaction recording the cash collected
            $collectionTransaction = FinancialTransaction::create([
                'description'      => $receivable->description . ' (receivable collected)',
                'amount'           => $receivable->total_amount,
                'type'             => 'income',
                'status'           => 'approved',
                'transaction_date' => now()->toDateString(),
                'user_id'          => $receivable->created_by,
                'approved_by'      => Auth::id(),
                'approved_at'      => now(),
                'is_receivable'    => true,
                'receivable_paid'  => true,
                'category'         => $receivable->category ?? null,
                'notes'            => "Collected from: {$receivable->customer_name}. Ref: {$receivable->reference_no}",
            ]);

            // 2. Mark the receivable paid.
            //    Do NOT overwrite income_transaction_id — that still points to the
            //    original income entry so the show page link stays intact.
            //    Mark receivable_paid on the original transaction instead.
            $receivable->update([
                'status'   => 'paid',
                'paid_at'  => now(),
                'paid_by'  => Auth::id(),
            ]);

            // 3. Flag the original income transaction as receivable paid
            if ($originalTransaction) {
                $originalTransaction->update(['receivable_paid' => true]);
            }

            // 4. Generate and attach the receivable paid slip to the
            //    collection transaction so it shows on the receivable show page
            //    via incomeTransaction.documents (original) or separately findable.
            //    We attach it to the ORIGINAL transaction so the show page
            //    (which loads incomeTransaction = original) can display it.
            $receivable->refresh();
            $this->saveApprovedReceivableAsDocument($receivable, $originalTransaction ?? $collectionTransaction);

            AuditLogger::log(
                'updated', $receivable,
                "Receivable {$receivable->reference_no} manually marked as paid — collection transaction #{$collectionTransaction->id} created"
            );
        });

        return redirect()->route('financial.receivable.show', $receivable)
            ->with('success', 'Receivable marked as paid. The approval slip is now visible below.');
    }
    

    // ── Record Partial Payment ─────────────────────────────────────────────

    public function recordPayment(Request $request, Receivable $receivable)
    {
        // Partial payments are not supported — kept for route compatibility
        return back()->with('error', 'Partial payments are not supported. Use "Mark as Paid" instead.');
    }
}