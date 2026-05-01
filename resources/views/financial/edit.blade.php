@extends('layouts.app')

@section('title', 'Edit Transaction — VSULHS SSLG')
@section('page-title', 'Edit Transaction')

@push('styles')
<style>
/* ════════════════════════════════════════════════
   EDIT TRANSACTION — Emerald & Gold Luxury Theme
   Matching all management views
════════════════════════════════════════════════ */

.form-container {
    max-width: 42rem;
    margin: 0 auto;
}

/* ── Form Card ── */
.form-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 1.25rem;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.05);
}
html.dark .form-card {
    box-shadow: 0 4px 20px rgba(0,0,0,0.22);
}

.form-card-header {
    background: linear-gradient(135deg, #064E3B 0%, #047857 60%, #065F46 100%);
    padding: 1rem 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.form-card-header-icon {
    width: 2.5rem;
    height: 2.5rem;
    background: rgba(255,255,255,0.15);
    border-radius: 0.75rem;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.form-card-header-icon svg {
    width: 1.25rem;
    height: 1.25rem;
    stroke: #fff;
}
.form-card-header h2 {
    font-size: 0.9rem;
    font-weight: 700;
    color: #fff;
    margin-bottom: 0.125rem;
}
.form-card-header p {
    font-size: 0.7rem;
    color: rgba(255,255,255,0.7);
}

/* ── Form Fields ── */
.form-label {
    display: block;
    font-size: 0.75rem;
    font-weight: 700;
    color: var(--text-2);
    margin-bottom: 0.5rem;
}
.form-label-required {
    color: #ef4444;
}
.form-input {
    width: 100%;
    padding: 0.625rem 1rem;
    font-size: 0.85rem;
    background: var(--surface-2);
    border: 1.5px solid var(--border);
    border-radius: 0.75rem;
    color: var(--text);
    font-family: 'Outfit', sans-serif;
    transition: all 0.2s ease;
}
.form-input:focus {
    outline: none;
    border-color: var(--gold);
    box-shadow: 0 0 0 3px rgba(212,175,55,0.12);
    background: var(--surface);
}
.form-input.error {
    border-color: #ef4444;
}
.form-textarea {
    width: 100%;
    padding: 0.625rem 1rem;
    font-size: 0.85rem;
    background: var(--surface-2);
    border: 1.5px solid var(--border);
    border-radius: 0.75rem;
    color: var(--text);
    font-family: 'Outfit', sans-serif;
    transition: all 0.2s ease;
    resize: vertical;
}
.form-textarea:focus {
    outline: none;
    border-color: var(--gold);
    box-shadow: 0 0 0 3px rgba(212,175,55,0.12);
    background: var(--surface);
}
.form-select {
    width: 100%;
    padding: 0.625rem 1rem;
    font-size: 0.85rem;
    background: var(--surface-2);
    border: 1.5px solid var(--border);
    border-radius: 0.75rem;
    color: var(--text);
    font-family: 'Outfit', sans-serif;
    cursor: pointer;
    transition: all 0.2s ease;
}
.form-select:focus {
    outline: none;
    border-color: var(--gold);
    box-shadow: 0 0 0 3px rgba(212,175,55,0.12);
}

/* ── Input Group (Amount with Currency Symbol) ── */
.input-group {
    display: flex;
}
.input-group-prepend {
    display: inline-flex;
    align-items: center;
    padding: 0 1rem;
    border: 1.5px solid var(--border);
    border-right: none;
    border-radius: 0.75rem 0 0 0.75rem;
    background: var(--surface-3);
    color: var(--text-3);
    font-size: 0.85rem;
    font-weight: 600;
    white-space: nowrap;
}
.input-group .form-input {
    border-radius: 0 0.75rem 0.75rem 0;
}

/* ── File Input Styling ── */
.form-file {
    width: 100%;
    padding: 0.5rem;
    font-size: 0.85rem;
    background: var(--surface-2);
    border: 1.5px solid var(--border);
    border-radius: 0.75rem;
    color: var(--text);
    font-family: 'Outfit', sans-serif;
    cursor: pointer;
}
.form-file::-webkit-file-upload-button {
    margin-right: 0.75rem;
    padding: 0.375rem 0.875rem;
    border-radius: 0.5rem;
    border: none;
    font-size: 0.75rem;
    font-weight: 600;
    background: rgba(5,150,105,0.1);
    color: #047857;
    cursor: pointer;
    transition: background 0.15s ease;
}
.form-file::-webkit-file-upload-button:hover {
    background: rgba(5,150,105,0.2);
}
html.dark .form-file::-webkit-file-upload-button {
    background: rgba(16,185,129,0.15);
    color: #6ee7b7;
}

/* ── Type Badge Styles ── */
.type-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.7rem;
    font-weight: 700;
    font-family: 'DM Mono', monospace;
}
.type-badge-income {
    background: rgba(5,150,105,0.1);
    color: #047857;
    border: 1px solid rgba(5,150,105,0.2);
}
.type-badge-expense {
    background: rgba(220,38,38,0.08);
    color: #dc2626;
    border: 1px solid rgba(220,38,38,0.18);
}
.pending-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.65rem;
    font-weight: 700;
    background: rgba(245,158,11,0.1);
    color: #d97706;
    border: 1px solid rgba(245,158,11,0.2);
}
html.dark .pending-badge {
    background: rgba(245,158,11,0.15);
    color: #fcd34d;
}
html.dark .type-badge-income {
    background: rgba(16,185,129,0.15);
    color: #6ee7b7;
}
html.dark .type-badge-expense {
    background: rgba(248,113,113,0.12);
    color: #fca5a5;
}

/* ── Error Alert ── */
.error-alert {
    background: rgba(220,38,38,0.08);
    border-left: 3px solid #ef4444;
    border-radius: 0.75rem;
    padding: 1rem;
    margin-bottom: 1rem;
}
.error-title {
    font-size: 0.75rem;
    font-weight: 700;
    color: #ef4444;
    margin-bottom: 0.5rem;
}
.error-list {
    list-style: disc;
    list-style-position: inside;
    font-size: 0.7rem;
    color: #dc2626;
}
html.dark .error-alert {
    background: rgba(248,113,113,0.12);
}
html.dark .error-list {
    color: #fca5a5;
}

/* ── Current File Display ── */
.current-file {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 0.75rem;
    background: var(--surface-2);
    border-radius: 0.5rem;
    margin-bottom: 0.75rem;
}
.current-file span {
    font-size: 0.7rem;
    color: var(--text-3);
}
.current-file a {
    font-size: 0.7rem;
    font-weight: 600;
    color: var(--emerald);
    text-decoration: none;
}
.current-file a:hover {
    color: var(--gold-dark);
}

/* ── Loading State ── */
.loading-placeholder {
    width: 100%;
    padding: 0.625rem 1rem;
    border: 1.5px solid var(--border);
    border-radius: 0.75rem;
    font-size: 0.85rem;
    color: var(--text-3);
    background: var(--surface-3);
    animation: pulse 1.5s ease-in-out infinite;
}
@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.5; }
}

