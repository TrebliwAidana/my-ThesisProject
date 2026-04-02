@extends('layouts.app')

@section('title', 'Create User')
@section('page-title', 'Create New User')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gold-200 dark:border-gold-800 overflow-hidden shadow-sm">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gold-800 bg-gray-50 dark:bg-gray-800/50">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Create New User</h2>
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Add a new user to the system</p>
        </div>
        
        <form method="POST" action="{{ route('admin.users.store') }}" class="p-5 space-y-5">
            @csrf
            
            {{-- First Name --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                    First Name <span class="text-red-500">*</span>
                </label>
                <input type="text" name="first_name" value="{{ old('first_name') }}" required 
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-gold-500 dark:bg-gray-700 dark:text-white"
                    placeholder="Enter first name">
                @error('first_name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            {{-- Middle Name (Optional) --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                    Middle Name <span class="text-gray-400 text-xs font-normal">(Optional)</span>
                </label>
                <input type="text" name="middle_name" value="{{ old('middle_name') }}" 
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-gold-500 dark:bg-gray-700 dark:text-white"
                    placeholder="Enter middle name">
                @error('middle_name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            {{-- Last Name --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                    Last Name <span class="text-red-500">*</span>
                </label>
                <input type="text" name="last_name" value="{{ old('last_name') }}" required 
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-gold-500 dark:bg-gray-700 dark:text-white"
                    placeholder="Enter last name">
                @error('last_name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            {{-- Email Address - Manual entry only --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                    Email Address <span class="text-red-500">*</span>
                </label>
                <input type="email" name="email" value="{{ old('email') }}" required 
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-gold-500 dark:bg-gray-700 dark:text-white"
                    placeholder="username@gmail.com">
                
                {{-- Hint Message --}}
                <div class="mt-2 p-2 bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 rounded-r-lg">
                    <div class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-yellow-600 dark:text-yellow-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-xs text-yellow-700 dark:text-yellow-300">
                            <span class="font-semibold">Hint:</span> Please use a valid Gmail account (e.g., yourname@gmail.com). 
                            A verification email will be sent to this address.
                        </p>
                    </div>
                </div>
                
                @error('email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            {{-- Role Selection --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                    Role <span class="text-red-500">*</span>
                </label>
                <select name="role_id" required 
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-gold-500 dark:bg-gray-700 dark:text-white">
                    <option value="">Select a role</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                            {{ $role->name }} @if($role->abbreviation)({{ $role->abbreviation }})@endif
                        </option>
                    @endforeach
                </select>
                @error('role_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            {{-- Position (Optional) --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                    Position <span class="text-gray-400 text-xs font-normal">(Optional)</span>
                </label>
                <input type="text" name="position" value="{{ old('position') }}" 
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-gold-500 dark:bg-gray-700 dark:text-white"
                    placeholder="e.g., President, Secretary, Adviser">
                @error('position')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            {{-- Student ID (Optional) --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                    Student ID <span class="text-gray-400 text-xs font-normal">(Optional)</span>
                </label>
                <input type="text" name="student_id" value="{{ old('student_id') }}" 
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-gold-500 dark:bg-gray-700 dark:text-white"
                    placeholder="Enter student ID number (e.g., 2020-12345)">
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Enter a valid student ID number if applicable</p>
                @error('student_id')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            {{-- Year Level - Grade 7 to 12 --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                    Year Level <span class="text-gray-400 text-xs font-normal">(Optional)</span>
                </label>
                <select name="year_level" 
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-gold-500 dark:bg-gray-700 dark:text-white">
                    <option value="">Select grade level</option>
                    <option value="Grade 7" {{ old('year_level') == 'Grade 7' ? 'selected' : '' }}>Grade 7</option>
                    <option value="Grade 8" {{ old('year_level') == 'Grade 8' ? 'selected' : '' }}>Grade 8</option>
                    <option value="Grade 9" {{ old('year_level') == 'Grade 9' ? 'selected' : '' }}>Grade 9</option>
                    <option value="Grade 10" {{ old('year_level') == 'Grade 10' ? 'selected' : '' }}>Grade 10</option>
                    <option value="Grade 11" {{ old('year_level') == 'Grade 11' ? 'selected' : '' }}>Grade 11</option>
                    <option value="Grade 12" {{ old('year_level') == 'Grade 12' ? 'selected' : '' }}>Grade 12</option>
                </select>
                @error('year_level')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            {{-- Password --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                    Password <span class="text-red-500">*</span>
                </label>
                <input type="password" name="password" required 
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-gold-500 dark:bg-gray-700 dark:text-white"
                    placeholder="Enter password">
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Minimum 8 characters</p>
                @error('password')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            {{-- Confirm Password --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                    Confirm Password <span class="text-red-500">*</span>
                </label>
                <input type="password" name="password_confirmation" required 
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-gold-500 dark:bg-gray-700 dark:text-white"
                    placeholder="Confirm password">
            </div>
            
            {{-- Active Status --}}
            <div>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}
                        class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-gold-500">
                    <span class="text-sm text-gray-700 dark:text-gray-300">Active</span>
                </label>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Inactive users cannot log in</p>
                @error('is_active')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            {{-- Action Buttons --}}
            <div class="flex gap-3 pt-4 border-t border-gray-100 dark:border-gold-800">
                <button type="submit" 
                    class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium rounded-lg bg-primary-600 hover:bg-gold-500 text-white transition-all duration-200 shadow-sm hover:shadow-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Create User
                </button>
                <a href="{{ route('admin.users.index') }}" 
                    class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium rounded-lg bg-gray-500 hover:bg-gray-600 text-white transition-all duration-200 shadow-sm hover:shadow-md">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>
@endsection