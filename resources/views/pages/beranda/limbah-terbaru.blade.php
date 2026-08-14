{{-- ═══════════════════════════════════════════════════════
    Section 3: Limbah Terbaru (Top 4 from marketplace)
    Lokasi: resources/views/pages/beranda/limbah-terbaru.blade.php
════════════════════════════════════════════════════════ --}}

<section class="bg-white py-16 md:py-24" id="limbah-terbaru">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header & Tombol Lihat Semua --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 gap-6">
            <div class="max-w-2xl">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-2 h-2 rounded-full bg-brand"></div>
                    <span class="text-xs font-bold text-brand tracking-widest uppercase">Terbaru</span>
                </div>
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 tracking-tight">Limbah Terbaru</h2>
                <p class="mt-3 text-gray-500 text-sm md:text-base">
                    Produk limbah yang baru saja ditambahkan oleh seller kami dari berbagai daerah.
                </p>
            </div>
            <div class="shrink-0 flex items-center">
                <a href="{{ url('/marketplace') }}"
                   class="group flex items-center justify-center w-full md:w-auto gap-2 bg-white border border-gray-200 hover:border-gray-300 text-gray-800 text-sm font-semibold px-6 py-3 rounded-xl transition-all duration-200 shadow-2xs">
                    Lihat Semua
                    <i data-lucide="arrow-right" class="w-4 h-4 text-gray-500 group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>
        </div>

        {{-- Skeleton Loader State --}}
        <div id="limbah-skeleton" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4">
            @for ($i = 0; $i < 4; $i++)
                <div class="bg-white rounded-xl border border-gray-100 overflow-hidden animate-pulse flex flex-col h-full shadow-2xs">
                    <div class="aspect-square bg-gray-200/80 w-full"></div>
                    <div class="p-3 flex flex-col gap-2 flex-1 justify-between">
                        <div class="space-y-1.5">
                            <div class="h-3.5 bg-gray-200 rounded-md w-full"></div>
                            <div class="h-3.5 bg-gray-200 rounded-md w-3/4"></div>
                        </div>
                        <div class="h-4 bg-brand/20 rounded-md w-1/2 mt-1"></div>
                        <div class="flex items-center justify-between mt-1">
                            <div class="h-3 bg-gray-100 rounded w-1/3"></div>
                            <div class="h-3 bg-gray-100 rounded w-1/4"></div>
                        </div>
                    </div>
                </div>
            @endfor
        </div>

        {{-- Real Card Grid (Matching Marketplace Card Dimensions & Styles) --}}
        <div id="limbah-content" class="hidden opacity-0 transition-opacity duration-300 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-4">
            @forelse($recentListings as $listing)
                <a href="{{ url('/marketplace/'.$listing->id) }}"
                   class="group bg-transparent hover:bg-white rounded-xl overflow-hidden border border-gray-200/80 shadow-2xs hover:shadow-xl hover:shadow-brand/10 hover:-translate-y-1 hover:border-brand/40 transition-all duration-300 flex flex-col h-full">

                    {{-- 1:1 Aspect Ratio Image Container --}}
                    <div class="relative w-full aspect-square bg-gray-100 overflow-hidden shrink-0">
                        <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300 ease-out"
                             src="{{ $listing->primary_image_url ?: 'https://placehold.co/400x400?text=Limbah' }}"
                             alt="{{ $listing->title }}"
                             loading="lazy"
                             onerror="this.src='https://placehold.co/400x400?text=Limbah'" />

                        {{-- Category badge overlay --}}
                        <span class="absolute top-2 left-2 bg-brand/90 backdrop-blur-xs text-white text-[9px] font-bold px-2 py-0.5 rounded uppercase tracking-wide shadow-xs">
                            {{ $listing->category_short_name }}
                        </span>
                    </div>

                    {{-- Card Body --}}
                    <div class="p-2.5 sm:p-3 flex flex-col justify-between grow gap-1.5">
                        <div>
                            <h3 class="text-xs sm:text-sm font-medium text-gray-900 line-clamp-2 leading-snug group-hover:text-brand transition-colors duration-200">
                                {{ $listing->title }}
                            </h3>
                            <p class="text-sm sm:text-base font-bold text-gray-900 mt-1">
                                Rp {{ number_format((float)($listing->price_per_unit ?? 0), 0, ',', '.') }}<span class="text-[11px] font-normal text-gray-400"> / {{ $listing->unit }}</span>
                            </p>
                        </div>
                        <div class="flex items-center justify-between text-[11px] text-gray-400 mt-1">
                            <span class="truncate flex items-center gap-1">
                                <i data-lucide="map-pin" class="w-3 h-3 text-gray-400 shrink-0"></i>
                                <span class="truncate">{{ $listing->city }}</span>
                            </span>
                            <span class="shrink-0 font-normal text-gray-400">
                                Stok: {{ number_format((float)($listing->quantity ?? 0), 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full text-center py-16 bg-gray-50 border border-dashed border-gray-200 rounded-2xl">
                    <p class="text-gray-500 text-sm">Belum ada listing yang ditayangkan.</p>
                </div>
            @endforelse
        </div>

    </div>
</section>

<script>
    (function() {
        function showLimbahContent() {
            const skel = document.getElementById('limbah-skeleton');
            const content = document.getElementById('limbah-content');
            if (skel && content) {
                skel.classList.add('hidden');
                content.classList.remove('hidden');
                setTimeout(() => {
                    content.classList.remove('opacity-0');
                }, 50);
            }
        }
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', showLimbahContent);
        } else {
            showLimbahContent();
        }
        document.addEventListener('turbo:load', showLimbahContent);
    })();
</script>
