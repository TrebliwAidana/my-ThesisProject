@extends('layouts.app')

@section('title', 'Financial Categories — VSULHS SSLG')
@section('page-title', 'Financial Categories Management')

@push('styles')
<style>
    /* ════════════════════════════════════════════════
       FINANCIAL CATEGORIES — Emerald & Gold Luxury Theme
       Matching Members & all management views
    ════════════════════════════════════════════════ */

    /* ── Hero banner (matching members-hero) ── */
    .categories-hero {
        position: relative;
        overflow: hidden;
        border-radius: 1.25rem;
        padding: 1.75rem 2rem;
        isolation: isolate;
        background: linear-gradient(135deg, #064E3B 0%, #065F46 35%, #047857 60%, #0A3A28 100%);
    }
    .categories-hero::before {
        content: '';
        position: absolute; inset: 0;
        background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,1) 1px, transparent 0);
        background-size: 28px 28px;
        opacity: 0.04; z-index: 0;
    }
    .categories-hero::after {
        content: '';
        position: absolute;
        top: -60px; right: -60px;
        width: 280px; height: 280px;
        background: radial-gradient(circle, rgba(212,175,55,0.35), transparent 65%);
        filter: blur(48px); z-index: 0;
    }
    .hero-content { position: relative; z-index: 1; }

    .hero-title {
        font-family: 'DM Serif Display', serif;
        font-size: clamp(1.5rem, 3.5vw, 2.2rem);
        color: #fff;
        letter-spacing: -0.02em;
        line-height: 1.1;
    }
    .hero-title span {
        background: linear-gradient(90deg, #F0CC55, #D4AF37);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .hero-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        padding: 0.3rem 0.75rem;
        background: rgba(255,255,255,0.1);
        border: 1px solid rgba(212,175,55,0.28);
        border-radius: 999px;
        font-size: 0.72rem;
        font-weight: 600;
        color: rgba(255,255,255,0.88);
        font-family: 'DM Mono', monospace;
    }

    /* ── Primary Action Button (Emerald → Gold on hover) ── */
    .btn-emerald {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.55rem 1.2rem;
        background: linear-gradient(135deg, var(--emerald), var(--emerald-dark));
        color: #fff;
        font-size: 0.8rem;
        font-weight: 700;
        border-radius: 0.75rem;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        font-family: 'Outfit', sans-serif;
        box-shadow: 0 2px 10px rgba(5,150,105,0.22);
    }
    .btn-emerald:hover {
        background: linear-gradient(135deg, var(--gold), var(--gold-dark));
        color: #0f172a;
        transform: translateY(-1px);
        box-shadow: 0 4px 16px rgba(212,175,55,0.35);
    }

    /* ── Filter Card ── */
    .filter-card {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 1rem;
        padding: 1.25rem;
        transition: all 0.25s cubic-bezier(0.4,0,0.2,1);
        position: relative;
        overflow: hidden;
    }
    .filter-card::before {
        content: '';
        position: absolute;
        bottom: 0; left: 0; right: 0;
        height: 2px;
        background: linear-gradient(90deg, var(--emerald), var(--gold));
        transform: scaleX(0);
        transform-origin: left;
        transition: transform 0.3s ease;
    }
    .filter-card:hover {
        border-color: rgba(212,175,55,0.4);
        box-shadow: 0 8px 28px rgba(0,0,0,0.08), 0 0 0 1px rgba(212,175,55,0.15);
        transform: translateY(-3px);
    }
    .filter-card:hover::before { transform: scaleX(1); }

    .filter-label {
        display: block;
        font-size: 0.68rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: var(--text-3);
        margin-bottom: 0.5rem;
        font-family: 'DM Mono', monospace;
    }

    .filter-input, .filter-select {
        width: 100%;
        padding: 0.5rem 0.875rem;
        font-size: 0.83rem;
        background: var(--surface-2);
        border: 1.5px solid var(--border);
        border-radius: 0.75rem;
        color: var(--text);
        transition: all 0.2s ease;
        font-family: 'Outfit', sans-serif;
    }
    .filter-input:focus, .filter-select:focus {
        outline: none;
        border-color: var(--gold);
        box-shadow: 0 0 0 3px rgba(212,175,55,0.12);
        background: var(--surface);
    }

    .btn-filter {
        background: linear-gradient(135deg, var(--gold), var(--gold-dark));
        color: #0f172a;
        padding: 0.5rem 1.2rem;
        border-radius: 0.75rem;
        font-size: 0.78rem;
        font-weight: 700;
        font-family: 'Outfit', sans-serif;
        border: none;
        transition: all 0.2s ease;
        cursor: pointer;
        box-shadow: 0 2px 10px rgba(212,175,55,0.25);
    }
    .btn-filter:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 16px rgba(212,175,55,0.35);
    }

    .btn-reset {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        background: transparent;
        border: 1.5px solid var(--border);
        color: var(--text-3);
        padding: 0.5rem 1.2rem;
        border-radius: 0.75rem;
        font-size: 0.78rem;
        font-weight: 600;
        font-family: 'Outfit', sans-serif;
        transition: all 0.18s ease;
        text-decoration: none;
        cursor: pointer;
    }
    .btn-reset:hover {
        border-color: rgba(212,175,55,0.5);
        color: var(--gold-dark);
        background: rgba(212,175,55,0.06);
    }
    html.dark .btn-reset:hover { color: var(--gold-light); }

    /* ── Table (matches members-table) ── */
    .table-wrap {
        background: var(--surface);
        border: 1px solid var(--border);
        border-radius: 1.25rem;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0,0,0,0.05);
    }
    html.dark .table-wrap { box-shadow: 0 4px 20px rgba(0,0,0,0.22); }

    .data-table {
        width: 100%;
        min-width: 800px;
        border-collapse: collapse;
        font-size: 0.82rem;
    }

    .data-table thead tr {
        background: linear-gradient(135deg, #064E3B 0%, #047857 60%, #065F46 100%);
    }

    .data-table th {
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
    .data-table th:last-child { text-align: right; }

    .data-table tbody tr {
        border-bottom: 1px solid var(--border);
        transition: background 0.15s ease;
    }
    .data-table tbody tr:last-child { border-bottom: none; }
    .data-table tbody tr:hover {
        background: rgba(212,175,55,0.025);
        box-shadow: inset 3px 0 0 var(--gold);
    }

    .data-table td {
        padding: 0.75rem 1rem;
        color: var(--text-2);
        vertical-align: middle;
    }
    .data-table td:last-child { text-align: right; }

    /* ── Badges ── */
    .badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.2rem 0.65rem;
        border-radius: 9999px;
        font-size: 0.65rem;
        font-weight: 700;
        font-family: 'DM Mono', monospace;
        white-space: nowrap;
    }
    .badge-income     { background: rgba(5,150,105,0.1); color: #047857; border: 1px solid rgba(5,150,105,0.2); }
    .badge-expense    { background: rgba(220,38,38,0.08); color: #dc2626; border: 1px solid rgba(220,38,38,0.18); }
    .badge-receivable { background: rgba(37,99,235,0.1); color: #1e40af; border: 1px solid rgba(37,99,235,0.2); }
    .badge-deleted    { background: rgba(107,114,128,0.1); color: #6b7280; border: 1px solid rgba(107,114,128,0.2); }

    html.dark .badge-income     { background: rgba(16,185,129,0.15); color: #6ee7b7; border-color: rgba(16,185,129,0.3); }
    html.dark .badge-expense    { background: rgba(248,113,113,0.12); color: #fca5a5; border-color: rgba(248,113,113,0.25); }
    html.dark .badge-receivable { background: rgba(96,165,250,0.12); color: #93c5fd; border-color: rgba(96,165,250,0.25); }
    html.dark .badge-deleted    { background: rgba(148,163,184,0.15); color: #cbd5e1; border-color: rgba(148,163,184,0.25); }

    /* ── Action Buttons ── */
    .action-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.25rem 0.6rem;
        font-size: 0.65rem;
        font-weight: 700;
        font-family: 'DM Mono', monospace;
        border-radius: 0.4rem;
        border: 1px solid transparent;
        cursor: pointer;
        transition: all 0.15s ease;
        text-decoration: none;
        background: none;
    }
    .action-edit {
        color: var(--gold-dark);
        border-color: rgba(212,175,55,0.25);
        background: rgba(212,175,55,0.07);
    }
    .action-edit:hover {
        background: rgba(212,175,55,0.16);
        border-color: rgba(212,175,55,0.45);
    }
    .action-delete {
        color: #64748b;
        border-color: rgba(100,116,139,0.2);
        background: rgba(100,116,139,0.06);
    }
    .action-delete:hover {
        color: #dc2626;
        border-color: rgba(220,38,38,0.3);
        background: rgba(220,38,38,0.06);
    }
    .action-restore {
        color: #059669;
        border-color: rgba(5,150,105,0.2);
        background: rgba(5,150,105,0.06);
    }
    .action-restore:hover {
        background: rgba(5,150,105,0.14);
        border-color: rgba(5,150,105,0.4);
    }
    .action-force {
        color: #7c3aed;
        border-color: rgba(124,58,237,0.2);
        background: rgba(124,58,237,0.06);
    }
    .action-force:hover {
        background: rgba(124,58,237,0.14);
        border-color: rgba(124,58,237,0.4);
    }

    html.dark .action-edit { color: var(--gold-light); }
    html.dark .action-delete { color: #fca5a5; }
    html.dark .action-restore { color: #6ee7b7; }
    html.dark .action-force { color: #a78bfa; }

    /* ── Empty State ── */
    .empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.75rem;
        padding: 4rem 1rem;
        text-align: center;
    }
    .empty-icon-ring {
        width: 4.5rem;
        height: 4.5rem;
        border-radius: 50%;
        background: linear-gradient(135deg, rgba(5,150,105,0.1), rgba(212,175,55,0.08));
        border: 1.5px dashed rgba(212,175,55,0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--text-3);
    }

    /* ── Pagination ── */
    .pagination-wrap {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        padding: 0.875rem 1.25rem;
        border-top: 1px solid var(--border);
        background: var(--surface-2);
    }
    .pagination-info {
        font-size: 0.7rem;
        color: var(--text-3);
        font-family: 'DM Mono', monospace;
    }
    .pagination-btns {
        display: flex;
        gap: 0.25rem;
    }
    .page-btn {
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
    .page-btn:not(.disabled):not(.current):hover {
        border-color: var(--gold);
        color: var(--gold-dark);
        background: rgba(212,175,55,0.08);
    }
    html.dark .page-btn:not(.disabled):not(.current):hover { color: var(--gold-light); }
    .page-btn.current {
        background: linear-gradient(135deg, var(--emerald), var(--emerald-dark));
        border-color: var(--emerald-dark);
        color: #fff;
        box-shadow: 0 2px 10px rgba(5,150,105,0.3);
    }
    .page-btn.disabled {
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
    .anim-4 { animation: fadeUp 0.38s ease 0.22s both; }

    /* Responsive */
    @media (max-width: 640px) {
        .categories-hero { padding: 1.25rem; }
        .hero-title { font-size: 1.4rem; }
        .filter-card .flex { flex-direction: column; }
    }
</style>
@endpush

@section('content')
<div class="space-y-5">

    {{-- ─── HERO BANNER (matches members-hero) ─── --}}
    <div class="categories-hero anim-1">
        <div class="hero-content flex flex-wrap justify-between items-start gap-4">
            <div>
                <p class="text-emerald-300/70 text-[10px] font-bold tracking-[0.2em] uppercase mb-2"
                   style="font-family:'DM Mono',monospace;">
                    {{ now()->format('F Y') }} · Financial Management
                </p>
                <h1 class="hero-title mb-3">
                    Financial<br><span>Categories</span>
                </h1>
                <div class="flex flex-wrap gap-2">
                    <span class="hero-pill">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Manage transaction categories
                    </span>
                    <span class="hero-pill">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                        </svg>
                        Income · Expense · Receivable
                    </span>
                </div>
            </div>

            {{-- New Category Button - Emerald base with Gold hover (matching Add Member button) --}}
            @if(Auth::user()->role->level === 1 || Auth::user()->hasPermission('financial_categories.create'))
            <a href="{{ route('admin.financial-categories.create') }}" class="btn-emerald" data-nav-link>
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                New Category
            </a>
            @endif
        </div>
    </div>

    {{-- ─── FILTER CARD ─── --}}
    <div class="filter-card anim-2">
        <form method="GET" action="{{ route('admin.financial-categories.index') }}" class="flex flex-wrap gap-4 items-end">
            <div class="flex-1 min-w-[160px]">
                <label class="filter-label">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Name…" class="filter-input">
            </div>
            <div class="w-40">
                <label class="filter-label">Type</label>
                <select name="type" class="filter-select">
                    <option value="">All Types</option>
                    <option value="income"     {{ request('type') === 'income'     ? 'selected' : '' }}>Income</option>
                    <option value="expense"    {{ request('type') === 'expense'    ? 'selected' : '' }}>Expense</option>
                    <option value="receivable" {{ request('type') === 'receivable' ? 'selected' : '' }}>Receivable</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit" class="btn-filter">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2a1 1 0 01-.293.707L13 13.414V19a1 1 0 01-.553.894l-4 2A1 1 0 017 21v-7.586L3.293 6.707A1 1 0 013 6V4z"/>
                    </svg>
                    Apply Filter
                </button>
                <a href="{{ route('admin.financial-categories.index') }}" class="btn-reset">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- ─── TABLE (matches members-table) ─── --}}
    <div class="table-wrap anim-3">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Description</th>
                        <th>Created</th>
                        <th class="right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $cat)
                        @php
                            $isTrashed = $cat->trashed();
                            $typeBadge = match($cat->type) {
                                'income'     => 'badge-income',
                                'expense'    => 'badge-expense',
                                'receivable' => 'badge-receivable',
                                default      => 'badge-income',
                            };
                        @endphp
                        <tr class="{{ $isTrashed ? 'opacity-75' : '' }}">
                            <td class="font-mono text-xs text-text-3">{{ $cat->id }}</td>
                            <td>
                                <span class="font-semibold text-text">{{ $cat->name }}</span>
                                @if($isTrashed)
                                    <span class="badge badge-deleted ml-2">
                                        <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Deleted
                                    </span>
                                @endif
                             </td>
                            <td><span class="badge {{ $typeBadge }}">{{ ucfirst($cat->type) }}</span></td>
                            <td class="text-text-3 text-sm">{{ $cat->description ?? '—' }}</td>
                            <td class="font-mono text-xs text-text-3 whitespace-nowrap">{{ $cat->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="flex items-center justify-end gap-1.5">
                                    @if($isTrashed)
                                        <form method="POST" action="{{ route('admin.financial-categories.restore', $cat->id) }}" class="inline">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="action-btn action-restore" title="Restore category">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                                </svg>
                                                Restore
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('admin.financial-categories.forceDelete', $cat->id) }}" class="inline"
                                              onsubmit="return confirm('⚠️ Permanently delete this category?\n\nThis action cannot be undone.')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="action-btn action-force" title="Permanently delete">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                                Force Del
                                            </button>
                                        </form>
                                    @else
                                        <a href="{{ route('admin.financial-categories.edit', $cat->id) }}" class="action-btn action-edit" title="Edit category">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            Edit
                                        </a>
                                        <form method="POST" action="{{ route('admin.financial-categories.destroy', $cat) }}" class="inline"
                                              onsubmit="return confirm('Delete this category?\n\nIt can be restored later.')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="action-btn action-delete" title="Delete category">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                                Delete
                                            </button>
                                        </form>
                                    @endif
                                </div>
                             </td>
                         </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty-state">
                                    <div class="empty-icon-ring">
                                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.25">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                        </svg>
                                    </div>
                                    <p class="text-text-2 font-semibold">No categories found</p>
                                    <p class="text-text-3 text-sm">Try adjusting your search or filters</p>
                                    @if(request()->hasAny(['search', 'type']))
                                    <a href="{{ route('admin.financial-categories.index') }}" class="text-emerald-600 hover:text-emerald-700 text-sm font-medium inline-flex items-center gap-1 mt-2">
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

        {{-- Pagination --}}
        @if($categories->hasPages())
        <div class="pagination-wrap anim-4">
            <p class="pagination-info">
                {{ $categories->firstItem() }}–{{ $categories->lastItem() }} of {{ $categories->total() }} categories
            </p>
            <div class="pagination-btns">
                @if($categories->onFirstPage())
                    <span class="page-btn disabled">← Prev</span>
                @else
                    <a href="{{ $categories->previousPageUrl() }}" data-nav-link class="page-btn">← Prev</a>
                @endif

                @foreach($categories->getUrlRange(max(1, $categories->currentPage() - 2), min($categories->lastPage(), $categories->currentPage() + 2)) as $page => $url)
                    @if($page == $categories->currentPage())
                        <span class="page-btn current">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" data-nav-link class="page-btn">{{ $page }}</a>
                    @endif
                @endforeach

                @if($categories->hasMorePages())
                    <a href="{{ $categories->nextPageUrl() }}" data-nav-link class="page-btn">Next →</a>
                @else
                    <span class="page-btn disabled">Next →</span>
                @endif
            </div>
        </div>
        @endif
    </div>

</div>
@endsection