@extends('layouts.app')

@section('title', 'Document Categories — VSULHS SSLG')
@section('page-title', 'Manage Document Categories')

@push('styles')
<style>
/* ════════════════════════════════════════════════
   DOCUMENT CATEGORIES — Emerald & Gold Luxury Theme
   Matching all other management views
════════════════════════════════════════════════ */

/* ── Hero Section ── */
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
.categories-hero-content { position: relative; z-index: 1; }

.categories-hero-title {
    font-family: 'DM Serif Display', serif;
    font-size: clamp(1.5rem, 3.5vw, 2.2rem);
    color: #fff;
    letter-spacing: -0.02em;
    line-height: 1.1;
}
.categories-hero-title span {
    background: linear-gradient(90deg, #F0CC55, #D4AF37);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.categories-hero-pill {
    display: inline-flex; align-items: center; gap: 0.4rem;
    padding: 0.3rem 0.75rem;
    background: rgba(255,255,255,0.1);
    border: 1px solid rgba(212,175,55,0.28);
    border-radius: 999px;
    font-size: 0.72rem; font-weight: 600;
    color: rgba(255,255,255,0.88);
    font-family: 'DM Mono', monospace;
}

/* ── Flash Messages ── */
.flash-success {
    background: rgba(5,150,105,0.1);
    border: 1px solid rgba(5,150,105,0.25);
    color: #047857;
    border-radius: 1rem;
    padding: 1rem;
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    margin-bottom: 1rem;
}
html.dark .flash-success {
    background: rgba(16,185,129,0.15);
    color: #6ee7b7;
    border-color: rgba(16,185,129,0.3);
}
.flash-error {
    background: rgba(220,38,38,0.1);
    border: 1px solid rgba(220,38,38,0.25);
    color: #dc2626;
    border-radius: 1rem;
    padding: 1rem;
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    margin-bottom: 1rem;
}
html.dark .flash-error {
    background: rgba(248,113,113,0.15);
    color: #fca5a5;
    border-color: rgba(248,113,113,0.3);
}

/* ── Categories Card ── */
.categories-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 1.25rem;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
}
html.dark .categories-card { box-shadow: 0 4px 20px rgba(0,0,0,0.22); }

.categories-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.875rem 1.25rem;
    border-bottom: 1px solid var(--border);
    background: var(--surface-2);
}
.categories-title {
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    color: var(--text-2);
    font-family: 'DM Mono', monospace;
}

/* ── Buttons ── */
.btn-emerald {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    font-size: 0.75rem;
    font-weight: 700;
    background: linear-gradient(135deg, var(--emerald), var(--emerald-dark));
    color: #fff;
    border: none;
    border-radius: 0.65rem;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
    box-shadow: 0 2px 10px rgba(5,150,105,0.22);
    font-family: 'Outfit', sans-serif;
}
.btn-emerald:hover {
    background: linear-gradient(135deg, var(--gold), var(--gold-dark));
    color: #0f172a;
    box-shadow: 0 4px 16px rgba(212,175,55,0.35);
    transform: translateY(-1px);
}

/* ── Categories Table ── */
.categories-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.82rem;
}
.categories-table thead tr {
    background: linear-gradient(135deg, #064E3B 0%, #047857 60%, #065F46 100%);
}
.categories-table th {
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
.categories-table th:last-child { text-align: right; }
.categories-table tbody tr {
    border-bottom: 1px solid var(--border);
    transition: background 0.15s ease;
}
.categories-table tbody tr:last-child { border-bottom: none; }
.categories-table tbody tr:hover { 
    background: rgba(212,175,55,0.025); 
    box-shadow: inset 3px 0 0 var(--gold);
}
.categories-table td {
    padding: 0.75rem 1rem;
    color: var(--text-2);
    vertical-align: middle;
}
.categories-table td:last-child { text-align: right; }

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
.status-badge-active {
    background: rgba(5,150,105,0.1);
    color: #047857;
    border: 1px solid rgba(5,150,105,0.2);
}
.status-badge-inactive {
    background: rgba(107,114,128,0.1);
    color: #6b7280;
    border: 1px solid rgba(107,114,128,0.2);
}
html.dark .status-badge-active {
    background: rgba(16,185,129,0.15);
    color: #6ee7b7;
}
html.dark .status-badge-inactive {
    background: rgba(107,114,128,0.2);
    color: #cbd5e1;
}

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
    white-space: nowrap;
    background: none;
}
.action-btn-edit {
    color: var(--gold-dark);
    border-color: rgba(212,175,55,0.25);
    background: rgba(212,175,55,0.07);
}
.action-btn-edit:hover {
    background: rgba(212,175,55,0.16);
    border-color: rgba(212,175,55,0.45);
}
.action-btn-delete {
    color: #64748b;
    border-color: rgba(100,116,139,0.2);
    background: rgba(100,116,139,0.06);
}
.action-btn-delete:hover {
    color: #dc2626;
    border-color: rgba(220,38,38,0.3);
    background: rgba(220,38,38,0.06);
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
.empty-state-icon {
    width: 4rem;
    height: 4rem;
    border-radius: 50%;
    background: linear-gradient(135deg, rgba(5,150,105,0.1), rgba(212,175,55,0.08));
    border: 1.5px dashed rgba(212,175,55,0.3);
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 0.5rem;
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
html.dark .pag-btn:not(.disabled):not(.current):hover { color: var(--gold-light); }
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

    {{-- ── HERO SECTION ── --}}
    <div class="categories-hero anim-1">
        <div class="categories-hero-content">
            <p class="text-emerald-300/70 text-[10px] font-bold tracking-[0.2em] uppercase mb-2"
               style="font-family:'DM Mono',monospace;">
                {{ now()->format('F Y') }} · Organisation
            </p>
            <h1 class="categories-hero-title mb-3">Document<br><span>Categories</span></h1>
            <div class="flex flex-wrap gap-2">
                <span class="categories-hero-pill">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    {{ $categories->total() }} Total Categories
                </span>
                <span class="categories-hero-pill">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Organise your documents
                </span>
            </div>
        </div>
    </div>

    {{-- ── Flash Messages ── --}}
    @if(session('success'))
    <div class="flash-success anim-2">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
        <p class="text-sm font-medium">{{ session('success') }}</p>
    </div>
    @endif
    @if(session('error'))
    <div class="flash-error anim-2">
        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>
        <p class="text-sm font-medium">{{ session('error') }}</p>
    </div>
    @endif

    {{-- ── Categories Table Card ── --}}
    <div class="categories-card anim-3">
        <div class="categories-header">
            <h2 class="categories-title">📁 Document Categories Directory</h2>
            @if(Auth::user()->role->level === 1 || Auth::user()->hasPermission('categories.create'))
                <a href="{{ route('admin.document-categories.create') }}" class="btn-emerald">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    New Category
                </a>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="categories-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th class="right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $cat)
                    <tr>
                        <td class="font-semibold text-text">{{ $cat->name }}</td>
                        <td class="text-text-3">{{ $cat->description ?? '—' }}</td>
                        <td>
                            @if($cat->is_active)
                                <span class="status-badge status-badge-active">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse inline-block mr-1"></span>
                                    Active
                                </span>
                            @else
                                <span class="status-badge status-badge-inactive">
                                    <span class="w-1.5 h-1.5 rounded-full bg-gray-400 inline-block mr-1"></span>
                                    Inactive
                                </span>
                            @endif
                        </td>
                        <td class="right">
                            <div class="flex items-center justify-end gap-1.5">
                                @if(Auth::user()->role->level === 1 || Auth::user()->hasPermission('categories.edit'))
                                    <a href="{{ route('admin.document-categories.edit', $cat) }}"
                                       class="action-btn action-btn-edit"
                                       title="Edit Category">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Edit
                                    </a>
                                @endif
                                @if(Auth::user()->role->level === 1 || Auth::user()->hasPermission('categories.delete'))
                                    <form method="POST" action="{{ route('admin.document-categories.destroy', $cat) }}" 
                                          onsubmit="return confirm('⚠️ Delete category &quot;{{ addslashes($cat->name) }}&quot;?\n\nDocuments in this category will not be deleted but may become uncategorised.')" 
                                          class="inline">
                                        @csrf 
                                        @method('DELETE')
                                        <button type="submit"
                                                class="action-btn action-btn-delete"
                                                title="Delete Category">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
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
                            <td colspan="4">
                                <div class="empty-state">
                                    <div class="empty-state-icon">
                                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.25">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                        </svg>
                                    </div>
                                    <p class="text-text-2 font-semibold">No categories found</p>
                                    <p class="text-text-3 text-sm">Create your first category to organise documents</p>
                                    @if(Auth::user()->role->level === 1 || Auth::user()->hasPermission('categories.create'))
                                        <a href="{{ route('admin.document-categories.create') }}" class="text-emerald-600 hover:text-emerald-700 text-sm font-medium inline-flex items-center gap-1 mt-2">
                                            Create a category →
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($categories->hasPages())
        <div class="pag-wrap">
            <p class="pag-info">
                {{ $categories->firstItem() }}–{{ $categories->lastItem() }} of {{ $categories->total() }} categories
            </p>
            <div class="pag-btns">
                @if($categories->onFirstPage())
                    <span class="pag-btn disabled">← Prev</span>
                @else
                    <a href="{{ $categories->previousPageUrl() }}" class="pag-btn">← Prev</a>
                @endif
                @foreach($categories->getUrlRange(max(1, $categories->currentPage() - 2), min($categories->lastPage(), $categories->currentPage() + 2)) as $page => $url)
                    @if($page == $categories->currentPage())
                        <span class="pag-btn current">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}" class="pag-btn">{{ $page }}</a>
                    @endif
                @endforeach
                @if($categories->hasMorePages())
                    <a href="{{ $categories->nextPageUrl() }}" class="pag-btn">Next →</a>
                @else
                    <span class="pag-btn disabled">Next →</span>
                @endif
            </div>
        </div>
        @endif
    </div>

</div>
@endsection