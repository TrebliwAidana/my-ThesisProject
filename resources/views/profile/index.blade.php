@extends('layouts.app')

@section('title', 'My Profile — VSULHS_SSLG')

@section('content')

{{-- Unified Centered Notifications using Uniform Component --}}
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

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">My Profile</h1>
    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage your personal information and password</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Profile Information Card --}}
    <div class="lg:col-span-2">
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gold-200 dark:border-gold-800 overflow-hidden">
            <div class="px-6 py-4 border-b border-gold-200 dark:border-gold-800">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-primary-100 dark:bg-primary-900/50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="font-semibold text-gray-900 dark:text-white">Profile Information</h2>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Update your account information</p>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('profile.update') }}" class="p-6">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Full Name</label>
                    <input type="text" name="full_name" value="{{ old('full_name', $user->full_name) }}" required
                           class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-transparent transition">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Email Address</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                           class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-transparent transition">
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Role</label>
                        <input type="text" value="{{ $user->role->name }}" disabled
                               class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 text-sm bg-gray-100 dark:bg-gray-600 cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Position</label>
                        <input type="text" value="{{ $user->position ?? '—' }}" disabled
                               class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 text-sm bg-gray-100 dark:bg-gray-600 cursor-not-allowed">
                    </div>
                </div>

                <div class="pt-4 border-t border-gold-200 dark:border-gold-800">
                    <button type="submit"
                            class="bg-primary-600 hover:bg-gold-500 text-white text-sm font-semibold px-6 py-2.5 rounded-lg transition shadow-sm hover:shadow">
                        Update Profile
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Change Password Card --}}
    <div class="lg:col-span-1">
        <div class="bg-white dark:bg-gray-800 rounded-xl border border-gold-200 dark:border-gold-800 overflow-hidden sticky top-6">
            <div class="px-6 py-4 border-b border-gold-200 dark:border-gold-800">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-green-100 dark:bg-green-900/50 flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
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
                        <input type="password" name="current_password" id="current_password"
                               class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-transparent transition">
                        <button type="button" onclick="togglePassword('current_password')"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <svg id="eye_current" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </button>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Required to change password</p>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">New Password</label>
                    <div class="relative">
                        <input type="password" name="new_password" id="new_password"
                               class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-transparent transition"
                               oninput="checkPasswordMatch()">
                        <button type="button" onclick="togglePassword('new_password')"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <svg id="eye_new" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </button>
                    </div>
                    <div id="passwordWarning" class="hidden mt-1 text-xs text-yellow-600 dark:text-yellow-400 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <span>Password should be different from your current password</span>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Minimum 8 characters</p>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Confirm New Password</label>
                    <div class="relative">
                        <input type="password" name="new_password_confirmation" id="confirm_password"
                               class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-transparent transition">
                        <button type="button" onclick="togglePassword('confirm_password')"
                                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <svg id="eye_confirm" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit"
                        class="w-full bg-gray-600 hover:bg-gray-700 text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition">
                    Change Password
                </button>
            </form>

            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/30 border-t border-gold-200 dark:border-gold-800">
                <div class="flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Password must be at least 8 characters long and different from current password
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Member Information Card --}}
<div class="mt-6">
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gold-200 dark:border-gold-800 overflow-hidden">
        <div class="px-6 py-4 border-b border-gold-200 dark:border-gold-800">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-gold-100 dark:bg-gold-900/50 flex items-center justify-center">
                    <svg class="w-5 h-5 text-gold-600 dark:text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <div>
                    <h2 class="font-semibold text-gray-900 dark:text-white">Member Information</h2>
                    <p class="text-xs text-gray-500 dark:text-gray-400">Additional membership details</p>
                </div>
            </div>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
                            {{ optional($user->member->term_start)->format('M d, Y') }} - 
                            {{ optional($user->member->term_end)->format('M d, Y') ?? 'Present' }}
                        @else
                            —
                        @endif
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
    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const type = field.getAttribute('type') === 'password' ? 'text' : 'password';
        field.setAttribute('type', type);
        
        const eyeIcon = document.querySelector(`#eye_${fieldId.split('_')[1]}`);
        if (eyeIcon) {
            if (type === 'text') {
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.05 10.05 0 013.125-4.125m4.542-1.042A9.977 9.977 0 0112 5c4.478 0 8.268 2.943 9.542 7a9.977 9.977 0 01-3.125 4.125m-4.542 1.042L3 3l18 18"></path>
                `;
            } else {
                eyeIcon.innerHTML = `
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                `;
            }
        }
    }
    
    function checkPasswordMatch() {
        const currentPwd = document.querySelector('input[name="current_password"]').value;
        const newPwd = document.getElementById('new_password').value;
        const warningDiv = document.getElementById('passwordWarning');
        
        if (currentPwd && newPwd && currentPwd === newPwd) {
            warningDiv.classList.remove('hidden');
        } else {
            warningDiv.classList.add('hidden');
        }
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('passwordForm');
        
        if (form) {
            form.addEventListener('submit', function(e) {
                const currentPwd = document.querySelector('input[name="current_password"]').value;
                const newPwd = document.getElementById('new_password').value;
                
                if (currentPwd && newPwd && currentPwd === newPwd) {
                    e.preventDefault();
                    alert('⚠️ Password Change Failed\n\nNew password cannot be the same as your current password.\n\nPlease choose a different password.');
                    document.getElementById('new_password').classList.add('border-red-500', 'ring-2', 'ring-red-500');
                    document.getElementById('new_password').focus();
                    return false;
                }
            });
        }
    });
</script>

@endsection