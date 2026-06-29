@extends('buyer.layouts.buyer')

@section('title', 'Profil Saya - Recyclink')
@section('header_title', 'Profil Saya')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Header -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
        <div>
            <h3 class="text-3xl font-extrabold text-gray-900 mb-2">Informasi Akun</h3>
            <p class="text-gray-500">Kelola detail informasi personal dan keamanan akun Anda.</p>
        </div>
        <a href="{{ route('buyer.profile.edit') }}" class="px-6 py-2.5 bg-[#115E3B] text-white font-bold rounded-xl hover:bg-[#064e3b] transition-all inline-flex items-center gap-2 mt-2 sm:mt-0 shadow-sm">
            <i data-lucide="pencil" class="w-4 h-4"></i> Edit Profil
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-xl">
            {{ session('success') }}
        </div>
    @endif

    @php
        $user = auth()->user();
        $profile = $user->buyerProfile;
        
        $phone = $user->phone_number;
        if (str_starts_with((string)$phone, '0')) {
            $phone = substr($phone, 1);
        } elseif (str_starts_with((string)$phone, '+62')) {
            $phone = substr($phone, 3);
        } elseif (str_starts_with((string)$phone, '62')) {
            $phone = substr($phone, 2);
        }

        $completedFields = 0;
        $totalFields = 4;
        
        if (!empty($user->name)) $completedFields++;
        if (!empty($user->email)) $completedFields++;
        if (!empty($user->phone_number)) $completedFields++;
        if (!empty($profile->address)) $completedFields++;
        
        $percentage = round(($completedFields / $totalFields) * 100);
        $memberId = 'RCY-' . date('Y') . '-' . str_pad($user->id, 4, '0', STR_PAD_LEFT);
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Kolom Kiri: Detail Akun -->
        <div class="lg:col-span-2">
            <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-8">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-8 gap-x-12">
                    <!-- Nama Lengkap -->
                    <div>
                        <span class="block text-sm font-medium text-gray-500 mb-2">Nama Lengkap</span>
                        <p class="text-xl font-bold text-gray-900">{{ $user->name ?? '-' }}</p>
                    </div>

                    <!-- ID Anggota -->
                    <div>
                        <span class="block text-sm font-medium text-gray-500 mb-2">ID Anggota</span>
                        <p class="text-xl font-bold text-gray-900">{{ $memberId }}</p>
                    </div>

                    <!-- Email -->
                    <div>
                        <span class="block text-sm font-medium text-gray-500 mb-2">Email</span>
                        <div class="flex items-center gap-2 text-gray-900">
                            <i data-lucide="mail" class="w-5 h-5 text-gray-400"></i>
                            <span class="text-lg font-medium">{{ $user->email ?? '-' }}</span>
                        </div>
                    </div>

                    <!-- Nomor Telepon -->
                    <div>
                        <span class="block text-sm font-medium text-gray-500 mb-2">Nomor Telepon</span>
                        <div class="flex items-center gap-2 text-gray-900">
                            <i data-lucide="phone" class="w-5 h-5 text-gray-400"></i>
                            <span class="text-lg font-medium">
                                @if($phone)
                                    +62 {{ $phone }}
                                @else
                                    -
                                @endif
                            </span>
                        </div>
                    </div>

                    <!-- Nama Perusahaan -->
                    <div>
                        <span class="block text-sm font-medium text-gray-500 mb-2">Nama Perusahaan</span>
                        <div class="flex items-center gap-2 text-gray-900">
                            <i data-lucide="building-2" class="w-5 h-5 text-gray-400"></i>
                            <span class="text-lg font-medium">{{ $profile->company_name ?? '-' }}</span>
                        </div>
                    </div>

                    <!-- Jenis Industri -->
                    <div>
                        <span class="block text-sm font-medium text-gray-500 mb-2">Jenis Industri</span>
                        <div class="flex items-center gap-2 text-gray-900">
                            <i data-lucide="factory" class="w-5 h-5 text-gray-400"></i>
                            <span class="text-lg font-medium">{{ $profile->industry_type ?? '-' }}</span>
                        </div>
                    </div>
                </div>

                <div class="mt-8">
                    <span class="block text-sm font-medium text-gray-500 mb-2">Alamat Pengiriman Utama</span>
                    <div class="flex items-start gap-2 text-gray-900">
                        <i data-lucide="map-pin" class="w-5 h-5 text-gray-400 mt-1 shrink-0"></i>
                        <div>
                            <p class="text-lg font-medium leading-relaxed">
                                {{ $profile->address ?? 'Belum ada alamat' }}
                            </p>
                            @if($profile->city || $profile->province || $profile->postal_code)
                                <p class="text-md text-gray-600 mt-1">
                                    {{ $profile->city ?? '' }}{{ $profile->city && $profile->province ? ', ' : '' }}{{ $profile->province ?? '' }} {{ $profile->postal_code ?? '' }}
                                </p>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Kolom Kanan: Progress Kelengkapan -->
        <div class="lg:col-span-1">
            <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-8">
                <h4 class="text-xl font-bold text-gray-900 mb-6">Lengkapi Profil</h4>
                
                <div class="flex justify-between items-center mb-3">
                    <span class="font-medium text-gray-600">Progress</span>
                    <span class="font-bold text-[#115E3B]">{{ $percentage }}%</span>
                </div>
                <div class="w-full bg-[#E8EDE1] rounded-full h-2.5 mb-8">
                    <div class="bg-[#115E3B] h-2.5 rounded-full transition-all duration-1000" style="width: {{ $percentage }}%"></div>
                </div>

                <ul class="space-y-4 mb-8">
                    <li class="flex items-center gap-3">
                        @if(!empty($user->email))
                            <i data-lucide="check-circle-2" class="w-6 h-6 text-[#115E3B] fill-[#115E3B]/10"></i>
                        @else
                            <i data-lucide="circle" class="w-6 h-6 text-gray-300"></i>
                        @endif
                        <span class="text-gray-700 font-medium">Verifikasi Email</span>
                    </li>
                    <li class="flex items-center gap-3">
                        @if(!empty($user->phone_number))
                            <i data-lucide="check-circle-2" class="w-6 h-6 text-[#115E3B] fill-[#115E3B]/10"></i>
                        @else
                            <i data-lucide="circle" class="w-6 h-6 text-gray-300"></i>
                        @endif
                        <span class="text-gray-700 font-medium">Nomor Telepon</span>
                    </li>
                    <li class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            @if(!empty($profile->address))
                                <i data-lucide="check-circle-2" class="w-6 h-6 text-[#115E3B] fill-[#115E3B]/10"></i>
                            @else
                                <i data-lucide="circle" class="w-6 h-6 text-gray-300"></i>
                            @endif
                            <span class="text-gray-700 font-medium">Alamat Pengiriman</span>
                        </div>
                        @if(empty($profile->address))
                            <a href="{{ route('buyer.profile.edit') }}" class="text-sm font-semibold text-[#115E3B] hover:underline">Isi</a>
                        @endif
                    </li>
                </ul>

                <div class="bg-indigo-50/60 border border-indigo-100 rounded-xl p-5">
                    <p class="text-indigo-900/80 italic font-medium leading-relaxed text-sm">
                        "Lengkapi profil dan alamat Anda untuk mempermudah transaksi dan perhitungan ongkos kirim otomatis."
                    </p>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
