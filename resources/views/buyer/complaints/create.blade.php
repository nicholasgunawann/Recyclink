@extends('buyer.layouts.buyer')
@section('title', 'Ajukan Komplain / Pengembalian')
@section('header_title', 'Ajukan Komplain')

@section('content')
<div class="p-6 max-w-3xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden p-6 lg:p-8">
        
        <h2 class="text-2xl font-bold text-gray-900 mb-6">Pusat Resolusi: Ajukan Komplain</h2>
        
        <div class="mb-6 p-4 bg-orange-50 border border-orange-200 rounded-xl flex items-start gap-3">
            <i data-lucide="alert-circle" class="w-5 h-5 text-orange-600 mt-0.5 shrink-0"></i>
            <div>
                <h4 class="font-bold text-orange-800 text-sm mb-1">Informasi & Syarat Pengajuan Komplain</h4>
                <ul class="text-xs text-orange-700 space-y-1.5 list-disc ml-4">
                    <li><strong>Batas Waktu:</strong> Komplain harus diajukan sebelum batas waktu penerimaan otomatis selesai (maks 2 hari sejak pesanan dibayar) atau sebelum tombol "Pesanan Diterima" Anda klik.</li>
                    <li><strong>Status Selesai:</strong> Jika pesanan sudah berstatus "Selesai", tombol komplain otomatis hilang dan dana akan diteruskan. Anda harus menghubungi penjual secara pribadi untuk ganti rugi.</li>
                    <li><strong>Bukti Wajib:</strong> Lampirkan Video Unboxing (tanpa jeda/edit saat membuka paket) dan foto kerusakan atau ketidaksesuaian produk.</li>
                </ul>
            </div>
        </div>

        <form action="{{ route('buyer.complaints.store', $order->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Subjek -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Subjek Komplain</label>
                <input type="text" name="subject" class="w-full border-gray-300 rounded-xl shadow-sm focus:ring-brand focus:border-brand px-4 py-3" placeholder="Misal: Barang tidak sesuai pesanan" required>
            </div>

            <!-- Tipe Komplain -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Alasan Komplain</label>
                <select name="complaint_type" class="w-full border-gray-300 rounded-xl shadow-sm focus:ring-brand focus:border-brand px-4 py-3" required>
                    <option value="">-- Pilih Alasan --</option>
                    <option value="missing_item">Barang tidak lengkap / kurang</option>
                    <option value="wrong_item">Barang salah / tidak sesuai deskripsi</option>
                    <option value="damaged">Barang rusak / cacat</option>
                    <option value="not_received">Pesanan belum sampai</option>
                    <option value="other">Lainnya</option>
                </select>
            </div>

            <!-- Deskripsi -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Detail Kronologi & Deskripsi</label>
                <textarea name="description" rows="5" class="w-full border-gray-300 rounded-xl shadow-sm focus:ring-brand focus:border-brand px-4 py-3 resize-none" placeholder="Ceritakan secara detail masalah yang Anda alami..." required></textarea>
            </div>

            <!-- Bukti (Evidence) -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-2">Lampirkan Bukti (Foto/Video Unboxing)</label>
                <input type="file" name="evidence" accept="image/jpeg,image/png,video/mp4" class="block w-full text-sm text-gray-500
                    file:mr-4 file:py-2.5 file:px-4
                    file:rounded-xl file:border-0
                    file:text-sm file:font-semibold
                    file:bg-brand/10 file:text-brand
                    hover:file:bg-brand/20 transition-all border border-gray-200 rounded-xl p-1" required>
                <p class="text-xs text-gray-500 mt-2">Maks. 20MB. Format: JPG, PNG, MP4.</p>
            </div>

            <hr class="border-gray-100">

            <div class="flex gap-4">
                <a href="{{ route('buyer.orders.show', $order->id) }}" class="px-6 py-3 border border-gray-300 rounded-xl text-gray-700 font-bold hover:bg-gray-50 transition-colors">Batal</a>
                <button type="submit" class="px-6 py-3 bg-brand hover:bg-brand-hover text-white font-bold rounded-xl transition-colors shadow flex items-center justify-center gap-2 flex-1">
                    <i data-lucide="send" class="w-4 h-4"></i> Ajukan Komplain
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
