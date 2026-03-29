@extends('layouts.app')

@section('title', 'Create User — VSULHS_SSLG')
@section('page-title', 'Create User')

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 transition">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
        </svg>
        Back to Users
    </a>
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mt-3">Create New User</h1>
    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Add a new user to the system with role-based permissions</p>
</div>

<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-6 max-w-2xl mx-auto">
    <form method="POST" action="{{ route('admin.users.store') }}" id="createUserForm">
        @csrf

        {{-- First Name & Last Name --}}
        <div class="grid grid-cols-2 gap-4">
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    First Name <span class="text-red-500">*</span>
                </label>
                <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" required
                       class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition @error('first_name') border-red-500 @enderror"
                       placeholder="Enter first name">
                @error('first_name')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Last Name <span class="text-red-500">*</span>
                </label>
                <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" required
                       class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition @error('last_name') border-red-500 @enderror"
                       placeholder="Enter last name">
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
            <input type="text" name="middle_name" value="{{ old('middle_name') }}"
                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition"
                   placeholder="Enter middle name (optional)">
        </div>

        {{-- Email with Auto-generate --}}
        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Email Address</label>
            <div class="relative">
                <input type="email" name="email" id="email" value="{{ old('email') }}" 
                       class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition @error('email') border-red-500 @enderror"
                       placeholder="Auto-generated: firstname.lastname@gmail.com">
                <button type="button" id="autoGenerateEmail" 
                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-xs text-indigo-600 hover:text-indigo-700 dark:text-indigo-400">
                    Generate
                </button>
            </div>
            @error('email')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                Leave empty to auto-generate: <span class="font-mono">firstname.lastname@gmail.com</span>
            </p>
        </div>

        {{-- Student ID --}}
        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                Student ID Number
            </label>
            <input type="text" name="student_id" value="{{ old('student_id') }}"
                   class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition @error('student_id') border-red-500 @enderror"
                   placeholder="e.g., 2024-0001">
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">For student identification</p>
            @error('student_id')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Year Level (Grades 7-12) --}}
        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Year Level</label>
            <select name="year_level" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                <option value="">Select Year Level</option>
                <option value="Grade 7" {{ old('year_level') == 'Grade 7' ? 'selected' : '' }}>Grade 7</option>
                <option value="Grade 8" {{ old('year_level') == 'Grade 8' ? 'selected' : '' }}>Grade 8</option>
                <option value="Grade 9" {{ old('year_level') == 'Grade 9' ? 'selected' : '' }}>Grade 9</option>
                <option value="Grade 10" {{ old('year_level') == 'Grade 10' ? 'selected' : '' }}>Grade 10</option>
                <option value="Grade 11" {{ old('year_level') == 'Grade 11' ? 'selected' : '' }}>Grade 11</option>
                <option value="Grade 12" {{ old('year_level') == 'Grade 12' ? 'selected' : '' }}>Grade 12</option>
            </select>
        </div>

        {{-- Role Selection --}}
        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Role</label>
            <select name="role_id" id="role_id" required
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition @error('role_id') border-red-500 @enderror">
                <option value="">Select Role</option>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}" data-role-name="{{ $role->name }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                        {{ $role->name }}
                    </option>
                @endforeach
            </select>
            @error('role_id')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Role determines what permissions the user has</p>
        </div>

        {{-- Position Dropdown (Dynamic based on role) --}}
        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Position</label>
            <select name="position" id="position" 
                    class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition">
                <option value="">Select Position</option>
            </select>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Position within the organization based on selected role</p>
        </div>

        {{-- Password Info --}}
        <div class="mb-4 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
            <div class="flex items-start gap-2">
                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div class="flex-1">
                    <p class="text-sm font-medium text-blue-800 dark:text-blue-300 mb-1">Default Password</p>
                    <p class="text-xs text-blue-700 dark:text-blue-400">
                        New users will have the default password: <span class="font-mono bg-blue-100 dark:bg-blue-800/50 px-1 py-0.5 rounded">password</span>
                    </p>
                    <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">
                        Users can change their password after first login.
                    </p>
                </div>
            </div>
        </div>

        {{-- Account Status --}}
        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Account Status</label>
            <div class="flex items-center gap-4">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="is_active" value="1" {{ old('is_active', 1) == 1 ? 'checked' : '' }} class="w-4 h-4 text-indigo-600 focus:ring-indigo-500">
                    <span class="text-sm text-gray-700 dark:text-gray-300">Active</span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="radio" name="is_active" value="0" {{ old('is_active') == 0 ? 'checked' : '' }} class="w-4 h-4 text-indigo-600 focus:ring-indigo-500">
                    <span class="text-sm text-gray-700 dark:text-gray-300">Inactive</span>
                </label>
            </div>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Inactive users cannot log in to the system</p>
        </div>

        {{-- Welcome Email Option --}}
        <div class="mb-6">
            <label class="flex items-center gap-2 cursor-pointer">
                <input type="checkbox" name="send_welcome_email" value="1" checked class="w-4 h-4 text-indigo-600 rounded focus:ring-indigo-500">
                <span class="text-sm text-gray-700 dark:text-gray-300">Send welcome email with login credentials</span>
            </label>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 ml-6">User will receive an email with their login details</p>
        </div>

        <div class="flex gap-3">
            <button type="submit" id="submitBtn"
                    class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition transform hover:scale-105 active:scale-95">
                Create User
            </button>
            <a href="{{ route('admin.users.index') }}"
               class="flex-1 text-center text-sm font-semibold text-gray-600 dark:text-gray-400 border border-gray-300 dark:border-gray-600 px-5 py-2.5 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                Cancel
            </a>
        </div>
    </form>
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
    
    positionSelect.innerHTML = '<option value="">Select Position</option>';
    
    if (roleName && positionOptions[roleName]) {
        positionOptions[roleName].forEach(pos => {
            const option = document.createElement('option');
            option.value = pos;
            option.textContent = pos;
            positionSelect.appendChild(option);
        });
        positionSelect.disabled = false;
    } else {
        positionSelect.disabled = true;
    }
}

// Generate email from first and last name
function generateEmail() {
    const firstName = document.getElementById('first_name').value;
    const lastName = document.getElementById('last_name').value;
    
    if (firstName && lastName) {
        const emailName = firstName.toLowerCase() + '.' + lastName.toLowerCase();
        const cleanEmail = emailName.replace(/[^a-z0-9.]/g, '');
        return cleanEmail + '@gmail.com';
    }
    return '';
}

// Auto-generate email
function updateEmail() {
    const emailInput = document.getElementById('email');
    const autoEmail = generateEmail();
    
    if (autoEmail && !emailInput.value) {
        emailInput.value = autoEmail;
    }
}

// Manual email generation
document.getElementById('autoGenerateEmail')?.addEventListener('click', function() {
    const email = generateEmail();
    if (email) {
        document.getElementById('email').value = email;
        const emailInput = document.getElementById('email');
        emailInput.classList.add('border-green-500');
        setTimeout(() => emailInput.classList.remove('border-green-500'), 2000);
    } else {
        alert('Please enter first name and last name first.');
    }
});

// Listen for name changes
document.getElementById('first_name')?.addEventListener('input', updateEmail);
document.getElementById('last_name')?.addEventListener('input', updateEmail);

// Role change listener
document.getElementById('role_id')?.addEventListener('change', updatePositionDropdown);

// Form validation
const form = document.getElementById('createUserForm');
if (form) {
    form.addEventListener('submit', function(e) {
        const firstName = document.getElementById('first_name').value;
        const lastName = document.getElementById('last_name').value;
        
        if (!firstName || !lastName) {
            e.preventDefault();
            alert('Please enter both first name and last name.');
            return false;
        }
        
        return true;
    });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updatePositionDropdown();
    updateEmail();
});
</script>
@endsection