@extends('buyer.layouts.buyer')
@section('title', 'Pilih Metode Pembayaran - Recyclink')
@section('header_title', 'Pilih Metode Pembayaran')

@section('content')
<div class="max-w-2xl mx-auto p-6 lg:p-8">

    <div class="mb-5 flex items-center justify-between">
        <a href="{{ route('buyer.orders.show', $order->id) }}" class="inline-flex items-center gap-2 text-sm font-semibold text-gray-500 hover:text-brand transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Kembali
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
                <p class="text-sm text-gray-600 font-semibold mb-1">Total Tagihan</p>
                <p class="text-2xl font-bold text-brand">Rp {{ number_format((float)($order->total_amount ?? 0), 0, ',', '.') }}</p>
            </div>
            <div class="text-right">
                <p class="text-xs text-gray-500 mb-1">Order ID</p>
                <p class="text-sm font-bold text-gray-900">{{ $order->order_code }}</p>
            </div>
        </div>

        @php
            $basePlatformFee = $order->subtotal * 0.05;
            $baseTotal = $order->subtotal + $order->shipping_cost + $basePlatformFee;
            $methods = [
                'bca' => ['name' => 'Virtual Account BCA', 'fee' => 4300, 'min' => 10000, 'icon' => 'building-2', 'desc' => 'Otomatis dicek (Bayar via m-BCA / ATM BCA)'],
                'bni' => ['name' => 'Virtual Account BNI', 'fee' => 3000, 'min' => 15000, 'icon' => 'building-2', 'desc' => 'Otomatis dicek (Bayar via BNI Mobile / ATM BNI)'],
                'bri' => ['name' => 'Virtual Account BRI', 'fee' => 3000, 'min' => 15000, 'icon' => 'building-2', 'desc' => 'Otomatis dicek (Bayar via BRImo / ATM BRI)'],
                'bsi' => ['name' => 'Virtual Account BSI', 'fee' => 3900, 'min' => 10000, 'icon' => 'building-2', 'desc' => 'Otomatis dicek (Bayar via BSI Mobile / ATM BSI)'],
                'qris' => ['name' => 'QRIS', 'fee' => ceil($baseTotal * 0.007) + 500, 'min' => 1000, 'icon' => 'qr-code', 'desc' => 'Scan kode QRIS menggunakan m-banking atau E-Wallet'],
                'cash_on_delivery' => ['name' => 'Cash / COD', 'fee' => 0, 'min' => 0, 'icon' => 'banknote', 'desc' => 'Bayar di tempat saat pengambilan / pengiriman'],
            ];

            $availableMethods = array_filter($methods, function($m) use ($baseTotal) {
                return $baseTotal >= $m['min'];
            });
            
            $firstAvailable = array_key_first($availableMethods);
        @endphp

        <form action="{{ route('buyer.orders.payment.store', $order->id) }}" method="POST" class="p-6">
            @csrf
            <input type="hidden" name="payment_method" id="selected-method" value="{{ $firstAvailable }}">

            <h3 class="font-bold text-gray-900 mb-4 text-lg">Metode Pembayaran (Payment Gateway)</h3>

            <div class="space-y-4">
                @foreach($availableMethods as $key => $method)
                    <label class="relative flex items-center p-4 border {{ $key === $firstAvailable ? 'border-brand bg-brand/5' : 'border-gray-200' }} rounded-2xl cursor-pointer hover:border-brand hover:bg-brand/5 transition-all method-label">
                        <input type="radio" name="method_radio" value="{{ $key }}" data-fee="{{ $method['fee'] }}" class="hidden" {{ $key === $firstAvailable ? 'checked' : '' }} onchange="selectMethod(this)">
                        <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center shadow-sm mr-4 shrink-0">
                            <i data-lucide="{{ $method['icon'] }}" class="w-5 h-5 text-brand"></i>
                        </div>
                        <div class="flex-1">
                            <h4 class="font-bold text-gray-900 text-sm">{{ $method['name'] }}</h4>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $method['desc'] }}</p>
                            @if($method['fee'] > 0)
                                <p class="text-xs font-semibold text-brand mt-1">+ Rp {{ number_format($method['fee'], 0, ',', '.') }} (Biaya Admin)</p>
                            @endif
                        </div>
                        <div class="w-5 h-5 rounded-full border-2 {{ $key === $firstAvailable ? 'border-brand bg-brand' : 'border-gray-300' }} flex items-center justify-center radio-indicator">
                            @if($key === $firstAvailable)
                                <div class="w-2 h-2 rounded-full bg-white"></div>
                            @endif
                        </div>
                    </label>
                @endforeach
            </div>

            <button type="submit" class="w-full h-14 mt-8 bg-brand hover:bg-brand-hover text-white font-bold text-base rounded-2xl shadow transform hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2">
                Bayar Sekarang
                <i data-lucide="arrow-right" class="w-5 h-5"></i>
            </button>
        </form>
    </div>
</div>

<script>
    const baseTotal = {{ $baseTotal }};
    const totalElement = document.querySelector('.text-2xl.font-bold.text-brand');

    function selectMethod(radio) {
        document.getElementById('selected-method').value = radio.value;
        
        // Reset all
        document.querySelectorAll('.method-label').forEach(el => {
            el.classList.remove('border-brand', 'bg-brand/5');
            el.classList.add('border-gray-200');
            const ind = el.querySelector('.radio-indicator');
            ind.classList.remove('border-brand', 'bg-brand');
            ind.classList.add('border-gray-300');
            ind.innerHTML = '';
        });
        
        // Highlight active
        const parent = radio.closest('.method-label');
        parent.classList.add('border-brand', 'bg-brand/5');
        parent.classList.remove('border-gray-200');
        const ind = parent.querySelector('.radio-indicator');
        ind.classList.add('border-brand', 'bg-brand');
        ind.classList.remove('border-gray-300');
        ind.innerHTML = '<div class="w-2 h-2 rounded-full bg-white"></div>';

        // Update total
        const fee = parseFloat(radio.getAttribute('data-fee')) || 0;
        const newTotal = baseTotal + fee;
        totalElement.innerHTML = 'Rp ' + newTotal.toLocaleString('id-ID');
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
