{{-- ═══════════════════════════════════════════════════
    Section 1: Hero & Search Bar
    Lokasi: resources/views/pages/beranda/hero.blade.php
════════════════════════════════════════════════════ --}}
<section class="relative overflow-hidden bg-white" id="hero">

    {{-- Subtle green blob background --}}
    <div class="absolute -top-32 -right-32 w-[600px] h-[600px] rounded-full bg-brand/5 pointer-events-none"></div>
    <div class="absolute -bottom-16 -left-16 w-64 h-64 rounded-full bg-brand/5 pointer-events-none"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-20 pb-24 lg:pt-28 lg:pb-32">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">

            {{-- Left: Text + Search --}}
            <div>
                {{-- Badge --}}
                <span class="inline-flex items-center gap-1.5 bg-brand/10 text-brand text-xs font-semibold px-3 py-1 rounded-full mb-5">
                    <span class="w-1.5 h-1.5 rounded-full bg-brand animate-pulse"></span>
                    Marketplace Limbah UMKM — Berbasis Semarang
                </span>

                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-gray-900 leading-tight tracking-tight">
                    Cuan Bertambah,<br>
                    <span class="text-brand">Lingkungan Terjaga</span>
                </h1>

                <p class="mt-5 text-lg text-gray-500 leading-relaxed max-w-lg">
                    Platform digital yang menghubungkan limbah UMKM dengan pihak yang membutuhkannya sebagai bahan baku ekonomis. Promosikan limbahmu, temukan pembeli, dan berkontribusi nyata untuk lingkungan.
                </p>

                {{-- Search Bar --}}
                <div class="mt-8 flex items-center bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden focus-within:ring-2 focus-within:ring-brand/30 focus-within:border-brand transition-all">
                    <div class="flex items-center pl-4 text-gray-400">
                        <i data-lucide="search" class="w-5 h-5"></i>
                    </div>
                    <input
                        id="hero-search"
                        type="text"
                        placeholder="Cari jenis limbah... (plastik, kertas, logam)"
                        class="flex-1 px-3 py-4 text-sm text-gray-700 placeholder-gray-400 bg-transparent outline-none"
                    >
                    <button class="m-2 bg-brand hover:bg-brand-hover text-white text-sm font-semibold px-5 py-2.5 rounded-xl transition-colors">
                        Cari
                    </button>
                </div>

                {{-- Quick tags --}}
                <div class="mt-4 flex flex-wrap gap-2">
                    @foreach(['Plastik', 'Kertas', 'Logam', 'Elektronik', 'Kaca'] as $tag)
                        <button class="text-xs text-gray-500 bg-gray-50 hover:bg-brand/10 hover:text-brand border border-gray-200 hover:border-brand/30 px-3 py-1 rounded-full transition-all">
                            {{ $tag }}
                        </button>
                    @endforeach
                </div>

                {{-- Stats --}}
                <div class="mt-10 flex items-center gap-8">
                    <div>
                        <p class="text-2xl font-bold text-gray-900">2.4K+</p>
                        <p class="text-xs text-gray-500 mt-0.5">Listing Aktif</p>
                    </div>
                    <div class="w-px h-10 bg-gray-100"></div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">850+</p>
                        <p class="text-xs text-gray-500 mt-0.5">Pengepul Terdaftar</p>
                    </div>
                    <div class="w-px h-10 bg-gray-100"></div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">32 Kota</p>
                        <p class="text-xs text-gray-500 mt-0.5">Jangkauan</p>
                    </div>
                </div>
            </div>

            {{-- Right: Illustration --}}
            <div class="hidden lg:flex justify-center items-center">
                <div class="relative">
                    {{-- Decorative ring --}}
                    <div class="absolute inset-0 rounded-full border-2 border-dashed border-brand/20 scale-110"></div>
                    <div class="w-80 h-80 bg-brand/5 rounded-full flex items-center justify-center">
                        <img src="{{ asset('images/herobg.png') }}" alt="Recyclink" class="w-56 h-56 object-contain drop-shadow-sm">
                    </div>
                    {{-- Floating badges --}}
                    <div class="absolute -top-4 -right-4 bg-white border border-gray-100 rounded-xl shadow-md px-3 py-2 flex items-center gap-2">
                        <div class="w-7 h-7 bg-green-50 rounded-full flex items-center justify-center">
                            <i data-lucide="check-circle" class="w-4 h-4 text-brand"></i>
                        </div>
                        <span class="text-xs font-semibold text-gray-700">Terverifikasi</span>
                    </div>
                    <div class="absolute -bottom-4 -left-4 bg-white border border-gray-100 rounded-xl shadow-md px-3 py-2 flex items-center gap-2">
                        <div class="w-7 h-7 bg-green-50 rounded-full flex items-center justify-center">
                            <i data-lucide="trending-up" class="w-4 h-4 text-brand"></i>
                        </div>
                        <span class="text-xs font-semibold text-gray-700">+120 Hari Ini</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>
