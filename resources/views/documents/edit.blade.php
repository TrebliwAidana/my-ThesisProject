@extends('layouts.app')

@section('title', 'Edit Document')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-emerald-200 dark:border-emerald-800 p-6">
        <h2 class="text-xl font-semibold mb-4">Edit Document Metadata</h2>

        <form method="POST" action="{{ route('documents.update', $document) }}">
            @csrf @method('PUT')

            <div class="mb-4">
                <label class="block text-sm font-semibold mb-2">Title</label>
                <input type="text" name="title" value="{{ old('title', $document->title) }}" required class="w-full border rounded-lg px-4 py-2">
            </div>

            <div class="mb-4">
                <label class="block text-sm font-semibold mb-2">Description</label>
                <textarea name="description" rows="3" class="w-full border rounded-lg px-4 py-2">{{ old('description', $document->description) }}</textarea>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-semibold mb-2">Category</label>
                    <input type="text" name="category" value="{{ old('category', $document->category) }}" class="w-full border rounded-lg px-4 py-2">
                </div>
                <div>
                    <label class="block text-sm font-semibold mb-2">Visibility</label>
                    <select name="is_public" class="w-full border rounded-lg px-4 py-2">
                        <option value="0" {{ !$document->is_public ? 'selected' : '' }}>Private (your organization)</option>
                        <option value="1" {{ $document->is_public ? 'selected' : '' }}>Public (everyone)</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-between gap-3">
                <button type="submit" class="bg-emerald-600 hover:bg-emerald-500 text-white px-6 py-2 rounded-lg">Update</button>
                <a href="{{ route('documents.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection