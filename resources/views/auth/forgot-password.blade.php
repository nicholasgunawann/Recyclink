<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="turbo-prefetch" content="true">
    <title>Lupa Sandi - Recyclink</title>
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
                Keamanan Akun Anda Adalah Prioritas Kami
            </h1>
            <p class="text-lg text-white/90 max-w-lg leading-relaxed">
                Kami akan membantu Anda memulihkan akses ke akun Recyclink dengan mudah dan aman.
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
                    <i data-lucide="key" class="w-7 h-7 text-brand"></i>
                </div>
                <h2 class="text-3xl font-bold text-gray-900">Lupa Kata Sandi?</h2>
                <p class="mt-3 text-gray-600 leading-relaxed">Jangan khawatir! Masukkan alamat email yang terdaftar, dan kami akan mengirimkan tautan untuk mengatur ulang kata sandi Anda.</p>
            </div>

            <form action="#" method="POST" class="space-y-6">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Alamat Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i data-lucide="mail" class="w-5 h-5 text-gray-400"></i>
                        </div>
                        <input type="email" name="email" id="email" required
                               class="block w-full pl-11 pr-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:ring-2 focus:ring-brand focus:border-brand transition-colors outline-none"
                               placeholder="nama@email.com">
                    </div>
                </div>

                <button type="button"
                        class="w-full flex justify-center py-3.5 px-4 border border-transparent rounded-xl shadow-sm text-base font-bold text-white bg-brand hover:bg-brand-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand transition-colors">
                    Kirim Tautan Reset
                </button>
            </form>
            
            <div class="mt-8 pt-6 border-t border-gray-100 text-center flex flex-col gap-4">
                <a href="{{ route('login') }}" class="inline-flex items-center justify-center gap-2 text-sm font-bold text-gray-600 hover:text-brand transition-colors">
                    Kembali ke halaman Masuk
                </a>
                <a href="{{ url('/') }}" class="inline-flex items-center justify-center gap-2 text-sm font-medium text-gray-500 hover:text-brand transition-colors">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    Kembali ke Beranda
                </a>
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
