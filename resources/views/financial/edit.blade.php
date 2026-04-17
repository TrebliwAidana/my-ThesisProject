@extends('layouts.app')

@section('title', 'Edit Transaction')
@section('page-title', 'Edit Transaction')

@section('content')
<div class="max-w-2xl mx-auto">
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
                {{ $transaction->type === 'income' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                {{ $transaction->type === 'income' ? '↑ Income' : '↓ Expense' }}
            </span>
            <span class="ml-2 text-xs text-amber-600 font-medium">Pending — editable</span>
        </div>

        <form method="POST" action="{{ route('financial.update', $transaction->id) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            {{-- Description --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Description <span class="text-red-500">*</span>
                </label>
                <input type="text" name="description"
                       value="{{ old('description', $transaction->description) }}" required
                       class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
            </div>

            {{-- Amount --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Amount (₱) <span class="text-red-500">*</span>
                </label>
                <div class="flex">
                    <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gold-300 dark:border-gold-600 bg-gray-50 dark:bg-gray-700 text-gray-500 text-sm">₱</span>
                    <input type="number" name="amount"
                           value="{{ old('amount', $transaction->amount) }}" required
                           min="0.01" step="0.01"
                           class="flex-1 border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-r-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                </div>
            </div>

            {{-- Category --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Category</label>
                <input type="text" name="category"
                       value="{{ old('category', $transaction->category) }}"
                       class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
            </div>

            {{-- Transaction Date --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Transaction Date <span class="text-red-500">*</span>
                </label>
                <input type="date" name="transaction_date"
                       value="{{ old('transaction_date', $transaction->transaction_date->format('Y-m-d')) }}"
                       max="{{ now()->format('Y-m-d') }}" required
                       class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
            </div>

            {{-- Notes --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Notes</label>
                <textarea name="notes" rows="3"
                          class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">{{ old('notes', $transaction->notes) }}</textarea>
            </div>

            {{-- Receipt --}}
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Receipt / Attachment</label>

                @if($transaction->receipt_path)
                    <div class="mb-2 flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <span>📎 Current file:</span>
                        <a href="{{ Storage::url($transaction->receipt_path) }}" target="_blank"
                           class="text-blue-600 hover:underline">View receipt</a>
                        <span class="text-xs text-gray-400">(Upload a new file to replace it)</span>
                    </div>
                @endif

                <input type="file" name="receipt" accept=".jpg,.jpeg,.png,.pdf"
                       class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Accepted: JPG, PNG, PDF — Max 5MB</p>
            </div>

            {{-- Buttons --}}
            <div class="flex gap-3">
                <button type="submit"
                        class="flex-1 bg-primary-600 hover:bg-gold-500 text-white font-semibold py-2 px-4 rounded-lg transition">
                    Update Transaction
                </button>
                <a href="{{ route('financial.show', $transaction->id) }}"
                   class="flex-1 text-center bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-semibold py-2 px-4 rounded-lg transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection