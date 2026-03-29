@extends('layouts.app')

@section('title', 'Edit Member — VSULHS_SSLG')

@section('content')
<div x-data="editMemberComponent()" x-init="init()">
    
    <div class="mb-6">
        <a href="{{ route('members.index') }}" class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300 transition">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Members
        </a>
        
        <div class="flex justify-between items-start mt-3">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Member</h1>
                @if(isset($user))
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $user->full_name }}</p>
                @elseif($member && $member->user)
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $member->user->full_name }}</p>
                @endif
            </div>
            
            <button @click="openHistoryModal()" 
                    class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                View History
            </button>
        </div>
        
        @if($member && $member->position_changed_at)
            <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                Last change: {{ $member->position_changed_at->diffForHumans() }}
                @if($member->positionChangedBy) by {{ $member->positionChangedBy->name }} @endif
            </div>
        @endif
    </div>

    @if(isset($user) && $user)
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 p-6 max-w-2xl mx-auto">
        {{-- FIXED: Changed action to use $user->id instead of $member->id --}}
        <form method="POST" action="{{ route('members.update', $user->id) }}" id="editMemberForm">
            @csrf 
            @method('PUT')

            {{-- Role Selection --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Role</label>
                <select name="role_id" id="role_id" required
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    @php
                        $adviserCount = \App\Models\User::whereHas('role', function($q) {
                            $q->where('name', 'Adviser');
                        })->count();
                        $isLastAdviser = ($user->role->name === 'Adviser' && $adviserCount <= 1);
                        $currentRoleId = old('role_id', $user->role_id);
                    @endphp
                    
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" data-role-name="{{ $role->name }}" 
                            {{ $currentRoleId == $role->id ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
                
                @if($isLastAdviser)
                    <input type="hidden" name="role_id" value="{{ $user->role_id }}">
                    <p class="mt-1 text-xs text-amber-600">Role is locked - last adviser in the system.</p>
                @endif
            </div>

            {{-- Position with Dynamic Options --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Position</label>
                <select name="position" id="position" required
                        class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <!-- Options will be populated by JavaScript -->
                </select>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Positions are based on the selected role</p>
            </div>

            {{-- Position Change Preview --}}
            <div id="positionChangePreview" class="mb-4 hidden">
                <div class="p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg">
                    <p class="text-sm text-blue-700 dark:text-blue-400">
                        Position will change from <strong id="originalPositionDisplay"></strong> 
                        to <strong id="newPositionDisplay"></strong>
                    </p>
                </div>
            </div>

            {{-- Position Change Reason --}}
            <div id="positionChangeReasonDiv" class="mb-4 hidden">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    Reason for Change <span class="text-red-500">*</span>
                </label>
                <textarea name="position_change_reason" id="position_change_reason" rows="3"
                          class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                          placeholder="Please provide a reason for changing this member's position..."></textarea>
            </div>

            {{-- Member Since --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Member Since</label>
                <input type="date" name="joined_at" value="{{ old('joined_at', optional($member->joined_at)->format('Y-m-d')) }}"
                       class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            {{-- Term Start --}}
            <div class="mb-4">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Term Start</label>
                <input type="date" name="term_start" value="{{ old('term_start', optional($member->term_start)->format('Y-m-d')) }}"
                       class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            {{-- Term End --}}
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Term End</label>
                <input type="date" name="term_end" value="{{ old('term_end', optional($member->term_end)->format('Y-m-d')) }}"
                       class="w-full border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <p class="text-xs text-gray-500 mt-1">Leave empty for ongoing term.</p>
            </div>

            {{-- Confirmation Checkbox --}}
            <div id="confirmationDiv" class="mb-6 hidden">
                <div class="flex items-start gap-3 p-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg">
                    <input type="checkbox" name="confirm_change" id="confirm_change"
                           class="mt-0.5 w-4 h-4 text-amber-600 rounded">
                    <label for="confirm_change" class="text-sm text-gray-700 dark:text-gray-300">
                        I confirm this position change is appropriate
                    </label>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit" id="submitBtn"
                        class="flex-1 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-5 py-2.5 rounded-lg transition">
                    Update Member
                </button>
                <a href="{{ route('members.index') }}"
                   class="flex-1 text-center text-sm font-semibold text-gray-600 dark:text-gray-400 border border-gray-300 dark:border-gray-600 px-5 py-2.5 rounded-lg hover:bg-gray-50 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>
    
    @elseif($member && $member->user_id)
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 max-w-2xl mx-auto">
        <div class="text-center">
            <svg class="w-16 h-16 mx-auto text-red-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
            </svg>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">User Record Missing</h3>
            <p class="text-gray-600 dark:text-gray-400 mb-4">
                This member record is linked to a user that no longer exists.
            </p>
            <a href="{{ route('members.index') }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition">
                Back to Members
            </a>
        </div>
    </div>
    
    @else
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 text-center">
        <p class="text-gray-500">Invalid member record.</p>
        <a href="{{ route('members.index') }}" class="mt-4 inline-block text-indigo-600">Back to Members</a>
    </div>
    @endif
</div>

<script>
function editMemberComponent() {
    return {
        memberId: {{ $member->id ?? 0 }},
        originalPosition: '{{ $member->position ?? $user->position ?? '' }}',
        currentPosition: '{{ $member->position ?? $user->position ?? '' }}',
        
        init() {
            // Initialize position dropdown
            this.updatePositionDropdown();
            
            // Add event listener for role change
            const roleSelect = document.getElementById('role_id');
            if (roleSelect) {
                roleSelect.addEventListener('change', () => this.updatePositionDropdown());
            }
            
            // Add event listener for position change
            const positionSelect = document.getElementById('position');
            if (positionSelect) {
                positionSelect.addEventListener('change', () => this.checkPositionChange());
            }
            
            // Set original position display
            const originalDisplay = document.getElementById('originalPositionDisplay');
            if (originalDisplay && this.originalPosition) {
                originalDisplay.textContent = this.originalPosition;
            }
        },
        
        updatePositionDropdown() {
            const roleSelect = document.getElementById('role_id');
            const positionSelect = document.getElementById('position');
            
            if (!roleSelect || !positionSelect) return;
            
            // Get selected role name from the data attribute
            const selectedOption = roleSelect.options[roleSelect.selectedIndex];
            const roleName = selectedOption ? selectedOption.getAttribute('data-role-name') || selectedOption.textContent : '';
            
            // Define positions based on role
            let positions = [];
            const roleLower = (roleName || '').toLowerCase();
            
            if (roleLower === 'adviser') {
                positions = ['Adviser'];
            } else if (roleLower === 'officer') {
                positions = ['President', 'Secretary', 'Treasurer', 'Auditor'];
            } else if (roleLower === 'auditor') {
                positions = ['Auditor'];
            } else if (roleLower === 'member') {
                positions = ['Member'];
            } else {
                positions = ['Member'];
            }
            
            // Clear and populate position dropdown
            positionSelect.innerHTML = '';
            positions.forEach(pos => {
                const option = document.createElement('option');
                option.value = pos;
                option.textContent = pos;
                if (pos === this.currentPosition) {
                    option.selected = true;
                }
                positionSelect.appendChild(option);
            });
            
            // If current position is not in new role's positions, select first available
            if (!positions.includes(this.currentPosition) && positions.length > 0) {
                this.currentPosition = positions[0];
                positionSelect.value = this.currentPosition;
            }
            
            // Check position change after update
            this.checkPositionChange();
        },
        
        checkPositionChange() {
            const positionSelect = document.getElementById('position');
            const previewDiv = document.getElementById('positionChangePreview');
            const reasonDiv = document.getElementById('positionChangeReasonDiv');
            const confirmationDiv = document.getElementById('confirmationDiv');
            const newPositionDisplay = document.getElementById('newPositionDisplay');
            const reasonTextarea = document.getElementById('position_change_reason');
            const confirmCheckbox = document.getElementById('confirm_change');
            
            if (!positionSelect) return;
            
            const newPosition = positionSelect.value;
            
            if (newPosition !== this.originalPosition) {
                if (previewDiv) previewDiv.classList.remove('hidden');
                if (reasonDiv) reasonDiv.classList.remove('hidden');
                if (confirmationDiv) confirmationDiv.classList.remove('hidden');
                if (newPositionDisplay) newPositionDisplay.textContent = newPosition;
                if (reasonTextarea) reasonTextarea.required = true;
                if (confirmCheckbox) confirmCheckbox.required = true;
            } else {
                if (previewDiv) previewDiv.classList.add('hidden');
                if (reasonDiv) reasonDiv.classList.add('hidden');
                if (confirmationDiv) confirmationDiv.classList.add('hidden');
                if (reasonTextarea) reasonTextarea.required = false;
                if (confirmCheckbox) confirmCheckbox.required = false;
            }
        },
        
        openHistoryModal() {
            if (this.memberId) {
                window.location.href = `/members/${this.memberId}/position-history`;
            }
        }
    }
}

// Form validation
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('editMemberForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const positionSelect = document.getElementById('position');
            const originalPosition = '{{ $member->position ?? $user->position ?? '' }}';
            const reasonTextarea = document.getElementById('position_change_reason');
            const confirmCheckbox = document.getElementById('confirm_change');
            
            if (positionSelect && positionSelect.value !== originalPosition) {
                if (!reasonTextarea.value.trim()) {
                    e.preventDefault();
                    alert('Please provide a reason for changing the position.');
                    reasonTextarea.focus();
                    return false;
                }
                
                if (!confirmCheckbox.checked) {
                    e.preventDefault();
                    alert('Please confirm that this position change is appropriate.');
                    confirmCheckbox.focus();
                    return false;
                }
            }
            return true;
        });
    }
});
</script>
@endsection