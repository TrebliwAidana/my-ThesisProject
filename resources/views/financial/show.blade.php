@extends('layouts.app')

@section('title', 'Transaction Details — VSULHS SSLG')
@section('page-title', 'Transaction Details')

@push('styles')
<style>
/* ════════════════════════════════════════════════
   TRANSACTION DETAILS — Emerald & Gold Luxury Theme
   Matching all management views
════════════════════════════════════════════════ */

/* ── Hero Section ── */
.transaction-hero {
    position: relative;
    overflow: hidden;
    border-radius: 1.25rem;
    padding: 1.75rem 2rem;
    isolation: isolate;
    background: linear-gradient(135deg, #064E3B 0%, #065F46 35%, #047857 60%, #0A3A28 100%);
}
.transaction-hero::before {
    content: '';
    position: absolute; inset: 0;
    background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,1) 1px, transparent 0);
    background-size: 28px 28px;
    opacity: 0.04; z-index: 0;
}
.transaction-hero::after {
    content: '';
    position: absolute;
    top: -60px; right: -60px;
    width: 280px; height: 280px;
    background: radial-gradient(circle, rgba(212,175,55,0.35), transparent 65%);
    filter: blur(48px); z-index: 0;
}
.transaction-hero-content { position: relative; z-index: 1; }

