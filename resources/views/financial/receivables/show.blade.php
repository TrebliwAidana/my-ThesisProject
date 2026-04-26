@extends('layouts.app')

@section('title', 'Receivable Details')
@section('page-title', 'Receivable Details')

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

    {{-- Back Button --}}
    <div>
        <a href="{{ route('financial.receivables') }}"
           class="inline-flex items-center gap-2 text-gray-600 hover:text-gray-800 dark:text-gray-400 dark:hover:text-gray-200">
            ← Back to Receivables
        </a>
    </div>

    {{-- Receivable Details Card --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow border border-gold-200 dark:border-gold-800 overflow-hidden">
        <div class="border-b border-gray-100 dark:border-gray-700 px-6 py-4 bg-gray-50 dark:bg-gray-700/50">
            <h2 class="text-xl font-bold text-gray-800 dark:text-white">Receivable #{{ $receivable->reference_no }}</h2>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Customer / Payer</dt>
                            <dd class="text-base text-gray-900 dark:text-white">{{ $receivable->customer_name ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Description</dt>
                            <dd class="text-base text-gray-900 dark:text-white">{{ $receivable->description }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Amount</dt>
                            <dd class="text-base font-semibold text-gray-900 dark:text-white">₱{{ number_format($receivable->total_amount, 2) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Amount Paid</dt>
                            <dd class="text-base font-semibold text-emerald-600">₱{{ number_format($receivable->paid_amount, 2) }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Remaining Balance</dt>
                            <dd class="text-base font-semibold {{ $receivable->remaining > 0 ? 'text-red-600' : 'text-green-600' }}">
                                ₱{{ number_format($receivable->remaining, 2) }}
                            </dd>
                        </div>
                    </dl>
                </div>
                <div>
                    <dl class="space-y-3">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Due Date</dt>
                            <dd class="text-base text-gray-900 dark:text-white">
                                @if($receivable->due_date)
                                    {{ \Carbon\Carbon::parse($receivable->due_date)->format('F d, Y') }}
                                    @if($receivable->due_date < now()->toDateString() && $receivable->status !== 'paid')
                                        <span class="ml-2 text-xs text-red-500">(Overdue)</span>
                                    @endif
                                @else
                                    —
                                @endif
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                            <dd>
                                @php
                                    $statusColors = [
                                        'pending' => 'bg-amber-100 text-amber-700',
                                        'partial' => 'bg-yellow-100 text-yellow-800',
                                        'paid'    => 'bg-green-100 text-green-800',
                                        'overdue' => 'bg-red-100 text-red-800',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $statusColors[$receivable->status] ?? 'bg-gray-100 text-gray-700' }}">
                                    {{ ucfirst($receivable->status) }}
                                </span>
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Created By</dt>
                            <dd class="text-base text-gray-900 dark:text-white">{{ $receivable->creator->full_name ?? '—' }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Created At</dt>
                            <dd class="text-base text-gray-900 dark:text-white">{{ $receivable->created_at->format('F d, Y h:i A') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            {{-- Linked Transaction Information --}}
            @if($receivable->incomeTransaction)
                <div class="mt-6 pt-6 border-t border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-semibold mb-3">Linked Income Transaction</h3>
                    <div class="bg-gray-50 dark:bg-gray-700/30 rounded-lg p-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <span class="text-xs text-gray-500 dark:text-gray-400 block">Transaction ID</span>
                                <span class="font-mono text-sm">#{{ $receivable->incomeTransaction->id }}</span>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500 dark:text-gray-400 block">Amount</span>
                                <span class="font-semibold">₱{{ number_format($receivable->incomeTransaction->amount, 2) }}</span>
                            </div>
                            <div>
                                <span class="text-xs text-gray-500 dark:text-gray-400 block">Status</span>
                                @php
                                    $txStatusColors = [
                                        'pending'  => 'bg-amber-100 text-amber-700',
                                        'audited'  => 'bg-blue-100 text-blue-700',
                                        'approved' => 'bg-emerald-100 text-emerald-700',
                                        'rejected' => 'bg-red-100 text-red-700',
                                    ];
                                @endphp
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $txStatusColors[$receivable->incomeTransaction->status] ?? 'bg-gray-100 text-gray-700' }}">
                                    {{ ucfirst($receivable->incomeTransaction->status) }}
                                </span>
                            </div>
                        </div>

                        {{-- MARK AS PAID BUTTON --}}
                        @if($receivable->incomeTransaction->status === 'approved' && !$receivable->incomeTransaction->receivable_paid && $receivable->status !== 'paid')
                            <div class="mt-4">
                                <form method="POST" action="{{ route('financial.receivable.mark-paid', $receivable) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                            class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-6 py-2 rounded-lg transition"
                                            onclick="return confirm('Mark this receivable as paid? This will add the amount to income reports.')">
                                        ✅ Mark as Paid
                                    </button>
                                </form>
                                <p class="text-xs text-gray-500 mt-2">This will include the amount in your financial report's Total Income.</p>
                            </div>
                        @elseif($receivable->incomeTransaction->status !== 'approved')
                            <div class="mt-4">
                                <p class="text-amber-600 text-sm">⚠️ This transaction must be <strong>approved</strong> (not just audited) before it can be marked as paid.</p>
                                <p class="text-xs text-gray-500 mt-1">Current status: {{ $receivable->incomeTransaction->status }}</p>
                            </div>
                        @elseif($receivable->incomeTransaction->receivable_paid)
                            <div class="mt-4">
                                <p class="text-green-600 text-sm">✓ Already marked as paid. Amount is included in income reports.</p>
                            </div>
                        @elseif($receivable->status === 'paid')
                            <div class="mt-4">
                                <p class="text-green-600 text-sm">✓ Receivable is already marked as paid.</p>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="mt-6 pt-6 border-t border-gray-100 dark:border-gray-700">
                    <div class="bg-red-50 dark:bg-red-900/20 rounded-lg p-4">
                        <p class="text-red-600">⚠️ No linked income transaction found. Please check the database.</p>
                    </div>
                </div>
            @endif

            {{-- Documents / Approval Slip ────────────────────────────────── --}}
            @php
                $paymentDocs = $receivable->incomeTransaction?->documents ?? collect();
            @endphp
            @if($paymentDocs->isNotEmpty())
                <div class="mt-6 pt-6 border-t border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-semibold mb-3 text-gray-800 dark:text-white">
                        📎 Payment Documents
                        @if($receivable->status === 'paid')
                            <span class="ml-2 text-xs font-normal text-emerald-600">
                                — Paid {{ $receivable->paid_at ? \Carbon\Carbon::parse($receivable->paid_at)->format('F d, Y h:i A') : '' }}
                            </span>
                        @endif
                    </h3>
                    <div class="space-y-3">
                        @foreach($paymentDocs as $doc)
                            @php $version = $doc->latestVersion; @endphp
                            <div class="flex items-center justify-between bg-gray-50 dark:bg-gray-700/30 rounded-lg px-4 py-3">
                                <div class="flex items-center gap-3 min-w-0">
                                    <span class="text-2xl shrink-0">📄</span>
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-gray-800 dark:text-white truncate">{{ $doc->title }}</p>
                                        @if($doc->description)
                                            <p class="text-xs text-gray-500 dark:text-gray-400 truncate mt-0.5">{{ $doc->description }}</p>
                                        @endif
                                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                                            Saved {{ $doc->created_at->format('F d, Y h:i A') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 shrink-0 ml-4">
                                    @if($version)
                                        <a href="{{ route('documents.version.download', [$doc, $version]) }}"
                                           class="inline-flex items-center gap-1 text-xs bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 hover:bg-blue-100 px-3 py-1.5 rounded-lg font-medium transition">
                                            ⬇ Download
                                        </a>
                                        <a href="{{ route('documents.preview', $doc) }}" target="_blank"
                                           class="inline-flex items-center gap-1 text-xs bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 px-3 py-1.5 rounded-lg font-medium transition">
                                            👁 Preview
                                        </a>
                                    @else
                                        <span class="text-xs text-gray-400 italic">No file version found</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @elseif($receivable->status === 'paid')
                <div class="mt-6 pt-6 border-t border-gray-100 dark:border-gray-700">
                    <h3 class="text-lg font-semibold mb-3 text-gray-800 dark:text-white">📎 Payment Documents</h3>
                    <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg p-4">
                        <p class="text-sm text-amber-700 dark:text-amber-400">
                            ⚠️ This receivable is paid but no documents were found. The approval slip may have failed to save.
                        </p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
