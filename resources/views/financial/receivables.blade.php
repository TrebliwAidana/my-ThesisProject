@extends('layouts.app')

@section('title', 'Accounts Receivable')
@section('page-title', 'Accounts Receivable')

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

    {{-- Summary Card --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow border border-gold-200 dark:border-gold-800 p-5">
        <p class="text-xs font-semibold uppercase tracking-widest text-gray-500 dark:text-gray-400">Total Outstanding Receivables</p>
        <p class="mt-1 text-2xl font-bold text-red-600">₱{{ number_format($totalOutstanding, 2) }}</p>
        <p class="text-xs text-gray-400 mt-1">Unpaid balance from all pending/partial/overdue receivables</p>
    </div>

    {{-- Actions --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow border border-gold-200 dark:border-gold-800 p-5">
        <a href="{{ route('financial.income.create') }}?is_receivable=1"
           class="inline-flex items-center gap-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
            + New Receivable
        </a>
    </div>

    {{-- Receivables Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow border border-gold-200 dark:border-gold-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300 uppercase text-xs tracking-wider">
                    <tr>
                        <th class="px-5 py-3 text-left">Reference</th>
                        <th class="px-5 py-3 text-left">Customer</th>
                        <th class="px-5 py-3 text-left">Description</th>
                        <th class="px-5 py-3 text-right">Total</th>
                        <th class="px-5 py-3 text-right">Paid</th>
                        <th class="px-5 py-3 text-right">Remaining</th>
                        <th class="px-5 py-3 text-left">Due Date</th>
                        <th class="px-5 py-3 text-center">Status</th>
                        <th class="px-5 py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($receivables as $rec)
                    @php
                        $remaining = $rec->total_amount - $rec->paid_amount;
                        $statusColors = [
                            'pending' => 'bg-amber-100 text-amber-700',
                            'partial' => 'bg-yellow-100 text-yellow-800',
                            'paid'    => 'bg-green-100 text-green-800',
                            'overdue' => 'bg-red-100 text-red-800',
                        ];
                    @endphp
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                        <td class="px-5 py-3 font-mono text-xs">{{ $rec->reference_no }}</td>
                        <td class="px-5 py-3">{{ $rec->customer_name ?? '—' }}</td>
                        <td class="px-5 py-3 max-w-xs truncate">{{ $rec->description }}</td>
                        <td class="px-5 py-3 text-right">₱{{ number_format($rec->total_amount, 2) }}</td>
                        <td class="px-5 py-3 text-right">₱{{ number_format($rec->paid_amount, 2) }}</td>
                        <td class="px-5 py-3 text-right font-semibold {{ $remaining > 0 ? 'text-red-600' : 'text-green-600' }}">
                            ₱{{ number_format($remaining, 2) }}
                        </td>
                        <td class="px-5 py-3 whitespace-nowrap">
                            @if($rec->due_date)
                                {{ \Carbon\Carbon::parse($rec->due_date)->format('M d, Y') }}
                                @if($rec->due_date < now()->toDateString() && $rec->status !== 'paid')
                                    <span class="ml-1 text-red-500 text-xs">(Overdue)</span>
                                @endif
                            @else
                                —
                            @endif
                        </td>
                        <td class="px-5 py-3 text-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $statusColors[$rec->status] ?? 'bg-gray-100 text-gray-700' }}">
                                {{ ucfirst($rec->status) }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-center">
                            <a href="{{ route('financial.receivable.show', $rec) }}"
                               class="text-xs text-blue-600 hover:text-blue-800 font-medium">View</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="px-5 py-10 text-center text-gray-400 dark:text-gray-500">
                            No receivables found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($receivables->hasPages())
        <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700">
            {{ $receivables->links() }}
        </div>
        @endif
    </div>
</div>
@endsection