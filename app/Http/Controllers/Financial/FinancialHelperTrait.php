<?php

namespace App\Http\Controllers\Financial;

use App\Models\Document;
use App\Models\DocumentCategory;
use App\Models\FinancialTransaction;
use App\Models\Receivable;
use App\Services\AuditLogger;
use App\Services\CloudinaryService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

trait FinancialHelperTrait
{
    // ── Guest Guard ────────────────────────────────────────────────────────

    protected function checkGuest(): void
    {
        if (Auth::user()->email === 'guest@gmail.com') {
            abort(403, 'Guest accounts cannot perform this action.');
        }
    }

    // ── Permission Guard ───────────────────────────────────────────────────

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

        if ($permission && !$user->hasPermission($permission) && (int) $user->role->level !== 1) {
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
        // ✅ Upload receipt to Cloudinary
        $cloudinary = new CloudinaryService();
        $uploaded   = $cloudinary->upload($file, 'vsulhs-sslg/receipts');

        $document = Document::create([
            'title'       => 'Receipt: ' . $transaction->description,
            'description' => 'Attached to financial transaction #' . $transaction->id,
            'tags'        => ['receipt', 'financial'],
            'owner_id'    => Auth::id(),
        ]);

        // ✅ Use new addVersion() signature with Cloudinary URL
        $document->addVersion(
            $uploaded['url'],
            $uploaded['public_id'],
            $changeNotes ?? 'Receipt uploaded',
            $file->getSize(),
            $file->getClientOriginalName(),
            $file->getMimeType(),
        );

        $transaction->documents()->syncWithoutDetaching([$document->id]);
    }

    // ── Approval PDF Helpers ───────────────────────────────────────────────

    protected function saveApprovedTransactionAsDocument(
        FinancialTransaction $transaction,
        ?\App\Models\User $actorUser = null
    ): void {
        $isReceivable = $transaction->type === 'receivable';
        $typeLabel    = $isReceivable ? 'Receivable' : ucfirst($transaction->type);
        $categoryName = $isReceivable ? 'Approved Receivable' : "Approved {$typeLabel}";

        $documentCategory = DocumentCategory::where('name', $categoryName)->first();
        $dateFormatted    = $transaction->transaction_date->format('Y-m-d');
        $title            = "{$categoryName}: {$transaction->description} [{$dateFormatted}]";
        $filename         = "transaction_{$transaction->id}_{$transaction->type}_paid.pdf";

        $pdf = $this->buildTransactionApprovalPdf($transaction, $typeLabel, $isReceivable, $actorUser);

        $this->writePdfToDocument($pdf, $filename, $title, $transaction, $typeLabel, $documentCategory, $actorUser);
    }

    protected function saveApprovedReceivableAsDocument(
        Receivable $receivable,
        FinancialTransaction $linkedTransaction
    ): void {
        $categoryName     = 'Approved Receivable';
        $documentCategory = DocumentCategory::where('name', $categoryName)->first();
        $title            = "Approved Receivable: {$receivable->description} [Ref: {$receivable->reference_no}]";
        $filename         = "receivable_{$receivable->id}_paid.pdf";

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

        // ✅ Write PDF to temp file then upload to Cloudinary
        $tempPath = $this->writePdfToTemp($filename, $pdf);

        if (! $tempPath) return;

        try {
            $cloudinary = new CloudinaryService();
            $uploaded   = $cloudinary->upload(
                new \Illuminate\Http\UploadedFile($tempPath, $filename, 'application/pdf', null, true),
                'vsulhs-sslg/financial'
            );

            $document = Document::create([
                'title'                => $title,
                'description'          => "Auto-generated receivable paid slip for Ref #{$receivable->reference_no}. "
                                        . "Amount: ₱" . number_format($receivable->total_amount, 2) . ". "
                                        . "Paid on: " . now()->format('F j, Y g:i A') . ".",
                'document_category_id' => $documentCategory?->id,
                'tags'                 => ['receivable', 'auto-generated', 'paid'],
                'owner_id'             => Auth::id(),
            ]);

            // ✅ New addVersion() signature
            $document->addVersion(
                $uploaded['url'],
                $uploaded['public_id'],
                "Auto-saved — Receivable #{$receivable->reference_no} paid via transaction #{$linkedTransaction->id}",
                filesize($tempPath),
                $filename,
                'application/pdf',
            );

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
            throw $e;
        } finally {
            if (file_exists($tempPath)) @unlink($tempPath);
        }
    }

    protected function buildTransactionApprovalPdf(
        FinancialTransaction $transaction,
        string $typeLabel,
        bool $isReceivable,
        ?\App\Models\User $actorUser = null
    ): \Barryvdh\DomPDF\PDF {
        $transaction->loadMissing(['user', 'approver', 'auditor']);

        return Pdf::loadView('financial.approval-slip-pdf', [
            'transaction'   => $transaction,
            'type_label'    => $typeLabel,
            'is_receivable' => $isReceivable,
            'approved_by'   => $actorUser ?? Auth::user(),
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

    protected function writePdfToDocument(
        \Barryvdh\DomPDF\PDF $pdf,
        string $filename,
        string $title,
        FinancialTransaction $transaction,
        string $typeLabel,
        ?DocumentCategory $documentCategory,
        ?\App\Models\User $actorUser = null
    ): void {
        $actor    = $actorUser ?? Auth::user();
        $tempPath = $this->writePdfToTemp($filename, $pdf);

        if (! $tempPath) return;

        try {
            // ✅ Upload PDF to Cloudinary
            $cloudinary = new CloudinaryService();
            $uploaded   = $cloudinary->upload(
                new \Illuminate\Http\UploadedFile($tempPath, $filename, 'application/pdf', null, true),
                'vsulhs-sslg/financial'
            );

            $document = Document::create([
                'title'                => $title,
                'description'          => "Auto-generated approval slip for {$typeLabel} #{$transaction->id}. "
                                        . "Amount: ₱" . number_format($transaction->amount, 2) . ". "
                                        . "Approved by: " . ($actor?->full_name ?? 'System')
                                        . " on " . now()->format('F j, Y g:i A') . ".",
                'document_category_id' => $documentCategory?->id,
                'tags'                 => [$transaction->type, $transaction->category ?? 'financial', 'auto-generated'],
                'owner_id'             => $actor?->id ?? Auth::id(),
            ]);

            // ✅ New addVersion() signature
            $document->addVersion(
                $uploaded['url'],
                $uploaded['public_id'],
                "Auto-saved on approval — {$typeLabel} transaction #{$transaction->id}",
                filesize($tempPath),
                $filename,
                'application/pdf',
            );

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

    // ── Private Helpers ────────────────────────────────────────────────────

    // ✅ New helper — writes PDF to temp file, returns path or null on failure
    private function writePdfToTemp(string $filename, \Barryvdh\DomPDF\PDF $pdf): ?string
    {
        $tempDir  = storage_path('app/temp/financial');
        if (! is_dir($tempDir)) mkdir($tempDir, 0755, true);
        $tempPath = "{$tempDir}/{$filename}";

        file_put_contents($tempPath, $pdf->output());

        if (! file_exists($tempPath) || filesize($tempPath) === 0) {
            \Illuminate\Support\Facades\Log::error("Failed to write PDF to {$tempPath}");
            return null;
        }

        return $tempPath;
    }
}