@extends('layouts.app')

@section('title', 'Manage Roles')

@section('content')
<div class="space-y-6">
    
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Roles Management</h1>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Manage system roles and their permissions</p>
        </div>
        <a href="{{ route('admin.roles.create') }}" 
           class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold px-4 py-2 rounded-lg transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-[1.02]">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Create New Role
        </a>
    </div>

    {{-- Roles Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gray-100 dark:border-gray-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700 dark:to-gray-800 border-b border-gray-200 dark:border-gray-700">
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">ID</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Role</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Abbreviation</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Description</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Level</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Users</th>
                        <th class="text-left px-6 py-4 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Created</th>
                        <th class="text-right px-6 py-4 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($roles as $role)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-all duration-150 group">
                        <td class="px-6 py-4 text-gray-500 dark:text-gray-400 text-sm">#{{ $role->id }}</td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-indigo-100 to-indigo-200 dark:from-indigo-900/50 dark:to-indigo-800/50 flex items-center justify-center text-xs font-bold text-indigo-700 dark:text-indigo-300">
                                    {{ strtoupper(substr($role->name, 0, 2)) }}
                                </div>
                                <span class="font-medium text-gray-900 dark:text-white">{{ $role->name }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($role->abbreviation)
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-mono bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400">
                                    {{ $role->abbreviation }}
                                </span>
                            @else
                                <span class="text-gray-400 dark:text-gray-500 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-gray-500 dark:text-gray-400 text-sm max-w-xs">
                            {{ Str::limit($role->desc ?? 'No description', 50) }}
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium bg-{{ $role->level <= 2 ? 'purple' : ($role->level <= 4 ? 'blue' : 'gray') }}-100 dark:bg-{{ $role->level <= 2 ? 'purple' : ($role->level <= 4 ? 'blue' : 'gray') }}-900/50 text-{{ $role->level <= 2 ? 'purple' : ($role->level <= 4 ? 'blue' : 'gray') }}-700 dark:text-{{ $role->level <= 2 ? 'purple' : ($role->level <= 4 ? 'blue' : 'gray') }}-300">
                                <span class="w-1.5 h-1.5 rounded-full bg-current"></span>
                                Level {{ $role->level }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 text-sm font-semibold">
                                {{ $role->users_count ?? $role->users->count() }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-gray-500 dark:text-gray-400 text-sm">
                            {{ $role->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                @if(!in_array($role->name, ['System Administrator', 'Supreme Admin', 'Supreme Officer', 'Org Admin', 'Org Officer', 'Adviser', 'Org Member', 'Guest']))
                                    <a href="{{ route('admin.roles.edit', $role) }}" 
                                       class="p-1.5 text-gray-500 hover:text-indigo-600 dark:text-gray-400 dark:hover:text-indigo-400 transition-colors rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700" 
                                       title="Edit role">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    <button type="button" 
                                            onclick="confirmDelete({{ $role->id }}, '{{ $role->name }}')" 
                                            class="p-1.5 text-gray-500 hover:text-red-600 dark:text-gray-400 dark:hover:text-red-400 transition-colors rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
                                            title="Delete role">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                @else
                                    <span class="text-xs text-gray-400 dark:text-gray-500 italic" title="System role cannot be edited or deleted">
                                        System Role
                                    </span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <svg class="w-12 h-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                <p class="text-gray-500 dark:text-gray-400 text-sm italic">No roles found.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if(isset($roles) && method_exists($roles, 'links'))
            <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                {{ $roles->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function confirmDelete(roleId, roleName) {
    const defaultRoles = ['System Administrator', 'Supreme Admin', 'Supreme Officer', 'Org Admin', 'Org Officer', 'Adviser', 'Org Member', 'Guest'];
    
    if (defaultRoles.includes(roleName)) {
        alert('Cannot delete system roles. These are required for the system to function properly.');
        return;
    }
    
    if (confirm(`⚠️ Are you sure you want to delete the role "${roleName}"?\n\nThis action cannot be undone. Users with this role will need to be reassigned.`)) {
        document.getElementById('delete-form-' + roleId).submit();
    }
}
</script>
@endpush

@foreach($roles as $role)
<form id="delete-form-{{ $role->id }}" action="{{ route('admin.roles.destroy', $role) }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endforeach
@endsection