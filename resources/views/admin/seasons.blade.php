@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4" style="background-color: #f8fafc; min-height: 100vh;">
<style>
    .season-card {
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.06);
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02);
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
    }
    .season-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
        border-color: rgba(0, 0, 0, 0.09);
    }
    .season-card:hover .poster-img {
        transform: scale(1.04);
    }
    .season-card:hover .poster-overlay {
        opacity: 1;
    }
    .poster-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(15, 23, 42, 0.6);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.25s ease;
        z-index: 5;
    }
    .finished-season {
        filter: grayscale(35%);
        opacity: 0.88;
    }
    .btn-glass-dark {
        background: rgba(15, 23, 42, 0.5);
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        transition: all 0.2s ease;
    }
    .btn-glass-dark:hover {
        background: rgba(15, 23, 42, 0.8);
    }
    .filter-card {
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.06);
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02);
    }
    .search-box-season {
        display: flex;
        align-items: center;
        background: #f1f5f9;
        border: 1px solid transparent;
        border-radius: 10px;
        padding: 2px 12px;
        transition: all 0.2s ease;
    }
    .search-box-season:focus-within {
        background: #ffffff;
        border-color: #f59e0b;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
    }
    .search-box-season input {
        border: 0;
        background: transparent;
        font-size: 0.85rem;
        padding: 8px 6px;
        outline: none;
        width: 100%;
        color: #1e293b;
    }
    .search-box-season i {
        color: #94a3b8;
    }
    .perm-progress {
        height: 6px;
        border-radius: 3px;
        background: #f1f5f9;
        overflow: hidden;
    }
    .perm-progress-bar {
        height: 100%;
        border-radius: 3px;
        transition: width 0.4s ease;
    }
    
    /* Subtle Soft Badges */
    .badge-soft-open {
        background-color: #f0fdf4;
        color: #16a34a;
        border: 1px solid #d1fae5;
        font-size: 0.7rem;
        font-weight: 700;
        padding: 5px 12px;
        border-radius: 30px;
    }
    .badge-soft-closed {
        background-color: #fef2f2;
        color: #dc2626;
        border: 1px solid #fee2e2;
        font-size: 0.7rem;
        font-weight: 700;
        padding: 5px 12px;
        border-radius: 30px;
    }
