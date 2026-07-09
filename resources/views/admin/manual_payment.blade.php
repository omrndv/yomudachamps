@extends('layouts.admin')

@section('title', 'Pembayaran Manual')

@section('content')
<div class="container-fluid py-4">
<style>
    .card-stat {
        border-radius: 16px !important;
        transition: all 0.22s ease-in-out !important;
        border: 1px solid rgba(0, 0, 0, 0.06) !important;
        box-shadow: 0 4px 12px rgba(0,0,0,0.02) !important;
    }
    .card-stat:hover {
        transform: translateY(-3px) !important;
        box-shadow: 0 8px 24px rgba(0,0,0,0.06) !important;
        border-color: rgba(0, 0, 0, 0.09) !important;
    }
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
    
    /* Tab Navigation mimicking Settings page */
    .payment-tabs {
        display: flex;
        gap: 4px;
        background: #f1f5f9;
        border-radius: 14px;
        padding: 4px;
        overflow-x: auto;
        scrollbar-width: none;
    }
    .payment-tabs::-webkit-scrollbar { display: none; }
    .payment-tab {
        padding: 10px 18px;
        border-radius: 10px;
        font-size: 0.82rem;
        font-weight: 600;
        color: #64748b;
        background: transparent;
        border: none;
        white-space: nowrap;
        cursor: pointer;
        transition: all 0.15s ease;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .payment-tab:hover {
        color: #334155;
        background: rgba(255,255,255,0.6);
    }
    .payment-tab.active {
        background: #ffffff;
        color: #0f172a;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    }
</style>
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-0">Pembayaran Manual</h3>
            <p class="text-secondary small mb-0">Kelola QRIS statis, verifikasi klaim bukti transfer, dan riwayat dana masuk.</p>
        </div>
        <!-- Sound alert for verifications -->
        <button class="btn btn-danger sound-status-btn d-flex align-items-center gap-2" id="toggle-sound" onclick="toggleSound()">
            <i class="bi bi-volume-up-fill"></i> Sound On
        </button>
    </div>

    <!-- Tab Switcher Controls -->
    <div class="payment-tabs mb-4">
        <button class="payment-tab active" id="tab-btn-verifier" onclick="switchTab('verifier')">
            <i class="bi bi-shield-check text-warning"></i> Antrean Verifikasi ({{ count($claimedTx) }})
        </button>
        <button class="payment-tab" id="tab-btn-settings" onclick="switchTab('settings')">
            <i class="bi bi-gear-fill"></i> Pengaturan QRIS
        </button>
        <button class="payment-tab" id="tab-btn-history" onclick="switchTab('history')">
            <i class="bi bi-journal-text"></i> Riwayat Transaksi
        </button>
    </div>

    <!-- TAB STATS ROW FOR MANUAL PAYMENT -->
    <div class="row g-3 mb-4">
        <!-- Card 1: Total Pemasukan Manual -->
        <div class="col-6 col-lg-3">
            <div class="card card-stat border-0 p-4 bg-white shadow-sm h-100 d-flex flex-column justify-content-between">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <p class="text-secondary small fw-bold mb-1 text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Total Uang Masuk</p>
                        <h3 class="fw-bold text-dark font-mono mb-0" style="font-size: 1.45rem; letter-spacing: -0.5px;">Rp {{ number_format($totalBalance, 0, ',', '.') }}</h3>
                    </div>
                    <div class="text-success bg-success-subtle" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; border-radius: 12px;">
                        <i class="bi bi-wallet2 fs-5"></i>
                    </div>
                </div>
                <p class="small text-muted mb-0 mt-2" style="font-size: 0.7rem;">Total dana terverifikasi (Lunas)</p>
            </div>
        </div>
        <!-- Card 2: Menunggu Verifikasi -->
        <div class="col-6 col-lg-3">
            <div class="card card-stat border-0 p-4 bg-white shadow-sm h-100 d-flex flex-column justify-content-between">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <p class="text-secondary small fw-bold mb-1 text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Butuh Verifikasi</p>
                        <h3 class="fw-bold text-warning font-mono mb-0" style="font-size: 1.45rem; letter-spacing: -0.5px;">{{ count($claimedTx) }} Antrean</h3>
                    </div>
                    <div class="text-warning bg-warning-subtle" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; border-radius: 12px;">
                        <i class="bi bi-hourglass-split fs-5"></i>
                    </div>
                </div>
                <p class="small text-muted mb-0 mt-2" style="font-size: 0.7rem;">Unggahan bukti transfer masuk</p>
            </div>
        </div>
        <!-- Card 3: Sukses Terverifikasi -->
        <div class="col-6 col-lg-3">
            <div class="card card-stat border-0 p-4 bg-white shadow-sm h-100 d-flex flex-column justify-content-between">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <p class="text-secondary small fw-bold mb-1 text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Transaksi Sukses</p>
                        <h3 class="fw-bold text-success font-mono mb-0" style="font-size: 1.45rem; letter-spacing: -0.5px;">{{ $successCount }} Sukses</h3>
                    </div>
                    <div class="text-success bg-success-subtle" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; border-radius: 12px;">
                        <i class="bi bi-patch-check fs-5"></i>
                    </div>
                </div>
                <p class="small text-muted mb-0 mt-2" style="font-size: 0.7rem;">Total transaksi terverifikasi</p>
            </div>
        </div>
        <!-- Card 4: Status Pending -->
        <div class="col-6 col-lg-3">
            <div class="card card-stat border-0 p-4 bg-white shadow-sm h-100 d-flex flex-column justify-content-between">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <p class="text-secondary small fw-bold mb-1 text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Transaksi Pending</p>
                        <h3 class="fw-bold text-secondary font-mono mb-0" style="font-size: 1.45rem; letter-spacing: -0.5px;">{{ $pendingCount }} Pending</h3>
                    </div>
                    <div class="text-secondary bg-secondary-subtle" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; border-radius: 12px;">
                        <i class="bi bi-clock fs-5"></i>
                    </div>
                </div>
                <p class="small text-muted mb-0 mt-2" style="font-size: 0.7rem;">Belum upload bukti pembayaran</p>
            </div>
        </div>
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
                                        <form action="/admin/manual-payment/settle/{{ $tx->id }}" method="POST" onsubmit="return confirm('Setujui pembayaran tim ini?')">
                                            @csrf
                                            <button type="submit" class="btn btn-success fw-bold w-100 py-2.5 rounded-3" style="font-size: 0.78rem;">
                                                Setujui
                                            </button>
                                        </form>
                                    </div>
                                    <div class="col-4">
                                        <form action="/admin/manual-payment/reject/{{ $tx->id }}" method="POST" onsubmit="return confirm('TOLAK pembayaran tim ini? Kapten akan diberitahu melalui WA untuk upload ulang.')">
                                            @csrf
                                            <button type="submit" class="btn btn-danger fw-bold w-100 py-2.5 rounded-3" style="font-size: 0.78rem;">
                                                Tolak
                                            </button>
                                        </form>
                                    </div>
                                    <div class="col-4">
                                        <form action="/admin/manual-payment/delete/{{ $tx->id }}" method="POST" onsubmit="return confirm('HAPUS transaksi ini secara permanen dari sistem? Tindakan ini tidak bisa dibatalkan.')">
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

                        <!-- Payment Method Name -->
                        <div class="col-12 col-md-6">
                            <label class="form-label text-uppercase text-secondary small fw-bold tracking-wider mb-1" style="font-size: 0.65rem;">Nama Metode Pembayaran (di Checkout)</label>
                            <input type="text" name="payment_name" value="{{ $settings['payment_name'] }}" required class="form-control bg-light py-2 fw-semibold" style="font-size: 0.85rem;" placeholder="QRIS (All Payment)">
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

                        <!-- Active QRIS Preview & Dynamic Simulation -->
                        @if($settings['qris_image'])
                        @php
                            $previewUrl = null;
                            if (!empty($settings['qris_image']) && !Str::startsWith($settings['qris_image'], ['/uploads', '/storage', 'http'])) {
                                try {
                                    $dynamicString = \App\Services\QrisService::generateDynamicQris($settings['qris_image'], 10200);
                                    $previewUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($dynamicString);
                                } catch (\Exception $e) {}
                            }
                        @endphp
                        <div class="col-12 pt-3 border-top border-light border-opacity-10">
                            <div class="row g-3">
                                <div class="col-12 col-md-6">
                                    <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-4 border h-100">
                                        @if(Str::startsWith($settings['qris_image'], ['/uploads', '/storage', 'http']))
                                            <img src="{{ $settings['qris_image'] }}" alt="QRIS Statis" class="rounded-3 border bg-white" style="width: 64px; height: 64px; object-fit: contain;">
                                            <div>
                                                <h6 class="fw-bold mb-1 text-dark">QRIS Statis Aktif (Gambar)</h6>
                                                <span class="text-secondary font-monospace d-block small text-truncate" style="font-size: 0.7rem; max-width: 200px;">{{ $settings['qris_image'] }}</span>
                                            </div>
                                        @else
                                            <div class="rounded-3 bg-primary bg-opacity-10 border border-primary border-opacity-10 flex items-center justify-center text-primary" style="width: 64px; height: 64px; display: flex; align-items: center; justify-content: center; flex-shrink: 0;">
                                                <i class="bi bi-qr-code fs-3"></i>
                                            </div>
                                            <div>
                                                <h6 class="fw-bold mb-1 text-dark">QRIS Statis Aktif (String Dinamis)</h6>
                                                <span class="text-secondary font-monospace d-block small text-truncate" style="font-size: 0.7rem; max-width: 200px;">{{ $settings['qris_image'] }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                @if($previewUrl)
                                <div class="col-12 col-md-6">
                                    <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-4 border h-100">
                                        <img src="{{ $previewUrl }}" alt="Simulasi QRIS Dinamis" class="rounded-3 border bg-white" style="width: 64px; height: 64px; object-fit: contain;">
                                        <div>
                                            <h6 class="fw-bold mb-1 text-dark">Simulasi QRIS Dinamis</h6>
                                            <span class="text-secondary d-block small" style="font-size: 0.7rem; line-height: 1.3;">QRIS Dinamis untuk nominal Rp 10.200 (Nominal pendaftaran pas terinjeksi otomatis)</span>
                                        </div>
                                    </div>
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
                    <div class="d-flex align-items-center gap-3">
                        <h5 class="fw-bold mb-0">Riwayat Seluruh Transaksi</h5>
                        <button type="submit" form="bulk-delete-form" id="btn-bulk-delete" class="btn btn-sm btn-outline-danger rounded-3 d-none fw-bold" style="font-size: 0.78rem;">
                            <i class="bi bi-trash3-fill me-1"></i> Hapus Terpilih (<span id="selected-count">0</span>)
                        </button>
                    </div>
                    <div class="d-flex gap-2 w-100 w-md-auto align-items-center">
                        <input type="text" id="history-search" placeholder="Cari TRX ID atau nama tim..." value="{{ request('search') }}" class="form-control form-control-sm px-3 py-2 rounded-3 bg-light border" style="font-size: 0.82rem; max-width: 250px;">
                        <button type="button" onclick="performSearch()" class="btn btn-sm btn-primary rounded-3 px-3">Cari</button>
                    </div>
                </div>

                <form id="bulk-delete-form" action="{{ route('admin.manual-payment.delete-bulk') }}" method="POST" onsubmit="return confirm('Hapus semua transaksi terpilih secara massal?')">
                    @csrf
                </form>
                <div class="table-responsive rounded-3 border">
                        <table class="table table-hover align-middle mb-0" style="font-size: 0.8rem;">
                            <thead class="table-light">
                            <tr>
                                <th class="py-3 px-3 text-center" style="width: 40px;">
                                    <input type="checkbox" class="form-check-input" id="select-all-trx" onclick="toggleSelectAll(this)">
                                </th>
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
                            @php
                                $isProof = $tx->gopay_reference && str_starts_with($tx->gopay_reference, 'PROOFS/');
                                $filename = $isProof ? str_replace('PROOFS/', '', $tx->gopay_reference) : null;
                                $imgUrl = $filename ? asset('uploads/proofs/' . $filename) : null;
                            @endphp
                            <tr style="cursor: pointer;" onclick="rowClick(event, this)"
                                data-trx-id="{{ $tx->trx_id }}"
                                data-qris-id="{{ $tx->id }}"
                                data-team-name="{{ $tx->team ? $tx->team->name : 'Quick Checkout' }}"
                                data-season-name="{{ ($tx->team && $tx->team->season) ? $tx->team->season->name : 'Merchandise / Lainnya' }}"
                                data-base-amount="Rp {{ number_format($tx->base_amount, 0, ',', '.') }}"
                                data-admin-fee="Rp {{ number_format($tx->unique_code ? $tx->amount - $tx->base_amount - $tx->unique_code : 0, 0, ',', '.') }}"
                                data-unique-code="Rp {{ number_format($tx->unique_code ?? 0, 0, ',', '.') }}"
                                data-amount="Rp {{ number_format($tx->amount, 0, ',', '.') }}"
                                data-status="{{ $tx->status }}"
                                data-created-at="{{ $tx->created_at ? $tx->created_at->timezone('Asia/Jakarta')->format('d M Y, H:i') : '-' }} WIB"
                                data-paid-at="{{ $tx->paid_at ? $tx->paid_at->timezone('Asia/Jakarta')->format('d M Y, H:i') : '-' }} WIB"
                                data-wa-number="{{ $tx->team ? $tx->team->wa_number : '' }}"
                                data-proof-url="{{ $imgUrl }}">
                                <td class="py-3 px-3 text-center">
                                    <input type="checkbox" name="selected_trx[]" value="{{ $tx->id }}" form="bulk-delete-form" class="form-check-input trx-checkbox" onclick="updateBulkButtonState()">
                                </td>
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
                                    @if($imgUrl)
                                        <a href="{{ $imgUrl }}" target="_blank" class="btn btn-xs btn-outline-secondary px-2.5 py-1 rounded-3 small">
                                            <i class="bi bi-image"></i> Lihat
                                        </a>
                                    @else
                                        <span class="text-secondary">-</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-center">
                                    @if($tx->status !== 'PAID')
                                    <div class="d-inline-flex gap-1">
                                        @if($tx->status === 'CLAIMED' || $tx->status === 'PENDING')
                                        <form action="/admin/manual-payment/settle/{{ $tx->id }}" method="POST" onsubmit="return confirm('Setujui pembayaran tim ini?')">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success p-1 rounded" title="Setujui Pembayaran">
                                                <i class="bi bi-check"></i>
                                            </button>
                                        </form>
                                        @endif
                                        @if($tx->status === 'CLAIMED')
                                        <form action="/admin/manual-payment/reject/{{ $tx->id }}" method="POST" onsubmit="return confirm('Tolak bukti transfer tim ini?')">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-danger p-1 rounded" title="Tolak Bukti">
                                                <i class="bi bi-x"></i>
                                            </button>
                                        </form>
                                        @endif
                                        <form action="/admin/manual-payment/delete/{{ $tx->id }}" method="POST" onsubmit="return confirm('Hapus transaksi ini secara permanen?')">
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
                                <td colspan="8" class="text-center py-4 text-secondary small">Belum ada transaksi pembayaran manual.</td>
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
    let soundEnabled = true;
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
        document.querySelectorAll('.payment-tab').forEach(b => {
            b.classList.remove('active');
        });

        // Add active state to selected button
        const activeBtn = document.getElementById('tab-btn-' + tabId);
        if (activeBtn) {
            activeBtn.classList.add('active');
        }

        const panel = document.getElementById('tab-panel-' + tabId);
        if (panel) {
            panel.classList.remove('d-none');
        }
        
        // Update URL hash context
        history.replaceState(null, null, '?tab=' + tabId);
    }

    // Modal & Bulk Delete Javascript
    const csrfToken = '{{ csrf_token() }}';
    let detailProofUrl = '';
    
    function rowClick(event, element) {
        // Prevent click trigger if interacting with forms, buttons, checkboxes or WhatsApp links
        const closestForm = event.target.closest('form');
        if (event.target.closest('button') || (closestForm && closestForm.id !== 'bulk-delete-form') || event.target.closest('a') || event.target.closest('.form-check-input')) {
            return;
        }
        
        const trxId = element.getAttribute('data-trx-id');
        const qrisId = element.getAttribute('data-qris-id') || trxId;
        const teamName = element.getAttribute('data-team-name');
        const seasonName = element.getAttribute('data-season-name');
        const baseAmount = element.getAttribute('data-base-amount');
        const adminFee = element.getAttribute('data-admin-fee');
        const uniqueCode = element.getAttribute('data-unique-code');
        const amount = element.getAttribute('data-amount');
        const status = element.getAttribute('data-status');
        const createdAt = element.getAttribute('data-created-at');
        const paidAt = element.getAttribute('data-paid-at');
        const waNumber = element.getAttribute('data-wa-number');
        const proofUrl = element.getAttribute('data-proof-url');

        // Populate fields
        document.getElementById('detail-trx-id').innerText = trxId;
        document.getElementById('detail-team-name').innerText = teamName;
        document.getElementById('detail-season-name').innerText = seasonName;
        document.getElementById('detail-base-amount').innerText = baseAmount;
        document.getElementById('detail-admin-fee').innerText = adminFee;
        document.getElementById('detail-unique-code').innerText = uniqueCode;
        document.getElementById('detail-amount').innerText = amount;
        document.getElementById('detail-created-at').innerText = createdAt;
        document.getElementById('detail-paid-at').innerText = paidAt;

        // Status Badge styling
        const statusBadge = document.getElementById('detail-status');
        statusBadge.innerText = status;
        statusBadge.className = 'badge mt-2 px-3 py-1.5 rounded-pill font-monospace';
        if (status === 'PAID') {
            statusBadge.classList.add('bg-success-subtle', 'text-success');
        } else if (status === 'CLAIMED') {
            statusBadge.classList.add('bg-warning-subtle', 'text-warning');
        } else if (status === 'PENDING') {
            statusBadge.classList.add('bg-secondary-subtle', 'text-secondary');
        } else {
            statusBadge.classList.add('bg-danger-subtle', 'text-danger');
        }

        // Proof Section
        const proofSection = document.getElementById('detail-proof-section');
        if (proofUrl) {
            detailProofUrl = proofUrl;
            document.getElementById('detail-proof-img').src = proofUrl;
            proofSection.classList.remove('d-none');
        } else {
            detailProofUrl = '';
            proofSection.classList.add('d-none');
        }

        // Actions generation
        const actionsDiv = document.getElementById('detail-actions');
        actionsDiv.innerHTML = '';

        if (status === 'CLAIMED' || status === 'PENDING') {
            // Settle form
            actionsDiv.innerHTML += `
                <form action="/admin/manual-payment/settle/${qrisId}" method="POST" class="d-inline" onsubmit="return confirm('Setujui pembayaran tim ini?')">
                    <input type="hidden" name="_token" value="${csrfToken}">
                    <button type="submit" class="btn btn-sm btn-success fw-bold px-3 py-1.5 rounded-3"><i class="bi bi-check"></i> Setujui</button>
                </form>
            `;
            
            if (status === 'CLAIMED') {
                // Reject form
                actionsDiv.innerHTML += `
                    <form action="/admin/manual-payment/reject/${qrisId}" method="POST" class="d-inline" onsubmit="return confirm('Tolak bukti transfer tim ini?')">
                        <input type="hidden" name="_token" value="${csrfToken}">
                        <button type="submit" class="btn btn-sm btn-danger fw-bold px-3 py-1.5 rounded-3"><i class="bi bi-x"></i> Tolak</button>
                    </form>
                `;
            }
        }
        
        if (status !== 'PAID') {
            // Delete form
            actionsDiv.innerHTML += `
                <form action="/admin/manual-payment/delete/${qrisId}" method="POST" class="d-inline" onsubmit="return confirm('Hapus transaksi ini secara permanen?')">
                    <input type="hidden" name="_token" value="${csrfToken}">
                    <button type="submit" class="btn btn-sm btn-secondary fw-bold px-3 py-1.5 rounded-3"><i class="bi bi-trash"></i> Hapus</button>
                </form>
            `;
        }

        // Show Modal
        const modal = new bootstrap.Modal(document.getElementById('trxDetailModal'));
        modal.show();
    }

    function openProofFromDetail() {
        if (detailProofUrl) {
            // Close details modal
            bootstrap.Modal.getInstance(document.getElementById('trxDetailModal')).hide();
            // Open lightbox
            openLightbox(detailProofUrl);
        }
    }

    function toggleSelectAll(selectAllCheckbox) {
        const checkboxes = document.querySelectorAll('.trx-checkbox');
        checkboxes.forEach(cb => cb.checked = selectAllCheckbox.checked);
        updateBulkButtonState();
    }

    function updateBulkButtonState() {
        const checkedBoxes = document.querySelectorAll('.trx-checkbox:checked');
        const bulkDeleteBtn = document.getElementById('btn-bulk-delete');
        const countSpan = document.getElementById('selected-count');
        const selectAllCheckbox = document.getElementById('select-all-trx');
        const totalCheckboxes = document.querySelectorAll('.trx-checkbox').length;

        if (checkedBoxes.length > 0) {
            bulkDeleteBtn.classList.remove('d-none');
            countSpan.innerText = checkedBoxes.length;
        } else {
            bulkDeleteBtn.classList.add('d-none');
        }

        if (selectAllCheckbox) {
            selectAllCheckbox.checked = (checkedBoxes.length === totalCheckboxes && totalCheckboxes > 0);
        }
    }

    function performSearch() {
        const searchInput = document.getElementById('history-search');
        const searchValue = searchInput ? searchInput.value : '';
        window.location.href = `?tab=history&search=${encodeURIComponent(searchValue)}`;
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

<!-- Transaction Detail Modal -->
<div class="modal fade" id="trxDetailModal" tabindex="-1" aria-labelledby="trxDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header bg-dark text-white rounded-top-4 border-0 py-3 px-4">
                <h6 class="modal-title fw-bold mb-0" id="trxDetailModalLabel">Detail Transaksi Manual</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center mb-4">
                    <span class="text-secondary text-uppercase small fw-bold tracking-wider" style="font-size: 0.65rem;">Nominal Akhir</span>
                    <h3 class="fw-black text-primary font-mono mt-1 mb-0" id="detail-amount">Rp 0</h3>
                    <span class="badge mt-2 px-3 py-1.5 rounded-pill font-monospace" id="detail-status">PENDING</span>
                </div>
                
                <div class="list-group list-group-flush rounded-3 border mb-4" style="font-size: 0.85rem;">
                    <div class="list-group-item d-flex justify-content-between py-2.5">
                        <span class="text-secondary">TRX ID</span>
                        <span class="fw-bold font-monospace" id="detail-trx-id">-</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between py-2.5">
                        <span class="text-secondary">Nama Tim</span>
                        <span class="fw-bold" id="detail-team-name">-</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between py-2.5">
                        <span class="text-secondary">Season</span>
                        <span class="fw-bold" id="detail-season-name">-</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between py-2.5">
                        <span class="text-secondary">Harga Dasar</span>
                        <span class="fw-semibold text-dark" id="detail-base-amount">Rp 0</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between py-2.5">
                        <span class="text-secondary">Biaya Admin</span>
                        <span class="fw-semibold text-dark" id="detail-admin-fee">Rp 0</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between py-2.5">
                        <span class="text-secondary">Kode Unik</span>
                        <span class="fw-semibold text-dark" id="detail-unique-code">Rp 0</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between py-2.5">
                        <span class="text-secondary">Waktu Dibuat</span>
                        <span class="text-dark" id="detail-created-at">-</span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between py-2.5">
                        <span class="text-secondary">Waktu Lunas</span>
                        <span class="text-dark" id="detail-paid-at">-</span>
                    </div>
                </div>

                <div class="d-none" id="detail-proof-section">
                    <label class="form-label text-uppercase text-secondary small fw-bold tracking-wider mb-2" style="font-size: 0.65rem;">Bukti Transfer</label>
                    <div class="border rounded-3 overflow-hidden bg-light text-center p-2" style="cursor: pointer;" onclick="openProofFromDetail()">
                        <img src="" id="detail-proof-img" class="img-fluid rounded" style="max-height: 180px; object-fit: contain;">
                        <div class="text-primary small fw-bold mt-2"><i class="bi bi-zoom-in"></i> Klik untuk memperbesar</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top-0 p-3 bg-light rounded-bottom-4 d-flex justify-content-between align-items-center">
                <div class="d-flex gap-1" id="detail-actions">
                    <!-- Actions generated dynamically -->
                </div>
                <button type="button" class="btn btn-sm btn-secondary px-3 py-1.5 rounded-3 fw-bold" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
@endpush
