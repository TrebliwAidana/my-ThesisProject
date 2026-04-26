<?php

namespace App\Http\Controllers\Financial;

use App\Http\Controllers\Controller;
use App\Models\FinancialTransaction;
use App\Models\Receivable;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class IncomeController extends Controller
{
    use FinancialHelperTrait;

    public function __construct()
    {
        $this->middleware('auth.custom');
    }

    // ── Create ─────────────────────────────────────────────────────────────

    public function create()
    {
        if ($response = $this->authorizeFinancialAction('create')) return $response;

        return view('financial.income.create');
    }

    // ── Store ──────────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        if ($response = $this->authorizeFinancialAction('create')) return $response;

        $validated = $this->validateTransaction($request);

        // Only treat as receivable if BOTH the checkbox AND amount are present
        $hasReceivable    = $request->boolean('has_receivable')
                            && filled($request->input('receivable_amount'));
        $cashAmount       = $validated['amount'];
        $receivableAmount = 0;

        if ($hasReceivable) {
            $request->validate([
                'receivable_amount' => 'required|numeric|min:0.01',
                'customer_name'     => 'required|string|max:255',
                'due_date'          => 'nullable|date',
            ]);

            $receivableAmount = (float) $request->input('receivable_amount');
        }

        DB::transaction(function () use ($request, $validated, $cashAmount, $hasReceivable, $receivableAmount) {
            $transaction = FinancialTransaction::create([
                'description'      => $validated['description'],
                'amount'           => $cashAmount,
                'type'             => 'income',
                'status'           => 'pending',
                'transaction_date' => $validated['transaction_date'],
                'user_id'          => Auth::id(),
                'category'         => $validated['category'] ?? null,
                'notes'            => $validated['notes'] ?? null,
                'is_receivable'    => false,
                'receivable_paid'  => false,
            ]);

            if ($hasReceivable && $receivableAmount > 0) {
                $receivable = Receivable::create([
                    'reference_no'          => Receivable::generateReference(),
                    'customer_name'         => $request->input('customer_name'),
                    'description'           => $validated['description'] . ' (receivable portion)',
                    'total_amount'          => $receivableAmount,
                    'due_date'              => $request->input('due_date'),
                    'status'                => 'pending',
                    'created_by'            => Auth::id(),
                    'income_transaction_id' => $transaction->id,
                    'category'              => $validated['category'] ?? null,
                ]);

                $transaction->receivable_id = $receivable->id;
                $transaction->save();

                AuditLogger::log(
                    'created', $receivable,
                    "Receivable created for transaction #{$transaction->id}"
                );
            }

            if ($request->hasFile('receipt')) {
                $this->attachReceiptDocument($transaction, $request->file('receipt'), 'Receipt uploaded');
            }

            AuditLogger::log(
                'created', $transaction,
                "Income transaction: {$transaction->description} (cash: ₱{$cashAmount})"
                . ($hasReceivable ? " + receivable: ₱{$receivableAmount}" : "")
            );
        });

        $message = 'Income recorded successfully.';
        if ($hasReceivable) {
            $message .= ' A receivable has also been created and will be tracked separately.';
        }

        return redirect()->route('financial.index')->with('success', $message);
    }
}
