@extends('layouts.app')

@section('title', 'Receivable Details — VSULHS SSLG')
@section('page-title', 'Receivable Details')

@push('styles')
<style>
/* ════════════════════════════════════════════════
   RECEIVABLE DETAILS — Emerald & Gold Luxury Theme
   Matching all management views
════════════════════════════════════════════════ */

/* ── Back Button ── */
.back-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.4rem 1rem;
    font-size: 0.75rem;
    font-weight: 500;
    background: transparent;
    color: var(--text-3);
    text-decoration: none;
    border-radius: 0.5rem;
    transition: all 0.18s ease;
}
.back-btn:hover {
    color: var(--gold-dark);
    background: rgba(212,175,55,0.08);
    transform: translateX(-2px);
}
html.dark .back-btn:hover {
    color: var(--gold-light);
}
.back-btn svg {
    width: 1rem;
    height: 1rem;
    stroke: currentColor;
    fill: none;
}

/* ── Hero Section ── */
.receivable-detail-hero {
    position: relative;
    overflow: hidden;
    border-radius: 1.25rem;
    padding: 1.75rem 2rem;
    isolation: isolate;
    background: linear-gradient(135deg, #064E3B 0%, #065F46 35%, #047857 60%, #0A3A28 100%);
}
.receivable-detail-hero::before {
    content: '';
    position: absolute; inset: 0;
    background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,1) 1px, transparent 0);
    background-size: 28px 28px;
    opacity: 0.04; z-index: 0;
}
.receivable-detail-hero::after {
    content: '';
    position: absolute;
    top: -60px; right: -60px;
    width: 280px; height: 280px;
    background: radial-gradient(circle, rgba(212,175,55,0.35), transparent 65%);
    filter: blur(48px); z-index: 0;
}
.receivable-detail-hero-content { position: relative; z-index: 1; }

.receivable-detail-hero-title {
    font-family: 'DM Serif Display', serif;
    font-size: clamp(1.5rem, 3.5vw, 2.2rem);
    color: #fff;
    letter-spacing: -0.02em;
    line-height: 1.1;
}
.receivable-detail-hero-title span {
    background: linear-gradient(90deg, #F0CC55, #D4AF37);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.receivable-detail-hero-pill {
    display: inline-flex; align-items: center; gap: 0.4rem;
    padding: 0.3rem 0.75rem;
    background: rgba(255,255,255,0.1);
    border: 1px solid rgba(212,175,55,0.28);
    border-radius: 999px;
    font-size: 0.72rem; font-weight: 600;
    color: rgba(255,255,255,0.88);
    font-family: 'DM Mono', monospace;
}

/* ── Detail Card ── */
.detail-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 1.25rem;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
}
html.dark .detail-card {
    box-shadow: 0 4px 20px rgba(0,0,0,0.22);
}
.detail-card-header {
    padding: 1rem 1.5rem;
    border-bottom: 1px solid var(--border);
    background: var(--surface-2);
}
.detail-card-header h2 {
    font-size: 1rem;
    font-weight: 700;
    color: var(--text);
    font-family: 'DM Serif Display', serif;
}
.detail-card-body {
    padding: 1.5rem;
}

/* ── Info Grid ── */
.info-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1.5rem;
}
@media (min-width: 768px) {
    .info-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
.info-section {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}
.info-row {
    display: flex;
    justify-content: space-between;
    align-items: baseline;
    padding: 0.5rem 0;
    border-bottom: 1px solid var(--border);
}
.info-row:last-child {
    border-bottom: none;
}
.info-label {
    font-size: 0.7rem;
    font-weight: 700;
    color: var(--text-3);
    font-family: 'DM Mono', monospace;
}
.info-value {
    font-size: 0.85rem;
    font-weight: 500;
    color: var(--text);
    text-align: right;
}
.info-value strong {
    font-weight: 700;
}

/* ── Status Badges ── */
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.7rem;
    font-weight: 700;
    font-family: 'DM Mono', monospace;
}
.status-pending {
    background: rgba(245,158,11,0.1);
    color: #d97706;
    border: 1px solid rgba(245,158,11,0.2);
}
.status-partial {
    background: rgba(234,179,8,0.1);
    color: #ca8a04;
    border: 1px solid rgba(234,179,8,0.2);
}
.status-paid {
    background: rgba(5,150,105,0.1);
    color: #047857;
    border: 1px solid rgba(5,150,105,0.2);
}
.status-overdue {
    background: rgba(220,38,38,0.1);
    color: #dc2626;
    border: 1px solid rgba(220,38,38,0.2);
}
html.dark .status-pending {
    background: rgba(245,158,11,0.15);
    color: #fcd34d;
}
html.dark .status-partial {
    background: rgba(234,179,8,0.15);
    color: #fde047;
}
html.dark .status-paid {
    background: rgba(16,185,129,0.15);
    color: #6ee7b7;
}
html.dark .status-overdue {
    background: rgba(248,113,113,0.15);
    color: #fca5a5;
}

