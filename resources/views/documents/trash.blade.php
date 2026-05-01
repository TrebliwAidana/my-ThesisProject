@extends('layouts.app')

@section('title', 'Document Trash — VSULHS SSLG')
@section('page-title', 'Document Trash')

@push('styles')
<style>
/* ════════════════════════════════════════════════
   DOCUMENT TRASH — Emerald & Gold Luxury Theme
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

/* ── Table Card ── */
.trash-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 1.25rem;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
}
html.dark .trash-card {
    box-shadow: 0 4px 20px rgba(0,0,0,0.22);
}

/* ── Table Styles ── */
.trash-table {
    width: 100%;
    min-width: 800px;
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
.trash-table th:last-child { text-align: right; }
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
.trash-table td:last-child { text-align: right; }

/* ── Category Badge ── */
.category-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.2rem 0.65rem;
    border-radius: 9999px;
    font-size: 0.65rem;
    font-weight: 700;
    font-family: 'DM Mono', monospace;
    background: rgba(5,150,105,0.1);
    color: #047857;
    border: 1px solid rgba(5,150,105,0.2);
}
html.dark .category-badge {
    background: rgba(16,185,129,0.15);
    color: #6ee7b7;
}

/* ── Action Buttons ── */
.action-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.35rem;
    padding: 0.3rem 0.7rem;
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
.action-restore {
    color: #059669;
    border-color: rgba(5,150,105,0.2);
    background: rgba(5,150,105,0.06);
}
.action-restore:hover {
    background: rgba(5,150,105,0.14);
    border-color: rgba(5,150,105,0.4);
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

/* ── File Icon Colors ── */
.file-icon-pdf { color: #f43f5e; }
.file-icon-doc { color: #3b82f6; }
.file-icon-xls { color: #059669; }
.file-icon-ppt { color: #ea580c; }
.file-icon-img { color: #7c3aed; }
.file-icon-zip { color: #ca8a04; }
.file-icon-default { color: #94a3b8; }

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
.empty-link {
    color: var(--emerald);
    font-size: 0.75rem;
    font-weight: 600;
    text-decoration: none;
    transition: color 0.15s ease;
}
.empty-link:hover {
    color: var(--gold-dark);
}
html.dark .empty-link:hover {
    color: var(--gold-light);
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
</style>
@endpush

@section('content')

<div class="space-y-5">
    
    {{-- Hero Section --}}
    <div class="trash-hero anim-1">
        <div class="trash-hero-content">
            <p class="text-emerald-300/70 text-[10px] font-bold tracking-[0.2em] uppercase mb-2"
               style="font-family:'DM Mono',monospace;">
                {{ now()->format('F Y') }} · Document Management
            </p>
            <h1 class="trash-hero-title mb-3">Document<br><span>Trash</span></h1>
            <div class="flex flex-wrap gap-2">
                <span class="trash-hero-pill">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    {{ $documents->total() }} Deleted Documents
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

    {{-- Trash Table Card --}}
    <div class="trash-card anim-2">
        <div class="overflow-x-auto">
            <table class="trash-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Deleted At</th>
                        <th>Size</th>
                        <th class="right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($documents as $doc)
                    @php
                        $version   = $doc->currentVersion;
                        $fileName  = $version?->file_name ?? '';
                        $ext       = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                        $fileIconClass = match(true) {
                            in_array($ext, ['pdf']) => 'file-icon-pdf',
                            in_array($ext, ['doc','docx']) => 'file-icon-doc',
                            in_array($ext, ['xls','xlsx']) => 'file-icon-xls',
                            in_array($ext, ['ppt','pptx']) => 'file-icon-ppt',
                            in_array($ext, ['jpg','jpeg','png','gif','webp']) => 'file-icon-img',
                            $ext === 'zip' => 'file-icon-zip',
                            default => 'file-icon-default',
                        };
                        $fileIcon = match(true) {
                            in_array($ext, ['pdf']) => '📄',
                            in_array($ext, ['doc','docx']) => '📝',
                            in_array($ext, ['xls','xlsx','ppt','pptx']) => '📊',
                            in_array($ext, ['jpg','jpeg','png','gif','webp']) => '🖼️',
                            $ext === 'zip' => '🗜️',
                            default => '📎',
                        };
                    @endphp
                    <tr>
                        {{-- Title with Icon --}}
                        <td>
                            <div class="flex items-center gap-2">
                                <span class="text-lg {{ $fileIconClass }} flex-shrink-0">{{ $fileIcon }}</span>
                                <div class="min-w-0">
                                    <span class="font-semibold text-text block truncate max-w-[220px]" title="{{ $doc->title }}">
                                        {{ $doc->title }}
                                    </span>
                                    @if($doc->description)
                                        <p class="text-xs text-text-3 truncate max-w-[220px]">{{ $doc->description }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- Category --}}
                        <td>
                            @if($doc->category)
                                <span class="category-badge">{{ $doc->category->name ?? $doc->category }}</span>
                            @else
                                <span class="text-text-3 text-xs">—</span>
                            @endif
                        </td>

                        {{-- Deleted At --}}
                        <td class="text-text-3 text-xs font-mono whitespace-nowrap">
                            {{ $doc->deleted_at->format('M d, Y H:i') }}
                        </td>

                        {{-- Size --}}
                        <td class="text-text-3 text-xs font-mono whitespace-nowrap">
                            {{ $doc->formatted_size }}
                        </td>

                        {{-- Actions --}}
                        <td class="right">
                            <div class="flex items-center justify-end gap-1.5">
                                <form method="POST" action="{{ route('documents.restore', $doc->id) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="action-btn action-restore" title="Restore Document">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                        </svg>
                                        Restore
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('documents.force-delete', $doc->id) }}" class="inline"
                                      onsubmit="return confirm('⚠️ Permanently delete &quot;{{ addslashes($doc->title) }}&quot;?\n\nThis action cannot be undone and will remove the document permanently.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="action-btn action-delete" title="Permanently Delete">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                        Delete Forever
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5">
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </div>
                                <h3 class="empty-title">Trash is empty</h3>
                                <p class="empty-desc">No soft-deleted documents found in the trash.</p>
                                <a href="{{ route('documents.index') }}" class="empty-link">
                                    Back to Documents →
                                </a>
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
                {{ $documents->firstItem() }}–{{ $documents->lastItem() }} of {{ $documents->total() }} deleted documents
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
</div>

@endsection