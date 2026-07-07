@extends('layouts.app')

@section('title', 'Kebijakan Privasi - Yomuda Championship')

@push('styles')
<style>
    :root {
        --ymd-yellow: #ffc107;
        --ymd-card: #141618;
        --ymd-border: rgba(255, 255, 255, 0.06);
    }

    .legal-page {
        padding: 20px 0 60px;
    }

    .legal-back {
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

    .legal-back:hover { color: var(--ymd-yellow); }

    .legal-hero {
        text-align: center;
        padding: 0 20px 50px;
    }

    .legal-hero-badge {
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

    .legal-hero-badge span {
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

    .legal-hero h1 {
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

    .legal-hero p {
        max-width: 480px;
        margin: 0 auto;
        color: rgba(255, 255, 255, 0.45);
        font-size: 0.95rem;
        line-height: 1.8;
    }

    .legal-card {
        background: var(--ymd-card);
        border: 1px solid var(--ymd-border);
        border-radius: 28px;
        padding: 48px 48px;
    }

    .legal-section {
        margin-bottom: 36px;
        padding-bottom: 36px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }

    .legal-section:last-child {
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: none;
    }

    .legal-section-number {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        height: 32px;
        border-radius: 10px;
        background: rgba(255, 193, 7, 0.1);
        border: 1px solid rgba(255, 193, 7, 0.2);
        color: var(--ymd-yellow);
        font-weight: 900;
        font-size: 0.78rem;
        flex-shrink: 0;
    }

    .legal-section h5 {
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 800;
        font-size: 1rem;
        color: #ffffff;
        margin-bottom: 14px;
    }

    .legal-section p {
        color: rgba(255, 255, 255, 0.6);
        font-size: 0.92rem;
        line-height: 1.9;
        margin: 0;
        padding-left: 44px;
    }

    .legal-tag {
        display: inline-block;
        background: rgba(255, 193, 7, 0.08);
        color: var(--ymd-yellow);
        border: 1px solid rgba(255, 193, 7, 0.18);
        border-radius: 8px;
        padding: 3px 10px;
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 0.5px;
        margin-left: auto;
    }

    .legal-updated {
        display: flex;
        align-items: center;
        gap: 8px;
        color: rgba(255, 255, 255, 0.3);
        font-size: 0.78rem;
        margin-bottom: 36px;
        padding-bottom: 28px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }

    @media (max-width: 768px) {
        .legal-card { padding: 28px 22px; }
        .legal-section p { padding-left: 0; margin-top: 10px; }
        .legal-section h5 { flex-wrap: wrap; }
    }
</style>
@endpush

@section('content')
<div class="legal-page">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-xl-7">

                <a href="{{ route('home') }}" class="legal-back">
                    <i class="bi bi-arrow-left"></i> Kembali ke Beranda
                </a>

                <div class="legal-hero">
                    <div class="legal-hero-badge">
                        <span></span> Dokumen Resmi
                    </div>
                    <h1>Kebijakan Privasi</h1>
                    <p>Kami menghargai privasi Anda dan berkomitmen penuh untuk melindungi data pribadi yang Anda percayakan kepada kami.</p>
                </div>

                <div class="legal-card">
                    <div class="legal-updated">
                        <i class="bi bi-calendar3"></i>
                        Terakhir diperbarui: Juli 2025
                    </div>

                    <div class="legal-section">
                        <h5>
                            <span class="legal-section-number">1</span>
                            Informasi Yang Kami Kumpulkan
                        </h5>
                        <p>Kami mengumpulkan informasi seperti Nama Tim dan Nomor WhatsApp untuk keperluan koordinasi turnamen dan proses pembayaran melalui gerbang pembayaran otomatis yang aman.</p>
                    </div>

                    <div class="legal-section">
                        <h5>
                            <span class="legal-section-number">2</span>
                            Penggunaan Data
                        </h5>
                        <p>Data Anda digunakan murni untuk validasi pendaftaran, pengiriman status pembayaran, dan pemberian akses ke grup koordinasi turnamen.</p>
                    </div>

                    <div class="legal-section">
                        <h5>
                            <span class="legal-section-number">3</span>
                            Keamanan Data
                        </h5>
                        <p>Kami tidak menjual atau membagikan data pribadi Anda kepada pihak ketiga selain untuk keperluan proses transaksi keuangan melalui payment gateway secara aman.</p>
                    </div>

                    <div class="legal-section">
                        <h5>
                            <span class="legal-section-number">4</span>
                            Kebijakan Pengembalian Dana
                            <span class="legal-tag">Refund Policy</span>
                        </h5>
                        <p>Biaya pendaftaran yang telah dibayarkan bersifat <strong style="color:#fff;">Non-Refundable</strong> atas pembatalan sepihak oleh peserta. Namun, apabila turnamen secara resmi dibatalkan atau ditunda secara total oleh pihak penyelenggara, seluruh tim berhak mendapatkan <strong style="color:#fff;">pengembalian dana 100%</strong> yang akan diproses maksimal dalam waktu <strong style="color:#fff;">3 hari kerja</strong> ke rekening asal atau transfer.</p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection