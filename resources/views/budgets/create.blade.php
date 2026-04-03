@extends('layouts.app')

@section('title', 'Create Budget Request — VSULHS_SSLG')
@section('page-title', 'Create Budget Request')

@section('content')
<div x-data="budgetForm()" x-init="init()" class="max-w-4xl mx-auto">
    {{-- Emerald Gradient Header --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-primary-600 to-primary-700 dark:from-primary-800 dark:to-primary-900 p-6 mb-6">
        <div class="relative z-10">
            <h1 class="text-2xl font-bold text-white">Create Budget Request</h1>
            <p class="text-primary-100 text-sm mt-1">Submit a new budget request for approval</p>
        </div>
        <div class="absolute top-0 right-0 -mt-8 -mr-8 w-64 h-64 bg-white/5 rounded-full blur-3xl"></div>
    </div>

    {{-- Estimated remaining budget card --}}
    @if($remainingBudget > 0)
    <div class="mb-6 p-4 bg-primary-50 dark:bg-primary-900/30 rounded-xl border border-primary-200 dark:border-primary-800">
        <div class="flex items-center gap-3">
            <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <p class="text-sm font-semibold text-primary-800 dark:text-primary-200">Estimated Remaining Budget ({{ now()->year }})</p>
                <p class="text-2xl font-bold text-primary-700 dark:text-primary-300">₱{{ number_format($remainingBudget, 2) }}</p>
                <p class="text-xs text-primary-600 dark:text-primary-400">Total approved so far: ₱{{ number_format($totalApproved, 2) }}</p>
            </div>
        </div>
    </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gold-200 dark:border-gold-800 overflow-hidden">
        <form method="POST" action="{{ route('budgets.store') }}" enctype="multipart/form-data" class="p-6 space-y-6" @submit="validateTotal">
            @csrf

            {{-- Copy from previous budget --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Copy from previous budget</label>
                <select x-model="copyId" @change="copyBudget" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm">
                    <option value="">-- Select a previous budget --</option>
                    @foreach($previousBudgets as $budget)
                        <option value="{{ $budget->id }}">{{ $budget->title }}</option>
                    @endforeach
                </select>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Select a budget to copy its details (title, description, dates, category, and line items). Attachments are not copied.</p>
            </div>

            {{-- Title --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Budget Title <span class="text-red-500">*</span></label>
                <input type="text" name="title" x-model="form.title" required
                       class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-transparent"
                       placeholder="e.g., Sports Festival Equipment">
            </div>

            {{-- Description with generate button --}}
            <div>
                <div class="flex justify-between items-center mb-2">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Description</label>
                    <button type="button" @click="generateDescription" class="text-xs text-primary-600 hover:text-primary-700">Generate description</button>
                </div>
                <textarea name="description" x-model="form.description" rows="4" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-transparent"
                          placeholder="Provide details about the budget request..."></textarea>
            </div>

            {{-- Date range --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Start Date</label>
                    <input type="date" name="start_date" x-model="form.start_date" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">End Date</label>
                    <input type="date" name="end_date" x-model="form.end_date" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm">
                </div>
            </div>

            {{-- Category (sorted by usage) --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Category <span class="text-red-500">*</span></label>
                <select name="category" x-model="form.category" required class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm">
                    <option value="">Select a category</option>
                    @foreach($sortedCategories as $category)
                        <option value="{{ $category->name }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Line items table --}}
            <div>
                <div class="flex justify-between items-center mb-2">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Line Items</label>
                    <button type="button" @click="addItem" class="text-xs bg-primary-600 hover:bg-gold-500 text-white px-3 py-1 rounded">+ Add Item</button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm border border-gray-200 dark:border-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-3 py-2 text-left">Description</th>
                                <th class="px-3 py-2 text-left">Quantity</th>
                                <th class="px-3 py-2 text-left">Unit Price (₱)</th>
                                <th class="px-3 py-2 text-left">Total (₱)</th>
                                <th class="px-3 py-2"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(item, index) in form.items" :key="index">
                                <tr>
                                    <td><input type="text" x-model="item.description" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded px-2 py-1 text-sm" required></td>
                                    <td><input type="number" x-model.number="item.quantity" @input="updateTotal" class="w-24 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded px-2 py-1 text-sm" min="1" required></td>
                                    <td><input type="number" x-model.number="item.unit_price" @input="updateTotal" class="w-32 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded px-2 py-1 text-sm" min="0" step="0.01" required></td>
                                    <td class="font-mono" x-text="formatMoney(item.quantity * item.unit_price)"></td>
                                    <td><button type="button" @click="removeItem(index)" class="text-red-500 hover:text-red-700">✖</button></td>
                                </tr>
                            </template>
                        </tbody>
                        <tfoot class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <td colspan="3" class="px-3 py-2 text-right font-semibold">Total Amount:</td>
                                <td class="px-3 py-2 font-mono font-bold" x-text="formatMoney(totalAmount)"></td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                <input type="hidden" name="items" :value="JSON.stringify(form.items)">
                <div x-show="totalAmount > 100000" class="mt-2 text-sm text-yellow-600 dark:text-yellow-400 flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    <span>High amount! Please double‑check the numbers.</span>
                </div>
                @error('items')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Drag & drop attachment with remove button --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Attachment <span class="text-gray-400">(Optional)</span></label>
                <div class="relative border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-4 text-center cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700/50 transition"
                     @dragover.prevent="dragover = true" @dragleave.prevent="dragover = false" @drop.prevent="handleDrop">
                    <input type="file" name="attachment" id="fileInput" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" @change="handleFileSelect">
                    <svg class="w-8 h-8 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Drag & drop or click to upload</p>
                    <div x-show="fileName" class="mt-2 flex items-center justify-center gap-2">
                        <span class="text-xs text-gray-600 dark:text-gray-300" x-text="fileName"></span>
                        <button type="button" @click="removeAttachment" class="text-red-500 hover:text-red-700 text-xs font-semibold">Remove</button>
                    </div>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Supported: PDF, DOC, DOCX, JPG, PNG (max 2MB)</p>
            </div>

            {{-- Buttons: Submit & Save as Draft --}}
            <div class="flex gap-3 pt-4 border-t border-gray-100 dark:border-gold-800">
                <button type="submit" class="flex-1 bg-primary-600 hover:bg-gold-500 text-white font-semibold py-2 px-4 rounded-lg transition shadow-sm hover:shadow-md">
                    Submit for Approval
                </button>
                <button type="submit" name="save_as_draft" value="1" class="flex-1 bg-gray-500 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded-lg transition">
                    Save as Draft
                </button>
                <a href="{{ route('budgets.index') }}" class="flex-1 text-center bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-semibold py-2 px-4 rounded-lg transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    function budgetForm() {
        return {
            copyId: '',
            form: {
                title: '',
                description: '',
                start_date: '',
                end_date: '',
                category: '',
                items: [{ description: '', quantity: 1, unit_price: 0 }],
            },
            fileName: '',
            totalAmount: 0,
            init() {
                this.updateTotal();
            },
            updateTotal() {
                let total = 0;
                this.form.items.forEach(item => {
                    total += (item.quantity || 0) * (item.unit_price || 0);
                });
                this.totalAmount = total;
            },
            addItem() {
                this.form.items.push({ description: '', quantity: 1, unit_price: 0 });
            },
            removeItem(index) {
                this.form.items.splice(index, 1);
                this.updateTotal();
            },
            formatMoney(value) {
                return '₱' + parseFloat(value).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            },
            generateDescription() {
                if (this.form.title) {
                    this.form.description = `Budget request for ${this.form.title} under the category ${this.form.category || 'selected category'}. This covers necessary expenses for the planned activities.`;
                } else {
                    this.form.description = 'Please provide a detailed description of the budget request.';
                }
            },
            copyBudget() {
                if (!this.copyId) return;
                fetch(`/budgets/copy-data/${this.copyId}`)
                    .then(res => {
                        if (!res.ok) throw new Error(`HTTP ${res.status} - ${res.statusText}`);
                        return res.json();
                    })
                    .then(data => {
                        console.log('Copy data received:', data);
                        // Force reactivity by creating new objects
                        this.form = {
                            title: data.title,
                            description: data.description,
                            start_date: data.start_date,
                            end_date: data.end_date,
                            category: data.category,
                            items: data.items.map(item => ({
                                description: item.description,
                                quantity: item.quantity,
                                unit_price: item.unit_price,
                            })),
                        };
                        this.updateTotal();
                    })
                    .catch(err => {
                        console.error('Copy error:', err);
                        alert('Failed to copy budget. Please try again.');
                    });
            },
            handleFileSelect(e) {
                const file = e.target.files[0];
                if (file) {
                    this.fileName = file.name;
                } else {
                    this.fileName = '';
                }
            },
            handleDrop(e) {
                e.preventDefault();
                this.dragover = false;
                const file = e.dataTransfer.files[0];
                if (file) {
                    const input = document.getElementById('fileInput');
                    const dt = new DataTransfer();
                    dt.items.add(file);
                    input.files = dt.files;
                    this.fileName = file.name;
                }
            },
            removeAttachment() {
                const input = document.getElementById('fileInput');
                input.value = '';
                this.fileName = '';
            },
            validateTotal(e) {
                if (this.totalAmount <= 0) {
                    e.preventDefault();
                    alert('Please add at least one line item with a valid amount.');
                    return false;
                }
                const remaining = {{ $remainingBudget ?? 0 }};
                if (remaining > 0 && this.totalAmount > remaining) {
                    if (!confirm(`Warning: This request (₱${this.formatMoney(this.totalAmount)}) exceeds the estimated remaining budget (₱${this.formatMoney(remaining)}). Continue anyway?`)) {
                        e.preventDefault();
                        return false;
                    }
                }
                const itemsInput = document.querySelector('input[name="items"]');
                if (itemsInput) itemsInput.value = JSON.stringify(this.form.items);
            }
        };
    }
</script>
@endsection