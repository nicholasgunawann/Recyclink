@extends('buyer.layouts.buyer')
@section('title', 'Detail Pesanan - Recyclink')
@section('header_title', 'Detail Pesanan')

@section('content')
<div class="p-6 lg:p-8">

    
    

    @php
        $statusStyles = [
            'pending'    => ['bg' => 'bg-amber-100', 'text' => 'text-amber-700', 'label' => 'Menunggu Konfirmasi', 'icon' => 'clock'],
            'waiting_payment' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-700', 'label' => 'Menunggu Pembayaran', 'icon' => 'credit-card'],
            'accepted'   => ['bg' => 'bg-blue-100',  'text' => 'text-blue-700',  'label' => 'Diterima Penjual', 'icon' => 'check'],
            'paid'       => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-700', 'label' => 'Sudah Dibayar', 'icon' => 'check-circle'],
            'processing' => ['bg' => 'bg-purple-100','text' => 'text-purple-700','label' => 'Sedang Diproses', 'icon' => 'loader'],
            'completed'  => ['bg' => 'bg-emerald-100','text' => 'text-emerald-700','label' => 'Selesai', 'icon' => 'check-circle-2'],
            'cancelled'  => ['bg' => 'bg-gray-100',  'text' => 'text-gray-600',  'label' => 'Dibatalkan', 'icon' => 'x-circle'],
            'rejected'   => ['bg' => 'bg-rose-100',  'text' => 'text-rose-700',  'label' => 'Ditolak', 'icon' => 'x-circle'],
        ];
        $style = $statusStyles[$order->order_status] ?? ['bg' => 'bg-gray-100', 'text' => 'text-gray-600', 'label' => strtoupper($order->order_status), 'icon' => 'info'];
    @endphp

    <div class="mb-5 flex items-center justify-between">
        <a href="{{ route('buyer.orders.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-gray-500 hover:text-brand transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Pesanan
        </a>
        <span class="text-xs font-bold px-3 py-1.5 rounded-full {{ $style['bg'] }} {{ $style['text'] }}">{{ $style['label'] }}</span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left: Order Items --}}
        <div class="lg:col-span-2 space-y-5">
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="font-bold text-gray-900">Item Pesanan</h3>
                    <p class="text-xs text-gray-400 mt-0.5">Kode: {{ $order->order_code }} · {{ $order->created_at->format('d M Y H:i') }}</p>
                </div>
                <div class="divide-y divide-gray-100 px-6">
                    @foreach($order->items ?? [] as $item)
                    @if($item->listing ?? false)
                    @php
                        $imgUrl = '';
                        if ($item->listing->primaryImage) {
                            $imgUrl = str_starts_with($item->listing->primaryImage->image_url, 'http')
                                ? $item->listing->primaryImage->image_url
                                : asset('storage/'.$item->listing->primaryImage->image_url);
                        }
                    @endphp
                    <div class="flex items-center gap-4 py-4">
                        <div class="w-16 h-16 bg-gray-100 rounded-xl overflow-hidden shrink-0">
                            @if($imgUrl)
                            <img src="{{ $imgUrl }}" class="w-full h-full object-cover">
                            @else
                            <div class="w-full h-full flex items-center justify-center text-gray-400"><i data-lucide="image" class="w-6 h-6"></i></div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-gray-900">{{ $item->listing->title }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ number_format((float)($item->quantity ?? 0), 2) }} {{ $item->listing->unit ?? '' }} × Rp {{ number_format((float)($item->price_per_unit ?? $item->listing->price_per_unit ?? 0), 0, ',', '.') }}</p>
                        </div>
                        <p class="text-sm font-bold text-brand shrink-0">Rp {{ number_format((float)($item->subtotal ?? ($item->quantity * ($item->price_per_unit ?? $item->listing->price_per_unit ?? 0))), 0, ',', '.') }}</p>
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>

            {{-- Pickup Info --}}
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="font-bold text-gray-900">Informasi Pengambilan</h3>
                </div>
                <div class="px-6 py-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider mb-1">Metode</p>
                        <p class="text-sm font-semibold text-gray-800">{{ $order->pickup_method === 'delivery' ? 'Kurir/Pengiriman' : 'Ambil Sendiri' }}</p>
                    </div>
                    @if($order->pickup_date)
                    <div>
                        <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider mb-1">Jadwal Ambil</p>
                        <p class="text-sm font-semibold text-gray-800">{{ \Carbon\Carbon::parse($order->pickup_date)->format('d M Y') }} {{ $order->pickup_time ? 'pukul '.$order->pickup_time : '' }}</p>
                    </div>
                    @endif
                    @if($order->pickup_address)
                    <div class="sm:col-span-2">
                        <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider mb-1">Alamat</p>
                        <p class="text-sm font-semibold text-gray-800">{{ $order->pickup_address }}</p>
                    </div>
                    @endif
                    @if($order->buyer_note)
                    <div class="sm:col-span-2">
                        <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider mb-1">Catatan</p>
                        <p class="text-sm font-semibold text-gray-800">{{ $order->buyer_note }}</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Action Buttons --}}
            @if(in_array($order->order_status, ['pending', 'accepted', 'waiting_payment', 'paid', 'processing', 'disputed']))
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm px-6 py-4 flex flex-col sm:flex-row gap-3">
                
                @if($order->order_status === 'waiting_payment')
                <a href="{{ route('buyer.orders.payment.create', $order->id) }}" class="px-5 py-2.5 bg-brand hover:bg-brand-hover text-white font-bold text-sm rounded-xl transition-colors flex-1 flex justify-center items-center gap-2">
                    <i data-lucide="credit-card" class="w-4 h-4"></i> Pilih Metode Pembayaran
                </a>
                @endif

                @if(in_array($order->order_status, ['paid', 'processing']))
                <form method="POST" action="{{ route('buyer.orders.complete', $order->id) }}" class="flex-1 flex" data-confirm="Konfirmasi bahwa Anda telah menerima pesanan dengan baik?">
                    @csrf @method('PATCH')
                    <button type="submit" class="w-full px-5 py-2.5 bg-brand hover:bg-brand-hover text-white font-bold text-sm rounded-xl transition-colors flex justify-center items-center gap-2">
                        <i data-lucide="check-circle-2" class="w-4 h-4"></i> Pesanan Diterima
                    </button>
                </form>
                
                <a href="{{ route('buyer.complaints.create', $order->id) }}" class="px-5 py-2.5 bg-orange-100 hover:bg-orange-200 text-orange-700 border border-orange-200 font-bold text-sm rounded-xl transition-colors flex justify-center items-center gap-2 flex-none">
                    <i data-lucide="alert-triangle" class="w-4 h-4"></i> Ajukan Komplain
                </a>
                @endif

                @if($order->order_status === 'disputed')
                @php
                    $complaint = \App\Models\Complaint::where('order_id', $order->id)->first();
                @endphp
                <a href="{{ route('buyer.complaints.show', $complaint->id) }}" class="px-5 py-2.5 bg-yellow-100 hover:bg-yellow-200 text-yellow-800 border border-yellow-200 font-bold text-sm rounded-xl transition-colors w-full flex justify-center items-center gap-2">
                    <i data-lucide="alert-circle" class="w-4 h-4"></i> Lihat Resolusi Komplain
                </a>
                @endif

                @if(in_array($order->order_status, ['pending', 'waiting_payment', 'accepted']))
                <form method="POST" action="{{ route('buyer.orders.cancel', $order->id) }}" class="flex-none" data-confirm="Batalkan pesanan ini?">
                    @csrf @method('PATCH')
                    <input type="hidden" name="reason" value="Dibatalkan oleh pembeli">
                    <button type="submit" class="w-full px-5 py-2.5 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-sm rounded-xl border border-rose-200 transition-colors">
                        Batalkan Pesanan
                    </button>
                </form>
                @endif
            </div>
            @endif
        </div>

        {{-- Right: Summary --}}
        <div class="space-y-5">
            {{-- Payment Instructions (VA/QRIS) --}}
            @if($order->payment && in_array($order->payment->payment_status, ['pending']))
                @if($order->payment->virtual_account_number || $order->payment->qris_url)
                <div class="bg-white border border-brand/20 rounded-2xl shadow-sm overflow-hidden border-2">
                    <div class="px-5 py-4 border-b border-gray-100 bg-brand/5">
                        <h3 class="font-bold text-brand flex items-center gap-2"><i data-lucide="wallet" class="w-4 h-4"></i> Instruksi Pembayaran</h3>
                    </div>
                    <div class="px-5 py-6 flex flex-col items-center text-center">
                        <p class="text-sm text-gray-500 mb-1">Metode yang dipilih:</p>
                        <p class="text-sm font-bold uppercase text-gray-900 mb-4">{{ str_replace('_', ' ', $order->payment->payment_method) }}</p>
                        
                        @if($order->payment->virtual_account_number)
                            <div class="bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 flex items-center justify-between gap-4 mt-2 w-full">
                                <div>
                                    <p class="text-xs text-gray-400 text-left mb-0.5">Nomor Virtual Account</p>
                                    <span class="font-mono text-lg font-bold tracking-wider text-gray-900">{{ $order->payment->virtual_account_number }}</span>
                                </div>
                                <button type="button" onclick="navigator.clipboard.writeText('{{ $order->payment->virtual_account_number }}'); alert('Nomor VA disalin!')" class="text-brand hover:bg-brand/10 p-2 rounded-lg transition-colors"><i data-lucide="copy" class="w-5 h-5"></i></button>
                            </div>
                            <p class="text-xs text-gray-400 mt-4 px-2">Gunakan nomor rekening di atas untuk melakukan transfer dari ATM, Internet Banking, atau Mobile Banking.</p>
                        @endif

                        @if($order->payment->qris_url)
                            <div class="mt-2 border border-gray-100 p-3 rounded-2xl inline-block bg-white shadow-sm">
                                <img src="{{ $order->payment->qris_url }}" alt="QRIS Barcode" class="w-48 h-48 object-contain">
                            </div>
                            <p class="text-xs text-gray-400 mt-4 px-2">Scan kode QR di atas menggunakan aplikasi e-Wallet (OVO, GoPay, Dana) atau Mobile Banking Anda.</p>
                        @endif
                    </div>
                </div>
                @endif
            @endif

            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="font-bold text-gray-900">Ringkasan Pembayaran</h3>
                </div>
                <div class="px-5 py-4 space-y-3 text-sm">
                    <div class="flex justify-between text-gray-600">
                        <span>Subtotal</span>
                        <span class="font-semibold">Rp {{ number_format((float)($order->subtotal ?? $order->total_amount ?? 0), 0, ',', '.') }}</span>
                    </div>
                    @if($order->shipping_cost)
                    <div class="flex justify-between text-gray-600">
                        <span>Ongkos Kirim</span>
                        <span class="font-semibold">Rp {{ number_format((float)($order->shipping_cost ?? 0), 0, ',', '.') }}</span>
                    </div>
                    @endif
                    @if($order->platform_fee)
                    <div class="flex justify-between text-gray-600">
                        <span>Biaya Platform</span>
                        <span class="font-semibold">Rp {{ number_format((float)($order->platform_fee ?? 0), 0, ',', '.') }}</span>
                    </div>
                    @endif
                    <div class="pt-3 border-t border-gray-100 flex justify-between font-bold text-gray-900">
                        <span>Total</span>
                        <span class="text-brand text-base">Rp {{ number_format((float)($order->total_amount ?? 0), 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            {{-- Seller Info --}}
            @if($order->seller)
            <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="font-bold text-gray-900">Informasi Penjual</h3>
                </div>
                <div class="px-5 py-4 flex items-center gap-3">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($order->seller->name) }}&background=7A9C59&color=fff" class="w-10 h-10 rounded-xl shrink-0">
                    <div>
                        <p class="text-sm font-bold text-gray-900">{{ $order->seller->sellerProfile->business_name ?? $order->seller->name }}</p>
                        <p class="text-xs text-gray-500">{{ $order->seller->sellerProfile->city ?? '' }}</p>
                    </div>
                </div>
                <div class="px-5 pb-4">
                    @php $firstItem = $order->items->first(); @endphp
                    @if($firstItem && $firstItem->listing)
                    <a href="{{ route('conversations.start', $firstItem->listing->id) }}" class="block text-center text-sm font-bold border border-brand text-brand hover:bg-brand hover:text-white px-4 py-2 rounded-xl transition-colors">
                        Chat Penjual
                    </a>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
