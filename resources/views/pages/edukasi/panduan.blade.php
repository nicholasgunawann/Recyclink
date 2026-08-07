<section class="py-12 bg-gray-50/50 min-h-[60vh]">
    <div class="w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Section Header --}}
        <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <span class="inline-flex items-center gap-1.5 bg-brand/10 text-brand px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider mb-3">
                    <i data-lucide="book-open" class="w-3.5 h-3.5"></i> Panduan Pengelolaan Limbah
                </span>
                <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">Langkah Strategis Operasional</h2>
                <p class="text-gray-500 text-sm sm:text-base mt-1 max-w-xl">
                    Panduan teknis dan manajerial yang dirancang khusus untuk meningkatkan efisiensi pengelolaan limbah industri Anda.
                </p>
            </div>
        </div>

        {{-- Cards Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($guides as $guide)
                @php
                    $parts = explode('|', $guide->content . '|');
                    $level = strtolower($parts[0] ?: 'pemula');
                    $link = $guide->formatted_content_url;
                    
                    $levelConfigs = [
                        'pemula' => [
                            'bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200/60',
                            'iconBg' => 'bg-emerald-100/80 text-emerald-700',
                            'label' => 'Pemula'
                        ],
                        'menengah' => [
                            'bg' => 'bg-amber-50 text-amber-700 border-amber-200/60',
                            'iconBg' => 'bg-amber-100/80 text-amber-700',
                            'label' => 'Menengah'
                        ],
                        'lanjutan' => [
                            'bg' => 'bg-rose-50 text-rose-700 border-rose-200/60',
                            'iconBg' => 'bg-rose-100/80 text-rose-700',
                            'label' => 'Lanjutan'
                        ]
                    ];
                    $config = $levelConfigs[$level] ?? $levelConfigs['pemula'];
                @endphp
                
                <a href="{{ $link }}" target="_blank" class="bg-white border border-gray-200/80 shadow-sm hover:shadow-xl hover:-translate-y-1.5 transition-all duration-300 rounded-2xl p-6 flex flex-col group cursor-pointer">
                    <div class="flex justify-between items-start mb-6">
                        <div class="w-12 h-12 {{ $config['iconBg'] }} rounded-xl flex items-center justify-center shadow-xs">
                            <i data-lucide="book" class="w-6 h-6"></i>
                        </div>
                        <span class="{{ $config['bg'] }} border px-3 py-1 rounded-lg text-[10px] font-extrabold uppercase tracking-wider shadow-xs">
                            {{ $config['label'] }}
                        </span>
                    </div>
                    
                    <h3 class="text-lg font-bold text-gray-900 mb-3 group-hover:text-brand transition-colors leading-snug">
                        {{ $guide->title }}
                    </h3>
                    
                    <p class="text-gray-500 text-sm mb-6 leading-relaxed line-clamp-3">
                        {{ $guide->excerpt }}
                    </p>

                    <div class="mt-auto flex items-center justify-between border-t border-gray-100 pt-4">
                        <div class="flex items-center gap-2 text-xs font-semibold text-gray-400">
                            <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                            <span>{{ strtoupper($guide->published_at ? \Carbon\Carbon::parse($guide->published_at)->translatedFormat('M Y') : 'Terbaru') }}</span>
                        </div>
                        <span class="text-brand text-xs font-bold flex items-center gap-1 group-hover:translate-x-1 transition-transform uppercase tracking-wider">
                            Buka Panduan <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
                        </span>
                    </div>
                </a>
            @empty
                <div class="col-span-3 py-16 text-center bg-white rounded-2xl border border-gray-200/80 shadow-xs">
                    <div class="w-14 h-14 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-4 text-gray-400 border border-gray-100">
                        <i data-lucide="book-open" class="w-7 h-7"></i>
                    </div>
                    <h4 class="font-bold text-gray-700">Belum Ada Panduan</h4>
                    <p class="text-sm text-gray-500 mt-1">Panduan pengelolaan limbah akan ditayangkan di sini.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>
