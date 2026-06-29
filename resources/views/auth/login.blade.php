<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk - Recyclink</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
</head>
<body class="bg-white flex min-h-screen antialiased">
    
    <!-- Left Side: Branding (Hidden on mobile) -->
    <div class="hidden lg:flex lg:w-1/2 bg-brand relative flex-col justify-between p-12 overflow-hidden">
        <!-- Background Decoration -->
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
            <h1 class="text-4xl lg:text-5xl font-extrabold text-white mb-6 leading-tight">
                Selamat Datang Kembali di Recyclink
            </h1>
            <p class="text-lg text-white/90 max-w-lg leading-relaxed">
                Platform marketplace limbah terpercaya. Kelola limbah Anda menjadi lebih bernilai dan dukung terciptanya bumi yang lebih hijau bersama kami.
            </p>
        </div>
        
        <div class="relative z-10 text-white/70 text-sm">
            &copy; {{ date('Y') }} Recyclink. Semua hak dilindungi.
        </div>
    </div>

    <!-- Right Side: Form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center p-6 sm:p-12 overflow-y-auto">
        <div class="max-w-md w-full mt-10 lg:mt-0">
            
            <!-- Mobile Header -->
            <div class="lg:hidden text-center mb-10">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-2 mb-6">
                    <img src="{{ asset('images/logo.png') }}" alt="Recyclink Logo" class="h-10 w-auto mx-auto">
                    <span class="text-2xl font-bold text-brand tracking-tight">Recyclink</span>
                </a>
                <h2 class="text-2xl font-bold text-gray-900">Selamat Datang</h2>
                <p class="mt-2 text-sm text-gray-600">Masuk ke akun Anda untuk melanjutkan</p>
            </div>

            <!-- Desktop Header -->
            <div class="hidden lg:block mb-10">
                <h2 class="text-3xl font-bold text-gray-900">Masuk ke Akun</h2>
                <p class="mt-2 text-gray-600">Silakan masukkan email dan kata sandi Anda.</p>
            </div>

            @if(session('error'))
                <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-100 flex items-start gap-3">
                    <i data-lucide="alert-circle" class="w-5 h-5 text-red-500 shrink-0 mt-0.5"></i>
                    <p class="text-sm text-red-700">{{ session('error') }}</p>
                </div>
            @endif

            @if(session('success'))
                <div class="mb-6 p-4 rounded-xl bg-green-50 border border-brand/20 flex items-start gap-3">
                    <i data-lucide="check-circle-2" class="w-5 h-5 text-brand shrink-0 mt-0.5"></i>
                    <p class="text-sm text-brand-dark">{{ session('success') }}</p>
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-6">
                @csrf
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Alamat Email</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i data-lucide="mail" class="w-5 h-5 text-gray-400"></i>
                        </div>
                        <input type="email" name="email" id="email" required value="{{ old('email') }}"
                               class="block w-full pl-11 pr-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:ring-2 focus:ring-brand focus:border-brand transition-colors outline-none"
                               placeholder="nama@email.com">
                    </div>
                    @error('email')
                        <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Kata Sandi</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i data-lucide="lock" class="w-5 h-5 text-gray-400"></i>
                        </div>
                        <input type="password" name="password" id="password" required
                               class="block w-full pl-11 pr-12 py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:ring-2 focus:ring-brand focus:border-brand transition-colors outline-none"
                               placeholder="••••••••">
                        <button type="button" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600 transition-colors btn-toggle-password" data-target="password">
                            <i data-lucide="eye" class="w-5 h-5 icon-eye"></i>
                            <i data-lucide="eye-off" class="w-5 h-5 icon-eye-off hidden"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox"
                               class="h-4 w-4 rounded border-gray-300 text-brand focus:ring-brand">
                        <label for="remember" class="ml-2 block text-sm text-gray-600">
                            Ingat saya
                        </label>
                    </div>
                    <div class="text-sm">
                        <a href="{{ url('/forgot-password') }}" class="font-semibold text-brand hover:text-brand-hover transition-colors">Lupa sandi?</a>
                    </div>
                </div>

                <button type="submit"
                        class="w-full flex justify-center py-3.5 px-4 border border-transparent rounded-xl shadow-sm text-base font-bold text-white bg-brand hover:bg-brand-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand transition-colors">
                    Masuk Sekarang
                </button>
            </form>
            
            <div class="mt-8 pt-6 border-t border-gray-100 text-center flex flex-col gap-4">
                <p class="text-sm text-gray-600">
                    Belum punya akun? 
                    <a href="{{ route('register') }}" class="font-bold text-brand hover:text-brand-hover transition-colors">Daftar Gratis</a>
                </p>
                <a href="{{ url('/') }}" class="inline-flex items-center justify-center gap-2 text-sm font-medium text-gray-500 hover:text-brand transition-colors">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
    
    <script>
        lucide.createIcons();
        
        // Password visibility toggle
        document.querySelectorAll('.btn-toggle-password').forEach(button => {
            button.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const input = document.getElementById(targetId);
                const iconEye = this.querySelector('.icon-eye');
                const iconEyeOff = this.querySelector('.icon-eye-off');
                
                if (input.type === 'password') {
                    input.type = 'text';
                    iconEye.classList.add('hidden');
                    iconEyeOff.classList.remove('hidden');
                } else {
                    input.type = 'password';
                    iconEye.classList.remove('hidden');
                    iconEyeOff.classList.add('hidden');
                }
            });
        });
    </script>
</body>
</html>
