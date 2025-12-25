@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4 text-center text-md-start">
        <div class="col-12">
            <h3 class="fw-bold text-dark">Pilih Season</h3>
            <p class="text-muted">Kelola data peserta, harga, dan slot berdasarkan periode turnamen.</p>
        </div>
    </div>

    <div class="row g-4">
        @foreach($seasons as $season)
        <div class="col-md-4 col-lg-3">
            <div class="card shadow-sm border-0 h-100 p-3 {{ $season->status == 'FINISHED' ? 'opacity-75' : '' }}"
                style="border-top: 4px solid {{ $season->status == 'ACTIVE' ? '#ffc107' : '#6c757d' }} !important; 
               transition: all 0.3s ease; position: relative;"
                onmouseover="this.style.transform='translateY(-5px)'"
                onmouseout="this.style.transform='translateY(0)'">

                <div class="dropdown position-absolute" style="top: 15px; right: 15px; z-index: 10;">
                    <button class="btn btn-link text-secondary p-0" data-bs-toggle="dropdown">
                        <i class="bi bi-three-dots-vertical fs-5"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                        <li>
                            <a class="dropdown-item small" href="#" data-bs-toggle="modal" data-bs-target="#modalEditSeason{{ $season->id }}">
                                <i class="bi bi-pencil-square me-2 text-warning"></i> Edit Season
                            </a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <a class="dropdown-item small text-danger" href="{{ route('admin.seasons.delete', $season->id) }}"
                                onclick="return confirm('Hapus season ini akan menghapus SEMUA data tim di dalamnya. Lanjutkan?')">
                                <i class="bi bi-trash3 me-2"></i> Hapus Season
                            </a>
                        </li>
                    </ul>
                </div>

                <a href="{{ route('admin.dashboard', $season->id) }}" class="text-decoration-none h-100 d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="rounded-3 {{ $season->status == 'ACTIVE' ? 'bg-warning bg-opacity-10 text-warning' : 'bg-light text-secondary' }} p-2">
                            <i class="bi {{ $season->status == 'ACTIVE' ? 'bi-trophy-fill' : 'bi-archive-fill' }} fs-4"></i>
                        </div>
                        <span class="badge {{ $season->status == 'ACTIVE' ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' }} rounded-pill px-3">
                            {{ $season->status }}
                        </span>
                    </div>

                    <h5 class="fw-bold text-dark mb-1">{{ $season->name }}</h5>
                    <p class="text-muted small mb-2">{{ $season->date_info }}</p>

                    <div class="mt-2 mb-3">
                        <div class="text-dark small fw-bold">Rp {{ number_format($season->price, 0, ',', '.') }}</div>
                        <div class="text-muted" style="font-size: 0.7rem;">Slot: {{ $season->teams_count }} / {{ $season->slot }}</div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-auto">
                        <span class="badge {{ $season->is_open ? 'bg-info-subtle text-info' : 'bg-danger-subtle text-danger' }}" style="font-size: 0.65rem;">
                            {{ $season->is_open ? 'PENDAFTARAN BUKA' : 'PENDAFTARAN TUTUP' }}
                        </span>
                        <i class="bi bi-arrow-right-circle text-warning fs-5"></i>
                    </div>
                </a>
            </div>
        </div>

        <div class="modal fade" id="modalEditSeason{{ $season->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow">
                    <div class="modal-header border-0">
                        <h5 class="fw-bold">Edit {{ $season->name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.seasons.update', $season->id) }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="row mb-3">
                                <div class="col-md-8">
                                    <label class="small fw-bold text-muted mb-1 text-uppercase">Nama Season</label>
                                    <input type="text" name="name" class="form-control" value="{{ $season->name }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="small fw-bold text-muted mb-1 text-uppercase">Status</label>
                                    <select name="status" class="form-select">
                                        <option value="ACTIVE" {{ $season->status == 'ACTIVE' ? 'selected' : '' }}>ACTIVE</option>
                                        <option value="FINISHED" {{ $season->status == 'FINISHED' ? 'selected' : '' }}>FINISHED</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="small fw-bold text-muted mb-1 text-uppercase">Harga (Rp)</label>
                                    <input type="number" name="price" class="form-control" value="{{ $season->price }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="small fw-bold text-muted mb-1 text-uppercase">Total Slot</label>
                                    <input type="number" name="slot" class="form-control" value="{{ $season->slot }}" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="small fw-bold text-muted mb-1 text-uppercase">Keterangan Waktu</label>
                                <input type="text" name="date_info" class="form-control" value="{{ $season->date_info }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="small fw-bold text-muted mb-1 text-uppercase">Link Grup WhatsApp</label>
                                <input type="url" name="wa_link" class="form-control" value="{{ $season->wa_link }}" placeholder="https://chat.whatsapp.com/...">
                            </div>
                            <div class="mb-0">
                                <label class="small fw-bold text-muted mb-1 text-uppercase">Pendaftaran</label>
                                <select name="is_open" class="form-select">
                                    <option value="1" {{ $season->is_open ? 'selected' : '' }}>BUKA</option>
                                    <option value="0" {{ !$season->is_open ? 'selected' : '' }}>TUTUP</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer border-0">
                            <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-dark fw-bold px-4">Update Season</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endforeach

        <div class="col-md-4 col-lg-3" data-bs-toggle="modal" data-bs-target="#modalTambahSeason">
            <div class="card shadow-sm border-0 h-100 p-3 d-flex flex-column align-items-center justify-content-center text-center py-5"
                style="border: 2px dashed #dee2e6 !important; background: transparent; cursor: pointer;">
                <div class="rounded-circle bg-light p-3 mb-2 text-secondary">
                    <i class="bi bi-plus-lg fs-4"></i>
                </div>
                <h6 class="fw-bold text-secondary">Tambah Season</h6>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTambahSeason" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0">
                <h5 class="fw-bold">Buat Season Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.seasons.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="small fw-bold text-muted mb-1 text-uppercase">Nama Season</label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: Season 8" required>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="small fw-bold text-muted mb-1 text-uppercase">Harga (Rp)</label>
                            <input type="number" name="price" class="form-control" placeholder="50000" required>
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold text-muted mb-1 text-uppercase">Total Slot</label>
                            <input type="number" name="slot" class="form-control" placeholder="64" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold text-muted mb-1 text-uppercase">Keterangan Waktu</label>
                        <input type="text" name="date_info" class="form-control" placeholder="Contoh: Minggu Ini" required>
                    </div>
                    <div class="mb-0">
                        <label class="small fw-bold text-muted mb-1 text-uppercase">Link Grup WhatsApp</label>
                        <input type="url" name="wa_link" class="form-control" placeholder="https://chat.whatsapp.com/...">
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning fw-bold px-4">Simpan Season</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection