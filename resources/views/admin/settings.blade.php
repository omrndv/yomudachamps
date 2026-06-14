@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4" style="background-color: #f8fafc; min-height: 100vh;">
    {{-- Header --}}
    <div class="row mb-4 align-items-center">
        <div class="col-md-12">
            <h2 class="fw-bold text-dark mb-1" style="font-size: 1.75rem; letter-spacing: -0.5px;">
                Pengaturan Umum Sistem
            </h2>
            <p class="text-secondary mb-0" style="font-size: 0.9rem;">
                Konfigurasi token WhatsApp Fonnte, kredensial Tripay Gateway, sosial media, dan aset visual turnamen.
            </p>
        </div>
    </div>

    {{-- Alert Success --}}
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    {{-- Form Pengaturan --}}
    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="row g-4">
        @csrf
        
        {{-- Kiri: Pengaturan WhatsApp & Sosial Media --}}
        <div class="col-lg-6">
            {{-- Card 1: WhatsApp & Support --}}
            <div class="card card-settings border-0 p-4 mb-4 bg-white">
                <h5 class="fw-bold text-dark mb-4 pb-2 border-bottom border-light">
                    <i class="bi bi-whatsapp text-success me-2"></i> WhatsApp & Support Settings
                </h5>

                <div class="mb-4">
                    <div class="form-check form-switch p-0 d-flex justify-content-between align-items-center bg-light-subtle p-3 rounded-3 border border-light-subtle" style="background-color: #f8fafc;">
                        <label class="form-check-label fw-bold text-dark mb-0 ps-1" for="wa_notification_enabled" style="font-size: 0.85rem;">
                            <i class="bi bi-bell-fill text-warning me-2"></i> Notifikasi WhatsApp Otomatis
                        </label>
                        <input class="form-check-input ms-0 shadow-none" type="checkbox" role="switch" id="wa_notification_enabled" name="wa_notification_enabled" value="true" style="cursor: pointer;"
                            {{ \App\Models\Setting::getVal('wa_notification_enabled', env('WA_NOTIFICATION_ENABLED', false)) == 'true' || \App\Models\Setting::getVal('wa_notification_enabled', env('WA_NOTIFICATION_ENABLED', false)) === true ? 'checked' : '' }}>
                    </div>
                    <small class="text-muted d-block mt-2" style="font-size: 0.75rem;">
                        Aktifkan untuk mengirim pesan WhatsApp otomatis konfirmasi koordinasi ketika tim berstatus <b>PAID</b> (Lunas).
                    </small>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary text-uppercase mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">Fonnte API Token</label>
                    <div class="input-group rounded-3 overflow-hidden border border-light-subtle shadow-none">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-key-fill text-secondary"></i></span>
                        <input type="text" name="fonnte_token" class="form-control border-0 bg-light shadow-none p-2.5" style="font-size: 0.85rem;"
                            placeholder="Token dari Fonnte Device API..." value="{{ \App\Models\Setting::getVal('fonnte_token', env('FONNTE_TOKEN')) }}">
                    </div>
                    <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Dapatkan di dashboard Fonnte pada menu Device/API Token.</small>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary text-uppercase mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">Nomor WA Support Admin</label>
                    <div class="input-group rounded-3 overflow-hidden border border-light-subtle shadow-none">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-telephone-fill text-secondary"></i></span>
                        <input type="text" name="admin_wa" class="form-control border-0 bg-light shadow-none p-2.5" style="font-size: 0.85rem;"
                            placeholder="Contoh: 0851-2261-6191..." value="{{ \App\Models\Setting::getVal('admin_wa', '0851-2261-6191') }}" required>
                    </div>
                    <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Nomor ini akan tertera di template WhatsApp otomatis dan halaman kontak.</small>
                </div>

                <div class="mb-0">
                    <label class="form-label small fw-bold text-secondary text-uppercase mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">Email Support Turnamen</label>
                    <div class="input-group rounded-3 overflow-hidden border border-light-subtle shadow-none">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-envelope-fill text-secondary"></i></span>
                        <input type="email" name="admin_email" class="form-control border-0 bg-light shadow-none p-2.5" style="font-size: 0.85rem;"
                            placeholder="Contoh: support@yomuda.com..." value="{{ \App\Models\Setting::getVal('admin_email', 'yomudachampionship@gmail.com') }}" required>
                    </div>
                    <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Email resmi turnamen yang ditampilkan di halaman bantuan.</small>
                </div>
            </div>

            {{-- Card 2: Sosial Media --}}
            <div class="card card-settings border-0 p-4 bg-white">
                <h5 class="fw-bold text-dark mb-4 pb-2 border-bottom border-light">
                    <i class="bi bi-share text-info me-2"></i> Tautan Media Sosial
                </h5>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary text-uppercase mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">Link Instagram</label>
                    <div class="input-group rounded-3 overflow-hidden border border-light-subtle shadow-none">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-instagram text-secondary"></i></span>
                        <input type="url" name="social_instagram" class="form-control border-0 bg-light shadow-none p-2.5" style="font-size: 0.85rem;"
                            placeholder="https://www.instagram.com/..." value="{{ \App\Models\Setting::getVal('social_instagram', 'https://www.instagram.com/yomuda.championship/') }}" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary text-uppercase mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">Link TikTok</label>
                    <div class="input-group rounded-3 overflow-hidden border border-light-subtle shadow-none">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-tiktok text-secondary"></i></span>
                        <input type="url" name="social_tiktok" class="form-control border-0 bg-light shadow-none p-2.5" style="font-size: 0.85rem;"
                            placeholder="https://www.tiktok.com/..." value="{{ \App\Models\Setting::getVal('social_tiktok', 'https://www.tiktok.com/@yomudachampionship') }}" required>
                    </div>
                </div>

                <div class="mb-0">
                    <label class="form-label small fw-bold text-secondary text-uppercase mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">Link YouTube</label>
                    <div class="input-group rounded-3 overflow-hidden border border-light-subtle shadow-none">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-youtube text-secondary"></i></span>
                        <input type="url" name="social_youtube" class="form-control border-0 bg-light shadow-none p-2.5" style="font-size: 0.85rem;"
                            placeholder="https://www.youtube.com/..." value="{{ \App\Models\Setting::getVal('social_youtube', 'https://www.youtube.com/@ymdchamps/streams') }}" required>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kanan: Pengaturan Tripay & Upload Aset --}}
        <div class="col-lg-6">
            {{-- Card 3: Tripay Gateway --}}
            <div class="card card-settings border-0 p-4 mb-4 bg-white">
                <h5 class="fw-bold text-dark mb-4 pb-2 border-bottom border-light">
                    <i class="bi bi-credit-card-2-back text-primary me-2"></i> Tripay Gateway Settings
                </h5>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary text-uppercase mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">Mode Tripay</label>
                    <div class="input-group rounded-3 overflow-hidden border border-light-subtle shadow-none">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-shield-fill text-secondary"></i></span>
                        <select name="tripay_mode" class="form-select border-0 bg-light shadow-none p-2.5" style="font-size: 0.85rem; cursor: pointer;">
                            <option value="sandbox" {{ \App\Models\Setting::getVal('tripay_mode', env('TRIPAY_MODE')) == 'sandbox' ? 'selected' : '' }}>Sandbox / Development (Testing)</option>
                            <option value="live" {{ \App\Models\Setting::getVal('tripay_mode', env('TRIPAY_MODE')) == 'live' ? 'selected' : '' }}>Live / Production (Asli)</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary text-uppercase mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">Tripay Merchant Code</label>
                    <div class="input-group rounded-3 overflow-hidden border border-light-subtle shadow-none">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-hash text-secondary"></i></span>
                        <input type="text" name="tripay_merchant_code" class="form-control border-0 bg-light shadow-none p-2.5" style="font-size: 0.85rem;"
                            placeholder="Contoh: T12345..." value="{{ \App\Models\Setting::getVal('tripay_merchant_code', env('TRIPAY_MERCHANT_CODE')) }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary text-uppercase mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">Tripay API Key</label>
                    <div class="input-group rounded-3 overflow-hidden border border-light-subtle shadow-none">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-key-fill text-secondary"></i></span>
                        <input type="text" name="tripay_api_key" class="form-control border-0 bg-light shadow-none p-2.5" style="font-size: 0.85rem;"
                            placeholder="Tripay API Key..." value="{{ \App\Models\Setting::getVal('tripay_api_key', env('TRIPAY_API_KEY')) }}">
                    </div>
                </div>

                <div class="mb-0">
                    <label class="form-label small fw-bold text-secondary text-uppercase mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">Tripay Private Key</label>
                    <div class="input-group rounded-3 overflow-hidden border border-light-subtle shadow-none">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-lock-fill text-secondary"></i></span>
                        <input type="text" name="tripay_private_key" class="form-control border-0 bg-light shadow-none p-2.5" style="font-size: 0.85rem;"
                            placeholder="Tripay Private Key..." value="{{ \App\Models\Setting::getVal('tripay_private_key', env('TRIPAY_PRIVATE_KEY')) }}">
                    </div>
                </div>
            </div>

            {{-- Card 4: Web Assets --}}
            <div class="card card-settings border-0 p-4 bg-white">
                <h5 class="fw-bold text-dark mb-4 pb-2 border-bottom border-light">
                    <i class="bi bi-image text-warning me-2"></i> Website Assets Upload
                </h5>

                <div class="mb-4">
                    <label class="form-label small fw-bold text-secondary text-uppercase mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">Logo Yomuda (PNG)</label>
                    <input type="file" name="logo" class="form-control border border-light-subtle rounded-3 shadow-none p-2" accept="image/png" onchange="previewAsset(this, 'logoPreview')">
                    <small class="text-muted d-block mt-2" style="font-size: 0.75rem;">
                        Pilih file PNG transparan untuk mengganti logo utama turnamen di header & footer.
                    </small>
                    <div class="mt-3 text-center">
                        <img id="logoPreview" src="{{ asset('images/logo-yomuda.png') }}" class="img-thumbnail bg-dark p-2 border-0 rounded-3 shadow-sm" style="max-height: 80px;">
                    </div>
                </div>

                <div class="mb-0">
                    <label class="form-label small fw-bold text-secondary text-uppercase mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">Favicon Website (ICO/PNG)</label>
                    <input type="file" name="favicon" class="form-control border border-light-subtle rounded-3 shadow-none p-2" accept="image/x-icon, image/png" onchange="previewAsset(this, 'faviconPreview')">
                    <small class="text-muted d-block mt-2" style="font-size: 0.75rem;">
                        Ganti ikon kecil yang muncul di tab browser kamu.
                    </small>
                    <div class="mt-3 text-center">
                        <img id="faviconPreview" src="{{ asset('favicon.ico') }}" class="img-thumbnail p-2 border-0 rounded-3 shadow-sm" style="max-height: 40px; max-width: 40px;">
                    </div>
                </div>
            </div>
        </div>

        {{-- Tombol Submit --}}
        <div class="col-12 text-end mt-4">
            <button type="submit" class="btn btn-warning fw-bold px-5 py-3 rounded-pill shadow text-dark transition-all" style="letter-spacing: 0.3px;">
                <i class="bi bi-check-lg me-1"></i> SIMPAN PENGATURAN
            </button>
        </div>
    </form>
</div>

{{-- Styling Khusus --}}
<style>
    .card-settings {
        background: #ffffff;
        border: 1px solid rgba(241, 245, 249, 0.8) !important;
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.01);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .card-settings:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.04), 0 4px 6px -2px rgba(0, 0, 0, 0.02);
    }
</style>

{{-- Script Preview Aset --}}
<script>
    function previewAsset(input, previewId) {
        const preview = document.getElementById(previewId);
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
