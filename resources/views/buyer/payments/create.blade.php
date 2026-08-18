@extends('buyer.layouts.buyer')
@section('title', 'Pilih Metode Pembayaran - Recyclink')
@section('header_title', 'Pilih Metode Pembayaran')

@section('content')
<div class="max-w-2xl mx-auto p-6 lg:p-8">

    <div class="mb-5 flex items-center justify-between">
        <a href="{{ route('buyer.orders.show', $order->id) }}" class="inline-flex items-center gap-2 text-sm font-semibold text-gray-500 hover:text-brand transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali ke Pesanan
        </a>
    </div>

    {{-- Flash Messages --}}
    @if(session('error'))
        <div class="mb-4 p-4 bg-red-50 border border-red-200 rounded-2xl flex items-start gap-3">
            <i data-lucide="alert-circle" class="w-5 h-5 text-red-500 mt-0.5 shrink-0"></i>
            <p class="text-sm font-semibold text-red-700">{{ session('error') }}</p>
        </div>
    @endif
    @if(session('success'))
        <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-2xl flex items-start gap-3">
            <i data-lucide="check-circle" class="w-5 h-5 text-green-500 mt-0.5 shrink-0"></i>
            <p class="text-sm font-semibold text-green-700">{{ session('success') }}</p>
        </div>
    @endif

    <div class="bg-white border border-gray-200 rounded-3xl shadow-sm overflow-hidden mb-6">
        <div class="px-6 py-5 bg-brand/5 border-b border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 font-semibold mb-0.5">Total Tagihan</p>
                <p class="text-2xl font-bold text-brand" id="total-tagihan-display">Rp {{ number_format((float)($order->total_amount ?? 0), 0, ',', '.') }}</p>
            </div>
            <div class="text-right">
                <p class="text-xs text-gray-400 mb-0.5">Order ID</p>
                <p class="text-sm font-bold text-gray-900 font-mono">{{ $order->order_code }}</p>
            </div>
        </div>

        @php
            $baseTotal = $order->subtotal + $order->shipping_cost;
            
            // ponytail: DompetX official live / sandbox payment methods
            $groups = [
                'E-Wallet & QRIS (Cek Otomatis)' => [
                    'qris' => [
                        'name' => 'QRIS (Semua Pembayaran)',
                        'fee' => ceil($baseTotal * 0.007) + 500,
                        'min' => 1000,
                        'icon' => 'qr-code',
                        'desc' => 'Scan QRIS instan via BCA Mobile, Livin by Mandiri, BRImo, GoPay, ShopeePay, DANA, OVO',
                        'badge' => 'Rekomendasi'
                    ],
                ],
                'Virtual Account (Cek Otomatis)' => [
                    'bri' => [
                        'name' => 'Virtual Account BRI',
                        'fee' => 3000,
                        'min' => 10000,
                        'icon' => 'building-2',
                        'desc' => 'Bayar via BRImo / ATM BRI / Internet Banking (Verifikasi Instan Otomatis)',
                        'badge' => 'Instan'
                    ],
                    'bni' => [
                        'name' => 'Virtual Account BNI',
                        'fee' => 3000,
                        'min' => 10000,
                        'icon' => 'building-2',
                        'desc' => 'Bayar via BNI Mobile Banking / ATM BNI / Internet Banking (Verifikasi Instan Otomatis)',
                        'badge' => 'Instan'
                    ],
                ],
                'Portal Pembayaran DompetX' => [
                    'dompetx_checkout' => [
                        'name' => 'Halaman Checkout DompetX',
                        'fee' => 3000,
                        'min' => 10000,
                        'icon' => 'external-link',
                        'desc' => 'Buka portal pembayaran resmi DompetX untuk memilih seluruh metode yang tersedia',
                        'badge' => 'Portal Resmi'
                    ],
                ],
            ];

            // Flatten to find first available
            $flat = [];
            foreach ($groups as $grp => $items) {
                foreach ($items as $k => $v) {
                    $flat[$k] = $v;
                }
            }
            $availableMethods = array_filter($flat, fn($m) => $baseTotal >= $m['min']);
            $firstAvailable = array_key_first($availableMethods) ?? 'qris';
        @endphp

        <form action="{{ route('buyer.orders.payment.store', $order->id) }}" method="POST" class="p-6">
            @csrf
            <input type="hidden" name="payment_method" id="selected-method" value="{{ $firstAvailable }}">

            <div class="space-y-6">
                @foreach($groups as $groupTitle => $items)
                    <div>
                        <h4 class="text-xs font-bold uppercase tracking-wider text-gray-400 mb-3">{{ $groupTitle }}</h4>
                        <div class="space-y-3">
                            @foreach($items as $key => $method)
                                @php
                                    $isEligible = $baseTotal >= $method['min'];
                                @endphp
                                <label class="relative flex items-center p-4 border rounded-2xl cursor-pointer transition-all method-label {{ $key === $firstAvailable ? 'border-brand bg-brand/5' : 'border-gray-200 hover:border-gray-300' }} {{ !$isEligible ? 'opacity-50 pointer-events-none' : '' }}">
                                    <input type="radio" name="method_radio" value="{{ $key }}" data-fee="{{ $method['fee'] }}" class="hidden" {{ $key === $firstAvailable ? 'checked' : '' }} {{ !$isEligible ? 'disabled' : '' }} onchange="selectMethod(this)">
                                    <div class="w-10 h-10 rounded-xl bg-white border border-gray-100 flex items-center justify-center shadow-sm mr-4 shrink-0 text-brand">
                                        <i data-lucide="{{ $method['icon'] }}" class="w-5 h-5"></i>
                                    </div>
                                    <div class="flex-1 min-w-0 pr-2">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <h5 class="font-bold text-gray-900 text-sm">{{ $method['name'] }}</h5>
                                            @if(isset($method['badge']))
                                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $method['badge'] === 'Rekomendasi' ? 'bg-brand/10 text-brand' : 'bg-emerald-50 text-emerald-700' }}">{{ $method['badge'] }}</span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-gray-500 mt-0.5 leading-snug">{{ $method['desc'] }}</p>
                                        @if($method['fee'] > 0)
                                            <p class="text-xs font-semibold text-brand mt-1">+ Rp {{ number_format($method['fee'], 0, ',', '.') }} (Biaya Admin)</p>
                                        @else
                                            <p class="text-xs font-semibold text-emerald-600 mt-1">Gratis Biaya Admin</p>
                                        @endif
                                    </div>
                                    <div class="w-5 h-5 rounded-full border-2 {{ $key === $firstAvailable ? 'border-brand bg-brand' : 'border-gray-300' }} flex items-center justify-center radio-indicator shrink-0">
                                        @if($key === $firstAvailable)
                                            <div class="w-2 h-2 rounded-full bg-white"></div>
                                        @endif
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <button type="submit" class="w-full h-14 mt-8 bg-brand hover:bg-brand-hover text-white font-bold text-base rounded-2xl shadow transform hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2 cursor-pointer">
                <span>Lanjutkan Pembayaran</span>
                <i data-lucide="arrow-right" class="w-5 h-5"></i>
            </button>
        </form>
    </div>
</div>

<script>
    const baseTotal = {{ $baseTotal }};
    const totalElement = document.getElementById('total-tagihan-display');

    function selectMethod(radio) {
        document.getElementById('selected-method').value = radio.value;
        
        // Reset all
        document.querySelectorAll('.method-label').forEach(el => {
            el.classList.remove('border-brand', 'bg-brand/5');
            el.classList.add('border-gray-200');
            const ind = el.querySelector('.radio-indicator');
            if (ind) {
                ind.classList.remove('border-brand', 'bg-brand');
                ind.classList.add('border-gray-300');
                ind.innerHTML = '';
            }
        });
        
        // Highlight active
        const parent = radio.closest('.method-label');
        if (parent) {
            parent.classList.add('border-brand', 'bg-brand/5');
            parent.classList.remove('border-gray-200');
            const ind = parent.querySelector('.radio-indicator');
            if (ind) {
                ind.classList.add('border-brand', 'bg-brand');
                ind.classList.remove('border-gray-300');
                ind.innerHTML = '<div class="w-2 h-2 rounded-full bg-white"></div>';
            }
        }

        // Update total
        const fee = parseFloat(radio.getAttribute('data-fee')) || 0;
        const newTotal = baseTotal + fee;
        if (totalElement) {
            totalElement.innerHTML = 'Rp ' + newTotal.toLocaleString('id-ID');
        }
    }

    // Initialize total on load
    window.addEventListener('DOMContentLoaded', () => {
        const checkedRadio = document.querySelector('input[name="method_radio"]:checked');
        if (checkedRadio) {
            selectMethod(checkedRadio);
        }
    });
</script>
@endsection
