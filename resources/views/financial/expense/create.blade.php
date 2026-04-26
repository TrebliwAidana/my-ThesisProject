@extends('layouts.app')

@section('title', 'Add Expense')
@section('page-title', 'Add Expense')

@section('content')

<style>
    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    input[type=number] {
        -moz-appearance: textfield;
        appearance: textfield;
    }
</style>

<div class="max-w-2xl mx-auto" x-data="expenseForm()">

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-red-200 dark:border-red-800 p-6">

        @if($errors->any())
            <div class="mb-4 p-3 bg-red-100 border border-red-300 text-red-700 rounded-lg text-sm">
                <strong>Please fix the following errors:</strong>
                <ul class="mt-2 list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="mb-6 flex items-center gap-2">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-red-100 text-red-700">↓ Expense</span>
            <span class="text-xs text-gray-400 dark:text-gray-500">Cash paid out</span>
        </div>

        <form method="POST" action="{{ route('financial.expense.store') }}" enctype="multipart/form-data" id="expense-form">
            @csrf

            {{-- Category --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Category <span class="text-red-500">*</span>
                </label>

                <template x-if="loadingCategories">
                    <div class="w-full border border-red-300 dark:border-red-700 rounded-lg px-4 py-2 text-sm text-gray-400 bg-gray-50 dark:bg-gray-700 animate-pulse">
                        Loading categories…
                    </div>
                </template>

                <template x-if="!loadingCategories">
                    <div>
                        <select
                            name="category_final"
                            x-model="category"
                            class="w-full border border-red-300 dark:border-red-700 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                        >
                            <option value="">— Select a category —</option>
                            <template x-for="cat in categories" :key="cat.id">
                                <option
                                    :value="cat.name"
                                    x-text="cat.name"
                                    :selected="cat.name === category"
                                ></option>
                            </template>
                        </select>

                        <template x-if="categories.length === 0">
                            <p class="text-xs text-amber-600 mt-1">
                                No active expense categories found.
                                @can('financial_categories.manage')
                                    <a href="{{ route('admin.financial-categories.index') }}" class="underline font-semibold">Add categories here.</a>
                                @endcan
                            </p>
                        </template>
                    </div>
                </template>
            </div>

            {{-- Description --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Description <span class="text-red-500">*</span>
                </label>
                <input type="text" name="description" value="{{ old('description') }}" required
                       placeholder="e.g. Office supplies purchase"
                       class="w-full border border-red-300 dark:border-red-700 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
            </div>

            {{-- Amount --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Amount (₱) <span class="text-red-500">*</span>
                </label>
                <div class="flex">
                    <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-red-300 dark:border-red-700 bg-red-50 dark:bg-gray-700 text-red-500 text-sm select-none">₱</span>
                    <input
                        type="number"
                        name="amount"
                        value="{{ old('amount') }}"
                        required
                        min="0.01"
                        step="any"
                        placeholder="e.g. 1500"
                        class="flex-1 border border-red-300 dark:border-red-700 dark:bg-gray-700 dark:text-white rounded-r-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent"
                    >
                </div>
                <p class="text-xs text-gray-500 mt-1">Cash paid out — goes through audit → approval.</p>
            </div>

            {{-- Transaction Date --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Transaction Date <span class="text-red-500">*</span>
                </label>
                <input type="date" name="transaction_date"
                       value="{{ old('transaction_date', now()->format('Y-m-d')) }}"
                       max="{{ now()->format('Y-m-d') }}" required
                       class="w-full border border-red-300 dark:border-red-700 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">
            </div>

            {{-- Notes --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Notes (optional)</label>
                <textarea name="notes" rows="3" placeholder="Any additional notes..."
                          class="w-full border border-red-300 dark:border-red-700 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-transparent">{{ old('notes') }}</textarea>
            </div>

            {{-- Receipt Upload --}}
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Receipt / Attachment</label>
                <input type="file" name="receipt" accept=".jpg,.jpeg,.png,.pdf"
                       class="w-full border border-red-300 dark:border-red-700 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-red-500
                              file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-semibold
                              file:bg-red-50 file:text-red-700 hover:file:bg-red-600 hover:file:text-white
                              dark:file:bg-red-900/30 dark:file:text-red-300 dark:hover:file:bg-red-700">
                <p class="text-xs text-gray-500 mt-1">Allowed: JPG, PNG, PDF – Max 5MB</p>
            </div>

            {{-- Buttons --}}
            <div class="flex gap-3">
                <button type="submit" form="expense-form"
                        class="flex-1 bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg transition shadow-sm">
                    Save Expense
                </button>
                <a href="{{ route('financial.index') }}"
                   class="flex-1 text-center bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-semibold py-2 px-4 rounded-lg transition shadow-sm">
                    Cancel
                </a>
            </div>

        </form>
    </div>

</div>

@endsection

@push('scripts')
<script>
function expenseForm() {
    return {
        category:          @json(old('category_final', '')),
        categories:        [],
        loadingCategories: true,

        init() {
            fetch('{{ route('api.financial-categories.list') }}?type=expense')
                .then(r => r.json())
                .then(data => {
                    this.categories = data;
                    this.loadingCategories = false;
                })
                .catch(() => {
                    this.loadingCategories = false;
                });
        },
    };
}
</script>
@endpush
