<?php

namespace App\Http\Controllers;

use App\Models\FinancialTransaction;
use App\Models\Document;
use App\Models\Receivable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Services\AuditLogger;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\User;

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

        $query = FinancialTransaction::with([
            'user:id,full_name,email',
            'approver:id,full_name,email',
            'auditor:id,full_name,email'
        ])->latest('transaction_date');

        // ---- NEW: Hide approved transactions unless explicitly requested ----
        $showApproved = $request->boolean('show_approved');   // false by default

        if (!$showApproved) {
            $query->where('status', '!=', 'approved');
        }
        // --------------------------------------------------------------------

        // Apply optional filters (they can be combined with the show_approved flag)
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

        // Financial totals always include approved transactions (regardless of the filter)
        $incomeTotal   = FinancialTransaction::income()->approved()->sum('amount');
        $expenseTotal  = FinancialTransaction::expense()->approved()->sum('amount');
        $balance       = $incomeTotal - $expenseTotal;
        $pendingCount  = FinancialTransaction::pending()->count();
        $auditedCount  = FinancialTransaction::audited()->count();

        $categories = FinancialTransaction::select('category')
            ->distinct()
            ->whereNotNull('category')
            ->orderBy('category')
            ->pluck('category');

        return view('financial.index', compact(
            'transactions', 'incomeTotal', 'expenseTotal', 'balance',
            'pendingCount', 'auditedCount', 'categories', 'showApproved'
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
    // Store (Income with optional Receivable)
    // -------------------------------------------------------------------------

    public function storeIncome(Request $request)
    {
        if ($response = $this->authorizeFinancialAction('create')) return $response;

        if ($request->boolean('is_receivable')) {
            $validated = $request->validate([
                'description'      => 'required|string|max:255',
                'customer_name'    => 'nullable|string|max:255',
                'receivable_total' => 'required|numeric|min:0.01',
                'due_date'         => 'nullable|date',
                'category_final'   => 'nullable|string|max:100',
                'notes'            => 'nullable|string|max:1000',
                'receipt'          => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            ]);

            DB::transaction(function () use ($validated, $request) {
                // 1. Create the income transaction (pending, not yet counted as income)
                $transaction = FinancialTransaction::create([
                    'description'      => $validated['description'],
                    'amount'           => $validated['receivable_total'],
                    'type'             => 'income',
                    'status'           => 'pending',           // goes through audit/approval
                    'transaction_date' => now(),
                    'user_id'          => Auth::id(),
                    'category'         => $validated['category_final'] ?? 'Receivable',
                    'notes'            => $validated['notes'],
                    'is_receivable'    => true,
                    'receivable_paid'  => false,
                ]);

                // 2. Create the receivable record linked to the transaction
                $receivable = Receivable::create([
                    'reference_no'           => Receivable::generateReference(),
                    'customer_name'          => $validated['customer_name'],
                    'description'            => $validated['description'],
                    'total_amount'           => $validated['receivable_total'],
                    'due_date'               => $validated['due_date'],
                    'status'                 => 'pending',
                    'created_by'             => Auth::id(),
                    'income_transaction_id'  => $transaction->id,  // link back
                ]);

                // 3. Attach receipt if uploaded
                if ($request->hasFile('receipt')) {
                    $this->attachReceiptDocument($transaction, $request->file('receipt'), 'Receivable attachment');
                }

                // 4. Link transaction to receivable
                $transaction->receivable_id = $receivable->id;
                $transaction->save();

                AuditLogger::log('created', $receivable, "Receivable created with linked transaction {$transaction->id}", [], $receivable->toArray());
            });

            return redirect()->route('financial.index')
                ->with('success', 'Receivable created. The income transaction will be audited and can be marked as paid after approval.');
        }

        // Normal income (immediate cash)
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

        $transaction = FinancialTransaction::with([
            'user', 'auditor', 'documents', 'receivable'
        ])->findOrFail($id);

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

            // Reload so approved_at / approved_by are available in the PDF
            $transaction->refresh();

            $this->saveApprovedTransactionAsDocument($transaction);

            AuditLogger::log(
                'approved',
                $transaction,
                "Transaction approved: {$transaction->description}",
                ['status' => $oldStatus],
                ['status' => 'approved']
            );
        });

        return back()->with('success', 'Transaction approved and saved to Documents successfully.');
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

        // ❌ Remove this block:
        // if ($transaction->status === 'approved') {
        //     return back()->with('error', 'Approved transactions cannot be deleted.');
        // }

        AuditLogger::log('deleted', $transaction, "Transaction: {$transaction->description}", $transaction->toArray(), []);

        DB::transaction(function () use ($transaction) {
            foreach ($transaction->documents as $doc) {
                $doc->delete();   // soft‑delete each attached document
            }
            $transaction->delete();   // soft‑delete the transaction
        });

        return redirect()->route('financial.index')
            ->with('success', 'Transaction deleted. It will now appear in the Trash.');
    }

    // -------------------------------------------------------------------------
    // Receivables Management
    // -------------------------------------------------------------------------

    public function receivablesIndex()
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

        return view('financial.receivables', compact('receivables', 'totalOutstanding'));
    }

    public function receivableShow(Receivable $receivable)
    {
        $this->checkGuest();
        if (!auth()->user()->hasPermission('financial.view') && auth()->user()->role->level !== 1) {
            abort(403);
        }
        $receivable->load(['payments', 'creator', 'incomeTransaction']);
        return view('financial.receivable-show', compact('receivable'));
    }

    public function markReceivablePaid(Receivable $receivable)
    {
        $this->checkGuest();
        if (!auth()->user()->hasPermission('financial.approve') && auth()->user()->role->level !== 1) {
            return back()->with('error', 'Permission denied.');
        }

        $transaction = $receivable->incomeTransaction;
        if (!$transaction) {
            return back()->with('error', 'Linked transaction not found.');
        }

        if ($transaction->status !== 'approved') {
            return back()->with('error', 'Transaction must be approved before marking as paid.');
        }

        if ($transaction->receivable_paid) {
            return back()->with('info', 'This receivable is already marked as paid.');
        }

        DB::transaction(function () use ($receivable, $transaction) {
            $transaction->update(['receivable_paid' => true]);
            $receivable->update(['status' => 'paid']);
            AuditLogger::log('updated', $receivable, "Receivable {$receivable->reference_no} marked as paid", [], ['status' => 'paid']);
        });

        return redirect()->route('financial.receivables')
            ->with('success', 'Receivable marked as paid. Amount now included in income.');
    }

    public function recordReceivablePayment(Request $request, Receivable $receivable)
    {
        // This method is kept for backward compatibility but not used in Option A.
        // In Option A, we don't use partial payments – we mark the whole receivable as paid.
        // If you still want to allow partial payments, uncomment the logic below.
        return back()->with('error', 'Partial payments are not supported in this version. Use "Mark as Paid" instead.');
    }

    // -------------------------------------------------------------------------
    // Report Generation (with correct receivable handling)
    // -------------------------------------------------------------------------

    public function reportForm()
    {
        $this->checkGuest();
        if (!auth()->user()->hasPermission('reports.view') && auth()->user()->role->level !== 1) {
            abort(403, 'You do not have permission to generate financial reports.');
        }
        return view('financial.report-form');
    }

    public function generateReport(Request $request)
    {
        $this->checkGuest();
        if (!auth()->user()->hasPermission('reports.view') && auth()->user()->role->level !== 1) {
            abort(403);
        }

        $validated = $request->validate([
            'start_date'     => 'required|date',
            'end_date'       => 'required|date|after_or_equal:start_date',
            'organization'   => 'nullable|string|max:255',
            'previous_cash'  => 'nullable|numeric|min:0',
        ]);

        $startDate = $validated['start_date'];
        $endDate   = $validated['end_date'];
        $orgName   = $validated['organization'] ?? '_________________________';
        $prevCash  = (float) ($validated['previous_cash'] ?? 0);

        // INCOME: approved, and either not a receivable OR a receivable that has been paid
        $incomeTotal = FinancialTransaction::where('type', 'income')
            ->where('status', 'approved')
            ->where(function ($q) {
                $q->where('is_receivable', false)
                  ->orWhere('receivable_paid', true);
            })
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('amount');

        // EXPENSES: all approved expenses
        $expenseTotal = FinancialTransaction::where('type', 'expense')
            ->where('status', 'approved')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('amount');

        $netFromOps = $incomeTotal - $expenseTotal;
        $netFinal   = $netFromOps + $prevCash;

        // OUTSTANDING RECEIVABLES: approved, receivable, not yet paid
        $receivablesTotal = FinancialTransaction::where('type', 'income')
            ->where('status', 'approved')
            ->where('is_receivable', true)
            ->where('receivable_paid', false)
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('amount');

        // Signatory names
        $auditor   = User::whereHas('role', fn($q) => $q->where('name', 'Auditor'))->where('is_active', true)->first();
        $president = User::whereHas('role', fn($q) => $q->where('name', 'President'))->where('is_active', true)->first();
        $adviser   = User::whereHas('role', fn($q) => $q->where('name', 'Club Adviser'))->where('is_active', true)->first();
        $treasurer = User::whereHas('role', fn($q) => $q->where('name', 'Treasurer'))->where('is_active', true)->first();
        $guidance  = User::whereHas('role', fn($q) => $q->where('name', 'Guidance Facilitator'))->where('is_active', true)->first();

        $data = [
            'org_name'        => $orgName,
            'start_date'      => $startDate,
            'end_date'        => $endDate,
            'income_total'    => $incomeTotal,
            'expense_total'   => $expenseTotal,
            'net_from_ops'    => $netFromOps,
            'prev_cash'       => $prevCash,
            'net_final'       => $netFinal,
            'receivables'     => $receivablesTotal,
            'generated_at'    => now(),
            'auditor_name'    => $auditor   ? $auditor->full_name   : '_________________________',
            'president_name'  => $president ? $president->full_name : '_________________________',
            'adviser_name'    => $adviser   ? $adviser->full_name   : '_________________________',
            'treasurer_name'  => $treasurer ? $treasurer->full_name : 'SHEERWINA MAE G. BALOTITE',
            'guidance_name'   => $guidance  ? $guidance->full_name  : 'NOEMI ELISA L. OQUIAS',
        ];

        $pdf = Pdf::loadView('financial.report-pdf', $data)
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

        return $pdf->download("financial_report_{$startDate}_to_{$endDate}.pdf");
    }

    public function preview(Request $request)
    {
        $this->checkGuest();
        if (!auth()->user()->hasPermission('reports.view') && auth()->user()->role->level !== 1) {
            abort(403);
        }

        $validated = $request->validate([
            'start_date'    => 'required|date',
            'end_date'      => 'required|date|after_or_equal:start_date',
            'organization'  => 'nullable|string|max:255',
            'previous_cash' => 'nullable|numeric|min:0',
        ]);

        $startDate = $validated['start_date'];
        $endDate   = $validated['end_date'];
        $orgName   = $validated['organization'] ?? '_________________________';
        $prevCash  = (float) ($validated['previous_cash'] ?? 0);

        // INCOME: approved, and either not a receivable OR a receivable that has been paid
        $incomeTotal = FinancialTransaction::where('type', 'income')
            ->where('status', 'approved')
            ->where(function ($q) {
                $q->where('is_receivable', false)
                  ->orWhere('receivable_paid', true);
            })
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('amount');

        $expenseTotal = FinancialTransaction::where('type', 'expense')
            ->where('status', 'approved')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('amount');

        $netFromOps = $incomeTotal - $expenseTotal;
        $netFinal   = $netFromOps + $prevCash;

        // OUTSTANDING RECEIVABLES
        $receivablesTotal = FinancialTransaction::where('type', 'income')
            ->where('status', 'approved')
            ->where('is_receivable', true)
            ->where('receivable_paid', false)
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('amount');

        $auditor   = User::whereHas('role', fn($q) => $q->where('name', 'Auditor'))->where('is_active', true)->first();
        $president = User::whereHas('role', fn($q) => $q->where('name', 'President'))->where('is_active', true)->first();
        $adviser   = User::whereHas('role', fn($q) => $q->where('name', 'Club Adviser'))->where('is_active', true)->first();
        $treasurer = User::whereHas('role', fn($q) => $q->where('name', 'Treasurer'))->where('is_active', true)->first();
        $guidance  = User::whereHas('role', fn($q) => $q->where('name', 'Guidance Facilitator'))->where('is_active', true)->first();

        $data = [
            'org_name'        => $orgName,
            'start_date'      => $startDate,
            'end_date'        => $endDate,
            'income_total'    => $incomeTotal,
            'expense_total'   => $expenseTotal,
            'net_from_ops'    => $netFromOps,
            'prev_cash'       => $prevCash,
            'net_final'       => $netFinal,
            'receivables'     => $receivablesTotal,
            'generated_at'    => now(),
            'auditor_name'    => $auditor   ? $auditor->full_name   : '_________________________',
            'president_name'  => $president ? $president->full_name : '_________________________',
            'adviser_name'    => $adviser   ? $adviser->full_name   : '_________________________',
            'treasurer_name'  => $treasurer ? $treasurer->full_name : '_________________________',
            'guidance_name'   => $guidance  ? $guidance->full_name  : '_________________________',
        ];

        $pdf = Pdf::loadView('financial.report-pdf', $data)
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

        return $pdf->stream("financial_report_preview.pdf");
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

    // -------------------------------------------------------------------------
    // Auto-Document Helpers (called on approval)
    // -------------------------------------------------------------------------

    /**
     * Generates a PDF approval slip and saves it into the Documents module,
     * then attaches it to the transaction via the morphToMany pivot.
     */
    private function saveApprovedTransactionAsDocument(FinancialTransaction $transaction): void
    {
        // Determine document type label
        $isReceivable  = (bool) $transaction->is_receivable;
        $typeLabel     = $isReceivable ? 'Receivable' : ucfirst($transaction->type);
        $categoryName  = "Approved {$typeLabel}"; // matches seeded DocumentCategory names

        // Resolve the DocumentCategory — fail gracefully if not seeded yet
        $documentCategory = \App\Models\DocumentCategory::where('name', $categoryName)->first();

        // Build a descriptive title
        $dateFormatted = $transaction->transaction_date->format('Y-m-d');
        $title = "{$categoryName}: {$transaction->description} [{$dateFormatted}]";

        // Render to PDF
        $pdf      = $this->buildTransactionApprovalPdf($transaction, $typeLabel, $isReceivable);
        $filename = "transaction_{$transaction->id}_{$transaction->type}_approved.pdf";
        $pdfBytes = $pdf->output();

        // Write to a temp path — addVersion() needs an UploadedFile instance
        $tempDir  = storage_path('app/temp/financial');
        if (!is_dir($tempDir)) {
            mkdir($tempDir, 0755, true);
        }
        $tempPath = "{$tempDir}/{$filename}";
        file_put_contents($tempPath, $pdfBytes);

        try {
            // Create the Document record with correct fillable fields
            $document = Document::create([
                'title'                => $title,
                'description'          => "Auto-generated approval slip for {$typeLabel} #{$transaction->id}. "
                                        . "Amount: ₱" . number_format($transaction->amount, 2) . ". "
                                        . "Approved by: " . (Auth::user()->full_name ?? Auth::user()->name)
                                        . " on " . now()->format('F j, Y g:i A') . ".",
                'document_category_id' => $documentCategory?->id,  // null-safe; nullable FK
                'tags'                 => [$transaction->type, $transaction->category ?? 'financial', 'auto-generated'],
                'owner_id'             => Auth::id(),
            ]);

            // addVersion() expects a real UploadedFile
            // The 5th argument `true` marks it as "already moved" (test mode), skipping the move
            $uploadedFile = new \Illuminate\Http\UploadedFile(
                $tempPath,
                $filename,
                'application/pdf',
                null,
                true   // test = true → treats the file as already in place, won't try to move it
            );

            $document->addVersion(
                $uploadedFile,
                "Auto-saved on approval — {$typeLabel} transaction #{$transaction->id}"
            );

            // Link document → transaction via the morphToMany pivot (attachments table)
            $transaction->documents()->attach($document->id);

            AuditLogger::log(
                'created',
                $document,
                "Approval document auto-saved for {$typeLabel} transaction #{$transaction->id}",
                [],
                ['document_id' => $document->id, 'transaction_id' => $transaction->id]
            );

        } finally {
            // Always clean up the temp file, even if something above threw
            if (file_exists($tempPath)) {
                @unlink($tempPath);
            }
        }
    }

    /**
     * Builds and returns the DomPDF instance for the approval slip.
     * Blade view: resources/views/financial/approval-slip-pdf.blade.php
     */
    private function buildTransactionApprovalPdf(
        FinancialTransaction $transaction,
        string $typeLabel,
        bool $isReceivable
    ): \Barryvdh\DomPDF\PDF {
        $transaction->loadMissing(['user', 'approver', 'auditor', 'receivable']);

        return Pdf::loadView('financial.approval-slip-pdf', [
            'transaction'   => $transaction,
            'type_label'    => $typeLabel,
            'is_receivable' => $isReceivable,
            'approved_by'   => Auth::user(),
            'generated_at'  => now(),
        ])
        ->setPaper('a4', 'portrait')
        ->setOptions([
            'defaultFont'          => 'sans-serif',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled'      => true,
            'dpi'                  => 96,
            'margin_left'          => 20,
            'margin_right'         => 20,
            'margin_top'           => 20,
            'margin_bottom'        => 20,
        ]);
    }
    // -------------------------------------------------------------------------
    // Trash / Restore / Force Delete – permission based
    // -------------------------------------------------------------------------

    public function trash()
    {
        $user = Auth::user();
        if ($user->role->level !== 1 && !$user->hasPermission('financial.manage')) {
            abort(403, 'You do not have permission to manage financial trash.');
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
        if ($user->role->level !== 1 && !$user->hasPermission('financial.manage')) {
            abort(403, 'You do not have permission to restore transactions.');
        }

        $transaction = FinancialTransaction::onlyTrashed()->findOrFail($id);

        // Restore the transaction (soft-delete undone)
        $transaction->restore();

        AuditLogger::log('restored', $transaction, "Financial transaction restored: {$transaction->description}");

        return redirect()->route('financial.trash')
            ->with('success', 'Transaction restored successfully.');
    }

    public function forceDelete($id)
    {
        $user = Auth::user();
        if ($user->role->level !== 1 && !$user->hasPermission('financial.manage')) {
            abort(403, 'You do not have permission to permanently delete transactions.');
        }

        $transaction = FinancialTransaction::onlyTrashed()->with('documents')->findOrFail($id);

        // Permanently delete attached documents first
        foreach ($transaction->documents as $doc) {
            $doc->forceDelete();   // This will remove the file (if you have file cleanup logic)
        }

        // Detach any remaining pivot records? Not needed if documents are force-deleted.

        AuditLogger::log('force_deleted', $transaction, "Financial transaction permanently deleted: {$transaction->description}", $transaction->toArray(), []);

        $transaction->forceDelete();

        return redirect()->route('financial.trash')
            ->with('success', 'Transaction permanently deleted.');
    }

    private function checkGuest()
    {
        if (auth()->user()->email === 'guest@gmail.com') {
            abort(403, 'Guest accounts cannot generate reports.');
        }
    }
}