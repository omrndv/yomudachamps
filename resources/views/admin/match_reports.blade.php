@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4" style="background-color: #f8fafc; min-height: 100vh;">
    {{-- Breadcrumb & Header --}}
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2" style="font-size: 0.85rem;">
                    <li class="breadcrumb-item"><a href="{{ route('admin.seasons') }}" class="text-decoration-none text-warning fw-semibold">Daftar Season</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard', $season->id) }}" class="text-decoration-none text-warning fw-semibold">{{ $season->name }}</a></li>
                    <li class="breadcrumb-item active text-secondary" aria-current="page">Laporan Skor</li>
                </ol>
            </nav>
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h2 class="fw-bold text-dark m-0" style="font-size: 1.75rem; letter-spacing: -0.5px;">
                        Laporan Hasil Tanding <span class="text-warning">{{ $season->name }}</span>
                    </h2>
                    <p class="text-secondary small mb-0 mt-1">
                        Verifikasi laporan skor dan bukti screenshot yang dikirimkan oleh perwakilan tim secara mandiri.
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.season.bracket', $season->id) }}" class="btn btn-outline-secondary btn-sm px-3 fw-bold rounded-pill shadow-sm">
                        <i class="bi bi-arrow-left me-1"></i> Kelola Bagan
                    </a>
                    
                    @if($reports->count() > 0)
                        <form action="{{ route('admin.season.match-reports.clear-all', $season->id) }}" method="POST" onsubmit="return confirm('PERINGATAN! Ini akan menghapus semua data laporan tanding beserta berkas bukti screenshot fisik di server untuk menghemat storage. Lanjutkan?')">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-sm px-3 fw-bold rounded-pill shadow-sm">
                                <i class="bi bi-trash3-fill me-1"></i> Hapus & Bersihkan Semua Laporan
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content Card --}}
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.9rem;">
                    <thead class="table-light text-secondary uppercase fw-semibold" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                        <tr>
                            <th class="ps-4 py-3">Waktu Kirim</th>
                            <th class="py-3">Pertandingan (Bagan)</th>
                            <th class="py-3 text-center">Skor Laporan</th>
                            <th class="py-3">Pelapor</th>
                            <th class="py-3">Bukti Screenshot</th>
                            <th class="py-3 text-center">Status</th>
                            <th class="pe-4 py-3 text-end">Tindakan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reports as $report)
                            <tr>
                                <td class="ps-4 text-secondary small">
                                    {{ $report->created_at->format('d M Y, H:i') }} WIB
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">
                                        {{ $report->bracket->team1->name ?? 'TBD' }}
                                        <span class="text-secondary fw-normal px-1">vs</span>
                                        {{ $report->bracket->team2->name ?? 'TBD' }}
                                    </div>
                                    <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2.5 py-1 mt-1" style="font-size: 0.62rem;">
                                        Babak {{ $report->bracket->round_number }} (Match {{ $report->bracket->match_number }})
                                    </span>
                                </td>
                                <td class="text-center fw-bold text-warning fs-5">
                                    {{ $report->score_team1 }} - {{ $report->score_team2 }}
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $report->reporterTeam->name }}</div>
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $report->reporterTeam->wa_number) }}" target="_blank" class="text-success text-decoration-none small">
                                        <i class="bi bi-whatsapp"></i> {{ $report->reporterTeam->wa_number }}
                                    </a>
                                </td>
                                <td>
                                    @if($report->image_proof)
                                        <a href="#" class="preview-trigger" data-img="{{ $report->image_proof }}">
                                            <div class="d-inline-flex align-items-center gap-1.5 p-1 px-2.5 rounded-3 border border-secondary border-opacity-15 bg-light hover-shadow" style="transition: 0.2s;">
                                                <i class="bi bi-image text-primary"></i>
                                                <span class="small text-secondary fw-bold" style="font-size: 0.72rem;">LIHAT BUKTI</span>
                                            </div>
                                        </a>
                                    @else
                                        <span class="text-muted italic small">Tidak ada gambar</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($report->status === 'PENDING')
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2.5 py-1.5" style="font-size: 0.68rem; font-weight: 700;">
                                            PENDING
                                        </span>
                                    @elseif($report->status === 'APPROVED')
                                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1.5" style="font-size: 0.68rem; font-weight: 700;">
                                            APPROVED
                                        </span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-2.5 py-1.5" style="font-size: 0.68rem; font-weight: 700;">
                                            REJECTED
                                        </span>
                                    @endif
                                </td>
                                <td class="pe-4 text-end">
                                    @if($report->status === 'PENDING')
                                        <div class="d-inline-flex gap-1.5">
                                            <form action="{{ route('admin.match-report.approve', $report->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin skor dan bukti sudah sesuai? Bagan otomatis ter-update!')">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm px-3 fw-bold rounded-pill shadow-sm" style="font-size: 0.75rem;">
                                                    <i class="bi bi-check-lg"></i> Terima
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.match-report.reject', $report->id) }}" method="POST" onsubmit="return confirm('Tolak laporan skor ini?')">
                                                @csrf
                                                <button type="submit" class="btn btn-outline-danger btn-sm px-3 fw-bold rounded-pill shadow-sm" style="font-size: 0.75rem;">
                                                    Tolak
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-muted small">Selesai diperiksa</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-secondary">
                                    <i class="bi bi-journal-x d-block mb-3 text-muted" style="font-size: 3rem;"></i>
                                    <h6 class="fw-bold mb-1">Belum Ada Laporan Laga</h6>
                                    <p class="small text-muted mb-0">Peserta belum mengirimkan laporan skor untuk season ini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Image Zoom Preview Modal --}}
<div class="modal fade" id="modalImagePreview" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 bg-transparent">
            <div class="modal-header border-0 pb-0 justify-content-end p-2">
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" style="filter: drop-shadow(0 2px 5px rgba(0,0,0,0.5));"></button>
            </div>
            <div class="modal-body p-0 text-center">
                <img id="previewImageSrc" src="" alt="Bukti Screenshot" class="img-fluid rounded-4 shadow-lg" style="max-height: 85vh; object-fit: contain;">
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modalPreview = new bootstrap.Modal(document.getElementById('modalImagePreview'));
        const previewImg = document.getElementById('previewImageSrc');

        document.querySelectorAll('.preview-trigger').forEach(trigger => {
            trigger.addEventListener('click', function(e) {
                e.preventDefault();
                const src = this.getAttribute('data-img');
                previewImg.src = src;
                modalPreview.show();
            });
        });
    });
</script>
@endpush
