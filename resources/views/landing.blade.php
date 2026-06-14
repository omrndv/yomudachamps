@extends('layouts.app')

@section('title', 'Yomuda Championship - Platform Turnamen E-sports Terpercaya')

@push('styles')
<style>
    :root {
        --ymd-yellow: #ffc107;
        --ymd-dark: #16191c;
    }

    .hero-section {
        position: relative;
        overflow: hidden;
        padding: 90px 0 70px;
        text-align: center;
        background:
            radial-gradient(circle at top, rgba(255, 193, 7, 0.14) 0%, transparent 42%),
            radial-gradient(circle at center, rgba(255, 193, 7, 0.07) 0%, transparent 70%);
    }

    .hero-section::before {
        content: '';
        position: absolute;
        inset: 0;
        background-image:
            linear-gradient(rgba(255, 255, 255, 0.025) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255, 255, 255, 0.025) 1px, transparent 1px);
        background-size: 44px 44px;
        mask-image: linear-gradient(to bottom, black, transparent 82%);
        pointer-events: none;
    }

    .hero-content {
        position: relative;
        z-index: 2;
    }

    .hero-logo {
        width: 125px;
        filter: drop-shadow(0 0 26px rgba(255, 193, 7, 0.32));
    }

    .hero-title {
        font-size: clamp(2.5rem, 5vw, 4.3rem);
        font-weight: 900;
        letter-spacing: -2px;
        background: linear-gradient(to bottom, #ffffff 35%, #ffc107 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 20px;
    }

    .hero-subtitle {
        max-width: 720px;
        font-size: 1.08rem;
        line-height: 1.9;
    }

    .btn-hero {
        background: var(--ymd-yellow);
        color: #000;
        font-weight: 900;
        border-radius: 16px;
        padding: 15px 32px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 0 28px rgba(255, 193, 7, 0.22);
        transition: transform 0.25s ease, box-shadow 0.25s ease, background 0.25s ease;
        border: 1px solid rgba(255, 255, 255, 0.15);
        letter-spacing: 0.5px;
    }

    .btn-hero:hover {
        background: #ffffff;
        color: #000;
        transform: translateY(-2px);
        box-shadow: 0 0 35px rgba(255, 255, 255, 0.18);
    }

    .section-box {
        background: rgba(255, 255, 255, 0.018);
        border-radius: 40px;
        border: 1px solid rgba(255, 255, 255, 0.05);
    }

    .section-title {
        font-weight: 700;
        letter-spacing: 2px;
        position: relative;
        display: inline-block;
        padding-bottom: 12px;
    }

    .section-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 56px;
        height: 3px;
        background: var(--ymd-yellow);
        border-radius: 999px;
        box-shadow: 0 0 18px rgba(255, 193, 7, 0.6);
    }

    .step-card {
        height: 100%;
        background: rgba(255, 255, 255, 0.035);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 26px;
        transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease, background 0.3s ease;
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
    }

    .step-card:hover {
        transform: translateY(-5px);
        border-color: rgba(255, 193, 7, 0.6);
        background: rgba(255, 193, 7, 0.035);
        box-shadow: 0 18px 45px rgba(0, 0, 0, 0.22);
    }

    .step-number-circle {
        width: 44px;
        height: 44px;
        background: var(--ymd-yellow);
        color: #000;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 900;
        font-size: 1rem;
        box-shadow: 0 0 22px rgba(255, 193, 7, 0.38);
    }

    .tournament-card {
        height: 100%;
        position: relative;
        overflow: hidden;
        background: var(--ymd-dark);
        border-radius: 30px;
        border: 1px solid rgba(255, 255, 255, 0.07);
        transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
        box-shadow: 0 16px 45px rgba(0, 0, 0, 0.18);
    }

    .tournament-card:hover {
        transform: translateY(-6px);
        border-color: rgba(255, 193, 7, 0.45);
        box-shadow: 0 26px 60px rgba(0, 0, 0, 0.32);
    }

    .card-img-wrapper {
        position: relative;
        height: 380px;
        overflow: hidden;
        background: #050505;
        transform: translateZ(0);
    }

    .card-img-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: 0.88;
        transition: transform 0.35s ease, opacity 0.35s ease;
    }

    .tournament-card:hover .card-img-wrapper img {
        opacity: 1;
        transform: scale(1.04);
    }

    .poster-placeholder {
        width: 100%;
        height: 100%;
        background:
            radial-gradient(circle at top, rgba(255, 193, 7, 0.18), transparent 50%),
            linear-gradient(135deg, #111418, #050505);
        display: flex;
        align-items: center;
        justify-content: center;
        color: rgba(255, 255, 255, 0.4);
        font-size: 3rem;
    }

    .card-overlay {
        position: absolute;
        inset: 0;
        background:
            linear-gradient(to bottom, rgba(0, 0, 0, 0.05) 35%, rgba(22, 25, 28, 1) 100%),
            linear-gradient(to top, rgba(0, 0, 0, 0.35), transparent 55%);
        pointer-events: none;
    }

    .badge-fee {
        position: absolute;
        top: 18px;
        left: 18px;
        z-index: 2;
        background: var(--ymd-yellow);
        color: #000;
        padding: 7px 14px;
        border-radius: 12px;
        font-weight: 900;
        font-size: 0.72rem;
        letter-spacing: 0.5px;
        box-shadow: 0 10px 24px rgba(0, 0, 0, 0.25);
    }

    .badge-open {
        background: rgba(25, 135, 84, 0.15);
        color: #3cff95;
        border: 1px solid rgba(60, 255, 149, 0.24);
        font-size: 0.62rem;
        padding: 6px 10px;
        border-radius: 10px;
        font-weight: 900;
        letter-spacing: 0.7px;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
        margin-bottom: 20px;
    }

    .info-item {
        background: rgba(255, 255, 255, 0.035);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 16px;
        padding: 13px 14px;
    }

    .info-item small {
        display: block;
        color: rgba(255, 255, 255, 0.48);
        font-size: 0.68rem;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        font-weight: 800;
        margin-bottom: 5px;
    }

    .info-item strong {
        display: block;
        color: #fff;
        font-size: 0.9rem;
        font-weight: 900;
        line-height: 1.35;
    }

    .btn-register {
        width: 100%;
        display: block;
        background: var(--ymd-yellow);
        color: #000;
        font-weight: 900;
        border-radius: 16px;
        padding: 15px;
        text-decoration: none;
        transition: transform 0.25s ease, background 0.25s ease;
        text-transform: uppercase;
        letter-spacing: 1px;
        border: none;
        text-align: center;
    }

    .btn-register:hover {
        background: #fff;
        color: #000;
        transform: translateY(-2px);
    }

    .empty-state {
        background: rgba(255, 255, 255, 0.025);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 30px;
        padding: 55px 24px;
    }

    .empty-icon {
        width: 64px;
        height: 64px;
        margin: 0 auto 18px;
        border-radius: 22px;
        background: rgba(255, 193, 7, 0.12);
        color: var(--ymd-yellow);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        border: 1px solid rgba(255, 193, 7, 0.18);
    }

    .sponsor-alt-section {
        position: relative;
        padding: 80px 0;
        background: radial-gradient(circle at center, rgba(255, 193, 7, 0.05) 0%, transparent 75%);
        border-top: 1px solid rgba(255, 255, 255, 0.05);
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }

    .sp-alt-header {
        margin-bottom: 50px;
    }

    .sp-alt-badge {
        display: inline-block;
        background: linear-gradient(90deg, rgba(255, 193, 7, 0.1) 0%, rgba(255, 193, 7, 0.2) 100%);
        color: #ffc107;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: 3px;
        text-transform: uppercase;
        padding: 10px 28px;
        border-radius: 50px;
        border: 1px solid rgba(255, 193, 7, 0.3);
        margin-bottom: 30px;
        box-shadow: 0 4px 20px rgba(255, 193, 7, 0.1);
        text-shadow: 0 0 10px rgba(255, 193, 7, 0.3);
    }

    .sp-alt-grid {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 24px;
        margin-bottom: 50px;
    }

    .sp-alt-card {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 15px 30px;
        transition: all 0.3s ease;
        position: relative;
        background: transparent;
        border: none;
        box-shadow: none;
    }

    .sp-alt-card img {
        max-width: 280px;
        max-height: 90px;
        object-fit: contain;
        position: relative;
        z-index: 2;
        filter: brightness(0) invert(1) drop-shadow(0 0 10px rgba(255, 255, 255, 0.45));
        opacity: 0.95;
        transition: all 0.3s ease;
    }

    .sp-alt-card:hover img {
        opacity: 1;
        transform: scale(1.08);
        filter: brightness(0) invert(1) drop-shadow(0 0 16px rgba(255, 255, 255, 0.65));
    }

    .sp-alt-card .sp-fallback {
        opacity: 0.95;
        text-shadow: 0 0 10px rgba(255, 255, 255, 0.45);
        transition: all 0.3s ease;
    }

    .sp-alt-card:hover .sp-fallback {
        opacity: 1;
        transform: scale(1.08);
        text-shadow: 0 0 16px rgba(255, 255, 255, 0.65);
    }

    .sp-premium-cta {
        position: relative;
        overflow: hidden;
        background: linear-gradient(135deg, rgba(255, 193, 7, 0.06) 0%, rgba(22, 25, 28, 0.7) 100%);
        border: 1px solid rgba(255, 193, 7, 0.25);
        border-radius: 32px;
        padding: 50px 60px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 40px;
        margin-top: 70px;
        box-shadow: 
            0 30px 60px rgba(0, 0, 0, 0.3),
            inset 0 1px 0 rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
    }

    .sp-premium-cta::before {
        content: '';
        position: absolute;
        inset: 0;
        background-image: 
            linear-gradient(rgba(255, 193, 7, 0.03) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255, 193, 7, 0.03) 1px, transparent 1px);
        background-size: 20px 20px;
        mask-image: radial-gradient(circle at center, black, transparent);
        pointer-events: none;
    }

    .sp-premium-cta::after {
        content: '';
        position: absolute;
        top: -120px;
        right: -80px;
        width: 350px;
        height: 350px;
        background: radial-gradient(circle, rgba(255, 193, 7, 0.18) 0%, transparent 70%);
        border-radius: 50%;
        filter: blur(20px);
        pointer-events: none;
    }

    .sp-premium-text {
        position: relative;
        z-index: 2;
        max-width: 600px;
    }

    .sp-premium-text h4 {
        font-size: 1.8rem;
        font-weight: 900;
        color: #ffffff;
        margin-bottom: 12px;
        letter-spacing: -0.5px;
        line-height: 1.3;
    }

    .sp-premium-text h4 span {
        color: #ffc107;
        position: relative;
        display: inline-block;
    }

    .sp-premium-text p {
        font-size: 1.05rem;
        color: rgba(255, 255, 255, 0.65);
        margin: 0;
        line-height: 1.7;
    }

    .sp-premium-btn {
        position: relative;
        z-index: 2;
        background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
        color: #000000;
        font-weight: 900;
        font-size: 0.95rem;
        text-transform: uppercase;
        letter-spacing: 1.2px;
        padding: 18px 40px;
        border-radius: 20px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 12px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 10px 30px rgba(255, 193, 7, 0.25);
        flex-shrink: 0;
        border: 1px solid rgba(255, 255, 255, 0.15);
    }

    .sp-premium-btn i {
        font-size: 1.3rem;
        transition: transform 0.3s ease;
    }

    .sp-premium-btn:hover {
        transform: translateY(-4px);
        box-shadow: 0 15px 35px rgba(255, 193, 7, 0.45);
        background: #ffffff;
        color: #000000;
        border-color: #ffc107;
    }

    .sp-premium-btn:hover i {
        transform: translateX(4px) translateY(-2px) rotate(10deg);
    }

    @media (max-width: 768px) {
        .hero-section {
            padding: 65px 0 45px;
        }

        .hero-logo {
            width: 96px;
        }

        .hero-title {
            letter-spacing: -1px;
        }

        .hero-subtitle {
            font-size: 0.96rem;
            line-height: 1.75;
        }

        .card-img-wrapper {
            height: 330px;
        }

        .section-box {
            border-radius: 28px;
        }

        .info-grid {
            grid-template-columns: 1fr;
        }

        .sp-premium-cta {
            flex-direction: column;
            text-align: center;
            padding: 40px 25px;
        }

        .sp-premium-text h4 {
            font-size: 1.35rem;
        }

        .sp-premium-btn {
            width: 100%;
            justify-content: center;
        }
    }
