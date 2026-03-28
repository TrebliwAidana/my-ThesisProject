@extends('layouts.app')

@section('title', 'Members — VSULHS_SSLG')

@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Members</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage organization members</p>
    </div>
    @if(in_array(Auth::user()->role->name, ['Adviser', 'Officer']))
    <a href="{{ route('members.create') }}" 
       class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition">
        + Add Member
    </a>
    @endif
</div>

<div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                <tr>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Name</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Email</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Role</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Position</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Status</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Member Since</th>
                    <th class="text-left px-5 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Term</th>
                    <th class="text-right px-5 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700" id="membersTableBody">
                @forelse($members as $member)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition member-row">
                    <td class="px-5 py-3 font-medium text-gray-900 dark:text-white member-name">{{ $member->user->full_name }}</td>
                    <td class="px-5 py-3 text-gray-600 dark:text-gray-400 member-email">{{ $member->user->email }}</td>
                    <td class="px-5 py-3 member-role">
                        <span class="bg-indigo-100 dark:bg-indigo-900/50 text-indigo-700 dark:text-indigo-400 text-xs font-bold px-2 py-0.5 rounded-full">
                            {{ $member->user->role->name }}
                        </span>
                    </td>
                    <td class="px-5 py-3 text-gray-600 dark:text-gray-400 member-position">{{ $member->position ?? '—' }}</td>
                    <td class="px-5 py-3 member-status">
                        @if ($member->isActive())
                            <span class="bg-green-100 dark:bg-green-900/50 text-green-800 dark:text-green-400 text-xs font-semibold px-2 py-0.5 rounded-full">Active</span>
                        @else
                            <span class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-400 text-xs font-semibold px-2 py-0.5 rounded-full">Inactive</span>
                        @endif
                    </td>
                    <td class="px-5 py-3 text-gray-600 dark:text-gray-400 member-joined">
                        {{ optional($member->joined_at)->format('M d, Y') ?? optional($member->term_start)->format('M d, Y') ?? 'N/A' }}
                    </td>
                    <td class="px-5 py-3 text-gray-600 dark:text-gray-400 text-xs member-term">
                        {{ optional($member->term_start)->format('M d, Y') }} - 
                        {{ optional($member->term_end)->format('M d, Y') ?? 'Present' }}
                    </td>
                    <td class="px-5 py-3 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('members.edit', $member->id) }}" class="text-xs font-medium text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 px-3 py-1 rounded hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                                Edit
                            </a>
                            <form method="POST" action="{{ route('members.destroy', $member->id) }}" 
                                  onsubmit="return confirmDelete('{{ $member->user->full_name }}', '{{ $member->user->role->name }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="text-xs font-medium text-red-600 dark:text-red-400 border border-red-200 dark:border-red-800 px-3 py-1 rounded hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                                    Remove
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-5 py-10 text-center text-gray-400 dark:text-gray-500 text-sm italic">No members found.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($members->hasPages())
    <div class="px-5 py-3 border-t border-gray-100 dark:border-gray-700">
        {{ $members->links() }}
    </div>
    @endif
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
        }
        
        searchInput.addEventListener('input', performSearch);
        performSearch();
    });
</script>

@endsection