<div class="bg-white border-b border-gray-200 sticky top-[64px] z-40">
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
document.addEventListener('DOMContentLoaded', () => {
    const tabBtns = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');

    tabBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            // 1. Reset all tabs styling
            tabBtns.forEach(b => {
                b.className = "tab-btn flex items-center gap-2 text-sm font-semibold text-gray-500 hover:text-gray-900 py-4 border-b-2 border-transparent transition-colors whitespace-nowrap";
            });
            
            // 2. Set active tab styling
            btn.className = "tab-btn active-tab flex items-center gap-2 text-sm font-semibold text-brand py-4 border-b-2 border-brand whitespace-nowrap";
            
            // 3. Hide all content
            tabContents.forEach(content => {
                content.classList.add('hidden');
                content.classList.remove('block');
            });
            
            // 4. Show target content
            const targetId = btn.getAttribute('data-target');
            const targetContent = document.getElementById(targetId);
            if (targetContent) {
                targetContent.classList.remove('hidden');
                targetContent.classList.add('block');
            }
        });
    });
});
</script>
