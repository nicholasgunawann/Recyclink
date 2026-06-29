@extends('seller.layouts.seller')

@section('title', 'Profil Usaha - Recyclink')
@section('header_title', 'Profil Usaha')

@section('content')
<div class="max-w-6xl mx-auto">
    <!-- Header -->
    <div class="mb-8 flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
        <div>
            <h3 class="text-3xl font-extrabold text-gray-900 mb-2">Informasi Akun</h3>
            <p class="text-gray-500">Kelola detail informasi usaha dan keamanan akun Anda.</p>
        </div>
        <a href="{{ route('seller.profile.edit') }}" class="px-6 py-2.5 bg-[#115E3B] text-white font-bold rounded-xl hover:bg-[#064e3b] transition-all inline-flex items-center gap-2 mt-2 sm:mt-0 shadow-sm">
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
        $profile = $user->sellerProfile;
        
        $phone = $user->phone_number;
        if (str_starts_with((string)$phone, '0')) {
            $phone = substr($phone, 1);
        } elseif (str_starts_with((string)$phone, '+62')) {
            $phone = substr($phone, 3);
        } elseif (str_starts_with((string)$phone, '62')) {
            $phone = substr($phone, 2);
        }

        $completedFields = 0;
        $totalFields = 5;
        
        if (!empty($profile->business_name)) $completedFields++;
        if (!empty($profile->business_type)) $completedFields++;
        if (!empty($user->phone_number)) $completedFields++;
        if (!empty($profile->address)) $completedFields++;
        if (!empty($profile->latitude) && !empty($profile->longitude)) $completedFields++;
        
        $percentage = round(($completedFields / $totalFields) * 100);
        $memberId = 'RCY-' . date('Y') . '-' . str_pad($user->id, 4, '0', STR_PAD_LEFT);
    @endphp

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Kolom Kiri: Detail Akun -->
        <div class="lg:col-span-2">
            <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-8">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-y-8 gap-x-12">
                    <!-- Nama Usaha -->
                    <div>
                        <span class="block text-sm font-medium text-gray-500 mb-2">Nama Usaha</span>
                        <p class="text-xl font-bold text-gray-900">{{ $profile->business_name ?? '-' }}</p>
                    </div>

                    <!-- ID Anggota -->
                    <div>
                        <span class="block text-sm font-medium text-gray-500 mb-2">ID Anggota</span>
                        <p class="text-xl font-bold text-gray-900">{{ $memberId }}</p>
                    </div>

                    <!-- Jenis Usaha -->
                    <div>
                        <span class="block text-sm font-medium text-gray-500 mb-2">Jenis Usaha</span>
                        <div class="flex items-center gap-2 text-gray-900">
                            <i data-lucide="building" class="w-5 h-5 text-gray-400"></i>
                            <span class="text-lg font-medium">{{ $profile->business_type ?? '-' }}</span>
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
                </div>

                <div class="mt-8">
                    <span class="block text-sm font-medium text-gray-500 mb-2">Alamat Usaha Lengkap</span>
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
                
                <div class="mt-8 pt-8 border-t border-gray-100">
                    <span class="block text-sm font-medium text-gray-500 mb-3">Titik Lokasi (Maps)</span>
                    @if(!empty($profile->latitude) && !empty($profile->longitude))
                        <a href="https://www.google.com/maps?q={{ $profile->latitude }},{{ $profile->longitude }}" target="_blank" class="inline-flex items-center gap-2 px-5 py-2.5 bg-blue-50 text-blue-700 font-bold rounded-xl hover:bg-blue-100 transition-colors">
                            <i data-lucide="map" class="w-5 h-5"></i> Buka di Google Maps
                        </a>
                    @else
                        <p class="text-lg font-semibold text-gray-900">-</p>
                    @endif
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
                        @if(!empty($user->phone_number))
                            <i data-lucide="check-circle-2" class="w-6 h-6 text-[#115E3B] fill-[#115E3B]/10"></i>
                        @else
                            <i data-lucide="circle" class="w-6 h-6 text-gray-300"></i>
                        @endif
                        <span class="text-gray-700 font-medium">Nomor Telepon</span>
                    </li>
                    <li class="flex items-center gap-3">
                        @if(!empty($profile->business_type))
                            <i data-lucide="check-circle-2" class="w-6 h-6 text-[#115E3B] fill-[#115E3B]/10"></i>
                        @else
                            <i data-lucide="circle" class="w-6 h-6 text-gray-300"></i>
                        @endif
                        <span class="text-gray-700 font-medium">Jenis Usaha</span>
                    </li>
                    <li class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            @if(!empty($profile->address))
                                <i data-lucide="check-circle-2" class="w-6 h-6 text-[#115E3B] fill-[#115E3B]/10"></i>
                            @else
                                <i data-lucide="circle" class="w-6 h-6 text-gray-300"></i>
                            @endif
                            <span class="text-gray-700 font-medium">Alamat Usaha</span>
                        </div>
                        @if(empty($profile->address))
                            <a href="{{ route('seller.profile.edit') }}" class="text-sm font-semibold text-[#115E3B] hover:underline">Isi</a>
                        @endif
                    </li>
                    <li class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            @if(!empty($profile->latitude) && !empty($profile->longitude))
                                <i data-lucide="check-circle-2" class="w-6 h-6 text-[#115E3B] fill-[#115E3B]/10"></i>
                            @else
                                <i data-lucide="circle" class="w-6 h-6 text-gray-300"></i>
                            @endif
                            <span class="text-gray-700 font-medium">Titik Lokasi (Maps)</span>
                        </div>
                        @if(empty($profile->latitude) || empty($profile->longitude))
                            <a href="{{ route('seller.profile.edit') }}" class="text-sm font-semibold text-[#115E3B] hover:underline">Pin</a>
                        @endif
                    </li>
                </ul>

                <div class="bg-indigo-50/60 border border-indigo-100 rounded-xl p-5">
                    <p class="text-indigo-900/80 italic font-medium leading-relaxed text-sm">
                        "Lengkapi profil usaha dan titik lokasi Anda agar pembeli dapat menemukan Anda dengan mudah."
                    </p>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
