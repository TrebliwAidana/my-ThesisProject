{{-- 
    Reusable Back Button Component
    Usage: <x-back-button />
    Place this file at: resources/views/components/back-button.blade.php
--}}
<div class="absolute top-6 left-6 z-20" x-data>
    <button
        @click="document.referrer && document.referrer !== window.location.href
            ? history.back()
            : window.location.href = '{{ route('landing') }}'"
        class="inline-flex items-center gap-2 px-3 py-2.5 md:px-4 md:py-2 rounded-full bg-white/10 backdrop-blur-sm text-white text-sm font-medium hover:bg-white/20 active:scale-95 transition-all duration-200 border border-white/20 min-h-[44px] md:min-h-0"
        aria-label="Go back"
    >
        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        <span class="hidden sm:inline">Back</span>
    </button>
</div>