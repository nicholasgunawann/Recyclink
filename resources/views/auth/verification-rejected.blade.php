<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Ditolak - Recyclink</title>
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
                Pendaftaran Belum Dapat Diterima
            </h1>
            <p class="text-lg text-white/90 max-w-lg leading-relaxed">
                Kami mohon maaf, sepertinya ada beberapa informasi yang perlu diperbaiki.
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

            @if($user->status === 'suspended')
                <div class="mb-10 text-center">
                    <div class="w-20 h-20 bg-red-50 rounded-full flex items-center justify-center mb-6 mx-auto">
                        <i data-lucide="ban" class="w-10 h-10 text-red-500"></i>
                    </div>
                    <h2 class="text-3xl font-bold text-gray-900">Akun Ditangguhkan</h2>
                    <p class="mt-4 text-gray-600 leading-relaxed text-sm">
                        Maaf, akun Anda telah ditangguhkan sementara dari sistem kami.
                    </p>
                    @if($user->rejection_reason)
                        <div class="mt-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm text-left">
                            <p class="font-bold mb-1">Alasan Penangguhan:</p>
                            <p>{{ $user->rejection_reason }}</p>
                        </div>
                    @endif
                </div>
                <div class="text-center text-gray-600 text-sm mt-4 mb-8">
                    Jika Anda merasa ini adalah sebuah kesalahan, silakan hubungi tim dukungan kami.
                </div>
            @else
                <div class="mb-10 text-center">
                    <div class="w-20 h-20 bg-red-50 rounded-full flex items-center justify-center mb-6 mx-auto">
                        <i data-lucide="x-circle" class="w-10 h-10 text-red-500"></i>
                    </div>
                    <h2 class="text-3xl font-bold text-gray-900">Verifikasi Ditolak</h2>
                    <p class="mt-4 text-gray-600 leading-relaxed text-sm">
                        Mohon perbarui informasi Anda di bawah ini dan kami akan meninjau ulang pendaftaran Anda.
                    </p>
                    @if($user->rejection_reason)
                        <div class="mt-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm text-left">
                            <p class="font-bold mb-1">Catatan dari Admin:</p>
                            <p>{{ $user->rejection_reason }}</p>
                        </div>
                    @endif
                </div>
                
                <form action="{{ route('verification.resubmit') }}" method="POST" class="space-y-4 text-left">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:ring-2 focus:ring-brand focus:border-brand transition-colors outline-none">
                        @error('name') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:ring-2 focus:ring-brand focus:border-brand transition-colors outline-none">
                        @error('email') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">No WhatsApp</label>
                        <input type="text" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}" required class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:ring-2 focus:ring-brand focus:border-brand transition-colors outline-none">
                        @error('phone_number') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>

                    @if($user->hasRole('buyer') && $user->buyerProfile)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Perusahaan / Institusi</label>
                            <input type="text" name="company_name" value="{{ old('company_name', $user->buyerProfile->company_name) }}" required class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:ring-2 focus:ring-brand focus:border-brand transition-colors outline-none">
                            @error('company_name') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Industri</label>
                            @php
                                $industryOptions = ['Manufaktur', 'Plastik & Karet', 'Kertas & Karton', 'Logam & Baja', 'Tekstil'];
                                $oldIndustry = old('industry_type', $user->buyerProfile->industry_type ?? '');
                                $isCustomIndustry = $oldIndustry && !in_array($oldIndustry, $industryOptions);
                            @endphp
                            <select id="industry_type_select" {{ $isCustomIndustry ? '' : 'name=industry_type' }} data-name="industry_type" required class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:ring-2 focus:ring-brand focus:border-brand transition-colors outline-none" onchange="handleLainnya('industry_type_select', 'industry_type_input')">
                                <option value="">Pilih Jenis Industri</option>
                                @foreach($industryOptions as $type)
                                    <option value="{{ $type }}" {{ $oldIndustry === $type ? 'selected' : '' }}>{{ $type }}</option>
                                @endforeach
                                <option value="Lainnya" {{ $isCustomIndustry ? 'selected' : '' }}>Lainnya</option>
                            </select>
                            <input type="text" id="industry_type_input" {{ $isCustomIndustry ? 'name=industry_type' : '' }} data-name="industry_type" value="{{ $isCustomIndustry ? $oldIndustry : '' }}" class="{{ $isCustomIndustry ? '' : 'hidden' }} mt-3 block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:ring-2 focus:ring-brand focus:border-brand transition-colors outline-none" placeholder="Ketik jenis industri...">
                            @error('industry_type') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Pengiriman Utama</label>
                            <textarea name="address" rows="2" required class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:ring-2 focus:ring-brand focus:border-brand transition-colors outline-none resize-none">{{ old('address', $user->buyerProfile->address) }}</textarea>
                            @error('address') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kota</label>
                                <input type="text" name="city" value="{{ old('city', $user->buyerProfile->city) }}" required class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:ring-2 focus:ring-brand focus:border-brand transition-colors outline-none">
                                @error('city') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Provinsi</label>
                                <input type="text" name="province" value="{{ old('province', $user->buyerProfile->province) }}" required class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:ring-2 focus:ring-brand focus:border-brand transition-colors outline-none">
                                @error('province') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kode Pos</label>
                            <input type="text" name="postal_code" value="{{ old('postal_code', $user->buyerProfile->postal_code) }}" required class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:ring-2 focus:ring-brand focus:border-brand transition-colors outline-none">
                            @error('postal_code') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                    @elseif($user->hasRole('seller') && $user->sellerProfile)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Usaha / Pengepul</label>
                            <input type="text" name="business_name" value="{{ old('business_name', $user->sellerProfile->business_name) }}" required class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:ring-2 focus:ring-brand focus:border-brand transition-colors outline-none">
                            @error('business_name') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Usaha</label>
                            @php
                                $businessOptions = ['Perorangan', 'Pengepul Kecil', 'Bank Sampah', 'Pengepul Besar'];
                                $oldBusiness = old('business_type', $user->sellerProfile->business_type ?? '');
                                $isCustomBusiness = $oldBusiness && !in_array($oldBusiness, $businessOptions);
                            @endphp
                            <select id="business_type_select" {{ $isCustomBusiness ? '' : 'name=business_type' }} data-name="business_type" required class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:ring-2 focus:ring-brand focus:border-brand transition-colors outline-none" onchange="handleLainnya('business_type_select', 'business_type_input')">
                                <option value="">Pilih Tipe Usaha</option>
                                @foreach($businessOptions as $type)
                                    <option value="{{ $type }}" {{ $oldBusiness === $type ? 'selected' : '' }}>{{ $type }}</option>
                                @endforeach
                                <option value="Lainnya" {{ $isCustomBusiness ? 'selected' : '' }}>Lainnya</option>
                            </select>
                            <input type="text" id="business_type_input" {{ $isCustomBusiness ? 'name=business_type' : '' }} data-name="business_type" value="{{ $isCustomBusiness ? $oldBusiness : '' }}" class="{{ $isCustomBusiness ? '' : 'hidden' }} mt-3 block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:ring-2 focus:ring-brand focus:border-brand transition-colors outline-none" placeholder="Ketik tipe usaha...">
                            @error('business_type') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap Usaha</label>
                            <textarea name="address" rows="2" required class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:ring-2 focus:ring-brand focus:border-brand transition-colors outline-none resize-none">{{ old('address', $user->sellerProfile->address) }}</textarea>
                            @error('address') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Kota</label>
                                <input type="text" name="city" value="{{ old('city', $user->sellerProfile->city) }}" required class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:ring-2 focus:ring-brand focus:border-brand transition-colors outline-none">
                                @error('city') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Provinsi</label>
                                <input type="text" name="province" value="{{ old('province', $user->sellerProfile->province) }}" required class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:ring-2 focus:ring-brand focus:border-brand transition-colors outline-none">
                                @error('province') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kode Pos</label>
                            <input type="text" name="postal_code" value="{{ old('postal_code', $user->sellerProfile->postal_code) }}" required class="block w-full px-4 py-3 bg-gray-50 border border-gray-200 rounded-xl text-gray-900 focus:ring-2 focus:ring-brand focus:border-brand transition-colors outline-none">
                            @error('postal_code') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                        </div>
                    @endif
                    
                    <button type="submit" class="w-full mt-2 inline-flex items-center justify-center gap-2 text-sm font-bold text-white transition-colors bg-brand hover:bg-brand-hover px-4 py-3 rounded-xl">
                        Kirim Ulang Data
                    </button>
                </form>
            @endif
            
            <div class="mt-6 pt-6 border-t border-gray-100 flex flex-col gap-4">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="inline-flex items-center justify-center gap-2 text-sm font-bold text-white transition-colors bg-brand hover:bg-brand-hover px-4 py-3 rounded-xl w-full">
                        <i data-lucide="arrow-left" class="w-4 h-4"></i>
                        Kembali
                    </button>
                </form>
            </div>
        </div>
    </div>
    
    <script>lucide.createIcons();</script>
    <script>
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
    </script>
</body>
</html>
