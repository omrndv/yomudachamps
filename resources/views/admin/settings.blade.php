@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4" style="background-color: #f8fafc; min-height: 100vh;">
    {{-- Header --}}
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <h2 class="fw-bold text-dark mb-1" style="font-size: 1.75rem; letter-spacing: -0.5px;">
                Pengaturan Umum Sistem
            </h2>
            <p class="text-secondary mb-0" style="font-size: 0.9rem;">
                Konfigurasi token WhatsApp Fonnte, kredensial Tripay Gateway, sosial media, dan aset visual turnamen.
            </p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <a href="/qris-gateway/dashboard" class="btn btn-primary d-inline-flex align-items-center gap-2" style="background-color: #4f46e5; border-color: #4f46e5; border-radius: 12px; font-weight: 600; padding: 10px 18px; font-size: 0.85rem;">
                <i class="bi bi-wallet2"></i> Kelola Dips Gateway (QRIS)
            </a>
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

                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary text-uppercase mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">Email Support Turnamen</label>
                    <div class="input-group rounded-3 overflow-hidden border border-light-subtle shadow-none">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-envelope-fill text-secondary"></i></span>
                        <input type="email" name="admin_email" class="form-control border-0 bg-light shadow-none p-2.5" style="font-size: 0.85rem;"
                            placeholder="Contoh: support@yomuda.com..." value="{{ \App\Models\Setting::getVal('admin_email', 'yomudachampionship@gmail.com') }}" required>
                    </div>
                    <small class="text-muted d-block mt-1" style="font-size: 0.75rem;">Email resmi turnamen yang ditampilkan di halaman bantuan.</small>
                </div>

                <div class="mb-0 pt-2 border-top border-light mt-3">
                    <label class="form-label small fw-bold text-secondary text-uppercase mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">Template WhatsApp (Status PAID)</label>
                    <textarea name="wa_template_paid" class="form-control rounded-3 shadow-none border border-light-subtle p-2.5 bg-light" rows="8" style="font-size: 0.85rem;" placeholder="Template pesan WhatsApp otomatis ketika status PAID...">{{ \App\Models\Setting::getVal('wa_template_paid', "Halo *{nama_tim}*! 🎮\n\nPembayaran pendaftaran turnamen *Yomuda Championship {nama_season}* telah *BERHASIL DIVERIFIKASI* (Lunas) dengan ID Transaksi: {id_transaksi}.\n\n{grup_info}Terima kasih telah bergabung, siapkan squad terbaikmu! 🔥\n\nKalau mau tanya-tanya bisa hubungi admin ke {nomor_admin} yaa.\n\n-- Yomuda Championship --") }}</textarea>
                    <small class="text-muted d-block mt-2" style="font-size: 0.73rem; line-height: 1.4;">
                        <strong>Placeholder dinamis:</strong><br>
                        <code>{nama_tim}</code> : Nama tim pendaftar<br>
                        <code>{nama_season}</code> : Nama season turnamen<br>
                        <code>{id_transaksi}</code> : Kode / ID Transaksi<br>
                        <code>{link_grup}</code> : Link grup WA koordinasi<br>
                        <code>{grup_info}</code> : Kalimat ajakan join grup WA otomatis<br>
                        <code>{nomor_admin}</code> : Nomor WA admin support<br>
                        <code>{harga}</code> : Harga tiket / biaya pendaftaran
                    </small>
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

            {{-- Card: Google Gemini API Configuration --}}
            <div class="card card-settings border-0 p-4 bg-white mb-4 shadow-sm rounded-4">
                <h5 class="fw-bold text-dark mb-4 pb-2 border-bottom border-light">
                    <i class="bi bi-stars text-success me-2"></i> Google Gemini AI Configuration
                </h5>
                <div class="mb-0">
                    <label class="form-label small fw-bold text-secondary text-uppercase mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">Gemini API Key</label>
                    <div class="input-group rounded-3 overflow-hidden border border-light-subtle shadow-none">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-key-fill text-success"></i></span>
                        <input type="text" name="gemini_api_key" class="form-control border-0 bg-light shadow-none p-2.5" style="font-size: 0.85rem;"
                            placeholder="AIzaSy..." value="{{ \App\Models\Setting::getVal('gemini_api_key', env('GEMINI_API_KEY')) }}">
                    </div>
                    <small class="text-muted d-block mt-2" style="font-size: 0.72rem;">
                        Masukkan API Key Gemini untuk mengaktifkan AI Rangkuman Juara & Validasi Laporan Menang otomatis. Dapatkan kunci gratis di <a href="https://aistudio.google.com/" target="_blank" class="text-success fw-bold text-decoration-none">Google AI Studio</a>.
                    </small>
                </div>
            </div>

            {{-- Card 4: Web Assets --}}
            <div class="card card-settings border-0 p-4 bg-white mb-4">
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

            {{-- Card 4.5: Tournament Rules --}}
            <div class="card card-settings border-0 p-4 mb-4 bg-white">
                <h5 class="fw-bold text-dark mb-4 pb-2 border-bottom border-light">
                    <i class="bi bi-file-earmark-ruled text-warning me-2"></i> Pengaturan Rules Turnamen
                </h5>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary text-uppercase mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">Link Google Drive Rules (Alternative)</label>
                    <div class="input-group rounded-3 overflow-hidden border border-light-subtle shadow-none">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-link-45deg text-secondary"></i></span>
                        <input type="url" name="global_rules_link" class="form-control border-0 bg-light shadow-none p-2.5" style="font-size: 0.85rem;"
                            placeholder="https://drive.google.com/..." value="{{ \App\Models\Setting::getVal('global_rules_link') }}">
                    </div>
                </div>

                <div class="mb-0">
                    <label class="form-label small fw-bold text-secondary text-uppercase mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">Upload File PDF Rules (Utama)</label>
                    <input type="file" name="rules_file" class="form-control border border-light-subtle rounded-3 shadow-none p-2" accept="application/pdf">
                    <small class="text-muted d-block mt-2" style="font-size: 0.75rem;">
                        Pilih file PDF peraturan turnamen. Jika di-upload, ini akan otomatis menggantikan link Google Drive di atas.
                    </small>
                </div>
            </div>

            {{-- Card 6: Log Retention / Auto Clean --}}
            <div class="card card-settings border-0 p-4 mb-0 bg-white">
                <h5 class="fw-bold text-dark mb-4 pb-2 border-bottom border-light">
                    <i class="bi bi-clock-history text-warning me-2"></i> Pembersihan Log Otomatis (Log Rotation)
                </h5>

                <div class="mb-0">
                    <label class="form-label small fw-bold text-secondary text-uppercase mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">Batas Penyimpanan Log (Hari)</label>
                    <div class="input-group rounded-3 overflow-hidden border border-light-subtle shadow-none">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-calendar-event-fill text-secondary"></i></span>
                        <input type="number" name="log_retention_days" class="form-control border-0 bg-light shadow-none p-2.5" style="font-size: 0.85rem;"
                            placeholder="15" min="1" value="{{ \App\Models\Setting::getVal('log_retention_days', '15') }}" required>
                    </div>
                    <small class="text-muted d-block mt-2" style="font-size: 0.75rem;">
                        Log aktivitas admin yang sudah melewati batas jumlah hari ini akan otomatis dibersihkan secara berkala untuk menghemat kapasitas database.
                    </small>
                </div>
            </div>
        </div>

        {{-- Kanan: Pengaturan Tripay & Upload Aset --}}
        <div class="col-lg-6">

            {{-- Card: Tripay Gateway Settings --}}
            <div class="card card-settings border-0 p-4 mb-4 bg-white">
                <h5 class="fw-bold text-dark mb-4 pb-2 border-bottom border-light">
                    <i class="bi bi-credit-card-2-front text-warning me-2"></i> Pengaturan TriPay Gateway
                </h5>

                 <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary text-uppercase mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">TriPay Mode</label>
                    <select name="tripay_mode" class="form-select rounded-3 shadow-none border-light-subtle" required>
                        <option value="sandbox" {{ \App\Models\Setting::getVal('tripay_mode', env('TRIPAY_MODE', 'sandbox')) === 'sandbox' ? 'selected' : '' }}>Sandbox (Testing)</option>
                        <option value="production" {{ \App\Models\Setting::getVal('tripay_mode', env('TRIPAY_MODE', 'sandbox')) === 'production' ? 'selected' : '' }}>Production (Live)</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary text-uppercase mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">Merchant Code</label>
                    <div class="input-group rounded-3 overflow-hidden border border-light-subtle shadow-none">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-hash text-secondary"></i></span>
                        <input type="text" name="tripay_merchant_code" class="form-control border-0 bg-light shadow-none p-2.5" style="font-size: 0.85rem;"
                            placeholder="T..." value="{{ \App\Models\Setting::getVal('tripay_merchant_code', env('TRIPAY_MERCHANT_CODE')) }}" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary text-uppercase mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">API Key</label>
                    <div class="input-group rounded-3 overflow-hidden border border-light-subtle shadow-none">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-key-fill text-secondary"></i></span>
                        <input type="text" name="tripay_api_key" class="form-control border-0 bg-light shadow-none p-2.5" style="font-size: 0.85rem;"
                            placeholder="DEV-..." value="{{ \App\Models\Setting::getVal('tripay_api_key', env('TRIPAY_API_KEY')) }}" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary text-uppercase mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">Private Key</label>
                    <div class="input-group rounded-3 overflow-hidden border border-light-subtle shadow-none">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-shield-lock-fill text-secondary"></i></span>
                        <input type="text" name="tripay_private_key" class="form-control border-0 bg-light shadow-none p-2.5" style="font-size: 0.85rem;"
                            placeholder="Kunci privat..." value="{{ \App\Models\Setting::getVal('tripay_private_key', env('TRIPAY_PRIVATE_KEY')) }}" required>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="payment_gateway_tripay" id="payment_gateway_tripay" value="on" {{ \App\Models\Setting::getVal('payment_gateway_tripay', 'on') === 'on' ? 'checked' : '' }}>
                        <label class="form-check-label small fw-bold text-dark" for="payment_gateway_tripay">Aktifkan Gateway TriPay</label>
                    </div>
                </div>
            </div>

            {{-- Card: iPaymu Gateway Settings --}}
            <div class="card card-settings border-0 p-4 mb-4 bg-white">
                <h5 class="fw-bold text-dark mb-4 pb-2 border-bottom border-light">
                    <i class="bi bi-credit-card text-warning me-2"></i> Pengaturan iPaymu Gateway
                </h5>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary text-uppercase mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">iPaymu Mode</label>
                    <select name="ipaymu_mode" class="form-select rounded-3 shadow-none border-light-subtle">
                        <option value="sandbox" {{ \App\Models\Setting::getVal('ipaymu_mode', 'sandbox') === 'sandbox' ? 'selected' : '' }}>Sandbox (Testing)</option>
                        <option value="production" {{ \App\Models\Setting::getVal('ipaymu_mode', 'sandbox') === 'production' ? 'selected' : '' }}>Production (Live)</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary text-uppercase mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">Virtual Account / VA</label>
                    <div class="input-group rounded-3 overflow-hidden border border-light-subtle shadow-none">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-hash text-secondary"></i></span>
                        <input type="text" name="ipaymu_va" class="form-control border-0 bg-light shadow-none p-2.5" style="font-size: 0.85rem;"
                            placeholder="Nomor VA iPaymu..." value="{{ \App\Models\Setting::getVal('ipaymu_va', env('IPAYMU_VA')) }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary text-uppercase mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">API Key / Secret Key</label>
                    <div class="input-group rounded-3 overflow-hidden border border-light-subtle shadow-none">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-key-fill text-secondary"></i></span>
                        <input type="text" name="ipaymu_api_key" class="form-control border-0 bg-light shadow-none p-2.5" style="font-size: 0.85rem;"
                            placeholder="Secret Key..." value="{{ \App\Models\Setting::getVal('ipaymu_api_key', env('IPAYMU_API_KEY')) }}">
                    </div>
                </div>

                <div class="mb-0">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="payment_gateway_ipaymu" id="payment_gateway_ipaymu" value="on" {{ \App\Models\Setting::getVal('payment_gateway_ipaymu', 'off') === 'on' ? 'checked' : '' }}>
                        <label class="form-check-label small fw-bold text-dark" for="payment_gateway_ipaymu">Aktifkan Gateway iPaymu</label>
                    </div>
                </div>
            </div>

            {{-- Card: GoPay QRIS Gateway Settings --}}
            <div class="card card-settings border-0 p-4 mb-4 bg-white">
                <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom border-light">
                    <h5 class="fw-bold text-dark mb-0">
                        <i class="bi bi-wallet2 text-warning me-2"></i> Dips Gateway (Pembayaran Manual)
                    </h5>
                    <a href="{{ route('admin.manual-payment') }}" class="btn btn-sm btn-outline-primary" style="border-radius: 8px;">
                        Kelola Gateway
                    </a>
                </div>

                <div class="mb-0">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="payment_gateway_gopay_qris" id="payment_gateway_gopay_qris" value="on" {{ \App\Models\Setting::getVal('payment_gateway_gopay_qris', 'on') === 'on' ? 'checked' : '' }}>
                        <label class="form-check-label small fw-bold text-dark" for="payment_gateway_gopay_qris">Aktifkan Dips Gateway (Pembayaran Manual)</label>
                    </div>
                </div>
            </div>

            {{-- Card: Mail & SMTP Settings --}}
            <div class="card card-settings border-0 p-4 mb-4 bg-white">
                <h5 class="fw-bold text-dark mb-4 pb-2 border-bottom border-light">
                    <i class="bi bi-envelope-paper text-warning me-2"></i> SMTP & Penerima Notifikasi
                </h5>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary text-uppercase mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">Email Penerima Notifikasi</label>
                    <input type="email" name="admin_notification_email" class="form-control rounded-3 shadow-none border-light-subtle p-2.5 bg-light" style="font-size: 0.85rem;"
                        placeholder="monotp94@gmail.com" value="{{ \App\Models\Setting::getVal('admin_notification_email', 'monotp94@gmail.com') }}" required>
                    <small class="text-muted d-block mt-1" style="font-size: 0.73rem;">Semua notifikasi registrasi lunas dan token expired akan dikirim ke email ini.</small>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-md-8">
                        <label class="form-label small fw-bold text-secondary text-uppercase mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">SMTP Host</label>
                        <input type="text" name="mail_host" class="form-control rounded-3 shadow-none border-light-subtle p-2.5 bg-light" style="font-size: 0.85rem;"
                            placeholder="smtp.gmail.com" value="{{ \App\Models\Setting::getVal('mail_host') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold text-secondary text-uppercase mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">SMTP Port</label>
                        <input type="text" name="mail_port" class="form-control rounded-3 shadow-none border-light-subtle p-2.5 bg-light" style="font-size: 0.85rem;"
                            placeholder="587" value="{{ \App\Models\Setting::getVal('mail_port', '587') }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary text-uppercase mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">SMTP Username</label>
                    <input type="text" name="mail_username" class="form-control rounded-3 shadow-none border-light-subtle p-2.5 bg-light" style="font-size: 0.85rem;"
                        placeholder="yomudachampionship@gmail.com" value="{{ \App\Models\Setting::getVal('mail_username') }}">
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary text-uppercase mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">SMTP Password</label>
                    <input type="password" name="mail_password" class="form-control rounded-3 shadow-none border-light-subtle p-2.5 bg-light" style="font-size: 0.85rem;"
                        placeholder="Masukkan password SMTP baru (kosongkan jika tidak diubah)">
                </div>

                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary text-uppercase mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">SMTP Enkripsi</label>
                    <select name="mail_encryption" class="form-select rounded-3 shadow-none border-light-subtle" style="font-size: 0.85rem;">
                        <option value="tls" {{ \App\Models\Setting::getVal('mail_encryption', 'tls') === 'tls' ? 'selected' : '' }}>TLS</option>
                        <option value="ssl" {{ \App\Models\Setting::getVal('mail_encryption') === 'ssl' ? 'selected' : '' }}>SSL</option>
                        <option value="none" {{ \App\Models\Setting::getVal('mail_encryption') === 'none' ? 'selected' : '' }}>Tanpa Enkripsi</option>
                    </select>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-secondary text-uppercase mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">Pengirim Email (Address)</label>
                        <input type="email" name="mail_from_address" class="form-control rounded-3 shadow-none border-light-subtle p-2.5 bg-light" style="font-size: 0.85rem;"
                            placeholder="yomudachampionship@gmail.com" value="{{ \App\Models\Setting::getVal('mail_from_address', 'yomudachampionship@gmail.com') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold text-secondary text-uppercase mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">Nama Pengirim (Name)</label>
                        <input type="text" name="mail_from_name" class="form-control rounded-3 shadow-none border-light-subtle p-2.5 bg-light" style="font-size: 0.85rem;"
                            placeholder="Yomuda Championship" value="{{ \App\Models\Setting::getVal('mail_from_name', 'Yomuda Championship') }}">
                    </div>
                </div>
            </div>

            {{-- Card 5: Maintenance Mode --}}
            <div class="card card-settings border-0 p-4 mb-0 bg-white">
                <h5 class="fw-bold text-dark mb-4 pb-2 border-bottom border-light">
                    <i class="bi bi-tools text-danger me-2"></i> Mode Pemeliharaan (Maintenance Mode)
                </h5>

                <div class="mb-4">
                    <div class="form-check form-switch p-0 d-flex justify-content-between align-items-center bg-light-subtle p-3 rounded-3 border border-light-subtle" style="background-color: #f8fafc;">
                        <label class="form-check-label fw-bold text-dark mb-0 ps-1" for="maintenance_mode" style="font-size: 0.85rem;">
                            <i class="bi bi-power text-danger me-2"></i> Aktifkan Mode Maintenance
                        </label>
                        <input class="form-check-input ms-0 shadow-none" type="checkbox" role="switch" id="maintenance_mode" name="maintenance_mode" value="true" style="cursor: pointer;"
                            {{ app()->isDownForMaintenance() ? 'checked' : '' }}>
                    </div>
                    <small class="text-muted d-block mt-2" style="font-size: 0.75rem;">
                        Jika diaktifkan, pengunjung biasa akan diarahkan ke halaman maintenance. Anda tetap dapat mengakses web karena sistem akan otomatis memberikan cookie bypass.
                    </small>
                </div>

                <div class="mb-0">
                    <label class="form-label small fw-bold text-secondary text-uppercase mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">Bypass Token / Secret URL</label>
                    <div class="input-group rounded-3 overflow-hidden border border-light-subtle shadow-none">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-shield-lock-fill text-secondary"></i></span>
                        <input type="text" name="maintenance_secret" class="form-control border-0 bg-light shadow-none p-2.5" style="font-size: 0.85rem;"
                            placeholder="yomudasecret" value="{{ \App\Models\Setting::getVal('maintenance_secret', 'yomudasecret') }}" required>
                    </div>
                    @if(app()->isDownForMaintenance())
                        <div class="alert alert-info border-0 rounded-3 mt-3 p-3 mb-0" style="font-size: 0.8rem; background-color: #f0f9ff; border: 1px solid #e0f2fe; color: #0369a1;">
                            <i class="bi bi-info-circle-fill me-1"></i> Link Bypass: <a href="{{ url('/' . \App\Models\Setting::getVal('maintenance_secret', 'yomudasecret')) }}" target="_blank" class="fw-bold" style="color: #0369a1; text-decoration: underline;">{{ url('/' . \App\Models\Setting::getVal('maintenance_secret', 'yomudasecret')) }}</a>
                        </div>
                    @endif
                </div>
            </div></div>
        </div>

        {{-- Tombol Submit Melayang --}}
        <div class="floating-save-bar">
            <button type="submit" class="btn btn-warning fw-bold px-4 py-3 rounded-pill shadow-lg text-dark transition-all d-flex align-items-center gap-2" style="letter-spacing: 0.5px; border: 2px solid rgba(255, 255, 255, 0.15);">
                <i class="bi bi-cloud-arrow-up-fill fs-5"></i> SIMPAN PENGATURAN
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
    .floating-save-bar {
        position: fixed;
        bottom: 30px;
        right: 30px;
        z-index: 9999;
    }
    .floating-save-bar button {
        box-shadow: 0 10px 30px rgba(255, 193, 7, 0.4) !important;
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
    }
    .floating-save-bar button:hover {
        transform: scale(1.05) translateY(-3px);
        box-shadow: 0 15px 35px rgba(255, 193, 7, 0.6) !important;
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
