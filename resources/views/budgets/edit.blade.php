@extends('layouts.app')

@section('title', 'Edit Budget — VSULHS_SSLG')

@section('content')

<div class="mb-6">
    <a href="{{ route('budgets.show', $budget) }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        Back to Budget Details
    </a>
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mt-3">Edit Budget Request</h1>
    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Modify your budget request before submission</p>
</div>

<div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 max-w-2xl mx-auto">
    <form method="POST" action="{{ route('budgets.update', $budget) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        @if($budget->status !== 'pending')
        <div class="mb-6 p-4 bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-500 rounded-r-lg">
            <div class="flex gap-3">
                <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <div>
                    <p class="text-sm text-yellow-800 dark:text-yellow-300 font-medium">Cannot Edit</p>
                    <p class="text-xs text-yellow-700 dark:text-yellow-400 mt-1">
                        This budget request has already been {{ $budget->status }} and cannot be edited.
                        Only pending requests can be modified.
                    </p>
                </div>
            </div>
        </div>
        @else
        {{-- Title --}}
        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Budget Title</label>
            <input type="text" name="title" value="{{ old('title', $budget->title) }}" required
                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
        </div>

        {{-- Description --}}
        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Description</label>
            <textarea name="description" rows="4"
                      class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                      placeholder="Provide details about this budget request...">{{ old('description', $budget->description) }}</textarea>
        </div>

        {{-- Amount and Category --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Amount (₱)</label>
                <input type="number" name="amount" value="{{ old('amount', $budget->amount) }}" required step="0.01" min="0"
                       class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Category</label>
                <select name="category" required
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                    <option value="">Select Category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->name }}" {{ old('category', $budget->category) == $category->name ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        {{-- Attachment --}}
        <div class="mb-6">
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Attachment</label>
            @if($budget->attachment_path)
            <div class="mb-3 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                    </svg>
                    <span class="text-sm text-gray-600 dark:text-gray-400">Current file attached</span>
                    <a href="{{ asset('storage/' . $budget->attachment_path) }}" target="_blank" class="text-xs text-indigo-600 hover:text-indigo-700 dark:text-indigo-400">
                        View
                    </a>
                </div>
            </div>
            @endif
            <input type="file" name="attachment" 
                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-900/50 dark:file:text-indigo-400">
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Accepted formats: PDF, DOC, DOCX, XLSX (Max: 5MB). Leave empty to keep current file.</p>
        </div>

        {{-- Info Box --}}
        <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
            <div class="flex gap-3">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <p class="text-sm text-blue-800 dark:text-blue-300 font-medium">Note:</p>
                    <p class="text-xs text-blue-700 dark:text-blue-400 mt-1">
                        Once you submit changes, the request will remain pending for review.
                        Any existing review comments will be cleared.
                    </p>
                </div>
            </div>
        </div>

        {{-- Buttons --}}
        <div class="flex gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
            <button type="submit"
                    class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition">
                Update Budget Request
            </button>
            <a href="{{ route('budgets.show', $budget) }}"
               class="flex-1 text-center text-sm font-semibold text-gray-600 dark:text-gray-400 border border-gray-300 dark:border-gray-600 px-5 py-2.5 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                Cancel
            </a>
        </div>
        @endif
    </form>
</div>

@endsection