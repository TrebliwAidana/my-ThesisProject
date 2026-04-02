@extends('layouts.app')

@section('title', 'User Management — VSULHS_SSLG')
@section('page-title', 'User Management')

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

<div class="space-y-6">
    {{-- Statistics Cards --}}
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

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gold-200 dark:border-gold-800 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Active Users</p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $activeUsers }}</p>
                </div>
                <div class="w-10 h-10 bg-green-100 dark:bg-green-900/50 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gold-200 dark:border-gold-800 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Verified Emails</p>
                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $verifiedEmails ?? 0 }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900/50 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gold-200 dark:border-gold-800 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Recent Logins (7d)</p>
                    <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $recentLogins }}</p>
                </div>
                <div class="w-10 h-10 bg-purple-100 dark:bg-purple-900/50 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- Actions Bar --}}
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.users.create') }}" class="bg-primary-600 hover:bg-gold-500 text-white text-sm font-semibold px-4 py-2 rounded-lg transition transform hover:scale-105 active:scale-95">
                + Add New User
            </a>
            <a href="{{ route('admin.roles.index') }}" class="border border-gold-300 dark:border-gold-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 px-4 py-2 rounded-lg text-sm font-medium transition">
                Manage Roles
            </a>
        </div>

        <form method="GET" action="{{ route('admin.users.index') }}" id="filter-form" class="flex items-center gap-3">
            <div class="relative">
                <input type="text"
                       name="search"
                       id="search-input"
                       list="user-suggestions"
                       value="{{ request('search') }}"
                       placeholder="Search users..."
                       class="pl-10 pr-4 py-2 border border-gold-200 dark:border-gold-800 dark:bg-gray-700 dark:text-white rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 w-64">
                <datalist id="user-suggestions">
                    @foreach($users as $user)
                        <option value="{{ $user->full_name }}"></option>
                        <option value="{{ $user->email }}"></option>
                    @endforeach
                </datalist>
                <svg class="absolute left-3 top-1/2 transform -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                @if(request()->filled('search'))
                <a href="{{ route('admin.users.index', array_merge(request()->except('search'), ['search' => ''])) }}"
                   class="absolute inset-y-0 right-0 pr-3 flex items-center">
                    <svg class="w-4 h-4 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </a>
                @endif
            </div>

            <select name="role" class="border border-gold-200 dark:border-gold-800 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                <option value="">All Roles</option>
                @foreach($roles as $role)
                    <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>{{ $role->name }}</option>
                @endforeach
            </select>

            <select name="status" class="border border-gold-200 dark:border-gold-800 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                <option value="">All Status</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
            </select>

            <select name="verification" class="border border-gold-200 dark:border-gold-800 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                <option value="">All Verification</option>
                <option value="verified" {{ request('verification') == 'verified' ? 'selected' : '' }}>Verified</option>
                <option value="unverified" {{ request('verification') == 'unverified' ? 'selected' : '' }}>Unverified</option>
            </select>

            <button type="submit" class="bg-primary-600 hover:bg-gold-500 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                Apply Filters
            </button>
            @if(request()->hasAny(['search', 'role', 'status', 'verification']))
            <a href="{{ route('admin.users.index') }}" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 text-sm">
                Reset
            </a>
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
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700" id="usersTableBody">
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
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-600 dark:text-gray-400">{{ $user->email }}</td>
                        <td class="px-6 py-4">
                            @if($user->email_verified_at)
                                <span class="inline-flex items-center gap-1 bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-400 text-xs font-semibold px-2 py-1 rounded-full">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Verified
                                </span>
                                <p class="text-xs text-gray-500 mt-1">{{ $user->email_verified_at->format('M d, Y') }}</p>
                            @else
                                <div class="flex flex-col gap-1">
                                    <span class="inline-flex items-center gap-1 bg-yellow-100 dark:bg-yellow-900/50 text-yellow-700 dark:text-yellow-400 text-xs font-semibold px-2 py-1 rounded-full w-fit">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                        </svg>
                                        Unverified
                                    </span>
                                    <div class="flex gap-1">
                                        <button onclick="sendVerification({{ $user->id }})" class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400">Send Email</button>
                                        <span class="text-gray-300">|</span>
                                        <form method="POST" action="{{ route('admin.users.verify-manual', $user->id) }}" class="inline">
                                            @csrf
                                            <button type="submit" class="text-xs text-green-600 hover:text-green-800 dark:text-green-400">Verify Manually</button>
                                        </form>
                                    </div>
                                </div>
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
                                <a href="{{ route('admin.users.edit', $user->id) }}" class="border border-gold-300 dark:border-gold-700 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 px-3 py-1 rounded text-xs transition">Edit</a>
                                @if($user->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}" onsubmit="return confirm('Are you sure you want to delete {{ $user->full_name }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="border border-red-200 dark:border-red-800 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 px-3 py-1 rounded text-xs transition">Delete</button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">No users found. </td>
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
    function sendVerification(userId) {
        if (confirm('Send verification email to this user?')) {
            fetch(`/admin/users/${userId}/send-verification`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Verification email sent successfully!');
                } else {
                    alert(data.message || 'Failed to send verification email.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred.');
            });
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

@endsection