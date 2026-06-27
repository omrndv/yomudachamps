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
                <i class="bi bi-person-plus-fill me-1"></i> Tambah Admin
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
            <div>{{ session('success') }}</div>
        </div>
    @endif

    {{-- Tabel Admin Premium --}}
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                    <thead class="text-white" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); font-size: 0.75rem; letter-spacing: 0.8px;">
                        <tr>
                            <th class="py-3.5 px-4" style="width: 60px;">#</th>
                            <th class="py-3.5 px-3">Info Akun</th>
                            <th class="py-3.5 px-3">Kontak / Email</th>
                            <th class="py-3.5 px-3" style="min-width: 480px;">Hak Akses Halaman (On / Off)</th>
                            <th class="py-3.5 px-3">Dibuat Pada</th>
                            <th class="py-3.5 px-4 text-end" style="width: 180px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-dark">
                        @forelse($admins as $index => $admin)
                            <tr>
                                <td class="py-4 px-4 text-secondary fw-semibold">
                                    {{ $index + 1 }}
                                </td>
                                <td class="py-4 px-3">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle me-3">
                                            {{ strtoupper(substr($admin->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <span class="fw-bold text-dark d-block" style="font-size: 0.95rem;">{{ $admin->name }}</span>
                                            <span class="text-secondary small">Username: <code class="bg-light text-dark px-1.5 py-0.5 rounded">{{ $admin->username }}</code></span>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-3 text-secondary">
                                    {{ $admin->email }}
                                </td>
                                <td class="py-4 px-3">
                                    {{-- Grouping Permission Switces --}}
                                    <div class="d-flex flex-wrap gap-2 pt-1">
                                        @php
                                            $userPerms = $admin->permissions;
                                            if (!is_array($userPerms)) {
                                                $userPerms = json_decode($userPerms, true) ?: [];
                                            }
                                            
                                            $availablePerms = [
                                                'seasons' => ['label' => 'Season & Tim', 'icon' => 'bi-trophy', 'color' => '#d97706'],
                                                'finance' => ['label' => 'Keuangan', 'icon' => 'bi-cash-stack', 'color' => '#10b981'],
                                                'solo_matchmaker' => ['label' => 'Matchmaker', 'icon' => 'bi-people', 'color' => '#3b82f6'],
                                                'notes' => ['label' => 'Catatan', 'icon' => 'bi-sticky', 'color' => '#8b5cf6'],
                                                'faqs' => ['label' => 'FAQ', 'icon' => 'bi-question-circle', 'color' => '#6b7280'],
                                                'activity_log' => ['label' => 'Log Aktivitas', 'icon' => 'bi-clock-history', 'color' => '#ec4899'],
                                            ];
                                        @endphp
                                        
                                        @foreach($availablePerms as $key => $pInfo)
                                            <div class="permission-pill d-flex align-items-center bg-light border border-light-subtle rounded-3 px-2.5 py-1.5">
                                                <div class="form-check form-switch p-0 m-0 d-flex align-items-center">
                                                    <input class="form-check-input permission-switch shadow-none" 
                                                           type="checkbox" 
                                                           role="switch" 
                                                           id="perm_{{ $admin->id }}_{{ $key }}"
                                                           data-admin-id="{{ $admin->id }}"
                                                           data-permission="{{ $key }}"
                                                           {{ in_array($key, $userPerms) ? 'checked' : '' }}
                                                           style="width: 2.2em; height: 1.1em; cursor: pointer; margin-right: 8px;">
                                                    <label class="form-check-label small fw-semibold text-secondary d-flex align-items-center" for="perm_{{ $admin->id }}_{{ $key }}" style="cursor: pointer; font-size: 0.78rem;">
                                                        <i class="bi {{ $pInfo['icon'] }} me-1" style="color: {{ $pInfo['color'] }}"></i> {{ $pInfo['label'] }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </td>
                                <td class="py-4 px-3 text-secondary" style="font-size: 0.8rem;">
                                    {{ $admin->created_at->format('d M Y') }}<br>
                                    <span class="text-muted" style="font-size: 0.75rem;">{{ $admin->created_at->format('H:i') }} WIB</span>
                                </td>
                                <td class="py-4 px-4 text-end">
                                    <button class="btn btn-sm btn-outline-secondary rounded-pill px-3 me-1 border-light-subtle shadow-sm" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editAdminModal{{ $admin->id }}">
                                        <i class="bi bi-pencil me-1"></i> Edit
                                    </button>
                                    <a href="{{ route('admin.manage.delete', $admin->id) }}" 
                                       class="btn btn-sm btn-outline-danger rounded-pill px-3 border-light-subtle shadow-sm" 
                                       onclick="return confirm('Apakah Anda yakin ingin menghapus akun admin {{ $admin->username }}?');">
                                        <i class="bi bi-trash me-1"></i> Hapus
                                    </a>
                                </td>
                            </tr>

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
                                        Belum ada akun admin yang dibuat.
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
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1200;">
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
        width: 42px;
        height: 42px;
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        color: #f59e0b;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.1rem;
        box-shadow: 0 3px 6px rgba(0,0,0,0.1);
        border: 2px solid #ffffff;
    }

    .permission-pill {
        transition: all 0.25s ease;
    }

    .permission-pill:hover {
        background-color: #f1f5f9 !important;
        border-color: #cbd5e1 !important;
        transform: translateY(-1px);
    }

    .hover-gold:hover {
        background-color: #d97706 !important;
        border-color: #d97706 !important;
        color: #ffffff !important;
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
