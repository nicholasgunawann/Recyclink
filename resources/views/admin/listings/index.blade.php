@extends('admin.layouts.admin')

@section('title', 'Verifikasi Listing Limbah - Recyclink')
@section('header_title', 'Verifikasi Limbah')

@section('content')
<div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
        <h3 class="text-2xl font-bold text-gray-900">Antrean Verifikasi Limbah</h3>
        <p class="text-gray-600 mt-1">Kelola dan moderasi listing limbah dari para penjual sebelum tayang.</p>
    </div>
</div>





<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm text-gray-600">
            <thead class="bg-gray-50 border-b border-gray-100 text-gray-700 font-bold">
                <tr>
                    <th class="px-6 py-4">Limbah</th>
                    <th class="px-6 py-4">Penjual</th>
                    <th class="px-6 py-4">Kategori</th>
                    <th class="px-6 py-4">Harga / Vol</th>
                    <th class="px-6 py-4">Status Verifikasi</th>
                    <th class="px-6 py-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($listings as $listing)
                <tr class="hover:bg-gray-50/50 transition-colors">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-gray-100 overflow-hidden shrink-0">
                                @if($listing->primaryImage)
                                    <img src="{{ $listing->primaryImage->url }}" alt="Foto" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-gray-400">
                                        <i data-lucide="image" class="w-5 h-5"></i>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <p class="font-bold text-gray-900 line-clamp-1" title="{{ $listing->title }}">{{ $listing->title }}</p>
                                <p class="text-xs text-gray-500">{{ $listing->created_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <p class="font-semibold text-gray-900">{{ $listing->seller->sellerProfile->business_name ?? $listing->seller->name ?? '-' }}</p>
                        <p class="text-xs text-gray-500">{{ $listing->seller->name ?? '-' }} &bull; {{ $listing->city }}</p>
                    </td>
                    <td class="px-6 py-4 font-medium text-gray-700">
                        {{ $listing->category->category_name ?? '-' }}
                    </td>
                    <td class="px-6 py-4">
                        <p class="font-bold text-brand">Rp {{ number_format($listing->price_per_unit, 0, ',', '.') }}</p>
                        <p class="text-xs text-gray-500">Stok: {{ $listing->quantity }} {{ $listing->unit }}</p>
                    </td>
                    <td class="px-6 py-4">
                        @if($listing->verification_status === 'pending')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-amber-100 text-amber-800">
                                <i data-lucide="clock" class="w-3.5 h-3.5"></i> Menunggu
                            </span>
                        @elseif($listing->verification_status === 'approved')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-100 text-emerald-800">
                                <i data-lucide="check-circle-2" class="w-3.5 h-3.5"></i> Disetujui
                            </span>
                        @elseif($listing->verification_status === 'rejected')
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-red-100 text-red-800">
                                <i data-lucide="x-circle" class="w-3.5 h-3.5"></i> Ditolak
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('admin.listings.verification.show', $listing) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-200 text-gray-700 hover:text-brand hover:border-brand hover:bg-brand/5 rounded-lg text-sm font-bold transition-colors shadow-sm">
                            <i data-lucide="eye" class="w-4 h-4"></i> Cek Konten
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 mb-4 text-gray-400">
                            <i data-lucide="check-square" class="w-8 h-8"></i>
                        </div>
                        <h4 class="text-lg font-bold text-gray-900">Semua Bersih!</h4>
                        <p class="text-gray-500 mt-1">Tidak ada listing limbah yang perlu diverifikasi saat ini.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($listings->hasPages())
    <div class="p-6 border-t border-gray-100">
        {{ $listings->links() }}
    </div>
    @endif
</div>
@endsection
