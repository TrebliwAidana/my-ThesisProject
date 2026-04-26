@extends('layouts.app')

@section('title', 'Financial Records')
@section('page-title', 'Financial Records')

@section('content')
<div class="space-y-6">

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="p-4 bg-green-100 border border-green-300 text-green-800 rounded-xl text-sm">✅ {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-red-100 border border-red-300 text-red-800 rounded-xl text-sm">❌ {{ session('error') }}</div>
    @endif

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
        {{-- Balance --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow border border-gold-200 dark:border-gold-800 p-5">
            <p class="text-xs font-semibold uppercase tracking-widest text-gray-500 dark:text-gray-400">Balance</p>
            <p class="mt-1 text-2xl font-bold {{ $balance >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                ₱{{ number_format($balance, 2) }}
            </p>
            <p class="text-xs text-gray-400 mt-1">Income + Paid receivables − Expenses</p>
        </div>
        {{-- Total Income --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow border border-gold-200 dark:border-gold-800 p-5">
            <p class="text-xs font-semibold uppercase tracking-widest text-gray-500 dark:text-gray-400">Total Income</p>
            <p class="mt-1 text-2xl font-bold text-emerald-600">₱{{ number_format($totalIncome, 2) }}</p>
            <p class="text-xs text-gray-400 mt-1">
                Cash: ₱{{ number_format($incomeTotal, 2) }}
                @if($receivablePaidTotal > 0)
                    + Collected: ₱{{ number_format($receivablePaidTotal, 2) }}
                @endif
            </p>
        </div>
        {{-- Expenses --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow border border-gold-200 dark:border-gold-800 p-5">
            <p class="text-xs font-semibold uppercase tracking-widest text-gray-500 dark:text-gray-400">Total Expenses</p>
            <p class="mt-1 text-2xl font-bold text-red-500">₱{{ number_format($expenseTotal, 2) }}</p>
            <p class="text-xs text-gray-400 mt-1">Approved expenses</p>
        </div>
        {{-- Pending --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow border border-gold-200 dark:border-gold-800 p-5">
            <p class="text-xs font-semibold uppercase tracking-widest text-gray-500 dark:text-gray-400">Pending</p>
            <p class="mt-1 text-2xl font-bold text-amber-500">{{ $pendingCount }}</p>
            <p class="text-xs text-gray-400 mt-1">Awaiting audit</p>
        </div>
        {{-- Audited --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow border border-gold-200 dark:border-gold-800 p-5">
            <p class="text-xs font-semibold uppercase tracking-widest text-gray-500 dark:text-gray-400">Audited</p>
            <p class="mt-1 text-2xl font-bold text-blue-500">{{ $auditedCount }}</p>
            <p class="text-xs text-gray-400 mt-1">Awaiting approval</p>
        </div>
    </div>

    {{-- Actions + Filters --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow border border-gold-200 dark:border-gold-800 p-5">
        @php
            $user = auth()->user();
            $canCreate     = $user->hasPermission('financial.create') || $user->role->level === 1;
            $canViewReports = $user->hasPermission('reports.view')   || $user->role->level === 1;
        @endphp

        <div class="flex flex-wrap gap-3 mb-4">
            @if($canCreate)
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
                <a href="{{ route('financial.receivable.create') }}"
                   class="inline-flex items-center justify-center gap-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition w-full sm:w-auto">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Add Receivable
                </a>
            @endif

            @if($canViewReports)
                <a href="{{ route('financial.report.form') }}"
                   class="inline-flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition w-full sm:w-auto">
                    📊 Generate Report
                </a>
            @endif

            @if($user->role->level === 1 || $user->hasPermission('financial.manage'))
                <a href="{{ route('financial.trash') }}"
                   class="inline-flex items-center gap-1.5 bg-gray-600 hover:bg-gray-700 text-white text-sm font-semibold px-3 py-2 rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Trash
                </a>
            @endif
        </div>

        {{-- Filters --}}
        <form method="GET" action="{{ route('financial.index') }}" class="flex flex-col sm:flex-row flex-wrap gap-3">
            <div class="flex-1 min-w-[180px]">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search description..."
                       class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
            </div>
            <div class="flex flex-wrap gap-3">
                <select name="type" class="border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                    <option value="">All Types</option>
                    <option value="income"     {{ request('type') === 'income'     ? 'selected' : '' }}>Income</option>
                    <option value="expense"    {{ request('type') === 'expense'    ? 'selected' : '' }}>Expense</option>
                    <option value="receivable" {{ request('type') === 'receivable' ? 'selected' : '' }}>Receivable</option>
                </select>
                <select name="status" class="border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                    <option value="">All Statuses</option>
                    <option value="pending"  {{ request('status') === 'pending'  ? 'selected' : '' }}>Pending</option>
                    <option value="audited"  {{ request('status') === 'audited'  ? 'selected' : '' }}>Audited</option>
                    <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved</option>
                    <option value="paid"     {{ request('status') === 'paid'     ? 'selected' : '' }}>Paid</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
                <select name="category" class="border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                    @endforeach
                </select>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       class="border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                       class="border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                <label class="inline-flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 cursor-pointer">
                    <input type="checkbox" name="show_approved" value="1" {{ request('show_approved') ? 'checked' : '' }}
                           class="rounded border-gray-300 dark:border-gray-600">
                    Show approved/paid
                </label>
                <button type="submit" class="bg-gold-500 hover:bg-gold-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                    Filter
                </button>
                @if(request()->hasAny(['search','type','status','category','date_from','date_to','show_approved']))
                    <a href="{{ route('financial.index') }}" class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 px-2 py-2">
                        ✕ Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow border border-gold-200 dark:border-gold-800 overflow-hidden">

        {{-- Desktop Table --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300 uppercase text-xs tracking-wider">
                    <tr>
                        <th class="px-5 py-3 text-left">Date</th>
                        <th class="px-5 py-3 text-left">Description</th>
                        <th class="px-5 py-3 text-left">Category</th>
                        <th class="px-5 py-3 text-center">Type</th>
                        <th class="px-5 py-3 text-right">Amount</th>
                        <th class="px-5 py-3 text-center">Status</th>
                        <th class="px-5 py-3 text-left">Submitted By</th>
                        <th class="px-5 py-3 text-left">Auditor</th>
                        <th class="px-5 py-3 text-left">Approver</th>
                        <th class="px-5 py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($transactions as $tx)
                    @php
                        $user      = auth()->user();
                        $canEdit   = ($user->hasPermission('financial.edit')   || $user->role->level === 1) && $tx->status === 'pending';
                        $canDelete = ($user->hasPermission('financial.delete') || $user->role->level === 1);
                        $canAudit  = ($user->hasPermission('financial.audit')  || $user->role->level === 1) && $tx->status === 'pending';
                        $canApprove = ($user->hasPermission('financial.approve') || $user->role->level === 1) && $tx->status === 'audited';
                        $canReject  = $canApprove;

                        $statusColors = [
                            'pending'  => 'bg-amber-100 text-amber-700',
                            'audited'  => 'bg-blue-100 text-blue-700',
                            'approved' => 'bg-emerald-100 text-emerald-700',
                            'paid'     => 'bg-green-100 text-green-800',
                            'rejected' => 'bg-red-100 text-red-700',
                        ];
                    @endphp
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition {{ $tx->type === 'receivable' && $tx->isOverdue() ? 'border-l-2 border-red-400' : '' }}">
                        <td class="px-5 py-3 text-gray-500 dark:text-gray-400 whitespace-nowrap text-xs">
                            {{ $tx->transaction_date->format('M d, Y') }}
                        </td>
                        <td class="px-5 py-3">
                            <p class="font-medium text-gray-800 dark:text-white truncate max-w-[200px]">{{ $tx->description }}</p>
                            @if($tx->type === 'receivable' && $tx->customer_name)
                                <p class="text-xs text-purple-500 mt-0.5">👤 {{ $tx->customer_name }}</p>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-gray-500 dark:text-gray-400 text-xs">{{ $tx->category ?? '—' }}</td>
                        <td class="px-5 py-3 text-center">
                            @if($tx->type === 'income')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">↑ Income</span>
                            @elseif($tx->type === 'expense')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700">↓ Expense</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-purple-100 text-purple-700">⏳ Receivable</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right font-semibold
                            {{ $tx->type === 'income' ? 'text-emerald-600' : ($tx->type === 'expense' ? 'text-red-500' : 'text-purple-600') }}">
                            {{ $tx->type === 'income' ? '+' : ($tx->type === 'expense' ? '-' : '') }}{{ $tx->formatted_amount }}
                        </td>
                        <td class="px-5 py-3 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $statusColors[$tx->status] ?? '' }}">
                                {{ $tx->status === 'paid' ? '✓ Paid' : ucfirst($tx->status) }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-gray-500 dark:text-gray-400 text-xs">{{ $tx->user->full_name ?? '—' }}</td>
                        <td class="px-5 py-3 text-gray-500 dark:text-gray-400 text-xs">{{ $tx->auditor->full_name ?? '—' }}</td>
                        <td class="px-5 py-3 text-gray-500 dark:text-gray-400 text-xs">{{ $tx->approver->full_name ?? '—' }}</td>
                        <td class="px-5 py-3 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <a href="{{ route('financial.show', $tx->id) }}"
                                   class="text-xs text-blue-600 hover:text-blue-800 font-medium">View</a>

                                @if($canEdit)
                                    <a href="{{ route('financial.edit', $tx->id) }}"
                                       class="text-xs text-gold-600 hover:text-gold-800 font-medium">Edit</a>
                                @endif

                                @if($canAudit)
                                    <form method="POST" action="{{ route('financial.audit', $tx->id) }}" class="inline">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="text-xs text-blue-600 hover:text-blue-800 font-medium">Audit</button>
                                    </form>
                                @endif

                                @if($canApprove)
                                    <form method="POST" action="{{ route('financial.approve', $tx->id) }}" class="inline">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="text-xs text-emerald-600 hover:text-emerald-800 font-medium">Approve</button>
                                    </form>
                                @endif

                                @if($canReject)
                                    <form method="POST" action="{{ route('financial.reject', $tx->id) }}" class="inline">
                                        @csrf @method('PATCH')
                                        <button type="submit" class="text-xs text-red-500 hover:text-red-700 font-medium">Reject</button>
                                    </form>
                                @endif

                                @if($canDelete)
                                    <form method="POST" action="{{ route('financial.destroy', $tx->id) }}"
                                          class="inline"
                                          onsubmit="return confirm(
                                              @if($tx->status === 'approved' || $tx->status === 'paid')
                                                  'This is a finalized transaction. Deleting it will also remove its approval document. Continue?'
                                              @else
                                                  'Delete this transaction?'
                                              @endif
                                          )">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-xs text-gray-400 hover:text-red-600 font-medium">Delete</button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-5 py-10 text-center text-gray-400 dark:text-gray-500">
                            No transactions found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Mobile Cards --}}
        <div class="md:hidden divide-y divide-gray-100 dark:divide-gray-700">
            @forelse($transactions as $tx)
            @php
                $user       = auth()->user();
                $canEdit    = ($user->hasPermission('financial.edit')    || $user->role->level === 1) && $tx->status === 'pending';
                $canDelete  = ($user->hasPermission('financial.delete')  || $user->role->level === 1);
                $canAudit   = ($user->hasPermission('financial.audit')   || $user->role->level === 1) && $tx->status === 'pending';
                $canApprove = ($user->hasPermission('financial.approve') || $user->role->level === 1) && $tx->status === 'audited';
                $canReject  = $canApprove;
                $statusColors = [
                    'pending'  => 'bg-amber-100 text-amber-700',
                    'audited'  => 'bg-blue-100 text-blue-700',
                    'approved' => 'bg-emerald-100 text-emerald-700',
                    'paid'     => 'bg-green-100 text-green-800',
                    'rejected' => 'bg-red-100 text-red-700',
                ];
            @endphp
            <div class="p-4 space-y-3 {{ $tx->type === 'receivable' && $tx->isOverdue() ? 'border-l-2 border-red-400' : '' }}">
                <div class="flex justify-between items-start">
                    <div class="flex-1 min-w-0">
                        <h3 class="text-base font-semibold text-gray-800 dark:text-white truncate">{{ $tx->description }}</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $tx->transaction_date->format('M d, Y') }}</p>
                        @if($tx->type === 'receivable' && $tx->customer_name)
                            <p class="text-xs text-purple-500 mt-0.5">👤 {{ $tx->customer_name }}</p>
                        @endif
                    </div>
                    <span class="text-lg font-bold ml-2
                        {{ $tx->type === 'income' ? 'text-emerald-600' : ($tx->type === 'expense' ? 'text-red-500' : 'text-purple-600') }}">
                        {{ $tx->type === 'income' ? '+' : ($tx->type === 'expense' ? '-' : '') }}{{ $tx->formatted_amount }}
                    </span>
                </div>
                <div class="grid grid-cols-2 gap-2 text-sm">
                    <div>
                        <span class="text-xs text-gray-400 block">Type</span>
                        @if($tx->type === 'income')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">↑ Income</span>
                        @elseif($tx->type === 'expense')
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700">↓ Expense</span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-purple-100 text-purple-700">⏳ Receivable</span>
                        @endif
                    </div>
                    <div>
                        <span class="text-xs text-gray-400 block">Status</span>
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $statusColors[$tx->status] ?? '' }}">
                            {{ $tx->status === 'paid' ? '✓ Paid' : ucfirst($tx->status) }}
                        </span>
                    </div>
                    <div>
                        <span class="text-xs text-gray-400 block">Submitted By</span>
                        <span class="font-medium">{{ $tx->user->full_name ?? '—' }}</span>
                    </div>
                    <div>
                        <span class="text-xs text-gray-400 block">Category</span>
                        <span class="font-medium">{{ $tx->category ?? '—' }}</span>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2 pt-1 border-t border-gray-100 dark:border-gray-700">
                    <a href="{{ route('financial.show', $tx->id) }}"
                       class="text-xs bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 px-3 py-1 rounded-full font-medium">View</a>
                    @if($canEdit)
                        <a href="{{ route('financial.edit', $tx->id) }}"
                           class="text-xs bg-gold-50 dark:bg-gold-900/30 text-gold-700 dark:text-gold-300 px-3 py-1 rounded-full font-medium">Edit</a>
                    @endif
                    @if($canAudit)
                        <form method="POST" action="{{ route('financial.audit', $tx->id) }}" class="inline">
                            @csrf @method('PATCH')
                            <button type="submit" class="text-xs bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 px-3 py-1 rounded-full font-medium">Audit</button>
                        </form>
                    @endif
                    @if($canApprove)
                        <form method="POST" action="{{ route('financial.approve', $tx->id) }}" class="inline">
                            @csrf @method('PATCH')
                            <button type="submit" class="text-xs bg-emerald-50 text-emerald-700 px-3 py-1 rounded-full font-medium">Approve</button>
                        </form>
                    @endif
                    @if($canReject)
                        <form method="POST" action="{{ route('financial.reject', $tx->id) }}" class="inline">
                            @csrf @method('PATCH')
                            <button type="submit" class="text-xs bg-red-50 text-red-700 px-3 py-1 rounded-full font-medium">Reject</button>
                        </form>
                    @endif
                    @if($canDelete)
                        <form method="POST" action="{{ route('financial.destroy', $tx->id) }}" class="inline"
                              onsubmit="return confirm('Delete this transaction?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 px-3 py-1 rounded-full font-medium">Delete</button>
                        </form>
                    @endif
                </div>
            </div>
            @empty
            <div class="p-8 text-center text-gray-400 dark:text-gray-500">No transactions found.</div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($transactions->hasPages())
        <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700">
            {{ $transactions->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
