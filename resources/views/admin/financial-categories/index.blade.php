@extends('layouts.app')

@section('title', 'Financial Categories')
@section('page-title', 'Financial Categories')

@section('content')
<div class="space-y-6">

    {{-- Header & New Category Button --}}
    <div class="flex flex-wrap justify-between items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Financial Categories</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Manage categories used in income and expense transactions.</p>
        </div>
        <a href="{{ route('admin.financial-categories.create') }}"
        class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-gold-500 text-white text-sm font-semibold px-4 py-2 rounded-lg transition shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            New Category
        </a>
    </div>

    {{-- Flash Messages --}}
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

    {{-- Filters Card (only search and type) --}}
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
                    <option value="income"  {{ request('type') === 'income'  ? 'selected' : '' }}>Income</option>
                    <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>Expense</option>
                    <option value="both"    {{ request('type') === 'both'    ? 'selected' : '' }}>Both</option>
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
                                        'income'  => 'bg-green-100 text-green-700 dark:bg-green-900/50 dark:text-green-300',
                                        'expense' => 'bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-300',
                                        default   => 'bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300',
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
                                        {{-- Edit --}}
                                        <button @click="openEditCategory({{ $cat->id }}, '{{ addslashes($cat->name) }}', '{{ $cat->type }}', '{{ addslashes($cat->description ?? '') }}')"
                                                class="inline-flex items-center gap-1 text-xs bg-emerald-100 hover:bg-gold-500 hover:text-white dark:bg-emerald-900/40 dark:hover:bg-gold-600 text-emerald-700 dark:text-emerald-300 font-medium px-2.5 py-1 rounded-lg transition"
                                                title="Edit">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            Edit
                                        </button>
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

{{-- CREATE MODAL (Alpine) --}}
<div x-show="openCreateModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background: rgba(0,0,0,0.6); backdrop-filter: blur(8px);" @click.self="openCreateModal = false" @keydown.window.escape="openCreateModal = false">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md overflow-hidden border border-gold-200 dark:border-gold-800">
        <div class="bg-gradient-to-r from-emerald-600 to-emerald-700 dark:from-emerald-800 dark:to-emerald-900 px-6 py-4 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-white">New Financial Category</h2>
            <button @click="openCreateModal = false" class="text-white/70 hover:text-white transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" action="{{ route('admin.financial-categories.store') }}" class="p-6 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required maxlength="100"
                       class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Applies To <span class="text-red-500">*</span></label>
                <select name="type" required
                        class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-transparent">
                    <option value="both"    {{ old('type') === 'both'    ? 'selected' : '' }}>Both (Income & Expense)</option>
                    <option value="income"  {{ old('type') === 'income'  ? 'selected' : '' }}>Income only</option>
                    <option value="expense" {{ old('type') === 'expense' ? 'selected' : '' }}>Expense only</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Description</label>
                <input type="text" name="description" value="{{ old('description') }}" maxlength="255"
                       class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-transparent">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 bg-emerald-600 hover:bg-gold-500 text-white font-semibold py-2 rounded-lg transition">Create Category</button>
                <button type="button" @click="openCreateModal = false" class="flex-1 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-semibold py-2 rounded-lg transition">Cancel</button>
            </div>
        </form>
    </div>
</div>

{{-- EDIT MODAL (Alpine) --}}
<div x-show="openEditModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" style="background: rgba(0,0,0,0.6); backdrop-filter: blur(8px);" @click.self="openEditModal = false" @keydown.window.escape="openEditModal = false">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md overflow-hidden border border-gold-200 dark:border-gold-800">
        <div class="bg-gradient-to-r from-emerald-600 to-emerald-700 dark:from-emerald-800 dark:to-emerald-900 px-6 py-4 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-white">Edit Financial Category</h2>
            <button @click="openEditModal = false" class="text-white/70 hover:text-white transition">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <form method="POST" :action="editAction" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" x-model="editName" required maxlength="100"
                       class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Applies To <span class="text-red-500">*</span></label>
                <select name="type" x-model="editType" required
                        class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-transparent">
                    <option value="both">Both (Income & Expense)</option>
                    <option value="income">Income only</option>
                    <option value="expense">Expense only</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Description</label>
                <input type="text" name="description" x-model="editDescription" maxlength="255"
                       class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-transparent">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 bg-emerald-600 hover:bg-gold-500 text-white font-semibold py-2 rounded-lg transition">Save Changes</button>
                <button type="button" @click="openEditModal = false" class="flex-1 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-semibold py-2 rounded-lg transition">Cancel</button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('financialCategories', () => ({
            openCreateModal: false,
            openEditModal: false,
            editAction: '',
            editName: '',
            editType: '',
            editDescription: '',
            openEditCategory(id, name, type, description) {
                this.editAction = `/admin/financial-categories/${id}`;
                this.editName = name;
                this.editType = type;
                this.editDescription = description;
                this.openEditModal = true;
            }
        }));
    });
</script>
@endpush

@push('styles')
<style>[x-cloak] { display: none !important; }</style>
@endpush
@endsection