@extends('layouts.app')

@section('title', 'Add Receivable — VSULHS SSLG')
@section('page-title', 'Add Receivable')

@push('styles')
<style>
/* ════════════════════════════════════════════════
   ADD RECEIVABLE — Emerald & Gold Luxury Theme
   Matching all management views with purple accent
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
    background: rgba(139,92,246,0.08);
    color: #7c3aed;
    font-size: 0.85rem;
    font-weight: 600;
    white-space: nowrap;
}
html.dark .input-group-prepend {
    background: rgba(139,92,246,0.12);
    color: #a78bfa;
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
    background: rgba(139,92,246,0.08);
    color: #7c3aed;
    cursor: pointer;
    transition: all 0.15s ease;
}
.form-file::-webkit-file-upload-button:hover {
    background: rgba(139,92,246,0.15);
}
html.dark .form-file::-webkit-file-upload-button {
    background: rgba(139,92,246,0.12);
    color: #a78bfa;
}
html.dark .form-file::-webkit-file-upload-button:hover {
    background: rgba(139,92,246,0.2);
}

/* ── Type Badge ── */
.type-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.7rem;
    font-weight: 700;
    font-family: 'DM Mono', monospace;
    background: rgba(139,92,246,0.1);
    color: #7c3aed;
    border: 1px solid rgba(139,92,246,0.2);
}
html.dark .type-badge {
    background: rgba(139,92,246,0.15);
    color: #a78bfa;
}

