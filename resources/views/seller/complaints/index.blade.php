@extends('seller.layouts.seller')
@section('title', 'Daftar Komplain (Pusat Resolusi)')
@section('header_title', 'Pusat Resolusi')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-900">Pusat Resolusi (Seller)</h2>
    </div>

    @if($complaints->count() > 0)
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100 text-gray-500 text-xs uppercase tracking-wider">
                            <th class="px-6 py-4 font-bold">No. Komplain</th>
                            <th class="px-6 py-4 font-bold">Pesanan</th>
                            <th class="px-6 py-4 font-bold">Pembeli</th>
                            <th class="px-6 py-4 font-bold">Status</th>
                            <th class="px-6 py-4 font-bold">Tanggal</th>
                            <th class="px-6 py-4 font-bold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($complaints as $complaint)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="font-semibold text-gray-900">{{ $complaint->complaint_number }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('seller.orders.show', $complaint->order_id) }}" class="text-brand hover:underline font-semibold text-sm">
                                    {{ $complaint->order->order_code }}
                                </a>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                {{ $complaint->complainant->name }}
                            </td>
                            <td class="px-6 py-4">
                                @if($complaint->isOpen())
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-800">Menunggu</span>
                                @elseif($complaint->status === 'under_review')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-blue-100 text-blue-800">Ditinjau Admin</span>
                                @elseif($complaint->isResolved())
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">Selesai</span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold bg-rose-100 text-rose-800">Ditolak</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $complaint->created_at->format('d M Y, H:i') }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('seller.complaints.show', $complaint->id) }}" class="inline-flex items-center justify-center bg-gray-100 hover:bg-gray-200 text-gray-700 w-9 h-9 rounded-xl transition-colors">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4 border-t border-gray-100">
                {{ $complaints->links() }}
            </div>
        </div>
    @else
        <div class="text-center py-20 bg-white rounded-2xl shadow-sm border border-gray-100">
            <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-lucide="inbox" class="w-10 h-10 text-gray-400"></i>
            </div>
            <h3 class="text-lg font-bold text-gray-900 mb-1">Belum Ada Komplain</h3>
            <p class="text-gray-500">Belum ada komplain atau resolusi untuk pesanan Anda.</p>
        </div>
    @endif
</div>
@endsection