.transaction-hero-title {
    font-family: 'DM Serif Display', serif;
    font-size: clamp(1.5rem, 3.5vw, 2.2rem);
    color: #fff;
    letter-spacing: -0.02em;
    line-height: 1.1;
}
.transaction-hero-title span {
    background: linear-gradient(90deg, #F0CC55, #D4AF37);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.transaction-hero-pill {
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
.detail-card-body {
    padding: 1.5rem;
}

/* ── Header Section ── */
.transaction-header {
    display: flex;
    flex-wrap: wrap;
    justify-content: space-between;
    align-items: flex-start;
    gap: 1rem;
    margin-bottom: 1rem;
}
.transaction-title {
    font-size: 1rem;
    font-weight: 700;
    color: var(--text);
    font-family: 'DM Serif Display', serif;
    margin-bottom: 0.25rem;
}
.transaction-date {
    font-size: 0.7rem;
    color: var(--text-3);
    font-family: 'DM Mono', monospace;
}
.transaction-customer {
    font-size: 0.75rem;
    color: #7c3aed;
    margin-top: 0.25rem;
}
.transaction-amount {
    font-size: 1.5rem;
    font-weight: 700;
    font-family: 'DM Mono', monospace;
}
.transaction-amount.income { color: #059669; }
.transaction-amount.expense { color: #dc2626; }
.transaction-amount.receivable { color: #7c3aed; }

/* ── Status Badge ── */
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
.status-audited {
    background: rgba(59,130,246,0.1);
    color: #2563eb;
    border: 1px solid rgba(59,130,246,0.2);
}
.status-approved {
    background: rgba(5,150,105,0.1);
    color: #047857;
    border: 1px solid rgba(5,150,105,0.2);
}
.status-rejected {
    background: rgba(220,38,38,0.1);
    color: #dc2626;
    border: 1px solid rgba(220,38,38,0.2);
}
.status-paid {
    background: rgba(5,150,105,0.1);
    color: #047857;
    border: 1px solid rgba(5,150,105,0.2);
}
html.dark .status-pending { background: rgba(245,158,11,0.15); color: #fcd34d; }
html.dark .status-audited { background: rgba(59,130,246,0.15); color: #60a5fa; }
html.dark .status-approved { background: rgba(16,185,129,0.15); color: #6ee7b7; }
html.dark .status-rejected { background: rgba(248,113,113,0.15); color: #fca5a5; }
html.dark .status-paid { background: rgba(16,185,129,0.15); color: #6ee7b7; }

/* ── Type Badge ── */
.type-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.2rem 0.65rem;
    border-radius: 9999px;
    font-size: 0.65rem;
    font-weight: 700;
    font-family: 'DM Mono', monospace;
}
.type-income {
    background: rgba(5,150,105,0.1);
    color: #047857;
    border: 1px solid rgba(5,150,105,0.2);
}
.type-expense {
    background: rgba(220,38,38,0.08);
    color: #dc2626;
    border: 1px solid rgba(220,38,38,0.18);
}
.type-receivable {
    background: rgba(124,58,237,0.1);
    color: #7c3aed;
    border: 1px solid rgba(124,58,237,0.2);
}
html.dark .type-income { background: rgba(16,185,129,0.15); color: #6ee7b7; }
html.dark .type-expense { background: rgba(248,113,113,0.12); color: #fca5a5; }
html.dark .type-receivable { background: rgba(139,92,246,0.12); color: #a78bfa; }

/* ── Info Grid ── */
.info-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
    margin: 1rem 0;
}
.info-item {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}
.info-label {
    font-size: 0.65rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--text-3);
    font-family: 'DM Mono', monospace;
}
.info-value {
    font-size: 0.8rem;
    font-weight: 500;
    color: var(--text);
}

/* ── Notes Box ── */
.notes-box {
    background: var(--surface-2);
    border-radius: 0.75rem;
    padding: 0.75rem 1rem;
    margin-top: 0.5rem;
}
.notes-box p {
    font-size: 0.8rem;
    color: var(--text-2);
    line-height: 1.5;
}

/* ── Document Section ── */
.doc-section {
    margin-top: 1rem;
}
.doc-section-title {
    font-size: 0.65rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--text-3);
    font-family: 'DM Mono', monospace;
    margin-bottom: 0.5rem;
}
.doc-auto-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.2rem 0.6rem;
    border-radius: 9999px;
    font-size: 0.6rem;
    font-weight: 600;
    background: rgba(5,150,105,0.1);
    color: #047857;
    margin-left: 0.5rem;
}
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
    font-size: 1.25rem;
    flex-shrink: 0;
}
.doc-info {
    flex: 1;
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
}
.doc-actions {
    display: flex;
    gap: 0.5rem;
}
.doc-btn {
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

/* ── Mark as Paid Box ── */
.mark-paid-box {
    background: rgba(124,58,237,0.05);
    border: 1px solid rgba(124,58,237,0.2);
    border-radius: 0.75rem;
    padding: 1rem;
    margin: 1rem 0;
}
.mark-paid-title {
    font-size: 0.8rem;
    font-weight: 700;
    color: #7c3aed;
    margin-bottom: 0.25rem;
}
.mark-paid-desc {
    font-size: 0.7rem;
    color: var(--text-3);
    margin-bottom: 0.75rem;
}
.btn-purple {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1.25rem;
    font-size: 0.75rem;
    font-weight: 700;
    background: linear-gradient(135deg, #7c3aed, #6d28d9);
    color: #fff;
    border: none;
    border-radius: 0.65rem;
    cursor: pointer;
    transition: all 0.2s ease;
}
.btn-purple:hover:not(:disabled) {
    background: linear-gradient(135deg, var(--gold), var(--gold-dark));
    color: #0f172a;
    transform: translateY(-1px);
}

/* ── Action Buttons ── */
.action-buttons {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid var(--border);
}
.btn-edit {
    background: linear-gradient(135deg, var(--gold), var(--gold-dark));
    color: #0f172a;
}
.btn-edit:hover {
    background: linear-gradient(135deg, var(--emerald), var(--emerald-dark));
    color: #fff;
}
.btn-audit {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
    color: #fff;
}
.btn-audit:hover {
    background: linear-gradient(135deg, var(--gold), var(--gold-dark));
    color: #0f172a;
}
.btn-approve {
    background: linear-gradient(135deg, #059669, #047857);
    color: #fff;
}
.btn-approve:hover {
    background: linear-gradient(135deg, var(--gold), var(--gold-dark));
    color: #0f172a;
}
.btn-reject {
    background: linear-gradient(135deg, #dc2626, #b91c1c);
    color: #fff;
}
.btn-reject:hover {
    background: linear-gradient(135deg, #ef4444, #dc2626);
}
.btn-delete {
    background: var(--surface-3);
    color: var(--text-3);
    border: 1px solid var(--border);
}
.btn-delete:hover {
    background: #dc2626;
    color: #fff;
    border-color: #dc2626;
}
.btn-back {
    background: transparent;
    color: var(--text-2);
    border: 1px solid var(--border);
}
.btn-back:hover {
    border-color: rgba(212,175,55,0.4);
    color: var(--gold-dark);
    background: rgba(212,175,55,0.06);
}
.btn-action {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    font-size: 0.75rem;
    font-weight: 700;
    border-radius: 0.65rem;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
    font-family: 'Outfit', sans-serif;
}

/* ── Overdue Indicator ── */
.overdue-indicator {
    color: #dc2626;
    font-size: 0.65rem;
    font-weight: 600;
    margin-left: 0.25rem;
}

/* ── Animations ── */
@keyframes fadeUp {
    from { opacity: 0; transform: translateY(14px); }
    to   { opacity: 1; transform: translateY(0); }
}
.anim-1 { animation: fadeUp 0.38s ease 0.04s both; }
.anim-2 { animation: fadeUp 0.38s ease 0.10s both; }

.spinner {
    width: 1rem;
    height: 1rem;
    animation: spin 0.8s linear infinite;
}
@keyframes spin {
    to { transform: rotate(360deg); }
}
</style>
@endpush

@section('content')

@php
    $amountColor = match($transaction->type) {
        'income'     => 'income',
        'expense'    => 'expense',
        'receivable' => 'receivable',
        default      => 'neutral',
    };
    $prefix = match($transaction->type) {
        'income'     => '+',
        'expense'    => '-',
        'receivable' => $transaction->status === 'paid' ? '+' : '⏳',
        default      => '',
    };
    $statusClass = match($transaction->status) {
        'pending'  => 'status-pending',
        'audited'  => 'status-audited',
        'approved' => 'status-approved',
        'rejected' => 'status-rejected',
        'paid'     => 'status-paid',
        default    => 'status-pending',
    };
    $typeClass = match($transaction->type) {
        'income'     => 'type-income',
        'expense'    => 'type-expense',
        'receivable' => 'type-receivable',
        default      => 'type-income',
    };

    $user = auth()->user();
    $isAdmin = (int) $user->role->level === 1;
    $canEdit = ($isAdmin || $user->hasPermission('financial.edit')) && $transaction->status === 'pending';
    $canDelete = ($isAdmin || $user->hasPermission('financial.delete')) && $transaction->status === 'pending';
    $canAudit = ($isAdmin || $user->hasPermission('financial.audit')) && $transaction->status === 'pending';
    $canApprove = ($isAdmin || $user->hasPermission('financial.approve')) && $transaction->status === 'audited';
    $canReject = $canApprove;
    $canMarkPaid = ($isAdmin || $user->hasPermission('financial.approve'))
                    && $transaction->type === 'receivable'
                    && $transaction->status === 'approved';
@endphp

<div class="space-y-5">
    
    {{-- Hero Section --}}
    <div class="transaction-hero anim-1">
        <div class="transaction-hero-content">
            <p class="text-emerald-300/70 text-[10px] font-bold tracking-[0.2em] uppercase mb-2"
               style="font-family:'DM Mono',monospace;">
                {{ now()->format('F Y') }} · Financial Management
            </p>
            <h1 class="transaction-hero-title mb-3">Transaction<br><span>Details</span></h1>
            <div class="flex flex-wrap gap-2">
                <span class="transaction-hero-pill">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Transaction #{{ $transaction->id }}
                </span>
            </div>
        </div>
    </div>

    {{-- Detail Card --}}
    <div class="detail-card anim-2">
        <div class="detail-card-body">

            {{-- Header --}}
            <div class="transaction-header">
                <div>
                    <h2 class="transaction-title">{{ $transaction->description }}</h2>
                    <p class="transaction-date">{{ $transaction->transaction_date->format('F d, Y') }}</p>
                    @if($transaction->type === 'receivable' && $transaction->customer_name)
                        <p class="transaction-customer">👤 {{ $transaction->customer_name }}</p>
                    @endif
                </div>
                <div class="text-right">
                    <div class="transaction-amount {{ $amountColor }}">
                        {{ $prefix }} {{ $transaction->formatted_amount }}
                    </div>
                    <div class="mt-1">
                        <span class="status-badge {{ $statusClass }}">
                            {{ $transaction->status === 'paid' ? '✓ Paid' : ucfirst($transaction->status) }}
                        </span>
                    </div>
                </div>
            </div>

            <hr class="border-border my-4">

            {{-- Info Grid --}}
            <div class="info-grid">
                <div class="info-item">
                    <span class="info-label">Type</span>
                    <span class="type-badge {{ $typeClass }}">
                        @if($transaction->type === 'income')
                            ↑ Income
                        @elseif($transaction->type === 'expense')
                            ↓ Expense
                        @else
                            ⏳ Receivable
                        @endif
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Category</span>
                    <span class="info-value">{{ $transaction->category ?? '—' }}</span>
                </div>

                @if($transaction->type === 'receivable')
                <div class="info-item">
                    <span class="info-label">Customer</span>
                    <span class="info-value">{{ $transaction->customer_name ?? '—' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Due Date</span>
                    <span class="info-value {{ $transaction->isOverdue() ? 'text-red-600 font-semibold' : '' }}">
                        {{ $transaction->due_date ? $transaction->due_date->format('M d, Y') : '—' }}
                        @if($transaction->isOverdue())
                            <span class="overdue-indicator">(Overdue)</span>
                        @endif
                    </span>
                </div>
                @endif

                <div class="info-item">
                    <span class="info-label">Submitted By</span>
                    <span class="info-value">{{ $transaction->user->full_name ?? '—' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Submitted On</span>
                    <span class="info-value">{{ $transaction->created_at->format('M d, Y h:i A') }}</span>
                </div>

                @if($transaction->audited_at)
                <div class="info-item">
                    <span class="info-label">Audited By</span>
                    <span class="info-value">{{ $transaction->auditor->full_name ?? '—' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Audited On</span>
                    <span class="info-value">{{ $transaction->audited_at->format('M d, Y h:i A') }}</span>
                </div>
                @endif

                @if($transaction->approved_at)
                <div class="info-item">
                    <span class="info-label">{{ $transaction->status === 'paid' ? 'Collected By' : 'Approved By' }}</span>
                    <span class="info-value">{{ $transaction->approver->full_name ?? '—' }}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">{{ $transaction->status === 'paid' ? 'Collected On' : 'Approved On' }}</span>
                    <span class="info-value">{{ $transaction->approved_at->format('M d, Y h:i A') }}</span>
                </div>
                @endif
            </div>

            {{-- Notes --}}
            @if($transaction->notes)
            <div class="info-item" style="grid-column: span 2;">
                <span class="info-label">Notes</span>
                <div class="notes-box">
                    <p>{{ $transaction->notes }}</p>
                </div>
            </div>
            @endif

            {{-- Documents Section --}}
            @if($transaction->documents->isNotEmpty())
                @php
                    $approvalDocs = $transaction->documents->filter(function($doc) {
                        $tags = is_array($doc->tags) ? $doc->tags : ($doc->tags ?? []);
                        return in_array('auto-generated', $tags);
                    });
                    $receiptDocs = $transaction->documents->filter(function($doc) {
                        $tags = is_array($doc->tags) ? $doc->tags : ($doc->tags ?? []);
                        return !in_array('auto-generated', $tags);
                    });
                @endphp

                @if($approvalDocs->isNotEmpty())
                <div class="doc-section">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="doc-section-title">Approval Document</span>
                        <span class="doc-auto-badge">✓ Auto-generated</span>
                    </div>
                    <div class="space-y-2">
                        @foreach($approvalDocs as $doc)
                            @php $version = $doc->currentVersion; @endphp
                            <div class="doc-item">
                                <div class="doc-icon">📋</div>
                                <div class="doc-info">
                                    <p class="doc-title">{{ $doc->title }}</p>
                                    <p class="doc-meta">Generated {{ $doc->created_at->format('M d, Y h:i A') }}</p>
                                </div>
                                <div class="doc-actions">
                                    @if($version)
                                        <a href="{{ route('documents.version.download', [$doc, $version]) }}" class="doc-btn doc-btn-download">⬇ Download</a>
                                        <a href="{{ route('documents.preview', $doc) }}" target="_blank" class="doc-btn doc-btn-preview">👁 Preview</a>
                                    @else
                                        <span class="text-xs text-text-3 italic">No file</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                @if($receiptDocs->isNotEmpty())
                <div class="doc-section">
                    <span class="doc-section-title">Attached Receipt</span>
                    <div class="space-y-2">
                        @foreach($receiptDocs as $doc)
                            @php $version = $doc->currentVersion; @endphp
                            <div class="doc-item">
                                <div class="doc-icon">📎</div>
                                <div class="doc-info">
                                    <p class="doc-title">{{ $doc->title }}</p>
                                </div>
                                <div class="doc-actions">
                                    @if($version)
                                        <a href="{{ route('documents.version.download', [$doc, $version]) }}" class="doc-btn doc-btn-download">⬇ Download</a>
                                        <a href="{{ route('documents.preview', $doc) }}" target="_blank" class="doc-btn doc-btn-preview">👁 Preview</a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            @endif

            {{-- Mark as Paid (Receivable only) --}}
            @if($canMarkPaid)
            <div class="mark-paid-box">
                <p class="mark-paid-title">Ready to collect?</p>
                <p class="mark-paid-desc">
                    Clicking <strong>Mark as Paid</strong> will add
                    <strong>₱{{ number_format($transaction->amount, 2) }}</strong> to Total Income
                    and save the approval slip to Documents automatically.
                </p>
                <form method="POST"
                      action="{{ route('financial.mark-as-paid', $transaction->id) }}"
                      x-data="{ submitting: false }"
                      @submit.prevent="
                          if (!confirm('Confirm payment received from {{ addslashes($transaction->customer_name) }}?\n\nThis will add ₱{{ number_format($transaction->amount, 2) }} to total income and cannot be undone.')) return;
                          submitting = true;
                          $el.submit();
                      ">
                    @csrf
                    @method('PATCH')
                    <button type="submit"
                            :disabled="submitting"
                            :class="submitting ? 'opacity-60 cursor-not-allowed' : ''"
                            class="btn-purple">
                        <svg x-show="submitting" class="spinner" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                        </svg>
                        <span x-text="submitting ? 'Processing…' : '✅ Mark as Paid'"></span>
                    </button>
                </form>
            </div>
            @endif

            {{-- Action Buttons --}}
            <div class="action-buttons">
                @if($canEdit)
                    <a href="{{ route('financial.edit', $transaction->id) }}" class="btn-action btn-edit">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit
                    </a>
                @endif

                @if($canAudit)
                    <form method="POST" action="{{ route('financial.audit', $transaction->id) }}" x-data="{ submitting: false }" @submit="if (!submitting) submitting = true; else $event.preventDefault();">
                        @csrf @method('PATCH')
                        <button type="submit" :disabled="submitting" :class="submitting ? 'opacity-60 cursor-not-allowed' : ''" class="btn-action btn-audit">
                            <svg x-show="submitting" class="spinner" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                            </svg>
                            <span x-text="submitting ? 'Processing…' : 'Mark as Audited'"></span>
                        </button>
                    </form>
                @endif

                @if($canApprove)
                    <form method="POST" action="{{ route('financial.approve', $transaction->id) }}" x-data="{ submitting: false }" @submit="if (!submitting) submitting = true; else $event.preventDefault();">
                        @csrf @method('PATCH')
                        <button type="submit" :disabled="submitting" :class="submitting ? 'opacity-60 cursor-not-allowed' : ''" class="btn-action btn-approve">
                            <svg x-show="submitting" class="spinner" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                            </svg>
                            <span x-text="submitting ? 'Processing…' : 'Approve'"></span>
                        </button>
                    </form>
                @endif

                @if($canReject)
                    <form method="POST" action="{{ route('financial.reject', $transaction->id) }}" x-data="{ submitting: false }" @submit.prevent="if (!confirm('Reject this transaction? This action cannot be undone.')) return; submitting = true; $el.submit();">
                        @csrf @method('PATCH')
                        <button type="submit" :disabled="submitting" :class="submitting ? 'opacity-60 cursor-not-allowed' : ''" class="btn-action btn-reject">
                            <svg x-show="submitting" class="spinner" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                            </svg>
                            <span x-text="submitting ? 'Processing…' : 'Reject'"></span>
                        </button>
                    </form>
                @endif

                @if($canDelete)
                    <form method="POST" action="{{ route('financial.destroy', $transaction->id) }}" x-data="{ submitting: false }" @submit.prevent="if (!confirm('Delete this transaction? It will be moved to Trash.')) return; submitting = true; $el.submit();">
                        @csrf @method('DELETE')
                        <button type="submit" :disabled="submitting" :class="submitting ? 'opacity-60 cursor-not-allowed' : ''" class="btn-action btn-delete">
                            <svg x-show="submitting" class="spinner" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                            </svg>
                            <span x-text="submitting ? 'Deleting…' : 'Delete'"></span>
                        </button>
                    </form>
                @endif

                <a href="{{ route('financial.index') }}" class="btn-action btn-back">
                    ← Back to List
                </a>
            </div>

        </div>
    </div>
</div>

@endsection