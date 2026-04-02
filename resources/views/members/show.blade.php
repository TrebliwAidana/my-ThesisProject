@extends('layouts.app')

@section('title', $member->full_name . ' — Member Profile')

@section('content')

@php
    $currentUser = auth()->user();
    $isSystemAdmin = $currentUser->role_id == 1;
    $canManageAccounts = ($isSystemAdmin || $currentUser->role->name === 'Supreme Admin' || $currentUser->role->name === 'Club Adviser');
@endphp

<div class="mb-6">
    <a href="{{ route('members.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        Back to Members
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    {{-- Profile Card --}}
    <div class="lg:col-span-1">
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gold-200 dark:border-gold-800 overflow-hidden shadow-sm">
            <div class="bg-gradient-to-r from-primary-600 to-primary-700 px-6 py-6">
                <div class="flex flex-col items-center text-center">
                    <div class="w-24 h-24 rounded-full bg-white/20 flex items-center justify-center text-white text-4xl font-bold shadow-lg mb-3">
                        {{ strtoupper(substr($member->full_name, 0, 2)) }}
                    </div>
                    <h2 class="text-xl font-bold text-white">{{ $member->full_name }}</h2>
                    <p class="text-primary-200 text-sm mt-1">{{ $member->position ?? 'No position' }}</p>
                    <div class="mt-3 flex flex-wrap gap-2 justify-center">
                        @php
                            $roleColors = [
                                'System Administrator' => 'bg-gold-100 text-gold-700 dark:bg-gold-900/50 dark:text-gold-300',
                                'Supreme Admin' => 'bg-primary-100 text-primary-700 dark:bg-primary-900/50 dark:text-primary-300',
                                'Supreme Officer' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300',
                                'Org Admin' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-300',
                                'Org Officer' => 'bg-sky-100 text-sky-700 dark:bg-sky-900/50 dark:text-sky-300',
                                'Club Adviser' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-300',
                                'Org Member' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
                            ];
                            $colorClass = $roleColors[$member->role->name] ?? 'bg-gray-100 text-gray-700';
                        @endphp
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium {{ $colorClass }}">
                            {{ $member->role->name }}
                        </span>
                        @if($member->role->abbreviation)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-mono bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">
                            {{ $member->role->abbreviation }}
                        </span>
                        @endif
                    </div>
                </div>
            </div>

            <div class="p-6 space-y-4">
                <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gold-800">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Email</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $member->email }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gold-800">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Member Since</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $memberSince }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gold-800">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Status</span>
                    <span class="text-sm font-medium">
                        @if($member->is_active)
                            <span class="inline-flex items-center gap-1 text-green-600 dark:text-green-400">
                                <span class="w-2 h-2 rounded-full bg-green-500"></span>
                                Active
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 text-red-600 dark:text-red-400">
                                <span class="w-2 h-2 rounded-full bg-red-500"></span>
                                Inactive
                            </span>
                        @endif
                    </span>
                </div>
                @if($member->student_id)
                <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gold-800">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Student ID</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $member->student_id }}</span>
                </div>
                @endif
                @if($member->year_level)
                <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gold-800">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Year Level</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $member->year_level }}</span>
                </div>
                @endif
                {{-- New Fields: Gender, Phone, Birthday --}}
                @if($member->gender)
                <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gold-800">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Gender</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $member->gender }}</span>
                </div>
                @endif
                @if($member->phone)
                <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gold-800">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Phone</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $member->phone }}</span>
                </div>
                @endif
                @if($member->birthday)
                <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gold-800">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Birthday</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $member->birthday->format('F d, Y') }}</span>
                </div>
                @endif
                <div class="flex justify-between items-center py-2">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Role Level</span>
                    <span class="text-sm font-medium text-gray-900 dark:text-white">Level {{ $member->role->level }}</span>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="px-6 py-4 border-t border-gray-100 dark:border-gold-800 bg-gray-50 dark:bg-gray-800/50">
                <div class="grid grid-cols-2 gap-3">
                    {{-- Edit Profile Button --}}
                    <a href="{{ route('members.edit', $member->id) }}" 
                       class="flex items-center justify-center gap-2 bg-primary-600 hover:bg-gold-500 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit Profile
                    </a>
                    
                    {{-- View History Button --}}
                    <a href="{{ route('members.edit-history', $member->id) }}" 
                       class="flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        View History
                    </a>
                </div>
                
                <div class="mt-3 grid grid-cols-1 gap-3">
                    {{-- Deactivate/Activate Account Button --}}
                    @if($canManageAccounts && $member->id !== auth()->id())
                        @if($member->is_active)
                            <button onclick="toggleAccountStatus('{{ $member->id }}', '{{ $member->full_name }}', 'deactivate')" 
                                    class="flex items-center justify-center gap-2 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                                </svg>
                                Deactivate Account
                            </button>
                        @else
                            <button onclick="toggleAccountStatus('{{ $member->id }}', '{{ $member->full_name }}', 'activate')" 
                                    class="flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Activate Account
                            </button>
                        @endif
                    @endif
                </div>
                
                {{-- Back Button --}}
                <div class="mt-3">
                    <a href="{{ route('members.index') }}" 
                       class="flex items-center justify-center gap-2 text-gray-600 dark:text-gray-400 border border-gray-300 dark:border-gray-600 px-4 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Members
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="lg:col-span-2 space-y-6">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gold-200 dark:border-gold-800 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Documents Uploaded</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $documentsCount }}</p>
                    </div>
                    <div class="w-12 h-12 bg-amber-100 dark:bg-amber-900/30 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gold-200 dark:border-gold-800 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Budgets Created</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $budgetsCount }}</p>
                    </div>
                    <div class="w-12 h-12 bg-emerald-100 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Activity --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gold-200 dark:border-gold-800 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-100 dark:border-gold-800 bg-gray-50 dark:bg-gray-800/50">
                <h3 class="font-semibold text-gray-900 dark:text-white">Recent Activity</h3>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($recentActivity ?? [] as $activity)
                <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center">
                            @if($activity['type'] === 'document')
                                <svg class="w-4 h-4 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            @elseif($activity['type'] === 'budget')
                                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            @else
                                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            @endif
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-900 dark:text-white">{{ $activity['description'] }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ $activity['time'] }}</p>
                        </div>
                    </div>
                </div>
                @empty
                <div class="p-12 text-center text-gray-500 dark:text-gray-400">
                    <p class="text-sm">No recent activity to display.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script>
function toggleAccountStatus(userId, userName, action) {
    const isDeactivate = action === 'deactivate';
    const message = isDeactivate 
        ? `⚠️ Are you sure you want to deactivate ${userName}'s account?\n\nDeactivated accounts cannot log in or access the system until reactivated.\n\nThis action can be undone by activating the account later.`
        : `✅ Are you sure you want to activate ${userName}'s account?\n\nActivated accounts will be able to log in and access the system again.`;
    
    if (confirm(message)) {
        const button = event.currentTarget;
        const originalText = button.innerHTML;
        
        button.innerHTML = '<svg class="animate-spin w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg> Processing...';
        button.disabled = true;
        
        fetch(`/members/${userId}/${action}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                setTimeout(() => window.location.reload(), 1000);
            } else {
                alert(data.error || 'Failed to process request');
                button.innerHTML = originalText;
                button.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
            button.innerHTML = originalText;
            button.disabled = false;
        });
    }
}
</script>

@endsection