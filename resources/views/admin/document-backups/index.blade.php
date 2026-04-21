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
}
@keyframes spin {
    to { transform: rotate(360deg); }
}
</style>

{{-- Header --}}
<div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-emerald-600 to-emerald-700 dark:from-emerald-800 dark:to-emerald-900 p-6 mb-6">
    <div class="relative z-10">
        <h1 class="text-2xl font-bold text-white">Document Backup & Restore</h1>
        <p class="text-emerald-100 text-sm mt-1">Create, download, and restore document backups</p>
    </div>
    <div class="absolute top-0 right-0 -mt-8 -mr-8 w-64 h-64 bg-white/5 rounded-full blur-3xl"></div>
</div>

{{-- Flash messages --}}
@if(session('success'))
    <div class="mb-4 p-4 bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 rounded-xl border border-green-200 dark:border-green-700 flex items-start gap-3">
        <span class="text-lg">✅</span>
        <p class="text-sm font-medium">{{ session('success') }}</p>
    </div>
@endif
@if(session('error'))
    <div class="mb-4 p-4 bg-red-100 dark:bg-red-900/30 text-red-800 dark:text-red-300 rounded-xl border border-red-200 dark:border-red-700 flex items-start gap-3">
        <span class="text-lg">❌</span>
        <p class="text-sm font-medium">{{ session('error') }}</p>
    </div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Left column: actions --}}
    <div class="lg:col-span-1 flex flex-col gap-6">

        {{-- Create Backup --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gold-200 dark:border-gold-800 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gold-100 dark:border-gold-800 bg-emerald-50 dark:bg-emerald-900/20">
                <h2 class="text-sm font-semibold text-emerald-800 dark:text-emerald-300 flex items-center gap-2">
                    <span>💾</span> Create New Backup
                </h2>
            </div>
            <div class="p-5">
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-4">
                    Generates a ZIP archive containing all document metadata, categories, version history, and physical files. Saved to the server and available for download.
                </p>

                <form id="backup-form" method="POST" action="{{ route('admin.document-backups.create') }}">
                    @csrf
                    <button type="submit" id="backup-btn"
                            class="w-full bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition flex items-center justify-center gap-2">
                        <span id="backup-icon">💾</span>
                        <span id="backup-text">Create Backup Now</span>
                    </button>
                </form>

                {{-- Progress area (hidden initially) --}}
                <div id="backup-progress" class="hidden mt-4">
                    <div class="flex items-center gap-2">
                        <div class="loader border-t-2 border-emerald-600 rounded-full w-5 h-5"></div>
                        <span id="backup-status" class="text-sm text-gray-600 dark:text-gray-300">Creating backup...</span>
                    </div>
                    <div id="backup-time" class="text-xs text-gray-400 mt-1"></div>
                </div>

                <p class="text-xs text-gray-400 dark:text-gray-500 mt-3 text-center">
                    ⚠️ Large document libraries may take a moment.
                </p>
            </div>
        </div>

        {{-- Restore from ZIP (unchanged) --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gold-200 dark:border-gold-800 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gold-100 dark:border-gold-800 bg-amber-50 dark:bg-amber-900/20">
                <h2 class="text-sm font-semibold text-amber-800 dark:text-amber-300 flex items-center gap-2">
                    <span>📤</span> Restore from Backup
                </h2>
            </div>
            <div class="p-5">
                <div class="mb-4 p-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-lg">
                    <p class="text-xs text-amber-800 dark:text-amber-300 font-medium">⚠️ Warning</p>
                    <p class="text-xs text-amber-700 dark:text-amber-400 mt-1">
                        Restoring will re-import documents and files. Existing records can be skipped or overwritten depending on your chosen mode.
                    </p>
                </div>

                <form method="POST"
                      action="{{ route('admin.document-backups.restore') }}"
                      enctype="multipart/form-data"
                      onsubmit="return confirmRestore()">
                    @csrf

                    <div class="mb-3">
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">
                            Backup ZIP File
                        </label>
                        <input type="file"
                               name="backup_file"
                               accept=".zip"
                               required
                               class="w-full text-xs text-gray-600 dark:text-gray-300 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-emerald-100 file:text-emerald-700 hover:file:bg-emerald-200 dark:file:bg-emerald-900/40 dark:file:text-emerald-300 border border-gold-200 dark:border-gold-700 rounded-lg p-1.5 bg-white dark:bg-gray-700">
                    </div>

                    <div class="mb-4">
                        <label class="block text-xs font-semibold text-gray-600 dark:text-gray-400 mb-1">
                            Restore Mode
                        </label>
                        <select name="mode"
                                class="w-full px-3 py-2 text-sm border border-gold-200 dark:border-gold-700 rounded-lg dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="skip">Skip existing (safe — keep current data)</option>
                            <option value="merge">Merge / overwrite existing</option>
                        </select>
                    </div>

                    <button type="submit"
                            class="w-full bg-amber-600 hover:bg-amber-700 text-white text-sm font-semibold px-4 py-2.5 rounded-lg transition flex items-center justify-center gap-2">
                        📤 Restore Backup
                    </button>
                </form>
            </div>
        </div>

    </div>

    {{-- Right column: backup list (unchanged) --}}
    <div class="lg:col-span-2">
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gold-200 dark:border-gold-800 shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-gold-100 dark:border-gold-800 flex items-center justify-between">
                <h2 class="text-sm font-semibold text-gray-800 dark:text-white flex items-center gap-2">
                    <span>🗂️</span> Saved Backups
                    <span class="ml-1 bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-300 text-xs font-bold px-2 py-0.5 rounded-full">
                        {{ count($backups) }}
                    </span>
                </h2>
                <p class="text-xs text-gray-400 dark:text-gray-500">Stored on server · private disk</p>
            </div>

            @if(count($backups) === 0)
                <div class="px-5 py-16 text-center">
                    <div class="text-5xl mb-4">📭</div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">No backups yet</p>
                    <p class="text-gray-400 dark:text-gray-500 text-xs mt-1">Create your first backup using the panel on the left.</p>
                </div>
            @else
                <div class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($backups as $backup)
                    <div class="px-5 py-4 flex items-center gap-4 hover:bg-gray-50 dark:hover:bg-gray-700/40 transition group">

                        <div class="flex-shrink-0 w-10 h-10 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg flex items-center justify-center text-xl">
                            🗜️
                        </div>

                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-800 dark:text-white truncate font-mono">
                                {{ $backup['filename'] }}
                            </p>
                            <div class="flex items-center gap-3 mt-0.5">
                                <span class="text-xs text-gray-400 dark:text-gray-500">{{ $backup['created_at'] }}</span>
                                <span class="text-xs text-emerald-600 dark:text-emerald-400 font-medium bg-emerald-50 dark:bg-emerald-900/20 px-2 py-0.5 rounded-full">
                                    {{ $backup['size'] }}
                                </span>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 flex-shrink-0">
                            <a href="{{ route('admin.document-backups.download', $backup['filename']) }}"
                               class="inline-flex items-center gap-1 text-xs font-semibold text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-700 px-3 py-1.5 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/20 transition">
                                ⬇️ Download
                            </a>
                            <form method="POST"
                                  action="{{ route('admin.document-backups.destroy', $backup['filename']) }}"
                                  onsubmit="return confirm('Delete backup {{ $backup['filename'] }}? This cannot be undone.')"
                                  class="inline">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="inline-flex items-center gap-1 text-xs font-semibold text-red-600 dark:text-red-400 border border-red-200 dark:border-red-700 px-3 py-1.5 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                                    🗑️ Delete
                                </button>
                            </form>
                        </div>

                    </div>
                    @endforeach
                </div>
            @endif
        </div>

        <div class="mt-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4">
            <p class="text-xs font-semibold text-blue-800 dark:text-blue-300 mb-2">ℹ️ What's included in each backup?</p>
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
function confirmRestore() {
    return confirm(
        '⚠️ Restore from this backup?\n\n' +
        'This will re-import documents and files from the ZIP. ' +
        'Make sure you have selected the correct restore mode.\n\n' +
        'Click OK to proceed.'
    );
}

// Handle backup creation via AJAX
document.getElementById('backup-form').addEventListener('submit', async function(e) {
    e.preventDefault();

    const btn = document.getElementById('backup-btn');
    const icon = document.getElementById('backup-icon');
    const textSpan = document.getElementById('backup-text');
    const progressDiv = document.getElementById('backup-progress');
    const statusSpan = document.getElementById('backup-status');
    const timeSpan = document.getElementById('backup-time');

    // Disable button and show progress
    btn.disabled = true;
    icon.textContent = '⏳';
    textSpan.textContent = 'Creating backup...';
    progressDiv.classList.remove('hidden');
    const startTime = Date.now();

    try {
        const formData = new FormData(this);
        const response = await fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const result = await response.json();

        const elapsed = ((Date.now() - startTime) / 1000).toFixed(1);
        if (response.ok && result.success) {
            statusSpan.innerHTML = '✅ Backup created successfully!';
            timeSpan.innerHTML = `Completed in ${elapsed} seconds.`;
            // Reload page after 1.5 seconds to show the new backup in the list
            setTimeout(() => location.reload(), 1500);
        } else {
            statusSpan.innerHTML = '❌ Error: ' + (result.error || 'Unknown error');
            timeSpan.innerHTML = `Failed after ${elapsed} seconds.`;
            // Re-enable button for retry
            btn.disabled = false;
            icon.textContent = '💾';
            textSpan.textContent = 'Create Backup Now';
        }
    } catch (err) {
        statusSpan.innerHTML = '❌ Network error – please try again.';
        timeSpan.innerHTML = '';
        btn.disabled = false;
        icon.textContent = '💾';
        textSpan.textContent = 'Create Backup Now';
    }
});
</script>

@endsection