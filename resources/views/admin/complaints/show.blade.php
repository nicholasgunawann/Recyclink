@extends('admin.layouts.admin')

@section('title', 'Detail Keluhan #' . $complaint->complaint_number . ' - Admin Recyclink')
@section('header_title', 'Detail Keluhan')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    {{-- Top Action Bar --}}
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.complaints.index') }}" class="inline-flex items-center text-sm font-semibold text-gray-500 hover:text-brand transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-2"></i>
            Kembali ke Daftar Keluhan
        </a>
        <div class="flex items-center gap-3">
            @php
                // ponytail: status labels config map
                $statusConfig = [
                    'open' => ['bg' => 'bg-amber-50 border-amber-200 text-amber-700', 'label' => 'Terbuka'],
                    'under_review' => ['bg' => 'bg-indigo-50 border-indigo-200 text-indigo-700', 'label' => 'Sedang Ditinjau'],
                    'resolved' => ['bg' => 'bg-emerald-50 border-emerald-200 text-emerald-700', 'label' => 'Selesai'],
                    'rejected' => ['bg' => 'bg-rose-50 border-rose-200 text-rose-700', 'label' => 'Ditolak'],
                    'appealed' => ['bg' => 'bg-purple-50 border-purple-200 text-purple-700', 'label' => 'Banding Ditinjau']
                ];
                $status = $statusConfig[$complaint->status] ?? ['bg' => 'bg-gray-50 border-gray-200 text-gray-700', 'label' => strtoupper($complaint->status)];
            @endphp
            <span class="inline-flex items-center px-3.5 py-1.5 rounded-full text-xs font-bold border {{ $status['bg'] }}">
                {{ $status['label'] }}
            </span>
        </div>
    </div>

    {{-- Validation Errors --}}
    @if ($errors->any())
        <div class="p-4 bg-red-50 border border-red-200 rounded-2xl flex items-start gap-3">
            <i data-lucide="alert-circle" class="w-5 h-5 text-red-500 mt-0.5 shrink-0"></i>
            <div>
                <p class="text-sm font-bold text-red-800">Terdapat kesalahan input:</p>
                <ul class="mt-1 list-disc list-inside text-sm text-red-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    {{-- Main Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Left column (Dispute Info, Evidence, Chat, Actions) --}}
        <div class="lg:col-span-2 space-y-6">
            
            {{-- Complaint Information --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm space-y-4">
                <div>
                    <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Nomor Keluhan</span>
                    <h4 class="text-xl font-mono font-bold text-gray-900 mt-1">#{{ $complaint->complaint_number }}</h4>
                </div>
                <hr class="border-gray-100">
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-400 font-semibold text-xs uppercase tracking-wider">Tipe Keluhan</p>
                        <p class="text-gray-800 font-bold mt-1">{{ strtoupper($complaint->complaint_type) }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400 font-semibold text-xs uppercase tracking-wider">Tanggal Pengaduan</p>
                        <p class="text-gray-800 font-bold mt-1">{{ $complaint->created_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>
                <hr class="border-gray-100">
                <div>
                    <p class="text-gray-400 font-semibold text-xs uppercase tracking-wider">Subjek</p>
                    <p class="text-gray-900 font-bold text-base mt-1">{{ $complaint->subject }}</p>
                </div>
                <div>
                    <p class="text-gray-400 font-semibold text-xs uppercase tracking-wider">Deskripsi Keluhan</p>
                    <p class="text-gray-800 mt-2 text-sm leading-relaxed whitespace-pre-line">{{ $complaint->description }}</p>
                </div>
            </div>

            {{-- Evidence Attachment --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm space-y-4">
                <h4 class="font-bold text-gray-900 text-base">Bukti Pendukung (Evidence)</h4>
                @if($complaint->evidence_url)
                    <div class="border border-gray-100 rounded-xl overflow-hidden shadow-sm bg-gray-50 max-w-lg">
                        <img src="{{ asset('storage/' . $complaint->evidence_url) }}" class="w-full h-auto object-cover max-h-80" alt="Bukti Sengketa">
                    </div>
                    <a href="{{ asset('storage/' . $complaint->evidence_url) }}" target="_blank" class="inline-flex items-center text-xs font-bold text-brand hover:underline">
                        <i data-lucide="external-link" class="w-4 h-4 mr-1"></i> Buka Gambar di Tab Baru
                    </a>
                @else
                    <p class="text-gray-400 text-sm italic">Tidak ada bukti gambar pendukung yang diunggah.</p>
                @endif
            </div>

            {{-- Chat Room / Ruang Diskusi (Admin View) --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden flex flex-col h-[500px]">
                <div class="p-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                    <div>
                        <h4 class="font-bold text-gray-900 text-base">Riwayat Diskusi Resolusi</h4>
                        <p class="text-xs text-gray-500">Percakapan antara Pembeli, Penjual, dan Admin</p>
                    </div>
                    <span class="text-xs font-bold px-2.5 py-1 rounded-full bg-brand/10 text-brand">Pusat Mediasi</span>
                </div>

                <div class="flex-1 overflow-y-auto p-4 space-y-4 bg-gray-50" id="chat-container">
                    @forelse($complaint->messages ?? [] as $msg)
                        <div class="flex {{ $msg->user_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-[75%] flex gap-3 {{ $msg->user_id === auth()->id() ? 'flex-row-reverse' : '' }}">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($msg->user->name ?? 'User') }}&background={{ $msg->user?->isAdmin() ? '7A9C59' : ($msg->user_id === $complaint->complainant_id ? '3b82f6' : '14b8a6') }}&color=fff" class="w-8 h-8 rounded-full shrink-0 mt-1">
                                <div class="{{ $msg->user_id === auth()->id() ? 'bg-brand text-white' : ($msg->user?->isAdmin() ? 'bg-purple-600 text-white' : 'bg-white border border-gray-200 text-gray-800') }} p-3 rounded-2xl shadow-sm">
                                    <p class="text-xs font-bold mb-1 opacity-80 {{ $msg->user_id === auth()->id() ? 'text-right' : '' }}">
                                        {{ $msg->user->name ?? 'User' }} 
                                        @if($msg->user?->isAdmin()) (Admin) 
                                        @elseif($msg->user_id === $complaint->complainant_id) (Pelapor / Pembeli) 
                                        @elseif($msg->user_id === $complaint->respondent_id) (Terlapor / Penjual) 
                                        @endif
                                    </p>
                                    <p class="text-sm whitespace-pre-wrap">{{ $msg->message }}</p>
                                    
                                    @if($msg->attachment_url)
                                        <a href="{{ asset('storage/' . $msg->attachment_url) }}" target="_blank" class="block mt-2">
                                            @if(Str::endsWith($msg->attachment_url, '.mp4'))
                                                <video src="{{ asset('storage/' . $msg->attachment_url) }}" class="w-full h-24 object-cover rounded-xl" controls></video>
                                            @else
                                                <img src="{{ asset('storage/' . $msg->attachment_url) }}" class="w-full h-24 object-cover rounded-xl">
                                            @endif
                                        </a>
                                    @endif
                                    
                                    <p class="text-[10px] mt-2 text-right opacity-60">{{ $msg->created_at->format('d M H:i') }}</p>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="h-full flex items-center justify-center text-center">
                            <div>
                                <i data-lucide="message-square" class="w-10 h-10 text-gray-300 mx-auto mb-2"></i>
                                <p class="text-gray-500 text-sm">Belum ada diskusi tercatat dalam sengketa ini.</p>
                            </div>
                        </div>
                    @endforelse
                </div>

                @if(!$complaint->isResolved() && $complaint->status !== \App\Models\Complaint::STATUS_APPEALED)
                <div class="p-3 bg-white border-t border-gray-100">
                    <form action="{{ route('admin.complaints.messages.store', $complaint->id) }}" method="POST" enctype="multipart/form-data" class="flex gap-3 items-end">
                        @csrf
                        <div class="flex-1">
                            <textarea name="message" rows="2" class="w-full border-gray-200 rounded-xl focus:ring-brand focus:border-brand px-3 py-2 text-sm resize-none bg-gray-50" placeholder="Tulis instruksi atau tanggapan Admin..." required></textarea>
                            <input type="file" name="attachment" accept="image/jpeg,image/png,video/mp4,application/pdf" class="mt-1 text-xs text-gray-500">
                        </div>
                        <button type="submit" class="h-10 px-5 bg-brand hover:bg-brand-hover text-white font-bold text-xs rounded-xl transition-colors shadow shrink-0 flex items-center gap-1.5">
                            <i data-lucide="send" class="w-4 h-4"></i> Kirim Pesan Admin
                        </button>
                    </form>
                </div>
                @endif
            </div>

            {{-- Actions Panel --}}
            @if($complaint->status === 'open' || $complaint->status === 'under_review')
                <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm space-y-4">
                    <h4 class="font-bold text-gray-900 text-base font-semibold">Resolusi & Tindakan Admin</h4>

                    @if($complaint->status === 'open')
                        <form action="{{ route('admin.complaints.process', $complaint->id) }}" method="POST">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="w-full py-3 bg-brand hover:bg-brand-hover text-white font-bold rounded-xl transition-all shadow-sm flex items-center justify-center gap-2">
                                <i data-lucide="shield-alert" class="w-5 h-5"></i> Mulai Tinjau Keluhan
                            </button>
                        </form>
                    @endif

                    @if($complaint->status === 'under_review')
                        <div class="flex gap-4">
                            <button type="button" onclick="showResolutionForm('resolve')" class="flex-1 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl transition-all flex items-center justify-center gap-2">
                                <i data-lucide="check-circle" class="w-4 h-4"></i> Selesaikan Dispute
                            </button>
                            <button type="button" onclick="showResolutionForm('reject')" class="flex-1 py-3 bg-white border border-gray-300 text-rose-600 font-bold rounded-xl hover:bg-rose-50 hover:border-rose-200 transition-all flex items-center justify-center gap-2">
                                <i data-lucide="x-circle" class="w-4 h-4"></i> Tolak Keluhan
                            </button>
                        </div>

                        {{-- Resolve Form --}}
                        <div id="resolve-form-container" class="hidden p-5 bg-emerald-50 border border-emerald-100 rounded-xl mt-4">
                            <form action="{{ route('admin.complaints.resolve', $complaint->id) }}" method="POST" class="space-y-4">
                                @csrf
                                @method('PATCH')
                                <div>
                                    <label class="block text-sm font-bold text-emerald-900 mb-1.5">Catatan Resolusi (Selesai)</label>
                                    <textarea name="resolution_note" rows="3" required class="w-full border border-emerald-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 bg-white" placeholder="Jelaskan kesepakatan damai / keputusan pengembalian dana..."></textarea>
                                </div>
                                <div class="flex justify-end gap-3 text-xs">
                                    <button type="button" onclick="hideResolutionForms()" class="px-4 py-2 text-gray-500 hover:text-gray-700 font-semibold">Batal</button>
                                    <button type="submit" class="px-5 py-2 bg-emerald-600 text-white rounded-lg font-bold hover:bg-emerald-700 transition-colors">Kirim Keputusan</button>
                                </div>
                            </form>
                        </div>

                        {{-- Reject Form --}}
                        <div id="reject-form-container" class="hidden p-5 bg-rose-50 border border-rose-100 rounded-xl mt-4">
                            <form action="{{ route('admin.complaints.reject', $complaint->id) }}" method="POST" class="space-y-4">
                                @csrf
                                @method('PATCH')
                                <div>
                                    <label class="block text-sm font-bold text-rose-900 mb-1.5">Alasan Penolakan Keluhan</label>
                                    <textarea name="resolution_note" rows="3" required class="w-full border border-rose-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-rose-500 bg-white" placeholder="Sebutkan alasan penolakan keluhan (bukti tidak valid, dll)..."></textarea>
                                </div>
                                <div class="flex justify-end gap-3 text-xs">
                                    <button type="button" onclick="hideResolutionForms()" class="px-4 py-2 text-gray-500 hover:text-gray-700 font-semibold">Batal</button>
                                    <button type="submit" class="px-5 py-2 bg-rose-600 text-white rounded-lg font-bold hover:bg-rose-700 transition-colors">Tolak Keluhan</button>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Final Resolution Summary --}}
            @if($complaint->status === 'resolved' || $complaint->status === 'rejected')
                <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm space-y-4 mb-6">
                    <h4 class="font-bold text-gray-900 text-base">Keputusan Akhir / Catatan Penyelesaian</h4>
                    <div class="p-4 rounded-xl text-sm leading-relaxed {{ $complaint->status === 'resolved' ? 'bg-emerald-50 text-emerald-800 border border-emerald-100' : 'bg-rose-50 text-rose-800 border border-rose-100' }}">
                        <p class="font-bold mb-1.5">{{ $complaint->status === 'resolved' ? 'Diselesaikan oleh Administrator:' : 'Ditolak oleh Administrator:' }}</p>
                        <p class="italic font-medium">"{{ $complaint->resolution_note ?? 'Tidak ada catatan penyelesaian.' }}"</p>
                        <p class="text-xs text-gray-400 mt-3 flex items-center gap-1"><i data-lucide="clock" class="w-3.5 h-3.5"></i> Selesai pada: {{ $complaint->resolved_at ? $complaint->resolved_at->format('d M Y, H:i') : $complaint->updated_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>
            @endif

            {{-- Appeal Section --}}
            @if($complaint->status === \App\Models\Complaint::STATUS_APPEALED)
                <div class="bg-white border border-blue-200 rounded-2xl p-6 shadow-sm space-y-4">
                    <h4 class="font-bold text-blue-900 text-base flex items-center gap-2"><i data-lucide="shield-alert" class="w-5 h-5 text-blue-600"></i> Pengajuan Banding Penjual</h4>
                    <div class="p-4 bg-blue-50 rounded-xl border border-blue-100 space-y-4">
                        <div>
                            <p class="text-xs text-blue-500 font-semibold uppercase tracking-wider mb-1">Tanggal Banding</p>
                            <p class="text-sm font-semibold text-blue-900">{{ $complaint->appealed_at ? $complaint->appealed_at->format('d M Y, H:i') : '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-blue-500 font-semibold uppercase tracking-wider mb-1">Alasan Banding</p>
                            <p class="text-sm text-blue-900 bg-white p-3 rounded-xl border border-blue-100">{{ $complaint->appeal_reason }}</p>
                        </div>
                        @if($complaint->appeal_evidence_url)
                        <div>
                            <p class="text-xs text-blue-500 font-semibold uppercase tracking-wider mb-2">Bukti Banding</p>
                            @if(Str::endsWith($complaint->appeal_evidence_url, '.mp4'))
                                <video src="{{ asset('storage/' . $complaint->appeal_evidence_url) }}" class="w-full h-48 object-cover rounded-xl border border-blue-200" controls></video>
                            @else
                                <img src="{{ asset('storage/' . $complaint->appeal_evidence_url) }}" class="w-full h-48 object-cover rounded-xl border border-blue-200">
                            @endif
                        </div>
                        @endif

                        {{-- Admin Actions for Appeal --}}
                        <div class="pt-4 border-t border-blue-200 flex gap-3">
                            <button type="button" onclick="showAppealForm('accept')" class="flex-1 py-2.5 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition-all flex items-center justify-center gap-2 text-sm">
                                <i data-lucide="check-circle" class="w-4 h-4"></i> Terima Banding (Penjual Menang)
                            </button>
                            <button type="button" onclick="showAppealForm('reject')" class="flex-1 py-2.5 bg-white border border-gray-300 text-rose-600 font-bold rounded-xl hover:bg-rose-50 transition-all flex items-center justify-center gap-2 text-sm">
                                <i data-lucide="x-circle" class="w-4 h-4"></i> Tolak Banding
                            </button>
                        </div>

                        {{-- Accept Appeal Form --}}
                        <div id="appeal-accept-form" class="hidden p-4 bg-white border border-blue-200 rounded-xl mt-4">
                            <form action="{{ route('admin.complaints.appeal.accept', $complaint->id) }}" method="POST" class="space-y-3">
                                @csrf
                                @method('PATCH')
                                <div>
                                    <label class="block text-xs font-bold text-blue-900 mb-1">Catatan Penerimaan Banding</label>
                                    <textarea name="appeal_resolution_note" rows="2" required class="w-full border border-blue-200 rounded-xl px-3 py-2 text-sm focus:ring-blue-500" placeholder="Jelaskan alasan menerima banding..."></textarea>
                                </div>
                                <div class="flex justify-end gap-2">
                                    <button type="button" onclick="hideAppealForms()" class="px-3 py-1.5 text-gray-500 text-xs font-semibold">Batal</button>
                                    <button type="submit" class="px-4 py-1.5 bg-blue-600 text-white text-xs font-bold rounded-lg hover:bg-blue-700">Kirim Keputusan</button>
                                </div>
                            </form>
                        </div>

                        {{-- Reject Appeal Form --}}
                        <div id="appeal-reject-form" class="hidden p-4 bg-white border border-rose-200 rounded-xl mt-4">
                            <form action="{{ route('admin.complaints.appeal.reject', $complaint->id) }}" method="POST" class="space-y-3">
                                @csrf
                                @method('PATCH')
                                <div>
                                    <label class="block text-xs font-bold text-rose-900 mb-1">Alasan Penolakan Banding</label>
                                    <textarea name="appeal_resolution_note" rows="2" required class="w-full border border-rose-200 rounded-xl px-3 py-2 text-sm focus:ring-rose-500" placeholder="Jelaskan alasan menolak banding..."></textarea>
                                </div>
                                <div class="flex justify-end gap-2">
                                    <button type="button" onclick="hideAppealForms()" class="px-3 py-1.5 text-gray-500 text-xs font-semibold">Batal</button>
                                    <button type="submit" class="px-4 py-1.5 bg-rose-600 text-white text-xs font-bold rounded-lg hover:bg-rose-700">Tolak Banding</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif

        </div>

        {{-- Right column (Complainant, Respondent, Order Snapshots) --}}
        <div class="space-y-6">
            
            {{-- Complainant (Pelapor) --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm space-y-4">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Pihak Pelapor</span>
                <div class="flex items-center gap-3">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($complaint->complainant->name ?? 'User') }}&background=f1f5f9&color=64748b" class="w-10 h-10 rounded-full border border-gray-100" alt="">
                    <div>
                        <h4 class="font-bold text-gray-900 text-sm leading-snug">{{ $complaint->complainant->name ?? 'N/A' }}</h4>
                        <p class="text-xs text-brand font-medium font-semibold">Pelapor (Complainant)</p>
                    </div>
                </div>
                <div class="text-xs text-gray-500 space-y-1.5 pt-2 border-t border-gray-50">
                    <p class="flex items-center gap-2"><i data-lucide="mail" class="w-3.5 h-3.5 text-gray-400"></i> {{ $complaint->complainant->email ?? '-' }}</p>
                    <p class="flex items-center gap-2"><i data-lucide="phone" class="w-3.5 h-3.5 text-gray-400"></i> {{ $complaint->complainant->phone_number ?? '-' }}</p>
                </div>
            </div>

            {{-- Respondent (Terlapor) --}}
            <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm space-y-4">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Pihak Terlapor</span>
                <div class="flex items-center gap-3">
                    <img src="https://ui-avatars.com/api/?name={{ urlencode($complaint->respondent->name ?? 'User') }}&background=f1f5f9&color=64748b" class="w-10 h-10 rounded-full border border-gray-100" alt="">
                    <div>
                        <h4 class="font-bold text-gray-900 text-sm leading-snug">{{ $complaint->respondent->name ?? 'N/A' }}</h4>
                        <p class="text-xs text-rose-500 font-semibold font-medium">Terlapor (Respondent)</p>
                    </div>
                </div>
                <div class="text-xs text-gray-500 space-y-1.5 pt-2 border-t border-gray-50">
                    <p class="flex items-center gap-2"><i data-lucide="mail" class="w-3.5 h-3.5 text-gray-400"></i> {{ $complaint->respondent->email ?? '-' }}</p>
                    <p class="flex items-center gap-2"><i data-lucide="phone" class="w-3.5 h-3.5 text-gray-400"></i> {{ $complaint->respondent->phone_number ?? '-' }}</p>
                </div>
            </div>

            {{-- Transaction Details --}}
            @if($complaint->order)
                <div class="bg-white border border-gray-200 rounded-2xl p-5 shadow-sm space-y-4">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Sengketa Transaksi</span>
                    <div>
                        <p class="text-xs text-gray-400">Kode Pesanan:</p>
                        <p class="font-mono font-bold text-gray-900 mt-0.5">{{ $complaint->order->order_code }}</p>
                    </div>
                    <div class="grid grid-cols-2 gap-3 text-xs pt-2 border-t border-gray-50">
                        <div>
                            <p class="text-gray-400">Total Nominal:</p>
                            <p class="font-bold text-gray-800 mt-0.5">Rp {{ number_format((float)($complaint->order->total_amount ?? 0), 0, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-gray-400">Status Pesanan:</p>
                            <p class="font-bold text-gray-800 mt-0.5">{{ strtoupper($complaint->order->order_status) }}</p>
                        </div>
                    </div>
                </div>
            @endif

        </div>

    </div>
</div>

<script>
    function showResolutionForm(type) {
        hideResolutionForms();
        if (type === 'resolve') {
            document.getElementById('resolve-form-container').classList.remove('hidden');
        } else if (type === 'reject') {
            document.getElementById('reject-form-container').classList.remove('hidden');
        }
    }

    function hideResolutionForms() {
        document.getElementById('resolve-form-container').classList.add('hidden');
        document.getElementById('reject-form-container').classList.add('hidden');
    }

    function showAppealForm(type) {
        hideAppealForms();
        if(type === 'accept') {
            document.getElementById('appeal-accept-form').classList.remove('hidden');
        } else {
            document.getElementById('appeal-reject-form').classList.remove('hidden');
        }
    }

    function hideAppealForms() {
        document.getElementById('appeal-accept-form')?.classList.add('hidden');
        document.getElementById('appeal-reject-form')?.classList.add('hidden');
    }

    // Scroll chat to bottom
    const chatContainer = document.getElementById('chat-container');
    if (chatContainer) {
        chatContainer.scrollTop = chatContainer.scrollHeight;
    }
</script>
@endsection
