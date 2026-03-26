@extends('layouts.app')
@section('title', 'Review Budget — VSULHS_SSLG')

@section('content')

<div class="mb-6">
    <a href="{{ route('budgets.index') }}" class="text-sm text-gray-500 hover:text-gray-800 transition">← Back to Budgets</a>
    <h1 class="text-2xl font-bold text-gray-900 mt-2">Review Budget</h1>
    <p class="text-sm text-gray-500 mt-1">{{ $budget->title }}</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    {{-- Budget Details --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-sm font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-100">Budget Details</h2>
        <dl class="space-y-3">
            <div class="flex justify-between text-sm">
                <dt class="text-gray-500">Title</dt>
                <dd class="font-medium text-gray-900">{{ $budget->title }}</dd>
            </div>
            <div class="flex justify-between text-sm">
                <dt class="text-gray-500">Total Amount</dt>
                <dd class="font-medium text-gray-900">₱{{ number_format($budget->total_amount, 2) }}</dd>
            </div>
            <div class="flex justify-between text-sm">
                <dt class="text-gray-500">Spent Amount</dt>
                <dd class="font-medium text-gray-900">₱{{ number_format($budget->spent_amount, 2) }}</dd>
            </div>
            <div class="flex justify-between text-sm">
                <dt class="text-gray-500">Remaining</dt>
                <dd class="font-semibold {{ $budget->remaining >= 0 ? 'text-green-700' : 'text-red-600' }}">
                    ₱{{ number_format($budget->remaining, 2) }}
                </dd>
            </div>
            <div class="flex justify-between text-sm">
                <dt class="text-gray-500">Period</dt>
                <dd class="font-medium text-gray-900">
                    {{ $budget->period_start->format('M d, Y') }} – {{ $budget->period_end->format('M d, Y') }}
                </dd>
            </div>
            <div class="flex justify-between text-sm">
                <dt class="text-gray-500">Reviewed By</dt>
                <dd class="font-medium text-gray-900">{{ $budget->reviewer?->full_name ?? 'Not yet reviewed' }}</dd>
            </div>
        </dl>

        {{-- Progress bar --}}
        @php $pct = $budget->total_amount > 0 ? min(round(($budget->spent_amount / $budget->total_amount) * 100), 100) : 0; @endphp
        <div class="mt-4">
            <div class="flex justify-between text-xs text-gray-500 mb-1">
                <span>Budget Used</span>
                <span>{{ $pct }}%</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-2">
                <div class="h-2 rounded-full {{ $pct >= 90 ? 'bg-red-500' : ($pct >= 70 ? 'bg-yellow-400' : 'bg-green-500') }}"
                     style="width: {{ $pct }}%"></div>
            </div>
        </div>
    </div>

    {{-- Review Form --}}
    <div class="bg-white rounded-xl border border-gray-200 p-6">
        <h2 class="text-sm font-semibold text-gray-700 mb-4 pb-2 border-b border-gray-100">Update Review</h2>
        <form method="POST" action="{{ route('budgets.review', $budget->id) }}">
            @csrf @method('PATCH')

            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 mb-1">
                    Spent Amount <span class="text-gray-400 font-normal">(max ₱{{ number_format($budget->total_amount, 2) }})</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 text-sm">₱</span>
                    <input type="number" name="spent_amount" step="0.01" min="0" max="{{ $budget->total_amount }}"
                           value="{{ old('spent_amount', $budget->spent_amount) }}" required
                           class="w-full border border-gray-300 rounded-lg pl-7 pr-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-black {{ $errors->has('spent_amount') ? 'border-red-400' : '' }}">
                </div>
                @error('spent_amount') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex gap-3">
                <button type="submit"
                        class="bg-black text-white text-sm font-semibold px-5 py-2 rounded-lg hover:bg-gray-800 transition">
                    Save Review
                </button>
                <a href="{{ route('budgets.index') }}"
                   class="text-sm font-semibold text-gray-600 border border-gray-300 px-5 py-2 rounded-lg hover:bg-gray-50 transition">
                    Cancel
                </a>
            </div>
        </form>
    </div>

</div>

@endsection
