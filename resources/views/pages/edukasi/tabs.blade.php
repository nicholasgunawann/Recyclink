<div class="bg-white border-b border-gray-200 sticky top-[80px] z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-center gap-6 sm:gap-10 overflow-x-auto no-scrollbar" id="edukasi-tabs">
            
            <button data-target="tab-content-artikel" class="tab-btn flex items-center gap-2 text-sm font-semibold text-gray-500 hover:text-gray-900 py-4 border-b-2 border-transparent transition-colors whitespace-nowrap">
                <i data-lucide="file-text" class="w-4 h-4"></i> Artikel & Tips
            </button>
            
            <button data-target="tab-content-video" class="tab-btn flex items-center gap-2 text-sm font-semibold text-gray-500 hover:text-gray-900 py-4 border-b-2 border-transparent transition-colors whitespace-nowrap">
                <i data-lucide="play-circle" class="w-4 h-4"></i> Video Edukasi
            </button>
            
            <button data-target="tab-content-panduan" class="tab-btn active-tab flex items-center gap-2 text-sm font-semibold text-brand py-4 border-b-2 border-brand whitespace-nowrap">
                <i data-lucide="book-open" class="w-4 h-4"></i> Panduan Pengelolaan Limbah
            </button>
            
            <button data-target="tab-content-faq" class="tab-btn flex items-center gap-2 text-sm font-semibold text-gray-500 hover:text-gray-900 py-4 border-b-2 border-transparent transition-colors whitespace-nowrap">
                <i data-lucide="help-circle" class="w-4 h-4"></i> Pertanyaan Umum
            </button>
            
        </div>
    </div>
</div>

<script>
function initEdukasiTabs() {
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');

    tabBtns.forEach(btn => {
        // Remove existing listeners to prevent duplicates
        const newBtn = btn.cloneNode(true);
        btn.parentNode.replaceChild(newBtn, btn);
        
        newBtn.addEventListener('click', () => {
            // 1. Reset all tabs styling
            const allBtns = document.querySelectorAll('.tab-btn');
            allBtns.forEach(b => {
                b.className = "tab-btn flex items-center gap-2 text-sm font-semibold text-gray-500 hover:text-gray-900 py-4 border-b-2 border-transparent transition-colors whitespace-nowrap";
            });
            
            // 2. Set active tab styling
            newBtn.className = "tab-btn active-tab flex items-center gap-2 text-sm font-semibold text-brand py-4 border-b-2 border-brand whitespace-nowrap";
            
            const targetId = newBtn.getAttribute('data-target');
            const targetContent = document.getElementById(targetId);

            const allContents = document.querySelectorAll('.tab-content');
            allContents.forEach(content => {
                content.classList.add('hidden');
                content.classList.remove('block');
            });

            if (targetContent) {
                targetContent.classList.remove('hidden');
                targetContent.classList.add('block', 'animate-pulse');
                setTimeout(() => targetContent.classList.remove('animate-pulse'), 180);
            }
        });
    });
}

document.addEventListener("turbo:load", initEdukasiTabs);
if (!window.Turbo || document.readyState === 'complete') {
    initEdukasiTabs();
}
</script>
