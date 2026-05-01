@extends('layouts.app')

@section('title', 'Financial Records')
@section('page-title', 'Financial Records')

@push('styles')
<style>
/* ════════════════════════════════════════════════
   FINANCIAL RECORDS — Emerald & Gold Luxury Theme
   Inspired by the Dashboard's deep emerald hero +
   DM Mono data aesthetic
════════════════════════════════════════════════ */

/* ── Hero ── */
.fin-hero {
    position: relative;
    overflow: hidden;
    border-radius: 1.25rem;
    padding: 1.75rem 2rem;
    isolation: isolate;
    background: linear-gradient(135deg, #064E3B 0%, #065F46 35%, #047857 60%, #0A3A28 100%);
}
.fin-hero::before {
    content: '';
    position: absolute; inset: 0;
    background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,1) 1px, transparent 0);
    background-size: 28px 28px;
    opacity: 0.04; z-index: 0;
}
.fin-hero::after {
    content: '';
    position: absolute;
    top: -60px; right: -60px;
    width: 280px; height: 280px;
    background: radial-gradient(circle, rgba(212,175,55,0.35), transparent 65%);
    filter: blur(48px); z-index: 0;
}
.fin-hero-content { position: relative; z-index: 1; }

.fin-hero-title {
    font-family: 'DM Serif Display', serif;
    font-size: clamp(1.5rem, 3.5vw, 2.2rem);
    color: #fff;
    letter-spacing: -0.02em;
    line-height: 1.1;
}
.fin-hero-title span {
    background: linear-gradient(90deg, #F0CC55, #D4AF37);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.fin-hero-pill {
    display: inline-flex; align-items: center; gap: 0.4rem;
    padding: 0.3rem 0.75rem;
    background: rgba(255,255,255,0.1);
    border: 1px solid rgba(212,175,55,0.28);
    border-radius: 999px;
    font-size: 0.72rem; font-weight: 600;
    color: rgba(255,255,255,0.88);
    font-family: 'DM Mono', monospace;
}

/* ── Summary Cards ── */
.fin-stat-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 1rem;
}
.fin-stat-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 1rem;
    padding: 1.1rem 1.2rem;
    position: relative;
    overflow: hidden;
    transition: all 0.25s cubic-bezier(0.4,0,0.2,1);
    cursor: default;
}
.fin-stat-card::after {
    content: '';
    position: absolute;
    bottom: 0; left: 0; right: 0;
    height: 2px;
    transform: scaleX(0);
    transform-origin: left;
    transition: transform 0.3s ease;
    border-radius: 0 0 999px 999px;
}
.fin-stat-card:hover {
    border-color: rgba(212,175,55,0.35);
    transform: translateY(-3px);
    box-shadow: 0 8px 28px rgba(0,0,0,0.08), 0 0 0 1px rgba(212,175,55,0.12);
}
.fin-stat-card:hover::after { transform: scaleX(1); }

