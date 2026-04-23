@extends('layouts.app')

@section('title', 'Add ' . ucfirst($type))
@section('page-title', 'Add ' . ucfirst($type))

@section('content')

{{-- Define category arrays BEFORE they are used in x-data --}}
@php
    $incomeCategories = [
        'Membership & Contributions',
        'Fundraising Activities',
        'Sales & Services',
        'School / Institutional Support',
        'Others'
    ];
    $expenseCategories = [
        'Administrative Expenses',
        'Event Expenses',
        'Food & Hospitality',
        'Procurement / Materials',
        'Transportation & Logistics',
        'Marketing & Promotion',
        'Honorarium & Fees',
        'School-Related Payments',
        'Others'
    ];
@endphp

<div class="max-w-2xl mx-auto" x-data='{
    category: @json(old('category', '')),
    showOtherInput: @json(
        old('category') && !in_array(old('category'), $type === 'income' ? $incomeCategories : $expenseCategories)
    ),
    otherCategory: @json(
        old('category') && !in_array(old('category'), $type === 'income' ? $incomeCategories : $expenseCategories)
            ? old('category')
            : ''
    ),
    isReceivable: @json(old('is_receivable', false)),
    amount: @json(old('amount', '')),
    receivableTotal: @json(old('receivable_total', '')),
    updateCategory() {
        if (this.category === "Others") {
            this.showOtherInput = true;
        } else {
            this.showOtherInput = false;
            this.otherCategory = "";
        }
    }
}' x-init="updateCategory()">
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

        {{-- Type badge --}}
        <div class="mb-6">
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold
                {{ $type === 'income' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                {{ $type === 'income' ? '↑ Income' : '↓ Expense' }}
            </span>
        </div>

        <form method="POST"
              action="{{ $type === 'income' ? route('financial.income.store') : route('financial.expense.store') }}"
              enctype="multipart/form-data">
            @csrf

            {{-- ========== 1. CATEGORY ========== --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Category <span class="text-red-500">*</span>
                </label>

                @php
                    // Now just set the categories list – arrays already defined above
                    $categories = $type === 'income' ? $incomeCategories : $expenseCategories;
                @endphp

                <select name="category" x-model="category" @change="updateCategory()"
                        class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                    <option value="">Select a category</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat }}" {{ old('category') === $cat ? 'selected' : '' }}>
                            {{ $cat }}
                        </option>
                    @endforeach
                </select>

                <div x-show="showOtherInput" x-transition class="mt-3">
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1">Specify other category</label>
                    <input type="text" x-model="otherCategory" name="category_other"
                           placeholder="Enter custom category"
                           class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">This will be saved as the category.</p>
                </div>

                <input type="hidden" name="category_final" :value="category === 'Others' ? otherCategory : category">
            </div>

            {{-- ========== 2. DESCRIPTION ========== --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Description <span class="text-red-500">*</span>
                </label>
                <input type="text" name="description" value="{{ old('description') }}" required
                       placeholder="e.g., Membership fees collection"
                       class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
            </div>

            {{-- ========== 3. AMOUNT (conditional) ========== --}}
            @if($type === 'income')
                {{-- Normal amount field – hidden when receivable is checked --}}
                <div x-show="!isReceivable" class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Amount (₱) <span class="text-red-500">*</span>
                    </label>
                    <div class="flex">
                        <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gold-300 dark:border-gold-600 bg-gray-50 dark:bg-gray-700 text-gray-500 text-sm">₱</span>
                        <input type="number" name="amount" x-model="amount"
                               min="0.01" step="0.01" placeholder="0.00"
                               :required="!isReceivable"
                               class="flex-1 border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-r-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                    </div>
                </div>

                {{-- Receivable amount – shown only when receivable is checked --}}
                <div x-show="isReceivable" class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Receivable Amount (₱) <span class="text-red-500">*</span>
                    </label>
                    <div class="flex">
                        <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gold-300 dark:border-gold-600 bg-gray-50 dark:bg-gray-700 text-gray-500 text-sm">₱</span>
                        <input type="number" name="receivable_total" x-model="receivableTotal"
                               min="0.01" step="0.01" placeholder="0.00"
                               :required="isReceivable"
                               class="flex-1 border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-r-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                    </div>
                    <p class="text-xs text-gray-500 mt-1">The full amount owed (will be used as the income amount once paid).</p>
                </div>
            @else
                {{-- For expenses, always show normal amount --}}
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Amount (₱) <span class="text-red-500">*</span>
                    </label>
                    <div class="flex">
                        <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gold-300 dark:border-gold-600 bg-gray-50 dark:bg-gray-700 text-gray-500 text-sm">₱</span>
                        <input type="number" name="amount" value="{{ old('amount') }}" required
                               min="0.01" step="0.01" placeholder="0.00"
                               class="flex-1 border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-r-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                    </div>
                </div>
            @endif

            {{-- ========== 4. TRANSACTION DATE ========== --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Transaction Date <span class="text-red-500">*</span>
                </label>
                <input type="date" name="transaction_date"
                       value="{{ old('transaction_date', now()->format('Y-m-d')) }}"
                       max="{{ now()->format('Y-m-d') }}" required
                       class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
            </div>

            {{-- ========== 5. NOTES ========== --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Notes (optional)</label>
                <textarea name="notes" rows="3" placeholder="Additional details..."
                          class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">{{ old('notes') }}</textarea>
            </div>

            {{-- ========== 6. ACCOUNT RECEIVABLE (only for income) ========== --}}
            @if($type === 'income')
            <div class="mb-4 border-t pt-4">
                <label class="inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="is_receivable" value="1" x-model="isReceivable"
                           class="rounded border-gray-300 dark:border-gray-600 text-emerald-600 focus:ring-emerald-500">
                    <span class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                        This is an Account Receivable (money not yet received)
                    </span>
                </label>
            </div>

            {{-- Receivable details (shown when checkbox is checked) --}}
            <div x-show="isReceivable" x-transition class="bg-gray-50 dark:bg-gray-700/50 p-4 rounded-lg mb-4">
                <h3 class="font-semibold text-gray-800 dark:text-gray-200 mb-3">Receivable Details</h3>
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Customer/Payer Name (optional)</label>
                    <input type="text" name="customer_name" value="{{ old('customer_name') }}"
                           class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded-lg px-3 py-2 text-sm">
                </div>
                <div class="mb-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Due Date (optional)</label>
                    <input type="date" name="due_date" value="{{ old('due_date') }}"
                           class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-800 rounded-lg px-3 py-2 text-sm">
                </div>
            </div>
            @endif

            {{-- ========== 7. RECEIPT UPLOAD ========== --}}
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Receipt / Attachment</label>
                <input type="file" name="receipt" accept=".jpg,.jpeg,.png,.pdf"
                       class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Accepted: JPG, PNG, PDF — Max 5MB</p>
            </div>

            {{-- Buttons --}}
            <div class="flex gap-3">
                <button type="submit"
                        class="flex-1 {{ $type === 'income' ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-red-600 hover:bg-red-700' }} text-white font-semibold py-2 px-4 rounded-lg transition">
                    Save {{ ucfirst($type) }}
                </button>
                <a href="{{ route('financial.index') }}"
                   class="flex-1 text-center bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-semibold py-2 px-4 rounded-lg transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection