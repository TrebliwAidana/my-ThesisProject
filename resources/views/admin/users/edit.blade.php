@extends('layouts.app')

@section('title', 'Edit User — ' . $user->full_name)

@php
    $roleColorMap = [
        'System Administrator' => 'bg-gold-100 text-gold-700 dark:bg-gold-900/50 dark:text-gold-300',
        'Club Adviser'         => 'bg-amber-100 text-amber-700 dark:bg-amber-900/50 dark:text-amber-300',
        'Treasurer'            => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/50 dark:text-emerald-300',
        'Auditor'              => 'bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300',
        'Guest'                => 'bg-gray-100 text-gray-600 dark:bg-gray-800 dark:text-gray-400',
    ];

    $colorClass = $roleColorMap[$user->role?->name] ?? 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300';
@endphp

@section('content')
<div class="max-w-3xl mx-auto">
    {{-- Header --}}
    <div class="mb-6">
        <a href="{{ route('admin.users.index') }}"
           class="inline-flex items-center gap-1 text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200 transition mb-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Back to Users
        </a>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit User</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
            Editing <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $user->full_name }}</span>
            <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $colorClass }}">
                {{ $user->role?->name ?? 'No Role' }}
            </span>
        </p>
    </div>

    {{-- Form Card --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gold-200 dark:border-gold-800 overflow-hidden shadow-sm">
        <div class="px-5 py-4 border-b border-gray-100 dark:border-gold-800 bg-gray-50 dark:bg-gray-800/50">
            <h2 class="text-sm font-semibold text-gray-900 dark:text-white">Account Details</h2>
        </div>

        {{--
            Double-submit protection is handled by the global JS guard in app.js.
            The guard runs in capture phase, disables the submit button on first
            submission, and shows a spinner — no Alpine wiring needed here.
            data-no-guard is NOT set, so the guard is active on this form.
        --}}
        <form method="POST" action="{{ route('admin.users.update', $user->id) }}" class="p-5 space-y-5">
            @csrf
            @method('PUT')

            {{-- Validation Errors --}}
            @if ($errors->any())
                <div class="p-3 bg-red-50 dark:bg-red-900/30 border border-red-300 dark:border-red-700 rounded-lg">
                    <p class="text-sm font-semibold text-red-700 dark:text-red-300 mb-1">Please fix the following errors:</p>
                    <ul class="text-sm text-red-600 dark:text-red-400 space-y-0.5 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Name Fields --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label for="first_name" class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                        First Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="first_name" name="first_name"
                           value="{{ old('first_name', $user->first_name) }}" required maxlength="255"
                           class="w-full px-3 py-2 border {{ $errors->has('first_name') ? 'border-red-400' : 'border-gray-300 dark:border-gray-600' }} rounded-lg focus:outline-none focus:ring-2 focus:ring-gold-500 dark:bg-gray-700 dark:text-white">
                    @error('first_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="middle_name" class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                        Middle Name
                    </label>
                    <input type="text" id="middle_name" name="middle_name"
                           value="{{ old('middle_name', $user->middle_name) }}" maxlength="255"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-gold-500 dark:bg-gray-700 dark:text-white">
                </div>
                <div>
                    <label for="last_name" class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                        Last Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="last_name" name="last_name"
                           value="{{ old('last_name', $user->last_name) }}" required maxlength="255"
                           class="w-full px-3 py-2 border {{ $errors->has('last_name') ? 'border-red-400' : 'border-gray-300 dark:border-gray-600' }} rounded-lg focus:outline-none focus:ring-2 focus:ring-gold-500 dark:bg-gray-700 dark:text-white">
                    @error('last_name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Email --}}
            <div>
                <label for="email" class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                    Email Address <span class="text-red-500">*</span>
                </label>
                <input type="email" id="email" name="email"
                       value="{{ old('email', $user->email) }}" required maxlength="255"
                       class="w-full px-3 py-2 border {{ $errors->has('email') ? 'border-red-400' : 'border-gray-300 dark:border-gray-600' }} rounded-lg focus:outline-none focus:ring-2 focus:ring-gold-500 dark:bg-gray-700 dark:text-white">
                @error('email') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Role --}}
            <div>
                <label for="role_id" class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                    Role <span class="text-red-500">*</span>
                </label>
                {{--
                    FIX: $roles now always contains 'level' because getCachedOrderedRoles()
                    selects ['id', 'name', 'level'] — both createUser() and editUser()
                    use the same helper, so the cache key always holds the right shape.
                --}}
                <select id="role_id" name="role_id" required
                        class="w-full px-3 py-2 border {{ $errors->has('role_id') ? 'border-red-400' : 'border-gray-300 dark:border-gray-600' }} rounded-lg focus:outline-none focus:ring-2 focus:ring-gold-500 dark:bg-gray-700 dark:text-white">
                    @foreach ($roles as $role)
                        <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                            {{ $role->name }} (Level {{ $role->level }})
                        </option>
                    @endforeach
                </select>
                @error('role_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Position --}}
            <div>
                <label for="position" class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                    Position
                </label>
                <input type="text" id="position" name="position"
                       value="{{ old('position', $user->position) }}" maxlength="255"
                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-gold-500 dark:bg-gray-700 dark:text-white"
                       placeholder="e.g., President, Secretary, Auditor">
                @error('position') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Student ID & Year Level --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="student_id" class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                        Student ID
                    </label>
                    <input type="text" id="student_id" name="student_id"
                           value="{{ old('student_id', $user->student_id) }}" maxlength="20"
                           class="w-full px-3 py-2 border {{ $errors->has('student_id') ? 'border-red-400' : 'border-gray-300 dark:border-gray-600' }} rounded-lg focus:outline-none focus:ring-2 focus:ring-gold-500 dark:bg-gray-700 dark:text-white"
                           placeholder="YYYY-XXXXX">
                    @error('student_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="year_level" class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                        Year Level
                    </label>
                    <select id="year_level" name="year_level"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-gold-500 dark:bg-gray-700 dark:text-white">
                        <option value="">— Select Year Level —</option>
                        @foreach (['Grade 7', 'Grade 8', 'Grade 9', 'Grade 10', 'Grade 11', 'Grade 12'] as $grade)
                            <option value="{{ $grade }}" {{ old('year_level', $user->year_level) == $grade ? 'selected' : '' }}>
                                {{ $grade }}
                            </option>
                        @endforeach
                    </select>
                    @error('year_level') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            {{-- Gender --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                    Gender <span class="text-red-500">*</span>
                </label>
                <div class="flex flex-wrap gap-3">
                    @foreach (['Male', 'Female', 'Other'] as $gender)
                        <label class="flex items-center gap-2 px-3 py-2 border {{ old('gender', $user->gender) == $gender ? 'border-gold-400 bg-gold-50 dark:bg-gold-900/20' : 'border-gray-300 dark:border-gray-600' }} rounded-lg cursor-pointer hover:border-gold-400 transition">
                            <input type="radio" name="gender" value="{{ $gender }}"
                                   {{ old('gender', $user->gender) == $gender ? 'checked' : '' }}
                                   class="text-gold-500 focus:ring-gold-500">
                            <span class="text-sm text-gray-700 dark:text-gray-300">{{ $gender }}</span>
                        </label>
                    @endforeach
                </div>
                @error('gender') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Phone --}}
            <div>
                <label for="phone" class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                    Phone Number
                </label>
                <input type="text" id="phone" name="phone"
                       value="{{ old('phone', $user->phone) }}" maxlength="20"
                       class="w-full px-3 py-2 border {{ $errors->has('phone') ? 'border-red-400' : 'border-gray-300 dark:border-gray-600' }} rounded-lg focus:outline-none focus:ring-2 focus:ring-gold-500 dark:bg-gray-700 dark:text-white"
                       placeholder="+639123456789">
                @error('phone') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Birthday --}}
            <div>
                <label for="birthday" class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                    Birthday
                </label>
                <input type="date" id="birthday" name="birthday"
                       value="{{ old('birthday', $user->birthday?->format('Y-m-d')) }}"
                       class="w-full px-3 py-2 border {{ $errors->has('birthday') ? 'border-red-400' : 'border-gray-300 dark:border-gray-600' }} rounded-lg focus:outline-none focus:ring-2 focus:ring-gold-500 dark:bg-gray-700 dark:text-white">
                @error('birthday') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Account Status --}}
            <div>
                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                    Account Status
                </label>
                <div class="flex items-center gap-3">
                    <label class="flex items-center gap-2 px-3 py-2 border {{ old('is_active', $user->is_active) ? 'border-emerald-400 bg-emerald-50 dark:bg-emerald-900/20' : 'border-gray-300 dark:border-gray-600' }} rounded-lg cursor-pointer">
                        <input type="radio" name="is_active" value="1"
                               {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                               class="text-emerald-500 focus:ring-emerald-500">
                        <span class="text-sm text-emerald-700 dark:text-emerald-400">Active</span>
                    </label>
                    <label class="flex items-center gap-2 px-3 py-2 border {{ ! old('is_active', $user->is_active) ? 'border-red-400 bg-red-50 dark:bg-red-900/20' : 'border-gray-300 dark:border-gray-600' }} rounded-lg cursor-pointer">
                        <input type="radio" name="is_active" value="0"
                               {{ ! old('is_active', $user->is_active) ? 'checked' : '' }}
                               class="text-red-500 focus:ring-red-500">
                        <span class="text-sm text-red-700 dark:text-red-400">Inactive</span>
                    </label>
                </div>
                @error('is_active') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Change Password --}}
            <div class="p-4 bg-gray-50 dark:bg-gray-700/30 rounded-lg border border-gray-200 dark:border-gray-600">
                <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">Change Password</p>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">Leave blank to keep the current password.</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                            New Password
                        </label>
                        <input type="password" id="password" name="password" minlength="8"
                               class="w-full px-3 py-2 border {{ $errors->has('password') ? 'border-red-400' : 'border-gray-300 dark:border-gray-600' }} rounded-lg focus:outline-none focus:ring-2 focus:ring-gold-500 dark:bg-gray-700 dark:text-white"
                               placeholder="Minimum 8 characters">
                        @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                            Confirm Password
                        </label>
                        <input type="password" id="password_confirmation" name="password_confirmation" minlength="8"
                               class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-gold-500 dark:bg-gray-700 dark:text-white"
                               placeholder="Re-enter password">
                    </div>
                </div>
            </div>

            {{-- Buttons --}}
            <div class="flex items-center justify-between pt-4 border-t border-gray-100 dark:border-gold-800">
                <div class="flex gap-3">
                    {{--
                        The global JS guard in app.js handles double-submit protection:
                        it disables this button and inserts a spinner on first submit,
                        and re-enables it on pageshow (bfcache restore).
                        No Alpine wiring needed — keeping this as a plain button avoids
                        the scope isolation and innerHTML-destroy bugs in the old approach.
                    --}}
                    <button type="submit"
                            class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium rounded-lg bg-primary-600 hover:bg-gold-500 text-white transition-all duration-200 shadow-sm hover:shadow-md">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Update User
                    </button>
                    <a href="{{ route('admin.users.index') }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 text-sm font-medium rounded-lg bg-gray-500 hover:bg-gray-600 text-white transition-all duration-200 shadow-sm hover:shadow-md">
                        Cancel
                    </a>
                </div>
            </div>
        </form>
    </div>

    {{-- Danger Zone --}}
    <div class="mt-6 bg-white dark:bg-gray-800 rounded-2xl border border-red-200 dark:border-red-800 overflow-hidden shadow-sm">
        <div class="px-5 py-4 border-b border-red-100 dark:border-red-800 bg-red-50 dark:bg-red-900/20">
            <h3 class="text-sm font-semibold text-red-700 dark:text-red-400">Danger Zone</h3>
        </div>
        <div class="p-5 flex flex-wrap items-center justify-between gap-3">
            <div>
                <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Reset Password & Send Email</p>
                <p class="text-xs text-gray-500 dark:text-gray-400">Generate a new random password and email it to the user.</p>
            </div>
            {{--
                confirm() runs before submit fires, so the global JS guard will
                only lock this button if the user clicks OK — correct behaviour.
            --}}
            <form method="POST"
                  action="{{ route('admin.users.reset-password', $user->id) }}"
                  onsubmit="return confirm('Reset password for {{ addslashes($user->full_name) }}?\n\nA new password will be generated and emailed.');">
                @csrf
                <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-red-700 dark:text-red-400 border border-red-300 dark:border-red-700 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/30 transition">
                    Reset Password
                </button>
            </form>
        </div>
    </div>
</div>
@endsection