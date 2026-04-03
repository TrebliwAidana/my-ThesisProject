@extends('layouts.app')

@section('title', 'Budgets — VSULHS_SSLG')

@section('content')
@php
    $statusColors = [
        'draft'     => 'bg-gray-200 text-gray-700 dark:bg-gray-600 dark:text-gray-300',
        'pending'   => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300',
        'reviewed'  => 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300',
        'approved'  => 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300',
        'rejected'  => 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300',
        'disbursed' => 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
    ];
@endphp

<style>
    /* Custom pagination styling */
    .pagination {
        @apply flex justify-center space-x-1;
    }
    .pagination .page-item {
        @apply list-none;
    }
    .pagination .page-link {
        @apply px-3 py-1.5 text-sm rounded-lg transition-all duration-200;
        @apply bg-white text-gray-700 border border-gold-200 dark:bg-gray-800 dark:text-gray-300 dark:border-gold-800;
    }
    .pagination .page-link:hover:not(.active) {
        @apply bg-gold-100 dark:bg-gold-900/30 border-gold-300 dark:border-gold-700;
    }
    .pagination .active .page-link {
        @apply bg-primary-600 text-white border-primary-600 dark:bg-primary-700 dark:border-primary-700;
    }
    .pagination .active .page-link:hover {
        @apply bg-primary-700;
    }
    .pagination .disabled .page-link {
        @apply opacity-50 cursor-not-allowed;
    }

    /* Sorting arrow */
    .sort-arrow {
        display: inline-block;
        margin-left: 4px;
        font-size: 0.7rem;
    }

    /* Table column alignment */
    .budget-table th, .budget-table td {
        vertical-align: middle;
    }
    .budget-table .title-col {
        max-width: 250px;
        word-break: break-word;
    }
    /* For small screens, allow truncation with ellipsis */
    @media (max-width: 768px) {
        .budget-table .title-col {
            max-width: 150px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
    }
</style>

{{-- Emerald Gradient Header --}}
<div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-primary-600 to-primary-700 dark:from-primary-800 dark:to-primary-900 p-6 mb-6">
    <div class="relative z-10">
        <h1 class="text-2xl font-bold text-white">Budget Management</h1>
        <p class="text-primary-100 text-sm mt-1">Manage and track budget requests</p>
    </div>
    <div class="absolute top-0 right-0 -mt-8 -mr-8 w-64 h-64 bg-white/5 rounded-full blur-3xl"></div>
</div>

{{-- Stats Cards (with responsive truncation) --}}
<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 mb-6">
    <!-- Draft -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gold-200 dark:border-gold-800 p-3 shadow-sm min-w-0">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-gray-100 dark:bg-gray-700 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="flex-1 min-w-0 text-right">
                <span class="text-base sm:text-lg md:text-xl font-bold text-gray-800 dark:text-white truncate block">{{ $statusCounts['draft'] ?? 0 }}</span>
            </div>
        </div>
        <p class="text-xs font-medium text-gray-600 dark:text-gray-400 mt-1 truncate">Draft</p>
    </div>
    <!-- Pending -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gold-200 dark:border-gold-800 p-3 shadow-sm min-w-0">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-yellow-50 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="flex-1 min-w-0 text-right">
                <span class="text-base sm:text-lg md:text-xl font-bold text-gray-800 dark:text-white truncate block">{{ $statusCounts['pending'] }}</span>
            </div>
        </div>
        <p class="text-xs font-medium text-gray-600 dark:text-gray-400 mt-1 truncate">Pending</p>
    </div>
    <!-- Reviewed -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gold-200 dark:border-gold-800 p-3 shadow-sm min-w-0">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-blue-50 dark:bg-blue-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="flex-1 min-w-0 text-right">
                <span class="text-base sm:text-lg md:text-xl font-bold text-gray-800 dark:text-white truncate block">{{ $statusCounts['reviewed'] }}</span>
            </div>
        </div>
        <p class="text-xs font-medium text-gray-600 dark:text-gray-400 mt-1 truncate">Reviewed</p>
    </div>
    <!-- Approved -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gold-200 dark:border-gold-800 p-3 shadow-sm min-w-0">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-green-50 dark:bg-green-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <div class="flex-1 min-w-0 text-right">
                <span class="text-base sm:text-lg md:text-xl font-bold text-gray-800 dark:text-white truncate block">{{ $statusCounts['approved'] }}</span>
            </div>
        </div>
        <p class="text-xs font-medium text-gray-600 dark:text-gray-400 mt-1 truncate">Approved</p>
    </div>
    <!-- Rejected -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gold-200 dark:border-gold-800 p-3 shadow-sm min-w-0">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-red-50 dark:bg-red-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </div>
            <div class="flex-1 min-w-0 text-right">
                <span class="text-base sm:text-lg md:text-xl font-bold text-gray-800 dark:text-white truncate block">{{ $statusCounts['rejected'] }}</span>
            </div>
        </div>
        <p class="text-xs font-medium text-gray-600 dark:text-gray-400 mt-1 truncate">Rejected</p>
    </div>
    <!-- Total Approved (currency) with smaller font -->
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gold-200 dark:border-gold-800 p-3 shadow-sm min-w-0">
        <div class="flex items-center gap-2">
            <div class="w-8 h-8 bg-primary-50 dark:bg-primary-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-4 h-4 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="flex-1 min-w-0 text-right">
                {{-- Reduced font size for currency amount --}}
                <span class="text-sm sm:text-base md:text-lg font-bold text-gray-800 dark:text-white truncate block">₱{{ number_format($totalApproved, 2) }}</span>
            </div>
        </div>
        <p class="text-xs font-medium text-gray-600 dark:text-gray-400 mt-1 truncate">Total Approved</p>
    </div>
</div>

{{-- Filters Card --}}
<div class="bg-white dark:bg-gray-800 rounded-xl border border-gold-200 dark:border-gold-800 p-4 mb-6 shadow-sm">
    <form method="GET" action="{{ route('budgets.index') }}" id="filter-form" class="space-y-3">
        <div class="flex flex-wrap gap-2 items-end">
            <div class="flex-1 min-w-[180px]">
                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Search</label>
                <input type="text" name="search" placeholder="Title or requester..." 
                       value="{{ request('search') }}"
                       class="w-full px-3 py-1.5 border border-gold-200 dark:border-gold-800 rounded-lg focus:ring-2 focus:ring-gold-500 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Status</label>
                <select name="status" class="px-3 py-1.5 border border-gold-200 dark:border-gold-800 rounded-lg focus:ring-2 focus:ring-gold-500 text-sm">
                    <option value="">All</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="reviewed" {{ request('status') == 'reviewed' ? 'selected' : '' }}>Reviewed</option>
                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="disbursed" {{ request('status') == 'disbursed' ? 'selected' : '' }}>Disbursed</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Category</label>
                <select name="category" class="px-3 py-1.5 border border-gold-200 dark:border-gold-800 rounded-lg focus:ring-2 focus:ring-gold-500 text-sm">
                    <option value="">All</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->name }}" {{ request('category') == $category->name ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Date From</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="px-3 py-1.5 border border-gold-200 dark:border-gold-800 rounded-lg text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Date To</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="px-3 py-1.5 border border-gold-200 dark:border-gold-800 rounded-lg text-sm">
            </div>
            <div class="flex gap-1">
                <button type="button" onclick="setDateRange('today')" class="px-3 py-1.5 text-xs border border-gold-200 dark:border-gold-800 rounded-lg hover:bg-gold-100 dark:hover:bg-gold-900/30 transition">Today</button>
                <button type="button" onclick="setDateRange('week')" class="px-3 py-1.5 text-xs border border-gold-200 dark:border-gold-800 rounded-lg hover:bg-gold-100 dark:hover:bg-gold-900/30 transition">This Week</button>
                <button type="button" onclick="setDateRange('month')" class="px-3 py-1.5 text-xs border border-gold-200 dark:border-gold-800 rounded-lg hover:bg-gold-100 dark:hover:bg-gold-900/30 transition">This Month</button>
            </div>
        </div>

        <div class="flex flex-wrap justify-between gap-2">
            <div class="flex gap-2">
                <button type="submit" class="bg-primary-600 hover:bg-gold-500 text-white px-4 py-1.5 rounded-lg text-sm font-medium transition">Apply Filters</button>
                @if(request()->anyFilled(['search', 'status', 'category', 'date_from', 'date_to']) || request()->boolean('my'))
                <a href="{{ route('budgets.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-1.5 rounded-lg text-sm transition">Clear</a>
                @endif
            </div>
            <div class="flex gap-2">
                <a href="{{ route('budgets.index', array_merge(request()->except('my'), ['my' => request()->boolean('my') ? 0 : 1])) }}" 
                   class="px-3 py-1.5 rounded-lg text-sm font-medium transition {{ request()->boolean('my') ? 'bg-primary-600 hover:bg-gold-500 text-white' : 'border border-gold-200 text-gray-700 dark:text-gray-300 hover:bg-gray-50' }}">
                    {{ request()->boolean('my') ? 'All Budgets' : 'My Budgets' }}
                </a>
                <a href="{{ route('budgets.export', request()->query()) }}" class="bg-primary-600 hover:bg-gold-500 text-white px-4 py-1.5 rounded-lg text-sm font-medium transition">Export CSV</a>
                @if(in_array(Auth::user()->role->name, ['System Administrator', 'Supreme Admin', 'Adviser', 'Officer']))
                <a href="{{ route('budgets.create') }}" class="inline-flex items-center gap-1 bg-primary-600 hover:bg-gold-500 text-white text-sm font-semibold px-3 py-1.5 rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    New Budget
                </a>
                @endif
            </div>
        </div>
    </form>
