<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $type_label }} Approval Slip</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #1e293b;
            background: #ffffff;
        }

        /* ── Header ── */
        .header {
            text-align: center;
            padding-bottom: 12px;
            border-bottom: 2px solid #059669;
            margin-bottom: 16px;
        }
        .header .org-name {
            font-size: 15px;
            font-weight: bold;
            color: #059669;
            letter-spacing: 0.5px;
        }
        .header .doc-title {
            font-size: 13px;
            font-weight: bold;
            color: #1e293b;
            margin-top: 4px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .header .doc-subtitle {
            font-size: 10px;
            color: #64748b;
            margin-top: 2px;
        }

        /* ── Status badge ── */
        .badge {
            display: inline-block;
            padding: 3px 12px;
            border-radius: 20px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .badge-approved  { background: #d1fae5; color: #065f46; border: 1px solid #6ee7b7; }
        .badge-income    { background: #d1fae5; color: #065f46; border: 1px solid #6ee7b7; }
        .badge-expense   { background: #fee2e2; color: #7f1d1d; border: 1px solid #fca5a5; }
        .badge-receivable{ background: #fef3c7; color: #78350f; border: 1px solid #fcd34d; }

        /* ── Info grid ── */
        .info-section {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 12px 14px;
            margin-bottom: 14px;
        }
        .info-section .section-title {
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #94a3b8;
            margin-bottom: 8px;
        }
        table.info-table {
            width: 100%;
            border-collapse: collapse;
        }
        table.info-table td {
            padding: 4px 0;
            vertical-align: top;
        }
        table.info-table td.label {
            width: 38%;
            color: #64748b;
            font-size: 10px;
        }
        table.info-table td.value {
            color: #1e293b;
            font-weight: 600;
            font-size: 10px;
        }

        /* ── Amount highlight ── */
        .amount-box {
            background: #f0fdf4;
            border: 1px solid #86efac;
            border-radius: 6px;
            padding: 10px 14px;
            margin-bottom: 14px;
            text-align: center;
        }
        .amount-box.expense {
            background: #fff1f2;
            border-color: #fda4af;
        }
        .amount-box.receivable {
            background: #fffbeb;
            border-color: #fcd34d;
        }
        .amount-box .label {
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #64748b;
            margin-bottom: 4px;
        }
        .amount-box .amount {
            font-size: 22px;
            font-weight: bold;
            color: #059669;
        }
        .amount-box.expense  .amount   { color: #dc2626; }
        .amount-box.receivable .amount { color: #d97706; }

        /* ── Receivable section ── */
        .receivable-section {
            background: #fffbeb;
            border: 1px solid #fcd34d;
            border-radius: 6px;
            padding: 10px 14px;
            margin-bottom: 14px;
        }
        .receivable-section .section-title {
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #92400e;
            margin-bottom: 8px;
        }

        /* ── Approval section ── */
        .approval-section {
            background: #f0fdf4;
            border: 1px solid #86efac;
            border-radius: 6px;
            padding: 10px 14px;
            margin-bottom: 14px;
        }
        .approval-section .section-title {
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #065f46;
            margin-bottom: 8px;
        }

        /* ── Notes ── */
        .notes-section {
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 10px 14px;
            margin-bottom: 14px;
        }
        .notes-section .section-title {
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #94a3b8;
            margin-bottom: 6px;
        }
        .notes-section p {
            font-size: 10px;
            color: #475569;
            line-height: 1.5;
        }

        /* ── Footer ── */
        .footer {
            border-top: 1px solid #e2e8f0;
            padding-top: 10px;
            margin-top: 6px;
            text-align: center;
            font-size: 9px;
            color: #94a3b8;
        }
        .footer .ref {
            font-weight: bold;
            color: #64748b;
        }

        /* ── Divider ── */
        .divider {
            border: none;
            border-top: 1px dashed #e2e8f0;
            margin: 12px 0;
        }

        /* ── Two column layout ── */
        .two-col { width: 100%; border-collapse: collapse; }
        .two-col td { width: 50%; vertical-align: top; padding: 0 6px; }
        .two-col td:first-child { padding-left: 0; }
        .two-col td:last-child  { padding-right: 0; }
    </style>
</head>
<body>

    {{-- ── Header ── --}}
    <div class="header">
        <div class="org-name">VSULHS SSLG — Student Gov Portal</div>
        <div class="doc-title">{{ $type_label }} Approval Slip</div>
        <div class="doc-subtitle">
            Generated {{ $generated_at->format('F j, Y \a\t g:i A') }}
            &nbsp;|&nbsp;
            Transaction #{{ $transaction->id }}
        </div>
    </div>

    {{-- ── Status & Type badges ── --}}
    <div style="text-align:center; margin-bottom:14px;">
        <span class="badge badge-approved">✓ Approved</span>
        &nbsp;
        @if($is_receivable)
            <span class="badge badge-receivable">Receivable</span>
        @elseif($transaction->type === 'income')
            <span class="badge badge-income">Income</span>
        @else
            <span class="badge badge-expense">Expense</span>
        @endif
    </div>

    {{-- ── Amount ── --}}
    <div class="amount-box {{ $is_receivable ? 'receivable' : ($transaction->type === 'expense' ? 'expense' : '') }}">
        <div class="label">
            @if($is_receivable) Receivable Amount
            @elseif($transaction->type === 'income') Total Income
            @else Total Expense
            @endif
        </div>
        <div class="amount">₱{{ number_format($transaction->amount, 2) }}</div>
    </div>

    {{-- ── Transaction Details ── --}}
    <div class="info-section">
        <div class="section-title">Transaction Details</div>
        <table class="info-table">
            <tr>
                <td class="label">Description</td>
                <td class="value">{{ $transaction->description }}</td>
            </tr>
            <tr>
                <td class="label">Category</td>
                <td class="value">{{ $transaction->category ?? '—' }}</td>
            </tr>
            <tr>
                <td class="label">Transaction Date</td>
                <td class="value">{{ $transaction->transaction_date->format('F j, Y') }}</td>
            </tr>
            <tr>
                <td class="label">Transaction ID</td>
                <td class="value">#{{ $transaction->id }}</td>
            </tr>
            <tr>
                <td class="label">Type</td>
                <td class="value">{{ $type_label }}</td>
            </tr>
            @if($transaction->notes)
            <tr>
                <td class="label">Notes</td>
                <td class="value">{{ $transaction->notes }}</td>
            </tr>
            @endif
        </table>
    </div>

    {{-- ── Receivable Details (if applicable) ── --}}
    @if($is_receivable && $transaction->receivable)
    <div class="receivable-section">
        <div class="section-title">⏳ Receivable Details</div>
        <table class="info-table">
            <tr>
                <td class="label">Reference No.</td>
                <td class="value">{{ $transaction->receivable->reference_no ?? '—' }}</td>
            </tr>
            <tr>
                <td class="label">Customer / Payer</td>
                <td class="value">{{ $transaction->receivable->customer_name ?? '—' }}</td>
            </tr>
            <tr>
                <td class="label">Total Amount</td>
                <td class="value">₱{{ number_format($transaction->receivable->total_amount ?? $transaction->amount, 2) }}</td>
            </tr>
            <tr>
                <td class="label">Due Date</td>
                <td class="value">
                    {{ $transaction->receivable->due_date
                        ? \Carbon\Carbon::parse($transaction->receivable->due_date)->format('F j, Y')
                        : '—' }}
                </td>
            </tr>
            <tr>
                <td class="label">Status</td>
                <td class="value">{{ ucfirst($transaction->receivable->status ?? '—') }}</td>
            </tr>
        </table>
    </div>
    @endif

    {{-- ── Recorded By / Approved By ── --}}
    <div class="approval-section">
        <div class="section-title">✓ Approval Information</div>
        <table class="two-col">
            <tr>
                <td>
                    <table class="info-table">
                        <tr>
                            <td class="label">Recorded By</td>
                            <td class="value">{{ $transaction->user?->full_name ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Role</td>
                            <td class="value">{{ $transaction->user?->role?->name ?? '—' }}</td>
                        </tr>
                    </table>
                </td>
                <td>
                    <table class="info-table">
                        <tr>
                            <td class="label">Approved By</td>
                            <td class="value">{{ $approved_by->full_name ?? '—' }}</td>
                        </tr>
                        <tr>
                            <td class="label">Approved At</td>
                            <td class="value">{{ $transaction->approved_at?->format('F j, Y g:i A') ?? '—' }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </div>

    {{-- ── Auditor (if audited) ── --}}
    @if($transaction->auditor)
    <div class="info-section">
        <div class="section-title">Audit Information</div>
        <table class="info-table">
            <tr>
                <td class="label">Audited By</td>
                <td class="value">{{ $transaction->auditor->full_name }}</td>
            </tr>
            <tr>
                <td class="label">Auditor Role</td>
                <td class="value">{{ $transaction->auditor->role?->name ?? '—' }}</td>
            </tr>
        </table>
    </div>
    @endif

    {{-- ── Footer ── --}}
    <div class="footer">
        <p class="ref">VSULHS SSLG &mdash; Official Financial Record</p>
        <p style="margin-top:3px;">
            This document was auto-generated upon approval on
            {{ $generated_at->format('F j, Y \a\t g:i A') }}.
            Transaction #{{ $transaction->id }} &mdash; {{ $type_label }}.
        </p>
    </div>

</body>
</html>