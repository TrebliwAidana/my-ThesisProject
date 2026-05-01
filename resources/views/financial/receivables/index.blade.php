@extends('layouts.app')

@section('title', 'Accounts Receivable — VSULHS SSLG')
@section('page-title', 'Accounts Receivable')

@push('styles')
<style>
/* ════════════════════════════════════════════════
   ACCOUNTS RECEIVABLE — Emerald & Gold Luxury Theme
   Matching all management views
════════════════════════════════════════════════ */

/* ── Hero Section ── */
.receivable-hero {
    position: relative;
    overflow: hidden;
    border-radius: 1.25rem;
    padding: 1.75rem 2rem;
    isolation: isolate;
    background: linear-gradient(135deg, #064E3B 0%, #065F46 35%, #047857 60%, #0A3A28 100%);
}
.receivable-hero::before {
    content: '';
    position: absolute; inset: 0;
    background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,1) 1px, transparent 0);
    background-size: 28px 28px;
    opacity: 0.04; z-index: 0;
}
.receivable-hero::after {
    content: '';
    position: absolute;
    top: -60px; right: -60px;
    width: 280px; height: 280px;
    background: radial-gradient(circle, rgba(212,175,55,0.35), transparent 65%);
    filter: blur(48px); z-index: 0;
}
.receivable-hero-content { position: relative; z-index: 1; }

.receivable-hero-title {
    font-family: 'DM Serif Display', serif;
    font-size: clamp(1.5rem, 3.5vw, 2.2rem);
    color: #fff;
    letter-spacing: -0.02em;
    line-height: 1.1;
}
.receivable-hero-title span {
    background: linear-gradient(90deg, #F0CC55, #D4AF37);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.receivable-hero-pill {
    display: inline-flex; align-items: center; gap: 0.4rem;
    padding: 0.3rem 0.75rem;
    background: rgba(255,255,255,0.1);
    border: 1px solid rgba(212,175,55,0.28);
    border-radius: 999px;
    font-size: 0.72rem; font-weight: 600;
    color: rgba(255,255,255,0.88);
    font-family: 'DM Mono', monospace;
}

/* ── Summary Card ── */
.summary-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 1rem;
    padding: 1.25rem;
    position: relative;
    overflow: hidden;
    transition: all 0.25s cubic-bezier(0.4,0,0.2,1);
}
.summary-card::after {
    content: '';
    position: absolute;
    bottom: 0; left: 0; right: 0;
    height: 2px;
    transform: scaleX(0);
    transform-origin: left;
    transition: transform 0.3s ease;
    background: linear-gradient(90deg, #7c3aed, #a78bfa);
}
.summary-card:hover {
    border-color: rgba(212,175,55,0.35);
    transform: translateY(-2px);
    box-shadow: 0 8px 28px rgba(0,0,0,0.08), 0 0 0 1px rgba(212,175,55,0.12);
}
.summary-card:hover::after {
    transform: scaleX(1);
}
.summary-label {
    font-size: 0.68rem;
    font-weight: 700;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: var(--text-3);
    font-family: 'DM Mono', monospace;
}
.summary-value {
    font-size: 1.75rem;
    font-weight: 700;
    color: #dc2626;
    font-family: 'DM Mono', monospace;
    margin-top: 0.25rem;
}
.summary-sub {
    font-size: 0.65rem;
    color: var(--text-3);
    margin-top: 0.25rem;
}

/* ── Action Button ── */
.btn-purple {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.6rem 1.2rem;
    font-size: 0.8rem;
    font-weight: 700;
    background: linear-gradient(135deg, #7c3aed, #6d28d9);
    color: #fff;
    border: none;
    border-radius: 0.75rem;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
    font-family: 'Outfit', sans-serif;
    box-shadow: 0 2px 10px rgba(124,58,237,0.22);
}
.btn-purple:hover {
    background: linear-gradient(135deg, var(--gold), var(--gold-dark));
    color: #0f172a;
    transform: translateY(-1px);
    box-shadow: 0 4px 16px rgba(212,175,55,0.35);
}

/* ── Table Styles ── */
.receivable-table-wrap {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 1.25rem;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
}
html.dark .receivable-table-wrap {
    box-shadow: 0 4px 20px rgba(0,0,0,0.22);
}

.receivable-table {
    width: 100%;
    min-width: 900px;
    border-collapse: collapse;
    font-size: 0.82rem;
}
.receivable-table thead tr {
    background: linear-gradient(135deg, #064E3B 0%, #047857 60%, #065F46 100%);
}
.receivable-table th {
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
.receivable-table th.text-right { text-align: right; }
.receivable-table th.text-center { text-align: center; }
.receivable-table tbody tr {
    border-bottom: 1px solid var(--border);
    transition: background 0.15s ease;
}
.receivable-table tbody tr:last-child { border-bottom: none; }
.receivable-table tbody tr:hover {
    background: rgba(212,175,55,0.025);
    box-shadow: inset 3px 0 0 var(--gold);
}
.receivable-table td {
    padding: 0.75rem 1rem;
    color: var(--text-2);
    vertical-align: middle;
}
.receivable-table td.text-right { text-align: right; }
.receivable-table td.text-center { text-align: center; }

/* ── Status Badges ── */
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

/* ── Reference Number ── */
.reference-number {
    font-family: 'DM Mono', monospace;
    font-size: 0.7rem;
    color: var(--text-3);
}

/* ── Amount Styling ── */
.amount-positive {
    color: #059669;
}
.amount-negative {
    color: #dc2626;
}
.amount-neutral {
    color: var(--text-2);
}

/* ── Overdue Indicator ── */
.overdue-indicator {
    color: #dc2626;
    font-size: 0.55rem;
    font-weight: 600;
    margin-left: 0.25rem;
}

/* ── View Action Link ── */
.view-link {
    color: var(--emerald);
    text-decoration: none;
    font-size: 0.7rem;
    font-weight: 600;
    font-family: 'DM Mono', monospace;
    transition: color 0.15s ease;
}
.view-link:hover {
    color: var(--gold-dark);
}
html.dark .view-link:hover {
    color: var(--gold-light);
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
    <div class="receivable-hero anim-1">
        <div class="receivable-hero-content">
            <p class="text-emerald-300/70 text-[10px] font-bold tracking-[0.2em] uppercase mb-2"
               style="font-family:'DM Mono',monospace;">
                {{ now()->format('F Y') }} · Financial Management
            </p>
            <h1 class="receivable-hero-title mb-3">Accounts<br><span>Receivable</span></h1>
            <div class="flex flex-wrap gap-2">
                <span class="receivable-hero-pill">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Track outstanding payments
                </span>
                <span class="receivable-hero-pill">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Monitor collection status
                </span>
            </div>
        </div>
    </div>

    {{-- Summary Card --}}
    <div class="summary-card anim-2">
        <p class="summary-label">Total Outstanding Receivables</p>
        <p class="summary-value">₱{{ number_format($totalOutstanding, 2) }}</p>
        <p class="summary-sub">Unpaid balance from all pending/partial/overdue receivables</p>
    </div>

    {{-- Actions --}}
    <div class="anim-2">
        <a href="{{ route('financial.receivable.create') }}" class="btn-purple">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            New Receivable
        </a>
    </div>

    {{-- Receivables Table --}}
    <div class="receivable-table-wrap anim-3">
        <div class="overflow-x-auto">
            <table class="receivable-table">
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>Customer</th>
                        <th>Description</th>
                        <th class="text-right">Total</th>
                        <th class="text-right">Paid</th>
                        <th class="text-right">Remaining</th>
                        <th>Due Date</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($receivables as $rec)
                    @php
                        $remaining = $rec->total_amount - $rec->paid_amount;
                        $statusClass = match($rec->status) {
                            'pending' => 'status-pending',
                            'partial' => 'status-partial',
                            'paid' => 'status-paid',
                            'overdue' => 'status-overdue',
                            default => 'status-pending',
                        };
                    @endphp
                    <tr>
                        <td class="reference-number">{{ $rec->reference_no }}</td>
                        <td class="font-medium text-text">{{ $rec->customer_name ?? '—' }}</td>
                        <td class="max-w-xs truncate">{{ $rec->description }}</td>
                        <td class="text-right amount-neutral">₱{{ number_format($rec->total_amount, 2) }}</td>
                        <td class="text-right amount-positive">₱{{ number_format($rec->paid_amount, 2) }}</td>
                        <td class="text-right {{ $remaining > 0 ? 'amount-negative' : 'amount-positive' }} font-semibold">
                            ₱{{ number_format($remaining, 2) }}
                        </td>
                        <td class="whitespace-nowrap">
                            @if($rec->due_date)
                                {{ \Carbon\Carbon::parse($rec->due_date)->format('M d, Y') }}
                                @if($rec->due_date < now()->toDateString() && $rec->status !== 'paid')
                                    <span class="overdue-indicator">(Overdue)</span>
                                @endif
                            @else
                                —
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="status-badge {{ $statusClass }}">
                                {{ ucfirst($rec->status) }}
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="{{ route('financial.receivable.show', $rec) }}" class="view-link">
                                View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9">
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <h3 class="empty-title">No receivables found</h3>
                                <p class="empty-desc">Start by adding a new receivable record.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
             </table>
        </div>

        @if($receivables->hasPages())
        <div class="pag-wrap">
            <p class="pag-info">
                {{ $receivables->firstItem() }}–{{ $receivables->lastItem() }} of {{ $receivables->total() }} receivables
            </p>
            <div class="pag-btns">
                @if($receivables->onFirstPage())
                    <span class="pag-btn disabled">← Prev</span>
                @else
                    <a href="{{ $receivables->previousPageUrl() }}" class="pag-btn">← Prev</a>
                @endif
                @foreach($receivables->getUrlRange(max(1, $receivables->currentPage() - 2), min($receivables->lastPage(), $receivables->currentPage() + 2)) as $page => $url)
                    @if($page == $receivables->currentPage())
                        <span class="pag-btn current">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="pag-btn">{{ $page }}</a>
                    @endif
                @endforeach
                @if($receivables->hasMorePages())
                    <a href="{{ $receivables->nextPageUrl() }}" class="pag-btn">Next →</a>
                @else
                    <span class="pag-btn disabled">Next →</span>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
@endsection