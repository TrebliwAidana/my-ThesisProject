@extends('layouts.app')

@section('title', 'Upload Document')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gold-200 dark:border-gold-800 overflow-hidden">
        <div class="bg-gradient-to-r from-emerald-600 to-emerald-700 dark:from-emerald-800 dark:to-emerald-900 px-6 py-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="font-semibold text-white">Upload New Document</h2>
                    <p class="text-emerald-100 text-xs">Add a document to the library (no category required)</p>
                </div>
            </div>
        </div>

        <div class="p-6">
            @if($errors->any())
                <div class="mb-4 p-3 bg-red-100 dark:bg-red-900/30 rounded text-red-700 text-sm">
                    <ul class="list-disc list-inside">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif

            <form method="POST" action="{{ route('documents.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" required
                           class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:ring-2 focus:ring-gold-500">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Description</label>
                    <textarea name="description" rows="3"
                              class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm">{{ old('description') }}</textarea>
                </div>

                {{-- Tags (optional, can be a comma-separated input) --}}
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Tags (optional, comma separated)</label>
                    <input type="text" name="tags" value="{{ old('tags') }}"
                           placeholder="e.g., report, minutes, 2025"
                           class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm">
                    <p class="text-xs text-gray-500 mt-1">Example: `financial, annual, draft`</p>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Change notes (optional)</label>
                    <textarea name="change_notes" rows="2"
                              class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm">{{ old('change_notes') }}</textarea>
                    <p class="text-xs text-gray-500 mt-1">Describes this version (e.g., “First draft”, “Approved version”).</p>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">File <span class="text-red-500">*</span></label>
                    <input type="file" name="file" required
                           accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.gif,.zip"
                           class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm
                                  file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-semibold
                                  file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                    <p class="text-xs text-gray-500 mt-1">Max 10MB. Allowed: PDF, Word, Excel, PowerPoint, images, ZIP.</p>
                </div>

                <div class="flex justify-between gap-3">
                    <button type="submit" class="bg-emerald-600 hover:bg-gold-500 text-white px-6 py-2 rounded-lg transition">Upload</button>
                    <a href="{{ route('documents.index') }}" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg transition">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection