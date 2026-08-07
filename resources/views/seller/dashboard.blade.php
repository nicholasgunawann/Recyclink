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
        <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight mb-2">Dashboard Penjual</h1>
        <h3 class="text-xl sm:text-2xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-brand to-emerald-600 tracking-tight">Halo, {{ auth()->user()->name }}</h3>
        <p class="text-gray-600 mt-1 text-base sm:text-lg">Selamat datang! Berikut ringkasan aktivitas toko dan penjualan Anda.</p>
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
                <h4 class="text-2xl font-bold text-gray-900 mt-1">{{ $completed_orders_count ?? 0 }}</h4>
            </div>
        </div>
        
        <!-- Card 4 -->
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-start gap-4">
            <div class="p-3 bg-purple-50 text-purple-500 rounded-xl">
                <i data-lucide="wallet" class="w-6 h-6"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Saldo Dompet</p>
                <h4 class="text-2xl font-bold text-gray-900 mt-1">Rp {{ number_format((float)($user->wallet->balance ?? 0), 0, ',', '.') }}</h4>
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
            @if(($recentOrders ?? collect())->isEmpty())
                <div class="flex flex-col items-center justify-center py-10 text-center border-2 border-dashed border-gray-100 rounded-xl bg-gray-50/50">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                        <i data-lucide="inbox" class="w-8 h-8 text-gray-400"></i>
                    </div>
                    <h5 class="text-gray-900 font-bold">Belum Ada Pesanan Masuk</h5>
                    <p class="text-gray-500 text-sm mt-2 max-w-xs">Pesanan dari pembeli akan muncul di sini. Pastikan ketersediaan limbah Anda selalu terbarui.</p>
                </div>
            @else
                <div class="divide-y divide-gray-100">
                    @php
                        // ponytail: reusable status badges
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
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold border {{ $st['bg'] }}">
                                        {{ $st['label'] }}
                                    </span>
                                </div>
                                <p class="text-xs text-gray-600 truncate">
                                    <span class="font-semibold text-gray-800">{{ $order->buyer->name ?? 'Pembeli' }}</span> • {{ $firstItem->waste_name_snapshot ?? 'Limbah' }}
                                </p>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <div class="font-bold text-sm text-brand">Rp {{ number_format((float)$order->total_amount, 0, ',', '.') }}</div>
                                <a href="{{ route('seller.orders.show', $order->id) }}" class="text-xs text-brand hover:underline font-semibold mt-0.5 inline-block">Detail →</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
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