/* ── Info Box ── */
.info-box {
    background: rgba(139,92,246,0.05);
    border: 1px solid rgba(139,92,246,0.15);
    border-radius: 0.75rem;
    padding: 0.75rem 1rem;
    margin-bottom: 1rem;
}
html.dark .info-box {
    background: rgba(139,92,246,0.08);
}
.info-box p {
    font-size: 0.7rem;
    color: var(--text-3);
}
.info-box strong {
    color: #7c3aed;
}
html.dark .info-box strong {
    color: #a78bfa;
}
.info-box svg {
    width: 1rem;
    height: 1rem;
    color: #7c3aed;
    flex-shrink: 0;
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
    box-shadow: 0 2px 10px rgba(5,150,105,0.22);
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
            <h1 class="text-2xl md:text-3xl font-bold text-white tracking-tight">Add Receivable</h1>
            <p class="text-emerald-100/80 text-sm mt-1.5 max-w-lg">Record amount to be collected from customers</p>
        </div>
    </div>

    {{-- Form Card --}}
    <div class="form-container anim-2" x-data="receivableForm()">
        <div class="form-card">
            <div class="form-card-header">
                <div class="form-card-header-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <h2>Add Receivable Transaction</h2>
                    <p>Record amount to be collected from a customer</p>
                </div>
            </div>

            <div class="p-6">

                {{-- Type Badge --}}
                <div class="mb-5 flex items-center gap-2">
                    <span class="type-badge">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        ⏳ Receivable
                    </span>
                    <span class="text-xs text-text-3">Amount to be collected from a customer</span>
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

                <form method="POST" action="{{ route('financial.receivable.store') }}" enctype="multipart/form-data" id="receivable-form">
                    @csrf

                    {{-- Category --}}
                    <div class="mb-5">
                        <label class="form-label">Category</label>

                        <template x-if="loadingCategories">
                            <div class="loading-placeholder">Loading categories...</div>
                        </template>

                        <template x-if="!loadingCategories">
                            <select name="category_final" x-model="category"
                                    class="form-select {{ $errors->has('category_final') ? 'error' : '' }}">
                                <option value="">— Select a category —</option>
                                <template x-for="cat in categories" :key="cat.id">
                                    <option :value="cat.name" x-text="cat.name" :selected="cat.name === category"></option>
                                </template>
                            </select>
                        </template>
                        @error('category_final') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Description --}}
                    <div class="mb-5">
                        <label class="form-label">Description <span class="form-label-required">*</span></label>
                        <input type="text" name="description" value="{{ old('description') }}" required
                               placeholder="e.g. Membership fee — John Doe"
                               class="form-input {{ $errors->has('description') ? 'error' : '' }}">
                        @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Customer Name --}}
                    <div class="mb-5">
                        <label class="form-label">Customer / Payer Name <span class="form-label-required">*</span></label>
                        <input type="text" name="customer_name" value="{{ old('customer_name') }}" required
                               placeholder="Full name of the person who owes"
                               class="form-input {{ $errors->has('customer_name') ? 'error' : '' }}">
                        @error('customer_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Amount --}}
                    <div class="mb-5">
                        <label class="form-label">Amount (PHP) <span class="form-label-required">*</span></label>
                        <div class="input-group">
                            <span class="input-group-prepend">₱</span>
                            <input type="number" name="amount" value="{{ old('amount') }}" required
                                   min="0.01" step="any" placeholder="0.00"
                                   class="form-input {{ $errors->has('amount') ? 'error' : '' }}">
                        </div>
                        <p class="form-hint">Amount the customer owes — will be added to income only after marked as paid.</p>
                        @error('amount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Transaction Date --}}
                    <div class="mb-5">
                        <label class="form-label">Date Recorded <span class="form-label-required">*</span></label>
                        <input type="date" name="transaction_date"
                               value="{{ old('transaction_date', now()->format('Y-m-d')) }}"
                               max="{{ now()->format('Y-m-d') }}" required
                               class="form-input {{ $errors->has('transaction_date') ? 'error' : '' }}">
                        @error('transaction_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Due Date --}}
                    <div class="mb-5">
                        <label class="form-label">Due Date <span class="font-normal text-text-3">(Optional)</span></label>
                        <input type="date" name="due_date" value="{{ old('due_date') }}"
                               min="{{ now()->format('Y-m-d') }}"
                               class="form-input {{ $errors->has('due_date') ? 'error' : '' }}">
                        <p class="form-hint">When the customer is expected to pay.</p>
                        @error('due_date') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Notes --}}
                    <div class="mb-5">
                        <label class="form-label">Notes <span class="font-normal text-text-3">(Optional)</span></label>
                        <textarea name="notes" rows="3" placeholder="Any additional notes..."
                                  class="form-textarea {{ $errors->has('notes') ? 'error' : '' }}">{{ old('notes') }}</textarea>
                        @error('notes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Receipt Upload --}}
                    <div class="mb-5">
                        <label class="form-label">Attachment <span class="font-normal text-text-3">(Optional)</span></label>
                        <input type="file" name="receipt" accept=".jpg,.jpeg,.png,.pdf"
                               class="form-file {{ $errors->has('receipt') ? 'error' : '' }}">
                        <p class="form-hint">Allowed: JPG, PNG, PDF - Max 5MB</p>
                        @error('receipt') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Info Box --}}
                    <div class="info-box">
                        <div class="flex items-start gap-2">
                            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <p>
                                <strong>Workflow:</strong> This receivable will go through 
                                <em>pending → audit → approved</em>. Once approved, use 
                                <em>Mark as Paid</em> on the detail page when the customer settles — 
                                only then will the amount be counted in Total Income.
                            </p>
                        </div>
                    </div>

                    {{-- Form Actions --}}
                    <div class="button-group">
                        <button type="submit"
                                x-data="{ busy: false }"
                                @click="if (busy) { $event.preventDefault(); $event.stopImmediatePropagation(); return; }"
                                @submit.window="if ($event.target === $el.closest('form')) { busy = true; }"
                                :disabled="busy"
                                class="btn-emerald">
                            <span x-show="!busy">
                                <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Create Receivable
                            </span>
                            <span x-show="busy" class="flex items-center gap-2">
                                <svg class="spinner" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                                </svg>
                                Creating...
                            </span>
                        </button>
                        <a href="{{ route('financial.index') }}" class="btn-red">
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