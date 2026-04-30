(() => {
    const submitted = new WeakSet();
    const clicked = new WeakSet();

    // ── Click guard ───────────────────────────────────────────────────
    document.addEventListener('click', (event) => {
        const btn = event.target.closest('[type="submit"]:not([data-no-guard])');
        if (!btn) return;

        const form = btn.closest('form');
        if (!form) return;

        // Block if already fully submitted
        if (submitted.has(form)) {
            event.preventDefault();
            event.stopImmediatePropagation();
            return;
        }

        // Block if attribute-level lock is set (survives Alpine re-renders)
        if (btn.hasAttribute('data-submitting')) {
            event.preventDefault();
            event.stopImmediatePropagation();
            return;
        }

        // Block a second click while the first is still pending
        if (clicked.has(btn)) {
            event.preventDefault();
            event.stopImmediatePropagation();
            return;
        }

        clicked.add(btn);

        const onSubmitOrCancel = () => {
            // FIX: extended from 50ms to 200ms so submitted.add(form)
            // in the submit handler always wins the race first
            setTimeout(() => {
                if (!submitted.has(form)) {
                    clicked.delete(btn);
                }
            }, 200); // was 50 — race condition fix
        };

        form.addEventListener('submit', onSubmitOrCancel, { once: true });

        // FIX: safety fallback only releases if submit never fired
        setTimeout(() => {
            if (!submitted.has(form)) {
                clicked.delete(btn);
            }
        }, 2000);

    }, true);

    // ── Submit guard ──────────────────────────────────────────────────
    document.addEventListener('submit', (event) => {
        const form = event.target;

        if (submitted.has(form)) {
            event.preventDefault();
            return;
        }

        submitted.add(form);

        const btn = form.querySelector('[type="submit"]:not([data-no-guard])');
        if (btn) {
            btn.disabled = true;
            // FIX: attribute-level lock survives Alpine patch cycles
            btn.setAttribute('data-submitting', 'true');
            btn.innerHTML = `
                <svg class="animate-spin inline w-4 h-4 mr-1 -mt-0.5" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                </svg>
                Saving...
            `;
        }
    }, true);
})();