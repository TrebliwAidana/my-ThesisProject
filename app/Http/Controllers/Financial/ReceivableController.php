<?php

namespace App\Http\Controllers\Financial;

use App\Http\Controllers\Controller;
use App\Models\FinancialTransaction;
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

    // ── Create ─────────────────────────────────────────────────────────────

    public function create()
    {
        if ($response = $this->authorizeFinancialAction('create')) return $response;

        return view('financial.receivables.create');
    }

    // ── Store ──────────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        if ($response = $this->authorizeFinancialAction('create')) return $response;

        $validated = $request->validate([
            'description'      => ['required', 'string', 'max:255'],
            'amount'           => ['required', 'numeric', 'min:0.01', 'max:9999999.99'],
            'customer_name'    => ['required', 'string', 'max:255'],
            'due_date'         => ['nullable', 'date', 'after_or_equal:today'],
            'category_final'   => ['nullable', 'string', 'max:100'],
            'transaction_date' => ['required', 'date', 'before_or_equal:today'],
            'notes'            => ['nullable', 'string', 'max:1000'],
            'receipt'          => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ], [
            'amount.min'                       => 'Amount must be greater than zero.',
            'due_date.after_or_equal'          => 'Due date cannot be in the past.',
            'transaction_date.before_or_equal' => 'Transaction date cannot be in the future.',
            'receipt.max'                      => 'Receipt file must not exceed 5MB.',
        ]);

        DB::transaction(function () use ($validated, $request) {
            $transaction = FinancialTransaction::create([
                'type'             => 'receivable',
                'status'           => 'pending',
                'user_id'          => Auth::id(),
                'description'      => $validated['description'],
                'amount'           => $validated['amount'],
                'customer_name'    => $validated['customer_name'],
                'due_date'         => $validated['due_date'] ?? null,
                'category'         => $validated['category_final'] ?? null,
                'transaction_date' => $validated['transaction_date'],
                'notes'            => $validated['notes'] ?? null,
            ]);

            if ($request->hasFile('receipt')) {
                $this->attachReceiptDocument(
                    $transaction,
                    $request->file('receipt'),
                    'Receipt uploaded'
                );
            }

            AuditLogger::log(
                'created', $transaction,
                "Receivable created: {$transaction->description} — Customer: {$transaction->customer_name}",
                [],
                $transaction->toArray()
            );
        });

        return redirect()->route('financial.index')
            ->with('success', 'Receivable recorded successfully and is pending audit.');
    }
}
