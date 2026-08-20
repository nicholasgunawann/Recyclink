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

    @php
        $baseTotal = $order->subtotal + $order->shipping_cost;
        
        // ponytail: All active channels from user's DompetX merchant dashboard
        $groups = [
            'E-Wallet & QRIS (Cek Otomatis)' => [
                'qris' => [
                    'name' => 'QRIS',
                    'code' => 'qris',
                    'fee' => ceil($baseTotal * 0.007) + 500,
                    'fee_text' => '0.7% + Rp 500',
                    'min' => 1000,
                    'max' => 8000000,
                    'icon' => 'qr-code',
                    'desc' => 'Scan QRIS instan via BCA Mobile, Livin, BRImo, GoPay, ShopeePay, DANA, OVO',
                    'badge' => 'Biaya ke Pelanggan'
                ],
            ],
            'Virtual Account (Cek Otomatis)' => [
                'bca' => [
                    'name' => 'Virtual Account BCA',
                    'code' => 'bca',
                    'fee' => 4300,
                    'fee_text' => 'Rp 4.300',
                    'min' => 10000,
                    'max' => 10000000,
                    'icon' => 'building-2',
                    'desc' => 'Bayar via m-BCA / KlikBCA / ATM BCA (Verifikasi Instan Otomatis)',
                    'badge' => 'Aktif'
                ],
                'bri' => [
                    'name' => 'Virtual Account BRI',
                    'code' => 'bri',
                    'fee' => 3000,
                    'fee_text' => 'Rp 3.000',
                    'min' => 15000,
                    'max' => 10000000,
                    'icon' => 'building-2',
                    'desc' => 'Bayar via BRImo / ATM BRI / Internet Banking (Verifikasi Instan Otomatis)',
                    'badge' => 'Aktif'
                ],
                'bni' => [
                    'name' => 'Virtual Account BNI',
                    'code' => 'bni',
                    'fee' => 3000,
                    'fee_text' => 'Rp 3.000',
                    'min' => 15000,
                    'max' => 1000000,
                    'icon' => 'building-2',
                    'desc' => 'Bayar via BNI Mobile Banking / ATM BNI / Internet Banking (Verifikasi Instan Otomatis)',
                    'badge' => 'Aktif'
                ],
                'bsi' => [
                    'name' => 'Virtual Account BSI',
                    'code' => 'bsi',
                    'fee' => 3900,
                    'fee_text' => 'Rp 3.900',
                    'min' => 10000,
                    'max' => 50000000,
                    'icon' => 'building-2',
                    'desc' => 'Bayar via BSI Mobile / ATM BSI / Internet Banking (Verifikasi Instan Otomatis)',
                    'badge' => 'Aktif'
                ],
            ],
            'Portal Pembayaran DompetX' => [
                'dompetx_checkout' => [
                    'name' => 'Halaman Checkout DompetX',
                    'code' => 'checkout',
                    'fee' => 3000,
                    'fee_text' => 'Rp 3.000',
                    'min' => 10000,
                    'max' => 50000000,
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
        $initialFee = isset($flat[$firstAvailable]) ? (float)$flat[$firstAvailable]['fee'] : 0;
        $initialTotal = $baseTotal + $initialFee;
    @endphp

    <div class="bg-white border border-gray-200 rounded-3xl shadow-sm overflow-hidden mb-6">
        <div class="px-6 py-5 bg-brand/5 border-b border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-500 font-semibold mb-0.5">Total Tagihan Pesanan</p>
                <p class="text-2xl font-bold text-brand" id="total-tagihan-display">Rp {{ number_format($initialTotal, 0, ',', '.') }}</p>
            </div>
            <div class="text-right">
                <p class="text-xs text-gray-400 mb-0.5">Order ID</p>
                <p class="text-sm font-bold text-gray-900 font-mono">{{ $order->order_code }}</p>
            </div>
        </div>

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
                                    $isEligible = $baseTotal >= $method['min'] && $baseTotal <= $method['max'];
                                @endphp
                                <label class="relative flex items-start p-4 border rounded-2xl cursor-pointer transition-all method-label {{ $key === $firstAvailable ? 'border-brand bg-brand/5' : 'border-gray-200 hover:border-gray-300' }} {{ !$isEligible ? 'opacity-60 bg-gray-50/70 cursor-not-allowed' : '' }}">
                                    <input type="radio" name="method_radio" value="{{ $key }}" data-fee="{{ $method['fee'] }}" class="hidden" {{ $key === $firstAvailable ? 'checked' : '' }} {{ !$isEligible ? 'disabled' : '' }} onchange="selectMethod(this)">
                                    
                                    <div class="w-11 h-11 rounded-xl bg-white border border-gray-100 flex items-center justify-center shadow-sm mr-4 shrink-0 text-brand mt-0.5">
                                        <i data-lucide="{{ $method['icon'] }}" class="w-5 h-5"></i>
                                    </div>
                                    
                                    <div class="flex-1 min-w-0 pr-2">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <h5 class="font-bold text-gray-900 text-sm">{{ $method['name'] }}</h5>
                                            @if($isEligible)
                                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-200 flex items-center gap-1">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 inline-block"></span>
                                                    {{ $method['badge'] }}
                                                </span>
                                            @else
                                                <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200">
                                                    Min. Rp {{ number_format($method['min'], 0, ',', '.') }}
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-gray-500 mt-1 leading-snug">{{ $method['desc'] }}</p>
                                        
                                        <div class="mt-2.5 flex items-center gap-3 text-[11px] text-gray-500 font-medium flex-wrap">
                                            <span class="text-brand font-bold bg-brand/10 px-2 py-0.5 rounded-md">
                                                Biaya: +Rp {{ number_format($method['fee'], 0, ',', '.') }} ({{ $method['fee_text'] }})
                                            </span>
                                            @if(!$isEligible)
                                                <span class="text-amber-600 font-semibold">
                                                    Tagihan pesanan (Rp {{ number_format($baseTotal, 0, ',', '.') }}) di bawah minimum gateway
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <div class="w-5 h-5 rounded-full border-2 {{ $key === $firstAvailable ? 'border-brand bg-brand' : 'border-gray-300' }} flex items-center justify-center radio-indicator shrink-0 mt-1">
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

            {{-- Price Breakdown Summary --}}
            <div class="mt-6 pt-4 border-t border-gray-100 space-y-2 text-xs text-gray-600 bg-gray-50/80 p-4 rounded-2xl border border-gray-100">
                <div class="flex justify-between">
                    <span>Subtotal Produk & Ongkir:</span>
                    <span class="font-semibold text-gray-800">Rp {{ number_format($baseTotal, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Est. Biaya Gateway (Biaya ke Pelanggan):</span>
                    <span class="font-semibold text-brand" id="summary-fee-display">+ Rp {{ number_format($initialFee, 0, ',', '.') }}</span>
                </div>
                <div class="border-t border-gray-200 pt-2 flex justify-between text-sm font-bold text-gray-900">
                    <span>Estimasi Total Tagihan:</span>
                    <span class="text-brand font-extrabold text-base" id="summary-total-display">Rp {{ number_format($initialTotal, 0, ',', '.') }}</span>
                </div>
                <p class="text-[10px] text-gray-400 text-center pt-1">* Total final sesuai nilai yang tertera pada halaman pembayaran DompetX</p>
            </div>

            <button type="submit" class="w-full h-14 mt-6 bg-brand hover:bg-brand-hover text-white font-bold text-base rounded-2xl shadow transform hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2 cursor-pointer">
                <span>Lanjutkan Pembayaran</span>
                <i data-lucide="arrow-right" class="w-5 h-5"></i>
            </button>
        </form>
    </div>
</div>

<script>
    const baseTotal = {{ $baseTotal }};

    function formatRupiah(num) {
        return 'Rp ' + Number(num).toLocaleString('id-ID');
    }

    function selectMethod(radio) {
        if (!radio) return;
        const selectedInput = document.getElementById('selected-method');
        if (selectedInput) selectedInput.value = radio.value;
        
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

        // Update totals
        const fee = parseFloat(radio.getAttribute('data-fee')) || 0;
        const newTotal = baseTotal + fee;
        const totalElement = document.getElementById('total-tagihan-display');
        const summaryFeeElement = document.getElementById('summary-fee-display');
        const summaryTotalElement = document.getElementById('summary-total-display');

        if (totalElement) {
            totalElement.textContent = formatRupiah(newTotal);
        }
        if (summaryFeeElement) {
            summaryFeeElement.textContent = '+ ' + formatRupiah(fee);
        }
        if (summaryTotalElement) {
            summaryTotalElement.textContent = formatRupiah(newTotal);
        }
    }

    function initPaymentSelection() {
        const checkedRadio = document.querySelector('input[name="method_radio"]:checked');
        if (checkedRadio) {
            selectMethod(checkedRadio);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initPaymentSelection);
    } else {
        initPaymentSelection();
    }
    document.addEventListener('turbo:load', initPaymentSelection);
    document.addEventListener('turbo:render', initPaymentSelection);
</script>
@endsection
