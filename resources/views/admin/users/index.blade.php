@extends('admin.layouts.admin')

@section('title', 'Manajemen Pengguna - Recyclink')
@section('header_title', 'Manajemen Pengguna')

@section('content')
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h3 class="text-2xl font-bold text-gray-900">Daftar Pengguna</h3>
            <p class="text-gray-600 mt-1">Kelola semua akun pengguna yang terdaftar di Recyclink.</p>
        </div>
        
        <!-- Search & Filter Placeholder -->
        <div class="flex items-center gap-3">
            <div class="relative">
                <i data-lucide="search" class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                <input type="text" placeholder="Cari pengguna..." class="pl-10 pr-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-brand focus:border-brand outline-none text-sm w-full sm:w-64">
            </div>
        </div>
    </div>

    
    

    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50/50 border-b border-gray-200 text-gray-900 font-semibold">
                    <tr>
                        <th class="px-6 py-4">Pengguna</th>
                        <th class="px-6 py-4">Peran (Role)</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Tanggal Bergabung</th>
                        <th class="px-6 py-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    @php
                                        $avatar = $user->avatar ? asset('storage/'.$user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($user->name).'&background=7A9C59&color=fff';
                                    @endphp
                                    <img src="{{ $avatar }}" alt="{{ $user->name }}" class="w-10 h-10 rounded-full object-cover border border-gray-200">
                                    <div>
                                        <h4 class="font-bold text-gray-900">{{ $user->name }}</h4>
                                        <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if($user->hasRole('admin'))
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-bold bg-purple-50 text-purple-700 border border-purple-100">
                                        Admin
                                    </span>
                                @elseif($user->hasRole('seller'))
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-bold bg-blue-50 text-blue-700 border border-blue-100">
                                        UMKM / Penjual
                                    </span>
                                @elseif($user->hasRole('buyer'))
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-bold bg-amber-50 text-amber-700 border border-amber-100">
                                        Pembeli
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-600">
                                        Belum Pilih Peran
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($user->status === 'active')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                        Aktif
                                    </span>
                                @elseif($user->status === 'suspended')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-red-50 text-red-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                                        Ditangguhkan
                                    </span>
                                @elseif($user->status === 'pending')
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        Menunggu
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-bold bg-gray-100 text-gray-700">
                                        <span class="w-1.5 h-1.5 rounded-full bg-gray-500"></span>
                                        Tidak Aktif
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-gray-500">
                                {{ $user->created_at->format('d M Y') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    
                                    <!-- Optional: Toggle Status Modal/Form -->
                                    @php
                                        $isSuperAdmin = $user->email === 'admin@recyclink.id';
                                        $isAdminUtama = $user->email === 'admin@recyclink.com';
                                        $currentUserIsSuperAdmin = auth()->user()->email === 'admin@recyclink.id';
                                        $isSelf = $user->id === auth()->id();
                                        
                                        $canUpdateStatus = !($isSuperAdmin || $isAdminUtama);
                                        
                                        $canDelete = false;
                                        if (!$isSelf && !$isSuperAdmin) {
                                            if ($isAdminUtama) {
                                                $canDelete = $currentUserIsSuperAdmin;
                                            } else {
                                                $canDelete = true;
                                            }
                                        }
                                    @endphp
                                    
                                    @if($canUpdateStatus)
                                        <!-- Status actions moved to modal -->
                                    @else
                                        <!-- Placeholder to maintain spacing -->
                                        <div class="w-8 inline-block"></div>
                                    @endif

                                    <!-- Button for detail view modal -->
                                    <button type="button" class="p-2 text-brand hover:bg-brand/10 rounded-lg transition-colors tooltip btn-view-user" title="Lihat Detail"
                                        data-name="{{ $user->name }}"
                                        data-email="{{ $user->email }}"
                                        data-phone="{{ $user->phone_number ?? '-' }}"
                                        data-role="{{ $user->roles->first()->name ?? 'Tidak ada' }}"
                                        data-joined="{{ $user->created_at->format('d M Y, H:i') }}"
                                        data-status="{{ ucfirst($user->status) }}"
                                        data-raw-status="{{ $user->status }}"
                                        data-reason="{{ $user->rejection_reason ?? '-' }}"
                                        data-can-update-status="{{ $canUpdateStatus ? 'true' : 'false' }}"
                                        data-user-id="{{ $user->id }}"
                                        @if($user->hasRole('buyer') && $user->buyerProfile)
                                            data-profile-type="buyer"
                                            data-company="{{ $user->buyerProfile->company_name ?? '-' }}"
                                            data-industry="{{ $user->buyerProfile->industry_type ?? '-' }}"
                                            data-address="{{ $user->buyerProfile->address ?? '-' }}"
                                            data-city="{{ $user->buyerProfile->city ?? '-' }}"
                                            data-province="{{ $user->buyerProfile->province ?? '-' }}"
                                            data-zip="{{ $user->buyerProfile->postal_code ?? '-' }}"
                                        @elseif($user->hasRole('seller') && $user->sellerProfile)
                                            data-profile-type="seller"
                                            data-business="{{ $user->sellerProfile->business_name ?? '-' }}"
                                            data-btype="{{ $user->sellerProfile->business_type ?? '-' }}"
                                            data-address="{{ $user->sellerProfile->address ?? '-' }}"
                                            data-city="{{ $user->sellerProfile->city ?? '-' }}"
                                            data-province="{{ $user->sellerProfile->province ?? '-' }}"
                                            data-zip="{{ $user->sellerProfile->postal_code ?? '-' }}"
                                            data-lat="{{ $user->sellerProfile->latitude ?? '-' }}"
                                            data-lng="{{ $user->sellerProfile->longitude ?? '-' }}"
                                            data-verification-status="{{ $user->sellerProfile->verification_status ?? '-' }}"
                                            data-user-id="{{ $user->id }}"
                                        @else
                                            data-profile-type="none"
                                        @endif
                                    >
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </button>
                                    @if($canDelete)
                                        <!-- Delete Button -->
                                        <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST" class="inline-block delete-user-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors tooltip btn-delete-user" title="Hapus Permanen">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    @else
                                        <!-- Placeholder to maintain spacing -->
                                        <div class="w-8 inline-block"></div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                        <i data-lucide="users" class="w-8 h-8 text-gray-400"></i>
                                    </div>
                                    <h5 class="text-gray-900 font-medium">Belum Ada Pengguna</h5>
                                    <p class="text-sm text-gray-500 mt-1">Data pengguna akan muncul di sini.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $users->links() }}
            </div>
        @endif
    </div>

<!-- User Detail Modal -->
<div id="userDetailModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4">
    <!-- Backdrop -->
    <div id="modalBackdrop" class="absolute inset-0 bg-gray-900/50 backdrop-blur-sm opacity-0 transition-opacity duration-300"></div>
    
    <!-- Modal Content -->
    <div id="modalContent" class="bg-white rounded-2xl shadow-xl w-full max-w-2xl transform scale-95 opacity-0 transition-all duration-300 relative z-10 max-h-[90vh] flex flex-col">
        <!-- Close Button -->
        <button type="button" id="closeModalBtn" class="absolute top-4 right-4 p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-xl transition-colors">
            <i data-lucide="x" class="w-5 h-5"></i>
        </button>
        
        <div class="p-6 border-b border-gray-100 flex items-center gap-4 shrink-0">
            <div class="w-12 h-12 rounded-xl bg-brand/10 text-brand flex items-center justify-center shrink-0">
                <i data-lucide="user" class="w-6 h-6"></i>
            </div>
            <div>
                <h3 class="text-xl font-bold text-gray-900" id="modalUserName">Nama Pengguna</h3>
                <p class="text-sm text-gray-500" id="modalUserEmail">email@example.com</p>
            </div>
            <div class="ml-auto mr-8">
                <span id="modalUserStatus" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">Status</span>
            </div>
        </div>
        
        <div class="p-6 overflow-y-auto">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <!-- Basic Info -->
                <div>
                    <h4 class="text-sm font-bold text-gray-900 mb-4 uppercase tracking-wider">Data Akun Utama</h4>
                    <div class="space-y-4">
                        <div>
                            <p class="text-xs text-gray-500 mb-1">No. WhatsApp</p>
                            <p class="text-sm font-medium text-gray-900" id="modalUserPhone">-</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Peran (Role)</p>
                            <p class="text-sm font-medium text-gray-900" id="modalUserRole">-</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 mb-1">Tanggal Bergabung</p>
                            <p class="text-sm font-medium text-gray-900" id="modalUserJoined">-</p>
                        </div>
                        <div id="modalReasonContainer" class="hidden">
                            <p class="text-xs text-red-500 mb-1 font-bold">Catatan / Alasan</p>
                            <p class="text-sm font-medium text-red-700 bg-red-50 p-3 rounded-xl border border-red-100" id="modalUserReason">-</p>
                        </div>
                    </div>
                </div>

                <!-- Profile Info -->
                <div id="modalProfileSection">
                    <!-- Dynamic content will be injected here -->
                </div>
            </div>
        </div>
        
        <!-- Action Button Container (Full Width Bottom) -->
        <div id="modalActionContainer" class="hidden border-t border-gray-100 p-6 bg-gray-50/50 rounded-b-2xl">
            <!-- Dynamic button will be injected here -->
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('userDetailModal');
        const backdrop = document.getElementById('modalBackdrop');
        const content = document.getElementById('modalContent');
        const closeBtn = document.getElementById('closeModalBtn');
        const profileSection = document.getElementById('modalProfileSection');
        
        function openModal() {
            modal.classList.remove('hidden');
            // small delay for transition
            setTimeout(() => {
                backdrop.classList.remove('opacity-0');
                backdrop.classList.add('opacity-100');
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10);
        }
        
        function closeModal() {
            backdrop.classList.remove('opacity-100');
            backdrop.classList.add('opacity-0');
            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 300);
        }
        
        closeBtn.addEventListener('click', closeModal);
        backdrop.addEventListener('click', closeModal);
        
        document.querySelectorAll('.btn-view-user').forEach(button => {
            button.addEventListener('click', function() {
                // Populate data
                document.getElementById('modalUserName').textContent = this.dataset.name;
                document.getElementById('modalUserEmail').textContent = this.dataset.email;
                document.getElementById('modalUserPhone').textContent = this.dataset.phone;
                document.getElementById('modalUserRole').textContent = this.dataset.role;
                document.getElementById('modalUserJoined').textContent = this.dataset.joined;
                
                const reasonText = this.dataset.reason;
                const reasonContainer = document.getElementById('modalReasonContainer');
                const reasonEl = document.getElementById('modalUserReason');
                if (reasonText && reasonText !== '-') {
                    reasonEl.textContent = reasonText;
                    reasonContainer.classList.remove('hidden');
                } else {
                    reasonContainer.classList.add('hidden');
                }
                
                const statusSpan = document.getElementById('modalUserStatus');
                statusSpan.textContent = this.dataset.status;
                statusSpan.className = 'inline-flex items-center px-3 py-1 rounded-full text-xs font-medium';
                if (this.dataset.status.toLowerCase() === 'active') {
                    statusSpan.classList.add('bg-emerald-100', 'text-emerald-700');
                } else if (this.dataset.status.toLowerCase() === 'suspended') {
                    statusSpan.classList.add('bg-red-100', 'text-red-700');
                } else {
                    statusSpan.classList.add('bg-amber-100', 'text-amber-700');
                }
                
                const type = this.dataset.profileType;
                let html = '';
                
                if (type === 'buyer') {
                    html = `
                        <h4 class="text-sm font-bold text-gray-900 mb-4 uppercase tracking-wider">Profil Pembeli</h4>
                        <div class="space-y-4">
                            <div><p class="text-xs text-gray-500 mb-1">Nama Perusahaan</p><p class="text-sm font-medium text-gray-900">${this.dataset.company}</p></div>
                            <div><p class="text-xs text-gray-500 mb-1">Jenis Industri</p><p class="text-sm font-medium text-gray-900">${this.dataset.industry}</p></div>
                            <div><p class="text-xs text-gray-500 mb-1">Alamat Lengkap</p><p class="text-sm font-medium text-gray-900">${this.dataset.address}</p></div>
                            <div><p class="text-xs text-gray-500 mb-1">Kota / Provinsi</p><p class="text-sm font-medium text-gray-900">${this.dataset.city}, ${this.dataset.province}</p></div>
                            <div><p class="text-xs text-gray-500 mb-1">Kode Pos</p><p class="text-sm font-medium text-gray-900">${this.dataset.zip}</p></div>
                        </div>
                    `;
                } else if (type === 'seller') {
                    let verificationBadge = '';
                    if (this.dataset.verificationStatus === 'pending') {
                        verificationBadge = '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-amber-100 text-amber-800 whitespace-nowrap uppercase tracking-normal">Menunggu Verifikasi</span>';
                    } else if (this.dataset.verificationStatus === 'verified') {
                        verificationBadge = '<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-emerald-100 text-emerald-800 whitespace-nowrap uppercase tracking-normal">Terverifikasi</span>';
                    }
                    
                    html = `
                        <div class="flex flex-col gap-2 mb-5">
                            <h4 class="text-sm font-bold text-gray-900 uppercase tracking-wider">
                                Profil Penjual
                            </h4>
                            <div>${verificationBadge}</div>
                        </div>
                        <div class="space-y-4">
                            <div><p class="text-xs text-gray-500 mb-1">Nama Usaha/Pengepul</p><p class="text-sm font-medium text-gray-900">${this.dataset.business}</p></div>
                            <div><p class="text-xs text-gray-500 mb-1">Tipe Usaha</p><p class="text-sm font-medium text-gray-900">${this.dataset.btype}</p></div>
                            <div><p class="text-xs text-gray-500 mb-1">Alamat Lengkap</p><p class="text-sm font-medium text-gray-900">${this.dataset.address}</p></div>
                            <div><p class="text-xs text-gray-500 mb-1">Kota / Provinsi</p><p class="text-sm font-medium text-gray-900">${this.dataset.city}, ${this.dataset.province}</p></div>
                            <div><p class="text-xs text-gray-500 mb-1">Kode Pos</p><p class="text-sm font-medium text-gray-900">${this.dataset.zip}</p></div>
                            <div>
                                <p class="text-xs text-gray-500 mb-1">Titik Lokasi (Maps)</p>
                                <p class="text-sm font-medium text-gray-900">
                                    ${this.dataset.lat !== '-' && this.dataset.lng !== '-' ? 
                                        `<a href="https://www.google.com/maps/search/?api=1&query=${this.dataset.lat},${this.dataset.lng}" target="_blank" class="text-blue-600 hover:underline flex items-center gap-1"><i data-lucide="map" class="w-4 h-4"></i> ${this.dataset.lat}, ${this.dataset.lng}</a>` 
                                        : 'Belum diatur'
                                    }
                                </p>
                            </div>
                        </div>
                    `;
                } else {
                    html = `<div class="flex items-center justify-center h-full bg-gray-50 rounded-xl border border-dashed border-gray-200 p-8"><p class="text-sm text-gray-500 text-center">Tidak ada data profil spesifik.</p></div>`;
                }
                profileSection.innerHTML = html;

                const actionContainer = document.getElementById('modalActionContainer');
                actionContainer.innerHTML = '';
                
                let showAction = false;
                if (this.dataset.canUpdateStatus === 'true') {
                    if (this.dataset.rawStatus === 'pending') {
                        actionContainer.innerHTML = `
                            <div class="flex gap-4">
                                <form action="/dashboard/admin/users/${this.dataset.userId}/status" method="POST" class="flex-1 form-reject-user">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="inactive">
                                    <input type="hidden" name="rejection_reason" class="rejection-reason-input" value="">
                                    <button type="button" class="w-full px-4 py-3 bg-red-100 text-red-700 font-bold rounded-xl hover:bg-red-200 transition-all flex items-center justify-center gap-2 btn-reject-user shadow-sm">
                                        <i data-lucide="x-circle" class="w-5 h-5"></i>
                                        Tolak Pendaftaran
                                    </button>
                                </form>
                                <form action="/dashboard/admin/users/${this.dataset.userId}/status" method="POST" class="flex-1">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="status" value="active">
                                    <button type="submit" class="w-full px-4 py-3 bg-[#719149] text-white font-bold rounded-xl hover:bg-[#607d3c] transition-all flex items-center justify-center gap-2 shadow-sm">
                                        <i data-lucide="check-circle" class="w-5 h-5"></i>
                                        Terima (Verifikasi)
                                    </button>
                                </form>
                            </div>
                        `;
                        showAction = true;
                        
                        // Re-bind sweetalert event listener for reject
                        setTimeout(() => {
                            const rejectBtn = actionContainer.querySelector('.btn-reject-user');
                            if (rejectBtn) {
                                rejectBtn.addEventListener('click', function() {
                                    const form = this.closest('form');
                                    Swal.fire({
                                        title: 'Tolak Pendaftaran?',
                                        text: 'Apakah Anda yakin ingin menolak pengguna ini?',
                                        icon: 'warning',
                                        input: 'textarea',
                                        inputLabel: 'Alasan Penolakan',
                                        inputPlaceholder: 'Tulis alasan penolakan di sini agar pengguna dapat memperbaiki profilnya...',
                                        inputAttributes: {
                                            'aria-label': 'Alasan penolakan'
                                        },
                                        showCancelButton: true,
                                        confirmButtonColor: '#ef4444',
                                        cancelButtonColor: '#6b7280',
                                        confirmButtonText: 'Tolak',
                                        cancelButtonText: 'Batal',
                                        customClass: {
                                            popup: 'rounded-2xl shadow-xl',
                                            confirmButton: 'rounded-xl',
                                            cancelButton: 'rounded-xl',
                                            input: 'rounded-xl p-3 border-gray-300 focus:border-brand focus:ring-brand/20'
                                        },
                                        preConfirm: (reason) => {
                                            if (!reason) {
                                                Swal.showValidationMessage('Alasan penolakan harus diisi')
                                            }
                                            return reason;
                                        }
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            form.querySelector('.rejection-reason-input').value = result.value;
                                            form.submit();
                                        }
                                    });
                                });
                            }
                        }, 100);
                        
                    } else if (this.dataset.rawStatus === 'active') {
                        actionContainer.innerHTML = `
                            <form action="/dashboard/admin/users/${this.dataset.userId}/status" method="POST" class="w-full form-suspend-user">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="suspended">
                                <input type="hidden" name="rejection_reason" class="suspension-reason-input" value="">
                                <button type="button" class="w-full px-4 py-3 bg-red-50 text-red-600 font-bold rounded-xl hover:bg-red-100 transition-all flex items-center justify-center gap-2 btn-suspend-user shadow-sm">
                                    <i data-lucide="ban" class="w-5 h-5"></i>
                                    Tangguhkan Akun
                                </button>
                            </form>
                        `;
                        showAction = true;
                        
                        setTimeout(() => {
                            const suspendBtn = actionContainer.querySelector('.btn-suspend-user');
                            if (suspendBtn) {
                                suspendBtn.addEventListener('click', function() {
                                    const form = this.closest('form');
                                    Swal.fire({
                                        title: 'Tangguhkan Akun?',
                                        text: 'Apakah Anda yakin ingin menangguhkan akun pengguna ini?',
                                        icon: 'warning',
                                        input: 'textarea',
                                        inputLabel: 'Alasan Penangguhan',
                                        showCancelButton: true,
                                        confirmButtonColor: '#ef4444',
                                        cancelButtonColor: '#6b7280',
                                        confirmButtonText: 'Tangguhkan',
                                        cancelButtonText: 'Batal',
                                        customClass: {
                                            popup: 'rounded-2xl shadow-xl',
                                            confirmButton: 'rounded-xl',
                                            cancelButton: 'rounded-xl'
                                        }
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            form.querySelector('.suspension-reason-input').value = result.value || 'Melanggar ketentuan layanan';
                                            form.submit();
                                        }
                                    });
                                });
                            }
                        }, 100);
                    }
                }
                
                if (showAction) {
                    actionContainer.classList.remove('hidden');
                } else {
                    actionContainer.classList.add('hidden');
                }
                
                // Re-initialize lucide icons for the new content in action container
                if (window.lucide) {
                    lucide.createIcons();
                }
                
                openModal();
            });
        });

        document.querySelectorAll('.btn-reject-user').forEach(button => {
            button.addEventListener('click', function() {
                const form = this.closest('form');
                const reasonInput = form.querySelector('.rejection-reason-input');
                
                Swal.fire({
                    title: 'Tolak Pendaftaran?',
                    text: 'Apakah Anda yakin ingin menolak pendaftaran pengguna ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    showCloseButton: true,
                    scrollbarPadding: false,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Tolak',
                    cancelButtonText: 'Batal',
                    customClass: {
                        popup: 'rounded-2xl shadow-xl',
                        confirmButton: 'rounded-xl',
                        cancelButton: 'rounded-xl'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
        
        document.querySelectorAll('.btn-suspend-user').forEach(button => {
            button.addEventListener('click', function() {
                const form = this.closest('form');
                const reasonInput = form.querySelector('.suspension-reason-input');
                
                Swal.fire({
                    title: 'Tangguhkan Akun?',
                    text: 'Apakah Anda yakin ingin menangguhkan akun pengguna ini?',
                    icon: 'warning',
                    showCancelButton: true,
                    showCloseButton: true,
                    scrollbarPadding: false,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Tangguhkan',
                    cancelButtonText: 'Batal',
                    customClass: {
                        popup: 'rounded-2xl shadow-xl',
                        confirmButton: 'rounded-xl',
                        cancelButton: 'rounded-xl'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
        
        document.querySelectorAll('.btn-delete-user').forEach(button => {
            button.addEventListener('click', function() {
                const form = this.closest('form');
                Swal.fire({
                    title: 'Hapus Pengguna?',
                    text: "Apakah Anda yakin ingin menghapus permanen pengguna ini? Aksi ini tidak dapat dibatalkan.",
                    icon: 'warning',
                    showCancelButton: true,
                    showCloseButton: true,
                    scrollbarPadding: false,
                    confirmButtonColor: '#ef4444',
                    cancelButtonColor: '#6b7280',
                    confirmButtonText: 'Ya, Hapus!',
                    cancelButtonText: 'Batal',
                    customClass: {
                        popup: 'rounded-2xl shadow-xl',
                        confirmButton: 'rounded-xl',
                        cancelButton: 'rounded-xl'
                    },
                    heightAuto: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>
@endpush
