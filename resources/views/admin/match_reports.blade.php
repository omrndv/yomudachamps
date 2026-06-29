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

    @php
        $countAll = $reports->count();
        $countPending = $reports->where('status', 'PENDING')->count();
        $countApproved = $reports->where('status', 'APPROVED')->count();
        $countRejected = $reports->where('status', 'REJECTED')->count();
    @endphp

    {{-- CSS Styles for Active Category Tabs (Premium Glassmorphic Design) --}}
    <style>
        #reportTabs {
            background-color: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(10px);
            border-radius: 100px;
            border: 1px solid rgba(226, 232, 240, 0.8);
            display: inline-flex !important;
            padding: 5px;
        }
        #reportTabs button {
            color: #64748b;
            background-color: transparent;
            font-size: 0.78rem;
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            border: none;
            outline: none;
        }
        #reportTabs button:hover {
            background-color: #f1f5f9;
            color: #1e293b;
        }
        #reportTabs button.active#tab-all {
            background-color: #0f172a !important;
            color: #ffffff !important;
        }
        #reportTabs button.active#tab-all .badge {
            background-color: rgba(255, 255, 255, 0.15) !important;
            color: #ffffff !important;
        }
        #reportTabs button.active#tab-pending {
            background-color: #f59e0b !important;
            color: #ffffff !important;
        }
        #reportTabs button.active#tab-pending .badge {
            background-color: rgba(255, 255, 255, 0.2) !important;
            color: #ffffff !important;
        }
        #reportTabs button.active#tab-approved {
            background-color: #10b981 !important;
            color: #ffffff !important;
        }
        #reportTabs button.active#tab-approved .badge {
            background-color: rgba(255, 255, 255, 0.2) !important;
            color: #ffffff !important;
        }
        #reportTabs button.active#tab-rejected {
            background-color: #ef4444 !important;
            color: #ffffff !important;
        }
        #reportTabs button.active#tab-rejected .badge {
            background-color: rgba(255, 255, 255, 0.2) !important;
            color: #ffffff !important;
        }
    </style>

    {{-- Categories / Tabs Filter --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex flex-wrap gap-1" id="reportTabs">
                <button class="btn btn-sm rounded-pill px-4 py-2.5 fw-bold active d-flex align-items-center gap-2" id="tab-all" data-status="ALL">
                    <i class="bi bi-grid-fill"></i> Semua Laporan 
                    <span class="badge bg-secondary-subtle text-secondary px-2.5 py-1 rounded-pill" style="font-size: 0.65rem;">{{ $countAll }}</span>
                </button>
                <button class="btn btn-sm rounded-pill px-4 py-2.5 fw-bold d-flex align-items-center gap-2" id="tab-pending" data-status="PENDING">
                    <i class="bi bi-clock-history"></i> Menunggu Persetujuan 
                    <span class="badge bg-warning-subtle text-warning px-2.5 py-1 rounded-pill" style="font-size: 0.65rem;">{{ $countPending }}</span>
                </button>
                <button class="btn btn-sm rounded-pill px-4 py-2.5 fw-bold d-flex align-items-center gap-2" id="tab-approved" data-status="APPROVED">
                    <i class="bi bi-check-circle-fill"></i> Disetujui 
                    <span class="badge bg-success-subtle text-success px-2.5 py-1 rounded-pill" style="font-size: 0.65rem;">{{ $countApproved }}</span>
                </button>
                <button class="btn btn-sm rounded-pill px-4 py-2.5 fw-bold d-flex align-items-center gap-2" id="tab-rejected" data-status="REJECTED">
                    <i class="bi bi-x-circle-fill"></i> Ditolak 
                    <span class="badge bg-danger-subtle text-danger px-2.5 py-1 rounded-pill" style="font-size: 0.65rem;">{{ $countRejected }}</span>
                </button>
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
                            <tr data-status="{{ $report->status }}" id="report-row-{{ $report->id }}">
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
                            <tr id="row-empty-state">
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

        // Preload all screenshot images in the background on page load
        document.querySelectorAll('.preview-trigger').forEach(trigger => {
            const src = trigger.getAttribute('data-img');
            if (src) {
                const img = new Image();
                img.src = src;
            }
        });

        document.querySelectorAll('.preview-trigger').forEach(trigger => {
            trigger.addEventListener('click', function(e) {
                e.preventDefault();
                const src = this.getAttribute('data-img');
                previewImg.src = src;
                modalPreview.show();
            });
        });

        // Client-side category filtering
        const tabButtons = document.querySelectorAll('#reportTabs button');
        const rows = document.querySelectorAll('tbody tr:not(#row-empty-state)');
        const emptyState = document.getElementById('row-empty-state');

        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                tabButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');

                const status = this.getAttribute('data-status');
                let visibleRows = 0;

                rows.forEach(row => {
                    const rowStatus = row.getAttribute('data-status');
                    if (status === 'ALL' || rowStatus === status) {
                        row.style.display = '';
                        visibleRows++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                if (emptyState) {
                    emptyState.style.display = (visibleRows === 0) ? '' : 'none';
                }
            });
        });

        // Web Audio API Synthesizer Chime Notification for new incoming match reports
        function playNewReportChime() {
            try {
                const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                
                // First note
                const osc1 = audioCtx.createOscillator();
                const gain1 = audioCtx.createGain();
                osc1.type = 'sine';
                osc1.frequency.setValueAtTime(659.25, audioCtx.currentTime); // E5
                gain1.gain.setValueAtTime(0, audioCtx.currentTime);
                gain1.gain.linearRampToValueAtTime(0.25, audioCtx.currentTime + 0.05);
                gain1.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.35);
                osc1.connect(gain1);
                gain1.connect(audioCtx.destination);
                
                // Second note
                const osc2 = audioCtx.createOscillator();
                const gain2 = audioCtx.createGain();
                osc2.type = 'sine';
                osc2.frequency.setValueAtTime(987.77, audioCtx.currentTime + 0.12); // B5
                gain2.gain.setValueAtTime(0, audioCtx.currentTime + 0.12);
                gain2.gain.linearRampToValueAtTime(0.25, audioCtx.currentTime + 0.15);
                gain2.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.5);
                osc2.connect(gain2);
                gain2.connect(audioCtx.destination);

                // Third note
                const osc3 = audioCtx.createOscillator();
                const gain3 = audioCtx.createGain();
                osc3.type = 'sine';
                osc3.frequency.setValueAtTime(1318.51, audioCtx.currentTime + 0.24); // E6
                gain3.gain.setValueAtTime(0, audioCtx.currentTime + 0.24);
                gain3.gain.linearRampToValueAtTime(0.3, audioCtx.currentTime + 0.28);
                gain3.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.7);
                osc3.connect(gain3);
                gain3.connect(audioCtx.destination);
                
                osc1.start();
                osc1.stop(audioCtx.currentTime + 0.4);
                osc2.start(audioCtx.currentTime + 0.12);
                osc2.stop(audioCtx.currentTime + 0.6);
                osc3.start(audioCtx.currentTime + 0.24);
                osc3.stop(audioCtx.currentTime + 1.0);
            } catch (e) {
                console.error('AudioContext error:', e);
            }
        }

        // Live Poll tracking for new reports
        let knownReportIds = new Set();
        document.querySelectorAll('tbody tr[data-status]').forEach(row => {
            const idStr = row.getAttribute('id');
            if (idStr) {
                knownReportIds.add(idStr.replace('report-row-', ''));
            }
        });

        function pollNewReports() {
            fetch("{{ route('admin.season.match-reports.poll', $season->id) }}")
                .then(r => r.json())
                .then(data => {
                    let hasNew = false;
                    const reports = data.reports;
                    
                    reports.forEach(r => {
                        if (!knownReportIds.has(String(r.id))) {
                            knownReportIds.add(String(r.id));
                            if (r.status === 'PENDING') {
                                hasNew = true;
                            }
                        }
                    });

                    if (hasNew) {
                        playNewReportChime();
                        // Automatically update data by reloading the page so CSRF tokens & html actions are fresh
                        setTimeout(() => {
                            window.location.reload();
                        }, 800);
                    }
                })
                .catch(err => console.error('Polling error:', err));
        }

        // Poll every 8 seconds
        setInterval(pollNewReports, 8000);
    });
</script>
@endpush
