@extends('layouts.app')

@section('title', 'Edit Member')
@section('page-title', 'Edit Member')

@php
    $nonStudentRoleIds = [1, 6, 8];
@endphp

@section('content')
<div x-data="memberEditForm()" x-init="init()" class="max-w-3xl mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gold-200 dark:border-gold-800 p-6">
        <form method="POST" action="{{ route('members.update', $member->id) }}">
            @csrf
            @method('PUT')

            @if($errors->any())
                <div class="mb-4 p-3 bg-red-100 border border-gold-300 dark:border-gold-700 text-red-700 rounded">
                    <strong>Please fix the following errors:</strong>
                    <ul class="mt-2 list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- First & Last Name --}}
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">First Name <span class="text-red-500">*</span></label>
                    <input type="text" name="first_name" value="{{ old('first_name', $member->first_name) }}" required
                           class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-transparent">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Last Name <span class="text-red-500">*</span></label>
                    <input type="text" name="last_name" value="{{ old('last_name', $member->last_name) }}" required
                           class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-transparent">
                </div>
            </div>

            {{-- Middle Name --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Middle Name <span class="text-gray-400">(Optional)</span></label>
                <input type="text" name="middle_name" value="{{ old('middle_name', $member->middle_name) }}"
                       class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-transparent">
            </div>

            {{-- Gender --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Gender <span class="text-red-500">*</span></label>
                <select name="gender" required
                        class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-transparent">
                    <option value="">Select Gender</option>
                    <option value="Male" {{ old('gender', $member->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                    <option value="Female" {{ old('gender', $member->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                    <option value="Other" {{ old('gender', $member->gender) == 'Other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>

            {{-- Birthday --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Birthday</label>
                <input type="date" name="birthday" value="{{ old('birthday', optional($member->birthday)->format('Y-m-d')) }}"
                       class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-transparent">
            </div>

            {{-- Phone --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Phone Number</label>
                <div class="flex">
                    <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gold-300 dark:border-gold-600 bg-gray-50 dark:bg-gray-700 text-gray-500">+63</span>
                    <input type="tel" name="phone" value="{{ old('phone', $member->phone ? substr($member->phone, 3) : '') }}" maxlength="10" placeholder="9123456789"
                           class="flex-1 border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-r-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-transparent"
                           oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0,10)">
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Enter 10-digit number (e.g., 9123456789). +63 will be added automatically.</p>
            </div>

            {{-- Email --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Email <span class="text-red-500">*</span></label>
                <input type="email" name="email" value="{{ old('email', $member->email) }}" required
                       class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-transparent">
            </div>

            {{-- Role --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Role <span class="text-red-500">*</span></label>
                <select name="role_id" x-model="selectedRoleId" required
                        class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-transparent">
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ old('role_id', $member->role_id) == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Position (dynamic) --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Position</label>
                <select name="position" x-model="selectedPosition"
                        class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-transparent">
                    <option value="">Select Position</option>
                    <template x-for="pos in positionOptions" :key="pos">
                        <option :value="pos" x-text="pos"></option>
                    </template>
                </select>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Positions are based on the selected role</p>
            </div>

            {{-- Reason for Position Change --}}
            <div x-show="positionChanged" x-cloak>
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Reason for Change <span class="text-red-500">*</span></label>
                    <textarea name="position_change_reason" rows="2"
                              class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-transparent"
                              placeholder="Please provide a reason for changing the position..."></textarea>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Required when changing position</p>
                </div>
            </div>

            {{-- Student ID --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Student ID</label>
                <input type="text" name="student_id" value="{{ old('student_id', $member->student_id) }}" placeholder="2020-12345"
                       class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-transparent">
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Format: YYYY-XXXXX (e.g., 2020-12345)</p>
            </div>

            {{-- Year Level (conditional) --}}
            <div x-show="isStudentRole" x-cloak>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Year Level</label>
                <select x-model="yearLevelValue"
                        class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-transparent">
                    <option value="">Select Grade</option>
                    @for($i=7; $i<=12; $i++)
                        <option value="Grade {{ $i }}">Grade {{ $i }}</option>
                    @endfor
                </select>
            </div>
            <input type="hidden" name="year_level" :value="yearLevelValue">
            <div x-show="!isStudentRole" class="text-sm text-gray-500 dark:text-gray-400 italic mb-4">
                Year level is not applicable for this role.
            </div>

            {{-- Change Password --}}
            <div class="mb-4">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" x-model="changePassword" class="rounded border-gold-300 text-gold-600 focus:ring-gold-500">
                    <span class="text-sm text-gray-700 dark:text-gray-300">Change Password</span>
                </label>
                <div x-show="changePassword" x-cloak>
                    <div class="mt-2">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">New Password</label>
                        <input type="password" name="password"
                               class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-transparent">
                    </div>
                    <div class="mt-2">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Confirm Password</label>
                        <input type="password" name="password_confirmation"
                               class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-transparent">
                    </div>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Leave blank to keep current password.</p>
            </div>

            {{-- Account Status --}}
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Account Status</label>
                <div class="flex gap-4">
                    <label class="inline-flex items-center gap-2">
                        <input type="radio" name="is_active" value="1" {{ old('is_active', $member->is_active) == 1 ? 'checked' : '' }} class="text-gold-600 focus:ring-gold-500">
                        <span>Active</span>
                    </label>
                    <label class="inline-flex items-center gap-2">
                        <input type="radio" name="is_active" value="0" {{ old('is_active', $member->is_active) == 0 ? 'checked' : '' }} class="text-gold-600 focus:ring-gold-500">
                        <span>Inactive</span>
                    </label>
                </div>
            </div>

            {{-- Buttons --}}
            <div class="flex gap-3">
                <button type="submit" class="flex-1 bg-primary-600 hover:bg-gold-500 text-white font-semibold py-2 px-4 rounded-lg transition shadow-sm hover:shadow-md">Update Member</button>
                <a href="{{ route('members.edit-history', $member->id) }}" class="flex-1 text-center bg-gray-100 hover:bg-gold-500 hover:text-white dark:bg-gray-700 dark:hover:bg-gold-600 text-gray-700 dark:text-gray-300 font-semibold py-2 px-4 rounded-lg transition">View History</a>
                <a href="{{ route('members.index') }}" class="flex-1 text-center bg-red-600 hover:bg-red-700 text-white font-semibold py-2 px-4 rounded-lg transition">Cancel</a>
            </div>
        </form>
    </div>
</div>

<style>[x-cloak] { display: none !important; }</style>

<script>
    // Hardcoded fallback mapping (same as Member::VALID_POSITIONS)
    const fallbackMapping = {
        1: [],
        2: ['SSLG President', 'SSLG Adviser', 'Student Affairs Head'],
        3: ['SSLG Secretary', 'SSLG Treasurer', 'SSLG PIO'],
        4: ['Organization President', 'Organization Vice President'],
        5: ['Organization Secretary', 'Organization Treasurer', 'Organization Auditor', 'Organization PIO'],
        6: ['Club Adviser'],
        7: ['Regular Member'],
        8: ['Guest'],
    };

    // Use validPositions from PHP if available, otherwise fallback
    let controllerMapping = @json(\App\Models\Member::VALID_POSITIONS);
    console.log('Controller mapping (edit):', controllerMapping);
    const validPositions = (Object.keys(controllerMapping).length > 0) ? controllerMapping : fallbackMapping;
    console.log('Final mapping (edit):', validPositions);

    const nonStudentRoleIds = @json($nonStudentRoleIds);
    console.log('Non-student role IDs (edit):', nonStudentRoleIds);

    function memberEditForm() {
        return {
            selectedRoleId: {{ old('role_id', $member->role_id) }},
            selectedPosition: '{{ old('position', $member->position) }}',
            positionOptions: [],
            changePassword: false,
            isStudentRole: true,
            yearLevelValue: '{{ old('year_level', $member->year_level) }}',
            init() {
                this.updatePositionOptions();
                this.checkIfStudentRole();
                this.$watch('selectedRoleId', () => {
                    console.log('Edit: role changed to', this.selectedRoleId);
                    this.updatePositionOptions();
                    this.checkIfStudentRole();
                });
                this.$watch('selectedPosition', () => {
                    console.log('Edit: position changed to', this.selectedPosition);
                    this.checkIfStudentRole();
                });
            },
            updatePositionOptions() {
                const roleId = parseInt(this.selectedRoleId);
                console.log('Edit: updatePositionOptions for roleId', roleId);
                if (roleId && validPositions[roleId]) {
                    this.positionOptions = validPositions[roleId];
                    console.log('Options found:', this.positionOptions);
                } else {
                    this.positionOptions = [];
                    console.warn('No options for roleId', roleId);
                }
                if (this.selectedPosition && !this.positionOptions.includes(this.selectedPosition)) {
                    this.selectedPosition = '';
                }
            },
            checkIfStudentRole() {
                const roleId = parseInt(this.selectedRoleId);
                const position = this.selectedPosition;
                console.log('Edit: checkIfStudentRole roleId', roleId, 'position', position);
                if (nonStudentRoleIds.includes(roleId)) {
                    this.isStudentRole = false;
                    this.yearLevelValue = '';
                    return;
                }
                if (roleId === 2) {
                    this.isStudentRole = (position === 'SSLG President');
                    if (!this.isStudentRole) this.yearLevelValue = '';
                    return;
                }
                this.isStudentRole = true;
            },
            positionChanged() {
                const originalPosition = '{{ $member->position }}';
                return this.selectedPosition && this.selectedPosition !== originalPosition;
            }
        };
    }
</script>
@endsection