@extends('layouts.app')

@section('title', 'Create Role')
@section('page-title', 'Create New Role')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm"
         x-data="roleForm()">

        <div class="px-5 py-4 border-b border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Create New Role</h2>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Define a new role in the system hierarchy</p>
        </div>

        <form method="POST" action="{{ route('admin.roles.store') }}" class="p-5 space-y-5">
            @csrf

            {{-- Role Name --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                    Role Name <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white"
                    placeholder="Enter role name">
                @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Abbreviation --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                    Abbreviation
                </label>
                <input type="text" name="abbreviation" value="{{ old('abbreviation') }}"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white"
                    placeholder="e.g., SA, OA, OM">
                @error('abbreviation') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Description --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                    Description
                </label>
                <textarea name="description" rows="3"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white"
                    placeholder="Describe this role...">{{ old('description') }}</textarea>
                @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Parent Role --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                    Parent Role (Hierarchy)
                </label>
                <select x-model="selectedParent" class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white">
                    <option value="">None (Top Level)</option>
                    @foreach($roles as $role)
                        @if($role->level <= 6)
                            <option value="{{ $role->id }}" data-level="{{ $role->level }}">
                                {{ $role->name }} (Level {{ $role->level }})
                            </option>
                        @endif
                    @endforeach
                </select>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                    The new role will inherit one level below the selected parent.
                </p>
            </div>

            {{-- Computed Level & Hidden Input --}}
            <div class="p-3 bg-gray-50 dark:bg-gray-700/30 rounded-lg border border-gray-200 dark:border-gray-600">
                <p class="text-xs text-gray-600 dark:text-gray-300">
                    Computed Level:
                    <span class="font-semibold text-indigo-600 dark:text-indigo-400" x-text="computedLevel"></span>
                </p>
                <input type="hidden" name="level" x-model="computedLevel">
                <div x-show="computedLevel == 1 && !isSystem" class="mt-2 text-xs text-amber-600 dark:text-amber-400">
                    ⚠️ Level 1 is reserved for system roles. Check the "System Role" option below.
                </div>
                {{-- Debug info (remove in production) --}}
                <div class="mt-2 text-xs text-gray-400">
                    Debug: selectedParent = <span x-text="selectedParent"></span> |
                    computedLevel = <span x-text="computedLevel"></span>
                </div>
            </div>

            {{-- System Role Checkbox --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                    System Role
                </label>
                <div class="flex items-start gap-3 p-3 bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-600 rounded-lg">
                    <input type="hidden" name="is_system" value="0">
                    <input type="checkbox" id="is_system" name="is_system" value="1"
                           x-model="isSystem"
                           class="mt-0.5 w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                    <div>
                        <label for="is_system" class="text-sm text-gray-700 dark:text-gray-300 cursor-pointer font-medium">
                            Mark as system role
                        </label>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                            System roles are protected — their names cannot be changed and they cannot be deleted.
                        </p>
                        <p class="text-xs text-amber-600 dark:text-amber-400 mt-1" x-show="isSystem">
                            ⚠️ Level 1 is now allowed.
                        </p>
                    </div>
                </div>
                @error('is_system') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Buttons --}}
            <div class="flex gap-3 pt-4 border-t border-gray-100 dark:border-gray-700">
                <button type="submit"
                    class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white transition-all duration-200 shadow-sm hover:shadow-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
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

<script>
function roleForm() {
    return {
        selectedParent: '{{ old('parent_id', '') }}',
        isSystem: {{ old('is_system', false) ? 'true' : 'false' }},

        get computedLevel() {
            if (!this.selectedParent) return 1;
            const option = document.querySelector(`option[value='${this.selectedParent}']`);
            if (!option) return 1;
            const parentLevel = parseInt(option.dataset.level);
            // If parentLevel is NaN, return 1
            if (isNaN(parentLevel)) return 1;
            return parentLevel + 1;
        }
    }
}
</script>
@endsection