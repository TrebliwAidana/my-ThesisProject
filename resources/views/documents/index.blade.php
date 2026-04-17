@extends('layouts.app')

@section('title', 'Documents')

@section('content')
<div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-emerald-600 to-emerald-700 dark:from-emerald-800 dark:to-emerald-900 p-6 mb-6">
    <div class="relative z-10">
        <h1 class="text-2xl font-bold text-white">Documents</h1>
        <p class="text-emerald-100 text-sm mt-1">Manage organizational documents</p>
    </div>
    <div class="absolute top-0 right-0 -mt-8 -mr-8 w-64 h-64 bg-white/5 rounded-full blur-3xl"></div>
</div>

{{-- Filter Card --}}
<div class="bg-white dark:bg-gray-800 rounded-xl border border-gold-200 dark:border-gold-800 p-4 mb-6 shadow-sm">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Title or description..."
                   class="w-full px-3 py-1.5 border border-gold-300 dark:border-gold-600 rounded-lg text-sm dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-gold-500">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Category</label>
            <select name="category"
                    class="px-3 py-1.5 border border-gold-300 dark:border-gold-600 rounded-lg text-sm dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-gold-500">
                <option value="">All</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Visibility</label>
            <select name="visibility"
                    class="px-3 py-1.5 border border-gold-300 dark:border-gold-600 rounded-lg text-sm dark:bg-gray-700 dark:text-white focus:outline-none focus:ring-2 focus:ring-gold-500">
                <option value="">All</option>
                <option value="public"  {{ request('visibility') == 'public'  ? 'selected' : '' }}>Public</option>
                <option value="private" {{ request('visibility') == 'private' ? 'selected' : '' }}>Private</option>
            </select>
        </div>
        <div>
            <button type="submit"
                    class="bg-emerald-600 hover:bg-gold-500 text-white px-4 py-1.5 rounded-lg text-sm transition">
                Filter
            </button>
            @if(request()->anyFilled(['search', 'category', 'visibility']))
                <a href="{{ route('documents.index') }}"
                   class="bg-gray-500 hover:bg-gold-500 text-white px-4 py-1.5 rounded-lg text-sm ml-2 transition">
                    Clear
                </a>
            @endif
        </div>

        {{-- Action Buttons (Trash + Upload) --}}
        <div class="ml-auto flex gap-2">
            <a href="{{ route('documents.trash') }}"
               class="inline-flex items-center gap-1 bg-gray-600 hover:bg-gray-700 text-white text-sm font-semibold px-3 py-1.5 rounded-lg transition">
                🗑️ Trash
            </a>
            @if(auth()->user()->hasPermission('documents.upload'))
                <a href="{{ route('documents.create') }}"
                   class="inline-flex items-center gap-1 bg-emerald-600 hover:bg-gold-500 text-white text-sm font-semibold px-3 py-1.5 rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Upload Document
                </a>
            @endif
        </div>
    </form>
</div>

{{-- Stats row --}}
<div class="grid grid-cols-3 gap-3 mb-6">
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gold-200 dark:border-gold-800 p-3 shadow-sm text-center">
        <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">{{ $documents->total() }}</p>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Total Documents</p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gold-200 dark:border-gold-800 p-3 shadow-sm text-center">
        <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $publicCount }}</p>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Public</p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gold-200 dark:border-gold-800 p-3 shadow-sm text-center">
        <p class="text-2xl font-bold text-gray-600 dark:text-gray-400">{{ $privateCount }}</p>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Private</p>
    </div>
</div>

