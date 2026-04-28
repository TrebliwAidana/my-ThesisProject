@extends('layouts.app')
@section('title', 'Roles — VSULHS_SSLG')

@section('content')

{{--
    Role color and level label maps are defined ONCE here, not inside the loop.
    Keys align to the 5 predefined roles in Member::VALID_POSITIONS.
    The fallback handles any custom roles created via the Add New Role form.
--}}
@php
    $roleColorMap = [
        'System Administrator' => 'bg-gold-100 text-gold-700 dark:bg-gold-900/50 dark:text-gold-300',
        'Club Adviser'         => 'bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-300',
        'Treasurer'            => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-300',
        'Auditor'              => 'bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300',
        'Guest'                => 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400',
    ];

    $levelLabelMap = [
        1 => 'System',
        2 => 'Adviser',
        3 => 'Officer',
        4 => 'Member',
        5 => 'Guest',
    ];

    $authUserId = auth()->id();
    $authRoleId = auth()->user()->role_id;
@endphp

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Roles</h1>
    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage system roles and view assigned users</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- ------------------------------------------------------------------ --}}
    {{-- Roles List                                                          --}}
    {{-- ------------------------------------------------------------------ --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gold-200 dark:border-gold-800 overflow-hidden shadow-sm">

        {{-- Toolbar --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 px-5 py-3 border-b border-gold-200 dark:border-gold-800 bg-gray-50 dark:bg-gray-700/50">
            <div class="relative">
                <input type="text"
                       id="roleSearch"
                       placeholder="Search by name or abbreviation…"
                       aria-label="Search roles"
                       class="w-full sm:w-64 pl-9 pr-3 py-1.5 text-sm rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 focus:ring-2 focus:ring-gold-500 focus:border-transparent">
                <svg class="absolute left-2.5 top-2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
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
                    @forelse ($roles as $role)
                        @php
                            $colorClass   = $roleColorMap[$role->name] ?? 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300';
                            $levelLabel   = $levelLabelMap[$role->level] ?? "Level {$role->level}";
                            $isPredefined = (bool) ($role->is_predefined ?? false);
                            $isHidden     = ! $role->is_visible;
                            $isOwnRole    = ($authRoleId === $role->id);
                        @endphp

                        <tr class="transition-all duration-150 role-row
                                   {{ $isHidden
                                       ? 'opacity-50 bg-gray-50 dark:bg-gray-800/40'
                                       : 'hover:bg-gray-50 dark:hover:bg-gray-700/50' }}"
                            data-name="{{ strtolower($role->name) }}"
                            data-abbr="{{ strtolower($role->abbreviation ?? '') }}">

                            {{-- Role name --}}
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-lg {{ $colorClass }} flex items-center justify-center text-xs font-bold flex-shrink-0"
                                         aria-hidden="true">
                                        {{ strtoupper(substr($role->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <span class="font-semibold text-gray-900 dark:text-white role-name">{{ $role->name }}</span>

                                        @if ($isHidden)
                                            <span class="ml-1.5 inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400">hidden</span>
                                        @endif

                                        @if ($isPredefined)
                                            <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-gold-50 text-gold-600 dark:bg-gold-900/30 dark:text-gold-400">predefined</span>
                                        @endif

                                        @if ($role->is_system)
                                            <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300">system</span>
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

                            {{-- Level --}}
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium {{ $colorClass }} cursor-default"
                                      title="{{ $levelLabel }}">
                                    Level {{ $role->level }}
                                </span>
                            </td>

                            {{-- Users count (pre-aggregated via withCount — no extra query) --}}
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 text-xs font-semibold">
                                    {{ $role->users_count }}
                                </span>
                            </td>

                            {{-- Permissions count (eager-loaded collection — no extra query) --}}
                            <td class="px-5 py-3 text-gray-600 dark:text-gray-400">
                                {{ $role->permissions->count() }}
                            </td>

                            {{-- Actions --}}
                            <td class="px-5 py-3 text-right">
                                <div class="flex items-center justify-end gap-1.5">

                                    {{-- Hide / Unhide --}}
                                    @if ($role->id === 1)
                                        {{-- System Administrator is never hideable --}}
                                        <span class="text-xs text-gray-400 dark:text-gray-500 italic" title="System Administrator cannot be hidden">Protected</span>

                                    @elseif ($isOwnRole && $role->is_visible)
                                        {{-- Prevent hiding your own active role --}}
                                        <button disabled
                                                class="text-xs font-medium px-2.5 py-1 rounded-lg border border-gray-300 text-gray-400 cursor-not-allowed bg-gray-50 dark:bg-gray-800"
                                                title="Cannot hide your own current role.">
                                            Hide
                                        </button>

                                    @elseif ($role->is_visible && $role->users_count > 0)
                                        {{-- Cannot hide a role with active users --}}
                                        <button disabled
                                                class="text-xs font-medium px-2.5 py-1 rounded-lg border border-gray-300 text-gray-400 cursor-not-allowed bg-gray-50 dark:bg-gray-800"
                                                title="Cannot hide — {{ $role->users_count }} user(s) assigned.">
                                            Hide
                                        </button>

                                    @else
                                        <form method="POST"
                                              action="{{ route('admin.roles.toggle-visibility', $role->id) }}?show_hidden={{ $showHidden ? '1' : '0' }}"
                                              onsubmit="return confirm('{{ $role->is_visible ? 'Hide' : 'Unhide' }} the role &quot;{{ addslashes($role->name) }}&quot;?')">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit"
                                                    class="text-xs font-medium px-2.5 py-1 rounded-lg border transition-all duration-200
                                                           {{ $isHidden
                                                               ? 'text-emerald-600 border-emerald-200 hover:bg-emerald-50 dark:text-emerald-400 dark:border-emerald-800 dark:hover:bg-emerald-900/30'
                                                               : 'text-amber-600 border-amber-200 hover:bg-amber-50 dark:text-amber-400 dark:border-amber-800 dark:hover:bg-amber-900/30' }}">
                                                {{ $isHidden ? 'Unhide' : 'Hide' }}
                                            </button>
                                        </form>
                                    @endif

                                    {{-- Edit — visible roles only --}}
                                    @if (! $isHidden)
                                        <a href="{{ route('admin.roles.edit', $role->id) }}"
                                           class="text-xs font-medium text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 border border-blue-200 dark:border-blue-800 px-2.5 py-1 rounded-lg hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-all duration-200">
                                            Edit
                                        </a>
                                    @endif

                                    {{-- Delete --}}
                                    @if (! $isPredefined && $role->users_count === 0 && ! $isHidden)
                                        <form method="POST"
                                              action="{{ route('admin.roles.destroy', $role->id) }}"
                                              onsubmit="return confirm('⚠️ Delete role &quot;{{ addslashes($role->name) }}&quot;?\n\nThis action cannot be undone.')"
                                              class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="text-xs font-medium text-red-600 hover:text-red-700 dark:text-red-400 dark:hover:text-red-300 border border-red-200 dark:border-red-800 px-2.5 py-1 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/30 transition-all duration-200">
                                                Delete
                                            </button>
                                        </form>

                                    @elseif ($isPredefined && ! $isHidden)
                                        <span class="text-xs text-gray-400 dark:text-gray-500 italic" title="Predefined roles cannot be deleted">Predefined</span>

                                    @elseif ($role->users_count > 0 && ! $isHidden)
                                        <span class="text-xs text-gray-400 dark:text-gray-500 italic" title="Role has {{ $role->users_count }} assigned user(s)">In use</span>
                                    @endif

                                </div>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center text-sm text-gray-400 dark:text-gray-500">
                                No roles found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        @if ($roles->hasPages())
            <div class="px-5 py-3 border-t border-gray-100 dark:border-gray-700">
                {{ $roles->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

    {{-- ------------------------------------------------------------------ --}}
    {{-- Add New Role                                                        --}}
    {{-- ------------------------------------------------------------------ --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gold-200 dark:border-gold-800 p-6 shadow-sm">
        <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-4 pb-2 border-b border-gray-100 dark:border-gold-800">
            Add New Role
        </h2>

        <form method="POST" action="{{ route('admin.roles.store') }}">
            @csrf

            {{-- Name --}}
            <div class="mb-4">
                <label for="role_name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Role Name</label>
                <input type="text"
                       id="role_name"
                       name="name"
                       value="{{ old('name') }}"
                       required
                       placeholder="e.g., Secretary, PRO"
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-transparent transition
                              {{ $errors->has('name') ? 'border-red-400 dark:border-red-500' : 'border-gray-300 dark:border-gray-600' }}
                              dark:bg-gray-700 dark:text-white">
                @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Abbreviation --}}
            <div class="mb-4">
                <label for="role_abbreviation" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                    Abbreviation <span class="font-normal text-gray-400">(Optional)</span>
                </label>
                <input type="text"
                       id="role_abbreviation"
                       name="abbreviation"
                       value="{{ old('abbreviation') }}"
                       placeholder="e.g., SEC, PRO"
                       maxlength="10"
                       class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-transparent transition">
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Short code for this role (max 10 characters)</p>
            </div>

            {{-- Level --}}
            <div class="mb-4">
                <label for="role_level" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Level</label>
                <input type="number"
                       id="role_level"
                       name="level"
                       value="{{ old('level', 4) }}"
                       min="2"
                       max="10"
                       required
                       class="w-full border rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-transparent transition
                              {{ $errors->has('level') ? 'border-red-400 dark:border-red-500' : 'border-gray-300 dark:border-gray-600' }}
                              dark:bg-gray-700 dark:text-white">
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                    Level 1 is reserved for System Administrator. Custom roles use level 2–10.
                    Lower number = higher authority.
                </p>
                @error('level')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Description --}}
            <div class="mb-4">
                <label for="role_desc" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                    Description <span class="font-normal text-gray-400">(Optional)</span>
                </label>
                <textarea id="role_desc"
                          name="desc"
                          rows="2"
                          placeholder="Describe the responsibilities of this role"
                          class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-transparent transition">{{ old('desc') }}</textarea>
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




{{-- ------------------------------------------------------------------ --}}
{{-- Info card                                                           --}}
{{-- ------------------------------------------------------------------ --}}
<div class="mt-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4">
    <div class="flex items-start gap-3">
        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <div class="space-y-1">
            <p class="text-sm font-medium text-blue-800 dark:text-blue-300">Role Hierarchy & Visibility</p>
            <p class="text-xs text-blue-700 dark:text-blue-400">
                <strong>Predefined roles:</strong>
                System Administrator, Club Adviser, Treasurer, Auditor, and Guest are built-in roles.
                Their names cannot be changed, but abbreviation, description, level, and permissions can be updated.
            </p>
            <p class="text-xs text-blue-700 dark:text-blue-400">
                <strong>Custom roles</strong> are fully editable and deletable as long as no users are assigned to them.
            </p>
            <p class="text-xs text-blue-700 dark:text-blue-400">
                <strong>Hiding a role</strong> removes it from all user creation and editing forms.
                Roles with active users cannot be hidden until those users are reassigned.
            </p>
        </div>
    </div>
</div>

@push('scripts')
<script>
    (() => {
        const input = document.getElementById('roleSearch');
        const rows  = document.querySelectorAll('#rolesTable tbody tr.role-row');

        input.addEventListener('input', () => {
            const filter = input.value.trim().toLowerCase();

            rows.forEach(row => {
                const name = row.dataset.name ?? '';
                const abbr = row.dataset.abbr ?? '';
                row.style.display = (!filter || name.includes(filter) || abbr.includes(filter)) ? '' : 'none';
            });
        });
    })();
</script>
@endpush

@endsection