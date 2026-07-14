<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="turbo-prefetch" content="true">
    <title>Verifikasi Email - Recyclink</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/lucide@0.460.0/dist/umd/lucide.min.js"></script>
    <script type="module" src="https://cdn.jsdelivr.net/npm/@hotwired/turbo@8.0.4/dist/turbo.es2017-umd.js"></script>
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
                Satu Langkah Lagi Menuju Dunia yang Lebih Hijau
            </h1>
            <p class="text-lg text-white/90 max-w-lg leading-relaxed">
                Verifikasi email Anda sekarang untuk mulai bergabung dengan komunitas peduli lingkungan Recyclink.
            </p>
        </div>
        
        <div class="relative z-10 text-white/70 text-sm">
            &copy; {{ date('Y') }} Recyclink. Semua hak dilindungi.
        </div>
    </div>

    <!-- Right Side: Form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-12 overflow-y-auto">
        <div class="max-w-md w-full mt-10 lg:mt-0">
            
            <div class="lg:hidden text-center mb-10">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-2 mb-6">
                    <img src="{{ asset('images/logo.png') }}" alt="Recyclink Logo" class="h-10 w-auto mx-auto">
                    <span class="text-2xl font-bold text-brand tracking-tight">Recyclink</span>
                </a>
            </div>

            <div class="mb-10 text-center lg:text-left">
                <div class="w-14 h-14 bg-brand/10 rounded-full flex items-center justify-center mb-6 mx-auto lg:mx-0">
                    <i data-lucide="mail-check" class="w-7 h-7 text-brand"></i>
                </div>
                <h2 class="text-3xl font-bold text-gray-900">Verifikasi Email Anda</h2>
                <p class="mt-3 text-gray-600 leading-relaxed">Terima kasih telah mendaftar! Sebelum memulai, bisakah Anda memverifikasi alamat email Anda dengan mengeklik tautan yang baru saja kami kirimkan ke email Anda? Jika Anda tidak menerima email tersebut, kami akan dengan senang hati mengirimkan yang baru.</p>
            </div>

            <form action="{{ route('verification.send') ?? '#' }}" method="POST" class="space-y-6">
                @csrf
                <button type="submit"
                        class="w-full flex justify-center py-3.5 px-4 border border-transparent rounded-xl shadow-sm text-base font-bold text-white bg-brand hover:bg-brand-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand transition-colors">
                    Kirim Ulang Email Verifikasi
                </button>
            </form>
            
            <div class="mt-8 pt-6 border-t border-gray-100 text-center flex flex-col gap-4">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="inline-flex items-center justify-center gap-2 text-sm font-bold text-red-600 hover:text-red-700 transition-colors bg-red-50 hover:bg-red-100 px-4 py-2.5 rounded-xl w-full">
                        <i data-lucide="log-out" class="w-4 h-4"></i>
                        Keluar Akun
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <script>document.addEventListener("turbo:load", function() {
            lucide.createIcons();
        });
        if (!window.Turbo) lucide.createIcons();</script>
    @include('layouts.global-loader')
</body>
</html>
