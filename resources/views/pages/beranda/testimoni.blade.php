{{-- ═══════════════════════════════════════════════════════
    Section 6: Testimoni & Mitra
    Lokasi: resources/views/pages/beranda/testimoni.blade.php
════════════════════════════════════════════════════════ --}}
<section class="bg-gray-50 py-20" id="testimoni">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- ── Testimoni ── --}}
        <div class="text-center max-w-xl mx-auto mb-14 scroll-reveal">
            <span class="text-xs font-semibold text-brand uppercase tracking-widest">Testimoni</span>
            <h2 class="mt-2 text-3xl font-bold text-gray-900 tracking-tight">
                Dipercaya Ribuan Pengguna
            </h2>
            <p class="mt-3 text-gray-500">
                Bergabunglah bersama pelaku UMKM dan pengepul yang sudah merasakan manfaat Recyclink.
            </p>
        </div>

        @php
            $testimonials = [
                [
                    'name'    => 'Garneta A.',
                    'role'    => 'Mahasiswa',
                    'avatar'  => 'GA',
                    'rating'  => 5,
                    'review'  => 'Wow website ini sangat bermanfaat, biasanya saya bingung sampah seperti kertas atau kardus harus dibuang kemana untuk mengurangi limbah, tapi semenjak ada recyclink sekarang sampah saya bisa bermanfaat dan bahkan menjadi uang 🥰🥰😍😍.',
                ],
                [
                    'name'    => 'Mizkya',
                    'role'    => 'Mahasiswa',
                    'avatar'  => 'MZ',
                    'rating'  => 5,
                    'review'  => 'Website recyclink sangat mudah untuk dipahami dan diakses. Ada berbagai macam kategori limbah dengan deskripsi yang sesuai difoto. Jadi lebih mudah untuk cari barang bekas untuk didaur ulang menjadi suatu kerajinan tangan 👍🏻👍🏻.',
                ],
                [
                    'name'    => 'Nikita',
                    'role'    => 'Anak Kost',
                    'avatar'  => 'NK',
                    'rating'  => 5,
                    'review'  => 'Sebagai anak kost, berkat recyclink jujur baru tau ternyata bekas galon bisa didaur ulang jadi tempat simpanan.',
                ],
                [
                    'name'    => 'Bu Lala',
                    'role'    => 'Pemilik Warung Madura',
                    'avatar'  => 'BL',
                    'rating'  => 5,
                    'review'  => 'Limbah kardus saya jdi ga menumpuk banyak di ruko. Ternyata banyak mahasiswa yang butuh berkat dikenalkan oleh recyclink.',
                ],
            ];
        @endphp

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-20">
            @foreach($testimonials as $t)
                <div class="bg-white border border-gray-100 rounded-2xl p-6 hover:shadow-md transition-shadow duration-300 scroll-reveal flex flex-col justify-between">
                    <div>
                        {{-- Stars --}}
                        <div class="flex items-center gap-0.5 mb-4">
                            @for($i = 0; $i < $t['rating']; $i++)
                                <svg class="w-4 h-4 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                </svg>
                            @endfor
                        </div>

                        <p class="text-sm text-gray-600 leading-relaxed mb-5 italic">
                            "{{ $t['review'] }}"
                        </p>
                    </div>

                    {{-- Author --}}
                    <div class="flex items-center gap-3 pt-4 border-t border-gray-50">
                        <div class="w-9 h-9 rounded-full bg-brand/10 text-brand text-xs font-bold flex items-center justify-center shrink-0">
                            {{ $t['avatar'] }}
                        </div>
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-gray-900 truncate">{{ $t['name'] }}</p>
                            <p class="text-xs text-gray-400 truncate">{{ $t['role'] }}</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
