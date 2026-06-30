@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4" style="background-color: #f8fafc; min-height: 100vh;">
    {{-- Header & Search Bar --}}
    <div class="row mb-4 align-items-center">
        <div class="col-md-6 text-center text-md-start mb-3 mb-md-0">
            <h2 class="fw-bold text-dark mb-1" style="font-size: 1.75rem; letter-spacing: -0.5px;">Manajemen Season</h2>
            <p class="text-secondary mb-0" style="font-size: 0.9rem;">Kelola poster, hadiah, harga pendaftaran, slot, dan status pendaftaran turnamen.</p>
        </div>
    </div>

    {{-- Filters Bar --}}
    <div class="card border-0 shadow-sm rounded-4 p-3 mb-4 bg-white">
        <div class="row g-3 align-items-center">
            <div class="col-md-4">
                <div class="input-group rounded-3 border border-light-subtle overflow-hidden bg-light">
                    <span class="input-group-text bg-light border-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" id="searchSeason" class="form-control border-0 ps-0 shadow-none bg-light" placeholder="Cari nama season..." style="font-size: 0.85rem;">
                </div>
            </div>
            <div class="col-md-3">
                <select id="filterStatus" class="form-select rounded-3 border-light-subtle shadow-none" style="font-size: 0.85rem;" onchange="applyPhpFilters()">
                    <option value="ACTIVE" {{ $status == 'ACTIVE' ? 'selected' : '' }}>Status: Aktif</option>
                    <option value="FINISHED" {{ $status == 'FINISHED' ? 'selected' : '' }}>Status: Selesai</option>
                    <option value="ALL" {{ $status == 'ALL' ? 'selected' : '' }}>Status: Semua</option>
                </select>
            </div>
            <div class="col-md-3">
                <select id="filterSeasonSelect" class="form-select rounded-3 border-light-subtle shadow-none" style="font-size: 0.85rem;" onchange="applyPhpFilters()">
                    <option value="ALL" {{ $seasonId == 'ALL' ? 'selected' : '' }}>Pilih Season: Semua</option>
                    @foreach($all_seasons as $s)
                    <option value="{{ $s->id }}" {{ $seasonId == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button class="btn btn-warning fw-bold text-dark w-100 rounded-3 d-flex align-items-center justify-content-center gap-2" style="font-size: 0.85rem;" data-bs-toggle="modal" data-bs-target="#modalTambahSeason">
                    <i class="bi bi-plus-lg"></i> Tambah Season
                </button>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success border-0 shadow-sm mb-4 rounded-3 d-flex align-items-center">
        <i class="bi bi-check-circle-fill me-2 fs-5"></i>
        <div>{{ session('success') }}</div>
    </div>
    @endif

    <div class="row g-4" id="seasonContainer">
        {{-- 1. LOOPING DAFTAR SEASON --}}
        @forelse($seasons as $season)
        <div class="col-md-6 col-lg-4 col-xl-3 season-card-item" data-id="{{ $season->id }}" data-status="{{ $season->status }}">
            <div class="card h-100 season-card {{ $season->status == 'FINISHED' ? 'finished-season' : '' }}">
                
                {{-- Action Dropdown (Absolute) --}}
                <div class="dropdown position-absolute" style="top: 12px; right: 12px; z-index: 10;">
                    <button class="btn btn-glass-dark btn-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;" data-bs-toggle="dropdown">
                        <i class="bi bi-three-dots-vertical text-white"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3" style="font-size: 0.85rem;">
                        <li>
                            <a class="dropdown-item py-2 fw-bold text-dark" href="#" data-bs-toggle="modal" data-bs-target="#modalEditSeason{{ $season->id }}">
                                <i class="bi bi-pencil-square me-2 text-warning"></i> Edit Season
                            </a>
                        </li>
                        <li><hr class="dropdown-divider my-1 border-light-subtle"></li>
                        <li>
                            <a class="dropdown-item py-2 fw-bold text-danger" href="{{ route('admin.seasons.delete', $season->id) }}" onclick="return confirm('Hapus season ini akan menghapus SEMUA data tim di dalamnya secara permanen. Lanjutkan?')">
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
                    <img src="{{ asset('storage/posters/' . $season->poster) }}" class="w-100 h-100 poster-img" style="object-fit: cover; object-position: top; transition: transform 0.5s ease;">
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
                <div class="card-body d-flex flex-column p-3">
                    <a href="{{ route('admin.dashboard', $season->id) }}" class="text-decoration-none d-flex flex-column h-100">
                        <h5 class="fw-bold text-slate-800 mb-1 season-title text-truncate">{{ $season->name }}</h5>
                        
                        <div class="text-warning fw-bold small mb-3 text-uppercase" style="letter-spacing: 0.5px; font-size: 0.75rem;">
                            <i class="bi bi-trophy-fill me-1"></i>{{ $season->prize_pool ?? 'Prize Pool Belum Set' }}
                        </div>

                        {{-- Progress Slot (UX Upgrade!) --}}
                        <div class="mb-3">
                            @php
                                $percent = $season->slot > 0 ? round(($season->teams_count / $season->slot) * 100) : 0;
                                $barColor = 'bg-primary';
                                if ($percent >= 90) $barColor = 'bg-danger';
                                elseif ($percent >= 60) $barColor = 'bg-warning';
                            @endphp
                            <div class="d-flex justify-content-between mb-1" style="font-size: 0.75rem;">
                                <span class="text-secondary fw-semibold">Pendaftaran</span>
                                <span class="text-slate-800 fw-bold">{{ $season->teams_count }} / {{ $season->slot }} Slot ({{ $percent }}%)</span>
                            </div>
                            <div class="progress rounded-pill bg-light" style="height: 6px;">
                                <div class="progress-bar rounded-pill {{ $barColor }}" role="progressbar" style="width: {{ $percent }}%"></div>
                            </div>
                        </div>

                        <div class="mb-3" style="font-size: 0.8rem;">
                            <div class="text-secondary mb-1">
                                <i class="bi bi-calendar-event me-1 text-muted"></i> {{ $season->date_info }}
                            </div>
                            <div class="text-slate-800 fw-bold">
                                <i class="bi bi-tag-fill me-1 text-muted"></i> Rp {{ number_format($season->price, 0, ',', '.') }}
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-auto border-top pt-2" style="font-size: 0.75rem;">
                            <span class="badge {{ $season->is_open ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-danger-subtle text-danger border border-danger-subtle' }} px-2 py-1.5 rounded-pill">
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
                    <div class="modal-header border-bottom border-light p-3">
                        <h5 class="fw-bold text-dark mb-0"><i class="bi bi-pencil-square text-warning me-2"></i>Edit {{ $season->name }}</h5>
                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('admin.seasons.update', $season->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="modal-body p-4">
                            <div class="row g-3 mb-3">
                                <div class="col-md-7">
                                    <label class="small fw-bold text-secondary mb-1 text-uppercase">Nama Season</label>
                                    <input type="text" name="name" class="form-control rounded-3 shadow-none border-light-subtle" value="{{ $season->name }}" required>
                                </div>
                                <div class="col-md-5">
                                    <label class="small fw-bold text-secondary mb-1 text-uppercase">Status</label>
                                    <select name="status" class="form-select rounded-3 shadow-none border-light-subtle">
                                        <option value="ACTIVE" {{ $season->status == 'ACTIVE' ? 'selected' : '' }}>ACTIVE</option>
                                        <option value="FINISHED" {{ $season->status == 'FINISHED' ? 'selected' : '' }}>FINISHED</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label class="small fw-bold text-secondary mb-1 text-uppercase">Prize Pool</label>
                                <input type="text" name="prize_pool" class="form-control rounded-3 shadow-none border-light-subtle" value="{{ $season->prize_pool }}">
                            </div>

                            <div class="mb-3">
                                <label class="small fw-bold text-secondary mb-1 text-uppercase">Ganti Poster</label>
                                <input type="file" name="poster" class="form-control rounded-3 shadow-none border-light-subtle" accept="image/*" onchange="previewImage(this, 'editPosterPreview{{ $season->id }}')">
                                <div class="mt-2 text-center">
                                    <img id="editPosterPreview{{ $season->id }}" src="{{ $season->poster ? asset('storage/posters/' . $season->poster) : '' }}" class="img-thumbnail rounded-3 shadow-sm" style="max-height: 120px; {{ $season->poster ? '' : 'display: none;' }}">
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-6">
                                    <label class="small fw-bold text-secondary mb-1 text-uppercase">Harga (Rp)</label>
                                    <input type="number" name="price" class="form-control rounded-3 shadow-none border-light-subtle" value="{{ $season->price }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="small fw-bold text-secondary mb-1 text-uppercase">Total Slot</label>
                                    <input type="number" name="slot" class="form-control rounded-3 shadow-none border-light-subtle" value="{{ $season->slot }}" required>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="small fw-bold text-secondary mb-1 text-uppercase">Keterangan Waktu</label>
                                <input type="text" name="date_info" class="form-control rounded-3 shadow-none border-light-subtle" value="{{ $season->date_info }}" required>
                            </div>

                            <div class="mb-3">
                                <label class="small fw-bold text-secondary mb-1 text-uppercase">Link Grup WhatsApp</label>
                                <input type="url" name="wa_link" class="form-control rounded-3 shadow-none border-light-subtle" value="{{ $season->wa_link }}">
                            </div>

                            <div class="mb-3">
                                <label class="small fw-bold text-secondary mb-1 text-uppercase">Link Rules (Google Drive PDF)</label>
                                <input type="url" name="rules_link" class="form-control rounded-3 shadow-none border-light-subtle" value="{{ $season->rules_link }}">
                            </div>

                            <div class="mb-3">
                                <label class="small fw-bold text-secondary mb-1 text-uppercase">Informasi Jadwal Rentang Waktu (Optional)</label>
                                <textarea name="schedule_info" class="form-control rounded-3 shadow-none border-light-subtle" rows="3" placeholder="Contoh:&#10;Babak 1: 20:00 - 20:40 WIB&#10;Babak 2: 20:40 - 21:20 WIB">{{ $season->schedule_info }}</textarea>
                            </div>

                            <div class="mb-0">
                                <label class="small fw-bold text-secondary mb-1 text-uppercase">Pendaftaran</label>
                                <select name="is_open" class="form-select rounded-3 shadow-none border-light-subtle">
                                    <option value="1" {{ $season->is_open ? 'selected' : '' }}>BUKA</option>
                                    <option value="0" {{ !$season->is_open ? 'selected' : '' }}>TUTUP</option>
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer border-0 p-3 pt-0">
                            <button type="button" class="btn btn-light fw-bold px-4" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-dark fw-bold px-4 shadow-sm">Update Season</button>
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
            <div class="modal-header border-bottom border-light p-3">
                <h5 class="fw-bold text-dark mb-0"><i class="bi bi-folder-plus text-warning me-2"></i>Buat Season Baru</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.seasons.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="small fw-bold text-secondary mb-1 text-uppercase">Nama Season</label>
                        <input type="text" name="name" class="form-control rounded-3 shadow-none border-light-subtle" placeholder="Contoh: Season 13" required>
                    </div>

                    <div class="mb-3">
                        <label class="small fw-bold text-secondary mb-1 text-uppercase">Prize Pool</label>
                        <input type="text" name="prize_pool" class="form-control rounded-3 shadow-none border-light-subtle" placeholder="Contoh: Rp 1.000.000" required>
                    </div>

                    <div class="mb-3">
                        <label class="small fw-bold text-secondary mb-1 text-uppercase">Upload Poster</label>
                        <input type="file" name="poster" class="form-control rounded-3 shadow-none border-light-subtle" accept="image/*" required onchange="previewImage(this, 'addPosterPreview')">
                        <div class="mt-2 text-center">
                            <img id="addPosterPreview" src="" class="img-thumbnail rounded-3 shadow-sm" style="max-height: 120px; display: none;">
                        </div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="small fw-bold text-secondary mb-1 text-uppercase">Harga (Rp)</label>
                            <input type="number" name="price" class="form-control rounded-3 shadow-none border-light-subtle" placeholder="10000" required>
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold text-secondary mb-1 text-uppercase">Total Slot</label>
                            <input type="number" name="slot" class="form-control rounded-3 shadow-none border-light-subtle" placeholder="32" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="small fw-bold text-secondary mb-1 text-uppercase">Keterangan Waktu</label>
                        <input type="text" name="date_info" class="form-control rounded-3 shadow-none border-light-subtle" placeholder="Contoh: 21 - 25 Jan 2026" required>
                    </div>

                    <div class="mb-3">
                        <label class="small fw-bold text-secondary mb-1 text-uppercase">Link Grup WhatsApp</label>
                        <input type="url" name="wa_link" class="form-control rounded-3 shadow-none border-light-subtle" placeholder="https://chat.whatsapp.com/...">
                    </div>

                    <div class="mb-3">
                        <label class="small fw-bold text-secondary mb-1 text-uppercase">Link Rules (Google Drive PDF)</label>
                        <input type="url" name="rules_link" class="form-control rounded-3 shadow-none border-light-subtle" placeholder="https://drive.google.com/...">
                    </div>

                    <div class="mb-3">
                        <label class="small fw-bold text-secondary mb-1 text-uppercase">Informasi Jadwal Rentang Waktu (Optional)</label>
                        <textarea name="schedule_info" class="form-control rounded-3 shadow-none border-light-subtle" rows="3" placeholder="Contoh:&#10;Babak 1: 20:00 - 20:40 WIB&#10;Babak 2: 20:40 - 21:20 WIB"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 pt-0">
                    <button type="button" class="btn btn-light fw-bold px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning fw-bold px-4 text-dark shadow-sm">Simpan Season</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- SCRIPT DAN STYLING --}}
<style>
    .season-card {
        background: #ffffff;
        border: 1px solid rgba(241, 245, 249, 0.8);
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.01);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        overflow: hidden;
    }
    .season-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 20px -5px rgba(0, 0, 0, 0.04), 0 8px 8px -5px rgba(0, 0, 0, 0.02);
        border-color: rgba(226, 232, 240, 0.8);
    }
    .season-card:hover .poster-img {
        transform: scale(1.05);
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
        background: rgba(0,0,0,0.5);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
        z-index: 5;
    }
    .finished-season {
        filter: grayscale(40%);
        opacity: 0.85;
    }
    .btn-glass-dark {
        background: rgba(15, 23, 42, 0.45);
        backdrop-filter: blur(4px);
        border: 1px solid rgba(255, 255, 255, 0.15);
        transition: all 0.2s ease;
    }
    .btn-glass-dark:hover {
        background: rgba(15, 23, 42, 0.75);
    }
</style>

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
        window.location.href = `?status=${status}&season_id=${seasonId}`;
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