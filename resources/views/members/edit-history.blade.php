@extends('layouts.app')

@section('title', 'Edit History - ' . ($user->full_name ?? 'Member'))

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('members.edit', $user->id) }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Edit Member
        </a>
        
        <div class="mt-3">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit History</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $user->full_name }}</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                Current Role: {{ $user->role->name ?? 'N/A' }} | 
                Current Position: {{ $user->position ?? 'N/A' }}
            </p>
        </div>
    </div>
    
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gold-800 overflow-hidden">
        <div class="px-6 py-4 border-b border-gold-200 dark:border-gold-800 bg-gray-50 dark:bg-gray-800/50">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Position Change History</h2>
        </div>
        
        <div class="divide-y divide-gray-200 dark:divide-gray-700">
            @forelse($positionLogs as $log)
            <div class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-400">
                                Position Change
                            </span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $log->created_at->format('M d, Y H:i:s') }}
                            </span>
                        </div>
                        
                        <div class="mb-2">
                            <span class="text-sm text-gray-500 dark:text-gray-400">From:</span>
                            <span class="text-sm font-mono bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded ml-2">{{ $log->old_position ?: 'Not set' }}</span>
                            <span class="text-sm text-gray-500 dark:text-gray-400 mx-2">→</span>
                            <span class="text-sm font-mono bg-gray-100 dark:bg-gray-700 px-2 py-1 rounded">{{ $log->new_position ?: 'Not set' }}</span>
                        </div>
                        
                        @if($log->reason)
                        <div class="mt-2 p-2 bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 rounded-r">
                            <p class="text-xs text-yellow-700 dark:text-yellow-300">
                                <span class="font-semibold">Reason:</span> {{ $log->reason }}
                            </p>
                        </div>
                        @endif
                        
                        <div class="mt-2 text-xs text-gray-400 dark:text-gray-500">
                            Changed by: {{ $log->changer->full_name ?? 'Unknown' }}
                            @if($log->ip_address)
                            • IP: {{ $log->ip_address }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="p-12 text-center">
                <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <p class="text-gray-500 dark:text-gray-400 text-lg">No Position Change History Yet</p>
                <p class="text-sm text-gray-400 dark:text-gray-500 mt-2">
                    When you change a member's position and provide a reason, it will appear here.
                </p>
                <div class="mt-4 p-4 bg-gray-50 dark:bg-gray-700/30 rounded-lg text-left">
                    <p class="text-xs text-gray-600 dark:text-gray-400 font-semibold mb-2">Current Information:</p>
                    <p class="text-sm text-gray-700 dark:text-gray-300">• Current Position: <strong>{{ $user->position ?? 'Not set' }}</strong></p>
                    <p class="text-sm text-gray-700 dark:text-gray-300 mt-1">• Current Role: <strong>{{ $user->role->name ?? 'Not set' }}</strong></p>
                    <p class="text-sm text-gray-700 dark:text-gray-300 mt-1">• Member Since: <strong>{{ optional($user->member->joined_at)->format('M d, Y') ?? 'Not set' }}</strong></p>
                </div>
            </div>
            @endforelse
        </div>
        
        @if($positionLogs->hasPages())
        <div class="px-6 py-4 border-t border-gold-200 dark:border-gold-800">
            {{ $positionLogs->links() }}
        </div>
        @endif
    </div>
</div>
@endsection