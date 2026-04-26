<?php

namespace App\Http\Controllers\Financial;

use App\Http\Controllers\Controller;
use App\Models\FinancialTransaction;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
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

        return view('financial.expense.create');
    }

    // ── Store ──────────────────────────────────────────────────────────────

    public function store(Request $request)
    {
        if ($response = $this->authorizeFinancialAction('create')) return $response;

        $validated            = $this->validateTransaction($request);
        $validated['type']    = 'expense';
        $validated['user_id'] = Auth::id();
        $validated['status']  = 'pending';

        DB::transaction(function () use ($validated, $request) {
            $transaction = FinancialTransaction::create($validated);

            if ($request->hasFile('receipt')) {
                $this->attachReceiptDocument(
                    $transaction,
                    $request->file('receipt'),
                    'Initial upload'
                );
            }

            AuditLogger::log(
                'created', $transaction,
                "Transaction: {$transaction->description}",
                [],
                $transaction->toArray()
            );
        });

        return redirect()->route('financial.index')
            ->with('success', 'Expense recorded successfully and is pending review.');
    }
}
