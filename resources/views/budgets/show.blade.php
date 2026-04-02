@extends('layouts.app')

@section('title', 'Budget Details — VSULHS_SSLG')

@section('content')

<div class="mb-6">
    <a href="{{ route('budgets.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        Back to Budgets
    </a>
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mt-3">Budget Details</h1>
    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">View and manage budget request</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Main Content --}}
    <div class="lg:col-span-2 space-y-6">
        {{-- Budget Information Card --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gold-800 overflow-hidden">
            <div class="bg-gradient-to-r from-primary-600 to-primary-700 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-white font-semibold text-lg">{{ $budget->title }}</h2>
                        <p class="text-primary-200 text-sm mt-1">Request ID: #{{ $budget->id }}</p>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-sm font-medium {{ $budget->status_color }}">
                        <span class="w-1.5 h-1.5 rounded-full 
                            {{ $budget->status == 'pending' ? 'bg-yellow-500' : '' }}
                            {{ $budget->status == 'reviewed' ? 'bg-blue-500' : '' }}
                            {{ $budget->status == 'approved' ? 'bg-green-500' : '' }}
                            {{ $budget->status == 'rejected' ? 'bg-red-500' : '' }}
                            {{ $budget->status == 'disbursed' ? 'bg-gray-500' : '' }}">
                        </span>
                        {{ ucfirst($budget->status) }}
                    </span>
                </div>
            </div>

            <div class="p-6 space-y-4">
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

                @if($budget->attachment_path)
                <div>
                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Attachment</label>
                    <a href="{{ asset('storage/' . $budget->attachment_path) }}" target="_blank" 
                       class="inline-flex items-center gap-2 mt-1 text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Download Attachment
                    </a>
                </div>
                @endif
            </div>
        </div>

        {{-- Request Information Card --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gold-800 overflow-hidden">
            <div class="px-6 py-4 border-b border-gold-200 dark:border-gold-800">
                <h3 class="font-semibold text-gray-900 dark:text-white">Request Information</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 gap-4">
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
            </div>
        </div>

        {{-- Review Information Card --}}
        @if($budget->reviewed_by || $budget->status != 'pending')
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gold-800 overflow-hidden">
            <div class="px-6 py-4 border-b border-gold-200 dark:border-gold-800">
                <h3 class="font-semibold text-gray-900 dark:text-white">Review Information</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Reviewed By</label>
                        <p class="text-gray-900 dark:text-white mt-1">{{ $budget->reviewer->full_name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Review Date</label>
                        <p class="text-gray-900 dark:text-white mt-1">{{ $budget->reviewed_at ? $budget->reviewed_at->format('F d, Y') : 'Not reviewed yet' }}</p>
                    </div>
                </div>
                @if($budget->review_remarks)
                <div class="mt-4 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Review Remarks</label>
                    <p class="text-gray-700 dark:text-gray-300 text-sm">{{ $budget->review_remarks }}</p>
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- Approval Information Card --}}
        @if($budget->approved_by)
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gold-800 overflow-hidden">
            <div class="px-6 py-4 border-b border-gold-200 dark:border-gold-800">
                <h3 class="font-semibold text-gray-900 dark:text-white">Approval Information</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Approved By</label>
                        <p class="text-gray-900 dark:text-white mt-1">{{ $budget->approver->full_name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Approval Date</label>
                        <p class="text-gray-900 dark:text-white mt-1">{{ $budget->approved_at ? $budget->approved_at->format('F d, Y') : 'Not approved yet' }}</p>
                    </div>
                </div>
                @if($budget->approval_remarks)
                <div class="mt-4 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Approval Remarks</label>
                    <p class="text-gray-700 dark:text-gray-300 text-sm">{{ $budget->approval_remarks }}</p>
                </div>
                @endif
            </div>
        </div>
        @endif
    </div>

    {{-- Sidebar Actions --}}
    <div class="lg:col-span-1 space-y-6">
        {{-- Action Buttons --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gold-800 overflow-hidden sticky top-6">
            <div class="px-6 py-4 border-b border-gold-200 dark:border-gold-800">
                <h3 class="font-semibold text-gray-900 dark:text-white">Actions</h3>
            </div>
            <div class="p-6 space-y-3">
                @if($budget->status == 'pending' && $budget->requested_by == Auth::id())
                <a href="{{ route('budgets.edit', $budget) }}" 
                   class="flex items-center justify-center gap-2 w-full px-4 py-2 bg-primary-600 hover:bg-gold-500 text-white rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                    Edit Request
                </a>
                @endif

                @if(in_array($budget->status, ['pending', 'reviewed']) && in_array(Auth::user()->role->name, ['Adviser', 'Officer']))
                <a href="{{ route('budgets.review', $budget) }}" 
                   class="flex items-center justify-center gap-2 w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Review Budget
                </a>
                @endif

                @if($budget->status == 'pending' && ($budget->requested_by == Auth::id() || Auth::user()->role->name == 'Adviser'))
                <form method="POST" action="{{ route('budgets.destroy', $budget) }}" onsubmit="return confirm('Are you sure you want to delete this budget request?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="flex items-center justify-center gap-2 w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Delete Request
                    </button>
                </form>
                @endif
            </div>
        </div>

        {{-- Timeline --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gold-800 overflow-hidden">
            <div class="px-6 py-4 border-b border-gold-200 dark:border-gold-800">
                <h3 class="font-semibold text-gray-900 dark:text-white">Timeline</h3>
            </div>
            <div class="p-6">
                <div class="space-y-4">
                    <div class="flex gap-3">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-primary-100 dark:bg-primary-900/50 flex items-center justify-center">
                            <svg class="w-4 h-4 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">Request Created</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $budget->created_at->format('F d, Y \a\t h:i A') }}</p>
                        </div>
                    </div>
                    
                    @if($budget->reviewed_at)
                    <div class="flex gap-3">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/50 flex items-center justify-center">
                            <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">Reviewed</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $budget->reviewed_at->format('F d, Y \a\t h:i A') }}</p>
                        </div>
                    </div>
                    @endif
                    
                    @if($budget->approved_at)
                    <div class="flex gap-3">
                        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-green-100 dark:bg-green-900/50 flex items-center justify-center">
                            <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ ucfirst($budget->status) }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $budget->approved_at->format('F d, Y \a\t h:i A') }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection