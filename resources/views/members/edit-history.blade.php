@extends('layouts.app')

@section('title', 'Edit History - ' . ($user->full_name ?? 'Member') . ' — VSULHS SSLG')
@section('page-title', 'Edit History')

@push('styles')
<style>
/* ════════════════════════════════════════════════
   EDIT HISTORY — Emerald & Gold Luxury Theme
   Matching all management views
════════════════════════════════════════════════ */

/* ── Container ── */
.history-container {
    max-width: 56rem;
    margin: 0 auto;
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

/* ── Header Section ── */
.history-header {
    margin-top: 0.5rem;
}
.history-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text);
    letter-spacing: -0.02em;
    font-family: 'DM Serif Display', serif;
}
.history-subtitle {
    font-size: 0.8rem;
    color: var(--text-3);
    margin-top: 0.25rem;
}
.history-meta {
    font-size: 0.7rem;
    color: var(--text-3);
    margin-top: 0.5rem;
    font-family: 'DM Mono', monospace;
}
.history-meta span {
    color: var(--emerald-dark);
}
html.dark .history-meta span {
    color: var(--emerald-light);
}

/* ── Card ── */
.history-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 1.25rem;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
    margin-top: 1.5rem;
}
html.dark .history-card {
    box-shadow: 0 4px 20px rgba(0,0,0,0.22);
}
.history-card-header {
    padding: 1rem 1.5rem;
    border-bottom: 1px solid var(--border);
    background: var(--surface-2);
}
.history-card-header h2 {
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: var(--text-2);
    font-family: 'DM Mono', monospace;
}

/* ── History Items ── */
.history-item {
    padding: 1.5rem;
    border-bottom: 1px solid var(--border);
    transition: background 0.15s ease;
}
.history-item:last-child {
    border-bottom: none;
}
.history-item:hover {
    background: rgba(212,175,55,0.025);
    box-shadow: inset 3px 0 0 var(--gold);
}

.history-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.2rem 0.65rem;
    border-radius: 9999px;
    font-size: 0.65rem;
    font-weight: 700;
    font-family: 'DM Mono', monospace;
    background: rgba(59,130,246,0.1);
    color: #3b82f6;
    border: 1px solid rgba(59,130,246,0.2);
}
html.dark .history-badge {
    background: rgba(59,130,246,0.15);
    color: #60a5fa;
}

.history-date {
    font-size: 0.7rem;
    color: var(--text-3);
    font-family: 'DM Mono', monospace;
}

/* ── Position Change Visual ── */
.position-change {
    margin-top: 0.75rem;
    margin-bottom: 0.75rem;
}
.position-from {
    font-size: 0.8rem;
    color: var(--text-2);
    background: var(--surface-3);
    padding: 0.25rem 0.75rem;
    border-radius: 0.5rem;
    font-family: 'DM Mono', monospace;
}
.position-arrow {
    color: var(--gold-dark);
    margin: 0 0.5rem;
    font-size: 0.8rem;
}
.position-to {
    font-size: 0.8rem;
    color: var(--text);
    font-weight: 600;
    background: rgba(5,150,105,0.1);
    padding: 0.25rem 0.75rem;
    border-radius: 0.5rem;
    font-family: 'DM Mono', monospace;
}
html.dark .position-to {
    background: rgba(16,185,129,0.15);
    color: #6ee7b7;
}

/* ── Reason Box ── */
.reason-box {
    margin-top: 0.75rem;
    padding: 0.75rem 1rem;
    background: rgba(212,175,55,0.05);
    border-left: 3px solid var(--gold);
    border-radius: 0.5rem;
}
.reason-box p {
    font-size: 0.7rem;
    color: var(--gold-dark);
    line-height: 1.5;
}
.reason-box .reason-label {
    font-weight: 700;
    margin-right: 0.5rem;
}
html.dark .reason-box {
    background: rgba(212,175,55,0.08);
}
html.dark .reason-box p {
    color: var(--gold-light);
}

/* ── Meta Information ── */
.history-meta-info {
    margin-top: 0.75rem;
    font-size: 0.65rem;
    color: var(--text-3);
    font-family: 'DM Mono', monospace;
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
}
.history-meta-info span {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
}

