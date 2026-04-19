<?php

namespace App\Http\Controllers;

use App\Models\FinancialTransaction;
use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Services\AuditLogger;

class FinancialController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth.custom');
    }

    // -------------------------------------------------------------------------
    // Index
    // -------------------------------------------------------------------------

    public function index(Request $request)
    {
        $user = Auth::user();

        // Guest block
        if ($user->email === 'guest@gmail.com') {
            return redirect()->route('dashboard')
                ->with('error', 'Guest accounts cannot view financial records.');
        }

        // Permission check (System Admin level 1 bypasses)
        if ($user->role->level !== 1 && !$user->hasPermission('financial.view')) {
            abort(403, 'You do not have permission to view financial records.');
        }

        $query = FinancialTransaction::with(['user', 'approver', 'auditor'])->latest('transaction_date');

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
        $auditedCount  = FinancialTransaction::audited()->count();

        $categories = FinancialTransaction::select('category')
            ->distinct()
            ->whereNotNull('category')
            ->pluck('category');

        return view('financial.index', compact(
            'transactions', 'incomeTotal', 'expenseTotal', 'balance',
            'pendingCount', 'auditedCount', 'categories'
        ));
    }

    // -------------------------------------------------------------------------
    // Show
    // -------------------------------------------------------------------------

    public function show(int $id)
    {
        $user = Auth::user();

        if ($user->email === 'guest@gmail.com') {
            return redirect()->route('dashboard')
                ->with('error', 'Guest accounts cannot view financial records.');
        }

        if ($user->role->level !== 1 && !$user->hasPermission('financial.view')) {
            abort(403, 'You do not have permission to view financial records.');
        }

        $transaction = FinancialTransaction::with(['user', 'approver', 'auditor', 'documents'])->findOrFail($id);
        return view('financial.show', compact('transaction'));
    }

    // -------------------------------------------------------------------------
    // Create Forms
    // -------------------------------------------------------------------------

    public function createIncome()
    {
        if ($response = $this->authorizeFinancialAction('create')) return $response;
        return view('financial.create', ['type' => 'income']);
    }

    public function createExpense()
    {
        if ($response = $this->authorizeFinancialAction('create')) return $response;
        return view('financial.create', ['type' => 'expense']);
    }

    // -------------------------------------------------------------------------
    // Store
    // -------------------------------------------------------------------------

    public function storeIncome(Request $request)
    {
        if ($response = $this->authorizeFinancialAction('create')) return $response;
        return $this->storeTransaction($request, 'income');
    }

    public function storeExpense(Request $request)
    {
        if ($response = $this->authorizeFinancialAction('create')) return $response;
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

            AuditLogger::log('created', $transaction, "Transaction: {$transaction->description}", [], $transaction->toArray());
        });

        return redirect()->route('financial.index')
            ->with('success', ucfirst($type) . ' recorded successfully and is pending review.');
    }

    // -------------------------------------------------------------------------
    // Edit / Update
    // -------------------------------------------------------------------------

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
        $oldData = $transaction->getOriginal();

        DB::transaction(function () use ($request, $transaction, $validated, $oldData) {
            $transaction->update($validated);

            if ($request->hasFile('receipt')) {
                foreach ($transaction->documents as $doc) {
                    $transaction->documents()->detach($doc->id);
                    $doc->delete();
                }
                $this->attachReceiptDocument($transaction, $request->file('receipt'), 'Updated receipt');
            }

            AuditLogger::log('updated', $transaction, "Transaction: {$transaction->description}", $oldData, $transaction->getChanges());
        });

        return redirect()->route('financial.index')
            ->with('success', 'Transaction updated successfully.');
    }

    // -------------------------------------------------------------------------
    // Audit (Auditor only)
    // -------------------------------------------------------------------------

    public function audit(Request $request, int $id)
    {
        $user = Auth::user();
        if ($user->email === 'guest@gmail.com') {
            return redirect()->route('dashboard')->with('error', 'Guest accounts cannot perform this action.');
        }

        if (!$user->hasPermission('financial.audit') && $user->role->level !== 1) {
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

        AuditLogger::log('audited', $transaction, "Transaction audited: {$transaction->description}", ['status' => $oldStatus], ['status' => 'audited']);

        return back()->with('success', 'Transaction marked as audited. Awaiting final approval.');
    }

    // -------------------------------------------------------------------------
    // Final Approve / Reject (Adviser/Admin)
    // -------------------------------------------------------------------------

    public function approve(int $id)
    {
        if ($response = $this->authorizeFinancialAction('approve')) return $response;

        $transaction = FinancialTransaction::findOrFail($id);

        if ($transaction->status !== 'audited') {
            return back()->with('error', 'Transaction must be audited before final approval.');
        }

        $oldStatus = $transaction->status;
        $transaction->update([
            'status'      => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        AuditLogger::log('approved', $transaction, "Transaction approved: {$transaction->description}", ['status' => $oldStatus], ['status' => 'approved']);

        return back()->with('success', 'Transaction approved successfully.');
    }

    public function reject(int $id)
    {
        if ($response = $this->authorizeFinancialAction('approve')) return $response;

        $transaction = FinancialTransaction::findOrFail($id);

        if (in_array($transaction->status, ['approved', 'rejected'])) {
            return back()->with('error', 'This transaction has already been finalized.');
        }

        $oldStatus = $transaction->status;
        $transaction->update(['status' => 'rejected']);

        AuditLogger::log('rejected', $transaction, "Transaction rejected: {$transaction->description}", ['status' => $oldStatus], ['status' => 'rejected']);

        return back()->with('success', 'Transaction rejected.');
    }

    // -------------------------------------------------------------------------
    // Destroy
    // -------------------------------------------------------------------------

    public function destroy(int $id)
    {
        if ($response = $this->authorizeFinancialAction('delete')) return $response;
        $transaction = FinancialTransaction::with('documents')->findOrFail($id);

        if ($transaction->status === 'approved') {
            return back()->with('error', 'Approved transactions cannot be deleted.');
        }

        AuditLogger::log('deleted', $transaction, "Transaction: {$transaction->description}", $transaction->toArray(), []);

        DB::transaction(function () use ($transaction) {
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

    private function authorizeFinancialAction(string $action)
    {
        $user = Auth::user();

        if ($user->email === 'guest@gmail.com') {
            return redirect()->route('dashboard')
                ->with('error', 'Guest accounts cannot perform financial actions.');
        }

        $permissionMap = [
            'create'  => 'financial.create',
            'edit'    => 'financial.edit',
            'delete'  => 'financial.delete',
            'approve' => 'financial.approve',
        ];

        $permission = $permissionMap[$action] ?? null;

        if ($permission && !$user->hasPermission($permission) && $user->role->level !== 1) {
            return back()->with('error', "You do not have permission to {$action} financial records.");
        }

        return null;
    }

    private function validateTransaction(Request $request): array
    {
        $validated = $request->validate([
            'description'      => ['required', 'string', 'max:255'],
            'amount'           => ['required', 'numeric', 'min:0.01', 'max:9999999.99'],
            'category_final'   => ['nullable', 'string', 'max:100'],
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

    private function attachReceiptDocument(FinancialTransaction $transaction, $file, ?string $changeNotes = null)
    {
        $document = Document::create([
            'title'       => 'Receipt: ' . $transaction->description,
            'description' => 'Attached to financial transaction #' . $transaction->id,
            'category'    => 'Financial Receipt',
            'is_public'   => false,
            'owner_id'    => Auth::id(),
        ]);

        $document->addVersion($file, $changeNotes ?? 'Receipt uploaded');
        $transaction->documents()->attach($document->id);
    }
}