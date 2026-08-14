@extends('admin.layouts.admin')

@section('title', 'Manajemen Transaksi - Admin Recyclink')
@section('header_title', 'Manajemen Transaksi')

@section('content')
<div class="mb-8">
    <h3 class="text-2xl font-bold text-gray-900">Daftar Transaksi</h3>
    <p class="text-gray-600 mt-1">Pantau seluruh riwayat pemesanan dan transaksi pembayaran di platform Recyclink.</p>
</div>

@if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-2xl flex items-start gap-3">
        <i data-lucide="check-circle" class="w-5 h-5 text-green-500 mt-0.5 shrink-0"></i>
        <p class="text-sm font-semibold text-green-700">{{ session('success') }}</p>
    </div>
@endif

@if(session('error'))
    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-2xl flex items-start gap-3">
        <i data-lucide="alert-circle" class="w-5 h-5 text-red-500 mt-0.5 shrink-0"></i>
        <p class="text-sm font-semibold text-red-700">{{ session('error') }}</p>
    </div>
@endif

<div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
    @if($orders->isEmpty())
        <div class="py-20 text-center flex flex-col items-center justify-center">
            <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center mb-4 border border-gray-100">
                <i data-lucide="line-chart" class="w-8 h-8 text-gray-400"></i>
            </div>
            <h4 class="font-bold text-gray-700">Belum Ada Transaksi</h4>
            <p class="text-sm text-gray-500 mt-1">Belum ada aktivitas transaksi pesanan dari pengguna.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600 border-collapse">
                <thead class="bg-gray-50/50 border-b border-gray-200 text-gray-900 font-semibold">
                    <tr>
                        <th class="px-6 py-4">Kode Transaksi</th>
                        <th class="px-6 py-4">Pembeli</th>
                        <th class="px-6 py-4">Penjual</th>
                        <th class="px-6 py-4">Total</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Tanggal</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @php
                        // ponytail: status configuration map
                        $statusConfig = [
                            'pending' => ['bg' => 'bg-amber-50 border-amber-200 text-amber-700', 'label' => 'Menunggu Konfirmasi'],
                            'waiting_payment' => ['bg' => 'bg-blue-50 border-blue-200 text-blue-700', 'label' => 'Menunggu Pembayaran'],
                            'paid' => ['bg' => 'bg-emerald-50 border-emerald-200 text-emerald-700', 'label' => 'Sudah Dibayar'],
                            'processing' => ['bg' => 'bg-indigo-50 border-indigo-200 text-indigo-700', 'label' => 'Sedang Diproses'],
                            'completed' => ['bg' => 'bg-gray-100 border-gray-200 text-gray-700', 'label' => 'Selesai'],
                            'rejected' => ['bg' => 'bg-rose-50 border-rose-200 text-rose-700', 'label' => 'Ditolak'],
                            'cancelled' => ['bg' => 'bg-red-50 border-red-200 text-red-700', 'label' => 'Dibatalkan']
                        ];
                    @endphp
                    @foreach($orders as $order)
                        @php
                            $status = $statusConfig[$order->order_status] ?? ['bg' => 'bg-gray-50 border-gray-200 text-gray-700', 'label' => strtoupper($order->order_status)];
                        @endphp
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 font-mono font-bold text-gray-900">{{ $order->order_code }}</td>
                            <td class="px-6 py-4 font-semibold text-gray-800">{{ $order->buyer->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 font-semibold text-gray-800">{{ $order->seller->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 font-bold text-brand">Rp {{ number_format((float)($order->total_amount ?? 0), 0, ',', '.') }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $status['bg'] }}">
                                    {{ $status['label'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-500">
                                {{ $order->created_at->format('d M Y, H:i') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('admin.transactions.show', $order->id) }}" class="inline-flex items-center justify-center px-3 py-1.5 border border-gray-200 hover:border-brand hover:text-brand font-bold text-xs rounded-xl bg-white transition-all">
                                        Detail
                                    </a>
                                    <form action="{{ route('admin.transactions.destroy', $order->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus transaksi {{ $order->order_code }} ini?');" class="inline" data-turbo="false">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center justify-center px-3 py-1.5 border border-red-200 text-red-600 hover:bg-red-50 hover:border-red-300 font-bold text-xs rounded-xl bg-white transition-all cursor-pointer" title="Hapus Transaksi">
                                            <i data-lucide="trash-2" class="w-3.5 h-3.5 mr-1"></i> Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($orders->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $orders->links() }}
            </div>
        @endif
    @endif
</div>
@endsection
