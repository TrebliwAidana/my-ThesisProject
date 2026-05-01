@extends('layouts.app')

@section('title', 'Audit Logs — VSULHS SSLG')
@section('page-title', 'Audit Logs')

@push('styles')
<style>
/* ════════════════════════════════════════════════
   AUDIT LOGS — Emerald & Gold Luxury Theme
   Matching all other management views
════════════════════════════════════════════════ */

/* ── Hero Section ── */
.audit-hero {
    position: relative;
    overflow: hidden;
    border-radius: 1.25rem;
    padding: 1.75rem 2rem;
    isolation: isolate;
    background: linear-gradient(135deg, #064E3B 0%, #065F46 35%, #047857 60%, #0A3A28 100%);
}
.audit-hero::before {
    content: '';
    position: absolute; inset: 0;
    background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,1) 1px, transparent 0);
    background-size: 28px 28px;
    opacity: 0.04; z-index: 0;
}
.audit-hero::after {
    content: '';
    position: absolute;
    top: -60px; right: -60px;
    width: 280px; height: 280px;
    background: radial-gradient(circle, rgba(212,175,55,0.35), transparent 65%);
    filter: blur(48px); z-index: 0;
}
.audit-hero-content { position: relative; z-index: 1; }

.audit-hero-title {
    font-family: 'DM Serif Display', serif;
    font-size: clamp(1.5rem, 3.5vw, 2.2rem);
    color: #fff;
    letter-spacing: -0.02em;
    line-height: 1.1;
}
.audit-hero-title span {
    background: linear-gradient(90deg, #F0CC55, #D4AF37);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.audit-hero-pill {
    display: inline-flex; align-items: center; gap: 0.4rem;
    padding: 0.3rem 0.75rem;
    background: rgba(255,255,255,0.1);
    border: 1px solid rgba(212,175,55,0.28);
    border-radius: 999px;
    font-size: 0.72rem; font-weight: 600;
    color: rgba(255,255,255,0.88);
    font-family: 'DM Mono', monospace;
}

/* ── Stat Cards ── */
.audit-stat-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 1rem;
}
.audit-stat-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 1rem;
    padding: 1.1rem 1.2rem;
    position: relative;
    overflow: hidden;
    transition: all 0.25s cubic-bezier(0.4,0,0.2,1);
    cursor: default;
}
.audit-stat-card::after {
    content: '';
    position: absolute;
    bottom: 0; left: 0; right: 0;
    height: 2px;
    transform: scaleX(0);
    transform-origin: left;
    transition: transform 0.3s ease;
    border-radius: 0 0 999px 999px;
}
.audit-stat-card:hover {
    border-color: rgba(212,175,55,0.35);
    transform: translateY(-3px);
    box-shadow: 0 8px 28px rgba(0,0,0,0.08), 0 0 0 1px rgba(212,175,55,0.12);
}
.audit-stat-card:hover::after { transform: scaleX(1); }

.audit-stat-card.total::after { background: linear-gradient(90deg, var(--emerald), var(--gold)); }
.audit-stat-card.events::after { background: linear-gradient(90deg, #3b82f6, #60a5fa); }
.audit-stat-card.users::after { background: linear-gradient(90deg, #7c3aed, #a78bfa); }
.audit-stat-card.recent::after { background: linear-gradient(90deg, #d97706, #f59e0b); }

.audit-stat-icon {
    width: 2.1rem; height: 2.1rem;
    border-radius: 0.6rem;
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 0.75rem;
    flex-shrink: 0;
}
.audit-stat-num {
    font-family: 'DM Mono', monospace;
    font-size: 1.6rem;
    font-weight: 700;
    letter-spacing: -0.04em;
    line-height: 1;
    margin-bottom: 0.25rem;
    color: var(--text);
}
.audit-stat-label {
    font-size: 0.67rem; font-weight: 700;
    letter-spacing: 0.09em; text-transform: uppercase;
    color: var(--text-3); font-family: 'DM Mono', monospace;
}
.audit-stat-sub {
    font-size: 0.65rem; color: var(--text-3);
    margin-top: 0.2rem; opacity: 0.75;
}

/* ── Filter Panel ── */
.audit-panel {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 1.25rem;
    padding: 1.25rem;
    box-shadow: 0 2px 12px rgba(0,0,0,0.04);
}
.audit-form-label {
    display: block;
    font-size: 0.65rem;
    font-weight: 700;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    color: var(--text-3);
    margin-bottom: 0.35rem;
    font-family: 'DM Mono', monospace;
}
.audit-select {
    padding: 0.5rem 0.75rem;
    font-size: 0.8rem;
    background: var(--surface-2);
    border: 1.5px solid var(--border);
    border-radius: 0.75rem;
    color: var(--text);
    font-family: 'Outfit', sans-serif;
    cursor: pointer;
    transition: all 0.2s ease;
    min-width: 140px;
}
.audit-select:focus {
    outline: none;
    border-color: var(--gold);
    box-shadow: 0 0 0 3px rgba(212,175,55,0.12);
}
html.dark .audit-select { background: rgba(15,23,42,0.5); }

.audit-input {
    padding: 0.5rem 0.75rem;
    font-size: 0.8rem;
    background: var(--surface-2);
    border: 1.5px solid var(--border);
    border-radius: 0.75rem;
    color: var(--text);
    font-family: 'Outfit', sans-serif;
    transition: all 0.2s ease;
    width: 100%;
    min-width: 160px;
}
.audit-input:focus {
    outline: none;
    border-color: var(--gold);
    box-shadow: 0 0 0 3px rgba(212,175,55,0.12);
    background: var(--surface);
}
.audit-input::placeholder { color: var(--text-3); }
html.dark .audit-input { background: rgba(15,23,42,0.5); }

/* ── Buttons ── */
.btn-filter {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.5rem 1rem;
    font-size: 0.8rem;
    font-weight: 700;
    background: linear-gradient(135deg, var(--gold), var(--gold-dark));
    color: #0f172a;
    border: none;
    border-radius: 0.65rem;
    cursor: pointer;
    transition: all 0.2s ease;
    box-shadow: 0 2px 10px rgba(212,175,55,0.25);
    font-family: 'Outfit', sans-serif;
}
.btn-filter:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 16px rgba(212,175,55,0.4);
}
.btn-clear {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.5rem 1rem;
    font-size: 0.8rem;
    font-weight: 600;
    background: var(--surface-3);
    color: var(--text-2);
    border: 1.5px solid var(--border);
    border-radius: 0.65rem;
    cursor: pointer;
    transition: all 0.18s ease;
    text-decoration: none;
    font-family: 'Outfit', sans-serif;
}
.btn-clear:hover {
    border-color: rgba(212,175,55,0.4);
    color: var(--gold-dark);
    background: rgba(212,175,55,0.06);
}

/* ── Audit Table ── */
.audit-table-wrap {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 1.25rem;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
}
html.dark .audit-table-wrap { box-shadow: 0 4px 20px rgba(0,0,0,0.22); }

.audit-table {
    width: 100%;
    min-width: 900px;
    border-collapse: collapse;
    font-size: 0.82rem;
}
.audit-table thead tr {
    background: linear-gradient(135deg, #064E3B 0%, #047857 60%, #065F46 100%);
}
.audit-table th {
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
.audit-table tbody tr {
    border-bottom: 1px solid var(--border);
    transition: background 0.15s ease;
}
.audit-table tbody tr:last-child { border-bottom: none; }
.audit-table tbody tr:hover { 
    background: rgba(212,175,55,0.025); 
    box-shadow: inset 3px 0 0 var(--gold);
}
.audit-table td {
    padding: 0.75rem 1rem;
    color: var(--text-2);
    vertical-align: middle;
}

/* ── Event Badges ── */
.event-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.2rem 0.65rem;
    border-radius: 9999px;
    font-size: 0.65rem;
    font-weight: 700;
    font-family: 'DM Mono', monospace;
}
.event-badge-created { background: rgba(5,150,105,0.1); color: #047857; border: 1px solid rgba(5,150,105,0.2); }
.event-badge-updated { background: rgba(245,158,11,0.1); color: #d97706; border: 1px solid rgba(245,158,11,0.2); }
.event-badge-deleted { background: rgba(220,38,38,0.1); color: #dc2626; border: 1px solid rgba(220,38,38,0.2); }
.event-badge-restored { background: rgba(59,130,246,0.1); color: #2563eb; border: 1px solid rgba(59,130,246,0.2); }
.event-badge-login { background: rgba(139,92,246,0.1); color: #7c3aed; border: 1px solid rgba(139,92,246,0.2); }
.event-badge-logout { background: rgba(107,114,128,0.1); color: #6b7280; border: 1px solid rgba(107,114,128,0.2); }
.event-badge-default { background: rgba(107,114,128,0.1); color: #6b7280; border: 1px solid rgba(107,114,128,0.2); }

html.dark .event-badge-created { background: rgba(16,185,129,0.15); color: #34d399; }
html.dark .event-badge-updated { background: rgba(245,158,11,0.15); color: #fbbf24; }
html.dark .event-badge-deleted { background: rgba(248,113,113,0.15); color: #f87171; }
html.dark .event-badge-restored { background: rgba(59,130,246,0.15); color: #60a5fa; }
html.dark .event-badge-login { background: rgba(139,92,246,0.15); color: #a78bfa; }
html.dark .event-badge-logout { background: rgba(107,114,128,0.2); color: #cbd5e1; }
html.dark .event-badge-default { background: rgba(107,114,128,0.2); color: #cbd5e1; }

/* ── IP Address Badge ── */
.ip-badge {
    font-family: 'DM Mono', monospace;
    font-size: 0.7rem;
    padding: 0.15rem 0.5rem;
    background: var(--surface-3);
    border-radius: 0.5rem;
    display: inline-block;
    color: var(--text-3);
}

/* ── Pagination ── */
.pag-wrap {
    display: flex; flex-wrap: wrap;
    align-items: center; justify-content: space-between;
    gap: 0.75rem; padding: 0.875rem 1.25rem;
    border-top: 1px solid var(--border);
    background: var(--surface-2);
}
.pag-info { font-size: 0.7rem; color: var(--text-3); font-family: 'DM Mono', monospace; }
.pag-btns { display: flex; gap: 0.25rem; }
.pag-btn {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 2rem; height: 2rem; padding: 0 0.5rem;
    font-size: 0.7rem; font-family: 'DM Mono', monospace; font-weight: 600;
    border-radius: 0.5rem; border: 1.5px solid var(--border);
    color: var(--text-3); background: var(--surface);
    text-decoration: none; transition: all 0.15s ease;
}
.pag-btn:not(.disabled):not(.current):hover {
    border-color: var(--gold); color: var(--gold-dark);
    background: rgba(212,175,55,0.08);
}
html.dark .pag-btn:not(.disabled):not(.current):hover { color: var(--gold-light); }
.pag-btn.current {
    background: linear-gradient(135deg, var(--emerald), var(--emerald-dark));
    border-color: var(--emerald-dark); color: #fff;
    box-shadow: 0 2px 10px rgba(5,150,105,0.3);
}
.pag-btn.disabled { opacity: 0.35; cursor: not-allowed; pointer-events: none; }

/* ── Empty State ── */
.audit-empty {
    display: flex; flex-direction: column; align-items: center;
    gap: 0.75rem; padding: 4rem 1rem; text-align: center;
}
.audit-empty-ring {
    width: 4.5rem; height: 4.5rem; border-radius: 50%;
    background: linear-gradient(135deg, rgba(5,150,105,0.1), rgba(212,175,55,0.08));
    border: 1.5px dashed rgba(212,175,55,0.3);
    display: flex; align-items: center; justify-content: center;
    color: var(--text-3);
}

/* ── Animations ── */
@keyframes fadeUp {
    from { opacity: 0; transform: translateY(14px); }
    to   { opacity: 1; transform: translateY(0); }
}
.anim-1 { animation: fadeUp 0.38s ease 0.04s both; }
.anim-2 { animation: fadeUp 0.38s ease 0.10s both; }
.anim-3 { animation: fadeUp 0.38s ease 0.16s both; }
.anim-4 { animation: fadeUp 0.38s ease 0.22s both; }
</style>
@endpush

@section('content')
@php
    $totalLogs = $logs->total();
    $uniqueEvents = $events->count();
    $uniqueUsers = $logs->pluck('user_name')->unique()->filter()->count();
    $recentCount = $logs->where('created_at', '>=', now()->subDays(7))->count();
@endphp

<div class="space-y-5">

    {{-- ── HERO SECTION ── --}}
    <div class="audit-hero anim-1">
        <div class="audit-hero-content">
            <p class="text-emerald-300/70 text-[10px] font-bold tracking-[0.2em] uppercase mb-2"
               style="font-family:'DM Mono',monospace;">
                {{ now()->format('F Y') }} · System Activity
            </p>
            <h1 class="audit-hero-title mb-3">Audit<br><span>Logs</span></h1>
            <div class="flex flex-wrap gap-2">
                <span class="audit-hero-pill">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    {{ number_format($totalLogs) }} Total Activities
                </span>
                <span class="audit-hero-pill">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Track system changes
                </span>
            </div>
        </div>
    </div>

    {{-- ── STATS CARDS ── --}}
    <div class="audit-stat-grid anim-2">
        {{-- Total Logs --}}
        <div class="audit-stat-card total">
            <div class="audit-stat-icon" style="background: #05966915;">
                <svg class="w-4 h-4" fill="none" stroke="#059669" viewBox="0 0 24 24" stroke-width="1.75">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <div class="audit-stat-num">{{ number_format($totalLogs) }}</div>
            <div class="audit-stat-label">Total Logs</div>
            <div class="audit-stat-sub">All recorded events</div>
        </div>

        {{-- Event Types --}}
        <div class="audit-stat-card events">
            <div class="audit-stat-icon" style="background: #3b82f615;">
                <svg class="w-4 h-4" fill="none" stroke="#3b82f6" viewBox="0 0 24 24" stroke-width="1.75">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A2 2 0 013 12V7a2 2 0 012-2h2z"/>
                </svg>
            </div>
            <div class="audit-stat-num">{{ number_format($uniqueEvents) }}</div>
            <div class="audit-stat-label">Event Types</div>
            <div class="audit-stat-sub">Unique actions tracked</div>
        </div>

        {{-- Active Users --}}
        <div class="audit-stat-card users">
            <div class="audit-stat-icon" style="background: #7c3aed15;">
                <svg class="w-4 h-4" fill="none" stroke="#7c3aed" viewBox="0 0 24 24" stroke-width="1.75">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
            </div>
            <div class="audit-stat-num">{{ number_format($uniqueUsers) }}</div>
            <div class="audit-stat-label">Active Users</div>
            <div class="audit-stat-sub">With logged activity</div>
        </div>

        {{-- Recent (7 days) --}}
        <div class="audit-stat-card recent">
            <div class="audit-stat-icon" style="background: #d9770615;">
                <svg class="w-4 h-4" fill="none" stroke="#d97706" viewBox="0 0 24 24" stroke-width="1.75">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="audit-stat-num">{{ number_format($recentCount) }}</div>
            <div class="audit-stat-label">Last 7 Days</div>
            <div class="audit-stat-sub">Recent activity</div>
        </div>
    </div>

    {{-- ── FILTER PANEL ── --}}
    <div class="audit-panel anim-3">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="audit-form-label">Event</label>
                <select name="event" class="audit-select">
                    <option value="">All Events</option>
                    @foreach($events as $event)
                        <option value="{{ $event }}" {{ request('event') == $event ? 'selected' : '' }}>{{ ucfirst($event) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="audit-form-label">User</label>
                <input type="text" name="user" value="{{ request('user') }}" placeholder="User name"
                       class="audit-input" autocomplete="off">
            </div>
            <div>
                <label class="audit-form-label">From Date</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="audit-input">
            </div>
            <div>
                <label class="audit-form-label">To Date</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="audit-input">
            </div>
            <div class="flex gap-2">
                <button type="submit" class="btn-filter">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                    </svg>
                    Filter
                </button>
                @if(request()->anyFilled(['event', 'user', 'date_from', 'date_to']))
                    <a href="{{ route('admin.auditlogs.index') }}" class="btn-clear">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- ── AUDIT LOGS TABLE ── --}}
    <div class="audit-table-wrap anim-4">
        <div class="overflow-x-auto">
            <table class="audit-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Event</th>
                        <th>Description</th>
                        <th>IP Address</th>
                        <th>Date & Time</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    @php
                        $eventClass = match($log->event) {
                            'created' => 'event-badge-created',
                            'updated' => 'event-badge-updated',
                            'deleted' => 'event-badge-deleted',
                            'restored' => 'event-badge-restored',
                            'login' => 'event-badge-login',
                            'logout' => 'event-badge-logout',
                            default => 'event-badge-default',
                        };
                    @endphp
                    <tr>
                        <td class="font-medium text-text">{{ $log->user_name ?? ($log->user->full_name ?? 'System') }}</td>
                        <td>
                            <span class="event-badge {{ $eventClass }}">
                                {{ ucfirst($log->event) }}
                            </span>
                        </td>
                        <td class="text-text-2">{{ $log->description }}</td>
                        <td>
                            @if($log->ip_address)
                                <code class="ip-badge">{{ $log->ip_address }}</code>
                            @else
                                <span class="text-text-3 text-xs">—</span>
                            @endif
                        </td>
                        <td class="text-text-3 font-mono text-xs whitespace-nowrap">
                            {{ $log->created_at->format('M d, Y H:i:s') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5">
                            <div class="audit-empty">
                                <div class="audit-empty-ring">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.25">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <p class="text-text-2 font-semibold">No audit logs found</p>
                                <p class="text-text-3 text-sm">Try adjusting your search or filters</p>
                                @if(request()->anyFilled(['event', 'user', 'date_from', 'date_to']))
                                    <a href="{{ route('admin.auditlogs.index') }}" class="text-emerald-600 hover:text-emerald-700 text-sm font-medium inline-flex items-center gap-1 mt-2">
                                        Clear all filters →
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
        <div class="pag-wrap">
            <p class="pag-info">
                {{ $logs->firstItem() }}–{{ $logs->lastItem() }} of {{ $logs->total() }} logs
            </p>
            <div class="pag-btns">
                @if($logs->onFirstPage())
                    <span class="pag-btn disabled">← Prev</span>
                @else
                    <a href="{{ $logs->previousPageUrl() }}" class="pag-btn">← Prev</a>
                @endif
                @foreach($logs->getUrlRange(max(1, $logs->currentPage() - 2), min($logs->lastPage(), $logs->currentPage() + 2)) as $page => $url)
                    @if($page == $logs->currentPage())
                        <span class="pag-btn current">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="pag-btn">{{ $page }}</a>
                    @endif
                @endforeach
                @if($logs->hasMorePages())
                    <a href="{{ $logs->nextPageUrl() }}" class="pag-btn">Next →</a>
                @else
                    <span class="pag-btn disabled">Next →</span>
                @endif
            </div>
        </div>
        @endif
    </div>

</div>
@endsection