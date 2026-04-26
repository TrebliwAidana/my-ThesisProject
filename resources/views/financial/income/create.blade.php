@extends('layouts.app')

@section('title', 'Add Income')
@section('page-title', 'Add Income')

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

<div class="max-w-5xl mx-auto" x-data="incomeForm()">

    <div :class="hasReceivable ? 'grid grid-cols-1 lg:grid-cols-2 gap-6 items-start' : 'grid grid-cols-1 max-w-2xl mx-auto'">

        {{-- ═══════════════════════════════════════
             CARD 1 — Cash Income
        ═══════════════════════════════════════ --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gold-200 dark:border-gold-800 p-6">

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
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-emerald-100 text-emerald-700">↑ Income</span>
                <span class="text-xs text-gray-400 dark:text-gray-500">Cash received now</span>
            </div>

            <form method="POST" action="{{ route('financial.income.store') }}" enctype="multipart/form-data" id="income-form">
                @csrf

                {{-- Category --}}
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Category <span class="text-red-500">*</span>
                    </label>

                    <template x-if="loadingCategories">
                        <div class="w-full border border-gold-300 dark:border-gold-600 rounded-lg px-4 py-2 text-sm text-gray-400 bg-gray-50 dark:bg-gray-700 animate-pulse">
                            Loading categories…
                        </div>
                    </template>

                    <template x-if="!loadingCategories">
                        <div>
                            <select
                                name="category_final"
                                x-model="category"
                                class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-transparent"
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
                                    No active income categories found.
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
                           placeholder="e.g. Membership fee collection"
                           class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-transparent">
                </div>

                {{-- Cash Income Amount --}}
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Cash Income Amount (₱) <span class="text-red-500">*</span>
                    </label>
                    <div class="flex">
                        <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gold-300 dark:border-gold-600 bg-gray-50 dark:bg-gray-700 text-gray-500 text-sm select-none">₱</span>
                        <input
                            type="number"
                            name="amount"
                            x-model="cashAmount"
                            required
                            min="0.01"
                            step="any"
                            placeholder="e.g. 2000"
                            class="flex-1 border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-r-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-transparent"
                        >
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Actual cash received today — goes through audit → approval.</p>
                </div>

                {{-- Receivable Toggle --}}
                <div class="border-t border-gray-100 dark:border-gray-700 pt-4 mb-4">
                    <label class="inline-flex items-center cursor-pointer gap-2">
                        <input type="checkbox" x-model="hasReceivable" name="has_receivable" value="1"
                               class="rounded border-gray-300 dark:border-gray-600 text-emerald-600 focus:ring-emerald-500">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            Include a receivable (customer pays later)
                        </span>
                    </label>
                    <p class="text-xs text-gray-400 mt-1 ml-6">A separate receivable card will appear to fill in the details.</p>
                </div>

                {{-- Transaction Date --}}
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Transaction Date <span class="text-red-500">*</span>
                    </label>
                    <input type="date" name="transaction_date"
                           value="{{ old('transaction_date', now()->format('Y-m-d')) }}"
                           max="{{ now()->format('Y-m-d') }}" required
                           class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-transparent">
                </div>

                {{-- Notes --}}
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Notes (optional)</label>
                    <textarea name="notes" rows="3" placeholder="Any additional notes..."
                              class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-transparent">{{ old('notes') }}</textarea>
                </div>

                {{-- Receipt Upload --}}
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Receipt / Attachment</label>
                    <input type="file" name="receipt" accept=".jpg,.jpeg,.png,.pdf"
                           class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500
                                  file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-semibold
                                  file:bg-emerald-50 file:text-emerald-700 hover:file:bg-gold-500 hover:file:text-white
                                  dark:file:bg-emerald-900/30 dark:file:text-emerald-300 dark:hover:file:bg-gold-600">
                    <p class="text-xs text-gray-500 mt-1">Allowed: JPG, PNG, PDF – Max 5MB</p>
                </div>

                {{-- Transaction Summary --}}
                <div x-show="hasReceivable && cashAmount && receivableAmount"
                     x-transition
                     class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-lg p-4 mb-4">
                    <p class="text-sm font-semibold text-emerald-700 dark:text-emerald-400 mb-2">Total Summary</p>
                    <div class="space-y-1 text-sm text-gray-700 dark:text-gray-300">
                        <div class="flex justify-between">
                            <span>Cash received now:</span>
                            <span class="font-medium">₱<span x-text="formatAmount(cashAmount)"></span></span>
                        </div>
                        <div class="flex justify-between">
                            <span>Receivable (collect later):</span>
                            <span class="font-medium">₱<span x-text="formatAmount(receivableAmount)"></span></span>
                        </div>
                        <div class="flex justify-between border-t border-emerald-200 dark:border-emerald-700 pt-2 font-semibold text-emerald-700 dark:text-emerald-400">
                            <span>Total expected:</span>
                            <span>₱<span x-text="totalAmount"></span></span>
                        </div>
                    </div>
                </div>

                {{-- Buttons --}}
                <div class="flex gap-3">
                    <button type="submit" form="income-form"
                            class="flex-1 bg-emerald-600 hover:bg-gold-500 text-white font-semibold py-2 px-4 rounded-lg transition shadow-sm">
                        Save Income
                    </button>
                    <a href="{{ route('financial.index') }}"
                       class="flex-1 text-center bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg transition shadow-sm">
                        Cancel
                    </a>
                </div>

            </form>
        </div>
        {{-- end card 1 --}}

        {{-- ═══════════════════════════════════════
             CARD 2 — Receivable Details
        ═══════════════════════════════════════ --}}
        <div
            x-show="hasReceivable"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-x-4"
            x-transition:enter-end="opacity-100 translate-x-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-x-0"
            x-transition:leave-end="opacity-0 translate-x-4"
            class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-amber-200 dark:border-amber-800 p-6"
        >
            <div class="mb-6 flex items-center gap-2">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold bg-amber-100 text-amber-700">⏳ Receivable</span>
                <span class="text-xs text-gray-400 dark:text-gray-500">Customer pays later</span>
            </div>

            {{-- Receivable Amount --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Receivable Amount (₱) <span class="text-red-500">*</span>
                </label>
                <div class="flex">
                    <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-amber-300 dark:border-amber-600 bg-amber-50 dark:bg-gray-700 text-amber-600 text-sm select-none">₱</span>
                    <input
                        type="number"
                        name="receivable_amount"
                        x-model="receivableAmount"
                        :disabled="!hasReceivable"
                        min="0.01"
                        step="any"
                        placeholder="e.g. 500"
                        class="flex-1 border border-amber-300 dark:border-amber-600 dark:bg-gray-700 dark:text-white rounded-r-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent"
                    >
                </div>
                <p class="text-xs text-gray-500 mt-1">Amount the customer still owes — tracked separately until collected.</p>
            </div>

            {{-- Customer Name --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Customer / Payer Name <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    name="customer_name"
                    value="{{ old('customer_name') }}"
                    :disabled="!hasReceivable"
                    placeholder="Full name of the person who owes"
                    class="w-full border border-amber-300 dark:border-amber-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent"
                >
            </div>

            {{-- Due Date --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Due Date <span class="text-gray-400 text-xs font-normal">(optional)</span>
                </label>
                <input
                    type="date"
                    name="due_date"
                    value="{{ old('due_date') }}"
                    :disabled="!hasReceivable"
                    min="{{ now()->format('Y-m-d') }}"
                    class="w-full border border-amber-300 dark:border-amber-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-amber-400 focus:border-transparent"
                >
                <p class="text-xs text-gray-500 mt-1">When the customer is expected to settle the amount.</p>
            </div>

            <div class="mt-6 p-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg">
                <p class="text-xs text-amber-700 dark:text-amber-400 leading-relaxed">
                    <strong>How it works:</strong> This receivable is tracked in the Receivables module.
                    Once the customer pays, mark it as <em>Paid</em> — it will automatically be added
                    to your total income and saved to Documents.
                </p>
            </div>

        </div>
        {{-- end card 2 --}}

    </div>
    {{-- end grid --}}

</div>
{{-- end x-data wrapper --}}

@endsection

@push('scripts')
<script>
function incomeForm() {
    return {
        category:          @json(old('category_final', '')),
        categories:        [],
        loadingCategories: true,
        hasReceivable:     @json(old('has_receivable') && old('receivable_amount') ? true : false),
        receivableAmount:  @json(old('receivable_amount', '')),
        cashAmount:        @json(old('amount', '')),

        init() {
            fetch('{{ route('api.financial-categories.list') }}?type=income')
                .then(r => r.json())
                .then(data => {
                    this.categories = data;
                    this.loadingCategories = false;
                })
                .catch(() => {
                    this.loadingCategories = false;
                });
        },

        formatAmount(val) {
            const num = parseFloat(val);
            return isNaN(num) ? '0.00' : num.toFixed(2);
        },

        get totalAmount() {
            const cash = parseFloat(this.cashAmount) || 0;
            const rec  = parseFloat(this.receivableAmount) || 0;
            return (cash + rec).toFixed(2);
        }
    };
}
</script>
@endpush
