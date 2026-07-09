@extends('seller.layouts.seller')

@section('title', 'Riwayat Penarikan Saldo - Seller Recyclink')
@section('header_title', 'Riwayat Penarikan')

@section('content')
<div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
    <div>
        <a href="{{ route('seller.wallet.index') }}" class="inline-flex items-center text-sm font-semibold text-gray-500 hover:text-brand transition-colors mb-4">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i> Kembali ke Dompet
        </a>
        <h3 class="text-2xl font-bold text-gray-900">Riwayat Penarikan Saldo</h3>
        <p class="text-gray-600 mt-1">Daftar permohonan penarikan dana ke rekening bank Anda.</p>
    </div>
    <div>
        <a href="{{ route('seller.withdrawals.create') }}" class="px-5 py-2.5 bg-brand hover:bg-brand-hover text-white text-sm font-bold rounded-xl shadow-sm transition-colors flex items-center gap-2">
            <i data-lucide="arrow-up-right" class="w-4 h-4"></i> Tarik Saldo Baru
        </a>
    </div>
</div>



<div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
    @if($withdrawals->isEmpty())
        <div class="py-20 text-center flex flex-col items-center justify-center">
            <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center mb-4 border border-gray-100">
                <i data-lucide="arrow-up-right" class="w-8 h-8 text-gray-400"></i>
            </div>
            <h4 class="font-bold text-gray-700">Belum Ada Penarikan</h4>
            <p class="text-sm text-gray-500 mt-1">Permohonan penarikan saldo Anda akan tercatat di sini.</p>
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600 border-collapse">
                <thead class="bg-gray-50/50 border-b border-gray-200 text-gray-900 font-semibold">
                    <tr>
                        <th class="px-6 py-4">Nomor Penarikan</th>
                        <th class="px-6 py-4">Rekening Tujuan</th>
                        <th class="px-6 py-4">Nominal Bersih</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Catatan Admin</th>
                        <th class="px-6 py-4">Tanggal Pengajuan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @php
                        // ponytail: status labels configuration map
                        $statusConfig = [
                            'pending' => ['bg' => 'bg-amber-50 border-amber-200 text-amber-700', 'label' => 'Diproses'],
                            'approved' => ['bg' => 'bg-blue-50 border-blue-200 text-blue-750', 'label' => 'Disetujui'],
                            'paid' => ['bg' => 'bg-emerald-50 border-emerald-200 text-emerald-700', 'label' => 'Selesai / Dibayar'],
                            'rejected' => ['bg' => 'bg-rose-50 border-rose-200 text-rose-700', 'label' => 'Ditolak']
                        ];
                    @endphp
                    @foreach($withdrawals as $w)
                        @php
                            $status = $statusConfig[$w->status] ?? ['bg' => 'bg-gray-50 border-gray-200 text-gray-700', 'label' => strtoupper($w->status)];
                        @endphp
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 font-mono font-bold text-gray-900">#{{ $w->withdrawal_number ?? $w->id }}</td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-800">{{ $w->bank_name }}</div>
                                <div class="text-xs text-gray-500 font-medium mt-0.5">{{ $w->bank_account_number }} a/n {{ $w->bank_account_name }}</div>
                            </td>
                            <td class="px-6 py-4 font-extrabold text-brand">
                                Rp {{ number_format($w->net_amount ?? $w->amount, 0, ',', '.') }}
                                @if($w->admin_fee > 0)
                                    <span class="block text-[10px] text-gray-400 font-semibold mt-0.5">Biaya: Rp {{ number_format($w->admin_fee, 0, ',', '.') }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $status['bg'] }}">
                                    {{ $status['label'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-500 italic max-w-xs truncate" title="{{ $w->admin_note }}">
                                {{ $w->admin_note ?? '-' }}
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-500">
                                {{ $w->created_at->format('d M Y, H:i') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($withdrawals->hasPages())
            <div class="px-6 py-4 border-t border-gray-100 font-semibold">
                {{ $withdrawals->links() }}
            </div>
        @endif
    @endif
</div>
@endsection
