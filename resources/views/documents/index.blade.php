@extends('layouts.app')

@section('title', 'Financial Documents — VSULHS SSLG')
@section('page-title', 'Financial Documents')

@push('styles')
<style>
/* ════════════════════════════════════════════════
   FINANCIAL DOCUMENTS — Emerald & Gold Luxury Theme
   Matching Members Directory design
════════════════════════════════════════════════ */

/* ── Hero Section (Matching Members Hero) ── */
.docs-hero {
    position: relative;
    overflow: hidden;
    border-radius: 1.25rem;
    padding: 1.75rem 2rem;
    isolation: isolate;
    background: linear-gradient(135deg, #064E3B 0%, #065F46 35%, #047857 60%, #0A3A28 100%);
}
.docs-hero::before {
    content: '';
    position: absolute; inset: 0;
    background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,1) 1px, transparent 0);
    background-size: 28px 28px;
    opacity: 0.04; z-index: 0;
}
.docs-hero::after {
    content: '';
    position: absolute;
    top: -60px; right: -60px;
    width: 280px; height: 280px;
    background: radial-gradient(circle, rgba(212,175,55,0.35), transparent 65%);
    filter: blur(48px); z-index: 0;
}
.docs-hero-content { position: relative; z-index: 1; }

.docs-hero-title {
    font-family: 'DM Serif Display', serif;
    font-size: clamp(1.5rem, 3.5vw, 2.2rem);
    color: #fff;
    letter-spacing: -0.02em;
    line-height: 1.1;
}
.docs-hero-title span {
    background: linear-gradient(90deg, #F0CC55, #D4AF37);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.docs-hero-pill {
    display: inline-flex; align-items: center; gap: 0.4rem;
    padding: 0.3rem 0.75rem;
    background: rgba(255,255,255,0.1);
    border: 1px solid rgba(212,175,55,0.28);
    border-radius: 999px;
    font-size: 0.72rem; font-weight: 600;
    color: rgba(255,255,255,0.88);
    font-family: 'DM Mono', monospace;
}

/* ── Stat Cards (Matching Members Stat Cards) ── */
.docs-stat-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 1rem;
}
.docs-stat-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 1rem;
    padding: 1.1rem 1.2rem;
    position: relative;
    overflow: hidden;
    transition: all 0.25s cubic-bezier(0.4,0,0.2,1);
    cursor: default;
}
.docs-stat-card::after {
    content: '';
    position: absolute;
    bottom: 0; left: 0; right: 0;
    height: 2px;
    transform: scaleX(0);
    transform-origin: left;
    transition: transform 0.3s ease;
    border-radius: 0 0 999px 999px;
}
.docs-stat-card:hover {
    border-color: rgba(212,175,55,0.35);
    transform: translateY(-3px);
    box-shadow: 0 8px 28px rgba(0,0,0,0.08), 0 0 0 1px rgba(212,175,55,0.12);
}
.docs-stat-card:hover::after { transform: scaleX(1); }

.docs-stat-card.all::after { background: linear-gradient(90deg, var(--emerald), var(--gold)); }
.docs-stat-card.inc::after { background: linear-gradient(90deg, #059669, #10b981); }
.docs-stat-card.exp::after { background: linear-gradient(90deg, #f43f5e, #fb7185); }
.docs-stat-card.rec::after { background: linear-gradient(90deg, #7c3aed, #a78bfa); }

.docs-stat-icon {
    width: 2.1rem; height: 2.1rem;
    border-radius: 0.6rem;
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 0.75rem;
    flex-shrink: 0;
}
.docs-stat-num {
    font-family: 'DM Mono', monospace;
    font-size: 1.6rem;
    font-weight: 700;
    letter-spacing: -0.04em;
    line-height: 1;
    margin-bottom: 0.25rem;
    color: var(--text);
}
.docs-stat-num.em { color: var(--emerald-dark); }
html.dark .docs-stat-num.em { color: var(--emerald-light); }
.docs-stat-num.rd { color: #e11d48; }
html.dark .docs-stat-num.rd { color: #fda4af; }
.docs-stat-num.pu { color: #7c3aed; }
html.dark .docs-stat-num.pu { color: #a78bfa; }

.docs-stat-label {
    font-size: 0.67rem; font-weight: 700;
    letter-spacing: 0.09em; text-transform: uppercase;
    color: var(--text-3); font-family: 'DM Mono', monospace;
}
.docs-stat-sub {
    font-size: 0.65rem; color: var(--text-3);
    margin-top: 0.2rem; opacity: 0.75;
}

/* ── Filter Panel (Matching Members Panel) ── */
.docs-panel {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 1.25rem;
    padding: 1.25rem;
    box-shadow: 0 2px 12px rgba(0,0,0,0.04);
}

.docs-input {
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
.docs-input:focus {
    outline: none;
    border-color: var(--gold);
    box-shadow: 0 0 0 3px rgba(212,175,55,0.12);
    background: var(--surface);
}
.docs-input::placeholder { color: var(--text-3); }
html.dark .docs-input { background: rgba(15,23,42,0.5); }

.docs-select {
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
.docs-select:focus {
    outline: none;
    border-color: var(--gold);
    box-shadow: 0 0 0 3px rgba(212,175,55,0.12);
}
html.dark .docs-select { background: rgba(15,23,42,0.5); }

/* ── Buttons (Matching Members Buttons) ── */
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

/* ── Documents Table (Matching Members Table) ── */
.docs-table-wrap {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 1.25rem;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
}
html.dark .docs-table-wrap { box-shadow: 0 4px 20px rgba(0,0,0,0.22); }

.docs-table {
    width: 100%;
    min-width: 900px;
    border-collapse: collapse;
    font-size: 0.82rem;
}
.docs-table thead tr {
    background: linear-gradient(135deg, #064E3B 0%, #047857 60%, #065F46 100%);
}
.docs-table th {
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
.docs-table th:last-child { text-align: right; }
.docs-table tbody tr {
    border-bottom: 1px solid var(--border);
    transition: background 0.15s ease;
}
.docs-table tbody tr:last-child { border-bottom: none; }
.docs-table tbody tr:hover { 
    background: rgba(212,175,55,0.025); 
    box-shadow: inset 3px 0 0 var(--gold);
}
.docs-table td {
    padding: 0.75rem 0.875rem;
    color: var(--text-2);
    vertical-align: middle;
}
.docs-table td:last-child { text-align: right; }

/* Accent borders for categories */
.docs-table tbody tr.accent-income { box-shadow: inset 3px 0 0 var(--emerald); }
.docs-table tbody tr.accent-expense { box-shadow: inset 3px 0 0 #f43f5e; }
.docs-table tbody tr.accent-receivable { box-shadow: inset 3px 0 0 #7c3aed; }
.docs-table tbody tr.accent-income:hover { box-shadow: inset 3px 0 0 var(--gold); }
.docs-table tbody tr.accent-expense:hover { box-shadow: inset 3px 0 0 var(--gold); }
.docs-table tbody tr.accent-receivable:hover { box-shadow: inset 3px 0 0 var(--gold); }

/* ── Badges ── */
.badge {
    display: inline-flex; align-items: center; gap: 0.25rem;
    padding: 0.18rem 0.55rem;
    border-radius: 999px;
    font-size: 0.67rem; font-weight: 700;
    font-family: 'DM Mono', monospace;
    letter-spacing: 0.04em;
    white-space: nowrap;
}
.badge-income     { background: rgba(5,150,105,0.1);  color:#047857; border:1px solid rgba(5,150,105,0.22); }
.badge-expense    { background: rgba(244,63,94,0.08); color:#be123c; border:1px solid rgba(244,63,94,0.2); }
.badge-receivable { background: rgba(124,58,237,0.1); color:#6d28d9; border:1px solid rgba(124,58,237,0.2); }
.badge-auto       { background: rgba(37,99,235,0.1);  color:#1e40af; border:1px solid rgba(37,99,235,0.2); }
.badge-manual     { background: var(--surface-3); color: var(--text-3); border: 1px solid var(--border-2); }

html.dark .badge-income     { background:rgba(16,185,129,0.15); color:#6ee7b7; border-color:rgba(16,185,129,0.3); }
html.dark .badge-expense    { background:rgba(244,63,94,0.15);  color:#fda4af; border-color:rgba(244,63,94,0.25); }
html.dark .badge-receivable { background:rgba(124,58,237,0.18); color:#a78bfa; border-color:rgba(124,58,237,0.3); }
html.dark .badge-auto       { background:rgba(37,99,235,0.18);  color:#93c5fd; border-color:rgba(37,99,235,0.3); }

/* ── Action Buttons (Matching Members Actions) ── */
.tbl-action {
    display: inline-flex; align-items: center; justify-content: center;
    padding: 0.3rem 0.5rem;
    font-size: 0.67rem; font-weight: 700;
    font-family: 'DM Mono', monospace;
    border-radius: 0.4rem;
    border: 1px solid transparent;
    cursor: pointer; transition: all 0.15s ease;
    text-decoration: none; white-space: nowrap;
    background: none;
}
.tbl-action.preview { color: #2563eb; border-color: rgba(37,99,235,0.2); background: rgba(37,99,235,0.06); }
.tbl-action.preview:hover { background: rgba(37,99,235,0.14); border-color: rgba(37,99,235,0.4); }
.tbl-action.download { color: var(--emerald-dark); border-color: rgba(5,150,105,0.2); background: rgba(5,150,105,0.06); }
.tbl-action.download:hover { background: rgba(5,150,105,0.14); border-color: rgba(5,150,105,0.4); }
.tbl-action.edit { color: var(--gold-dark); border-color: rgba(212,175,55,0.25); background: rgba(212,175,55,0.07); }
.tbl-action.edit:hover { background: rgba(212,175,55,0.16); border-color: rgba(212,175,55,0.45); }
.tbl-action.delete { color: #64748b; border-color: rgba(100,116,139,0.2); background: rgba(100,116,139,0.06); }
.tbl-action.delete:hover { color: #e11d48; border-color: rgba(225,29,72,0.3); background: rgba(225,29,72,0.06); }
.tbl-action.disabled { opacity: 0.35; cursor: not-allowed; pointer-events: none; }

html.dark .tbl-action.preview { color: #60a5fa; }
html.dark .tbl-action.download { color: #6ee7b7; }
html.dark .tbl-action.edit { color: var(--gold-light); }
html.dark .tbl-action.delete { color: #fda4af; }

/* ── Document Title Link ── */
.doc-title-link {
    color: var(--emerald-dark);
    font-weight: 600;
    text-decoration: none;
    transition: color 0.15s ease;
    display: block;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 220px;
}
.doc-title-link:hover { color: var(--gold-dark); }
html.dark .doc-title-link { color: var(--emerald-light); }
html.dark .doc-title-link:hover { color: var(--gold-light); }

/* ── Pagination (Matching Members Pagination) ── */
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
.docs-empty {
    display: flex; flex-direction: column; align-items: center;
    gap: 0.75rem; padding: 4rem 1rem; text-align: center;
}
.docs-empty-ring {
    width: 4.5rem; height: 4.5rem; border-radius: 50%;
    background: linear-gradient(135deg, rgba(5,150,105,0.1), rgba(212,175,55,0.08));
    border: 1.5px dashed rgba(212,175,55,0.3);
    display: flex; align-items: center; justify-content: center;
    color: var(--text-3);
}

/* ── Mobile Cards (Matching Members Mobile) ── */
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
.anim-1 { animation: fadeUp 0.38s ease 0.04s both; }
.anim-2 { animation: fadeUp 0.38s ease 0.10s both; }
.anim-3 { animation: fadeUp 0.38s ease 0.16s both; }
.anim-4 { animation: fadeUp 0.38s ease 0.22s both; }
</style>
@endpush

@section('content')
<div class="space-y-5">

    {{-- ── HERO SECTION (Matching Members Hero) ── --}}
    <div class="docs-hero anim-1">
        <div class="docs-hero-content">
            <p class="text-emerald-300/70 text-[10px] font-bold tracking-[0.2em] uppercase mb-2"
               style="font-family:'DM Mono',monospace;">
                {{ now()->format('F Y') }} · Document Repository
            </p>
            <h1 class="docs-hero-title mb-3">Financial<br><span>Documents</span></h1>
            <div class="flex flex-wrap gap-2">
                <span class="docs-hero-pill">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Income, Expense &amp; Receivable records
                </span>
            </div>
        </div>
    </div>

    {{-- ── STAT CARDS (Matching Members Stats) ── --}}
    @if($autoGeneratedCount > 0)
    <div class="docs-stat-grid anim-2">
        @php
            $statCards = [
                ['key' => 'all', 'count' => $totalCount, 'label' => 'All Documents', 'sub' => 'Total documents', 'class' => 'all', 'color' => '#059669'],
                ['key' => 'inc', 'count' => $incomeCount, 'label' => 'Approved Income', 'sub' => 'Income records', 'class' => 'inc', 'color' => '#059669'],
                ['key' => 'exp', 'count' => $expenseCount, 'label' => 'Approved Expense', 'sub' => 'Expense records', 'class' => 'exp', 'color' => '#f43f5e'],
                ['key' => 'rec', 'count' => $receivableCount, 'label' => 'Approved Receivable', 'sub' => 'Receivable records', 'class' => 'rec', 'color' => '#7c3aed'],
            ];
        @endphp
        @foreach($statCards as $card)
        <div class="docs-stat-card {{ $card['class'] }}">
            <div class="docs-stat-icon" style="background: {{ $card['color'] }}15;">
                @if($card['key'] === 'all')
                    <svg class="w-4 h-4" fill="none" stroke="{{ $card['color'] }}" viewBox="0 0 24 24" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                @elseif($card['key'] === 'inc')
                    <svg class="w-4 h-4" fill="none" stroke="{{ $card['color'] }}" viewBox="0 0 24 24" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                    </svg>
                @elseif($card['key'] === 'exp')
                    <svg class="w-4 h-4" fill="none" stroke="{{ $card['color'] }}" viewBox="0 0 24 24" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M17 13l-5 5m0 0l-5-5m5 5V6"/>
                    </svg>
                @else
                    <svg class="w-4 h-4" fill="none" stroke="{{ $card['color'] }}" viewBox="0 0 24 24" stroke-width="1.75">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                @endif
            </div>
            <div class="docs-stat-num {{ $card['key'] === 'inc' ? 'em' : ($card['key'] === 'exp' ? 'rd' : ($card['key'] === 'rec' ? 'pu' : '')) }}">
                {{ number_format($card['count']) }}
            </div>
            <div class="docs-stat-label">{{ $card['label'] }}</div>
            <div class="docs-stat-sub">{{ $card['sub'] }}</div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- ── ACTION TOOLBAR (Matching Members Toolbar) ── --}}
    <div class="flex flex-wrap items-center justify-between gap-4 anim-2">
        <div class="flex flex-wrap gap-2">
            @if(Auth::user()->role->level === 1 || Auth::user()->hasPermission('documents.create'))
            <a href="{{ route('documents.create') }}" class="btn-emerald" data-nav-link>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Upload Document
            </a>
            @endif
            @if(Auth::user()->role->level === 1 || Auth::user()->hasPermission('documents.manage'))
            <a href="{{ route('documents.trash') }}" class="btn-clear" data-nav-link>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Trash
            </a>
            @endif
        </div>
    </div>

    {{-- ── FILTER PANEL (Matching Members Panel) ── --}}
    <div class="docs-panel anim-3">
        <form method="GET" action="{{ route('documents.index') }}" class="flex flex-wrap gap-2">
            <div class="flex-1 min-w-[180px]">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search title or description…" class="docs-input">
            </div>
            <select name="category" class="docs-select">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>
            <select name="source" class="docs-select">
                <option value="">All Sources</option>
                <option value="auto"   {{ request('source') === 'auto'   ? 'selected' : '' }}>Auto-generated</option>
                <option value="manual" {{ request('source') === 'manual' ? 'selected' : '' }}>Manually uploaded</option>
            </select>
            <button type="submit" class="btn-filter">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                </svg>
                Filter
            </button>
            @if(request()->hasAny(['search', 'category', 'source']))
            <a href="{{ route('documents.index') }}" class="btn-clear">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
                Clear
            </a>
            @endif
        </form>
    </div>

    {{-- ── DESKTOP TABLE ── --}}
    <div class="docs-table-wrap anim-4 hidden md:block">
        <div class="overflow-x-auto">
            <table class="docs-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Source</th>
                        <th>Uploaded By</th>
                        <th>Size</th>
                        <th>Date</th>
                        <th class="right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($documents as $doc)
                    @php
                        $version   = $doc->currentVersion;
                        $fileName  = $version?->file_name ?? '';
                        $ext       = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                        $iconColor = match(true) {
                            $ext === 'pdf'                             => '#f43f5e',
                            in_array($ext, ['doc','docx'])             => '#2563eb',
                            in_array($ext, ['xls','xlsx'])             => '#059669',
                            in_array($ext, ['ppt','pptx'])             => '#ea580c',
                            in_array($ext, ['jpg','jpeg','png','gif']) => '#7c3aed',
                            $ext === 'zip'                             => '#ca8a04',
                            default                                    => '#94a3b8',
                        };
                        $tags      = $doc->tags ?? [];
                        $isAutoGen = in_array('auto-generated', $tags);
                        $catName   = $doc->category?->name ?? '';
                        $accentRow = match(true) {
                            str_contains($catName, 'Income')     => 'accent-income',
                            str_contains($catName, 'Expense')    => 'accent-expense',
                            str_contains($catName, 'Receivable') => 'accent-receivable',
                            default                              => '',
                        };
                        $catBadge = match(true) {
                            str_contains($catName, 'Income')     => 'badge-income',
                            str_contains($catName, 'Expense')    => 'badge-expense',
                            str_contains($catName, 'Receivable') => 'badge-receivable',
                            default                              => 'badge-income',
                        };
                    @endphp
                    <tr class="{{ $accentRow }}">
                        {{-- Title --}}
                        <td>
                            <div class="flex items-center gap-2.5">
                                <span style="color:{{ $iconColor }}; flex-shrink:0;">
                                    @if($ext === 'pdf')
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm-1 1.5L18.5 9H13V3.5zM6 20V4h5v7h7v9H6z"/></svg>
                                    @elseif(in_array($ext, ['doc','docx']))
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm-1 1.5L18.5 9H13V3.5zM9 13h6v1H9v-1zm0 2h6v1H9v-1zm0 2h4v1H9v-1z"/></svg>
                                    @elseif(in_array($ext, ['jpg','jpeg','png','gif']))
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    @else
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    @endif
                                </span>
                                <div class="min-w-0">
                                    <a href="{{ route('documents.show', $doc) }}"
                                       class="doc-title-link" title="{{ $doc->title }}">
                                        {{ $doc->title }}
                                    </a>
                                    @if($doc->description)
                                        <p class="text-xs truncate max-w-[200px]" style="color:var(--text-3);">{{ $doc->description }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- Category --}}
                        <td>
                            @if($doc->category)
                                <span class="badge {{ $catBadge }}">{{ $doc->category->name }}</span>
                            @else
                                <span style="color:var(--text-3);">—</span>
                            @endif
                        </td>

                        {{-- Source --}}
                        <td>
                            @if($isAutoGen)
                                <span class="badge badge-auto">
                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                    Auto
                                </span>
                            @else
                                <span class="badge badge-manual">
                                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                    Manual
                                </span>
                            @endif
                        </td>

                        {{-- Uploaded By --}}
                        <td class="text-xs font-mono" style="color:var(--text-3);">
                            {{ $doc->owner->full_name ?? 'System' }}
                        </td>

                        {{-- Size --}}
                        <td class="text-xs font-mono whitespace-nowrap" style="color:var(--text-3);">
                            {{ $doc->formatted_size }}
                        </td>

                        {{-- Date --}}
                        <td class="text-xs font-mono whitespace-nowrap" style="color:var(--text-3);">
                            {{ $doc->created_at->format('M d, Y') }}
                        </td>

                        {{-- Actions --}}
                        <td class="right">
                            <div class="flex items-center justify-end gap-1">
                                @if($version && in_array($ext, ['pdf','jpg','jpeg','png','gif']))
                                <button onclick="openPreview('{{ route('documents.preview', $doc) }}','{{ route('documents.download', $doc) }}',{{ json_encode($doc->title) }},'{{ $ext }}')"
                                        class="tbl-action preview" title="Preview">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    Preview
                                </button>
                                @endif

                                @if($version)
                                <a href="{{ route('documents.download', $doc) }}" class="tbl-action download" title="Download">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                                    Download
                                </a>
                                @endif

                                @if(Auth::user()->role->level === 1 || Auth::user()->hasPermission('documents.edit'))
                                    @if($isAutoGen)
                                        <span class="tbl-action disabled" title="Auto-generated — cannot be edited">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            Edit
                                        </span>
                                    @else
                                        <a href="{{ route('documents.edit', $doc) }}" class="tbl-action edit" title="Edit">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            Edit
                                        </a>
                                    @endif
                                @endif

                                @if(Auth::user()->role->level === 1 || Auth::user()->hasPermission('documents.delete'))
                                <form method="POST" action="{{ route('documents.destroy', $doc) }}"
                                      onsubmit="return confirm('{{ $isAutoGen ? 'This is an auto-generated approval record. Delete anyway?' : 'Delete this document?' }}')"
                                      class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="tbl-action delete" title="Delete">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        Delete
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7">
                            <div class="docs-empty">
                                <div class="docs-empty-ring">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.25">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <p class="text-text-2 font-semibold">No documents found</p>
                                <p class="text-text-3 text-sm">Try adjusting your search or filters</p>
                                @if(Auth::user()->role->level === 1 || Auth::user()->hasPermission('documents.create'))
                                    <a href="{{ route('documents.create') }}" class="text-emerald-600 hover:text-emerald-700 text-sm font-medium inline-flex items-center gap-1 mt-2">
                                        Upload your first document →
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($documents->hasPages())
        <div class="pag-wrap">
            <p class="pag-info">
                {{ $documents->firstItem() }}–{{ $documents->lastItem() }} of {{ $documents->total() }} documents
            </p>
            <div class="pag-btns">
                @if($documents->onFirstPage())
                    <span class="pag-btn disabled">← Prev</span>
                @else
                    <a href="{{ $documents->previousPageUrl() }}" class="pag-btn">← Prev</a>
                @endif
                @foreach($documents->getUrlRange(max(1, $documents->currentPage() - 2), min($documents->lastPage(), $documents->currentPage() + 2)) as $page => $url)
                    @if($page == $documents->currentPage())
                        <span class="pag-btn current">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="pag-btn">{{ $page }}</a>
                    @endif
                @endforeach
                @if($documents->hasMorePages())
                    <a href="{{ $documents->nextPageUrl() }}" class="pag-btn">Next →</a>
                @else
                    <span class="pag-btn disabled">Next →</span>
                @endif
            </div>
        </div>
        @endif
    </div>

    {{-- ── MOBILE CARDS (Matching Members Mobile) ── --}}
    <div class="docs-table-wrap anim-4 md:hidden">
        @forelse($documents as $doc)
        @php
            $version   = $doc->currentVersion;
            $fileName  = $version?->file_name ?? '';
            $ext       = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            $tags      = $doc->tags ?? [];
            $isAutoGen = in_array('auto-generated', $tags);
            $catName   = $doc->category?->name ?? '';
            $catBadge  = match(true) {
                str_contains($catName, 'Income')     => 'badge-income',
                str_contains($catName, 'Expense')    => 'badge-expense',
                str_contains($catName, 'Receivable') => 'badge-receivable',
                default                              => 'badge-income',
            };
        @endphp
        <div class="mob-card">
            <div class="flex items-start gap-3 mb-3">
                <div class="flex-1 min-w-0">
                    <a href="{{ route('documents.show', $doc) }}" class="doc-title-link" style="max-width: none;">
                        {{ $doc->title }}
                    </a>
                    @if($doc->description)
                        <p class="text-xs text-text-3 mt-1">{{ $doc->description }}</p>
                    @endif
                </div>
            </div>
            <div class="flex flex-wrap gap-2 mb-3">
                @if($doc->category)
                    <span class="badge {{ $catBadge }}">{{ $doc->category->name }}</span>
                @endif
                @if($isAutoGen)
                    <span class="badge badge-auto">⚡ Auto</span>
                @else
                    <span class="badge badge-manual">📄 Manual</span>
                @endif
            </div>
            <div class="flex items-center justify-between text-xs text-text-3 font-mono">
                <span>{{ $doc->owner->full_name ?? 'System' }}</span>
                <span>{{ $doc->created_at->format('M d, Y') }}</span>
            </div>
            <div class="flex items-center justify-end gap-2 mt-3 pt-2 border-t border-border">
                @if($version && in_array($ext, ['pdf','jpg','jpeg','png','gif']))
                <button onclick="openPreview('{{ route('documents.preview', $doc) }}','{{ route('documents.download', $doc) }}',{{ json_encode($doc->title) }},'{{ $ext }}')"
                        class="tbl-action preview">Preview</button>
                @endif
                @if($version)
                <a href="{{ route('documents.download', $doc) }}" class="tbl-action download">Download</a>
                @endif
                @if((Auth::user()->role->level === 1 || Auth::user()->hasPermission('documents.edit')) && !$isAutoGen)
                <a href="{{ route('documents.edit', $doc) }}" class="tbl-action edit">Edit</a>
                @endif
            </div>
        </div>
        @empty
        <div class="docs-empty">
            <div class="docs-empty-ring">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.25" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <p class="text-text-2 font-semibold">No documents found</p>
        </div>
        @endforelse

        @if($documents->hasPages())
        <div class="pag-wrap">
            <p class="pag-info">{{ $documents->firstItem() }}–{{ $documents->lastItem() }} of {{ $documents->total() }}</p>
            <div class="pag-btns">
                @if($documents->onFirstPage())
                    <span class="pag-btn disabled">← Prev</span>
                @else
                    <a href="{{ $documents->previousPageUrl() }}" class="pag-btn">← Prev</a>
                @endif
                @foreach($documents->getUrlRange(max(1, $documents->currentPage() - 2), min($documents->lastPage(), $documents->currentPage() + 2)) as $page => $url)
                    @if($page == $documents->currentPage())
                        <span class="pag-btn current">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="pag-btn">{{ $page }}</a>
                    @endif
                @endforeach
                @if($documents->hasMorePages())
                    <a href="{{ $documents->nextPageUrl() }}" class="pag-btn">Next →</a>
                @else
                    <span class="pag-btn disabled">Next →</span>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>

{{-- ── Preview Modal (Preserved Logic) ── --}}
<div id="preview-modal" class="fixed inset-0 z-50 hidden items-center justify-center p-4"
     style="background:rgba(0,0,0,0.72);" aria-modal="true" role="dialog">
    <div class="relative rounded-2xl w-full max-w-4xl max-h-[90vh] flex flex-col overflow-hidden"
         style="background:var(--surface); border:1px solid var(--border); box-shadow:0 24px 64px rgba(0,0,0,0.3);">
        <div class="flex items-center justify-between px-5 py-3 flex-shrink-0"
             style="border-bottom:1px solid var(--border); background:linear-gradient(135deg,#047857,#059669);">
            <h3 id="preview-title" class="text-sm font-semibold text-white truncate max-w-sm" style="font-family:'DM Mono',monospace;"></h3>
            <div class="flex items-center gap-2">
                <a id="preview-download-link" href="#" class="btn-clear" style="padding:.3rem .75rem; font-size:.75rem; background:rgba(255,255,255,0.15); color:#fff; border-color:rgba(212,175,55,0.3);">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                    Download
                </a>
                <button onclick="closePreview()"
                        class="text-white/70 hover:text-white transition text-2xl leading-none w-7 h-7 flex items-center justify-center rounded-lg hover:bg-white/10">&times;</button>
            </div>
        </div>
        <div class="flex-1 overflow-auto flex items-center justify-center p-4 min-h-0">
            <iframe id="preview-iframe" src="" class="hidden w-full rounded-lg" style="height:70vh; border:1px solid var(--border);" title="PDF preview"></iframe>
            <img id="preview-img" src="" alt="Document preview" class="hidden max-w-full max-h-full rounded-lg object-contain">
            <p id="preview-unsupported" class="hidden text-sm" style="color:var(--text-3);">Preview not available for this file type.</p>
        </div>
    </div>
</div>

<script>
function openPreview(previewUrl, downloadUrl, title, ext) {
    document.getElementById('preview-title').textContent = title;
    document.getElementById('preview-download-link').href = downloadUrl;
    const iframe = document.getElementById('preview-iframe');
    const img    = document.getElementById('preview-img');
    const unsup  = document.getElementById('preview-unsupported');
    iframe.classList.add('hidden'); iframe.src = '';
    img.classList.add('hidden');    img.src    = '';
    unsup.classList.add('hidden');
    if (ext === 'pdf') { iframe.src = previewUrl; iframe.classList.remove('hidden'); }
    else if (['jpg','jpeg','png','gif','webp'].includes(ext)) { img.src = previewUrl; img.classList.remove('hidden'); }
    else { unsup.classList.remove('hidden'); }
    document.getElementById('preview-modal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function closePreview() {
    document.getElementById('preview-modal').style.display = 'none';
    document.getElementById('preview-iframe').src = '';
    document.getElementById('preview-img').src    = '';
    document.body.style.overflow = '';
}
document.getElementById('preview-modal').addEventListener('click', function(e) { if (e.target === this) closePreview(); });
document.addEventListener('keydown', function(e) { if (e.key === 'Escape') closePreview(); });
</script>
@endsection