@extends('layouts.app')

@section('title', 'Members — VSULHS_SSLG')

@section('content')

{{-- Alert Messages with Enhanced Styling --}}
@if(session('success'))
    <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border-l-4 border-green-500 dark:border-green-400 rounded-r-lg shadow-sm">
        <div class="flex items-center gap-3">
            <div class="flex-shrink-0">
                <svg class="w-5 h-5 text-green-500 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="text-sm text-green-800 dark:text-green-300">
                {!! session('success') !!}
            </div>
        </div>
    </div>
@endif

@if(session('error'))
    <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 dark:border-red-400 rounded-r-lg shadow-sm">
        <div class="flex items-center gap-3">
            <div class="flex-shrink-0">
                <svg class="w-5 h-5 text-red-500 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <div class="text-sm text-red-800 dark:text-red-300">
                {!! session('error') !!}
            </div>
        </div>
    </div>
@endif

{{-- Header with Enhanced Styling --}}
<div class="mb-8">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 dark:from-white dark:to-gray-300 bg-clip-text text-transparent">
                Members
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 flex items-center gap-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                Manage organization members
            </p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3">
            <!-- Enhanced Search Bar -->
            <div class="relative group">
                <input type="text" 
                       id="searchMembers" 
                       placeholder="Search members..." 
                       class="w-full sm:w-80 pl-11 pr-11 py-2.5 text-sm border border-gray-200 dark:border-gray-700 rounded-xl focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent dark:bg-gray-800 dark:text-gray-200 transition-all duration-200 bg-gray-50 dark:bg-gray-800/50 hover:bg-white dark:hover:bg-gray-800">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                    <svg class="h-4.5 w-4.5 text-gray-400 dark:text-gray-500 group-focus-within:text-indigo-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <button id="clearSearch" class="absolute inset-y-0 right-0 pr-3.5 flex items-center hidden group">
                    <svg class="h-4.5 w-4.5 text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <!-- Enhanced Add Member Button -->
            <a href="{{ route('members.create') }}" 
               class="inline-flex items-center justify-center gap-2 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-all duration-200 shadow-md hover:shadow-lg transform hover:scale-[1.02]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add Member
            </a>
        </div>
    </div>
</div>

{{-- Enhanced Members Table Card --}}
<div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800 border-b border-gray-200 dark:border-gray-700">
                    <th class="text-left px-6 py-4 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Member</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Email</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Role</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Position</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Status</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Member Since</th>
                    <th class="text-left px-6 py-4 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Term</th>
                    <th class="text-right px-6 py-4 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700" id="membersTableBody">
                @forelse ($members as $member)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-all duration-150 member-row group">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-100 to-indigo-200 dark:from-indigo-900/50 dark:to-indigo-800/50 flex items-center justify-center text-sm font-bold text-indigo-700 dark:text-indigo-300 shadow-sm">
                                {{ strtoupper(substr($member->user->full_name, 0, 2)) }}
                            </div>
                            <div>
                                <p class="font-semibold text-gray-900 dark:text-white member-name">{{ $member->user->full_name }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $member->user->email }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-gray-600 dark:text-gray-400 member-email text-sm">{{ $member->user->email }}</td>
                    <td class="px-6 py-4 member-role">
                        @php
                            $roleColors = [
                                'Adviser' => 'bg-purple-100 dark:bg-purple-900/50 text-purple-700 dark:text-purple-300',
                                'Officer' => 'bg-blue-100 dark:bg-blue-900/50 text-blue-700 dark:text-blue-300',
                                'Auditor' => 'bg-amber-100 dark:bg-amber-900/50 text-amber-700 dark:text-amber-300',
                                'Member' => 'bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-300',
                            ];
                            $roleColor = $roleColors[$member->user->role->name] ?? 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300';
                        @endphp
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $roleColor }}">
                            {{ $member->user->role->name }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-gray-600 dark:text-gray-400 member-position text-sm font-medium">{{ $member->position ?? '—' }}</td>
                    <td class="px-6 py-4 member-status">
                        @if ($member->isActive())
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-300">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span>
                                Active
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">
                                <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                                Inactive
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-gray-500 dark:text-gray-400 member-joined text-sm">
                        {{ optional($member->joined_at)->format('M d, Y') ?? optional($member->term_start)->format('M d, Y') ?? 'N/A' }}
                    </td>
                    <td class="px-6 py-4 text-gray-500 dark:text-gray-400 member-term text-xs">
                        {{ optional($member->term_start)->format('M d, Y') }} - 
                        {{ optional($member->term_end)->format('M d, Y') ?? 'Present' }}
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('members.edit', $member->id) }}" 
                               class="p-1.5 text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400 transition-colors rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700" 
                               title="Edit member">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </a>
                            <form method="POST" action="{{ route('members.destroy', $member->id) }}" 
                                  onsubmit="return confirmDelete('{{ $member->user->full_name }}', '{{ $member->user->role->name }}')"
                                  class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="p-1.5 text-gray-500 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-400 transition-colors rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
                                        title="Remove member">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-12 text-center">
                        <div class="flex flex-col items-center gap-3">
                            <svg class="w-12 h-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <p class="text-gray-500 dark:text-gray-400 text-sm italic">No members found.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($members->hasPages())
    <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
        {{ $members->links() }}
    </div>
    @endif
    
    {{-- Search results count --}}
    <div id="searchCount" class="px-6 py-3 text-xs text-gray-500 dark:text-gray-400 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50 hidden">
        <div class="flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            Showing <span id="visibleCount" class="font-semibold">0</span> of <span id="totalCount" class="font-semibold">0</span> members
        </div>
    </div>
</div>

<script>
    function confirmDelete(userName, userRole) {
        if (userRole === 'Adviser') {
            return confirm(`⚠️⚠️⚠️ WARNING ⚠️⚠️⚠️\n\nYou are about to delete ${userName}, who is an ADVISER.\n\n⚠️ Make sure this is NOT the last adviser in the system!\n\n⚠️ Deleting the last adviser will lock you out of admin features.\n\nAre you absolutely sure you want to continue?`);
        } else {
            return confirm(`⚠️ You are about to delete ${userName}.\n\nThis action cannot be undone.\n\nAre you sure you want to delete this ${userRole}?`);
        }
    }
    
    // Search functionality
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchMembers');
        const clearButton = document.getElementById('clearSearch');
        const rows = document.querySelectorAll('.member-row');
        const searchCountDiv = document.getElementById('searchCount');
        const visibleCountSpan = document.getElementById('visibleCount');
        const totalCountSpan = document.getElementById('totalCount');
        
        // Set total count
        if (totalCountSpan) {
            totalCountSpan.textContent = rows.length;
        }
        
        // Clear button functionality
        if (clearButton) {
            clearButton.addEventListener('click', function() {
                searchInput.value = '';
                searchInput.dispatchEvent(new Event('input'));
                clearButton.classList.add('hidden');
                searchInput.focus();
            });
        }
        
        function performSearch() {
            const query = searchInput.value.toLowerCase().trim();
            let visibleCount = 0;
            
            // Show/hide clear button
            if (clearButton) {
                if (query.length > 0) {
                    clearButton.classList.remove('hidden');
                } else {
                    clearButton.classList.add('hidden');
                }
            }
            
            rows.forEach(row => {
                const name = row.querySelector('.member-name')?.textContent.toLowerCase() || '';
                const email = row.querySelector('.member-email')?.textContent.toLowerCase() || '';
                const role = row.querySelector('.member-role')?.textContent.toLowerCase() || '';
                const position = row.querySelector('.member-position')?.textContent.toLowerCase() || '';
                const status = row.querySelector('.member-status')?.textContent.toLowerCase() || '';
                const joined = row.querySelector('.member-joined')?.textContent.toLowerCase() || '';
                const term = row.querySelector('.member-term')?.textContent.toLowerCase() || '';
                
                const searchableText = `${name} ${email} ${role} ${position} ${status} ${joined} ${term}`;
                
                if (query === '' || searchableText.includes(query)) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });
            
            // Update search count display
            if (searchCountDiv && visibleCountSpan) {
                if (query !== '') {
                    visibleCountSpan.textContent = visibleCount;
                    searchCountDiv.classList.remove('hidden');
                } else {
                    searchCountDiv.classList.add('hidden');
                }
            }
            
            // Show "no results" message if needed
            const tbody = document.getElementById('membersTableBody');
            const existingNoResults = tbody.querySelector('.no-results-row');
            
            if (visibleCount === 0 && rows.length > 0) {
                if (!existingNoResults) {
                    const noResultsTr = document.createElement('tr');
                    noResultsTr.className = 'no-results-row';
                    noResultsTr.innerHTML = `
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <svg class="w-12 h-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                                <p class="text-gray-500 dark:text-gray-400 text-sm italic">No members match your search.</p>
                            </div>
                        </td>
                    `;
                    tbody.appendChild(noResultsTr);
                }
            } else {
                if (existingNoResults) {
                    existingNoResults.remove();
                }
            }
        }
        
        // Add event listener for search input
        searchInput.addEventListener('input', performSearch);
        
        // Initial setup
        performSearch();
    });
</script>

@endsection