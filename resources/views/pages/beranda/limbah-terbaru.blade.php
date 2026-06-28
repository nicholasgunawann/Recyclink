{{-- ═══════════════════════════════════════════════════════
    Section 3: Limbah Terbaru (Static Data – top 4 from marketplace)
    Lokasi: resources/views/pages/beranda/limbah-terbaru.blade.php
════════════════════════════════════════════════════════ --}}

@php
$recentListings = [
    ['id'=>1,  'title'=>'Styrofoam / EPS Bekas – Bongkar Gudang',       'categoryLabel'=>'Plastik',        'city'=>'Surakarta',  'price'=>3000,  'unit'=>'kg',    'stock'=>8000,   'image'=>'https://images.unsplash.com/photo-1628177142898-93e36e4e3a50?w=600&q=80'],
    ['id'=>10, 'title'=>'Aluminium Kaleng Cacah – Siap Lebur',           'categoryLabel'=>'Aluminium',      'city'=>'Tangerang',  'price'=>14500, 'unit'=>'kg',    'stock'=>8000,   'image'=>'https://images.unsplash.com/photo-1558346547-4439467bd1d5?w=600&q=80'],
    ['id'=>11, 'title'=>'Minyak Jelantah (UCO) – Food Grade',            'categoryLabel'=>'Minyak',         'city'=>'Jakarta',    'price'=>8500,  'unit'=>'liter', 'stock'=>10000,  'image'=>'https://images.unsplash.com/photo-1510498468133-c97f0e0dcdbe?w=600&q=80'],
    ['id'=>9,  'title'=>'Serbuk Kayu Halus – Biomassa Energi',           'categoryLabel'=>'Kayu & Biomassa','city'=>'Medan',      'price'=>400,   'unit'=>'kg',    'stock'=>50000,  'image'=>'https://images.unsplash.com/photo-1574323347407-f5e1ad6d020b?w=600&q=80'],
];
@endphp

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
            @foreach($recentListings as $listing)
                <a href="{{ url('/marketplace/'.$listing['id']) }}"
                   class="group bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-shadow duration-200 flex flex-col">

                    {{-- Image: full-bleed dengan badge kategori overlay --}}
                    <div class="relative h-52 bg-gray-100 shrink-0 overflow-hidden">
                        <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                             src="{{ $listing['image'] }}"
                             alt="{{ $listing['title'] }}"
                             onerror="this.parentElement.innerHTML='<div class=\'w-full h-full flex items-center justify-center bg-gray-100\'><i data-lucide=\'image\' class=\'w-10 h-10 text-gray-300\'></i></div>'" />

                        {{-- Category badge overlay --}}
                        <span class="absolute top-3 left-3 bg-brand text-white text-[10px] font-bold uppercase tracking-wider px-3 py-1 rounded-full shadow-sm">
                            {{ $listing['categoryLabel'] }}
                        </span>
                    </div>

                    {{-- Content --}}
                    <div class="p-4 flex flex-col grow">

                        {{-- Title --}}
                        <h5 class="text-base font-bold text-gray-900 line-clamp-2 leading-snug mb-1 group-hover:text-brand transition-colors">
                            {{ $listing['title'] }}
                        </h5>

                        {{-- Location --}}
                        <div class="flex items-center gap-1 text-xs text-gray-400 mb-4">
                            <i data-lucide="map-pin" class="w-3.5 h-3.5 shrink-0"></i>
                            <span>{{ $listing['city'] }}</span>
                        </div>

                        {{-- Spacer --}}
                        <div class="grow"></div>

                        {{-- Footer: Price + Arrow Button --}}
                        <div class="flex items-end justify-between gap-3">
                            <div>
                                <p class="text-xl font-bold text-gray-900 leading-tight">
                                    Rp {{ number_format($listing['price'], 0, ',', '.') }}
                                    <span class="text-xs font-normal text-gray-400">/ {{ $listing['unit'] }}</span>
                                </p>
                                <p class="text-xs text-gray-400 mt-0.5">
                                    Stok: {{ number_format($listing['stock'], 0, ',', '.') }} {{ $listing['unit'] }}
                                </p>
                            </div>
                            {{-- Tombol ikon panah bulat hijau --}}
                            <span class="shrink-0 w-10 h-10 rounded-full bg-brand group-hover:bg-[#5b7a48] text-white flex items-center justify-center transition-colors shadow-sm">
                                <i data-lucide="arrow-right" class="w-4 h-4"></i>
                            </span>
                        </div>

                    </div>
                </a>
            @endforeach
        </div>

    </div>
</section>
