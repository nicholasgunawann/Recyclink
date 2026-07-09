@extends('seller.layouts.seller')

@section('title', 'Tarik Saldo - Seller Recyclink')
@section('header_title', 'Tarik Saldo')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-8">
        <a href="{{ route('seller.wallet.index') }}" class="inline-flex items-center text-sm font-semibold text-gray-500 hover:text-brand transition-colors mb-4">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> Kembali ke Dompet
        </a>
        <h3 class="text-2xl font-bold text-gray-900">Ajukan Penarikan Saldo</h3>
        <p class="text-gray-600 mt-1">Isi formulir rekening bank di bawah ini untuk menarik dana dari saldo utama.</p>
    </div>

    {{-- Error Alerts --}}
    

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 items-start">
        
        <!-- Info Balance Card -->
        <div class="md:col-span-1 bg-white border border-gray-200 rounded-2xl p-6 shadow-sm space-y-4">
            <div>
                <p class="text-gray-400 font-semibold text-xs uppercase tracking-wider">Saldo Tersedia</p>
                <h4 class="text-xl font-extrabold text-gray-900 mt-1">Rp {{ number_format($wallet->balance, 0, ',', '.') }}</h4>
            </div>
            <hr class="border-gray-100">
            <div class="text-xs text-gray-500 leading-relaxed space-y-2">
                <p class="font-bold text-gray-700">Syarat & Ketentuan:</p>
                <p>&bull; Batas minimum penarikan adalah <strong>Rp 10.000</strong>.</p>
                <p>&bull; Proses pencairan dana membutuhkan waktu 1-3 hari kerja untuk verifikasi admin.</p>
                <p>&bull; Pastikan nomor rekening dan nama pemilik bank tujuan sudah benar.</p>
            </div>
        </div>

        <!-- Form Card -->
        <div class="md:col-span-2 bg-white border border-gray-200 rounded-2xl p-6 lg:p-8 shadow-sm">
            <form action="{{ route('seller.withdrawals.store') }}" method="POST" class="space-y-5">
                @csrf

                <!-- Bank Name -->
                <div>
                    <label for="bank_name" class="block text-sm font-bold text-gray-700 mb-2">Nama Bank</label>
                    <select name="bank_name" id="bank_name" required class="w-full rounded-xl border-gray-200 text-sm focus:border-brand focus:ring-brand/20">
                        <option value="">-- Pilih Bank --</option>
                        <option value="BCA" {{ old('bank_name') === 'BCA' ? 'selected' : '' }}>BCA (Bank Central Asia)</option>
                        <option value="Mandiri" {{ old('bank_name') === 'Mandiri' ? 'selected' : '' }}>Mandiri</option>
                        <option value="BNI" {{ old('bank_name') === 'BNI' ? 'selected' : '' }}>BNI (Bank Negara Indonesia)</option>
                        <option value="BRI" {{ old('bank_name') === 'BRI' ? 'selected' : '' }}>BRI (Bank Rakyat Indonesia)</option>
                        <option value="BSI" {{ old('bank_name') === 'BSI' ? 'selected' : '' }}>BSI (Bank Syariah Indonesia)</option>
                    </select>
                    @error('bank_name')
                        <p class="text-xs text-red-650 mt-1.5 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Account Number -->
                <div>
                    <label for="bank_account_number" class="block text-sm font-bold text-gray-700 mb-2">Nomor Rekening</label>
                    <input type="text" name="bank_account_number" id="bank_account_number" value="{{ old('bank_account_number') }}" required placeholder="Contoh: 1234567890" class="w-full rounded-xl border-gray-200 text-sm focus:border-brand focus:ring-brand/20">
                    @error('bank_account_number')
                        <p class="text-xs text-red-650 mt-1.5 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Account Name -->
                <div>
                    <label for="bank_account_name" class="block text-sm font-bold text-gray-700 mb-2">Nama Pemilik Rekening</label>
                    <input type="text" name="bank_account_name" id="bank_account_name" value="{{ old('bank_account_name') }}" required placeholder="Masukkan nama sesuai buku tabungan" class="w-full rounded-xl border-gray-200 text-sm focus:border-brand focus:ring-brand/20">
                    @error('bank_account_name')
                        <p class="text-xs text-red-650 mt-1.5 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Amount -->
                <div>
                    <label for="amount" class="block text-sm font-bold text-gray-700 mb-2">Jumlah Penarikan (Rp)</label>
                    <input type="number" name="amount" id="amount" value="{{ old('amount') }}" required min="10000" max="{{ (int) $wallet->balance }}" placeholder="Minimal Rp 10.000" class="w-full rounded-xl border-gray-200 text-sm focus:border-brand focus:ring-brand/20">
                    @error('amount')
                        <p class="text-xs text-red-650 mt-1.5 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <div class="pt-4">
                    <button type="submit" class="w-full py-3 bg-brand hover:bg-brand-hover text-white font-bold rounded-xl transition-all shadow-sm flex items-center justify-center gap-2">
                        <i data-lucide="send" class="w-4 h-4"></i> Ajukan Penarikan
                    </button>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection
