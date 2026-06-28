@extends('layouts.master')
@section('title', 'Detail Produk – Recyclink')
@section('content')
<div class="bg-gray-50 min-h-screen py-8">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    
    <!-- Back Button -->
    <div class="mb-8">
      <a href="/marketplace" class="inline-flex items-center text-sm font-semibold text-gray-500 hover:text-brand transition-colors">
        <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Kembali
      </a>
    </div>

    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden mb-12">
      <div class="flex flex-col lg:flex-row">
        
        <!-- Product Images -->
        <div class="w-full lg:w-5/12 p-6 lg:p-8 border-b lg:border-b-0 lg:border-r border-gray-100">
          <div class="aspect-w-1 aspect-h-1 w-full rounded-xl overflow-hidden bg-gray-100 relative">
            <img src="{{ $listing->image }}" alt="{{ $listing->title }}"
                 class="w-full h-full object-cover object-center" id="mainImage">
            <span class="absolute top-4 left-4 bg-brand text-white text-[10px] font-bold uppercase tracking-wider px-3 py-1.5 rounded-full shadow-sm">{{ $listing->categoryLabel }}</span>
          </div>
        </div>

        <!-- Product Details -->
        <div class="w-full lg:w-7/12 p-6 lg:p-8 flex flex-col">
          <div class="mb-2 flex items-center gap-2 text-sm text-gray-500">
            <svg class="w-4 h-4 text-brand" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0zM15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <span>{{ $listing->city }}</span>
          </div>

          <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 mb-4 leading-tight">{{ $listing->title }}</h1>
          
          <div class="mb-6 flex items-end gap-3">
            <span class="text-3xl sm:text-4xl font-bold text-brand">Rp {{ number_format($listing->price, 0, ',', '.') }}</span>
            <span class="text-lg text-gray-500 mb-1">/ {{ $listing->unit }}</span>
          </div>
          
          <div class="bg-brand/5 border border-brand/10 rounded-xl p-5 mb-8 flex flex-wrap gap-6 sm:gap-10">
            <div>
              <p class="text-xs text-brand-hover uppercase font-semibold tracking-wider mb-1">Minimum Order</p>
              <p class="text-sm font-bold text-gray-900">{{ $listing->moq }}</p>
            </div>
            <div>
              <p class="text-xs text-brand-hover uppercase font-semibold tracking-wider mb-1">Stok Tersedia</p>
              <p class="text-sm font-bold text-gray-900">{{ number_format($listing->stock, 0, ',', '.') }} {{ $listing->unit }}</p>
            </div>
            <div>
              <p class="text-xs text-brand-hover uppercase font-semibold tracking-wider mb-1">Kondisi</p>
              <p class="text-sm font-bold text-gray-900">{{ $listing->kondisi }}</p>
            </div>
          </div>

          <div class="prose prose-sm sm:prose-base text-gray-700 mb-8 max-w-none">
            <h3 class="text-lg font-bold text-gray-900 mb-3">Deskripsi Limbah</h3>
            <p>{{ $listing->desc }}</p>
          </div>

          <div class="mt-auto pt-6 border-t border-gray-100">
            <div class="flex flex-col sm:flex-row gap-4 items-end">
          <div class="mt-auto pt-6 border-t border-gray-100">
            
            <div class="flex flex-col sm:flex-row gap-4 items-end mb-5">
              <!-- Quantity Input -->
              <div class="w-full sm:w-auto">
                <label for="quantity" class="block text-sm font-semibold text-gray-900 mb-2">Kuantitas Pesanan (kg)</label>
                <div class="flex items-center w-full sm:w-36 h-12 border border-gray-300 rounded-xl overflow-hidden bg-white shadow-sm">
                  <button type="button" id="btn-minus" class="w-12 h-full flex items-center justify-center text-gray-500 hover:bg-gray-200 hover:text-brand transition-colors bg-gray-50 font-medium text-xl border-r border-gray-200">-</button>
                  <input type="number" id="quantity" name="quantity" min="1" value="{{ intval($listing->moq) }}"
                         class="w-full h-full text-center border-0 focus:ring-0 text-base font-bold p-0 text-gray-900 bg-white" style="-moz-appearance: textfield;">
                  <button type="button" id="btn-plus" class="w-12 h-full flex items-center justify-center text-gray-500 hover:bg-gray-200 hover:text-brand transition-colors bg-gray-50 font-medium text-xl border-l border-gray-200">+</button>
                </div>
              </div>

              <!-- Total Price Display -->
              <div class="flex-none w-full sm:w-48 flex flex-col justify-center items-center text-center h-12 bg-brand/5 rounded-xl border border-brand/20 px-4">
                 <span class="text-[11px] text-brand-hover uppercase font-bold tracking-wider mb-0.5">Total Harga</span>
                 <span class="text-lg font-bold text-brand leading-none truncate" id="total-price">Rp 0</span>
              </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-3 w-full">
              <button type="button" id="btn-cart" class="flex-1 h-12 bg-brand flex justify-center gap-2 items-center text-white px-4 rounded-xl font-semibold hover:bg-brand-hover transition-all duration-200 shadow hover:shadow-md transform hover:-translate-y-0.5">
                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                <span class="truncate">Masukkan Keranjang</span>
              </button>
              <button type="button" id="btn-chat" class="flex-1 h-12 bg-white border-2 border-brand text-brand flex justify-center gap-2 items-center px-4 rounded-xl font-semibold hover:bg-brand/5 transition-all duration-200 shadow-sm hover:shadow-md transform hover:-translate-y-0.5">
                <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                <span class="truncate">Chat Penjual</span>
              </button>
            </div>
          </div>
          
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Toast Notification -->
<div id="toast-notification" class="fixed top-24 right-0 transform translate-x-full opacity-0 transition-all duration-300 ease-in-out z-[9999] pr-4 pointer-events-none">
    <div class="bg-white border-l-4 border-red-500 p-4 rounded-xl shadow-2xl flex items-start gap-3 max-w-sm pointer-events-auto">
        <svg class="w-5 h-5 text-red-500 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
        </svg>
        <div>
            <h3 class="text-sm font-bold text-red-800">Akses Ditolak</h3>
            <p id="toast-message" class="text-sm text-red-700 mt-1">Maaf, Anda belum login.</p>
        </div>
        <button type="button" id="toast-close" class="text-red-400 hover:text-red-600 transition-colors ml-auto">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>
</div>

<style>
/* Hilangkan panah spinner bawaan input number */
input[type="number"]::-webkit-inner-spin-button,
input[type="number"]::-webkit-outer-spin-button {
    -webkit-appearance: none;
    margin: 0;
}
</style>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const pricePerKg = {{ $listing->price }};
    const input = document.getElementById('quantity');
    const btnMinus = document.getElementById('btn-minus');
    const btnPlus = document.getElementById('btn-plus');
    const totalPriceEl = document.getElementById('total-price');

    // Format angka jadi Rupiah (contoh: 1.500.000)
    function formatRupiah(number) {
        return new Intl.NumberFormat('id-ID').format(number);
    }

    // Update total harga
    function updateTotal() {
        let qty = parseInt(input.value);
        if (isNaN(qty) || qty < 1) qty = 0;
        const total = qty * pricePerKg;
        totalPriceEl.textContent = 'Rp ' + formatRupiah(total);
    }

    // Tombol minus
    btnMinus.addEventListener('click', () => {
        let qty = parseInt(input.value) || 0;
        if (qty > 1) {
            input.value = qty - 1;
            updateTotal();
        }
    });

    // Tombol plus
    btnPlus.addEventListener('click', () => {
        let qty = parseInt(input.value) || 0;
        input.value = qty + 1;
        updateTotal();
    });

    // Ketik manual di input
    input.addEventListener('input', updateTotal);

    // Initial load
    updateTotal();

    // Toast Logic
    const toast = document.getElementById('toast-notification');
    const toastMessage = document.getElementById('toast-message');
    const toastClose = document.getElementById('toast-close');
    let toastTimeout;

    function showToast(message) {
        toastMessage.textContent = message;
        
        // Animasi masuk (geser dari kanan)
        toast.classList.remove('translate-x-full', 'opacity-0');
        
        clearTimeout(toastTimeout);
        
        // Animasi keluar otomatis setelah 4 detik
        toastTimeout = setTimeout(() => {
            hideToast();
        }, 4000);
    }
    
    function hideToast() {
        toast.classList.add('translate-x-full', 'opacity-0');
    }

    toastClose.addEventListener('click', hideToast);

    document.getElementById('btn-cart').addEventListener('click', () => {
        showToast('Maaf, Anda harus masuk (login) terlebih dahulu untuk menambahkan limbah ini ke keranjang Anda.');
    });

    document.getElementById('btn-chat').addEventListener('click', () => {
        showToast('Maaf, fitur chat dengan penjual hanya bisa digunakan oleh akun yang sudah login.');
    });
});
</script>
@endpush
@endsection