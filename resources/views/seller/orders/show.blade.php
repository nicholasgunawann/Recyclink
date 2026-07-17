@extends('seller.layouts.seller')

@section('title', 'Detail Pesanan #' . $order->order_code . ' - Recyclink')
@section('header_title', 'Detail Pesanan')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    {{-- Top Action Bar --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('seller.orders.index') }}" class="inline-flex items-center text-sm font-semibold text-gray-500 hover:text-brand transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
            Kembali ke Daftar Pesanan
        </a>
        <div class="flex items-center gap-3">
            @php
                // ponytail: statuses map
                $statusConfig = [
                    'pending' => ['bg' => 'bg-amber-50 border-amber-200 text-amber-700', 'label' => 'Menunggu Konfirmasi'],
                    'waiting_payment' => ['bg' => 'bg-blue-50 border-blue-200 text-blue-700', 'label' => 'Menunggu Pembayaran'],
                    'paid' => ['bg' => 'bg-emerald-50 border-emerald-200 text-emerald-700', 'label' => 'Sudah Dibayar / Siap Diproses'],
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
        </div>
    </div>

    {{-- Main Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
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

            {{-- Action Forms (Accept / Reject / Process) --}}
            @if($order->order_status === 'pending' || $order->order_status === 'paid')
                <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                    <h4 class="font-bold text-gray-900 text-base mb-4">Tindakan Pesanan</h4>
                    
                    @if($order->order_status === 'pending')
                        <div class="flex flex-col sm:flex-row gap-3">
                            <form action="{{ route('seller.orders.accept', $order->id) }}" method="POST" class="flex-1">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="w-full py-3 bg-brand hover:bg-brand-hover text-white font-bold rounded-xl transition-all shadow-sm flex items-center justify-center gap-2">
                                    <i data-lucide="check" class="w-4 h-4"></i> Terima Pesanan
                                </button>
                            </form>
                            <button type="button" onclick="document.getElementById('reject-form-container').classList.remove('hidden')" class="flex-1 py-3 bg-white border border-gray-300 text-red-600 font-bold rounded-xl hover:bg-red-50 hover:border-red-200 transition-all flex items-center justify-center gap-2">
                                <i data-lucide="x" class="w-4 h-4"></i> Tolak Pesanan
                            </button>
                        </div>

                        {{-- Rejection Form Panel --}}
                        <div id="reject-form-container" class="hidden mt-5 p-5 bg-rose-50 border border-rose-100 rounded-xl">
                            <form action="{{ route('seller.orders.reject', $order->id) }}" method="POST" class="space-y-4">
                                @csrf
                                @method('PATCH')
                                <div>
                                    <label class="block text-sm font-bold text-rose-900 mb-1.5">Alasan Penolakan</label>
                                    <textarea name="reason" rows="2" required class="w-full border border-rose-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500 focus:border-rose-500 bg-white" placeholder="Sebutkan alasan penolakan..."></textarea>
                                </div>
                                <div class="flex justify-end gap-3 text-xs">
                                    <button type="button" onclick="document.getElementById('reject-form-container').classList.add('hidden')" class="px-4 py-2 text-gray-500 hover:text-gray-700 font-semibold">Batal</button>
                                    <button type="submit" class="px-5 py-2 bg-rose-600 text-white rounded-lg font-bold hover:bg-rose-700 transition-colors">Kirim Penolakan</button>
                                </div>
                            </form>
                        </div>
                    @endif

                    @if($order->order_status === 'paid')
                        <form action="{{ route('seller.orders.processing', $order->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl transition-all shadow-sm flex items-center justify-center gap-2">
                                <i data-lucide="play" class="w-4 h-4"></i> Proses Sekarang
                            </button>
                        </form>
                    @endif
                </div>
            @endif

            @if($order->order_status === 'disputed')
                <div class="mt-4">
                    @php
                        $complaint = \App\Models\Complaint::where('order_id', $order->id)->first();
                    @endphp
                    <a href="{{ route('seller.complaints.show', $complaint->id) }}" class="px-5 py-2.5 bg-yellow-100 hover:bg-yellow-200 text-yellow-800 border border-yellow-200 font-bold text-sm rounded-xl transition-colors w-full flex justify-center items-center gap-2">
                        <i data-lucide="alert-circle" class="w-4 h-4"></i> Lihat Resolusi Komplain
                    </a>
                </div>
            @endif

        </div>

        {{-- Right column (Buyer info, Payment Breakdown) --}}
        <div class="space-y-6">
            
            {{-- Buyer Profile Card --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm flex flex-col items-center text-center">
                <img src="https://ui-avatars.com/api/?name={{ urlencode($order->buyer->name) }}&background=f1f5f9&color=64748b" class="w-16 h-16 rounded-full mb-4 border border-gray-100" alt="">
                <h4 class="font-bold text-gray-900 text-base leading-snug">{{ $order->buyer->name }}</h4>
                <p class="text-xs text-gray-400 mt-0.5">Akun Buyer Terverifikasi</p>
                
                <div class="w-full border-t border-gray-100 my-4 pt-4 text-left text-xs space-y-2 text-gray-600">
                    <p class="flex items-center gap-2"><i data-lucide="phone" class="w-3.5 h-3.5 text-gray-400 shrink-0"></i> {{ $order->buyer->phone_number ?? '-' }}</p>
                    <p class="flex items-center gap-2"><i data-lucide="mail" class="w-3.5 h-3.5 text-gray-400 shrink-0"></i> {{ $order->buyer->email }}</p>
                </div>

                {{-- Chat Buyer button --}}
                @if($item)
                    <form action="{{ route('conversations.start', $item->listing_id) }}" method="POST" class="w-full">
                        @csrf
                        <input type="hidden" name="message" value="Halo, saya menindaklanjuti pesanan Anda #{{ $order->order_code }}">
                        <button type="submit" class="w-full py-2.5 bg-brand/10 hover:bg-brand/20 text-brand text-xs font-bold rounded-xl transition-all flex items-center justify-center gap-1.5">
                            <i data-lucide="message-circle" class="w-4 h-4"></i> Chat Pembeli
                        </button>
                    </form>
                @endif
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
                        <span>Total Pendapatan</span>
                        <span class="text-brand">Rp {{ number_format((float)($order->total_amount ?? 0), 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            {{-- Payment Status Info --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm space-y-3">
                <h4 class="font-bold text-gray-900 text-base">Status Pembayaran</h4>
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
                        <span>Menunggu Pembayaran Transfer</span>
                    </div>
                @endif
            </div>

        </div>

    </div>
</div>
@endsection
