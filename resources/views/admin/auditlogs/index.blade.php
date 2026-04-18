@extends('layouts.app')

@section('title', 'Audit Logs')
@section('page-title', 'Audit Logs')

@section('content')
<div class="bg-white dark:bg-gray-800 rounded-xl border border-gold-200 dark:border-gold-800 overflow-hidden shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-emerald-600 dark:bg-emerald-800 text-white">
                <tr>
                    <th class="px-5 py-3 text-left">User</th>
                    <th class="px-5 py-3 text-left">Action</th>
                    <th class="px-5 py-3 text-left">Description</th>
                    <th class="px-5 py-3 text-left">IP Address</th>
                    <th class="px-5 py-3 text-left">Date</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($logs as $log)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                    <td class="px-5 py-3">{{ $log->user->full_name ?? 'System' }}</td>
                    <td class="px-5 py-3">{{ $log->action }}</td>
                    <td class="px-5 py-3">{{ $log->description }}</td>
                    <td class="px-5 py-3">{{ $log->ip_address }}</td>
                    <td class="px-5 py-3">{{ $log->created_at->format('M d, Y H:i') }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-5 py-8 text-center text-gray-500">No audit logs found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
    <div class="p-4 border-t border-gray-100 dark:border-gray-700">
        {{ $logs->links() }}
    </div>
    @endif
</div>
@endsection