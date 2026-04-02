@extends('layouts.app')
@section('title', 'Documents — VSULHS_SSLG')

@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Documents</h1>
        <p class="text-sm text-gray-500 mt-1">Uploaded files and records</p>
    </div>
    <a href="{{ route('documents.create') }}"
       class="bg-black text-white text-sm font-semibold px-4 py-2 rounded-lg hover:bg-gray-800 transition">
        + Upload Document
    </a>
</div>

<div class="bg-white rounded-xl border border-gold-200 overflow-hidden">
    <table class="w-full text-sm">
        <thead class="bg-gray-50 border-b border-gold-200">
            <tr>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Title</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Uploaded By</th>
                <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse ($documents as $doc)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-5 py-3 font-medium text-gray-900">{{ $doc->title }}</td>
                <td class="px-5 py-3 text-gray-600">{{ $doc->uploader->full_name }}</td>
                <td class="px-5 py-3 text-gray-600">{{ $doc->created_at->format('M d, Y') }}</td>
                <td class="px-5 py-3 text-right">
                    <div class="flex items-center justify-end gap-2">
                        <a href="{{ route('documents.show', $doc->id) }}" target="_blank"
                           class="text-xs font-medium text-gray-700 border border-gray-300 px-3 py-1 rounded hover:bg-gray-100 transition">
                            View
                        </a>
                        <form method="POST" action="{{ route('documents.destroy', $doc->id) }}"
                              onsubmit="return confirm('Delete this document?')">
                            @csrf @method('DELETE')
                            <button class="text-xs font-medium text-red-600 border border-red-200 px-3 py-1 rounded hover:bg-red-50 transition">
                                Delete
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="px-5 py-10 text-center text-gray-400 text-sm italic">No documents uploaded yet.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    @if ($documents->hasPages())
    <div class="px-5 py-3 border-t border-gray-100">{{ $documents->links() }}</div>
    @endif
</div>

@endsection
