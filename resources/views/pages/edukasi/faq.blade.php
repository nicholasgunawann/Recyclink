@php
    $faqs = [
        [
            'question' => 'Bagaimana sistem pembayaran aman (Escrow) bekerja?',
            'answer' => 'Dana yang dibayarkan pembeli akan ditahan oleh sistem Recyclink secara aman. Dana hanya akan diteruskan kepada penjual setelah pembeli melakukan konfirmasi penerimaan barang dan melakukan pengecekan kualitas sesuai dengan deskripsi yang tercantum saat transaksi.',
            'active' => true,
        ],
        [
            'question' => 'Apakah Recyclink menyediakan jasa pengangkutan limbah?',
            'answer' => 'Tidak, saat ini kami tidak menyediakan layanan logistik pengangkutan. Sistem transaksi di Recyclink dilakukan secara COD (Cash on Delivery) atau kesepakatan pertemuan langsung antara pembeli dan penjual.',
            'active' => false,
        ],
        [
            'question' => 'Apa yang harus dilakukan jika material tidak sesuai pesanan?',
            'answer' => 'Anda dapat mengajukan komplain melalui pusat resolusi kami dalam waktu maksimal 2x24 jam setelah barang diterima. Dana akan ditahan sementara tim kami membantu memediasi dan memberikan solusi terbaik.',
            'active' => false,
        ],
        [
            'question' => 'Bagaimana cara menjadi mitra penjual terverifikasi?',
            'answer' => 'Anda perlu melengkapi profil perusahaan, mengunggah dokumen legalitas bisnis, dan melewati proses verifikasi oleh tim internal Recyclink. Status terverifikasi akan meningkatkan kepercayaan pembeli secara signifikan.',
            'active' => false,
        ],
    ];
@endphp

<section class="py-12 bg-gray-50/50 min-h-[60vh]">
    <div class="w-full max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        
        {{-- Section Header --}}
        <div class="mb-10">
            <span class="inline-flex items-center gap-1.5 bg-brand/10 text-brand px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider mb-3">
                <i data-lucide="help-circle" class="w-3.5 h-3.5"></i> FAQ
            </span>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">Pertanyaan yang Sering Diajukan</h2>
            <p class="text-gray-500 text-sm sm:text-base mt-1 max-w-xl mx-auto">
                Segala hal yang perlu Anda ketahui tentang penggunaan platform marketplace limbah kami.
            </p>
        </div>

        {{-- Accordion --}}
        <div class="space-y-3.5 text-left">
            @foreach($faqs as $index => $faq)
            <details name="faq-accordion" class="group bg-white border border-gray-200/80 open:border-brand/40 open:shadow-md rounded-2xl overflow-hidden transition-all duration-300" {{ $faq['active'] ? 'open' : '' }}>
                <summary class="w-full px-6 py-5 flex items-center justify-between focus:outline-none list-none cursor-pointer select-none">
                    <span class="font-bold text-gray-900 text-sm sm:text-base text-left group-open:text-brand transition-colors">{{ $faq['question'] }}</span>
                    <div class="w-8 h-8 rounded-full bg-gray-50 group-open:bg-brand/10 flex items-center justify-center shrink-0 ml-4 transition-colors">
                        <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400 group-open:text-brand group-open:rotate-180 transition-transform duration-300"></i>
                    </div>
                </summary>
                <div class="px-6 pb-6 pt-1 border-t border-gray-100/80">
                    <p class="text-gray-600 text-sm leading-relaxed">
                        {{ $faq['answer'] }}
                    </p>
                </div>
            </details>
            @endforeach
        </div>
        
        {{-- Contact Support --}}
        <div class="mt-12 p-8 bg-white border border-gray-200/80 rounded-2xl shadow-xs flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="text-left">
                <h4 class="font-bold text-gray-900 text-base">Masih Punya Pertanyaan Lain?</h4>
                <p class="text-xs sm:text-sm text-gray-500 mt-0.5">Tim pusat bantuan kami siap membantu operasional daur ulang Anda.</p>
            </div>
            <a href="mailto:support@recyclink.id" class="inline-flex items-center gap-2 bg-brand text-white px-5 py-3 rounded-xl font-bold text-sm hover:bg-brand-hover transition-all shadow-sm shrink-0 whitespace-nowrap">
                <i data-lucide="message-circle" class="w-4 h-4"></i> Hubungi Support 24/7
            </a>
        </div>
        
    </div>
</section>
