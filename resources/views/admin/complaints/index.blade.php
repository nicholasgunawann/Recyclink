@extends('admin.layouts.admin')

@section('title', 'Manajemen Keluhan / Dispute - Admin Recyclink')
@section('header_title', 'Manajemen Keluhan')

@section('content')
<div class="mb-8">
    <h3 class="text-2xl font-bold text-gray-900">Daftar Keluhan & Dispute</h3>
    <p class="text-gray-600 mt-1">Kelola dan mediasi keluhan atau sengketa transaksi antar pengguna.</p>
</div>




<div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
    @if($complaints->isEmpty())
        <div class="py-20 text-center flex flex-col items-center justify-center">
            <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center mb-4 border border-gray-100">
                <i data-lucide="shield-check" class="w-8 h-8 text-gray-400"></i>
            </div>
            <h4 class="font-bold text-gray-700">Tidak Ada Keluhan Aktif</h4>
            <p class="text-sm text-gray-500 mt-1">Saat ini tidak ada laporan sengketa transaksi yang perlu dimediasi.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600 border-collapse">
                <thead class="bg-gray-50/50 border-b border-gray-200 text-gray-900 font-semibold">
                    <tr>
                        <th class="px-6 py-4">Nomor Keluhan</th>
                        <th class="px-6 py-4">Pesanan</th>
                        <th class="px-6 py-4">Pelapor</th>
                        <th class="px-6 py-4">Terlapor</th>
                        <th class="px-6 py-4">Subjek & Tipe</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @php
                        // ponytail: status badge configuration map
                        $statusConfig = [
                            'open' => ['bg' => 'bg-amber-50 text-amber-700 border-amber-200', 'label' => 'Terbuka'],
                            'under_review' => ['bg' => 'bg-indigo-50 text-indigo-700 border-indigo-200', 'label' => 'Ditinjau'],
                            'resolved' => ['bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'label' => 'Selesai'],
                            'rejected' => ['bg' => 'bg-rose-50 text-rose-700 border-rose-200', 'label' => 'Ditolak']
                        ];
                    @endphp
                    @foreach($complaints as $c)
                        @php
                            $status = $statusConfig[$c->status] ?? ['bg' => 'bg-gray-50 text-gray-700 border-gray-100', 'label' => strtoupper($c->status)];
                        @endphp
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 font-mono font-bold text-gray-900">#{{ $c->complaint_number }}</td>
                            <td class="px-6 py-4 font-mono text-xs font-semibold text-gray-500">
                                @if($c->order)
                                    {{ $c->order->order_code }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="px-6 py-4 font-medium text-gray-800">{{ $c->complainant->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 font-medium text-gray-800">{{ $c->respondent->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4">
                                <div class="font-semibold text-gray-800">{{ $c->subject }}</div>
                                <div class="text-xs text-gray-400 mt-0.5">{{ strtoupper($c->complaint_type) }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $status['bg'] }}">
                                    {{ $status['label'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('admin.complaints.show', $c->id) }}" class="inline-flex items-center justify-center px-3 py-1.5 border border-gray-200 hover:border-brand hover:text-brand font-bold text-xs rounded-xl bg-white transition-all">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($complaints->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $complaints->links() }}
            </div>
        @endif
    @endif
</div>
@endsection
