@extends('layouts.app')

@section('title', 'Document Backups — VSULHS SSLG')
@section('page-title', 'Document Backup & Restore')

@push('styles')
<style>
/* ════════════════════════════════════════════════
   DOCUMENT BACKUPS — Emerald & Gold Luxury Theme
   Matching all other management views
════════════════════════════════════════════════ */

/* ── Hero Section ── */
.backups-hero {
    position: relative;
    overflow: hidden;
    border-radius: 1.25rem;
    padding: 1.75rem 2rem;
    isolation: isolate;
    background: linear-gradient(135deg, #064E3B 0%, #065F46 35%, #047857 60%, #0A3A28 100%);
}
.backups-hero::before {
    content: '';
    position: absolute; inset: 0;
    background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,1) 1px, transparent 0);
    background-size: 28px 28px;
    opacity: 0.04; z-index: 0;
}
.backups-hero::after {
    content: '';
    position: absolute;
    top: -60px; right: -60px;
    width: 280px; height: 280px;
    background: radial-gradient(circle, rgba(212,175,55,0.35), transparent 65%);
    filter: blur(48px); z-index: 0;
}
.backups-hero-content { position: relative; z-index: 1; }

.backups-hero-title {
    font-family: 'DM Serif Display', serif;
    font-size: clamp(1.5rem, 3.5vw, 2.2rem);
    color: #fff;
    letter-spacing: -0.02em;
    line-height: 1.1;
}
.backups-hero-title span {
    background: linear-gradient(90deg, #F0CC55, #D4AF37);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}
.backups-hero-pill {
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

/* ── Cards ── */
.backups-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 1.25rem;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
}
html.dark .backups-card { box-shadow: 0 4px 20px rgba(0,0,0,0.22); }

.backups-card-header {
    padding: 0.875rem 1.25rem;
    border-bottom: 1px solid var(--border);
}
.backups-card-header-emerald {
    background: linear-gradient(135deg, #064E3B 0%, #047857 60%, #065F46 100%);
}
.backups-card-header-amber {
    background: rgba(217,119,6,0.15);
    border-bottom-color: rgba(217,119,6,0.3);
}
html.dark .backups-card-header-amber {
    background: rgba(217,119,6,0.08);
}
.backups-card-title {
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.1em;
    text-transform: uppercase;
    font-family: 'DM Mono', monospace;
}
.backups-card-title-emerald { color: rgba(255,255,255,0.85); }
.backups-card-title-amber { color: #d97706; }
html.dark .backups-card-title-amber { color: #fbbf24; }

/* ── Form Elements ── */
.backups-label {
    display: block;
    font-size: 0.65rem;
    font-weight: 700;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    color: var(--text-3);
    margin-bottom: 0.35rem;
    font-family: 'DM Mono', monospace;
}
.backups-select {
    width: 100%;
    padding: 0.5rem 0.75rem;
    font-size: 0.8rem;
    background: var(--surface-2);
    border: 1.5px solid var(--border);
    border-radius: 0.75rem;
    color: var(--text);
    font-family: 'Outfit', sans-serif;
    cursor: pointer;
    transition: all 0.2s ease;
}
.backups-select:focus {
    outline: none;
    border-color: var(--gold);
    box-shadow: 0 0 0 3px rgba(212,175,55,0.12);
}
.backups-input {
    width: 100%;
    padding: 0.5rem 0.75rem;
    font-size: 0.8rem;
    background: var(--surface-2);
    border: 1.5px solid var(--border);
    border-radius: 0.75rem;
    color: var(--text);
    font-family: 'Outfit', sans-serif;
    transition: all 0.2s ease;
}
.backups-input:focus {
    outline: none;
    border-color: var(--gold);
    box-shadow: 0 0 0 3px rgba(212,175,55,0.12);
    background: var(--surface);
}
.backups-file-input {
    width: 100%;
    font-size: 0.7rem;
    color: var(--text-3);
    padding: 0.375rem;
    background: var(--surface-2);
    border: 1.5px solid var(--border);
    border-radius: 0.75rem;
}
.backups-file-input::-webkit-file-upload-button {
    margin-right: 0.75rem;
    padding: 0.375rem 0.75rem;
    border-radius: 0.5rem;
    border: none;
    font-size: 0.65rem;
    font-weight: 600;
    background: rgba(5,150,105,0.15);
    color: #047857;
    cursor: pointer;
}
html.dark .backups-file-input::-webkit-file-upload-button {
    background: rgba(16,185,129,0.2);
    color: #34d399;
}

/* ── Buttons ── */
.btn-emerald {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    width: 100%;
    padding: 0.6rem 1rem;
    font-size: 0.8rem;
    font-weight: 700;
    background: linear-gradient(135deg, var(--emerald), var(--emerald-dark));
    color: #fff;
    border: none;
    border-radius: 0.75rem;
    cursor: pointer;
    transition: all 0.2s ease;
    font-family: 'Outfit', sans-serif;
}
.btn-emerald:hover:not(:disabled) {
    background: linear-gradient(135deg, var(--gold), var(--gold-dark));
    color: #0f172a;
    transform: translateY(-1px);
}
.btn-emerald:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
.btn-amber {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    width: 100%;
    padding: 0.6rem 1rem;
    font-size: 0.8rem;
    font-weight: 700;
    background: linear-gradient(135deg, #d97706, #b45309);
    color: #fff;
    border: none;
    border-radius: 0.75rem;
    cursor: pointer;
    transition: all 0.2s ease;
    font-family: 'Outfit', sans-serif;
}
.btn-amber:hover {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    transform: translateY(-1px);
}
.btn-blue {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.4rem 0.875rem;
    font-size: 0.7rem;
    font-weight: 700;
    background: rgba(59,130,246,0.1);
    color: #2563eb;
    border: 1px solid rgba(59,130,246,0.25);
    border-radius: 0.5rem;
    cursor: pointer;
    transition: all 0.15s ease;
    text-decoration: none;
}
.btn-blue:hover {
    background: rgba(59,130,246,0.2);
    border-color: rgba(59,130,246,0.4);
}
.btn-red {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.4rem 0.875rem;
    font-size: 0.7rem;
    font-weight: 700;
    background: rgba(220,38,38,0.1);
    color: #dc2626;
    border: 1px solid rgba(220,38,38,0.25);
    border-radius: 0.5rem;
    cursor: pointer;
    transition: all 0.15s ease;
}
.btn-red:hover {
    background: rgba(220,38,38,0.2);
    border-color: rgba(220,38,38,0.4);
}

/* ── Checkbox List ── */
.backups-checkbox-list {
    max-height: 12rem;
    overflow-y: auto;
    border: 1px solid var(--border);
    border-radius: 0.75rem;
    background: var(--surface-2);
}
.backups-checkbox-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.5rem 0.75rem;
    cursor: pointer;
    transition: background 0.15s ease;
    border-bottom: 1px solid var(--border);
}
.backups-checkbox-item:last-child {
    border-bottom: none;
}
.backups-checkbox-item:hover {
    background: rgba(212,175,55,0.05);
}
.backups-checkbox-item input[type="checkbox"] {
    width: 1rem;
    height: 1rem;
    border-radius: 0.25rem;
    border: 1.5px solid var(--border);
    accent-color: var(--emerald);
    cursor: pointer;
}
.backups-checkbox-label {
    flex: 1;
    font-size: 0.75rem;
    font-weight: 500;
    color: var(--text-2);
    cursor: pointer;
}
.backups-checkbox-count {
    font-size: 0.65rem;
    padding: 0.125rem 0.5rem;
    background: var(--surface-3);
    border-radius: 9999px;
    color: var(--text-3);
    font-family: 'DM Mono', monospace;
}

/* ── Stat Grid ── */
.backups-stat-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 0.75rem;
    margin-top: 0.75rem;
}
.backups-stat-item {
    background: var(--surface-2);
    border-radius: 0.75rem;
    padding: 0.5rem 0.75rem;
}
.backups-stat-label {
    font-size: 0.6rem;
    font-weight: 700;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    color: var(--text-3);
    margin-bottom: 0.25rem;
    font-family: 'DM Mono', monospace;
}
.backups-stat-value {
    font-size: 0.85rem;
    font-weight: 700;
    color: var(--text);
    font-family: 'DM Mono', monospace;
}

/* ── Filter Chips ── */
.filter-chip {
    padding: 0.25rem 0.75rem;
    font-size: 0.7rem;
    border-radius: 9999px;
    border: 1.5px solid var(--border);
    background: transparent;
    color: var(--text-3);
    cursor: pointer;
    transition: all 0.15s ease;
}
.filter-chip:hover {
    border-color: rgba(212,175,55,0.4);
    color: var(--gold-dark);
}
.filter-chip.active {
    background: linear-gradient(135deg, var(--emerald), var(--emerald-dark));
    border-color: transparent;
    color: #fff;
}
.sort-btn {
    padding: 0.25rem 0.65rem;
    font-size: 0.65rem;
    border-radius: 9999px;
    border: 1.5px solid var(--border);
    background: transparent;
    color: var(--text-3);
    cursor: pointer;
    transition: all 0.15s ease;
}
.sort-btn:hover {
    border-color: rgba(212,175,55,0.4);
    color: var(--gold-dark);
}
.sort-btn.active {
    background: linear-gradient(135deg, var(--emerald), var(--emerald-dark));
    border-color: transparent;
    color: #fff;
}

/* ── Backup Row ── */
.backup-row {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid var(--border);
    transition: background 0.15s ease;
}
.backup-row:last-child {
    border-bottom: none;
}
.backup-row:hover {
    background: rgba(212,175,55,0.025);
    box-shadow: inset 3px 0 0 var(--gold);
}
.backup-icon {
    width: 2.5rem;
    height: 2.5rem;
    background: rgba(5,150,105,0.1);
    border-radius: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
}
.backup-filename {
    font-size: 0.85rem;
    font-weight: 600;
    color: var(--text);
    font-family: 'DM Mono', monospace;
    margin-bottom: 0.25rem;
}
.backup-meta {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 0.5rem;
    margin-top: 0.375rem;
}
.backup-meta-date {
    font-size: 0.65rem;
    color: var(--text-3);
    font-family: 'DM Mono', monospace;
}
.backup-meta-size {
    font-size: 0.65rem;
    padding: 0.125rem 0.5rem;
    background: rgba(5,150,105,0.1);
    color: #047857;
    border-radius: 9999px;
    font-weight: 600;
}
html.dark .backup-meta-size {
    background: rgba(16,185,129,0.15);
    color: #6ee7b7;
}
.backup-badge {
    font-size: 0.6rem;
    padding: 0.125rem 0.5rem;
    border-radius: 9999px;
    font-weight: 600;
}
.backup-badge-all {
    background: var(--surface-3);
    color: var(--text-3);
}
.backup-badge-category {
    background: rgba(20,184,166,0.1);
    color: #0d9488;
    border: 1px solid rgba(20,184,166,0.2);
}
.backup-badge-filetype {
    background: rgba(139,92,246,0.1);
    color: #7c3aed;
    border: 1px solid rgba(139,92,246,0.2);
}
.backup-badge-financial {
    background: rgba(245,158,11,0.1);
    color: #b45309;
    border: 1px solid rgba(245,158,11,0.2);
}
html.dark .backup-badge-financial {
    background: rgba(245,158,11,0.15);
    color: #fbbf24;
    border-color: rgba(245,158,11,0.25);
}

/* ── Progress Loader ── */
.backups-loader {
    width: 1.25rem;
    height: 1.25rem;
    border: 2px solid #10b981;
    border-top-color: transparent;
    border-radius: 9999px;
    animation: spin 0.8s linear infinite;
}
@keyframes spin {
    to { transform: rotate(360deg); }
}

/* ── Info Box ── */
.backups-info {
    margin-top: 1rem;
    background: rgba(59,130,246,0.05);
    border: 1px solid rgba(59,130,246,0.15);
    border-radius: 1rem;
    padding: 1rem;
}
html.dark .backups-info {
    background: rgba(59,130,246,0.08);
    border-color: rgba(59,130,246,0.2);
}
.backups-info-title {
    font-size: 0.7rem;
    font-weight: 700;
    color: #2563eb;
    margin-bottom: 0.5rem;
}
html.dark .backups-info-title { color: #60a5fa; }
.backups-info-list {
    font-size: 0.65rem;
    color: #1e40af;
    list-style: none;
    padding-left: 0;
}
html.dark .backups-info-list { color: #93c5fd; }
.backups-info-list li {
    margin-bottom: 0.25rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
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
    <div class="backups-hero anim-1">
        <div class="backups-hero-content">
            <p class="text-emerald-300/70 text-[10px] font-bold tracking-[0.2em] uppercase mb-2"
               style="font-family:'DM Mono',monospace;">
                {{ now()->format('F Y') }} · Data Protection
            </p>
            <h1 class="backups-hero-title mb-3">Document<br><span>Backup & Restore</span></h1>
            <div class="flex flex-wrap gap-2">
                <span class="backups-hero-pill">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Create & Download Backups
                </span>
                <span class="backups-hero-pill">
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Restore from ZIP
                </span>
            </div>
        </div>
    </div>

    {{-- ── Flash Messages ── --}}
    @if(session('success'))
    <div class="flash-success anim-2">
        <span class="text-lg">✅</span>
        <p class="text-sm font-medium">{{ session('success') }}</p>
    </div>
    @endif
    @if(session('error'))
    <div class="flash-error anim-2">
        <span class="text-lg">❌</span>
        <p class="text-sm font-medium">{{ session('error') }}</p>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ════════════════════════════════════════════════════════════════════════
             LEFT COLUMN — Actions
        ════════════════════════════════════════════════════════════════════════ --}}
        <div class="lg:col-span-1 flex flex-col gap-6">

            {{-- ── Create Backup Card ── --}}
            <div class="backups-card anim-2">
                <div class="backups-card-header backups-card-header-emerald">
                    <h2 class="backups-card-title backups-card-title-emerald flex items-center gap-2">
                        <span>💾</span> Create New Backup
                    </h2>
                </div>

                <div class="p-5">
                    <p class="text-xs text-text-3 mb-4">
                        Generates a ZIP archive containing document metadata, categories,
                        version history, and physical files.
                    </p>

                    <form id="backup-form"
                          method="POST"
                          action="{{ route('admin.document-backups.create') }}">
                        @csrf

                        {{-- Category multi-select --}}
                        <div class="mb-4">
                            <div class="flex items-center justify-between mb-1">
                                <label class="backups-label">Document Categories</label>
                                <span id="cat-count" class="text-xs text-text-3">0 selected</span>
                            </div>

                            <div class="flex flex-wrap gap-1.5 mb-2">
                                <button type="button" onclick="catSelectAll()"
                                        class="filter-chip">Select all</button>
                                <button type="button" onclick="catClearAll()"
                                        class="filter-chip">Clear</button>
                            </div>

                            <div class="backups-checkbox-list">
                                @foreach($categories as $cat)
                                <label class="backups-checkbox-item">
                                    <input type="checkbox"
                                           name="category_ids[]"
                                           value="{{ $cat->id }}"
                                           class="cat-cb"
                                           onchange="syncCatUI()">
                                    <span class="backups-checkbox-label">{{ $cat->name }}</span>
                                    <span class="backups-checkbox-count">{{ $cat->documents_count }}</span>
                                </label>
                                @endforeach
                            </div>

                            <div id="cat-preview" class="mt-2 flex flex-wrap gap-1 min-h-[20px]">
                                <span class="text-xs text-text-3 italic">None selected — backs up all categories</span>
                            </div>
                        </div>

                        {{-- File-type filter --}}
                        <div class="mb-4">
                            <label class="backups-label">File Type</label>
                            <select name="file_type" class="backups-select">
                                @foreach($fileTypeGroups as $key)
                                    <option value="{{ $key }}">
                                        {{ Str::title($key === 'all' ? 'All file types' : $key) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <button type="submit" id="backup-btn" class="btn-emerald">
                            <span id="backup-icon">💾</span>
                            <span id="backup-text">Create Backup Now</span>
                        </button>
                    </form>

                    <div id="backup-progress" class="hidden mt-4">
                        <div class="flex items-center gap-2">
                            <div class="backups-loader"></div>
                            <span id="backup-status" class="text-sm text-text-2">Creating backup…</span>
                        </div>
                        <div id="backup-time" class="text-xs text-text-3 mt-1"></div>
                    </div>

                    <p class="text-xs text-text-3 text-center mt-3">⚠️ Large document libraries may take a moment.</p>
                </div>
            </div>

            {{-- ── Restore from ZIP Card ── --}}
            <div class="backups-card anim-3">
                <div class="backups-card-header backups-card-header-amber">
                    <h2 class="backups-card-title backups-card-title-amber flex items-center gap-2">
                        <span>📤</span> Restore from Backup
                    </h2>
                </div>

                <div class="p-5">
                    <div class="mb-4 p-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg">
                        <p class="text-xs font-semibold text-amber-800 dark:text-amber-300">⚠️ Warning</p>
                        <p class="text-xs text-amber-700 dark:text-amber-400 mt-1">
                            Restoring will re-import documents and files. Existing records
                            are skipped or overwritten based on your chosen mode.
                        </p>
                    </div>

                    <form method="POST"
                          action="{{ route('admin.document-backups.restore') }}"
                          enctype="multipart/form-data"
                          id="restore-form">
                        @csrf

                        {{-- ZIP file upload --}}
                        <div class="mb-3">
                            <label class="backups-label">Backup ZIP File</label>
                            <input type="file"
                                   name="backup_file"
                                   accept=".zip"
                                   required
                                   class="backups-file-input">
                        </div>

                        {{-- Restore mode — maps to controller: mode (merge|skip) --}}
                        <div class="mb-3">
                            <label class="backups-label">Restore Mode</label>
                            <select name="mode" class="backups-select">
                                <option value="skip">Skip existing (safe — keep current data)</option>
                                <option value="merge">Merge / overwrite existing</option>
                            </select>
                        </div>

                        {{-- Financial restore toggle — maps to controller: restore_financials --}}
                        <div class="mb-3">
                            <label class="backups-label">Financial Records</label>
                            <label class="flex items-center gap-2 cursor-pointer group">
                                <input type="checkbox"
                                       name="restore_financials"
                                       value="1"
                                       checked
                                       id="restore-financials-cb"
                                       onchange="toggleFinancialOptions()"
                                       class="rounded border-border accent-emerald-600">
                                <span class="text-xs text-text-3 group-hover:text-text-2 transition">
                                    Restore financial transactions from this backup
                                </span>
                            </label>
                        </div>

                        {{-- Financial restore status — maps to controller: financial_restore_status (as_is|force_pending) --}}
                        <div id="financial-options" class="mb-3">
                            <label class="backups-label">Financial Status on Restore</label>
                            <select name="financial_restore_status" class="backups-select">
                                <option value="as_is">Keep original status (approved/paid/etc.)</option>
                                <option value="force_pending">Reset all to pending (re-audit required)</option>
                            </select>
                            <p class="text-xs text-text-3 mt-1.5 leading-relaxed">
                                "Keep original" will also regenerate approval document copies for approved/paid records.
                            </p>
                        </div>

                        {{-- Force restore override — maps to controller: force_restore --}}
                        <label class="flex items-center gap-2 mb-4 cursor-pointer group">
                            <input type="checkbox"
                                   name="force_restore"
                                   value="1"
                                   class="rounded border-border accent-amber-500">
                            <span class="text-xs text-text-3 group-hover:text-text-2 transition">
                                Force restore (override duplicate guard)
                            </span>
                        </label>

                        <button type="button" onclick="confirmRestore()" class="btn-amber">
                            📤 Restore Backup
                        </button>

                        <div id="restore-confirm" class="hidden mt-3 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                            <p class="text-xs font-semibold text-red-800 dark:text-red-300 mb-1">Confirm restore?</p>
                            <p class="text-xs text-red-700 dark:text-red-400 mb-3">
                                This will re-import all documents and files from the ZIP.
                                Make sure you've selected the correct restore mode.
                            </p>
                            <div class="flex gap-2">
                                <button type="submit" class="flex-1 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition">Yes, restore</button>
                                <button type="button" onclick="cancelRestore()" class="flex-1 border border-gray-200 dark:border-gray-600 text-text-3 text-xs font-semibold px-3 py-1.5 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">Cancel</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>

        {{-- ════════════════════════════════════════════════════════════════════════
             RIGHT COLUMN — Backup list
        ════════════════════════════════════════════════════════════════════════ --}}
        <div class="lg:col-span-2">
            <div class="backups-card anim-4">
                <div class="backups-card-header border-b border-border">
                    <div class="flex items-center justify-between mb-3">
                        <h2 class="backups-card-title text-text-2 flex items-center gap-2">
                            <span>🗂️</span> Saved Backups
                            <span class="ml-1 bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300 text-xs font-bold px-2 py-0.5 rounded-full">
                                {{ $stats['count'] }}
                            </span>
                        </h2>
                        <p class="text-xs text-text-3">Stored on server · private disk</p>
                    </div>

                    @if($stats['count'] > 0)
                    <div class="backups-stat-grid">
                        <div class="backups-stat-item">
                            <p class="backups-stat-label">Total backups</p>
                            <p class="backups-stat-value">{{ $stats['count'] }}</p>
                        </div>
                        <div class="backups-stat-item">
                            <p class="backups-stat-label">Storage used</p>
                            <p class="backups-stat-value">{{ $stats['total_size'] }}</p>
                        </div>
                        <div class="backups-stat-item">
                            <p class="backups-stat-label">Latest</p>
                            <p class="backups-stat-value truncate">{{ $stats['latest'] }}</p>
                        </div>
                        <div class="backups-stat-item">
                            <p class="backups-stat-label">Oldest</p>
                            <p class="backups-stat-value truncate">{{ $stats['oldest'] }}</p>
                        </div>
                    </div>
                    @endif
                </div>

                @if($stats['count'] > 0)
                {{--
                    Filter chips: array_column() works because $backups is a plain PHP array of associative arrays,
                    not an Eloquent collection — the controller passes it via compact() after building it manually.
                    array_unique(array_column(...)) is therefore safe here.
                --}}
                @php
                    $uniqueFiletypes  = array_unique(array_column($backups, 'filetype_slug'));
                    $uniqueCategories = array_unique(array_column($backups, 'category_slug'));
                @endphp
                <div class="px-5 py-3 border-b border-border flex items-center gap-3 flex-wrap">
                    <div class="flex flex-wrap gap-1.5 flex-1" id="filter-chips">
                        <button onclick="setFilter('all')" data-filter="all" class="filter-chip active">All</button>

                        @foreach($uniqueFiletypes as $typeSlug)
                            @if($typeSlug !== 'all')
                            <button onclick="setFilter('type:{{ $typeSlug }}')"
                                    data-filter="type:{{ $typeSlug }}"
                                    class="filter-chip">{{ Str::title($typeSlug) }}</button>
                            @endif
                        @endforeach

                        @foreach($uniqueCategories as $catSlug)
                            @if($catSlug !== 'all')
                            <button onclick="setFilter('cat:{{ $catSlug }}')"
                                    data-filter="cat:{{ $catSlug }}"
                                    class="filter-chip">{{ Str::title(str_replace(['__', '_', '--'], [', ', ' ', ', '], $catSlug)) }}</button>
                            @endif
                        @endforeach
                    </div>

                    <div class="flex items-center gap-1 flex-shrink-0">
                        <button onclick="setSort('newest')" data-sort="newest" class="sort-btn active">Newest</button>
                        <button onclick="setSort('oldest')" data-sort="oldest" class="sort-btn">Oldest</button>
                        <button onclick="setSort('largest')" data-sort="largest" class="sort-btn">Largest</button>
                    </div>
                </div>
                @endif

                @if(count($backups) === 0)
                    <div class="px-5 py-16 text-center">
                        <div class="text-5xl mb-4">📭</div>
                        <p class="text-text-2 text-sm font-medium">No backups yet</p>
                        <p class="text-text-3 text-xs mt-1">Create your first backup using the panel on the left.</p>
                    </div>
                @else
                    <div id="backup-list" class="divide-y divide-border">
                        @foreach($backups as $backup)
                        {{--
                            $backup keys (from controller index()):
                              filename, path, size, size_bytes, category_slug,
                              filetype_slug, has_financial_data, created_at, created_ts
                        --}}
                        <div class="backup-row"
                             data-category="{{ $backup['category_slug'] }}"
                             data-filetype="{{ $backup['filetype_slug'] }}"
                             data-ts="{{ $backup['created_ts'] }}"
                             data-size="{{ $backup['size_bytes'] }}">

                            <div class="flex items-center gap-4">
                                <div class="backup-icon">🗜️</div>

                                <div class="flex-1 min-w-0">
                                    <p class="backup-filename truncate">{{ $backup['filename'] }}</p>

                                    <div class="backup-meta">
                                        <span class="backup-meta-date">{{ $backup['created_at'] }}</span>
                                        <span class="backup-meta-size">{{ $backup['size'] }}</span>

                                        {{-- Category badge --}}
                                        @if($backup['category_slug'] !== 'all')
                                            <span class="backup-badge backup-badge-category">
                                                {{ Str::title(str_replace(['__', '_', '--'], [', ', ' ', ', '], $backup['category_slug'])) }}
                                            </span>
                                        @else
                                            <span class="backup-badge backup-badge-all">All categories</span>
                                        @endif

                                        {{-- File type badge --}}
                                        @if($backup['filetype_slug'] !== 'all')
                                            <span class="backup-badge backup-badge-filetype">{{ Str::title($backup['filetype_slug']) }} only</span>
                                        @endif

                                        {{-- Financial data badge — driven by has_financial_data from controller --}}
                                        @if($backup['has_financial_data'])
                                            <span class="backup-badge backup-badge-financial">💰 Financials</span>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex items-center gap-2 flex-shrink-0">
                                    <a href="{{ route('admin.document-backups.download', $backup['filename']) }}"
                                       class="btn-blue">⬇️ Download</a>
                                    <button type="button"
                                            onclick="showDeleteConfirm(this)"
                                            data-filename="{{ $backup['filename'] }}"
                                            data-action="{{ route('admin.document-backups.destroy', $backup['filename']) }}"
                                            class="btn-red">🗑️ Delete</button>
                                </div>
                            </div>

                            {{-- Inline delete confirm panel --}}
                            <div class="delete-confirm hidden mt-3 p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                                <p class="text-xs font-semibold text-red-800 dark:text-red-300 mb-0.5">Delete this backup?</p>
                                <p class="delete-filename text-xs text-red-600 dark:text-red-400 font-mono mb-2 break-all"></p>
                                <p class="text-xs text-red-600 dark:text-red-400 mb-2">This cannot be undone and will permanently remove the file.</p>
                                <div class="flex gap-2">
                                    <form method="POST" class="delete-form flex-1" style="display:contents">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="flex-1 bg-red-600 hover:bg-red-700 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition">
                                            Yes, delete
                                        </button>
                                    </form>
                                    <button type="button"
                                            onclick="hideDeleteConfirm(this)"
                                            class="flex-1 border border-gray-200 dark:border-gray-600 text-text-3 text-xs font-semibold px-3 py-1.5 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                        Cancel
                                    </button>
                                </div>
                            </div>

                        </div>
                        @endforeach
                    </div>

                    <div id="no-results" class="hidden px-5 py-10 text-center">
                        <div class="text-4xl mb-3">🔍</div>
                        <p class="text-text-2 text-sm">No backups match this filter.</p>
                    </div>
                @endif
            </div>

            {{-- Info Box --}}
            <div class="backups-info">
                <p class="backups-info-title">ℹ️ What's included in each backup?</p>
                <ul class="backups-info-list">
                    <li>✅ All document categories</li>
                    <li>✅ All document metadata (title, description, category, owner, timestamps, trash state)</li>
                    <li>✅ Full version history for every document</li>
                    <li>✅ All physical files (PDFs, images, docs, etc.)</li>
                    <li>✅ Soft-deleted documents (trash)</li>
                    <li>✅ Financial transactions (income, expenses, receivables)</li>
                </ul>
            </div>
        </div>

    </div>

</div>

<script>
// ─── Category multi-select ────────────────────────────────────────────────────
function syncCatUI() {
    const boxes   = [...document.querySelectorAll('.cat-cb:checked')];
    const counter = document.getElementById('cat-count');
    const preview = document.getElementById('cat-preview');

    counter.textContent = boxes.length ? `${boxes.length} selected` : '0 selected';

    if (!boxes.length) {
        preview.innerHTML = `<span class="text-xs text-text-3 italic">None selected — backs up all categories</span>`;
        return;
    }

    preview.innerHTML = boxes.map(cb => {
        const name = cb.closest('.backups-checkbox-item').querySelector('.backups-checkbox-label').textContent.trim();
        return `<span class="text-xs bg-emerald-100 dark:bg-emerald-900/40 text-emerald-800 dark:text-emerald-300 px-2 py-0.5 rounded-full font-medium">${name}</span>`;
    }).join('');
}

function catSelectAll() {
    document.querySelectorAll('.cat-cb').forEach(cb => cb.checked = true);
    syncCatUI();
}
function catClearAll() {
    document.querySelectorAll('.cat-cb').forEach(cb => cb.checked = false);
    syncCatUI();
}

// ─── Financial options visibility ─────────────────────────────────────────────
// Toggles the financial_restore_status select based on the restore_financials checkbox.
function toggleFinancialOptions() {
    const cb      = document.getElementById('restore-financials-cb');
    const options = document.getElementById('financial-options');
    options.style.display = cb.checked ? '' : 'none';
}

// ─── Restore inline confirm ───────────────────────────────────────────────────
function confirmRestore() {
    document.getElementById('restore-confirm').classList.remove('hidden');
}
function cancelRestore() {
    document.getElementById('restore-confirm').classList.add('hidden');
}

// ─── Delete inline confirm ────────────────────────────────────────────────────
function showDeleteConfirm(btn) {
    // Collapse any other open confirm panels first
    document.querySelectorAll('.delete-confirm').forEach(el => el.classList.add('hidden'));

    const row     = btn.closest('.backup-row');
    const confirm = row.querySelector('.delete-confirm');
    const form    = confirm.querySelector('.delete-form');
    const label   = confirm.querySelector('.delete-filename');

    form.action       = btn.dataset.action;
    label.textContent = btn.dataset.filename;

    confirm.classList.remove('hidden');
}
function hideDeleteConfirm(btn) {
    btn.closest('.delete-confirm').classList.add('hidden');
}

// ─── Filter + sort ────────────────────────────────────────────────────────────
let activeFilter = 'all';
let activeSort   = 'newest';

function setFilter(value) {
    activeFilter = value;
    document.querySelectorAll('.filter-chip').forEach(btn => {
        btn.classList.toggle('active', btn.dataset.filter === value);
    });
    applyFilterSort();
}

function setSort(value) {
    activeSort = value;
    document.querySelectorAll('.sort-btn').forEach(btn => {
        btn.classList.toggle('active', btn.dataset.sort === value);
    });
    applyFilterSort();
}

function applyFilterSort() {
    const list  = document.getElementById('backup-list');
    const noRes = document.getElementById('no-results');
    if (!list) return;

    let rows = [...list.querySelectorAll('.backup-row')];

    // Apply visibility filter
    rows.forEach(row => {
        const cat  = row.dataset.category ?? '';
        const type = row.dataset.filetype ?? '';
        let visible = true;

        if (activeFilter !== 'all') {
            if (activeFilter.startsWith('type:')) {
                visible = type === activeFilter.slice(5);
            } else if (activeFilter.startsWith('cat:')) {
                visible = cat === activeFilter.slice(4);
            }
        }
        row.style.display = visible ? '' : 'none';
    });

    // Sort visible rows
    const visible = rows.filter(r => r.style.display !== 'none');
    visible.sort((a, b) => {
        if (activeSort === 'newest')  return Number(b.dataset.ts)   - Number(a.dataset.ts);
        if (activeSort === 'oldest')  return Number(a.dataset.ts)   - Number(b.dataset.ts);
        if (activeSort === 'largest') return Number(b.dataset.size) - Number(a.dataset.size);
        return 0;
    });
    visible.forEach(row => list.appendChild(row));

    noRes?.classList.toggle('hidden', visible.length > 0);
}

// ─── Backup creation AJAX ─────────────────────────────────────────────────────
document.getElementById('backup-form').addEventListener('submit', async function (e) {
    e.preventDefault();

    const btn      = document.getElementById('backup-btn');
    const icon     = document.getElementById('backup-icon');
    const text     = document.getElementById('backup-text');
    const progress = document.getElementById('backup-progress');
    const status   = document.getElementById('backup-status');
    const timeEl   = document.getElementById('backup-time');

    btn.disabled       = true;
    icon.textContent   = '⏳';
    text.textContent   = 'Creating backup…';
    progress.classList.remove('hidden');
    status.textContent = 'Creating backup…';
    timeEl.textContent = '';

    const startTime = Date.now();

    try {
        const res    = await fetch(this.action, {
            method:  'POST',
            body:    new FormData(this),
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        });
        const result = await res.json();
        const elapsed = ((Date.now() - startTime) / 1000).toFixed(1);

        if (res.ok && result.success) {
            // Controller returns: success, filename, elapsed, financial_count, message
            status.innerHTML   = `✅ ${result.message ?? 'Backup created successfully!'}`;
            timeEl.textContent = `Completed in ${elapsed}s`;
            setTimeout(() => location.reload(), 1800);
        } else {
            status.innerHTML   = '❌ Error: ' + (result.error ?? 'Unknown error');
            timeEl.textContent = `Failed after ${elapsed}s`;
            btn.disabled       = false;
            icon.textContent   = '💾';
            text.textContent   = 'Create Backup Now';
        }
    } catch {
        status.innerHTML   = '❌ Network error — please try again.';
        timeEl.textContent = '';
        btn.disabled       = false;
        icon.textContent   = '💾';
        text.textContent   = 'Create Backup Now';
    }
});
</script>
@endsection