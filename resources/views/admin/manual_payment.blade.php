@extends('layouts.admin')

@section('title', 'Pembayaran Manual')

@section('content')
<div class="container-fluid py-4">
<style>
    .claim-card {
        border-radius: 20px !important;
        border: 1px solid rgba(0, 0, 0, 0.08) !important;
        background: #fff !important;
        box-shadow: 0 6px 20px rgba(0,0,0,0.03) !important;
        margin-bottom: 24px !important;
        padding: 24px !important;
    }
    .dark .claim-card {
        background: #141618 !important;
        border-color: rgba(255, 255, 255, 0.05) !important;
    }
    .proof-img-wrapper {
        border-radius: 12px !important;
        overflow: hidden !important;
        border: 1px solid rgba(0,0,0,0.08) !important;
        background: #f8fafc !important;
        position: relative !important;
        cursor: pointer !important;
        height: 250px !important;
        width: 100% !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
    }
    .dark .proof-img-wrapper {
        background: #080809 !important;
        border-color: rgba(255,255,255,0.08) !important;
    }
    .proof-img-wrapper img {
        display: block !important;
        width: auto !important;
        height: auto !important;
        max-width: 100% !important;
        max-height: 100% !important;
        object-fit: contain !important;
    }
    .proof-img-overlay {
        position: absolute !important;
        inset: 0 !important;
        background: rgba(0,0,0,0.4) !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        opacity: 0 !important;
        transition: opacity 0.2s !important;
    }
    .proof-img-wrapper:hover .proof-img-overlay {
        opacity: 1 !important;
    }
    .sound-status-btn {
        font-weight: 800;
        font-size: 0.75rem;
        border-radius: 50px;
        padding: 6px 16px;
    }
    /* Lightbox Modal */
    .lightbox-modal {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.9);
        z-index: 9999;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 16px;
    }
    .lightbox-content {
        max-width: 100%;
        max-height: 90vh;
        object-fit: contain;
        border-radius: 8px;
    }
    .lightbox-close {
        position: absolute;
        top: 24px;
        right: 24px;
        color: #fff;
        font-size: 1.8rem;
        cursor: pointer;
        background: rgba(255,255,255,0.1);
        width: 44px;
        height: 44px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    /* Fix giant pagination SVGs */
    .pagination svg, nav[role="navigation"] svg {
        width: 16px !important;
        height: 16px !important;
        display: inline-block !important;
    }
</style>
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-0">Pembayaran Manual</h3>
            <p class="text-secondary small mb-0">Kelola QRIS statis, verifikasi klaim bukti transfer, dan riwayat dana masuk.</p>
        </div>
        <!-- Sound alert for verifications -->
        <button class="btn btn-outline-danger sound-status-btn d-flex align-items-center gap-2" id="toggle-sound" onclick="toggleSound()">
            <i class="bi bi-volume-mute-fill"></i> Sound Off
        </button>
    </div>

    <!-- Tab Switcher Controls -->
    <div class="d-flex gap-2 mb-4 overflow-auto pb-2">
        <button class="btn btn-warning text-dark fw-bold rounded-pill px-4 tab-nav-btn active" id="tab-btn-verifier" onclick="switchTab('verifier')">
            <i class="bi bi-shield-check me-1"></i> Antrean Verifikasi ({{ count($claimedTx) }})
        </button>
        <button class="btn btn-light fw-bold rounded-pill px-4 tab-nav-btn" id="tab-btn-settings" onclick="switchTab('settings')">
            <i class="bi bi-gear-fill me-1"></i> Pengaturan QRIS
        </button>
        <button class="btn btn-light fw-bold rounded-pill px-4 tab-nav-btn" id="tab-btn-history" onclick="switchTab('history')">
            <i class="bi bi-journal-text me-1"></i> Riwayat Transaksi
        </button>
    </div>

    <!-- TAB 1: Antrean Verifikasi -->
    <div class="tab-content-panel" id="tab-panel-verifier">
        <div class="row">
            <div class="col-12 col-xl-8">
                <div class="row">
                    @forelse($claimedTx as $tx)
                        @php
                            $team = $tx->team;
                            $filename = str_replace('PROOFS/', '', $tx->gopay_reference);
                            $imgUrl = asset('uploads/proofs/' . $filename);
                        @endphp
                        <div class="col-12 col-md-6 mb-4">
                            <div class="claim-card">
                                <div class="d-flex justify-content-between align-items-start border-bottom pb-3 mb-3 border-light border-opacity-10">
                                    <div>
                                        <h5 class="fw-extrabold mb-0 text-dark">{{ $team ? $team->name : 'Quick Checkout' }}</h5>
                                        <span class="text-warning small fw-bold">{{ ($team && $team->season) ? $team->season->name : 'Merchandise / Lainnya' }}</span>
                                    </div>
                                    <div class="text-end">
                                        <span class="text-muted d-block uppercase small fw-bold" style="font-size: 0.65rem;">Nominal</span>
                                        <h5 class="text-primary fw-black mb-0">Rp {{ number_format($tx->amount, 0, ',', '.') }}</h5>
                                    </div>
                                </div>

                                <div class="proof-img-wrapper mb-3" onclick="openLightbox('{{ $imgUrl }}')">
                                    <img src="{{ $imgUrl }}" alt="Bukti Transfer" loading="lazy">
                                    <div class="proof-img-overlay">
                                        <i class="bi bi-zoom-in text-white fs-3"></i>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    @if($team)
                                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $team->wa_number) }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-success rounded-pill fw-bold">
                                            <i class="bi bi-whatsapp me-1"></i> WhatsApp Kapten
                                        </a>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary py-1.5 px-3 rounded-pill fw-bold">No WhatsApp</span>
                                    @endif
                                    <span class="text-secondary font-monospace" style="font-size: 0.72rem;">TRX: {{ $tx->trx_id }}</span>
                                </div>

                                <div class="row g-2">
                                    <div class="col-4">
                                        <form action="/admin/manual-payment/settle/{{ $tx->trx_id }}" method="POST" onsubmit="return confirm('Setujui pembayaran tim ini?')">
                                            @csrf
                                            <button type="submit" class="btn btn-success fw-bold w-100 py-2.5 rounded-3" style="font-size: 0.78rem;">
                                                Setujui
                                            </button>
                                        </form>
                                    </div>
                                    <div class="col-4">
                                        <form action="/admin/manual-payment/reject/{{ $tx->trx_id }}" method="POST" onsubmit="return confirm('TOLAK pembayaran tim ini? Kapten akan diberitahu melalui WA untuk upload ulang.')">
                                            @csrf
                                            <button type="submit" class="btn btn-danger fw-bold w-100 py-2.5 rounded-3" style="font-size: 0.78rem;">
                                                Tolak
                                            </button>
                                        </form>
                                    </div>
                                    <div class="col-4">
                                        <form action="/admin/manual-payment/delete/{{ $tx->trx_id }}" method="POST" onsubmit="return confirm('HAPUS transaksi ini secara permanen dari sistem? Tindakan ini tidak bisa dibatalkan.')">
                                            @csrf
                                            <button type="submit" class="btn btn-secondary fw-bold w-100 py-2.5 rounded-3" style="font-size: 0.78rem;">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="card border-0 shadow-sm rounded-4 text-center py-5">
                                <div class="fs-1 text-muted mb-2">😴</div>
                                <h6 class="fw-bold mb-1">Semua Beres!</h6>
                                <p class="text-secondary small mb-0">Belum ada antrean bukti transfer baru yang diklaim oleh peserta.</p>
                            </div>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Recent verifications sidebar -->
            <div class="col-12 col-xl-4">
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4">
                    <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                        <i class="bi bi-clock-history text-secondary"></i> Baru Saja Disetujui (Hari Ini)
                    </h5>
                    <div class="list-group list-group-flush">
                        @forelse($recentTx as $tx)
                            <div class="list-group-item px-0 py-3 border-light border-opacity-10 d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="fw-bold mb-0 text-dark">{{ $tx->team ? $tx->team->name : 'Quick Checkout' }}</h6>
                                    <span class="text-secondary small" style="font-size: 0.7rem;">Lunas pada: {{ $tx->paid_at ? $tx->paid_at->timezone('Asia/Jakarta')->format('H:i') : '' }} WIB</span>
                                </div>
                                <span class="text-success fw-bold font-mono">
                                    +Rp {{ number_format($tx->amount, 0, ',', '.') }}
                                </span>
                            </div>
                        @empty
                            <p class="text-secondary text-center small py-4 my-0">Belum ada riwayat persetujuan hari ini.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 2: Pengaturan QRIS -->
    <div class="tab-content-panel d-none" id="tab-panel-settings">
        <div class="row">
            <!-- Balance info -->
            <div class="col-12 col-lg-4 mb-4">
                <div class="card border-0 bg-dark text-white shadow-sm rounded-4 p-4 h-100 d-flex flex-column justify-content-between">
                    <div>
                        <span class="text-secondary text-uppercase small fw-bold tracking-wider d-block mb-3" style="font-size: 0.65rem;">Total Pendapatan Terverifikasi</span>
                        <h2 class="fw-black font-mono text-white mb-2">Rp {{ number_format($totalBalance, 0, ',', '.') }}</h2>
                        <p class="text-secondary small mb-0">Akumulasi saldo masuk dari transaksi manual lunas (PAID).</p>
                    </div>
                </div>
            </div>

            <!-- Configuration Settings Form -->
            <div class="col-12 col-lg-8 mb-4">
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <h5 class="fw-bold mb-4 d-flex align-items-center gap-2">
                        <i class="bi bi-sliders text-primary"></i> Konfigurasi Pembayaran Manual
                    </h5>

                    <form action="{{ route('admin.manual-payment.settings') }}" method="POST" enctype="multipart/form-data" class="row g-4">
                        @csrf
                        <!-- Status Switch -->
                        <div class="col-12 col-md-6 d-flex flex-column justify-content-center">
                            <label class="form-label text-uppercase text-secondary small fw-bold tracking-wider mb-2" style="font-size: 0.65rem;">Status Gerbang Pembayaran</label>
                            <div class="form-check form-switch mt-1">
                                <input class="form-check-input" type="checkbox" role="switch" name="enabled" id="manualPaymentEnabled" {{ $settings['enabled'] ? 'checked' : '' }} style="width: 2.8em; height: 1.5em; cursor: pointer;">
                                <label class="form-check-label fw-bold text-dark ms-2" for="manualPaymentEnabled" style="cursor: pointer; line-height: 1.7;">Aktifkan Pembayaran Manual</label>
                            </div>
                        </div>

                        <!-- Admin Fee -->
                        <div class="col-12 col-md-6">
                            <label class="form-label text-uppercase text-secondary small fw-bold tracking-wider mb-1" style="font-size: 0.65rem;">Biaya Admin (Rp)</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light text-secondary border-end-0 fw-bold" style="font-size: 0.8rem;">Rp</span>
                                <input type="number" name="admin_fee" value="{{ $settings['admin_fee'] }}" required min="0" class="form-control bg-light border-start-0 py-2 fw-semibold" style="font-size: 0.85rem;">
                            </div>
                        </div>

                        <!-- Min Code -->
                        <div class="col-12 col-md-6">
                            <label class="form-label text-uppercase text-secondary small fw-bold tracking-wider mb-1" style="font-size: 0.65rem;">Kode Unik Minimum</label>
                            <input type="number" name="unique_min" value="{{ $settings['unique_min'] }}" required min="0" class="form-control bg-light py-2 fw-semibold" style="font-size: 0.85rem;">
                        </div>

                        <!-- Max Code -->
                        <div class="col-12 col-md-6">
                            <label class="form-label text-uppercase text-secondary small fw-bold tracking-wider mb-1" style="font-size: 0.65rem;">Kode Unik Maksimum</label>
                            <input type="number" name="unique_max" value="{{ $settings['unique_max'] }}" required min="0" class="form-control bg-light py-2 fw-semibold" style="font-size: 0.85rem;">
                        </div>

                        <!-- QRIS Upload & Preview -->
                        <div class="col-12 pt-3 border-top border-light border-opacity-10">
                            <div class="row g-4">
                                <div class="col-12 col-md-6">
                                    <label class="form-label text-uppercase text-secondary small fw-bold tracking-wider mb-1" style="font-size: 0.65rem;">String QRIS Statis (EMVCo)</label>
                                    <textarea id="static_qris_input" name="static_qris_string" rows="3" class="form-control bg-light font-monospace" style="font-size: 0.78rem;" placeholder="00020101021226650016...">{{ !Str::startsWith($settings['qris_image'], ['/uploads', '/storage', 'http']) ? $settings['qris_image'] : '' }}</textarea>
                                </div>

                                <div class="col-12 col-md-6">
                                    <label class="form-label text-uppercase text-secondary small fw-bold tracking-wider mb-1" style="font-size: 0.65rem;">Unggah Gambar QRIS Statis (Otomatis Ekstrak)</label>
                                    <input type="file" id="qr-input-file" accept="image/*" name="qris_image_file" class="form-control form-control-sm bg-light mb-2">
                                    <div id="qr-scan-result" class="text-success small fw-bold d-none">
                                        <i class="bi bi-check-circle-fill"></i> Berhasil membaca QRIS! String disalin ke kolom kiri.
                                    </div>
                                    <div id="qr-scan-error" class="text-danger small fw-bold d-none">
                                        <i class="bi bi-exclamation-triangle-fill"></i> Gagal membaca QR code dari gambar. File akan diunggah langsung sebagai gambar statis.
                                    </div>
                                    <div id="reader" class="d-none"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Active QRIS Preview -->
                        @if($settings['qris_image'])
                        <div class="col-12 pt-3 border-top border-light border-opacity-10">
                            <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-4 border">
                                @if(Str::startsWith($settings['qris_image'], ['/uploads', '/storage', 'http']))
                                    <img src="{{ $settings['qris_image'] }}" alt="QRIS Statis" class="rounded-3 border bg-white" style="width: 64px; height: 64px; object-fit: contain;">
                                    <div>
                                        <h6 class="fw-bold mb-1 text-dark">QRIS Statis Aktif (Gambar)</h6>
                                        <span class="text-secondary font-monospace d-block small" style="font-size: 0.7rem;">{{ $settings['qris_image'] }}</span>
                                    </div>
                                @else
                                    <div class="rounded-3 bg-primary bg-opacity-10 border border-primary border-opacity-10 flex items-center justify-center text-primary" style="width: 64px; height: 64px; display: flex; align-items: center; justify-content: center;">
                                        <i class="bi bi-qr-code fs-3"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-1 text-dark">QRIS Statis Aktif (String Dinamis)</h6>
                                        <span class="text-secondary font-monospace d-block small text-truncate" style="font-size: 0.7rem; max-width: 320px;">{{ $settings['qris_image'] }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                        @endif

                        <div class="col-12 text-end pt-3 border-top border-light border-opacity-10">
                            <button type="submit" class="btn btn-primary px-4 py-2 rounded-pill fw-bold">
                                <i class="bi bi-check-lg me-1"></i> Simpan Konfigurasi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- TAB 3: Riwayat Transaksi -->
    <div class="tab-content-panel d-none" id="tab-panel-history">
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
                <h5 class="fw-bold mb-0">Riwayat Seluruh Transaksi</h5>
                <form action="{{ route('admin.manual-payment') }}" method="GET" class="d-flex gap-2 w-100 w-md-auto">
                    <!-- Maintain active tab context -->
                    <input type="hidden" name="tab" value="history">
                    <input type="text" name="search" placeholder="Cari TRX ID atau nama tim..." value="{{ request('search') }}" class="form-control form-control-sm px-3 py-2 rounded-3 bg-light border" style="font-size: 0.82rem; max-width: 250px;">
                    <button type="submit" class="btn btn-sm btn-primary rounded-3 px-3">Cari</button>
                </form>
            </div>

            <div class="table-responsive rounded-3 border">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.8rem;">
                    <thead class="table-light">
                        <tr>
                            <th class="py-3 px-4 text-secondary text-uppercase fw-bold" style="font-size: 0.68rem;">TRX ID</th>
                            <th class="py-3 px-4 text-secondary text-uppercase fw-bold" style="font-size: 0.68rem;">Tim</th>
                            <th class="py-3 px-4 text-secondary text-uppercase fw-bold" style="font-size: 0.68rem;">Season</th>
                            <th class="py-3 px-4 text-secondary text-uppercase fw-bold text-end" style="font-size: 0.68rem;">Jumlah</th>
                            <th class="py-3 px-4 text-secondary text-uppercase fw-bold text-center" style="font-size: 0.68rem;">Status</th>
                            <th class="py-3 px-4 text-secondary text-uppercase fw-bold text-center" style="font-size: 0.68rem;">Bukti</th>
                            <th class="py-3 px-4 text-secondary text-uppercase fw-bold text-center" style="font-size: 0.68rem;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $tx)
                        <tr>
                            <td class="py-3 px-4 font-monospace fw-bold text-secondary">{{ $tx->trx_id }}</td>
                            <td class="py-3 px-4">
                                <span class="fw-bold text-dark d-block">{{ $tx->team ? $tx->team->name : 'Quick Checkout' }}</span>
                                @if($tx->team)
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $tx->team->wa_number) }}" target="_blank" class="text-success small fw-bold text-decoration-none">
                                    <i class="bi bi-whatsapp"></i> {{ $tx->team->wa_number }}
                                </a>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-secondary">{{ ($tx->team && $tx->team->season) ? $tx->team->season->name : 'Merchandise / Lainnya' }}</td>
                            <td class="py-3 px-4 text-end fw-bold font-mono">Rp {{ number_format($tx->amount, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-center">
                                @if($tx->status === 'PAID')
                                    <span class="badge bg-success-subtle text-success border border-success border-opacity-10 px-2.5 py-1.5 rounded-pill uppercase font-black" style="font-size: 0.65rem;">PAID</span>
                                @elseif($tx->status === 'CLAIMED')
                                    <span class="badge bg-warning-subtle text-warning border border-warning border-opacity-10 px-2.5 py-1.5 rounded-pill uppercase font-black" style="font-size: 0.65rem;">CLAIMED</span>
                                @elseif($tx->status === 'PENDING')
                                    <span class="badge bg-secondary-subtle text-secondary border border-secondary border-opacity-10 px-2.5 py-1.5 rounded-pill uppercase font-black" style="font-size: 0.65rem;">PENDING</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger border-opacity-10 px-2.5 py-1.5 rounded-pill uppercase font-black" style="font-size: 0.65rem;">EXPIRED</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($tx->gopay_reference && str_starts_with($tx->gopay_reference, 'PROOFS/'))
                                    @php
                                        $filename = str_replace('PROOFS/', '', $tx->gopay_reference);
                                    @endphp
                                    <a href="{{ asset('uploads/proofs/' . $filename) }}" target="_blank" class="btn btn-xs btn-outline-secondary px-2.5 py-1 rounded-3 small">
                                        <i class="bi bi-image"></i> Lihat
                                    </a>
                                @else
                                    <span class="text-secondary">-</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($tx->status === 'CLAIMED' || $tx->status === 'PENDING' || $tx->status === 'EXPIRED')
                                <div class="d-inline-flex gap-1">
                                    <form action="/admin/manual-payment/settle/{{ $tx->trx_id }}" method="POST" onsubmit="return confirm('Setujui pembayaran tim ini?')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success p-1 rounded" title="Setujui Pembayaran">
                                            <i class="bi bi-check"></i>
                                        </button>
                                    </form>
                                    @if($tx->status === 'CLAIMED')
                                    <form action="/admin/manual-payment/reject/{{ $tx->trx_id }}" method="POST" onsubmit="return confirm('Tolak bukti transfer tim ini?')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-danger p-1 rounded" title="Tolak Bukti">
                                            <i class="bi bi-x"></i>
                                        </button>
                                    </form>
                                    @endif
                                    <form action="/admin/manual-payment/delete/{{ $tx->trx_id }}" method="POST" onsubmit="return confirm('Hapus transaksi ini secara permanen?')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-secondary p-1 rounded" title="Hapus Transaksi">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                                @else
                                    <span class="text-secondary">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-secondary small">Belum ada transaksi pembayaran manual.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4 d-flex justify-content-center">
                {{ $transactions->appends(request()->input())->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>
</div>

<!-- Lightbox Modal -->
<div class="lightbox-modal" id="lightbox" onclick="closeLightbox()">
    <span class="lightbox-close"><i class="bi bi-x-lg"></i></span>
    <img src="" class="lightbox-content" id="lightbox-img" onclick="event.stopPropagation()">
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
    let soundEnabled = false;
    let currentClaimsCount = {{ count($claimedTx) }};

    function toggleSound() {
        soundEnabled = !soundEnabled;
        const btn = document.getElementById('toggle-sound');
        if (soundEnabled) {
            btn.classList.remove('btn-outline-danger');
            btn.classList.add('btn-danger');
            btn.innerHTML = '<i class="bi bi-volume-up-fill"></i> Sound On';
            playSyntheticChime();
        } else {
            btn.classList.remove('btn-danger');
            btn.classList.add('btn-outline-danger');
            btn.innerHTML = '<i class="bi bi-volume-mute-fill"></i> Sound Off';
        }
    }

    function playSyntheticChime() {
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            const osc1 = ctx.createOscillator();
            const gain1 = ctx.createGain();
            osc1.type = 'sine';
            osc1.frequency.setValueAtTime(523.25, ctx.currentTime);
            gain1.gain.setValueAtTime(0.15, ctx.currentTime);
            gain1.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.3);
            osc1.connect(gain1);
            gain1.connect(ctx.destination);
            osc1.start();
            osc1.stop(ctx.currentTime + 0.3);
            
            setTimeout(() => {
                const osc2 = ctx.createOscillator();
                const gain2 = ctx.createGain();
                osc2.type = 'sine';
                osc2.frequency.setValueAtTime(659.25, ctx.currentTime);
                gain2.gain.setValueAtTime(0.15, ctx.currentTime);
                gain2.gain.exponentialRampToValueAtTime(0.01, ctx.currentTime + 0.4);
                osc2.connect(gain2);
                gain2.connect(ctx.destination);
                osc2.start();
                osc2.stop(ctx.currentTime + 0.4);
            }, 150);
        } catch (e) {
            console.log("Audio playing error:", e);
        }
    }

    // Polling antrean baru
    function checkNewClaims() {
        fetch("{{ route('qris.verify-payments.count') }}")
            .then(res => res.json())
            .then(data => {
                if (data.count > currentClaimsCount) {
                    if (soundEnabled) {
                        playSyntheticChime();
                    }
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                } else if (data.count < currentClaimsCount) {
                    window.location.reload();
                }
            })
            .catch(err => console.log("Error checking claims:", err));
    }

    setInterval(checkNewClaims, 15000); // Poll every 15 seconds

    // Lightbox handlers
    function openLightbox(url) {
        const lb = document.getElementById('lightbox');
        const img = document.getElementById('lightbox-img');
        img.src = url;
        lb.style.display = 'flex';
    }

    function closeLightbox() {
        document.getElementById('lightbox').style.display = 'none';
    }

    // Switch Tabs
    function switchTab(tabId) {
        document.querySelectorAll('.tab-content-panel').forEach(p => p.classList.add('d-none'));
        
        // Remove active state from all buttons
        document.querySelectorAll('.tab-nav-btn').forEach(b => {
            b.classList.remove('active');
            b.classList.remove('btn-warning');
            b.classList.remove('text-dark');
            b.classList.add('btn-light');
        });

        // Add active state to selected button
        const activeBtn = document.getElementById('tab-btn-' + tabId);
        if (activeBtn) {
            activeBtn.classList.add('active');
            activeBtn.classList.remove('btn-light');
            activeBtn.classList.add('btn-warning');
            activeBtn.classList.add('text-dark');
        }

        const panel = document.getElementById('tab-panel-' + tabId);
        if (panel) {
            panel.classList.remove('d-none');
        }
        
        // Update URL hash context
        history.replaceState(null, null, '?tab=' + tabId);
    }

    // Auto load tab from query string
    document.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const activeTab = urlParams.get('tab');
        if (activeTab && ['verifier', 'settings', 'history'].includes(activeTab)) {
            switchTab(activeTab);
        }

        // QR Image Extractor Scanner
        const fileinput = document.getElementById('qr-input-file');
        const staticQrisInput = document.getElementById('static_qris_input');
        const scanResult = document.getElementById('qr-scan-result');
        const scanError = document.getElementById('qr-scan-error');

        if (fileinput) {
            fileinput.addEventListener('change', e => {
                if (e.target.files.length == 0) return;
                scanResult.classList.add('d-none');
                scanError.classList.add('d-none');
                
                const imageFile = e.target.files[0];
                const html5QrCode = new Html5Qrcode("reader");

                html5QrCode.scanFile(imageFile, true)
                .then(decodedText => {
                    staticQrisInput.value = decodedText;
                    scanResult.classList.remove('d-none');
                })
                .catch(err => {
                    scanError.classList.remove('d-none');
                    console.log(`QR scanning error: ${err}`);
                });
            });
        }
    });
</script>
@endpush
