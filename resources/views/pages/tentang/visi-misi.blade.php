<section class="py-20 bg-gray-50 border-t border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-16 items-start">
            
            {{-- Left Content: Visi & Misi Card --}}
            <div class="bg-white rounded-3xl border border-gray-200 p-8 sm:p-10 shadow-sm">
                
                {{-- Visi --}}
                <div class="mb-10">
                    <div class="w-10 h-10 bg-brand/10 rounded-full flex items-center justify-center mb-6">
                        <i data-lucide="eye" class="w-5 h-5 text-brand"></i>
                    </div>
                    <h2 class="text-2xl font-extrabold text-brand mb-4 tracking-tight">Visi Kami</h2>
                    <p class="text-gray-600 leading-relaxed text-base uppercase">
                        Menjadi platform marketplace limbah terdepan yang menghubungkan UMKM dan pengguna limbah untuk menciptakan ekosistem ekonomi sirkular yang berkelanjutan di Indonesia.
                    </p>
                </div>
                
                <hr class="border-gray-200 mb-10">
                
                {{-- Misi --}}
                <div>
                    <h2 class="text-2xl font-extrabold text-brand mb-6 tracking-tight">Misi Utama</h2>
                    <ul class="space-y-5">
                        <li class="flex items-start gap-4">
                            <i data-lucide="check-circle" class="w-6 h-6 text-brand shrink-0 mt-0.5"></i>
                            <span class="text-gray-600 leading-relaxed uppercase text-sm">Menyediakan platform digital yang mempermudah promosi dan pencarian limbah bernilai ekonomi.</span>
                        </li>
                        <li class="flex items-start gap-4">
                            <i data-lucide="check-circle" class="w-6 h-6 text-brand shrink-0 mt-0.5"></i>
                            <span class="text-gray-600 leading-relaxed uppercase text-sm">Menghubungkan UMKM penghasil limbah dengan pihak yang membutuhkan bahan baku secara lebih efisien dan transparan.</span>
                        </li>
                        <li class="flex items-start gap-4">
                            <i data-lucide="check-circle" class="w-6 h-6 text-brand shrink-0 mt-0.5"></i>
                            <span class="text-gray-600 leading-relaxed uppercase text-sm">Meningkatkan kesadaran dan edukasi masyarakat mengenai pemanfaatan limbah.</span>
                        </li>
                        <li class="flex items-start gap-4">
                            <i data-lucide="check-circle" class="w-6 h-6 text-brand shrink-0 mt-0.5"></i>
                            <span class="text-gray-600 leading-relaxed uppercase text-sm">Mendorong terciptanya peluang ekonomi baru bagi UMKM melalui optimalisasi limbah produksi.</span>
                        </li>
                        <li class="flex items-start gap-4">
                            <i data-lucide="check-circle" class="w-6 h-6 text-brand shrink-0 mt-0.5"></i>
                            <span class="text-gray-600 leading-relaxed uppercase text-sm">Membangun jaringan kolaborasi dengan komunitas, industri, akademisi, dan pemerintah untuk mendukung pengelolaan limbah yang berkelanjutan.</span>
                        </li>
                    </ul>
                </div>
                
            </div>
            
            {{-- Right Content: Image --}}
            <div class="h-full">
                <img src="{{ asset('images/heroTtg.png') }}" alt="Industrial Machine" class="w-full h-full object-cover rounded-3xl shadow-md min-h-[400px]">
            </div>
            
        </div>
    </div>
</section>
