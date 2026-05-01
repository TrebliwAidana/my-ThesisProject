@extends('layouts.app')

@section('title', 'Financial Trash — VSULHS SSLG')
@section('page-title', 'Trash – Financial Transactions')

@push('styles')
<style>
/* ════════════════════════════════════════════════
   FINANCIAL TRASH — Emerald & Gold Luxury Theme
   Matching all management views
════════════════════════════════════════════════ */

/* ── Hero Section ── */
.trash-hero {
    position: relative;
    overflow: hidden;
    border-radius: 1.25rem;
    padding: 1.75rem 2rem;
    isolation: isolate;
    background: linear-gradient(135deg, #064E3B 0%, #065F46 35%, #047857 60%, #0A3A28 100%);
}
.trash-hero::before {
    content: '';
    position: absolute; inset: 0;
    background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,1) 1px, transparent 0);
    background-size: 28px 28px;
    opacity: 0.04; z-index: 0;
}
.trash-hero::after {
    content: '';
    position: absolute;
    top: -60px; right: -60px;
    width: 280px; height: 280px;
    background: radial-gradient(circle, rgba(212,175,55,0.35), transparent 65%);
    filter: blur(48px); z-index: 0;
}
.trash-hero-content { position: relative; z-index: 1; }

.trash-hero-title {
    font-family: 'DM Serif Display', serif;
    font-size: clamp(1.5rem, 3.5vw, 2.2rem);
    color: #fff;
    letter-spacing: -0.02em;
    line-height: 1.1;
}
.trash-hero-title span {
    background: linear-gradient(90deg, #F0CC55, #D4AF37);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.trash-hero-pill {
    display: inline-flex; align-items: center; gap: 0.4rem;
    padding: 0.3rem 0.75rem;
    background: rgba(255,255,255,0.1);
    border: 1px solid rgba(212,175,55,0.28);
    border-radius: 999px;
    font-size: 0.72rem; font-weight: 600;
    color: rgba(255,255,255,0.88);
    font-family: 'DM Mono', monospace;
}

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

/* ── Table Styles ── */
.trash-table-wrap {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 1.25rem;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
}
html.dark .trash-table-wrap {
    box-shadow: 0 4px 20px rgba(0,0,0,0.22);
}

.trash-table {
    width: 100%;
    min-width: 900px;
    border-collapse: collapse;
    font-size: 0.82rem;
}
.trash-table thead tr {
    background: linear-gradient(135deg, #064E3B 0%, #047857 60%, #065F46 100%);
}
.trash-table th {
    padding: 0.7rem 1rem;
    text-align: left;
    font-size: 0.63rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: rgba(255,255,255,0.82);
    font-family: 'DM Mono', monospace;
    white-space: nowrap;
}
.trash-table th.text-right { text-align: right; }
.trash-table th.text-center { text-align: center; }
.trash-table tbody tr {
    border-bottom: 1px solid var(--border);
    transition: background 0.15s ease;
}
.trash-table tbody tr:last-child { border-bottom: none; }
.trash-table tbody tr:hover {
    background: rgba(212,175,55,0.025);
    box-shadow: inset 3px 0 0 var(--gold);
}
.trash-table td {
    padding: 0.75rem 1rem;
    color: var(--text-2);
    vertical-align: middle;
}
.trash-table td.text-right { text-align: right; }
.trash-table td.text-center { text-align: center; }

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
html.dark .type-income {
    background: rgba(16,185,129,0.15);
    color: #6ee7b7;
}
html.dark .type-expense {
    background: rgba(248,113,113,0.12);
    color: #fca5a5;
}

/* ── Status Badge ── */
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.2rem 0.65rem;
    border-radius: 9999px;
    font-size: 0.65rem;
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
html.dark .status-pending {
    background: rgba(245,158,11,0.15);
    color: #fcd34d;
}
html.dark .status-audited {
    background: rgba(59,130,246,0.15);
    color: #60a5fa;
}
html.dark .status-approved {
    background: rgba(16,185,129,0.15);
    color: #6ee7b7;
}
html.dark .status-rejected {
    background: rgba(248,113,113,0.15);
    color: #fca5a5;
}

/* ── Amount Styling ── */
.amount-income {
    color: #059669;
}
.amount-expense {
    color: #dc2626;
}

/* ── Action Buttons ── */
.action-restore {
    color: var(--emerald);
    font-size: 0.7rem;
    font-weight: 600;
    font-family: 'DM Mono', monospace;
    text-decoration: none;
    transition: color 0.15s ease;
}
.action-restore:hover {
    color: var(--gold-dark);
}
.action-delete {
    color: #64748b;
    font-size: 0.7rem;
    font-weight: 600;
    font-family: 'DM Mono', monospace;
    text-decoration: none;
    transition: all 0.15s ease;
}
.action-delete:hover {
    color: #dc2626;
}
.action-divider {
    color: var(--border);
    margin: 0 0.25rem;
}

/* ── Empty State ── */
.empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.75rem;
    padding: 3rem 1rem;
    text-align: center;
}
.empty-icon {
    width: 4rem;
    height: 4rem;
    border-radius: 50%;
    background: linear-gradient(135deg, rgba(5,150,105,0.1), rgba(212,175,55,0.08));
    border: 1.5px dashed rgba(212,175,55,0.3);
    display: flex;
    align-items: center;
    justify-content: center;
}
.empty-icon svg {
    width: 2rem;
    height: 2rem;
    stroke: var(--text-3);
}
.empty-title {
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--text-2);
}
.empty-desc {
    font-size: 0.75rem;
    color: var(--text-3);
}

