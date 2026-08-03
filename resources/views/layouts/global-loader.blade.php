{{-- Global Top Progress Bar (Sleek non-blocking UX) --}}
<div id="universal-top-progress" class="fixed top-0 left-0 right-0 h-0.5 bg-brand z-[999999] pointer-events-none transition-all duration-300 opacity-0 w-0 shadow-[0_0_8px_rgba(16,185,129,0.8)]"></div>

<script>
    (function() {
        let progressTimer;
        function getProgressBar() {
            return document.getElementById('universal-top-progress');
        }

        function startProgress() {
            const bar = getProgressBar();
            if (!bar) return;
            clearTimeout(progressTimer);
            bar.style.transition = 'width 0.3s ease, opacity 0.1s ease';
            bar.style.opacity = '1';
            bar.style.width = '30%';

            progressTimer = setTimeout(() => {
                bar.style.width = '70%';
            }, 200);
        }

        function completeProgress() {
            const bar = getProgressBar();
            if (!bar) return;
            clearTimeout(progressTimer);
            bar.style.width = '100%';
            progressTimer = setTimeout(() => {
                bar.style.opacity = '0';
                setTimeout(() => {
                    bar.style.width = '0%';
                }, 300);
            }, 150);
        }

        document.addEventListener("turbo:visit", startProgress);
        document.addEventListener("turbo:submit-start", startProgress);
        document.addEventListener("turbo:load", completeProgress);
        document.addEventListener("turbo:frame-load", completeProgress);
        window.addEventListener('pageshow', completeProgress);
    })();
</script>
