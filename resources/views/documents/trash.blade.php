@extends('layouts.app')

@section('title', 'Document Trash')
@section('page-title', 'Document Trash')

@section('content')
{{-- Page Header (Emerald gradient) --}}
<div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-emerald-600 to-emerald-700 dark:from-emerald-800 dark:to-emerald-900 p-6 mb-6">
    <div class="relative z-10">
        <h1 class="text-2xl font-bold text-white">Document Trash</h1>
        <p class="text-emerald-100 text-sm mt-1">Soft-deleted documents pending permanent removal</p>
    </div>
    <div class="absolute top-0 right-0 -mt-8 -mr-8 w-64 h-64 bg-white/5 rounded-full blur-3xl"></div>
</div>

<div class="bg-white dark:bg-gray-800 rounded-xl border border-gold-200 dark:border-gold-800 overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-emerald-600 dark:bg-emerald-700 text-white">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider">Title</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider">Category</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider">Deleted At</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider">Size</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($documents as $doc)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                    <td class="px-5 py-3 font-medium max-w-xs">
                        <div class="flex items-center gap-2">
                            @php
                                $version   = $doc->currentVersion;
                                $fileName  = $version?->file_name ?? '';
                                $ext       = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
                                $iconColor = match(true) {
                                    in_array($ext, ['pdf'])                    => 'text-red-500',
                                    in_array($ext, ['doc','docx'])             => 'text-blue-500',
                                    in_array($ext, ['xls','xlsx'])             => 'text-green-600',
                                    in_array($ext, ['ppt','pptx'])             => 'text-orange-500',
                                    in_array($ext, ['jpg','jpeg','png','gif']) => 'text-purple-500',
                                    in_array($ext, ['zip'])                    => 'text-yellow-600',
                                    default                                    => 'text-gray-400',
                                };
                            @endphp
                            <span class="text-lg {{ $iconColor }} flex-shrink-0">
                                @if(in_array($ext, ['pdf']))📄
                                @elseif(in_array($ext, ['doc','docx']))📝
                                @elseif(in_array($ext, ['xls','xlsx','ppt','pptx']))📊
                                @elseif(in_array($ext, ['jpg','jpeg','png','gif']))🖼️
                                @elseif($ext === 'zip')🗜️
                                @else📎
                                @endif
                            </span>
                            <div class="min-w-0">
                                <span class="text-gray-800 dark:text-white block truncate max-w-[200px]" title="{{ $doc->title }}">
                                    {{ $doc->title }}
                                </span>
                                @if($doc->description)
                                <p class="text-xs text-gray-400 truncate max-w-[200px]">{{ $doc->description }}</p>
                                @endif
                            </div>
                        </div>
                    </td>

                    <td class="px-5 py-3">
                        @if($doc->category)
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 dark:bg-emerald-900/40 text-emerald-800 dark:text-emerald-300">
                            {{ $doc->category->name ?? $doc->category }}
                        </span>
                        @else
                        <span class="text-gray-400">—</span>
                        @endif
                    </td>

                    <td class="px-5 py-3 text-gray-500 dark:text-gray-400 text-xs">
                        {{ $doc->deleted_at->format('M d, Y H:i') }}
                    </td>

                    <td class="px-5 py-3 text-gray-500 dark:text-gray-400 text-xs font-mono">
                        {{ $doc->formatted_size }}
                    </td>

                    <td class="px-5 py-3 text-right">
                        <div class="flex items-center justify-end gap-2">
                            {{-- Restore --}}
                            <form method="POST" action="{{ route('documents.restore', $doc->id) }}" class="inline">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                    class="inline-flex items-center gap-1 text-xs bg-emerald-100 hover:bg-gold-500 hover:text-white dark:bg-emerald-900/40 dark:hover:bg-gold-600 text-emerald-700 dark:text-emerald-300 font-medium px-2.5 py-1 rounded-lg transition">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                    </svg>
                                    Restore
                                </button>
                            </form>

                            {{-- Force Delete --}}
                            <form method="POST" action="{{ route('documents.force-delete', $doc->id) }}" class="inline"
                                  onsubmit="return confirm('Permanently delete this document? This action cannot be undone.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="inline-flex items-center gap-1 text-xs bg-red-100 hover:bg-red-200 dark:bg-red-900/40 dark:hover:bg-red-900/70 text-red-700 dark:text-red-300 font-medium px-2.5 py-1 rounded-lg transition">
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
                    <td colspan="5" class="px-5 py-12 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <svg class="w-16 h-16 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            <p class="text-gray-500 dark:text-gray-400">Trash is empty.</p>
                            <a href="{{ route('documents.index') }}" class="text-emerald-600 hover:text-gold-500 dark:text-emerald-400 dark:hover:text-gold-400 transition text-sm">Back to Documents</a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($documents->hasPages())
    <div class="px-5 py-3 border-t border-gold-200 dark:border-gold-800 bg-gray-50 dark:bg-gray-800/50">
        {{ $documents->links() }}
    </div>
    @endif
</div>
@endsection