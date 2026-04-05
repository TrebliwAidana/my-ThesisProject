@extends('layouts.app')

@section('title', 'User Management — VSULHS_SSLG')
@section('page-title', 'User Management')

@section('content')
<style>
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

<div class="space-y-6">
    {{-- Statistics Cards (keep as is) --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gold-200 dark:border-gold-800 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Users</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $totalUsers }}</p>
                </div>
                <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900/50 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
        <!-- You can add more stats cards here if needed -->
    </div>

    {{-- Actions Bar --}}
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.users.create') }}" class="bg-primary-600 hover:bg-gold-500 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                + Add New User
            </a>
            <a href="{{ route('admin.roles.index') }}" class="border border-gold-300 dark:border-gold-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition">
                Manage Roles
            </a>
        </div>

        <form method="GET" action="{{ route('admin.users.index') }}" id="filter-form" class="flex flex-wrap items-center gap-3">
            <div class="relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search users..."
                       class="pl-10 pr-4 py-2 border border-gold-200 dark:border-gold-800 dark:bg-gray-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 w-64">
                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>

            <select name="role" class="border border-gold-200 dark:border-gold-800 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm">
                <option value="">All Roles</option>
                @foreach($roles as $role)
                    <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>{{ $role->name }}</option>
                @endforeach
            </select>

            <select name="status" class="border border-gold-200 dark:border-gold-800 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm">
                <option value="">All Status</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>

            <select name="verification" class="border border-gold-200 dark:border-gold-800 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm">
                <option value="">All Verification</option>
                <option value="verified" {{ request('verification') == 'verified' ? 'selected' : '' }}>Verified</option>
                <option value="unverified" {{ request('verification') == 'unverified' ? 'selected' : '' }}>Unverified</option>
            </select>

            <label class="flex items-center gap-2">
                <input type="checkbox" name="trashed" value="1" {{ request()->boolean('trashed') ? 'checked' : '' }} onchange="this.form.submit()"
                       class="rounded border-gold-300 text-primary-600 focus:ring-gold-500">
                <span class="text-sm text-gray-700 dark:text-gray-300">Show deleted users</span>
            </label>

            <button type="submit" class="bg-primary-600 hover:bg-gold-500 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">Apply Filters</button>
            @if(request()->hasAny(['search', 'role', 'status', 'verification', 'trashed']))
            <a href="{{ route('admin.users.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 text-sm">Reset</a>
            @endif
        </form>
    </div>

    {{-- Users Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gold-200 dark:border-gold-800 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm admin-users-table">
                <thead class="bg-gray-50 dark:bg-gray-700 border-b border-gold-200 dark:border-gold-600">
                    <tr>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">User</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Email</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Email Status</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Role</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Position</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Account Status</th>
                        <th class="text-left px-6 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Last Login</th>
                        <th class="text-right px-6 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-r from-primary-500 to-primary-600 flex items-center justify-center text-white text-xs font-bold">
                                    {{ strtoupper(substr($user->full_name, 0, 2)) }}
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">{{ $user->full_name }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">ID: {{ $user->id }}</p>
                                    @if($user->trashed())
                                        <span class="inline-block mt-1 text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded-full">Deleted</span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $user->email }}</td>
                        <td class="px-6 py-4">
                            @if($user->email_verified_at)
                                <span class="inline-flex items-center gap-1 bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-400 text-xs font-semibold px-2 py-1 rounded-full">
                                    Verified
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 bg-yellow-100 dark:bg-yellow-900/50 text-yellow-700 dark:text-yellow-400 text-xs font-semibold px-2 py-1 rounded-full">
                                    Unverified
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="bg-primary-100 dark:bg-primary-900/50 text-primary-700 dark:text-primary-400 text-xs font-semibold px-2 py-1 rounded-full">{{ $user->role->name }}</span>
                        </td>
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $user->position ?? '—' }}</td>
                        <td class="px-6 py-4">
                            @if($user->is_active)
                                <span class="bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-400 text-xs font-semibold px-2 py-1 rounded-full">Active</span>
                            @else
                                <span class="bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 text-xs font-semibold px-2 py-1 rounded-full">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $user->last_login_at ? $user->last_login_at->diffForHumans() : 'Never' }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                @if($user->trashed())
                                    <form method="POST" action="{{ route('admin.users.restore', $user->id) }}" onsubmit="return confirm('Restore this user?')" class="inline">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-800 text-xs font-medium">Restore</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.users.force-delete', $user->id) }}" onsubmit="return confirm('Permanently delete this user? This action cannot be undone.')" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-medium">Force Delete</button>
                                    </form>
                                @else
                                    {{-- Manual Email Verification Button --}}
                                    @if(!$user->hasVerifiedEmail())
                                        <form method="POST" action="{{ route('admin.users.verify-manual', $user->id) }}" onsubmit="return confirm('Mark this user\'s email as verified?')" class="inline">
                                            @csrf
                                            <button type="submit" class="text-blue-600 hover:text-blue-800 text-xs font-medium border border-blue-200 dark:border-blue-800 px-2 py-1 rounded">
                                                Verify
                                            </button>
                                        </form>
                                    @endif

                                    <a href="{{ route('admin.users.edit', $user->id) }}" class="border border-gold-300 dark:border-gold-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 px-3 py-1 rounded text-xs transition">Edit</a>

                                    @if($user->id !== auth()->id())
                                    <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}" onsubmit="return confirm('Soft delete {{ $user->full_name }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 px-3 py-1 rounded text-xs transition">Delete</button>
                                    </form>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">No users found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
        <div class="px-6 py-3 border-t border-gray-100 dark:border-gold-800">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>

<script>
    // Keep any existing scripts (e.g., sendVerification) if needed
</script>
@endsection