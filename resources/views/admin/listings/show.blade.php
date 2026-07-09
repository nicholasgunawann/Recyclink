@extends('admin.layouts.admin')

@section('title', 'Detail Verifikasi Listing - Recyclink')
@section('header_title', 'Cek Konten Limbah')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="mb-6 flex items-center gap-4">
        <a href="{{ route('admin.listings.verification.index') }}" class="p-2 bg-white rounded-xl shadow-sm border border-gray-100 text-gray-500 hover:text-brand transition-colors">
            <i data-lucide="arrow-left" class="w-5 h-5"></i>
        </a>
        <div>
            <h3 class="text-2xl font-bold text-gray-900">Detail Konten Limbah</h3>
            <p class="text-gray-600 mt-1">Pastikan foto, deskripsi, harga, lokasi, dan kontak valid sebelum disetujui.</p>
        </div>
    </div>

    

    

    <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden mb-6">
        <div class="p-6 md:p-8 space-y-8">
            
            {{-- Foto --}}
            <div>
                <h4 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <i data-lucide="image" class="w-5 h-5 text-gray-400"></i> 
                    1. Foto Limbah
                    <span class="text-xs font-normal text-gray-500 ml-2 bg-gray-100 px-2 py-1 rounded-md">Cek: Apakah sesuai dengan limbah?</span>
                </h4>
                @if($listing->images && $listing->images->count() > 0)
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                        @foreach($listing->images as $img)
                            <div class="aspect-square rounded-xl overflow-hidden border border-gray-200 shadow-sm relative group">
                                <img src="{{ $img->url }}" alt="Foto Limbah" class="w-full h-full object-cover">
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="p-6 bg-red-50 text-red-700 border border-red-200 rounded-xl flex items-center gap-3 font-semibold">
                        <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                        Tidak ada foto yang diunggah.
                    </div>
                @endif
            </div>

            <hr class="border-gray-100">

            {{-- Info Utama & Harga --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <h4 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <i data-lucide="align-left" class="w-5 h-5 text-gray-400"></i> 
                        2. Info & Deskripsi
                        <span class="text-xs font-normal text-gray-500 ml-2 bg-gray-100 px-2 py-1 rounded-md">Cek: Tidak menyesatkan?</span>
                    </h4>
                    <div class="space-y-4">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 mb-1">Judul Listing</p>
                            <p class="font-bold text-gray-900 text-lg">{{ $listing->title }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 mb-1">Kategori</p>
                            <p class="font-medium text-gray-800">{{ $listing->category->category_name ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 mb-1">Deskripsi</p>
                            <p class="text-gray-700 whitespace-pre-wrap bg-gray-50 p-4 rounded-xl text-sm border border-gray-100">{{ $listing->description ?: 'Tidak ada deskripsi.' }}</p>
                        </div>
                    </div>
                </div>

                <div>
                    <h4 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <i data-lucide="tag" class="w-5 h-5 text-gray-400"></i> 
                        3. Harga & Kuantitas
                        <span class="text-xs font-normal text-gray-500 ml-2 bg-gray-100 px-2 py-1 rounded-md">Cek: Masuk akal?</span>
                    </h4>
                    <div class="space-y-4">
                        <div class="p-4 bg-brand/5 border border-brand/20 rounded-xl">
                            <p class="text-xs font-bold text-brand mb-1">Harga per Satuan</p>
                            <p class="text-2xl font-black text-gray-900">Rp {{ number_format($listing->price_per_unit, 0, ',', '.') }} <span class="text-lg text-gray-500 font-medium">/ {{ $listing->unit }}</span></p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 mb-1">Total Volume / Stok</p>
                            <p class="font-bold text-gray-900 text-lg">{{ $listing->quantity }} {{ $listing->unit }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="border-gray-100">

            {{-- Lokasi & Kontak --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <h4 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <i data-lucide="map-pin" class="w-5 h-5 text-gray-400"></i> 
                        4. Lokasi
                        <span class="text-xs font-normal text-gray-500 ml-2 bg-gray-100 px-2 py-1 rounded-md">Cek: Jelas?</span>
                    </h4>
                    <div class="space-y-4">
                        <div>
                            <p class="text-xs font-semibold text-gray-500 mb-1">Kota/Kabupaten</p>
                            <p class="font-bold text-gray-900">{{ $listing->city }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-semibold text-gray-500 mb-1">Alamat Lengkap</p>
                            <p class="text-gray-700 bg-gray-50 p-4 rounded-xl text-sm border border-gray-100">{{ $listing->address }}</p>
                        </div>
                    </div>
                </div>

                <div>
                    <h4 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                        <i data-lucide="user" class="w-5 h-5 text-gray-400"></i> 
                        5. Kontak Penjual
                        <span class="text-xs font-normal text-gray-500 ml-2 bg-gray-100 px-2 py-1 rounded-md">Cek: Valid?</span>
                    </h4>
                    <div class="space-y-4">
                        <div class="flex items-center gap-3 p-4 border border-gray-200 rounded-xl">
                            <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center shrink-0">
                                <i data-lucide="store" class="w-5 h-5 text-gray-500"></i>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500">Nama Usaha</p>
                                <p class="font-bold text-gray-900">{{ $listing->seller->sellerProfile->business_name ?? $listing->seller->name ?? 'Tidak ada data' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3 p-4 border border-gray-200 rounded-xl">
                            <div class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center shrink-0">
                                <i data-lucide="phone" class="w-5 h-5 text-emerald-600"></i>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-gray-500">No. Telepon / WhatsApp</p>
                                <p class="font-bold text-gray-900">{{ $listing->seller->phone_number ?? 'Tidak disertakan' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Aksi --}}
        <div class="p-6 md:p-8 bg-gray-50 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4">
            
            <div class="flex items-center gap-2 w-full sm:w-auto">
                <span class="text-sm font-semibold text-gray-600">Status saat ini:</span>
                @if($listing->verification_status === 'pending')
                    <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-100 text-amber-800">Menunggu</span>
                @elseif($listing->verification_status === 'approved')
                    <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-100 text-emerald-800">Disetujui</span>
                @elseif($listing->verification_status === 'rejected')
                    <span class="px-2.5 py-1 rounded-lg text-xs font-bold bg-red-100 text-red-800">Ditolak</span>
                @endif
            </div>

            <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
                @if($listing->verification_status === 'pending')
                    <button type="button" onclick="document.getElementById('reject-modal').classList.remove('hidden')" class="w-full sm:w-auto px-6 py-2.5 bg-white border border-red-200 text-red-600 hover:bg-red-50 hover:border-red-300 font-bold rounded-xl transition-colors flex items-center justify-center gap-2">
                        <i data-lucide="x" class="w-4 h-4"></i> Tolak Listing
                    </button>
                    
                    <button type="button" onclick="document.getElementById('approve-modal').classList.remove('hidden')" class="w-full sm:w-auto px-6 py-2.5 bg-emerald-500 text-white hover:bg-emerald-600 font-bold rounded-xl shadow-sm transition-colors flex items-center justify-center gap-2">
                        <i data-lucide="check" class="w-4 h-4"></i> Setujui & Tayangkan
                    </button>
                @else
                    <form action="{{ route('admin.listings.verification.deactivate', $listing) }}" method="POST" data-confirm="Nonaktifkan listing ini?">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-gray-200 text-gray-700 hover:bg-gray-300 font-bold rounded-xl transition-colors">
                            Nonaktifkan
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Approve Modal --}}
<div id="approve-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm" onclick="document.getElementById('approve-modal').classList.add('hidden')"></div>
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
        <form action="{{ route('admin.listings.verification.approve', $listing) }}" method="POST">
            @csrf
            @method('PATCH')
            <div class="p-6">
                <div class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mb-4 mx-auto">
                    <i data-lucide="check-circle-2" class="w-6 h-6"></i>
                </div>
                <h4 class="text-xl font-bold text-gray-900 mb-2 text-center">Setujui Listing</h4>
                <p class="text-sm text-gray-600 text-center">Apakah Anda yakin konten ini sudah sesuai? Listing akan langsung tayang di Marketplace dan dapat dilihat oleh pembeli.</p>
            </div>
            <div class="p-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('approve-modal').classList.add('hidden')" class="px-4 py-2 bg-white border border-gray-200 font-bold text-gray-700 rounded-xl hover:bg-gray-50">Batal</button>
                <button type="submit" class="px-4 py-2 bg-emerald-600 text-white font-bold rounded-xl shadow-sm hover:bg-emerald-700">Ya, Tayangkan</button>
            </div>
        </form>
    </div>
</div>

{{-- Reject Modal --}}
<div id="reject-modal" class="fixed inset-0 z-50 hidden">
    <div class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm" onclick="document.getElementById('reject-modal').classList.add('hidden')"></div>
    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
        <form action="{{ route('admin.listings.verification.reject', $listing) }}" method="POST">
            @csrf
            @method('PATCH')
            <div class="p-6">
                <h4 class="text-xl font-bold text-gray-900 mb-2 flex items-center gap-2">
                    <i data-lucide="alert-triangle" class="w-5 h-5 text-red-500"></i> Tolak Listing
                </h4>
                <p class="text-sm text-gray-600 mb-4">Berikan alasan penolakan agar penjual dapat memperbaiki listingnya.</p>
                
                <textarea name="reason" rows="4" class="w-full bg-gray-50 border border-gray-200 text-gray-900 text-sm rounded-xl focus:ring-red-500 focus:border-red-500 block p-3" placeholder="Contoh: Foto tidak sesuai, deskripsi menyesatkan..." required></textarea>
            </div>
            <div class="p-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3">
                <button type="button" onclick="document.getElementById('reject-modal').classList.add('hidden')" class="px-4 py-2 bg-white border border-gray-200 font-bold text-gray-700 rounded-xl hover:bg-gray-50">Batal</button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white font-bold rounded-xl shadow-sm hover:bg-red-700">Kirim Penolakan</button>
            </div>
        </form>
    </div>
</div>

@endsection
