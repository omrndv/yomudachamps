@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4" style="background-color: #f8fafc; min-height: 100vh;">
    {{-- Validation and Feedback --}}
    @if ($errors->any())
        <div class="alert alert-danger py-2 small border-0 mb-3 rounded-3 shadow-sm">
            @foreach ($errors->all() as $error)
                <li class="list-unstyled"><i class="bi bi-exclamation-triangle-fill me-2"></i>{{ $error }}</li>
            @endforeach
        </div>
    @endif
    @if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                background: '#ffffff',
                color: '#1e293b',
            });
            Toast.fire({
                icon: 'success',
                title: "{{ session('success') }}"
            });
        });
    </script>
    @endif
    @if(session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 4000,
                timerProgressBar: true,
                background: '#ffffff',
                color: '#1e293b',
            });
            Toast.fire({
                icon: 'error',
                title: "{{ session('error') }}"
            });
        });
    </script>
    @endif

    {{-- Breadcrumb & Header --}}
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2" style="font-size: 0.85rem;">
                    <li class="breadcrumb-item"><a href="{{ route('admin.seasons') }}" class="text-decoration-none text-warning fw-semibold">Daftar Season</a></li>
                    <li class="breadcrumb-item active text-secondary" aria-current="page">{{ $current_season->name }}</li>
                </ol>
            </nav>
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h2 class="fw-bold text-dark m-0" style="font-size: 1.75rem; letter-spacing: -0.5px;">
                        Kelola Peserta <span class="text-warning">{{ $current_season->name }}</span>
                    </h2>
                    <p class="text-secondary small mb-0 mt-1">Kelola data pendaftar, verifikasi pembayaran, ekspor bracket, dan koordinasi grup WA.</p>
                </div>
                <div class="d-flex gap-2 flex-wrap">
                    {{-- Toggle Duplicate --}}
                    <button type="button" id="btnFilterDuplicate" class="btn btn-outline-danger btn-sm px-3 fw-bold rounded-pill shadow-sm" onclick="toggleDuplicateFilter()">
                        <i class="bi bi-filter-square me-1"></i> Lihat Duplikat
                    </button>
                    {{-- Format WA --}}
                    <button type="button" class="btn btn-outline-success btn-sm px-3 fw-bold rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#modalExportWA">
                        <i class="bi bi-whatsapp me-1"></i> Format WA
                    </button>
                    {{-- Kelola Bracket --}}
                    <a href="{{ route('admin.season.bracket', $current_season->id) }}" class="btn btn-outline-primary btn-sm px-3 fw-bold rounded-pill shadow-sm">
                        <i class="bi bi-diagram-3 me-1"></i> Kelola Bracket
                    </a>
                    {{-- Solo Matchmaker --}}
                    <a href="{{ route('admin.solo.matchmaker', $current_season->id) }}" class="btn btn-warning btn-sm px-3 fw-bold rounded-pill shadow-sm">
                        <i class="bi bi-people me-1"></i> Solo Matchmaker
                    </a>
                    {{-- Cetak Sertifikat --}}
                    <a href="{{ route('admin.season.certificate', $current_season->id) }}" class="btn btn-outline-warning text-dark btn-sm px-3 fw-bold rounded-pill shadow-sm">
                        <i class="bi bi-file-earmark-pdf me-1"></i> Cetak Sertifikat
                    </a>
                    {{-- Bulk Add --}}
                    <button class="btn btn-dark btn-sm px-3 fw-bold rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#modalBulk">
                        <i class="bi bi-stack me-1"></i> Bulk Add
                    </button>
                    {{-- Reset Data --}}
                    <a href="{{ route('admin.team.deleteAll', $current_season->id) }}"
                        class="btn btn-outline-danger btn-sm px-3 fw-bold rounded-pill shadow-sm"
                        onclick="return confirm('PERINGATAN! Semua data tim di season ini akan dihapus permanen. Lanjutkan?')">
                        <i class="bi bi-trash3-fill me-1"></i> Reset Data
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Bulk Delete Hidden Form --}}
    <form id="formBulkDelete" action="{{ route('admin.team.bulkDelete') }}" method="POST">
        @csrf
        <input type="hidden" name="team_ids" id="selectedIds">
    </form>

    @php
        $total_pendaftar = $filtered_teams->count(); // Semua (Pending + Paid)
        $total_paid = $filtered_teams->where('status', 'PAID')->count();
        $total_pending = $total_pendaftar - $total_paid;
        $total_slot = $current_season->slot;
        
        // Persentase untuk Bar
        $persen_paid = ($total_slot > 0) ? ($total_paid / $total_slot) * 100 : 0;
        $persen_pending = ($total_slot > 0) ? ($total_pending / $total_slot) * 100 : 0;
    @endphp

    @php
        $overSlots = $filtered_teams->where('status', 'FAILED')
            ->filter(function($t) {
                return in_array(strtoupper($t->status_tripay), ['PAID', 'BERHASIL', 'SUCCESS', 'SETTLEMENT']);
            });
    @endphp

    @if($overSlots->count() > 0)
        <div class="alert alert-warning border-0 rounded-4 p-3 mb-4 d-flex justify-content-between align-items-center shadow-sm" style="background-color: #fffbeb; border-left: 5px solid #f59e0b !important;">
            <div class="d-flex align-items-center gap-3">
                <span class="d-inline-flex align-items-center justify-content-center bg-warning bg-opacity-20 text-warning rounded-circle" style="width: 48px; height: 48px;">
                    <i class="bi bi-exclamation-triangle-fill fs-4"></i>
                </span>
                <div>
                    <h6 class="fw-bold text-dark mb-1">Ada Pembayaran Bocor / Over-Slot!</h6>
                    <p class="text-secondary small mb-0">Terdapat <strong>{{ $overSlots->count() }} tim</strong> yang sudah membayar sukses namun status pendaftarannya gagal karena slot turnamen penuh.</p>
                </div>
            </div>
            <button class="btn btn-warning text-dark fw-bold rounded-pill px-4 btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#modalOverSlot">
                <i class="bi bi-eye-fill me-1"></i> Tampilkan Tim Over-Slot
            </button>
        </div>
    @endif

    {{-- Stats Cards Row --}}
    <div class="row g-3 mb-4">
        {{-- Card 1: Tim Lunas & Slot --}}
        <div class="col-md-6">
            <div class="card card-custom border-0 p-4 bg-white shadow-sm rounded-4 h-100">
                <div class="d-flex justify-content-between mb-3">
                    <div>
                        <p class="text-secondary small fw-bold mb-1 text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Tim Lunas / Slot</p>
                        <h3 class="fw-bold text-dark mb-0" style="font-size: 1.6rem; letter-spacing: -0.5px;">
                            {{ $total_paid }} <span class="fs-6 text-muted fw-normal">/ {{ $total_slot }} Slot</span>
                        </h3>
                    </div>
                    <div class="icon-shape text-success bg-success-subtle" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; border-radius: 12px;">
                        <i class="bi bi-shield-check fs-5"></i>
                    </div>
                </div>
                
                {{-- Progress Bar Lunas --}}
                <div class="progress shadow-none bg-light-subtle rounded-pill mb-3" style="height: 8px; background-color: #f1f5f9;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $persen_paid }}%;" title="Lunas: {{ $total_paid }}"></div>
                </div>

                <div class="d-flex justify-content-between align-items-center" style="font-size: 0.75rem;">
                    <span class="badge bg-success text-white px-2.5 py-1 rounded-pill">
                        Lunas: <strong>{{ $total_paid }} Tim</strong>
                    </span>
                    <span class="fw-bold {{ ($total_slot - $total_paid) <= 5 ? 'text-danger animate-pulse' : 'text-muted' }}">
                        Sisa Slot Lunas: {{ max(0, $total_slot - $total_paid) }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Card 2: Estimasi Pendapatan --}}
        <div class="col-md-6">
            <div class="card card-custom border-0 p-4 bg-white shadow-sm rounded-4 h-100 d-flex flex-column justify-content-between">
                <div class="d-flex justify-content-between mb-2">
                    <div>
                        <p class="text-secondary small fw-bold mb-1 text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Pendapatan Bersih (Total Akhir)</p>
                        <h3 class="fw-bold {{ $net_income >= 0 ? 'text-success' : 'text-danger' }} mb-0" style="font-size: 1.6rem; letter-spacing: -0.5px;">
                            Rp {{ number_format($net_income, 0, ',', '.') }}
                        </h3>
                    </div>
                    <div class="icon-shape {{ $net_income >= 0 ? 'text-success bg-success-subtle' : 'text-danger bg-danger-subtle' }}" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; border-radius: 12px;">
                        <i class="bi bi-wallet2 fs-5"></i>
                    </div>
                </div>
                <div class="row text-center mt-2 border-top border-light pt-2" style="font-size: 0.72rem;">
                    <div class="col-4 border-end">
                        <span class="text-muted d-block">Registrasi (Gross)</span>
                        <strong class="text-dark">Rp {{ number_format($total_income, 0, ',', '.') }}</strong>
                    </div>
                    <div class="col-4 border-end">
                        <span class="text-muted d-block">Pemasukan Lain</span>
                        <strong class="text-success">+ Rp {{ number_format($additional_income, 0, ',', '.') }}</strong>
                    </div>
                    <div class="col-4">
                        <span class="text-muted d-block">Pengeluaran</span>
                        <strong class="text-danger">- Rp {{ number_format($total_expense, 0, ',', '.') }}</strong>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top border-light">
                    <p class="small text-muted mb-0" style="font-size: 0.7rem;">
                        Total Akhir = Registrasi + Pemasukan Lain - Pengeluaran
                    </p>
                    <a href="{{ route('admin.season.finance.index', $current_season->id) }}" class="btn btn-warning btn-sm fw-bold px-3 rounded-pill shadow-sm text-dark" style="font-size: 0.75rem;">
                        <i class="bi bi-bank me-1"></i> Rincian & Kelola
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Container / Table --}}
    <div class="card border-0 shadow-sm p-4 rounded-4 bg-white" style="border: 1px solid rgba(0, 0, 0, 0.06) !important;">
        <div class="row g-3 mb-4 align-items-center">
            <div class="col-md-4">
                <div class="search-box-season">
                    <i class="bi bi-search"></i>
                    <input type="text" id="searchTable" placeholder="Cari nama tim atau ID TRX...">
                </div>
            </div>
            <div class="col-md-8 text-md-end text-muted small" id="tableMetaInfo">
                Menampilkan <span class="fw-bold text-dark">{{ $filtered_teams->count() }}</span> pendaftar.
            </div>
        </div>

        <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
            <table class="table table-hover align-middle mb-0 season-table" style="font-size: 0.85rem;">
                <thead class="bg-light" style="position: sticky; top: 0; z-index: 1;">
                    <tr class="text-secondary small fw-bold" style="font-size: 0.75rem; border-bottom: 2px solid #f1f5f9;">
                        <th class="px-3 py-3 border-0" width="40">
                            <input type="checkbox" class="form-check-input" id="checkAll">
                        </th>
                        <th class="py-3 border-0" width="50">#</th>
                        <th class="py-3 border-0">Waktu Daftar</th>
                        <th class="py-3 border-0">ID TRX</th>
                        <th class="py-3 border-0">Nama Tim</th>
                        <th class="py-3 border-0">Nomor WhatsApp</th>
                        <th class="text-center py-3 border-0">Status</th>
                        <th class="text-center py-3 border-0" width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $name_counts = $filtered_teams->groupBy('name')->map->count();
                        $wa_counts = $filtered_teams->groupBy('wa_number')->map->count();
                    @endphp

                    @forelse($filtered_teams as $index => $team)
                    @php
                        $is_duplicate = ($name_counts[$team->name] > 1 || $wa_counts[$team->wa_number] > 1);
                    @endphp
                    <tr class="team-row {{ $is_duplicate ? 'is-duplicate' : '' }}" style="border-bottom: 1px solid #f8fafc; {{ $is_duplicate ? 'border-left: 4px solid #ef4444 !important; background-color: rgba(239, 68, 68, 0.02) !important;' : '' }}">
                        <td class="px-3">
                            <input type="checkbox" class="form-check-input team-checkbox" value="{{ $team->id }}">
                        </td>
                        <td class="text-muted fw-semibold">{{ $index + 1 }}</td>
                        
                        <td>
                            <span class="d-block small fw-bold text-dark">{{ date('d M', strtotime($team->created_at)) }}</span>
                            <span class="d-block text-muted" style="font-size: 0.7rem;">{{ date('H:i', strtotime($team->created_at)) }} WIB</span>
                        </td>

                        <td class="small fw-bold text-primary">#{{ $team->trx_id }}</td>
                        <td>
                            <span class="fw-bold text-slate-800">{{ $team->name }}</span>
                            @if($name_counts[$team->name] > 1)
                                <span class="badge bg-danger text-white ms-1" style="font-size: 0.6rem;">NAMA DOUBLE ({{ $name_counts[$team->name] }})</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <code class="text-dark fw-bold" style="font-size: 0.8rem;">{{ $team->wa_number }}</code>
                                
                                @if($team->is_loyal)
                                    <span class="badge bg-primary text-white px-2 d-flex align-items-center gap-1" 
                                          style="font-size: 0.6rem; cursor: pointer;" 
                                          data-bs-toggle="tooltip" 
                                          data-bs-html="true" 
                                          title="<strong>Pelanggan Setia!</strong><br>Pernah ikut di:<br> 
                                          @foreach($team->history as $h)
                                            • {{ $h->season->name }} <i>({{ $h->name }})</i><br>
                                          @endforeach">
                                        <i class="bi bi-star-fill text-warning"></i> LOYALTY ({{ $team->history->count() }})
                                    </span>
                                @endif
                    
                                @if($wa_counts[$team->wa_number] > 1)
                                    <span class="badge bg-warning text-dark px-2" style="font-size: 0.6rem;">WA DOUBLE ({{ $wa_counts[$team->wa_number] }})</span>
                                @endif
                                
                                @php
                                    $wa = preg_replace('/[^0-9]/', '', $team->wa_number);
                                    $wa_link = str_starts_with($wa, '0') ? '62' . substr($wa, 1) : (str_starts_with($wa, '8') ? '62' . $wa : $wa);
                                @endphp
                                <a href="https://wa.me/{{ $wa_link }}" target="_blank" class="btn btn-sm btn-light-success text-success border border-success-subtle rounded-circle p-1 d-flex align-items-center justify-content-center" style="width: 24px; height: 24px;" title="Chat WhatsApp">
                                    <i class="bi bi-whatsapp" style="font-size: 0.75rem;"></i>
                                </a>
                            </div>
                        </td>
                        
                        <td class="text-center">
                            <span class="badge {{ $team->status == 'PAID' ? 'bg-success text-white' : 'bg-warning text-dark' }} px-3 py-1.5 rounded-pill" style="font-size: 0.65rem; font-weight: 600;">
                                {{ $team->status }}
                            </span>
                        </td>
                        
                        <td class="text-center">
                            <div class="d-flex justify-content-center gap-2">
                                <a href="javascript:void(0)" class="btn btn-sm btn-outline-secondary border border-light-subtle rounded-3 p-1.5 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $team->id }}">
                                    <i class="bi bi-pencil-square" style="font-size: 0.85rem;"></i>
                                </a>
                                <a href="{{ route('admin.team.delete', $team->id) }}"
                                    class="btn btn-sm btn-outline-danger border border-danger-subtle rounded-3 p-1.5 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;"
                                    onclick="return confirm('Yakin mau hapus tim {{ $team->name }}?')">
                                    <i class="bi bi-trash" style="font-size: 0.85rem;"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-emoji-frown fs-2 d-block mb-2 text-secondary opacity-50"></i>
                            Belum ada peserta terdaftar di season ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modern Floating Action Bar for Bulk Delete (UX Delight!) --}}
