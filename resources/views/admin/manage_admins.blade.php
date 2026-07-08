@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
<style>
    .admin-card {
        border-radius: 16px;
        border: 1px solid rgba(0,0,0,0.06);
        background: #fff;
        transition: all 0.2s ease;
        overflow: hidden;
    }
    .admin-card:hover {
        border-color: rgba(0,0,0,0.1);
        box-shadow: 0 8px 24px rgba(0,0,0,0.06);
        transform: translateY(-2px);
    }
    .avatar-circle {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        color: #f59e0b;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.2rem;
        flex-shrink: 0;
    }
    .avatar-circle-modal {
        width: 50px;
        height: 50px;
        background: rgba(255, 255, 255, 0.15);
        color: #f59e0b;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.25rem;
        border: 2px solid rgba(255,255,255,0.25);
    }
    .icon-square {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        flex-shrink: 0;
    }
    .permission-card {
        transition: all 0.2s ease-in-out;
    }
    .permission-card:hover {
        border-color: #cbd5e1 !important;
        box-shadow: 0 5px 12px rgba(0, 0, 0, 0.03);
        transform: translateY(-1.5px);
    }
    .hover-gold:hover {
        background-color: #d97706 !important;
        border-color: #d97706 !important;
        color: #ffffff !important;
    }
    .hover-underline:hover {
        text-decoration: underline !important;
    }
    .form-check-input:checked {
        background-color: #d97706 !important;
        border-color: #d97706 !important;
    }
    .perm-progress {
        height: 4px;
        border-radius: 2px;
        background: #f1f5f9;
        overflow: hidden;
    }
    .perm-progress-bar {
        height: 100%;
        border-radius: 2px;
        background: linear-gradient(90deg, #f59e0b, #d97706);
        transition: width 0.4s ease;
    }
    .admin-action-btn {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1px solid rgba(0,0,0,0.08);
        background: #f8fafc;
        color: #64748b;
        transition: all 0.15s ease;
        font-size: 0.85rem;
    }
    .admin-action-btn:hover {
        background: #f1f5f9;
        border-color: rgba(0,0,0,0.12);
        color: #334155;
    }
    .admin-action-btn.btn-del:hover {
        background: #fef2f2;
        border-color: #fca5a5;
        color: #dc2626;
    }
    .stat-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 12px;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 600;
        background: #fef3c7;
        color: #92400e;
    }
    @media (max-width: 575.98px) {
        .admin-card-body {
            padding: 16px !important;
        }
        .avatar-circle {
            width: 42px;
            height: 42px;
            font-size: 1.05rem;
            border-radius: 12px;
        }
    }
