@extends('layouts.app')

@section('title', 'Review Budget — VSULHS_SSLG')

@section('content')

<div class="mb-6">
    <a href="{{ route('budgets.show', $budget) }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        Back to Budget Details
    </a>
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mt-3">Review Budget Request</h1>
    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Review and make a decision on this budget request</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Budget Information --}}
    <div class="lg:col-span-2">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gold-800 overflow-hidden">
            <div class="bg-gradient-to-r from-primary-600 to-primary-700 px-6 py-4">
                <h2 class="text-white font-semibold text-lg">Budget Request Details</h2>
                <p class="text-primary-200 text-sm mt-1">Please review the information below before making a decision</p>
            </div>

            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Title</label>
                    <p class="text-gray-900 dark:text-white mt-1 font-medium">{{ $budget->title }}</p>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Amount</label>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">₱{{ number_format($budget->amount, 2) }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Category</label>
                        <p class="text-gray-900 dark:text-white mt-1 font-medium">{{ $budget->category }}</p>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Description</label>
                    <p class="text-gray-700 dark:text-gray-300 mt-1 leading-relaxed">{{ $budget->description ?? 'No description provided.' }}</p>
                </div>

                <div class="grid grid-cols-2 gap-4 pt-4 border-t border-gold-200 dark:border-gold-800">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Requested By</label>
                        <p class="text-gray-900 dark:text-white mt-1">{{ $budget->requester->full_name ?? 'N/A' }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $budget->requester->email ?? '' }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Request Date</label>
                        <p class="text-gray-900 dark:text-white mt-1">{{ $budget->created_at->format('F d, Y') }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $budget->created_at->format('h:i A') }}</p>
                    </div>
                </div>

                @if($budget->attachment_path)
                <div>
                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Attachment</label>
                    <a href="{{ asset('storage/' . $budget->attachment_path) }}" target="_blank" 
                       class="inline-flex items-center gap-2 mt-1 text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        View Attachment
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Review Form --}}
    <div class="lg:col-span-1">
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gold-800 overflow-hidden sticky top-6">
            <div class="px-6 py-4 border-b border-gold-200 dark:border-gold-800">
                <h3 class="font-semibold text-gray-900 dark:text-white">Review Decision</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Approve or reject this budget request</p>
            </div>

            <form method="POST" action="{{ route('budgets.approve', $budget) }}" class="p-6">
                @csrf

                {{-- Remarks --}}
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Remarks (Optional)</label>
                    <textarea name="review_remarks" rows="4" 
                              class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-transparent transition"
                              placeholder="Add any comments or notes about this request...">{{ old('review_remarks') }}</textarea>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">These remarks will be visible to the requester</p>
                </div>

                {{-- Action Buttons --}}
                <div class="space-y-3">
                    <button type="submit" name="action" value="approve"
                            class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-lg transition font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Approve Budget
                    </button>
                    
                    <button type="submit" name="action" value="reject"
                            class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-lg transition font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Reject Budget
                    </button>
                    
                    <a href="{{ route('budgets.show', $budget) }}" 
                       class="block text-center w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        Cancel
                    </a>
                </div>
            </form>
        </div>

        {{-- Review Guidelines --}}
        <div class="mt-4 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
            <div class="flex gap-3">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <p class="text-sm text-blue-800 dark:text-blue-300 font-medium">Review Guidelines</p>
                    <ul class="text-xs text-blue-700 dark:text-blue-400 mt-2 space-y-1">
                        <li>• Verify the budget amount is reasonable</li>
                        <li>• Check if the category is appropriate</li>
                        <li>• Ensure all required details are provided</li>
                        <li>• Provide constructive feedback if rejecting</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection