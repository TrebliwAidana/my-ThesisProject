<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Approval Slip — {{ $type_label }} #{{ $transaction->id }}</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: sans-serif; font-size: 13px; color: #1a1a1a; margin: 0; padding: 0; }

        .header { text-align: center; border-bottom: 2px solid #1e40af; padding-bottom: 12px; margin-bottom: 20px; }
        .header h2 { margin: 0 0 4px; font-size: 18px; color: #1e40af; }
        .header .subtitle { font-size: 11px; color: #6b7280; }

        .badge {
            display: inline-block; padding: 3px 12px; border-radius: 12px;
            background: #d1fae5; color: #065f46; font-weight: bold;
            font-size: 11px; letter-spacing: 0.5px; text-transform: uppercase;
        }

        .section-title {
            font-size: 11px; font-weight: bold; text-transform: uppercase;
            letter-spacing: 0.8px; color: #6b7280;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 4px; margin: 20px 0 8px;
        }

        table { width: 100%; border-collapse: collapse; }
        table td { padding: 7px 10px; border-bottom: 1px solid #f3f4f6; vertical-align: top; }
        table td:first-child { width: 38%; font-weight: 600; color: #374151; background: #f9fafb; }
        table td:last-child  { color: #111827; }

        .amount { font-size: 16px; font-weight: bold; color: #1e40af; }

        .footer {
            margin-top: 32px; padding-top: 10px;
            border-top: 1px solid #e5e7eb;
            font-size: 10px; color: #9ca3af; text-align: center;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>{{ $is_receivable ? 'Receivable' : $type_label }} — Approval Slip</h2>
        <div class="subtitle">
            Auto-generated &bull; {{ $generated_at->format('F j, Y \a\t g:i A') }}
        </div>
    </div>

    {{-- ── Transaction Details ── --}}
    <div class="section-title">Transaction Details</div>
    <table>
        <tr>
            <td>Transaction #</td>
            <td>#{{ $transaction->id }}</td>
        </tr>
        <tr>
            <td>Type</td>
            <td>{{ $type_label }}{{ $is_receivable ? ' (Receivable)' : '' }}</td>
        </tr>
        <tr>
            <td>Description</td>
            <td>{{ $transaction->description }}</td>
        </tr>
        <tr>
            <td>Amount</td>
            <td><span class="amount">₱{{ number_format($transaction->amount, 2) }}</span></td>
        </tr>
        <tr>
            <td>Category</td>
            <td>{{ $transaction->category ?? '—' }}</td>
        </tr>
        <tr>
            <td>Transaction Date</td>
            <td>{{ $transaction->transaction_date->format('F j, Y') }}</td>
        </tr>
        @if($transaction->notes)
        <tr>
            <td>Notes</td>
            <td>{{ $transaction->notes }}</td>
        </tr>
        @endif
        <tr>
            <td>Status</td>
            <td><span class="badge">Approved</span></td>
        </tr>
    </table>

    {{-- ── Workflow Trail ── --}}
    <div class="section-title">Approval Workflow</div>
    <table>
        <tr>
            <td>Recorded By</td>
            <td>
                {{ $transaction->user->full_name ?? $transaction->user->name ?? '—' }}
                @if($transaction->created_at)
                    <br><small style="color:#6b7280;">{{ $transaction->created_at->format('M j, Y g:i A') }}</small>
                @endif
            </td>
        </tr>
        <tr>
            <td>Audited By</td>
            <td>
                {{ optional($transaction->auditor)->full_name ?? '—' }}
                @if($transaction->audited_at)
                    <br><small style="color:#6b7280;">{{ \Carbon\Carbon::parse($transaction->audited_at)->format('M j, Y g:i A') }}</small>
                @endif
            </td>
        </tr>
        <tr>
            <td>Approved By</td>
            <td>
                {{ $approved_by->full_name ?? $approved_by->name }}
                <br><small style="color:#6b7280;">{{ $generated_at->format('M j, Y g:i A') }}</small>
            </td>
        </tr>
    </table>

    {{-- ── Receivable Details (if applicable) ── --}}
    @if($is_receivable && $transaction->receivable)
    <div class="section-title">Receivable Details</div>
    <table>
        <tr>
            <td>Reference No.</td>
            <td>{{ $transaction->receivable->reference_no }}</td>
        </tr>
        <tr>
            <td>Customer</td>
            <td>{{ $transaction->receivable->customer_name ?? '—' }}</td>
        </tr>
        <tr>
            <td>Due Date</td>
            <td>
                @if($transaction->receivable->due_date)
                    {{ \Carbon\Carbon::parse($transaction->receivable->due_date)->format('F j, Y') }}
                @else
                    —
                @endif
            </td>
        </tr>
        <tr>
            <td>Payment Status</td>
            <td>{{ ucfirst($transaction->receivable->status) }}</td>
        </tr>
    </table>
    @endif

    <div class="footer">
        This document was automatically generated by the system upon final approval.
        &bull; Transaction ID: {{ $transaction->id }}
        &bull; {{ $generated_at->toDateTimeString() }}
    </div>

</body>
</html>