</style>

    {{-- Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1" style="font-size: 1.6rem; letter-spacing: -0.5px;">
                <i class="bi bi-person-gear text-warning me-2" style="font-size: 1.4rem;"></i>Kelola Akun Admin
            </h2>
            <p class="text-secondary mb-0" style="font-size: 0.85rem;">
                Atur otorisasi halaman untuk setiap akun admin secara instan menggunakan switch aktif/nonaktif.
            </p>
        </div>
        <button class="btn btn-warning fw-bold px-4 py-2 rounded-pill shadow-sm text-dark hover-gold flex-shrink-0" data-bs-toggle="modal" data-bs-target="#addAdminModal" style="font-size: 0.85rem;">
            <i class="bi bi-person-plus-fill me-1"></i> Tambah Staf Admin
        </button>
    </div>

    {{-- Validation and Feedback --}}
    @if ($errors->any())
        <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4 py-3 small">
            @foreach ($errors->all() as $error)
                <div class="d-flex align-items-center mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center py-3">
            <i class="bi bi-check-circle-fill me-2 fs-5 text-success"></i>
            <div class="fw-semibold">{{ session('success') }}</div>
        </div>
    @endif

    {{-- Stats Summary Bar --}}
    <div class="d-flex align-items-center gap-3 mb-4 flex-wrap">
        <div class="stat-pill">
            <i class="bi bi-people-fill"></i> {{ count($admins) }} Total Admin
        </div>
        <div class="stat-pill" style="background: #f0fdf4; color: #166534;">
            <i class="bi bi-shield-check"></i> 12 Modul Izin Tersedia
        </div>
    </div>

    {{-- Admin Cards Grid --}}
    <div class="row g-3">
        @forelse($admins as $index => $admin)
            @php
                $userPerms = $admin->permissions;
                if (!is_array($userPerms)) {
                    $userPerms = json_decode($userPerms, true) ?: [];
                }
                $activeCount = count($userPerms);
                $permPercent = round(($activeCount / 12) * 100);
            @endphp
            <div class="col-12 col-md-6 col-xl-4">
                <div class="admin-card h-100">
                    <div class="admin-card-body p-4">
                        {{-- Top row: Avatar + Info + Actions --}}
                        <div class="d-flex align-items-start justify-content-between mb-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="avatar-circle">
                                    {{ strtoupper(substr($admin->name, 0, 1)) }}
                                </div>
                                <div>
                                    <h6 class="fw-bold text-dark mb-0" style="font-size: 0.95rem;">{{ $admin->name }}</h6>
                                    <span class="text-secondary" style="font-size: 0.78rem;">@{{ $admin->username }}</span>
                                </div>
                            </div>
                            <div class="d-flex gap-1">
                                <button class="admin-action-btn" title="Edit" data-bs-toggle="modal" data-bs-target="#editAdminModal{{ $admin->id }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <a href="{{ route('admin.manage.delete', $admin->id) }}" 
                                   class="admin-action-btn btn-del" title="Hapus"
                                   onclick="return confirm('Apakah Anda yakin ingin menghapus akun admin {{ $admin->username }}? Hapus akun tidak dapat dibatalkan.');">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>
                        </div>

                        {{-- Email --}}
                        <div class="d-flex align-items-center gap-2 mb-3 text-secondary" style="font-size: 0.8rem;">
                            <i class="bi bi-envelope text-muted"></i>
                            <span class="text-truncate">{{ $admin->email }}</span>
                        </div>

                        {{-- Permission Progress --}}
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-secondary fw-semibold" style="font-size: 0.72rem; text-transform: uppercase; letter-spacing: 0.5px;">Hak Akses</span>
                                <span class="fw-bold" style="font-size: 0.78rem; color: {{ $activeCount >= 10 ? '#16a34a' : ($activeCount >= 5 ? '#d97706' : '#64748b') }};">{{ $activeCount }}/12</span>
                            </div>
                            <div class="perm-progress">
                                <div class="perm-progress-bar" style="width: {{ $permPercent }}%;"></div>
                            </div>
                        </div>

                        {{-- Footer: Date + Atur Izin Button --}}
                        <div class="d-flex justify-content-between align-items-center pt-2" style="border-top: 1px solid #f1f5f9;">
                            <span class="text-muted" style="font-size: 0.72rem;">
                                <i class="bi bi-calendar3 me-1"></i>{{ $admin->created_at->format('d M Y') }}
                            </span>
                            <button class="btn btn-sm btn-outline-warning fw-bold rounded-pill px-3 py-1 text-dark" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#permissionsModal{{ $admin->id }}"
                                    style="font-size: 0.75rem; border-width: 1.5px;">
                                <i class="bi bi-shield-lock me-1"></i>Atur Izin
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Permissions Management Modal --}}
            <div class="modal fade" id="permissionsModal{{ $admin->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">
                        <div class="modal-header border-0 text-white px-4 py-4" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
                            <div class="d-flex align-items-center">
                                <div class="avatar-circle-modal me-3">
                                    {{ strtoupper(substr($admin->name, 0, 1)) }}
                                </div>
                                <div class="text-start">
                                    <h5 class="modal-title fw-bold text-white mb-0">Atur Hak Akses</h5>
                                    <p class="text-white-50 mb-0 small">{{ $admin->name }} ({{ $admin->email }})</p>
                                </div>
                            </div>
                            <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4 bg-light" style="max-height: 65vh; overflow-y: auto;">
                            <p class="text-secondary small mb-4">
                                <i class="bi bi-info-circle me-1"></i> Perubahan izin di bawah ini disinkronkan secara instan ke server. Staf admin terkait akan segera dibatasi atau diizinkan akses setelah tombol diubah.
                            </p>
                            
                            <div class="row g-3">
                                @php
                                    $availablePerms = [
                                        'dashboard' => [
                                            'label' => 'Dashboard Utama',
                                            'desc' => 'Mengakses halaman beranda dashboard utama admin yang menampilkan ringkasan umum sistem turnamen, statistik pendaftaran cepat, dan rangkuman data kas.',
                                            'icon' => 'bi-grid-1x2',
                                            'color' => '#0f172a',
                                            'bg' => '#e2e8f0'
                                        ],
                                        'seasons' => [
                                            'label' => 'Daftar Season', 
                                            'desc' => 'Mengakses menu Daftar Season, membuat season baru, mengubah detail season, dan mengelola tim pendaftar.',
                                            'icon' => 'bi-trophy', 
                                            'color' => '#d97706',
                                            'bg' => '#fef3c7'
                                        ],
                                        'teams' => [
                                            'label' => 'Daftar Team (Super)',
                                            'desc' => 'Melihat list global semua tim, memfilter berdasarkan status pembayaran, mempermudah pelacakan tim.',
                                            'icon' => 'bi-people-fill',
                                            'color' => '#06b6d4',
                                            'bg' => '#ecfeff'
                                        ],
                                        'payments' => [
                                            'label' => 'Riwayat Pembayaran (Super)',
                                            'desc' => 'Mengakses mutasi log transaksi TriPay, sinkronisasi gateway, serta dashboard kelola QRIS manual (antrean klaim bukti transfer, setting biaya admin & kode unik, serta manual/force settle).',
                                            'icon' => 'bi-cash-stack',
                                            'color' => '#10b981',
                                            'bg' => '#d1fae5'
                                        ],
                                        'notes' => [
                                            'label' => 'Catatan Staf', 
                                            'desc' => 'Melihat, membuat, mengubah, dan menghapus catatan koordinasi internal antar administrator.',
                                            'icon' => 'bi-sticky', 
                                            'color' => '#8b5cf6',
                                            'bg' => '#ede9fe'
                                        ],
                                        'settings' => [
                                            'label' => 'Pengaturan Sistem (Super)',
                                            'desc' => 'Mengonfigurasi token API WhatsApp Fonnte, kredensial Tripay/iPaymu Gateway, email support, template notifikasi, nama metode pembayaran, serta mengakses log notifikasi webhook/callback gateway.',
                                            'icon' => 'bi-gear',
                                            'color' => '#ea580c',
                                            'bg' => '#ffedd5'
                                        ],
                                        'faqs' => [
                                            'label' => 'Kelola FAQ', 
                                            'desc' => 'Menambah, mengubah susunan urutan, dan menghapus daftar tanya jawab publik di halaman utama.',
                                            'icon' => 'bi-question-circle', 
                                            'color' => '#6b7280',
                                            'bg' => '#f3f4f6'
                                        ],
                                        'activity_log' => [
                                            'label' => 'Log Aktivitas', 
                                            'desc' => 'Melihat audit log rekaman jejak tindakan administratif seluruh administrator platform.',
                                            'icon' => 'bi-clock-history', 
                                            'color' => '#ec4899',
                                            'bg' => '#fce7f3'
                                        ],
                                        'manage' => [
                                            'label' => 'Kelola Admin (Super)',
                                            'desc' => 'Menambah, mengedit, menghapus akun admin, mengatur pembagian izin, mengelola penyimpanan server (Storage Manager), serta mengakses log error Laravel.',
                                            'icon' => 'bi-person-gear',
                                            'color' => '#3b82f6',
                                            'bg' => '#dbeafe'
                                        ],
                                        'backup' => [
                                            'label' => 'Backup Database (Super)',
                                            'desc' => 'Mengunduh file salinan struktur dan data SQL database platform secara langsung ke storage lokal.',
                                            'icon' => 'bi-database-down',
                                            'color' => '#ef4444',
                                            'bg' => '#fee2e2'
                                        ],
                                        'finance' => [
                                            'label' => 'Keuangan Ledger (Modul Season)', 
                                            'desc' => 'Mengakses data finansial per-season, menginput pemasukan manual/bulk, pengeluaran kas, serta melihat rekapitulasi cashflow.',
                                            'icon' => 'bi-currency-exchange', 
                                            'color' => '#22c55e',
                                            'bg' => '#f0fdf4'
                                        ],
                                        'solo_matchmaker' => [
                                            'label' => 'Solo Matchmaker (Modul Season)', 
                                            'desc' => 'Menggunakan modul penyusun tim otomatis untuk memproses pendaftar solo ke dalam tim-tim seimbang.',
                                            'icon' => 'bi-people', 
                                            'color' => '#6366f1',
                                            'bg' => '#e0e7ff'
                                        ],
                                    ];
                                @endphp

                                @foreach($availablePerms as $key => $pInfo)
                                    <div class="col-md-6">
                                        <div class="permission-card bg-white border border-light-subtle rounded-3 p-3 h-100 d-flex justify-content-between align-items-start">
                                            <div class="d-flex align-items-start me-3">
                                                <div class="icon-square me-3" style="background-color: {{ $pInfo['bg'] }}; color: {{ $pInfo['color'] }}; flex-shrink: 0;">
                                                    <i class="bi {{ $pInfo['icon'] }}"></i>
                                                </div>
                                                <div class="text-start">
                                                    <h6 class="fw-bold text-dark mb-1" style="font-size: 0.88rem;">{{ $pInfo['label'] }}</h6>
                                                    <p class="text-secondary mb-0" style="font-size: 0.74rem; line-height: 1.4;">{{ $pInfo['desc'] }}</p>
                                                </div>
                                            </div>
                                            <div class="form-check form-switch p-0 m-0 pt-1">
                                                <input class="form-check-input permission-switch shadow-none" 
                                                       type="checkbox" 
                                                       role="switch" 
                                                       id="perm_modal_{{ $admin->id }}_{{ $key }}"
                                                       data-admin-id="{{ $admin->id }}"
                                                       data-permission="{{ $key }}"
                                                       {{ in_array($key, $userPerms) ? 'checked' : '' }}
                                                       style="width: 2.3em; height: 1.15em; cursor: pointer;">
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="modal-footer border-0 bg-white px-4 py-3">
                            <button type="button" class="btn btn-dark rounded-pill px-4 fw-semibold shadow-sm text-white" data-bs-dismiss="modal">Tutup & Simpan</button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Edit Admin Modal --}}
            <div class="modal fade" id="editAdminModal{{ $admin->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 rounded-4 shadow-lg">
                        <form action="{{ route('admin.manage.update', $admin->id) }}" method="POST">
                            @csrf
                            <div class="modal-header border-bottom border-light px-4 py-3">
                                <h5 class="modal-title fw-bold text-dark"><i class="bi bi-pencil-square text-warning me-1"></i> Edit Akun Admin</h5>
                                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body p-4 text-start">
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-secondary text-uppercase mb-1" style="font-size: 0.7rem;">Nama Lengkap</label>
                                    <input type="text" name="name" class="form-control rounded-3 border-light-subtle shadow-none py-2 px-3" 
                                           value="{{ $admin->name }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-secondary text-uppercase mb-1" style="font-size: 0.7rem;">Username</label>
                                    <input type="text" name="username" class="form-control rounded-3 border-light-subtle shadow-none py-2 px-3" 
                                           value="{{ $admin->username }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-secondary text-uppercase mb-1" style="font-size: 0.7rem;">Email</label>
                                    <input type="email" name="email" class="form-control rounded-3 border-light-subtle shadow-none py-2 px-3" 
                                           value="{{ $admin->email }}" required>
                                </div>
                                <div class="mb-0">
                                    <label class="form-label small fw-bold text-secondary text-uppercase mb-1" style="font-size: 0.7rem;">Password Baru (Kosongkan jika tidak diubah)</label>
                                    <input type="password" name="password" class="form-control rounded-3 border-light-subtle shadow-none py-2 px-3" 
                                           placeholder="Minimal 6 karakter...">
                                </div>
                            </div>
                            <div class="modal-footer border-top border-light px-4 py-3">
                                <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold" data-bs-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold text-dark hover-gold">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card border-0 shadow-sm rounded-4 bg-white">
                    <div class="card-body py-5 text-center text-secondary">
                        <i class="bi bi-people fs-1 text-muted mb-3 d-block"></i>
                        Belum ada staf admin yang ditambahkan ke sistem.
                    </div>
                </div>
            </div>
        @endforelse
    </div>
