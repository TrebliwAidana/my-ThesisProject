@extends('layouts.app')

@section('title', $organization->name . ' — VSULHS_SSLG')
@section('page-title', 'Organization Detail')

@section('content')
@php $user = auth()->user(); @endphp

{{-- Header --}}
<div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-primary-600 to-primary-700 dark:from-primary-800 dark:to-primary-900 p-6 mb-6">
    <div class="relative z-10 flex items-start justify-between flex-wrap gap-3">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-white/10 flex items-center justify-center text-white text-xl font-bold flex-shrink-0">
                {{ strtoupper(substr($organization->abbreviation ?? $organization->name, 0, 2)) }}
            </div>
            <div>
                <h1 class="text-2xl font-bold text-white">{{ $organization->name }}</h1>
                <div class="flex items-center gap-2 mt-1 flex-wrap">
                    @if($organization->abbreviation)
                    <span class="text-primary-200 text-sm font-mono">{{ $organization->abbreviation }}</span>
                    @endif
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-white/10 text-white">{{ $organization->type_label }}</span>
                    @if($organization->academic_year)
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-white/10 text-white">{{ $organization->academic_year }}</span>
                    @endif
                    @if($organization->is_active)
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-green-400/20 text-green-200 border border-green-400/30">Active</span>
                    @else
                    <span class="px-2 py-0.5 rounded-full text-xs font-medium bg-gray-400/20 text-gray-200 border border-gray-400/30">Inactive</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="flex items-center gap-2">
            @if($user->role->level === 1 || $user->organization_id === $organization->id)
            <a href="{{ route('admin.organizations.edit', $organization) }}"
               class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 text-white text-sm font-medium px-4 py-2 rounded-xl transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Edit
            </a>
            @endif
            <a href="{{ route('admin.organizations.index') }}"
               class="inline-flex items-center gap-2 bg-white/10 hover:bg-white/20 text-white text-sm font-medium px-4 py-2 rounded-xl transition">
                ← Back
            </a>
        </div>
    </div>
    <div class="absolute top-0 right-0 -mt-8 -mr-8 w-64 h-64 bg-white/5 rounded-full blur-3xl pointer-events-none"></div>
</div>

{{-- Stats Row --}}
<div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gold-200 dark:border-gold-800 p-5 shadow-sm text-center">
        <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $organization->users->count() }}</p>
        <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mt-1">Members</p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gold-200 dark:border-gold-800 p-5 shadow-sm text-center">
        <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $documentCount }}</p>
        <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mt-1">Documents</p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gold-200 dark:border-gold-800 p-5 shadow-sm text-center">
        <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $budgetSummary['pending'] }}</p>
        <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mt-1">Pending Budgets</p>
    </div>
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gold-200 dark:border-gold-800 p-5 shadow-sm text-center">
        <p class="text-xl font-bold text-gray-800 dark:text-white">₱{{ number_format($budgetSummary['approved'], 2) }}</p>
        <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mt-1">Approved Budget</p>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

    {{-- Left: Info + Members by Role --}}
    <div class="space-y-5">

        {{-- Info Card --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gold-200 dark:border-gold-800 shadow-sm p-5">
            <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-4">Details</h3>
            <dl class="space-y-3 text-sm">
                @if($organization->description)
                <div>
                    <dt class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Description</dt>
                    <dd class="mt-1 text-gray-700 dark:text-gray-300">{{ $organization->description }}</dd>
                </div>
                @endif
                <div>
                    <dt class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Adviser</dt>
                    <dd class="mt-1 text-gray-700 dark:text-gray-300">{{ $organization->adviser?->full_name ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Type</dt>
                    <dd class="mt-1 text-gray-700 dark:text-gray-300">{{ $organization->type_label }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Academic Year</dt>
                    <dd class="mt-1 text-gray-700 dark:text-gray-300">{{ $organization->academic_year ?? '—' }}</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Created</dt>
                    <dd class="mt-1 text-gray-700 dark:text-gray-300">{{ $organization->created_at->format('M d, Y') }}</dd>
                </div>
            </dl>
        </div>

        {{-- Members by Role --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gold-200 dark:border-gold-800 shadow-sm p-5">
            <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider mb-4">Members by Role</h3>
            @forelse($membersByRole as $roleName => $members)
            <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700 last:border-0">
                <span class="text-sm text-gray-700 dark:text-gray-300">{{ $roleName }}</span>
                <span class="text-sm font-bold text-gray-800 dark:text-white">{{ $members->count() }}</span>
            </div>
            @empty
            <p class="text-sm text-gray-400">No members yet.</p>
            @endforelse
        </div>
    </div>

    {{-- Right: Recent Docs + Recent Budgets --}}
    <div class="lg:col-span-2 space-y-5">

        {{-- Recent Documents --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gold-200 dark:border-gold-800 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-5 py-3.5 bg-primary-600 dark:bg-primary-700">
                <h3 class="text-sm font-bold text-white">Recent Documents</h3>
                <a href="{{ route('documents.index') }}" class="text-xs text-primary-100 hover:text-white transition">View all →</a>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($recentDocs as $doc)
                <div class="flex items-center justify-between px-5 py-3">
                    <div class="min-w-0">
                        <a href="{{ route('documents.show', $doc) }}" class="text-sm font-medium text-primary-600 dark:text-primary-400 hover:underline truncate block max-w-xs">
                            {{ $doc->title }}
                        </a>
                        <p class="text-xs text-gray-400 mt-0.5">{{ $doc->created_at->format('M d, Y') }}</p>
                    </div>
                    <span class="text-xs text-gray-400 font-mono flex-shrink-0 ml-3">{{ $doc->formatted_size }}</span>
                </div>
                @empty
                <p class="px-5 py-4 text-sm text-gray-400">No documents yet.</p>
                @endforelse
            </div>
        </div>

        {{-- Recent Budgets --}}
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gold-200 dark:border-gold-800 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-5 py-3.5 bg-primary-600 dark:bg-primary-700">
                <h3 class="text-sm font-bold text-white">Recent Budgets</h3>
                <a href="{{ route('budgets.index') }}" class="text-xs text-primary-100 hover:text-white transition">View all →</a>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($recentBudgets as $budget)
                @php
                    $statusColors = [
                        'draft'     => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
                        'pending'   => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300',
                        'reviewed'  => 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300',
                        'approved'  => 'bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300',
                        'rejected'  => 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300',
                        'disbursed' => 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300',
                    ];
                @endphp
                <div class="flex items-center justify-between px-5 py-3">
                    <div class="min-w-0">
                        <a href="{{ route('budgets.show', $budget) }}" class="text-sm font-medium text-primary-600 dark:text-primary-400 hover:underline truncate block max-w-xs">
                            {{ $budget->title }}
                        </a>
                        <p class="text-xs text-gray-400 mt-0.5">₱{{ number_format($budget->amount, 2) }}</p>
                    </div>
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$budget->status] ?? '' }} flex-shrink-0 ml-3">
                        {{ ucfirst($budget->status) }}
                    </span>
                </div>
                @empty
                <p class="px-5 py-4 text-sm text-gray-400">No budget requests yet.</p>
                @endforelse
            </div>
        </div>

    </div>
</div>
@endsection