@extends('admin.layouts.admin')

@section('title', 'Detail Penarikan #' . ($withdrawal->withdrawal_number ?? $withdrawal->id) . ' - Admin Recyclink')
@section('header_title', 'Detail Penarikan Saldo')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    {{-- Top Action Bar / Breadcrumb --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <a href="{{ route('admin.withdrawals.index') }}" class="inline-flex items-center text-sm font-semibold text-gray-500 hover:text-brand transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
            Kembali ke Daftar Penarikan
        </a>

        @php
            $statusConfig = [
                'pending' => ['bg' => 'bg-amber-50 border-amber-200 text-amber-700', 'icon' => 'clock', 'label' => 'Menunggu Tinjauan'],
                'approved' => ['bg' => 'bg-blue-50 border-blue-200 text-blue-700', 'icon' => 'arrow-up-right', 'label' => 'Siap Ditransfer'],
                'paid' => ['bg' => 'bg-emerald-50 border-emerald-200 text-emerald-700', 'icon' => 'check-circle-2', 'label' => 'Selesai / Sudah Ditransfer'],
                'rejected' => ['bg' => 'bg-rose-50 border-rose-200 text-rose-700', 'icon' => 'x-circle', 'label' => 'Ditolak (Refund)']
            ];
            $status = $statusConfig[$withdrawal->status] ?? ['bg' => 'bg-gray-50 border-gray-200 text-gray-700', 'icon' => 'help-circle', 'label' => strtoupper($withdrawal->status)];
            $netAmount = (float)($withdrawal->net_amount ?? ($withdrawal->amount - ($withdrawal->admin_fee ?? 0)));
        @endphp

        <div class="flex items-center gap-3">
            <span class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full text-xs font-bold border {{ $status['bg'] }}">
                <i data-lucide="{{ $status['icon'] }}" class="w-4 h-4"></i>
                {{ $status['label'] }}
            </span>
        </div>
    </div>

    {{-- Flash Alerts --}}
    @if(session('success'))
        <div class="p-4 bg-green-50 border border-green-200 rounded-2xl flex items-start gap-3 shadow-sm">
            <i data-lucide="check-circle" class="w-5 h-5 text-green-500 mt-0.5 shrink-0"></i>
            <p class="text-sm font-semibold text-green-700">{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 bg-red-50 border border-red-200 rounded-2xl flex items-start gap-3 shadow-sm">
            <i data-lucide="alert-circle" class="w-5 h-5 text-red-500 mt-0.5 shrink-0"></i>
            <p class="text-sm font-semibold text-red-700">{{ session('error') }}</p>
        </div>
    @endif

    {{-- Main Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Left Column: Financial & Bank Details --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Financial Summary Card --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm space-y-5">
                <div class="flex items-center justify-between border-b border-gray-100 pb-4">
                    <div>
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Nomor Pengajuan</span>
                        <h4 class="font-mono font-extrabold text-xl text-gray-900 mt-0.5">#{{ $withdrawal->withdrawal_number ?? 'WD-' . $withdrawal->id }}</h4>
                    </div>
                    <div class="text-right">
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Tanggal Pengajuan</span>
                        <p class="text-sm font-semibold text-gray-700 mt-0.5">{{ $withdrawal->created_at->format('d M Y, H:i') }} WIB</p>
                    </div>
                </div>

                {{-- Net Transfer Highlight --}}
                <div class="p-5 bg-gradient-to-r from-emerald-50 to-teal-50 border border-emerald-200 rounded-2xl">
                    <p class="text-xs font-bold text-emerald-800 uppercase tracking-wider">Nominal Bersih yang Harus Ditransfer</p>
                    <h2 class="text-3xl sm:text-4xl font-black text-brand tracking-tight mt-1">
                        Rp {{ number_format($netAmount, 0, ',', '.') }}
                    </h2>
                    <p class="text-xs text-emerald-700 font-medium mt-1">Jumlah dana bersih setelah dikurangi biaya operasional/admin platform.</p>
                </div>

                {{-- Cost Breakdown --}}
                <div class="space-y-3 text-sm pt-2">
                    <h5 class="text-xs font-bold text-gray-400 uppercase tracking-wider">Rincian Perhitungan</h5>
                    <div class="p-4 bg-gray-50 rounded-xl space-y-2.5 border border-gray-100">
                        <div class="flex justify-between items-center text-gray-600">
                            <span>Jumlah Pengajuan Saldo (Kotor)</span>
                            <span class="font-bold text-gray-800">Rp {{ number_format((float)$withdrawal->amount, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between items-center text-gray-600">
                            <span>Biaya Admin Platform (3%)</span>
                            <span class="font-semibold text-rose-600">- Rp {{ number_format((float)($withdrawal->admin_fee ?? 0), 0, ',', '.') }}</span>
                        </div>
                        <div class="border-t border-gray-200 pt-2 flex justify-between items-center text-base font-bold text-gray-900">
                            <span>Total Transfer ke Rekening</span>
                            <span class="text-brand">Rp {{ number_format($netAmount, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bank Account Details Card --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm space-y-4">
                <div class="flex items-center gap-3 border-b border-gray-100 pb-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center border border-blue-100 text-blue-600">
                        <i data-lucide="building-2" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 text-base">Rekening Tujuan Transfer</h4>
                        <p class="text-xs text-gray-500">Informasi rekening bank penerima milik penjual.</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm pt-1">
                    <div class="p-4 bg-gray-50 rounded-xl border border-gray-100">
                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block mb-1">Nama Bank / Fintech</span>
                        <p class="font-extrabold text-gray-900 text-base uppercase">{{ $withdrawal->bank_name }}</p>
                    </div>

                    <div class="p-4 bg-gray-50 rounded-xl border border-gray-100">
                        <span class="text-[11px] font-bold text-gray-400 uppercase tracking-wider block mb-1">Nama Pemilik Rekening</span>
                        <p class="font-bold text-gray-900 text-base">{{ $withdrawal->bank_account_name }}</p>
                    </div>

                    <div class="sm:col-span-2 p-4 bg-brand/5 rounded-xl border border-brand/20 flex items-center justify-between">
                        <div>
                            <span class="text-[11px] font-bold text-brand uppercase tracking-wider block mb-0.5">Nomor Rekening Tujuan</span>
                            <p class="font-mono font-extrabold text-lg sm:text-xl text-gray-900 tracking-wide" id="bank-acc-num">{{ $withdrawal->bank_account_number }}</p>
                        </div>
                        <button type="button" onclick="navigator.clipboard.writeText('{{ $withdrawal->bank_account_number }}'); alert('Nomor rekening disalin ke clipboard!');"
                                class="px-3.5 py-2 bg-white hover:bg-gray-100 border border-gray-200 text-gray-700 font-bold text-xs rounded-xl shadow-xs transition-all flex items-center gap-1.5 cursor-pointer">
                            <i data-lucide="copy" class="w-3.5 h-3.5"></i> Salin No. Rekening
                        </button>
                    </div>
                </div>
            </div>

            {{-- Audit Trail & Admin Note --}}
            @if($withdrawal->processed_at || $withdrawal->approver || $withdrawal->admin_note)
                <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm space-y-4">
                    <h4 class="font-bold text-gray-900 text-base border-b border-gray-100 pb-3">Riwayat & Catatan Admin</h4>
                    
                    <div class="space-y-3 text-sm">
                        @if($withdrawal->approver)
                            <div class="flex items-center justify-between text-gray-600">
                                <span>Diproses oleh:</span>
                                <span class="font-bold text-gray-800">{{ $withdrawal->approver->name }} ({{ $withdrawal->approver->email }})</span>
                            </div>
                        @endif

                        @if($withdrawal->processed_at)
                            <div class="flex items-center justify-between text-gray-600">
                                <span>Waktu Pemrosesan:</span>
                                <span class="font-semibold text-gray-800">{{ $withdrawal->processed_at->format('d M Y, H:i') }} WIB</span>
                            </div>
                        @endif

                        @if($withdrawal->admin_note)
                            <div class="pt-2">
                                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider block mb-1.5">Catatan / Alasan:</span>
                                <div class="p-4 bg-gray-50 rounded-xl border border-gray-200 text-gray-700 font-medium text-sm leading-relaxed">
                                    {{ $withdrawal->admin_note }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

        </div>

        {{-- Right Column: Admin Actions & Seller Profile --}}
        <div class="space-y-6">

            {{-- Action Panel Card --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm space-y-4">
                <h4 class="font-bold text-gray-900 text-base border-b border-gray-100 pb-3">Tindakan Admin</h4>

                @if($withdrawal->status === 'pending')
                    <p class="text-xs text-gray-500 leading-relaxed">
                        Periksa keabsahan pengajuan. Jika disetujui, Anda dapat melanjutkan ke proses transfer bank. Jika ditolak, saldo akan otomatis dikembalikan ke dompet penjual.
                    </p>

                    {{-- Form Setujui (Approve) --}}
                    <form action="{{ route('admin.withdrawals.approve', $withdrawal->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menyetujui pengajuan penarikan dana ini?');" data-turbo="false">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="w-full py-3 px-4 bg-brand hover:bg-brand-hover text-white font-bold text-sm rounded-xl shadow-sm transition-all flex items-center justify-center gap-2 cursor-pointer">
                            <i data-lucide="check" class="w-4 h-4"></i>
                            Setujui Penarikan
                        </button>
                    </form>

                    {{-- Form Tolak (Reject) --}}
                    <div class="pt-3 border-t border-gray-100">
                        <button type="button" onclick="document.getElementById('reject-form-container').classList.toggle('hidden');"
                                class="w-full py-2.5 px-4 bg-white border border-rose-200 hover:bg-rose-50 text-rose-600 font-bold text-xs rounded-xl transition-all flex items-center justify-center gap-1.5 cursor-pointer">
                            <i data-lucide="x" class="w-4 h-4"></i>
                            Tolak Pengajuan & Refund Saldo
                        </button>

                        <div id="reject-form-container" class="hidden mt-3 p-4 bg-rose-50/60 rounded-xl border border-rose-200 space-y-3">
                            <form action="{{ route('admin.withdrawals.reject', $withdrawal->id) }}" method="POST" onsubmit="return confirm('Tolak ajuan ini dan kembalikan dana ke saldo dompet penjual?');" data-turbo="false">
                                @csrf
                                @method('PATCH')
                                <label for="admin_note" class="block text-xs font-bold text-rose-900 mb-1">Alasan Penolakan <span class="text-rose-600">*</span></label>
                                <textarea name="admin_note" id="admin_note" rows="3" required placeholder="Contoh: Nomor rekening tidak valid / nama tidak sesuai identitas."
                                          class="w-full p-2.5 text-xs bg-white border border-rose-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-rose-400 text-gray-800"></textarea>
                                <button type="submit" class="w-full mt-2 py-2 px-3 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-lg transition-colors cursor-pointer shadow-xs">
                                    Konfirmasi Penolakan & Refund
                                </button>
                            </form>
                        </div>
                    </div>

                @elseif($withdrawal->status === 'approved')
                    <div class="p-4 bg-blue-50 border border-blue-200 rounded-xl text-xs text-blue-900 space-y-2">
                        <div class="flex items-center gap-2 font-bold text-sm text-blue-800">
                            <i data-lucide="info" class="w-4 h-4 shrink-0"></i>
                            <span>Siap Ditransfer</span>
                        </div>
                        <p class="leading-relaxed">
                            Silakan lakukan transfer uang manual via m-Banking/Internet Banking sebesar <strong>Rp {{ number_format($netAmount, 0, ',', '.') }}</strong> ke rekening di samping.
                        </p>
                    </div>

                    {{-- Form Tandai Dibayar (Mark as Paid) --}}
                    <form action="{{ route('admin.withdrawals.pay', $withdrawal->id) }}" method="POST" onsubmit="return confirm('Apakah Anda telah berhasil mentransfer dana bersih ke rekening penjual?');" data-turbo="false">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="w-full py-3 px-4 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm rounded-xl shadow-sm transition-all flex items-center justify-center gap-2 cursor-pointer">
                            <i data-lucide="check-circle" class="w-4 h-4"></i>
                            Tandai Sudah Ditransfer (Selesai)
                        </button>
                    </form>

                @elseif($withdrawal->status === 'paid')
                    <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-center space-y-2">
                        <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center mx-auto text-emerald-600">
                            <i data-lucide="check-circle-2" class="w-6 h-6"></i>
                        </div>
                        <h5 class="font-bold text-emerald-900 text-sm">Penarikan Selesai</h5>
                        <p class="text-xs text-emerald-700 leading-relaxed">
                            Dana telah berhasil ditransfer ke rekening penjual dan status penarikan telah ditutup.
                        </p>
                    </div>

                @elseif($withdrawal->status === 'rejected')
                    <div class="p-4 bg-rose-50 border border-rose-200 rounded-xl text-center space-y-2">
                        <div class="w-10 h-10 bg-rose-100 rounded-full flex items-center justify-center mx-auto text-rose-600">
                            <i data-lucide="x-circle" class="w-6 h-6"></i>
                        </div>
                        <h5 class="font-bold text-rose-900 text-sm">Pengajuan Ditolak</h5>
                        <p class="text-xs text-rose-700 leading-relaxed">
                            Dana sebesar <strong>Rp {{ number_format((float)$withdrawal->amount, 0, ',', '.') }}</strong> telah dikembalikan secara otomatis ke dompet penjual.
                        </p>
                    </div>
                @endif
            </div>

            {{-- Seller Profile Card --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm space-y-4">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block">Informasi Penjual</span>
                
                <div class="flex items-center gap-3">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($withdrawal->user->name ?? 'User') }}&background=7A9C59&color=fff" class="w-12 h-12 rounded-xl object-cover border border-gray-100" alt="">
                    <div class="min-w-0">
                        <h4 class="font-bold text-gray-900 text-sm leading-snug truncate">{{ $withdrawal->user->name ?? 'N/A' }}</h4>
                        <p class="text-xs text-brand font-medium">Penjual Terdaftar</p>
                    </div>
                </div>

                <div class="text-xs text-gray-600 space-y-2 pt-2 border-t border-gray-100">
                    <p class="flex items-center gap-2 truncate"><i data-lucide="mail" class="w-3.5 h-3.5 text-gray-400 shrink-0"></i> {{ $withdrawal->user->email ?? '-' }}</p>
                    <p class="flex items-center gap-2"><i data-lucide="phone" class="w-3.5 h-3.5 text-gray-400 shrink-0"></i> {{ $withdrawal->user->phone_number ?? '-' }}</p>
                    @if($withdrawal->user && $withdrawal->user->sellerProfile)
                        <p class="flex items-center gap-2"><i data-lucide="store" class="w-3.5 h-3.5 text-gray-400 shrink-0"></i> Toko: <span class="font-bold text-gray-800">{{ $withdrawal->user->sellerProfile->business_name }}</span></p>
                        @if($withdrawal->user->sellerProfile->city)
                            <p class="flex items-center gap-2"><i data-lucide="map-pin" class="w-3.5 h-3.5 text-gray-400 shrink-0"></i> {{ $withdrawal->user->sellerProfile->city }}</p>
                        @endif
                    @endif
                </div>

                {{-- Saldo Dompet Saat Ini --}}
                <div class="p-3.5 bg-gray-50 rounded-xl border border-gray-100 flex items-center justify-between text-xs">
                    <span class="text-gray-500 font-medium">Saldo Dompet Saat Ini:</span>
                    <span class="font-extrabold text-gray-900">
                        Rp {{ number_format((float)($withdrawal->wallet->balance ?? 0), 0, ',', '.') }}
                    </span>
                </div>

                @if($withdrawal->user)
                    <a href="{{ route('admin.users.show', $withdrawal->user->id) }}"
                       class="block w-full py-2 px-3 text-center bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold text-xs rounded-xl transition-colors">
                        Lihat Profil Lengkap Pengguna
                    </a>
                @endif
            </div>

        </div>

    </div>
</div>
@endsection
