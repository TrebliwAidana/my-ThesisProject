@extends('layouts.app')

@section('title', 'Generate Financial Report')
@section('page-title', 'Generate Financial Report')

@section('content')
<div class="max-w-2xl mx-auto">

    {{-- Emerald Gradient Header --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-r from-emerald-600 to-emerald-700 dark:from-emerald-800 dark:to-emerald-900 p-6 mb-6">
        <div class="relative z-10">
            <h1 class="text-2xl font-bold text-white">Financial Report Generator</h1>
            <p class="text-emerald-100 text-sm mt-1">Generate comprehensive financial reports</p>
        </div>
        <div class="absolute top-0 right-0 -mt-8 -mr-8 w-64 h-64 bg-white/5 rounded-full blur-3xl"></div>
    </div>

    {{-- Form Card --}}
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl border border-gold-200 dark:border-gold-800 overflow-hidden">
        <div class="p-6">

            @if ($errors->any())
                <div class="mb-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                    <ul class="text-sm text-red-600 dark:text-red-400 list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('financial.report.generate') }}" id="reportForm">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Organization / Club Name
                    </label>
                    <input type="text" name="organization"
                           value="{{ old('organization', auth()->user()->organization ?? '') }}"
                           class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-transparent">
                </div>

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Start Date <span class="text-red-500">*</span></label>
                        <input type="date" name="start_date" id="field_start_date" required
                               class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-transparent">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">End Date <span class="text-red-500">*</span></label>
                        <input type="date" name="end_date" id="field_end_date" required
                               class="w-full border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-transparent">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        Previous Cash Deposited (optional)
                    </label>
                    <div class="flex">
                        <span class="inline-flex items-center px-3 rounded-l-lg border border-r-0 border-gold-300 dark:border-gold-600 bg-gray-50 dark:bg-gray-700 text-gray-500 text-sm select-none">₱</span>
                        <input type="number" step="0.01" name="previous_cash" value="0"
                               class="flex-1 border border-gold-300 dark:border-gold-600 dark:bg-gray-700 dark:text-white rounded-r-lg px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-gold-500 focus:border-transparent">
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">If there was a previous balance, enter it here.</p>
                </div>

                {{-- File Format - Smaller boxes --}}
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                        File Format <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-3 gap-2">
                        @foreach ([
                            ['value' => 'pdf',   'label' => 'PDF', 'icon' => '📄', 'desc' => 'Printable'],
                            ['value' => 'excel', 'label' => 'Excel', 'icon' => '📊', 'desc' => 'Spreadsheet'],
                            ['value' => 'word',  'label' => 'Word', 'icon' => '📝', 'desc' => 'Editable'],
                        ] as $fmt)
                        <label class="cursor-pointer">
                            <input type="radio" name="format" value="{{ $fmt['value'] }}"
                                   class="sr-only peer" {{ $fmt['value'] === 'pdf' ? 'checked' : '' }}>
                            <div class="border-2 border-gold-200 dark:border-gold-800 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 dark:peer-checked:bg-emerald-900/20 rounded-lg py-2.5 text-center transition hover:border-gold-400 cursor-pointer">
                                <div class="text-lg">{{ $fmt['icon'] }}</div>
                                <div class="text-xs font-medium text-gray-700 dark:text-gray-300">{{ $fmt['label'] }}</div>
                                <div class="text-[10px] text-gray-400 dark:text-gray-500">{{ $fmt['desc'] }}</div>
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>

                <div class="flex gap-3 pt-4 border-t border-gold-200 dark:border-gold-800">
                    <button type="submit"
                            class="flex-1 bg-emerald-600 hover:bg-gold-500 text-white font-semibold py-2.5 px-4 rounded-lg transition shadow-sm flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        Download
                    </button>

                    <button type="button"
                            id="previewBtn"
                            onclick="previewReport(event)"
                            class="flex-1 bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed text-white font-semibold py-2.5 px-4 rounded-lg transition shadow-sm flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Preview
                    </button>
                </div>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-3 text-center">
                    💡 Preview shows PDF layout. Use <strong>Download</strong> to get your chosen format.
                </p>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
async function previewReport(event) {
    const startDate = document.getElementById('field_start_date').value;
    const endDate   = document.getElementById('field_end_date').value;

    if (!startDate || !endDate) {
        alert('Please select both Start Date and End Date before previewing.');
        return;
    }

    const btn = document.getElementById('previewBtn');
    const originalHtml = btn.innerHTML;
    btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Generating...';
    btn.disabled = true;

    try {
        const formData = new FormData(document.getElementById('reportForm'));
        formData.set('format', 'pdf');

        const response = await fetch('{{ route('financial.report.preview') }}', {
            method: 'POST',
            body: formData,
            credentials: 'same-origin',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
            },
        });

        if (response.redirected) {
            alert('Session expired or permission denied. Please refresh and try again.');
            return;
        }

        if (!response.ok) {
            console.error('Server error:', response.status);
            alert('Server error (' + response.status + '). Please try again.');
            return;
        }

        const contentType = response.headers.get('Content-Type') || '';
        if (!contentType.includes('application/pdf')) {
            alert('Unexpected response from server.');
            return;
        }

        const blobUrl = URL.createObjectURL(await response.blob());
        const newTab  = window.open(blobUrl, '_blank');

        if (!newTab) {
            alert('Popup was blocked. Please allow popups for this site and try again.');
        }

        setTimeout(() => URL.revokeObjectURL(blobUrl), 15000);

    } catch (err) {
        console.error('Preview error:', err);
        alert('A network error occurred. Please try again.');
    } finally {
        btn.innerHTML = originalHtml;
        btn.disabled = false;
    }
}

document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('previewBtn').disabled = false;

    const today    = new Date();
    const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
    const lastDay  = new Date(today.getFullYear(), today.getMonth() + 1, 0);

    const startDateInput = document.getElementById('field_start_date');
    const endDateInput   = document.getElementById('field_end_date');

    if (!startDateInput.value) {
        startDateInput.value = firstDay.toISOString().split('T')[0];
    }
    if (!endDateInput.value) {
        endDateInput.value = lastDay.toISOString().split('T')[0];
    }
});
</script>
@endpush

@push('styles')
<style>
    .peer:checked + div {
        border-color: #10b981;
        background-color: #ecfdf5;
    }
    .dark .peer:checked + div {
        background-color: rgba(16, 185, 129, 0.1);
    }
</style>
@endpush
@endsection