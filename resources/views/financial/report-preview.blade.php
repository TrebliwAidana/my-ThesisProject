@extends('layouts.app')

@section('title', 'Generate Financial Report')

@section('content')
<div class="max-w-2xl mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
    <h1 class="text-2xl font-bold mb-6">Generate Financial Report</h1>

    <form method="POST" action="{{ route('financial.report.generate') }}" id="reportForm">
        @csrf

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Organization / Club Name</label>
            <input type="text" name="organization" value="{{ old('organization', auth()->user()->organization ?? '') }}" 
                   class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 dark:bg-gray-700">
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start Date</label>
                <input type="date" name="start_date" required class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 dark:bg-gray-700">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date</label>
                <input type="date" name="end_date" required class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 dark:bg-gray-700">
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Previous Cash Deposited (optional)</label>
            <input type="number" step="0.01" name="previous_cash" value="0" 
                   class="w-full border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 dark:bg-gray-700">
            <p class="text-xs text-gray-500 mt-1">If there was a previous balance, enter it here.</p>
        </div>

        <div class="flex gap-3">
            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg">
                📄 Download PDF
            </button>
            <button type="button" onclick="previewReport()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg">
                👁️ Preview
            </button>
        </div>
    </form>
</div>

<script>
function previewReport() {
    const form = document.getElementById('reportForm');
    // Change action to preview route
    form.action = "{{ route('financial.report.preview') }}";
    form.target = "_blank";  // open preview in new tab
    form.submit();
    // Reset action back to PDF generation for normal submit
    form.action = "{{ route('financial.report.generate') }}";
    form.target = "";
}
</script>
@endsection