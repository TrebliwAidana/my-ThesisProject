@extends('layouts.app')

@section('title', 'Members — VSULHS_SSLG')

@section('content')
@php
    $currentUser = auth()->user();
    $isSystemAdmin = $currentUser->role_id == 1;
@endphp

{{-- Header --}}
<div class="mb-6">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 dark:from-white dark:to-gray-300 bg-clip-text text-transparent">
                Members
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                Manage organization members by role
            </p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
            {{-- Search Form (GET) --}}
            <form method="GET" action="{{ route('members.index') }}" class="relative group">
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Search by name, email, role..."
                       class="w-full sm:w-80 pl-11 pr-11 py-2.5 text-sm border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-800 dark:text-gray-200 transition-all duration-200 bg-gray-50 dark:bg-gray-800/50">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <svg class="h-4.5 w-4.5 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                @if(request()->filled('search'))
                <a href="{{ route('members.index') }}" class="absolute inset-y-0 right-0 pr-3.5 flex items-center">
                    <svg class="h-4.5 w-4.5 text-gray-400 hover:text-gray-600 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </a>
                @endif
            </form>

            {{-- Add Member Button (only if user has permission) --}}
            @if($isSystemAdmin || $currentUser->hasPermission('members.create'))
            <a href="{{ route('members.create') }}"
               class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-[1.02]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Member
            </a>
            @endif
        </div>
    </div>
</div>

{{-- Statistics Cards (global totals) --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl p-4 text-white shadow-lg">
        <p class="text-xs opacity-90">System Admins</p>
        <p class="text-2xl font-bold">{{ $totalStats['system_admin'] }}</p>
    </div>
    <div class="bg-gradient-to-br from-indigo-500 to-indigo-600 rounded-xl p-4 text-white shadow-lg">
        <p class="text-xs opacity-90">Supreme Level</p>
        <p class="text-2xl font-bold">{{ $totalStats['supreme'] }}</p>
    </div>
    <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-xl p-4 text-white shadow-lg">
        <p class="text-xs opacity-90">Org Leaders</p>
        <p class="text-2xl font-bold">{{ $totalStats['leaders'] }}</p>
    </div>
    <div class="bg-gradient-to-br from-slate-500 to-slate-600 rounded-xl p-4 text-white shadow-lg">
        <p class="text-xs opacity-90">Regular Members</p>
        <p class="text-2xl font-bold">{{ $totalStats['members'] }}</p>
    </div>
</div>

{{-- Role Filter Tabs (server-side) --}}
<div class="border-b border-gray-200 dark:border-gray-700 mb-6">
    <nav class="flex flex-wrap gap-4">
        <a href="{{ route('members.index', array_merge(request()->except('role'), ['role' => 'all'])) }}"
           class="px-4 py-2 text-sm font-medium border-b-2 {{ $roleFilter == 'all' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400' }}">
            All Members
            <span class="ml-1 text-xs bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded-full">{{ $totalStats['all'] }}</span>
        </a>
        <a href="{{ route('members.index', array_merge(request()->except('role'), ['role' => 'admin'])) }}"
           class="px-4 py-2 text-sm font-medium border-b-2 {{ $roleFilter == 'admin' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400' }}">
            System Admin
            <span class="ml-1 text-xs bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded-full">{{ $totalStats['system_admin'] }}</span>
        </a>
        <a href="{{ route('members.index', array_merge(request()->except('role'), ['role' => 'supreme'])) }}"
           class="px-4 py-2 text-sm font-medium border-b-2 {{ $roleFilter == 'supreme' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400' }}">
            Supreme
            <span class="ml-1 text-xs bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded-full">{{ $totalStats['supreme'] }}</span>
        </a>
        <a href="{{ route('members.index', array_merge(request()->except('role'), ['role' => 'org-leader'])) }}"
           class="px-4 py-2 text-sm font-medium border-b-2 {{ $roleFilter == 'org-leader' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400' }}">
            Org Leaders
            <span class="ml-1 text-xs bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded-full">{{ $totalStats['leaders'] }}</span>
        </a>
        <a href="{{ route('members.index', array_merge(request()->except('role'), ['role' => 'adviser'])) }}"
           class="px-4 py-2 text-sm font-medium border-b-2 {{ $roleFilter == 'adviser' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400' }}">
            Club Advisers
            <span class="ml-1 text-xs bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded-full">{{ $totalStats['advisers'] }}</span>
        </a>
        <a href="{{ route('members.index', array_merge(request()->except('role'), ['role' => 'member'])) }}"
           class="px-4 py-2 text-sm font-medium border-b-2 {{ $roleFilter == 'member' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400' }}">
            Members
            <span class="ml-1 text-xs bg-gray-100 dark:bg-gray-700 px-1.5 py-0.5 rounded-full">{{ $totalStats['members'] }}</span>
        </a>
    </nav>
</div>

{{-- Members Table --}}
<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <th class="text-left px-6 py-4 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Member</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Email</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Role</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Status</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Level</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Joined</th>
                    <th class="text-right px-6 py-4 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse ($users as $member)
                @php
                    $color = $roleColors[$member->role->name] ?? 'gray';
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
                        <div class="relative group">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-{{ $color }}-100 text-{{ $color }}-700 dark:bg-{{ $color }}-900/50 dark:text-{{ $color }}-300">
                                {{ $member->role->abbreviation ?? $member->role->name }}
                            </span>
                            <div class="absolute bottom-full left-0 mb-2 hidden group-hover:block z-20">
                                <div class="bg-gray-900 text-white text-xs rounded-lg py-2 px-3 w-48 shadow-xl">
                                    <p class="font-semibold mb-1">{{ $member->role->name }}</p>
                                    <p class="text-gray-300 text-xs">{{ $member->role->desc ?? 'No description' }}</p>
                                    <p class="text-gray-400 text-xs mt-1">Level {{ $member->role->level }}</p>
                                </div>
                            </div>
                        </div>
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
                                <div class="h-full rounded-full bg-indigo-500" style="width: {{ min(($member->role->level / 8) * 100, 100) }}%"></div>
                            </div>
                            <span class="text-xs text-gray-500">Lv.{{ $member->role->level }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-500 dark:text-gray-400 text-sm">
                        {{ optional($member->created_at)->format('M d, Y') }}
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('members.show', $member->id) }}"
                               class="p-1.5 text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 transition-colors rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
                               title="View member">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </a>
                            @if($isSystemAdmin || $currentUser->hasPermission('members.edit'))
                            <a href="{{ route('members.edit', $member->id) }}"
                               class="p-1.5 text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400 transition-colors rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
                               title="Edit member">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>
                            @endif
                            <a href="{{ route('members.edit-history', $member->id) }}"
                               class="p-1.5 text-gray-500 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 transition-colors rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
                               title="View history">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </a>
                            @if(($isSystemAdmin || $currentUser->hasPermission('members.delete')) && $member->id !== auth()->id())
                            <button type="button"
                                    onclick="confirmDelete('{{ $member->id }}', '{{ $member->full_name }}', '{{ $member->role->name }}')"
                                    class="p-1.5 text-gray-500 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-400 transition-colors rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
                                    title="Remove member">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <svg class="w-12 h-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <p class="text-gray-500 dark:text-gray-400 text-sm italic">No members found.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($users->hasPages())
    <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
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
</script>

@foreach($users as $member)
<form id="delete-form-{{ $member->id }}" action="{{ route('members.destroy', $member->id) }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endforeach

@endsection