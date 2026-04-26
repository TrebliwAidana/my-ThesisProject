<?php

namespace App\Http\Controllers\Financial;

use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\FinancialTransaction;
use App\Models\Receivable;
use App\Services\AuditLogger;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

trait FinancialHelperTrait
{
    // ── Guest Guard ────────────────────────────────────────────────────────

    protected function checkGuest(): void
    {
        if (auth()->user()->email === 'guest@gmail.com') {
            abort(403, 'Guest accounts cannot perform this action.');
        }
    }

    // ── Permission Guard ───────────────────────────────────────────────────

    /**
     * Returns a RedirectResponse if the user is unauthorized, or null if OK.
     * Usage:  if ($response = $this->authorizeFinancialAction('create')) return $response;
     */
    protected function authorizeFinancialAction(string $action): ?RedirectResponse
    {
        $user = Auth::user();

        if ($user->email === 'guest@gmail.com') {
            return redirect()->route('dashboard')
                ->with('error', 'Guest accounts cannot perform financial actions.');
        }

        $map = [
            'create'  => 'financial.create',
            'edit'    => 'financial.edit',
            'delete'  => 'financial.delete',
            'approve' => 'financial.approve',
        ];

        $permission = $map[$action] ?? null;

        if ($permission && !$user->hasPermission($permission) && $user->role->level !== 1) {
            return back()->with('error', "You do not have permission to {$action} financial records.");
        }

        return null;
    }

    // ── Validation ─────────────────────────────────────────────────────────

    protected function validateTransaction(Request $request): array
    {
        $validated = $request->validate([
            'description'      => ['required', 'string', 'max:255'],
            'amount'           => ['required', 'numeric', 'min:0.01', 'max:9999999.99'],
            'category_final'   => ['nullable', 'string', 'max:100'],
            'transaction_date' => ['required', 'date', 'before_or_equal:today'],
            'notes'            => ['nullable', 'string', 'max:1000'],
            'receipt'          => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ], [
            'amount.min'                       => 'Amount must be greater than zero.',
            'transaction_date.before_or_equal' => 'Transaction date cannot be in the future.',
            'receipt.max'                      => 'Receipt file must not exceed 5MB.',
        ]);

        // Normalize the category field name
        $validated['category'] = $validated['category_final'] ?? null;
        unset($validated['category_final']);

        return $validated;
    }

    // ── Receipt Attachment ─────────────────────────────────────────────────

    protected function attachReceiptDocument(
        FinancialTransaction $transaction,
        $file,
        ?string $changeNotes = null
    ): void {
        $document = Document::create([
            'title'       => 'Receipt: ' . $transaction->description,
            'description' => 'Attached to financial transaction #' . $transaction->id,
            'category'    => 'Financial Receipt',
            'is_public'   => false,
            'owner_id'    => Auth::id(),
        ]);

        $document->addVersion($file, $changeNotes ?? 'Receipt uploaded');
        $transaction->documents()->syncWithoutDetaching([$document->id]);
    }

    // ── Approval PDF Helpers ───────────────────────────────────────────────

    /**
     * Generates and saves the income/expense approval PDF to Documents.
     */
    protected function saveApprovedTransactionAsDocument(FinancialTransaction $transaction): void
    {
        $isReceivable = (bool) $transaction->is_receivable;
        $typeLabel    = $isReceivable ? 'Receivable' : ucfirst($transaction->type);
        $categoryName = "Approved {$typeLabel}";

        $documentCategory = DocumentCategory::where('name', $categoryName)->first();
        $dateFormatted    = $transaction->transaction_date->format('Y-m-d');
        $title            = "{$categoryName}: {$transaction->description} [{$dateFormatted}]";

        $pdf      = $this->buildTransactionApprovalPdf($transaction, $typeLabel, $isReceivable);
        $filename = "transaction_{$transaction->id}_{$transaction->type}_approved.pdf";

        $this->writePdfToDocument($pdf, $filename, $title, $transaction, $typeLabel, $documentCategory);
    }

