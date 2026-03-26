@extends('layouts.app')
@section('title', 'Users — VSULHS_SSLG')

@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Users</h1>
        <p class="text-sm text-gray-500 mt-1">Manage system users</p>
    </div>
    <a href="{{ route('admin.users.create') }}"
       class="bg-black text-white text-sm font-semibold px-4 py-2 rounded-lg hover:bg-gray-800 transition">
        + Add User
    </a>
</div>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Name</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Email</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Role</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Created</th>
                <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($users as $user)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-5 py-3 font-medium text-gray-900">{{ $user->full_name }}</td>
                <td class="px-5 py-3 text-gray-600">{{ $user->email }}</td>
                <td class="px-5 py-3">
                    <span class="bg-black text-white text-xs font-bold px-2 py-0.5 rounded-full">
                        {{ $user->role->name }}
                    </span>
                </td>
                <td class="px-5 py-3 text-gray-600">{{ $user->created_at->format('M d, Y') }}</td>
                <td class="px-5 py-3 text-right">
                    <div class="flex items-center justify-end gap-2">
                        <a href="{{ route('admin.users.edit', $user->id) }}"
                           class="text-xs font-medium text-gray-700 border border-gray-300 px-3 py-1 rounded hover:bg-gray-100 transition">
                            Edit
                        </a>
                        @if ($user->id !== Auth::id())
                        <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}"
                              onsubmit="return confirm('Delete this user?')">
                            @csrf @method('DELETE')
                            <button class="text-xs font-medium text-red-600 border border-red-200 px-3 py-1 rounded hover:bg-red-50 transition">
                                Delete
                            </button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-5 py-10 text-center text-gray-400 text-sm italic">No users found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if ($users->hasPages())
    <div class="px-5 py-3 border-t border-gray-100">{{ $users->links() }}</div>
    @endif
</div>

@endsection
