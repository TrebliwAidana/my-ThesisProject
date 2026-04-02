@extends('layouts.app')
@section('title', 'Roles — VSULHS_SSLG')

@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Roles</h1>
    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage system roles and view assigned users</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- Roles List --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gold-200 dark:border-gold-800 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-700/50 border-b border-gold-200 dark:border-gold-800">
                    <tr>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Role</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Abbr</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Level</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Users</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Permissions</th>
                        <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach ($roles as $role)
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
                        $isSystemRole = in_array($role->name, ['System Administrator', 'Supreme Admin', 'Supreme Officer', 'Org Admin', 'Org Officer', 'Adviser', 'Org Member', 'Guest']);
                    @endphp
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-all duration-150 group">
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-lg {{ $colorClass }} flex items-center justify-center text-xs font-bold">
                                    {{ strtoupper(substr($role->name, 0, 1)) }}
                                </div>
                                <span class="font-semibold text-gray-900 dark:text-white">{{ $role->name }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-3">
                            @if($role->abbreviation)
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-mono bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">
                                    {{ $role->abbreviation }}
                                </span>
                            @else
                                <span class="text-gray-400 dark:text-gray-500 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium {{ $colorClass }}">
                                Level {{ $role->level }}
                            </span>
                        </td>
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 text-xs font-semibold">
                                {{ $role->users_count }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-gray-600 dark:text-gray-400">
                            {{ $role->permissions->count() }}
                        </td>
                        <td class="px-5 py-3 text-right">
                            @if (!$isSystemRole && $role->users_count === 0)
                                <form method="POST" action="{{ route('admin.roles.destroy', $role->id) }}"
                                      onsubmit="return confirm('⚠️ Delete role "{{ $role->name }}"?\n\nThis action cannot be undone.')"
                                      class="inline">
                                    @csrf @method('DELETE')
                                    <button class="text-xs font-medium text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 border border-red-200 dark:border-red-800 px-3 py-1 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/30 transition-all duration-200">
                                        Delete
                                    </button>
                                </form>
                            @elseif($isSystemRole)
                                <span class="text-xs text-gray-400 dark:text-gray-500 italic" title="System role cannot be deleted">
                                    System Role
                                </span>
                            @else
                                <span class="text-xs text-gray-400 dark:text-gray-500 italic" title="Role has assigned users">
                                    In Use ({{ $role->users_count }} users)
                                </span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Add Role --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gold-200 dark:border-gold-800 p-6 shadow-sm">
        <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4 pb-2 border-b border-gray-100 dark:border-gold-800">
            Add New Role
        </h2>
        <form method="POST" action="{{ route('admin.roles.store') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Role Name</label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g., Treasurer, Secretary"
                       class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-transparent transition {{ $errors->has('name') ? 'border-red-400' : '' }}">
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Abbreviation (Optional)</label>
                <input type="text" name="abbreviation" value="{{ old('abbreviation') }}" placeholder="e.g., TR, SEC"
                       class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-transparent transition">
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Short code for this role (e.g., TR for Treasurer)</p>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Level</label>
                <select name="level" required
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-transparent transition">
                    <option value="5">Level 5 - Organization Officer</option>
                    <option value="6">Level 6 - Adviser</option>
                    <option value="7">Level 7 - Organization Member</option>
                    <option value="8">Level 8 - Guest</option>
                </select>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    Level determines hierarchy. Level 1-4 are system reserved roles.
                </p>
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Description (Optional)</label>
                <textarea name="description" rows="2" placeholder="Describe the responsibilities of this role"
                          class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-transparent transition">{{ old('description') }}</textarea>
            </div>
            
            <div class="flex items-center gap-3">
                <button type="submit"
                        class="bg-primary-600 hover:bg-gold-500 text-white text-sm font-semibold px-5 py-2 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-[1.02]">
                    Create Role
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
            <p class="text-sm font-medium text-blue-800 dark:text-blue-300">Role Hierarchy Information</p>
            <p class="text-xs text-blue-700 dark:text-blue-400 mt-1">
                <strong>System Roles (Level 1-4):</strong> System Administrator (1), Supreme Admin (2), Supreme Officer (3), Org Admin (4) - Cannot be deleted<br>
                <strong>Organization Roles (Level 5-8):</strong> Org Officer (5), Adviser (6), Org Member (7), Guest (8) - Can be created and managed
            </p>
        </div>
    </div>
</div>

@endsection