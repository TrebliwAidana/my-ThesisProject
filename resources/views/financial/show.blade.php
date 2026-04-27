@extends('layouts.app')

@section('title', 'Transaction Details')
@section('page-title', 'Transaction Details')

@section('content')
<div class="max-w-2xl mx-auto space-y-4">

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gold-200 dark:border-gold-800 p-6 space-y-5">

        {{-- Header --}}
        <div class="flex items-start justify-between">
            <div>
                <h2 class="text-lg font-bold text-gray-900 dark:text-white">{{ $transaction->description }}</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                    {{ $transaction->transaction_date->format('F d, Y') }}
                </p>
                @if($transaction->type === 'receivable' && $transaction->customer_name)
                    <p class="text-sm text-purple-600 dark:text-purple-400 mt-0.5 font-medium">
                        👤 {{ $transaction->customer_name }}
                    </p>
                @endif
            </div>
            <div class="flex flex-col items-end gap-2">
                @php
                    $amountColor = match($transaction->type) {
                        'income'     => 'text-emerald-600',
                        'expense'    => 'text-red-500',
                        'receivable' => 'text-purple-600',
                        default      => 'text-gray-700',
                    };
                    $prefix = match($transaction->type) {
                        'income'     => '+',
                        'expense'    => '-',
                        'receivable' => $transaction->status === 'paid' ? '+' : '⏳',
                        default      => '',
                    };
                    $statusColors = [
                        'pending'  => 'bg-amber-100 text-amber-700',
                        'audited'  => 'bg-blue-100 text-blue-700',
                        'approved' => 'bg-emerald-100 text-emerald-700',
                        'rejected' => 'bg-red-100 text-red-700',
                        'paid'     => 'bg-green-100 text-green-800',
                    ];
                @endphp
                <span class="text-2xl font-bold {{ $amountColor }}">
                    {{ $prefix }} {{ $transaction->formatted_amount }}
                </span>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $statusColors[$transaction->status] ?? '' }}">
                    {{ $transaction->status === 'paid' ? '✓ Paid' : ucfirst($transaction->status) }}
                </span>
            </div>
        </div>

        <hr class="border-gray-100 dark:border-gray-700">

        {{-- Details Grid --}}
        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">Type</p>
                @if($transaction->type === 'income')
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">↑ Income</span>
                @elseif($transaction->type === 'expense')
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-700">↓ Expense</span>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-purple-100 text-purple-700">⏳ Receivable</span>
                @endif
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">Category</p>
                <p class="text-gray-800 dark:text-white">{{ $transaction->category ?? '—' }}</p>
            </div>

            @if($transaction->type === 'receivable')
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">Customer</p>
                <p class="text-gray-800 dark:text-white">{{ $transaction->customer_name ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">Due Date</p>
                <p class="{{ $transaction->isOverdue() ? 'text-red-600 font-semibold' : 'text-gray-800 dark:text-white' }}">
                    {{ $transaction->due_date ? $transaction->due_date->format('M d, Y') : '—' }}
                    @if($transaction->isOverdue())
                        <span class="ml-1 text-xs">(Overdue)</span>
                    @endif
                </p>
            </div>
            @endif

            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">Submitted By</p>
                <p class="text-gray-800 dark:text-white">{{ $transaction->user->full_name ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">Submitted On</p>
                <p class="text-gray-800 dark:text-white">{{ $transaction->created_at->format('M d, Y h:i A') }}</p>
            </div>

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

            @if($transaction->approved_at)
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">
                    {{ $transaction->status === 'paid' ? 'Collected By' : 'Approved By' }}
                </p>
                <p class="text-gray-800 dark:text-white">{{ $transaction->approver->full_name ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">
                    {{ $transaction->status === 'paid' ? 'Collected On' : 'Approved On' }}
                </p>
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

        {{-- ══════════════════════════════════════════════════════════
             SUGGESTION 3 — Approval Document section
             Clearly labeled, distinguished from generic documents
        ══════════════════════════════════════════════════════════ --}}
        @if($transaction->documents->isNotEmpty())
        @php
            $approvalDocs = $transaction->documents->filter(function($doc) {
                $tags = is_array($doc->tags) ? $doc->tags : ($doc->tags ?? []);
                return in_array('auto-generated', $tags);
            });
            $receiptDocs = $transaction->documents->filter(function($doc) {
                $tags = is_array($doc->tags) ? $doc->tags : ($doc->tags ?? []);
                return !in_array('auto-generated', $tags);
            });
        @endphp

        {{-- Approval Document --}}
        @if($approvalDocs->isNotEmpty())
        <div>
            <div class="flex items-center gap-2 mb-2">
                <p class="text-xs font-semibold uppercase tracking-wider text-gray-400">Approval Document</p>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                    ✓ Auto-generated
                </span>
            </div>
            <div class="space-y-2">
                @foreach($approvalDocs as $doc)
                    @php $version = $doc->latestVersion; @endphp
                    <div class="flex items-center justify-between bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-lg px-4 py-2.5">
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="shrink-0 text-emerald-600">📋</span>
                            <div class="min-w-0">
                                <span class="text-sm font-medium text-gray-800 dark:text-white truncate block">{{ $doc->title }}</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">Generated {{ $doc->created_at->format('M d, Y h:i A') }}</span>
                            </div>
                        </div>
                        <div class="flex gap-2 shrink-0 ml-3">
                            @if($version)
                                <a href="{{ route('documents.version.download', [$doc, $version]) }}"
                                   class="text-xs bg-emerald-600 hover:bg-emerald-700 text-white font-medium px-3 py-1 rounded-lg transition">
                                    ⬇ Download
                                </a>
                                <a href="{{ route('documents.preview', $doc) }}" target="_blank"
                                   class="text-xs text-gray-500 hover:text-gray-700 dark:text-gray-400 font-medium border border-gray-300 dark:border-gray-600 px-3 py-1 rounded-lg transition">
                                    👁 Preview
                                </a>
                            @else
                                <span class="text-xs text-gray-400 italic">File not yet generated</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1.5 ml-1">
                This document is a copy saved from Financial Records. To remove it, delete the transaction from
                <a href="{{ route('financial.index') }}" class="underline hover:text-gray-600">Financial Records</a>.
            </p>
        </div>
        @endif

        {{-- Receipt / Manual attachments --}}
        @if($receiptDocs->isNotEmpty())
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-gray-400 mb-2">Attached Receipt</p>
            <div class="space-y-2">
                @foreach($receiptDocs as $doc)
                    @php $version = $doc->latestVersion; @endphp
                    <div class="flex items-center justify-between bg-gray-50 dark:bg-gray-700/30 rounded-lg px-4 py-2.5">
                        <div class="flex items-center gap-2 min-w-0">
                            <span class="shrink-0">📎</span>
                            <span class="text-sm text-gray-800 dark:text-white truncate">{{ $doc->title }}</span>
                        </div>
                        <div class="flex gap-2 shrink-0 ml-3">
                            @if($version)
                                <a href="{{ route('documents.version.download', [$doc, $version]) }}"
                                   class="text-xs text-blue-600 hover:text-blue-800 font-medium">⬇ Download</a>
                                <a href="{{ route('documents.preview', $doc) }}" target="_blank"
                                   class="text-xs text-gray-500 hover:text-gray-700 font-medium">👁 Preview</a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
        @endif

        <hr class="border-gray-100 dark:border-gray-700">

        {{-- ══════════════════════════════════════════════════════════
             Mark as Paid — receivable only, status = approved
        ══════════════════════════════════════════════════════════ --}}
        @if($transaction->type === 'receivable' && $transaction->status === 'approved')
            @if(auth()->user()->hasPermission('approve_financial_transactions') || auth()->user()->role->level === 1)
            <div class="bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-700 rounded-xl p-4">
                <p class="text-sm font-semibold text-purple-800 dark:text-purple-300 mb-1">Ready to collect?</p>
                <p class="text-xs text-purple-600 dark:text-purple-400 mb-3">
                    Clicking <strong>Mark as Paid</strong> will add
                    <strong>₱{{ number_format($transaction->amount, 2) }}</strong> to Total Income
                    and save the approval slip to Documents automatically.
                </p>
                <form method="POST" action="{{ route('financial.mark-as-paid', $transaction->id) }}"
                      onsubmit="return confirm('Confirm payment received from {{ addslashes($transaction->customer_name) }}?\n\nThis will add ₱{{ number_format($transaction->amount, 2) }} to total income and cannot be undone.')">
                    @csrf
                    @method('PATCH')
                    <button type="submit"
                            class="bg-purple-600 hover:bg-purple-700 text-white font-semibold px-6 py-2 rounded-lg transition">
                        ✅ Mark as Paid
                    </button>
                </form>
            </div>
            @endif
        @endif

        {{-- ══════════════════════════════════════════════════════════
             Actions
        ══════════════════════════════════════════════════════════ --}}
        <div class="flex flex-wrap gap-3">
            @php $user = auth()->user(); @endphp

            @if($transaction->status === 'pending')
                @if($user->hasPermission('submit_financial_transactions') || $user->role->level === 1)
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
            @endif

            @if($transaction->status === 'pending' && $user->hasPermission('view_financial_transactions'))
                <form method="POST" action="{{ route('financial.audit', $transaction->id) }}" class="inline">
                    @csrf @method('PATCH')
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                        Mark as Audited
                    </button>
                </form>
            @endif

            @if($transaction->status === 'audited' && $user->hasPermission('approve_financial_transactions'))
                <form method="POST" action="{{ route('financial.approve', $transaction->id) }}" class="inline">
                    @csrf @method('PATCH')
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                        Approve
                    </button>
                </form>
                <form method="POST" action="{{ route('financial.reject', $transaction->id) }}" class="inline">
                    @csrf @method('PATCH')
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
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