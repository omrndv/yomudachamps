@extends('layouts.app')

@section('title', 'Kontak Kami - Yomuda Championship')

@push('styles')
<style>
    :root {
        --ymd-yellow: #ffc107;
        --ymd-card: #141618;
        --ymd-border: rgba(255, 255, 255, 0.06);
    }

    .contact-page { padding: 10px 0 60px; }

    .contact-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: rgba(255, 255, 255, 0.4);
        font-size: 0.85rem;
        font-weight: 600;
        text-decoration: none;
        margin-bottom: 32px;
        transition: color 0.25s;
    }
    .contact-back:hover { color: var(--ymd-yellow); }

    .contact-hero {
        text-align: center;
        padding: 0 16px 40px;
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
        margin-bottom: 18px;
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
        font-size: clamp(1.8rem, 5vw, 2.8rem);
        font-weight: 900;
        letter-spacing: -1.5px;
        line-height: 1.15;
        margin-bottom: 12px;
        background: linear-gradient(160deg, #ffffff 40%, #ffc107 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .contact-hero p {
        max-width: 440px;
        margin: 0 auto;
        color: rgba(255, 255, 255, 0.45);
        font-size: 0.92rem;
        line-height: 1.8;
    }

    .contact-card {
        background: var(--ymd-card);
        border: 1px solid var(--ymd-border);
        border-radius: 24px;
        overflow: hidden;
    }

    .contact-channel {
        display: flex;
        align-items: center;
        gap: 16px;
        padding: 22px 28px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        text-decoration: none;
        transition: background 0.25s;
        min-width: 0;
    }

    .contact-channel:last-child { border-bottom: none; }
    .contact-channel:hover { background: rgba(255, 255, 255, 0.025); }

    .contact-channel-icon {
        width: 48px;
        height: 48px;
        min-width: 48px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        transition: transform 0.25s;
    }

    .contact-channel:hover .contact-channel-icon { transform: scale(1.08); }

    .icon-wa    { background: rgba(37,211,102,0.1);  border: 1px solid rgba(37,211,102,0.2);  color: #25D366; }
    .icon-email { background: rgba(255,193,7,0.1);   border: 1px solid rgba(255,193,7,0.2);   color: var(--ymd-yellow); }
    .icon-loc   { background: rgba(100,150,255,0.1); border: 1px solid rgba(100,150,255,0.2); color: #6496ff; }
    .icon-clock { background: rgba(200,100,255,0.1); border: 1px solid rgba(200,100,255,0.2); color: #c864ff; }

    .contact-channel-info {
        flex: 1;
        min-width: 0;
    }

    .contact-channel-label {
        font-size: 0.68rem;
        font-weight: 800;
        letter-spacing: 1.2px;
        text-transform: uppercase;
        color: rgba(255, 255, 255, 0.35);
        margin-bottom: 3px;
    }

    .contact-channel-value {
        font-weight: 700;
        font-size: 0.92rem;
        color: #fff;
        word-break: break-all;
        line-height: 1.4;
    }

    .contact-channel-sub {
        font-size: 0.75rem;
        color: rgba(255, 255, 255, 0.38);
        margin-top: 2px;
        line-height: 1.4;
    }

    .contact-channel-arrow {
        color: rgba(255, 255, 255, 0.18);
        font-size: 0.85rem;
        flex-shrink: 0;
        transition: color 0.25s, transform 0.25s;
        margin-left: 4px;
    }

    .contact-channel:hover .contact-channel-arrow {
        color: var(--ymd-yellow);
        transform: translateX(4px);
    }

    .contact-channel-static { cursor: default; }
    .contact-channel-static:hover { background: transparent; }

    @media (max-width: 576px) {
        .contact-channel { padding: 18px 18px; gap: 14px; }
        .contact-channel-icon { width: 42px; height: 42px; min-width: 42px; font-size: 1.1rem; border-radius: 12px; }
        .contact-channel-value { font-size: 0.85rem; }
        .contact-channel-sub { font-size: 0.72rem; }
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
            <div class="col-12 col-md-10 col-lg-8 col-xl-6">

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
                            <div class="contact-channel-sub">Untuk keperluan formal &amp; dokumentasi</div>
                        </div>
                        <i class="bi bi-arrow-right contact-channel-arrow"></i>
                    </a>

                    <div class="contact-channel contact-channel-static">
                        <div class="contact-channel-icon icon-loc">
                            <i class="bi bi-geo-alt-fill"></i>
                        </div>
                        <div class="contact-channel-info">
                            <div class="contact-channel-label">Alamat Usaha</div>
                            <div class="contact-channel-value">Sokaraja Kulon, Sokaraja</div>
                            <div class="contact-channel-sub">Banyumas, Jawa Tengah 53181</div>
                        </div>
                    </div>

                    <div class="contact-channel contact-channel-static">
                        <div class="contact-channel-icon icon-clock">
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