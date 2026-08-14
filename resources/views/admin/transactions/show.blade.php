@extends('admin.layouts.admin')

@section('title', 'Detail Transaksi #' . $order->order_code . ' - Admin Recyclink')
@section('header_title', 'Detail Transaksi')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    {{-- Top Action Bar --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.transactions.index') }}" class="inline-flex items-center text-sm font-semibold text-gray-500 hover:text-brand transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
            Kembali ke Daftar Transaksi
        </a>
        <div class="flex items-center gap-3">
            @php
                // ponytail: statuses map
                $statusConfig = [
                    'pending' => ['bg' => 'bg-amber-50 border-amber-200 text-amber-700', 'label' => 'Menunggu Konfirmasi'],
                    'waiting_payment' => ['bg' => 'bg-blue-50 border-blue-200 text-blue-700', 'label' => 'Menunggu Pembayaran'],
                    'paid' => ['bg' => 'bg-emerald-50 border-emerald-200 text-emerald-700', 'label' => 'Sudah Dibayar'],
                    'processing' => ['bg' => 'bg-indigo-50 border-indigo-200 text-indigo-700', 'label' => 'Sedang Diproses'],
                    'completed' => ['bg' => 'bg-gray-100 border-gray-200 text-gray-700', 'label' => 'Selesai'],
                    'rejected' => ['bg' => 'bg-rose-50 border-rose-200 text-rose-700', 'label' => 'Ditolak'],
                    'cancelled' => ['bg' => 'bg-red-50 border-red-200 text-red-700', 'label' => 'Dibatalkan']
                ];
                $status = $statusConfig[$order->order_status] ?? ['bg' => 'bg-gray-50 border-gray-200 text-gray-700', 'label' => strtoupper($order->order_status)];
            @endphp
            <span class="inline-flex items-center px-3.5 py-1.5 rounded-full text-xs font-bold border {{ $status['bg'] }}">
                {{ $status['label'] }}
            </span>
            @if(!in_array($order->order_status, ['paid', 'processing', 'completed']))
                <form action="{{ route('admin.transactions.destroy', $order->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus transaksi {{ $order->order_code }} yang belum selesai ini?');" class="inline" data-turbo="false">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="inline-flex items-center justify-center px-3 py-1.5 border border-red-200 text-red-600 hover:bg-red-50 hover:border-red-300 font-bold text-xs rounded-xl bg-white transition-all cursor-pointer" title="Hapus Transaksi">
                        <i data-lucide="trash-2" class="w-3.5 h-3.5 mr-1"></i> Hapus Transaksi
                    </button>
                </form>
            @endif
        </div>
    </div>

    @if(session('error'))
        <div class="p-4 bg-red-50 border border-red-200 rounded-2xl flex items-start gap-3">
            <i data-lucide="alert-circle" class="w-5 h-5 text-red-500 mt-0.5 shrink-0"></i>
            <p class="text-sm font-semibold text-red-700">{{ session('error') }}</p>
        </div>
    @endif
        
        {{-- Left column (Order Details, Items, Pickup Info) --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Order & Product Info --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <h4 class="font-bold text-gray-900 text-base mb-4">Informasi Produk</h4>
                
                @php $item = $order->items->first(); @endphp
                @if($item)
                    <div class="flex items-start gap-4">
                        <div class="w-16 h-16 rounded-xl overflow-hidden shrink-0 border border-gray-100 bg-gray-50">
                            <img src="{{ $item->listing && $item->listing->primaryImage ? $item->listing->primaryImage->url : 'https://placehold.co/100x100?text=Limbah' }}" class="w-full h-full object-cover" alt="">
                        </div>
                        <div class="flex-1 min-w-0">
                            <h5 class="font-bold text-gray-900 leading-snug">{{ $item->waste_name_snapshot }}</h5>
                            <p class="text-sm text-gray-500 mt-1 font-semibold">Rp {{ number_format((float)($item->price_per_unit_snapshot ?? 0), 0, ',', '.') }} / {{ $item->unit }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">Jumlah: {{ number_format((float)($item->quantity ?? 0), 0, ',', '.') }} {{ $item->unit }}</p>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Pickup Details --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm space-y-4">
                <h4 class="font-bold text-gray-900 text-base">Informasi Pengambilan / Logistik</h4>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-400 font-semibold text-xs uppercase tracking-wider">Metode Pengambilan</p>
                        <p class="text-gray-800 font-bold mt-1">{{ $order->pickup_method === 'delivery' ? 'Kirim ke Lokasi Buyer' : 'Ambil Sendiri (Pickup)' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 font-semibold text-xs uppercase tracking-wider">Waktu Rencana Pengambilan</p>
                        <p class="text-gray-800 font-bold mt-1">
                            {{ $order->pickup_date ? \Carbon\Carbon::parse($order->pickup_date)->format('d M Y') : '-' }} 
                            {{ $order->pickup_time ? ' Jam ' . $order->pickup_time : '' }}
                        </p>
                    </div>
                    <div class="sm:col-span-2">
                        <p class="text-gray-400 font-semibold text-xs uppercase tracking-wider">Alamat Pengambilan</p>
                        <p class="text-gray-800 mt-1 font-medium leading-relaxed">{{ $order->pickup_address ?? '-' }}</p>
                    </div>
                    <div class="sm:col-span-2">
                        <p class="text-gray-400 font-semibold text-xs uppercase tracking-wider">Catatan Tambahan Buyer</p>
                        <p class="text-gray-800 mt-1 italic font-medium">"{{ $order->buyer_note ?? 'Tidak ada catatan' }}"</p>
                    </div>
                </div>
            </div>

        </div>

        {{-- Right column (Buyer, Seller & Payment Breakdown) --}}
        <div class="space-y-6">
            
            {{-- Buyer Profile Card --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm space-y-4">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Pihak Pembeli (Buyer)</span>
                <div class="flex items-center gap-3">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($order->buyer->name ?? 'User') }}&background=f1f5f9&color=64748b" class="w-10 h-10 rounded-full border border-gray-100" alt="">
                    <div>
                        <h4 class="font-bold text-gray-900 text-sm leading-snug">{{ $order->buyer->name ?? 'N/A' }}</h4>
                        <p class="text-xs text-brand font-medium">Pembeli Terdaftar</p>
                    </div>
                </div>
                <div class="text-xs text-gray-500 space-y-1.5 pt-2 border-t border-gray-50">
                    <p class="flex items-center gap-2"><i data-lucide="mail" class="w-3.5 h-3.5 text-gray-400"></i> {{ $order->buyer->email ?? '-' }}</p>
                    <p class="flex items-center gap-2"><i data-lucide="phone" class="w-3.5 h-3.5 text-gray-400"></i> {{ $order->buyer->phone_number ?? '-' }}</p>
                </div>
            </div>

            {{-- Seller Profile Card --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm space-y-4">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Pihak Penjual (Seller)</span>
                <div class="flex items-center gap-3">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($order->seller->name ?? 'User') }}&background=f1f5f9&color=64748b" class="w-10 h-10 rounded-full border border-gray-100" alt="">
                    <div>
                        <h4 class="font-bold text-gray-900 text-sm leading-snug">{{ $order->seller->name ?? 'N/A' }}</h4>
                        <p class="text-xs text-emerald-600 font-medium font-semibold">Penjual Terdaftar</p>
                    </div>
                </div>
                <div class="text-xs text-gray-500 space-y-1.5 pt-2 border-t border-gray-50">
                    <p class="flex items-center gap-2"><i data-lucide="mail" class="w-3.5 h-3.5 text-gray-400"></i> {{ $order->seller->email ?? '-' }}</p>
                    <p class="flex items-center gap-2"><i data-lucide="phone" class="w-3.5 h-3.5 text-gray-400"></i> {{ $order->seller->phone_number ?? '-' }}</p>
                </div>
            </div>

            {{-- Cost Breakdown --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm space-y-4">
                <h4 class="font-bold text-gray-900 text-base">Rincian Pembayaran</h4>
                
                <div class="space-y-2.5 text-sm text-gray-600">
                    <div class="flex justify-between">
                        <span>Subtotal Produk</span>
                        <span class="font-semibold text-gray-800">Rp {{ number_format((float)($order->subtotal ?? 0), 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Biaya Penanganan (5%)</span>
                        <span class="font-semibold text-gray-800">Rp {{ number_format((float)($order->platform_fee ?? 0), 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Biaya Pengiriman</span>
                        <span class="font-semibold text-gray-800">Rp {{ number_format((float)($order->shipping_cost ?? 0), 0, ',', '.') }}</span>
                    </div>
                    <hr class="border-gray-100 my-2">
                    <div class="flex justify-between text-base font-bold text-gray-900">
                        <span>Total Transaksi</span>
                        <span class="text-brand">Rp {{ number_format((float)($order->total_amount ?? 0), 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            {{-- Payment Status Info --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm space-y-3">
                <h4 class="font-bold text-gray-900 text-base">Status Transfer & Bukti</h4>
                @if($order->payment)
                    <div class="p-3 bg-emerald-50 border border-emerald-100 rounded-xl text-emerald-800 text-xs flex items-center gap-2">
                        <i data-lucide="check-circle" class="w-4 h-4 shrink-0"></i>
                        <span>Pembayaran Telah Diverifikasi</span>
                    </div>
                    @if($order->payment->payment_proof)
                        <div class="mt-2">
                            <p class="text-xs text-gray-400 font-semibold mb-1">Bukti Transfer:</p>
                            <a href="{{ asset('storage/' . $order->payment->payment_proof) }}" target="_blank" class="block w-full border border-gray-200 hover:border-brand rounded-xl overflow-hidden shadow-sm group">
                                <img src="{{ asset('storage/' . $order->payment->payment_proof) }}" alt="Bukti Pembayaran" class="w-full h-auto object-cover max-h-40 group-hover:scale-105 transition-transform">
                            </a>
                        </div>
                    @endif
                @else
                    <div class="p-3 bg-amber-50 border border-amber-100 rounded-xl text-amber-800 text-xs flex items-center gap-2">
                        <i data-lucide="clock" class="w-4 h-4 shrink-0"></i>
                        <span>Menunggu Pembayaran</span>
                    </div>
                @endif
            </div>

        </div>

    </div>
</div>
@endsection
