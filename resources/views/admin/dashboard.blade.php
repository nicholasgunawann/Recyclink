@extends('admin.layouts.admin')

@section('title', 'Dashboard Admin - Recyclink')
@section('header_title', 'Dashboard Admin')

@section('content')
    <div class="mb-8">
        <h3 class="text-3xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-brand to-emerald-600 tracking-tight">Selamat datang kembali!</h3>
        <p class="text-gray-600 mt-2 text-lg">Berikut adalah ringkasan aktivitas Recyclink secara keseluruhan.</p>
    </div>

    <!-- Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Pengguna -->
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-center gap-5 hover:border-blue-200 hover:shadow-md transition-all duration-300">
            <div class="w-14 h-14 rounded-2xl bg-blue-50 flex items-center justify-center shrink-0">
                <i data-lucide="users" class="w-7 h-7 text-blue-600"></i>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-500 mb-1">Total Pengguna</p>
                <h4 class="text-3xl font-extrabold text-gray-900">{{ number_format($stats['total_users'] ?? 0, 0, ',', '.') }}</h4>
            </div>
        </div>
        
        <!-- Total Limbah -->
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-center gap-5 hover:border-emerald-200 hover:shadow-md transition-all duration-300">
            <div class="w-14 h-14 rounded-2xl bg-emerald-50 flex items-center justify-center shrink-0">
                <i data-lucide="package" class="w-7 h-7 text-emerald-600"></i>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-500 mb-1">Total Limbah</p>
                <h4 class="text-3xl font-extrabold text-gray-900">{{ number_format($stats['total_listings'] ?? 0, 0, ',', '.') }}</h4>
            </div>
        </div>

        <!-- Total Transaksi -->
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-center gap-5 hover:border-purple-200 hover:shadow-md transition-all duration-300">
            <div class="w-14 h-14 rounded-2xl bg-purple-50 flex items-center justify-center shrink-0">
                <i data-lucide="shopping-bag" class="w-7 h-7 text-purple-600"></i>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-500 mb-1">Total Transaksi</p>
                <h4 class="text-3xl font-extrabold text-gray-900">{{ number_format($stats['total_transactions'] ?? 0, 0, ',', '.') }}</h4>
            </div>
        </div>

        <!-- Total Pendapatan -->
        <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-center gap-5 hover:border-amber-200 hover:shadow-md transition-all duration-300">
            <div class="w-14 h-14 rounded-2xl bg-amber-50 flex items-center justify-center shrink-0">
                <i data-lucide="banknote" class="w-7 h-7 text-amber-600"></i>
            </div>
            <div>
                <p class="text-sm font-semibold text-gray-500 mb-1">Total Transaksi Selesai</p>
                <h4 class="text-xl sm:text-2xl font-extrabold text-gray-900">Rp{{ number_format($stats['total_revenue'] ?? 0, 0, ',', '.') }}</h4>
            </div>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
        
        <!-- Recent Transactions -->
        <div class="xl:col-span-2 bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex flex-col">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between bg-white">
                <div class="flex items-center gap-3">
                    <div class="p-2 bg-brand/10 rounded-lg">
                        <i data-lucide="activity" class="w-5 h-5 text-brand"></i>
                    </div>
                    <h3 class="font-bold text-lg text-gray-900">Aktivitas Transaksi Terbaru</h3>
                </div>
                <a href="{{ route('admin.transactions.index') }}" class="text-sm font-bold text-brand hover:text-brand-hover bg-brand/5 px-4 py-2 rounded-lg transition-colors">
                    Lihat Semua
                </a>
            </div>
            <div class="p-6 flex-1 flex flex-col justify-start bg-gray-50/50">
                @if(($recentOrders ?? collect())->isEmpty())
                    <div class="flex flex-col items-center justify-center text-center py-12 my-auto">
                        <div class="w-20 h-20 bg-white rounded-full shadow-sm flex items-center justify-center mb-5">
                            <i data-lucide="inbox" class="w-10 h-10 text-gray-300"></i>
                        </div>
                        <h5 class="text-lg text-gray-900 font-bold mb-2">Belum Ada Transaksi Terbaru</h5>
                        <p class="text-gray-500 max-w-sm">Aktivitas transaksi jual beli limbah terbaru akan muncul di bagian ini untuk memudahkan pemantauan.</p>
                    </div>
                @else
                    <div class="divide-y divide-gray-200/60">
                        @php
                            // ponytail: status badge styling map
                            $statusConfig = [
                                'pending' => ['bg' => 'bg-amber-50 text-amber-700 border-amber-200', 'label' => 'Menunggu Konfirmasi'],
                                'waiting_payment' => ['bg' => 'bg-blue-50 text-blue-700 border-blue-200', 'label' => 'Menunggu Pembayaran'],
                                'paid' => ['bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'label' => 'Sudah Dibayar'],
                                'processing' => ['bg' => 'bg-indigo-50 text-indigo-700 border-indigo-200', 'label' => 'Diproses'],
                                'completed' => ['bg' => 'bg-gray-100 text-gray-700 border-gray-200', 'label' => 'Selesai'],
                                'rejected' => ['bg' => 'bg-rose-50 text-rose-700 border-rose-200', 'label' => 'Ditolak'],
                                'cancelled' => ['bg' => 'bg-red-50 text-red-700 border-red-200', 'label' => 'Dibatalkan']
                            ];
                        @endphp
                        @foreach($recentOrders as $order)
                            @php
                                $st = $statusConfig[$order->order_status] ?? ['bg' => 'bg-gray-50 text-gray-700 border-gray-200', 'label' => strtoupper($order->order_status)];
                            @endphp
                            <div class="py-4 flex items-center justify-between gap-4">
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="font-mono font-bold text-sm text-gray-900">{{ $order->order_code }}</span>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold border {{ $st['bg'] }}">
                                            {{ $st['label'] }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-gray-600 truncate">
                                        Pembeli: <span class="font-semibold text-gray-800">{{ $order->buyer->name ?? '-' }}</span> | Penjual: <span class="font-semibold text-gray-800">{{ $order->seller->name ?? '-' }}</span>
                                    </p>
                                </div>
                                <div class="text-right flex-shrink-0">
                                    <div class="font-bold text-sm text-brand">Rp {{ number_format((float)$order->total_amount, 0, ',', '.') }}</div>
                                    <a href="{{ route('admin.transactions.show', $order->id) }}" class="text-xs text-brand hover:underline font-semibold mt-0.5 inline-block">Lihat Detail →</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- System Alerts / Pending actions -->
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex flex-col">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center gap-3 bg-white">
                <div class="p-2 bg-amber-50 rounded-lg">
                    <i data-lucide="bell-ring" class="w-5 h-5 text-amber-500"></i>
                </div>
                <h3 class="font-bold text-lg text-gray-900">Tindakan Diperlukan</h3>
            </div>
            
            <div class="p-6 flex-1 flex flex-col justify-start bg-gray-50/50">
                @if(($pendingVerificationsCount ?? 0) === 0 && ($pendingComplaintsCount ?? 0) === 0)
                    <div class="flex flex-col items-center justify-center text-center py-10 my-auto">
                        <div class="w-20 h-20 bg-white rounded-full shadow-sm flex items-center justify-center mb-5 relative">
                            <i data-lucide="check-circle-2" class="w-10 h-10 text-emerald-400"></i>
                        </div>
                        <h5 class="text-lg text-gray-900 font-bold mb-2">Semua Bersih!</h5>
                        <p class="text-gray-500">Tidak ada limbah yang membutuhkan verifikasi atau komplain tertunda saat ini.</p>
                    </div>
                @else
                    <div class="space-y-4">
                        @if(($pendingVerificationsCount ?? 0) > 0)
                            <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <i data-lucide="file-text" class="w-5 h-5 text-amber-600"></i>
                                    <div>
                                        <h6 class="font-bold text-amber-900 text-sm">Verifikasi Limbah</h6>
                                        <p class="text-xs text-amber-700">{{ $pendingVerificationsCount }} limbah menunggu persetujuan</p>
                                    </div>
                                </div>
                                <a href="{{ route('admin.listings.verification.index') }}" class="px-3 py-1.5 bg-amber-600 text-white font-bold text-xs rounded-lg hover:bg-amber-700 transition-colors">
                                    Tinjau
                                </a>
                            </div>
                        @endif

                        @if(($pendingComplaintsCount ?? 0) > 0)
                            <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <i data-lucide="alert-triangle" class="w-5 h-5 text-rose-600"></i>
                                    <div>
                                        <h6 class="font-bold text-rose-900 text-sm">Komplain Pengguna</h6>
                                        <p class="text-xs text-rose-700">{{ $pendingComplaintsCount }} komplain butuh tindakan</p>
                                    </div>
                                </div>
                                <a href="{{ route('admin.complaints.index') }}" class="px-3 py-1.5 bg-rose-600 text-white font-bold text-xs rounded-lg hover:bg-rose-700 transition-colors">
                                    Tinjau
                                </a>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>

    </div>
@endsection
