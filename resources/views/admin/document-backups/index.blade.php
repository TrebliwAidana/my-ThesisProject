@extends('layouts.app')

@section('title', 'Document Backups')
@section('page-title', 'Document Backup & Restore')

@section('content')

<style>
.loader {
    border-width: 2px;
    border-style: solid;
    border-color: #10b981 transparent transparent transparent;
    animation: spin 0.8s linear infinite;
    border-radius: 9999px;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* Category checkbox grid */
.cat-item input[type="checkbox"]:checked ~ .cat-label {
    color: #065f46;
    font-weight: 500;
}
</style>

{{-- ─── Header ─────────────────────────────────────────────────────────────── --}}
<div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-emerald-600 to-emerald-700
            dark:from-emerald-800 dark:to-emerald-900 p-6 mb-6">
    <div class="relative z-10">
        <h1 class="text-2xl font-bold text-white">Document Backup & Restore</h1>
        <p class="text-emerald-100 text-sm mt-1">Create, download, and restore document backups</p>
    </div>
    <div class="absolute top-0 right-0 -mt-8 -mr-8 w-64 h-64 bg-white/5 rounded-full blur-3xl"></div>
</div>

{{-- ─── Flash messages ──────────────────────────────────────────────────────── --}}
@if(session('success'))
    <div class="mb-4 p-4 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300
                rounded-xl border border-green-200 dark:border-green-700 flex items-start gap-3">
        <span class="text-lg">✅</span>
        <p class="text-sm font-medium">{{ session('success') }}</p>
    </div>
@endif
@if(session('error'))
    <div class="mb-4 p-4 bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300
                rounded-xl border border-red-200 dark:border-red-700 flex items-start gap-3">
        <span class="text-lg">❌</span>
        <p class="text-sm font-medium">{{ session('error') }}</p>
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- ════════════════════════════════════════════════════════════════════════
         LEFT COLUMN — Actions
    ════════════════════════════════════════════════════════════════════════ --}}
    <div class="lg:col-span-1 flex flex-col gap-6">

        {{-- ── Create Backup ──────────────────────────────────────────────── --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gold-200 dark:border-gold-800
                    shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gold-100 dark:border-gold-800
                        bg-emerald-50 dark:bg-emerald-900/20">
                <h2 class="text-sm font-semibold text-emerald-800 dark:text-emerald-300
                           flex items-center gap-2">
                    <span>💾</span> Create New Backup
                </h2>
            </div>

            <div class="p-5">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">
                    Generates a ZIP archive containing document metadata, categories,
                    version history, and physical files.
                </p>

                <form id="backup-form"
                      method="POST"
                      action="{{ route('admin.document-backups.create') }}">
                    @csrf

                    {{-- ── Category multi-select ──────────────────────────── --}}
                    <div class="mb-4">
                        <div class="flex items-center justify-between mb-1">
                            <label class="text-xs font-semibold text-gray-600 dark:text-gray-400">
                                Document Categories
                            </label>
                            <span id="cat-count"
                                  class="text-xs text-gray-400 dark:text-gray-500">
                                0 selected
                            </span>
                        </div>

                        {{-- Quick-select shortcuts --}}
                        <div class="flex flex-wrap gap-1.5 mb-2">
                            <button type="button" onclick="catSelectAll()"
                                    class="text-xs px-2.5 py-1 rounded-full border
                                           border-gray-200 dark:border-gray-600
                                           text-gray-500 dark:text-gray-400
                                           hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                Select all
                            </button>
                            <button type="button" onclick="catClearAll()"
                                    class="text-xs px-2.5 py-1 rounded-full border
                                           border-red-200 dark:border-red-800
                                           text-red-500 dark:text-red-400
                                           hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                                Clear
                            </button>
                        </div>

                        {{-- Scrollable checkbox list --}}
                        <div class="max-h-48 overflow-y-auto border
                                    border-gold-200 dark:border-gold-700 rounded-lg
                                    divide-y divide-gray-100 dark:divide-gray-700">
                            @foreach($categories as $cat)
                            <label class="cat-item flex items-center gap-3 px-3 py-2 cursor-pointer
                                          hover:bg-gray-50 dark:hover:bg-gray-700/40 transition
                                          has-[:checked]:bg-emerald-50
                                          has-[:checked]:dark:bg-emerald-900/20">
                                <input type="checkbox"
                                       name="category_ids[]"
                                       value="{{ $cat->id }}"
                                       class="cat-cb rounded text-emerald-600
                                              focus:ring-emerald-500
                                              border-gray-300 dark:border-gray-600
                                              dark:bg-gray-700"
                                       onchange="syncCatUI()">
                                <span class="cat-label text-xs text-gray-700
                                             dark:text-gray-300 flex-1">
                                    {{ $cat->name }}
                                </span>
                                <span class="text-xs bg-gray-100 dark:bg-gray-700
                                             text-gray-500 dark:text-gray-400
                                             px-1.5 py-0.5 rounded-full tabular-nums">
                                    {{ $cat->documents_count }}
                                </span>
                            </label>
                            @endforeach
                        </div>

                        {{-- Live selection preview --}}
                        <div id="cat-preview"
                             class="mt-2 flex flex-wrap gap-1 min-h-[20px]">
                            <span class="text-xs text-gray-400 dark:text-gray-500 italic">
                                None selected — backs up all categories
                            </span>
                        </div>
                    </div>

                    {{-- ── File-type filter ───────────────────────────────── --}}
                    <div class="mb-4">
                        <label class="block text-xs font-semibold text-gray-600
                                      dark:text-gray-400 mb-1">
                            File Type
                        </label>
                        <select name="file_type"
                                class="w-full px-3 py-2 text-sm border
                                       border-gold-200 dark:border-gold-700 rounded-lg
                                       dark:bg-gray-700 dark:text-white
                                       focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            @foreach($fileTypeGroups as $key)
                                <option value="{{ $key }}">
                                    {{ Str::title($key === 'all' ? 'All file types' : $key) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" id="backup-btn"
                            class="w-full bg-emerald-600 hover:bg-emerald-700
                                   text-white text-sm font-semibold px-4 py-2.5
                                   rounded-lg transition flex items-center justify-center gap-2">
                        <span id="backup-icon">💾</span>
                        <span id="backup-text">Create Backup Now</span>
                    </button>
                </form>

                {{-- Progress area (hidden initially) --}}
                <div id="backup-progress" class="hidden mt-4">
                    <div class="flex items-center gap-2">
                        <div class="loader w-5 h-5"></div>
                        <span id="backup-status"
                              class="text-sm text-gray-600 dark:text-gray-300">
                            Creating backup…
                        </span>
                    </div>
                    <div id="backup-time"
                         class="text-xs text-gray-400 mt-1"></div>
                </div>

                <p class="text-xs text-gray-400 dark:text-gray-500 mt-3 text-center">
                    ⚠️ Large document libraries may take a moment.
                </p>
            </div>
        </div>

        {{-- ── Restore from ZIP ───────────────────────────────────────────── --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gold-200 dark:border-gold-800
                    shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gold-100 dark:border-gold-800
                        bg-amber-50 dark:bg-amber-900/20">
                <h2 class="text-sm font-semibold text-amber-800 dark:text-amber-300
                           flex items-center gap-2">
                    <span>📤</span> Restore from Backup
                </h2>
            </div>

            <div class="p-5">
                <div class="mb-4 p-3 bg-amber-50 dark:bg-amber-900/20
                            border border-amber-200 dark:border-amber-700 rounded-lg">
                    <p class="text-xs text-amber-800 dark:text-amber-300 font-medium">⚠️ Warning</p>
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

                    <div class="mb-3">
                        <label class="block text-xs font-semibold text-gray-600
                                      dark:text-gray-400 mb-1">
                            Backup ZIP File
                        </label>
                        <input type="file"
                               name="backup_file"
                               accept=".zip"
                               required
                               class="w-full text-xs text-gray-600 dark:text-gray-300
                                      file:mr-3 file:py-1.5 file:px-3 file:rounded-lg
                                      file:border-0 file:text-xs file:font-semibold
                                      file:bg-emerald-100 file:text-emerald-700
                                      hover:file:bg-emerald-200
                                      dark:file:bg-emerald-900/40 dark:file:text-emerald-300
                                      border border-gold-200 dark:border-gold-700
                                      rounded-lg p-1.5 bg-white dark:bg-gray-700">
                    </div>

                    <div class="mb-3">
                        <label class="block text-xs font-semibold text-gray-600
                                      dark:text-gray-400 mb-1">
                            Restore Mode
                        </label>
                        <select name="mode"
                                class="w-full px-3 py-2 text-sm border
                                       border-gold-200 dark:border-gold-700 rounded-lg
                                       dark:bg-gray-700 dark:text-white
                                       focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="skip">Skip existing (safe — keep current data)</option>
                            <option value="merge">Merge / overwrite existing</option>
                        </select>
                    </div>

                    {{-- Force-restore — exposed in UI (was hidden/missing before) --}}
                    <label class="flex items-center gap-2 mb-4 cursor-pointer group">
                        <input type="checkbox"
                               name="force_restore"
                               value="1"
                               class="rounded text-amber-500 border-gray-300
                                      dark:border-gray-600 dark:bg-gray-700
                                      focus:ring-amber-400">
                        <span class="text-xs text-gray-600 dark:text-gray-400
                                     group-hover:text-gray-800 dark:group-hover:text-gray-200
                                     transition">
                            Force restore (override duplicate guard)
                        </span>
                    </label>

                    <button type="button"
                            onclick="confirmRestore()"
                            class="w-full bg-amber-600 hover:bg-amber-700
                                   text-white text-sm font-semibold px-4 py-2.5
                                   rounded-lg transition flex items-center justify-center gap-2">
                        📤 Restore Backup
                    </button>

                    {{-- Inline confirm (replaces window.confirm) --}}
                    <div id="restore-confirm"
                         class="hidden mt-3 p-3 bg-red-50 dark:bg-red-900/20
                                border border-red-200 dark:border-red-700 rounded-lg">
                        <p class="text-xs font-semibold text-red-800 dark:text-red-300 mb-1">
                            Confirm restore?
                        </p>
                        <p class="text-xs text-red-700 dark:text-red-400 mb-3">
                            This will re-import all documents and files from the ZIP.
                            Make sure you've selected the correct restore mode.
                        </p>
                        <div class="flex gap-2">
                            <button type="submit"
                                    class="flex-1 bg-red-600 hover:bg-red-700
                                           text-white text-xs font-semibold
                                           px-3 py-1.5 rounded-lg transition">
                                Yes, restore
                            </button>
                            <button type="button"
                                    onclick="cancelRestore()"
                                    class="flex-1 border border-gray-200 dark:border-gray-600
                                           text-gray-600 dark:text-gray-400 text-xs font-semibold
                                           px-3 py-1.5 rounded-lg hover:bg-gray-50
                                           dark:hover:bg-gray-700 transition">
                                Cancel
                            </button>
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
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gold-200 dark:border-gold-800
                    shadow-sm overflow-hidden">

            {{-- Header + summary stats --}}
            <div class="px-5 py-4 border-b border-gold-100 dark:border-gold-800">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-sm font-semibold text-gray-800 dark:text-white
                               flex items-center gap-2">
                        <span>🗂️</span> Saved Backups
                        <span class="ml-1 bg-emerald-100 dark:bg-emerald-900/40
                                     text-emerald-700 dark:text-emerald-300
                                     text-xs font-bold px-2 py-0.5 rounded-full">
                            {{ $stats['count'] }}
                        </span>
                    </h2>
                    <p class="text-xs text-gray-400 dark:text-gray-500">
                        Stored on server · private disk
                    </p>
                </div>

                {{-- Summary stat bar --}}
                @if($stats['count'] > 0)
                <div class="grid grid-cols-4 gap-2">
                    @foreach([
                        ['label' => 'Total backups',  'value' => $stats['count']],
                        ['label' => 'Storage used',   'value' => $stats['total_size']],
                        ['label' => 'Latest',         'value' => $stats['latest']],
                        ['label' => 'Oldest',         'value' => $stats['oldest']],
                    ] as $stat)
                    <div class="bg-gray-50 dark:bg-gray-700/40 rounded-lg px-3 py-2">
                        <p class="text-xs text-gray-400 dark:text-gray-500 mb-0.5">
                            {{ $stat['label'] }}
                        </p>
                        <p class="text-sm font-semibold text-gray-800 dark:text-white truncate">
                            {{ $stat['value'] }}
                        </p>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>

            {{-- Filter + sort bar --}}
            @if($stats['count'] > 0)
            <div class="px-5 py-3 border-b border-gray-100 dark:border-gray-700
                        flex items-center gap-3 flex-wrap">
                {{-- Filter chips --}}
                <div class="flex flex-wrap gap-1.5 flex-1" id="filter-chips">
                    <button onclick="setFilter('all')"
                            data-filter="all"
                            class="filter-chip active text-xs px-3 py-1 rounded-full border
                                   border-emerald-300 dark:border-emerald-700
                                   bg-emerald-50 dark:bg-emerald-900/30
                                   text-emerald-700 dark:text-emerald-300 transition">
                        All
                    </button>
                    @foreach(array_unique(array_column($backups, 'filetype_slug')) as $typeSlug)
                        @if($typeSlug !== 'all')
                        <button onclick="setFilter('type:{{ $typeSlug }}')"
                                data-filter="type:{{ $typeSlug }}"
                                class="filter-chip text-xs px-3 py-1 rounded-full border
                                       border-gray-200 dark:border-gray-600
                                       text-gray-500 dark:text-gray-400
                                       hover:border-emerald-300 hover:text-emerald-700
                                       dark:hover:text-emerald-300 transition">
                            {{ Str::title($typeSlug) }}
                        </button>
                        @endif
                    @endforeach
                    @foreach(array_unique(array_column($backups, 'category_slug')) as $catSlug)
                        @if($catSlug !== 'all')
                        <button onclick="setFilter('cat:{{ $catSlug }}')"
                                data-filter="cat:{{ $catSlug }}"
                                class="filter-chip text-xs px-3 py-1 rounded-full border
                                       border-gray-200 dark:border-gray-600
                                       text-gray-500 dark:text-gray-400
                                       hover:border-emerald-300 hover:text-emerald-700
                                       dark:hover:text-emerald-300 transition">
                            {{ Str::title(str_replace(['__', '_', '--'], [', ', ' ', ', '], $catSlug)) }}
                        </button>
                        @endif
                    @endforeach
                </div>

                {{-- Sort --}}
                <div class="flex items-center gap-1 flex-shrink-0">
                    <button onclick="setSort('newest')" data-sort="newest"
                            class="sort-btn active text-xs px-2.5 py-1 rounded-full border
                                   border-emerald-300 bg-emerald-50 dark:bg-emerald-900/30
                                   text-emerald-700 dark:text-emerald-300 transition">
                        Newest
                    </button>
                    <button onclick="setSort('oldest')" data-sort="oldest"
                            class="sort-btn text-xs px-2.5 py-1 rounded-full border
                                   border-gray-200 dark:border-gray-600
                                   text-gray-500 dark:text-gray-400
                                   hover:border-emerald-300 transition">
                        Oldest
                    </button>
                    <button onclick="setSort('largest')" data-sort="largest"
                            class="sort-btn text-xs px-2.5 py-1 rounded-full border
                                   border-gray-200 dark:border-gray-600
                                   text-gray-500 dark:text-gray-400
                                   hover:border-emerald-300 transition">
                        Largest
                    </button>
                </div>
            </div>
            @endif

            {{-- Backup rows --}}
            @if(count($backups) === 0)
                <div class="px-5 py-16 text-center">
                    <div class="text-5xl mb-4">📭</div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">No backups yet</p>
                    <p class="text-gray-400 dark:text-gray-500 text-xs mt-1">
                        Create your first backup using the panel on the left.
                    </p>
                </div>
            @else
                <div id="backup-list" class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($backups as $backup)
                    <div class="backup-row px-5 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/40 transition"
                         data-category="{{ $backup['category_slug'] }}"
                         data-filetype="{{ $backup['filetype_slug'] }}"
                         data-ts="{{ $backup['created_ts'] }}"
                         data-size="{{ $backup['size_bytes'] }}">

                        <div class="flex items-center gap-4">

                            <div class="flex-shrink-0 w-10 h-10 bg-emerald-100 dark:bg-emerald-900/30
                                        rounded-lg flex items-center justify-center text-xl">
                                🗜️
                            </div>

                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-800 dark:text-white
                                          truncate font-mono">
                                    {{ $backup['filename'] }}
                                </p>

                                {{-- Scope tags + metadata --}}
                                <div class="flex items-center flex-wrap gap-1.5 mt-1">
                                    <span class="text-xs text-gray-400 dark:text-gray-500">
                                        {{ $backup['created_at'] }}
                                    </span>
                                    <span class="text-xs text-emerald-600 dark:text-emerald-400
                                                 font-medium bg-emerald-50 dark:bg-emerald-900/20
                                                 px-2 py-0.5 rounded-full">
                                        {{ $backup['size'] }}
                                    </span>
                                    @if($backup['category_slug'] !== 'all')
                                        <span class="text-xs bg-teal-50 dark:bg-teal-900/20
                                                     text-teal-700 dark:text-teal-300
                                                     border border-teal-200 dark:border-teal-700
                                                     px-2 py-0.5 rounded-full">
                                            {{ Str::title(str_replace(['__', '_', '--'], [', ', ' ', ', '], $backup['category_slug'])) }}
                                        </span>
                                    @else
                                        <span class="text-xs bg-gray-100 dark:bg-gray-700
                                                     text-gray-500 dark:text-gray-400
                                                     px-2 py-0.5 rounded-full">
                                            All categories
                                        </span>
                                    @endif
                                    @if($backup['filetype_slug'] !== 'all')
                                        <span class="text-xs bg-purple-50 dark:bg-purple-900/20
                                                     text-purple-700 dark:text-purple-300
                                                     border border-purple-200 dark:border-purple-700
                                                     px-2 py-0.5 rounded-full">
                                            {{ Str::title($backup['filetype_slug']) }} only
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Actions --}}
                            <div class="flex items-center gap-2 flex-shrink-0">
                                <a href="{{ route('admin.document-backups.download', $backup['filename']) }}"
                                   class="inline-flex items-center gap-1 text-xs font-semibold
                                          text-blue-600 dark:text-blue-400
                                          border border-blue-200 dark:border-blue-700
                                          px-3 py-1.5 rounded-lg
                                          hover:bg-blue-50 dark:hover:bg-blue-900/20 transition">
                                    ⬇️ Download
                                </a>
                                <button type="button"
                                        onclick="showDeleteConfirm(this)"
                                        data-filename="{{ $backup['filename'] }}"
                                        data-action="{{ route('admin.document-backups.destroy', $backup['filename']) }}"
                                        class="inline-flex items-center gap-1 text-xs font-semibold
                                               text-red-600 dark:text-red-400
                                               border border-red-200 dark:border-red-700
                                               px-3 py-1.5 rounded-lg
                                               hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                                    🗑️ Delete
                                </button>
                            </div>
                        </div>

                        {{-- Inline delete confirm (hidden by default) --}}
                        <div class="delete-confirm hidden mt-3 p-3
                                    bg-red-50 dark:bg-red-900/20
                                    border border-red-200 dark:border-red-700 rounded-lg">
                            <p class="text-xs font-semibold text-red-800 dark:text-red-300 mb-0.5">
                                Delete this backup?
                            </p>
                            <p class="delete-filename text-xs text-red-600 dark:text-red-400
                                      font-mono mb-2 break-all"></p>
                            <p class="text-xs text-red-600 dark:text-red-400 mb-2">
                                This cannot be undone and will permanently remove the file.
                            </p>
                            <div class="flex gap-2">
                                <form method="POST" class="delete-form flex-1" style="display:contents">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="flex-1 bg-red-600 hover:bg-red-700
                                                   text-white text-xs font-semibold
                                                   px-3 py-1.5 rounded-lg transition">
                                        Yes, delete
                                    </button>
                                </form>
                                <button type="button"
                                        onclick="hideDeleteConfirm(this)"
                                        class="flex-1 border border-gray-200 dark:border-gray-600
                                               text-gray-600 dark:text-gray-400 text-xs font-semibold
                                               px-3 py-1.5 rounded-lg
                                               hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                    Cancel
                                </button>
                            </div>
                        </div>

                    </div>
                    @endforeach
                </div>

                {{-- Empty state shown when filters match nothing --}}
                <div id="no-results" class="hidden px-5 py-10 text-center">
                    <div class="text-4xl mb-3">🔍</div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm">
                        No backups match this filter.
                    </p>
                </div>
            @endif
        </div>

        {{-- Info box --}}
        <div class="mt-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800
                    rounded-xl p-4">
            <p class="text-xs font-semibold text-blue-800 dark:text-blue-300 mb-2">
                ℹ️ What's included in each backup?
            </p>
            <ul class="text-xs text-blue-700 dark:text-blue-400 space-y-1">
                <li>✅ All document categories</li>
                <li>✅ All document metadata (title, description, category, owner, timestamps, trash state)</li>
                <li>✅ Full version history for every document</li>
                <li>✅ All physical files (PDFs, images, docs, etc.)</li>
                <li>✅ Soft-deleted documents (trash)</li>
            </ul>
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
        preview.innerHTML = `<span class="text-xs text-gray-400 dark:text-gray-500 italic">
            None selected — backs up all categories</span>`;
        return;
    }

    preview.innerHTML = boxes.map(cb => {
        const name = cb.closest('label').querySelector('.cat-label').textContent.trim();
        return `<span class="text-xs bg-emerald-100 dark:bg-emerald-900/40
                            text-emerald-800 dark:text-emerald-300
                            px-2 py-0.5 rounded-full font-medium">${name}</span>`;
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

// ─── Restore inline confirm ───────────────────────────────────────────────────
function confirmRestore() {
    document.getElementById('restore-confirm').classList.remove('hidden');
}
function cancelRestore() {
    document.getElementById('restore-confirm').classList.add('hidden');
}

// ─── Delete inline confirm ────────────────────────────────────────────────────
function showDeleteConfirm(btn) {
    // Hide any other open confirms first.
    document.querySelectorAll('.delete-confirm').forEach(el => el.classList.add('hidden'));

    const row     = btn.closest('.backup-row');
    const confirm = row.querySelector('.delete-confirm');
    const form    = confirm.querySelector('.delete-form');
    const label   = confirm.querySelector('.delete-filename');

    form.action         = btn.dataset.action;
    label.textContent   = btn.dataset.filename;

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
        const isActive = btn.dataset.filter === value;
        btn.classList.toggle('active', isActive);
        btn.classList.toggle('border-emerald-300', isActive);
        btn.classList.toggle('bg-emerald-50', isActive);
        btn.classList.toggle('text-emerald-700', isActive);
        btn.classList.toggle('dark:bg-emerald-900/30', isActive);
        btn.classList.toggle('dark:text-emerald-300', isActive);
        btn.classList.toggle('border-gray-200', !isActive);
        btn.classList.toggle('text-gray-500', !isActive);
        btn.classList.toggle('dark:border-gray-600', !isActive);
        btn.classList.toggle('dark:text-gray-400', !isActive);
    });
    applyFilterSort();
}

function setSort(value) {
    activeSort = value;
    document.querySelectorAll('.sort-btn').forEach(btn => {
        const isActive = btn.dataset.sort === value;
        btn.classList.toggle('active', isActive);
        btn.classList.toggle('border-emerald-300', isActive);
        btn.classList.toggle('bg-emerald-50', isActive);
        btn.classList.toggle('text-emerald-700', isActive);
        btn.classList.toggle('dark:bg-emerald-900/30', isActive);
        btn.classList.toggle('dark:text-emerald-300', isActive);
        btn.classList.toggle('border-gray-200', !isActive);
        btn.classList.toggle('text-gray-500', !isActive);
        btn.classList.toggle('dark:border-gray-600', !isActive);
        btn.classList.toggle('dark:text-gray-400', !isActive);
    });
    applyFilterSort();
}

function applyFilterSort() {
    const list    = document.getElementById('backup-list');
    const noRes   = document.getElementById('no-results');
    if (!list) return;

    let rows = [...list.querySelectorAll('.backup-row')];

    // Filter
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
        if (activeSort === 'newest')  return b.dataset.ts   - a.dataset.ts;
        if (activeSort === 'oldest')  return a.dataset.ts   - b.dataset.ts;
        if (activeSort === 'largest') return b.dataset.size - a.dataset.size;
        return 0;
    });
    visible.forEach(row => list.appendChild(row));

    // Toggle empty state
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

    btn.disabled     = true;
    icon.textContent = '⏳';
    text.textContent = 'Creating backup…';
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
            status.innerHTML   = '✅ Backup created successfully!';
            timeEl.textContent = `Completed in ${elapsed}s`;
            setTimeout(() => location.reload(), 1500);
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