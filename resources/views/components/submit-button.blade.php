<button
    {{ $attributes->merge(['type' => 'submit']) }}
    x-data="{ busy: false }"
    @click="if (busy || $el.hasAttribute('data-submitting')) { $event.preventDefault(); $event.stopImmediatePropagation(); return; } busy = true;"
    @submit.window.capture="
        if ($el.closest('form') && $event.target === $el.closest('form')) {
            busy = true;
            $el.disabled = true;
            $el.setAttribute('data-submitting', 'true');
        }
    "
>{{ $slot }}</button>