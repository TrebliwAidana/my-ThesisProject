@extends('layouts.app')

@section('title', 'Financial Records')
@section('page-title', 'Financial Records')

@section('content')
<div class="space-y-6">

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="p-4 bg-green-100 border border-green-300 text-green-800 rounded-xl text-sm">
            ✅ {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-red-100 border border-red-300 text-red-800 rounded-xl text-sm">
            ❌ {{ session('error') }}
        </div>
    @endif

    {{-- Summary Cards (Responsive Grid) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Balance --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow border border-gold-200 dark:border-gold-800 p-5">
            <p class="text-xs font-semibold uppercase tracking-widest text-gray-500 dark:text-gray-400">Balance</p>
            <p class="mt-1 text-2xl font-bold {{ $balance >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                ₱{{ number_format($balance, 2) }}
            </p>
            <p class="text-xs text-gray-400 mt-1">Approved transactions only</p>
        </div>
        {{-- Income --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow border border-gold-200 dark:border-gold-800 p-5">
            <p class="text-xs font-semibold uppercase tracking-widest text-gray-500 dark:text-gray-400">Total Income</p>
            <p class="mt-1 text-2xl font-bold text-emerald-600">₱{{ number_format($incomeTotal, 2) }}</p>
            <p class="text-xs text-gray-400 mt-1">Approved income</p>
        </div>
        {{-- Expense --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow border border-gold-200 dark:border-gold-800 p-5">
            <p class="text-xs font-semibold uppercase tracking-widest text-gray-500 dark:text-gray-400">Total Expenses</p>
            <p class="mt-1 text-2xl font-bold text-red-500">₱{{ number_format($expenseTotal, 2) }}</p>
            <p class="text-xs text-gray-400 mt-1">Approved expenses</p>
        </div>
        {{-- Pending --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow border border-gold-200 dark:border-gold-800 p-5">
            <p class="text-xs font-semibold uppercase tracking-widest text-gray-500 dark:text-gray-400">Pending</p>
            <p class="mt-1 text-2xl font-bold text-amber-500">{{ $pendingCount }}</p>
            <p class="text-xs text-gray-400 mt-1">Awaiting approval</p>
        </div>
    </div>

    {{-- Actions + Filters (Responsive Stack) --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow border border-gold-200 dark:border-gold-800 p-5">
        <div class="flex flex-wrap gap-3 mb-4">
            <a href="{{ route('financial.income.create') }}"
               class="inline-flex items-center justify-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition w-full sm:w-auto">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Add Income
            </a>
            <a href="{{ route('financial.expense.create') }}"
               class="inline-flex items-center justify-center gap-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition w-full sm:w-auto">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Add Expense
            </a>
        </div>

        {{-- Filters (Collapsible on mobile? We'll keep it simple with wrap) --}}
        <form method="GET" action="{{ route('financial.index') }}" class="flex flex-col sm:flex-row flex-wrap gap-3">
            <div class="flex-1 min-w-[180px]">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search description..."
                       class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
            </div>

            <div class="flex flex-wrap gap-3">
                <select name="type" class="border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                    <option value="">All Types</option>
                    <option value="income"  {{ request('type') === 'income'  ? 'selected' : '' }}>Income</option>
                    <option value="expense" {{ request('type') === 'expense' ? 'selected' : '' }}>Expense</option>
                </select>

                <select name="status" class="border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                    <option value="">All Statuses</option>
                    <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>Pending</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>

                @if($categories->count())
                <select name="category" class="border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
                @endif

                <div class="flex gap-2">
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                           class="w-32 border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                    <span class="self-center text-gray-400">—</span>
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                           class="w-32 border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                </div>

                <button type="submit" class="bg-gold-500 hover:bg-gold-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition whitespace-nowrap">
                    Filter
                </button>
                <a href="{{ route('financial.index') }}" class="bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-semibold px-4 py-2 rounded-lg transition whitespace-nowrap">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Transactions: Desktop Table + Mobile Cards --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow border border-gold-200 dark:border-gold-800 overflow-hidden">
        {{-- Desktop Table (Hidden on mobile) --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300 uppercase text-xs tracking-wider">
                    <tr>
                        <th class="px-5 py-3 text-left">Date</th>
                        <th class="px-5 py-3 text-left">Description</th>
                        <th class="px-5 py-3 text-left">Category</th>
                        <th class="px-5 py-3 text-left">Type</th>
                        <th class="px-5 py-3 text-right">Amount</th>
                        <th class="px-5 py-3 text-left">Status</th>
                        <th class="px-5 py-3 text-left">Submitted By</th>
                        <th class="px-5 py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($transactions as $tx)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                        <td class="px-5 py-3 text-gray-600 dark:text-gray-300 whitespace-nowrap">
                            {{ $tx->transaction_date->format('M d, Y') }}
                        </td>
                        <td class="px-5 py-3 text-gray-800 dark:text-white font-medium max-w-xs truncate">
                            {{ $tx->description }}
                        </td>
                        <td class="px-5 py-3 text-gray-500 dark:text-gray-400">
                            {{ $tx->category ?? '—' }}
                        </td>
                        <td class="px-5 py-3">
                            @if($tx->type === 'income')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">Income</span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700">Expense</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right font-semibold {{ $tx->type === 'income' ? 'text-emerald-600' : 'text-red-500' }}">
                            {{ $tx->type === 'income' ? '+' : '-' }}{{ $tx->formatted_amount }}
                        </td>
                        <td class="px-5 py-3">
                            @php
                                $statusColors = [
                                    'pending'  => 'bg-amber-100 text-amber-700',
                                    'approved' => 'bg-emerald-100 text-emerald-700',
                                    'rejected' => 'bg-red-100 text-red-700',
                                ];
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $statusColors[$tx->status] ?? '' }}">
                                {{ ucfirst($tx->status) }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-gray-500 dark:text-gray-400 text-xs">
                            {{ $tx->user->full_name ?? '—' }}
                        </td>
                        <td class="px-5 py-3 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('financial.show', $tx->id) }}"
                                   class="text-xs text-blue-600 hover:text-blue-800 font-medium">View</a>

                                @if($tx->status === 'pending')
                                    <a href="{{ route('financial.edit', $tx->id) }}"
                                       class="text-xs text-gold-600 hover:text-gold-800 font-medium">Edit</a>

                                    <form method="POST" action="{{ route('financial.approve', $tx->id) }}" class="inline">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="text-xs text-emerald-600 hover:text-emerald-800 font-medium">Approve</button>
                                    </form>

                                    <form method="POST" action="{{ route('financial.reject', $tx->id) }}" class="inline">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-medium">Reject</button>
                                    </form>

                                    <form method="POST" action="{{ route('financial.destroy', $tx->id) }}"
                                          class="inline" onsubmit="return confirm('Delete this transaction?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-xs text-gray-400 hover:text-red-600 font-medium">Delete</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-5 py-10 text-center text-gray-400 dark:text-gray-500">
                            No transactions found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile Cards (Visible only on small screens) --}}
        <div class="md:hidden divide-y divide-gray-100 dark:divide-gray-700">
            @forelse($transactions as $tx)
            <div class="p-4 space-y-3">
                {{-- Header: Description + Amount --}}
                <div class="flex justify-between items-start">
                    <div class="flex-1 min-w-0">
                        <h3 class="text-base font-semibold text-gray-800 dark:text-white truncate">
                            {{ $tx->description }}
                        </h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            {{ $tx->transaction_date->format('M d, Y') }}
                        </p>
                    </div>
                    <div class="text-right">
                        <span class="text-lg font-bold {{ $tx->type === 'income' ? 'text-emerald-600' : 'text-red-500' }}">
                            {{ $tx->type === 'income' ? '+' : '-' }}{{ $tx->formatted_amount }}
                        </span>
                    </div>
                </div>

                {{-- Meta Grid --}}
                <div class="grid grid-cols-2 gap-2 text-sm">
                    <div>
                        <span class="text-xs text-gray-400 block">Category</span>
                        <span class="font-medium">{{ $tx->category ?? '—' }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-gray-400 block">Type</span>
                        @if($tx->type === 'income')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">Income</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700">Expense</span>
                        @endif
                    </div>
                    <div>
                        <span class="text-xs text-gray-400 block">Status</span>
                        @php
                            $statusColors = [
                                'pending'  => 'bg-amber-100 text-amber-700',
                                'approved' => 'bg-emerald-100 text-emerald-700',
                                'rejected' => 'bg-red-100 text-red-700',
                            ];
                        @endphp
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $statusColors[$tx->status] ?? '' }}">
                            {{ ucfirst($tx->status) }}
                        </span>
                    </div>
                    <div>
                        <span class="text-xs text-gray-400 block">Submitted By</span>
                        <span class="font-medium">{{ $tx->user->full_name ?? '—' }}</span>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex flex-wrap gap-2 pt-1 border-t border-gray-100 dark:border-gray-700">
                    <a href="{{ route('financial.show', $tx->id) }}"
                       class="text-xs bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 px-3 py-1 rounded-full font-medium">View</a>

                    @if($tx->status === 'pending')
                        <a href="{{ route('financial.edit', $tx->id) }}"
                           class="text-xs bg-gold-50 dark:bg-gold-900/30 text-gold-700 dark:text-gold-300 px-3 py-1 rounded-full font-medium">Edit</a>

                        <form method="POST" action="{{ route('financial.approve', $tx->id) }}" class="inline">
                            @csrf @method('PATCH')
                            <button type="submit" class="text-xs bg-emerald-50 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 px-3 py-1 rounded-full font-medium">Approve</button>
                        </form>

                        <form method="POST" action="{{ route('financial.reject', $tx->id) }}" class="inline">
                            @csrf @method('PATCH')
                            <button type="submit" class="text-xs bg-red-50 dark:bg-red-900/30 text-red-700 dark:text-red-300 px-3 py-1 rounded-full font-medium">Reject</button>
                        </form>

                        <form method="POST" action="{{ route('financial.destroy', $tx->id) }}"
                              class="inline" onsubmit="return confirm('Delete this transaction?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-3 py-1 rounded-full font-medium">Delete</button>
                        </form>
                    @endif
                </div>
            </div>
            @empty
            <div class="p-8 text-center text-gray-400 dark:text-gray-500">
                No transactions found.
            </div>
            @endforelse
        </div>

        {{-- Pagination (Responsive) --}}
        @if($transactions->hasPages())
        <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700">
            {{ $transactions->links() }}
        </div>
        @endif
    </div>

</div>
@endsection