{{-- ═══════════════════════════════════════════════════════
    Section 4: Kategori Limbah
    Lokasi: resources/views/pages/beranda/kategori.blade.php
════════════════════════════════════════════════════════ --}}
<section class="bg-gray-50 py-20" id="kategori">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="mb-10">
            <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">Kategori Limbah</h2>
            <p class="mt-2 text-gray-500 max-w-2xl text-base">
                Jelajahi berbagai jenis material sisa industri yang tersedia di marketplace kami.
            </p>
        </div>

        {{-- Category Grid --}}
        @php
            $categories = [
                ['label' => 'Plastik', 'sub' => 'PET, HDPE, LDPE',    'icon' => 'recycle'],
                ['label' => 'Logam',   'sub' => 'Besi, Tembaga, Alum', 'icon' => 'wrench'],
                ['label' => 'Tekstil', 'sub' => 'Sisa Kain, Serat',   'icon' => 'shirt'],
                ['label' => 'Kertas',  'sub' => 'Kardus, Arsip',      'icon' => 'file-text'],
                ['label' => 'Kimia',   'sub' => 'Cairan, Pelarut',    'icon' => 'flask-conical'],
                ['label' => 'Lainnya', 'sub' => 'Kaca, Kayu, Karet',  'icon' => 'more-horizontal'],
            ];
        @endphp

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            @foreach($categories as $cat)
                <div class="group bg-white border border-gray-200 rounded-2xl p-6
                          flex flex-col items-center text-center
                          hover:border-brand/30 hover:shadow-md hover:-translate-y-1
                          transition-all duration-300">

                    <div class="w-14 h-14 rounded-full bg-brand/10 flex items-center justify-center mb-4
                                group-hover:scale-110 transition-transform duration-300">
                        <i data-lucide="{{ $cat['icon'] }}"
                           class="w-6 h-6 text-brand"></i>
                    </div>
                    <p class="text-base font-bold text-gray-900 mb-1">
                        {{ $cat['label'] }}
                    </p>
                    <p class="text-[11px] text-gray-500 font-medium">
                        {{ $cat['sub'] }}
                    </p>
                </div>
            @endforeach
        </div>
    </div>
</section>
