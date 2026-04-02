@extends('layouts.app')

@section('title', 'Budgets — VSULHS_SSLG')

@section('content')

{{-- Vanishing Popup Notifications --}}
@if(session('success'))
    <div x-data="{ show: true }" 
         x-init="setTimeout(() => show = false, 5000)"
         x-show="show"
         x-transition:enter="transform transition duration-300 ease-out"
         x-transition:enter-start="translate-x-full opacity-0"
         x-transition:enter-end="translate-x-0 opacity-100"
         x-transition:leave="transform transition duration-200 ease-in"
         x-transition:leave-start="translate-x-0 opacity-100"
         x-transition:leave-end="translate-x-full opacity-0"
         class="fixed top-20 right-4 z-50 w-96 max-w-full rounded-lg shadow-lg overflow-hidden border-l-4 border-green-500 bg-green-50 dark:bg-green-900/30">
        <div class="p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm font-medium text-green-800 dark:text-green-200">
                        {{ session('success') }}
                    </p>
                </div>
                <div class="ml-4 flex-shrink-0">
                    <button @click="show = false" class="inline-flex text-green-500 hover:text-green-600 focus:outline-none">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif

@if(session('error'))
    <div x-data="{ show: true }" 
         x-init="setTimeout(() => show = false, 5000)"
         x-show="show"
         x-transition:enter="transform transition duration-300 ease-out"
         x-transition:enter-start="translate-x-full opacity-0"
         x-transition:enter-end="translate-x-0 opacity-100"
         x-transition:leave="transform transition duration-200 ease-in"
         x-transition:leave-start="translate-x-0 opacity-100"
         x-transition:leave-end="translate-x-full opacity-0"
         class="fixed top-20 right-4 z-50 w-96 max-w-full rounded-lg shadow-lg overflow-hidden border-l-4 border-red-500 bg-red-50 dark:bg-red-900/30">
        <div class="p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm font-medium text-red-800 dark:text-red-200">
                        {{ session('error') }}
                    </p>
                </div>
                <div class="ml-4 flex-shrink-0">
                    <button @click="show = false" class="inline-flex text-red-500 hover:text-red-600 focus:outline-none">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif

{{-- Header --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Budget Management</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage and track budget requests</p>
    </div>
    @if(in_array(Auth::user()->role->name, ['Adviser', 'Officer']))
    <a href="{{ route('budgets.create') }}" 
       class="bg-primary-600 hover:bg-gold-500 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
        + New Budget Request
    </a>
    @endif
</div>

{{-- Stats Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gold-200 dark:border-gold-800 p-4">
        <div class="flex items-center justify-between">
            <div class="w-8 h-8 bg-yellow-50 dark:bg-yellow-900/50 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <span class="text-2xl font-bold text-gray-800 dark:text-white">{{ $statusCounts['pending'] ?? 0 }}</span>
        </div>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Pending</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gold-200 dark:border-gold-800 p-4">
        <div class="flex items-center justify-between">
            <div class="w-8 h-8 bg-blue-50 dark:bg-blue-900/50 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <span class="text-2xl font-bold text-gray-800 dark:text-white">{{ $statusCounts['reviewed'] ?? 0 }}</span>
        </div>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Reviewed</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gold-200 dark:border-gold-800 p-4">
        <div class="flex items-center justify-between">
            <div class="w-8 h-8 bg-green-50 dark:bg-green-900/50 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <span class="text-2xl font-bold text-gray-800 dark:text-white">{{ $statusCounts['approved'] ?? 0 }}</span>
        </div>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Approved</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gold-200 dark:border-gold-800 p-4">
        <div class="flex items-center justify-between">
            <div class="w-8 h-8 bg-red-50 dark:bg-red-900/50 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </div>
            <span class="text-2xl font-bold text-gray-800 dark:text-white">{{ $statusCounts['rejected'] ?? 0 }}</span>
        </div>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Rejected</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gold-200 dark:border-gold-800 p-4">
        <div class="flex items-center justify-between">
            <div class="w-8 h-8 bg-gray-50 dark:bg-gray-700/50 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
            </div>
            <span class="text-2xl font-bold text-gray-800 dark:text-white">{{ $statusCounts['disbursed'] ?? 0 }}</span>
        </div>
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Disbursed</p>
    </div>
</div>

{{-- Filters --}}
<div class="bg-white dark:bg-gray-800 rounded-xl border border-gold-200 dark:border-gold-800 p-5 mb-6">
    <form method="GET" action="{{ route('budgets.index') }}" class="flex flex-wrap gap-3">
        <div class="flex-1 min-w-[200px]">
            <input type="text" name="search" placeholder="Search by title..." 
                   value="{{ request('search') }}"
                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold-500 focus:border-transparent dark:bg-gray-700 dark:text-white text-sm">
        </div>
        <select name="status" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold-500 dark:bg-gray-700 dark:text-white text-sm">
            <option value="">All Status</option>
            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
            <option value="reviewed" {{ request('status') == 'reviewed' ? 'selected' : '' }}>Reviewed</option>
            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Approved</option>
            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
            <option value="disbursed" {{ request('status') == 'disbursed' ? 'selected' : '' }}>Disbursed</option>
        </select>
        <select name="category" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-gold-500 dark:bg-gray-700 dark:text-white text-sm">
            <option value="">All Categories</option>
            @foreach($categories as $category)
            <option value="{{ $category->name }}" {{ request('category') == $category->name ? 'selected' : '' }}>
                {{ $category->name }}
            </option>
            @endforeach
        </select>
        <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-gold-500 text-white rounded-lg text-sm transition">
            Filter
        </button>
        @if(request()->anyFilled(['search', 'status', 'category']))
        <a href="{{ route('budgets.index') }}" class="px-4 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg text-sm transition">
            Clear
        </a>
        @endif
    </form>
</div>

{{-- Budgets Table --}}
<div class="bg-white dark:bg-gray-800 rounded-xl border border-gold-200 dark:border-gold-800 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700 border-b border-gold-200 dark:border-gray-600">
                叉
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Title</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Amount</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Category</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Status</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Requested By</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Date</th>
                    <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Actions</th>
                </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($budgets as $budget)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                    <td class="px-5 py-3 font-medium text-gray-900 dark:text-white">{{ Str::limit($budget->title, 40) }}</td>
                    <td class="px-5 py-3 text-gray-600 dark:text-gray-400 font-mono">₱{{ number_format($budget->amount, 2) }}</td>
                    <td class="px-5 py-3">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300">
                            {{ $budget->category }}
                        </span>
                    </td>
                    <td class="px-5 py-3">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium {{ $budget->status_color }}">
                            <span class="w-1.5 h-1.5 rounded-full 
                                {{ $budget->status == 'pending' ? 'bg-yellow-500' : '' }}
                                {{ $budget->status == 'reviewed' ? 'bg-blue-500' : '' }}
                                {{ $budget->status == 'approved' ? 'bg-green-500' : '' }}
                                {{ $budget->status == 'rejected' ? 'bg-red-500' : '' }}
                                {{ $budget->status == 'disbursed' ? 'bg-gray-500' : '' }}">
                            </span>
                            {{ ucfirst($budget->status) }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-gray-600 dark:text-gray-400">{{ $budget->requester->full_name ?? 'N/A' }}</td>
                    <td class="px-5 py-3 text-gray-500 dark:text-gray-400 text-xs">{{ $budget->created_at->format('M d, Y') }}</td>
                    <td class="px-5 py-3 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('budgets.show', $budget) }}" 
                               class="text-primary-600 hover:text-primary-800 dark:text-primary-400 dark:hover:text-primary-300" 
                               title="View Details">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </a>
                            @if($budget->status == 'pending' && $budget->requested_by == Auth::id())
                            <a href="{{ route('budgets.edit', $budget) }}" 
                               class="text-gray-600 hover:text-primary-600 dark:text-gray-400 dark:hover:text-primary-400" 
                               title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>
                            @endif
                            @if(in_array($budget->status, ['pending', 'reviewed']) && in_array(Auth::user()->role->name, ['Adviser', 'Officer']))
                            <a href="{{ route('budgets.review', $budget) }}" 
                               class="text-green-600 hover:text-green-800 dark:text-green-400 dark:hover:text-green-300" 
                               title="Review">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-5 py-10 text-center text-gray-400 dark:text-gray-500 text-sm italic">
                        No budget requests found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($budgets->hasPages())
    <div class="px-5 py-3 border-t border-gray-100 dark:border-gold-800">
        {{ $budgets->links() }}
    </div>
    @endif
</div>

@endsection