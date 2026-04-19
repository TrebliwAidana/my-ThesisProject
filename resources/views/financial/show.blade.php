@extends('layouts.app')

@section('title', 'Transaction Details')
@section('page-title', 'Transaction Details')

@section('content')
<div class="max-w-2xl mx-auto space-y-4">

    {{-- Flash --}}
    @if(session('success'))
        <div class="p-4 bg-green-100 border border-green-300 text-green-800 rounded-xl text-sm">✅ {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-red-100 border border-red-300 text-red-800 rounded-xl text-sm">❌ {{ session('error') }}</div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gold-200 dark:border-gold-800 p-6 space-y-5">

        {{-- Header --}}
        <div class="flex items-start justify-between">
            <div>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">{{ $transaction->description }}</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                    {{ $transaction->transaction_date->format('F d, Y') }}
                </p>
            </div>
            <div class="flex flex-col items-end gap-2">
                <span class="text-2xl font-bold {{ $transaction->type === 'income' ? 'text-emerald-600' : 'text-red-500' }}">
                    {{ $transaction->type === 'income' ? '+' : '-' }}{{ $transaction->formatted_amount }}
                </span>
                @php
                    $statusColors = [
                        'pending'  => 'bg-amber-100 text-amber-700',
                        'audited'  => 'bg-blue-100 text-blue-700',
                        'approved' => 'bg-emerald-100 text-emerald-700',
                        'rejected' => 'bg-red-100 text-red-700',
                    ];
                @endphp
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $statusColors[$transaction->status] ?? '' }}">
                    {{ ucfirst($transaction->status) }}
                </span>
            </div>
        </div>

        <hr class="border-gray-100 dark:border-gray-700">

        {{-- Details Grid --}}
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">Type</p>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                    {{ $transaction->type === 'income' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                    {{ ucfirst($transaction->type) }}
                </span>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">Category</p>
                <p class="text-gray-800 dark:text-white">{{ $transaction->17_category ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">Submitted By</p>
                <p class="text-gray-800 dark:text-white">{{ $transaction->user->full_name ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">Submitted On</p>
                <p class="text-gray-800 dark:text-white">{{ $transaction->created_at->format('M d, Y h:i A') }}</p>
            </div>

            {{-- Auditor Info (if audited) --}}
            @if($transaction->audited_at)
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">Audited By</p>
                <p class="text-gray-800 dark:text-white">{{ $transaction->auditor->full_name ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">Audited On</p>
                <p class="text-gray-800 dark:text-white">{{ $transaction->audited_at->format('M d, Y h:i A') }}</p>
            </div>
            @endif

            {{-- Approver Info (if approved) --}}
            @if($transaction->approved_at)
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">Approved By</p>
                <p class="text-gray-800 dark:text-white">{{ $transaction->approver->full_name ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">Approved On</p>
                <p class="text-gray-800 dark:text-white">{{ $transaction->approved_at->format('M d, Y h:i A') }}</p>
            </div>
            @endif
        </div>

        {{-- Notes --}}
        @if($transaction->notes)
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">Notes</p>
            <p class="text-sm text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                {{ $transaction->notes }}
            </p>
        </div>
        @endif

        {{-- Receipt --}}
        @if($transaction->documents->isNotEmpty())
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">Receipt</p>
            @foreach($transaction->documents as $doc)
                <a href="{{ route('documents.show', $doc->id) }}" target="_blank"
                   class="inline-flex items-center gap-2 text-sm text-blue-600 hover:text-blue-800 font-medium">
                    📎 View Receipt
                </a>
            @endforeach
        </div>
        @endif

        <hr class="border-gray-100 dark:border-gray-700">

        {{-- Actions --}}
        <div class="flex flex-wrap gap-3">
            @php $user = auth()->user(); @endphp

            {{-- Treasurer / Creator: Edit & Delete (only when pending) --}}
            @if($transaction->status === 'pending' && $user->can('update', $transaction))
                <a href="{{ route('financial.edit', $transaction->id) }}"
                   class="bg-gold-500 hover:bg-gold-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                    Edit
                </a>

                <form method="POST" action="{{ route('financial.destroy', $transaction->id) }}"
                      class="inline" onsubmit="return confirm('Delete this transaction?')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="bg-gray-200 hover:bg-red-600 hover:text-white text-gray-700 text-sm font-semibold px-4 py-2 rounded-lg transition">
                        Delete
                    </button>
                </form>
            @endif

            {{-- Auditor: Mark as Audited (only when pending) --}}
            @if($transaction->status === 'pending' && $user->hasPermission('financial.audit'))
                <form method="POST" action="{{ route('financial.audit', $transaction->id) }}" style="display:inline;">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                        Mark as Audited
                    </button>
                </form>
            @endif

            {{-- Adviser / Final Approver: Approve & Reject (only when audited) --}}
            @if($transaction->status === 'audited' && $user->hasPermission('financial.approve'))
                <form method="POST" action="{{ route('financial.approve', $transaction->id) }}" style="display:inline;">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
                        Approve
                    </button>
                </form>

                <form method="POST" action="{{ route('financial.reject', $transaction->id) }}" style="display:inline;">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">
                        Reject
                    </button>
                </form>
            @endif

            <a href="{{ route('financial.index') }}"
               class="bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-semibold px-4 py-2 rounded-lg transition">
                ← Back to List
            </a>
        </div>

    </div>
</div>
@endsection