<?php

namespace App\Exports;

use App\Models\FinancialTransaction;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class FinancialReportExport implements FromArray, WithStyles, WithTitle, WithColumnWidths, WithEvents
{
    public function __construct(private array $validated) {}

    public function title(): string { return 'Financial Report'; }

    public function columnWidths(): array
    {
        return ['A' => 40, 'B' => 20, 'C' => 30];
    }

    public function array(): array
    {
        $incomeTotal  = $this->getIncomeTotal();
        $expenseTotal = $this->getExpenseTotal();
        $prevCash     = (float) ($this->validated['previous_cash'] ?? 0);
        $netFromOps   = $incomeTotal - $expenseTotal;
        $netFinal     = $netFromOps + $prevCash;
        $receivables  = $this->getReceivablesTotal();

        $org       = $this->validated['organization'] ?? '_________________________';
        $startDate = \Carbon\Carbon::parse($this->validated['start_date'])->format('F d, Y');
        $endDate   = \Carbon\Carbon::parse($this->validated['end_date'])->format('F d, Y');

        return [
            // Header
            ['VSU INTEGRATED HIGH SCHOOL', '', ''],
            ['Baybay City, Leyte', '', ''],
            ['OFFICE OF THE VSUIHS GUIDANCE FACILITATOR', '', ''],
            ['', '', ''],
            ['FINANCIAL REPORT', '', ''],
            ['of the ' . $org, '', ''],
            ['For the period ' . $startDate . ' to ' . $endDate, '', ''],
            ['', '', ''],

            // Table header
            ['Details', 'Amount (PHP)', 'Remarks'],

            // Table rows
            ['A. Cash on Hand (attach list)',          number_format($incomeTotal, 2),  ''],
            ['B. Less: Expenses (attach receipts)',    number_format($expenseTotal, 2), ''],
            ['C. Total Income (A minus B)',            number_format($netFromOps, 2),   ''],
            ['D. Receivables (attach list)',           number_format($receivables, 2),  ''],
            ['E. Previous Cash Deposited',             number_format($prevCash, 2),     ''],
            ['NET INCOME (C + E)',                     number_format($netFinal, 2),     ''],
            ['', '', ''],

            // Signatures
            ['Certified True and Correct:', '', ''],
            ['', '', ''],
            [$this->validated['treasurer_name'] ?? 'SHEERWINA MAE G. BALOTITE', '', ''],
            ['Treasurer', '', ''],
            ['', '', ''],
            [$this->validated['auditor_name'] ?? '_________________________', '', $this->validated['president_name'] ?? '_________________________'],
            ['Auditor', '', 'President'],
            ['', '', ''],
            [$this->validated['adviser_name'] ?? '_________________________', '', $this->validated['guidance_name'] ?? '_________________________'],
            ['Adviser', '', 'Guidance Facilitator'],
            ['', '', ''],

            // Receipt
            ['Received the amount of ' . number_format($netFinal, 2) . ' from the Organization Treasurer being designated as VSUIHS Treasurer on ' . now()->format('m/d/Y') . '.', '', ''],
            ['', '', ''],
            [$this->validated['treasurer_name'] ?? '_________________________', '', ''],
            ['VSUIHS Treasurer', '', ''],
            ['', '', ''],

            // Note
            ['Note: To be submitted in TRIPLICATE 3 DAYS BEFORE scheduled quarterly examination.', '', ''],
            ['', '', ''],

            // Footer
            ['VSU INTEGRATED HIGH SCHOOL', '', ''],
            ['Visayas State University, Baybay City, Leyte', '', ''],
            ['Email: jhs@vsu.edu.ph / integrated.hs@vsu.edu.ph', '', ''],
            ['Website: www.vsu.edu.ph | Phone: +63 53 565 0600 Local 1074 (JHS) 1075 (SHS)', '', ''],
            ['Generated on ' . now()->format('F d, Y \a\t h:i A'), '', ''],
        ];
    }

    public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet): array
    {
        return [
            1  => ['font' => ['bold' => true], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]],
            3  => ['font' => ['bold' => true], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]],
            5  => ['font' => ['bold' => true, 'size' => 13], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]],
            9  => ['font' => ['bold' => true], 'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D9D9D9']]],
            15 => ['font' => ['bold' => true]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Merge header cells
                foreach ([1, 2, 3, 4, 5, 6, 7, 8] as $row) {
                    $sheet->mergeCells("A{$row}:C{$row}");
                }

                // Border around the financial table (rows 9-15)
                $sheet->getStyle('A9:C15')->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color'       => ['rgb' => '000000'],
                        ],
                    ],
                ]);

                // Right-align amount column in table
                $sheet->getStyle('B10:B15')->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_RIGHT);

                // Center signature names
                foreach ([19, 22, 25] as $row) {
                    $sheet->getStyle("A{$row}")->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("C{$row}")->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }

                // Underline signature name cells
                foreach (['A19', 'A22', 'A25', 'C22', 'C25'] as $cell) {
                    $sheet->getStyle($cell)->applyFromArray([
                        'font' => ['bold' => true, 'underline' => true],
                    ]);
                }

                // Note row — italic bold center
                $noteRow = 33;
                $sheet->mergeCells("A{$noteRow}:C{$noteRow}");
                $sheet->getStyle("A{$noteRow}")->applyFromArray([
                    'font'      => ['bold' => true, 'italic' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                // Footer rows — center and small
                foreach (range(35, 39) as $row) {
                    $sheet->mergeCells("A{$row}:C{$row}");
                    $sheet->getStyle("A{$row}")->applyFromArray([
                        'font'      => ['size' => 9],
                        'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                    ]);
                }
            },
        ];
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