.fin-stat-card.balance ::after { background: linear-gradient(90deg, var(--emerald), var(--gold)); }
.fin-stat-card.income  ::after { background: linear-gradient(90deg, #059669, #10b981); }
.fin-stat-card.expense ::after { background: linear-gradient(90deg, #f43f5e, #fb7185); }
.fin-stat-card.pending ::after { background: linear-gradient(90deg, #f59e0b, #fcd34d); }
.fin-stat-card.audited ::after { background: linear-gradient(90deg, #3b82f6, #60a5fa); }

.fin-stat-icon {
    width: 2.1rem; height: 2.1rem;
    border-radius: 0.6rem;
    display: flex; align-items: center; justify-content: center;
    margin-bottom: 0.75rem;
    flex-shrink: 0;
}
.fin-stat-num {
    font-family: 'DM Mono', monospace;
    font-size: 1.6rem;
    font-weight: 700;
    letter-spacing: -0.04em;
    line-height: 1;
    margin-bottom: 0.25rem;
    color: var(--text);
}
.fin-stat-num.em  { color: var(--emerald-dark); }
html.dark .fin-stat-num.em { color: var(--emerald-light); }
.fin-stat-num.rd  { color: #e11d48; }
html.dark .fin-stat-num.rd { color: #fda4af; }
.fin-stat-num.am  { color: #b45309; }
html.dark .fin-stat-num.am { color: #fcd34d; }
.fin-stat-num.bl  { color: #1e40af; }
html.dark .fin-stat-num.bl { color: #93c5fd; }

.fin-stat-label {
    font-size: 0.67rem; font-weight: 700;
    letter-spacing: 0.09em; text-transform: uppercase;
    color: var(--text-3); font-family: 'DM Mono', monospace;
}
.fin-stat-sub {
    font-size: 0.65rem; color: var(--text-3);
    margin-top: 0.2rem; opacity: 0.75;
}



/* ── Filter Panel ── */
.fin-panel {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 1.25rem;
    padding: 1.25rem;
    box-shadow: 0 2px 12px rgba(0,0,0,0.04);
}

.fin-input {
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
.fin-input:focus {
    outline: none;
    border-color: var(--gold);
    box-shadow: 0 0 0 3px rgba(212,175,55,0.12);
    background: var(--surface);
}
.fin-input::placeholder { color: var(--text-3); }
html.dark .fin-input { background: rgba(15,23,42,0.5); }

.fin-select {
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
.fin-select:focus {
    outline: none;
    border-color: var(--gold);
    box-shadow: 0 0 0 3px rgba(212,175,55,0.12);
}
html.dark .fin-select { background: rgba(15,23,42,0.5); }

/* ── Action Buttons ── */
.btn-em {
    display: inline-flex; align-items: center; gap: .4rem;
    padding: .48rem 1rem; font-size: .8rem; font-weight: 700;
    background: linear-gradient(135deg, var(--emerald), var(--emerald-dark));
    color: #fff; border: none; border-radius: .65rem;
    cursor: pointer; transition: all .2s ease; text-decoration: none;
    box-shadow: 0 2px 10px rgba(5,150,105,.22);
    font-family: 'Outfit', sans-serif; white-space: nowrap;
}
.btn-em:hover {
    background: linear-gradient(135deg, var(--gold), var(--gold-dark));
    color: #0f172a; box-shadow: 0 4px 16px rgba(212,175,55,.35);
    transform: translateY(-1px);
}

.btn-rd {
    display: inline-flex; align-items: center; gap: .4rem;
    padding: .48rem 1rem; font-size: .8rem; font-weight: 700;
    background: linear-gradient(135deg, #e11d48, #f43f5e);
    color: #fff; border: none; border-radius: .65rem;
    cursor: pointer; transition: all .2s ease; text-decoration: none;
    box-shadow: 0 2px 10px rgba(225,29,72,.22);
    font-family: 'Outfit', sans-serif; white-space: nowrap;
}
.btn-rd:hover { filter: brightness(1.08); transform: translateY(-1px); }

.btn-pu {
    display: inline-flex; align-items: center; gap: .4rem;
    padding: .48rem 1rem; font-size: .8rem; font-weight: 700;
    background: linear-gradient(135deg, #7c3aed, #6d28d9);
    color: #fff; border: none; border-radius: .65rem;
    cursor: pointer; transition: all .2s ease; text-decoration: none;
    box-shadow: 0 2px 10px rgba(124,58,237,.22);
    font-family: 'Outfit', sans-serif; white-space: nowrap;
}
.btn-pu:hover { filter: brightness(1.08); transform: translateY(-1px); }

.btn-bl {
    display: inline-flex; align-items: center; gap: .4rem;
    padding: .48rem 1rem; font-size: .8rem; font-weight: 700;
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    color: #fff; border: none; border-radius: .65rem;
    cursor: pointer; transition: all .2s ease; text-decoration: none;
    box-shadow: 0 2px 10px rgba(37,99,235,.2);
    font-family: 'Outfit', sans-serif; white-space: nowrap;
}
.btn-bl:hover { filter: brightness(1.08); transform: translateY(-1px); }

.btn-gray {
    display: inline-flex; align-items: center; gap: .4rem;
    padding: .48rem .875rem; font-size: .8rem; font-weight: 600;
    background: var(--surface-3); color: var(--text-2);
    border: 1.5px solid var(--border); border-radius: .65rem;
    cursor: pointer; transition: all .18s ease; text-decoration: none;
    font-family: 'Outfit', sans-serif; white-space: nowrap;
}
.btn-gray:hover {
    border-color: rgba(212,175,55,.4); color: var(--gold-dark);
    background: rgba(212,175,55,.06);
}
html.dark .btn-gray:hover { color: var(--gold-light); }

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

/* ── Table Wrapper ── */
.doc-table {
    table-layout: fixed;
    width: 100%;
}

/* Explicit column widths */
.doc-table col.c-title    { width: 28%; }
.doc-table col.c-category { width: 16%; }  /* enough for badges */
.doc-table col.c-source   { width: 10%; }
.doc-table col.c-by       { width: 14%; }
.doc-table col.c-size     { width: 7%;  }
.doc-table col.c-date     { width: 10%; }
.doc-table col.c-actions  { width: 8%;  }

/* Prevent cells from overflowing */
.doc-table td, .doc-table th {
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.fin-table-wrap {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 1.25rem;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
}
html.dark .fin-table-wrap { box-shadow: 0 4px 20px rgba(0,0,0,0.22); }

/* ── Desktop Table — fixed column widths ── */
.fin-table {
    width: 100%;
    min-width: 960px;
    border-collapse: collapse;
    font-size: 0.82rem;
    table-layout: auto;
}

/* Remove position:relative from thead tr — this is the bug */
.fin-table thead tr {
    background: linear-gradient(135deg, #064E3B 0%, #047857 60%, #065F46 100%);
}

/* Replace ::after with a box-shadow instead */
.fin-table thead tr th:last-child {
    box-shadow: inset 0 -1px 0 rgba(212,175,55,0.5);
}

.fin-table th {
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
.fin-table th.right  { text-align: right; }
.fin-table th.center { text-align: center; }

/* Remove position:relative from tbody tr too */
.fin-table tbody tr {
    border-bottom: 1px solid var(--border);
    transition: background 0.15s ease;
}
.fin-table tbody tr:last-child { border-bottom: none; }
.fin-table tbody tr:hover { background: rgba(212,175,55,0.025); box-shadow: inset 3px 0 0 var(--gold); }

.fin-table td {
    padding: 0.75rem 0.875rem;
    color: var(--text-2);
    vertical-align: middle;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.fin-table td.right   { text-align: right; }
.fin-table td.center  { text-align: center; }
.fin-table td.actions { text-align: right; }

.fin-table tbody tr.overdue { box-shadow: inset 3px 0 0 #f43f5e; }

/* ── Badges ── */
.badge {
    display: inline-flex; align-items: center; gap: 0.25rem;
    padding: 0.18rem 0.55rem;
    border-radius: 999px;
    font-size: 0.67rem; font-weight: 700;
    font-family: 'DM Mono', monospace;
    letter-spacing: 0.04em;
    white-space: nowrap; vertical-align: middle;
}
.badge-income    { background: rgba(5,150,105,0.1);  color:#047857; border:1px solid rgba(5,150,105,0.22); }
.badge-expense   { background: rgba(244,63,94,0.08); color:#be123c; border:1px solid rgba(244,63,94,0.2); }
.badge-receivable{ background: rgba(124,58,237,0.1); color:#6d28d9; border:1px solid rgba(124,58,237,0.2); }
.badge-pending   { background: rgba(245,158,11,0.1); color:#92400e; border:1px solid rgba(245,158,11,0.22); }
.badge-audited   { background: rgba(37,99,235,0.1);  color:#1e40af; border:1px solid rgba(37,99,235,0.2); }
.badge-approved  { background: rgba(5,150,105,0.1);  color:#047857; border:1px solid rgba(5,150,105,0.22); }
.badge-paid      { background: rgba(16,185,129,0.12);color:#064e3b; border:1px solid rgba(16,185,129,0.3); }
.badge-rejected  { background: rgba(244,63,94,0.08); color:#be123c; border:1px solid rgba(244,63,94,0.2); }

html.dark .badge-income    { background:rgba(16,185,129,0.15); color:#6ee7b7; border-color:rgba(16,185,129,0.3); }
html.dark .badge-expense   { background:rgba(244,63,94,0.15);  color:#fda4af; border-color:rgba(244,63,94,0.25); }
html.dark .badge-receivable{ background:rgba(124,58,237,0.18); color:#a78bfa; border-color:rgba(124,58,237,0.3); }
html.dark .badge-pending   { background:rgba(245,158,11,0.15); color:#fcd34d; border-color:rgba(245,158,11,0.25); }
html.dark .badge-audited   { background:rgba(37,99,235,0.18);  color:#93c5fd; border-color:rgba(37,99,235,0.3); }
html.dark .badge-approved  { background:rgba(5,150,105,0.18);  color:#6ee7b7; border-color:rgba(5,150,105,0.3); }
html.dark .badge-paid      { background:rgba(16,185,129,0.2);  color:#a7f3d0; border-color:rgba(16,185,129,0.35); }
html.dark .badge-rejected  { background:rgba(244,63,94,0.15);  color:#fda4af; border-color:rgba(244,63,94,0.25); }

/* ── Inline action links ── */
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
.tbl-action.view    { color: #2563eb; border-color: rgba(37,99,235,0.2); background: rgba(37,99,235,0.06); }
.tbl-action.view:hover { background: rgba(37,99,235,0.14); border-color: rgba(37,99,235,0.4); }
.tbl-action.edit    { color: var(--gold-dark); border-color: rgba(212,175,55,0.25); background: rgba(212,175,55,0.07); }
.tbl-action.edit:hover { background: rgba(212,175,55,0.16); border-color: rgba(212,175,55,0.45); }
.tbl-action.audit   { color: #2563eb; border-color: rgba(37,99,235,0.2); background: rgba(37,99,235,0.06); }
.tbl-action.audit:hover { background: rgba(37,99,235,0.14); border-color: rgba(37,99,235,0.4); }
.tbl-action.approve { color: var(--emerald-dark); border-color: rgba(5,150,105,0.22); background: rgba(5,150,105,0.07); }
.tbl-action.approve:hover { background: rgba(5,150,105,0.14); border-color: rgba(5,150,105,0.4); }
.tbl-action.reject  { color: #e11d48; border-color: rgba(225,29,72,0.2); background: rgba(225,29,72,0.06); }
.tbl-action.reject:hover { background: rgba(225,29,72,0.12); border-color: rgba(225,29,72,0.4); }
.tbl-action.delete  { color: #64748b; border-color: rgba(100,116,139,0.2); background: rgba(100,116,139,0.06); }
.tbl-action.delete:hover { color: #e11d48; border-color: rgba(225,29,72,0.3); background: rgba(225,29,72,0.06); }

html.dark .tbl-action.view    { color: #60a5fa; }
html.dark .tbl-action.edit    { color: var(--gold-light); }
html.dark .tbl-action.audit   { color: #60a5fa; }
html.dark .tbl-action.approve { color: #6ee7b7; }
html.dark .tbl-action.reject  { color: #fda4af; }

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
    background: rgba(212,175,55,0.08); box-shadow: 0 2px 8px rgba(212,175,55,0.15);
}
html.dark .pag-btn:not(.disabled):not(.current):hover { color: var(--gold-light); }
.pag-btn.current {
    background: linear-gradient(135deg, var(--emerald), var(--emerald-dark));
    border-color: var(--emerald-dark); color: #fff;
    box-shadow: 0 2px 10px rgba(5,150,105,0.3);
}
.pag-btn.disabled { opacity: 0.35; cursor: not-allowed; pointer-events: none; }

/* ── Empty State ── */
.fin-empty {
    display: flex; flex-direction: column; align-items: center;
    gap: 0.75rem; padding: 4rem 1rem; text-align: center;
}
.fin-empty-ring {
    width: 4.5rem; height: 4.5rem; border-radius: 50%;
    background: linear-gradient(135deg, rgba(5,150,105,0.1), rgba(212,175,55,0.08));
    border: 1.5px dashed rgba(212,175,55,0.3);
    display: flex; align-items: center; justify-content: center;
    color: var(--text-3);
}

/* ── Warning notice ── */
.fin-notice {
    font-size: 0.75rem; padding: 0.6rem 1rem;
    border-radius: 0.75rem; font-family: 'DM Mono', monospace;
    margin-top: 0.75rem; display: flex; align-items: flex-start; gap: 0.5rem;
}
.fin-notice.info  { background: rgba(5,150,105,0.07); color: var(--emerald-dark); border: 1px solid rgba(5,150,105,0.2); }
.fin-notice.warn  { background: rgba(245,158,11,0.07); color: #92400e; border: 1px solid rgba(245,158,11,0.22); }
html.dark .fin-notice.info  { color: var(--emerald-light); }
html.dark .fin-notice.warn  { color: #fcd34d; }

/* ── Animations ── */
@keyframes fadeUp {
    from { opacity: 0; transform: translateY(14px); }
    to   { opacity: 1; transform: translateY(0); }
}
.a1 { animation: fadeUp 0.38s ease 0.04s both; }
.a2 { animation: fadeUp 0.38s ease 0.10s both; }
.a3 { animation: fadeUp 0.38s ease 0.16s both; }
.a4 { animation: fadeUp 0.38s ease 0.22s both; }
.a5 { animation: fadeUp 0.38s ease 0.28s both; }
</style>
@endpush

@section('content')
@php
    $user    = auth()->user();
    $isAdmin = $user->role->level === 1;
    $canCreate      = $isAdmin || $user->hasPermission('financial.create');
    $canViewReports = $isAdmin || $user->hasPermission('financial.approve');
    $canViewTrash   = $isAdmin || $user->hasPermission('financial.delete');
@endphp

<div class="space-y-5">

    {{-- ── HERO ── --}}
    <div class="fin-hero a1">
        <div class="fin-hero-content">
            <p class="text-emerald-300/70 text-[10px] font-bold tracking-[0.2em] uppercase mb-2"
            style="font-family:'DM Mono',monospace;">{{ now()->format('F Y') }}</p>
            <h1 class="fin-hero-title mb-3">Financial<br><span>Records</span></h1>
            <div class="flex flex-wrap gap-2">
                <span class="fin-hero-pill">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                    Income · Expense · Receivable
                </span>
            </div>
        </div>
    </div>

    {{-- ── SUMMARY CARDS ── --}}
    <div class="fin-stat-grid a2">
        {{-- Balance --}}
        <div class="fin-stat-card balance">
            <div class="fin-stat-icon" style="background:rgba(5,150,105,0.1);">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:var(--emerald);" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"/></svg>
            </div>
            <div class="fin-stat-num {{ $balance >= 0 ? 'em' : 'rd' }}">
                {{ $balance < 0 ? '-' : '' }}₱{{ number_format(abs($balance), 0) }}
            </div>
            <div class="fin-stat-label">Balance</div>
            <div class="fin-stat-sub">{{ $balance >= 0 ? 'Surplus' : 'Deficit' }}</div>
        </div>

        {{-- Income --}}
        <div class="fin-stat-card income">
            <div class="fin-stat-icon" style="background:rgba(5,150,105,0.1);">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:var(--emerald);" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M7 11l5-5m0 0l5 5m-5-5v12"/></svg>
            </div>
            <div class="fin-stat-num em">₱{{ number_format($totalIncome, 0) }}</div>
            <div class="fin-stat-label">Total Income</div>
            <div class="fin-stat-sub">Cash + Collected</div>
        </div>

        {{-- Expenses --}}
        <div class="fin-stat-card expense">
            <div class="fin-stat-icon" style="background:rgba(244,63,94,0.08);">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:#e11d48;" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M17 13l-5 5m0 0l-5-5m5 5V6"/></svg>
            </div>
            <div class="fin-stat-num rd">₱{{ number_format($expenseTotal, 0) }}</div>
            <div class="fin-stat-label">Total Expenses</div>
            <div class="fin-stat-sub">Approved only</div>
        </div>

        {{-- Pending --}}
        <div class="fin-stat-card pending">
            <div class="fin-stat-icon" style="background:rgba(245,158,11,0.1);">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:#b45309;" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <div class="fin-stat-num am">{{ $pendingCount }}</div>
            <div class="fin-stat-label">Pending</div>
            <div class="fin-stat-sub">Awaiting audit</div>
        </div>

        {{-- Audited --}}
        <div class="fin-stat-card audited">
            <div class="fin-stat-icon" style="background:rgba(37,99,235,0.1);">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color:#2563eb;" stroke-width="1.75"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
            </div>
            <div class="fin-stat-num bl">{{ $auditedCount }}</div>
            <div class="fin-stat-label">Audited</div>
            <div class="fin-stat-sub">Awaiting approval</div>
        </div>
    </div>

     {{-- ── ACTION TOOLBAR ── --}}
    <div class="fin-toolbar a2">
        <div class="flex flex-wrap items-center gap-2">
            @if($canCreate)
                <a href="{{ route('financial.income.create') }}" class="btn-em">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Add Income
                </a>
                <a href="{{ route('financial.expense.create') }}" class="btn-rd">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M20 12H4"/></svg>
                    Add Expense
                </a>
                <a href="{{ route('financial.receivable.create') }}" class="btn-pu">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Add Receivable
                </a>
            @endif

            {{-- Divider --}}
            @if($canCreate && ($canViewReports || $canViewTrash))
                <div class="fin-toolbar-divider"></div>
            @endif

            @if($canViewReports)
                <a href="{{ route('financial.report.form') }}" class="btn-bl">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Generate Report
                </a>
            @endif
            @if($canViewTrash)
                <a href="{{ route('financial.trash') }}" class="btn-gray">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    Trash
                </a>
            @endif
        </div>
    </div>


    {{-- ── FILTER PANEL ── --}}
    <div class="fin-panel a3">
        <form method="GET" action="{{ route('financial.index') }}" class="space-y-3">
            <div class="flex flex-wrap gap-2">
                <div class="flex-1 min-w-[200px]">
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Search description…" class="fin-input">
                </div>
                <select name="type" class="fin-select">
                    <option value="">All Types</option>
                    <option value="income"     {{ request('type') === 'income'     ? 'selected' : '' }}>Income</option>
                    <option value="expense"    {{ request('type') === 'expense'    ? 'selected' : '' }}>Expense</option>
                    <option value="receivable" {{ request('type') === 'receivable' ? 'selected' : '' }}>Receivable</option>
                </select>
                <select name="status" class="fin-select">
                    <option value="">All Statuses</option>
                    <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>Pending</option>
                    <option value="audited"  {{ request('status') === 'audited'  ? 'selected' : '' }}>Audited</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="paid"     {{ request('status') === 'paid'     ? 'selected' : '' }}>Paid</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
                <select name="category" class="fin-select">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="fin-select">
                <input type="date" name="date_to"   value="{{ request('date_to') }}"   class="fin-select">
                <label class="fin-select inline-flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="show_approved" value="1" {{ request('show_approved') ? 'checked' : '' }}
                           class="rounded" style="accent-color:var(--emerald);">
                    <span style="font-size:.8rem;">Show approved/paid</span>
                </label>
                <button type="submit" class="btn-filter">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/></svg>
                    Filter
                </button>
                @if(request()->hasAny(['search','type','status','category','date_from','date_to','show_approved']))
                    <a href="{{ route('financial.index') }}" class="btn-gray">✕ Clear</a>
                @endif
            </div>
        </form>

        @if(!request('show_approved'))
            <div class="fin-notice info">
                <svg class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Showing active records only (pending, audited, rejected). Summary totals always reflect all approved/paid transactions.
                <a href="{{ request()->fullUrlWithQuery(['show_approved' => 1]) }}"
                   style="color:var(--emerald-dark); text-decoration:underline; margin-left:.35rem;">Show approved/paid →</a>
            </div>
        @else
            <div class="fin-notice warn">
                <svg class="w-3.5 h-3.5 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                Showing all records including finalized (approved/paid). These are read-only — no actions available.
            </div>
        @endif
    </div>

    {{-- ── DESKTOP TABLE ── --}}
    <div class="fin-table-wrap a4 hidden md:block">
        <div class="overflow-x-auto">
            <table class="fin-table">
                <colgroup>
                    <col class="c-date">
                    <col class="c-desc">
                    <col class="c-cat">
                    <col class="c-type">
                    <col class="c-amount">
                    <col class="c-status">
                    <col class="c-by">
                    <col class="c-auditor">
                    <col class="c-approv">
                    <col class="c-actions">
                </colgroup>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Description</th>
                        <th>Category</th>
                        <th class="center">Type</th>
                        <th class="right">Amount</th>
                        <th class="center">Status</th>
                        <th>Submitted By</th>
                        <th>Auditor</th>
                        <th>Approver</th>
                        <th class="right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $tx)
                    @php
                        $canEdit    = ($isAdmin || $user->hasPermission('financial.edit'))   && $tx->status === 'pending';
                        $canDelete  = ($isAdmin || $user->hasPermission('financial.delete'));
                        $canAudit   = ($isAdmin || $user->hasPermission('financial.audit'))  && $tx->status === 'pending';
                        $canApprove = ($isAdmin || $user->hasPermission('financial.approve')) && $tx->status === 'audited';
                        $canReject  = $canApprove;
                        $isOverdue  = $tx->type === 'receivable' && $tx->isOverdue();
                    @endphp
                    <tr class="{{ $isOverdue ? 'overdue' : '' }}">
                        {{-- Date --}}
                        <td style="font-family:'DM Mono',monospace; font-size:.72rem; color:var(--text-3);">
                            {{ $tx->transaction_date->format('M d, Y') }}
                        </td>

                        {{-- Description --}}
                        <td>
                            <p class="font-semibold truncate" style="color:var(--text); font-size:.83rem;">{{ $tx->description }}</p>
                            @if($tx->type === 'receivable' && $tx->customer_name)
                                <p class="text-[11px] mt-0.5" style="color:#7c3aed; font-family:'DM Mono',monospace;">
                                    👤 {{ $tx->customer_name }}
                                </p>
                            @endif
                        </td>

                        {{-- Category --}}
                        <td style="font-size:.75rem; color:var(--text-3);">
                            <span class="truncate block">{{ $tx->category ?? '—' }}</span>
                        </td>

                        {{-- Type --}}
                        <td class="center">
                            <span class="badge badge-{{ $tx->type }}">
                                {{ $tx->type === 'income' ? '↑' : ($tx->type === 'expense' ? '↓' : '⏳') }}
                                {{ ucfirst($tx->type) }}
                            </span>
                        </td>

                        {{-- Amount --}}
                        <td class="right" style="font-family:'DM Mono',monospace; font-weight:700; font-size:.83rem;
                            color:{{ $tx->type === 'income' ? 'var(--emerald-dark)' : ($tx->type === 'expense' ? '#e11d48' : '#7c3aed') }};">
                            {{ $tx->type === 'income' ? '+' : ($tx->type === 'expense' ? '−' : '') }}{{ $tx->formatted_amount }}
                        </td>

                        {{-- Status --}}
                        <td class="center">
                            <span class="badge badge-{{ $tx->status }}">
                                {{ $tx->status === 'paid' ? '✓ Paid' : ucfirst($tx->status) }}
                            </span>
                        </td>

                        {{-- Submitted By --}}
                        <td style="font-size:.75rem; color:var(--text-3);">
                            <span class="truncate block">{{ $tx->user->full_name ?? '—' }}</span>
                        </td>

                        {{-- Auditor --}}
                        <td style="font-size:.75rem; color:var(--text-3);">
                            <span class="truncate block">{{ $tx->auditor->full_name ?? '—' }}</span>
                        </td>

                        {{-- Approver --}}
                        <td style="font-size:.75rem; color:var(--text-3);">
                            <span class="truncate block">{{ $tx->approver->full_name ?? '—' }}</span>
                        </td>

                        {{-- Actions --}}
                        <td class="actions">
                            <div class="flex items-center justify-end gap-1 flex-wrap">
                                <a href="{{ route('financial.show', $tx->id) }}" class="tbl-action view">View</a>
                                @if($canEdit)
                                    <a href="{{ route('financial.edit', $tx->id) }}" class="tbl-action edit">Edit</a>
                                @endif
                                @if($canAudit)
                                    <form method="POST" action="{{ route('financial.audit', $tx->id) }}" class="inline">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="tbl-action audit">Audit</button>
                                    </form>
                                @endif
                                @if($canApprove)
                                    <form method="POST" action="{{ route('financial.approve', $tx->id) }}" class="inline">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="tbl-action approve">Approve</button>
                                    </form>
                                @endif
                                @if($canReject)
                                    <form method="POST" action="{{ route('financial.reject', $tx->id) }}" class="inline">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="tbl-action reject">Reject</button>
                                    </form>
                                @endif
                                @if($canDelete)
                                    <form method="POST" action="{{ route('financial.destroy', $tx->id) }}" class="inline"
                                          onsubmit="return confirm({{ $tx->status === 'approved' || $tx->status === 'paid' ? '\'This is a finalized transaction. Deleting it will also remove its approval document. Continue?\'' : '\'Delete this transaction?\'' }})">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="tbl-action delete">Del</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10">
                            <div class="fin-empty">
                                <div class="fin-empty-ring">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.25"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                </div>
                                <p style="color:var(--text-3); font-size:.875rem; font-weight:600;">No transactions found.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
        <div class="pag-wrap">
            <p class="pag-info">{{ $transactions->firstItem() }}–{{ $transactions->lastItem() }} of {{ $transactions->total() }} records</p>
            <div class="pag-btns">
                @if($transactions->onFirstPage())
                    <span class="pag-btn disabled">← Prev</span>
                @else
                    <a href="{{ $transactions->previousPageUrl() }}" class="pag-btn">← Prev</a>
                @endif
                @foreach($transactions->getUrlRange(max(1,$transactions->currentPage()-2), min($transactions->lastPage(),$transactions->currentPage()+2)) as $page => $url)
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

    {{-- ── MOBILE CARDS ── --}}
    <div class="fin-table-wrap a4 md:hidden">
        @forelse($transactions as $tx)
        @php
            $canEdit    = ($isAdmin || $user->hasPermission('financial.edit'))   && $tx->status === 'pending';
            $canDelete  = ($isAdmin || $user->hasPermission('financial.delete'));
            $canAudit   = ($isAdmin || $user->hasPermission('financial.audit'))  && $tx->status === 'pending';
            $canApprove = ($isAdmin || $user->hasPermission('financial.approve')) && $tx->status === 'audited';
            $canReject  = $canApprove;
        @endphp
        <div class="mob-card">
            <div class="flex justify-between items-start mb-2">
                <div class="flex-1 min-w-0 mr-2">
                    <p class="font-semibold text-sm truncate" style="color:var(--text);">{{ $tx->description }}</p>
                    <p class="text-[11px] mt-0.5" style="color:var(--text-3); font-family:'DM Mono',monospace;">
                        {{ $tx->transaction_date->format('M d, Y') }}
                        @if($tx->category) · {{ $tx->category }} @endif
                    </p>
                    @if($tx->type === 'receivable' && $tx->customer_name)
                        <p class="text-[11px] mt-0.5" style="color:#7c3aed;">👤 {{ $tx->customer_name }}</p>
                    @endif
                </div>
                <span class="font-bold text-sm flex-shrink-0" style="font-family:'DM Mono',monospace;
                    color:{{ $tx->type === 'income' ? 'var(--emerald-dark)' : ($tx->type === 'expense' ? '#e11d48' : '#7c3aed') }};">
                    {{ $tx->type === 'income' ? '+' : ($tx->type === 'expense' ? '−' : '') }}{{ $tx->formatted_amount }}
                </span>
            </div>
            <div class="flex flex-wrap gap-1.5 mb-2">
                <span class="badge badge-{{ $tx->type }}">{{ ucfirst($tx->type) }}</span>
                <span class="badge badge-{{ $tx->status }}">{{ $tx->status === 'paid' ? '✓ Paid' : ucfirst($tx->status) }}</span>
            </div>
            <div class="text-[11px] mb-2" style="color:var(--text-3); font-family:'DM Mono',monospace;">
                by {{ $tx->user->full_name ?? '—' }}
                @if($tx->auditor) · audited: {{ $tx->auditor->full_name }} @endif
                @if($tx->approver) · approved: {{ $tx->approver->full_name }} @endif
            </div>
            <div class="flex flex-wrap gap-1.5 pt-2" style="border-top:1px solid var(--border);">
                <a href="{{ route('financial.show', $tx->id) }}" class="tbl-action view">View</a>
                @if($canEdit)   <a href="{{ route('financial.edit', $tx->id) }}" class="tbl-action edit">Edit</a> @endif
                @if($canAudit)
                    <form method="POST" action="{{ route('financial.audit', $tx->id) }}" class="inline">@csrf @method('PATCH')
                        <button type="submit" class="tbl-action audit">Audit</button></form>
                @endif
                @if($canApprove)
                    <form method="POST" action="{{ route('financial.approve', $tx->id) }}" class="inline">@csrf @method('PATCH')
                        <button type="submit" class="tbl-action approve">Approve</button></form>
                @endif
                @if($canReject)
                    <form method="POST" action="{{ route('financial.reject', $tx->id) }}" class="inline">@csrf @method('PATCH')
                        <button type="submit" class="tbl-action reject">Reject</button></form>
                @endif
                @if($canDelete)
                    <form method="POST" action="{{ route('financial.destroy', $tx->id) }}" class="inline"
                          onsubmit="return confirm('Delete this transaction?')">@csrf @method('DELETE')
                        <button type="submit" class="tbl-action delete">Delete</button></form>
                @endif
            </div>
        </div>
        @empty
        <div class="fin-empty">
            <div class="fin-empty-ring">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.25"><path stroke-linecap="round" stroke-linejoin="round" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <p style="color:var(--text-3); font-size:.875rem; font-weight:600;">No transactions found.</p>
        </div>
        @endforelse

        @if($transactions->hasPages())
        <div class="pag-wrap">
            <p class="pag-info">{{ $transactions->firstItem() }}–{{ $transactions->lastItem() }} of {{ $transactions->total() }}</p>
            <div class="pag-btns">
                @if($transactions->onFirstPage())
                    <span class="pag-btn disabled">← Prev</span>
                @else
                    <a href="{{ $transactions->previousPageUrl() }}" class="pag-btn">← Prev</a>
                @endif
                @foreach($transactions->getUrlRange(max(1,$transactions->currentPage()-2), min($transactions->lastPage(),$transactions->currentPage()+2)) as $page => $url)
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