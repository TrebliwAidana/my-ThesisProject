@extends('layouts.app')

@section('title', 'Members — VSULHS_SSLG')

@section('content')

@php
    $currentUser    = auth()->user();
    $isSystemAdmin  = $currentUser->role->level === 1;
    $guestEmail     = 'guest@gmail.com';

    // Badge classes keyed by role name — keep in sync with Role::name values in DB
    $roleBadgeClasses = [
        'System Administrator' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/50 dark:text-purple-300',
        'Club Adviser'         => 'bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-300',
        'Treasurer'            => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-300',
        'Auditor'              => 'bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300',
        'Guest'                => 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400',
    ];
    $avatarBg = [
        'System Administrator' => 'from-purple-400 to-purple-600',
        'Club Adviser'         => 'from-amber-400 to-amber-600',
        'Treasurer'            => 'from-emerald-400 to-emerald-600',
        'Auditor'              => 'from-blue-400 to-blue-600',
        'Guest'                => 'from-gray-300 to-gray-500',
    ];
@endphp

<div
    x-data="{
        search: '{{ request('search') }}',
        searchTimeout: null,
        submitSearch() {
            clearTimeout(this.searchTimeout);
            this.searchTimeout = setTimeout(() => this.$refs.filterForm.submit(), 500);
        }
    }"
    class="space-y-6 md:space-y-8"
