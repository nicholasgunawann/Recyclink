<footer class="bg-white border-t border-gray-100" id="footer">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-10">

            {{-- Brand --}}
            <div class="md:col-span-1">
                <a href="{{ url('/') }}" class="flex items-center gap-2.5 mb-4">
                    <img src="{{ asset('images/logo.png') }}" alt="Recyclink" class="h-9 w-auto">
                    <span class="text-lg font-bold text-gray-900">Recyclink</span>
                </a>
                <p class="text-sm text-gray-500 leading-relaxed">
                    Platform marketplace limbah yang menghubungkan penghasil limbah dengan pengepul dan industri daur ulang secara transparan.
                </p>
                {{-- Sosial Media --}}
                <div class="flex items-center gap-3 mt-5">
                    <a href="#" aria-label="Facebook"
                       class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 text-gray-500 hover:bg-brand hover:text-white transition-colors">
                        <i data-lucide="facebook" class="w-4 h-4"></i>
                    </a>
                    <a href="#" aria-label="Instagram"
                       class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 text-gray-500 hover:bg-brand hover:text-white transition-colors">
                        <i data-lucide="instagram" class="w-4 h-4"></i>
                    </a>
                    <a href="#" aria-label="TikTok"
                       class="w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 text-gray-500 hover:bg-brand hover:text-white transition-colors">
                        <i data-lucide="music" class="w-4 h-4"></i>
                    </a>
                </div>
            </div>

            {{-- Platform --}}
            <div>
                <h4 class="text-sm font-semibold text-gray-900 mb-4">Platform</h4>
                <ul class="space-y-2.5">
                    <li><a href="#" class="text-sm text-gray-500 hover:text-brand transition-colors">Jelajah Limbah</a></li>
                    <li><a href="#" class="text-sm text-gray-500 hover:text-brand transition-colors">Pasang Iklan Limbah</a></li>
                    <li><a href="#" class="text-sm text-gray-500 hover:text-brand transition-colors">Kategori Limbah</a></li>
                    <li><a href="#" class="text-sm text-gray-500 hover:text-brand transition-colors">Harga Limbah Terkini</a></li>
                </ul>
            </div>

            {{-- Perusahaan --}}
            <div>
                <h4 class="text-sm font-semibold text-gray-900 mb-4">Perusahaan</h4>
                <ul class="space-y-2.5">
                    <li><a href="{{ url('/tentang') }}" class="text-sm text-gray-500 hover:text-brand transition-colors">Tentang & Kontak</a></li>
                    <li><a href="{{ url('/edukasi') }}" class="text-sm text-gray-500 hover:text-brand transition-colors">Edukasi</a></li>
                    <li><a href="#" class="text-sm text-gray-500 hover:text-brand transition-colors">Karir</a></li>
                </ul>
            </div>

            {{-- Dukungan --}}
            <div>
                <h4 class="text-sm font-semibold text-gray-900 mb-4">Dukungan</h4>
                <ul class="space-y-2.5">
                    <li><a href="#" class="text-sm text-gray-500 hover:text-brand transition-colors">Pusat Bantuan</a></li>
                    <li><a href="#" class="text-sm text-gray-500 hover:text-brand transition-colors">Hubungi Kami</a></li>
                    <li><a href="#" class="text-sm text-gray-500 hover:text-brand transition-colors">Kebijakan Privasi</a></li>
                    <li><a href="#" class="text-sm text-gray-500 hover:text-brand transition-colors">Syarat & Ketentuan</a></li>
                </ul>
            </div>
        </div>

        {{-- Bottom Bar --}}
        <div class="mt-12 pt-6 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-3">
            <p class="text-xs text-gray-400">
                &copy; {{ date('Y') }} Recyclink. Semua hak dilindungi.
            </p>
            <p class="text-xs text-gray-400 flex items-center gap-1">
                Dibuat dengan
                <i data-lucide="heart" class="w-3.5 h-3.5 text-brand fill-brand"></i>
                untuk bumi yang lebih hijau.
            </p>
        </div>
    </div>
</footer>
