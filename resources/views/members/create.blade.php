@extends('layouts.app')

@section('title', 'Create Member')
@section('page-title', 'Create New Member')

@php
    $validPositions = \App\Models\Member::VALID_POSITIONS;
    $nonStudentRoleIds = [1, 6, 8];
@endphp

@section('content')
{{-- Emerald Gradient Header --}}
<div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-primary-600 to-primary-700 dark:from-primary-800 dark:to-primary-900 p-6 mb-6">
    <div class="relative z-10">
        <h1 class="text-2xl font-bold text-white">Create New Member</h1>
        <p class="text-primary-100 text-sm mt-1">Add a new member to your organization</p>
    </div>
    <div class="absolute top-0 right-0 -mt-8 -mr-8 w-64 h-64 bg-white/5 rounded-full blur-3xl"></div>
</div>

<div x-data="memberCreateForm()" x-init="init()" class="max-w-3xl mx-auto">
    <!-- rest of the form (same as before, but ensure the header is removed from inside) -->
    ...
</div>

<script>
    // same script as before
</script>
@endsection