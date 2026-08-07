{{-- Global Top Progress Bar & Theme Detail Page Loader --}}
<div id="universal-top-progress" class="fixed top-0 left-0 right-0 h-1 bg-brand z-[9999999] pointer-events-none transition-all duration-300 opacity-0 w-0 shadow-[0_0_12px_rgba(122,156,89,0.9)]"></div>

{{-- Detail Page Theme Loader Overlay --}}
<div id="detail-page-loader" class="fixed inset-0 bg-white/80 backdrop-blur-md z-[999998] flex flex-col items-center justify-center opacity-0 pointer-events-none transition-opacity duration-300">
    <div class="relative flex flex-col items-center justify-center p-8 rounded-3xl bg-white/95 border border-gray-100 shadow-2xl max-w-xs w-full text-center">
        {{-- Animated Pulsing Brand Logo Ring --}}
        <div class="relative w-20 h-20 mb-4 flex items-center justify-center">
            <div class="absolute inset-0 rounded-full border-2 border-dashed border-brand/40 animate-[spin_8s_linear_infinite]"></div>
            <div class="absolute inset-1 rounded-full bg-brand/10 animate-ping opacity-75"></div>
            <div class="relative w-14 h-14 bg-white rounded-2xl shadow-md border border-gray-100 p-2.5 flex items-center justify-center">
                <img src="{{ asset('images/logo.png') }}" alt="Recyclink Loading" class="w-full h-full object-contain animate-pulse">
            </div>
        </div>

        {{-- Loader Title & Subtitle --}}
        <h4 id="detail-loader-title" class="text-sm font-bold text-gray-900 mb-1">Memuat Halaman...</h4>
        <p id="detail-loader-subtitle" class="text-xs text-gray-400 font-medium">Mohon tunggu sebentar</p>

        {{-- Subtle Progress Bar --}}
        <div class="w-full bg-gray-100 h-1.5 rounded-full mt-4 overflow-hidden">
            <div class="bg-brand h-full rounded-full animate-[pulse_1s_infinite] w-full"></div>
        </div>
    </div>
</div>

<script>
    (function() {
        let progressTimer;

        function getProgressBar() {
            return document.getElementById('universal-top-progress');
        }

        function getOverlayLoader() {
            return document.getElementById('detail-page-loader');
        }

        function showDetailLoader(targetUrl) {
            const overlay = getOverlayLoader();
            const titleEl = document.getElementById('detail-loader-title');
            const subEl = document.getElementById('detail-loader-subtitle');

            if (!overlay) return;

            let title = "Memuat Halaman...";
            let subtitle = "Menyiapkan data Recyclink";

            if (targetUrl) {
                if (targetUrl.includes('/marketplace/') && !targetUrl.includes('/marketplace/store')) {
                    title = "Memuat Detail Limbah...";
                    subtitle = "Menampilkan spesifikasi & harga terbaru";
                } else if (targetUrl.includes('/toko/') || targetUrl.includes('/marketplace/store')) {
                    title = "Memuat Detail Toko...";
                    subtitle = "Menyiapkan profil & etalase penjual";
                } else if (targetUrl.includes('/edukasi')) {
                    title = "Memuat Konten Edukasi...";
                    subtitle = "Menyiapkan artikel & video panduan";
                } else if (targetUrl.includes('/orders') || targetUrl.includes('/pesanan')) {
                    title = "Memuat Detail Pesanan...";
                    subtitle = "Menyiapkan data transaksi";
                }
            }

            if (titleEl) titleEl.textContent = title;
            if (subEl) subEl.textContent = subtitle;

            overlay.classList.remove('pointer-events-none');
            overlay.classList.add('opacity-100');
        }

        function hideDetailLoader() {
            const overlay = getOverlayLoader();
            if (!overlay) return;

            overlay.classList.remove('opacity-100');
            setTimeout(() => {
                overlay.classList.add('pointer-events-none');
            }, 300);
        }

        function startProgress(e) {
            const bar = getProgressBar();
            if (bar) {
                clearTimeout(progressTimer);
                bar.style.transition = 'width 0.3s ease, opacity 0.1s ease';
                bar.style.opacity = '1';
                bar.style.width = '30%';

                progressTimer = setTimeout(() => {
                    bar.style.width = '70%';
                }, 200);
            }

            let targetUrl = '';
            if (e && e.detail && e.detail.url) {
                targetUrl = e.detail.url;
            } else if (document.activeElement && document.activeElement.href) {
                targetUrl = document.activeElement.href;
            }

            if (targetUrl && (
                targetUrl.includes('/marketplace/') ||
                targetUrl.includes('/toko/') ||
                targetUrl.includes('/edukasi') ||
                targetUrl.includes('/orders') ||
                targetUrl.includes('/pesanan')
            )) {
                showDetailLoader(targetUrl);
            }
        }

        function completeProgress() {
            const bar = getProgressBar();
            if (bar) {
                clearTimeout(progressTimer);
                bar.style.width = '100%';
                progressTimer = setTimeout(() => {
                    bar.style.opacity = '0';
                    setTimeout(() => {
                        bar.style.width = '0%';
                    }, 300);
                }, 150);
            }
            hideDetailLoader();
        }

        document.addEventListener('click', function(e) {
            const link = e.target.closest('a');
            if (link && link.href && !link.target && !e.ctrlKey && !e.metaKey) {
                const url = link.href;
                if (url.includes('/marketplace/') || url.includes('/toko/') || url.includes('/edukasi') || url.includes('/orders')) {
                    showDetailLoader(url);
                }
            }
        });

        document.addEventListener("turbo:visit", startProgress);
        document.addEventListener("turbo:submit-start", startProgress);
        document.addEventListener("turbo:load", completeProgress);
        document.addEventListener("turbo:frame-load", completeProgress);
        window.addEventListener('pageshow', completeProgress);
        window.addEventListener('popstate', completeProgress);
    })();
</script>
