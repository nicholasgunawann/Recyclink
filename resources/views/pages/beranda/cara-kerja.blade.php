{{-- ═══════════════════════════════════════════════════════
    Section 2: Cara Kerja
    Lokasi: resources/views/pages/beranda/cara-kerja.blade.php
════════════════════════════════════════════════════════ --}}
<section class="bg-white py-20" id="cara-kerja">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="text-center max-w-xl mx-auto mb-14">
            <span class="text-xs font-semibold text-brand uppercase tracking-widest">Cara Kerja</span>
            <h2 class="mt-2 text-3xl font-bold text-gray-900 tracking-tight">
                Mulai dalam 4 Langkah Mudah
            </h2>
            <p class="mt-3 text-gray-500">
                Tidak perlu ribet. Recyclink menghubungkanmu dengan pengepul hanya dalam beberapa menit.
            </p>
        </div>

        {{-- Steps --}}
        <div class="relative">
            {{-- Connector line (desktop only) --}}
            <div class="hidden lg:block absolute top-10 left-1/2 -translate-x-1/2 w-3/4 h-px bg-brand/20"></div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">

                @php
                    $steps = [
                        ['step' => '01', 'icon' => 'search',        'title' => 'Cari Limbah',          'desc' => 'Masukkan jenis limbah yang kamu miliki atau gunakan filter kategori untuk menemukan listing yang tepat.'],
                        ['step' => '02', 'icon' => 'message-circle','title' => 'Hubungi Pengepul',       'desc' => 'Pilih pengepul terverifikasi dan mulai negosiasi harga langsung melalui platform chat kami.'],
                        ['step' => '03', 'icon' => 'map-pin',       'title' => 'Jadwalkan Pickup',      'desc' => 'Tentukan waktu dan lokasi penjemputan. Pengepul akan datang ke tempatmu.'],
                        ['step' => '04', 'icon' => 'check-circle',  'title' => 'Daur Ulang & Untung',   'desc' => 'Limbahmu terambil, kamu mendapat bayaran, dan bumi semakin hijau. Win-win!'],
                    ];
                @endphp

                @foreach($steps as $s)
                    <div class="relative flex flex-col items-center text-center group">
                        <div class="relative mb-5">
                            <div class="w-20 h-20 rounded-2xl bg-white border border-gray-100 shadow-sm
                                        flex items-center justify-center
                                        group-hover:border-brand/30 group-hover:shadow-md
                                        transition-all duration-300">
                                <i data-lucide="{{ $s['icon'] }}" class="w-8 h-8 text-brand"></i>
                            </div>
                            <span class="absolute -top-2 -right-2 w-6 h-6 bg-brand text-white text-[10px] font-bold rounded-full flex items-center justify-center shadow">
                                {{ $s['step'] }}
                            </span>
                        </div>
                        <h3 class="text-base font-semibold text-gray-900 mb-1.5">{{ $s['title'] }}</h3>
                        <p class="text-sm text-gray-500 leading-relaxed">{{ $s['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- CTA --}}
        <div class="mt-12 text-center">
            <a href="{{ url('/register') }}"
               class="inline-flex items-center gap-2 bg-brand hover:bg-brand-hover text-white font-semibold px-6 py-3 rounded-xl transition-colors shadow-sm">
                Mulai Sekarang
                <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </a>
        </div>
    </div>
</section>