/* ── Buttons ── */
.btn-emerald {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.6rem 1.5rem;
    font-size: 0.85rem;
    font-weight: 700;
    background: linear-gradient(135deg, var(--emerald), var(--emerald-dark));
    color: #fff;
    border: none;
    border-radius: 0.75rem;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
    font-family: 'Outfit', sans-serif;
    flex: 1;
}
.btn-emerald:hover:not(:disabled) {
    background: linear-gradient(135deg, var(--gold), var(--gold-dark));
    color: #0f172a;
    transform: translateY(-1px);
    box-shadow: 0 4px 16px rgba(212,175,55,0.35);
}
.btn-emerald:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.btn-red {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.6rem 1.5rem;
    font-size: 0.85rem;
    font-weight: 700;
    background: linear-gradient(135deg, #dc2626, #b91c1c);
    color: #fff;
    border: none;
    border-radius: 0.75rem;
    cursor: pointer;
    transition: all 0.2s ease;
    text-decoration: none;
    font-family: 'Outfit', sans-serif;
    box-shadow: 0 2px 10px rgba(220,38,38,0.22);
}
.btn-red:hover {
    background: linear-gradient(135deg, #ef4444, #dc2626);
    transform: translateY(-1px);
    box-shadow: 0 4px 16px rgba(220,38,38,0.35);
}

.button-group {
    display: flex;
    justify-content: space-between;
    gap: 1rem;
    margin-top: 0.5rem;
}

/* ── Form Hint ── */
.form-hint {
    font-size: 0.65rem;
    color: var(--text-3);
    margin-top: 0.25rem;
}

/* ── Animations ── */
@keyframes fadeUp {
    from { opacity: 0; transform: translateY(14px); }
    to   { opacity: 1; transform: translateY(0); }
}
.anim-1 { animation: fadeUp 0.38s ease 0.04s both; }
.anim-2 { animation: fadeUp 0.38s ease 0.10s both; }

.spinner {
    width: 1rem;
    height: 1rem;
    animation: spin 0.8s linear infinite;
}
@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Remove number input spinners */
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
@endpush

@section('content')

<div class="space-y-5">
    
    {{-- Hero Section --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-emerald-600 to-emerald-700 dark:from-emerald-800 dark:to-emerald-900 p-6 md:p-7">
        <div class="absolute inset-0 opacity-[0.05]"
             style="background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,1) 1px, transparent 0); background-size: 28px 28px;"></div>
        <div class="absolute -top-16 right-0 w-64 h-64 rounded-full opacity-20"
             style="background: radial-gradient(circle, #D4AF37, transparent 65%); filter: blur(48px);"></div>
        <div class="relative z-10">
            <p class="text-emerald-300 text-[10px] font-bold tracking-[0.2em] uppercase mb-2"
               style="font-family:'DM Mono',monospace;">
                {{ now()->format('F Y') }} · Financial Management
            </p>
            <h1 class="text-2xl md:text-3xl font-bold text-white tracking-tight">Edit Transaction</h1>
            <p class="text-emerald-100/80 text-sm mt-1.5 max-w-lg">Update transaction details for pending records</p>
        </div>
    </div>

    {{-- Form Card --}}
    <div class="form-container anim-2">
        <div class="form-card">
            <div class="form-card-header">
                <div class="form-card-header-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
                <div>
                    <h2>Edit Transaction</h2>
                    <p>Update transaction details</p>
                </div>
            </div>

            <div class="p-6">

                {{-- Type Badges --}}
                <div class="mb-5 flex items-center gap-2">
                    <span class="type-badge {{ $transaction->type === 'income' ? 'type-badge-income' : 'type-badge-expense' }}">
                        @if($transaction->type === 'income')
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"/>
                            </svg>
                            ↑ Income
                        @else
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"/>
                            </svg>
                            ↓ Expense
                        @endif
                    </span>
                    <span class="pending-badge">
                        <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Pending — editable
                    </span>
                </div>

                @if($errors->any())
                    <div class="error-alert">
                        <div class="error-title">Please fix the following errors:</div>
                        <ul class="error-list">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST"
                      action="{{ route('financial.update', $transaction->id) }}"
                      enctype="multipart/form-data"
                      x-data="{ submitting: false }"
                      @submit="submitting = true">
                    @csrf
                    @method('PUT')

                    {{-- Description --}}
                    <div class="mb-5">
                        <label class="form-label">Description <span class="form-label-required">*</span></label>
                        <input type="text" name="description"
                               value="{{ old('description', $transaction->description) }}" required
                               class="form-input {{ $errors->has('description') ? 'error' : '' }}">
                        @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Amount --}}
                    <div class="mb-5">
                        <label class="form-label">Amount (PHP) <span class="form-label-required">*</span></label>
                        <div class="input-group">
                            <span class="input-group-prepend">₱</span>
                            <input type="number" name="amount"
                                   value="{{ old('amount', $transaction->amount) }}" required
                                   min="0.01" step="0.01"
                                   class="form-input {{ $errors->has('amount') ? 'error' : '' }}">
                        </div>
                        @error('amount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Category Dropdown --}}
                    <div class="mb-5"
                         x-data="{
                             category: @json(old('category_final', $transaction->category ?? '')),
                             categories: [],
                             loading: true,
                             init() {
                                 fetch('{{ route('api.financial-categories.list') }}?type={{ $transaction->type }}')
                                     .then(r => r.json())
                                     .then(data => { this.categories = data; this.loading = false; })
                                     .catch(() => { this.loading = false; });
                             }
                         }"
                         x-init="init()">
                        <label class="form-label">Category</label>

                        <template x-if="loading">
                            <div class="loading-placeholder">Loading categories...</div>
                        </template>

                        <template x-if="!loading">
                            <div>
                                <select name="category_final"
                                        x-model="category"
                                        class="form-select {{ $errors->has('category_final') ? 'error' : '' }}">
                                    <option value="">— No category —</option>
                                    <template x-for="cat in categories" :key="cat.id">
                                        <option :value="cat.name" x-text="cat.name" :selected="cat.name === category"></option>
                                    </template>
                                    {{-- Graceful fallback: if saved category no longer exists in DB --}}
                                    <template x-if="category && !categories.find(c => c.name === category)">
                                        <option :value="category" x-text="category + ' (archived)'" selected></option>
                                    </template>
                                </select>

                                <template x-if="categories.length === 0">
                                    <p class="form-hint text-amber-600 dark:text-amber-400 mt-1">
                                        No active categories found for this transaction type.
                                        @can('financial_categories.manage')
                                            <a href="{{ route('admin.financial-categories.index') }}" class="underline font-semibold">Manage categories.</a>
                                        @endcan
                                    </p>
                                </template>
                            </div>
                        </template>
                        @error('category_final') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Transaction Date --}}
                    <div class="mb-5">
                        <label class="form-label">Transaction Date <span class="form-label-required">*</span></label>
                        <input type="date" name="transaction_date"
                               value="{{ old('transaction_date', $transaction->transaction_date->format('Y-m-d')) }}"
                               max="{{ now()->format('Y-m-d') }}" required
                               class="form-input {{ $errors->has('transaction_date') ? 'error' : '' }}">
                        @error('transaction_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Notes --}}
                    <div class="mb-5">
                        <label class="form-label">Notes <span class="font-normal text-text-3">(Optional)</span></label>
                        <textarea name="notes" rows="3"
                                  class="form-textarea {{ $errors->has('notes') ? 'error' : '' }}">{{ old('notes', $transaction->notes) }}</textarea>
                        @error('notes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Receipt / Attachment --}}
                    <div class="mb-5">
                        <label class="form-label">Receipt / Attachment</label>

                        @if($transaction->documents->isNotEmpty())
                            @php $doc = $transaction->documents->first(); @endphp
                            @if($doc && $doc->currentVersion)
                                <div class="current-file">
                                    <span>📎 Current file:</span>
                                    <a href="{{ route('documents.download', $doc) }}" target="_blank">
                                        {{ $doc->currentVersion->file_name }}
                                    </a>
                                    <span class="text-xs text-text-3">(Upload a new file to replace it)</span>
                                </div>
                            @endif
                        @endif

                        <input type="file" name="receipt" accept=".jpg,.jpeg,.png,.pdf"
                               class="form-file {{ $errors->has('receipt') ? 'error' : '' }}">
                        <p class="form-hint">Accepted: JPG, PNG, PDF — Max 5MB</p>
                        @error('receipt') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Buttons --}}
                    <div class="button-group">
                        <button type="submit"
                                :disabled="submitting"
                                :class="submitting ? 'opacity-60 cursor-not-allowed' : ''"
                                class="btn-emerald">
                            <svg x-show="submitting" class="spinner" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                            </svg>
                            <span x-text="submitting ? 'Saving...' : 'Update Transaction'"></span>
                        </button>
                        <a href="{{ route('financial.show', $transaction->id) }}" class="btn-red">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection