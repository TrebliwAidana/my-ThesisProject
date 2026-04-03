@extends('layouts.app')

@section('title', 'My Profile — VSULHS_SSLG')

@section('content')

{{-- Notifications --}}
@if(session('success') || session('password_success') || $errors->any())
    <div class="fixed top-4 left-1/2 transform -translate-x-1/2 z-50 w-full max-w-md pointer-events-none">
        <div class="pointer-events-auto space-y-2">
            @if(session('success'))
                <x-notification type="success" title="Profile Updated!" message="{{ session('success') }}" />
            @endif
            @if(session('password_success'))
                <x-notification type="success" title="Password Changed!" message="{{ session('password_success') }}" />
            @endif
            @if($errors->any())
                <x-notification type="error" title="Validation Error">
                    <ul class="text-sm text-red-700 dark:text-red-300 mt-0.5 list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </x-notification>
            @endif
        </div>
    </div>
@endif

{{-- Header --}}
<div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-emerald-600 to-emerald-700 dark:from-emerald-800 dark:to-emerald-900 p-6 mb-6">
    <div class="relative z-10 flex items-center gap-5">
        <div class="relative flex-shrink-0">
            <div id="headerAvatarWrap" class="w-16 h-16 rounded-full overflow-hidden ring-2 ring-white/30 bg-emerald-500 flex items-center justify-center">
                @if($user->avatar)
                    <img id="headerAvatar" src="{{ Storage::url($user->avatar) }}" alt="Avatar" class="w-full h-full object-cover">
                @else
                    <span id="headerInitials" class="text-white text-xl font-bold select-none">
                        {{ strtoupper(substr($user->first_name ?? $user->full_name, 0, 1)) }}{{ strtoupper(substr($user->last_name ?? '', 0, 1)) }}
                    </span>
                @endif
            </div>
        </div>
        <div>
            <h1 class="text-2xl font-bold text-white">{{ $user->full_name }}</h1>
            <p class="text-emerald-100 text-sm mt-0.5">
                {{ $user->role->name ?? '' }}
                @if($user->position) · {{ $user->position }} @endif
            </p>
            @if($user->last_login_at)
                <p class="text-emerald-200/70 text-xs mt-1">Last login: {{ $user->last_login_at->format('M d, Y H:i') }}</p>
            @endif
        </div>
    </div>
    <div class="absolute top-0 right-0 -mt-8 -mr-8 w-64 h-64 bg-white/5 rounded-full blur-3xl"></div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- ── Profile Information (full edit) ─────────────────────────── --}}
    <div class="lg:col-span-2 space-y-6">

        {{-- Avatar Upload Card (unchanged) --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-emerald-200 dark:border-emerald-800 overflow-hidden">
            <div class="px-6 py-4 border-b border-emerald-200 dark:border-emerald-800">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-900/50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-semibold text-gray-900 dark:text-white">Profile Photo</h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400">JPG, PNG, GIF, WebP — max 2MB</p>
                    </div>
                </div>
            </div>
            <div class="p-6 flex items-center gap-6">
                <div class="relative flex-shrink-0">
                    <div id="avatarPreviewWrap" class="w-20 h-20 rounded-full overflow-hidden ring-2 ring-emerald-200 dark:ring-emerald-700 bg-emerald-100 dark:bg-emerald-900 flex items-center justify-center">
                        @if($user->avatar)
                            <img id="avatarPreview" src="{{ Storage::url($user->avatar) }}" alt="Avatar" class="w-full h-full object-cover">
                        @else
                            <span id="avatarInitials" class="text-emerald-700 dark:text-emerald-300 text-2xl font-bold select-none">
                                {{ strtoupper(substr($user->first_name ?? $user->full_name, 0, 1)) }}{{ strtoupper(substr($user->last_name ?? '', 0, 1)) }}
                            </span>
                        @endif
                    </div>
                </div>
                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data" id="avatarForm" class="flex-1">
                    @csrf
                    @method('PUT')
                    {{-- Keep other fields as hidden so they are not lost --}}
                    <input type="hidden" name="first_name" value="{{ old('first_name', $user->first_name) }}">
                    <input type="hidden" name="middle_name" value="{{ old('middle_name', $user->middle_name) }}">
                    <input type="hidden" name="last_name" value="{{ old('last_name', $user->last_name) }}">
                    <input type="hidden" name="gender" value="{{ old('gender', $user->gender) }}">
                    <input type="hidden" name="birthday" value="{{ old('birthday', optional($user->birthday)->format('Y-m-d')) }}">
                    <input type="hidden" name="phone" value="{{ old('phone', $user->phone ? substr($user->phone, 3) : '') }}">
                    <input type="hidden" name="student_id" value="{{ old('student_id', $user->student_id) }}">
                    <input type="hidden" name="year_level" value="{{ old('year_level', $user->year_level) }}">
                    <input type="hidden" name="email" value="{{ $user->email }}">
                    <div class="flex flex-col gap-2">
                        <label for="avatarInput"
                               class="cursor-pointer inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-semibold px-4 py-2 rounded-lg transition w-fit">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                            Upload Photo
                        </label>
                        <input type="file" id="avatarInput" name="avatar" accept="image/*" class="hidden">
                        <p id="avatarFileName" class="text-xs text-gray-400 dark:text-gray-500">No file chosen</p>
                        <button type="submit" id="avatarSubmitBtn"
                                class="hidden items-center gap-2 bg-gray-700 hover:bg-gray-600 text-white text-sm font-semibold px-4 py-2 rounded-lg transition w-fit">
                            Save Photo
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Profile Information Card (with all editable fields) --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-emerald-200 dark:border-emerald-800 overflow-hidden">
            <div class="px-6 py-4 border-b border-emerald-200 dark:border-emerald-800">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-900/50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-semibold text-gray-900 dark:text-white">Profile Information</h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Update your personal details</p>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('profile.update') }}" class="p-6" id="profileForm">
                @csrf
                @method('PUT')

                {{-- First Name & Last Name (2 cols) --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">First Name <span class="text-red-500">*</span></label>
                        <input type="text" name="first_name" value="{{ old('first_name', $user->first_name) }}" required
                               class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Last Name <span class="text-red-500">*</span></label>
                        <input type="text" name="last_name" value="{{ old('last_name', $user->last_name) }}" required
                               class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition">
                    </div>
                </div>

                {{-- Middle Name (optional) --}}
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Middle Name <span class="text-gray-400">(Optional)</span></label>
                    <input type="text" name="middle_name" value="{{ old('middle_name', $user->middle_name) }}"
                           class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition">
                </div>

                {{-- Gender --}}
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Gender <span class="text-red-500">*</span></label>
                    <select name="gender" required class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition">
                        <option value="">Select Gender</option>
                        <option value="Male" {{ old('gender', $user->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('gender', $user->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                        <option value="Other" {{ old('gender', $user->gender) == 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                {{-- Birthday --}}
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Birthday</label>
                    <input type="date" name="birthday" value="{{ old('birthday', optional($user->birthday)->format('Y-m-d')) }}"
                           class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition">
                </div>

                {{-- Phone Number --}}
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Phone Number</label>
                    <div class="flex">
                        <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-500">+63</span>
                        <input type="tel" name="phone" value="{{ old('phone', $user->phone ? substr($user->phone, 3) : '') }}" maxlength="10" placeholder="9123456789"
                               class="flex-1 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-r-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition"
                               oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,10)">
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Enter 10-digit number (e.g., 9123456789). +63 will be added automatically.</p>
                </div>

                {{-- Email (read‑only? Actually editable but we keep as editable) --}}
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Email Address <span class="text-red-500">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                           class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition">
                </div>

                {{-- Student ID (editable by user, but unique) --}}
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Student ID</label>
                    <input type="text" name="student_id" value="{{ old('student_id', $user->student_id) }}" placeholder="2020-12345"
                           class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Format: YYYY-XXXXX (e.g., 2020-12345)</p>
                </div>

                {{-- Year Level (conditional – uses same logic as edit member) --}}
                @php
                    $nonStudentRoleIds = [1, 6, 8];
                    $roleId = $user->role_id;
                    $position = $user->position;
                    $isStudentRole = !in_array($roleId, $nonStudentRoleIds);
                    if ($roleId == 2) {
                        $isStudentRole = ($position === 'SSLG President');
                    }
                @endphp

                <div class="mb-4" x-data="{ isStudentRole: {{ $isStudentRole ? 'true' : 'false' }} }">
                    <div x-show="isStudentRole" x-cloak>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Year Level</label>
                        <select name="year_level" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition">
                            <option value="">Select Grade</option>
                            <option value="Grade 7" {{ old('year_level', $user->year_level) == 'Grade 7' ? 'selected' : '' }}>Grade 7</option>
                            <option value="Grade 8" {{ old('year_level', $user->year_level) == 'Grade 8' ? 'selected' : '' }}>Grade 8</option>
                            <option value="Grade 9" {{ old('year_level', $user->year_level) == 'Grade 9' ? 'selected' : '' }}>Grade 9</option>
                            <option value="Grade 10" {{ old('year_level', $user->year_level) == 'Grade 10' ? 'selected' : '' }}>Grade 10</option>
                            <option value="Grade 11" {{ old('year_level', $user->year_level) == 'Grade 11' ? 'selected' : '' }}>Grade 11</option>
                            <option value="Grade 12" {{ old('year_level', $user->year_level) == 'Grade 12' ? 'selected' : '' }}>Grade 12</option>
                        </select>
                    </div>
                    <div x-show="!isStudentRole" class="text-sm text-gray-500 dark:text-gray-400 italic">
                        Year level is not applicable for your role.
                    </div>
                </div>

                {{-- Role & Position (read‑only) --}}
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Role</label>
                        <input type="text" value="{{ $user->role->name }}" disabled
                               class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2.5 text-sm bg-gray-100 dark:bg-gray-600 dark:text-white cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Position</label>
                        <input type="text" value="{{ $user->position ?? '—' }}" disabled
                               class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-4 py-2.5 text-sm bg-gray-100 dark:bg-gray-600 dark:text-white cursor-not-allowed">
                    </div>
                </div>

                <div class="pt-4 border-t border-emerald-200 dark:border-emerald-800 flex gap-3">
                    <button type="submit"
                            class="bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-semibold px-6 py-2.5 rounded-lg transition shadow-sm hover:shadow">
                        Update Profile
                    </button>
                    <a href="{{ route('dashboard') }}"
                       class="bg-red-600 hover:bg-red-700 text-white text-sm font-semibold px-6 py-2.5 rounded-lg transition shadow-sm hover:shadow">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- ── Change Password Card (unchanged) ───────────────────────── --}}
    <div class="lg:col-span-1">
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-emerald-200 dark:border-emerald-800 overflow-hidden sticky top-6">
            <div class="px-6 py-4 border-b border-emerald-200 dark:border-emerald-800">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-green-100 dark:bg-green-900/50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-semibold text-gray-900 dark:text-white">Change Password</h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Update your password</p>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('profile.password') }}" class="p-6" id="passwordForm">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Current Password</label>
                    <div class="relative">
                        <input type="password" name="current_password" id="field_current"
                               class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition"
                               oninput="checkPasswordMatch()">
                        <button type="button" onclick="togglePwd('current')"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <svg id="eye_current" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    @error('current_password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Required to change password</p>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">New Password</label>
                    <div class="relative">
                        <input type="password" name="new_password" id="field_new"
                               class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition"
                               oninput="checkPasswordStrength(); checkPasswordMatch()">
                        <button type="button" onclick="togglePwd('new')"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <svg id="eye_new" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    <div id="strengthWrap" class="hidden mt-2">
                        <div class="flex gap-1 mb-1">
                            <div id="bar1" class="h-1 flex-1 rounded-full bg-gray-200 dark:bg-gray-600"></div>
                            <div id="bar2" class="h-1 flex-1 rounded-full bg-gray-200 dark:bg-gray-600"></div>
                            <div id="bar3" class="h-1 flex-1 rounded-full bg-gray-200 dark:bg-gray-600"></div>
                            <div id="bar4" class="h-1 flex-1 rounded-full bg-gray-200 dark:bg-gray-600"></div>
                        </div>
                        <p id="strengthLabel" class="text-xs text-gray-500"></p>
                    </div>
                    <div id="samePasswordWarning" class="hidden mt-1 text-xs text-yellow-600 dark:text-yellow-400 flex items-center gap-1">
                        <svg class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Must be different from your current password</span>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Minimum 8 characters</p>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Confirm New Password</label>
                    <div class="relative">
                        <input type="password" name="new_password_confirmation" id="field_confirm"
                               class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition"
                               oninput="checkConfirmMatch()">
                        <button type="button" onclick="togglePwd('confirm')"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <svg id="eye_confirm" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                    <div id="matchIndicator" class="hidden mt-1 text-xs flex items-center gap-1">
                        <svg id="matchIcon" class="w-3 h-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"></svg>
                        <span id="matchText"></span>
                    </div>
                </div>

                <button type="submit"
                        class="w-full bg-gray-600 hover:bg-gray-700 text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition">
                    Change Password
                </button>
            </form>

            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/30 border-t border-emerald-200 dark:border-emerald-800">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        At least 8 characters, different from current password
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Member Information Card (unchanged) --}}
<div class="mt-6">
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-emerald-200 dark:border-emerald-800 overflow-hidden">
        <div class="px-6 py-4 border-b border-emerald-200 dark:border-emerald-800">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-emerald-100 dark:bg-emerald-900/50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                </div>
                <div>
                    <h2 class="font-semibold text-gray-900 dark:text-white">Member Information</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Additional membership details</p>
                </div>
            </div>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Member Since</label>
                    <p class="text-gray-900 dark:text-white mt-1 font-medium">
                        {{ optional($user->member?->joined_at ?? $user->member?->term_start)->format('F d, Y') ?? '—' }}
                    </p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Current Term</label>
                    <p class="text-gray-900 dark:text-white mt-1 font-medium">
                        @if($user->member)
                            {{ optional($user->member->term_start)->format('M d, Y') }} –
                            {{ optional($user->member->term_end)->format('M d, Y') ?? 'Present' }}
                        @else
                            —
                        @endif
                    </p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Last Login</label>
                    <p class="text-gray-900 dark:text-white mt-1 font-medium">
                        {{ $user->last_login_at ? $user->last_login_at->format('M d, Y H:i') : '—' }}
                    </p>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Last Updated</label>
                    <p class="text-gray-900 dark:text-white mt-1 font-medium">{{ optional($user->updated_at)->format('F d, Y') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Avatar preview (unchanged)
document.getElementById('avatarInput').addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;
    document.getElementById('avatarFileName').textContent = file.name;
    document.getElementById('avatarSubmitBtn').classList.remove('hidden');
    document.getElementById('avatarSubmitBtn').classList.add('inline-flex');
    const reader = new FileReader();
    reader.onload = function (e) {
        const src = e.target.result;
        const wrap = document.getElementById('avatarPreviewWrap');
        let img = document.getElementById('avatarPreview');
        if (!img) {
            img = document.createElement('img');
            img.id = 'avatarPreview';
            img.className = 'w-full h-full object-cover';
            wrap.innerHTML = '';
            wrap.appendChild(img);
        }
        img.src = src;
        const headerWrap = document.getElementById('headerAvatarWrap');
        let headerImg = document.getElementById('headerAvatar');
        if (!headerImg) {
            headerImg = document.createElement('img');
            headerImg.id = 'headerAvatar';
            headerImg.className = 'w-full h-full object-cover';
            headerWrap.innerHTML = '';
            headerWrap.appendChild(headerImg);
        }
        headerImg.src = src;
    };
    reader.readAsDataURL(file);
});

// Eye toggle (unchanged)
const eyePaths = {
    show: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.05 10.05 0 013.125-4.125m4.542-1.042A9.977 9.977 0 0112 5c4.478 0 8.268 2.943 9.542 7a9.977 9.977 0 01-3.125 4.125m-4.542 1.042L3 3l18 18"/>`,
    hide: `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>`
};

function togglePwd(alias) {
    const field = document.getElementById('field_' + alias);
    const eye   = document.getElementById('eye_' + alias);
    if (!field || !eye) return;
    const showing = field.type === 'text';
    field.type    = showing ? 'password' : 'text';
    eye.innerHTML = showing ? eyePaths.hide : eyePaths.show;
}

// Password strength
function checkPasswordStrength() {
    const val = document.getElementById('field_new').value;
    const wrap = document.getElementById('strengthWrap');
    if (!val) { wrap.classList.add('hidden'); return; }
    wrap.classList.remove('hidden');
    let score = 0;
    if (val.length >= 8)              score++;
    if (val.length >= 12)             score++;
    if (/[A-Z]/.test(val) && /[a-z]/.test(val)) score++;
    if (/[0-9]/.test(val))            score++;
    if (/[^A-Za-z0-9]/.test(val))     score++;
    const level = Math.min(4, Math.max(1, score));
    const configs = [null,
        { label: 'Weak',      color: 'bg-red-400' },
        { label: 'Fair',      color: 'bg-yellow-400' },
        { label: 'Good',      color: 'bg-blue-400' },
        { label: 'Strong',    color: 'bg-emerald-500' },
    ];
    const cfg = configs[level];
    for (let i = 1; i <= 4; i++) {
        const bar = document.getElementById('bar' + i);
        bar.className = 'h-1 flex-1 rounded-full transition-colors duration-300 ' +
            (i <= level ? cfg.color : 'bg-gray-200 dark:bg-gray-600');
    }
    document.getElementById('strengthLabel').textContent = cfg.label;
    document.getElementById('strengthLabel').className = 'text-xs ' + (level === 1 ? 'text-red-500' : level === 2 ? 'text-yellow-500' : level === 3 ? 'text-blue-500' : 'text-emerald-600');
}

function checkPasswordMatch() {
    const cur = document.getElementById('field_current').value;
    const nw  = document.getElementById('field_new').value;
    const warn = document.getElementById('samePasswordWarning');
    (cur && nw && cur === nw) ? warn.classList.remove('hidden') : warn.classList.add('hidden');
}

function checkConfirmMatch() {
    const nw  = document.getElementById('field_new').value;
    const cfm = document.getElementById('field_confirm').value;
    const indicator = document.getElementById('matchIndicator');
    const icon      = document.getElementById('matchIcon');
    const text      = document.getElementById('matchText');
    if (!cfm) { indicator.classList.add('hidden'); return; }
    indicator.classList.remove('hidden');
    if (nw === cfm) {
        indicator.className = 'mt-1 text-xs flex items-center gap-1 text-emerald-600 dark:text-emerald-400';
        icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>`;
        text.textContent = 'Passwords match';
    } else {
        indicator.className = 'mt-1 text-xs flex items-center gap-1 text-red-500';
        icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/>`;
        text.textContent = 'Passwords do not match';
    }
}

document.getElementById('passwordForm').addEventListener('submit', function (e) {
    const cur = document.getElementById('field_current').value;
    const nw  = document.getElementById('field_new').value;
    if (cur && nw && cur === nw) {
        e.preventDefault();
        document.getElementById('field_new').classList.add('border-red-500', 'ring-2', 'ring-red-500');
        document.getElementById('field_new').focus();
    }
});
</script>

@endsection