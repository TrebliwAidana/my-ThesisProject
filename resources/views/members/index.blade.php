@extends('layouts.app')
@section('title', 'Members — VSULHS_SSLG')

@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Members</h1>
        <p class="text-sm text-gray-500 mt-1">Manage organization members</p>
    </div>
    <a href="{{ route('members.create') }}"
       class="bg-black text-white text-sm font-semibold px-4 py-2 rounded-lg hover:bg-gray-800 transition">
        + Add Member
    </a>
</div>

<div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Name</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Email</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Role</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Organization</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Joined</th>
                <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($members as $member)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-5 py-3 font-medium text-gray-900">{{ $member->user->full_name }}</td>
                <td class="px-5 py-3 text-gray-600">{{ $member->user->email }}</td>
                <td class="px-5 py-3">
                    <span class="bg-black text-white text-xs font-bold px-2 py-0.5 rounded-full">
                        {{ $member->user->role->name }}
                    </span>
                </td>
                <td class="px-5 py-3 text-gray-600">{{ $member->organization }}</td>
                <td class="px-5 py-3">
                    @if ($member->status === 'active')
                        <span class="bg-green-100 text-green-800 text-xs font-semibold px-2 py-0.5 rounded-full">Active</span>
                    @elseif ($member->status === 'inactive')
                        <span class="bg-gray-100 text-gray-700 text-xs font-semibold px-2 py-0.5 rounded-full">Inactive</span>
                    @else
                        <span class="bg-red-100 text-red-700 text-xs font-semibold px-2 py-0.5 rounded-full">Suspended</span>
                    @endif
                </td>
                <td class="px-5 py-3 text-gray-600">{{ $member->joined_at->format('M d, Y') }}</td>
                <td class="px-5 py-3 text-right">
                    <div class="flex items-center justify-end gap-2">
                        <a href="{{ route('members.edit', $member->id) }}"
                           class="text-xs font-medium text-gray-700 border border-gray-300 px-3 py-1 rounded hover:bg-gray-100 transition">
                            Edit
                        </a>
                        <form method="POST" action="{{ route('members.destroy', $member->id) }}"
                              onsubmit="return confirm('Remove this member?')">
                            @csrf @method('DELETE')
                            <button class="text-xs font-medium text-red-600 border border-red-200 px-3 py-1 rounded hover:bg-red-50 transition">
                                Remove
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="px-5 py-10 text-center text-gray-400 text-sm italic">No members found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    @if ($members->hasPages())
    <div class="px-5 py-3 border-t border-gray-100">
        {{ $members->links() }}
    </div>
    @endif
</div>

@endsection