    /**
     * Generates and saves the receivable paid PDF to Documents,
     * attaching it to the linked income transaction so it appears
     * on the receivable show page via incomeTransaction.documents.
     */
    protected function saveApprovedReceivableAsDocument(
        Receivable $receivable,
        FinancialTransaction $linkedTransaction
    ): void {
        $categoryName     = 'Approved Receivable';
        $documentCategory = DocumentCategory::where('name', $categoryName)->first();
        $title            = "Approved Receivable: {$receivable->description} [Ref: {$receivable->reference_no}]";
        $filename         = "receivable_{$receivable->id}_paid.pdf";

        $tempDir  = storage_path('app/temp/financial');
        if (!is_dir($tempDir)) mkdir($tempDir, 0755, true);
        $tempPath = "{$tempDir}/{$filename}";

        // Generate and write the PDF to disk first
        $pdf = Pdf::loadView('financial.approval-slip-pdf', [
            'transaction'   => $linkedTransaction,
            'type_label'    => 'Receivable',
            'is_receivable' => true,
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

        file_put_contents($tempPath, $pdf->output());

        // Ensure the file actually exists before proceeding
        if (!file_exists($tempPath) || filesize($tempPath) === 0) {
            \Illuminate\Support\Facades\Log::error("Failed to write receivable PDF to {$tempPath}");
            return;
        }

        try {
            $document = Document::create([
                'title'                => $title,
                'description'          => "Auto-generated receivable paid slip for Ref #{$receivable->reference_no}. "
                                        . "Amount: ₱" . number_format($receivable->total_amount, 2) . ". "
                                        . "Paid on: " . now()->format('F j, Y g:i A') . ".",
                'document_category_id' => $documentCategory?->id,
                'tags'                 => ['receivable', 'auto-generated', 'paid'],
                'owner_id'             => Auth::id(),
            ]);

            // Build the UploadedFile BEFORE the finally block can delete it
            $uploadedFile = new \Illuminate\Http\UploadedFile(
                $tempPath,
                $filename,
                'application/pdf',
                null,
                true  // test mode — skips is_uploaded_file() check
            );

            $document->addVersion(
                $uploadedFile,
                "Auto-saved — Receivable #{$receivable->reference_no} paid via transaction #{$linkedTransaction->id}"
            );

            // syncWithoutDetaching prevents duplicate pivot rows
            $linkedTransaction->documents()->syncWithoutDetaching([$document->id]);

            AuditLogger::log(
                'created', $document,
                "Receivable paid slip saved for Ref #{$receivable->reference_no}",
                [],
                ['document_id' => $document->id, 'receivable_id' => $receivable->id]
            );

        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error(
                "saveApprovedReceivableAsDocument failed for receivable #{$receivable->id}: " . $e->getMessage()
            );
            throw $e; // re-throw so the DB::transaction rolls back
        } finally {
            if (file_exists($tempPath)) @unlink($tempPath);
        }
    }

    /**
     * Builds the DomPDF instance for a transaction approval slip.
     */
    protected function buildTransactionApprovalPdf(
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

    /**
     * Writes a DomPDF instance to a temp file, creates a Document record,
     * versions it, and attaches it to the transaction via the pivot.
     */
    protected function writePdfToDocument(
        \Barryvdh\DomPDF\PDF $pdf,
        string $filename,
        string $title,
        FinancialTransaction $transaction,
        string $typeLabel,
        ?DocumentCategory $documentCategory
    ): void {
        $tempDir  = storage_path('app/temp/financial');
        if (!is_dir($tempDir)) mkdir($tempDir, 0755, true);
        $tempPath = "{$tempDir}/{$filename}";
        file_put_contents($tempPath, $pdf->output());

        try {
            $document = Document::create([
                'title'                => $title,
                'description'          => "Auto-generated approval slip for {$typeLabel} #{$transaction->id}. "
                                        . "Amount: ₱" . number_format($transaction->amount, 2) . ". "
                                        . "Approved by: " . Auth::user()->full_name
                                        . " on " . now()->format('F j, Y g:i A') . ".",
                'document_category_id' => $documentCategory?->id,
                'tags'                 => [$transaction->type, $transaction->category ?? 'financial', 'auto-generated'],
                'owner_id'             => Auth::id(),
            ]);

            $uploadedFile = new \Illuminate\Http\UploadedFile(
                $tempPath, $filename, 'application/pdf', null, true
            );

            $document->addVersion(
                $uploadedFile,
                "Auto-saved on approval — {$typeLabel} transaction #{$transaction->id}"
            );

            // syncWithoutDetaching prevents duplicate pivot rows if addVersion
            // internally also attaches (depends on Document implementation)
            $transaction->documents()->syncWithoutDetaching([$document->id]);

            AuditLogger::log(
                'created', $document,
                "Approval document auto-saved for {$typeLabel} transaction #{$transaction->id}",
                [],
                ['document_id' => $document->id, 'transaction_id' => $transaction->id]
            );

        } finally {
            if (file_exists($tempPath)) @unlink($tempPath);
        }
    }
}