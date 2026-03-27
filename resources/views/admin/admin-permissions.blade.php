@extends('layouts.app')
@section('title', 'Permissions — VSULHS_SSLG')

@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Permissions</h1>
    <p class="text-sm text-gray-500 mt-1">Assign permissions to roles</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- Permissions list --}}
    <div class="bg-white rounded-xl border border-gray-200 overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100 bg-gray-50">
            <h2 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">All Permissions</h2>
        </div>
        <ul class="divide-y divide-gray-100">
            @foreach ($permissions as $perm)
            <li class="px-5 py-3 flex items-start justify-between gap-3">
                <div>
                    <p class="text-sm font-medium text-gray-900">{{ str_replace('_', ' ', $perm->name) }}</p>
                    <p class="text-xs text-gray-500 mt-0.5">{{ $perm->description }}</p>
                </div>
                <div class="flex flex-wrap gap-1 justify-end shrink-0">
                    @foreach ($perm->roles as $role)
                        <span class="bg-black text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $role->name }}</span>
                    @endforeach
                </div>
            </li>
            @endforeach
        </ul>
    </div>

    {{-- Sync permissions to role --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-sm font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-100">Sync Role Permissions</h2>
        <form method="POST" action="{{ route('admin.permissions.sync') }}">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 mb-1">Select Role</label>
                <select name="role_id" required
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-black">
                    <option value="">— Choose a role —</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}">{{ $role->name }}</option>
                    @endforeach
                </select>
                @error('role_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-2">Permissions</label>
                <div class="space-y-2 max-h-64 overflow-y-auto border border-gray-200 rounded-lg p-3">
                    @foreach ($permissions as $perm)
                    <label class="flex items-center gap-2 text-sm cursor-pointer">
                        <input type="checkbox" name="permission_ids[]" value="{{ $perm->id }}"
                               class="rounded border-gray-300 text-black focus:ring-black">
                        <span class="text-gray-700">{{ str_replace('_', ' ', $perm->name) }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            <button type="submit"
                    class="bg-black text-white text-sm font-semibold px-5 py-2 rounded-lg hover:bg-gray-800 transition">
                Sync Permissions
            </button>
        </form>
    </div>

</div>

@endsection