<div id="floatingActionBar" class="fixed-bottom d-none justify-content-center mb-4 animate-slide-up" style="z-index: 1040;">
    <div class="bg-dark text-white rounded-pill px-4 py-3 shadow-lg d-flex align-items-center gap-3 border border-secondary border-opacity-20">
        <span class="small fw-semibold text-white-50"><span id="countSelectedFloat" class="text-warning fw-bold fs-5">0</span> Tim Terpilih</span>
        <div class="vr bg-secondary opacity-30 my-1"></div>
        <button type="button" class="btn btn-danger btn-sm rounded-pill px-3 fw-bold d-flex align-items-center gap-2" onclick="bulkDelete()">
            <i class="bi bi-trash-fill"></i> Hapus Terpilih
        </button>
        <button type="button" class="btn btn-outline-secondary btn-sm text-white border-0 rounded-pill px-2" onclick="deselectAll()">
            Batal
        </button>
    </div>
</div>

{{-- TEAM EDIT MODAL --}}
@foreach($filtered_teams as $team)
<div class="modal fade" id="modalEdit{{ $team->id }}" tabindex="-1" aria-hidden="true" style="z-index: 1065;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-bottom border-light p-3">
                <h5 class="fw-bold text-dark mb-0"><i class="bi bi-pencil-square text-warning me-2"></i>Edit Tim: {{ $team->name }}</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.team.update', $team->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4 text-start">
                    <div class="mb-3">
                        <label class="small fw-bold text-secondary mb-1">Nama Tim</label>
                        <input type="text" name="name" class="form-control rounded-3 shadow-none border-light-subtle" value="{{ $team->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold text-secondary mb-1">Nomor WhatsApp</label>
                        <input type="text" name="wa_number" class="form-control rounded-3 shadow-none border-light-subtle" value="{{ $team->wa_number }}" required>
                    </div>
                    <div class="mb-0">
                        <label class="small fw-bold text-secondary mb-1">Status Pembayaran</label>
                        <select name="status" class="form-select rounded-3 shadow-none border-light-subtle">
                            <option value="PENDING" {{ $team->status == 'PENDING' ? 'selected' : '' }}>PENDING</option>
                            <option value="PAID" {{ $team->status == 'PAID' ? 'selected' : '' }}>PAID (Lunas)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 pt-0">
                    <button type="button" class="btn btn-light fw-bold px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning fw-bold px-4 text-dark shadow-sm">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

