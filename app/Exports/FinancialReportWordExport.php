<?php

namespace App\Exports;

use App\Models\FinancialTransaction;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\SimpleType\TblWidth;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Style\Table;

class FinancialReportWordExport
{
    public function __construct(private array $validated) {}

    public function build(): string
    {
        $phpWord = new PhpWord();
        $phpWord->setDefaultFontName('Times New Roman');
        $phpWord->setDefaultFontSize(11);

        $section = $phpWord->addSection([
            'marginTop'    => 720,
            'marginBottom' => 720,
            'marginLeft'   => 1080,
            'marginRight'  => 1080,
        ]);

        $center  = ['alignment' => 'center'];
        $bold    = ['bold' => true];
        $boldSm  = ['bold' => true, 'size' => 9];
        $italic  = ['italic' => true, 'size' => 9];

        $incomeTotal  = $this->getIncomeTotal();
        $expenseTotal = $this->getExpenseTotal();
        $prevCash     = (float) ($this->validated['previous_cash'] ?? 0);
        $netFromOps   = $incomeTotal - $expenseTotal;
        $netFinal     = $netFromOps + $prevCash;
        $receivables  = $this->getReceivablesTotal();
        $org          = $this->validated['organization'] ?? '_________________________';
        $startDate    = \Carbon\Carbon::parse($this->validated['start_date'])->format('F d, Y');
        $endDate      = \Carbon\Carbon::parse($this->validated['end_date'])->format('F d, Y');

        // ── Header ────────────────────────────────────────────────────────
        $section->addText('VSU INTEGRATED HIGH SCHOOL', $bold, $center);
        $section->addText('Baybay City, Leyte', [], $center);
        $section->addText('─────────────────────────────────────────', ['size' => 8], $center);
        $section->addText('OFFICE OF THE VSUIHS GUIDANCE FACILITATOR', $bold, $center);
        $section->addTextBreak(1);

        // ── Title ─────────────────────────────────────────────────────────
        $section->addText('FINANCIAL REPORT', ['bold' => true, 'size' => 13], $center);
        $section->addText('of the ' . $org, [], $center);
        $section->addText('For the period ' . $startDate . ' to ' . $endDate, ['italic' => true, 'size' => 10], $center);
        $section->addTextBreak(1);

        // ── Financial table ───────────────────────────────────────────────
        $tableStyle = [
            'borderSize'  => 6,
            'borderColor' => '000000',
            'cellMargin'  => 80,
            'width'       => 100,
            'unit'        => TblWidth::PERCENT,
        ];
        $headerCellBg = ['bgColor' => 'D9D9D9'];

        $table = $section->addTable($tableStyle);

        // Table header row
        $table->addRow();
        $table->addCell(6000, $headerCellBg)->addText('Details',       $bold, $center);
        $table->addCell(2000, $headerCellBg)->addText('Amount (PHP)',   $bold, $center);
        $table->addCell(2000, $headerCellBg)->addText('Remarks',        $bold, $center);

        // Data rows
        $rows = [
            ['A. Cash on Hand',          '(attach list)',    $incomeTotal,  false],
            ['B. Less: Expenses',        '(attach receipts)',$expenseTotal, false],
            ['C. Total Income',          '(A minus B)',      $netFromOps,   false],
            ['D. Receivables',           '(attach list)',    $receivables,  false],
            ['E. Previous Cash Deposited','',                $prevCash,     false],
            ['NET INCOME',               '(C + E)',          $netFinal,     true],
        ];

        foreach ($rows as [$label, $sub, $amount, $isBold]) {
            $font = $isBold ? $bold : [];
            $table->addRow();
            $cell = $table->addCell(6000);
            $cell->addText($label, $isBold ? $bold : ['bold' => true]);
            if ($sub) $cell->addText($sub, $italic);
            $table->addCell(2000)->addText('₱ ' . number_format($amount, 2), $font, ['alignment' => 'right']);
            $table->addCell(2000)->addText('');
        }

        $section->addTextBreak(1);

        // ── Signatures ────────────────────────────────────────────────────
        $section->addText('Certified True and Correct:', $bold, $center);
        $section->addTextBreak(1);

        $sigTable = $section->addTable(['unit' => TblWidth::PERCENT, 'width' => 100]);

        // Treasurer (centered, full width)
        $treasurerName = $this->validated['treasurer_name'] ?? 'SHEERWINA MAE G. BALOTITE';
        $sigTable->addRow();
        $sigTable->addCell(3000)->addText('');
        $cell = $sigTable->addCell(4000, ['borderBottomSize' => 6, 'borderBottomColor' => '000000']);
        $cell->addText($treasurerName, $bold, $center);
        $sigTable->addCell(3000)->addText('');

        $sigTable->addRow();
        $sigTable->addCell(3000)->addText('');
        $sigTable->addCell(4000)->addText('Treasurer', [], $center);
        $sigTable->addCell(3000)->addText('');

        $sigTable->addRow();
        $sigTable->addCell()->addText(''); // spacer

        // Auditor + President
        $auditorName   = $this->validated['auditor_name']   ?? '_________________________';
        $presidentName = $this->validated['president_name'] ?? '_________________________';
        $adviserName   = $this->validated['adviser_name']   ?? '_________________________';
        $guidanceName  = $this->validated['guidance_name']  ?? 'NOEMI ELISA L. OQUIAS';

        $sigTable->addRow();
        $sigTable->addCell(4500, ['borderBottomSize' => 6, 'borderBottomColor' => '000000'])
            ->addText($auditorName, $bold, $center);
        $sigTable->addCell(1000)->addText('');
        $sigTable->addCell(4500, ['borderBottomSize' => 6, 'borderBottomColor' => '000000'])
            ->addText($presidentName, $bold, $center);

        $sigTable->addRow();
        $sigTable->addCell(4500)->addText('Auditor', [], $center);
        $sigTable->addCell(1000)->addText('');
        $sigTable->addCell(4500)->addText('President', [], $center);

        $sigTable->addRow();
        $sigTable->addCell()->addText(''); // spacer

        // Adviser + Guidance
        $sigTable->addRow();
        $sigTable->addCell(4500, ['borderBottomSize' => 6, 'borderBottomColor' => '000000'])
            ->addText($adviserName, $bold, $center);
        $sigTable->addCell(1000)->addText('');
        $sigTable->addCell(4500, ['borderBottomSize' => 6, 'borderBottomColor' => '000000'])
            ->addText($guidanceName, $bold, $center);

        $sigTable->addRow();
        $sigTable->addCell(4500)->addText('Adviser', [], $center);
        $sigTable->addCell(1000)->addText('');
        $sigTable->addCell(4500)->addText('Guidance Facilitator', [], $center);

        $section->addTextBreak(1);

        // ── Receipt acknowledgment ────────────────────────────────────────
        $section->addText(
            'Received the amount of ' . number_format($netFinal, 2) .
            ' (PHP ' . number_format($netFinal, 2) . ') from the Organization Treasurer' .
            ' being designated as VSUIHS Treasurer on ' . now()->format('m/d/Y') . '.',
            [],
            []
        );
        $section->addTextBreak(1);
        $section->addText($treasurerName, $bold, $center);
        $section->addText('VSUIHS Treasurer', [], $center);
        $section->addTextBreak(1);

        // ── Note ──────────────────────────────────────────────────────────
        $section->addText(
            'Note: To be submitted in TRIPLICATE 3 DAYS BEFORE scheduled quarterly examination.',
            ['bold' => true, 'italic' => true, 'size' => 10],
            $center
        );

        $section->addTextBreak(1);
        $section->addText('─────────────────────────────────────────', ['size' => 8], $center);

        // ── Footer ────────────────────────────────────────────────────────
        foreach ([
            'VSU INTEGRATED HIGH SCHOOL',
            'Visayas State University, Baybay City, Leyte',
            'Email: jhs@vsu.edu.ph / integrated.hs@vsu.edu.ph',
            'Website: www.vsu.edu.ph | Phone: +63 53 565 0600 Local 1074 (JHS) 1075 (SHS)',
            'Generated on ' . now()->format('F d, Y \a\t h:i A'),
        ] as $line) {
            $section->addText($line, ['size' => 9], $center);
        }

        // ── Save ──────────────────────────────────────────────────────────
        $path = storage_path('app/temp_financial_report_' . now()->timestamp . '.docx');
        IOFactory::createWriter($phpWord, 'Word2007')->save($path);

        return $path;
    }

    private function getIncomeTotal(): float
    {
        return FinancialTransaction::whereBetween('transaction_date', [
                $this->validated['start_date'], $this->validated['end_date'],
            ])
            ->where(fn($q) => $q
                ->where(fn($i) => $i->where('type', 'income')->where('status', 'approved'))
                ->orWhere(fn($i) => $i->where('type', 'receivable')->where('status', 'paid'))
            )
            ->sum('amount');
    }

    private function getExpenseTotal(): float
    {
        return FinancialTransaction::where('type', 'expense')
            ->where('status', 'approved')
            ->whereBetween('transaction_date', [
                $this->validated['start_date'], $this->validated['end_date'],
            ])
            ->sum('amount');
    }

    private function getReceivablesTotal(): float
    {
        return FinancialTransaction::where('type', 'receivable')
            ->where('status', 'approved')
            ->whereBetween('transaction_date', [
                $this->validated['start_date'], $this->validated['end_date'],
            ])
            ->sum('amount');
    }
}