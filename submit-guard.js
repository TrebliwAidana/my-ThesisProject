// ── ADDED: Global single-submission guard ─────────────────────────────
    // Covers ALL forms on ALL pages — no per-blade changes needed.
    // Handles three cases:
    //   1. Normal forms       — locks on submit
    //   2. Confirm dialogs    — only locks AFTER user clicks OK (not on Cancel)
    //   3. Double-click       — click-level guard blocks second click immediately
    //
    // Opt a specific button out with data-no-guard:
    //   <button type="submit" data-no-guard>Search</button>
    // ─────────────────────────────────────────────────────────────────────
    (() => {
        // Tracks forms that have been fully submitted (past any confirm dialog)
        const submitted = new WeakSet();

        // Tracks buttons that have been clicked once but confirm not yet resolved
        const clicked = new WeakSet();

        // ── Click guard ───────────────────────────────────────────────────
        // Fires before the form's onsubmit — blocks a second click immediately.
        // Does NOT lock the button here because confirm() hasn't run yet;
        // we only lock after submit confirms the form is actually going through.
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

            // Block a second click while the first is still pending (e.g. confirm dialog open)
            if (clicked.has(btn)) {
                event.preventDefault();
                event.stopImmediatePropagation();
                return;
            }

            // Mark this button as pending — cleared if submit doesn't fire
            // (i.e. user clicked Cancel on the confirm dialog)
            clicked.add(btn);

            // If the form has a confirm dialog (onsubmit="return confirm(...)"),
            // the submit event only fires if OK is clicked. We use a one-time
            // submit listener on this specific form to clear the pending state
            // if the submission is cancelled.
            const onSubmitOrCancel = () => {
                // Runs after confirm resolves either way.
                // If submit fired: the submit listener below handles locking.
                // If cancelled: we need to un-mark the button so it can be clicked again.
                setTimeout(() => {
                    if (!submitted.has(form)) {
                        // Submit didn't go through — release the click lock
                        clicked.delete(btn);
                    }
                }, 50);
            };

            form.addEventListener('submit', onSubmitOrCancel, { once: true });

            // Safety fallback: if neither submit nor cancel fires within 2s
            // (edge case), release the lock so the button isn't stuck disabled.
            setTimeout(() => {
                if (!submitted.has(form)) {
                    clicked.delete(btn);
                }
            }, 2000);

        }, true); // capture phase

        // ── Submit guard ──────────────────────────────────────────────────
        // Fires after onsubmit confirm() resolves with OK.
        // This is where we definitively lock the button and show the spinner.
        document.addEventListener('submit', (event) => {
            const form = event.target;

            // Already submitted — block duplicate
            if (submitted.has(form)) {
                event.preventDefault();
                return;
            }

            submitted.add(form);

            const btn = form.querySelector('[type="submit"]:not([data-no-guard])');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = `
                    <svg class="animate-spin inline w-4 h-4 mr-1 -mt-0.5" fill="none" viewBox="0 0 24 24" aria-hidden="true">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                    </svg>
                    Saving...
                `;
            }
        }, true); // capture phase
    })();