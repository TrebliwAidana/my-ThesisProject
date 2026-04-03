@extends('layouts.app')

@section('title', $document->title)

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-emerald-200 dark:border-emerald-800 overflow-hidden">
        <div class="px-6 py-4 border-b border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/20">
            <h1 class="text-2xl font-bold">{{ $document->title }}</h1>
            <p class="text-sm text-gray-500">Uploaded by {{ $document->uploader->full_name }} on {{ $document->created_at->format('F d, Y') }}</p>
        </div>
        <div class="p-6 space-y-4">
            <div>
                <h3 class="font-semibold">Description</h3>
                <p class="text-gray-700 dark:text-gray-300">{{ $document->description ?: 'No description provided.' }}</p>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div><span class="font-semibold">Category:</span> {{ $document->category ?? '—' }}</div>
                <div><span class="font-semibold">File size:</span> {{ $document->formatted_size }}</div>
                <div><span class="font-semibold">MIME type:</span> {{ $document->mime_type }}</div>
                <div><span class="font-semibold">Visibility:</span> {{ $document->is_public ? 'Public' : 'Private (organization only)' }}</div>
            </div>
            <div class="pt-4 flex gap-3">
                <a href="{{ route('documents.download', $document) }}" class="bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2 rounded-lg">⬇️ Download</a>
                <a href="{{ route('documents.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">Back</a>
            </div>
        </div>
    </div>
</div>
@endsection