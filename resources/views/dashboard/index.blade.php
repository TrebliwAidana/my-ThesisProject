@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-8">

    {{-- Header - Welcome Section --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-indigo-600 to-indigo-800 dark:from-indigo-900 dark:to-indigo-950 p-6 md:p-8">
        <div class="relative z-10">
            <h1 class="text-2xl md:text-3xl font-bold text-white tracking-tight">
                Welcome back, {{ $user->full_name }}!
            </h1>
            <div class="flex flex-wrap items-center gap-3 mt-3">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-white/20 rounded-full text-sm text-white">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    {{ $user->role->name }}
                </span>
                @if($user->position)
                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-white/20 rounded-full text-sm text-white">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    {{ $user->position }}
                </span>
                @endif
            </div>
            <p class="text-indigo-100 text-sm md:text-base mt-4 max-w-2xl">
                Here's what's happening with your organization today. Monitor member activity, recent documents, and budget status at a glance.
            </p>
        </div>
        <div class="absolute top-0 right-0 -mt-8 -mr-8 w-64 h-64 bg-white/5 rounded-full blur-3xl"></div>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        {{-- Total Members Card --}}
        <div class="group bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg transition-all duration-300 hover:scale-[1.02]">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-indigo-50 dark:bg-indigo-900/50 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <span class="text-3xl font-bold text-gray-800 dark:text-white">{{ $totalMembers }}</span>
            </div>
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Members</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">All registered members</p>
        </div>

        {{-- Active Members Card --}}
        <div class="group bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg transition-all duration-300 hover:scale-[1.02]">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-50 dark:bg-green-900/50 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <span class="text-3xl font-bold text-gray-800 dark:text-white">{{ $activeMembers }}</span>
            </div>
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Active Members</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Currently active in organization</p>
        </div>

        {{-- Officers Card --}}
        <div class="group bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg transition-all duration-300 hover:scale-[1.02]">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-sky-50 dark:bg-sky-900/50 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-sky-600 dark:text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <span class="text-3xl font-bold text-gray-800 dark:text-white">{{ $officersCount }}</span>
            </div>
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Officers & Advisers</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Leadership team members</p>
        </div>

        {{-- New Members This Month Card --}}
        <div class="group bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 hover:shadow-lg transition-all duration-300 hover:scale-[1.02]">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-purple-50 dark:bg-purple-900/50 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                </div>
                <span class="text-3xl font-bold text-gray-800 dark:text-white">{{ $newMembersThisMonth }}</span>
            </div>
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">New Members</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Joined in {{ now()->format('F Y') }}</p>
        </div>
    </div>

    {{-- Two Column Layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Profile Card --}}
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden sticky top-6">
                <div class="bg-gradient-to-r from-indigo-600 to-indigo-700 px-6 py-5">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-white font-semibold text-lg">Profile Information</h3>
                            <p class="text-indigo-200 text-sm">Your personal details</p>
                        </div>
                    </div>
                </div>

                <div class="p-6">
                    <div class="flex flex-col items-center text-center mb-6">
                        <div class="w-24 h-24 rounded-full bg-gradient-to-br from-indigo-500 to-indigo-600 flex items-center justify-center text-white text-3xl font-bold shadow-lg mb-3">
                            {{ strtoupper(substr($user->full_name, 0, 2)) }}
                        </div>
                        <h4 class="text-lg font-bold text-gray-900 dark:text-white">{{ $user->full_name }}</h4>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                        <div class="flex gap-2 mt-3">
                            <span class="px-3 py-1 bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-400 rounded-full text-xs font-medium">
                                Active
                            </span>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Role</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->role->name }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Position</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->member?->position ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Member Since</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ optional($user->member?->joined_at ?? $user->member?->term_start)->format('M d, Y') ?? '—' }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gray-700">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Current Term</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">
                                @if($user->member)
                                    {{ optional($user->member->term_start)->format('M d, Y') }} - 
                                    {{ optional($user->member->term_end)->format('M d, Y') ?? 'Present' }}
                                @else
                                    —
                                @endif
                            </span>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <span class="text-sm text-gray-500 dark:text-gray-400">Last Updated</span>
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ optional($user->updated_at)->format('M d, Y') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent Activity Section --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Recent Documents --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-amber-100 dark:bg-amber-900/30 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-900 dark:text-white">Recent Documents</h3>
                    </div>
                    <a href="{{ route('documents.index') }}" class="text-sm text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium flex items-center gap-1">
                        View all
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
                <ul class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($recentDocuments as $doc)
                    <li class="flex items-center gap-4 px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                        <div class="w-10 h-10 bg-amber-50 dark:bg-amber-900/30 rounded-xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-5 h-5 text-amber-500 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $doc->title }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                {{ $doc->uploader->full_name ?? 'Unknown' }} · {{ optional($doc->uploaded_at ?? $doc->created_at)->diffForHumans() }}
                            </p>
                        </div>
                    </li>
                    @empty
                    <li class="px-6 py-8 text-center text-sm text-gray-400 dark:text-gray-500">No documents yet.</li>
                    @endforelse
                </ul>
            </div>

            {{-- Recent Budgets --}}
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-900 dark:text-white">Recent Budgets</h3>
                    </div>
                    <a href="{{ route('budgets.index') }}" class="text-sm text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 dark:hover:text-indigo-300 font-medium flex items-center gap-1">
                        View all
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
                <ul class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($recentBudgets as $budget)
                    @php 
                        $statusColors = [
                            'approved' => 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400',
                            'rejected' => 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400',
                            'pending' => 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-700 dark:text-yellow-400'
                        ];
                    @endphp
                    <li class="flex items-center justify-between px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ Str::limit($budget->description ?? $budget->desc ?? '—', 50) }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 font-mono">₱{{ number_format($budget->amount, 2) }}</p>
                        </div>
                        <span class="text-xs font-medium px-2.5 py-1 rounded-full ml-4 {{ $statusColors[$budget->status] ?? 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400' }}">
                            {{ ucfirst($budget->status) }}
                        </span>
                    </li>
                    @empty
                    <li class="px-6 py-8 text-center text-sm text-gray-400 dark:text-gray-500">No budget entries yet.</li>
                    @endforelse
                </ul>
            </div>

            {{-- Budget Summary Card --}}
            <div class="bg-gradient-to-r from-emerald-600 to-teal-600 dark:from-emerald-800 dark:to-teal-800 rounded-2xl p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-emerald-100 text-sm font-medium">Total Approved Budget</p>
                        <p class="text-3xl font-bold text-white mt-1">₱{{ number_format($totalBudget, 2) }}</p>
                        <p class="text-emerald-100 text-xs mt-2">All approved budget requests</p>
                    </div>
                    <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>
@endsection