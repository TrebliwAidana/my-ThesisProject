@extends('layouts.app')

@section('title', 'Edit User — VSULHS_SSLG')
@section('page-title', 'Edit User')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-6">
        <form method="POST" action="{{ route('admin.users.update', $user->id) }}" id="editUserForm">
            @csrf
            @method('PUT')

            {{-- First Name & Last Name --}}
            <div class="grid grid-cols-2 gap-4">
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        First Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}" required
                           class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('first_name') border-red-500 @enderror">
                    @error('first_name')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Last Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}" required
                           class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('last_name') border-red-500 @enderror">
                    @error('last_name')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Middle Name (Optional) --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Middle Name <span class="text-gray-400">(Optional)</span>
                </label>
                <input type="text" name="middle_name" value="{{ old('middle_name', $user->middle_name) }}"
                       class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            {{-- Email --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Email Address</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                       class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('email') border-red-500 @enderror">
                @error('email')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Student ID --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Student ID Number
                </label>
                <input type="text" name="student_id" value="{{ old('student_id', $user->student_id) }}"
                       class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('student_id') border-red-500 @enderror"
                       placeholder="e.g., 2024-0001">
                @error('student_id')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Year Level --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Year Level</label>
                <select name="year_level" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Select Year Level</option>
                    <option value="Grade 7" {{ old('year_level', $user->year_level) == 'Grade 7' ? 'selected' : '' }}>Grade 7</option>
                    <option value="Grade 8" {{ old('year_level', $user->year_level) == 'Grade 8' ? 'selected' : '' }}>Grade 8</option>
                    <option value="Grade 9" {{ old('year_level', $user->year_level) == 'Grade 9' ? 'selected' : '' }}>Grade 9</option>
                    <option value="Grade 10" {{ old('year_level', $user->year_level) == 'Grade 10' ? 'selected' : '' }}>Grade 10</option>
                    <option value="Grade 11" {{ old('year_level', $user->year_level) == 'Grade 11' ? 'selected' : '' }}>Grade 11</option>
                    <option value="Grade 12" {{ old('year_level', $user->year_level) == 'Grade 12' ? 'selected' : '' }}>Grade 12</option>
                </select>
            </div>

            {{-- Role Selection --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Role</label>
                <select name="role_id" id="role_id" required
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" data-role-name="{{ $role->name }}" 
                            {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
                @error('role_id')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Position Dropdown --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Position</label>
                <select name="position" id="position" 
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Select Position</option>
                </select>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Positions are based on the selected role</p>
            </div>

            {{-- Password (optional for edit) --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Password (leave blank to keep current)</label>
                <div class="relative">
                    <input type="password" name="password" id="password" 
                           class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('password') border-red-500 @enderror">
                    <button type="button" id="generatePassword" 
                            class="absolute right-3 top-1/2 transform -translate-y-1/2 text-xs text-indigo-600 hover:text-indigo-700 dark:text-indigo-400">
                        Generate
                    </button>
                </div>
                
                <div id="passwordMatch" class="text-xs mt-1"></div>
                <p class="text-xs text-gray-500 mt-1">Minimum 8 characters. Leave blank to keep current password.</p>
                @error('password')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Password Confirmation --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Confirm Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation" 
                       class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            {{-- Account Status --}}
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Account Status</label>
                <div class="flex items-center gap-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="is_active" value="1" {{ old('is_active', $user->is_active) == 1 ? 'checked' : '' }} class="w-4 h-4 text-indigo-600">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Active</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" name="is_active" value="0" {{ old('is_active', $user->is_active) == 0 ? 'checked' : '' }} class="w-4 h-4 text-indigo-600">
                        <span class="text-sm text-gray-700 dark:text-gray-300">Inactive</span>
                    </label>
                </div>
            </div>

            {{-- Reset Password Section --}}
            <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Reset Password</h3>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">
                    Send a password reset link to {{ $user->email }}
                </p>
                <form method="POST" action="{{ route('admin.users.reset-password', $user->id) }}" class="inline">
                    @csrf
                    <button type="submit" class="bg-gray-600 hover:bg-gray-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
                        Send Reset Link
                    </button>
                </form>
            </div>

            <div class="flex gap-3">
                <button type="submit"
                        class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition">
                    Update User
                </button>
                <a href="{{ route('admin.users.index') }}"
                   class="flex-1 text-center text-sm font-semibold text-gray-600 dark:text-gray-400 border border-gray-300 dark:border-gray-600 px-5 py-2.5 rounded-lg hover:bg-gray-50 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
// Position options based on role
const positionOptions = {
    'Adviser': ['Adviser'],
    'Officer': ['President', 'Vice President', 'Secretary', 'Treasurer'],
    'Auditor': ['Auditor'],
    'Member': ['Member']
};

// Update position dropdown based on selected role
function updatePositionDropdown() {
    const roleSelect = document.getElementById('role_id');
    const positionSelect = document.getElementById('position');
    const selectedOption = roleSelect.options[roleSelect.selectedIndex];
    const roleName = selectedOption ? selectedOption.getAttribute('data-role-name') || selectedOption.textContent : '';
    const currentPosition = '{{ $user->position }}';
    
    positionSelect.innerHTML = '<option value="">Select Position</option>';
    
    if (roleName && positionOptions[roleName]) {
        positionOptions[roleName].forEach(pos => {
            const option = document.createElement('option');
            option.value = pos;
            option.textContent = pos;
            if (pos === currentPosition) {
                option.selected = true;
            }
            positionSelect.appendChild(option);
        });
        positionSelect.disabled = false;
    } else {
        positionSelect.disabled = true;
    }
}

// Password match checker (simplified)
function checkPasswordMatch() {
    const password = document.getElementById('password')?.value;
    const confirm = document.getElementById('password_confirmation')?.value;
    const matchDiv = document.getElementById('passwordMatch');
    
    if (confirm && confirm.length > 0) {
        if (password === confirm) {
            matchDiv.innerHTML = '<span class="text-green-500">✓ Passwords match</span>';
        } else {
            matchDiv.innerHTML = '<span class="text-red-500">✗ Passwords do not match</span>';
        }
    } else {
        matchDiv.innerHTML = '';
    }
}

// Generate random password
document.getElementById('generatePassword')?.addEventListener('click', function() {
    const chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
    let password = '';
    for (let i = 0; i < 12; i++) {
        password += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    document.getElementById('password').value = password;
    document.getElementById('password_confirmation').value = password;
    checkPasswordMatch();
});

document.getElementById('password_confirmation')?.addEventListener('input', checkPasswordMatch);
document.getElementById('password')?.addEventListener('input', checkPasswordMatch);
document.getElementById('role_id')?.addEventListener('change', updatePositionDropdown);

// Initialize on page load
updatePositionDropdown();
</script>
@endsection