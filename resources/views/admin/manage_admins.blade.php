@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4" style="background-color: #f8fafc; min-height: 100vh;">
    {{-- Header --}}
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <h2 class="fw-bold text-dark mb-1" style="font-size: 1.75rem; letter-spacing: -0.5px;">
                Kelola Akun Admin
            </h2>
            <p class="text-secondary mb-0" style="font-size: 0.9rem;">
                Atur otorisasi halaman untuk setiap akun admin secara instan menggunakan switch aktif/nonaktif.
            </p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <button class="btn btn-warning fw-bold px-4 py-2.5 rounded-pill shadow-sm text-dark hover-gold" data-bs-toggle="modal" data-bs-target="#addAdminModal">
                <i class="bi bi-person-plus-fill me-1"></i> Tambah Staf Admin
            </button>
        </div>
    </div>

    {{-- Validation and Feedback --}}
    @if ($errors->any())
        <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4 py-2.5 small">
            @foreach ($errors->all() as $error)
                <li class="list-unstyled"><i class="bi bi-exclamation-triangle-fill me-2"></i>{{ $error }}</li>
            @endforeach
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center py-2.5">
            <i class="bi bi-check-circle-fill me-2 fs-5 text-success"></i>
            <div class="fw-semibold">{{ session('success') }}</div>
        </div>
    @endif

    {{-- Tabel Admin Premium --}}
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0" style="font-size: 0.88rem;">
                    <thead class="bg-light text-secondary text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.8px; border-bottom: 1px solid #edf2f7;">
                        <tr>
                            <th class="py-3.5 px-4" style="width: 60px;">#</th>
                            <th class="py-3.5 px-3">Nama & Username</th>
                            <th class="py-3.5 px-3">Alamat Email</th>
                            <th class="py-3.5 px-3">Hak Akses Modul</th>
                            <th class="py-3.5 px-3">Tanggal Dibuat</th>
                            <th class="py-3.5 px-4 text-end" style="width: 220px;">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody class="text-dark">
                        @forelse($admins as $index => $admin)
                            @php
                                $userPerms = $admin->permissions;
                                if (!is_array($userPerms)) {
                                    $userPerms = json_decode($userPerms, true) ?: [];
                                }
                                $activeCount = count($userPerms);
                            @endphp
                            <tr style="border-bottom: 1px solid #f1f5f9;">
                                <td class="py-3.5 px-4 text-secondary fw-semibold">
                                    {{ $index + 1 }}
                                </td>
                                <td class="py-3.5 px-3">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle me-3">
                                            {{ strtoupper(substr($admin->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <span class="fw-bold text-dark d-block" style="font-size: 0.92rem;">{{ $admin->name }}</span>
                                            <span class="text-secondary small">Username: <code class="bg-light text-dark px-1.5 py-0.5 rounded">{{ $admin->username }}</code></span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3.5 px-3 text-secondary">
                                    <i class="bi bi-envelope-fill text-muted me-1.5"></i>{{ $admin->email }}
                                </td>
                                <td class="py-3.5 px-3">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-light text-dark border border-light-subtle rounded-pill px-3 py-2 fw-semibold" style="font-size: 0.78rem;">
                                            <i class="bi bi-shield-lock-fill text-warning me-1.5"></i>{{ $activeCount }} dari 12 Akses Aktif
                                        </span>
                                        <button class="btn btn-sm btn-link text-decoration-none fw-bold p-0 text-warning hover-underline" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#permissionsModal{{ $admin->id }}"
                                                style="font-size: 0.8rem;">
                                            Atur Izin <i class="bi bi-chevron-right ms-0.5"></i>
                                        </button>
                                    </div>
                                </td>
                                <td class="py-3.5 px-3 text-secondary" style="font-size: 0.8rem;">
                                    <div>{{ $admin->created_at->format('d M Y') }}</div>
                                    <div class="text-muted" style="font-size: 0.75rem;">{{ $admin->created_at->format('H:i') }} WIB</div>
                                </td>
                                <td class="py-3.5 px-4 text-end">
                                    <div class="d-flex justify-content-end gap-1.5">
                                        <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 border-light-subtle shadow-sm" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editAdminModal{{ $admin->id }}">
                                            <i class="bi bi-pencil me-1"></i> Edit
                                        </button>
                                        <a href="{{ route('admin.manage.delete', $admin->id) }}" 
                                           class="btn btn-sm btn-outline-danger rounded-pill px-3 border-light-subtle shadow-sm" 
                                           onclick="return confirm('Apakah Anda yakin ingin menghapus akun admin {{ $admin->username }}? Hapus akun tidak dapat dibatalkan.');">
                                            <i class="bi bi-trash me-1"></i> Hapus
                                        </a>
                                    </div>
                                </td>
                            </tr>

                            {{-- Permissions Management Modal (Dedicated, Premium Look) --}}
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
                                                <i class="bi bi-info-circle me-1.5"></i> Perubahan izin di bawah ini disinkronkan secara instan ke server. Staf admin terkait akan segera dibatasi atau diizinkan akses setelah tombol diubah.
                                            </p>
                                            
                                            <div class="row g-3">
                                                @php
                                                    $availablePerms = [
                                                        'dashboard' => [
                                                            'label' => 'Dashboard Utama',
                                                            'desc' => 'Mengakses ringkasan umum sistem turnamen, statistik cepat pendaftaran, dan data ringkas mutasi kas.',
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
                                                            'desc' => 'Mengakses mutasi log transaksi TriPay gateway secara live, status MDR/biaya flat, dan sinkronisasi manual.',
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
                                                            'desc' => 'Mengonfigurasi token API WhatsApp Fonnte, kredensial Tripay Gateway, email support, dan template notifikasi.',
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
                                                            'desc' => 'Menambah, mengedit informasi, menghapus akun admin, dan mengatur pembagian hak akses ini.',
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
                                                    <input type="text" name="name" class="form-control rounded-3 border-light-subtle shadow-none p-2.5" 
                                                           value="{{ $admin->name }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold text-secondary text-uppercase mb-1" style="font-size: 0.7rem;">Username</label>
                                                    <input type="text" name="username" class="form-control rounded-3 border-light-subtle shadow-none p-2.5" 
                                                           value="{{ $admin->username }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold text-secondary text-uppercase mb-1" style="font-size: 0.7rem;">Email</label>
                                                    <input type="email" name="email" class="form-control rounded-3 border-light-subtle shadow-none p-2.5" 
                                                           value="{{ $admin->email }}" required>
                                                </div>
                                                <div class="mb-0">
                                                    <label class="form-label small fw-bold text-secondary text-uppercase mb-1" style="font-size: 0.7rem;">Password Baru (Kosongkan jika tidak diubah)</label>
                                                    <input type="password" name="password" class="form-control rounded-3 border-light-subtle shadow-none p-2.5" 
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
                            <tr>
                                <td colspan="6" class="py-5 text-center text-secondary">
                                    <div class="py-4">
                                        <i class="bi bi-people fs-1 text-muted mb-3 d-block"></i>
                                        Belum ada staf admin yang ditambahkan ke sistem.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
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
                        <input type="text" name="name" class="form-control rounded-3 border-light-subtle shadow-none p-2.5" 
                               placeholder="Nama lengkap admin..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary text-uppercase mb-1" style="font-size: 0.7rem;">Username</label>
                        <input type="text" name="username" class="form-control rounded-3 border-light-subtle shadow-none p-2.5" 
                               placeholder="Username untuk login..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary text-uppercase mb-1" style="font-size: 0.7rem;">Email</label>
                        <input type="email" name="email" class="form-control rounded-3 border-light-subtle shadow-none p-2.5" 
                               placeholder="Alamat email aktif..." required>
                    </div>
                    <div class="mb-0">
                        <label class="form-label small fw-bold text-secondary text-uppercase mb-1" style="font-size: 0.7rem;">Password</label>
                        <input type="password" name="password" class="form-control rounded-3 border-light-subtle shadow-none p-2.5" 
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

{{-- Toast Container for instant action feedback --}}
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1300;">
    <div id="permissionToast" class="toast align-items-center border-0 text-white rounded-3 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body d-flex align-items-center py-2.5">
                <i class="bi me-2 fs-5" id="toastIcon"></i>
                <span id="toastMessage" class="fw-semibold"></span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto shadow-none" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<style>
    .avatar-circle {
        width: 44px;
        height: 44px;
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        color: #f59e0b;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.15rem;
        box-shadow: 0 4px 8px rgba(0,0,0,0.06);
        border: 2px solid #ffffff;
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
        border-radius: 8px;
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

    /* Custom form-switch styling for premium look */
    .form-check-input:checked {
        background-color: #d97706 !important;
        border-color: #d97706 !important;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.permission-switch').forEach(switchEl => {
        switchEl.addEventListener('change', function() {
            const adminId = this.dataset.adminId;
            const permission = this.dataset.permission;
            const status = this.checked ? 1 : 0;
            
            // Disable switch momentarily to prevent double clicks
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
                    
                    // Live update active count badges in background table
                    // Find the row containing this toggle
                    const row = document.getElementById(`perm_modal_${adminId}_${permission}`).closest('.modal');
                    if (row) {
                        // Find the matching button/row on background table
                        const tableRow = document.querySelector(`button[data-bs-target="#permissionsModal${adminId}"]`).closest('tr');
                        if (tableRow && data.permissions) {
                            const countBadge = tableRow.querySelector('.badge');
                            if (countBadge) {
                                countBadge.innerHTML = `<i class="bi bi-shield-lock-fill text-warning me-1.5"></i>${data.permissions.length} dari 12 Akses Aktif`;
                            }
                        }
                    }
                } else {
                    this.checked = !this.checked; // Revert
                    showToast('Gagal', data.message || 'Gagal memperbarui izin', 'danger');
                }
            })
            .catch(err => {
                this.disabled = false;
                this.checked = !this.checked; // Revert
                showToast('Gagal', 'Terjadi kesalahan jaringan', 'danger');
            });
        });
    });
});

function showToast(title, message, type) {
    const toastEl = document.getElementById('permissionToast');
    const toastMessage = document.getElementById('toastMessage');
    const toastIcon = document.getElementById('toastIcon');
    
    // Clear classes
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
