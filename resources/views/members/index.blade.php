@extends('layouts.app')

@section('title', 'Members — VSULHS_SSLG')

@section('content')

@php
    $currentUser = auth()->user();
    $isSystemAdmin = $currentUser->role->level === 1;
    $guestEmail = 'guest@gmail.com';
    $roleBadgeClasses = [
        'System Administrator' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/50 dark:text-purple-300',
        'Supreme Admin'        => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300',
        'Supreme Officer'      => 'bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300',
        'Org Admin'            => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-300',
        'Org Officer'          => 'bg-sky-100 text-sky-700 dark:bg-sky-900/50 dark:text-sky-300',
        'Club Adviser'         => 'bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-300',
        'Org Member'           => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
        'Guest'                => 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400',
    ];
    $avatarBg = [
        'System Administrator' => 'from-purple-400 to-purple-600',
        'Supreme Admin'        => 'from-indigo-400 to-indigo-600',
        'Supreme Officer'      => 'from-blue-400 to-blue-600',
        'Org Admin'            => 'from-emerald-400 to-emerald-600',
        'Org Officer'          => 'from-sky-400 to-sky-600',
        'Club Adviser'         => 'from-amber-400 to-amber-600',
        'Org Member'           => 'from-gray-400 to-gray-600',
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

    {{-- ─── Header — responsive with flex layout to avoid overlap on mobile ─── --}}
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

            {{-- Add Member button — now part of flex flow, no overlap --}}
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

    {{-- ─── Stats Grid — responsive cards with touch-friendly sizing ─── --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 md:gap-5">
        <!-- System Admins -->
        <div class="group bg-white dark:bg-gray-800 rounded-xl md:rounded-2xl border border-gold-200 dark:border-gold-800 p-4 md:p-6 hover:shadow-lg transition-all duration-300 hover:scale-[1.02]">
            <div class="flex items-center justify-between mb-3 md:mb-4">
                <div class="w-10 h-10 md:w-12 md:h-12 bg-purple-50 dark:bg-purple-900/50 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 md:w-6 md:h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                </div>
                <span class="text-xl md:text-3xl font-bold text-gray-800 dark:text-white">{{ number_format($filteredStats['system_admin']) }}</span>
            </div>
            <p class="text-xs md:text-sm font-medium text-gray-600 dark:text-gray-400">System Admins</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5 md:mt-1">Full system control</p>
        </div>

        <!-- Supreme Level -->
        <div class="group bg-white dark:bg-gray-800 rounded-xl md:rounded-2xl border border-gold-200 dark:border-gold-800 p-4 md:p-6 hover:shadow-lg transition-all duration-300 hover:scale-[1.02]">
            <div class="flex items-center justify-between mb-3 md:mb-4">
                <div class="w-10 h-10 md:w-12 md:h-12 bg-indigo-50 dark:bg-indigo-900/50 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 md:w-6 md:h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <span class="text-xl md:text-3xl font-bold text-gray-800 dark:text-white">{{ number_format($filteredStats['supreme']) }}</span>
            </div>
            <p class="text-xs md:text-sm font-medium text-gray-600 dark:text-gray-400">Supreme Level</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5 md:mt-1">Level 3 and above</p>
        </div>

        <!-- Org Leaders -->
        <div class="group bg-white dark:bg-gray-800 rounded-xl md:rounded-2xl border border-gold-200 dark:border-gold-800 p-4 md:p-6 hover:shadow-lg transition-all duration-300 hover:scale-[1.02]">
            <div class="flex items-center justify-between mb-3 md:mb-4">
                <div class="w-10 h-10 md:w-12 md:h-12 bg-emerald-50 dark:bg-emerald-900/50 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 md:w-6 md:h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <span class="text-xl md:text-3xl font-bold text-gray-800 dark:text-white">{{ number_format($filteredStats['leaders']) }}</span>
            </div>
            <p class="text-xs md:text-sm font-medium text-gray-600 dark:text-gray-400">Org Leaders</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5 md:mt-1">Admins and Officers</p>
        </div>

        <!-- Club Advisers -->
        <div class="group bg-white dark:bg-gray-800 rounded-xl md:rounded-2xl border border-gold-200 dark:border-gold-800 p-4 md:p-6 hover:shadow-lg transition-all duration-300 hover:scale-[1.02]">
            <div class="flex items-center justify-between mb-3 md:mb-4">
                <div class="w-10 h-10 md:w-12 md:h-12 bg-amber-50 dark:bg-amber-900/50 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 md:w-6 md:h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <span class="text-xl md:text-3xl font-bold text-gray-800 dark:text-white">{{ number_format($filteredStats['advisers']) }}</span>
            </div>
            <p class="text-xs md:text-sm font-medium text-gray-600 dark:text-gray-400">Club Advisers</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5 md:mt-1">Organization advisers</p>
        </div>

        <!-- Regular Members -->
        <div class="group bg-white dark:bg-gray-800 rounded-xl md:rounded-2xl border border-gold-200 dark:border-gold-800 p-4 md:p-6 hover:shadow-lg transition-all duration-300 hover:scale-[1.02]">
            <div class="flex items-center justify-between mb-3 md:mb-4">
                <div class="w-10 h-10 md:w-12 md:h-12 bg-slate-50 dark:bg-slate-900/50 rounded-xl flex items-center justify-center">
                    <svg class="w-5 h-5 md:w-6 md:h-6 text-slate-600 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                </div>
                <span class="text-xl md:text-3xl font-bold text-gray-800 dark:text-white">{{ number_format($filteredStats['members']) }}</span>
            </div>
            <p class="text-xs md:text-sm font-medium text-gray-600 dark:text-gray-400">Regular Members</p>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5 md:mt-1">Organization members</p>
        </div>
    </div>

    {{-- ─── Search & Filter — responsive stacking on mobile ─── --}}
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
                    placeholder="Search by name, email, role…"
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
                <a href="{{ route('members.index', array_merge(request()->except('search'), ['search' => ''])) }}"
                   class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </a>
                @endif
            </div>

            <div class="flex flex-wrap gap-2">
                <select name="status"
                    class="flex-1 sm:flex-none border border-gold-200 dark:border-gold-800 rounded-xl px-3 py-2 text-sm bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-gold-500 transition">
                    <option value="">All Status</option>
                    <option value="active"   {{ request('status') == 'active'   ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>

                <select name="verification"
                    class="flex-1 sm:flex-none border border-gold-200 dark:border-gold-800 rounded-xl px-3 py-2 text-sm bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-300 focus:outline-none focus:ring-2 focus:ring-gold-500 transition">
                    <option value="">All Verification</option>
                    <option value="verified"   {{ request('verification') == 'verified'   ? 'selected' : '' }}>Verified</option>
                    <option value="unverified" {{ request('verification') == 'unverified' ? 'selected' : '' }}>Unverified</option>
                </select>

                <button type="submit"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-primary-600 hover:bg-gold-500 text-white text-sm font-medium rounded-xl transition shadow-sm">
                    Apply Filters
                </button>

                @if(request()->hasAny(['search', 'status', 'verification', 'role']) && request()->input('role') != 'all')
                <a href="{{ route('members.index') }}"
                   class="inline-flex items-center justify-center text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 px-3 py-2 rounded-xl transition">
                    Clear
                </a>
                @endif
            </div>
        </div>

        {{-- Role Tabs — responsive wrapping with touch-friendly taps --}}
        <div class="mt-5 border-b border-gold-200 dark:border-gold-800 overflow-x-auto pb-px">
            <nav class="flex flex-nowrap sm:flex-wrap gap-1 -mb-px min-w-max sm:min-w-0">
                @php
                    $tabs = [
                        ['key' => 'all',        'label' => 'All Members',  'count' => $filteredStats['all']],
                        ['key' => 'admin',      'label' => 'System Admin', 'count' => $filteredStats['system_admin']],
                        ['key' => 'supreme',    'label' => 'Supreme',      'count' => $filteredStats['supreme']],
                        ['key' => 'org-leader', 'label' => 'Org Leaders',  'count' => $filteredStats['leaders']],
                        ['key' => 'adviser',    'label' => 'Club Advisers','count' => $filteredStats['advisers']],
                        ['key' => 'member',     'label' => 'Members',      'count' => $filteredStats['members']],
                    ];
                @endphp
                @foreach($tabs as $tab)
                <a href="{{ route('members.index', array_merge(request()->except('role'), ['role' => $tab['key']])) }}"
                   class="inline-flex items-center gap-1.5 px-3 py-2 text-xs sm:text-sm font-medium border-b-2 transition-colors whitespace-nowrap
                       {{ $roleFilter == $tab['key']
                            ? 'border-gold-500 text-gold-600 dark:text-gold-400'
                            : 'border-transparent text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 hover:border-gold-300 dark:hover:border-gold-700' }}">
                    {{ $tab['label'] }}
                    <span class="text-xs bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 rounded-full px-1.5 py-0.5">{{ $tab['count'] }}</span>
                </a>
                @endforeach
            </nav>
        </div>
    </form>

    {{-- ─── Members Table — horizontal scroll enabled for mobile ─── --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gold-200 dark:border-gold-800 overflow-hidden shadow-xl">
        {{-- Overflow-x-auto ensures horizontal scroll on small screens --}}
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
                        <th class="text01-left px-4 py-3 text-xs font-semibold uppercase tracking-wide">Joined</th>
                        <th class="text-right px-4 py-3 text-xs font-semibold uppercase tracking-wide">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">

                    @forelse ($users as $member)
                    @php
                        $badgeClass  = $roleBadgeClasses[$member->role->name] ?? 'bg-gray-100 text-gray-700';
                        $gradientBg  = $avatarBg[$member->role->name]         ?? 'from-gray-400 to-gray-600';
                        $initials    = strtoupper(substr($member->full_name, 0, 2));
                        $isGuest     = $member->email === $guestEmail;
                        // For guest account, only show actions to System Admin; Delete always hidden for guest
                        $canEditGuest = $isGuest && $isSystemAdmin;
                    @endphp

                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                @if($member->avatar)
                                <img src="{{ str_starts_with($member->avatar, 'http') ? $member->avatar : asset('storage/' . $member->avatar) }}"
                                    alt="{{ $member->full_name }}"
                                    class="w-8 h-8 md:w-9 md:h-9 rounded-xl object-cover shadow-sm flex-shrink-0">
                                    <div class="w-8 h-8 md:w-9 md:h-9 rounded-xl bg-gradient-to-br {{ $gradientBg }} 
                                        flex items-center justify-center text-xs md:text-sm font-bold text-white shadow-sm flex-shrink-0">
                                        {{ $initials }}
                                    </div>
                                @endif
                                <div class="min-w-0">
                                    <p class="font-semibold text-gray-900 dark:text-white truncate max-w-[140px] md:max-w-none">{{ $member->full_name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 truncate max-w-[140px] md:max-w-none">{{ $member->position ?? 'No position' }}</p>
                                </div>
                            </div>
                        </td>

                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400 max-w-[160px] truncate">
                            {{ $member->email }}
                        </td>

                        <td class="px-4 py-3">
                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium {{ $badgeClass }}">
                                {{ $member->role->abbreviation ?? $member->role->name }}
                            </span>
                        </td>

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

                        <td class="px-4 py-3">
                            @if($member->is_active)
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-300">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                    Active
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-300">
                                    <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                    Inactive
                                </span>
                            @endif
                        </td>

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

                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400 whitespace-nowrap text-xs md:text-sm">
                            {{ optional($member->created_at)->format('M d, Y') }}
                        </td>

                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-1.5">
                                {{-- Guest account: show actions only to System Admin; no delete --}}
                                @if($isGuest && !$isSystemAdmin)
                                    <span class="text-xs text-gray-400 dark:text-gray-500 italic px-2 py-1">System Account</span>
                                @else
                                    {{-- View button always shown --}}
                                    <a href="{{ route('members.show', $member->id) }}"
                                       class="p-1.5 rounded-lg text-gray-500 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-colors"
                                       title="View">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                        </svg>
                                    </a>

                                    {{-- Edit button: hide for guest unless system admin --}}
                                    @if(($isSystemAdmin || Gate::allows('members.edit')) && (!$isGuest || $canEditGuest))
                                    <a href="{{ route('members.edit', $member->id) }}"
                                       class="p-1.5 rounded-lg text-gray-500 hover:text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors"
                                       title="Edit">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    @endif

                                    {{-- History button always shown (even for guest, system admin can see history) --}}
                                    <a href="{{ route('members.edit-history', $member->id) }}"
                                       class="p-1.5 rounded-lg text-gray-500 hover:text-gold-600 hover:bg-gold-50 dark:hover:bg-gold-900/20 transition-colors"
                                       title="History">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </a>

                                    {{-- Delete button: never shown for guest --}}
                                    @if(!$isGuest && ($isSystemAdmin || Gate::allows('members.delete')) && $member->id !== auth()->id())
                                    <button
                                        type="button"
                                        onclick="confirmDelete('{{ $member->id }}', '{{ $member->full_name }}', '{{ $member->role->name }}', '{{ $member->email }}')"
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
                                @if(request()->hasAny(['search', 'status', 'verification']) || (request()->has('role') && request()->role != 'all'))
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

{{-- Hidden delete forms --}}
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
    // Prevent deletion of guest account (extra guard)
    if (userEmail === '{{ $guestEmail }}') {
        alert('The shared guest account cannot be deleted.');
        return;
    }

    const systemRoles = ['System Administrator', 'Supreme Admin', 'Supreme Officer'];
    if (systemRoles.includes(userRole)) {
        alert(`Cannot delete "${userName}".\n\nThis user has a system role (${userRole}) required for the system to function.`);
        return;
    }
    const isAdviser = userRole === 'Club Adviser';
    const message = isAdviser
        ? `WARNING: You are about to delete ${userName} (Club Adviser).\n\nMake sure this is NOT the last club adviser in the system!\n\nAre you absolutely sure?`
        : `You are about to delete ${userName} (${userRole}).\n\nThis action cannot be undone. Continue?`;
    if (confirm(message)) {
        document.getElementById(`delete-form-${userId}`).submit();
    }
}
</script>

@endsection