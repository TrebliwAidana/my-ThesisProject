@extends('layouts.app')

@section('title', 'User Management — VSULHS SSLG')
@section('page-title', 'User Management')

@push('styles')
<style>
/* ════════════════════════════════════════════════
   USER MANAGEMENT — Emerald & Gold Luxury Theme
   Matching Members & Financial Documents design
════════════════════════════════════════════════ */

/* ── Hero Section ── */
.users-hero {
    position: relative;
    overflow: hidden;
    border-radius: 1.25rem;
    padding: 1.75rem 2rem;
    isolation: isolate;
    background: linear-gradient(135deg, #064E3B 0%, #065F46 35%, #047857 60%, #0A3A28 100%);
}
.users-hero::before {
    content: '';
    position: absolute; inset: 0;
    background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,1) 1px, transparent 0);
    background-size: 28px 28px;
    opacity: 0.04; z-index: 0;
}
.users-hero::after {
    content: '';
    position: absolute;
    top: -60px; right: -60px;
    width: 280px; height: 280px;
    background: radial-gradient(circle, rgba(212,175,55,0.35), transparent 65%);
    filter: blur(48px); z-index: 0;
}
.users-hero-content { position: relative; z-index: 1; }

.users-hero-title {
    font-family: 'DM Serif Display', serif;
    font-size: clamp(1.5rem, 3.5vw, 2.2rem);
    color: #fff;
    letter-spacing: -0.02em;
    line-height: 1.1;
}
.users-hero-title span {
    background: linear-gradient(90deg, #F0CC55, #D4AF37);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.users-hero-pill {
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
.users-stat-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 1rem;
}
.users-stat-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 1rem;
    padding: 1.1rem 1.2rem;
    position: relative;
    overflow: hidden;
    transition: all 0.25s cubic-bezier(0.4,0,0.2,1);
    cursor: default;
}
.users-stat-card::after {
    content: '';
    position: absolute;
    bottom: 0; left: 0; right: 0;
    height: 2px;
    transform: scaleX(0);
    transform-origin: left;
    transition: transform 0.3s ease;
    border-radius: 0 0 999px 999px;
}
.users-stat-card:hover {
    border-color: rgba(212,175,55,0.35);
    transform: translateY(-3px);
    box-shadow: 0 8px 28px rgba(0,0,0,0.08), 0 0 0 1px rgba(212,175,55,0.12);
}
.users-stat-card:hover::after { transform: scaleX(1); }

