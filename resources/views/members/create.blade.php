@extends('layouts.app')

@section('title', 'Add New Member — VSULHS_SSLG')

@section('content')

{{-- Vanishing Popup Notifications for Validation Errors --}}
@if($errors->any())
    <div x-data="{ show: true }" 
         x-init="setTimeout(() => show = false, 5000)"
         x-show="show"
         x-transition:enter="transform transition duration-300 ease-out"
         x-transition:enter-start="translate-x-full opacity-0"
         x-transition:enter-end="translate-x-0 opacity-100"
         x-transition:leave="transform transition duration-200 ease-in"
         x-transition:leave-start="translate-x-0 opacity-100"
         x-transition:leave-end="translate-x-full opacity-0"
         class="fixed top-20 right-4 z-50 w-96 max-w-full rounded-lg shadow-lg overflow-hidden border-l-4 border-red-500 bg-red-50 dark:bg-red-900/30">
        <div class="p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm font-medium text-red-800 dark:text-red-200">Please fix the following errors:</p>
                    <ul class="text-sm text-red-700 dark:text-red-300 mt-1 list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <div class="ml-4 flex-shrink-0">
                    <button @click="show = false" class="inline-flex text-red-500 hover:text-red-600 focus:outline-none">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif

<div class="mb-6">
    <a href="{{ route('members.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        Back to Members
    </a>
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mt-3">Add New Member</h1>
    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Create a new member account</p>
</div>

<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-6 max-w-2xl mx-auto">
    <form method="POST" action="{{ route('members.store') }}">
        @csrf

        {{-- Full Name --}}
        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Full Name</label>
            <input type="text" name="full_name" value="{{ old('full_name') }}" required
                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
        </div>

        {{-- Role Selection --}}
        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Role</label>
            <select name="role_id" id="role_id" required
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                <option value="">Select Role</option>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                        {{ $role->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Position Selection (dynamic based on role) --}}
        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Position</label>
            <select name="position" id="position" required
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                <option value="">Select Position</option>
            </select>
        </div>

        {{-- Member Since (Joined At) --}}
        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Member Since</label>
            <input type="date" name="joined_at" value="{{ old('joined_at') }}" required
                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">The date when this member first joined the organization.</p>
        </div>

        {{-- Term Start --}}
        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Term Start</label>
            <input type="date" name="term_start" value="{{ old('term_start') }}" required
                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
        </div>

        {{-- Term End --}}
        <div class="mb-6">
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Term End</label>
            <input type="date" name="term_end" value="{{ old('term_end') }}"
                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Leave empty for ongoing term.</p>
        </div>

        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4 mb-6">
            <p class="text-sm text-blue-800 dark:text-blue-300">
                <strong>Auto-generated credentials:</strong><br>
                Email: <span class="font-mono">[firstname]@vsulhs-sslg.com</span><br>
                Password: <span class="font-mono">password</span>
            </p>
        </div>

        <div class="flex gap-3">
            <button type="submit"
                    class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition">
                Create Member
            </button>
            <a href="{{ route('members.index') }}"
               class="flex-1 text-center text-sm font-semibold text-gray-600 dark:text-gray-400 border border-gray-300 dark:border-gray-600 px-5 py-2.5 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                Cancel
            </a>
        </div>
    </form>
</div>

<script>
    const positionsByRole = {
        'Adviser': ['Adviser'],
        'Officer': ['President', 'Secretary', 'Treasurer', 'Auditor'],
        'Auditor': ['Auditor'],
        'Member': ['Member']
    };

    const roleSelect = document.getElementById('role_id');
    const positionSelect = document.getElementById('position');

    function updatePositions() {
        const selectedOption = roleSelect.options[roleSelect.selectedIndex];
        const selectedRole = selectedOption ? selectedOption.text : '';
        
        positionSelect.innerHTML = '<option value="">Select Position</option>';
        
        if (selectedRole && positionsByRole[selectedRole]) {
            positionsByRole[selectedRole].forEach(position => {
                const option = document.createElement('option');
                option.value = position;
                option.textContent = position;
                positionSelect.appendChild(option);
            });
        }
    }

    roleSelect.addEventListener('change', updatePositions);
    
    if (roleSelect.value) {
        updatePositions();
    }
</script>

@endsection