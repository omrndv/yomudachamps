@extends('layouts.app')

@section('title', 'Verifikasi Pembayaran - Yomuda Admin')

@push('styles')
<style>
    :root {
        --ymd-yellow: #ffc107;
        --ymd-dark: #0e0f11;
        --ymd-card: #141618;
        --ymd-border: rgba(255, 255, 255, 0.06);
    }

    body {
        background-color: #0b0c0e !important;
    }

    .pwa-wrapper {
        min-height: 100vh;
        padding: 20px 0 80px;
        width: 100%;
        color: #fff;
    }

    .pwa-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-bottom: 20px;
        border-bottom: 1px solid var(--ymd-border);
        margin-bottom: 24px;
    }

    .pwa-title h2 {
        font-size: 1.25rem;
        font-weight: 800;
        letter-spacing: -0.5px;
        margin: 0;
        background: linear-gradient(160deg, #ffffff 40%, #ffc107 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .pwa-title span {
        font-size: 0.72rem;
        color: rgba(255, 255, 255, 0.4);
        display: block;
        margin-top: 3px;
        font-weight: 600;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: rgba(25, 135, 84, 0.1);
        border: 1px solid rgba(25, 135, 84, 0.25);
        color: #2ec4b6;
        padding: 5px 12px;
        border-radius: 50px;
        font-size: 0.68rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-pill.sound-off {
        background: rgba(220, 53, 69, 0.1);
        border-color: rgba(220, 53, 69, 0.25);
        color: #ff6b6b;
    }

    .claim-card {
        background: var(--ymd-card);
        border: 1px solid var(--ymd-border);
        border-radius: 20px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.2);
    }

    .claim-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 12px;
        margin-bottom: 14px;
        padding-bottom: 12px;
        border-bottom: 1px solid rgba(255,255,255,0.04);
    }

    .team-name {
        font-weight: 800;
        font-size: 1.05rem;
        color: #fff;
        line-height: 1.3;
    }

    .season-name {
        font-size: 0.72rem;
        color: var(--ymd-yellow);
        font-weight: 700;
        text-transform: uppercase;
        display: block;
        margin-top: 2px;
    }

    .unique-amount {
        text-align: right;
    }

    .unique-amount span {
        font-size: 0.65rem;
        color: rgba(255, 255, 255, 0.4);
        display: block;
        text-transform: uppercase;
        font-weight: 700;
    }

    .unique-amount strong {
        color: #ffc107;
        font-size: 1.05rem;
        font-weight: 900;
    }

    .proof-img-wrapper {
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid rgba(255,255,255,0.08);
        background: #080809;
        margin-bottom: 16px;
        position: relative;
        cursor: pointer;
        height: 220px;
    }

    .proof-img-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    .proof-img-overlay {
        position: absolute;
        inset: 0;
        background: rgba(0,0,0,0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.2s;
    }

    .proof-img-wrapper:hover .proof-img-overlay {
        opacity: 1;
    }

    .contact-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #25d366;
        font-size: 0.8rem;
        font-weight: 700;
        text-decoration: none;
        margin-bottom: 16px;
        background: rgba(37, 211, 102, 0.08);
        border: 1px solid rgba(37, 211, 102, 0.15);
        padding: 5px 12px;
        border-radius: 8px;
    }

    .action-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }

    .btn-action {
        padding: 12px;
        font-weight: 800;
        font-size: 0.85rem;
        text-transform: uppercase;
        border-radius: 12px;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        transition: transform 0.2s, opacity 0.2s;
    }

    .btn-action:active {
        transform: scale(0.96);
    }

    .btn-approve {
        background: #198754;
        color: #fff;
    }

    .btn-reject {
        background: #dc3545;
        color: #fff;
    }

    /* Lightbox Modal */
    .lightbox-modal {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.95);
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

    /* History section */
    .history-section {
        margin-top: 36px;
    }

    .history-title {
        font-size: 0.85rem;
        font-weight: 800;
        letter-spacing: 1px;
        text-transform: uppercase;
        color: rgba(255,255,255,0.35);
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .history-card {
        background: rgba(255,255,255,0.02);
        border: 1px solid var(--ymd-border);
        border-radius: 16px;
        padding: 14px 18px;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .history-card-info h6 {
        font-weight: 700;
        font-size: 0.88rem;
        color: #fff;
        margin: 0;
    }

    .history-card-info span {
        font-size: 0.7rem;
        color: rgba(255,255,255,0.4);
    }
</style>
@endpush

@section('content')
<div class="pwa-wrapper">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-10 col-lg-8 col-xl-6">

                <!-- Header PWA -->
                <div class="pwa-header">
                    <div class="pwa-title">
                        <h2>Yomuda Admin</h2>
                        <span>Verifikasi Pembayaran Manual</span>
                    </div>
                    <button class="status-pill sound-off" id="toggle-sound" onclick="toggleSound()">
                        <i class="bi bi-volume-mute-fill"></i> Sound Off
                    </button>
                </div>

                <!-- Antrean Verifikasi -->
                <div id="claims-container">
                    @forelse($claimedTx as $tx)
                        @php
                            $team = $tx->team;
                            $filename = str_replace('PROOFS/', '', $tx->gopay_reference);
                            $imgUrl = asset('uploads/proofs/' . $filename);
                        @endphp
                        <div class="claim-card" data-id="{{ $tx->id }}">
                            <div class="claim-card-header">
                                <div>
                                    <span class="team-name">{{ $team ? $team->name : 'N/A' }}</span>
                                    <span class="season-name">{{ ($team && $team->season) ? $team->season->name : 'N/A' }}</span>
                                </div>
                                <div class="unique-amount">
                                    <span>Nominal</span>
                                    <strong>Rp {{ number_format($tx->amount, 0, ',', '.') }}</strong>
                                </div>
                            </div>

                            <div class="proof-img-wrapper" onclick="openLightbox('{{ $imgUrl }}')">
                                <img src="{{ $imgUrl }}" alt="Bukti Transfer" loading="lazy">
                                <div class="proof-img-overlay">
                                    <i class="bi bi-zoom-in text-white fs-3"></i>
                                </div>
                            </div>

                            <div class="d-flex align-items-center justify-content-between">
                                @if($team)
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $team->wa_number) }}" target="_blank" rel="noopener" class="contact-link">
                                        <i class="bi bi-whatsapp"></i> Hubungi Kapten
                                    </a>
                                @endif
                                <span class="small text-secondary" style="font-size: 0.72rem;">TRX: {{ $tx->trx_id }}</span>
                            </div>

                            <div class="action-row">
                                <form action="{{ route('qris.settle', $tx->trx_id) }}" method="POST" onsubmit="return confirm('Setujui pembayaran tim ini?')">
                                    @csrf
                                    <button type="submit" class="btn-action btn-approve w-100">
                                        <i class="bi bi-check-circle-fill"></i> Setujui
                                    </button>
                                </form>
                                <form action="{{ route('qris.reject', $tx->trx_id) }}" method="POST" onsubmit="return confirm('TOLAK pembayaran tim ini? Kapten akan diberitahu melalui WA untuk upload ulang.')">
                                    @csrf
                                    <button type="submit" class="btn-action btn-reject w-100">
                                        <i class="bi bi-x-circle-fill"></i> Tolak
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5 text-secondary" style="background: var(--ymd-card); border: 1px solid var(--ymd-border); border-radius: 20px;">
                            <div class="fs-1 text-muted mb-2">😴</div>
                            <h6 class="fw-bold text-white mb-1">Semua Beres!</h6>
                            <p class="small text-secondary mb-0">Belum ada antrean bukti transfer baru.</p>
                        </div>
                    @endforelse
                </div>

                <!-- Riwayat Hari Ini -->
                <div class="history-section">
                    <span class="history-title">
                        <i class="bi bi-clock-history"></i> Baru Saja Disetujui
                    </span>
                    @forelse($recentTx as $tx)
                        <div class="history-card">
                            <div class="history-card-info">
                                <h6>{{ $tx->team ? $tx->team->name : 'N/A' }}</h6>
                                <span>Lunas pada: {{ $tx->paid_at ? $tx->paid_at->timezone('Asia/Jakarta')->format('H:i') : '' }} WIB</span>
                            </div>
                            <div class="text-success fw-bold text-end" style="font-size: 0.88rem;">
                                +Rp {{ number_format($tx->amount, 0, ',', '.') }}
                            </div>
                        </div>
                    @empty
                        <p class="small text-secondary text-center py-3">Belum ada riwayat persetujuan hari ini.</p>
                    @endforelse
                </div>

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
<script>
    let soundEnabled = false;
    let currentClaimsCount = {{ count($claimedTx) }};

    function toggleSound() {
        soundEnabled = !soundEnabled;
        const btn = document.getElementById('toggle-sound');
        if (soundEnabled) {
            btn.classList.remove('sound-off');
            btn.innerHTML = '<i class="bi bi-volume-up-fill"></i> Sound On';
            // Play initial test sound
            playSyntheticChime();
        } else {
            btn.classList.add('sound-off');
            btn.innerHTML = '<i class="bi bi-volume-mute-fill"></i> Sound Off';
        }
    }

    function playSyntheticChime() {
        try {
            const ctx = new (window.AudioContext || window.webkitAudioContext)();
            
            // Ding (C5)
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
            
            // Dong (E5)
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
            console.log("Audio not allowed:", e);
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
                    // Auto reload page to show new cards
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                } else if (data.count < currentClaimsCount) {
                    // Item approved from another screen, reload
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
</script>
@endpush
