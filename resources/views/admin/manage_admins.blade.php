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
                Tambahkan, edit, atau hapus akun admin yang dapat mengelola pendaftaran turnamen.
            </p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <button class="btn btn-warning fw-bold px-4 py-2.5 rounded-pill shadow-sm text-dark" data-bs-toggle="modal" data-bs-target="#addAdminModal">
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

    {{-- Tabel Admin --}}
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                    <thead class="bg-light text-secondary text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.8px;">
                        <tr>
                            <th class="py-3 px-4" style="width: 60px;">#</th>
                            <th class="py-3 px-3">Nama</th>
                            <th class="py-3 px-3">Username</th>
                            <th class="py-3 px-3">Email</th>
                            <th class="py-3 px-3">Dibuat Pada</th>
                            <th class="py-3 px-4 text-end" style="width: 180px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-dark">
                        @forelse($admins as $index => $admin)
                            <tr>
                                <td class="py-3 px-4 text-secondary fw-semibold">
                                    {{ $index + 1 }}
                                </td>
                                <td class="py-3 px-3 fw-bold">
                                    {{ $admin->name }}
                                </td>
                                <td class="py-3 px-3">
                                    <code class="bg-light text-dark px-2 py-1 rounded" style="font-size: 0.8rem;">{{ $admin->username }}</code>
                                </td>
                                <td class="py-3 px-3 text-secondary">
                                    {{ $admin->email }}
                                </td>
                                <td class="py-3 px-3 text-secondary">
                                    {{ $admin->created_at->format('d M Y, H:i') }}
                                </td>
                                <td class="py-3 px-4 text-end">
                                    <button class="btn btn-sm btn-outline-secondary rounded-pill me-1" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editAdminModal{{ $admin->id }}">
                                        <i class="bi bi-pencil"></i> Edit
                                    </button>
                                    <a href="{{ route('admin.manage.delete', $admin->id) }}" 
                                       class="btn btn-sm btn-outline-danger rounded-pill" 
                                       onclick="return confirm('Apakah Anda yakin ingin menghapus akun admin {{ $admin->username }}?');">
                                        <i class="bi bi-trash"></i> Hapus
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
                                                    <label class="form-label small fw-bold text-secondary text-uppercase mb-1">Nama Lengkap</label>
                                                    <input type="text" name="name" class="form-control rounded-3 border-light-subtle shadow-none p-2.5" 
                                                           value="{{ $admin->name }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold text-secondary text-uppercase mb-1">Username</label>
                                                    <input type="text" name="username" class="form-control rounded-3 border-light-subtle shadow-none p-2.5" 
                                                           value="{{ $admin->username }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold text-secondary text-uppercase mb-1">Email</label>
                                                    <input type="email" name="email" class="form-control rounded-3 border-light-subtle shadow-none p-2.5" 
                                                           value="{{ $admin->email }}" required>
                                                </div>
                                                <div class="mb-0">
                                                    <label class="form-label small fw-bold text-secondary text-uppercase mb-1">Password Baru (Kosongkan jika tidak diubah)</label>
                                                    <input type="password" name="password" class="form-control rounded-3 border-light-subtle shadow-none p-2.5" 
                                                           placeholder="Minimal 6 karakter...">
                                                </div>
                                            </div>
                                            <div class="modal-footer border-top border-light px-4 py-3">
                                                <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold text-dark">Simpan Perubahan</button>
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
                        <label class="form-label small fw-bold text-secondary text-uppercase mb-1">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control rounded-3 border-light-subtle shadow-none p-2.5" 
                               placeholder="Nama lengkap admin..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary text-uppercase mb-1">Username</label>
                        <input type="text" name="username" class="form-control rounded-3 border-light-subtle shadow-none p-2.5" 
                               placeholder="Username untuk login..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary text-uppercase mb-1">Email</label>
                        <input type="email" name="email" class="form-control rounded-3 border-light-subtle shadow-none p-2.5" 
                               placeholder="Alamat email aktif..." required>
                    </div>
                    <div class="mb-0">
                        <label class="form-label small fw-bold text-secondary text-uppercase mb-1">Password</label>
                        <input type="password" name="password" class="form-control rounded-3 border-light-subtle shadow-none p-2.5" 
                               placeholder="Minimal 6 karakter..." required>
                    </div>
                </div>
                <div class="modal-footer border-top border-light px-4 py-3">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold text-dark">Simpan Akun</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
