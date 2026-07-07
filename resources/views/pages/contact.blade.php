@extends('layouts.app')

@section('title', 'Kontak Kami - Yomuda Championship')

@push('styles')
<style>
    :root {
        --ymd-yellow: #ffc107;
        --ymd-card: #141618;
        --ymd-border: rgba(255, 255, 255, 0.06);
    }

    .contact-page {
        padding: 20px 0 60px;
    }

    .contact-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: rgba(255, 255, 255, 0.4);
        font-size: 0.85rem;
        font-weight: 600;
        text-decoration: none;
        margin-bottom: 40px;
        transition: color 0.25s;
    }

    .contact-back:hover { color: var(--ymd-yellow); }

    .contact-hero {
        text-align: center;
        padding: 0 20px 50px;
    }

    .contact-hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(255, 193, 7, 0.08);
        border: 1px solid rgba(255, 193, 7, 0.22);
        border-radius: 50px;
        padding: 7px 20px;
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 2px;
        text-transform: uppercase;
        color: var(--ymd-yellow);
        margin-bottom: 20px;
    }

    .contact-hero-badge span {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: var(--ymd-yellow);
        animation: pulse-dot 1.8s ease infinite;
    }

    @keyframes pulse-dot {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.4; transform: scale(0.7); }
    }

    .contact-hero h1 {
        font-size: clamp(1.8rem, 4vw, 2.8rem);
        font-weight: 900;
        letter-spacing: -1.5px;
        line-height: 1.1;
        margin-bottom: 14px;
        background: linear-gradient(160deg, #ffffff 40%, #ffc107 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .contact-hero p {
        max-width: 460px;
        margin: 0 auto;
        color: rgba(255, 255, 255, 0.45);
        font-size: 0.95rem;
        line-height: 1.8;
    }

    .contact-card {
        background: var(--ymd-card);
        border: 1px solid var(--ymd-border);
        border-radius: 28px;
        overflow: hidden;
    }

    .contact-channel {
        display: flex;
        align-items: center;
        gap: 20px;
        padding: 28px 36px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        text-decoration: none;
        transition: background 0.25s, border-color 0.25s;
    }

    .contact-channel:last-child { border-bottom: none; }

    .contact-channel:hover {
        background: rgba(255, 255, 255, 0.02);
    }

    .contact-channel-icon {
        width: 52px;
        height: 52px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        flex-shrink: 0;
        transition: transform 0.25s;
    }

    .contact-channel:hover .contact-channel-icon {
        transform: scale(1.08);
    }

    .icon-wa {
        background: rgba(37, 211, 102, 0.1);
        border: 1px solid rgba(37, 211, 102, 0.2);
        color: #25D366;
    }

    .icon-email {
        background: rgba(255, 193, 7, 0.1);
        border: 1px solid rgba(255, 193, 7, 0.2);
        color: var(--ymd-yellow);
    }

    .icon-location {
        background: rgba(100, 150, 255, 0.1);
        border: 1px solid rgba(100, 150, 255, 0.2);
        color: #6496ff;
    }

    .icon-hours {
        background: rgba(200, 100, 255, 0.1);
        border: 1px solid rgba(200, 100, 255, 0.2);
        color: #c864ff;
    }

    .contact-channel-info {
        flex: 1;
        min-width: 0;
    }

    .contact-channel-label {
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 1.2px;
        text-transform: uppercase;
        color: rgba(255, 255, 255, 0.35);
        margin-bottom: 4px;
    }

    .contact-channel-value {
        font-weight: 700;
        font-size: 0.97rem;
        color: #fff;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .contact-channel-sub {
        font-size: 0.78rem;
        color: rgba(255, 255, 255, 0.4);
        margin-top: 2px;
    }

    .contact-channel-arrow {
        color: rgba(255, 255, 255, 0.2);
        font-size: 0.9rem;
        transition: color 0.25s, transform 0.25s;
    }

    .contact-channel:hover .contact-channel-arrow {
        color: var(--ymd-yellow);
        transform: translateX(4px);
    }

    .contact-channel-static {
        cursor: default;
    }

    .contact-channel-static:hover {
        background: transparent;
    }

    .contact-channel-static .contact-channel-arrow { display: none; }

    @media (max-width: 768px) {
        .contact-channel { padding: 22px 22px; gap: 16px; }
        .contact-channel-icon { width: 44px; height: 44px; font-size: 1.15rem; border-radius: 13px; }
        .contact-channel-value { font-size: 0.88rem; }
    }
</style>
@endpush

@section('content')
@php
    $adminWaLink = '6285122616191';
    $adminEmail = 'yomudachampionship@gmail.com';
@endphp

<div class="contact-page">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-7 col-xl-6">

                <a href="{{ route('home') }}" class="contact-back">
                    <i class="bi bi-arrow-left"></i> Kembali ke Beranda
                </a>

                <div class="contact-hero">
                    <div class="contact-hero-badge">
                        <span></span> Support 24/7
                    </div>
                    <h1>Hubungi Kami</h1>
                    <p>Butuh bantuan terkait pendaftaran atau pembayaran? Pilih saluran yang paling nyaman untuk kamu.</p>
                </div>

                <div class="contact-card">

                    <a href="https://wa.me/{{ $adminWaLink }}" target="_blank" rel="noopener" class="contact-channel">
                        <div class="contact-channel-icon icon-wa">
                            <i class="bi bi-whatsapp"></i>
                        </div>
                        <div class="contact-channel-info">
                            <div class="contact-channel-label">WhatsApp Support</div>
                            <div class="contact-channel-value">+62 851-2261-6191</div>
                            <div class="contact-channel-sub">Respon cepat, biasanya &lt; 1 jam</div>
                        </div>
                        <i class="bi bi-arrow-right contact-channel-arrow"></i>
                    </a>

                    <a href="mailto:{{ $adminEmail }}" class="contact-channel">
                        <div class="contact-channel-icon icon-email">
                            <i class="bi bi-envelope-fill"></i>
                        </div>
                        <div class="contact-channel-info">
                            <div class="contact-channel-label">Email Official</div>
                            <div class="contact-channel-value">{{ $adminEmail }}</div>
                            <div class="contact-channel-sub">Untuk keperluan formal & dokumentasi</div>
                        </div>
                        <i class="bi bi-arrow-right contact-channel-arrow"></i>
                    </a>

                    <div class="contact-channel contact-channel-static">
                        <div class="contact-channel-icon icon-location">
                            <i class="bi bi-geo-alt-fill"></i>
                        </div>
                        <div class="contact-channel-info">
                            <div class="contact-channel-label">Alamat Usaha</div>
                            <div class="contact-channel-value">Sokaraja Kulon, Sokaraja</div>
                            <div class="contact-channel-sub">Banyumas, Jawa Tengah 53181</div>
                        </div>
                    </div>

                    <div class="contact-channel contact-channel-static">
                        <div class="contact-channel-icon icon-hours">
                            <i class="bi bi-clock-fill"></i>
                        </div>
                        <div class="contact-channel-info">
                            <div class="contact-channel-label">Jam Operasional</div>
                            <div class="contact-channel-value">09:00 – 22:00 WIB</div>
                            <div class="contact-channel-sub">Senin s/d Minggu, setiap hari</div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>
@endsection