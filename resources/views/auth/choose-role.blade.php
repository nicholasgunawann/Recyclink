<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Peran - Recyclink</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <style>
        .role-card { border: 2px solid #e5e7eb; transition: all 0.2s ease; }
        .role-card.active {
            border-color: #16a34a;
            box-shadow: 0 0 0 4px rgba(22,163,74,0.15);
        }
        .role-card .check-badge {
            opacity: 0;
            transform: scale(0.5);
            transition: all 0.2s ease;
        }
        .role-card.active .check-badge {
            opacity: 1;
            transform: scale(1);
        }
        .role-card .pilih-label { opacity: 0; transition: opacity 0.2s ease; }
        .role-card:hover .pilih-label { opacity: 1; }
        .role-card.active .pilih-label { opacity: 1; color: #16a34a; }
    </style>
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

    <form action="{{ route('choose.role.store') }}" method="POST" class="w-full max-w-4xl">
        @csrf
        <input type="hidden" name="role" id="selectedRole" value="">

        <div class="grid md:grid-cols-2 gap-6 mb-10">

            {{-- Seller Card --}}
            <div class="role-card relative bg-white rounded-3xl p-8 shadow-md cursor-pointer"
                 id="card-seller"
                 onclick="selectRole('seller')">
                <div class="check-badge absolute top-5 right-5 w-8 h-8 rounded-full bg-green-600 text-white flex items-center justify-center shadow-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
                <div class="flex flex-col items-center text-center">
                    <div class="w-20 h-20 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mb-6">
                        <i data-lucide="store" class="w-10 h-10"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Penjual Limbah</h3>
                    <p class="text-gray-500 mb-4">Masyarakat / UMKM</p>
                    <p class="text-gray-600">Saya ingin menjual, menyalurkan, dan mendapatkan keuntungan dari limbah daur ulang.</p>
                    <div class="mt-6">
                        <span class="pilih-label inline-flex items-center gap-2 text-brand font-bold">
                            Pilih Peran Ini <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </span>
                    </div>
                </div>
            </div>

            {{-- Buyer Card --}}
            <div class="role-card relative bg-white rounded-3xl p-8 shadow-md cursor-pointer"
                 id="card-buyer"
                 onclick="selectRole('buyer')">
                <div class="check-badge absolute top-5 right-5 w-8 h-8 rounded-full bg-green-600 text-white flex items-center justify-center shadow-lg">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                </div>
                <div class="flex flex-col items-center text-center">
                    <div class="w-20 h-20 bg-amber-50 text-amber-600 rounded-full flex items-center justify-center mb-6">
                        <i data-lucide="factory" class="w-10 h-10"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Pembeli Limbah</h3>
                    <p class="text-gray-500 mb-4">Pabrik / Industri</p>
                    <p class="text-gray-600">Saya ingin mencari dan membeli bahan baku limbah daur ulang untuk kebutuhan industri.</p>
                    <div class="mt-6">
                        <span class="pilih-label inline-flex items-center gap-2 text-brand font-bold">
                            Pilih Peran Ini <i data-lucide="arrow-right" class="w-4 h-4"></i>
                        </span>
                    </div>
                </div>
            </div>

        </div>

        <div class="text-center">
            <button type="submit" id="submitBtn"
                    disabled
                    class="inline-flex items-center justify-center gap-2 px-8 py-4 text-white font-bold rounded-2xl shadow-lg transition-all w-full sm:w-auto min-w-[250px] text-lg opacity-50 cursor-not-allowed"
                    style="background-color: #16a34a;">
                Lanjutkan ke Dashboard
                <i data-lucide="arrow-right" class="w-5 h-5"></i>
            </button>
            <p class="text-gray-400 text-sm mt-3" id="hintText">Pilih peran terlebih dahulu</p>
        </div>
    </form>

    <script>
        function selectRole(role) {
            document.getElementById('selectedRole').value = role;

            document.getElementById('card-seller').classList.toggle('active', role === 'seller');
            document.getElementById('card-buyer').classList.toggle('active', role === 'buyer');

            const btn = document.getElementById('submitBtn');
            btn.disabled = false;
            btn.classList.remove('opacity-50', 'cursor-not-allowed');

            const label = role === 'seller' ? 'Penjual Limbah' : 'Pembeli Limbah';
            const hint = document.getElementById('hintText');
            hint.textContent = '✓ ' + label + ' dipilih';
            hint.className = 'text-green-600 text-sm font-medium mt-3';
        }

        lucide.createIcons();
    </script>
</body>
</html>
