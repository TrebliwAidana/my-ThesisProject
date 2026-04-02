@extends('layouts.app')

@section('title', 'Members — VSULHS_SSLG')

@section('content')
<style>
    /* Custom pagination styling */
    .pagination {
        @apply flex justify-center space-x-1;
    }
    .pagination .page-item {
        @apply list-none;
    }
    .pagination .page-link {
        @apply px-3 py-1.5 text-sm rounded-lg transition-all duration-200;
        @apply bg-white text-gray-700 border border-gold-200 dark:bg-gray-800 dark:text-gray-300 dark:border-gold-800;
    }
    .pagination .page-link:hover:not(.active) {
        @apply bg-gold-100 dark:bg-gold-900/30 border-gold-300 dark:border-gold-700;
    }
    .pagination .active .page-link {
        @apply bg-primary-600 text-white border-primary-600 dark:bg-primary-700 dark:border-primary-700;
    }
    .pagination .active .page-link:hover {
        @apply bg-primary-700;
    }
    .pagination .disabled .page-link {
        @apply opacity-50 cursor-not-allowed;
    }
</style>

@php
    $currentUser = auth()->user();
    $isSystemAdmin = $currentUser->role_id == 1;

    $roleBadgeClasses = [
        'System Administrator' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/50 dark:text-purple-300',
        'Supreme Admin'        => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/50 dark:text-indigo-300',
        'Supreme Officer'      => 'bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300',
        'Org Admin'            => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-300',
        'Org Officer'          => 'bg-sky-100 text-sky-700 dark:bg-sky-900/50 dark:text-sky-300',
        'Club Adviser'         => 'bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-300',
        'Org Member'           => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
        'Guest'                => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400',
    ];
@endphp

{{-- Emerald Gradient Header --}}
<div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-primary-600 to-primary-700 dark:from-primary-800 dark:to-primary-900 p-6 mb-6">
    <div class="relative z-10">
        <h1 class="text-2xl font-bold text-white">Members</h1>
        <p class="text-primary-100 text-sm mt-1">Manage organization members by role</p>
    </div>
    <div class="absolute top-0 right-0 -mt-8 -mr-8 w-64 h-64 bg-white/5 rounded-full blur-3xl"></div>
</div>

{{-- Statistics Cards (filtered) --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-5 mb-6">
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gold-200 dark:border-gold-800 p-6 shadow-sm hover:shadow-lg transition-all duration-300 text-center">
        <div class="flex items-center justify-center mb-3">
            <div class="w-12 h-12 bg-purple-50 dark:bg-purple-900/30 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $filteredStats['system_admin'] }}</p>
        <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mt-1">System Admins</p>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Full system control</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gold-200 dark:border-gold-800 p-6 shadow-sm hover:shadow-lg transition-all duration-300 text-center">
        <div class="flex items-center justify-center mb-3">
            <div class="w-12 h-12 bg-indigo-50 dark:bg-indigo-900/30 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $filteredStats['supreme'] }}</p>
        <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mt-1">Supreme Level</p>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Level 3 and above</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gold-200 dark:border-gold-800 p-6 shadow-sm hover:shadow-lg transition-all duration-300 text-center">
        <div class="flex items-center justify-center mb-3">
            <div class="w-12 h-12 bg-emerald-50 dark:bg-emerald-900/30 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $filteredStats['leaders'] }}</p>
        <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mt-1">Org Leaders</p>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Admins and Officers</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gold-200 dark:border-gold-800 p-6 shadow-sm hover:shadow-lg transition-all duration-300 text-center">
        <div class="flex items-center justify-center mb-3">
            <div class="w-12 h-12 bg-amber-50 dark:bg-amber-900/30 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $filteredStats['advisers'] }}</p>
        <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mt-1">Club Advisers</p>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Organization advisers</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gold-200 dark:border-gold-800 p-6 shadow-sm hover:shadow-lg transition-all duration-300 text-center">
        <div class="flex items-center justify-center mb-3">
            <div class="w-12 h-12 bg-slate-50 dark:bg-slate-900/30 rounded-xl flex items-center justify-center">
                <svg class="w-6 h-6 text-slate-600 dark:text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                </svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $filteredStats['members'] }}</p>
        <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mt-1">Regular Members</p>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Organization members</p>
    </div>
</div>

{{-- Search & Filter Form with Add Member button inline --}}
<form method="GET" action="{{ route('members.index') }}" id="filter-form" class="mb-6">
    <div class="flex flex-wrap gap-3 items-end">
        {{-- Search Input with datalist --}}
        <div class="relative flex-1 sm:flex-initial">
            <input type="text"
                   name="search"
                   id="search-input"
                   list="member-suggestions"
                   value="{{ request('search') }}"
                   placeholder="Search by name, email, role..."
                   class="w-full sm:w-80 pl-11 pr-11 py-2.5 text-sm border border-gold-200 dark:border-gold-800 rounded-xl focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-transparent dark:bg-gray-800 dark:text-gray-200 bg-gray-50 dark:bg-gray-800/50">
            <datalist id="member-suggestions">
                @foreach($users as $member)
                    <option value="{{ $member->full_name }}"></option>
                    <option value="{{ $member->email }}"></option>
                @endforeach
            </datalist>
            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                <svg class="h-4.5 w-4.5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            @if(request()->filled('search'))
            <a href="{{ route('members.index', array_merge(request()->except('search'), ['search' => ''])) }}"
               class="absolute inset-y-0 right-0 pr-3.5 flex items-center">
                <svg class="h-4.5 w-4.5 text-gray-400 hover:text-gray-600 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </a>
            @endif
        </div>

        {{-- Status Dropdown --}}
        <select name="status" class="border border-gold-200 dark:border-gold-800 rounded-xl px-4 py-2.5 text-sm bg-gray-50 dark:bg-gray-800/50 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500">
            <option value="">All Status</option>
            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>

        {{-- Verification Dropdown --}}
        <select name="verification" class="border border-gold-200 dark:border-gold-800 rounded-xl px-4 py-2.5 text-sm bg-gray-50 dark:bg-gray-800/50 text-gray-700 dark:text-gray-200 focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-gold-500">
            <option value="">All Verification</option>
            <option value="verified" {{ request('verification') == 'verified' ? 'selected' : '' }}>Verified</option>
            <option value="unverified" {{ request('verification') == 'unverified' ? 'selected' : '' }}>Unverified</option>
        </select>

        {{-- Apply Filters Button --}}
        <button type="submit" class="bg-primary-600 hover:bg-gold-500 text-white px-4 py-2.5 rounded-xl text-sm font-medium transition shadow-sm hover:shadow-md transform hover:scale-[1.02] active:scale-[0.98]">
            Apply Filters
        </button>

        {{-- Add Member Button (inline, right-aligned) --}}
        @if($isSystemAdmin || $currentUser->hasPermission('members.create'))
        <a href="{{ route('members.create') }}"
           class="inline-flex items-center justify-center gap-2 bg-primary-600 hover:bg-gold-500 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-[1.02] ml-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Add Member
        </a>
        @endif

        {{-- Clear All Filters (only if filters active) --}}
        @if(request()->hasAny(['search', 'status', 'verification', 'role']) && request()->input('role') != 'all')
        <a href="{{ route('members.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 px-4 py-2.5 rounded-xl text-sm transition">
            Clear Filters
        </a>
        @endif
    </div>
</form>

{{-- Role Filter Tabs (gold active) --}}
<div class="border-b border-gold-200 dark:border-gold-800 mb-6">
    <nav class="flex flex-wrap gap-4">
        <a href="{{ route('members.index', array_merge(request()->except('role'), ['role' => 'all'])) }}"
           class="px-4 py-2 text-sm font-medium border-b-2 {{ $roleFilter == 'all' ? 'border-gold-500 text-gold-600 dark:text-gold-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400' }}">
            All Members
            <span class="ml-1 text-xs bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded-full">{{ $filteredStats['all'] }}</span>
        </a>
        <a href="{{ route('members.index', array_merge(request()->except('role'), ['role' => 'admin'])) }}"
           class="px-4 py-2 text-sm font-medium border-b-2 {{ $roleFilter == 'admin' ? 'border-gold-500 text-gold-600 dark:text-gold-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400' }}">
            System Admin
            <span class="ml-1 text-xs bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded-full">{{ $filteredStats['system_admin'] }}</span>
        </a>
        <a href="{{ route('members.index', array_merge(request()->except('role'), ['role' => 'supreme'])) }}"
           class="px-4 py-2 text-sm font-medium border-b-2 {{ $roleFilter == 'supreme' ? 'border-gold-500 text-gold-600 dark:text-gold-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400' }}">
            Supreme
            <span class="ml-1 text-xs bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded-full">{{ $filteredStats['supreme'] }}</span>
        </a>
        <a href="{{ route('members.index', array_merge(request()->except('role'), ['role' => 'org-leader'])) }}"
           class="px-4 py-2 text-sm font-medium border-b-2 {{ $roleFilter == 'org-leader' ? 'border-gold-500 text-gold-600 dark:text-gold-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400' }}">
            Org Leaders
            <span class="ml-1 text-xs bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded-full">{{ $filteredStats['leaders'] }}</span>
        </a>
        <a href="{{ route('members.index', array_merge(request()->except('role'), ['role' => 'adviser'])) }}"
           class="px-4 py-2 text-sm font-medium border-b-2 {{ $roleFilter == 'adviser' ? 'border-gold-500 text-gold-600 dark:text-gold-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400' }}">
            Club Advisers
            <span class="ml-1 text-xs bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded-full">{{ $filteredStats['advisers'] }}</span>
        </a>
        <a href="{{ route('members.index', array_merge(request()->except('role'), ['role' => 'member'])) }}"
           class="px-4 py-2 text-sm font-medium border-b-2 {{ $roleFilter == 'member' ? 'border-gold-500 text-gold-600 dark:text-gold-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400' }}">
            Members
            <span class="ml-1 text-xs bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded-full">{{ $filteredStats['members'] }}</span>
        </a>
    </nav>
</div>

{{-- Members Table with Emerald Header --}}
<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gold-200 dark:border-gold-800 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-primary-600 dark:bg-primary-700 text-white border-b border-gold-200 dark:border-gold-800">
                    <th class="text-left px-6 py-4 text-xs font-semibold uppercase tracking-wider">Member</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold uppercase tracking-wider">Email</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold uppercase tracking-wider">Role</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold uppercase tracking-wider">Verified</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold uppercase tracking-wider">Status</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold uppercase tracking-wider">Level</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold uppercase tracking-wider">Joined</th>
                    <th class="text-right px-6 py-4 text-xs font-semibold uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse ($users as $member)
                @php
                    $color = $roleColors[$member->role->name] ?? 'gray';
                    $badgeClass = $roleBadgeClasses[$member->role->name] ?? 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300';
                @endphp
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-all duration-150">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-{{ $color }}-100 to-{{ $color }}-200 dark:from-{{ $color }}-900/50 dark:to-{{ $color }}-800/50 flex items-center justify-center text-sm font-bold text-{{ $color }}-700 dark:text-{{ $color }}-300 shadow-sm">
                                {{ strtoupper(substr($member->full_name, 0, 2)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-white">{{ $member->full_name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $member->position ?? 'No position' }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-600 dark:text-gray-400 text-sm">{{ $member->email }}</td>
                    <td class="px-6 py-4">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium {{ $badgeClass }}">
                            {{ $member->role->abbreviation ?? $member->role->name }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        @if($member->email_verified_at)
                            <span class="inline-flex items-center gap-1 text-green-600 dark:text-green-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Verified
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 text-yellow-600 dark:text-yellow-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                                Unverified
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if ($member->is_active)
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-300">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                Active
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-300">
                                <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                Inactive
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-2">
                            <div class="w-12 h-1.5 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                <div class="h-full rounded-full bg-primary-500" style="width: {{ min(($member->role->level / 8) * 100, 100) }}%"></div>
                            </div>
                            <span class="text-xs text-gray-500">Lv.{{ $member->role->level }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-500 dark:text-gray-400 text-sm">
                        {{ optional($member->created_at)->format('M d, Y') }}
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('members.show', $member->id) }}" class="p-1.5 text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 transition-colors rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700" title="View member" aria-label="View member details">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </a>
                            @if($isSystemAdmin || $currentUser->hasPermission('members.edit'))
                            <a href="{{ route('members.edit', $member->id) }}" class="p-1.5 text-gray-500 hover:text-primary-600 dark:text-gray-400 dark:hover:text-primary-400 transition-colors rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700" title="Edit member" aria-label="Edit member">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </a>
                            @endif
                            <a href="{{ route('members.edit-history', $member->id) }}" class="p-1.5 text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 transition-colors rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700" title="View history" aria-label="View edit history">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </a>
                            @if(($isSystemAdmin || $currentUser->hasPermission('members.delete')) && $member->id !== auth()->id())
                            <button type="button" onclick="confirmDelete('{{ $member->id }}', '{{ $member->full_name }}', '{{ $member->role->name }}')" class="p-1.5 text-gray-500 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-400 transition-colors rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700" title="Remove member" aria-label="Delete member">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <svg class="w-12 h-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <p class="text-gray-500 dark:text-gray-400 text-sm italic">No members found.</p>
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

    @if ($users->hasPages())
    <div class="px-6 py-4 border-t border-gray-100 dark:border-gold-800 bg-gray-50 dark:bg-gray-800/50">
        {{ $users->links() }}
    </div>
    @endif
</div>

<script>
    function confirmDelete(userId, userName, userRole) {
        const systemRoles = ['System Administrator', 'Supreme Admin', 'Supreme Officer'];
        if (systemRoles.includes(userRole)) {
            alert(`⚠️ Cannot delete "${userName}".\n\nThis user has a system role (${userRole}) which is required for the system to function properly.`);
            return;
        }
        if (userRole === 'Club Adviser') {
            const message = `⚠️ WARNING: You are about to delete ${userName}, who is a CLUB ADVISER.\n\nMake sure this is NOT the last club adviser in the system!\n\nDeleting the last club adviser will lock you out of admin features.\n\nAre you absolutely sure you want to continue?`;
            if (confirm(message)) {
                document.getElementById(`delete-form-${userId}`).submit();
            }
        } else {
            const message = `⚠️ You are about to delete ${userName}.\n\nRole: ${userRole}\n\nThis action cannot be undone.\n\nAre you sure you want to delete this user?`;
            if (confirm(message)) {
                document.getElementById(`delete-form-${userId}`).submit();
            }
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('search-input');
        if (searchInput) {
            let timeout = null;
            searchInput.addEventListener('input', function() {
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    document.getElementById('filter-form').submit();
                }, 500);
            });
        }
    });
</script>

@foreach($users as $member)
<form id="delete-form-{{ $member->id }}" action="{{ route('members.destroy', $member->id) }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endforeach

@endsection