</style>

    {{-- Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1" style="font-size: 1.6rem; letter-spacing: -0.5px;">Manajemen Season</h2>
            <p class="text-secondary mb-0" style="font-size: 0.85rem;">Kelola poster, hadiah, harga pendaftaran, slot, dan status pendaftaran turnamen.</p>
        </div>
    </div>

    {{-- Filters Bar --}}
    <div class="card filter-card p-3 mb-4 border-0">
        <div class="row g-3 align-items-center">
            <div class="col-12 col-md-3">
                <div class="search-box-season">
                    <i class="bi bi-search"></i>
                    <input type="text" id="searchSeason" placeholder="Cari nama season...">
                </div>
            </div>
            <div class="col-6 col-md-2">
                <select id="filterStatus" class="form-select rounded-3 border-light-subtle shadow-none" style="font-size: 0.85rem;" onchange="applyPhpFilters()">
                    <option value="ACTIVE" {{ $status == 'ACTIVE' ? 'selected' : '' }}>Status: Aktif</option>
                    <option value="FINISHED" {{ $status == 'FINISHED' ? 'selected' : '' }}>Status: Selesai</option>
                    <option value="ALL" {{ $status == 'ALL' ? 'selected' : '' }}>Status: Semua</option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <select id="filterSeries" class="form-select rounded-3 border-light-subtle shadow-none" style="font-size: 0.85rem;" onchange="applyPhpFilters()">
                    <option value="ALL" {{ $series == 'ALL' ? 'selected' : '' }}>Series: Semua</option>
                    <option value="CHAMPIONSHIP" {{ $series == 'CHAMPIONSHIP' ? 'selected' : '' }}>Championship</option>
                    <option value="FAST_TOUR" {{ $series == 'FAST_TOUR' ? 'selected' : '' }}>Fast Tour</option>
                </select>
            </div>
            <div class="col-12 col-md-3">
                <select id="filterSeasonSelect" class="form-select rounded-3 border-light-subtle shadow-none" style="font-size: 0.85rem;" onchange="applyPhpFilters()">
                    <option value="ALL" {{ $seasonId == 'ALL' ? 'selected' : '' }}>Pilih Season: Semua</option>
                    @foreach($all_seasons as $s)
                    <option value="{{ $s->id }}" {{ $seasonId == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-2">
                <button class="btn btn-warning fw-bold text-dark w-100 rounded-pill py-2 d-flex align-items-center justify-content-center gap-1.5 hover-gold shadow-sm" style="font-size: 0.85rem;" data-bs-toggle="modal" data-bs-target="#modalTambahSeason">
                    <i class="bi bi-plus-lg"></i> Tambah Season
                </button>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success border-0 shadow-sm mb-4 rounded-3 d-flex align-items-center py-3">
        <i class="bi bi-check-circle-fill me-2 fs-5 text-success"></i>
        <div class="fw-semibold">{{ session('success') }}</div>
    </div>
    @endif

    <div class="row g-4" id="seasonContainer">
        {{-- LOOPING DAFTAR SEASON --}}
        @forelse($seasons as $season)
        <div class="col-12 col-md-6 col-lg-4 col-xl-3 season-card-item" data-id="{{ $season->id }}" data-status="{{ $season->status }}">
            <div class="card h-100 season-card {{ $season->status == 'FINISHED' ? 'finished-season' : '' }}">
                
                {{-- Action Dropdown (Absolute) --}}
                <div class="dropdown position-absolute" style="top: 12px; right: 12px; z-index: 10;">
                    <button class="btn btn-glass-dark btn-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; padding:0;" data-bs-toggle="dropdown">
                        <i class="bi bi-three-dots-vertical text-white"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 p-2" style="font-size: 0.85rem;">
                        <li>
                            <a class="dropdown-item py-2 fw-bold text-dark rounded-3" href="#" data-bs-toggle="modal" data-bs-target="#modalEditSeason{{ $season->id }}">
                                <i class="bi bi-pencil-square me-2 text-warning"></i> Edit Season
                            </a>
                        </li>
                        <li><hr class="dropdown-divider my-1 border-light-subtle"></li>
                        <li>
                            <a class="dropdown-item py-2 fw-bold text-danger rounded-3" href="{{ route('admin.seasons.delete', $season->id) }}" onclick="return confirm('Hapus season ini akan menghapus SEMUA data tim di dalamnya secara permanen. Lanjutkan?')">
                                <i class="bi bi-trash3 me-2"></i> Hapus Season
                            </a>
                        </li>
                    </ul>
                </div>

                {{-- Status Badge (Absolute) --}}
                <span class="badge {{ $season->status == 'ACTIVE' ? 'bg-success' : 'bg-secondary' }} position-absolute text-uppercase" style="top: 12px; left: 12px; font-size: 0.65rem; padding: 6px 12px; border-radius: 30px; letter-spacing: 0.5px; z-index: 10;">
                    {{ $season->status == 'ACTIVE' ? 'Aktif' : 'Selesai' }}
                </span>

                {{-- Image/Poster Link --}}
                <a href="{{ route('admin.dashboard', $season->id) }}" class="d-block w-100 text-decoration-none" style="height: 180px; overflow: hidden; background: #1e293b; position: relative;">
                    @if($season->poster)
                    <img src="{{ asset('storage/posters/' . $season->poster) }}" class="w-100 h-100 poster-img" style="object-fit: cover; object-position: top; transition: transform 0.4s ease;" loading="lazy">
                    @else
                    <div class="d-flex align-items-center justify-content-center h-100 text-white-50 px-3 text-center" style="background: linear-gradient(135deg, #1e293b 0%, #334155 100%);">
                        <div>
                            <i class="bi bi-controller d-block fs-1 mb-1 text-warning"></i>
                            <span class="small fw-semibold d-block">Yomuda Championship</span>
                            <span class="text-white-50" style="font-size: 0.65rem;">Poster Belum Ditentukan</span>
                        </div>
                    </div>
                    @endif
                    <div class="poster-overlay">
                        <i class="bi bi-eye-fill text-white fs-4"></i>
                        <span class="text-white small fw-bold d-block mt-1">KELOLA TURNAMEN</span>
                    </div>
                </a>

                {{-- Card Body --}}
                <div class="card-body d-flex flex-column p-4">
                    <a href="{{ route('admin.dashboard', $season->id) }}" class="text-decoration-none d-flex flex-column h-100">
                        <h5 class="fw-bold text-dark mb-1 season-title text-truncate" style="font-size: 0.95rem;">{{ $season->name }}</h5>
                        
                        <div class="text-warning fw-bold small mb-3 text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem;">
                            <i class="bi bi-trophy-fill me-1"></i>{{ $season->prize_pool ?? 'Prize Pool Belum Set' }}
                        </div>

                        {{-- Progress Slot --}}
                        <div class="mb-3">
                            @php
                                $percent = $season->slot > 0 ? round(($season->teams_count / $season->slot) * 100) : 0;
                                $barColor = 'bg-warning';
                                if ($percent >= 90) $barColor = 'bg-danger';
                                elseif ($percent >= 60) $barColor = 'bg-warning';
                                else $barColor = 'bg-success';
                            @endphp
                            <div class="d-flex justify-content-between mb-1.5" style="font-size: 0.72rem;">
                                <span class="text-secondary fw-semibold">Pendaftaran</span>
                                <span class="text-dark fw-bold">{{ $season->teams_count }} / {{ $season->slot }} Slot</span>
                            </div>
                            <div class="perm-progress">
                                <div class="perm-progress-bar {{ $barColor }}" style="width: {{ $percent }}%"></div>
                            </div>
                        </div>

                        <div class="mb-3" style="font-size: 0.8rem;">
                            <div class="text-secondary mb-1">
                                <i class="bi bi-calendar-event me-1.5 text-muted"></i> {{ $season->date_info }}
                            </div>
                            <div class="text-dark fw-bold">
                                <i class="bi bi-tag-fill me-1.5 text-muted"></i> Rp {{ number_format($season->price, 0, ',', '.') }}
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-auto border-top pt-3" style="font-size: 0.75rem;">
                            <span class="{{ $season->is_open ? 'badge-soft-open' : 'badge-soft-closed' }}">
                                {{ $season->is_open ? 'PENDAFTARAN BUKA' : 'PENDAFTARAN TUTUP' }}
                            </span>
                            <span class="text-warning fw-bold d-flex align-items-center gap-1">
                                Kelola <i class="bi bi-chevron-right"></i>
                            </span>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        {{-- MODAL EDIT SEASON --}}
        <div class="modal fade" id="modalEditSeason{{ $season->id }}" tabindex="-1" aria-hidden="true" style="z-index: 1055;">
            <div class="modal-dialog modal-dialog-centered text-start">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-header border-bottom border-light px-4 py-3">
                        <h5 class="fw-bold text-dark mb-0"><i class="bi bi-pencil-square text-warning me-1.5"></i>Edit {{ $season->name }}</h5>
                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.seasons.update', $season->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body p-4">
                            <div class="row g-3 mb-3">
                                <div class="col-md-7">
                                    <label class="small fw-bold text-secondary mb-1 text-uppercase" style="font-size: 0.7rem;">Nama Season</label>
                                    <input type="text" name="name" class="form-control rounded-3 shadow-none border-light-subtle py-2 px-3" value="{{ $season->name }}" required style="font-size: 0.85rem;">
                                </div>
                                <div class="col-md-5">
                                    <label class="small fw-bold text-secondary mb-1 text-uppercase" style="font-size: 0.7rem;">Status</label>
                                    <select name="status" class="form-select rounded-3 shadow-none border-light-subtle py-2" style="font-size: 0.85rem;">
                                        <option value="ACTIVE" {{ $season->status == 'ACTIVE' ? 'selected' : '' }}>ACTIVE</option>
                                        <option value="FINISHED" {{ $season->status == 'FINISHED' ? 'selected' : '' }}>FINISHED</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="small fw-bold text-secondary mb-1 text-uppercase" style="font-size: 0.7rem;">Prize Pool</label>
                                <input type="text" name="prize_pool" class="form-control rounded-3 shadow-none border-light-subtle py-2 px-3" value="{{ $season->prize_pool }}" style="font-size: 0.85rem;">
                            </div>

                            <div class="mb-3">
                                <label class="small fw-bold text-secondary mb-1 text-uppercase" style="font-size: 0.7rem;">Ganti Poster</label>
                                <input type="file" name="poster" class="form-control rounded-3 shadow-none border-light-subtle" accept="image/*" onchange="previewImage(this, 'editPosterPreview{{ $season->id }}')" style="font-size: 0.85rem;">
                                <div class="mt-2 text-center">
                                    <img id="editPosterPreview{{ $season->id }}" src="{{ $season->poster ? asset('storage/posters/' . $season->poster) : '' }}" class="img-thumbnail rounded-3 shadow-sm" loading="lazy" style="max-height: 120px; {{ $season->poster ? '' : 'display: none;' }}">
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="small fw-bold text-secondary mb-1 text-uppercase" style="font-size: 0.7rem;">Harga (Rp)</label>
                                    <input type="number" name="price" class="form-control rounded-3 shadow-none border-light-subtle py-2 px-3" value="{{ $season->price }}" required style="font-size: 0.85rem;">
                                </div>
                                <div class="col-md-6">
                                    <label class="small fw-bold text-secondary mb-1 text-uppercase" style="font-size: 0.7rem;">Total Slot</label>
                                    <input type="number" name="slot" class="form-control rounded-3 shadow-none border-light-subtle py-2 px-3" value="{{ $season->slot }}" required style="font-size: 0.85rem;">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="small fw-bold text-secondary mb-1 text-uppercase" style="font-size: 0.7rem;">Keterangan Waktu</label>
                                <input type="text" name="date_info" class="form-control rounded-3 shadow-none border-light-subtle py-2 px-3" value="{{ $season->date_info }}" required style="font-size: 0.85rem;">
                            </div>

                            <div class="mb-3">
                                <label class="small fw-bold text-secondary mb-1 text-uppercase" style="font-size: 0.7rem;">Link Grup WhatsApp</label>
                                <input type="url" name="wa_link" class="form-control rounded-3 shadow-none border-light-subtle py-2 px-3" value="{{ $season->wa_link }}" style="font-size: 0.85rem;">
                            </div>

                            <div class="mb-3">
                                <label class="small fw-bold text-secondary mb-1 text-uppercase" style="font-size: 0.7rem;">Link Rules (Google Drive PDF)</label>
                                <input type="url" name="rules_link" class="form-control rounded-3 shadow-none border-light-subtle py-2 px-3" value="{{ $season->rules_link }}" style="font-size: 0.85rem;">
                            </div>

                            <div class="mb-3">
                                <label class="small fw-bold text-secondary mb-1 text-uppercase" style="font-size: 0.7rem;">Informasi Jadwal Rentang Waktu (Optional)</label>
                                <textarea name="schedule_info" class="form-control rounded-3 shadow-none border-light-subtle py-2 px-3" rows="3" placeholder="Contoh:&#10;Babak 1: 20:00 - 20:40 WIB&#10;Babak 2: 20:40 - 21:20 WIB" style="font-size: 0.85rem;">{{ $season->schedule_info }}</textarea>
                            </div>

                            <div class="mb-0">
                                <label class="small fw-bold text-secondary mb-1 text-uppercase" style="font-size: 0.7rem;">Pendaftaran</label>
                                <select name="is_open" class="form-select rounded-3 shadow-none border-light-subtle py-2" style="font-size: 0.85rem;">
                                    <option value="1" {{ $season->is_open ? 'selected' : '' }}>BUKA</option>
                                    <option value="0" {{ !$season->is_open ? 'selected' : '' }}>TUTUP</option>
                                </select>
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
        {{-- Handled by search script placeholder --}}
        @endforelse
    </div>

    {{-- Pencarian Kosong (Empty State Placeholder) --}}
    <div id="noSearchResult" class="col-12 text-center py-5 d-none">
        <div class="py-5 bg-white rounded-4 shadow-sm border border-light-subtle">
            <i class="bi bi-search fs-1 text-muted opacity-50 mb-3 d-block"></i>
            <h5 class="fw-bold text-dark">Pencarian Tidak Ditemukan</h5>
            <p class="text-secondary small">Tidak ada season yang cocok dengan kata kunci yang Anda masukkan.</p>
        </div>
    </div>
</div>

{{-- MODAL TAMBAH SEASON --}}
<div class="modal fade" id="modalTambahSeason" tabindex="-1" aria-hidden="true" style="z-index: 1055;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom border-light px-4 py-3">
                <h5 class="fw-bold text-dark mb-0"><i class="bi bi-folder-plus text-warning me-1.5"></i>Buat Season Baru</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.seasons.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="small fw-bold text-secondary mb-1 text-uppercase" style="font-size: 0.7rem;">Nama Season</label>
                        <input type="text" name="name" class="form-control rounded-3 shadow-none border-light-subtle py-2 px-3" placeholder="Contoh: Season 13" required style="font-size: 0.85rem;">
                    </div>

                    <div class="mb-3">
                        <label class="small fw-bold text-secondary mb-1 text-uppercase" style="font-size: 0.7rem;">Prize Pool</label>
                        <input type="text" name="prize_pool" class="form-control rounded-3 shadow-none border-light-subtle py-2 px-3" placeholder="Contoh: Rp 1.000.000" required style="font-size: 0.85rem;">
                    </div>

                    <div class="mb-3">
                        <label class="small fw-bold text-secondary mb-1 text-uppercase" style="font-size: 0.7rem;">Upload Poster</label>
                        <input type="file" name="poster" class="form-control rounded-3 shadow-none border-light-subtle" accept="image/*" required onchange="previewImage(this, 'addPosterPreview')" style="font-size: 0.85rem;">
                        <div class="mt-2 text-center">
                            <img id="addPosterPreview" src="" class="img-thumbnail rounded-3 shadow-sm" style="max-height: 120px; display: none;">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="small fw-bold text-secondary mb-1 text-uppercase" style="font-size: 0.7rem;">Harga (Rp)</label>
                            <input type="number" name="price" class="form-control rounded-3 shadow-none border-light-subtle py-2 px-3" placeholder="10000" required style="font-size: 0.85rem;">
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold text-secondary mb-1 text-uppercase" style="font-size: 0.7rem;">Total Slot</label>
                            <input type="number" name="slot" class="form-control rounded-3 shadow-none border-light-subtle py-2 px-3" placeholder="32" required style="font-size: 0.85rem;">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="small fw-bold text-secondary mb-1 text-uppercase" style="font-size: 0.7rem;">Keterangan Waktu</label>
                        <input type="text" name="date_info" class="form-control rounded-3 shadow-none border-light-subtle py-2 px-3" placeholder="Contoh: 21 - 25 Jan 2026" required style="font-size: 0.85rem;">
                    </div>

                    <div class="mb-3">
                        <label class="small fw-bold text-secondary mb-1 text-uppercase" style="font-size: 0.7rem;">Link Grup WhatsApp</label>
                        <input type="url" name="wa_link" class="form-control rounded-3 shadow-none border-light-subtle py-2 px-3" placeholder="https://chat.whatsapp.com/..." style="font-size: 0.85rem;">
                    </div>

                    <div class="mb-3">
                        <label class="small fw-bold text-secondary mb-1 text-uppercase" style="font-size: 0.7rem;">Link Rules (Google Drive PDF)</label>
                        <input type="url" name="rules_link" class="form-control rounded-3 shadow-none border-light-subtle py-2 px-3" placeholder="https://drive.google.com/..." style="font-size: 0.85rem;">
                    </div>

                    <div class="mb-3">
                        <label class="small fw-bold text-secondary mb-1 text-uppercase" style="font-size: 0.7rem;">Informasi Jadwal Rentang Waktu (Optional)</label>
                        <textarea name="schedule_info" class="form-control rounded-3 shadow-none border-light-subtle py-2 px-3" rows="3" placeholder="Contoh:&#10;Babak 1: 20:00 - 20:40 WIB&#10;Babak 2: 20:40 - 21:20 WIB" style="font-size: 0.85rem;"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top border-light px-4 py-3">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold text-dark hover-gold">Simpan Season</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Live Image Preview Helper (UX Upgrade!)
    function previewImage(input, previewId) {
        const preview = document.getElementById(previewId);
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
                preview.style.display = 'inline-block';
            }
            reader.readAsDataURL(input.files[0]);
        } else {
            preview.src = '';
            preview.style.display = 'none';
        }
    }

    // PHP-side Filter redirection
    function applyPhpFilters() {
        let status = document.getElementById('filterStatus').value;
        let seasonId = document.getElementById('filterSeasonSelect').value;
        let series = document.getElementById('filterSeries').value;
        window.location.href = `?status=${status}&season_id=${seasonId}&series=${series}`;
    }

    // Instant Search (Client-side search on loaded cards only)
    function filterSeasons() {
        let searchQuery = document.getElementById('searchSeason').value.toLowerCase();
        let seasonCards = document.querySelectorAll('.season-card-item');
        let visibleCount = 0;

        seasonCards.forEach(item => {
            let title = item.querySelector('.season-title').innerText.toLowerCase();
            let matchesSearch = title.includes(searchQuery);

            if (matchesSearch) {
                item.style.display = "";
                visibleCount++;
            } else {
                item.style.display = "none";
            }
        });

        let noResult = document.getElementById('noSearchResult');
        if (visibleCount === 0) {
            noResult.classList.remove('d-none');
        } else {
            noResult.classList.add('d-none');
        }
    }

    // Attach event listeners for search
    document.getElementById('searchSeason').addEventListener('keyup', filterSeasons);
</script>
@endsection