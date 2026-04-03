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

<div class="bg-white dark:bg-gray-800 rounded-xl border border-emerald-200 dark:border-emerald-800 p-4 mb-6 shadow-sm">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Title or description..." class="w-full px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Category</label>
            <select name="category" class="px-3 py-1.5 border border-gray-300 dark:border-gray-600 rounded-lg text-sm">
                <option value="">All</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <button type="submit" class="bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-1.5 rounded-lg text-sm">Filter</button>
            @if(request()->anyFilled(['search', 'category']))
                <a href="{{ route('documents.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-1.5 rounded-lg text-sm ml-2">Clear</a>
            @endif
        </div>
        @can('upload documents', \App\Models\Document::class)
        <div class="ml-auto">
            <a href="{{ route('documents.create') }}" class="inline-flex items-center gap-1 bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-semibold px-3 py-1.5 rounded-lg">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Upload Document
            </a>
        </div>
        @endcan
    </form>
</div>

<div class="bg-white dark:bg-gray-800 rounded-xl border border-emerald-200 dark:border-emerald-800 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-emerald-50 dark:bg-emerald-900/30">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-semibold">Title</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold">Category</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold">Uploaded By</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold">Size</th>
                    <th class="px-5 py-3 text-left text-xs font-semibold">Date</th>
                    <th class="px-5 py-3 text-right text-xs font-semibold">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($documents as $doc)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                    <td class="px-5 py-3 font-medium">
                        <a href="{{ route('documents.show', $doc) }}" class="text-emerald-600 hover:underline">{{ $doc->title }}</a>
                        @if(!$doc->is_public && $doc->organization_id)
                            <span class="ml-2 text-xs bg-gray-200 dark:bg-gray-700 px-1.5 py-0.5 rounded">Private</span>
                        @endif
                    </td>
                    <td class="px-5 py-3">{{ $doc->category ?? '—' }}</td>
                    <td class="px-5 py-3">{{ $doc->uploader->full_name ?? 'Unknown' }}</td>
                    <td class="px-5 py-3">{{ $doc->formatted_size }}</td>
                    <td class="px-5 py-3 text-gray-500">{{ $doc->created_at->format('M d, Y') }}</td>
                    <td class="px-5 py-3 text-right">
                        <div class="flex justify-end gap-2">
                            <a href="{{ route('documents.download', $doc) }}" class="text-blue-600 hover:text-blue-800" title="Download">⬇️</a>
                            @can('manage documents')
                            <a href="{{ route('documents.edit', $doc) }}" class="text-emerald-600 hover:text-emerald-800">✏️</a>
                            <form method="POST" action="{{ route('documents.destroy', $doc) }}" onsubmit="return confirm('Delete this document?')" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800">🗑️</button>
                            </form>
                            @endcan
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-5 py-12 text-center text-gray-500">No documents found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($documents->hasPages())
    <div class="px-5 py-3 border-t border-gray-100 dark:border-gray-700">{{ $documents->links() }}</div>
    @endif
</div>
@endsection