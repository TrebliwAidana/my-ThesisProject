@extends('layouts.app')
@section('title', 'Permissions — VSULHS_SSLG')

@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Permissions</h1>
    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Assign permissions to roles and manage access control</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- Permissions list --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gold-200 dark:border-gold-800 overflow-hidden shadow-sm">
        <div class="px-5 py-3 border-b border-gray-100 dark:border-gold-800 bg-gray-50 dark:bg-gray-800/50">
            <h2 class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">All Permissions</h2>
        </div>
        <div class="max-h-[600px] overflow-y-auto">
            <ul class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse ($permissions as $perm)
                <li class="px-5 py-3 flex items-start justify-between gap-3 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-all duration-150">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ str_replace('_', ' ', $perm->name) }}</p>
                        @if($perm->description)
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $perm->description }}</p>
                        @endif
                    </div>
                    <div class="flex flex-wrap gap-1 justify-end shrink-0">
                        @forelse ($perm->roles as $role)
                            @php
                                $roleColors = [
                                    'System Administrator' => 'bg-gold-100 text-gold-700 dark:bg-gold-900/50 dark:text-gold-300',
                                    'Supreme Admin' => 'bg-primary-100 text-primary-700 dark:bg-primary-900/50 dark:text-primary-300',
                                    'Supreme Officer' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300',
                                    'Org Admin' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-300',
                                    'Org Officer' => 'bg-sky-100 text-sky-700 dark:bg-sky-900/50 dark:text-sky-300',
                                    'Adviser' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-300',
                                    'Org Member' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
                                    'Guest' => 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400',
                                ];
                                $colorClass = $roleColors[$role->name] ?? 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300';
                            @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $colorClass }}">
                                {{ $role->abbreviation ?? $role->name }}
                            </span>
                        @empty
                            <span class="text-xs text-gray-400 dark:text-gray-500 italic">No roles assigned</span>
                        @endforelse
                    </div>
                </li>
                @empty
                <li class="px-5 py-8 text-center">
                    <div class="flex flex-col items-center gap-2">
                        <svg class="w-10 h-10 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <p class="text-sm text-gray-500 dark:text-gray-400">No permissions found.</p>
                    </div>
                </li>
                @endforelse
            </ul>
        </div>
    </div>

    {{-- Sync permissions to role --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gold-200 dark:border-gold-800 p-6 shadow-sm">
        <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4 pb-2 border-b border-gray-100 dark:border-gold-800">
            Sync Role Permissions
        </h2>
        <form method="POST" action="{{ route('admin.permissions.sync') }}">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Select Role</label>
                <select name="role_id" required
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-transparent transition">
                    <option value="">— Choose a role —</option>
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}">{{ $role->name }} @if($role->abbreviation)({{ $role->abbreviation }})@endif</option>
                    @endforeach
                </select>
                @error('role_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Permissions</label>
                <div class="space-y-2 max-h-96 overflow-y-auto border border-gold-200 dark:border-gold-800 rounded-lg p-3 bg-gray-50 dark:bg-gray-900/30">
                    @foreach ($permissions as $perm)
                    <label class="flex items-center gap-2 text-sm cursor-pointer group hover:bg-gray-100 dark:hover:bg-gray-700/50 p-1 rounded transition">
                        <input type="checkbox" name="permission_ids[]" value="{{ $perm->id }}"
                               class="rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-gold-500 transition">
                        <span class="text-gray-700 dark:text-gray-300 group-hover:text-primary-600 dark:group-hover:text-primary-400 transition">
                            {{ str_replace('_', ' ', $perm->name) }}
                        </span>
                    </label>
                    @endforeach
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">
                    <svg class="inline w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Select the permissions you want to assign to this role. Unchecked permissions will be removed.
                </p>
            </div>

            <div class="flex items-center gap-3">
                <button type="submit"
                        class="bg-primary-600 hover:bg-gold-500 text-white text-sm font-semibold px-5 py-2 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-[1.02]">
                    Sync Permissions
                </button>
                <button type="reset"
                        class="text-gray-600 dark:text-gray-400 text-sm font-medium px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    Reset
                </button>
            </div>
        </form>
    </div>

</div>

{{-- Information Card --}}
<div class="mt-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4">
    <div class="flex items-start gap-3">
        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <div>
            <p class="text-sm font-medium text-blue-800 dark:text-blue-300">About Permissions</p>
            <p class="text-xs text-blue-700 dark:text-blue-400 mt-1">
                Permissions control what actions users can perform. Roles inherit permissions from their parent roles.
                System roles (SysAdmin, SA, AD, etc.) have pre-configured permissions and cannot be modified.
            </p>
        </div>
    </div>
</div>

@endsection