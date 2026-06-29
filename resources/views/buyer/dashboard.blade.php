@extends('buyer.layouts.buyer')

@section('title', 'Dashboard Pembeli - Recyclink')
@section('header_title', 'Dashboard')

@section('content')

{{-- Profile completion banner --}}
@if(!app(\App\Services\ProfileService::class)->checkProfileCompletion(auth()->user()))
    <div class="mb-6 flex items-start gap-4 bg-amber-50 border border-amber-200 rounded-2xl p-5">
        <div class="flex-shrink-0 w-10 h-10 bg-amber-100 rounded-xl flex items-center justify-center text-amber-500">
            <i data-lucide="alert-circle" class="w-5 h-5"></i>
        </div>
        <div class="flex-1">
            <h4 class="font-bold text-amber-800">Profil Anda Belum Lengkap</h4>
            <p class="text-amber-700 text-sm mt-0.5">Lengkapi nama, nomor telepon, alamat, dan email Anda agar bisa mulai bertransaksi di Recyclink.</p>
        </div>
        <a href="{{ route('buyer.profile.edit') }}"
           class="flex-shrink-0 px-4 py-2 bg-amber-500 text-white font-bold text-sm rounded-xl hover:bg-amber-600 transition-colors whitespace-nowrap">
            Lengkapi Sekarang →
        </a>
    </div>
@endif

<div class="mb-8">
    <h3 class="text-2xl font-bold text-gray-900">Halo, {{ auth()->user()->name }} 👋</h3>
    <p class="text-gray-600 mt-1">Selamat datang! Berikut ringkasan aktivitas belanja limbah Anda.</p>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
    <!-- Card 1 -->
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-start gap-4">
        <div class="p-3 bg-brand/10 text-brand rounded-xl">
            <i data-lucide="shopping-cart" class="w-6 h-6"></i>
        </div>
        <div>
            <p class="text-sm text-gray-500 font-medium">Total Pesanan</p>
            <h4 class="text-2xl font-bold text-gray-900 mt-1">{{ $ordersCount ?? 0 }}</h4>
        </div>
    </div>

    <!-- Card 2 -->
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-start gap-4">
        <div class="p-3 bg-amber-50 text-amber-500 rounded-xl">
            <i data-lucide="clock" class="w-6 h-6"></i>
        </div>
        <div>
            <p class="text-sm text-gray-500 font-medium">Pesanan Diproses</p>
            <h4 class="text-2xl font-bold text-gray-900 mt-1">0</h4>
        </div>
    </div>

    <!-- Card 3 -->
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-start gap-4">
        <div class="p-3 bg-red-50 text-red-500 rounded-xl">
            <i data-lucide="heart" class="w-6 h-6"></i>
        </div>
        <div>
            <p class="text-sm text-gray-500 font-medium">Limbah Tersimpan</p>
            <h4 class="text-2xl font-bold text-gray-900 mt-1">{{ $favoritesCount ?? 0 }}</h4>
        </div>
    </div>

    <!-- Card 4 -->
    <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-start gap-4">
        <div class="p-3 bg-blue-50 text-blue-500 rounded-xl">
            <i data-lucide="check-circle" class="w-6 h-6"></i>
        </div>
        <div>
            <p class="text-sm text-gray-500 font-medium">Pesanan Selesai</p>
            <h4 class="text-2xl font-bold text-gray-900 mt-1">0</h4>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-6">
    <h4 class="text-lg font-bold text-gray-900 mb-6">Aktivitas Terkini</h4>
    <div class="flex flex-col items-center justify-center py-12 text-center border-2 border-dashed border-gray-100 rounded-xl bg-gray-50/50">
        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
            <i data-lucide="activity" class="w-8 h-8 text-gray-400"></i>
        </div>
        <h5 class="text-gray-900 font-bold">Belum Ada Aktivitas</h5>
        <p class="text-gray-500 text-sm mt-2 max-w-sm">Anda belum melakukan transaksi atau menyimpan limbah apa pun. Mulai cari limbah sekarang!</p>
        <a href="{{ url('/marketplace') }}" class="mt-6 px-6 py-2.5 bg-brand text-white font-bold rounded-xl hover:bg-brand-hover transition-colors inline-flex items-center gap-2">
            Eksplorasi Marketplace <i data-lucide="arrow-right" class="w-4 h-4"></i>
        </a>
    </div>
</div>

@endsection