>

    {{-- ─── Header ─── --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-primary-600 to-primary-800 dark:from-primary-800 dark:to-primary-900 p-5 md:p-8">
        <div class="relative z-10 flex flex-wrap justify-between items-start gap-4">
            <div>
                <h1 class="text-xl md:text-3xl font-bold text-white tracking-tight">Members</h1>
                <div class="flex flex-wrap items-center gap-2 mt-2 md:mt-3">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 md:px-3 md:py-1 bg-white/20 rounded-full text-xs md:text-sm text-white">
                        <svg class="w-3 h-3 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        {{ $filteredStats['all'] }} total members
                    </span>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 md:px-3 md:py-1 bg-white/20 rounded-full text-xs md:text-sm text-white">
                        <svg class="w-3 h-3 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Manage organization members by role
                    </span>
                </div>
            </div>

            @if($isSystemAdmin || Gate::allows('members.create'))
            <a href="{{ route('members.create') }}"
               class="inline-flex items-center gap-2 px-3 py-1.5 md:px-4 md:py-2 bg-white/20 hover:bg-white/30 border border-white/30 text-white text-xs md:text-sm font-medium rounded-xl transition shadow-sm shrink-0">
                <svg class="w-3.5 h-3.5 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
                </svg>
                Add Member
            </a>
            @endif
        </div>
        <div class="absolute top-0 right-0 -mt-8 -mr-8 w-48 h-48 md:w-64 md:h-64 bg-white/5 rounded-full blur-3xl"></div>
    </div>

    {{-- ─── Stats Grid ─── --}}
    {{--
        Keys MUST match what MemberController@index puts in $filteredStats:
        'admin', 'adviser', 'treasurer', 'auditor', 'guest', 'custom', 'all'
    --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 md:gap-5">

        {{-- All --}}
        <div class="group bg-white dark:bg-gray-800 rounded-xl md:rounded-2xl border border-gold-200 dark:border-gold-800 p-4 md:p-5 hover:shadow-lg transition-all duration-300 hover:scale-[1.02]">
            <div class="flex items-center justify-between mb-3">
                <div class="w-9 h-9 bg-primary-50 dark:bg-primary-900/50 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <span class="text-xl md:text-2xl font-bold text-gray-800 dark:text-white">{{ number_format($filteredStats['all']) }}</span>
            </div>
            <p class="text-xs font-medium text-gray-600 dark:text-gray-400">All Members</p>
        </div>

        {{-- System Admin --}}
        <div class="group bg-white dark:bg-gray-800 rounded-xl md:rounded-2xl border border-gold-200 dark:border-gold-800 p-4 md:p-5 hover:shadow-lg transition-all duration-300 hover:scale-[1.02]">
            <div class="flex items-center justify-between mb-3">
                <div class="w-9 h-9 bg-purple-50 dark:bg-purple-900/50 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                </div>
                <span class="text-xl md:text-2xl font-bold text-gray-800 dark:text-white">{{ number_format($filteredStats['admin']) }}</span>
            </div>
            <p class="text-xs font-medium text-gray-600 dark:text-gray-400">System Admins</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Full system control</p>
        </div>

        {{-- Club Advisers --}}
        <div class="group bg-white dark:bg-gray-800 rounded-xl md:rounded-2xl border border-gold-200 dark:border-gold-800 p-4 md:p-5 hover:shadow-lg transition-all duration-300 hover:scale-[1.02]">
            <div class="flex items-center justify-between mb-3">
                <div class="w-9 h-9 bg-amber-50 dark:bg-amber-900/50 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <span class="text-xl md:text-2xl font-bold text-gray-800 dark:text-white">{{ number_format($filteredStats['adviser']) }}</span>
            </div>
            <p class="text-xs font-medium text-gray-600 dark:text-gray-400">Club Advisers</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Organization advisers</p>
        </div>

        {{-- Treasurers --}}
        <div class="group bg-white dark:bg-gray-800 rounded-xl md:rounded-2xl border border-gold-200 dark:border-gold-800 p-4 md:p-5 hover:shadow-lg transition-all duration-300 hover:scale-[1.02]">
            <div class="flex items-center justify-between mb-3">
                <div class="w-9 h-9 bg-emerald-50 dark:bg-emerald-900/50 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="text-xl md:text-2xl font-bold text-gray-800 dark:text-white">{{ number_format($filteredStats['treasurer']) }}</span>
            </div>
            <p class="text-xs font-medium text-gray-600 dark:text-gray-400">Treasurers</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Finance handlers</p>
        </div>

        {{-- Auditors --}}
        <div class="group bg-white dark:bg-gray-800 rounded-xl md:rounded-2xl border border-gold-200 dark:border-gold-800 p-4 md:p-5 hover:shadow-lg transition-all duration-300 hover:scale-[1.02]">
            <div class="flex items-center justify-between mb-3">
                <div class="w-9 h-9 bg-blue-50 dark:bg-blue-900/50 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                </div>
                <span class="text-xl md:text-2xl font-bold text-gray-800 dark:text-white">{{ number_format($filteredStats['auditor']) }}</span>
            </div>
            <p class="text-xs font-medium text-gray-600 dark:text-gray-400">Auditors</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Finance reviewers</p>
        </div>

        {{-- Guest + Custom --}}
        <div class="group bg-white dark:bg-gray-800 rounded-xl md:rounded-2xl border border-gold-200 dark:border-gold-800 p-4 md:p-5 hover:shadow-lg transition-all duration-300 hover:scale-[1.02]">
            <div class="flex items-center justify-between mb-3">
                <div class="w-9 h-9 bg-gray-50 dark:bg-gray-900/50 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                {{-- guest + custom combined for the "other" card --}}
                <span class="text-xl md:text-2xl font-bold text-gray-800 dark:text-white">{{ number_format($filteredStats['guest'] + $filteredStats['custom']) }}</span>
            </div>
            <p class="text-xs font-medium text-gray-600 dark:text-gray-400">Guest / Custom</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">Limited access</p>
        </div>

    </div>

    {{-- ─── Search & Filter ─── --}}
    <form method="GET" action="{{ route('members.index') }}" id="filter-form" x-ref="filterForm">
        <div class="flex flex-col sm:flex-row flex-wrap gap-3 items-stretch sm:items-center">

            {{-- Search --}}
            <div class="relative flex-1 min-w-[180px]">
                <input
                    type="text"
                    name="search"
                    x-model="search"
                    @input="submitSearch()"
                    list="member-suggestions"
                    placeholder="Search by name or email…"
                    class="w-full pl-9 pr-8 py-2 text-sm border border-gold-200 dark:border-gold-800 rounded-xl bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-transparent transition"
                >
                <datalist id="member-suggestions">
                    @foreach($users as $member)
                        <option value="{{ $member->full_name }}"></option>
                        <option value="{{ $member->email }}"></option>
                    @endforeach
                </datalist>
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                @if(request()->filled('search'))
                <a href="{{ route('members.index', array_merge(request()->except('search', 'page'))) }}"
                   class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </a>
                @endif
            </div>

            <div class="flex flex-wrap gap-2">
                <select name="status" onchange="this.form.submit()"
                    class="flex-1 sm:flex-none border border-gold-200 dark:border-gold-800 rounded-xl px-3 py-2 text-sm bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-gold-500 transition">
                    <option value="">All Status</option>
                    <option value="active"   {{ request('status') === 'active'   ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>

                <select name="verification" onchange="this.form.submit()"
                    class="flex-1 sm:flex-none border border-gold-200 dark:border-gold-800 rounded-xl px-3 py-2 text-sm bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-gold-500 transition">
                    <option value="">All Verification</option>
                    <option value="verified"   {{ request('verification') === 'verified'   ? 'selected' : '' }}>Verified</option>
                    <option value="unverified" {{ request('verification') === 'unverified' ? 'selected' : '' }}>Unverified</option>
                </select>

                @if(request()->hasAny(['search', 'status', 'verification']) || (request()->filled('role') && request('role') !== 'all'))
                <a href="{{ route('members.index') }}"
                   class="inline-flex items-center justify-center text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 px-3 py-2 rounded-xl border border-gold-200 dark:border-gold-800 transition">
                    Clear
                </a>
                @endif
            </div>
        </div>

        {{-- Role Tabs — keys match controller's match() + $filteredStats keys exactly --}}
        <div class="mt-5 border-b border-gold-200 dark:border-gold-800 overflow-x-auto pb-px">
            <nav class="flex flex-nowrap sm:flex-wrap gap-1 -mb-px min-w-max sm:min-w-0">
                @php
                    $tabs = [
                        ['key' => 'all',      'label' => 'All',          'count' => $filteredStats['all']],
                        ['key' => 'admin',    'label' => 'System Admin', 'count' => $filteredStats['admin']],
                        ['key' => 'adviser',  'label' => 'Adviser',      'count' => $filteredStats['adviser']],
                        ['key' => 'treasurer','label' => 'Treasurer',    'count' => $filteredStats['treasurer']],
                        ['key' => 'auditor',  'label' => 'Auditor',      'count' => $filteredStats['auditor']],
                        ['key' => 'guest',    'label' => 'Guest',        'count' => $filteredStats['guest']],
                        ['key' => 'custom',   'label' => 'Custom',       'count' => $filteredStats['custom']],
                    ];
                @endphp
                @foreach($tabs as $tab)
                <a href="{{ route('members.index', array_merge(request()->except('role', 'page'), ['role' => $tab['key']])) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-2 text-xs sm:text-sm font-medium border-b-2 transition-colors whitespace-nowrap
                       {{ $roleFilter === $tab['key']
                            ? 'border-gold-500 text-gold-600 dark:text-gold-400'
                            : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:border-gold-300 dark:hover:border-gold-700' }}">
                    {{ $tab['label'] }}
                    <span class="text-xs bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 rounded-full px-1.5 py-0.5">
                        {{ $tab['count'] }}
                    </span>
                </a>
                @endforeach
            </nav>
        </div>
    </form>

    {{-- ─── Members Table ─── --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gold-200 dark:border-gold-800 overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="min-w-full w-full text-sm">
                <thead>
                    <tr class="bg-primary-600 dark:bg-primary-700 text-white border-b border-gold-200">
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wide">Member</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wide">Email</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wide">Role</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wide">Verified</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wide">Status</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wide">Level</th>
                        <th class="text-left px-4 py-3 text-xs font-semibold uppercase tracking-wide">Joined</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold uppercase tracking-wide">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">

                    @forelse ($users as $member)
                    @php
                        $badgeClass   = $roleBadgeClasses[$member->role->name] ?? 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300';
                        $gradientBg   = $avatarBg[$member->role->name]         ?? 'from-gray-400 to-gray-600';
                        $initials     = strtoupper(mb_substr($member->full_name, 0, 2));
                        $isGuest      = $member->email === $guestEmail;
                        $canEditGuest = $isGuest && $isSystemAdmin;
                    @endphp

                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">

                        {{-- Member name + position --}}
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                @if($member->avatar)
                                    {{-- Handle both Cloudinary (absolute URL) and local storage paths --}}
                                    <img src="{{ str_starts_with($member->avatar, 'http') ? $member->avatar : asset('storage/' . $member->avatar) }}"
                                         alt="{{ $member->full_name }}"
                                         class="w-8 h-8 md:w-9 md:h-9 rounded-xl object-cover shadow-sm flex-shrink-0">
                                @else
                                    <div class="w-8 h-8 md:w-9 md:h-9 rounded-xl bg-gradient-to-br {{ $gradientBg }}
                                         flex items-center justify-center text-xs md:text-sm font-bold text-white shadow-sm flex-shrink-0">
                                        {{ $initials }}
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <p class="font-semibold text-gray-900 dark:text-white truncate max-w-[140px] md:max-w-none">
                                        {{ $member->full_name }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate max-w-[140px] md:max-w-none">
                                        {{ $member->position ?? '—' }}
                                    </p>
                                </div>
                            </div>
                        </td>

                        {{-- Email --}}
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400 max-w-[160px] truncate">
                            {{ $member->email }}
                        </td>

                        {{-- Role badge --}}
                        <td class="px-4 py-3">
                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium {{ $badgeClass }}">
                                {{ $member->role->abbreviation ?? $member->role->name }}
                            </span>
                        </td>

                        {{-- Verified --}}
                        <td class="px-4 py-3">
                            @if($member->email_verified_at)
                                <span class="inline-flex items-center gap-1 text-green-600 dark:text-green-400 text-xs">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Verified
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 text-yellow-600 dark:text-yellow-400 text-xs">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                                    </svg>
                                    Unverified
                                </span>
                            @endif
                        </td>

                        {{-- Status --}}
                        <td class="px-4 py-3">
                            @if($member->is_active)
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-300">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>Active
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-300">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>Inactive
                                </span>
                            @endif
                        </td>

                        {{-- Role level progress --}}
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-12 h-1.5 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full bg-primary-500"
                                         style="width: {{ min(($member->role->level / 8) * 100, 100) }}%">
                                    </div>
                                </div>
                                <span class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">Lv.{{ $member->role->level }}</span>
                            </div>
                        </td>

                        {{-- Joined date --}}
                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400 whitespace-nowrap text-xs md:text-sm">
                            {{ optional($member->created_at)->format('M d, Y') }}
                        </td>

                        {{-- Actions --}}
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1.5">

                                @if($isGuest && !$isSystemAdmin)
                                    <span class="text-xs text-gray-400 dark:text-gray-500 italic px-2 py-1">System Account</span>
                                @else
                                    {{-- View --}}
                                    <a href="{{ route('members.show', $member->id) }}"
                                       class="p-1.5 rounded-lg text-gray-500 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors"
                                       title="View">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>

                                    {{-- Edit —only for non-guest, OR system admin editing guest --}}
                                    @if(($isSystemAdmin || Gate::allows('members.edit')) && (!$isGuest || $canEditGuest))
                                    <a href="{{ route('members.edit', $member->id) }}"
                                       class="p-1.5 rounded-lg text-gray-500 hover:text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors"
                                       title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    @endif

                                    {{-- History --}}
                                    <a href="{{ route('members.edit-history', $member->id) }}"
                                       class="p-1.5 rounded-lg text-gray-500 hover:text-gold-600 hover:bg-gold-50 dark:hover:bg-gold-900/20 transition-colors"
                                       title="History">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </a>

                                    {{-- Delete — never for guest, never for self --}}
                                    @if(!$isGuest && ($isSystemAdmin || Gate::allows('members.delete')) && $member->id !== auth()->id())
                                    <button
                                        type="button"
                                        onclick="confirmDelete('{{ $member->id }}', '{{ addslashes($member->full_name) }}', '{{ addslashes($member->role->name) }}', '{{ $member->email }}')"
                                        class="p-1.5 rounded-lg text-gray-500 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors"
                                        title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                        </svg>
                                    </button>
                                    @endif
                                @endif

                            </div>
                        </td>
                    </tr>

                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-10 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="w-10 h-10 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <p class="text-gray-500 dark:text-gray-400 text-sm">No members found.</p>
                                @if(request()->hasAny(['search', 'status', 'verification']) || (request()->filled('role') && request('role') !== 'all'))
                                    <a href="{{ route('members.index') }}" class="text-primary-600 hover:underline text-sm">Clear all filters</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforelse

                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if($users->hasPages())
        <div class="px-4 py-3 border-t border-gold-100 dark:border-gold-800 bg-gray-50 dark:bg-gray-800/50 flex flex-wrap items-center justify-between gap-3">
            <p class="text-xs text-gray-400 dark:text-gray-500">
                Showing {{ $users->firstItem() }}–{{ $users->lastItem() }} of {{ $users->total() }} members
            </p>
            <div class="flex items-center gap-1">
                @if($users->onFirstPage())
                    <span class="px-2.5 py-1 text-xs rounded-lg border border-gold-200 dark:border-gold-800 text-gray-300 dark:text-gray-600 cursor-not-allowed">Prev</span>
                @else
                    <a href="{{ $users->previousPageUrl() }}"
                       class="px-2.5 py-1 text-xs rounded-lg border border-gold-200 dark:border-gold-800 text-gray-600 dark:text-gray-400 hover:bg-gold-50 dark:hover:bg-gold-900/20 hover:border-gold-400 transition-colors">Prev</a>
                @endif

                @foreach($users->getUrlRange(max(1, $users->currentPage() - 2), min($users->lastPage(), $users->currentPage() + 2)) as $page => $url)
                    @if($page == $users->currentPage())
                        <span class="px-2.5 py-1 text-xs rounded-lg border border-primary-600 bg-primary-600 text-white font-medium">{{ $page }}</span>
                    @else
                        <a href="{{ $url }}"
                           class="px-2.5 py-1 text-xs rounded-lg border border-gold-200 dark:border-gold-800 text-gray-600 dark:text-gray-400 hover:bg-gold-50 dark:hover:bg-gold-900/20 hover:border-gold-400 transition-colors">{{ $page }}</a>
                    @endif
                @endforeach

                @if($users->hasMorePages())
                    <a href="{{ $users->nextPageUrl() }}"
                       class="px-2.5 py-1 text-xs rounded-lg border border-gold-200 dark:border-gold-800 text-gray-600 dark:text-gray-400 hover:bg-gold-50 dark:hover:bg-gold-900/20 hover:border-gold-400 transition-colors">Next</a>
                @else
                    <span class="px-2.5 py-1 text-xs rounded-lg border border-gold-200 dark:border-gold-800 text-gray-300 dark:text-gray-600 cursor-not-allowed">Next</span>
                @endif
            </div>
        </div>
        @endif
    </div>
</div>

{{-- Hidden delete forms — guest excluded (delete button is never rendered for guest) --}}
@foreach($users as $member)
    @if($member->email !== $guestEmail)
    <form id="delete-form-{{ $member->id }}" action="{{ route('members.destroy', $member->id) }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>
    @endif
@endforeach

<script>
function confirmDelete(userId, userName, userRole, userEmail) {
    if (userEmail === '{{ $guestEmail }}') {
        alert('The shared guest account cannot be deleted.');
        return;
    }
    const msg = `You are about to delete ${userName} (${userRole}).\n\nThis action cannot be undone. Continue?`;
    if (confirm(msg)) {
        document.getElementById(`delete-form-${userId}`)?.submit();
    }
}
</script>

@endsection