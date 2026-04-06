@extends('layouts.app')

@section('title', 'Edit Document')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gold-200 dark:border-gold-800 overflow-hidden">
        {{-- Emerald header --}}
        <div class="bg-gradient-to-r from-emerald-600 to-emerald-700 dark:from-emerald-800 dark:to-emerald-900 px-6 py-4 border-b border-emerald-200 dark:border-emerald-800">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="font-semibold text-white">Edit Document Metadata</h2>
                    <p class="text-emerald-100 text-xs">Update document information</p>
                </div>
            </div>
        </div>

        <div class="p-6">
            <form method="POST" action="{{ route('documents.update', $document) }}">
                @csrf @method('PUT')

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $document->title) }}" required
                           class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-transparent">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Description</label>
                    <textarea name="description" rows="3"
                              class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-transparent">{{ old('description', $document->description) }}</textarea>
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Category</label>
                        <input type="text" name="category" value="{{ old('category', $document->category) }}"
                               class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Visibility</label>
                        <select name="is_public"
                                class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-transparent">
                            <option value="0" {{ !$document->is_public ? 'selected' : '' }}>Private (your organization)</option>
                            <option value="1" {{ $document->is_public ? 'selected' : '' }}>Public (everyone)</option>
                        </select>
                    </div>
                </div>

                <div class="flex justify-between gap-3">
                    <button type="submit"
                            class="bg-emerald-600 hover:bg-gold-500 text-white text-sm font-semibold px-6 py-2 rounded-lg transition shadow-sm">
                        Update
                    </button>
                    <a href="{{ route('documents.index') }}"
                       class="bg-red-600 hover:bg-red-700 text-white text-sm font-semibold px-6 py-2 rounded-lg transition shadow-sm">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection