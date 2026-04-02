@extends('layouts.app')
@section('title', 'Upload Document — VSULHS_SSLG')

@section('content')

<div class="mb-6">
    <a href="{{ route('documents.index') }}" class="text-sm text-gray-500 hover:text-gray-800 transition">← Back to Documents</a>
    <h1 class="text-2xl font-bold text-gray-900 mt-2">Upload Document</h1>
</div>

<div class="bg-white rounded-xl border border-gold-200 p-6 max-w-xl">
    <form method="POST" action="{{ route('documents.store') }}" enctype="multipart/form-data">
        @csrf

        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Document Title</label>
            <input type="text" name="title" value="{{ old('title') }}" required placeholder="e.g. Meeting Minutes Q1 2025"
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-black {{ $errors->has('title') ? 'border-red-400' : '' }}">
            @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-6">
            <label class="block text-sm font-semibold text-gray-700 mb-1">File</label>
            <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-gray-400 transition">
                <input type="file" name="file" id="file" required accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.png,.jpg,.jpeg"
                       class="hidden" onchange="updateFileName(this)">
                <label for="file" class="cursor-pointer">
                    <svg class="w-8 h-8 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    <p id="file-label" class="text-sm text-gray-500">Click to browse or drag & drop</p>
                    <p class="text-xs text-gray-400 mt-1">PDF, DOC, DOCX, XLS, XLSX, PPT, PNG, JPG — max 10MB</p>
                </label>
            </div>
            @error('file') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex gap-3">
            <button type="submit"
                    class="bg-black text-white text-sm font-semibold px-5 py-2 rounded-lg hover:bg-gray-800 transition">
                Upload
            </button>
            <a href="{{ route('documents.index') }}"
               class="text-sm font-semibold text-gray-600 border border-gray-300 px-5 py-2 rounded-lg hover:bg-gray-50 transition">
                Cancel
            </a>
        </div>
    </form>
</div>

<script>
    function updateFileName(input) {
        const label = document.getElementById('file-label');
        label.textContent = input.files[0]?.name ?? 'Click to browse or drag & drop';
    }
</script>

@endsection
