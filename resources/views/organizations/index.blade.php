@extends('layouts.app')

@section('title', 'Organizations — VSULHS_SSLG')
@section('page-title', 'Organizations')

@section('content')
@php $user = auth()->user(); @endphp

{{-- Header --}}
<div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-primary-600 to-primary-700 dark:from-primary-800 dark:to-primary-900 p-6 mb-6">
    <div class="relative z-10 flex items-start justify-between flex-wrap gap-3">
        <div>
            <h1 class="text-2xl font-bold text-white">Organizations</h1>
            <p class="text-primary-100 text-sm mt-1">Manage school organizations and clubs</p>
        </div>
        @if($user->role->level === 1)
        <a href="{{ route('admin.organizations.create') }}"
           class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 text-white text-sm font-semibold px-4 py-2 rounded-xl transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            New Organization
        </a>
        @endif
    </div>
    <div class="absolute top-0 right-0 -mt-8 -mr-8 w-64 h-64 bg-white/5 rounded-full blur-3xl pointer-events-none"></div>
</div>

{{-- Stats --}}
<div class="grid grid-cols-2 sm:grid-cols-3 gap-4 mb-6">
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gold-200 dark:border-gold-800 p-5 shadow-sm hover:shadow-lg transition-all text-center">
        <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $totalOrgs }}</p>
        <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mt-1">Total Organizations</p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gold-200 dark:border-gold-800 p-5 shadow-sm hover:shadow-lg transition-all text-center">
        <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $activeOrgs }}</p>
        <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mt-1">Active</p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gold-200 dark:border-gold-800 p-5 shadow-sm hover:shadow-lg transition-all text-center">
        <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $totalMembers }}</p>
        <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mt-1">Total Members</p>
    </div>
</div>

{{-- Filters --}}
<div class="bg-white dark:bg-gray-800 rounded-2xl border border-gold-200 dark:border-gold-800 p-4 mb-5 shadow-sm">
    <form method="GET" class="flex flex-wrap gap-3 items-end">
        <div class="flex-1 min-w-[200px]">
            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Search</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Name or abbreviation..."
                   class="w-full px-3 py-2 border border-gold-200 dark:border-gold-800 rounded-xl text-sm bg-gray-50 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-gold-500 focus:outline-none">
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Type</label>
            <select name="type" class="px-3 py-2 border border-gold-200 dark:border-gold-800 rounded-xl text-sm bg-gray-50 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-gold-500 focus:outline-none">
                <option value="">All Types</option>
                @foreach($types as $key => $label)
                <option value="{{ $key }}" {{ request('type') == $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Status</label>
            <select name="status" class="px-3 py-2 border border-gold-200 dark:border-gold-800 rounded-xl text-sm bg-gray-50 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-gold-500 focus:outline-none">
                <option value="">All</option>
                <option value="active"   {{ request('status') == 'active'   ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="bg-primary-600 hover:bg-gold-500 text-white px-4 py-2 rounded-xl text-sm font-medium transition">
                Apply
            </button>
            @if(request()->anyFilled(['search','type','status']))
            <a href="{{ route('admin.organizations.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-xl text-sm transition">Clear</a>
            @endif
        </div>
    </form>
</div>

{{-- Organizations Table --}}
<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gold-200 dark:border-gold-800 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-primary-600 dark:bg-primary-700 text-white">
                    <th class="text-left px-5 py-3.5 text-xs font-semibold uppercase tracking-wider">Organization</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold uppercase tracking-wider">Type</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold uppercase tracking-wider">Adviser</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold uppercase tracking-wider">Members</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold uppercase tracking-wider">Docs</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold uppercase tracking-wider">Year</th>
                    <th class="text-left px-5 py-3.5 text-xs font-semibold uppercase tracking-wider">Status</th>
                    <th class="text-right px-5 py-3.5 text-xs font-semibold uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($organizations as $org)
                @php
                    $typeColors = [
                        'ssg'      => 'bg-purple-100 text-purple-700 dark:bg-purple-900/50 dark:text-purple-300',
                        'club'     => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-300',
                        'sports'   => 'bg-orange-100 text-orange-700 dark:bg-orange-900/50 dark:text-orange-300',
                        'academic' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300',
                        'cultural' => 'bg-pink-100 text-pink-700 dark:bg-pink-900/50 dark:text-pink-300',
                    ];
                    $typeBadge = $typeColors[$org->type] ?? 'bg-gray-100 text-gray-700';
                @endphp
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-primary-100 to-primary-200 dark:from-primary-900/50 dark:to-primary-800/50 flex items-center justify-center text-sm font-bold text-primary-700 dark:text-primary-300 flex-shrink-0">
                                {{ strtoupper(substr($org->abbreviation ?? $org->name, 0, 2)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-white">{{ $org->name }}</p>
                                @if($org->abbreviation)
                                <p class="text-xs text-gray-500 dark:text-gray-400 font-mono">{{ $org->abbreviation }}</p>
                                @endif
                            </div>
                        </div>
                    </td>
                    <td class="px-5 py-3.5">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $typeBadge }}">
                            {{ $org->type_label }}
                        </span>
                    </td>
                    <td class="px-5 py-3.5 text-gray-600 dark:text-gray-400">
                        {{ $org->adviser?->full_name ?? '—' }}
                    </td>
                    <td class="px-5 py-3.5">
                        <div class="flex items-center gap-2">
                            <span class="font-semibold text-gray-800 dark:text-white">{{ $org->users_count }}</span>
                            <span class="text-xs text-gray-400">members</span>
                        </div>
                    </td>
                    <td class="px-5 py-3.5 text-gray-600 dark:text-gray-400">{{ $org->documents_count }}</td>
                    <td class="px-5 py-3.5 text-gray-500 dark:text-gray-400 text-xs font-mono">
                        {{ $org->academic_year ?? '—' }}
                    </td>
                    <td class="px-5 py-3.5">
                        @if($org->is_active)
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-300">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Active
                        </span>
                        @else
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">
                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span> Inactive
                        </span>
                        @endif
                    </td>
                    <td class="px-5 py-3.5 text-right">
                        <div class="flex items-center justify-end gap-1">
                            <a href="{{ route('admin.organizations.show', $org) }}"
                               class="p-1.5 text-gray-400 hover:text-blue-600 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition" title="View">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                            </a>
                            @if($user->role->level === 1 || $user->organization_id === $org->id)
                            <a href="{{ route('admin.organizations.edit', $org) }}"
                               class="p-1.5 text-gray-400 hover:text-primary-600 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                            </a>
                            @endif
                            @if($user->role->level === 1)
                            <form action="{{ route('admin.organizations.destroy', $org) }}" method="POST"
                                  onsubmit="return confirm('Delete {{ $org->name }}? This cannot be undone.')" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition" title="Delete">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                    </svg>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-5 py-12 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <svg class="w-16 h-16 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                            <p class="text-gray-500 dark:text-gray-400">No organizations found.</p>
                            @if($user->role->level === 1)
                            <a href="{{ route('admin.organizations.create') }}" class="text-primary-600 hover:underline text-sm">Create the first organization</a>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($organizations->hasPages())
    <div class="px-5 py-3 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
        {{ $organizations->links() }}
    </div>
    @endif
</div>
@endsection