/* ── Transaction Status Badges ── */
.tx-status-pending { background: rgba(245,158,11,0.1); color: #d97706; border: 1px solid rgba(245,158,11,0.2); }
.tx-status-audited { background: rgba(59,130,246,0.1); color: #2563eb; border: 1px solid rgba(59,130,246,0.2); }
.tx-status-approved { background: rgba(5,150,105,0.1); color: #047857; border: 1px solid rgba(5,150,105,0.2); }
.tx-status-rejected { background: rgba(220,38,38,0.1); color: #dc2626; border: 1px solid rgba(220,38,38,0.2); }

html.dark .tx-status-pending { background: rgba(245,158,11,0.15); color: #fcd34d; }
html.dark .tx-status-audited { background: rgba(59,130,246,0.15); color: #60a5fa; }
html.dark .tx-status-approved { background: rgba(16,185,129,0.15); color: #6ee7b7; }
html.dark .tx-status-rejected { background: rgba(248,113,113,0.15); color: #fca5a5; }

/* ── Amount Styling ── */
.amount-positive { color: #059669; }
.amount-negative { color: #dc2626; }
.amount-neutral { color: var(--text); }

/* ── Overdue Indicator ── */
.overdue-indicator {
    color: #dc2626;
    font-size: 0.65rem;
    font-weight: 600;
    margin-left: 0.5rem;
}

/* ── Linked Transaction Box ── */
.linked-transaction {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid var(--border);
}
.linked-transaction h3 {
    font-size: 0.85rem;
    font-weight: 700;
    color: var(--text-2);
    margin-bottom: 1rem;
    font-family: 'DM Mono', monospace;
}
.transaction-box {
    background: var(--surface-2);
    border: 1px solid var(--border);
    border-radius: 0.75rem;
    padding: 1rem;
}
.transaction-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 1rem;
}
.transaction-item {
    display: flex;
    flex-direction: column;
}
.transaction-label {
    font-size: 0.6rem;
    font-weight: 700;
    color: var(--text-3);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 0.25rem;
}
.transaction-value {
    font-size: 0.85rem;
    font-weight: 500;
    color: var(--text);
}

/* ── Buttons ── */
.btn-emerald {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1.25rem;
    font-size: 0.8rem;
    font-weight: 700;
    background: linear-gradient(135deg, var(--emerald), var(--emerald-dark));
    color: #fff;
    border: none;
    border-radius: 0.65rem;
    cursor: pointer;
    transition: all 0.2s ease;
    font-family: 'Outfit', sans-serif;
}
.btn-emerald:hover {
    background: linear-gradient(135deg, var(--gold), var(--gold-dark));
    color: #0f172a;
    transform: translateY(-1px);
}

/* ── Document Items ── */
.doc-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: var(--surface-2);
    border: 1px solid var(--border);
    border-radius: 0.75rem;
    padding: 0.75rem 1rem;
    transition: all 0.15s ease;
}
.doc-item:hover {
    border-color: rgba(212,175,55,0.35);
    background: rgba(212,175,55,0.025);
}
.doc-icon {
    font-size: 1.5rem;
    flex-shrink: 0;
}
.doc-info {
    flex: 1;
    min-width: 0;
    margin-left: 0.75rem;
}
.doc-title {
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--text);
}
.doc-meta {
    font-size: 0.65rem;
    color: var(--text-3);
    margin-top: 0.25rem;
}
.doc-actions {
    display: flex;
    gap: 0.5rem;
    flex-shrink: 0;
}
.doc-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.25rem 0.75rem;
    font-size: 0.65rem;
    font-weight: 600;
    border-radius: 0.5rem;
    text-decoration: none;
    transition: all 0.15s ease;
}
.doc-btn-download {
    background: rgba(5,150,105,0.1);
    color: #047857;
    border: 1px solid rgba(5,150,105,0.2);
}
.doc-btn-download:hover {
    background: rgba(5,150,105,0.2);
}
.doc-btn-preview {
    background: rgba(59,130,246,0.1);
    color: #2563eb;
    border: 1px solid rgba(59,130,246,0.2);
}
.doc-btn-preview:hover {
    background: rgba(59,130,246,0.2);
}
html.dark .doc-btn-download {
    background: rgba(16,185,129,0.15);
    color: #6ee7b7;
}
html.dark .doc-btn-preview {
    background: rgba(59,130,246,0.15);
    color: #60a5fa;
}

/* ── Warning/Info Boxes ── */
.warning-box {
    background: rgba(245,158,11,0.08);
    border-left: 3px solid #f59e0b;
    border-radius: 0.75rem;
    padding: 1rem;
    margin-top: 1rem;
}
.warning-box p {
    font-size: 0.75rem;
    color: #d97706;
}
.success-box {
    background: rgba(5,150,105,0.08);
    border-left: 3px solid #059669;
    border-radius: 0.75rem;
    padding: 1rem;
    margin-top: 1rem;
}
.success-box p {
    font-size: 0.75rem;
    color: #047857;
}
.error-box {
    background: rgba(220,38,38,0.08);
    border-left: 3px solid #dc2626;
    border-radius: 0.75rem;
    padding: 1rem;
    margin-top: 1rem;
}
.error-box p {
    font-size: 0.75rem;
    color: #dc2626;
}
html.dark .warning-box p { color: #fcd34d; }
html.dark .success-box p { color: #6ee7b7; }
html.dark .error-box p { color: #fca5a5; }

/* ── Animations ── */
@keyframes fadeUp {
    from { opacity: 0; transform: translateY(14px); }
    to   { opacity: 1; transform: translateY(0); }
}
.anim-1 { animation: fadeUp 0.38s ease 0.04s both; }
.anim-2 { animation: fadeUp 0.38s ease 0.10s both; }
.anim-3 { animation: fadeUp 0.38s ease 0.16s both; }
</style>
@endpush

@section('content')
<div class="space-y-5">

    {{-- Back Button --}}
    <div class="anim-1">
        <a href="{{ route('financial.receivables') }}" class="back-btn">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Receivables
        </a>
    </div>

    {{-- Hero Section --}}
    <div class="receivable-detail-hero anim-1">
        <div class="receivable-detail-hero-content">
            <p class="text-emerald-300/70 text-[10px] font-bold tracking-[0.2em] uppercase mb-2"
               style="font-family:'DM Mono',monospace;">
                {{ now()->format('F Y') }} · Receivable Details
            </p>
            <h1 class="receivable-detail-hero-title mb-3">Receivable<br><span>Details</span></h1>
            <div class="flex flex-wrap gap-2">
                <span class="receivable-detail-hero-pill">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Reference: {{ $receivable->reference_no }}
                </span>
            </div>
        </div>
    </div>

    {{-- Receivable Details Card --}}
    <div class="detail-card anim-2">
        <div class="detail-card-header">
            <h2>Receivable #{{ $receivable->reference_no }}</h2>
        </div>
        <div class="detail-card-body">
            <div class="info-grid">
                {{-- Left Column --}}
                <div class="info-section">
                    <div class="info-row">
                        <span class="info-label">Customer / Payer</span>
                        <span class="info-value">{{ $receivable->customer_name ?? '—' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Description</span>
                        <span class="info-value">{{ $receivable->description }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Total Amount</span>
                        <span class="info-value"><strong>₱{{ number_format($receivable->total_amount, 2) }}</strong></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Amount Paid</span>
                        <span class="info-value amount-positive"><strong>₱{{ number_format($receivable->paid_amount, 2) }}</strong></span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Remaining Balance</span>
                        <span class="info-value {{ $receivable->remaining > 0 ? 'amount-negative' : 'amount-positive' }}">
                            <strong>₱{{ number_format($receivable->remaining, 2) }}</strong>
                        </span>
                    </div>
                </div>

                {{-- Right Column --}}
                <div class="info-section">
                    <div class="info-row">
                        <span class="info-label">Due Date</span>
                        <span class="info-value">
                            @if($receivable->due_date)
                                {{ \Carbon\Carbon::parse($receivable->due_date)->format('F d, Y') }}
                                @if($receivable->due_date < now()->toDateString() && $receivable->status !== 'paid')
                                    <span class="overdue-indicator">(Overdue)</span>
                                @endif
                            @else
                                —
                            @endif
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Status</span>
                        <span class="info-value">
                            @php
                                $statusClass = match($receivable->status) {
                                    'pending' => 'status-pending',
                                    'partial' => 'status-partial',
                                    'paid' => 'status-paid',
                                    'overdue' => 'status-overdue',
                                    default => 'status-pending',
                                };
                            @endphp
                            <span class="status-badge {{ $statusClass }}">
                                {{ ucfirst($receivable->status) }}
                            </span>
                        </span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Created By</span>
                        <span class="info-value">{{ $receivable->creator->full_name ?? '—' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">Created At</span>
                        <span class="info-value">{{ $receivable->created_at->format('F d, Y h:i A') }}</span>
                    </div>
                </div>
            </div>

            {{-- Linked Transaction Information --}}
            @if($receivable->incomeTransaction)
                <div class="linked-transaction">
                    <h3>📊 Linked Income Transaction</h3>
                    <div class="transaction-box">
                        <div class="transaction-grid">
                            <div class="transaction-item">
                                <span class="transaction-label">Transaction ID</span>
                                <span class="transaction-value font-mono">#{{ $receivable->incomeTransaction->id }}</span>
                            </div>
                            <div class="transaction-item">
                                <span class="transaction-label">Amount</span>
                                <span class="transaction-value"><strong>₱{{ number_format($receivable->incomeTransaction->amount, 2) }}</strong></span>
                            </div>
                            <div class="transaction-item">
                                <span class="transaction-label">Status</span>
                                <span class="transaction-value">
                                    @php
                                        $txStatusClass = match($receivable->incomeTransaction->status) {
                                            'pending' => 'tx-status-pending',
                                            'audited' => 'tx-status-audited',
                                            'approved' => 'tx-status-approved',
                                            'rejected' => 'tx-status-rejected',
                                            default => 'tx-status-pending',
                                        };
                                    @endphp
                                    <span class="status-badge {{ $txStatusClass }}">
                                        {{ ucfirst($receivable->incomeTransaction->status) }}
                                    </span>
                                </span>
                            </div>
                        </div>

                        {{-- MARK AS PAID BUTTON --}}
                        @if($receivable->incomeTransaction->status === 'approved' && !$receivable->incomeTransaction->receivable_paid && $receivable->status !== 'paid')
                            <div class="mt-4">
                                <form method="POST" action="{{ route('financial.receivable.mark-paid', $receivable) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn-emerald"
                                            onclick="return confirm('✅ Mark this receivable as paid?\n\nThis will add the amount to income reports.')">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                        </svg>
                                        Mark as Paid
                                    </button>
                                </form>
                                <p class="text-xs text-text-3 mt-2">This will include the amount in your financial report's Total Income.</p>
                            </div>
                        @elseif($receivable->incomeTransaction->status !== 'approved')
                            <div class="warning-box">
                                <p>⚠️ This transaction must be <strong>approved</strong> (not just audited) before it can be marked as paid.</p>
                                <p class="text-xs mt-1">Current status: {{ $receivable->incomeTransaction->status }}</p>
                            </div>
                        @elseif($receivable->incomeTransaction->receivable_paid)
                            <div class="success-box">
                                <p>✓ Already marked as paid. Amount is included in income reports.</p>
                            </div>
                        @elseif($receivable->status === 'paid')
                            <div class="success-box">
                                <p>✓ Receivable is already marked as paid.</p>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="linked-transaction">
                    <div class="error-box">
                        <p>⚠️ No linked income transaction found. Please check the database.</p>
                    </div>
                </div>
            @endif

            {{-- Documents / Approval Slip --}}
            @php
                $paymentDocs = $receivable->incomeTransaction?->documents ?? collect();
            @endphp
            @if($paymentDocs->isNotEmpty())
                <div class="linked-transaction">
                    <h3>📎 Payment Documents</h3>
                    <div class="space-y-3">
                        @foreach($paymentDocs as $doc)
                            @php $version = $doc->currentVersion; @endphp
                            <div class="doc-item">
                                <div class="doc-icon">📄</div>
                                <div class="doc-info">
                                    <p class="doc-title">{{ $doc->title }}</p>
                                    @if($doc->description)
                                        <p class="doc-meta">{{ $doc->description }}</p>
                                    @endif
                                    <p class="doc-meta">Saved {{ $doc->created_at->format('F d, Y h:i A') }}</p>
                                </div>
                                <div class="doc-actions">
                                    @if($version)
                                        <a href="{{ route('documents.version.download', [$doc, $version]) }}" class="doc-btn doc-btn-download">
                                            ⬇ Download
                                        </a>
                                        @php
                                            $ext = strtolower(pathinfo($version->file_name, PATHINFO_EXTENSION));
                                        @endphp
                                        @if(in_array($ext, ['pdf','jpg','jpeg','png','gif']))
                                            <a href="{{ route('documents.preview', $doc) }}" target="_blank" class="doc-btn doc-btn-preview">
                                                👁 Preview
                                            </a>
                                        @endif
                                    @else
                                        <span class="text-xs text-text-3 italic">No file version found</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @if($receivable->status === 'paid' && $receivable->paid_at)
                        <p class="text-xs text-text-3 mt-2">
                            Paid on {{ \Carbon\Carbon::parse($receivable->paid_at)->format('F d, Y h:i A') }}
                        </p>
                    @endif
                </div>
            @elseif($receivable->status === 'paid')
                <div class="linked-transaction">
                    <h3>📎 Payment Documents</h3>
                    <div class="warning-box">
                        <p>⚠️ This receivable is paid but no documents were found. The approval slip may have failed to save.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection