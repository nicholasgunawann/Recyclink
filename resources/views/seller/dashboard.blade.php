@extends('seller.layouts.seller')

@section('title', 'Dashboard Penjual - Recyclink')
@section('header_title', 'Dashboard')

@section('content')

{{-- Profile completion banner --}}
@if(!app(\App\Services\ProfileService::class)->checkProfileCompletion(auth()->user()))
    <div class="mb-6 flex items-start gap-4 bg-amber-50 border border-amber-200 rounded-2xl p-5">
        <div class="flex-shrink-0 w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center text-amber-500">
            <i data-lucide="alert-circle" class="w-5 h-5"></i>
        </div>
        <div class="flex-1">
            <h4 class="font-bold text-amber-800">Profil Usaha Belum Lengkap</h4>
            <p class="text-amber-700 text-sm mt-0.5">Lengkapi nama usaha, jenis usaha, nomor telepon, alamat, dan lokasi maps agar bisa mulai berjualan.</p>
        </div>
        <a href="{{ route('seller.profile.edit') }}"
           class="flex-shrink-0 px-4 py-2 bg-amber-500 text-white font-bold text-sm rounded-xl hover:bg-amber-600 transition-colors whitespace-nowrap">
            Lengkapi Sekarang →
        </a>
    </div>
@endif

    <div class="mb-8">
        <h3 class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-brand to-emerald-600 tracking-tight">Halo, {{ auth()->user()->name }}</h3>
        <p class="text-gray-600 mt-2 text-lg">Selamat datang! Berikut ringkasan aktivitas toko dan penjualan Anda.</p>
    </div>


    <!-- Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
        <!-- Card 1 -->
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-start gap-4">
            <div class="p-3 bg-brand/10 text-brand rounded-xl">
                <i data-lucide="package" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Total Limbah</p>
                <h4 class="text-2xl font-bold text-gray-900 mt-1">{{ $listingsCount ?? 0 }}</h4>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-start gap-4">
            <div class="p-3 bg-amber-50 text-amber-500 rounded-xl">
                <i data-lucide="clipboard-list" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Total Pesanan</p>
                <h4 class="text-2xl font-bold text-gray-900 mt-1">{{ $ordersCount ?? 0 }}</h4>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-start gap-4">
            <div class="p-3 bg-emerald-50 text-emerald-500 rounded-xl">
                <i data-lucide="check-circle" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Pesanan Selesai</p>
                <h4 class="text-2xl font-bold text-gray-900 mt-1">0</h4>
            </div>
        </div>
        
        <!-- Card 4 -->
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-start gap-4">
            <div class="p-3 bg-purple-50 text-purple-500 rounded-xl">
                <i data-lucide="wallet" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Saldo Dompet</p>
                <h4 class="text-2xl font-bold text-gray-900 mt-1">Rp {{ number_format($user->wallet->balance ?? 0, 0, ',', '.') }}</h4>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Orders -->
        <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-6">
                <h4 class="text-lg font-bold text-gray-900">Pesanan Masuk Terkini</h4>
                <a href="{{ route('seller.orders.index') }}" class="text-brand font-bold text-sm hover:underline">Lihat Semua</a>
            </div>
            <div class="flex flex-col items-center justify-center py-10 text-center border-2 border-dashed border-gray-100 rounded-xl bg-gray-50/50">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                    <i data-lucide="inbox" class="w-8 h-8 text-gray-400"></i>
                </div>
                <h5 class="text-gray-900 font-bold">Belum Ada Pesanan Masuk</h5>
                <p class="text-gray-500 text-sm mt-2 max-w-xs">Pesanan dari pembeli akan muncul di sini. Pastikan ketersediaan limbah Anda selalu terbarui.</p>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-6">
            <h4 class="text-lg font-bold text-gray-900 mb-6">Aksi Cepat</h4>
            <div class="grid grid-cols-2 gap-4">
                <a href="{{ route('seller.listings.create') }}" class="group p-5 bg-gray-50 rounded-2xl border border-gray-100 hover:border-brand hover:bg-brand/5 transition-all text-center flex flex-col items-center">
                    <div class="w-12 h-12 bg-white rounded-xl shadow-sm flex items-center justify-center mb-3 group-hover:scale-110 transition-transform text-brand">
                        <i data-lucide="plus-circle" class="w-6 h-6"></i>
                    </div>
                    <span class="font-bold text-gray-900">Tambah Limbah</span>
                </a>
            </div>
        </div>
    </div>
@endsection
