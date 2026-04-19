@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="space-y-8">

    {{-- Header - Welcome Section with dynamic greeting --}}
    @php
        $hour = now()->format('H');
        $greeting = match(true) {
            $hour < 12 => 'Good morning',
            $hour < 18 => 'Good afternoon',
            default => 'Good evening'
        };
    @endphp
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-primary-600 to-primary-800 dark:from-primary-800 dark:to-primary-900 p-6 md:p-8">
        <div class="relative z-10">
            <h1 class="text-2xl md:text-3xl font-bold text-white tracking-tight">
                {{ $greeting }}, {{ $user->first_name ?: $user->email }}!
            </h1>
            <div class="flex flex-wrap items-center gap-3 mt-3">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-white/20 rounded-full text-sm text-white">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    {{ $user->role->name }}
                </span>
                @if($user->role->abbreviation)
                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-white/20 rounded-full text-sm text-white">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                    {{ $user->role->abbreviation }}
                </span>
                @endif
                @if($user->position)
                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-white/20 rounded-full text-sm text-white">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    {{ $user->position }}
                </span>
                @endif
                @if(isset($userBadges) && count($userBadges) > 0)
                    @foreach($userBadges as $badge)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-{{ $badge['color'] }}-500/30 rounded-full text-sm text-white">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                        </svg>
                        {{ $badge['text'] }}
                    </span>
                    @endforeach
                @endif
            </div>
            <p class="text-primary-100 text-sm md:text-base mt-4 max-w-2xl">
                {{ $roleDescription }}
            </p>
            @if($pendingTasksCount > 0)
            <div class="mt-4 inline-flex items-center gap-2 bg-white/20 rounded-full px-3 py-1">
                <span class="w-2 h-2 rounded-full bg-red-500 animate-pulse"></span>
                <span class="text-xs text-white">You have {{ $pendingTasksCount }} pending task(s)</span>
            </div>
            @endif
        </div>
        <div class="absolute top-0 right-0 -mt-8 -mr-8 w-64 h-64 bg-white/5 rounded-full blur-3xl"></div>
    </div>

    {{-- Summary Cards (Income, Expense, Balance, Pending) --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gold-200 dark:border-gold-800 p-4 shadow-sm overflow-hidden">
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Income</p>
            <p class="text-xl sm:text-2xl md:text-3xl font-bold text-emerald-600 break-all">
                ₱{{ number_format($incomeTotal, 2) }}
            </p>
            <p class="text-xs text-gray-400 mt-1">Approved income</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gold-200 dark:border-gold-800 p-4 shadow-sm overflow-hidden">
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Expenses</p>
            <p class="text-xl sm:text-2xl md:text-3xl font-bold text-red-500 break-all">
                ₱{{ number_format($expenseTotal, 2) }}
            </p>
            <p class="text-xs text-gray-400 mt-1">Approved expenses</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gold-200 dark:border-gold-800 p-4 shadow-sm overflow-hidden">
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Balance</p>
            <p class="text-xl sm:text-2xl md:text-3xl font-bold break-all {{ $balance >= 0 ? 'text-emerald-600' : 'text-red-600' }}">
                ₱{{ number_format($balance, 2) }}
            </p>
            <p class="text-xs text-gray-400 mt-1">Net balance</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gold-200 dark:border-gold-800 p-4 shadow-sm overflow-hidden">
            <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pending</p>
            <p class="text-xl sm:text-2xl md:text-3xl font-bold text-amber-500 break-all">{{ $pendingTransactions }}</p>
            <p class="text-xs text-gray-400 mt-1">Awaiting action</p>
        </div>
    </div>

    {{-- Financial Chart with Range Toggle (Weekly / Monthly / Yearly) --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gold-200 dark:border-gold-800 p-6">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 bg-indigo-100 dark:bg-indigo-900/30 rounded-lg flex items-center justify-center">
                    <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <h3 class="font-semibold text-gray-900 dark:text-white">Income vs Expenses</h3>
            </div>
            {{-- Range Toggle Buttons --}}
            <div class="flex gap-2">
                <a href="{{ route('dashboard', ['range' => 'weekly']) }}"
                   class="px-3 py-1 text-xs rounded-lg {{ $range === 'weekly' ? 'bg-emerald-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300' }} transition">
                    Weekly
                </a>
                <a href="{{ route('dashboard', ['range' => 'monthly']) }}"
                   class="px-3 py-1 text-xs rounded-lg {{ $range === 'monthly' ? 'bg-emerald-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300' }} transition">
                    Monthly
                </a>
                <a href="{{ route('dashboard', ['range' => 'yearly']) }}"
                   class="px-3 py-1 text-xs rounded-lg {{ $range === 'yearly' ? 'bg-emerald-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300' }} transition">
                    Yearly
                </a>
            </div>
        </div>
        <div class="relative h-64 min-h-[16rem]">
            <canvas id="financialChart"></canvas>
        </div>
    </div>

    {{-- Stats Grid (unchanged, but kept for context) --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="group bg-white dark:bg-gray-800 rounded-2xl border border-gold-200 dark:border-gold-800 p-6 hover:shadow-lg transition-all duration-300 hover:scale-[1.02] overflow-hidden">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-primary-50 dark:bg-primary-900/50 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
                <span class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-800 dark:text-white break-all">{{ number_format($totalMembers) }}</span>
            </div>
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Members</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">All registered members</p>
        </div>

        <div class="group bg-white dark:bg-gray-800 rounded-2xl border border-gold-200 dark:border-gold-800 p-6 hover:shadow-lg transition-all duration-300 hover:scale-[1.02] overflow-hidden">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-green-50 dark:bg-green-900/50 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <span class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-800 dark:text-white break-all">{{ number_format($activeMembers) }}</span>
            </div>
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Active Members</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Currently active in organization</p>
        </div>

        <div class="group bg-white dark:bg-gray-800 rounded-2xl border border-gold-200 dark:border-gold-800 p-6 hover:shadow-lg transition-all duration-300 hover:scale-[1.02] overflow-hidden">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-sky-50 dark:bg-sky-900/50 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-sky-600 dark:text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <span class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-800 dark:text-white break-all">{{ number_format($officersCount) }}</span>
            </div>
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Leaders & Officers</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Administrators, Advisers, and Officers</p>
        </div>

        <div class="group bg-white dark:bg-gray-800 rounded-2xl border border-gold-200 dark:border-gold-800 p-6 hover:shadow-lg transition-all duration-300 hover:scale-[1.02] overflow-hidden">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 bg-gold-50 dark:bg-gold-900/50 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6 text-gold-600 dark:text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                    </svg>
                </div>
                <span class="text-xl sm:text-2xl md:text-3xl font-bold text-gray-800 dark:text-white break-all">{{ number_format($newMembersThisMonth) }}</span>
            </div>
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">New Members</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Joined in {{ now()->format('F Y') }}</p>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="flex flex-wrap gap-3">
        @if($user->hasPermission('members.create'))
        <a href="{{ route('members.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-primary-600 hover:bg-gold-500 text-white text-sm font-medium rounded-xl transition shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Add Member
        </a>
        @endif
        @if($user->hasPermission('documents.create'))
        <a href="{{ route('documents.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-gold-500 text-white text-sm font-medium rounded-xl transition shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
            Upload Document
        </a>
        @endif
        @if($user->hasPermission('financial.create'))
        <a href="{{ route('financial.income.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-gold-500 text-white text-sm font-medium rounded-xl transition shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Add Income
        </a>
        <a href="{{ route('financial.expense.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-rose-600 hover:bg-gold-500 text-white text-sm font-medium rounded-xl transition shadow-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
            </svg>
            Add Expense
        </a>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Profile Card --}}
        <div class="lg:col-span-1">
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gold-200 dark:border-gold-800 overflow-hidden lg:sticky lg:top-6">
                <div class="bg-gradient-to-r from-primary-600 to-primary-700 px-6 py-5">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center">
                            @if($user->avatar_url)
                                <img src="{{ $user->avatar_url }}" alt="{{ $user->full_name }}'s avatar" class="w-10 h-10 rounded-full object-cover" loading="lazy">
                            @else
                                <div class="w-10 h-10 rounded-full bg-white/30 flex items-center justify-center text-white font-bold text-lg">
                                    {{ strtoupper(substr($user->full_name, 0, 2)) }}
                                </div>
                            @endif
                        </div>
                        <div>
                            <h3 class="text-white font-semibold text-lg">Profile Information</h3>
                            <p class="text-primary-200 text-sm">Your personal details</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="flex flex-col items-center text-center mb-6">
                        <div class="w-24 h-24 rounded-full bg-gradient-to-br from-primary-500 to-primary-600 flex items-center justify-center text-white text-3xl font-bold shadow-lg mb-3 overflow-hidden">
                            @if($user->avatar_url)
                                <img src="{{ $user->avatar_url }}" alt="{{ $user->full_name }}" class="w-full h-full object-cover" loading="lazy">
                            @else
                                {{ strtoupper(substr($user->full_name, 0, 2)) }}
                            @endif
                        </div>
                        <h4 class="text-lg font-bold text-gray-900 dark:text-white">{{ $user->full_name }}</h4>
                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                        <div class="flex flex-wrap gap-2 mt-3 justify-center">
                            <span class="px-3 py-1 bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-400 rounded-full text-xs font-medium">Active</span>
                            @if($user->role->abbreviation)
                            <span class="px-3 py-1 bg-primary-100 dark:bg-primary-900/50 text-primary-700 dark:text-primary-400 rounded-full text-xs font-medium">{{ $user->role->abbreviation }}</span>
                            @endif
                            @if($user->hasVerifiedEmail())
                                <span class="px-3 py-1 bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-400 rounded-full text-xs font-medium">Verified</span>
                            @else
                                <span class="px-3 py-1 bg-yellow-100 dark:bg-yellow-900/50 text-yellow-700 dark:text-yellow-400 rounded-full text-xs font-medium">Unverified</span>
                            @endif
                        </div>
                        <a href="{{ route('profile.index') }}" class="mt-4 inline-flex items-center gap-1 text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400" aria-label="Edit Profile">
                            Edit Profile <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </a>
                    </div>
                    <div class="space-y-4">
                        <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gold-800"><span class="text-sm text-gray-500 dark:text-gray-400">Role</span><span class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->role->name }}</span></div>
                        @if($user->role->abbreviation)<div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gold-800"><span class="text-sm text-gray-500 dark:text-gray-400">Abbreviation</span><span class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->role->abbreviation }}</span></div>@endif
                        <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gold-800"><span class="text-sm text-gray-500 dark:text-gray-400">Position</span><span class="text-sm font-medium text-gray-900 dark:text-white">{{ $user->member?->position ?? $user->position ?? '—' }}</span></div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gold-800"><span class="text-sm text-gray-500 dark:text-gray-400">Member Since</span><span class="text-sm font-medium text-gray-900 dark:text-white">{{ optional($user->member?->joined_at ?? $user->member?->term_start ?? $user->created_at)->format('M d, Y') ?? '—' }}</span></div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-100 dark:border-gold-800"><span class="text12-sm text-gray-500 dark:text-gray-400">Last Login</span><span class="text-sm font-medium text-gray-900 dark:text-white">{{ optional($user->last_login_at)->format('M d, Y H:i') ?? 'Never' }}</span></div>
                        <div class="flex justify-between items-center py-2"><span class="text-sm text-gray-500 dark:text-gray-400">Last Updated</span><span class="text-sm font-medium text-gray-900 dark:text-white">{{ optional($user->updated_at)->format('M d, Y') }}</span></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right column --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Recent Documents --}}
            @if($user->hasPermission('documents.view') && isset($recentDocuments) && count($recentDocuments) > 0)
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gold-200 dark:border-gold-800 overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gold-800">
                    <div class="flex items-center gap-3"><div class="w-8 h-8 bg-amber-100 dark:bg-amber-900/30 rounded-lg flex items-center justify-center"><svg class="w-4 h-4 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg></div><h3 class="font-semibold text-gray-900 dark:text-white">Recent Documents</h3></div>
                    <a href="{{ route('documents.index') }}" class="text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400 font-medium flex items-center gap-1">View all <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></a>
                </div>
                <ul class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($recentDocuments as $doc)
                    <li class="flex items-center gap-4 px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                        <div class="w-10 h-10 bg-amber-50 dark:bg-amber-900/30 rounded-xl flex items-center justify-center flex-shrink-0"><svg class="w-5 h-5 text-amber-500 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg></div>
                        <div class="flex-1 min-w-0"><p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $doc->title }}</p><p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $doc->uploader->full_name ?? 'Unknown' }} · {{ optional($doc->created_at)->diffForHumans() }}</p></div>
                    </li>
                    @empty
                    <li class="px-6 py-8 text-center text-sm text-gray-400 dark:text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        No documents yet. <a href="{{ route('documents.create') }}" class="text-primary-600 hover:underline">Upload your first document</a>
                    </li>
                    @endforelse
                </ul>
            </div>
            @endif

            {{-- Recent Transactions --}}
            @if($user->hasPermission('financial.view') && isset($recentTransactions) && count($recentTransactions) > 0)
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gold-200 dark:border-gold-800 overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gold-800">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 bg-emerald-100 dark:bg-emerald-900/30 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-900 dark:text-white">Recent Transactions</h3>
                    </div>
                    <a href="{{ route('financial.index') }}" class="text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400 font-medium flex items-center gap-1">View all <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></a>
                </div>
                <ul class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($recentTransactions as $tx)
                    <li class="flex items-center justify-between px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $tx->description }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $tx->type === 'income' ? 'Income' : 'Expense' }} · {{ optional($tx->date)->format('M d, Y') ?? optional($tx->created_at)->format('M d, Y') }}</p>
                        </div>
                        <span class="text-xs font-medium px-2.5 py-1 rounded-full {{ $tx->type === 'income' ? 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' : 'bg-rose-100 dark:bg-rose-900/30 text-rose-700 dark:text-rose-400' }}">
                            {{ $tx->type === 'income' ? '+' : '-' }} ₱{{ number_format($tx->amount, 2) }}
                        </span>
                    </li>
                    @empty
                    <li class="px-6 py-8 text-center text-sm text-gray-400 dark:text-gray-500">
                        <svg class="w-12 h-12 mx-auto mb-3 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        No transactions yet. <a href="{{ route('financial.income.create') }}" class="text-primary-600 hover:underline">Add your first income or expense</a>
                    </li>
                    @endforelse
                </ul>
            </div>
            @endif

            {{-- Pending Approvals --}}
            @if($user->hasPermission('financial.approve') && isset($pendingApprovals) && count($pendingApprovals) > 0)
            <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gold-200 dark:border-gold-800 overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gold-800">
                    <div class="flex items-center gap-3"><div class="w-8 h-8 bg-orange-100 dark:bg-orange-900/30 rounded-lg flex items-center justify-center"><svg class="w-4 h-4 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg></div><h3 class="font-semibold text-gray-900 dark:text-white">Pending Approvals</h3></div>
                    <a href="{{ route('financial.index') }}" class="text-sm text-primary-600 hover:text-primary-700 dark:text-primary-400 font-medium flex items-center gap-1">Review all <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></a>
                </div>
                <ul class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($pendingApprovals as $item)
                    <li class="flex items-center justify-between px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                        <div class="flex-1 min-w-0"><p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $item['title'] }}</p><p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $item['type'] }} · Submitted by {{ $item['submitter'] }}</p></div>
                        <div class="flex gap-2"><a href="{{ $item['link'] }}" class="text-xs px-3 py-1 bg-primary-100 dark:bg-primary-900/30 text-primary-700 dark:text-primary-300 rounded-full hover:bg-primary-200 transition">Review</a></div>
                    </li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Financial Summary Card --}}
            @if($user->hasPermission('financial.view') && isset($totalIncome))
            <div class="bg-gradient-to-r from-emerald-600 to-teal-600 dark:from-emerald-800 dark:to-teal-800 rounded-2xl p-6 overflow-hidden">
                <div class="flex items-center justify-between">
                    <div class="min-w-0 flex-1">
                        <p class="text-emerald-100 text-sm font-medium">Total Income (All Time)</p>
                        <p class="text-2xl md:text-3xl font-bold text-white mt-1 break-all">₱{{ number_format($totalIncome, 2) }}</p>
                        <p class="text-emerald-100 text-xs mt-2 break-all">Total Expenses: ₱{{ number_format($totalExpense, 2) }} | Net: ₱{{ number_format($netBalance, 2) }}</p>
                    </div>
                    <div class="w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center flex-shrink-0 ml-3">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Chart.js Script --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('financialChart').getContext('2d');
        const chartData = @json($chartData);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: chartData.labels,
                datasets: [
                    {
                        label: 'Income',
                        data: chartData.income,
                        backgroundColor: '#059669',
                        borderRadius: 6
                    },
                    {
                        label: 'Expense',
                        data: chartData.expense,
                        backgroundColor: '#EF4444',
                        borderRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { color: document.documentElement.classList.contains('dark') ? '#E2E8F0' : '#1E293B' }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let value = context.raw;
                                return context.dataset.label + ': ₱' + value.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: value => '₱' + value.toLocaleString()
                        }
                    }
                }
            }
        });
    });
</script>
@endsection