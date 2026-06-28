@extends('admin.layouts.admin')

@section('title', 'Dashboard Admin - Recyclink')
@section('header_title', 'Dashboard Admin')

@section('content')
    <div class="mb-8">
        <h3 class="text-3xl font-extrabold text-gray-900 tracking-tight">Selamat datang kembali! 👋</h3>
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
            <div class="p-6 flex-1 flex flex-col justify-center bg-gray-50/50">
                <!-- Placeholder if no data -->
                <div class="flex flex-col items-center justify-center text-center py-12">
                    <div class="w-20 h-20 bg-white rounded-full shadow-sm flex items-center justify-center mb-5">
                        <i data-lucide="inbox" class="w-10 h-10 text-gray-300"></i>
                    </div>
                    <h5 class="text-lg text-gray-900 font-bold mb-2">Belum Ada Transaksi Terbaru</h5>
                    <p class="text-gray-500 max-w-sm">Aktivitas transaksi jual beli limbah terbaru akan muncul di bagian ini untuk memudahkan pemantauan.</p>
                </div>
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
            
            <div class="p-6 flex-1 flex flex-col justify-center bg-gray-50/50">
                <div class="flex flex-col items-center justify-center text-center py-10">
                    <div class="w-20 h-20 bg-white rounded-full shadow-sm flex items-center justify-center mb-5 relative">
                        <i data-lucide="check-circle-2" class="w-10 h-10 text-emerald-400"></i>
                    </div>
                    <h5 class="text-lg text-gray-900 font-bold mb-2">Semua Bersih!</h5>
                    <p class="text-gray-500">Tidak ada limbah yang membutuhkan verifikasi atau komplain tertunda saat ini.</p>
                </div>
            </div>
        </div>

    </div>
@endsection
