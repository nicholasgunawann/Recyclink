@extends('seller.layouts.seller')

@section('title', 'Lengkapi Profil - Recyclink')
@section('header_title', 'Lengkapi Profil')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Back Link -->
    <a href="{{ route('seller.dashboard') }}" class="inline-flex items-center text-gray-500 hover:text-gray-900 mb-6 font-medium transition-colors text-sm">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 mr-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5"/><path d="M12 19l-7-7 7-7"/></svg>
        Kembali ke Halaman Profil
    </a>

    <!-- Header -->
    <div class="mb-8">
        <h3 class="text-3xl font-extrabold text-gray-900 mb-2">Lengkapi Profil Usaha 👋</h3>
        <p class="text-gray-500">Pastikan informasi profil Anda terbaru untuk memudahkan proses verifikasi dan transaksi di sistem kami.</p>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl">
            {{ session('error') }}
        </div>
    @endif

    @php
        $user = auth()->user();
        $profile = $user->sellerProfile;
        $completedFields = 0;
        $totalFields = 6;
        
        if (!empty($profile->business_name)) $completedFields++;
        if (!empty($user->phone_number)) $completedFields++;
        if (!empty($profile->business_type)) $completedFields++;
        if (!empty($profile->address)) $completedFields++;
        if (!empty($profile->latitude)) $completedFields++;
        if (!empty($profile->longitude)) $completedFields++;
        
        $percentage = round(($completedFields / $totalFields) * 100);

        // Format phone number to remove leading 0 or 62/+62 for the input
        $phone = old('phone_number', $user->phone_number);
        if (str_starts_with((string)$phone, '0')) {
            $phone = substr($phone, 1);
        } elseif (str_starts_with((string)$phone, '+62')) {
            $phone = substr($phone, 3);
        } elseif (str_starts_with((string)$phone, '62')) {
            $phone = substr($phone, 2);
        }
    @endphp

    <!-- Progress Bar -->
    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-6 mb-6 mt-6">
        <div class="flex justify-between items-center mb-4">
            <span class="font-bold text-gray-900">Kelengkapan Profil</span>
            <span class="font-bold text-[#719149]">{{ $percentage }}% Selesai</span>
        </div>
        <div class="w-full bg-[#E8EDE1] rounded-full h-3">
            <div class="bg-[#719149] h-3 rounded-full transition-all duration-1000" style="width: {{ $percentage }}%"></div>
        </div>
    </div>

    <!-- Form Container -->
    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-8">
        <form action="{{ route('seller.profile.update') }}" method="POST" class="space-y-8">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Nama Usaha -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nama Usaha</label>
                    <input type="text" name="business_name" value="{{ old('business_name', $profile->business_name ?? '') }}" placeholder="Contoh: Bank Sampah Berkah" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-brand focus:ring focus:ring-brand/20 transition-all py-3 px-4 text-gray-900" required>
                    @error('business_name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Jenis Usaha -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Jenis Usaha</label>
                    @php
                        $businessOptions = ['Perorangan', 'Pengepul Kecil', 'Bank Sampah', 'Pengepul Besar'];
                        $oldBusiness = old('business_type', $profile->business_type ?? '');
                        $isCustomBusiness = $oldBusiness && !in_array($oldBusiness, $businessOptions);
                    @endphp
                    <select id="business_type_select" {{ $isCustomBusiness ? '' : 'name=business_type' }} data-name="business_type" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-brand focus:ring focus:ring-brand/20 transition-all py-3 px-4 text-gray-900" onchange="handleLainnya('business_type_select', 'business_type_input')">
                        <option value="">Pilih Tipe Usaha</option>
                        @foreach($businessOptions as $opt)
                            <option value="{{ $opt }}" {{ $oldBusiness === $opt ? 'selected' : '' }}>{{ $opt }}</option>
                        @endforeach
                        <option value="Lainnya" {{ $isCustomBusiness ? 'selected' : '' }}>Lainnya</option>
                    </select>
                    <input type="text" id="business_type_input" {{ $isCustomBusiness ? 'name=business_type' : '' }} data-name="business_type" value="{{ $isCustomBusiness ? $oldBusiness : '' }}" class="{{ $isCustomBusiness ? '' : 'hidden' }} mt-3 w-full rounded-xl border-gray-300 shadow-sm focus:border-brand focus:ring focus:ring-brand/20 transition-all py-3 px-4 text-gray-900" placeholder="Ketik jenis usaha...">
                    @error('business_type') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Nomor Telepon -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Telepon/WA</label>
                    <div class="flex rounded-xl shadow-sm border border-gray-300 overflow-hidden focus-within:border-brand focus-within:ring focus-within:ring-brand/20 transition-all">
                        <span class="inline-flex items-center px-4 bg-indigo-50/50 text-gray-600 font-bold border-r border-gray-300">
                            +62
                        </span>
                        <input type="text" name="phone_number" value="{{ $phone }}" placeholder="81234567890" class="flex-1 border-0 focus:ring-0 py-3 px-4 text-gray-900" required>
                    </div>
                    @error('phone_number') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Password -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Ganti Password (Opsional)</label>
                    <div class="relative">
                        <input type="password" name="password" placeholder="••••••••" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-brand focus:ring focus:ring-brand/20 transition-all py-3 pl-4 pr-10 text-gray-900">
                        <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none">
                            <i data-lucide="eye" class="w-4 h-4 text-gray-400"></i>
                        </div>
                    </div>
                    @error('password') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Alamat -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Alamat Lengkap</label>
                <textarea name="address" rows="3" placeholder="Masukkan alamat lengkap usaha Anda..." class="w-full rounded-xl border-gray-300 shadow-sm focus:border-brand focus:ring focus:ring-brand/20 transition-all py-3 px-4 text-gray-900" required>{{ old('address', $profile->address ?? '') }}</textarea>
                @error('address') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Kota -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kota / Kabupaten</label>
                    <input type="text" name="city" value="{{ old('city', $profile->city ?? '') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-brand focus:ring focus:ring-brand/20 transition-all py-3 px-4 text-gray-900" required>
                    @error('city') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Provinsi -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Provinsi</label>
                    <input type="text" name="province" value="{{ old('province', $profile->province ?? '') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-brand focus:ring focus:ring-brand/20 transition-all py-3 px-4 text-gray-900" required>
                    @error('province') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Kode Pos -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Kode Pos</label>
                    <input type="text" name="postal_code" value="{{ old('postal_code', $profile->postal_code ?? '') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-brand focus:ring focus:ring-brand/20 transition-all py-3 px-4 text-gray-900" required>
                    @error('postal_code') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Latitude -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Latitude (Lokasi Maps)</label>
                    <div class="relative">
                        <input type="text" name="latitude" placeholder="-6.200000" value="{{ old('latitude', $profile->latitude ?? '') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-brand focus:ring focus:ring-brand/20 transition-all py-3 pl-10 px-4 text-gray-900">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                            <i data-lucide="map-pin" class="w-4 h-4 text-gray-400"></i>
                        </div>
                    </div>
                    @error('latitude') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Longitude -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Longitude (Lokasi Maps)</label>
                    <div class="relative">
                        <input type="text" name="longitude" placeholder="106.816666" value="{{ old('longitude', $profile->longitude ?? '') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-brand focus:ring focus:ring-brand/20 transition-all py-3 pl-10 px-4 text-gray-900">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                            <i data-lucide="map-pin" class="w-4 h-4 text-gray-400"></i>
                        </div>
                    </div>
                    @error('longitude') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
            </div>

            <!-- Buttons -->
            <div class="pt-8 flex items-center justify-end gap-6 mt-4">
                <a href="{{ route('seller.dashboard') }}" class="text-gray-700 font-bold hover:text-gray-900 px-2 py-2 transition-all">
                    Batal
                </a>
                <button type="submit" class="px-6 py-3 bg-[#719149] text-white font-bold rounded-xl hover:bg-[#607d3c] transition-all inline-flex items-center gap-2">
                    <i data-lucide="save" class="w-5 h-5"></i> Simpan Profil
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
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
@endpush
@endsection
