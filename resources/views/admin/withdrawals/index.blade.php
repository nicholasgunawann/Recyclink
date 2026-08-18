@extends('admin.layouts.admin')

@section('title', 'Manajemen Penarikan Saldo - Admin Recyclink')
@section('header_title', 'Penarikan Saldo Penjual')

@section('content')
<div class="mb-8">
    <h3 class="text-2xl font-bold text-gray-900">Manajemen Penarikan Saldo Penjual</h3>
    <p class="text-gray-600 mt-1">Kelola dan verifikasi seluruh pengajuan penarikan dana (payout) dari saldo dompet penjual.</p>
</div>

{{-- Flash Alerts --}}
@if(session('success'))
    <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-2xl flex items-start gap-3 shadow-sm">
        <i data-lucide="check-circle" class="w-5 h-5 text-green-500 mt-0.5 shrink-0"></i>
        <p class="text-sm font-semibold text-green-700">{{ session('success') }}</p>
    </div>
@endif

@if(session('error'))
    <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-2xl flex items-start gap-3 shadow-sm">
        <i data-lucide="alert-circle" class="w-5 h-5 text-red-500 mt-0.5 shrink-0"></i>
        <p class="text-sm font-semibold text-red-700">{{ session('error') }}</p>
    </div>
@endif

{{-- Summary Stats Cards --}}
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
    <!-- Menunggu Persetujuan -->
    <a href="{{ route('admin.withdrawals.index', ['status' => 'pending']) }}" class="bg-white rounded-2xl p-5 border border-amber-200/80 shadow-sm flex items-center justify-between hover:shadow-md hover:border-amber-300 transition-all">
        <div class="space-y-1">
            <p class="text-xs font-bold text-amber-600 uppercase tracking-wider">Menunggu Tinjauan</p>
            <h4 class="text-2xl font-extrabold text-gray-900">{{ number_format($stats['pending'] ?? 0, 0, ',', '.') }}</h4>
            <p class="text-[11px] text-gray-500">Ajuan butuh persetujuan</p>
        </div>
        <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center shrink-0 border border-amber-100">
            <i data-lucide="clock" class="w-6 h-6 text-amber-600"></i>
        </div>
    </a>

    <!-- Siap Ditransfer / Disetujui -->
    <a href="{{ route('admin.withdrawals.index', ['status' => 'approved']) }}" class="bg-white rounded-2xl p-5 border border-blue-200/80 shadow-sm flex items-center justify-between hover:shadow-md hover:border-blue-300 transition-all">
        <div class="space-y-1">
            <p class="text-xs font-bold text-blue-600 uppercase tracking-wider">Siap Ditransfer</p>
            <h4 class="text-2xl font-extrabold text-gray-900">{{ number_format($stats['approved'] ?? 0, 0, ',', '.') }}</h4>
            <p class="text-[11px] text-gray-500">Telah disetujui admin</p>
        </div>
        <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center shrink-0 border border-blue-100">
            <i data-lucide="arrow-up-right" class="w-6 h-6 text-blue-600"></i>
        </div>
    </a>

    <!-- Selesai / Dibayar -->
    <a href="{{ route('admin.withdrawals.index', ['status' => 'paid']) }}" class="bg-white rounded-2xl p-5 border border-emerald-200/80 shadow-sm flex items-center justify-between hover:shadow-md hover:border-emerald-300 transition-all">
        <div class="space-y-1">
            <p class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Selesai Dibayar</p>
            <h4 class="text-2xl font-extrabold text-gray-900">{{ number_format($stats['paid'] ?? 0, 0, ',', '.') }}</h4>
            <p class="text-[11px] text-gray-500">Rp {{ number_format($stats['total_paid_amount'] ?? 0, 0, ',', '.') }}</p>
        </div>
        <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center shrink-0 border border-emerald-100">
            <i data-lucide="check-circle-2" class="w-6 h-6 text-emerald-600"></i>
        </div>
    </a>

    <!-- Ditolak -->
    <a href="{{ route('admin.withdrawals.index', ['status' => 'rejected']) }}" class="bg-white rounded-2xl p-5 border border-rose-200/80 shadow-sm flex items-center justify-between hover:shadow-md hover:border-rose-300 transition-all">
        <div class="space-y-1">
            <p class="text-xs font-bold text-rose-600 uppercase tracking-wider">Ditolak / Refund</p>
            <h4 class="text-2xl font-extrabold text-gray-900">{{ number_format($stats['rejected'] ?? 0, 0, ',', '.') }}</h4>
            <p class="text-[11px] text-gray-500">Saldo direfund ke seller</p>
        </div>
        <div class="w-12 h-12 rounded-xl bg-rose-50 flex items-center justify-center shrink-0 border border-rose-100">
            <i data-lucide="x-circle" class="w-6 h-6 text-rose-600"></i>
        </div>
    </a>
</div>

{{-- Filters & Search Section --}}
<div class="bg-white border border-gray-200 rounded-2xl p-5 mb-6 shadow-sm">
    <form method="GET" action="{{ route('admin.withdrawals.index') }}" class="flex flex-col md:flex-row items-center justify-between gap-4">
        {{-- Status Filter Tabs --}}
        <div class="flex items-center gap-1.5 overflow-x-auto w-full md:w-auto pb-2 md:pb-0 scrollbar-none">
            @php
                $currentStatus = request('status', '');
            @endphp
            <a href="{{ route('admin.withdrawals.index', array_merge(request()->except(['status', 'page']))) }}"
               class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition-all {{ $currentStatus === '' ? 'bg-brand text-white shadow-sm' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
                Semua ({{ $stats['total'] ?? 0 }})
            </a>
            <a href="{{ route('admin.withdrawals.index', array_merge(request()->except(['page']), ['status' => 'pending'])) }}"
               class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition-all {{ $currentStatus === 'pending' ? 'bg-amber-500 text-white shadow-sm' : 'bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200' }}">
                Menunggu ({{ $stats['pending'] ?? 0 }})
            </a>
            <a href="{{ route('admin.withdrawals.index', array_merge(request()->except(['page']), ['status' => 'approved'])) }}"
               class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition-all {{ $currentStatus === 'approved' ? 'bg-blue-600 text-white shadow-sm' : 'bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200' }}">
                Siap Ditransfer ({{ $stats['approved'] ?? 0 }})
            </a>
            <a href="{{ route('admin.withdrawals.index', array_merge(request()->except(['page']), ['status' => 'paid'])) }}"
               class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition-all {{ $currentStatus === 'paid' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200' }}">
                Selesai ({{ $stats['paid'] ?? 0 }})
            </a>
            <a href="{{ route('admin.withdrawals.index', array_merge(request()->except(['page']), ['status' => 'rejected'])) }}"
               class="px-3.5 py-2 rounded-xl text-xs font-bold whitespace-nowrap transition-all {{ $currentStatus === 'rejected' ? 'bg-rose-600 text-white shadow-sm' : 'bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200' }}">
                Ditolak ({{ $stats['rejected'] ?? 0 }})
            </a>
        </div>

        {{-- Search Input --}}
        <div class="w-full md:w-80 flex items-center gap-2">
            <div class="relative w-full">
                <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute left-3.5 top-1/2 -translate-y-1/2"></i>
                <input type="text"
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="Cari no. WD, penjual, bank..."
                       class="w-full pl-9 pr-4 py-2 text-xs bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand/20 focus:border-brand transition-all">
            </div>
            @if(request('search') || request('status'))
                <a href="{{ route('admin.withdrawals.index') }}" class="p-2 text-gray-400 hover:text-gray-600 rounded-xl hover:bg-gray-100 transition-colors" title="Reset Filter">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </a>
            @endif
        </div>
    </form>
</div>

{{-- Data Table Section --}}
<div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
    @if($withdrawals->isEmpty())
        <div class="py-20 text-center flex flex-col items-center justify-center">
            <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center mb-4 border border-gray-100">
                <i data-lucide="wallet" class="w-8 h-8 text-gray-400"></i>
            </div>
            <h4 class="font-bold text-gray-700">Tidak Ada Data Penarikan</h4>
            <p class="text-sm text-gray-500 mt-1">
                @if(request('search') || request('status'))
                    Tidak ditemukan pengajuan penarikan yang cocok dengan kriteria filter saat ini.
                @else
                    Belum ada pengajuan penarikan dana dari penjual.
                @endif
            </p>
            @if(request('search') || request('status'))
                <a href="{{ route('admin.withdrawals.index') }}" class="mt-4 px-4 py-2 bg-brand/10 text-brand font-bold text-xs rounded-xl hover:bg-brand/20 transition-colors">
                    Reset Filter
                </a>
            @endif
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600 border-collapse">
                <thead class="bg-gray-50/50 border-b border-gray-200 text-gray-900 font-semibold">
                    <tr>
                        <th class="px-6 py-4">No. Pengajuan</th>
                        <th class="px-6 py-4">Penjual</th>
                        <th class="px-6 py-4">Rekening Tujuan</th>
                        <th class="px-6 py-4">Nominal Transfer</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Tanggal</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @php
                        // ponytail: status configuration map
                        $statusConfig = [
                            'pending' => ['bg' => 'bg-amber-50 border-amber-200 text-amber-700', 'label' => 'Menunggu Tinjauan'],
                            'approved' => ['bg' => 'bg-blue-50 border-blue-200 text-blue-700', 'label' => 'Siap Ditransfer'],
                            'paid' => ['bg' => 'bg-emerald-50 border-emerald-200 text-emerald-700', 'label' => 'Selesai / Dibayar'],
                            'rejected' => ['bg' => 'bg-rose-50 border-rose-200 text-rose-700', 'label' => 'Ditolak']
                        ];
                    @endphp
                    @foreach($withdrawals as $w)
                        @php
                            $status = $statusConfig[$w->status] ?? ['bg' => 'bg-gray-50 border-gray-200 text-gray-700', 'label' => strtoupper($w->status)];
                            $netAmount = (float)($w->net_amount ?? ($w->amount - ($w->admin_fee ?? 0)));
                        @endphp
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <span class="font-mono font-bold text-gray-900 block text-xs">#{{ $w->withdrawal_number ?? 'WD-' . $w->id }}</span>
                                <span class="text-[11px] text-gray-400">ID: {{ $w->id }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-900 text-xs">{{ $w->user->name ?? 'Penjual' }}</div>
                                <div class="text-[11px] text-gray-500 truncate max-w-[160px]">{{ $w->user->email ?? '-' }}</div>
                                @if($w->user && $w->user->sellerProfile && $w->user->sellerProfile->business_name)
                                    <span class="inline-block mt-0.5 text-[10px] font-semibold text-brand bg-brand/10 px-1.5 py-0.5 rounded">
                                        {{ $w->user->sellerProfile->business_name }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-gray-800 text-xs uppercase">{{ $w->bank_name }}</div>
                                <div class="text-xs font-mono text-gray-600 font-medium">{{ $w->bank_account_number }}</div>
                                <div class="text-[11px] text-gray-400">a/n {{ $w->bank_account_name }}</div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-extrabold text-brand text-sm">
                                    Rp {{ number_format($netAmount, 0, ',', '.') }}
                                </div>
                                <div class="text-[10px] text-gray-400 font-medium">
                                    Pengajuan: Rp {{ number_format((float)$w->amount, 0, ',', '.') }}
                                    @if((float)$w->admin_fee > 0)
                                        (Fee: Rp {{ number_format((float)$w->admin_fee, 0, ',', '.') }})
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border {{ $status['bg'] }}">
                                    {{ $status['label'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs text-gray-500">
                                <div>{{ $w->created_at->format('d M Y') }}</div>
                                <div class="text-[11px] text-gray-400">{{ $w->created_at->format('H:i') }} WIB</div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('admin.withdrawals.show', $w->id) }}"
                                   class="inline-flex items-center justify-center gap-1 px-3.5 py-1.5 border border-gray-200 hover:border-brand hover:text-brand font-bold text-xs rounded-xl bg-white transition-all shadow-xs">
                                    <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($withdrawals->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $withdrawals->links() }}
            </div>
        @endif
    @endif
</div>
@endsection
