@extends('buyer.layouts.buyer')
@section('title', 'Pesanan Saya - Recyclink')
@section('header_title', 'Pesanan Saya')

@section('content')
<div class="p-6 lg:p-8">

    
    

    <div class="mb-6">
        <h2 class="text-xl font-bold text-gray-900">Riwayat Pesanan</h2>
        <p class="text-sm text-gray-500 mt-1">Pantau semua pesanan limbah Anda</p>
    </div>

    @if($orders->isEmpty())
    <div class="bg-white border border-gray-200 border-dashed rounded-2xl py-20 flex flex-col items-center justify-center text-center">
        <div class="w-16 h-16 bg-brand/5 rounded-2xl flex items-center justify-center mb-4">
            <i data-lucide="shopping-cart" class="w-8 h-8 text-brand/40"></i>
        </div>
        <h3 class="text-base font-bold text-gray-700 mb-1">Belum Ada Pesanan</h3>
        <p class="text-sm text-gray-400 max-w-xs">Temukan limbah yang Anda butuhkan di Marketplace dan lakukan pemesanan.</p>
        <a href="{{ route('marketplace.index') }}" class="mt-5 px-5 py-2.5 bg-brand text-white text-sm font-bold rounded-xl hover:bg-brand-hover transition-colors">
            Jelajahi Marketplace
        </a>
    </div>
    @else
    <div class="space-y-4">
        @foreach($orders as $order)
        @php
            $statusStyles = [
                'pending'    => ['bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'label' => 'Menunggu Konfirmasi'],
                'accepted'   => ['bg' => 'bg-blue-100',  'text' => 'text-blue-700',  'label' => 'Diterima Penjual'],
                'processing' => ['bg' => 'bg-purple-100','text' => 'text-purple-700','label' => 'Sedang Diproses'],
                'completed'  => ['bg' => 'bg-emerald-100','text' => 'text-emerald-700','label' => 'Selesai'],
                'cancelled'  => ['bg' => 'bg-gray-100',  'text' => 'text-gray-600',  'label' => 'Dibatalkan'],
                'rejected'   => ['bg' => 'bg-rose-100',  'text' => 'text-rose-700',  'label' => 'Ditolak'],
            ];
            $style = $statusStyles[$order->order_status] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-600', 'label' => strtoupper($order->order_status)];
        @endphp
        <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
            {{-- Header --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-5 py-4 border-b border-gray-100 bg-gray-50/50">
                <div class="flex items-center gap-3">
                    <i data-lucide="package" class="w-5 h-5 text-brand"></i>
                    <div>
                        <p class="text-sm font-bold text-gray-900">{{ $order->order_code }}</p>
                        <p class="text-xs text-gray-400">{{ $order->created_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>
                <span class="self-start sm:self-auto text-xs font-bold px-3 py-1.5 rounded-full {{ $style['bg'] }} {{ $style['text'] }}">
                    {{ $style['label'] }}
                </span>
            </div>

            {{-- Items --}}
            <div class="px-5 py-4">
                @foreach($order->items ?? [] as $item)
                <div class="flex items-center gap-3 mb-3 last:mb-0">
                    @if($item->listing ?? false)
                    <div class="w-14 h-14 bg-gray-100 rounded-xl overflow-hidden shrink-0">
                        <img src="{{ $item->listing->primaryImage ? asset('storage/'.$item->listing->primaryImage->image_url) : '' }}" class="w-full h-full object-cover" onerror="this.style.display='none'">
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-gray-800 truncate">{{ $item->listing->title }}</p>
                        <p class="text-xs text-gray-500">{{ number_format($item->quantity, 2) }} {{ $item->listing->unit }}</p>
                    </div>
                    <p class="text-sm font-bold text-brand shrink-0">Rp {{ number_format($item->subtotal ?? ($item->quantity * $item->price_per_unit), 0, ',', '.') }}</p>
                    @endif
                </div>
                @endforeach
            </div>

            {{-- Footer --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 px-5 py-3 border-t border-gray-100">
                <div>
                    <p class="text-xs text-gray-500">Total Pembayaran</p>
                    <p class="text-base font-bold text-gray-900">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
                </div>
                <a href="{{ route('buyer.orders.show', $order->id) }}" class="text-sm font-bold text-brand hover:text-brand-hover transition-colors border border-brand px-4 py-2 rounded-xl hover:bg-brand hover:text-white">
                    Lihat Detail
                </a>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-8">
        {{ $orders->links() }}
    </div>
    @endif
</div>
@endsection
