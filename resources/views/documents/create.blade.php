@extends('layouts.app')

@section('title', 'Upload Document — VSULHS SSLG')
@section('page-title', 'Upload Document')

@push('styles')
<style>
/* ════════════════════════════════════════════════
   UPLOAD DOCUMENT — Emerald & Gold Luxury Theme
   Matching all management views
════════════════════════════════════════════════ */

.form-container {
    max-width: 56rem;
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

/* ── Error Alert ── */
.error-alert {
    background: rgba(220,38,38,0.08);
    border-left: 3px solid #ef4444;
    border-radius: 0.75rem;
    padding: 1rem;
    margin-bottom: 1rem;
}
.error-list {
    list-style: disc;
    list-style-position: inside;
    font-size: 0.75rem;
    color: #dc2626;
}
html.dark .error-alert {
    background: rgba(248,113,113,0.12);
}
html.dark .error-list {
    color: #fca5a5;
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
.form-hint code {
    background: var(--surface-3);
    padding: 0.125rem 0.375rem;
    border-radius: 0.25rem;
    font-family: 'DM Mono', monospace;
    font-size: 0.6rem;
}

/* ── Grid Layout ── */
.form-grid {
    display: grid;
    grid-template-columns: 1fr;
    gap: 1rem;
}
@media (min-width: 768px) {
    .form-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
    }
}

/* ── Loading Spinner ── */
@keyframes spin {
    to { transform: rotate(360deg); }
}
.spinner {
    width: 1rem;
    height: 1rem;
    animation: spin 0.8s linear infinite;
}

/* ── Animations ── */
@keyframes fadeUp {
    from { opacity: 0; transform: translateY(14px); }
    to   { opacity: 1; transform: translateY(0); }
}
.anim-1 { animation: fadeUp 0.38s ease 0.04s both; }
.anim-2 { animation: fadeUp 0.38s ease 0.10s both; }
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
                {{ now()->format('F Y') }} · Document Management
            </p>
            <h1 class="text-2xl md:text-3xl font-bold text-white tracking-tight">Upload Document</h1>
            <p class="text-emerald-100/80 text-sm mt-1.5 max-w-lg">Add a new document to the library</p>
        </div>
    </div>

    {{-- Form Card --}}
    <div class="form-container anim-2">
        <div class="form-card">
            <div class="form-card-header">
                <div class="flex items-center gap-3">
                    <div class="form-card-header-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h2>Upload New Document</h2>
                        <p>Add a document to the library</p>
                    </div>
                </div>
            </div>

            <div class="p-6">
                {{-- Error Alerts --}}
                @if($errors->any())
                    <div class="error-alert">
                        <ul class="error-list">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('documents.store') }}" enctype="multipart/form-data">
                    @csrf

                    {{-- Title --}}
                    <div class="mb-5">
                        <label class="form-label">Title <span class="form-label-required">*</span></label>
                        <input type="text" name="title" value="{{ old('title') }}" required
                               class="form-input {{ $errors->has('title') ? 'error' : '' }}">
                        @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Description --}}
                    <div class="mb-5">
                        <label class="form-label">Description</label>
                        <textarea name="description" rows="3"
                                  class="form-textarea {{ $errors->has('description') ? 'error' : '' }}">{{ old('description') }}</textarea>
                        @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Category and Tags Grid --}}
                    <div class="form-grid mb-5">
                        <div>
                            <label class="form-label">Category</label>
                            <select name="document_category_id" 
                                    class="form-select {{ $errors->has('document_category_id') ? 'error' : '' }}">
                                <option value="">— No category —</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}"
                                        {{ old('document_category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            <p class="form-hint">Categories are managed in the admin panel.</p>
                            @error('document_category_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        
                        <div>
                            <label class="form-label">Tags <span class="font-normal text-text-3">(Optional, comma-separated)</span></label>
                            <input type="text" name="tags" value="{{ old('tags') }}"
                                   placeholder="e.g., report, minutes, 2025"
                                   class="form-input {{ $errors->has('tags') ? 'error' : '' }}">
                            <p class="form-hint">Example: <code>financial, annual, draft</code></p>
                            @error('tags') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Change Notes --}}
                    <div class="mb-5">
                        <label class="form-label">Change Notes <span class="font-normal text-text-3">(Optional)</span></label>
                        <textarea name="change_notes" rows="2"
                                  class="form-textarea {{ $errors->has('change_notes') ? 'error' : '' }}"
                                  placeholder="Describes this version (e.g., 'First draft', 'Approved version')">{{ old('change_notes') }}</textarea>
                        @error('change_notes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- File Upload --}}
                    <div class="mb-5">
                        <label class="form-label">File <span class="form-label-required">*</span></label>
                        <input type="file" name="file" required
                               accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.gif,.zip"
                               class="form-file {{ $errors->has('file') ? 'error' : '' }}">
                        <p class="form-hint">Max 10 MB. Allowed: PDF, Word, Excel, PowerPoint, images, ZIP.</p>
                        @error('file') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                                </svg>
                                Upload Document
                            </span>
                            <span x-show="busy" class="flex items-center gap-2">
                                <svg class="spinner" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                                </svg>
                                Uploading...
                            </span>
                        </button>
                        <a href="{{ route('documents.index') }}" class="btn-red">
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