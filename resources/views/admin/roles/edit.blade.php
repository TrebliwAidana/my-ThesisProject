@extends('layouts.app')

@section('title', 'Edit Role — ' . $role->name)

@php
    /**
     * Level map driven by the same constraints as AdminController:
     *   - Level 1 is reserved for system roles (is_system = true only)
     *   - Non-system roles may use levels 2–10
     *   - Predefined roles can only update level (name/desc/etc. are locked)
     */
    $levelLabels = [
        1  => 'System Reserved',
        2  => 'Club Advisor',
        3  => 'Treasurer',
        4  => 'Auditor',
        5  => 'Guest',
        6  => 'Level 6',
        7  => 'Level 7',
        8  => 'Level 8',
        9  => 'Level 9',
        10 => 'Level 10',
    ];

    // Level 1 is only available when is_system is checked; otherwise 2–10
    $editableLevels = collect($levelLabels)->except(1);
@endphp

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    {{-- ── Header ──────────────────────────────────────────────────────────── --}}
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('admin.roles.index') }}"
               class="inline-flex items-center gap-1 text-sm text-gray-500 hover:text-gray-700
                      dark:hover:text-gray-300 transition mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to Roles
            </a>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Role</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                Updating: <span class="font-medium text-gray-700 dark:text-gray-300">{{ $role->name }}</span>
            </p>
        </div>

        @if ($role->is_system)
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold
                         bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-400
                         border border-amber-200 dark:border-amber-800">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0
                             00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                System Role — Locked
            </span>
        @endif
    </div>

    {{-- ── Flash Messages ───────────────────────────────────────────────────── --}}
    @foreach (['success' => 'green', 'error' => 'red', 'info' => 'blue'] as $type => $color)
        @if (session($type))
            <div class="p-4 bg-{{ $color }}-50 dark:bg-{{ $color }}-900/20
                        border border-{{ $color }}-200 dark:border-{{ $color }}-800 rounded-xl">
                <p class="text-sm font-semibold text-{{ $color }}-700 dark:text-{{ $color }}-400">
                    {{ session($type) }}
                </p>
            </div>
        @endif
    @endforeach

    @if ($errors->any())
        <div class="p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl">
            <p class="text-sm font-semibold text-red-700 dark:text-red-400 mb-1">Please fix the following errors:</p>
            <ul class="text-sm text-red-600 dark:text-red-400 space-y-0.5 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- ── Main Card ────────────────────────────────────────────────────────── --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gold-200 dark:border-gold-800 p-6">

        @if ($role->is_system)
            {{-- ── Read-only view: system roles cannot be edited ──────────── --}}
            <div class="text-center p-6 bg-amber-50 dark:bg-amber-900/20 rounded-xl mb-6">
                <svg class="w-12 h-12 mx-auto text-amber-500 dark:text-amber-400 mb-2"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0
                             00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                <p class="text-amber-700 dark:text-amber-400 font-semibold">System Role — Cannot be edited</p>
                <p class="text-sm text-amber-600 dark:text-amber-500 mt-1">
                    System roles are protected and cannot be modified.
                </p>
            </div>

            <dl class="space-y-4">
                @foreach ([
                    'Role Name'    => $role->name,
                    'Abbreviation' => $role->abbreviation ?? '—',
                    'Description'  => $role->desc ?? '—',
                    'Level'        => 'Level ' . $role->level . ' — ' . ($levelLabels[$role->level] ?? 'Unknown'),
                ] as $label => $value)
                    <div>
                        <dt class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                            {{ $label }}
                        </dt>
                        <dd class="w-full border border-gold-200 dark:border-gray-600 rounded-lg px-4 py-2.5
                                   text-sm bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400">
                            {{ $value }}
                        </dd>
                    </div>
                @endforeach

                <div>
                    <dt class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">System Role</dt>
                    <dd class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-gray-700/50
                               border border-gold-200 dark:border-gray-600 rounded-lg">
                        <input type="checkbox" disabled checked
                               class="w-4 h-4 rounded border-gray-300 text-primary-600 cursor-not-allowed">
                        <span class="text-sm text-gray-700 dark:text-gray-300">
                            This role is marked as a system role
                        </span>
                    </dd>
                </div>
            </dl>

            <div class="flex justify-end mt-6">
                <a href="{{ route('admin.roles.index') }}"
                   class="px-5 py-2.5 text-sm font-medium rounded-lg bg-gray-500 hover:bg-gray-600
                          text-white transition-all duration-200 shadow-sm">
                    Back to Roles
                </a>
            </div>

        @elseif ($role->is_predefined)
            {{-- ── Predefined roles: level-only form (controller enforces this) ─── --}}
            <div class="p-4 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800
                        rounded-xl mb-6">
                <p class="text-sm text-blue-700 dark:text-blue-400">
                    <span class="font-semibold">Predefined role</span> — only the
                    <span class="font-semibold">Level</span> can be changed.
                    Name, description, and other fields are locked.
                </p>
            </div>

            <form method="POST" action="{{ route('admin.roles.update', $role->id) }}">
                @csrf
                @method('PUT')

                <div class="mb-6">
                    <label for="level"
                           class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                        Level <span class="text-red-500">*</span>
                    </label>
                    <select id="level" name="level" required
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                   dark:text-white rounded-lg px-4 py-2.5 text-sm
                                   focus:outline-none focus:ring-2 focus:ring-gold-500
                                   @error('level') border-red-400 @enderror">
                        @foreach ($editableLevels as $lvl => $label)
                            <option value="{{ $lvl }}"
                                {{ old('level', $role->level) == $lvl ? 'selected' : '' }}>
                                Level {{ $lvl }} — {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                        Lower number = higher authority. Level 1 is reserved for system roles.
                    </p>
                    @error('level')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <hr class="border-gray-100 dark:border-gold-800 mb-5">

                <div class="flex gap-3">
                    <button type="submit"
                            class="flex-1 bg-primary-600 hover:bg-gold-500 text-white text-sm font-semibold
                                   px-5 py-2.5 rounded-lg transition flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M5 13l4 4L19 7"/>
                        </svg>
                        Save Changes
                    </button>
                    <a href="{{ route('admin.roles.index') }}"
                       class="flex-1 text-center text-sm font-semibold text-gray-600 dark:text-gray-400
                              border border-gray-300 dark:border-gray-600 px-5 py-2.5 rounded-lg
                              hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        Cancel
                    </a>
                </div>
            </form>

        @else
            {{-- ── Full editable form for custom (non-system, non-predefined) roles ── --}}
            <form method="POST" action="{{ route('admin.roles.update', $role->id) }}">
                @csrf
                @method('PUT')

                {{-- Role Name --}}
                <div class="mb-5">
                    <label for="name"
                           class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                        Role Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name"
                           value="{{ old('name', $role->name) }}"
                           required maxlength="100"
                           class="w-full border dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5
                                  text-sm focus:outline-none focus:ring-2 focus:ring-gold-500
                                  @error('name') border-red-400 @else border-gray-300 dark:border-gray-600 @enderror">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Abbreviation --}}
                <div class="mb-5">
                    <label for="abbreviation"
                           class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                        Abbreviation
                        <span class="text-gray-400 font-normal">(Optional)</span>
                    </label>
                    <input type="text" id="abbreviation" name="abbreviation"
                           value="{{ old('abbreviation', $role->abbreviation) }}"
                           maxlength="10"
                           class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                  dark:text-white rounded-lg px-4 py-2.5 text-sm
                                  focus:outline-none focus:ring-2 focus:ring-gold-500
                                  @error('abbreviation') border-red-400 @enderror">
                    @error('abbreviation')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Description — field name "desc" matches DB column and controller validation --}}
                <div class="mb-5">
                    <label for="desc"
                           class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                        Description
                        <span class="text-gray-400 font-normal">(Optional)</span>
                    </label>
                    <textarea id="desc" name="desc" rows="3" maxlength="500"
                              class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                     dark:text-white rounded-lg px-4 py-2.5 text-sm
                                     focus:outline-none focus:ring-2 focus:ring-gold-500 resize-none
                                     @error('desc') border-red-400 @enderror">{{ old('desc', $role->desc) }}</textarea>
                    @error('desc')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{--
                    Level + System Role — one x-data scope so the is_system checkbox
                    reactively unlocks level 1 in the dropdown.
                    Controller guard: level == 1 is rejected unless is_system = true.
                --}}
                <div class="mb-5" x-data="{ isSystem: {{ old('is_system', $role->is_system) ? 'true' : 'false' }} }">

                    <label for="level"
                           class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                        Level <span class="text-red-500">*</span>
                    </label>
                    <select id="level" name="level" required
                            class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700
                                   dark:text-white rounded-lg px-4 py-2.5 text-sm
                                   focus:outline-none focus:ring-2 focus:ring-gold-500
                                   @error('level') border-red-400 @enderror">

                        {{-- Level 1 only unlocks when is_system is toggled on --}}
                        <template x-if="isSystem">
                            <option value="1"
                                {{ old('level', $role->level) == 1 ? 'selected' : '' }}>
                                Level 1 — {{ $levelLabels[1] }}
                            </option>
                        </template>

                        @foreach ($editableLevels as $lvl => $label)
                            <option value="{{ $lvl }}"
                                {{ old('level', $role->level) == $lvl ? 'selected' : '' }}>
                                Level {{ $lvl }} — {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                        Lower number = higher authority. Level 1 is reserved for system roles.
                    </p>
                    @error('level')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror

                    {{-- System Role toggle --}}
                    <div class="mt-5">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                            System Role
                        </label>
                        <div class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-gray-700/50
                                    border border-gold-200 dark:border-gray-600 rounded-lg">
                            <input type="hidden" name="is_system" value="0">
                            <input type="checkbox" id="is_system" name="is_system" value="1"
                                   x-model="isSystem"
                                   {{ old('is_system', $role->is_system) ? 'checked' : '' }}
                                   class="mt-0.5 w-4 h-4 rounded border-gray-300 text-primary-600
                                          focus:ring-gold-500">
                            <div>
                                <label for="is_system"
                                       class="text-sm text-gray-700 dark:text-gray-300 cursor-pointer font-medium">
                                    Mark as system role
                                </label>
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                                    System roles are protected — names cannot be changed and they
                                    cannot be deleted. Enables level 1 selection above.
                                </p>
                            </div>
                        </div>
                        @error('is_system')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <hr class="border-gray-100 dark:border-gold-800 mb-5">

                <div class="flex gap-3">
                    <button type="submit"
                            class="flex-1 bg-primary-600 hover:bg-gold-500 text-white text-sm font-semibold
                                   px-5 py-2.5 rounded-lg transition flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M5 13l4 4L19 7"/>
                        </svg>
                        Save Changes
                    </button>
                    <a href="{{ route('admin.roles.index') }}"
                       class="flex-1 text-center text-sm font-semibold text-gray-600 dark:text-gray-400
                              border border-gray-300 dark:border-gray-600 px-5 py-2.5 rounded-lg
                              hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        Cancel
                    </a>
                </div>
            </form>
        @endif
    </div>

    {{-- ── Danger Zone — custom roles only ─────────────────────────────────── --}}
    @if (!$role->is_system && !$role->is_predefined)
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-red-200 dark:border-red-900/50 p-6">
            <h3 class="text-sm font-semibold text-red-600 dark:text-red-400 mb-1">Danger Zone</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                Deleting this role is permanent. All users currently assigned this role must be
                reassigned first.
            </p>
            <button type="button"
                    onclick="confirmDelete({{ $role->id }}, '{{ addslashes($role->name) }}')"
                    class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold
                           text-red-600 dark:text-red-400 border border-red-300 dark:border-red-700
                           rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0
                             01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0
                             00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
                Delete this Role
            </button>
        </div>

        <form id="delete-role-form"
              action="{{ route('admin.roles.destroy', $role->id) }}"
              method="POST"
              class="hidden">
            @csrf
            @method('DELETE')
        </form>
    @endif

</div>

@push('scripts')
<script>
function confirmDelete(roleId, roleName) {
    if (!confirm(`⚠️ Delete role "${roleName}"?\n\nThis action cannot be undone.\nAll users must be unassigned from this role first.`)) return;
    document.getElementById('delete-role-form').submit();
}
</script>
@endpush
@endsection