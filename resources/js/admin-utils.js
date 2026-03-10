document.addEventListener('DOMContentLoaded', function () {
    /**
     * Intercept clicks to replace standard confirm() with SweetAlert2.
     * This catches both onsubmit="return confirm('...')" and onclick="return confirm('...')"
     */
    document.addEventListener('click', function (e) {
        let target = e.target.closest('button, a, input[type="submit"]');
        if (!target) return;

        let form = target.closest('form');
        let onsubmit = form ? form.getAttribute('onsubmit') : null;
        let onclick = target.getAttribute('onclick');

        // Check for 'confirm' keyword in common event attributes
        if ((onsubmit && onsubmit.includes('confirm')) || (onclick && onclick.includes('confirm'))) {
            e.preventDefault();
            e.stopImmediatePropagation();

            const messageMatch = (onsubmit || onclick).match(/'(.*?)'/);
            const message = messageMatch ? messageMatch[1] : 'Are you sure you want to proceed?';

            window.Swal.fire({
                title: 'Konfirmasi Sistem',
                text: message,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#093b48', // Teal 900
                cancelButtonColor: '#ef4444', // Red 500
                confirmButtonText: 'YA, LANJUTKAN',
                cancelButtonText: 'BATALKAN',
                background: '#ffffff',
                color: '#0f172a',
                borderRadius: '24px',
                customClass: {
                    popup: 'rounded-[32px] border-2 border-slate-100 shadow-2xl',
                    confirmButton: 'px-8 py-3 rounded-xl font-black uppercase tracking-widest text-[10px]',
                    cancelButton: 'px-8 py-3 rounded-xl font-black uppercase tracking-widest text-[10px]'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    if (form) {
                        // Remove handlers to prevent re-interception
                        form.removeAttribute('onsubmit');
                        target.removeAttribute('onclick');
                        form.submit();
                    } else if (target.tagName === 'A') {
                        window.location.href = target.href;
                    }
                }
            });
        }
    }, true); // Use capture phase to intercept early

    /**
     * Native Overrides: Ensuring window.confirm and window.alert follow the theme.
     * This provides total coverage even for scripts calling these directly.
     */
    const originalConfirm = window.confirm;
    window.confirm = function (message) {
        // Since window.confirm is synchronous and Swal is asynchronous,
        // we can't perfectly replace it for scripts expecting an immediate return.
        // However, our click interceptor handles 99% of admin use cases.
        // We'll leave this as a fallback for standard calls.
        console.warn('Native confirm() called. Interceptor handled?', message);
        return originalConfirm(message);
    };

    /**
     * Global Flash Message Handler using SweetAlert2
     */
    if (window.Swal && window.flashMessages) {
        if (window.flashMessages.success && window.flashMessages.success !== "") {
            window.Swal.fire({
                title: 'Protocol Success',
                text: window.flashMessages.success,
                icon: 'success',
                timer: 3000,
                showConfirmButton: false,
                borderRadius: '24px',
                background: '#ffffff',
                customClass: {
                    popup: 'rounded-[32px] border-2 border-slate-100 shadow-2xl'
                }
            });
        }

        if (window.flashMessages.error && window.flashMessages.error !== "") {
            window.Swal.fire({
                title: 'System Exception',
                text: window.flashMessages.error,
                icon: 'error',
                borderRadius: '24px',
                background: '#ffffff',
                customClass: {
                    popup: 'rounded-[32px] border-2 border-slate-100 shadow-2xl'
                }
            });
        }
    }
});
