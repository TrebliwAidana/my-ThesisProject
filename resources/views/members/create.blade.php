@extends('layouts.app')

@section('title', 'Add New Member')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-slate-200 dark:border-gray-700">
        <div class="px-6 py-4 border-b border-slate-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold text-slate-800 dark:text-slate-200">Add New Member</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Email will be auto-generated as: firstname@vsulhs-sslg.com</p>
        </div>
        
        <form action="{{ route('members.store') }}" method="POST" class="p-6">
            @csrf
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Full Name</label>
                    <input type="text" name="full_name" required value="{{ old('full_name') }}"
                           class="w-full px-4 py-2 border border-slate-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white @error('full_name') border-red-500 @enderror">
                    @error('full_name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Position</label>
                    <input type="text" name="position" required value="{{ old('position') }}"
                           class="w-full px-4 py-2 border border-slate-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white @error('position') border-red-500 @enderror">
                    @error('position')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Term Start</label>
                        <input type="date" name="term_start" required value="{{ old('term_start') }}"
                               class="w-full px-4 py-2 border border-slate-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white @error('term_start') border-red-500 @enderror">
                        @error('term_start')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">Term End</label>
                        <input type="date" name="term_end" value="{{ old('term_end') }}"
                               class="w-full px-4 py-2 border border-slate-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white">
                        @error('term_end')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <div class="bg-slate-50 dark:bg-gray-700/50 rounded-lg p-4 mt-4">
                    <p class="text-sm text-slate-600 dark:text-slate-400">
                        <strong>Auto-generated credentials:</strong><br>
                        Email: <span class="font-mono text-indigo-600 dark:text-indigo-400">[firstname]@vsulhs-sslg.com</span><br>
                        Password: <span class="font-mono text-indigo-600 dark:text-indigo-400">password</span>
                    </p>
                </div>
            </div>
            
            <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-slate-200 dark:border-gray-700">
                <a href="{{ route('members.index') }}" 
                   class="px-4 py-2 border border-slate-300 dark:border-gray-600 rounded-lg text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-gray-700">
                    Cancel
                </a>
                <button type="submit"
                        class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium">
                    Create Member
                </button>
            </div>
        </form>
    </div>
</div>
@endsection