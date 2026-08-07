{{-- ═══════════════════════════════════════════════════
    Section: Sticky Education Pill Tabs Navigation
    Lokasi: resources/views/pages/edukasi/tabs.blade.php
════════════════════════════════════════════════════ --}}
<div class="bg-white/90 backdrop-blur-md border-b border-gray-100 sticky top-[76px] z-40 py-3 shadow-xs">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-center gap-2 sm:gap-4 overflow-x-auto no-scrollbar py-1" id="edukasi-tabs">
            
            <button data-target="tab-content-artikel" class="tab-btn flex items-center gap-2 text-xs sm:text-sm font-bold text-gray-600 hover:text-brand bg-gray-100/80 hover:bg-brand/10 px-4 sm:px-5 py-2.5 rounded-full transition-all whitespace-nowrap cursor-pointer">
                <i data-lucide="file-text" class="w-4 h-4"></i> Artikel & Tips
                <span class="bg-white text-gray-700 text-[11px] font-extrabold px-2 py-0.5 rounded-full shadow-xs border border-gray-200/60">{{ $articles->count() }}</span>
            </button>
            
            <button data-target="tab-content-video" class="tab-btn flex items-center gap-2 text-xs sm:text-sm font-bold text-gray-600 hover:text-brand bg-gray-100/80 hover:bg-brand/10 px-4 sm:px-5 py-2.5 rounded-full transition-all whitespace-nowrap cursor-pointer">
                <i data-lucide="play-circle" class="w-4 h-4"></i> Video Edukasi
                <span class="bg-white text-gray-700 text-[11px] font-extrabold px-2 py-0.5 rounded-full shadow-xs border border-gray-200/60">{{ $videos->count() }}</span>
            </button>
            
            <button data-target="tab-content-panduan" class="tab-btn active-tab flex items-center gap-2 text-xs sm:text-sm font-bold text-white bg-brand shadow-md shadow-brand/20 px-4 sm:px-5 py-2.5 rounded-full transition-all whitespace-nowrap cursor-pointer">
                <i data-lucide="book-open" class="w-4 h-4"></i> Panduan Limbah
                <span class="bg-white/20 text-white text-[11px] font-extrabold px-2 py-0.5 rounded-full backdrop-blur-xs">{{ $guides->count() }}</span>
            </button>
            
            <button data-target="tab-content-faq" class="tab-btn flex items-center gap-2 text-xs sm:text-sm font-bold text-gray-600 hover:text-brand bg-gray-100/80 hover:bg-brand/10 px-4 sm:px-5 py-2.5 rounded-full transition-all whitespace-nowrap cursor-pointer">
                <i data-lucide="help-circle" class="w-4 h-4"></i> Pertanyaan Umum
                <span class="bg-white text-gray-700 text-[11px] font-extrabold px-2 py-0.5 rounded-full shadow-xs border border-gray-200/60">FAQ</span>
            </button>
            
        </div>
    </div>
</div>

<script>
function initEdukasiTabs() {
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');

    const inactiveClass = "tab-btn flex items-center gap-2 text-xs sm:text-sm font-bold text-gray-600 hover:text-brand bg-gray-100/80 hover:bg-brand/10 px-4 sm:px-5 py-2.5 rounded-full transition-all whitespace-nowrap cursor-pointer";
    const activeClass = "tab-btn active-tab flex items-center gap-2 text-xs sm:text-sm font-bold text-white bg-brand shadow-md shadow-brand/20 px-4 sm:px-5 py-2.5 rounded-full transition-all whitespace-nowrap cursor-pointer";

    tabBtns.forEach(btn => {
        const newBtn = btn.cloneNode(true);
        btn.parentNode.replaceChild(newBtn, btn);
        
        newBtn.addEventListener('click', () => {
            const allBtns = document.querySelectorAll('.tab-btn');
            allBtns.forEach(b => {
                b.className = inactiveClass;
                const counter = b.querySelector('span');
                if (counter) {
                    counter.className = "bg-white text-gray-700 text-[11px] font-extrabold px-2 py-0.5 rounded-full shadow-xs border border-gray-200/60";
                }
            });
            
            newBtn.className = activeClass;
            const activeCounter = newBtn.querySelector('span');
            if (activeCounter) {
                activeCounter.className = "bg-white/20 text-white text-[11px] font-extrabold px-2 py-0.5 rounded-full backdrop-blur-xs";
            }
            
            const targetId = newBtn.getAttribute('data-target');
            const targetContent = document.getElementById(targetId);

            const allContents = document.querySelectorAll('.tab-content');
            allContents.forEach(content => {
                content.classList.add('hidden');
                content.classList.remove('block');
            });

            if (targetContent) {
                targetContent.classList.remove('hidden');
                targetContent.classList.add('block');
            }
        });
    });
}

document.addEventListener("turbo:load", initEdukasiTabs);
if (!window.Turbo || document.readyState === 'complete') {
    initEdukasiTabs();
}
</script>