/* ── Pagination ── */
.pag-wrap {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 0.75rem;
    padding: 0.875rem 1.25rem;
    border-top: 1px solid var(--border);
    background: var(--surface-2);
}
.pag-info {
    font-size: 0.7rem;
    color: var(--text-3);
    font-family: 'DM Mono', monospace;
}
.pag-btns {
    display: flex;
    gap: 0.25rem;
}
.pag-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-width: 2rem;
    height: 2rem;
    padding: 0 0.5rem;
    font-size: 0.7rem;
    font-family: 'DM Mono', monospace;
    font-weight: 600;
    border-radius: 0.5rem;
    border: 1.5px solid var(--border);
    color: var(--text-3);
    background: var(--surface);
    text-decoration: none;
    transition: all 0.15s ease;
}
.pag-btn:not(.disabled):not(.current):hover {
    border-color: var(--gold);
    color: var(--gold-dark);
    background: rgba(212,175,55,0.08);
}
html.dark .pag-btn:not(.disabled):not(.current):hover {
    color: var(--gold-light);
}
.pag-btn.current {
    background: linear-gradient(135deg, var(--emerald), var(--emerald-dark));
    border-color: var(--emerald-dark);
    color: #fff;
    box-shadow: 0 2px 10px rgba(5,150,105,0.3);
}
.pag-btn.disabled {
    opacity: 0.35;
    cursor: not-allowed;
    pointer-events: none;
}

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

    {{-- Hero Section --}}
    <div class="trash-hero anim-1">
        <div class="trash-hero-content">
            <p class="text-emerald-300/70 text-[10px] font-bold tracking-[0.2em] uppercase mb-2"
               style="font-family:'DM Mono',monospace;">
                {{ now()->format('F Y') }} · Financial Management
            </p>
            <h1 class="trash-hero-title mb-3">Financial<br><span>Trash</span></h1>
            <div class="flex flex-wrap gap-2">
                <span class="trash-hero-pill">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    {{ $transactions->total() }} Deleted Transactions
                </span>
                <span class="trash-hero-pill">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Restore or permanently delete
                </span>
            </div>
        </div>
    </div>

    {{-- Back Button --}}
    <div class="anim-1">
        <a href="{{ route('financial.index') }}" class="back-btn">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Back to Financial Records
        </a>
    </div>

    {{-- Trash Table --}}
    <div class="trash-table-wrap anim-2">
        <div class="overflow-x-auto">
            <table class="trash-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Description</th>
                        <th>Category</th>
                        <th>Type</th>
                        <th class="text-right">Amount</th>
                        <th>Status</th>
                        <th>Deleted At</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $tx)
                    @php
                        $typeClass = $tx->type === 'income' ? 'type-income' : 'type-expense';
                        $statusClass = match($tx->status) {
                            'pending' => 'status-pending',
                            'audited' => 'status-audited',
                            'approved' => 'status-approved',
                            'rejected' => 'status-rejected',
                            default => 'status-pending',
                        };
                        $amountClass = $tx->type === 'income' ? 'amount-income' : 'amount-expense';
                        $amountPrefix = $tx->type === 'income' ? '+' : '-';
                    @endphp
                    <tr>
                        <td class="text-text-3 font-mono text-xs">{{ $tx->transaction_date->format('M d, Y') }}</td>
                        <td class="font-medium text-text">{{ $tx->description }}</td>
                        <td class="text-text-3">{{ $tx->category ?? '—' }}</td>
                        <td>
                            <span class="type-badge {{ $typeClass }}">
                                {{ ucfirst($tx->type) }}
                            </span>
                        </td>
                        <td class="text-right font-semibold {{ $amountClass }}">
                            {{ $amountPrefix }}{{ number_format($tx->amount, 2) }}
                        </td>
                        <td>
                            <span class="status-badge {{ $statusClass }}">
                                {{ ucfirst($tx->status) }}
                            </span>
                        </td>
                        <td class="text-text-3 text-xs font-mono">{{ $tx->deleted_at->format('M d, Y H:i') }}</td>
                        <td class="text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <form method="POST" action="{{ route('financial.restore', $tx->id) }}" class="inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="action-restore">Restore</button>
                                </form>
                                <span class="action-divider">|</span>
                                <form method="POST" action="{{ route('financial.force-delete', $tx->id) }}" 
                                      onsubmit="return confirm('⚠️ Permanently delete this transaction and all its attached documents?\n\nThis action cannot be undone.')" 
                                      class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="action-delete">Delete Forever</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8">
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </div>
                                <h3 class="empty-title">Trash is empty</h3>
                                <p class="empty-desc">No deleted financial transactions found.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
        <div class="pag-wrap">
            <p class="pag-info">
                {{ $transactions->firstItem() }}–{{ $transactions->lastItem() }} of {{ $transactions->total() }} deleted transactions
            </p>
            <div class="pag-btns">
                @if($transactions->onFirstPage())
                    <span class="pag-btn disabled">← Prev</span>
                @else
                    <a href="{{ $transactions->previousPageUrl() }}" class="pag-btn">← Prev</a>
                @endif
                @foreach($transactions->getUrlRange(max(1, $transactions->currentPage() - 2), min($transactions->lastPage(), $transactions->currentPage() + 2)) as $page => $url)
                    @if($page == $transactions->currentPage())
                        <span class="pag-btn current">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="pag-btn">{{ $page }}</a>
                    @endif
                @endforeach
                @if($transactions->hasMorePages())
                    <a href="{{ $transactions->nextPageUrl() }}" class="pag-btn">Next →</a>
                @else
                    <span class="pag-btn disabled">Next →</span>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
@endsection