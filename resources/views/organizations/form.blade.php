@php
    $isEdit = isset($organization) && $organization !== null;
    $action = $isEdit
        ? route('admin.organizations.update', $organization)
        : route('admin.organizations.store');
@endphp

<div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-primary-600 to-primary-700 dark:from-primary-800 dark:to-primary-900 p-6 mb-6">
    <div class="relative z-10">
        <h1 class="text-2xl font-bold text-white">{{ $isEdit ? 'Edit ' . $organization->name : 'New Organization' }}</h1>
        <p class="text-primary-100 text-sm mt-1">{{ $isEdit ? 'Update organization details' : 'Register a new school organization' }}</p>
    </div>
    <div class="absolute top-0 right-0 -mt-8 -mr-8 w-64 h-64 bg-white/5 rounded-full blur-3xl pointer-events-none"></div>
</div>

<form method="POST" action="{{ $action }}" enctype="multipart/form-data">
    @csrf
    @if($isEdit) @method('PUT') @endif

    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gold-200 dark:border-gold-800 shadow-sm p-6 space-y-5">

        {{-- Name + Abbreviation --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                    Organization Name <span class="text-red-500">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name', $organization?->name) }}" required
                       class="w-full px-3 py-2.5 border border-gold-200 dark:border-gold-800 rounded-xl text-sm bg-gray-50 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-gold-500 focus:outline-none @error('name') border-red-400 @enderror">
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Abbreviation</label>
                <input type="text" name="abbreviation" value="{{ old('abbreviation', $organization?->abbreviation) }}" maxlength="20"
                       placeholder="e.g. SSG, SBO"
                       class="w-full px-3 py-2.5 border border-gold-200 dark:border-gold-800 rounded-xl text-sm bg-gray-50 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-gold-500 focus:outline-none">
            </div>
        </div>

        {{-- Description --}}
        <div>
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Description</label>
            <textarea name="description" rows="3"
                      class="w-full px-3 py-2.5 border border-gold-200 dark:border-gold-800 rounded-xl text-sm bg-gray-50 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-gold-500 focus:outline-none resize-none">{{ old('description', $organization?->description) }}</textarea>
        </div>

        {{-- Type + Year + Adviser --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">
                    Type <span class="text-red-500">*</span>
                </label>
                <select name="type" required
                        class="w-full px-3 py-2.5 border border-gold-200 dark:border-gold-800 rounded-xl text-sm bg-gray-50 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-gold-500 focus:outline-none">
                    @foreach($types as $key => $label)
                    <option value="{{ $key }}" {{ old('type', $organization?->type) == $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Academic Year</label>
                <input type="text" name="academic_year"
                       value="{{ old('academic_year', $organization?->academic_year) }}"
                       placeholder="e.g. 2025-2026"
                       class="w-full px-3 py-2.5 border border-gold-200 dark:border-gold-800 rounded-xl text-sm bg-gray-50 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-gold-500 focus:outline-none">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Adviser</label>
                <select name="adviser_id"
                        class="w-full px-3 py-2.5 border border-gold-200 dark:border-gold-800 rounded-xl text-sm bg-gray-50 dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-gold-500 focus:outline-none">
                    <option value="">— None —</option>
                    @foreach($advisers as $adviser)
                    <option value="{{ $adviser->id }}"
                            {{ old('adviser_id', $organization?->adviser_id) == $adviser->id ? 'selected' : '' }}>
                        {{ $adviser->full_name }}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Logo + Active --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 items-start">
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Logo</label>
                <input type="file" name="logo" accept="image/*"
                       class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 transition">
                @if($isEdit && $organization->logo)
                <div class="mt-2 flex items-center gap-2">
                    <img src="{{ $organization->logo_url }}" alt="Logo" class="w-10 h-10 rounded-lg object-cover border border-gold-200">
                    <span class="text-xs text-gray-400">Upload a new image to replace</span>
                </div>
                @endif
                @error('logo')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>
            <div class="flex items-center gap-3 pt-7">
                <input type="checkbox" name="is_active" id="is_active" value="1"
                       {{ old('is_active', $organization?->is_active ?? true) ? 'checked' : '' }}
                       class="w-4 h-4 text-primary-600 rounded border-gray-300 focus:ring-primary-500">
                <label for="is_active" class="text-sm font-medium text-gray-700 dark:text-gray-300">
                    Active organization
                </label>
            </div>
        </div>

        {{-- Actions --}}
        <div class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-gray-700">
            <a href="{{ $isEdit ? route('admin.organizations.show', $organization) : route('admin.organizations.index') }}"
               class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 transition">
                ← Cancel
            </a>
            <button type="submit"
                    class="bg-primary-600 hover:bg-gold-500 text-white px-6 py-2.5 rounded-xl text-sm font-semibold transition shadow-md">
                {{ $isEdit ? 'Save Changes' : 'Create Organization' }}
            </button>
        </div>
    </div>
</form>