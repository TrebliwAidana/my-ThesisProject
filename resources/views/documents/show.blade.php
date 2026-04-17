@extends('layouts.app')

@section('title', $document->title)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    {{-- Document Details Card --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-emerald-200 dark:border-emerald-800 overflow-hidden shadow-sm">
        <div class="px-6 py-4 border-b border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/20">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $document->title }}</h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Uploaded by {{ $document->owner->full_name ?? 'Unknown' }} 
                        on {{ $document->created_at->format('F d, Y') }}
                    </p>
                </div>
                @if($document->is_public)
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/40 text-blue-800 dark:text-blue-300">
                        🌐 Public
                    </span>
                @else
                    <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                        🔒 Private
                    </span>
                @endif
            </div>
        </div>

        <div class="p-6 space-y-4">
            <div>
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Description</h3>
                <p class="text-gray-700 dark:text-gray-300">{{ $document->description ?: 'No description provided.' }}</p>
            </div>

            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="font-semibold text-gray-600 dark:text-gray-400">Category:</span>
                    <span class="text-gray-800 dark:text-gray-200">{{ $document->category ?? '—' }}</span>
                </div>
                <div>
                    <span class="font-semibold text-gray-600 dark:text-gray-400">File size:</span>
                    <span class="text-gray-800 dark:text-gray-200">{{ $document->formatted_size }}</span>
                </div>
                @if($document->currentVersion)
                <div>
                    <span class="font-semibold text-gray-600 dark:text-gray-400">MIME type:</span>
                    <span class="text-gray-800 dark:text-gray-200">{{ $document->currentVersion->mime_type }}</span>
                </div>
                <div>
                    <span class="font-semibold text-gray-600 dark:text-gray-400">Current version:</span>
                    <span class="text-gray-800 dark:text-gray-200">v{{ $document->currentVersion->version_number }}</span>
                </div>
                @endif
            </div>

            {{-- Current File Actions --}}
            <div class="pt-4 flex flex-wrap gap-3">
                @if($document->currentVersion)
                    @php
                        $ext = strtolower(pathinfo($document->currentVersion->file_name, PATHINFO_EXTENSION));
                    @endphp
                    @if(in_array($ext, ['pdf','jpg','jpeg','png','gif']))
                        <a href="{{ route('documents.preview', $document) }}" 
                           target="_blank"
                           class="inline-flex items-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                            👁️ Preview
                        </a>
                    @endif
                    <a href="{{ route('documents.download', $document) }}" 
                       class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg transition">
                        ⬇️ Download (v{{ $document->currentVersion->version_number }})
                    </a>
                @else
                    <span class="text-gray-400 dark:text-gray-500 italic">No file attached to this document.</span>
                @endif

                @can('update', $document)
                    <a href="{{ route('documents.edit', $document) }}" 
                       class="inline-flex items-center gap-2 bg-gold-500 hover:bg-gold-600 text-white px-4 py-2 rounded-lg transition">
                        ✏️ Edit
                    </a>
                @endcan

                @can('delete', $document)
                    <form method="POST" action="{{ route('documents.destroy', $document) }}" 
                          onsubmit="return confirm('Are you sure you want to delete this document?')" class="inline">
                        @csrf @method('DELETE')
                        <button type="submit" 
                                class="inline-flex items-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition">
                            🗑️ Delete
                        </button>
                    </form>
                @endcan

                <a href="{{ route('documents.index') }}" 
                   class="inline-flex items-center gap-2 bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition">
                    ← Back to List
                </a>
            </div>
        </div>
    </div>

    {{-- Version History Card --}}
    @if($document->versions->count() > 0)
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gold-200 dark:border-gold-800 overflow-hidden shadow-sm">
        <div class="px-6 py-3 border-b border-gold-200 dark:border-gold-800 bg-gold-50 dark:bg-gold-900/20">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Version History</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300 uppercase text-xs tracking-wider">
                    <tr>
                        <th class="px-5 py-3 text-left">Version</th>
                        <th class="px-5 py-3 text-left">File Name</th>
                        <th class="px-5 py-3 text-left">Size</th>
                        <th class="px-5 py-3 text-left">Uploaded By</th>
                        <th class="px-5 py-3 text-left">Date</th>
                        <th class="px-5 py-3 text-left">Change Notes</th>
                        <th class="px-5 py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($document->versions as $version)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                        <td class="px-5 py-3 font-medium">
                            v{{ $version->version_number }}
                            @if($document->currentVersion && $version->id === $document->currentVersion->id)
                                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 dark:bg-emerald-900/40 text-emerald-800 dark:text-emerald-300">
                                    Current
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-3 max-w-xs truncate">{{ $version->file_name }}</td>
                        <td class="px-5 py-3 text-gray-500 dark:text-gray-400 text-xs font-mono">
                            @php
                                $bytes = $version->file_size;
                                $units = ['B', 'KB', 'MB', 'GB'];
                                $i = 0;
                                while ($bytes >= 1024 && $i < count($units) - 1) {
                                    $bytes /= 1024;
                                    $i++;
                                }
                            @endphp
                            {{ round($bytes, 2) . ' ' . $units[$i] }}
                        </td>
                        <td class="px-5 py-3 text-gray-600 dark:text-gray-400">{{ $version->uploader->full_name ?? 'Unknown' }}</td>
                        <td class="px-5 py-3 text-gray-500 dark:text-gray-400 text-xs">{{ $version->created_at->format('M d, Y H:i') }}</td>
                        <td class="px-5 py-3 text-gray-500 dark:text-gray-400 max-w-xs">
                            {{ $version->change_notes ?: '—' }}
                        </td>
                        <td class="px-5 py-3 text-center">
                            <a href="{{ route('documents.version.download', [$document->id, $version->id]) }}" 
                               class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                                Download
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection