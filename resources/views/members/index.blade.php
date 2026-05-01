@extends('layouts.app')

@section('title', 'Members — VSULHS SSLG')
@section('page-title', 'Members Directory')

@push('styles')
<style>
/* ════════════════════════════════════════════════
   MEMBERS DIRECTORY — Emerald & Gold Luxury Theme
   Inspired by Financial Records hero design
════════════════════════════════════════════════ */

/* ── Hero ── */
.members-hero {
    position: relative;
    overflow: hidden;
    border-radius: 1.25rem;
    padding: 1.75rem 2rem;
    isolation: isolate;
    background: linear-gradient(135deg, #064E3B 0%, #065F46 35%, #047857 60%, #0A3A28 100%);
}
.members-hero::before {
    content: '';
    position: absolute; inset: 0;
    background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,1) 1px, transparent 0);
    background-size: 28px 28px;
    opacity: 0.04; z-index: 0;
}
.members-hero::after {
    content: '';
    position: absolute;
    top: -60px; right: -60px;
    width: 280px; height: 280px;
    background: radial-gradient(circle, rgba(212,175,55,0.35), transparent 65%);
    filter: blur(48px); z-index: 0;
}
.members-hero-content { position: relative; z-index: 1; }

.members-hero-title {
    font-family: 'DM Serif Display', serif;
    font-size: clamp(1.5rem, 3.5vw, 2.2rem);
    color: #fff;
    letter-spacing: -0.02em;
    line-height: 1.1;
}
.members-hero-title span {
    background: linear-gradient(90deg, #F0CC55, #D4AF37);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.members-hero-pill {
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
.member-stat-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 1rem;
}

/* ── Emerald Button (matches Financial Records Add Income) ── */
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

/* ── Clear Button (matches Financial Records btn-gray) ── */
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

.member-stat-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 1rem;
    padding: 1.1rem 1.2rem;
    position: relative;
    overflow: hidden;
    transition: all 0.25s cubic-bezier(0.4,0,0.2,1);
    cursor: default;
}
.member-stat-card::after {
    content: '';
    position: absolute;
    bottom: 0; left: 0; right: 0;
    height: 2px;
    transform: scaleX(0);
    transform-origin: left;
    transition: transform 0.3s ease;
    border-radius: 0 0 999px 999px;
}
.member-stat-card:hover {
    border-color: rgba(212,175,55,0.35);
    transform: translateY(-3px);
    box-shadow: 0 8px 28px rgba(0,0,0,0.08), 0 0 0 1px rgba(212,175,55,0.12);
}
.member-stat-card:hover::after { transform: scaleX(1); }

