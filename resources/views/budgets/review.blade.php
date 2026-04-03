@extends('layouts.app')

@section('title', 'Review Budget — VSULHS_SSLG')
@section('page-title', 'Review Budget Request')

@section('content')
{{-- Emerald Gradient Header --}}
<div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-primary-600 to-primary-700 dark:from-primary-800 dark:to-primary-900 p-6 mb-6">
    <div class="relative z-10">
        <h1 class="text-2xl font-bold text-white">Review Budget Request</h1>
        <p class="text-primary-100 text-sm mt-1">Approve or reject a budget request</p>
    </div>
    <div class="absolute top-0 right-0 -mt-8 -mr-8 w-64 h-64 bg-white/5 rounded-full blur-3xl"></div>
</div>

<div class="max-w-3xl mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gold-200 dark:border-gold-800 overflow-hidden">
        {{-- Budget Details Card --}}
        <div class="px-6 py-4 border-b border-gold-200 dark:border-gold-800 bg-primary-50 dark:bg-primary-900/20">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $budget->title }}</h2>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Requested by: {{ optional($budget->requester)->full_name ?? 'Unknown' }}
            </p>
        </div>
        <div class="p-6 space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div><span class="font-semibold">Amount:</span> ₱{{ number_format($budget->amount, 2) }}</div>
                <div><span class="font-semibold">Category:</span> {{ $budget->category }}</div>
                <div><span class="font-semibold">Description:</span> {{ $budget->description ?: '—' }}</div>
                <div><span class="font-semibold">Submitted:</span> {{ $budget->created_at->format('M d, Y') }}</div>
            </div>

            <div class="border-t border-gold-200 dark:border-gold-800 pt-4">
                <h3 class="font-semibold text-gray-900 dark:text-white mb-3">Take action</h3>
                <div class="flex flex-col sm:flex-row gap-4">
                    {{-- Approve Form --}}
                    <form method="POST" action="{{ route('budgets.approve', $budget) }}" class="flex-1">
                        @csrf
                        <textarea name="approval_remarks" rows="2" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg p-2 text-sm focus:ring-2 focus:ring-gold-500 focus:border-transparent" placeholder="Approval remarks (optional)"></textarea>
                        <button type="submit" class="w-full mt-2 bg-primary-600 hover:bg-gold-500 text-white font-medium py-2 rounded-lg transition shadow-sm hover:shadow-md transform hover:scale-[1.02] active:scale-[0.98]">
                            Approve
                        </button>
                    </form>

                    {{-- Reject Form --}}
                    <form method="POST" action="{{ route('budgets.reject', $budget) }}" class="flex-1">
                        @csrf
                        <textarea name="review_remarks" rows="2" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg p-2 text-sm focus:ring-2 focus:ring-gold-500 focus:border-transparent" placeholder="Rejection reason (required)" required></textarea>
                        <button type="submit" class="w-full mt-2 bg-red-600 hover:bg-red-700 text-white font-medium py-2 rounded-lg transition shadow-sm hover:shadow-md transform hover:scale-[1.02] active:scale-[0.98]">
                            Reject
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection