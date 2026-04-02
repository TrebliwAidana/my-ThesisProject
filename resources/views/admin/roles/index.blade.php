@extends('layouts.app')

@section('title', 'Manage Roles')

@section('content')
<div class="space-y-6">

    {{-- Header with conditional create button --}}
    @php
        $user = auth()->user();
        $canManageRoles = $user && $user->role && $user->role->level <= 1; // adjust level as needed
    @endphp

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Roles Management</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage system roles and their permissions</p>
        </div>
        @if($canManageRoles)
            <a href="{{ route('admin.roles.create') }}"
               class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition shadow-sm">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Create New Role
            </a>
        @endif
    </div>

    {{-- Flash messages with auto-hide --}}
    @if(session('success'))
        <div class="p-4 bg-green-50 border border-green-200 rounded-xl text-sm text-green-700 flex justify-between items-center flash-message">
            <span>✅ {{ session('success') }}</span>
            <button type="button" class="text-green-700 hover:text-green-900" onclick="this.parentElement.remove()">✕</button>
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 bg-red-50 border border-red-200 rounded-xl text-sm text-red-700 flex justify-between items-center flash-message">
            <span>❌ {{ session('error') }}</span>
            <button type="button" class="text-red-700 hover:text-red-900" onclick="this.parentElement.remove()">✕</button>
        </div>
    @endif

    {{-- Search / Filter --}}
    <form method="GET" action="{{ route('admin.roles.index') }}" class="flex gap-2">
        <input type="text" name="search" value="{{ request('search') }}"
               placeholder="Search by name or abbreviation..."
               class="flex-1 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white">
        <button type="submit" class="px-4 py-2 bg-gray-200 dark:bg-gray-700 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </button>
        @if(request('search'))
            <a href="{{ route('admin.roles.index') }}" class="px-4 py-2 text-gray-500 hover:text-gray-700">Clear</a>
        @endif
    </form>

    {{-- Roles Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800 border-b border-gray-200 dark:border-gray-700">
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide">ID</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide">Role</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide">Abbreviation</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide">Description</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide">Level</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide">Users</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide">Created</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wide">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($roles as $role)
                        @php
                            $level = $role->level ?? 1;
                            $color = $level <= 2 ? 'purple' : ($level <= 4 ? 'blue' : 'gray');
                            $levelTooltip = match($level) {
                                1 => 'Highest authority – System Administrator',
                                2 => 'Supreme Admin – oversees all organizations',
                                3 => 'Supreme Officer – cross‑org support',
                                4 => 'Club Adviser – faculty advisor',
                                5 => 'Org Admin – full control of one organization',
                                6 => 'Org Officer – limited control of one organization',
                                7 => 'Org Member – read‑only access',
                                default => "Authority level $level"
                            };
                        @endphp

                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">

                            {{-- ID --}}
                            <td class="px-6 py-4 text-gray-500 dark:text-gray-400">#{{ $role->id }}</td>

                            {{-- Role Name --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center text-xs font-bold text-indigo-700 dark:text-indigo-300">
                                        {{ strtoupper(substr($role->name, 0, 2)) }}
                                    </div>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $role->name }}</span>
                                </div>
                            </td>

                            {{-- Abbreviation --}}
                            <td class="px-6 py-4">
                                @if($role->abbreviation)
                                    <span class="px-2.5 py-1 rounded-full text-xs font-mono bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">
                                        {{ $role->abbreviation }}
                                    </span>
                                @else
                                    <span class="text-gray-400 text-xs">—</span>
                                @endif
                            </td>

                            {{-- Description --}}
                            <td class="px-6 py-4 text-gray-500 dark:text-gray-400 max-w-xs">
                                {{ \Illuminate\Support\Str::limit($role->description ?? 'No description', 50) }}
                            </td>

                            {{-- Level with tooltip --}}
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-medium
                                    bg-{{ $color }}-100 dark:bg-{{ $color }}-900/50
                                    text-{{ $color }}-700 dark:text-{{ $color }}-300
                                    cursor-help" title="{{ $levelTooltip }}">
                                    Level {{ $level }}
                                </span>
                            </td>

                            {{-- Users count (eager-loaded) --}}
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 text-sm font-semibold">
                                    {{ $role->users_count ?? $role->users()->count() }}
                                </span>
                            </td>

                            {{-- Created --}}
                            <td class="px-6 py-4 text-gray-500 dark:text-gray-400">
                                {{ $role->created_at->format('M d, Y') }}
                            </td>

                            {{-- Actions (only if user can manage roles) --}}
                            <td class="px-6 py-4">
                                <div class="flex justify-end items-center gap-1">
                                    @if($canManageRoles)
                                        @if(!$role->is_system)
                                            {{-- Edit --}}
                                            <button type="button"
                                                    onclick="window.location.href='{{ route('admin.roles.edit', $role->id) }}'"
                                                    class="p-1.5 text-gray-500 hover:text-indigo-600 dark:hover:text-indigo-400 rounded-lg hover:bg-indigo-50 dark:hover:bg-gray-700 transition-colors"
                                                    title="Edit role">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                            </button>

                                            {{-- Delete --}}
                                            <button type="button"
                                                    onclick="confirmDelete({{ $role->id }}, '{{ addslashes($role->name) }}')"
                                                    class="p-1.5 text-gray-500 hover:text-red-600 dark:hover:text-red-400 rounded-lg hover:bg-red-50 dark:hover:bg-gray-700 transition-colors"
                                                    title="Delete role">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-300">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                                </svg>
                                                System Role
                                            </span>
                                        @endif
                                    @else
                                        <span class="text-xs text-gray-400 italic px-2">No access</span>
                                    @endif
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center gap-2">
                                    <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                              d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                    <p class="text-gray-400 italic text-sm">No roles found.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if(isset($roles) && method_exists($roles, 'links'))
            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                {{ $roles->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Hidden Delete Forms --}}
@foreach($roles as $role)
    <form id="delete-form-{{ $role->id }}"
          action="{{ route('admin.roles.destroy', $role->id) }}"
          method="POST"
          style="display:none;">
        @csrf
        @method('DELETE')
    </form>
@endforeach

@push('scripts')
<script>
    // Auto-hide flash messages after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            document.querySelectorAll('.flash-message').forEach(function(msg) {
                msg.style.transition = 'opacity 0.5s';
                msg.style.opacity = '0';
                setTimeout(function() { msg.remove(); }, 500);
            });
        }, 5000);
    });

    function confirmDelete(roleId, roleName) {
        if (!confirm(`⚠️ Delete role "${roleName}"?\n\nThis action cannot be undone.\nUsers with this role will need to be reassigned.`)) {
            return;
        }
        document.getElementById('delete-form-' + roleId).submit();
    }
</script>
@endpush

@endsection