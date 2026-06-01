<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

use App\Models\Document;
use App\Models\DocumentCategory;

Artisan::command('fix:doc-categories', function () {
    $expense    = DocumentCategory::where('name', 'Approved Expense')->first();
    $receivable = DocumentCategory::where('name', 'Approved Receivable')->first();
    $income     = DocumentCategory::where('name', 'Approved Income')->first();

    $fixed = 0;

    Document::whereNull('document_category_id')
        ->whereJsonContains('tags', 'auto-generated')
        ->each(function ($doc) use ($expense, $receivable, $income, &$fixed) {
            if (str_contains($doc->title, 'Expense') && $expense) {
                $doc->document_category_id = $expense->id;
            } elseif (str_contains($doc->title, 'Receivable') && $receivable) {
                $doc->document_category_id = $receivable->id;
            } elseif (str_contains($doc->title, 'Income') && $income) {
                $doc->document_category_id = $income->id;
            } else {
                return;
            }
            $doc->saveQuietly();
            $fixed++;
        });

    $this->info("Done. Fixed {$fixed} document(s).");
});