/* ── Empty State ── */
.empty-state {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 1rem;
    padding: 3rem 2rem;
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
    font-size: 1rem;
    font-weight: 600;
    color: var(--text-2);
}
.empty-desc {
    font-size: 0.75rem;
    color: var(--text-3);
    max-width: 300px;
}
.empty-info-box {
    margin-top: 1rem;
    padding: 1rem;
    background: var(--surface-2);
    border-radius: 0.75rem;
    text-align: left;
    width: 100%;
    max-width: 400px;
}
.empty-info-box p {
    font-size: 0.7rem;
    color: var(--text-3);
    margin-bottom: 0.5rem;
}
.empty-info-box .info-label {
    font-weight: 700;
    color: var(--text-2);
    margin-bottom: 0.25rem;
}
.empty-info-box .info-value {
    font-family: 'DM Mono', monospace;
    color: var(--emerald-dark);
}
html.dark .empty-info-box .info-value {
    color: var(--emerald-light);
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
<div class="history-container">
    
    {{-- Back Button --}}
    <div class="anim-1">
        <a href="{{ route('members.edit', $user->id) }}" class="back-btn">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Edit Member
        </a>
        
        <div class="history-header">
            <h1 class="history-title">Edit History</h1>
            <p class="history-subtitle">{{ $user->full_name }}</p>
            <p class="history-meta">
                Current Role: <span>{{ $user->role->name ?? 'N/A' }}</span> | 
                Current Position: <span>{{ $user->position ?? 'N/A' }}</span>
            </p>
        </div>
    </div>
    
    {{-- History Card --}}
    <div class="history-card anim-2">
        <div class="history-card-header">
            <h2>📋 Position Change History</h2>
        </div>
        
        <div class="divide-y divide-border">
            @forelse($positionLogs as $log)
            <div class="history-item">
                <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
                    <div class="flex items-center gap-2">
                        <span class="history-badge">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                            </svg>
                            Position Change
                        </span>
                        <span class="history-date">{{ $log->created_at->format('M d, Y H:i:s') }}</span>
                    </div>
                </div>
                
                <div class="position-change">
                    <span class="position-from">{{ $log->old_position ?: 'Not set' }}</span>
                    <span class="position-arrow">→</span>
                    <span class="position-to">{{ $log->new_position ?: 'Not set' }}</span>
                </div>
                
                @if($log->reason)
                <div class="reason-box">
                    <p>
                        <span class="reason-label">📝 Reason:</span>
                        {{ $log->reason }}
                    </p>
                </div>
                @endif
                
                <div class="history-meta-info">
                    <span>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        Changed by: {{ $log->changer->full_name ?? 'Unknown' }}
                    </span>
                    @if($log->ip_address)
                    <span>
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M12 5l7 7-7 7"/>
                        </svg>
                        IP: {{ $log->ip_address }}
                    </span>
                    @endif
                </div>
            </div>
            @empty
            <div class="empty-state">
                <div class="empty-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h3 class="empty-title">No Position Change History Yet</h3>
                <p class="empty-desc">
                    When you change a member's position and provide a reason, it will appear here.
                </p>
                
                <div class="empty-info-box">
                    <p class="info-label">📌 Current Information:</p>
                    <p>• Current Position: <span class="info-value">{{ $user->position ?? 'Not set' }}</span></p>
                    <p>• Current Role: <span class="info-value">{{ $user->role->name ?? 'Not set' }}</span></p>
                    <p>• Member Since: <span class="info-value">{{ optional($user->member->joined_at)->format('M d, Y') ?? 'Not set' }}</span></p>
                </div>
            </div>
            @endforelse
        </div>
        
        @if($positionLogs->hasPages())
        <div class="pag-wrap">
            <p class="pag-info">
                {{ $positionLogs->firstItem() }}–{{ $positionLogs->lastItem() }} of {{ $positionLogs->total() }} records
            </p>
            <div class="pag-btns">
                @if($positionLogs->onFirstPage())
                    <span class="pag-btn disabled">← Prev</span>
                @else
                    <a href="{{ $positionLogs->previousPageUrl() }}" class="pag-btn">← Prev</a>
                @endif
                @foreach($positionLogs->getUrlRange(max(1, $positionLogs->currentPage() - 2), min($positionLogs->lastPage(), $positionLogs->currentPage() + 2)) as $page => $url)
                    @if($page == $positionLogs->currentPage())
                        <span class="pag-btn current">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="pag-btn">{{ $page }}</a>
                    @endif
                @endforeach
                @if($positionLogs->hasMorePages())
                    <a href="{{ $positionLogs->nextPageUrl() }}" class="pag-btn">Next →</a>
                @else
                    <span class="pag-btn disabled">Next →</span>
                @endif
            </div>
        </div>
        @endif
    </div>
    
</div>
@endsection