<button
    {{ $attributes->merge([
        'type' => 'submit',
    ]) }}
    x-data="submitOnce()"
    @click="handle"
    :disabled="submitting"
    :class="submitting ? 'opacity-50 cursor-not-allowed pointer-events-none' : ''"
>
    <span x-show="!submitting">{{ $slot }}</span>
    <span x-show="submitting" x-cloak class="inline-flex items-center gap-2">
        <svg class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
        </svg>
        {{ $loadingText ?? 'Saving...' }}
    </span>
</button>