.member-stat-card.all::after { background: linear-gradient(90deg, var(--emerald), var(--gold)); }
.member-stat-card.admin::after { background: linear-gradient(90deg, #9333ea, #a855f7); }
.member-stat-card.adviser::after { background: linear-gradient(90deg, #d97706, #f59e0b); }
.member-stat-card.treasurer::after { background: linear-gradient(90deg, #059669, #10b981); }
.member-stat-card.auditor::after { background: linear-gradient(90deg, #2563eb, #3b82f6); }
.member-stat-card.guest::after { background: linear-gradient(90deg, #6b7280, #9ca3af); }

.member-stat-icon {
    width: 2.1rem; height: 2.1rem;
    border-radius: 0.6rem;
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 0.75rem;
    flex-shrink: 0;
}
.member-stat-num {
    font-family: 'DM Mono', monospace;
    font-size: 1.6rem;
    font-weight: 700;
    letter-spacing: -0.04em;
    line-height: 1;
    margin-bottom: 0.25rem;
    color: var(--text);
}
.member-stat-label {
    font-size: 0.67rem; font-weight: 700;
    letter-spacing: 0.09em; text-transform: uppercase;
    color: var(--text-3); font-family: 'DM Mono', monospace;
}
.member-stat-sub {
    font-size: 0.65rem; color: var(--text-3);
    margin-top: 0.2rem; opacity: 0.75;
}

/* ── Filter Panel ── */
.member-panel {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 1.25rem;
    padding: 1.25rem;
    box-shadow: 0 2px 12px rgba(0,0,0,0.04);
}

.member-input {
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
.member-input:focus {
    outline: none;
    border-color: var(--gold);
    box-shadow: 0 0 0 3px rgba(212,175,55,0.12);
    background: var(--surface);
}
.member-input::placeholder { color: var(--text-3); }
html.dark .member-input { background: rgba(15,23,42,0.5); }

.member-select {
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
.member-select:focus {
    outline: none;
    border-color: var(--gold);
    box-shadow: 0 0 0 3px rgba(212,175,55,0.12);
}
html.dark .member-select { background: rgba(15,23,42,0.5); }

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

/* ── Role Tabs ── */
.role-tabs {
    border-bottom: 1px solid var(--border);
    overflow-x: auto;
    padding-bottom: 1px;
}
.role-tab {
    display: inline-flex; align-items: center; gap: 0.5rem;
    padding: 0.6rem 1rem;
    font-size: 0.8rem;
    font-weight: 600;
    border-bottom: 2px solid transparent;
    transition: all 0.2s ease;
    white-space: nowrap;
    text-decoration: none;
    color: var(--text-3);
}
.role-tab:hover {
    color: var(--gold-dark);
    border-bottom-color: rgba(212,175,55,0.4);
}
.role-tab.active {
    border-bottom-color: var(--gold);
    color: var(--gold-dark);
}
html.dark .role-tab.active { color: var(--gold-light); }
.role-tab-count {
    font-size: 0.7rem;
    padding: 0.15rem 0.5rem;
    border-radius: 999px;
    background: var(--surface-3);
    color: var(--text-3);
    font-family: 'DM Mono', monospace;
}

/* ── Member Table ── */
.member-table-wrap {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 1.25rem;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
}
html.dark .member-table-wrap { box-shadow: 0 4px 20px rgba(0,0,0,0.22); }

.member-table {
    width: 100%;
    min-width: 900px;
    border-collapse: collapse;
    font-size: 0.82rem;
}
.member-table thead tr {
    background: linear-gradient(135deg, #064E3B 0%, #047857 60%, #065F46 100%);
}
.member-table th {
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
.member-table th.right { text-align: right; }
.member-table th.center { text-align: center; }
.member-table tbody tr {
    border-bottom: 1px solid var(--border);
    transition: background 0.15s ease;
}
.member-table tbody tr:last-child { border-bottom: none; }
.member-table tbody tr:hover { 
    background: rgba(212,175,55,0.025); 
    box-shadow: inset 3px 0 0 var(--gold);
}
.member-table td {
    padding: 0.75rem 0.875rem;
    color: var(--text-2);
    vertical-align: middle;
}

/* ── Badges ── */
.badge-role {
    display: inline-flex; align-items: center; gap: 0.25rem;
    padding: 0.18rem 0.55rem;
    border-radius: 999px;
    font-size: 0.67rem; font-weight: 700;
    font-family: 'DM Mono', monospace;
    letter-spacing: 0.04em;
    white-space: nowrap;
}
.badge-role-admin { background: rgba(147,51,234,0.1); color:#7e22ce; border:1px solid rgba(147,51,234,0.2); }
.badge-role-adviser { background: rgba(217,119,6,0.1); color:#b45309; border:1px solid rgba(217,119,6,0.2); }
.badge-role-treasurer { background: rgba(5,150,105,0.1); color:#047857; border:1px solid rgba(5,150,105,0.2); }
.badge-role-auditor { background: rgba(37,99,235,0.1); color:#1e40af; border:1px solid rgba(37,99,235,0.2); }
.badge-role-guest { background: rgba(107,114,128,0.1); color:#4b5563; border:1px solid rgba(107,114,128,0.2); }

html.dark .badge-role-admin { background:rgba(147,51,234,0.18); color:#d8b4fe; border-color:rgba(147,51,234,0.3); }
html.dark .badge-role-adviser { background:rgba(217,119,6,0.18); color:#fcd34d; border-color:rgba(217,119,6,0.3); }
html.dark .badge-role-treasurer { background:rgba(16,185,129,0.18); color:#6ee7b7; border-color:rgba(16,185,129,0.3); }
html.dark .badge-role-auditor { background:rgba(37,99,235,0.18); color:#93c5fd; border-color:rgba(37,99,235,0.3); }
html.dark .badge-role-guest { background:rgba(107,114,128,0.18); color:#cbd5e1; border-color:rgba(107,114,128,0.3); }

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
.tbl-action.view { color: #2563eb; border-color: rgba(37,99,235,0.2); background: rgba(37,99,235,0.06); }
.tbl-action.view:hover { background: rgba(37,99,235,0.14); border-color: rgba(37,99,235,0.4); }
.tbl-action.edit { color: var(--gold-dark); border-color: rgba(212,175,55,0.25); background: rgba(212,175,55,0.07); }
.tbl-action.edit:hover { background: rgba(212,175,55,0.16); border-color: rgba(212,175,55,0.45); }
.tbl-action.history { color: #7c3aed; border-color: rgba(124,58,237,0.2); background: rgba(124,58,237,0.06); }
.tbl-action.history:hover { background: rgba(124,58,237,0.14); border-color: rgba(124,58,237,0.4); }
.tbl-action.delete { color: #64748b; border-color: rgba(100,116,139,0.2); background: rgba(100,116,139,0.06); }
.tbl-action.delete:hover { color: #e11d48; border-color: rgba(225,29,72,0.3); background: rgba(225,29,72,0.06); }

html.dark .tbl-action.view { color: #60a5fa; }
html.dark .tbl-action.edit { color: var(--gold-light); }
html.dark .tbl-action.history { color: #a78bfa; }

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
.member-empty {
    display: flex; flex-direction: column; align-items: center;
    gap: 0.75rem; padding: 4rem 1rem; text-align: center;
}
.member-empty-ring {
    width: 4.5rem; height: 4.5rem; border-radius: 50%;
    background: linear-gradient(135deg, rgba(5,150,105,0.1), rgba(212,175,55,0.08));
    border: 1.5px dashed rgba(212,175,55,0.3);
    display: flex; align-items: center; justify-content: center;
    color: var(--text-3);
}

/* ── Mobile Cards ── */
.mob-card {
    padding: 1rem 1.1rem;
    border-bottom: 1px solid var(--border);
    position: relative; transition: background 0.15s ease;
}
.mob-card:last-child { border-bottom: none; }
.mob-card:hover { background: rgba(212,175,55,0.035); }
.mob-card::before {
    content: '';
    position: absolute;
    left: 0; top: 0; bottom: 0; width: 3px;
    background: var(--border);
    transition: background 0.18s ease;
}
.mob-card:hover::before { background: var(--gold); }

/* ── Animations ── */
@keyframes fadeUp {
    from { opacity: 0; transform: translateY(14px); }
    to   { opacity: 1; transform: translateY(0); }
}
.a1 { animation: fadeUp 0.38s ease 0.04s both; }
.a2 { animation: fadeUp 0.38s ease 0.10s both; }
.a3 { animation: fadeUp 0.38s ease 0.16s both; }
.a4 { animation: fadeUp 0.38s ease 0.22s both; }
</style>
@endpush

@section('content')
@php
    $currentUser    = auth()->user();
    $isSystemAdmin  = $currentUser->role->level === 1;
    $guestEmail     = 'guest@gmail.com';

    $roleBadgeMap = [
        'System Administrator' => 'admin',
        'Club Adviser' => 'adviser',
        'Treasurer' => 'treasurer',
        'Auditor' => 'auditor',
        'Guest' => 'guest',
    ];
    
    $avatarBg = [
        'System Administrator' => 'from-purple-500 to-purple-700',
        'Club Adviser'         => 'from-amber-500 to-amber-700',
        'Treasurer'            => 'from-emerald-500 to-emerald-700',
        'Auditor'              => 'from-blue-500 to-blue-700',
        'Guest'                => 'from-gray-400 to-gray-600',
    ];
@endphp

<div class="space-y-5">

    {{-- ── HERO SECTION (Matching Financial Records) ── --}}
    <div class="members-hero a1">
        <div class="members-hero-content">
            <p class="text-emerald-300/70 text-[10px] font-bold tracking-[0.2em] uppercase mb-2"
               style="font-family:'DM Mono',monospace;">
                {{ now()->format('F Y') }} · Organization Roster
            </p>
            <h1 class="members-hero-title mb-3">Members<br><span>Directory</span></h1>
            <div class="flex flex-wrap gap-2">
                <span class="members-hero-pill">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    {{ $filteredStats['all'] }} Total Members
                </span>
                <span class="members-hero-pill">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Manage by Role
                </span>
            </div>
        </div>
    </div>

    {{-- ── STATS CARDS ── --}}
    <div class="member-stat-grid a2">
        @php
            $statsCards = [
                ['key' => 'all', 'label' => 'Total Members', 'sub' => 'Active roster', 'icon' => 'users', 'class' => 'all', 'color' => '#059669'],
                ['key' => 'admin', 'label' => 'System Admins', 'sub' => 'Full control', 'icon' => 'shield', 'class' => 'admin', 'color' => '#9333ea'],
                ['key' => 'adviser', 'label' => 'Club Advisers', 'sub' => 'Organization advisers', 'icon' => 'academic', 'class' => 'adviser', 'color' => '#d97706'],
                ['key' => 'treasurer', 'label' => 'Treasurers', 'sub' => 'Finance handlers', 'icon' => 'currency', 'class' => 'treasurer', 'color' => '#059669'],
                ['key' => 'auditor', 'label' => 'Auditors', 'sub' => 'Finance reviewers', 'icon' => 'clipboard', 'class' => 'auditor', 'color' => '#2563eb'],
                ['key' => 'guest', 'label' => 'Guest / Custom', 'sub' => 'Limited access', 'icon' => 'user', 'class' => 'guest', 'color' => '#6b7280'],
            ];
        @endphp
        @foreach($statsCards as $card)
        @php
            $count = $card['key'] === 'all' ? $filteredStats['all'] : 
                    ($card['key'] === 'guest' ? $filteredStats['guest'] + $filteredStats['custom'] : $filteredStats[$card['key']]);
        @endphp
        <div class="member-stat-card {{ $card['class'] }}">
            <div class="member-stat-icon" style="background: {{ $card['color'] }}15;">
                @if($card['icon'] === 'users')
                    <svg class="w-4 h-4" fill="none" stroke="{{ $card['color'] }}" viewBox="0 0 24 24" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                @elseif($card['icon'] === 'shield')
                    <svg class="w-4 h-4" fill="none" stroke="{{ $card['color'] }}" viewBox="0 0 24 24" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                    </svg>
                @elseif($card['icon'] === 'academic')
                    <svg class="w-4 h-4" fill="none" stroke="{{ $card['color'] }}" viewBox="0 0 24 24" stroke-width="1.75">
                        <path d="M12 14l9-5-9-5-9 5 9 5z" stroke="currentColor" stroke-width="2"/>
                        <path d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" stroke="currentColor" stroke-width="2"/>
                    </svg>
                @elseif($card['icon'] === 'currency')
                    <svg class="w-4 h-4" fill="none" stroke="{{ $card['color'] }}" viewBox="0 0 24 24" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                @elseif($card['icon'] === 'clipboard')
                    <svg class="w-4 h-4" fill="none" stroke="{{ $card['color'] }}" viewBox="0 0 24 24" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                @else
                    <svg class="w-4 h-4" fill="none" stroke="{{ $card['color'] }}" viewBox="0 0 24 24" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                @endif
            </div>
            <div class="member-stat-num">{{ number_format($count) }}</div>
            <div class="member-stat-label">{{ $card['label'] }}</div>
            <div class="member-stat-sub">{{ $card['sub'] }}</div>
        </div>
        @endforeach
    </div>

    {{-- ── ACTION TOOLBAR with Emerald/Gold Button ── --}}
    <div class="flex flex-wrap items-center justify-between gap-4 a2">
        <div class="flex flex-wrap gap-2">
            @if($isSystemAdmin || Gate::allows('members.create'))
            <a href="{{ route('members.create') }}" 
            class="btn-emerald" 
            data-nav-link>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Add Member
            </a>
            @endif
        </div>

    {{-- ── FILTER PANEL ── --}}
    <div class="member-panel a3">
        <form method="GET" action="{{ route('members.index') }}" id="filter-form" x-ref="filterForm">
            <div class="flex flex-wrap gap-2">
                <div class="flex-1 min-w-[200px]">
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Search by name or email…" class="member-input">
                </div>
                <select name="status" class="member-select" onchange="this.form.submit()">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>✅ Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>⭕ Inactive</option>
                </select>
                <select name="verification" class="member-select" onchange="this.form.submit()">
                    <option value="">All Verification</option>
                    <option value="verified" {{ request('verification') === 'verified' ? 'selected' : '' }}>✓ Verified</option>
                    <option value="unverified" {{ request('verification') === 'unverified' ? 'selected' : '' }}>⚠ Unverified</option>
                </select>
                <button type="submit" class="btn-filter">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                    </svg>
                    Filter
                </button>
                @if(request()->hasAny(['search','status','verification']))
                    <a href="{{ route('members.index') }}" class="btn-clear">✕ Clear</a>
                @endif
            </div>
        </form>
    </div>

    {{-- ── ROLE TABS ── --}}
    <div class="role-tabs a3">
        @php
            $tabs = [
                ['key' => 'all', 'label' => 'All', 'count' => $filteredStats['all']],
                ['key' => 'admin', 'label' => 'System Admin', 'count' => $filteredStats['admin']],
                ['key' => 'adviser', 'label' => 'Adviser', 'count' => $filteredStats['adviser']],
                ['key' => 'treasurer', 'label' => 'Treasurer', 'count' => $filteredStats['treasurer']],
                ['key' => 'auditor', 'label' => 'Auditor', 'count' => $filteredStats['auditor']],
                ['key' => 'guest', 'label' => 'Guest', 'count' => $filteredStats['guest']],
                ['key' => 'custom', 'label' => 'Custom', 'count' => $filteredStats['custom']],
            ];
        @endphp
        @foreach($tabs as $tab)
        <a href="{{ route('members.index', array_merge(request()->except('role', 'page'), ['role' => $tab['key']])) }}"
           data-nav-link
           class="role-tab {{ $roleFilter === $tab['key'] ? 'active' : '' }}">
            {{ $tab['label'] }}
            <span class="role-tab-count">{{ $tab['count'] }}</span>
        </a>
        @endforeach
    </div>

    {{-- ── DESKTOP TABLE ── --}}
    <div class="member-table-wrap a4 hidden md:block">
        <div class="overflow-x-auto">
            <table class="member-table">
                <thead>
                    <tr>
                        <th>Member</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th class="center">Verified</th>
                        <th class="center">Status</th>
                        <th class="center">Level</th>
                        <th>Joined</th>
                        <th class="right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $member)
                    @php
                        $badgeType = $roleBadgeMap[$member->role->name] ?? 'guest';
                        $avatarGradient = $avatarBg[$member->role->name] ?? 'from-gray-400 to-gray-600';
                        $initials = strtoupper(mb_substr($member->full_name, 0, 2));
                        $isGuest = $member->email === $guestEmail;
                        $canEditGuest = $isGuest && $isSystemAdmin;
                    @endphp
                    <tr>
                        <td>
                            <div class="flex items-center gap-3">
                                @if($member->avatar)
                                    <img src="{{ str_starts_with($member->avatar, 'http') ? $member->avatar : asset('storage/' . $member->avatar) }}"
                                         alt="{{ $member->full_name }}"
                                         class="w-8 h-8 rounded-lg object-cover shadow-sm flex-shrink-0">
                                @else
                                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br {{ $avatarGradient }}
                                         flex items-center justify-center text-xs font-bold text-white shadow-sm flex-shrink-0">
                                        {{ $initials }}
                                    </div>
                                @endif
                                <div>
                                    <p class="font-semibold text-text truncate max-w-[160px]">{{ $member->full_name }}</p>
                                    <p class="text-xs text-text-3 truncate max-w-[160px]">{{ $member->position ?? '—' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="text-text-2 font-mono text-xs">{{ $member->email }}</td>
                        <td>
                            <span class="badge-role badge-role-{{ $badgeType }}">
                                {{ $member->role->abbreviation ?? $member->role->name }}
                            </span>
                        </td>
                        <td class="center">
                            @if($member->email_verified_at)
                                <span class="inline-flex items-center gap-1 text-emerald-600 dark:text-emerald-400 text-xs">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Yes
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 text-amber-600 dark:text-amber-400 text-xs">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                                    No
                                </span>
                            @endif
                        </td>
                        <td class="center">
                            @if($member->is_active)
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    Active
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-300">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                    Inactive
                                </span>
                            @endif
                        </td>
                        <td class="center">
                            <div class="flex items-center justify-center gap-2">
                                <div class="w-12 h-1 bg-surface-3 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full bg-gradient-to-r from-emerald-500 to-gold"
                                         style="width: {{ min(($member->role->level / 8) * 100, 100) }}%">
                                    </div>
                                </div>
                                <span class="text-xs font-mono text-text-3">Lv.{{ $member->role->level }}</span>
                            </div>
                        </td>
                        <td class="text-text-3 text-xs font-mono whitespace-nowrap">
                            {{ optional($member->created_at)->format('M d, Y') }}
                        </td>
                        <td class="right">
                            <div class="flex items-center justify-end gap-1">
                                @if($isGuest && !$isSystemAdmin)
                                    <span class="text-xs text-text-3 italic">System Account</span>
                                @else
                                    <a href="{{ route('members.show', $member->id) }}" class="tbl-action view" title="View">View</a>
                                    @if(($isSystemAdmin || Gate::allows('members.edit')) && (!$isGuest || $canEditGuest))
                                        <a href="{{ route('members.edit', $member->id) }}" class="tbl-action edit" title="Edit">Edit</a>
                                    @endif
                                    <a href="{{ route('members.edit-history', $member->id) }}" class="tbl-action history" title="History">Hist</a>
                                    @if(!$isGuest && ($isSystemAdmin || Gate::allows('members.delete')) && $member->id !== auth()->id())
                                        <button type="button" onclick="confirmDelete('{{ $member->id }}', '{{ addslashes($member->full_name) }}', '{{ addslashes($member->role->name) }}', '{{ $member->email }}')"
                                                class="tbl-action delete" title="Delete">Del</button>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8">
                            <div class="member-empty">
                                <div class="member-empty-ring">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.25" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                </div>
                                <p class="text-text-2 font-semibold">No members found</p>
                                <p class="text-text-3 text-sm">Try adjusting your search or filters</p>
                                @if(request()->hasAny(['search', 'status', 'verification']) || (request()->filled('role') && request('role') !== 'all'))
                                    <a href="{{ route('members.index') }}" class="text-emerald-600 hover:text-emerald-700 text-sm">Clear all filters →</a>
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
            <p class="pag-info">{{ $users->firstItem() }}–{{ $users->lastItem() }} of {{ $users->total() }} members</p>
            <div class="pag-btns">
                @if($users->onFirstPage())
                    <span class="pag-btn disabled">← Prev</span>
                @else
                    <a href="{{ $users->previousPageUrl() }}" class="pag-btn">← Prev</a>
                @endif
                @foreach($users->getUrlRange(max(1,$users->currentPage()-2), min($users->lastPage(),$users->currentPage()+2)) as $page => $url)
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

    {{-- ── MOBILE CARDS ── --}}
    <div class="member-table-wrap a4 md:hidden">
        @forelse($users as $member)
        @php
            $badgeType = $roleBadgeMap[$member->role->name] ?? 'guest';
            $avatarGradient = $avatarBg[$member->role->name] ?? 'from-gray-400 to-gray-600';
            $initials = strtoupper(mb_substr($member->full_name, 0, 2));
            $isGuest = $member->email === $guestEmail;
        @endphp
        <div class="mob-card">
            <div class="flex items-start gap-3 mb-3">
                @if($member->avatar)
                    <img src="{{ str_starts_with($member->avatar, 'http') ? $member->avatar : asset('storage/' . $member->avatar) }}"
                         alt="{{ $member->full_name }}"
                         class="w-10 h-10 rounded-xl object-cover shadow-sm flex-shrink-0">
                @else
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br {{ $avatarGradient }}
                         flex items-center justify-center text-sm font-bold text-white shadow-sm flex-shrink-0">
                        {{ $initials }}
                    </div>
                @endif
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-text truncate">{{ $member->full_name }}</p>
                    <p class="text-xs text-text-3 truncate">{{ $member->position ?? '—' }}</p>
                    <p class="text-xs font-mono text-text-3 mt-1 truncate">{{ $member->email }}</p>
                </div>
            </div>
            <div class="flex flex-wrap gap-2 mb-3">
                <span class="badge-role badge-role-{{ $badgeType }}">{{ $member->role->name }}</span>
                @if($member->is_active)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs bg-emerald-100 dark:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300">● Active</span>
                @else
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-300">○ Inactive</span>
                @endif
                @if($member->email_verified_at)
                    <span class="text-emerald-600 dark:text-emerald-400 text-xs">✓ Verified</span>
                @else
                    <span class="text-amber-600 dark:text-amber-400 text-xs">⚠ Unverified</span>
                @endif
            </div>
            <div class="flex items-center justify-between">
                <span class="text-xs text-text-3 font-mono">Joined {{ optional($member->created_at)->format('M d, Y') }}</span>
                <div class="flex gap-1">
                    <a href="{{ route('members.show', $member->id) }}" class="tbl-action view">View</a>
                    @if(!$isGuest)
                        <a href="{{ route('members.edit', $member->id) }}" class="tbl-action edit">Edit</a>
                        <a href="{{ route('members.edit-history', $member->id) }}" class="tbl-action history">Hist</a>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="member-empty">
            <div class="member-empty-ring">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.25" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
            </div>
            <p class="text-text-2 font-semibold">No members found</p>
        </div>
        @endforelse

        @if($users->hasPages())
        <div class="pag-wrap">
            <p class="pag-info">{{ $users->firstItem() }}–{{ $users->lastItem() }} of {{ $users->total() }}</p>
            <div class="pag-btns">
                @if($users->onFirstPage())
                    <span class="pag-btn disabled">← Prev</span>
                @else
                    <a href="{{ $users->previousPageUrl() }}" class="pag-btn">← Prev</a>
                @endif
                @foreach($users->getUrlRange(max(1,$users->currentPage()-2), min($users->lastPage(),$users->currentPage()+2)) as $page => $url)
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

@push('scripts')
<script>
(function () {
    'use strict';

    window.confirmDelete = function (userId, userName, userRole, userEmail) {
        if (userEmail === '{{ $guestEmail }}') {
            if (window.showNotification) {
                window.showNotification('The shared guest account cannot be deleted.', 'warning', 4000);
            } else {
                alert('The shared guest account cannot be deleted.');
            }
            return;
        }

        if (confirm(`⚠️ Delete ${userName} (${userRole})?\n\nThis action cannot be undone.`)) {
            const form = document.getElementById(`delete-form-${userId}`);
            if (form) {
                if (window.showNavSkeleton) window.showNavSkeleton();
                form.submit();
            }
        }
    };
}());
</script>
@endpush

@foreach($users as $member)
    @if($member->email !== $guestEmail)
    <form id="delete-form-{{ $member->id }}" action="{{ route('members.destroy', $member->id) }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>
    @endif
@endforeach