{{-- BULK IMPORT MODAL --}}
<div class="modal fade" id="modalBulk" tabindex="-1" aria-hidden="true" style="z-index: 1055;">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom border-light p-3">
                <h5 class="fw-bold text-dark mb-0"><i class="bi bi-stack text-warning me-2"></i>Import Data (Bulk Add)</h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.bulk.store', $current_season->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="alert alert-info py-2 small border-0 mb-3 rounded-3 d-flex align-items-center">
                        <i class="bi bi-info-circle-fill me-2 fs-5"></i>
                        <div><strong>Format:</strong> Nama Tim [Spasi atau Tab] Nomor WA. Masukkan satu baris per tim.</div>
                    </div>
                    <textarea name="bulk_data" class="form-control rounded-3 border-light-subtle shadow-none p-3" rows="10" placeholder="Contoh:&#10;GARUDA TEAM 081234567890&#10;ELANG ESPORT 089988776655" required style="font-family: monospace; font-size: 0.85rem;"></textarea>
                </div>
                <div class="modal-footer border-0 p-3 pt-0">
                    <button type="button" class="btn btn-light fw-bold px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-dark fw-bold px-4 shadow-sm">Proses & Tambahkan</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- EXPORT WA MODAL (Bug Fix Applied!) --}}
<div class="modal fade" id="modalExportWA" tabindex="-1" aria-hidden="true" style="z-index: 1055;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-dark text-white p-3 border-0 rounded-top-4">
                <h5 class="fw-bold mb-0"><i class="bi bi-whatsapp me-2 text-success"></i>Format Salin WA</h5>
                <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body bg-light p-4">
                <p class="small text-secondary mb-2">Salin teks nomor WA di bawah untuk broadcast grup koordinasi:</p>
                <textarea id="textPengumuman" class="form-control border-0 shadow-sm p-3 rounded-3" rows="12" readonly
                    style="font-family: 'JetBrains Mono', Courier, monospace; font-size: 13px; resize: none; background: #fff; line-height: 1.5;">📢 PENGUMUMAN NOMOR WHATSAPP PERWAKILAN TIM

