<?php

namespace App\Http\Controllers\Financial;

use App\Http\Controllers\Controller;
use App\Models\FinancialTransaction;
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

        DB::transaction(function () use ($request, $validated) {
            $transaction = FinancialTransaction::create([
                'type'             => 'income',
                'status'           => 'pending',
                'user_id'          => Auth::id(),
                'description'      => $validated['description'],
                'amount'           => $validated['amount'],
                'category'         => $validated['category'] ?? null,
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
                "Income transaction: {$transaction->description}",
                [],
                $transaction->toArray()
            );
        });

        return redirect()->route('financial.index')
            ->with('success', 'Income recorded successfully and is pending audit.');
    }
}