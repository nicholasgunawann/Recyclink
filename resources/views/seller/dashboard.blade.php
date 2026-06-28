@extends('seller.layouts.seller')

@section('title', 'Dashboard Penjual - Recyclink')
@section('header_title', 'Dashboard')

@section('content')
    <div class="mb-8">
        <h3 class="text-2xl font-bold text-gray-900">Halo, {{ auth()->user()->name }} 👋</h3>
        <p class="text-gray-600 mt-1">Selamat datang kembali! Berikut ringkasan aktivitas toko dan penjualan Anda.</p>
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
                <a href="#" class="text-brand font-bold text-sm hover:underline">Lihat Semua</a>
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
                <a href="#" class="group p-5 bg-gray-50 rounded-2xl border border-gray-100 hover:border-brand hover:bg-brand/5 transition-all text-center flex flex-col items-center">
                    <div class="w-12 h-12 bg-white rounded-xl shadow-sm flex items-center justify-center mb-3 group-hover:scale-110 transition-transform text-brand">
                        <i data-lucide="plus-circle" class="w-6 h-6"></i>
                    </div>
                    <span class="font-bold text-gray-900">Tambah Limbah</span>
                </a>
                <a href="#" class="group p-5 bg-gray-50 rounded-2xl border border-gray-100 hover:border-brand hover:bg-brand/5 transition-all text-center flex flex-col items-center">
                    <div class="w-12 h-12 bg-white rounded-xl shadow-sm flex items-center justify-center mb-3 group-hover:scale-110 transition-transform text-amber-500">
                        <i data-lucide="user" class="w-6 h-6"></i>
                    </div>
                    <span class="font-bold text-gray-900">Lengkapi Profil</span>
                </a>
                <a href="#" class="group p-5 bg-gray-50 rounded-2xl border border-gray-100 hover:border-brand hover:bg-brand/5 transition-all text-center flex flex-col items-center">
                    <div class="w-12 h-12 bg-white rounded-xl shadow-sm flex items-center justify-center mb-3 group-hover:scale-110 transition-transform text-purple-500">
                        <i data-lucide="banknote" class="w-6 h-6"></i>
                    </div>
                    <span class="font-bold text-gray-900">Tarik Saldo</span>
                </a>
                <a href="#" class="group p-5 bg-gray-50 rounded-2xl border border-gray-100 hover:border-brand hover:bg-brand/5 transition-all text-center flex flex-col items-center">
                    <div class="w-12 h-12 bg-white rounded-xl shadow-sm flex items-center justify-center mb-3 group-hover:scale-110 transition-transform text-emerald-500">
                        <i data-lucide="message-square" class="w-6 h-6"></i>
                    </div>
                    <span class="font-bold text-gray-900">Pesan Masuk</span>
                </a>
            </div>
        </div>
    </div>
@endsection
