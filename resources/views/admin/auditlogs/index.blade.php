@extends('layouts.app')

@section('title', 'Audit Logs')
@section('page-title', 'Audit Logs')

@section('content')
<div class="space-y-4">
    {{-- Filter Card (optional, you can add filters later) --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gold-200 dark:border-gold-800 p-4 shadow-sm">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
            <div>
                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">Event</label>
                <select name="event"
                        class="px-3 py-1.5 border border-gold-300 dark:border-gold-600 rounded-lg text-sm dark:bg-gray-700 dark:text-white">
                    <option value="">All Events</option>
                    @foreach($events as $event)
                        <option value="{{ $event }}" {{ request('event') == $event ? 'selected' : '' }}>{{ ucfirst($event) }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">User</label>
                <input type="text" name="user" value="{{ request('user') }}" placeholder="User name"
                       class="px-3 py-1.5 border border-gold-300 dark:border-gold-600 rounded-lg text-sm dark:bg-gray-700 dark:text-white">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">From</label>
                <input type="date" name="date_from" value="{{ request('date_from') }}"
                       class="px-3 py-1.5 border border-gold-300 dark:border-gold-600 rounded-lg text-sm dark:bg-gray-700 dark:text-white">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-500 dark:text-gray-400 mb-1">To</label>
                <input type="date" name="date_to" value="{{ request('date_to') }}"
                       class="px-3 py-1.5 border border-gold-300 dark:border-gold-600 rounded-lg text-sm dark:bg-gray-700 dark:text-white">
            </div>
            <div>
                <button type="submit"
                        class="bg-emerald-600 hover:bg-gold-500 text-white px-4 py-1.5 rounded-lg text-sm transition">
                    Filter
                </button>
                @if(request()->anyFilled(['event', 'user', 'date_from', 'date_to']))
                    <a href="{{ route('admin.auditlogs.index') }}"
                       class="bg-gray-500 hover:bg-gold-500 text-white px-4 py-1.5 rounded-lg text-sm ml-2 transition">
                        Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Audit Logs Table --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gold-200 dark:border-gold-800 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-emerald-600 dark:bg-emerald-800 text-white">
                    <tr>
                        <th class="px-5 py-3 text-left">User</th>
                        <th class="px-5 py-3 text-left">Event</th>
                        <th class="px-5 py-3 text-left">Description</th>
                        <th class="px-5 py-3 text-left">IP Address</th>
                        <th class="px-5 py-3 text-left">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($logs as $log)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-5 py-3">{{ $log->user_name ?? ($log->user->full_name ?? 'System') }}</td>
                        <td class="px-5 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700">
                                {{ ucfirst($log->event) }}
                            </span>
                        </td>
                        <td class="px-5 py-3">{{ $log->description }}</td>
                        <td class="px-5 py-3 text-gray-500 dark:text-gray-400">{{ $log->ip_address ?? '—' }}</td>
                        <td class="px-5 py-3 text-gray-500 dark:text-gray-400">{{ $log->created_at->format('M d, Y H:i') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-5 py-8 text-center text-gray-500">No audit logs found.</td>
                    </tr>
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
</div>
@endsection