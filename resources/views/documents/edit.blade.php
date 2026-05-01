@extends('layouts.app')

@section('title', 'Edit Document — VSULHS SSLG')
@section('page-title', 'Edit Document')

@push('styles')
<style>
/* ════════════════════════════════════════════════
   EDIT DOCUMENT — Emerald & Gold Luxury Theme
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

/* ── Info Box ── */
.info-box {
    background: rgba(5,150,105,0.05);
    border: 1px solid rgba(5,150,105,0.15);
    border-radius: 0.75rem;
    padding: 0.75rem 1rem;
    margin-bottom: 1rem;
}
html.dark .info-box {
    background: rgba(16,185,129,0.08);
}
.info-box p {
    font-size: 0.7rem;
    color: var(--text-3);
}
.info-box svg {
    width: 1rem;
    height: 1rem;
    color: var(--emerald);
    flex-shrink: 0;
}
.info-highlight {
    font-weight: 600;
    color: var(--emerald-dark);
}
html.dark .info-highlight {
    color: var(--emerald-light);
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
.btn-emerald:hover {
    background: linear-gradient(135deg, var(--gold), var(--gold-dark));
    color: #0f172a;
    transform: translateY(-1px);
    box-shadow: 0 4px 16px rgba(212,175,55,0.35);
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
.form-hint-success {
    color: var(--emerald);
}
.form-hint-warning {
    color: #d97706;
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
            <h1 class="text-2xl md:text-3xl font-bold text-white tracking-tight">Edit Document</h1>
            <p class="text-emerald-100/80 text-sm mt-1.5 max-w-lg">Update document metadata, category, and file version</p>
        </div>
    </div>

    {{-- Form Card --}}
    <div class="form-container anim-2">
        <div class="form-card">
            <div class="form-card-header">
                <div class="flex items-center gap-3">
                    <div class="form-card-header-icon">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                        </svg>
                    </div>
                    <div>
                        <h2>Edit Document Metadata</h2>
                        <p>Update document information</p>
                    </div>
                </div>
            </div>

            <div class="p-6">
                {{-- Current Document Info Box --}}
                <div class="info-box">
                    <div class="flex items-start gap-2">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p>You are editing: <span class="info-highlight">{{ $document->title }}</span> 
                        @if($document->currentVersion)
                            (Version {{ $document->currentVersion->version_number }})
                        @endif
                        </p>
                    </div>
                </div>

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

                <form method="POST" action="{{ route('documents.update', $document) }}" enctype="multipart/form-data">
                    @csrf 
                    @method('PUT')

                    {{-- Title --}}
                    <div class="mb-5">
                        <label class="form-label">Title <span class="form-label-required">*</span></label>
                        <input type="text" name="title" value="{{ old('title', $document->title) }}" required
                               class="form-input {{ $errors->has('title') ? 'error' : '' }}">
                        @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Description --}}
                    <div class="mb-5">
                        <label class="form-label">Description</label>
                        <textarea name="description" rows="3"
                                  class="form-textarea {{ $errors->has('description') ? 'error' : '' }}">{{ old('description', $document->description) }}</textarea>
                        @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Category Select --}}
                    <div class="mb-5">
                        <label class="form-label">Category</label>
                        <select name="document_category_id" class="form-select {{ $errors->has('document_category_id') ? 'error' : '' }}">
                            <option value="">— No category —</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}"
                                    {{ old('document_category_id', $document->document_category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="form-hint">Categories are managed in the admin panel under Document Categories.</p>
                        @error('document_category_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Replace File Section --}}
                    <div class="mb-5">
                        <label class="form-label">
                            Replace File
                            <span class="font-normal text-text-3">(optional — leave blank to keep current version)</span>
                        </label>
                        <input type="file" name="file"
                               accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.gif,.zip"
                               class="form-file {{ $errors->has('file') ? 'error' : '' }}">
                        <p class="form-hint">Max 20 MB. Allowed: PDF, Word, Excel, PowerPoint, images, ZIP.</p>
                        @if($document->currentVersion)
                            <p class="form-hint form-hint-success mt-1">
                                Current file: {{ $document->currentVersion->file_name }} 
                                ({{ $document->formatted_size }})
                            </p>
                        @endif
                        @error('file') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Change Notes --}}
                    <div class="mb-5">
                        <label class="form-label">Change Notes <span class="font-normal text-text-3">(optional)</span></label>
                        <textarea name="change_notes" rows="2"
                                  class="form-textarea {{ $errors->has('change_notes') ? 'error' : '' }}"
                                  placeholder="Describes what changed in this version (e.g., 'Updated content', 'Fixed formatting')">{{ old('change_notes') }}</textarea>
                        <p class="form-hint">Describes what changed in this version (only used when a new file is uploaded).</p>
                        @error('change_notes') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Form Actions --}}
                    <div class="button-group">
                        <button type="submit" class="btn-emerald">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            Update Document
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