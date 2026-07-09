@extends('seller.layouts.seller')

@section('title', 'Daftar Listing Limbah - Recyclink')
@section('header_title', 'Listing Limbah')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h3 class="text-2xl font-bold text-gray-900">Limbah Saya</h3>
        <p class="text-gray-600 mt-1">Kelola listing produk limbah Anda yang akan dijual.</p>
    </div>
    <a href="{{ route('seller.listings.create') }}" class="inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-brand text-white font-bold rounded-xl hover:bg-brand-hover transition-colors shadow-sm">
        <i data-lucide="plus" class="w-5 h-5"></i>
        Tambah Listing
    </a>
</div>

@if($listings->isEmpty())
    <div class="bg-white border-2 border-dashed border-gray-200 rounded-2xl p-12 text-center flex flex-col items-center">
        <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-5">
            <i data-lucide="package-open" class="w-10 h-10 text-gray-400"></i>
        </div>
        <h4 class="text-xl font-bold text-gray-900">Belum Ada Listing</h4>
        <p class="text-gray-500 mt-2 max-w-md">Anda belum menambahkan limbah apapun untuk dijual. Mulai jual limbah Anda sekarang dengan menambah listing baru.</p>
        <a href="{{ route('seller.listings.create') }}" class="mt-6 inline-flex items-center gap-2 px-6 py-3 bg-white border border-brand text-brand hover:bg-brand hover:text-white transition-colors font-bold rounded-xl">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Tambah Limbah Pertama
        </a>
    </div>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        @foreach($listings as $listing)
            <div class="bg-white border border-gray-100 rounded-2xl shadow-sm overflow-hidden flex flex-col group hover:shadow-md transition-shadow relative">
                
                {{-- Status Badge (Tayang / Menunggu Verifikasi) --}}
                <div class="absolute top-3 left-3 z-10 flex gap-2">
                    @if($listing->verification_status === 'pending')
                        <span class="bg-amber-100 text-amber-800 text-xs font-bold px-2.5 py-1 rounded-lg backdrop-blur-md bg-opacity-90 flex items-center gap-1.5 shadow-sm">
                            <i data-lucide="clock" class="w-3 h-3"></i> Menunggu Verifikasi
                        </span>
                    @elseif($listing->verification_status === 'approved')
                        <span class="bg-emerald-100 text-emerald-800 text-xs font-bold px-2.5 py-1 rounded-lg backdrop-blur-md bg-opacity-90 flex items-center gap-1.5 shadow-sm">
                            <i data-lucide="check-circle-2" class="w-3 h-3"></i> Tayang
                        </span>
                    @elseif($listing->verification_status === 'rejected')
                        <span class="bg-red-100 text-red-800 text-xs font-bold px-2.5 py-1 rounded-lg backdrop-blur-md bg-opacity-90 flex items-center gap-1.5 shadow-sm">
                            <i data-lucide="x-circle" class="w-3 h-3"></i> Ditolak
                        </span>
                    @endif
                </div>

                <div class="absolute top-3 right-3 z-10">
                    <span class="bg-white/90 backdrop-blur-md text-gray-800 text-xs font-bold px-2.5 py-1 rounded-lg shadow-sm border border-gray-100">
                        {{ $listing->category->category_name ?? 'Limbah' }}
                    </span>
                </div>

                <div class="h-48 bg-gray-100 relative overflow-hidden">
                    @if($listing->images && $listing->images->count() > 0)
                        <img src="{{ str_starts_with($listing->images->first()->image_url, 'http') ? $listing->images->first()->image_url : asset('storage/' . $listing->images->first()->image_url) }}" alt="{{ $listing->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    @else
                        <div class="w-full h-full flex flex-col justify-center items-center text-gray-400 gap-2">
                            <i data-lucide="image" class="w-10 h-10"></i>
                            <span class="text-sm font-medium">Tanpa Foto</span>
                        </div>
                    @endif
                </div>

                <div class="p-5 flex flex-col flex-1">
                    <h4 class="font-bold text-gray-900 text-lg mb-1 line-clamp-1" title="{{ $listing->title }}">{{ $listing->title }}</h4>
                    
                    <div class="text-brand font-extrabold text-lg mb-3">
                        Rp {{ number_format($listing->price_per_unit, 0, ',', '.') }} <span class="text-sm text-gray-500 font-medium">/ {{ $listing->unit }}</span>
                    </div>

                    <div class="space-y-2 mb-5">
                        <div class="flex items-center gap-2 text-sm text-gray-600">
                            <i data-lucide="box" class="w-4 h-4 text-gray-400"></i>
                            <span class="font-medium">Stok:</span> {{ $listing->quantity }} {{ $listing->unit }}
                        </div>
                        <div class="flex items-start gap-2 text-sm text-gray-600">
                            <i data-lucide="map-pin" class="w-4 h-4 text-gray-400 shrink-0 mt-0.5"></i>
                            <span class="line-clamp-2 leading-tight">{{ $listing->city }}</span>
                        </div>
                    </div>

                    <div class="mt-auto pt-4 border-t border-gray-100 flex gap-2">
                        <a href="{{ route('seller.listings.edit', $listing) }}" class="flex-1 py-2 text-center bg-gray-50 text-gray-700 font-semibold text-sm rounded-xl hover:bg-gray-100 transition-colors">
                            Edit
                        </a>
                        <form action="{{ route('seller.listings.destroy', $listing) }}" method="POST" class="flex-none" data-confirm="Apakah Anda yakin ingin menghapus listing ini?">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-2 bg-red-50 text-red-600 rounded-xl hover:bg-red-100 transition-colors" title="Hapus">
                                <i data-lucide="trash-2" class="w-5 h-5"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-8">
        {{ $listings->links() }}
    </div>
@endif

@endsection