.users-stat-card.total::after { background: linear-gradient(90deg, var(--emerald), var(--gold)); }
.users-stat-card.active::after { background: linear-gradient(90deg, #059669, #10b981); }
.users-stat-card.verified::after { background: linear-gradient(90deg, #2563eb, #60a5fa); }
.users-stat-card.logins::after { background: linear-gradient(90deg, #7c3aed, #a78bfa); }

.users-stat-icon {
    width: 2.1rem; height: 2.1rem;
    border-radius: 0.6rem;
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 0.75rem;
    flex-shrink: 0;
}
.users-stat-num {
    font-family: 'DM Mono', monospace;
    font-size: 1.6rem;
    font-weight: 700;
    letter-spacing: -0.04em;
    line-height: 1;
    margin-bottom: 0.25rem;
    color: var(--text);
}
.users-stat-label {
    font-size: 0.67rem; font-weight: 700;
    letter-spacing: 0.09em; text-transform: uppercase;
    color: var(--text-3); font-family: 'DM Mono', monospace;
}
.users-stat-sub {
    font-size: 0.65rem; color: var(--text-3);
    margin-top: 0.2rem; opacity: 0.75;
}

/* ── Filter Panel ── */
.users-panel {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 1.25rem;
    padding: 1.25rem;
    box-shadow: 0 2px 12px rgba(0,0,0,0.04);
}

.users-input {
    padding: 0.5rem 0.875rem;
    font-size: 0.83rem;
    background: var(--surface-2);
    border: 1.5px solid var(--border);
    border-radius: 0.75rem;
    color: var(--text);
    font-family: 'Outfit', sans-serif;
    transition: all 0.2s ease;
    width: 100%;
}
.users-input:focus {
    outline: none;
    border-color: var(--gold);
    box-shadow: 0 0 0 3px rgba(212,175,55,0.12);
    background: var(--surface);
}
.users-input::placeholder { color: var(--text-3); }
html.dark .users-input { background: rgba(15,23,42,0.5); }

.users-select {
    padding: 0.5rem 0.75rem;
    font-size: 0.8rem;
    background: var(--surface-2);
    border: 1.5px solid var(--border);
    border-radius: 0.75rem;
    color: var(--text);
    font-family: 'Outfit', sans-serif;
    cursor: pointer;
    transition: all 0.2s ease;
    white-space: nowrap;
}
.users-select:focus {
    outline: none;
    border-color: var(--gold);
    box-shadow: 0 0 0 3px rgba(212,175,55,0.12);
}
html.dark .users-select { background: rgba(15,23,42,0.5); }

/* ── Buttons ── */
.btn-emerald {
    display: inline-flex; align-items: center; gap: .4rem;
    padding: .48rem 1rem; font-size: .8rem; font-weight: 700;
    background: linear-gradient(135deg, var(--emerald), var(--emerald-dark));
    color: #fff; border: none; border-radius: .65rem;
    cursor: pointer; transition: all .2s ease; text-decoration: none;
    box-shadow: 0 2px 10px rgba(5,150,105,.22);
    font-family: 'Outfit', sans-serif; white-space: nowrap;
}
.btn-emerald:hover {
    background: linear-gradient(135deg, var(--gold), var(--gold-dark));
    color: #0f172a; box-shadow: 0 4px 16px rgba(212,175,55,.35);
    transform: translateY(-1px);
}

.btn-outline {
    display: inline-flex; align-items: center; gap: .4rem;
    padding: .48rem .875rem; font-size: .8rem; font-weight: 600;
    background: transparent; color: var(--text-2);
    border: 1.5px solid var(--border); border-radius: .65rem;
    cursor: pointer; transition: all .18s ease; text-decoration: none;
    font-family: 'Outfit', sans-serif; white-space: nowrap;
}
.btn-outline:hover {
    border-color: rgba(212,175,55,.4); color: var(--gold-dark);
    background: rgba(212,175,55,.06);
}

.btn-filter {
    display: inline-flex; align-items: center; gap: .4rem;
    padding: .48rem 1rem; font-size: .8rem; font-weight: 700;
    background: linear-gradient(135deg, var(--gold), var(--gold-dark));
    color: #0f172a; border: none; border-radius: .65rem;
    cursor: pointer; transition: all .2s ease;
    box-shadow: 0 2px 10px rgba(212,175,55,.25);
    font-family: 'Outfit', sans-serif; white-space: nowrap;
}
.btn-filter:hover { transform: translateY(-1px); box-shadow: 0 4px 16px rgba(212,175,55,.4); }

.btn-clear {
    display: inline-flex; align-items: center; gap: .4rem;
    padding: .48rem .875rem; font-size: .8rem; font-weight: 600;
    background: var(--surface-3); color: var(--text-2);
    border: 1.5px solid var(--border); border-radius: .65rem;
    cursor: pointer; transition: all .18s ease; text-decoration: none;
    font-family: 'Outfit', sans-serif; white-space: nowrap;
}
.btn-clear:hover {
    border-color: rgba(212,175,55,.4); color: var(--gold-dark);
    background: rgba(212,175,55,.06);
}

/* ── User Table ── */
.users-table-wrap {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 1.25rem;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
}
html.dark .users-table-wrap { box-shadow: 0 4px 20px rgba(0,0,0,0.22); }

.users-table {
    width: 100%;
    min-width: 1000px;
    border-collapse: collapse;
    font-size: 0.82rem;
}
.users-table thead tr {
    background: linear-gradient(135deg, #064E3B 0%, #047857 60%, #065F46 100%);
}
.users-table th {
    padding: 0.7rem 0.875rem;
    text-align: left;
    font-size: 0.63rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: rgba(255,255,255,0.82);
    font-family: 'DM Mono', monospace;
    white-space: nowrap;
}
.users-table th:last-child { text-align: right; }
.users-table tbody tr {
    border-bottom: 1px solid var(--border);
    transition: background 0.15s ease;
}
.users-table tbody tr:last-child { border-bottom: none; }
.users-table tbody tr:hover { 
    background: rgba(212,175,55,0.025); 
    box-shadow: inset 3px 0 0 var(--gold);
}
.users-table td {
    padding: 0.75rem 0.875rem;
    color: var(--text-2);
    vertical-align: middle;
}
.users-table td:last-child { text-align: right; }

/* ── All Badges with Rounded Edges (pill shape) ── */
.badge-role, .badge-status, .badge-verified, .badge-unverified, .badge-status-deleted {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.7rem;
    font-weight: 700;
    font-family: 'DM Mono', monospace;
    white-space: nowrap;
    line-height: 1.2;
}

/* ── Role Badges with Colors ── */
.badge-role-admin { background: linear-gradient(135deg, #8b5cf6, #7c3aed); color: #fff; box-shadow: 0 1px 3px rgba(139,92,246,0.3); }
.badge-role-adviser { background: linear-gradient(135deg, #f59e0b, #d97706); color: #fff; box-shadow: 0 1px 3px rgba(245,158,11,0.3); }
.badge-role-treasurer { background: linear-gradient(135deg, var(--emerald), var(--emerald-dark)); color: #fff; box-shadow: 0 1px 3px rgba(5,150,105,0.3); }
.badge-role-auditor { background: linear-gradient(135deg, #3b82f6, #2563eb); color: #fff; box-shadow: 0 1px 3px rgba(59,130,246,0.3); }
.badge-role-guest { background: linear-gradient(135deg, #94a3b8, #64748b); color: #fff; box-shadow: 0 1px 3px rgba(100,116,139,0.3); }

html.dark .badge-role-admin { background: linear-gradient(135deg, #a78bfa, #8b5cf6); }
html.dark .badge-role-adviser { background: linear-gradient(135deg, #fbbf24, #f59e0b); }
html.dark .badge-role-treasurer { background: linear-gradient(135deg, #34d399, #10b981); }
html.dark .badge-role-auditor { background: linear-gradient(135deg, #60a5fa, #3b82f6); }
html.dark .badge-role-guest { background: linear-gradient(135deg, #cbd5e1, #94a3b8); }

/* ── Status Badges (Rounded Edges) ── */
.badge-status-active {
    background: rgba(5,150,105,0.12);
    color: #047857;
    border: 1px solid rgba(5,150,105,0.25);
}
.badge-status-inactive {
    background: rgba(220,38,38,0.12);
    color: #dc2626;
    border: 1px solid rgba(220,38,38,0.25);
}
.badge-status-deleted {
    background: rgba(107,114,128,0.12);
    color: #6b7280;
    border: 1px solid rgba(107,114,128,0.25);
}

html.dark .badge-status-active {
    background: rgba(16,185,129,0.15);
    color: #6ee7b7;
    border-color: rgba(16,185,129,0.3);
}
html.dark .badge-status-inactive {
    background: rgba(248,113,113,0.15);
    color: #fca5a5;
    border-color: rgba(248,113,113,0.3);
}
html.dark .badge-status-deleted {
    background: rgba(107,114,128,0.2);
    color: #cbd5e1;
    border-color: rgba(107,114,128,0.3);
}

/* ── Email Verification Badges (Rounded Edges) ── */
.badge-verified {
    background: rgba(5,150,105,0.1);
    color: #047857;
    border: 1px solid rgba(5,150,105,0.22);
}
.badge-unverified {
    background: rgba(245,158,11,0.1);
    color: #92400e;
    border: 1px solid rgba(245,158,11,0.22);
}

html.dark .badge-verified { background: rgba(16,185,129,0.15); color: #6ee7b7; border-color: rgba(16,185,129,0.3); }
html.dark .badge-unverified { background: rgba(245,158,11,0.15); color: #fcd34d; border-color: rgba(245,158,11,0.3); }

/* ── Avatar Styles ── */
.user-avatar {
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 0.75rem;
    object-fit: cover;
    flex-shrink: 0;
}
.user-avatar-placeholder {
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.875rem;
    color: white;
    flex-shrink: 0;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}

/* ── Action Buttons ── */
.tbl-action {
    display: inline-flex; align-items: center; justify-content: center;
    padding: 0.2rem 0.5rem;
    font-size: 0.67rem; font-weight: 700;
    font-family: 'DM Mono', monospace;
    border-radius: 0.4rem;
    border: 1px solid transparent;
    cursor: pointer; transition: all 0.15s ease;
    text-decoration: none; white-space: nowrap;
    background: none;
}
.tbl-action.edit { color: var(--gold-dark); border-color: rgba(212,175,55,0.25); background: rgba(212,175,55,0.07); }
.tbl-action.edit:hover { background: rgba(212,175,55,0.16); border-color: rgba(212,175,55,0.45); }
.tbl-action.delete { color: #64748b; border-color: rgba(100,116,139,0.2); background: rgba(100,116,139,0.06); }
.tbl-action.delete:hover { color: #dc2626; border-color: rgba(220,38,38,0.3); background: rgba(220,38,38,0.06); }
.tbl-action.restore { color: #059669; border-color: rgba(5,150,105,0.2); background: rgba(5,150,105,0.06); }
.tbl-action.restore:hover { background: rgba(5,150,105,0.14); border-color: rgba(5,150,105,0.4); }
.tbl-action.verify { color: #2563eb; border-color: rgba(37,99,235,0.2); background: rgba(37,99,235,0.06); }
.tbl-action.verify:hover { background: rgba(37,99,235,0.14); border-color: rgba(37,99,235,0.4); }

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
.users-empty {
    display: flex; flex-direction: column; align-items: center;
    gap: 0.75rem; padding: 4rem 1rem; text-align: center;
}
.users-empty-ring {
    width: 4.5rem; height: 4.5rem; border-radius: 50%;
    background: linear-gradient(135deg, rgba(5,150,105,0.1), rgba(212,175,55,0.08));
    border: 1.5px dashed rgba(212,175,55,0.3);
    display: flex; align-items: center; justify-content: center;
    color: var(--text-3);
}

/* ── Checkbox Styling ── */
.users-checkbox {
    width: 1rem; height: 1rem;
    border-radius: 0.25rem;
    border: 1.5px solid var(--border);
    background: var(--surface);
    cursor: pointer;
    accent-color: var(--emerald);
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
<div class="space-y-5">

    {{-- ── HERO SECTION ── --}}
    <div class="users-hero anim-1">
        <div class="users-hero-content">
            <p class="text-emerald-300/70 text-[10px] font-bold tracking-[0.2em] uppercase mb-2"
               style="font-family:'DM Mono',monospace;">
                {{ now()->format('F Y') }} · System Administration
            </p>
            <h1 class="users-hero-title mb-3">User<br><span>Management</span></h1>
            <div class="flex flex-wrap gap-2">
                <span class="users-hero-pill">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    {{ $stats->total ?? 0 }} Total Users
                </span>
                <span class="users-hero-pill">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Manage roles & permissions
                </span>
            </div>
        </div>
    </div>

    {{-- ── STATS CARDS ── --}}
    <div class="users-stat-grid anim-2">
        @php
            $statCards = [
                ['key' => 'total', 'count' => $stats->total ?? 0, 'label' => 'Total Users', 'sub' => 'All registered users', 'class' => 'total', 'color' => '#059669', 'icon' => 'users'],
                ['key' => 'active', 'count' => $stats->active ?? 0, 'label' => 'Active Users', 'sub' => 'Currently active', 'class' => 'active', 'color' => '#059669', 'icon' => 'check'],
                ['key' => 'verified', 'count' => $stats->verified ?? 0, 'label' => 'Verified Emails', 'sub' => 'Email confirmed', 'class' => 'verified', 'color' => '#2563eb', 'icon' => 'mail'],
                ['key' => 'logins', 'count' => $stats->recent_logins ?? 0, 'label' => 'Recent Logins', 'sub' => 'Last 7 days', 'class' => 'logins', 'color' => '#7c3aed', 'icon' => 'clock'],
            ];
        @endphp
        @foreach($statCards as $card)
        <div class="users-stat-card {{ $card['class'] }}">
            <div class="users-stat-icon" style="background: {{ $card['color'] }}15;">
                @if($card['icon'] === 'users')
                    <svg class="w-4 h-4" fill="none" stroke="{{ $card['color'] }}" viewBox="0 0 24 24" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                @elseif($card['icon'] === 'check')
                    <svg class="w-4 h-4" fill="none" stroke="{{ $card['color'] }}" viewBox="0 0 24 24" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                @elseif($card['icon'] === 'mail')
                    <svg class="w-4 h-4" fill="none" stroke="{{ $card['color'] }}" viewBox="0 0 24 24" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                @else
                    <svg class="w-4 h-4" fill="none" stroke="{{ $card['color'] }}" viewBox="0 0 24 24" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                @endif
            </div>
            <div class="users-stat-num">{{ number_format($card['count']) }}</div>
            <div class="users-stat-label">{{ $card['label'] }}</div>
            <div class="users-stat-sub">{{ $card['sub'] }}</div>
        </div>
        @endforeach
    </div>

    {{-- ── ACTION TOOLBAR ── --}}
    <div class="flex flex-wrap items-center justify-between gap-4 anim-2">
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('admin.users.create') }}" class="btn-emerald" data-nav-link>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Add New User
            </a>
            <a href="{{ route('admin.roles.index') }}" class="btn-outline" data-nav-link>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
                Manage Roles
            </a>
        </div>
    </div>

    {{-- ── FILTER PANEL ── --}}
    <div class="users-panel anim-3">
        <form method="GET" action="{{ route('admin.users.index') }}" id="filter-form" class="flex flex-wrap gap-2 items-end">
            <div class="flex-1 min-w-[180px]">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Search users by name or email…" class="users-input">
            </div>
            <select name="role" class="users-select">
                <option value="">All Roles</option>
                @foreach($roles as $role)
                    <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>{{ $role->name }}</option>
                @endforeach
            </select>
            <select name="status" class="users-select">
                <option value="">All Status</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>✅ Active</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>⭕ Inactive</option>
            </select>
            <select name="verification" class="users-select">
                <option value="">All Verification</option>
                <option value="verified" {{ request('verification') == 'verified' ? 'selected' : '' }}>✓ Verified</option>
                <option value="unverified" {{ request('verification') == 'unverified' ? 'selected' : '' }}>⚠ Unverified</option>
            </select>
            <label class="flex items-center gap-2 px-2">
                <input type="checkbox" name="trashed" value="1" {{ request()->boolean('trashed') ? 'checked' : '' }} onchange="this.form.submit()"
                       class="users-checkbox">
                <span class="text-sm text-text-2">Show deleted users</span>
            </label>
            <button type="submit" class="btn-filter">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                </svg>
                Apply Filters
            </button>
            @if(request()->hasAny(['search', 'role', 'status', 'verification', 'trashed']))
            <a href="{{ route('admin.users.index') }}" class="btn-clear">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Reset
            </a>
            @endif
        </form>
    </div>

    {{-- ── USERS TABLE ── --}}
    <div class="users-table-wrap anim-4">
        <div class="overflow-x-auto">
            <table class="users-table">
                <thead>
                    <tr>
                        <th>User</th>
                        <th>Email</th>
                        <th>Email Status</th>
                        <th>Role</th>
                        <th>Position</th>
                        <th>Account Status</th>
                        <th>Last Login</th>
                        <th class="right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    @php
                        $roleBadgeClass = match($user->role->name) {
                            'System Administrator' => 'badge-role-admin',
                            'Club Adviser' => 'badge-role-adviser',
                            'Treasurer' => 'badge-role-treasurer',
                            'Auditor' => 'badge-role-auditor',
                            'Guest' => 'badge-role-guest',
                            default => 'badge-role-guest',
                        };
                        $isDeleted = $user->trashed();
                        $avatarGradient = match($user->role->name) {
                            'System Administrator' => 'from-purple-500 to-purple-700',
                            'Club Adviser' => 'from-amber-500 to-amber-700',
                            'Treasurer' => 'from-emerald-500 to-emerald-700',
                            'Auditor' => 'from-blue-500 to-blue-700',
                            'Guest' => 'from-gray-400 to-gray-600',
                            default => 'from-emerald-500 to-emerald-700',
                        };
                    @endphp
                    <tr>
                        {{-- User with Avatar --}}
                        <td>
                            <div class="flex items-center gap-3">
                                @if($user->avatar)
                                    <img src="{{ str_starts_with($user->avatar, 'http') ? $user->avatar : asset('storage/' . $user->avatar) }}"
                                         alt="{{ $user->full_name }}"
                                         class="user-avatar">
                                @else
                                    <div class="user-avatar-placeholder bg-gradient-to-br {{ $avatarGradient }}">
                                        {{ strtoupper(substr($user->full_name, 0, 2)) }}
                                    </div>
                                @endif
                                <div>
                                    <p class="font-semibold text-text">{{ $user->full_name }}</p>
                                    <p class="text-xs text-text-3">ID: {{ $user->id }}</p>
                                    @if($isDeleted)
                                        <span class="badge-status-deleted mt-1 inline-block">🗑️ Deleted</span>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- Email --}}
                        <td class="text-text-2 font-mono text-xs">{{ $user->email }}</td>

                        {{-- Email Status - FIXED BADGE --}}
                        <td>
                            @if($user->email_verified_at)
                                <span class="badge-verified">
                                    <svg class="w-2.5 h-2.5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    Verified
                                </span>
                            @else
                                <span class="badge-unverified">
                                    <svg class="w-2.5 h-2.5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                    Unverified
                                </span>
                            @endif
                        </td>

                        {{-- Role with Color Badge --}}
                        <td><span class="badge-role {{ $roleBadgeClass }}">{{ $user->role->name }}</span></td>

                        {{-- Position --}}
                        <td class="text-text-3 text-xs">{{ $user->position ?? '—' }}</td>

                        {{-- Account Status with Red for Inactive & Rounded Edges --}}
                        <td>
                            @if($isDeleted)
                                <span class="badge-status-deleted">
                                    <svg class="w-2.5 h-2.5 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                    Deleted
                                </span>
                            @elseif($user->is_active)
                                <span class="badge-status-active">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse inline-block mr-1"></span>
                                    Active
                                </span>
                            @else
                                <span class="badge-status-inactive">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500 inline-block mr-1"></span>
                                    Inactive
                                </span>
                            @endif
                        </td>

                        {{-- Last Login --}}
                        <td class="text-text-3 text-xs font-mono whitespace-nowrap">
                            {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}
                        </td>

                        {{-- Actions --}}
                        <td class="right">
                            <div class="flex items-center justify-end gap-1">
                                @if($isDeleted)
                                    <form method="POST" action="{{ route('admin.users.restore', $user->id) }}" onsubmit="return confirm('Restore this user?')" class="inline">
                                        @csrf
                                        <button type="submit" class="tbl-action restore">Restore</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.users.force-delete', $user->id) }}" onsubmit="return confirm('⚠️ Permanently delete this user? This action cannot be undone.')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="tbl-action delete">Force Del</button>
                                    </form>
                                @else
                                    @if(!$user->hasVerifiedEmail())
                                        <form method="POST" action="{{ route('admin.users.verify-manual', $user->id) }}" onsubmit="return confirm('Mark this user\'s email as verified?')" class="inline">
                                            @csrf
                                            <button type="submit" class="tbl-action verify">Verify</button>
                                        </form>
                                    @endif

                                    <a href="{{ route('admin.users.edit', $user->id) }}" class="tbl-action edit">Edit</a>

                                    @if($user->id !== auth()->id())
                                        <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}" onsubmit="return confirm('Soft delete {{ $user->full_name }}?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="tbl-action delete">Delete</button>
                                        </form>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8">
                            <div class="users-empty">
                                <div class="users-empty-ring">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.25">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                </div>
                                <p class="text-text-2 font-semibold">No users found</p>
                                <p class="text-text-3 text-sm">Try adjusting your search or filters</p>
                                @if(request()->hasAny(['search', 'role', 'status', 'verification', 'trashed']))
                                    <a href="{{ route('admin.users.index') }}" class="text-emerald-600 hover:text-emerald-700 text-sm font-medium inline-flex items-center gap-1 mt-2">
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

        @if($users->hasPages())
        <div class="pag-wrap">
            <p class="pag-info">
                {{ $users->firstItem() }}–{{ $users->lastItem() }} of {{ $users->total() }} users
            </p>
            <div class="pag-btns">
                @if($users->onFirstPage())
                    <span class="pag-btn disabled">← Prev</span>
                @else
                    <a href="{{ $users->previousPageUrl() }}" class="pag-btn">← Prev</a>
                @endif
                @foreach($users->getUrlRange(max(1, $users->currentPage() - 2), min($users->lastPage(), $users->currentPage() + 2)) as $page => $url)
                    @if($page == $users->currentPage())
                        <span class="pag-btn current">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="pag-btn">{{ $page }}</a>
                    @endif
                @endforeach
                @if($users->hasMorePages())
                    <a href="{{ $users->nextPageUrl() }}" class="pag-btn">Next →</a>
                @else
                    <span class="pag-btn disabled">Next →</span>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>
@endsection