@extends('layouts.app')

@section('title', 'Financial Trash')
@section('page-title', 'Trash – Financial Transactions')

@section('content')
<div class="space-y-6">

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

    <div class="flex justify-between items-center mb-4">
        <a href="{{ route('financial.index') }}" class="text-blue-600 hover:underline text-sm">← Back to Records</a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow border border-gold-200 dark:border-gold-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700 text-gray-600 dark:text-gray-300 uppercase text-xs tracking-wider">
                    <tr>
                        <th class="px-5 py-3 text-left">Date</th>
                        <th class="px-5 py-3 text-left">Description</th>
                        <th class="px-5 py-3 text-left">Category</th>
                        <th class="px-5 py-3 text-left">Type</th>
                        <th class="px-5 py-3 text-right">Amount</th>
                        <th class="px-5 py-3 text-left">Status</th>
                        <th class="px-5 py-3 text-left">Deleted At</th>
                        <th class="px-5 py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($transactions as $tx)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-5 py-3">{{ $tx->transaction_date->format('M d, Y') }}</td>
                        <td class="px-5 py-3 font-medium">{{ $tx->description }}</td>
                        <td class="px-5 py-3 text-gray-500">{{ $tx->category ?? '—' }}</td>
                        <td class="px-5 py-3">
                            @if($tx->type === 'income')
                                <span class="text-emerald-600 font-semibold">Income</span>
                            @else
                                <span class="text-red-500 font-semibold">Expense</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right font-semibold {{ $tx->type === 'income' ? 'text-emerald-600' : 'text-red-500' }}">
                            {{ $tx->type === 'income' ? '+' : '-' }}{{ number_format($tx->amount, 2) }}
                        </td>
                        <td class="px-5 py-3">
                            @php
                                $statusColors = [
                                    'pending'  => 'bg-amber-100 text-amber-700',
                                    'audited'  => 'bg-blue-100 text-blue-700',
                                    'approved' => 'bg-emerald-100 text-emerald-700',
                                    'rejected' => 'bg-red-100 text-red-700',
                                ];
                            @endphp
                            <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $statusColors[$tx->status] ?? '' }}">
                                {{ ucfirst($tx->status) }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-xs text-gray-500">{{ $tx->deleted_at->format('M d, Y H:i') }}</td>
                        <td class="px-5 py-3 text-center">
                            <div class="flex items-center justify-center gap-2">
                                {{-- Restore --}}
                                <form method="POST" action="{{ route('financial.restore', $tx->id) }}" class="inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="text-xs text-blue-600 hover:underline">Restore</button>
                                </form>
                                {{-- Force Delete --}}
                                <form method="POST" action="{{ route('financial.force-delete', $tx->id) }}" 
                                      onsubmit="return confirm('Permanently delete this transaction and all its attached documents?')" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-xs text-red-600 hover:underline">Delete Permanently</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-5 py-10 text-center text-gray-400">No deleted transactions.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
        <div class="px-5 py-4 border-t border-gray-100 dark:border-gray-700">
            {{ $transactions->links() }}
        </div>
        @endif
    </div>
</div>
@endsection