</style>
@endpush

@section('content')
<div class="container pb-5">

    <section class="hero-section mb-5">
        <div class="hero-content">
            <img src="{{ asset('images/logo-yomuda.png') }}" alt="Yomuda Logo" class="hero-logo mb-4">

            <h1 class="hero-title text-uppercase">Yomuda Championship</h1>

            <p class="text-white-50 mx-auto px-3 hero-subtitle">
                Platform turnamen E-sports otomatis yang terintegrasi.
                Daftarkan timmu sekarang, lakukan pembayaran dengan mudah,
                dan amankan kursimu menuju arena kompetitif Yomuda.
            </p>

            <div class="mt-4">
                <a href="#tournaments" class="btn-hero">
                    JELAJAHI TURNAMEN
                    <i class="bi bi-chevron-down"></i>
                </a>
            </div>
        </div>
    </section>

    <section class="py-5 mb-5 section-box">
        <div class="text-center mb-5">
            <h3 class="section-title text-white text-uppercase mb-3">Cara Bergabung</h3>
            <p class="text-white-50 mb-0 px-3">
                Ikuti alur pendaftaran sampai pembayaran berhasil.
            </p>
        </div>

        <div class="row g-4 px-lg-5 px-3">
            <div class="col-md-6 col-lg-3">
                <div class="step-card p-4 text-center">
                    <div class="step-number-circle mx-auto mb-3">1</div>
                    <h6 class="text-warning fw-bold text-uppercase">Registrasi</h6>
                    <p class="small text-white-50 mb-0">
                        Pilih turnamen dan isi data tim kamu dengan benar.
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="step-card p-4 text-center">
                    <div class="step-number-circle mx-auto mb-3">2</div>
                    <h6 class="text-warning fw-bold text-uppercase">Checkout</h6>
                    <p class="small text-white-50 mb-0">
                        Pilih metode pembayaran yang tersedia.
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="step-card p-4 text-center">
                    <div class="step-number-circle mx-auto mb-3">3</div>
                    <h6 class="text-warning fw-bold text-uppercase">Pembayaran</h6>
                    <p class="small text-white-50 mb-0">
                        Selesaikan transaksi sebelum batas waktu pembayaran.
                    </p>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="step-card p-4 text-center">
                    <div class="step-number-circle mx-auto mb-3">4</div>
                    <h6 class="text-warning fw-bold text-uppercase">Validasi</h6>
                    <p class="small text-white-50 mb-0">
                        Status otomatis PAID dan peserta diarahkan ke grup koordinasi.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section id="tournaments" class="py-5">
        <div class="text-center mb-5">
            <h3 class="section-title text-white text-uppercase mb-3">Turnamen Aktif</h3>
            <p class="text-white-50 mb-0 px-3">
                Daftarkan tim kamu dan bersiap masuk ke arena kompetitif Yomuda.
            </p>
        </div>
        

        <div class="row g-4 justify-content-center">
            @forelse($active_seasons as $season)
                <div class="col-md-6 col-lg-4">
                    <div class="tournament-card">
                        <div class="card-img-wrapper">
                            <div class="badge-fee">
                                Rp {{ number_format($season->price ?? 0, 0, ',', '.') }}/Team
                            </div>

                            @if($season->poster)
                                <img src="{{ asset('storage/posters/' . $season->poster) }}" alt="Poster {{ $season->name }}">
                            @else
                                <div class="poster-placeholder">
                                    <i class="bi bi-trophy"></i>
                                </div>
                            @endif

                            <div class="card-overlay"></div>
                        </div>

                        <div class="p-4 pt-3">
                            <div class="d-flex justify-content-between align-items-start gap-3 mb-4">
                                <div>
                                    <h4 class="fw-bold text-white mb-1 text-uppercase" style="letter-spacing: 1px; font-size: 1.13rem;">
                                        {{ $season->name }}
                                    </h4>
                                    <small class="text-white-50">
                                        {{ $season->date_info ?: 'Tanggal akan diinformasikan' }}
                                    </small>
                                </div>

                                <span class="badge-open text-uppercase">OPEN</span>
                            </div>

                            <div class="info-grid">
                                <div class="info-item">
                                    <small>Prize Pool</small>
                                    <strong>
                                        {{ trim($season->prize_pool ?? '') !== '' ? $season->prize_pool : 'Segera diumumkan' }}
                                    </strong>
                                </div>
                            
                                <div class="info-item">
                                    <small>Registrasi</small>
                                    <strong>
                                        Rp {{ number_format($season->price ?? 0, 0, ',', '.') }}
                                    </strong>
                                </div>
                            
                                <div class="info-item">
                                    <small>Jadwal</small>
                                    <strong>
                                        {{ $season->date_info ?: 'Coming Soon' }}
                                    </strong>
                                </div>
                            
                                <div class="info-item">
                                    <small>Status</small>
                                    <strong>Dibuka</strong>
                                </div>
                            </div>

                            <a href="{{ route('register.form') }}" class="btn-register">
                                CEK PENDAFTARAN
                                <i class="bi bi-arrow-right ms-1"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="empty-state text-center">
                        <div class="empty-icon">
                            <i class="bi bi-trophy"></i>
                        </div>

                        <h5 class="text-white fw-bold mb-2">
                            Belum Ada Turnamen Aktif
                        </h5>

                        <p class="text-white-50 mb-0">
                            Pantau terus Yomuda Championship untuk informasi season berikutnya.
                        </p>
                    </div>
                </div>
            @endforelse
        </div>
    </section>

    <section class="sponsor-alt-section my-5">
        <div class="text-center sp-alt-header">
            <h3 class="section-title text-white text-uppercase d-block mb-3">Official Sponsors</h3>
            <p class="text-white-50 mb-0 px-3">
                Partner yang mendukung perjalanan kompetitif Yomuda Championship.
            </p>
        </div>

        <div class="text-center">
            <div class="sp-alt-badge">Gold Partner</div>
        </div>

        <div class="sp-alt-grid">
            <div class="sp-alt-card">
                <img
                    src="{{ asset('images/getuklogo.png') }}"
                    alt="Getuk Goreng ASRI"
                    onerror="this.style.display='none'; this.nextElementSibling.classList.remove('d-none'); this.nextElementSibling.classList.add('d-flex');"
                >

                <div class="d-none flex-column align-items-center justify-content-center w-100 h-100 text-center text-white sp-fallback" style="position: relative; z-index: 2;">
                    <i class="bi bi-trophy text-warning fs-3 mb-2"></i>
                    <span class="fw-bold text-uppercase" style="font-size: 11px; letter-spacing: 1.5px; color: #ffffff;">
                        Getuk Goreng ASRI
                    </span>
                </div>
            </div>
        </div>

        <div class="sp-premium-cta">
            <div class="sp-premium-text">
                <h4>Tertarik menjadi bagian dari <span>Yomuda Championship?</span></h4>
                <p class="mb-0">
                    Dapatkan eksposur maksimal untuk brand Anda dan jangkau ribuan audiens
                    di komunitas esports kami yang terus berkembang.
                </p>
            </div>

            <a href="https://wa.me/6285122616191" class="sp-premium-btn" target="_blank" rel="noopener noreferrer">
                <i class="bi bi-whatsapp"></i>
                Hubungi Kami
            </a>
        </div>
    </section>

</div>
@endsection