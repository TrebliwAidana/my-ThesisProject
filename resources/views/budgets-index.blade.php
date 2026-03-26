@extends('layouts.app')
@section('title', 'Budgets — VSULHS_SSLG')

@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Budgets</h1>
        <p class="text-sm text-gray-500 mt-1">Financial records and budget reviews</p>
    </div>
    @if (Auth::user()->role->name === 'Admin')
    <a href="{{ route('budgets.create') }}"
       class="bg-black text-white text-sm font-semibold px-4 py-2 rounded-lg hover:bg-gray-800 transition">
        + New Budget
    </a>
    @endif
</div>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Title</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Total</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Spent</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Remaining</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Period</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Reviewed By</th>
                <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($budgets as $budget)
            @php
                $pct = $budget->total_amount > 0
                    ? round(($budget->spent_amount / $budget->total_amount) * 100)
                    : 0;
            @endphp
            <tr class="hover:bg-gray-50 transition">
                <td class="px-5 py-3 font-medium text-gray-900">{{ $budget->title }}</td>
                <td class="px-5 py-3 text-gray-700">₱{{ number_format($budget->total_amount, 2) }}</td>
                <td class="px-5 py-3 text-gray-700">₱{{ number_format($budget->spent_amount, 2) }}</td>
                <td class="px-5 py-3">
                    <span class="{{ $budget->remaining >= 0 ? 'text-green-700' : 'text-red-600' }} font-semibold">
                        ₱{{ number_format($budget->remaining, 2) }}
                    </span>
                </td>
                <td class="px-5 py-3 text-gray-600 text-xs">
                    {{ $budget->period_start->format('M d') }} – {{ $budget->period_end->format('M d, Y') }}
                </td>
                <td class="px-5 py-3 text-gray-600">
                    {{ $budget->reviewer?->full_name ?? '—' }}
                </td>
                <td class="px-5 py-3 text-right">
                    <div class="flex items-center justify-end gap-2">
                        <a href="{{ route('budgets.show', $budget->id) }}"
                           class="text-xs font-medium text-gray-700 border border-gray-300 px-3 py-1 rounded hover:bg-gray-100 transition">
                            Review
                        </a>
                        @if (Auth::user()->role->name === 'Admin')
                        <form method="POST" action="{{ route('budgets.destroy', $budget->id) }}"
                              onsubmit="return confirm('Delete this budget?')">
                            @csrf @method('DELETE')
                            <button class="text-xs font-medium text-red-600 border border-red-200 px-3 py-1 rounded hover:bg-red-50 transition">
                                Delete
                            </button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-5 py-10 text-center text-gray-400 text-sm italic">No budgets found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if ($budgets->hasPages())
    <div class="px-5 py-3 border-t border-gray-100">{{ $budgets->links() }}</div>
    @endif
</div>

@endsection
