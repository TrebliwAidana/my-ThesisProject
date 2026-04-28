@extends('layouts.app')

@section('title', 'Financial Categories')
@section('page-title', 'Financial Categories')

@section('content')
<div class="space-y-6">

    {{-- Header & New Category Button --}}
    <div class="flex flex-wrap justify-between items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Financial Categories</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Manage categories used in income, expense, and receivable transactions.</p>
        </div>
        <a href="{{ route('admin.financial-categories.create') }}"
           class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-gold-500 text-white text-sm font-semibold px-4 py-2 rounded-lg transition shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            New Category
        </a>
    </div>

    {{-- Flash Messages
    @if(session('success'))
        <div class="p-4 bg-green-100 dark:bg-green-900/30 border border-gold-300 dark:border-gold-800 text-green-800 dark:text-green-300 rounded-xl text-sm">
            ✅ {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-red-100 dark:bg-red-900/30 border border-gold-300 dark:border-gold-800 text-red-800 dark:text-red-300 rounded-xl text-sm">
            ❌ {{ session('error') }}
        </div>
    @endif --}}

    {{-- Filters Card (search, type – replaced Both with Receivable) --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gold-200 dark:border-gold-800 p-4 shadow-sm">
        <form method="GET" action="{{ route('admin.financial-categories.index') }}" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Name…"
                       class="text-sm rounded-lg border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Type</label>
                <select name="type"
                        class="text-sm rounded-lg border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white px-3 py-2 focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-transparent">
                    <option value="">All Types</option>
                    <option value="income"     {{ request('type') === 'income'     ? 'selected' : '' }}>Income</option>
                    <option value="expense"    {{ request('type') === 'expense'    ? 'selected' : '' }}>Expense</option>
                    <option value="receivable" {{ request('type') === 'receivable' ? 'selected' : '' }}>Receivable</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit"
                        class="bg-emerald-600 hover:bg-gold-500 text-white px-4 py-1.5 rounded-lg text-sm font-medium transition">Filter</button>
                <a href="{{ route('admin.financial-categories.index') }}"
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-1.5 rounded-lg text-sm transition">Reset</a>
            </div>
        </form>
    </div>

    {{-- Table Card --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gold-200 dark:border-gold-800 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-emerald-600 dark:bg-emerald-700 text-white">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider">ID</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider">Name</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider">Type</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider">Description</th>
                        <th class="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wider">Created</th>
                        <th class="px-5 py-3 text-right text-xs font-semibold uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($categories as $cat)
                        @php
                            $isTrashed = $cat->trashed();
                            $rowClass = $isTrashed ? 'bg-red-50 dark:bg-red-900/10 opacity-75' : '';
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition {{ $rowClass }}">
                            <td class="px-5 py-3 text-gray-500 dark:text-gray-400">{{ $cat->id }}</td>
                            <td class="px-5 py-3 font-medium text-gray-900 dark:text-white">
                                {{ $cat->name }}
                                @if($isTrashed)
                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-300">Deleted</span>
                                @endif
                            </td>
                            <td class="px-5 py-3">
                                @php
                                    $typeBadge = match($cat->type) {
                                        'income'     => 'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-300',
                                        'expense'    => 'bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-300',
                                        'receivable' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300',
                                        default      => 'bg-purple-100 text-purple-700 dark:bg-purple-900/50 dark:text-purple-300', // fallback for 'both' if any
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $typeBadge }}">
                                    {{ ucfirst($cat->type) }}
                                </span>
                            </td>
                            <td class="px-5 py-3 text-gray-500 dark:text-gray-400">{{ $cat->description ?? '—' }}</td>
                            <td class="px-5 py-3 text-gray-500 dark:text-gray-400 text-xs">{{ $cat->created_at->format('M d, Y') }}</td>
                            <td class="px-5 py-3 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if($isTrashed)
                                        {{-- Restore --}}
                                        <form method="POST" action="{{ route('admin.financial-categories.restore', $cat->id) }}" class="inline">
                                            @csrf @method('PATCH')
                                            <button type="submit"
                                                    class="inline-flex items-center gap-1 text-xs bg-emerald-100 hover:bg-gold-500 hover:text-white dark:bg-emerald-900/40 dark:hover:bg-gold-600 text-emerald-700 dark:text-emerald-300 font-medium px-2.5 py-1 rounded-lg transition"
                                                    title="Restore">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg>
                                                Restore
                                            </button>
                                        </form>
                                        {{-- Force Delete --}}
                                        <form method="POST" action="{{ route('admin.financial-categories.forceDelete', $cat->id) }}" class="inline"
                                              onsubmit="return confirm('Permanently delete this category? This cannot be undone.')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex items-center gap-1 text-xs bg-red-100 hover:bg-red-200 dark:bg-red-900/40 dark:hover:bg-red-900/70 text-red-700 dark:text-red-300 font-medium px-2.5 py-1 rounded-lg transition"
                                                    title="Permanently Delete">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                Force Delete
                                            </button>
                                        </form>
                                    @else
                                        {{-- Edit – now a direct link, same as Create --}}
                                        <a href="{{ route('admin.financial-categories.edit', $cat->id) }}"
                                           class="inline-flex items-center gap-1 text-xs bg-emerald-100 hover:bg-gold-500 hover:text-white dark:bg-emerald-900/40 dark:hover:bg-gold-600 text-emerald-700 dark:text-emerald-300 font-medium px-2.5 py-1 rounded-lg transition"
                                           title="Edit">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            Edit
                                        </a>
                                        {{-- Soft Delete --}}
                                        <form method="POST" action="{{ route('admin.financial-categories.destroy', $cat) }}" class="inline"
                                              onsubmit="return confirm('Delete this category? It can be restored later.')">
                                            @csrf @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex items-center gap-1 text-xs bg-red-100 hover:bg-red-200 dark:bg-red-900/40 dark:hover:bg-red-900/70 text-red-700 dark:text-red-300 font-medium px-2.5 py-1 rounded-lg transition"
                                                    title="Delete">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                Delete
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-gray-500 dark:text-gray-400">
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