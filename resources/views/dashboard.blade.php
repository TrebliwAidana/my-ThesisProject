@extends('layouts.app')
@section('title', 'Dashboard — VSULHS_SSLG')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
    <p class="text-sm text-gray-500 mt-1">
        Welcome back, {{ $user->full_name ?? 'User' }}
    </p>
</div>

{{-- Top Stats Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">

    {{-- Role --}}
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Role</p>
        <span class="inline-block text-sm font-semibold px-3 py-1 rounded-full {{ $roleColor }}">
            {{ $roleName }}
        </span>
    </div>

    {{-- Status --}}
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Status</p>
        <span class="inline-block text-sm font-semibold px-3 py-1 rounded-full {{ $statusColor }}">
            {{ $statusDisplay }}
        </span>
    </div>

    {{-- Organization --}}
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Organization</p>
        <p class="text-xl font-bold text-gray-900">{{ $user->member?->organization ?? '—' }}</p>
    </div>

    {{-- Joined --}}
    <div class="bg-white rounded-xl border border-gray-200 p-5">
        <p class="text-xs font-semibold text-gray-400 uppercase tracking-widest mb-1">Joined</p>
        <p class="text-xl font-bold text-gray-900">{{ $joinedAt }}</p>
    </div>
</div>

{{-- Account Info --}}
<div class="bg-white rounded-xl border border-gray-200 p-6 mb-6">
    <h2 class="text-sm font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-100">Account Information</h2>
    <dl class="space-y-3 text-sm text-gray-500">
        <div class="flex justify-between">
            <dt>Full Name</dt>
            <dd class="font-medium text-gray-900">{{ $user->full_name ?? '—' }}</dd>
        </div>
        <div class="flex justify-between">
            <dt>Email</dt>
            <dd class="font-medium text-gray-900">{{ $user->email ?? '—' }}</dd>
        </div>
        <div class="flex justify-between">
            <dt>Member Since</dt>
            <dd class="font-medium text-gray-900">{{ $memberSince }}</dd>
        </div>
    </dl>
</div>
@endsection