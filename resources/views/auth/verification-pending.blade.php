<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menunggu Verifikasi - Recyclink</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
</head>
<body class="bg-white flex min-h-screen antialiased">
    
    <!-- Left Side: Branding -->
    <div class="hidden lg:flex lg:w-1/2 bg-brand relative flex-col justify-between p-12 overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0">
            <div class="absolute -top-[20%] -left-[10%] w-[70%] h-[70%] rounded-full bg-white/10 blur-3xl"></div>
            <div class="absolute -bottom-[20%] -right-[10%] w-[60%] h-[60%] rounded-full bg-white/10 blur-3xl"></div>
        </div>

        <div class="relative z-10">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-3">
                <div class="bg-white p-1.5 rounded-xl shadow-sm">
                    <img src="{{ asset('images/logo.png') }}" alt="Recyclink Logo" class="h-9 w-auto">
                </div>
                <span class="text-3xl font-bold text-white tracking-tight">Recyclink</span>
            </a>
        </div>
        
        <div class="relative z-10 mb-20">
            <h1 class="text-4xl font-extrabold text-white mb-6 leading-tight">
                Terima Kasih Telah Bergabung!
            </h1>
            <p class="text-lg text-white/90 max-w-lg leading-relaxed">
                Kami sangat menghargai antusiasme Anda. Saat ini, tim kami sedang meninjau pendaftaran Anda.
            </p>
        </div>
        
        <div class="relative z-10 text-white/70 text-sm">
            &copy; {{ date('Y') }} Recyclink. Semua hak dilindungi.
        </div>
    </div>

    <!-- Right Side: Content -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-12 overflow-y-auto">
        <div class="max-w-md w-full mt-10 lg:mt-0 text-center">
            
            <div class="lg:hidden text-center mb-10">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-2 mb-6">
                    <img src="{{ asset('images/logo.png') }}" alt="Recyclink Logo" class="h-10 w-auto mx-auto">
                    <span class="text-2xl font-bold text-brand tracking-tight">Recyclink</span>
                </a>
            </div>

            <div class="mb-10 text-center">
                @if($user->isActive())
                    <div class="w-20 h-20 bg-emerald-50 rounded-full flex items-center justify-center mb-6 mx-auto">
                        <i data-lucide="check-circle" class="w-10 h-10 text-emerald-500"></i>
                    </div>
                    <h2 class="text-3xl font-bold text-gray-900">Verifikasi Berhasil!</h2>
                    <p class="mt-4 text-gray-600 leading-relaxed text-lg">
                        Selamat! Akun Anda telah berhasil diverifikasi oleh tim kami. Anda sekarang dapat mulai menggunakan layanan Recyclink.
                    </p>
                    <div class="mt-8">
                        <form action="{{ route('verification.acknowledge') }}" method="POST">
                            @csrf
                            <button type="submit" class="inline-flex items-center justify-center gap-2 text-sm font-bold text-white transition-colors bg-brand hover:bg-brand-hover px-6 py-3.5 rounded-xl w-full sm:w-auto shadow-sm">
                                Masuk ke Dashboard
                                <i data-lucide="arrow-right" class="w-4 h-4"></i>
                            </button>
                        </form>
                    </div>
                @else
                    <div class="w-20 h-20 bg-amber-50 rounded-full flex items-center justify-center mb-6 mx-auto">
                        <i data-lucide="clock" class="w-10 h-10 text-amber-500"></i>
                    </div>
                    <h2 class="text-3xl font-bold text-gray-900">Menunggu Verifikasi</h2>
                    <p class="mt-4 text-gray-600 leading-relaxed text-lg">
                        Tunggu beberapa saat ya, akun sedang tahap verifikasi. Silakan periksa kembali nanti!
                    </p>
                @endif
            </div>
            
            <div class="mt-8 pt-6 border-t border-gray-100 flex flex-col gap-4">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="inline-flex items-center justify-center gap-2 text-sm font-bold text-gray-600 hover:text-red-600 transition-colors bg-gray-50 hover:bg-red-50 px-4 py-3 rounded-xl w-full border border-gray-200 hover:border-red-200">
                        <i data-lucide="log-out" class="w-4 h-4"></i>
                        Keluar Akun
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <script>lucide.createIcons();</script>
</body>
</html>
