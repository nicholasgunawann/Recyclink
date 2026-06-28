<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Peran - Recyclink</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
</head>
<body class="bg-gray-50 flex min-h-screen antialiased flex-col items-center justify-center p-6">
    
    <div class="max-w-3xl w-full text-center mb-10">
        <div class="inline-flex items-center gap-3 bg-white px-6 py-3 rounded-full shadow-sm mb-8 border border-gray-100">
            <img src="{{ asset('images/logo.png') }}" alt="Recyclink Logo" class="h-8 w-auto">
            <span class="text-2xl font-bold text-brand tracking-tight">Recyclink</span>
        </div>
        
        @if(session('success'))
            <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl flex items-center justify-center gap-3 max-w-lg mx-auto">
                <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600"></i>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        <h1 class="text-4xl font-extrabold text-gray-900 mb-4">Selamat Akun Anda Telah Diverifikasi!</h1>
        <p class="text-lg text-gray-600 max-w-xl mx-auto">
            Langkah terakhir, silakan pilih peran Anda. Apakah Anda ingin menjadi penjual limbah (UMKM/Masyarakat) atau pembeli limbah (Industri)?
        </p>
    </div>

    <form action="{{ route('choose.role.store') }}" method="POST" class="w-full max-w-4xl grid md:grid-cols-2 gap-6">
        @csrf
        
        <!-- Seller Option -->
        <label class="relative block cursor-pointer group">
            <input type="radio" name="role" value="seller" class="peer sr-only" required>
            <div class="h-full bg-white rounded-3xl p-8 border-2 border-transparent peer-checked:border-brand peer-checked:ring-4 peer-checked:ring-brand/20 transition-all hover:-translate-y-1 hover:shadow-xl shadow-md flex flex-col items-center text-center">
                <div class="w-20 h-20 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                    <i data-lucide="store" class="w-10 h-10"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Penjual Limbah</h3>
                <p class="text-gray-500 mb-6">Masyarakat / UMKM</p>
                <p class="text-gray-600">Saya ingin menjual, menyalurkan, dan mendapatkan keuntungan dari limbah daur ulang.</p>
                <div class="mt-auto pt-6">
                    <span class="inline-flex items-center gap-2 text-brand font-bold opacity-0 group-hover:opacity-100 peer-checked:opacity-100 transition-opacity">
                        Pilih Peran Ini <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </span>
                </div>
            </div>
            <!-- Checkmark Indicator -->
            <div class="absolute top-6 right-6 w-8 h-8 rounded-full bg-brand text-white flex items-center justify-center opacity-0 scale-50 peer-checked:opacity-100 peer-checked:scale-100 transition-all shadow-lg">
                <i data-lucide="check" class="w-5 h-5"></i>
            </div>
        </label>

        <!-- Buyer Option -->
        <label class="relative block cursor-pointer group">
            <input type="radio" name="role" value="buyer" class="peer sr-only" required>
            <div class="h-full bg-white rounded-3xl p-8 border-2 border-transparent peer-checked:border-brand peer-checked:ring-4 peer-checked:ring-brand/20 transition-all hover:-translate-y-1 hover:shadow-xl shadow-md flex flex-col items-center text-center">
                <div class="w-20 h-20 bg-amber-50 text-amber-600 rounded-full flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                    <i data-lucide="factory" class="w-10 h-10"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-2">Pembeli Limbah</h3>
                <p class="text-gray-500 mb-6">Pabrik / Industri</p>
                <p class="text-gray-600">Saya ingin mencari dan membeli bahan baku limbah daur ulang untuk kebutuhan industri.</p>
                <div class="mt-auto pt-6">
                    <span class="inline-flex items-center gap-2 text-brand font-bold opacity-0 group-hover:opacity-100 peer-checked:opacity-100 transition-opacity">
                        Pilih Peran Ini <i data-lucide="arrow-right" class="w-4 h-4"></i>
                    </span>
                </div>
            </div>
            <!-- Checkmark Indicator -->
            <div class="absolute top-6 right-6 w-8 h-8 rounded-full bg-brand text-white flex items-center justify-center opacity-0 scale-50 peer-checked:opacity-100 peer-checked:scale-100 transition-all shadow-lg">
                <i data-lucide="check" class="w-5 h-5"></i>
            </div>
        </label>

        <div class="md:col-span-2 text-center mt-8">
            <button type="submit" class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-brand hover:bg-brand-hover text-white font-bold rounded-2xl shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all w-full sm:w-auto min-w-[250px] text-lg">
                Lanjutkan ke Dashboard
                <i data-lucide="arrow-right" class="w-5 h-5"></i>
            </button>
        </div>
    </form>
    
    <script>lucide.createIcons();</script>
</body>
</html>
