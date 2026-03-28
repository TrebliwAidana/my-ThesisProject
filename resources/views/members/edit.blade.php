@extends('layouts.app')

@section('title', 'Edit Member — VSULHS_SSLG')

@section('content')

<div class="mb-6">
    <a href="{{ route('members.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        Back to Members
    </a>
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mt-3">Edit Member</h1>
    @if($member && $member->user)
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Editing {{ $member->user->full_name }}</p>
    @endif
</div>

@if($member && $member->user)
<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-6 max-w-2xl mx-auto">
    <form method="POST" action="{{ route('members.update', $member->id) }}">
        @csrf 
        @method('PUT')

        {{-- Member Info Card --}}
        <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700/30 rounded-xl">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-indigo-100 to-indigo-200 dark:from-indigo-900/50 dark:to-indigo-800/50 flex items-center justify-center text-lg font-bold text-indigo-700 dark:text-indigo-300">
                    {{ strtoupper(substr($member->user->full_name, 0, 2)) }}
                </div>
                <div>
                    <p class="font-semibold text-gray-900 dark:text-white">{{ $member->user->full_name }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $member->user->email }}</p>
                </div>
            </div>
        </div>

        {{-- Role Selection (reordered to come first) --}}
        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Role</label>
            <select name="role_id" id="role_id" required
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition {{ $errors->has('role_id') ? 'border-red-500' : '' }}">
                <option value="">Select Role</option>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}" {{ old('role_id', $member->user->role_id) == $role->id ? 'selected' : '' }}>
                        {{ $role->name }}
                    </option>
                @endforeach
            </select>
            @error('role_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            
            @php
                $adviserCount = \App\Models\User::whereHas('role', function($q) {
                    $q->where('name', 'Adviser');
                })->count();
                $isLastAdviser = ($member->user->role->name === 'Adviser' && $adviserCount <= 1);
            @endphp
            
            @if($isLastAdviser)
                <div class="mt-2 p-3 bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-500 rounded">
                    <p class="text-sm text-yellow-800 dark:text-yellow-300">
                        ⚠️ <strong>Warning:</strong> This is the LAST ADVISER in the system.<br>
                        You cannot change the role of the last adviser. The role must remain as "Adviser" to maintain system administration.
                    </p>
                </div>
            @endif
        </div>

        {{-- Position Selection (dropdown, dynamic based on role) --}}
        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Position</label>
            <select name="position" id="position" required
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition {{ $errors->has('position') ? 'border-red-500' : '' }}">
                <option value="">Select Position</option>
                @php
                    $currentRoleName = $member->user->role->name;
                    $allowedPositions = [
                        'Adviser' => ['Adviser'],
                        'Officer' => ['President', 'Secretary', 'Treasurer', 'Auditor'],
                        'Auditor' => ['Auditor'],
                        'Member' => ['Member'],
                    ];
                    $currentPositions = $allowedPositions[$currentRoleName] ?? [];
                @endphp
                @foreach($currentPositions as $pos)
                    <option value="{{ $pos }}" {{ old('position', $member->position) == $pos ? 'selected' : '' }}>
                        {{ $pos }}
                    </option>
                @endforeach
            </select>
            @error('position') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Member Since (Joined At) --}}
        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Member Since</label>
            <input type="date" name="joined_at" value="{{ old('joined_at', optional($member->joined_at)->format('Y-m-d')) }}" required
                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition {{ $errors->has('joined_at') ? 'border-red-500' : '' }}">
            @error('joined_at') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">The date when this member first joined the organization.</p>
        </div>

        {{-- Term Start --}}
        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Term Start</label>
            <input type="date" name="term_start" value="{{ old('term_start', optional($member->term_start)->format('Y-m-d')) }}" required
                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition {{ $errors->has('term_start') ? 'border-red-500' : '' }}">
            @error('term_start') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Term End --}}
        <div class="mb-6">
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Term End</label>
            <input type="date" name="term_end" value="{{ old('term_end', optional($member->term_end)->format('Y-m-d')) }}"
                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition {{ $errors->has('term_end') ? 'border-red-500' : '' }}">
            @error('term_end') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Leave empty for ongoing term.</p>
        </div>

        <div class="flex gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
            <button type="submit"
                    class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition duration-200 shadow-md hover:shadow-lg">
                Update Member
            </button>
            <a href="{{ route('members.index') }}"
               class="flex-1 text-center text-sm font-semibold text-gray-600 dark:text-gray-400 border border-gray-300 dark:border-gray-600 px-5 py-2.5 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                Cancel
            </a>
        </div>
    </form>
</div>

<script>
    // Dynamic position dropdown based on role selection
    const positionsByRole = {
        'Adviser': ['Adviser'],
        'Officer': ['President', 'Secretary', 'Treasurer', 'Auditor'],
        'Auditor': ['Auditor'],
        'Member': ['Member']
    };

    const roleSelect = document.getElementById('role_id');
    const positionSelect = document.getElementById('position');
    const currentPosition = "{{ $member->position }}";

    function updatePositions() {
        const selectedOption = roleSelect.options[roleSelect.selectedIndex];
        const selectedRole = selectedOption ? selectedOption.text : '';
        
        // Clear current options
        positionSelect.innerHTML = '<option value="">Select Position</option>';
        
        if (selectedRole && positionsByRole[selectedRole]) {
            positionsByRole[selectedRole].forEach(position => {
                const option = document.createElement('option');
                option.value = position;
                option.textContent = position;
                // Pre-select the current position if it matches
                if (position === currentPosition) {
                    option.selected = true;
                }
                positionSelect.appendChild(option);
            });
        }
    }

    // Add event listener for role change
    roleSelect.addEventListener('change', updatePositions);
</script>

@else
<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-8 max-w-2xl mx-auto text-center">
    <svg class="w-16 h-16 mx-auto text-red-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
    </svg>
    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Invalid Member Record</h3>
    <p class="text-gray-600 dark:text-gray-400 mb-4">This member record is missing the associated user account.</p>
    <a href="{{ route('members.index') }}" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        Back to Members
    </a>
</div>
@endif

@endsection