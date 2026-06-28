@php
    $articlesDummy = [
        [
            'category' => 'EKONOMI SIRKULAR',
            'time' => '5 Menit Baca',
            'title' => 'Implementasi Ekonomi Sirkular pada Industri Manufaktur',
            'desc' => 'Bagaimana perusahaan besar mengubah limbah produksi menjadi sumber pendapatan baru melalui sistem loop...',
        ],
        [
            'category' => 'MANAJEMEN LIMBAH',
            'time' => '4 Menit Baca',
            'title' => 'Panduan Pemilahan Limbah Plastik Grade Industri',
            'desc' => 'Teknik akurat membedakan PP, HDPE, dan PET untuk mendapatkan harga jual maksimal di marketplace.',
        ],
        [
            'category' => 'TREN PASAR',
            'time' => '6 Menit Baca',
            'title' => 'Analisis Harga Material Daur Ulang Kuartal II 2024',
            'desc' => 'Tinjauan komprehensif fluktuasi harga logam dan kertas di pasar domestik serta faktor global yang mempengaruhinya.',
        ]
    ];
    
    // Fill up to 10 items to show horizontal scrolling
    $articlesList = [];
    for($i = 0; $i < 10; $i++) {
        $articlesList[] = $articlesDummy[$i % 3];
    }
@endphp

<section class="py-16 bg-gray-50 border-t border-gray-100 min-h-[70vh] flex flex-col justify-center">
    <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Section Header --}}
        <div class="mb-10">
            <span class="inline-block bg-brand/10 text-brand px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-widest mb-4">
                Wawasan Terbaru
            </span>
            <h2 class="text-3xl font-extrabold text-gray-900 mb-4 tracking-tight">Artikel & Strategi Pengelolaan Limbah</h2>
            <p class="text-gray-500 max-w-2xl text-lg">
                Pelajari konsep ekonomi sirkular, teknik pemilahan material industri, dan tren pasar daur ulang global.
            </p>
        </div>

        {{-- Scrollable Container --}}
        <div class="flex gap-6 overflow-x-auto no-scrollbar snap-x pb-8 -mx-4 px-4 sm:mx-0 sm:px-0">
            @foreach($articlesList as $art)
            <div class="min-w-[320px] max-w-[320px] shrink-0 snap-start bg-white rounded-2xl border border-gray-200 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 flex flex-col overflow-hidden group cursor-pointer">
                
                {{-- Image Placeholder --}}
                <div class="w-full h-48 bg-[#e2e8f0] flex items-center justify-center relative overflow-hidden">
                    <div class="w-16 h-16 border-4 border-gray-300 rounded-lg group-hover:scale-110 transition-transform duration-500"></div>
                </div>
                
                {{-- Content --}}
                <div class="p-6 flex flex-col flex-grow">
                    <div class="flex items-center gap-3 mb-4">
                        <span class="bg-brand/10 text-brand px-2 py-1 rounded text-[9px] font-bold uppercase tracking-widest">
                            {{ $art['category'] }}
                        </span>
                        <span class="text-[11px] text-gray-400 font-medium">{{ $art['time'] }}</span>
                    </div>
                    
                    <h3 class="text-lg font-bold text-gray-900 mb-3 group-hover:text-brand transition-colors leading-snug line-clamp-2">
                        {{ $art['title'] }}
                    </h3>
                    
                    <p class="text-gray-500 text-sm mb-6 leading-relaxed line-clamp-3">
                        {{ $art['desc'] }}
                    </p>
                    
                    <div class="mt-auto pt-4 border-t border-gray-100">
                        <a href="#" class="text-brand text-xs font-bold flex items-center gap-1 group-hover:translate-x-1 transition-transform">
                            Baca Selengkapnya <i data-lucide="arrow-right" class="w-3 h-3"></i>
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
    </div>
</section>
