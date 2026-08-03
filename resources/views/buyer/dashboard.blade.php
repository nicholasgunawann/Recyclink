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
    <h3 class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-brand to-emerald-600 tracking-tight">Halo, {{ auth()->user()->name }}</h3>
    <p class="text-gray-600 mt-2 text-lg">Selamat datang! Berikut ringkasan aktivitas belanja limbah Anda.</p>
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
            <h4 class="text-2xl font-bold text-gray-900 mt-1">{{ $processing_orders_count ?? 0 }}</h4>
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
            <h4 class="text-2xl font-bold text-gray-900 mt-1">{{ $completed_orders_count ?? 0 }}</h4>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="bg-white border border-gray-100 rounded-2xl shadow-sm p-6">
    <div class="flex items-center justify-between mb-6">
        <h4 class="text-lg font-bold text-gray-900">Aktivitas Terkini</h4>
        @if(($recentOrders ?? collect())->isNotEmpty())
            <a href="{{ route('buyer.orders.index') }}" class="text-brand font-bold text-sm hover:underline">Lihat Semua Pesanan</a>
        @endif
    </div>

    @if(($recentOrders ?? collect())->isEmpty())
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
    @else
        <div class="divide-y divide-gray-100">
            @php
                // ponytail: reusable status map
                $statusConfig = [
                    'pending' => ['bg' => 'bg-amber-50 text-amber-700 border-amber-200', 'label' => 'Menunggu Konfirmasi'],
                    'waiting_payment' => ['bg' => 'bg-blue-50 text-blue-700 border-blue-200', 'label' => 'Menunggu Pembayaran'],
                    'paid' => ['bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'label' => 'Sudah Dibayar'],
                    'processing' => ['bg' => 'bg-indigo-50 text-indigo-700 border-indigo-200', 'label' => 'Diproses'],
                    'completed' => ['bg' => 'bg-gray-100 text-gray-700 border-gray-200', 'label' => 'Selesai'],
                    'rejected' => ['bg' => 'bg-rose-50 text-rose-700 border-rose-200', 'label' => 'Ditolak'],
                    'cancelled' => ['bg' => 'bg-red-50 text-red-700 border-red-200', 'label' => 'Dibatalkan']
                ];
            @endphp
            @foreach($recentOrders as $order)
                @php
                    $st = $statusConfig[$order->order_status] ?? ['bg' => 'bg-gray-50 text-gray-700 border-gray-200', 'label' => strtoupper($order->order_status)];
                    $firstItem = $order->items->first();
                @endphp
                <div class="py-4 flex items-center justify-between gap-4 hover:bg-gray-50/50 rounded-xl px-2 transition-colors">
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="font-mono font-bold text-sm text-gray-900">{{ $order->order_code }}</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $st['bg'] }}">
                                {{ $st['label'] }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-600 truncate">
                            <span class="font-semibold text-gray-800">{{ $order->seller->sellerProfile->store_name ?? $order->seller->name ?? 'Toko' }}</span> • {{ $firstItem->waste_name_snapshot ?? 'Limbah' }}
                        </p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <div class="font-bold text-sm text-brand">Rp {{ number_format((float)$order->total_amount, 0, ',', '.') }}</div>
                        <a href="{{ route('buyer.orders.show', $order->id) }}" class="text-xs text-brand hover:underline font-semibold mt-0.5 inline-block">Detail →</a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

@endsection