</div>

{{-- Add Admin Modal --}}
<div class="modal fade" id="addAdminModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <form action="{{ route('admin.manage.store') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom border-light px-4 py-3">
                    <h5 class="modal-title fw-bold text-dark"><i class="bi bi-person-plus-fill text-warning me-1"></i> Tambah Akun Admin Baru</h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 text-start">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary text-uppercase mb-1" style="font-size: 0.7rem;">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control rounded-3 border-light-subtle shadow-none py-2 px-3" 
                               placeholder="Nama lengkap admin..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary text-uppercase mb-1" style="font-size: 0.7rem;">Username</label>
                        <input type="text" name="username" class="form-control rounded-3 border-light-subtle shadow-none py-2 px-3" 
                               placeholder="Username untuk login..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary text-uppercase mb-1" style="font-size: 0.7rem;">Email</label>
                        <input type="email" name="email" class="form-control rounded-3 border-light-subtle shadow-none py-2 px-3" 
                               placeholder="Alamat email aktif..." required>
                    </div>
                    <div class="mb-0">
                        <label class="form-label small fw-bold text-secondary text-uppercase mb-1" style="font-size: 0.7rem;">Password</label>
                        <input type="password" name="password" class="form-control rounded-3 border-light-subtle shadow-none py-2 px-3" 
                               placeholder="Minimal 6 karakter..." required>
                    </div>
                </div>
                <div class="modal-footer border-top border-light px-4 py-3">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold text-dark hover-gold">Simpan Akun</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Toast Container --}}
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1300;">
    <div id="permissionToast" class="toast align-items-center border-0 text-white rounded-3 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body d-flex align-items-center py-3">
                <i class="bi me-2 fs-5" id="toastIcon"></i>
                <span id="toastMessage" class="fw-semibold"></span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto shadow-none" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.permission-switch').forEach(switchEl => {
        switchEl.addEventListener('change', function() {
            const adminId = this.dataset.adminId;
            const permission = this.dataset.permission;
            const status = this.checked ? 1 : 0;
            
            this.disabled = true;
            
            fetch("{{ route('admin.manage.toggle-permission') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    admin_id: adminId,
                    permission: permission,
                    status: status
                })
            })
            .then(res => res.json())
            .then(data => {
                this.disabled = false;
                if (data.success) {
                    showToast('Berhasil', data.message || 'Izin berhasil diperbarui', 'success');
                    
                    // Live update progress bar on card
                    const card = document.querySelector(`[data-bs-target="#permissionsModal${adminId}"]`);
                    if (card && data.permissions) {
                        const adminCard = card.closest('.admin-card');
                        if (adminCard) {
                            const countEl = adminCard.querySelector('.perm-count');
                            const barEl = adminCard.querySelector('.perm-progress-bar');
                            if (countEl) countEl.textContent = `${data.permissions.length}/12`;
                            if (barEl) barEl.style.width = `${Math.round((data.permissions.length / 12) * 100)}%`;
                        }
                    }
                } else {
                    this.checked = !this.checked;
                    showToast('Gagal', data.message || 'Gagal memperbarui izin', 'danger');
                }
            })
            .catch(err => {
                this.disabled = false;
                this.checked = !this.checked;
                showToast('Gagal', 'Terjadi kesalahan jaringan', 'danger');
            });
        });
    });
});

function showToast(title, message, type) {
    const toastEl = document.getElementById('permissionToast');
    const toastMessage = document.getElementById('toastMessage');
    const toastIcon = document.getElementById('toastIcon');
    
    toastEl.classList.remove('bg-success', 'bg-danger');
    toastIcon.classList.remove('bi-check-circle-fill', 'bi-x-circle-fill');
    
    if (type === 'success') {
        toastEl.classList.add('bg-success');
        toastIcon.classList.add('bi-check-circle-fill');
    } else {
        toastEl.classList.add('bg-danger');
        toastIcon.classList.add('bi-x-circle-fill');
    }
    
    toastMessage.textContent = message;
    
    const toast = new bootstrap.Toast(toastEl, { delay: 3500 });
    toast.show();
}
</script>
@endsection
