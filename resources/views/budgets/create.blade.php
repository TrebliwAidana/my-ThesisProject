@extends('layouts.app')

@section('title', 'Create User — VSULHS_SSLG')
@section('page-title', 'Create New User')

@php
    $validPositions = \App\Models\Member::VALID_POSITIONS;
@endphp

@section('content')
<div x-data="userCreateForm()" x-init="init()" class="max-w-3xl mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gold-800 p-6">
        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf

            {{-- First Name & Last Name --}}
            <div class="grid grid-cols-2 gap-4">
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        First Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="first_name" value="{{ old('first_name') }}" required
                           class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Last Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="last_name" value="{{ old('last_name') }}" required
                           class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                </div>
            </div>

            {{-- Middle Name (Optional) --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Middle Name <span class="text-gray-400">(Optional)</span>
                </label>
                <input type="text" name="middle_name" value="{{ old('middle_name') }}"
                       class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
            </div>

            {{-- Email --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">A verification email will be sent to this address.</p>
            </div>

            {{-- Student ID --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Student ID</label>
                <input type="text" name="student_id" value="{{ old('student_id') }}"
                       class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500"
                       placeholder="2020-12345">
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Format: YYYY-XXXXX (e.g., 2020-12345)</p>
            </div>

            {{-- Year Level --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Year Level</label>
                <select name="year_level" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm">
                    <option value="">Select Year Level</option>
                    <option value="Grade 7">Grade 7</option>
                    <option value="Grade 8">Grade 8</option>
                    <option value="Grade 9">Grade 9</option>
                    <option value="Grade 10">Grade 10</option>
                    <option value="Grade 11">Grade 11</option>
                    <option value="Grade 12">Grade 12</option>
                </select>
            </div>

            {{-- Gender --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Gender <span class="text-red-500">*</span></label>
                <select name="gender" required class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm">
                    <option value="">Select Gender</option>
                    <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                    <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                    <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>

            {{-- Phone --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Phone Number</label>
                <div class="flex">
                    <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gray-300 dark:border-gray-600 bg-gray-50 dark:bg-gray-700 text-gray-500">+63</span>
                    <input type="tel" name="phone" value="{{ old('phone') }}" maxlength="10" placeholder="9123456789"
                           class="flex-1 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-r-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500"
                           oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,10)">
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Enter 10-digit number (e.g., 9123456789). The country code (+63) will be added automatically.</p>
            </div>

            {{-- Birthday --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Birthday</label>
                <input type="date" name="birthday" value="{{ old('birthday') }}"
                       class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500">
            </div>

            {{-- Role Selection --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Role <span class="text-red-500">*</span></label>
                <select name="role_id" x-model="selectedRoleId" required
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm">
                    <option value="">Select a role</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ old('role_id') == $role->id ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Position Dropdown (dynamic) --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Position</label>
                <select name="position" x-model="selectedPosition"
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm">
                    <option value="">Select Position</option>
                    <template x-for="pos in positionOptions" :key="pos">
                        <option :value="pos" x-text="pos"></option>
                    </template>
                </select>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Positions are based on the selected role</p>
            </div>

            {{-- Password (optional) --}}
            <div class="mb-4">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" x-model="setPassword" class="rounded border-gray-300 text-primary-600">
                    <span class="text-sm text-gray-700 dark:text-gray-300">Set a custom password (optional)</span>
                </label>
                <div x-show="setPassword" x-cloak>
                    <div class="mt-2">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Password</label>
                        <input type="password" name="password" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm">
                    </div>
                    <div class="mt-2">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Confirm Password</label>
                        <input type="password" name="password_confirmation" class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm">
                    </div>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">If left blank, a random password will be generated and emailed.</p>
            </div>

            {{-- Account Status --}}
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Account Status</label>
                <div class="flex gap-4">
                    <label class="flex items-center gap-2">
                        <input type="radio" name="is_active" value="1" {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                        <span>Active</span>
                    </label>
                    <label class="flex items-center gap-2">
                        <input type="radio" name="is_active" value="0" {{ old('is_active') == '0' ? 'checked' : '' }}>
                        <span>Inactive</span>
                    </label>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Inactive users cannot log in.</p>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-primary-600 hover:bg-gold-500 text-white font-semibold py-2 px-4 rounded-lg transition">Create User</button>
                <a href="{{ route('admin.users.index') }}" class="flex-1 text-center bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 font-semibold py-2 px-4 rounded-lg transition">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
    const validPositions = @json(\App\Models\Member::VALID_POSITIONS);

    function userCreateForm() {
        return {
            selectedRoleId: {{ old('role_id', 'null') }},
            selectedPosition: '{{ old('position') }}',
            positionOptions: [],
            setPassword: false,
            init() {
                this.updatePositionOptions();
                this.$watch('selectedRoleId', () => this.updatePositionOptions());
            },
            updatePositionOptions() {
                const roleId = this.selectedRoleId;
                if (roleId && validPositions[roleId]) {
                    this.positionOptions = validPositions[roleId];
                } else {
                    this.positionOptions = [];
                }
                if (this.selectedPosition && !this.positionOptions.includes(this.selectedPosition)) {
                    this.selectedPosition = '';
                }
            }
        };
    }
</script>
@endsection