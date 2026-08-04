{{-- ═══════════════════════════════════════════════════════
    Section 3: Limbah Terbaru (Static Data – top 4 from marketplace)
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
                   class="group flex items-center justify-center w-full md:w-auto gap-2 bg-white border border-gray-200 hover:border-gray-300 text-gray-800 text-sm font-semibold px-6 py-3 rounded-xl transition-all duration-200">
                    Lihat Semua
                    <i data-lucide="arrow-right" class="w-4 h-4 text-gray-500 group-hover:translate-x-1 transition-transform"></i>
                </a>
            </div>
        </div>

        {{-- Card Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @forelse($recentListings as $listing)
                @if(!is_object($listing) && !is_array($listing))
                    @continue
                @endif
                @php
                    $listingId = data_get($listing, 'id');
                    $primaryImageUrl = data_get($listing, 'primaryImage.url');
                    $title = data_get($listing, 'title', '');
                    $categoryName = data_get($listing, 'category.category_name', 'Lainnya');
                    $city = data_get($listing, 'city', '');
                    $price = data_get($listing, 'price_per_unit', 0);
                    $unit = data_get($listing, 'unit', '');
                    $quantity = data_get($listing, 'quantity', 0);
                @endphp
                <a href="{{ url('/marketplace/'.$listingId) }}"
                   class="group bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-200 flex flex-col">

                    {{-- Image: full-bleed dengan badge kategori overlay --}}
                    <div class="relative h-52 bg-gray-100 shrink-0 overflow-hidden">
                        <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                             src="{{ $primaryImageUrl }}"
                             alt="{{ $title }}"
                             onerror="this.parentElement.innerHTML='<div class=\'w-full h-full flex items-center justify-center bg-gray-100\'><i data-lucide=\'image\' class=\'w-10 h-10 text-gray-300\'></i></div>'" />

                        {{-- Category badge overlay --}}
                        <span class="absolute top-3 left-3 bg-brand text-white text-[10px] font-bold uppercase tracking-wider px-3 py-1 rounded-full shadow-sm">
                            {{ $categoryName }}
                        </span>
                    </div>

                    {{-- Content --}}
                    <div class="p-4 flex flex-col grow">

                        {{-- Title --}}
                        <h3 class="text-base font-bold text-gray-900 mb-1 leading-snug line-clamp-2 group-hover:text-brand transition-colors">
                            {{ $title }}
                        </h3>

                        {{-- Location --}}
                        <div class="flex items-center gap-1.5 text-xs text-gray-400 mb-4">
                            <i data-lucide="map-pin" class="w-3.5 h-3.5"></i>
                            <span class="truncate">{{ $city }}</span>
                        </div>

                        <div class="grow"></div>

                        {{-- Price & Stock --}}
                        <div class="flex items-end justify-between mt-2">
                            <div>
                                <p class="text-xl font-bold text-gray-900 leading-tight group-hover:text-brand transition-colors">
                                    Rp {{ number_format((float)$price, 0, ',', '.') }}<span class="text-xs font-normal text-gray-400"> / {{ $unit }}</span>
                                </p>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    Stok: {{ number_format((float)$quantity, 0, ',', '.') }} {{ $unit }}
                                </p>
                            </div>
                            <div class="w-8 h-8 rounded-full bg-brand/10 flex items-center justify-center group-hover:bg-brand transition-colors shrink-0">
                                <i data-lucide="arrow-right" class="w-4 h-4 text-brand group-hover:text-white"></i>
                            </div>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-1 sm:col-span-2 lg:col-span-4 text-center py-16 bg-gray-50 border border-dashed border-gray-200 rounded-2xl">
                    <p class="text-gray-500">Belum ada listing yang ditayangkan.</p>
                </div>
            @endforelse
        </div>

    </div>
</section>