Berikut adalah daftar nomor WhatsApp perwakilan setiap tim.
Tim yang berada di bracket atas wajib membuat room dan mengundang tim lawan.

@forelse($paid_teams as $key => $team)
{{ $team->name }} --- {{ $team->wa_number }}
@if(($key + 1) % 10 == 0)

@endif
@empty
(Belum ada tim dengan status PAID)
@endforelse</textarea>
            </div>
            <div class="modal-footer bg-light border-0 p-4 pt-0 rounded-bottom-4">
                {{-- Bug Fix: copyToClipboard now correctly passes 'textPengumuman' --}}
                <button type="button" class="btn btn-success fw-bold w-100 py-2.5 rounded-pill shadow-sm" onclick="copyToClipboard('textPengumuman')">
                    <i class="bi bi-clipboard-check me-2"></i> SALIN TEKS SEKARANG
                </button>
            </div>
        </div>
    </div>
</div>

{{-- EXPORT BRACKET MODAL --}}
<div class="modal fade" id="modalExportBracket" tabindex="-1" aria-hidden="true" style="z-index: 1055;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0 bg-primary text-white p-3 rounded-top-4" style="background: linear-gradient(45deg, #0d6efd, #1d4ed8) !important;">
                <h5 class="modal-title fw-bold text-white mb-0">
                    <i class="bi bi-trophy-fill me-2"></i>List Bracket {{ $current_season->name }}
                </h5>
                <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body p-4 bg-light">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="small fw-bold text-secondary text-uppercase" style="font-size: 0.7rem;">
                        <i class="bi bi-info-circle me-1"></i> Format: Per Baris
                    </span>
                    <span class="badge bg-primary text-white rounded-pill px-3" style="font-size: 0.7rem;">
                        Total: {{ $paid_teams->count() }} Tim
                    </span>
                </div>

                <div class="position-relative">
                    <textarea id="textBracketOnly" 
                        class="form-control border-0 shadow-sm p-3 fw-bold" 
                        rows="12" 
                        readonly
                        style="font-family: 'JetBrains Mono', monospace; font-size: 14px; background: #ffffff; color: #1d4ed8; border-radius: 12px; line-height: 1.6; resize: none;"
                    >@foreach($paid_teams as $team){{ $team->name }}&#10;@endforeach</textarea>
                </div>

                <p class="text-center small text-secondary mt-3 mb-0" style="font-size: 0.75rem;">
                    Teks di atas sudah siap langsung di-paste ke platform <strong>Challonge</strong> atau <strong>MobaZhi</strong>.
                </p>
            </div>

            <div class="modal-footer border-0 p-4 pt-0 bg-light rounded-bottom-4">
                <button type="button" 
                    class="btn btn-primary fw-bold w-100 py-2.5 shadow rounded-pill" 
                    onclick="copyToClipboard('textBracketOnly')"
                >
                    <i class="bi bi-clipboard2-check-fill me-2"></i> SALIN LIST NAMA TIM
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Custom Style --}}
<style>
    .card-custom {
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.01);
        border: 1px solid rgba(0, 0, 0, 0.06) !important;
    }
    .btn-light-success {
        background-color: rgba(16, 185, 129, 0.1);
        border: 1px solid rgba(16, 185, 129, 0.15);
        color: #10b981;
        transition: all 0.2s ease;
    }
    .btn-light-success:hover {
        background-color: #10b981;
        color: #fff !important;
    }
    .animate-slide-up {
        animation: slideUp 0.3s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    }
    @keyframes slideUp {
        from {
            transform: translate3d(0, 100px, 0);
            opacity: 0;
        }
        to {
            transform: translate3d(0, 0, 0);
            opacity: 1;
        }
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
    .season-table th {
        font-size: 0.72rem;
        letter-spacing: 0.8px;
        font-weight: 700;
        text-transform: uppercase;
        color: #64748b;
        background-color: #f8fafc;
        border-bottom: 1px solid #edf2f7;
        padding: 14px 16px;
    }
    .season-table td {
        padding: 14px 16px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.85rem;
    }
</style>

{{-- Table & Checkbox Actions Script --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
    
    const checkAll = document.getElementById('checkAll');
    const checkboxes = document.querySelectorAll('.team-checkbox');
    const floatingActionBar = document.getElementById('floatingActionBar');
    const countSelectedFloat = document.getElementById('countSelectedFloat');
    let showingOnlyDuplicates = false;

    function toggleDuplicateFilter() {
        const rows = document.querySelectorAll('.team-row');
        const btn = document.getElementById('btnFilterDuplicate');
        showingOnlyDuplicates = !showingOnlyDuplicates;

        rows.forEach(row => {
            if (showingOnlyDuplicates) {
                if (row.classList.contains('is-duplicate')) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
                btn.innerHTML = '<i class="bi bi-arrow-left-square me-1"></i> Lihat Semua';
                btn.classList.replace('btn-outline-danger', 'btn-danger');
            } else {
                row.style.display = "";
                btn.innerHTML = '<i class="bi bi-filter-square me-1"></i> Lihat Duplikat';
                btn.classList.replace('btn-danger', 'btn-outline-danger');
            }
        });
        updateFloatingActionBar();
    }

    function updateFloatingActionBar() {
        const checkedCount = document.querySelectorAll('.team-checkbox:checked').length;
        if (checkedCount > 0) {
            floatingActionBar.classList.remove('d-none');
            floatingActionBar.classList.add('d-flex');
            countSelectedFloat.innerText = checkedCount;
        } else {
            floatingActionBar.classList.add('d-none');
            floatingActionBar.classList.remove('d-flex');
        }
    }

    function deselectAll() {
        checkboxes.forEach(cb => cb.checked = false);
        if(checkAll) checkAll.checked = false;
        updateFloatingActionBar();
    }

    if(checkAll) {
        checkAll.addEventListener('change', function() {
            checkboxes.forEach(cb => {
                const row = cb.closest('tr');
                if(row.style.display !== 'none') {
                    cb.checked = this.checked;
                }
            });
            updateFloatingActionBar();
        });
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', updateFloatingActionBar);
    });

    function bulkDelete() {
        const selectedIds = Array.from(document.querySelectorAll('.team-checkbox:checked')).map(cb => cb.value);
        
        Swal.fire({
            title: 'Hapus Tim?',
            text: `Yakin ingin menghapus ${selectedIds.length} tim terpilih?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-danger rounded-pill px-4 fw-bold',
                cancelButton: 'btn btn-light rounded-pill px-4 fw-bold'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('selectedIds').value = JSON.stringify(selectedIds);
                document.getElementById('formBulkDelete').submit();
            }
        });
    }

    document.getElementById('searchTable').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('.team-row');
        let visibleCount = 0;

        rows.forEach(row => {
            let text = row.innerText.toLowerCase();
            if (showingOnlyDuplicates) {
                if (row.classList.contains('is-duplicate') && text.includes(filter)) {
                    row.style.display = "";
                    visibleCount++;
                } else {
                    row.style.display = "none";
                }
            } else {
                if (text.includes(filter)) {
                    row.style.display = "";
                    visibleCount++;
                } else {
                    row.style.display = "none";
                }
            }
        });
        
        // Update meta text count
        document.getElementById('tableMetaInfo').innerHTML = 'Menampilkan <span class="fw-bold text-dark">' + visibleCount + '</span> dari <span class="fw-bold">' + rows.length + '</span> pendaftar.';
        updateFloatingActionBar();
    });

    function copyToClipboard(elementId) {
        var copyText = document.getElementById(elementId);
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(copyText.value).then(() => {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Teks berhasil disalin ke clipboard!',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            });
        });
    }
</script>

{{-- Modal Over-Slot --}}
@if(isset($overSlots) && $overSlots->count() > 0)
<div class="modal fade" id="modalOverSlot" tabindex="-1" aria-hidden="true" style="z-index: 1060;">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-bottom border-light p-3 bg-danger text-white rounded-top-4" style="background: linear-gradient(45deg, #dc3545, #b91c1c) !important;">
                <h5 class="modal-title fw-bold text-white mb-0">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i> Laporan Pembayaran Over-Slot (Bocor)
                </h5>
                <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-light">
                <p class="text-secondary small mb-3">
                    Tim di bawah ini telah menyelesaikan pembayaran sukses di payment gateway, tetapi slot pendaftaran untuk season ini sudah penuh sesaat sebelum mereka terverifikasi. Anda harus menghubungi mereka untuk memproses pengembalian dana (refund) manual.
                </p>
                <div class="table-responsive rounded-3 overflow-hidden border border-light-subtle bg-white">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                        <thead class="table-dark">
                            <tr>
                                <th class="ps-3">Nama Tim</th>
                                <th>WhatsApp Kapten</th>
                                <th>Metode Bayar</th>
                                <th>TRX ID</th>
                                <th class="text-end pe-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($overSlots as $osTeam)
                                <tr>
                                    <td class="fw-bold text-dark ps-3">{{ $osTeam->name }}</td>
                                    <td>
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $osTeam->wa_number) }}" target="_blank" class="btn btn-sm btn-outline-success rounded-pill px-2.5 py-1 fw-semibold" style="font-size: 0.75rem;">
                                            <i class="bi bi-whatsapp me-1"></i> {{ $osTeam->wa_number }}
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle text-uppercase px-2 py-1" style="font-size: 0.65rem;">
                                            {{ str_contains($osTeam->payment_method ?? '', 'http') ? 'iPaymu' : ($osTeam->payment_method ?? 'TriPay') }}
                                        </span>
                                    </td>
                                    <td><code class="text-danger fw-bold">{{ $osTeam->trx_id }}</code></td>
                                    <td class="text-end pe-3">
                                        <a href="{{ route('admin.team.delete', $osTeam->id) }}" class="btn btn-sm btn-danger rounded-3" onclick="return confirm('Hapus tim over-slot ini dari daftar?')">
                                            <i class="bi bi-trash-fill"></i> Hapus
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-0 p-3 pt-0 bg-light rounded-bottom-4">
                <button type="button" class="btn btn-light fw-bold px-4" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endif
@endsection