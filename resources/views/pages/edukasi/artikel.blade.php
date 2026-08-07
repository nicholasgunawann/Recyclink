<section class="py-12 bg-gray-50/50 min-h-[60vh]">
    <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Section Header --}}
        <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <span class="inline-flex items-center gap-1.5 bg-brand/10 text-brand px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider mb-3">
                    <i data-lucide="newspaper" class="w-3.5 h-3.5"></i> Wawasan Terbaru
                </span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">Artikel & Strategi Pengelolaan Limbah</h2>
                <p class="text-gray-500 text-sm sm:text-base mt-1 max-w-xl">
                    Pelajari konsep ekonomi sirkular, teknik pemilahan material industri, dan tren pasar daur ulang global.
                </p>
            </div>
        </div>

        {{-- Cards Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($articles as $art)
            <a href="{{ $art->formatted_content_url }}" target="_blank" class="bg-white rounded-2xl border border-gray-200/80 shadow-sm hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 flex flex-col overflow-hidden group cursor-pointer">
                
                {{-- Image --}}
                <div class="w-full h-52 bg-gray-100 relative overflow-hidden">
                    <img src="{{ $art->display_thumbnail_url }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="{{ $art->title }}">
                    <span class="absolute top-4 left-4 bg-white/90 backdrop-blur-md border border-white/60 text-brand text-[10px] font-extrabold px-2.5 py-1 rounded-lg uppercase tracking-wider shadow-xs">
                        Artikel & Tips
                    </span>
                </div>
                
                {{-- Content --}}
                <div class="p-6 flex flex-col flex-grow">
                    <div class="flex items-center justify-between text-xs text-gray-400 font-medium mb-3">
                        <span class="flex items-center gap-1">
                            <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                            {{ $art->published_at ? $art->published_at->diffForHumans() : 'Baru saja' }}
                        </span>
                        <span class="flex items-center gap-1">
                            <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                            {{ number_format($art->view_count ?? 0) }} dibaca
                        </span>
                    </div>
                    
                    <h3 class="text-lg font-bold text-gray-900 mb-3 group-hover:text-brand transition-colors leading-snug line-clamp-2">
                        {{ $art->title }}
                    </h3>
                    
                    <p class="text-gray-500 text-sm mb-6 leading-relaxed line-clamp-3">
                        {{ $art->excerpt }}
                    </p>
                    
                    <div class="mt-auto pt-4 border-t border-gray-100 flex items-center justify-between">
                        <span class="text-brand text-xs font-bold flex items-center gap-1.5 group-hover:translate-x-1 transition-transform">
                            Baca Selengkapnya <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                        </span>
                        <span class="text-[11px] font-semibold text-gray-400 bg-gray-50 px-2 py-0.5 rounded">Rujukan Utama</span>
                    </div>
                </div>
            </a>
            @empty
                <div class="col-span-3 py-16 text-center bg-white rounded-2xl border border-gray-200/80 shadow-xs">
                    <div class="w-14 h-14 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-4 text-gray-400 border border-gray-100">
                        <i data-lucide="file-text" class="w-7 h-7"></i>
                    </div>
                    <h4 class="font-bold text-gray-700">Belum Ada Artikel</h4>
                    <p class="text-sm text-gray-500 mt-1">Artikel & tips pengelolaan limbah akan ditayangkan di sini.</p>
                </div>
            @endforelse
        </div>
        
    </div>
</section>
