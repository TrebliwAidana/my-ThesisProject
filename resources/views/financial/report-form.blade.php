@extends('layouts.app')

@section('title', 'Generate Financial Report')

@section('content')
<div class="max-w-2xl mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
    <h1 class="text-2xl font-bold mb-6">Generate Financial Report</h1>

    {{-- Validation errors --}}
    @if ($errors->any())
        <div class="mb-4 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
            <ul class="text-sm text-red-600 dark:text-red-400 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Main download form --}}
    <form method="POST" action="{{ route('financial.report.generate') }}" id="reportForm">
        @csrf

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Organization / Club Name
            </label>
            <input type="text"
                   name="organization"
                   id="field_organization"
                   value="{{ old('organization', auth()->user()->organization ?? '') }}"
                   class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 dark:bg-gray-700">
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Start Date
                </label>
                <input type="date"
                       name="start_date"
                       id="field_start_date"
                       required
                       class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 dark:bg-gray-700">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    End Date
                </label>
                <input type="date"
                       name="end_date"
                       id="field_end_date"
                       required
                       class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 dark:bg-gray-700">
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Previous Cash Deposited (optional)
            </label>
            <input type="number"
                   step="0.01"
                   name="previous_cash"
                   id="field_previous_cash"
                   value="0"
                   class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 dark:bg-gray-700">
            <p class="text-xs text-gray-500 mt-1">If there was a previous balance, enter it here.</p>
        </div>

        <div class="flex gap-3">
            {{-- Download button (normal form submit) --}}
            <button type="submit"
                    class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg font-medium transition">
                📄 Download PDF
            </button>

            {{-- Preview button (fetch + blob, never leaves page) --}}
            <button type="button"
                    id="previewBtn"
                    onclick="previewReport(event)"
                    class="bg-blue-600 hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed text-white px-4 py-2 rounded-lg font-medium transition">
                👁️ Preview
            </button>
        </div>
    </form>
</div>

<script>
async function previewReport(event) {
    const startDate = document.getElementById('field_start_date').value;
    const endDate   = document.getElementById('field_end_date').value;
    const org       = document.getElementById('field_organization').value;
    const prevCash  = document.getElementById('field_previous_cash').value;

    if (!startDate || !endDate) {
        alert('Please select both Start Date and End Date before previewing.');
        return;
    }

    const btn = document.getElementById('previewBtn');
    btn.innerHTML  = '⏳ Generating...';
    btn.disabled   = true;

    try {
        const formData = new FormData();
        formData.append('_token',        '{{ csrf_token() }}');
        formData.append('start_date',    startDate);
        formData.append('end_date',      endDate);
        formData.append('organization',  org);
        formData.append('previous_cash', prevCash || '0');

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
            alert('Session expired or permission denied. Please refresh the page and try again.');
            return;
        }

        if (!response.ok) {
            const text = await response.text();
            console.error('Server responded with error:', response.status, text);
            alert('Server error (' + response.status + '). Check console for details.');
            return;
        }

        const contentType = response.headers.get('Content-Type') || '';
        if (!contentType.includes('application/pdf')) {
            const text = await response.text();
            console.error('Expected PDF but got:', contentType, text);
            alert('Unexpected response from server. Check console for details.');
            return;
        }

        // Create a blob URL from the PDF and open it in a new tab
        const blob    = await response.blob();
        const blobUrl = URL.createObjectURL(blob);
        const newTab  = window.open(blobUrl, '_blank');

        if (!newTab) {
            alert('Popup was blocked. Please allow popups for this site and try again.');
        }

        // Release the blob URL after the tab has loaded
        setTimeout(() => URL.revokeObjectURL(blobUrl), 15000);

    } catch (err) {
        console.error('Preview fetch error:', err);
        alert('A network error occurred. Please try again.');
    } finally {
        btn.innerHTML = '👁️ Preview';
        btn.disabled  = false;
    }
}
</script>
@endsection