</div>

{{-- Filtered results count --}}
<div class="mb-3 text-sm text-gray-500 dark:text-gray-400">
    Showing {{ $budgets->firstItem() ?? 0 }}–{{ $budgets->lastItem() ?? 0 }} of {{ $budgets->total() }} budgets
</div>

{{-- Budgets Table --}}
<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gold-200 dark:border-gold-800 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="budget-table w-full text-sm">
            <thead>
                <tr class="bg-primary-600 dark:bg-primary-700 text-white border-b border-gold-200 dark:border-gold-800">
                    <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider">
                        <a href="{{ route('budgets.index', array_merge(request()->except(['sort', 'order']), ['sort' => 'title', 'order' => request('sort') == 'title' && request('order') == 'asc' ? 'desc' : 'asc'])) }}" class="hover:underline flex items-center gap-1">
                            Title
                            @if(request('sort') == 'title')
                                <span class="sort-arrow">{{ request('order') == 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </a>
                    </th>
                    <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider">
                        <a href="{{ route('budgets.index', array_merge(request()->except(['sort', 'order']), ['sort' => 'amount', 'order' => request('sort') == 'amount' && request('order') == 'asc' ? 'desc' : 'asc'])) }}" class="hover:underline flex items-center gap-1">
                            Amount
                            @if(request('sort') == 'amount')
                                <span class="sort-arrow">{{ request('order') == 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </a>
                    </th>
                    <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider">Category</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider">
                        <a href="{{ route('budgets.index', array_merge(request()->except(['sort', 'order']), ['sort' => 'status', 'order' => request('sort') == 'status' && request('order') == 'asc' ? 'desc' : 'asc'])) }}" class="hover:underline flex items-center gap-1">
                            Status
                            @if(request('sort') == 'status')
                                <span class="sort-arrow">{{ request('order') == 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </a>
                    </th>
                    <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider">Requester</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold uppercase tracking-wider">
                        <a href="{{ route('budgets.index', array_merge(request()->except(['sort', 'order']), ['sort' => 'created_at', 'order' => request('sort') == 'created_at' && request('order') == 'asc' ? 'desc' : 'asc'])) }}" class="hover:underline flex items-center gap-1">
                            Date
                            @if(request('sort') == 'created_at')
                                <span class="sort-arrow">{{ request('order') == 'asc' ? '↑' : '↓' }}</span>
                            @endif
                        </a>
                    </th>
                    <th class="text-right px-5 py-3 text-xs font-semibold uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($budgets as $budget)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                    <td class="px-5 py-3 font-medium text-gray-900 dark:text-white title-col">
                        {{ Str::limit($budget->title, 40) }}
                        @if($budget->attachment_path)
                        <a href="{{ Storage::url($budget->attachment_path) }}" target="_blank" class="inline-block ml-1 text-blue-500 hover:text-blue-700" title="Download attachment">
                            <svg class="w-4 h-4 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                            </svg>
                        </a>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-gray-600 dark:text-gray-400 font-mono">₱{{ number_format($budget->amount, 2) }}</td>
                    <td class="px-5 py-3">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                            {{ $budget->category }}
                        </span>
                    </td>
                    <td class="px-5 py-3">
                        <div class="relative group">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium {{ $statusColors[$budget->status] }}">
                                <span class="w-1.5 h-1.5 rounded-full 
                                    {{ $budget->status == 'draft' ? 'bg-gray-500' : '' }}
                                    {{ $budget->status == 'pending' ? 'bg-yellow-500' : '' }}
                                    {{ $budget->status == 'reviewed' ? 'bg-blue-500' : '' }}
                                    {{ $budget->status == 'approved' ? 'bg-green-500' : '' }}
                                    {{ $budget->status == 'rejected' ? 'bg-red-500' : '' }}
                                    {{ $budget->status == 'disbursed' ? 'bg-gray-500' : '' }}">
                                </span>
                                {{ ucfirst($budget->status) }}
                            </span>
                            @if(in_array($budget->status, ['rejected', 'approved']) && ($budget->review_remarks || $budget->approval_remarks))
                            <div class="absolute bottom-full left-0 mb-1 hidden group-hover:block z-10 bg-gray-800 text-white text-xs rounded px-2 py-1 whitespace-nowrap">
                                {{ $budget->status == 'rejected' ? $budget->review_remarks : $budget->approval_remarks }}
                            </div>
                            @endif
                        </div>
                    </td>
                    <td class="px-5 py-3 text-gray-600 dark:text-gray-400">{{ $budget->requester->full_name ?? 'N/A' }}</td>
                    <td class="px-5 py-3 text-gray-500 dark:text-gray-400 text-xs">{{ $budget->created_at->format('M d, Y') }}</td>
                    <td class="px-5 py-3 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('budgets.show', $budget) }}" class="p-1.5 text-gray-500 hover:text-blue-600" title="View Details">👁️</a>
                            @if($budget->status == 'pending' && $budget->requested_by == Auth::id())
                            <a href="{{ route('budgets.edit', $budget) }}" class="p-1.5 text-gray-500 hover:text-primary-600" title="Edit">✏️</a>
                            @endif
                            @if(in_array($budget->status, ['pending', 'reviewed']) && in_array(Auth::user()->role->name, ['System Administrator', 'Supreme Admin', 'Adviser', 'Officer']))
                            <a href="{{ route('budgets.review', $budget) }}" class="p-1.5 text-gray-500 hover:text-green-600" title="Review">✓</a>
                            @endif
                            @if($budget->status == 'approved' && Auth::user()->role->name == 'System Administrator')
                            <form method="POST" action="{{ route('budgets.disburse', $budget) }}" onsubmit="return confirm('Mark this budget as disbursed?')" class="inline">
                                @csrf
                                <button type="submit" class="p-1.5 text-gray-500 hover:text-purple-600" title="Disburse">💰</button>
                            </form>
                            @endif
                            <a href="{{ route('budgets.copy', $budget) }}" class="p-1.5 text-gray-500 hover:text-yellow-600" title="Copy" onclick="return confirm('Copy this budget?')">📋</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-12 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="text-gray-500 dark:text-gray-400 text-lg">No budget requests found.</p>
                            @if(in_array(Auth::user()->role->name, ['System Administrator', 'Supreme Admin', 'Adviser', 'Officer']))
                            <a href="{{ route('budgets.create') }}" class="text-primary-600 hover:underline">Create your first budget request</a>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($budgets->hasPages())
    <div class="px-5 py-3 border-t border-gray-100 dark:border-gold-800 bg-gray-50 dark:bg-gray-800/50">
        {{ $budgets->links() }}
    </div>
    @endif
</div>

<script>
    function setDateRange(range) {
        const today = new Date();
        let from = '', to = '';
        if (range === 'today') {
            from = to = today.toISOString().split('T')[0];
        } else if (range === 'week') {
            const firstDay = new Date(today);
            firstDay.setDate(today.getDate() - today.getDay());
            const lastDay = new Date(firstDay);
            lastDay.setDate(firstDay.getDate() + 6);
            from = firstDay.toISOString().split('T')[0];
            to = lastDay.toISOString().split('T')[0];
        } else if (range === 'month') {
            from = new Date(today.getFullYear(), today.getMonth(), 1).toISOString().split('T')[0];
            to = new Date(today.getFullYear(), today.getMonth() + 1, 0).toISOString().split('T')[0];
        }
        document.querySelector('input[name="date_from"]').value = from;
        document.querySelector('input[name="date_to"]').value = to;
        document.getElementById('filter-form').submit();
    }

    // Fix flash messages reappearing on back/forward
    window.addEventListener('pageshow', function(event) {
        if (event.persisted) {
            // Clear any lingering flash messages from the DOM
            document.querySelectorAll('.flash-message, .bg-green-50, .bg-red-50, .bg-yellow-50, .bg-blue-50').forEach(el => {
                el.remove();
            });
            // Optionally clear the session flash data via AJAX (if you have a route)
            fetch('/clear-flash-messages', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Content-Type': 'application/json'
                }
            }).catch(err => console.warn('Could not clear flash messages', err));
        }
    });
</script>
@endsection