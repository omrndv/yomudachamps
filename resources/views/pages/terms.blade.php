@extends('layouts.app')

@section('title', 'Ketentuan Layanan - Yomuda Championship')

@push('styles')
<style>
    :root {
        --ymd-yellow: #ffc107;
        --ymd-card: #141618;
        --ymd-border: rgba(255, 255, 255, 0.06);
    }

    .legal-page { padding: 10px 0 60px; width: 100%; }

    .legal-back {
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
    .legal-back:hover { color: var(--ymd-yellow); }

    .legal-hero {
        text-align: center;
        padding: 0 16px 40px;
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
        margin-bottom: 18px;
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

    .legal-hero p {
        max-width: 460px;
        margin: 0 auto;
        color: rgba(255, 255, 255, 0.45);
        font-size: 0.92rem;
        line-height: 1.8;
    }

    .legal-card {
        background: var(--ymd-card);
        border: 1px solid var(--ymd-border);
        border-radius: 24px;
        padding: 36px 36px;
    }

    .legal-updated {
        display: flex;
        align-items: center;
        gap: 8px;
        color: rgba(255, 255, 255, 0.28);
        font-size: 0.76rem;
        margin-bottom: 28px;
        padding-bottom: 24px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }

    .legal-section {
        margin-bottom: 28px;
        padding-bottom: 28px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }

    .legal-section:last-child {
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: none;
    }

    .legal-section-head {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        margin-bottom: 12px;
    }

    .legal-section-number {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 30px;
        min-width: 30px;
        border-radius: 9px;
        background: rgba(255, 193, 7, 0.1);
        border: 1px solid rgba(255, 193, 7, 0.2);
        color: var(--ymd-yellow);
        font-weight: 900;
        font-size: 0.76rem;
        margin-top: 1px;
    }

    .legal-section-title {
        font-weight: 800;
        font-size: 0.97rem;
        color: #fff;
        line-height: 1.4;
        flex: 1;
        min-width: 0;
    }

    .legal-tag {
        display: inline-block;
        background: rgba(255, 193, 7, 0.08);
        color: var(--ymd-yellow);
        border: 1px solid rgba(255, 193, 7, 0.18);
        border-radius: 7px;
        padding: 2px 9px;
        font-size: 0.68rem;
        font-weight: 800;
        letter-spacing: 0.5px;
        white-space: nowrap;
        margin-left: 8px;
        vertical-align: middle;
    }

    .legal-tag-red {
        background: rgba(255, 80, 80, 0.08);
        color: #ff6b6b;
        border-color: rgba(255, 80, 80, 0.2);
    }

    .legal-section p {
        color: rgba(255, 255, 255, 0.58);
        font-size: 0.9rem;
        line-height: 1.9;
        margin: 0;
        padding-left: 44px;
    }

    @media (max-width: 576px) {
        .legal-card { padding: 24px 20px; }
        .legal-section p { padding-left: 0; margin-top: 8px; }
        .legal-section-head { gap: 10px; }
    }
</style>
@endpush

@section('content')
<div class="legal-page">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-md-10 col-lg-8 col-xl-7">

                <a href="{{ route('home') }}" class="legal-back">
                    <i class="bi bi-arrow-left"></i> Kembali ke Beranda
                </a>

                <div class="legal-hero">
                    <div class="legal-hero-badge">
                        <span></span> Dokumen Resmi
                    </div>
                    <h1>Syarat &amp; Ketentuan</h1>
                    <p>Dengan mendaftar di Yomuda Championship, Anda menyetujui seluruh ketentuan layanan yang berlaku berikut ini.</p>
                </div>

                <div class="legal-card">
                    <div class="legal-updated">
                        <i class="bi bi-calendar3"></i>
                        Terakhir diperbarui: Juli 2025
                    </div>

                    <div class="legal-section">
                        <div class="legal-section-head">
                            <span class="legal-section-number">1</span>
                            <span class="legal-section-title">Ketentuan Umum</span>
                        </div>
                        <p>Dengan mendaftar di Yomuda Championship, Anda setuju untuk mengikuti seluruh aturan kompetisi dan regulasi yang telah ditetapkan oleh panitia penyelenggara.</p>
                    </div>

                    <div class="legal-section">
                        <div class="legal-section-head">
                            <span class="legal-section-number">2</span>
                            <span class="legal-section-title">Pendaftaran &amp; Pembayaran</span>
                        </div>
                        <p>Proses pendaftaran tim dilakukan secara otomatis melalui platform kami dengan dukungan payment gateway otomatis. Peserta diharapkan segera menyelesaikan pembayaran setelah mendaftar untuk mengamankan slot turnamen.</p>
                    </div>

                    <div class="legal-section">
                        <div class="legal-section-head">
                            <span class="legal-section-number">3</span>
                            <span class="legal-section-title">Pengembalian Dana <span class="legal-tag">Refund Policy</span></span>
                        </div>
                        <p>Biaya pendaftaran yang telah dibayarkan bersifat <strong style="color:#fff;">Non-Refundable</strong> atas pembatalan sepihak oleh peserta. Namun, apabila turnamen secara resmi dibatalkan atau ditunda secara total oleh pihak penyelenggara, seluruh tim berhak mendapatkan <strong style="color:#fff;">pengembalian dana 100%</strong> yang akan diproses maksimal dalam waktu <strong style="color:#fff;">3 hari kerja</strong>.</p>
                    </div>

                    <div class="legal-section">
                        <div class="legal-section-head">
                            <span class="legal-section-number">4</span>
                            <span class="legal-section-title">Kebijakan Kompetisi <span class="legal-tag legal-tag-red">Penting</span></span>
                        </div>
                        <p>Segala bentuk tindakan tidak sportif seperti penggunaan program ilegal (Cheat / Map Hack) atau penggunaan joki akan berakibat pada <strong style="color:#ff6b6b;">diskualifikasi tim</strong> tanpa adanya pengembalian biaya pendaftaran.</p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection