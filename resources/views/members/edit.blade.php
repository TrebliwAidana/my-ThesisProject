@extends('layouts.app')
@section('title', 'Edit Member — VSULHS_SSLG')

@section('content')

<div class="mb-6">
    <a href="{{ route('members.index') }}" class="text-sm text-gray-500 hover:text-gray-800 transition">← Back to Members</a>
    <h1 class="text-2xl font-bold text-gray-900 mt-2">Edit Member</h1>
    <p class="text-sm text-gray-500 mt-1">{{ $member->user->full_name }}</p>
</div>

<div class="bg-white rounded-xl border border-gray-200 p-6 max-w-xl">
    <form method="POST" action="{{ route('members.update', $member->id) }}">
        @csrf @method('PUT')

        {{-- Organization --}}
        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Organization</label>
            <input type="text" name="organization" value="{{ old('organization', $member->organization) }}" required
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-black {{ $errors->has('organization') ? 'border-red-400' : '' }}">
            @error('organization') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Status --}}
        <div class="mb-4">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Status</label>
            <select name="status" required
                    class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-black">
                @foreach (['active', 'inactive', 'suspended'] as $s)
                    <option value="{{ $s }}" {{ old('status', $member->status) === $s ? 'selected' : '' }}>
                        {{ ucfirst($s) }}
                    </option>
                @endforeach
            </select>
            @error('status') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- Joined At --}}
        <div class="mb-6">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Date Joined</label>
            <input type="date" name="joined_at" value="{{ old('joined_at', $member->joined_at->format('Y-m-d')) }}" required
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-black {{ $errors->has('joined_at') ? 'border-red-400' : '' }}">
            @error('joined_at') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex gap-3">
            <button type="submit"
                    class="bg-black text-white text-sm font-semibold px-5 py-2 rounded-lg hover:bg-gray-800 transition">
                Save Changes
            </button>
            <a href="{{ route('members.index') }}"
               class="text-sm font-semibold text-gray-600 border border-gray-300 px-5 py-2 rounded-lg hover:bg-gray-50 transition">
                Cancel
            </a>
        </div>
    </form>
</div>

@endsection
