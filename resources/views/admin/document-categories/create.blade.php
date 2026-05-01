@extends('layouts.app')

@section('title', 'Create Category')
@section('page-title', 'Create Document Category')

@section('content')
<div class="max-w-xl mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gold-200 dark:border-gold-800 p-6">
        <form method="POST" action="{{ route('admin.document-categories.store') }}">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-semibold mb-2">Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold mb-2">Description</label>
                <textarea name="description" rows="3"
                          class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2">{{ old('description') }}</textarea>
            </div>

            <div class="mb-6">
                <label class="inline-flex items-center gap-2">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                    <span class="text-sm">Active</span>
                </label>
            </div>

            <div class="flex gap-3">
                    <button
                        type="submit"
                        x-data="{ busy: false }"
                        @click="if (busy) { $event.preventDefault(); $event.stopImmediatePropagation(); return; }"
                        @submit.window="if ($event.target === $el.closest('form')) { busy = true; }"
                        :disabled="busy"
                        :class="busy ? 'opacity-60 cursor-not-allowed' : ''"
                        class="bg-emerald-600 hover:bg-gold-500 text-white px-6 py-2 rounded-lg transition"
                    >
                        <span x-show="!busy">Create Category</span>
                        <span x-show="busy" class="flex items-center gap-2">
                            <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                            </svg>
                            Creating...
                        </span>
                    </button>
                <a href="{{ route('admin.document-categories.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection