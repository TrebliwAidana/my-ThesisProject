@extends('layouts.app')

@section('title', 'Generate Financial Report')

@section('content')
<div class="max-w-2xl mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
    <h1 class="text-2xl font-bold mb-6">Generate Financial Report</h1>

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
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Organization / Club Name
            </label>
            <input type="text" name="organization" id="organization"
                   value="{{ old('organization', auth()->user()->organization ?? '') }}"
                   class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 dark:bg-gray-700">
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
                <input type="date" name="start_date" id="start_date" required
                       class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 dark:bg-gray-700">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date</label>
                <input type="date" name="end_date" id="end_date" required
                       class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 dark:bg-gray-700">
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                Previous Cash Deposited (optional)
            </label>
            <input type="number" step="0.01" name="previous_cash" id="previous_cash" value="0"
                   class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 dark:bg-gray-700">
            <p class="text-xs text-gray-500 mt-1">If there was a previous balance, enter it here.</p>
        </div>

        {{-- File Format --}}
        <div class="mb-6">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                File Format
            </label>
            <div class="flex gap-3">
                @foreach ([
                    ['value' => 'pdf',   'label' => '📄 PDF',          'desc' => 'Printable report'],
                    ['value' => 'excel', 'label' => '📊 Excel (.xlsx)', 'desc' => 'Spreadsheet format'],
                    ['value' => 'word',  'label' => '📝 Word (.docx)',  'desc' => 'Editable document'],
                ] as $fmt)
                <label class="flex-1 cursor-pointer">
                    <input type="radio" name="format" value="{{ $fmt['value'] }}"
                           class="sr-only peer" {{ $fmt['value'] === 'pdf' ? 'checked' : '' }}>
                    <div class="border-2 border-gray-200 dark:border-gray-600 peer-checked:border-emerald-500 peer-checked:bg-emerald-50 dark:peer-checked:bg-emerald-900/20 rounded-lg p-3 text-center transition">
                        <div class="font-medium text-sm text-gray-700 dark:text-gray-300">{{ $fmt['label'] }}</div>
                        <div class="text-xs text-gray-400 mt-0.5">{{ $fmt['desc'] }}</div>
                    </div>
                </label>
                @endforeach
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <button type="submit"
                    class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2 rounded-lg font-medium transition">
                ⬇️ Download
            </button>
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
    const startDate = document.getElementById('start_date').value;  
    const endDate   = document.getElementById('end_date').value;

    if (!startDate || !endDate) {
        alert('Please select both Start Date and End Date before previewing.');
        return;
    }

    const btn = document.getElementById('previewBtn');
    btn.innerHTML = '⏳ Generating...';
    btn.disabled  = true;

    try {
        const formData = new FormData(document.getElementById('reportForm'));
        formData.set('format', 'pdf'); // preview always uses PDF

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
            console.error('Server error:', response.status, await response.text());
            alert('Server error (' + response.status + '). Check console for details.');
            return;
        }

        const contentType = response.headers.get('Content-Type') || '';
        if (!contentType.includes('application/pdf')) {
            console.error('Expected PDF but got:', contentType);
            alert('Unexpected response from server. Check console for details.');
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
        btn.innerHTML = '👁️ Preview';
        btn.disabled  = false;
    }
}

// Set default dates to current month
document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('previewBtn').disabled = false;

    const today    = new Date();
    const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
    const lastDay  = new Date(today.getFullYear(), today.getMonth() + 1, 0);

    const startDateInput = document.getElementById('start_date');  // fixed
    const endDateInput   = document.getElementById('end_date');    // fixed

    if (!startDateInput.value) {
        startDateInput.value = firstDay.toISOString().split('T')[0];
    }
    if (!endDateInput.value) {
        endDateInput.value = lastDay.toISOString().split('T')[0];
    }
});
</script>
@endsection