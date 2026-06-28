{{-- ═══════════════════════════════════════════════════════
    Section 3: Keunggulan Recyclink
    Lokasi: resources/views/pages/beranda/keunggulan.blade.php
════════════════════════════════════════════════════════ --}}
<section class="bg-gray-50 py-20" id="keunggulan">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header --}}
        <div class="text-center max-w-2xl mx-auto mb-14">
            <h2 class="text-3xl font-bold text-gray-900 tracking-tight">
                Mengapa Menggunakan Recyclink?
            </h2>
            <p class="mt-4 text-gray-600">
                Kami menyediakan ekosistem yang aman dan efisien bagi pelaku industri untuk mengelola sumber daya sisa.
            </p>
        </div>

        {{-- Bento Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            
            {{-- Card 1: Verifikasi Aman (Span 2) --}}
            <div class="md:col-span-2 bg-brand rounded-3xl p-8 relative overflow-hidden flex flex-col justify-center group hover:-translate-y-1 hover:shadow-xl hover:shadow-brand/30 transition-all duration-300">
                <div class="absolute right-6 top-1/2 -translate-y-1/2 w-24 h-24 bg-white/10 rounded-full flex items-center justify-center group-hover:scale-110 transition-transform duration-500">
                    <i data-lucide="shield-check" class="w-10 h-10 text-white"></i>
                </div>
                <div class="relative z-10 w-3/4">
                    <h3 class="text-2xl font-bold text-white mb-3">Verifikasi Aman</h3>
                    <p class="text-green-50 text-sm leading-relaxed">
                        Setiap penjual dan pembeli melewati proses verifikasi identitas dan legalitas usaha yang ketat untuk menjamin keamanan transaksi.
                    </p>
                </div>
            </div>

            {{-- Card 2: Transaksi Mudah (Span 1) --}}
            <div class="md:col-span-1 bg-white border border-gray-200 rounded-3xl p-8 flex flex-col group hover:-translate-y-1 hover:shadow-xl hover:border-brand/30 transition-all duration-300">
                <i data-lucide="banknote" class="w-8 h-8 text-brand mb-6 group-hover:scale-110 transition-transform duration-300 transform origin-left"></i>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Transaksi Mudah</h3>
                <p class="text-gray-500 text-sm">Sistem pembayaran escrow terintegrasi.</p>
            </div>

            {{-- Card 3: Jaringan Luas (Span 1) --}}
            <div class="md:col-span-1 bg-white border border-gray-200 rounded-3xl p-8 flex flex-col group hover:-translate-y-1 hover:shadow-xl hover:border-brand/30 transition-all duration-300">
                <i data-lucide="network" class="w-8 h-8 text-brand mb-6 group-hover:scale-110 transition-transform duration-300 transform origin-left"></i>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Jaringan Luas</h3>
                <p class="text-gray-500 text-sm">Ribuan mitra industri di seluruh Indonesia.</p>
            </div>

            {{-- Card 4: Laporan ESG (Span 1) --}}
            <div class="md:col-span-1 bg-white border border-gray-200 rounded-3xl p-8 flex flex-col group hover:-translate-y-1 hover:shadow-xl hover:border-brand/30 transition-all duration-300">
                <i data-lucide="bar-chart-2" class="w-8 h-8 text-brand mb-6 group-hover:scale-110 transition-transform duration-300 transform origin-left"></i>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Laporan ESG</h3>
                <p class="text-gray-500 text-sm">Data dampak lingkungan yang terukur.</p>
            </div>

            {{-- Card 5: Bantuan 24/7 (Span 2) --}}
            <div class="md:col-span-2 bg-[#a6ebd1] rounded-3xl p-8 flex flex-col justify-center group hover:-translate-y-1 hover:shadow-xl transition-all duration-300">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform duration-300">
                        <i data-lucide="headset" class="w-6 h-6 text-brand"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-brand">Bantuan 24/7</h3>
                </div>
                <p class="text-brand text-sm leading-relaxed">
                    Tim ahli kami siap membantu proses logistik dan konsultasi regulasi limbah kapan saja Anda butuhkan.
                </p>
            </div>

            {{-- Card 6: Logistik Terpadu (Span 1) --}}
            <div class="md:col-span-1 bg-white border border-gray-200 rounded-3xl p-8 flex flex-col group hover:-translate-y-1 hover:shadow-xl hover:border-brand/30 transition-all duration-300">
                <i data-lucide="truck" class="w-8 h-8 text-brand mb-6 group-hover:scale-110 transition-transform duration-300 transform origin-left"></i>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Logistik Terpadu</h3>
                <p class="text-gray-500 text-sm">Armada khusus limbah B3 dan Non-B3.</p>
            </div>

        </div>
    </div>
</section>