{{-- Documents Table --}}
<div class="bg-white dark:bg-gray-800 rounded-xl border border-gold-200 dark:border-gold-800 overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-emerald-600 dark:bg-emerald-800 text-white">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider">Title</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider">Category</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider">Uploaded By</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider">Size</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider">Visibility</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider">Date</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($documents as $doc)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                    <td class="px-5 py-3 font-medium max-w-xs">
                        <div class="flex items-center gap-2">
                            @php
                                $version = $doc->currentVersion;
                                $fileName = $version?->file_name ?? '';
                                $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                                $iconColor = match(true) {
                                    in_array($ext, ['pdf'])              => 'text-red-500',
                                    in_array($ext, ['doc','docx'])       => 'text-blue-500',
                                    in_array($ext, ['xls','xlsx'])       => 'text-green-600',
                                    in_array($ext, ['ppt','pptx'])       => 'text-orange-500',
                                    in_array($ext, ['jpg','jpeg','png','gif']) => 'text-purple-500',
                                    in_array($ext, ['zip'])              => 'text-yellow-600',
                                    default                              => 'text-gray-400',
                                };
                            @endphp
                            <span class="text-lg {{ $iconColor }} flex-shrink-0">
                                @if(in_array($ext, ['pdf']))📄
                                @elseif(in_array($ext, ['doc','docx']))📝
                                @elseif(in_array($ext, ['xls','xlsx']))📊
                                @elseif(in_array($ext, ['ppt','pptx']))📊
                                @elseif(in_array($ext, ['jpg','jpeg','png','gif']))🖼️
                                @elseif(in_array($ext, ['zip']))🗜️
                                @else📎
                                @endif
                            </span>
                            <div class="min-w-0">
                                <a href="{{ route('documents.show', $doc) }}" class="text-emerald-600 dark:text-emerald-400 hover:underline block truncate max-w-[200px]" title="{{ $doc->title }}">
                                    {{ $doc->title }}
                                </a>
                                @if($doc->description)
                                <p class="text-xs text-gray-400 truncate max-w-[200px]">{{ $doc->description }}</p>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3">
                        @if($doc->category)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 dark:bg-emerald-900/40 text-emerald-800 dark:text-emerald-300">
                            {{ $doc->category }}
                        </span>
                        @else
                        <span class="text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-gray-600 dark:text-gray-400">{{ $doc->owner->full_name ?? 'Unknown' }}</td>
                    <td class="px-5 py-3 text-gray-500 dark:text-gray-400 text-xs font-mono">{{ $doc->formatted_size }}</td>
                    <td class="px-5 py-3">
                        @if($doc->is_public)
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-300">
                                🌐 Public
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                                🔒 Private
                            </span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-gray-500 dark:text-gray-400 text-xs">{{ $doc->created_at->format('M d, Y') }}</td>
                    <td class="px-5 py-3 text-right">
                        <div class="flex items-center justify-end gap-1">
                            @if($version)
                                @php
                                    $ext = strtolower(pathinfo($version->file_name, PATHINFO_EXTENSION));
                                @endphp
                                {{-- Preview (only for images and PDFs via secure route) --}}
                                @if(in_array($ext, ['pdf','jpg','jpeg','png','gif']))
                                <button onclick="openPreview('{{ route('documents.preview', $doc) }}', '{{ $doc->title }}', '{{ $ext }}')"
                                        class="p-1.5 text-gray-400 hover:text-emerald-600 dark:hover:text-emerald-400 transition" title="Preview">
                                    👁️
                                </button>
                                @endif
                                <a href="{{ route('documents.download', $doc) }}" class="p-1.5 text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition" title="Download">⬇️</a>
                            @else
                                <span class="text-xs text-gray-400 px-2" title="No file attached">—</span>
                            @endif

                            @if(auth()->user()->hasPermission('documents.manage'))
                            <a href="{{ route('documents.edit', $doc) }}" class="p-1.5 text-gray-400 hover:text-emerald-600 dark:hover:text-emerald-400 transition" title="Edit">✏️</a>
                            <form method="POST" action="{{ route('documents.destroy', $doc) }}" onsubmit="return confirm('Delete this document?')" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition" title="Delete">🗑️</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-12 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <svg class="w-16 h-16 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="text-gray-500 dark:text-gray-400">No documents found.</p>
                            @if(auth()->user()->hasPermission('documents.upload'))
                            <a href="{{ route('documents.create') }}" class="text-emerald-600 hover:underline text-sm">Upload your first document</a>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($documents->hasPages())
    <div class="px-5 py-3 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
        {{ $documents->links() }}
    </div>
    @endif
</div>

{{-- Preview Modal --}}
<div id="preview-modal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4" style="background:rgba(0,0,0,0.7)">
    <div class="relative bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] flex flex-col overflow-hidden">
        <div class="flex items-center justify-between px-5 py-3 border-b border-gray-200 dark:border-gray-700 flex-shrink-0">
            <h3 id="preview-title" class="text-sm font-semibold text-gray-800 dark:text-white truncate max-w-md"></h3>
            <div class="flex items-center gap-2">
                <a id="preview-download-link" href="#" class="text-xs bg-emerald-600 hover:bg-gold-500 text-white px-3 py-1 rounded-lg transition">⬇️ Download</a>
                <button onclick="closePreview()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition text-xl leading-none">&times;</button>
            </div>
        </div>
        <div class="flex-1 overflow-auto flex items-center justify-center p-4 min-h-0">
            <iframe id="preview-iframe" src="" class="hidden w-full rounded-lg border border-gray-200 dark:border-gray-700" style="height:70vh"></iframe>
            <img id="preview-img" src="" alt="" class="hidden max-w-full max-h-full rounded-lg object-contain">
            <p id="preview-unsupported" class="hidden text-gray-500 dark:text-gray-400 text-sm">Preview not available for this file type.</p>
        </div>
    </div>
</div>

<script>
function openPreview(url, title, ext) {
    document.getElementById('preview-title').textContent = title;
    document.getElementById('preview-download-link').href = url.replace('/preview', '/download');

    const iframe = document.getElementById('preview-iframe');
    const img    = document.getElementById('preview-img');
    const unsup  = document.getElementById('preview-unsupported');

    iframe.classList.add('hidden'); iframe.src = '';
    img.classList.add('hidden');    img.src    = '';
    unsup.classList.add('hidden');

    const imageExts = ['jpg','jpeg','png','gif','webp'];
    if (ext === 'pdf') {
        iframe.src = url;
        iframe.classList.remove('hidden');
    } else if (imageExts.includes(ext)) {
        img.src = url;
        img.classList.remove('hidden');
    } else {
        unsup.classList.remove('hidden');
    }

    document.getElementById('preview-modal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}

function closePreview() {
    document.getElementById('preview-modal').classList.add('hidden');
    document.getElementById('preview-iframe').src = '';
    document.getElementById('preview-img').src    = '';
    document.body.style.overflow = '';
}

document.getElementById('preview-modal').addEventListener('click', function(e) {
    if (e.target === this) closePreview();
});

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closePreview();
});
</script>
@endsection