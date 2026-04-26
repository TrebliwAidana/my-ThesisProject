@extends('layouts.app')

@section('title', 'Edit Financial Category')
@section('page-title', 'Edit Financial Category')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gold-200 dark:border-gold-800 overflow-hidden">
        <div class="bg-gradient-to-r from-emerald-600 to-emerald-700 dark:from-emerald-800 dark:to-emerald-900 px-6 py-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-white">Edit Financial Category</h2>
                    <p class="text-sm text-emerald-100">Update category information</p>
                </div>
            </div>
        </div>

        <div class="p-6">
            <form method="POST" action="{{ route('admin.financial-categories.update', $financialCategory) }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Name <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $financialCategory->name) }}" required maxlength="100"
                           class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-transparent">
                    @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Applies To <span class="text-red-500">*</span></label>
                    <select name="type" required
                            class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-transparent">
                        <option value="both"    {{ old('type', $financialCategory->type) === 'both'    ? 'selected' : '' }}>Both (Income & Expense)</option>
                        <option value="income"  {{ old('type', $financialCategory->type) === 'income'  ? 'selected' : '' }}>Income only</option>
                        <option value="expense" {{ old('type', $financialCategory->type) === 'expense' ? 'selected' : '' }}>Expense only</option>
                    </select>
                    @error('type')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Description</label>
                    <input type="text" name="description" value="{{ old('description', $financialCategory->description) }}" maxlength="255"
                           class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-transparent">
                    @error('description')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="flex gap-3 pt-4 border-t border-gold-200 dark:border-gold-800">
                    <button type="submit"
                            class="flex-1 bg-emerald-600 hover:bg-gold-500 text-white font-semibold py-2 rounded-lg transition shadow-sm">
                        Save Changes
                    </button>
                    <a href="{{ route('admin.financial-categories.index') }}"
                       class="flex-1 bg-gray-200 hover:bg-gray-300 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-semibold py-2 rounded-lg text-center transition">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection