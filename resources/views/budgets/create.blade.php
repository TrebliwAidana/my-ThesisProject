@extends('layouts.app')

@section('title', 'Create Budget Request — VSULHS_SSLG')

@section('content')

{{-- Vanishing Popup Notifications for Validation Errors --}}
@if($errors->any())
    <div x-data="{ show: true }" 
         x-init="setTimeout(() => show = false, 5000)"
         x-show="show"
         x-transition:enter="transform transition duration-300 ease-out"
         x-transition:enter-start="translate-x-full opacity-0"
         x-transition:enter-end="translate-x-0 opacity-100"
         x-transition:leave="transform transition duration-200 ease-in"
         x-transition:leave-start="translate-x-0 opacity-100"
         x-transition:leave-end="translate-x-full opacity-0"
         class="fixed top-20 right-4 z-50 w-96 max-w-full rounded-lg shadow-lg overflow-hidden border-l-4 border-red-500 bg-red-50 dark:bg-red-900/30">
        <div class="p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm font-medium text-red-800 dark:text-red-200">Please fix the following errors:</p>
                    <ul class="text-sm text-red-700 dark:text-red-300 mt-1 list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <div class="ml-4 flex-shrink-0">
                    <button @click="show = false" class="inline-flex text-red-500 hover:text-red-600 focus:outline-none">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif

{{-- Rest of your budgets/create.blade.php content remains the same --}}
<div class="mb-6">
    <a href="{{ route('budgets.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        Back to Budgets
    </a>
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mt-3">Create Budget Request</h1>
    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Submit a new budget request for approval</p>
</div>

<div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 max-w-2xl mx-auto">
    <form method="POST" action="{{ route('budgets.store') }}" enctype="multipart/form-data">
        @csrf
        <!-- Keep all your existing form fields exactly as they are -->
        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Budget Title</label>
            <input type="text" name="title" value="{{ old('title') }}" required
                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
        </div>

        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Description</label>
            <textarea name="description" rows="4"
                      class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                      placeholder="Provide details about this budget request...">{{ old('description') }}</textarea>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Amount (₱)</label>
                <input type="number" name="amount" value="{{ old('amount') }}" required step="0.01" min="0"
                       class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
            </div>
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Category</label>
                <select name="category" required
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                    <option value="">Select Category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->name }}" {{ old('category') == $category->name ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Attachment (Optional)</label>
            <input type="file" name="attachment" 
                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 dark:file:bg-indigo-900/50 dark:file:text-indigo-400">
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Accepted formats: PDF, DOC, DOCX, XLSX (Max: 5MB)</p>
        </div>

        <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
            <div class="flex gap-3">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <p class="text-sm text-blue-800 dark:text-blue-300 font-medium">Important Information</p>
                    <p class="text-xs text-blue-700 dark:text-blue-400 mt-1">
                        • All budget requests will be reviewed by officers and advisers.<br>
                        • Please provide accurate and complete information.<br>
                        • Attach supporting documents if available.<br>
                        • You can edit your request until it is reviewed.
                    </p>
                </div>
            </div>
        </div>

        <div class="flex gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
            <button type="submit"
                    class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition">
                Submit Budget Request
            </button>
            <a href="{{ route('budgets.index') }}"
               class="flex-1 text-center text-sm font-semibold text-gray-600 dark:text-gray-400 border border-gray-300 dark:border-gray-600 px-5 py-2.5 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                Cancel
            </a>
        </div>
    </form>
</div>

@endsection