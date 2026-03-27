@extends('layouts.app')
@section('title', 'Roles — VSULHS_SSLG')

@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Roles</h1>
    <p class="text-sm text-gray-500 mt-1">Manage system roles</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- Roles List --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Role</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Users</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Permissions</th>
                    <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach ($roles as $role)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-5 py-3 font-semibold text-gray-900">{{ $role->name }}</td>
                    <td class="px-5 py-3 text-gray-600">{{ $role->users_count }}</td>
                    <td class="px-5 py-3 text-gray-600">{{ $role->permissions->count() }}</td>
                    <td class="px-5 py-3 text-right">
                        @if ($role->users_count === 0)
                        <form method="POST" action="{{ route('admin.roles.destroy', $role->id) }}"
                              onsubmit="return confirm('Delete this role?')">
                            @csrf @method('DELETE')
                            <button class="text-xs font-medium text-red-600 border border-red-200 px-3 py-1 rounded hover:bg-red-50 transition">
                                Delete
                            </button>
                        </form>
                        @else
                        <span class="text-xs text-gray-400 italic">In use</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Add Role --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-sm font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-100">Add New Role</h2>
        <form method="POST" action="{{ route('admin.roles.store') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Role Name</label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. Treasurer"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-black {{ $errors->has('name') ? 'border-red-400' : '' }}">
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <button type="submit"
                    class="bg-black text-white text-sm font-semibold px-5 py-2 rounded-lg hover:bg-gray-800 transition">
                Create Role
            </button>
        </form>
    </div>

</div>

@endsection
