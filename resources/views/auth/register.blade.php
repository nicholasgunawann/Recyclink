<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="turbo-prefetch" content="true">
    <title>Daftar - Recyclink</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script src="https://cdn.jsdelivr.net/npm/lucide@0.460.0/dist/umd/lucide.min.js"></script>
    <script type="module" src="https://cdn.jsdelivr.net/npm/@hotwired/turbo@8.0.4/dist/turbo.es2017-umd.js"></script>
</head>
<body class="bg-white flex min-h-screen antialiased">
    
    <!-- Left Side: Branding -->
    <div class="hidden lg:flex lg:w-[45%] bg-brand relative flex-col justify-between p-12 overflow-hidden fixed h-screen sticky top-0">
        <!-- Background Decoration -->
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden z-0">
            <div class="absolute -top-[10%] -left-[20%] w-[80%] h-[80%] rounded-full bg-white/10 blur-3xl"></div>
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
                Mulai Perjalanan Anda Bersama Kami
            </h1>
            <p class="text-lg text-white/90 max-w-lg leading-relaxed">
                Bergabunglah dengan ribuan pengguna lainnya dalam menciptakan ekosistem daur ulang yang transparan dan menguntungkan.
            </p>
        </div>
        
        <div class="relative z-10 text-white/70 text-sm">
            &copy; {{ date('Y') }} Recyclink. Semua hak dilindungi.
        </div>
    </div>

    <!-- Right Side: Form -->
    <div class="w-full lg:w-[55%] flex items-center justify-center p-6 sm:p-12 lg:px-20 overflow-y-auto">
        <div class="w-full max-w-xl mt-10 lg:mt-0">
            
            <!-- Mobile Header -->
            <div class="lg:hidden text-center mb-10">
                <a href="{{ url('/') }}" class="inline-flex items-center gap-2 mb-6">
                    <img src="{{ asset('images/logo.png') }}" alt="Recyclink Logo" class="h-10 w-auto mx-auto">
                    <span class="text-2xl font-bold text-brand tracking-tight">Recyclink</span>
                </a>
                <h2 class="text-2xl font-bold text-gray-900">Buat Akun Baru</h2>
                <p class="mt-2 text-sm text-gray-600">Bergabung bersama kami untuk bumi yang lebih hijau.</p>
            </div>

            <!-- Desktop Header -->
            <div class="hidden lg:block mb-10">
                <h2 class="text-3xl font-bold text-gray-900">Buat Akun Baru</h2>
                <p class="mt-2 text-gray-600">Lengkapi data di bawah ini untuk mendaftar.</p>
            </div>

            

            <form action="{{ route('register') }}" method="POST" class="space-y-6">
                @csrf
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Daftar Sebagai</label>
                    <div class="grid grid-cols-2 gap-4">
                        <label class="relative flex cursor-pointer rounded-xl border border-gray-200 bg-white p-4 shadow-sm focus:outline-none" id="label-role-buyer">
                            <input type="radio" name="role" value="buyer" class="peer sr-only" required onchange="toggleRoleFields()">
                            <div class="flex w-full items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="rounded-full bg-brand/10 p-2 text-brand">
                                        <i data-lucide="shopping-cart" class="h-5 w-5"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Pembeli</p>
                                        <p class="text-xs text-gray-500">Mencari limbah</p>
                                    </div>
                                </div>
                                <i data-lucide="check-circle-2" class="h-5 w-5 text-brand hidden peer-checked:block"></i>
                            </div>
                            <span class="pointer-events-none absolute -inset-px rounded-xl border-2 border-transparent peer-checked:border-brand" aria-hidden="true"></span>
                        </label>
                        
                        <label class="relative flex cursor-pointer rounded-xl border border-gray-200 bg-white p-4 shadow-sm focus:outline-none" id="label-role-seller">
                            <input type="radio" name="role" value="seller" class="peer sr-only" required onchange="toggleRoleFields()">
                            <div class="flex w-full items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="rounded-full bg-brand/10 p-2 text-brand">
                                        <i data-lucide="store" class="h-5 w-5"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Penjual</p>
                                        <p class="text-xs text-gray-500">Pengepul UMKM</p>
                                    </div>
                                </div>
                                <i data-lucide="check-circle-2" class="h-5 w-5 text-brand hidden peer-checked:block"></i>
                            </div>
                            <span class="pointer-events-none absolute -inset-px rounded-xl border-2 border-transparent peer-checked:border-brand" aria-hidden="true"></span>
                        </label>
                    </div>
                    @error('role') <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <!-- Basic Info Group -->
                    <div class="sm:col-span-2"><h3 class="text-lg font-semibold border-b pb-2">Informasi Akun</h3></div>
                    
                    <!-- Nama Lengkap -->
                    <div class="sm:col-span-2">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap PIC</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i data-lucide="user" class="w-5 h-5 text-gray-400"></i>
                            </div>
                            <input type="text" name="name" id="name" required value="{{ old('name') }}"
                                   class="block w-full pl-11 pr-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:ring-2 focus:ring-brand focus:border-brand transition-colors outline-none"
                                   placeholder="Budi Santoso">
                        </div>
                        @error('name') <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <!-- Email -->
                    <div class="sm:col-span-2">
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Alamat Email</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i data-lucide="mail" class="w-5 h-5 text-gray-400"></i>
                            </div>
                            <input type="email" name="email" id="email" required value="{{ old('email') }}"
                                   class="block w-full pl-11 pr-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:ring-2 focus:ring-brand focus:border-brand transition-colors outline-none"
                                   placeholder="nama@email.com">
                        </div>
                        @error('email') <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <!-- No HP -->
                    <div class="sm:col-span-2">
                        <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-2">No. WhatsApp</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i data-lucide="phone" class="w-5 h-5 text-gray-400"></i>
                            </div>
                            <input type="text" name="phone_number" id="phone_number" required value="{{ old('phone_number') }}"
                                   class="block w-full pl-11 pr-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:ring-2 focus:ring-brand focus:border-brand transition-colors outline-none"
                                   placeholder="08123456789">
                        </div>
                        @error('phone_number') <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Kata Sandi</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i data-lucide="lock" class="w-5 h-5 text-gray-400"></i>
                            </div>
                            <input type="password" name="password" id="password" required
                                   class="block w-full pl-11 pr-12 py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:ring-2 focus:ring-brand focus:border-brand transition-colors outline-none"
                                   placeholder="Minimal 8 karakter">
                            <button type="button" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600 transition-colors btn-toggle-password" data-target="password">
                                <i data-lucide="eye" class="w-5 h-5 icon-eye"></i>
                                <i data-lucide="eye-off" class="w-5 h-5 icon-eye-off hidden"></i>
                            </button>
                        </div>
                        @error('password') <p class="mt-1.5 text-sm text-red-500">{{ $message }}</p> @enderror
                    </div>

                    <!-- Konfirmasi Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Sandi</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <i data-lucide="lock" class="w-5 h-5 text-gray-400"></i>
                            </div>
                            <input type="password" name="password_confirmation" id="password_confirmation" required
                                   class="block w-full pl-11 pr-12 py-3.5 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:ring-2 focus:ring-brand focus:border-brand transition-colors outline-none"
                                   placeholder="Ulangi kata sandi">
                            <button type="button" class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-gray-600 transition-colors btn-toggle-password" data-target="password_confirmation">
                                <i data-lucide="eye" class="w-5 h-5 icon-eye"></i>
                                <i data-lucide="eye-off" class="w-5 h-5 icon-eye-off hidden"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Specific Profile Data Group (Hidden initially) -->
                    <div class="sm:col-span-2 hidden role-section" id="section-profile"><h3 class="text-lg font-semibold border-b pb-2 mt-4">Informasi Profil</h3></div>

                    <!-- Buyer Fields -->
                    <div class="sm:col-span-2 hidden role-buyer-field">
                        <label for="company_name" class="block text-sm font-medium text-gray-700 mb-2">Nama Perusahaan (Pembeli)</label>
                        <input type="text" name="company_name" id="company_name" value="{{ old('company_name') }}"
                               class="block w-full px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand outline-none"
                               placeholder="PT. Contoh Industri">
                    </div>
                    <div class="sm:col-span-2 hidden role-buyer-field">
                        <label for="industry_type_select" class="block text-sm font-medium text-gray-700 mb-2">Jenis Industri</label>
                        @php
                            $industryOptions = ['Manufaktur', 'Plastik & Karet', 'Kertas & Karton', 'Logam & Baja', 'Tekstil'];
                            $oldIndustry = old('industry_type');
                            $isCustomIndustry = $oldIndustry && !in_array($oldIndustry, $industryOptions);
                        @endphp
                        <select id="industry_type_select" {{ $isCustomIndustry ? '' : 'name=industry_type' }} data-name="industry_type" class="block w-full px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand outline-none" onchange="handleLainnya('industry_type_select', 'industry_type_input')">
                            <option value="">Pilih Jenis Industri</option>
                            @foreach($industryOptions as $opt)
                                <option value="{{ $opt }}" {{ $oldIndustry === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                            @endforeach
                            <option value="Lainnya" {{ $isCustomIndustry ? 'selected' : '' }}>Lainnya</option>
                        </select>
                        <input type="text" id="industry_type_input" {{ $isCustomIndustry ? 'name=industry_type' : '' }} data-name="industry_type" value="{{ $isCustomIndustry ? $oldIndustry : '' }}"
                               class="{{ $isCustomIndustry ? '' : 'hidden' }} mt-3 block w-full px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand outline-none"
                               placeholder="Ketik jenis industri...">
                    </div>

                    <!-- Seller Fields -->
                    <div class="sm:col-span-2 hidden role-seller-field">
                        <label for="business_name" class="block text-sm font-medium text-gray-700 mb-2">Nama Usaha/Pengepul (Penjual)</label>
                        <input type="text" name="business_name" id="business_name" value="{{ old('business_name') }}"
                               class="block w-full px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand outline-none"
                               placeholder="CV. Pengepul Sejahtera">
                    </div>
                    <div class="sm:col-span-2 hidden role-seller-field">
                        <label for="business_type_select" class="block text-sm font-medium text-gray-700 mb-2">Tipe Usaha</label>
                        @php
                            $businessOptions = ['Perorangan', 'Pengepul Kecil', 'Bank Sampah', 'Pengepul Besar'];
                            $oldBusiness = old('business_type');
                            $isCustomBusiness = $oldBusiness && !in_array($oldBusiness, $businessOptions);
                        @endphp
                        <select id="business_type_select" {{ $isCustomBusiness ? '' : 'name=business_type' }} data-name="business_type" class="block w-full px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand outline-none" onchange="handleLainnya('business_type_select', 'business_type_input')">
                            <option value="">Pilih Tipe Usaha</option>
                            @foreach($businessOptions as $opt)
                                <option value="{{ $opt }}" {{ $oldBusiness === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                            @endforeach
                            <option value="Lainnya" {{ $isCustomBusiness ? 'selected' : '' }}>Lainnya</option>
                        </select>
                        <input type="text" id="business_type_input" {{ $isCustomBusiness ? 'name=business_type' : '' }} data-name="business_type" value="{{ $isCustomBusiness ? $oldBusiness : '' }}"
                               class="{{ $isCustomBusiness ? '' : 'hidden' }} mt-3 block w-full px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand outline-none"
                               placeholder="Ketik tipe usaha...">
                    </div>

                    <!-- Common Address Fields -->
                    <div class="sm:col-span-2 hidden role-common-field">
                        <label for="address" class="block text-sm font-medium text-gray-700 mb-2">Alamat Lengkap</label>
                        <textarea name="address" id="address" rows="3"
                                  class="block w-full px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand outline-none"
                                  placeholder="Jalan, RT/RW, Kecamatan, dsb">{{ old('address') }}</textarea>
                    </div>
                    
                    <div class="hidden role-common-field">
                        <label for="city" class="block text-sm font-medium text-gray-700 mb-2">Kota/Kabupaten</label>
                        <input type="text" name="city" id="city" value="{{ old('city') }}"
                               class="block w-full px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand outline-none"
                               placeholder="Jakarta Pusat">
                    </div>
                    <div class="hidden role-common-field">
                        <label for="province" class="block text-sm font-medium text-gray-700 mb-2">Provinsi</label>
                        <input type="text" name="province" id="province" value="{{ old('province') }}"
                               class="block w-full px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand outline-none"
                               placeholder="DKI Jakarta">
                    </div>
                    <div class="hidden role-common-field">
                        <label for="postal_code" class="block text-sm font-medium text-gray-700 mb-2">Kode Pos</label>
                        <input type="text" name="postal_code" id="postal_code" value="{{ old('postal_code') }}"
                               class="block w-full px-4 py-3.5 bg-gray-50 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand outline-none"
                               placeholder="10110">
                    </div>

                    <!-- Titik Lokasi Maps -->
                    <div class="sm:col-span-2 hidden role-common-field mt-2 p-5 bg-gray-50 border border-gray-100 rounded-2xl flex flex-col gap-5">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div>
                                <label class="block text-sm font-bold text-gray-900 mb-1">Titik Lokasi (Maps)</label>
                                <p class="text-sm text-gray-500">Isi koordinat secara manual atau gunakan deteksi otomatis dari browser.</p>
                            </div>
                            
                            <button type="button" id="btn-detect-location" onclick="getLocation()" class="px-5 py-2.5 bg-gray-900 text-white hover:bg-gray-800 rounded-xl font-bold flex items-center justify-center gap-2 transition-all shadow-sm whitespace-nowrap w-full sm:w-auto">
                                <i data-lucide="map-pin" class="w-4 h-4"></i>
                                Deteksi Lokasi
                            </button>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-2 uppercase tracking-wide">Latitude</label>
                                <input type="text" name="latitude" id="lat-input" value="{{ old('latitude') }}" placeholder="Contoh: -6.2088" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-brand focus:ring focus:ring-brand/20 transition-all py-3 px-4 text-gray-900 font-mono text-sm">
                                @error('latitude') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-2 uppercase tracking-wide">Longitude</label>
                                <input type="text" name="longitude" id="lng-input" value="{{ old('longitude') }}" placeholder="Contoh: 106.8456" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-brand focus:ring focus:ring-brand/20 transition-all py-3 px-4 text-gray-900 font-mono text-sm">
                                @error('longitude') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                </div>

                <div class="mt-6">
                    <button type="submit" id="submitBtn" disabled
                            class="w-full flex justify-center py-3.5 px-4 border border-transparent rounded-xl shadow-sm text-base font-bold text-white bg-brand hover:bg-brand-hover focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        Buat Akun Sekarang
                    </button>
                </div>
            </form>
            
            <div class="mt-8 pt-6 border-t border-gray-100 text-center flex flex-col gap-4">
                <p class="text-sm text-gray-600">
                    Sudah memiliki akun? 
                    <a href="{{ route('login') }}" class="font-bold text-brand hover:text-brand-hover transition-colors">Masuk di sini</a>
                </p>
                <a href="{{ url('/') }}" class="inline-flex items-center justify-center gap-2 text-sm font-medium text-gray-500 hover:text-brand transition-colors">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
    
    <script>
        document.addEventListener("turbo:load", function() {
            lucide.createIcons();
        });
        if (!window.Turbo) lucide.createIcons();

        function toggleRoleFields() {
            const role = document.querySelector('input[name="role"]:checked')?.value;
            
            const profileSection = document.getElementById('section-profile');
            const buyerFields = document.querySelectorAll('.role-buyer-field');
            const sellerFields = document.querySelectorAll('.role-seller-field');
            const commonFields = document.querySelectorAll('.role-common-field');
            
            // Elements
            const cCompany = document.getElementById('company_name');
            const cIndustry = document.getElementById('industry_type');
            const cBusiness = document.getElementById('business_name');
            const cBType = document.getElementById('business_type');
            const cAddress = document.getElementById('address');
            const cCity = document.getElementById('city');
            const cProv = document.getElementById('province');
            const cZip = document.getElementById('postal_code');
            const submitBtn = document.getElementById('submitBtn');

            if (role) {
                submitBtn.removeAttribute('disabled');
                
                profileSection.classList.remove('hidden');
                commonFields.forEach(el => el.classList.remove('hidden'));
                cAddress.setAttribute('required', 'required');
                cCity.setAttribute('required', 'required');
                cProv.setAttribute('required', 'required');
                cZip.setAttribute('required', 'required');

                if (role === 'buyer') {
                    buyerFields.forEach(el => el.classList.remove('hidden'));
                    sellerFields.forEach(el => el.classList.add('hidden'));
                    
                    cCompany.setAttribute('required', 'required');
                    cIndustry.setAttribute('required', 'required');
                    cBusiness.removeAttribute('required');
                    cBType.removeAttribute('required');
                } else if (role === 'seller') {
                    sellerFields.forEach(el => el.classList.remove('hidden'));
                    buyerFields.forEach(el => el.classList.add('hidden'));

                    cBusiness.setAttribute('required', 'required');
                    cBType.setAttribute('required', 'required');
                    cCompany.removeAttribute('required');
                    cIndustry.removeAttribute('required');
                }
            }
        }
        
        // Run on load in case of old input
        toggleRoleFields();

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

        function handleLainnya(selectId, inputId) {
            const select = document.getElementById(selectId);
            const input = document.getElementById(inputId);
            if (select.value === 'Lainnya') {
                input.classList.remove('hidden');
                input.setAttribute('name', select.getAttribute('data-name'));
                select.removeAttribute('name');
                input.required = true;
            } else {
                input.classList.add('hidden');
                select.setAttribute('name', input.getAttribute('data-name'));
                input.removeAttribute('name');
                input.required = false;
            }
        }

        function getLocation() {
            const btn = document.getElementById('btn-detect-location');
            const originalText = btn.innerHTML;
            
            if (navigator.geolocation) {
                btn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i> Mendeteksi...';
                btn.disabled = true;
                
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        document.getElementById('lat-input').value = position.coords.latitude;
                        document.getElementById('lng-input').value = position.coords.longitude;
                        
                        btn.innerHTML = '<i data-lucide="check-circle" class="w-4 h-4 text-emerald-400"></i> Berhasil Dideteksi';
                        btn.classList.replace('bg-gray-900', 'bg-gray-800');
                        
                        setTimeout(() => {
                            btn.innerHTML = originalText;
                            btn.disabled = false;
                            btn.classList.replace('bg-gray-800', 'bg-gray-900');
                            lucide.createIcons();
                        }, 3000);
                    },
                    function(error) {
                        btn.innerHTML = '<i data-lucide="x-circle" class="w-4 h-4 text-red-400"></i> Gagal';
                        alert('Harap izinkan akses lokasi pada browser Anda.');
                        
                        setTimeout(() => {
                            btn.innerHTML = originalText;
                            btn.disabled = false;
                            lucide.createIcons();
                        }, 3000);
                    },
                    { timeout: 10000, enableHighAccuracy: true }
                );
            } else {
                alert('Browser Anda tidak mendukung Geolocation.');
            }
        }
    </script>
    @include('layouts.global-loader')
</body>
</html>
