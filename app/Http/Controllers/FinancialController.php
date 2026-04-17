<?php

namespace App\Http\Controllers;

use App\Models\FinancialTransaction;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FinancialController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.custom');
    }

    // -------------------------------------------------------------------------
    // Index — dashboard summary + transaction list
    // -------------------------------------------------------------------------

    public function index(Request $request)
    {
        $query = FinancialTransaction::with('user', 'approver')->latest('transaction_date');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }
        if ($request->filled('search')) {
            $query->where('description', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('date_from')) {
            $query->whereDate('transaction_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('transaction_date', '<=', $request->date_to);
        }

        $transactions = $query->paginate(15)->appends($request->except('page'));

        $incomeTotal   = FinancialTransaction::income()->approved()->sum('amount');
        $expenseTotal  = FinancialTransaction::expense()->approved()->sum('amount');
        $balance       = $incomeTotal - $expenseTotal;
        $pendingCount  = FinancialTransaction::pending()->count();

        $categories = FinancialTransaction::select('category')
            ->distinct()
            ->whereNotNull('category')
            ->pluck('category');

        return view('financial.index', compact(
            'transactions', 'incomeTotal', 'expenseTotal', 'balance', 'pendingCount', 'categories'
        ));
    }

    // -------------------------------------------------------------------------
    // Show
    // -------------------------------------------------------------------------

    public function show(int $id)
    {
        $transaction = FinancialTransaction::with('user', 'approver', 'documents')->findOrFail($id);
        return view('financial.show', compact('transaction'));
    }

    // -------------------------------------------------------------------------
    // Create Forms
    // -------------------------------------------------------------------------

    public function createIncome()
    {
        return view('financial.create', ['type' => 'income']);
    }

    public function createExpense()
    {
        return view('financial.create', ['type' => 'expense']);
    }

    // -------------------------------------------------------------------------
    // Store (Unified)
    // -------------------------------------------------------------------------

    public function storeIncome(Request $request)
    {
        return $this->storeTransaction($request, 'income');
    }

    public function storeExpense(Request $request)
    {
        return $this->storeTransaction($request, 'expense');
    }

    private function storeTransaction(Request $request, string $type)
    {
        $validated = $this->validateTransaction($request);
        $validated['type']    = $type;
        $validated['user_id'] = Auth::id();
        $validated['status']  = 'pending';

        DB::transaction(function () use ($validated, $request) {
            $transaction = FinancialTransaction::create($validated);

            if ($request->hasFile('receipt')) {
                $this->attachReceiptDocument($transaction, $request->file('receipt'), 'Initial upload');
            }
        });

        return redirect()->route('financial.index')
            ->with('success', ucfirst($type) . ' recorded successfully and is pending approval.');
    }

    // -------------------------------------------------------------------------
    // Edit / Update
    // -------------------------------------------------------------------------

    public function edit(int $id)
    {
        $transaction = FinancialTransaction::with('documents')->findOrFail($id);

        if ($transaction->status !== 'pending') {
            return back()->with('error', 'Only pending transactions can be edited.');
        }

        return view('financial.edit', compact('transaction'));
    }

    public function update(Request $request, int $id)
    {
        $transaction = FinancialTransaction::with('documents')->findOrFail($id);

        if ($transaction->status !== 'pending') {
            return back()->with('error', 'Only pending transactions can be edited.');
        }

        $validated = $this->validateTransaction($request);

        DB::transaction(function () use ($request, $transaction, $validated) {
            $transaction->update($validated);

            if ($request->hasFile('receipt')) {
                // Remove old receipt document(s)
                foreach ($transaction->documents as $doc) {
                    $transaction->documents()->detach($doc->id);
                    $doc->delete();
                }

                $this->attachReceiptDocument($transaction, $request->file('receipt'), 'Updated receipt');
            }
        });

        return redirect()->route('financial.index')
            ->with('success', 'Transaction updated successfully.');
    }

    // -------------------------------------------------------------------------
    // Approve / Reject
    // -------------------------------------------------------------------------

    public function approve(int $id)
    {
        $transaction = FinancialTransaction::findOrFail($id);

        if ($transaction->status !== 'pending') {
            return back()->with('error', 'Only pending transactions can be approved.');
        }

        $transaction->update([
            'status'      => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Transaction approved successfully.');
    }

    public function reject(int $id)
    {
        $transaction = FinancialTransaction::findOrFail($id);

        if ($transaction->status !== 'pending') {
            return back()->with('error', 'Only pending transactions can be rejected.');
        }

        $transaction->update(['status' => 'rejected']);

        return back()->with('success', 'Transaction rejected.');
    }

    // -------------------------------------------------------------------------
    // Destroy
    // -------------------------------------------------------------------------

    public function destroy(int $id)
    {
        $transaction = FinancialTransaction::with('documents')->findOrFail($id);

        if ($transaction->status === 'approved') {
            return back()->with('error', 'Approved transactions cannot be deleted.');
        }

        DB::transaction(function () use ($transaction) {
            // Delete attached documents (cascade deletes versions/files)
            foreach ($transaction->documents as $doc) {
                $doc->delete();
            }
            $transaction->delete();
        });

        return redirect()->route('financial.index')
            ->with('success', 'Transaction deleted successfully.');
    }

    // -------------------------------------------------------------------------
    // Private Helpers
    // -------------------------------------------------------------------------

    private function validateTransaction(Request $request): array
    {
        return $request->validate([
            'description'      => ['required', 'string', 'max:255'],
            'amount'           => ['required', 'numeric', 'min:0.01', 'max:9999999.99'],
            'category'         => ['nullable', 'string', 'max:100'],
            'transaction_date' => ['required', 'date', 'before_or_equal:today'],
            'notes'            => ['nullable', 'string', 'max:1000'],
            'receipt'          => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ], [
            'amount.min'                  => 'Amount must be greater than zero.',
            'transaction_date.before_or_equal' => 'Transaction date cannot be in the future.',
            'receipt.max'                 => 'Receipt file must not exceed 5MB.',
        ]);
            $validated['category'] = $validated['category_final'] ?? null;
             unset($validated['category_final']);

            return $validated;
    }

private function attachReceiptDocument(FinancialTransaction $transaction, $file, ?string $changeNotes = null)    {
        $document = Document::create([
            'title'       => 'Receipt: ' . $transaction->description,
            'description' => 'Attached to financial transaction #' . $transaction->id,
            'category'    => 'Financial Receipt',
            'is_public'   => false,
            'owner_id'    => Auth::id(),
        ]);

        // Assumes your Document model has an addVersion() method
        $document->addVersion($file, $changeNotes ?? 'Receipt uploaded');

        $transaction->documents()->attach($document->id);
    }
}