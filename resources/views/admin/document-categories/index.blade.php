@extends('layouts.app')

@section('title', 'Document Categories')
@section('page-title', 'Manage Document Categories')

@section('content')
<div class="space-y-6">

    {{-- Emerald Gradient Header --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-emerald-600 to-emerald-700 dark:from-emerald-800 dark:to-emerald-900 p-6 mb-6">
        <div class="relative z-10">
            <h1 class="text-2xl font-bold text-white">Document Categories</h1>
            <p class="text-emerald-100 text-sm mt-1">Manage categories for document organisation</p>
        </div>
        <div class="absolute top-0 right-0 -mt-8 -mr-8 w-64 h-64 bg-white/5 rounded-full blur-3xl"></div>
    </div>

    {{-- Flash Messages (gold borders) --}}
    @if(session('success'))
        <div class="p-4 bg-green-100 dark:bg-green-900/30 border border-gold-300 dark:border-gold-800 text-green-800 dark:text-green-300 rounded-xl text-sm">
            ✅ {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-red-100 dark:bg-red-900/30 border border-gold-300 dark:border-gold-800 text-red-800 dark:text-red-300 rounded-xl text-sm">
            ❌ {{ session('error') }}
        </div>
    @endif

    {{-- Table Card --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gold-200 dark:border-gold-800 overflow-hidden shadow-sm">
        <div class="flex justify-between items-center px-5 py-4 border-b border-gold-200 dark:border-gold-800">
            <h2 class="text-lg font-semibold text-gray-800 dark:text-white">Document Categories</h2>
            @if(Auth::user()->role->level === 1 || Auth::user()->hasPermission('categories.create'))
                <a href="{{ route('admin.document-categories.create') }}"
                   class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-gold-500 text-white text-sm font-semibold px-4 py-2 rounded-lg transition shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    New Category
                </a>
            @endif
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-emerald-600 dark:bg-emerald-700 text-white">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider">Name</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider">Description</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider">Status</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($categories as $cat)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                        <td class="px-5 py-3 font-medium text-gray-900 dark:text-white">{{ $cat->name }}</td>
                        <td class="px-5 py-3 text-gray-500 dark:text-gray-400">{{ $cat->description ?? '—' }}</td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $cat->is_active ? 'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-300' : 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300' }}">
                                {{ $cat->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                @if(Auth::user()->role->level === 1 || Auth::user()->hasPermission('categories.edit'))
                                    <a href="{{ route('admin.document-categories.edit', $cat) }}"
                                       class="inline-flex items-center gap-1 text-xs bg-emerald-100 hover:bg-gold-500 hover:text-white dark:bg-emerald-900/40 dark:hover:bg-gold-600 text-emerald-700 dark:text-emerald-300 font-medium px-2.5 py-1 rounded-lg transition"
                                       title="Edit">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                        Edit
                                    </a>
                                @endif
                                @if(Auth::user()->role->level === 1 || Auth::user()->hasPermission('categories.delete'))
                                    <form method="POST" action="{{ route('admin.document-categories.destroy', $cat) }}" onsubmit="return confirm('Delete this category?')" class="inline">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center gap-1 text-xs bg-red-100 hover:bg-red-200 dark:bg-red-900/40 dark:hover:bg-red-900/70 text-red-700 dark:text-red-300 font-medium px-2.5 py-1 rounded-lg transition"
                                                title="Delete">
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
                            <td colspan="4" class="px-5 py-12 text-center text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center gap-2">
                                    <svg class="w-12 h-12 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <p>No categories found.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($categories->hasPages())
        <div class="px-5 py-3 border-t border-gold-200 dark:border-gold-800 bg-gray-50 dark:bg-gray-800/50">
            {{ $categories->links() }}
        </div>
        @endif
    </div>
</div>
@endsection