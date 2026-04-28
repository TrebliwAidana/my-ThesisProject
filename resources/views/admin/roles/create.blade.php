@extends('layouts.app')

@section('title', 'Create Role')
@section('page-title', 'Create New Role')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gold-200 dark:border-gold-800 overflow-hidden shadow-sm"
         x-data="roleForm()">

        <div class="px-5 py-4 border-b border-gray-100 dark:border-gold-800 bg-gray-50 dark:bg-gray-800/50">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Create New Role</h2>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Define a new role in the system hierarchy</p>
        </div>

        <form method="POST" action="{{ route('admin.roles.store') }}" class="p-5 space-y-5">
            @csrf

            @if ($errors->any())
                <div class="p-3 bg-red-50 dark:bg-red-900/30 border border-red-300 dark:border-red-700 rounded-lg">
                    <p class="text-sm font-semibold text-red-700 dark:text-red-300 mb-1">Please fix the following errors:</p>
                    <ul class="text-sm text-red-600 dark:text-red-400 space-y-0.5 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Role Name --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                    Role Name <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name') }}" required maxlength="100"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-gold-500 dark:bg-gray-700 dark:text-white {{ $errors->has('name') ? 'border-red-400' : '' }}"
                    placeholder="Enter role name">
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Abbreviation --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                    Abbreviation <span class="text-gray-400 font-normal">(Optional)</span>
                </label>
                <input type="text" name="abbreviation" value="{{ old('abbreviation') }}" maxlength="10"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-gold-500 dark:bg-gray-700 dark:text-white"
                    placeholder="e.g., SA, OA, OM">
                @error('abbreviation') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{--
                FIX 1: field was name="description" — controller validates & stores "desc".
                Silent discard: description text was accepted by the form but never saved.
            --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                    Description <span class="text-gray-400 font-normal">(Optional)</span>
                </label>
                <textarea name="desc" rows="3" maxlength="500"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-gold-500 dark:bg-gray-700 dark:text-white resize-none"
                    placeholder="Describe this role…">{{ old('desc') }}</textarea>
                @error('desc') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{--
                FIX 2 + 3: Parent Role select now has name="parent_id" so the value is
                actually submitted. Previously there was no name attribute, meaning
                parent_id was always null regardless of the user's selection.

                FIX 4: position data is stored in x-data as a JSON map built server-side,
                so computedLevel never has to query the DOM. This avoids the race
                condition where the Alpine getter ran before the <option> existed.
            --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                    Parent Role <span class="text-gray-400 font-normal">(Optional — sets hierarchy level)</span>
                </label>
                <select name="parent_id"
                        x-model="selectedParent"
                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-gold-500 dark:bg-gray-700 dark:text-white">
                    <option value="">None (creates at level 2)</option>
                    @foreach($roles as $role)
                        @if($role->level <= 6)
                            <option value="{{ $role->id }}"
                                    data-level="{{ $role->level }}"
                                    {{ old('parent_id') == $role->id ? 'selected' : '' }}>
                                {{ $role->name }} (Level {{ $role->level }})
                            </option>
                        @endif
                    @endforeach
                </select>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                    The new role will be placed one level below the selected parent. Leave empty for level 2.
                </p>
                @error('parent_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{--
                FIX 5: Computed Level info panel.
                - Removed the debug output block (was left in from development).
                - Level-1 warning no longer fires on initial load because the default
                  computedLevel is 2 (no parent = level 2), not 1.
                - Hidden input carries the resolved level to the controller.
            --}}
            <div class="p-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg border border-gold-200 dark:border-gray-600">
                <p class="text-xs text-gray-600 dark:text-gray-300">
                    Computed Level:
                    <span class="font-semibold text-primary-600 dark:text-primary-400" x-text="computedLevel"></span>
                    <span class="text-gray-400 ml-1">(lower = higher authority)</span>
                </p>
                <input type="hidden" name="level" x-model="computedLevel">

                {{--
                    Only warn when: level IS 1 AND is_system is NOT checked.
                    Previously this fired on page load because the old default was 1.
                --}}
                <p class="mt-2 text-xs text-amber-600 dark:text-amber-400"
                   x-show="computedLevel == 1 && !isSystem" x-cloak>
                    ⚠️ Level 1 is reserved for system roles. Enable "System Role" below.
                </p>
            </div>

            {{-- System Role --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                    System Role
                </label>
                <div class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 border border-gold-200 dark:border-gray-600 rounded-lg">
                    <input type="hidden" name="is_system" value="0">
                    <input type="checkbox" id="is_system" name="is_system" value="1"
                           x-model="isSystem"
                           {{ old('is_system') ? 'checked' : '' }}
                           class="mt-0.5 w-4 h-4 rounded border-gray-300 text-primary-600 focus:ring-gold-500">
                    <div>
                        <label for="is_system" class="text-sm text-gray-700 dark:text-gray-300 cursor-pointer font-medium">
                            Mark as system role
                        </label>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                            System roles are protected — their names cannot be changed and they cannot be deleted.
                        </p>
                        <p class="text-xs text-green-600 dark:text-green-400 mt-1" x-show="isSystem" x-cloak>
                            ✓ Level 1 is now permitted.
                        </p>
                    </div>
                </div>
                @error('is_system') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Buttons --}}
            <div class="flex gap-3 pt-4 border-t border-gray-100 dark:border-gold-800">
                <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium rounded-lg bg-primary-600 hover:bg-gold-500 text-white transition-all duration-200 shadow-sm hover:shadow-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Create Role
                </button>
                <a href="{{ route('admin.roles.index') }}"
                    class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium rounded-lg bg-gray-500 hover:bg-gray-600 text-white transition-all duration-200 shadow-sm hover:shadow-md">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<style>[x-cloak] { display: none !important; }</style>

@push('scripts')
<script>
    {{--
        Level map built server-side: role_id → level.
        This removes the DOM query from the Alpine getter entirely, eliminating
        the race condition where computedLevel ran before the <option> existed.
    --}}
    const roleLevelMap = @json(
        $roles->where('level', '<=', 6)->pluck('level', 'id')
    );

    function roleForm() {
        return {
            {{--
                FIX: default selectedParent is '' (no parent).
                computedLevel now defaults to 2 (not 1), so the level-1
                warning does NOT fire on fresh page load.
            --}}
            selectedParent: '{{ old('parent_id', '') }}',
            isSystem: {{ old('is_system', false) ? 'true' : 'false' }},

            get computedLevel() {
                const id = parseInt(this.selectedParent);
                if (!id || !roleLevelMap[id]) {
                    // No parent selected → place at level 2 (just below System Administrator)
                    return 2;
                }
                return roleLevelMap[id] + 1;
            },
        };
    }
</script>
@endpush

@endsection