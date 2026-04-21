@extends('layouts.app')

@section('title', 'Document Categories')
@section('page-title', 'Manage Document Categories')

@section('content')
<div class="bg-white dark:bg-gray-800 rounded-xl border border-gold-200 dark:border-gold-800 overflow-hidden shadow-sm">
    @if(session('success'))
        <div class="m-4 p-3 bg-green-100 text-green-700 rounded border border-green-200">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="m-4 p-3 bg-red-100 text-red-700 rounded border border-red-200">
            {{ session('error') }}
        </div>
    @endif
    <div class="p-4 border-b border-gold-200 dark:border-gold-800 flex justify-between items-center">
        <h2 class="text-lg font-semibold text-gray-800 dark:text-white">Categories</h2>
        <a href="{{ route('admin.document-categories.create') }}"
           class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm">
            + New Category
        </a>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-5 py-3 text-left">Name</th>
                    <th class="px-5 py-3 text-left">Description</th>
                    <th class="px-5 py-3 text-left">Status</th>
                    <th class="px-5 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($categories as $cat)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <td class="px-5 py-3 font-medium">{{ $cat->name }}</td>
                    <td class="px-5 py-3 text-gray-600 dark:text-gray-400">{{ $cat->description ?? '—' }}</td>
                    <td class="px-5 py-3">
                        <span class="px-2 py-1 rounded-full text-xs font-medium {{ $cat->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                            {{ $cat->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.document-categories.edit', $cat) }}" class="text-blue-600 hover:underline text-xs">Edit</a>
                            <form method="POST" action="{{ route('admin.document-categories.destroy', $cat) }}" onsubmit="return confirm('Delete this category?')" class="inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline text-xs">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" class="px-5 py-8 text-center text-gray-500">No categories found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($categories->hasPages())
    <div class="p-4 border-t border-gray-100 dark:border-gray-700">
        {{ $categories->links() }}
    </div>
    @endif
</div>
@endsection