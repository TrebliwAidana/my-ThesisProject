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

        {{-- Table toolbar with search & show hidden toggle --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-3 border-b border-gold-200 dark:border-gold-800 bg-gray-50 dark:bg-gray-700/50">
            {{-- Search input (client‑side filtering) --}}
            <div class="relative">
                <input type="text" id="roleSearch" placeholder="Search by name or abbreviation..." 
                       class="w-full sm:w-64 pl-9 pr-3 py-1.5 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-gold-500 focus:border-transparent">
                <svg class="absolute left-2.5 top-2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
            </div>
            <div class="flex items-center gap-2">
                @if ($showHidden)
                    <span class="inline-flex items-center gap-1.5 text-xs font-medium text-amber-700 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/30 border border-amber-200 dark:border-amber-800 px-2.5 py-1 rounded-full">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 inline-block"></span>
                        Showing hidden roles
                    </span>
                @endif
                <a href="{{ route('admin.roles.index', $showHidden ? [] : ['show_hidden' => 1]) }}"
                   class="text-xs font-medium px-3 py-1.5 rounded-lg border transition-all duration-200
                          {{ $showHidden
                              ? 'text-gray-600 border-gray-300 hover:bg-gray-100 dark:text-gray-400 dark:border-gray-600 dark:hover:bg-gray-700'
                              : 'text-amber-600 border-amber-200 hover:bg-amber-50 dark:text-amber-400 dark:border-amber-800 dark:hover:bg-amber-900/30' }}">
                    {{ $showHidden ? '← Hide hidden roles' : 'Show hidden roles' }}
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm" id="rolesTable">
                <thead class="bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Role</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Abbr</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Level</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Users</th>
                        <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Perms</th>
                        <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach ($roles as $role)
                    @php
                        $roleColors = [
                            'System Administrator' => 'bg-gold-100 text-gold-700 dark:bg-gold-900/50 dark:text-gold-300',
                            'Supreme Admin'        => 'bg-primary-100 text-primary-700 dark:bg-primary-900/50 dark:text-primary-300',
                            'Supreme Officer'      => 'bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300',
                            'Org Admin'            => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-300',
                            'Org Officer'          => 'bg-sky-100 text-sky-700 dark:bg-sky-900/50 dark:text-sky-300',
                            'Adviser'              => 'bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-300',
                            'Org Member'           => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
                            'Guest'                => 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400',
                        ];
                        $colorClass   = $roleColors[$role->name] ?? 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300';
                        $isPredefined = $role->is_predefined ?? false;
                        $isHidden     = !$role->is_visible;
                        
                        // Descriptive level names
                        $levelNames = [
                            1 => 'System Admin',
                            2 => 'Supreme Admin',
                            3 => 'Supreme Officer',
                            4 => 'Org Admin',
                            5 => 'Org Officer',
                            6 => 'Adviser',
                            7 => 'Member',
                            8 => 'Guest',
                        ];
                        $levelLabel = $levelNames[$role->level] ?? "Level {$role->level}";
                    @endphp

                    <tr class="transition-all duration-150 role-row
                               {{ $isHidden
                                   ? 'opacity-50 bg-gray-50 dark:bg-gray-800/40'
                                   : 'hover:bg-gray-50 dark:hover:bg-gray-700/50' }}">

                        {{-- Role name --}}
                        <td class="px-5 py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-6 h-6 rounded-lg {{ $colorClass }} flex items-center justify-center text-xs font-bold flex-shrink-0">
                                    {{ strtoupper(substr($role->name, 0, 1)) }}
                                </div>
                                <div>
                                    <span class="font-semibold text-gray-900 dark:text-white role-name">{{ $role->name }}</span>
                                    @if ($isHidden)
                                        <span class="ml-1.5 inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400">
                                            hidden
                                        </span>
                                    @endif
                                    @if ($isPredefined)
                                        <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-gold-50 text-gold-600 dark:bg-gold-900/30 dark:text-gold-400">
                                            predefined
                                        </span>
                                    @endif
                                    @if ($role->is_system)
                                        <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300">
                                            system
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- Abbreviation --}}
                        <td class="px-5 py-3">
                            @if ($role->abbreviation)
                                <span class="inline-flex px-2 py-0.5 rounded-full text-xs font-mono bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">
                                    {{ $role->abbreviation }}
                                </span>
                            @else
                                <span class="text-gray-400 dark:text-gray-500 text-xs">—</span>
                            @endif
                        </td>

                        {{-- Level with descriptive tooltip --}}
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium {{ $colorClass }} cursor-help" title="{{ $levelLabel }}">
                                Level {{ $role->level }}
                            </span>
                        </td>

                        {{-- Users count --}}
                        <td class="px-5 py-3">
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 text-xs font-semibold">
                                {{ $role->users_count }}
                            </span>
                        </td>

                        {{-- Permissions count --}}
                        <td class="px-5 py-3 text-gray-600 dark:text-gray-400">
                            {{ $role->permissions->count() }}
                        </td>

                        {{-- Actions --}}
                        <td class="px-5 py-3 text-right">
                            <div class="flex items-center justify-end gap-1.5">

                                {{-- Hide / Unhide toggle (with confirm and disable when users assigned) --}}
                                @if ($role->is_visible && $role->users_count > 0)
                                    <button disabled
                                            class="text-xs font-medium px-2.5 py-1 rounded-lg border border-gray-300 text-gray-400 cursor-not-allowed bg-gray-50 dark:bg-gray-800"
                                            title="Cannot hide – role has {{ $role->users_count }} user(s) assigned.">
                                        Hide
                                    </button>
                                @else
                                    <form method="POST"
                                          action="{{ route('admin.roles.toggle-visibility', $role->id) }}?show_hidden={{ $showHidden ? '1' : '0' }}"
                                          onsubmit="return confirm('Are you sure you want to {{ $role->is_visible ? 'hide' : 'unhide' }} the role “{{ addslashes($role->name) }}”?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                                class="text-xs font-medium px-2.5 py-1 rounded-lg border transition-all duration-200
                                                       {{ $isHidden
                                                           ? 'text-emerald-600 border-emerald-200 hover:bg-emerald-50 dark:text-emerald-400 dark:border-emerald-800 dark:hover:bg-emerald-900/30'
                                                           : 'text-amber-600 border-amber-200 hover:bg-amber-50 dark:text-amber-400 dark:border-amber-800 dark:hover:bg-amber-900/30' }}"
                                                title="{{ $isHidden ? 'Make this role available to users' : 'Hide this role from user selectors' }}">
                                            {{ $isHidden ? 'Unhide' : 'Hide' }}
                                        </button>
                                    </form>
                                @endif

                                {{-- Edit – visible roles only --}}
                                @if (!$isHidden)
                                    <a href="{{ route('admin.roles.edit', $role->id) }}"
                                       class="text-xs font-medium text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 border border-blue-200 dark:border-blue-800 px-2.5 py-1 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-all duration-200">
                                        Edit
                                    </a>
                                @endif

                                {{-- Delete – only non‑predefined, no users, visible --}}
                                @if (!$isPredefined && $role->users_count === 0 && !$isHidden)
                                    <form method="POST"
                                          action="{{ route('admin.roles.destroy', $role->id) }}"
                                          onsubmit="return confirm('⚠️ Delete role &quot;{{ $role->name }}&quot;?\n\nThis action cannot be undone.')"
                                          class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="text-xs font-medium text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 border border-red-200 dark:border-red-800 px-2.5 py-1 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/30 transition-all duration-200">
                                            Delete
                                        </button>
                                    </form>
                                @elseif ($isPredefined && !$isHidden)
                                    <span class="text-xs text-gray-400 dark:text-gray-500 italic" title="Predefined roles cannot be deleted">
                                        Predefined
                                    </span>
                                @elseif ($role->users_count > 0 && !$isHidden)
                                    <span class="text-xs text-gray-400 dark:text-gray-500 italic" title="Role has assigned users">
                                        In use
                                    </span>
                                @endif

                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Add Role form (unchanged) --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gold-200 dark:border-gold-800 p-6 shadow-sm">
        <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4 pb-2 border-b border-gray-100 dark:border-gold-800">
            Add New Role
        </h2>
        <form method="POST" action="{{ route('admin.roles.store') }}">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Role Name</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       placeholder="e.g., Treasurer, Secretary"
                       class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-transparent transition {{ $errors->has('name') ? 'border-red-400' : '' }}">
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                    Abbreviation <span class="font-normal text-gray-400">(Optional)</span>
                </label>
                <input type="text" name="abbreviation" value="{{ old('abbreviation') }}"
                       placeholder="e.g., TR, SEC"
                       class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-transparent transition">
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Short code for this role (e.g., TR for Treasurer)</p>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Level</label>
                <select name="level" required
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-transparent transition">
                    <option value="5" {{ old('level') == 5 ? 'selected' : '' }}>Level 5 — Organization Officer</option>
                    <option value="6" {{ old('level') == 6 ? 'selected' : '' }}>Level 6 — Adviser</option>
                    <option value="7" {{ old('level') == 7 ? 'selected' : '' }}>Level 7 — Organization Member</option>
                    <option value="8" {{ old('level') == 8 ? 'selected' : '' }}>Level 8 — Guest</option>
                </select>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    Level determines hierarchy. Level 1–4 are system reserved roles.
                </p>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                    Description <span class="font-normal text-gray-400">(Optional)</span>
                </label>
                <textarea name="description" rows="2"
                          placeholder="Describe the responsibilities of this role"
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
        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <div class="space-y-1">
            <p class="text-sm font-medium text-blue-800 dark:text-blue-300">Role Hierarchy & Visibility</p>
            <p class="text-xs text-blue-700 dark:text-blue-400">
                <strong>System / Predefined roles (Level 1–4):</strong>
                System Administrator, Supreme Admin, Supreme Officer, Org Admin — names cannot be changed, but abbreviation, description, level, and permissions can be updated.
            </p>
            <p class="text-xs text-blue-700 dark:text-blue-400">
                <strong>Organization roles (Level 5–8):</strong>
                Org Officer, Adviser, Org Member, Guest — fully editable and deletable (when no users are assigned).
            </p>
            <p class="text-xs text-blue-700 dark:text-blue-400">
                <strong>Hiding a role</strong> removes it from all user creation and editing forms. Roles with active users cannot be hidden until those users are reassigned.
            </p>
        </div>
    </div>
</div>

{{-- Client‑side search script --}}
@push('scripts')
<script>
    document.getElementById('roleSearch').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('#rolesTable tbody tr');
        rows.forEach(row => {
            let nameCell = row.querySelector('td:first-child .role-name');
            let name = nameCell ? nameCell.innerText.toLowerCase() : '';
            let abbrCell = row.querySelector('td:nth-child(2) span');
            let abbr = abbrCell ? abbrCell.innerText.toLowerCase() : '';
            if (name.includes(filter) || abbr.includes(filter)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
</script>
@endpush

@endsection