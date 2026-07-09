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

    <div class="bg-white border border-gray-200 rounded-3xl shadow-sm overflow-hidden mb-6">
        <div class="px-6 py-5 bg-brand/5 border-b border-gray-100 flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600 font-semibold mb-1">Total Tagihan</p>
                <p class="text-2xl font-bold text-brand">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</p>
            </div>
            <div class="text-right">
                <p class="text-xs text-gray-500 mb-1">Order ID</p>
                <p class="text-sm font-bold text-gray-900">{{ $order->order_code }}</p>
            </div>
        </div>

        

        <form action="{{ route('buyer.orders.payment.store', $order->id) }}" method="POST" class="p-6">
            @csrf
            <input type="hidden" name="payment_method" id="selected-method" value="bca">

            <h3 class="font-bold text-gray-900 mb-4 text-lg">Metode Pembayaran (Payment Gateway)</h3>

            <div class="space-y-4">
                {{-- Virtual Account BCA --}}
                <label class="relative flex items-center p-4 border border-brand bg-brand/5 rounded-2xl cursor-pointer hover:border-brand hover:bg-brand/5 transition-all method-label">
                    <input type="radio" name="method_radio" value="bca" class="hidden" checked onchange="selectMethod(this)">
                    <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center shadow-sm mr-4 shrink-0">
                        <i data-lucide="building-2" class="w-5 h-5 text-brand"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-gray-900 text-sm">Virtual Account BCA</h4>
                        <p class="text-xs text-gray-500 mt-0.5">Otomatis dicek (Bayar via m-BCA / ATM BCA)</p>
                    </div>
                    <div class="w-5 h-5 rounded-full border-2 border-brand bg-brand flex items-center justify-center radio-indicator">
                        <div class="w-2 h-2 rounded-full bg-white"></div>
                    </div>
                </label>

                {{-- Virtual Account BNI --}}
                <label class="relative flex items-center p-4 border border-gray-200 rounded-2xl cursor-pointer hover:border-brand hover:bg-brand/5 transition-all method-label">
                    <input type="radio" name="method_radio" value="bni" class="hidden" onchange="selectMethod(this)">
                    <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center shadow-sm mr-4 shrink-0">
                        <i data-lucide="building-2" class="w-5 h-5 text-brand"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-gray-900 text-sm">Virtual Account BNI</h4>
                        <p class="text-xs text-gray-500 mt-0.5">Otomatis dicek (Bayar via BNI Mobile / ATM BNI)</p>
                    </div>
                    <div class="w-5 h-5 rounded-full border-2 border-gray-300 flex items-center justify-center radio-indicator"></div>
                </label>

                {{-- Virtual Account BRI --}}
                <label class="relative flex items-center p-4 border border-gray-200 rounded-2xl cursor-pointer hover:border-brand hover:bg-brand/5 transition-all method-label">
                    <input type="radio" name="method_radio" value="bri" class="hidden" onchange="selectMethod(this)">
                    <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center shadow-sm mr-4 shrink-0">
                        <i data-lucide="building-2" class="w-5 h-5 text-brand"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-gray-900 text-sm">Virtual Account BRI</h4>
                        <p class="text-xs text-gray-500 mt-0.5">Otomatis dicek (Bayar via BRImo / ATM BRI)</p>
                    </div>
                    <div class="w-5 h-5 rounded-full border-2 border-gray-300 flex items-center justify-center radio-indicator"></div>
                </label>

                {{-- Virtual Account BSI --}}
                <label class="relative flex items-center p-4 border border-gray-200 rounded-2xl cursor-pointer hover:border-brand hover:bg-brand/5 transition-all method-label">
                    <input type="radio" name="method_radio" value="bsi" class="hidden" onchange="selectMethod(this)">
                    <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center shadow-sm mr-4 shrink-0">
                        <i data-lucide="building-2" class="w-5 h-5 text-brand"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-gray-900 text-sm">Virtual Account BSI</h4>
                        <p class="text-xs text-gray-500 mt-0.5">Otomatis dicek (Bayar via BSI Mobile / ATM BSI)</p>
                    </div>
                    <div class="w-5 h-5 rounded-full border-2 border-gray-300 flex items-center justify-center radio-indicator"></div>
                </label>

                {{-- QRIS --}}
                <label class="relative flex items-center p-4 border border-gray-200 rounded-2xl cursor-pointer hover:border-brand hover:bg-brand/5 transition-all method-label">
                    <input type="radio" name="method_radio" value="qris" class="hidden" onchange="selectMethod(this)">
                    <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center shadow-sm mr-4 shrink-0">
                        <i data-lucide="qr-code" class="w-5 h-5 text-brand"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-gray-900 text-sm">QRIS</h4>
                        <p class="text-xs text-gray-500 mt-0.5">Scan kode QRIS menggunakan m-banking atau E-Wallet</p>
                    </div>
                    <div class="w-5 h-5 rounded-full border-2 border-gray-300 flex items-center justify-center radio-indicator"></div>
                </label>

                {{-- COD / Cash --}}
                <label class="relative flex items-center p-4 border border-gray-200 rounded-2xl cursor-pointer hover:border-brand hover:bg-brand/5 transition-all method-label">
                    <input type="radio" name="method_radio" value="cash_on_delivery" class="hidden" onchange="selectMethod(this)">
                    <div class="w-10 h-10 rounded-xl bg-white flex items-center justify-center shadow-sm mr-4 shrink-0">
                        <i data-lucide="banknote" class="w-5 h-5 text-brand"></i>
                    </div>
                    <div class="flex-1">
                        <h4 class="font-bold text-gray-900 text-sm">Cash / COD</h4>
                        <p class="text-xs text-gray-500 mt-0.5">Bayar di tempat saat pengambilan / pengiriman</p>
                    </div>
                    <div class="w-5 h-5 rounded-full border-2 border-gray-300 flex items-center justify-center radio-indicator"></div>
                </label>
            </div>

            <button type="submit" class="w-full h-14 mt-8 bg-brand hover:bg-brand-hover text-white font-bold text-base rounded-2xl shadow transform hover:-translate-y-0.5 transition-all flex items-center justify-center gap-2">
                Bayar Sekarang
                <i data-lucide="arrow-right" class="w-5 h-5"></i>
            </button>
        </form>
    </div>
</div>

<script>
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
    }
</script>
@endsection
