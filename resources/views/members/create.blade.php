@extends('layouts.app')

@section('title', 'Add New Member — VSULHS_SSLG')

@section('content')

<div class="mb-6">
    <a href="{{ route('members.index') }}" class="text-sm text-gray-500 hover:text-gray-800 transition">← Back to Members</a>
    <h1 class="text-2xl font-bold text-gray-900 mt-2">Add New Member</h1>
    <p class="text-sm text-gray-500 mt-1">Create a new member account</p>
</div>

<div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 max-w-xl">
    <form method="POST" action="{{ route('members.store') }}">
        @csrf

        {{-- Full Name --}}
        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Full Name</label>
            <input type="text" name="full_name" value="{{ old('full_name') }}" required
                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 {{ $errors->has('full_name') ? 'border-red-400' : '' }}">
            @error('full_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Role Selection --}}
        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Role</label>
            <select name="role_id" id="role_id" required
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 {{ $errors->has('role_id') ? 'border-red-400' : '' }}">
                <option value="">Select Role</option>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                        {{ $role->name }}
                    </option>
                @endforeach
            </select>
            @error('role_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Position Selection (dynamic based on role) --}}
        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Position</label>
            <select name="position" id="position" required
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 {{ $errors->has('position') ? 'border-red-400' : '' }}">
                <option value="">Select Position</option>
            </select>
            @error('position') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Member Since (Joined At) --}}
        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Member Since</label>
            <input type="date" name="joined_at" value="{{ old('joined_at') }}" required
                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 {{ $errors->has('joined_at') ? 'border-red-400' : '' }}">
            @error('joined_at') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">The date when this member first joined the organization.</p>
        </div>

        {{-- Term Start --}}
        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Term Start</label>
            <input type="date" name="term_start" value="{{ old('term_start') }}" required
                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 {{ $errors->has('term_start') ? 'border-red-400' : '' }}">
            @error('term_start') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Term End --}}
        <div class="mb-6">
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Term End</label>
            <input type="date" name="term_end" value="{{ old('term_end') }}"
                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 {{ $errors->has('term_end') ? 'border-red-400' : '' }}">
            @error('term_end') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
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
                    class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-5 py-2 rounded-lg transition">
                Create Member
            </button>
            <a href="{{ route('members.index') }}"
               class="text-sm font-semibold text-gray-600 dark:text-gray-400 border border-gray-300 dark:border-gray-600 px-5 py-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                Cancel
            </a>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const roleSelect = document.getElementById('role_id');
        const positionSelect = document.getElementById('position');
        
        const positions = {
            'Adviser': ['Adviser'],
            'Officer': ['President', 'Secretary', 'Treasurer', 'Auditor'],
            'Auditor': ['Auditor'],
            'Member': ['Member']
        };
        
        function updatePositionDropdown() {
            const selectedOption = roleSelect.options[roleSelect.selectedIndex];
            const selectedRole = selectedOption ? selectedOption.text : '';
            
            positionSelect.innerHTML = '<option value="">Select Position</option>';
            
            if (selectedRole && positions[selectedRole]) {
                positions[selectedRole].forEach(function(position) {
                    const option = document.createElement('option');
                    option.value = position;
                    option.textContent = position;
                    positionSelect.appendChild(option);
                });
            }
        }
        
        roleSelect.addEventListener('change', updatePositionDropdown);
        
        if (roleSelect.value) {
            updatePositionDropdown();
        }
    });
</script>

@endsection