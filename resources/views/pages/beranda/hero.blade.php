{{-- ═══════════════════════════════════════════════════
    Section 1: Hero Section (Full-Page 100vh Layout)
    Lokasi: resources/views/pages/beranda/hero.blade.php
════════════════════════════════════════════════════ --}}
<section class="relative min-h-[calc(100vh-80px)] flex flex-col justify-center overflow-hidden bg-gradient-to-b from-white via-emerald-50/20 to-white py-12 lg:py-0" id="hero">

    {{-- Decorative Ambient Glowing Blobs --}}
    <div class="absolute -top-40 -right-40 w-[650px] h-[650px] rounded-full bg-brand/10 blur-3xl pointer-events-none"></div>
    <div class="absolute top-1/3 -left-40 w-[500px] h-[500px] rounded-full bg-emerald-300/10 blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-20 right-1/4 w-80 h-80 rounded-full bg-brand/5 blur-2xl pointer-events-none"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full my-auto">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center">

            {{-- Left Column: Content & Call to Actions (Col 7) --}}
            <div class="lg:col-span-7 flex flex-col justify-center scroll-reveal-left">

                {{-- Hero Heading --}}
                <h1 class="text-4xl sm:text-5xl lg:text-6xl xl:text-7xl font-extrabold text-gray-900 leading-[1.15] tracking-tight mb-6">
                    Cuan Bertambah,<br>
                    <span class="text-brand">
                        Lingkungan Terjaga
                    </span>
                </h1>

                {{-- Hero Subheading (H2) --}}
                <h2 class="text-base sm:text-lg lg:text-xl text-gray-600 leading-relaxed max-w-2xl font-normal">
                    Platform digital yang menghubungkan limbah UMKM dengan pihak yang membutuhkannya sebagai bahan baku ekonomis. Promosikan limbahmu, temukan pembeli, dan berkontribusi nyata untuk lingkungan.
                </h2>

            </div>

            {{-- Right Column: Dynamic Graphic & Glassmorphism Badges (Col 5) --}}
            <div class="lg:col-span-5 flex justify-center items-center relative mt-6 lg:mt-0 scroll-reveal-right">
                <div class="relative w-full max-w-lg aspect-square flex items-center justify-center">
                    
                    {{-- Decorative Layered Glowing Rings --}}
                    <div class="absolute inset-0 rounded-full border border-dashed border-brand/30 animate-[spin_60s_linear_infinite]"></div>
                    <div class="absolute inset-4 rounded-full border border-brand/15"></div>
                    <div class="absolute inset-12 bg-gradient-to-tr from-brand/10 via-emerald-100/40 to-transparent rounded-full backdrop-blur-sm shadow-inner"></div>

                    {{-- Cardboard Box Hero Graphic --}}
                    <div class="relative z-10 w-72 h-72 sm:w-88 sm:h-88 flex items-center justify-center p-4">
                        <img src="{{ asset('images/herobg.png') }}" alt="Recyclink Box" class="w-full h-full object-contain drop-shadow-2xl hover:scale-105 transition-transform duration-500">
                    </div>

                    {{-- Floating Glassmorphism Badge 1: Terverifikasi (Top Right) --}}
                    <div class="absolute -top-2 right-4 sm:right-8 bg-white/90 backdrop-blur-md border border-white/60 rounded-2xl shadow-xl p-3.5 flex items-center gap-3 z-20 hover:scale-105 transition-all">
                        <div class="w-10 h-10 bg-emerald-500/10 rounded-xl flex items-center justify-center text-brand shrink-0">
                            <i data-lucide="shield-check" class="w-6 h-6 text-brand"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-medium">Status Akun</p>
                            <p class="text-sm font-bold text-gray-900">100% Terverifikasi</p>
                        </div>
                    </div>

                    {{-- Floating Glassmorphism Badge 2: Growth Trend (Bottom Left) --}}
                    <div class="absolute -bottom-2 left-4 sm:left-8 bg-white/90 backdrop-blur-md border border-white/60 rounded-2xl shadow-xl p-3.5 flex items-center gap-3 z-20 hover:scale-105 transition-all">
                        <div class="w-10 h-10 bg-brand/10 rounded-xl flex items-center justify-center shrink-0">
                            <i data-lucide="trending-up" class="w-6 h-6 text-brand"></i>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 font-medium">Transaksi Baru</p>
                            <p class="text-sm font-bold text-gray-900">+120 Hari Ini</p>
                        </div>
                    </div>

                    {{-- Floating Glassmorphism Badge 3: Material Daur Ulang (Top Left) --}}
                    <div class="absolute top-1/3 -left-4 sm:-left-6 hidden sm:flex bg-white/90 backdrop-blur-md border border-white/60 rounded-2xl shadow-lg px-3.5 py-2.5 items-center gap-2 z-20">
                        <div class="w-2.5 h-2.5 rounded-full bg-brand"></div>
                        <span class="text-xs font-bold text-gray-800">Plastik & Kardus Ready</span>
                    </div>

                    {{-- Floating Glassmorphism Badge 4: Eco Impact (Bottom Right) --}}
                    <div class="absolute bottom-1/4 -right-4 sm:-right-6 hidden sm:flex bg-white/90 backdrop-blur-md border border-white/60 rounded-2xl shadow-lg px-3.5 py-2.5 items-center gap-2.5 z-20">
                        <div class="w-7 h-7 bg-emerald-100 rounded-lg flex items-center justify-center">
                            <i data-lucide="leaf" class="w-4 h-4 text-emerald-600"></i>
                        </div>
                        <span class="text-xs font-bold text-gray-800">Bebas Emisi Limbah</span>
                    </div>

                </div>
            </div>

        </div>
    </div>
</section>
