{{-- ponytail: minimal reusable toast notification element for Recyclink theme --}}
<div id="toast-container" class="fixed top-5 right-5 z-[99999] flex flex-col gap-3 max-w-sm w-full pointer-events-none px-4 sm:px-0"></div>

<script>
    (function() {
        // ponytail: global toast notification handler matching Recyclink theme
        window.showToast = function(message, type = 'info', duration = 4000) {
            let container = document.getElementById('toast-container');
            if (!container) {
                container = document.createElement('div');
                container.id = 'toast-container';
                container.className = 'fixed top-5 right-5 z-[99999] flex flex-col gap-3 max-w-sm w-full pointer-events-none px-4 sm:px-0';
                document.body.appendChild(container);
            }

            const toast = document.createElement('div');
            toast.className = 'pointer-events-auto flex items-start gap-3 w-full bg-white/95 backdrop-blur-md border rounded-2xl p-4 shadow-xl shadow-gray-200/50 transition-all duration-300 transform translate-x-full opacity-0 relative overflow-hidden';
            
            // Recyclink theme styles based on toast type
            let iconSvg = '';
            let borderClass = 'border-brand/30';
            let iconBgClass = 'bg-brand/10 text-brand';

            if (type === 'success') {
                borderClass = 'border-emerald-200';
                iconBgClass = 'bg-emerald-100 text-emerald-600';
                iconSvg = `<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>`;
            } else if (type === 'error' || type === 'danger') {
                borderClass = 'border-red-200';
                iconBgClass = 'bg-red-100 text-red-600';
                iconSvg = `<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>`;
            } else if (type === 'warning') {
                borderClass = 'border-amber-200';
                iconBgClass = 'bg-amber-100 text-amber-600';
                iconSvg = `<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`;
            } else {
                // Info / default brand
                borderClass = 'border-teal-200';
                iconBgClass = 'bg-teal-100 text-teal-600';
                iconSvg = `<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>`;
            }

            toast.classList.add(...borderClass.split(' '));

            toast.innerHTML = `
                <div class="shrink-0 p-2 rounded-xl ${iconBgClass}">
                    ${iconSvg}
                </div>
                <div class="flex-1 min-w-0 pt-0.5">
                    <p class="text-xs sm:text-sm font-semibold text-gray-800 leading-snug break-words">${message}</p>
                </div>
                <button type="button" class="shrink-0 text-gray-400 hover:text-gray-600 p-1 rounded-lg transition-colors cursor-pointer" onclick="this.closest('.pointer-events-auto').remove()">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            `;

            container.appendChild(toast);

            // Animate in
            requestAnimationFrame(() => {
                toast.classList.remove('translate-x-full', 'opacity-0');
            });

            // Auto dismiss
            let timer = setTimeout(() => {
                dismissToast(toast);
            }, duration);

            toast.addEventListener('mouseenter', () => clearTimeout(timer));
            toast.addEventListener('mouseleave', () => {
                timer = setTimeout(() => dismissToast(toast), duration);
            });
        };

        function dismissToast(toast) {
            if (!toast || !toast.parentNode) return;
            toast.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => {
                if (toast.parentNode) toast.remove();
            }, 300);
        }
    })();
</script>
