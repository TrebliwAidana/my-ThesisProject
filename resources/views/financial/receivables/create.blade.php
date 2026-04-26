@extends('layouts.app')

@section('title', 'Add Receivable')
@section('page-title', 'Add Receivable')

@section('content')

<style>
    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button { -webkit-appearance: none; margin: 0; }
    input[type=number] { -moz-appearance: textfield; appearance: textfield; }
</style>

<div class="max-w-2xl mx-auto" x-data="receivableForm()">

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-purple-200 dark:border-purple-800 p-6">

        @if($errors->any())
            <div class="mb-4 p-3 bg-red-100 border border-red-300 text-red-700 rounded-lg text-sm">
                <strong>Please fix the following errors:</strong>
                <ul class="mt-2 list-disc list-inside">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        <div class="mb-6 flex items-center gap-2">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-purple-100 text-purple-700">
                ⏳ Receivable
            </span>
            <span class="text-xs text-gray-400 dark:text-gray-500">Amount to be collected from a customer</span>
        </div>

        <form method="POST" action="{{ route('financial.receivable.store') }}" enctype="multipart/form-data" id="receivable-form">
            @csrf

            {{-- Category --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Category
                </label>
                <template x-if="loadingCategories">
                    <div class="w-full border border-purple-300 dark:border-purple-700 rounded-lg px-4 py-2 text-sm text-gray-400 bg-gray-50 dark:bg-gray-700 animate-pulse">
                        Loading categories…
                    </div>
                </template>
                <template x-if="!loadingCategories">
                    <select name="category_final" x-model="category"
                            class="w-full border border-purple-300 dark:border-purple-700 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <option value="">— Select a category —</option>
                        <template x-for="cat in categories" :key="cat.id">
                            <option :value="cat.name" x-text="cat.name" :selected="cat.name === category"></option>
                        </template>
                    </select>
                </template>
            </div>

            {{-- Description --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Description <span class="text-red-500">*</span>
                </label>
                <input type="text" name="description" value="{{ old('description') }}" required
                       placeholder="e.g. Membership fee — John Doe"
                       class="w-full border border-purple-300 dark:border-purple-700 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
            </div>

            {{-- Customer Name --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Customer / Payer Name <span class="text-red-500">*</span>
                </label>
                <input type="text" name="customer_name" value="{{ old('customer_name') }}" required
                       placeholder="Full name of the person who owes"
                       class="w-full border border-purple-300 dark:border-purple-700 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
            </div>

            {{-- Amount --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Amount (₱) <span class="text-red-500">*</span>
                </label>
                <div class="flex">
                    <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-purple-300 dark:border-purple-700 bg-purple-50 dark:bg-gray-700 text-purple-500 text-sm select-none">₱</span>
                    <input type="number" name="amount" value="{{ old('amount') }}" required
                           min="0.01" step="any" placeholder="e.g. 500"
                           class="flex-1 border border-purple-300 dark:border-purple-700 dark:bg-gray-700 dark:text-white rounded-r-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
                </div>
                <p class="text-xs text-gray-500 mt-1">Amount the customer owes — will be added to income only after marked as paid.</p>
            </div>

            {{-- Transaction Date --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Date Recorded <span class="text-red-500">*</span>
                </label>
                <input type="date" name="transaction_date"
                       value="{{ old('transaction_date', now()->format('Y-m-d')) }}"
                       max="{{ now()->format('Y-m-d') }}" required
                       class="w-full border border-purple-300 dark:border-purple-700 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
            </div>

            {{-- Due Date --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Due Date <span class="text-gray-400 text-xs font-normal">(optional)</span>
                </label>
                <input type="date" name="due_date" value="{{ old('due_date') }}"
                       min="{{ now()->format('Y-m-d') }}"
                       class="w-full border border-purple-300 dark:border-purple-700 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">
                <p class="text-xs text-gray-500 mt-1">When the customer is expected to pay.</p>
            </div>

            {{-- Notes --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Notes (optional)</label>
                <textarea name="notes" rows="3" placeholder="Any additional notes..."
                          class="w-full border border-purple-300 dark:border-purple-700 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500">{{ old('notes') }}</textarea>
            </div>

            {{-- Receipt Upload --}}
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Attachment (optional)</label>
                <input type="file" name="receipt" accept=".jpg,.jpeg,.png,.pdf"
                       class="w-full border border-purple-300 dark:border-purple-700 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-purple-500
                              file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-semibold
                              file:bg-purple-50 file:text-purple-700 hover:file:bg-purple-600 hover:file:text-white
                              dark:file:bg-purple-900/30 dark:file:text-purple-300">
                <p class="text-xs text-gray-500 mt-1">Allowed: JPG, PNG, PDF – Max 5MB</p>
            </div>

            {{-- Info box --}}
            <div class="mb-6 p-3 bg-purple-50 dark:bg-purple-900/20 border border-purple-200 dark:border-purple-800 rounded-lg">
                <p class="text-xs text-purple-700 dark:text-purple-400 leading-relaxed">
                    <strong>Workflow:</strong> This receivable will go through <em>pending → audit → approved</em>.
                    Once approved, use <em>Mark as Paid</em> on the detail page when the customer settles —
                    only then will the amount be counted in Total Income.
                </p>
            </div>

            {{-- Buttons --}}
            <div class="flex gap-3">
                <button type="submit"
                        class="flex-1 bg-purple-600 hover:bg-purple-700 text-white font-semibold py-2 px-4 rounded-lg transition shadow-sm">
                    Save Receivable
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
function receivableForm() {
    return {
        category: @json(old('category_final', '')),
        categories: [],
        loadingCategories: true,

        init() {
            fetch('{{ route('api.financial-categories.list') }}?type=receivable')
                .then(r => r.json())
                .then(data => { this.categories = data; this.loadingCategories = false; })
                .catch(() => { this.loadingCategories = false; });
        },
    };
}
</script>
@endpush