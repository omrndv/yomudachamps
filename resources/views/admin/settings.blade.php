@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">

<style>
    .settings-card {
        background: #ffffff;
        border: 1px solid rgba(0,0,0,0.06);
        border-radius: 16px;
        transition: all 0.2s ease;
        overflow: hidden;
    }
    .settings-card:hover {
        border-color: rgba(0,0,0,0.1);
        box-shadow: 0 8px 24px rgba(0,0,0,0.05);
    }
    .settings-card-header {
        padding: 20px 24px 16px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .settings-card-header .icon-box {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        flex-shrink: 0;
    }
    .settings-card-body {
        padding: 24px;
    }
    .form-field {
        margin-bottom: 20px;
    }
    .form-field:last-child {
        margin-bottom: 0;
    }
    .form-field label.field-label {
        display: block;
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        margin-bottom: 6px;
    }
    .form-field .field-input {
        display: flex;
        align-items: center;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        overflow: hidden;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }
    .form-field .field-input:focus-within {
        border-color: #f59e0b;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
    }
    .form-field .field-input .fi-icon {
        padding: 0 12px;
        color: #94a3b8;
        font-size: 1rem;
        flex-shrink: 0;
    }
    .form-field .field-input input,
    .form-field .field-input textarea,
    .form-field .field-input select {
        flex: 1;
        border: 0;
        background: transparent;
        padding: 10px 12px 10px 0;
        font-size: 0.85rem;
        color: #1e293b;
        outline: none;
        box-shadow: none;
    }
    .form-field .field-input textarea {
        padding: 12px 12px 12px 0;
        resize: vertical;
    }
    .form-field .field-hint {
        font-size: 0.73rem;
        color: #94a3b8;
        margin-top: 6px;
        line-height: 1.45;
    }
    .form-field .field-hint code {
        background: #f1f5f9;
        color: #475569;
        padding: 1px 5px;
        border-radius: 4px;
        font-size: 0.72rem;
    }

    /* Tab Navigation */
    .settings-tabs {
        display: flex;
        gap: 4px;
        background: #f1f5f9;
        border-radius: 14px;
        padding: 4px;
        overflow-x: auto;
        scrollbar-width: none;
    }
    .settings-tabs::-webkit-scrollbar { display: none; }
    .settings-tab {
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
    .settings-tab:hover {
        color: #334155;
        background: rgba(255,255,255,0.6);
    }
    .settings-tab.active {
        background: #ffffff;
        color: #0f172a;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    }
    .settings-tab i { font-size: 1rem; }
    .tab-pane { display: none; }
    .tab-pane.active { display: block; }

    /* Switch styling */
    .switch-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 14px 16px;
    }
    .switch-row label {
        font-size: 0.85rem;
        font-weight: 600;
        color: #1e293b;
    }
    .form-check-input:checked {
        background-color: #f59e0b !important;
        border-color: #f59e0b !important;
    }

    /* Floating Save */
    .floating-save-bar {
        position: fixed;
        bottom: 24px;
        right: 24px;
        z-index: 9999;
    }
    .floating-save-bar button {
        box-shadow: 0 8px 24px rgba(245, 158, 11, 0.4) !important;
        transition: all 0.2s ease;
    }
    .floating-save-bar button:hover {
        transform: translateY(-3px) scale(1.03);
        box-shadow: 0 12px 32px rgba(245, 158, 11, 0.5) !important;
    }

    /* Preview box */
    .asset-preview {
        background: #0f172a;
        border-radius: 10px;
        padding: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    @media (max-width: 767.98px) {
        .settings-card-body { padding: 16px; }
        .settings-card-header { padding: 16px 16px 12px; }
        .settings-tabs { padding: 3px; gap: 2px; }
        .settings-tab { padding: 8px 12px; font-size: 0.78rem; }
    }
</style>

    {{-- Page Header --}}
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3 mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1" style="font-size: 1.6rem; letter-spacing: -0.5px;">
                Pengaturan Sistem
            </h2>
            <p class="text-secondary mb-0" style="font-size: 0.85rem;">
                Konfigurasi token, kredensial gateway, media sosial, dan aset visual turnamen.
            </p>
        </div>
        <a href="/qris-gateway/dashboard" class="btn btn-dark fw-semibold px-4 py-2 rounded-pill flex-shrink-0 d-inline-flex align-items-center gap-2" style="font-size: 0.82rem;">
            <i class="bi bi-wallet2"></i> Kelola Dips Gateway
        </a>
    </div>

    {{-- Alert Success --}}
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center py-3">
            <i class="bi bi-check-circle-fill me-2 fs-5 text-success"></i>
            <div class="fw-semibold">{{ session('success') }}</div>
        </div>
    @endif

    {{-- Tab Navigation --}}
    <div class="settings-tabs mb-4">
        <button type="button" class="settings-tab active" data-tab="tab-whatsapp">
            <i class="bi bi-whatsapp text-success"></i> WhatsApp
        </button>
        <button type="button" class="settings-tab" data-tab="tab-gateway">
            <i class="bi bi-credit-card-2-front"></i> Payment Gateway
        </button>
        <button type="button" class="settings-tab" data-tab="tab-smtp">
            <i class="bi bi-envelope-paper"></i> Email & SMTP
        </button>
        <button type="button" class="settings-tab" data-tab="tab-social">
            <i class="bi bi-share"></i> Sosial & Aset
        </button>
        <button type="button" class="settings-tab" data-tab="tab-system">
            <i class="bi bi-sliders"></i> Sistem
        </button>
    </div>

    {{-- Form --}}
    <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- ============================================ --}}
        {{-- TAB 1: WhatsApp & Support --}}
        {{-- ============================================ --}}
        <div class="tab-pane active" id="tab-whatsapp">
            <div class="row g-4">
                <div class="col-lg-6">
                    {{-- WhatsApp Config --}}
                    <div class="settings-card mb-4">
                        <div class="settings-card-header">
                            <div class="icon-box" style="background: #dcfce7; color: #16a34a;"><i class="bi bi-whatsapp"></i></div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0" style="font-size: 0.95rem;">WhatsApp & Support</h6>
                                <span class="text-secondary" style="font-size: 0.75rem;">Konfigurasi Fonnte API dan kontak support</span>
                            </div>
                        </div>
                        <div class="settings-card-body">
                            <div class="switch-row mb-3">
                                <label for="wa_notification_enabled">
                                    <i class="bi bi-bell-fill text-warning me-2"></i> Notifikasi WA Otomatis
                                </label>
                                <div class="form-check form-switch m-0 p-0">
                                    <input class="form-check-input shadow-none ms-0" type="checkbox" role="switch" id="wa_notification_enabled" name="wa_notification_enabled" value="true" style="cursor:pointer; width: 2.5em; height: 1.25em;"
                                        {{ \App\Models\Setting::getVal('wa_notification_enabled', env('WA_NOTIFICATION_ENABLED', false)) == 'true' || \App\Models\Setting::getVal('wa_notification_enabled', env('WA_NOTIFICATION_ENABLED', false)) === true ? 'checked' : '' }}>
                                </div>
                            </div>
                            <p class="field-hint mt-0 mb-3">Kirim pesan WhatsApp otomatis saat tim berstatus <b>PAID</b> (Lunas).</p>

                            <div class="form-field">
                                <label class="field-label">Fonnte API Token</label>
                                <div class="field-input">
                                    <i class="bi bi-key-fill fi-icon"></i>
                                    <input type="text" name="fonnte_token" placeholder="Token dari Fonnte Device API..."
                                        value="{{ \App\Models\Setting::getVal('fonnte_token', env('FONNTE_TOKEN')) }}">
                                </div>
                                <p class="field-hint">Dapatkan di dashboard Fonnte pada menu Device/API Token.</p>
                            </div>

                            <div class="form-field">
                                <label class="field-label">Nomor WA Support Admin</label>
                                <div class="field-input">
                                    <i class="bi bi-telephone-fill fi-icon"></i>
                                    <input type="text" name="admin_wa" placeholder="0851-2261-6191"
                                        value="{{ \App\Models\Setting::getVal('admin_wa', '0851-2261-6191') }}" required>
                                </div>
                                <p class="field-hint">Ditampilkan di template WA otomatis dan halaman kontak.</p>
                            </div>

                            <div class="form-field">
                                <label class="field-label">Email Support Turnamen</label>
                                <div class="field-input">
                                    <i class="bi bi-envelope-fill fi-icon"></i>
                                    <input type="email" name="admin_email" placeholder="support@yomuda.com"
                                        value="{{ \App\Models\Setting::getVal('admin_email', 'yomudachampionship@gmail.com') }}" required>
                                </div>
                                <p class="field-hint">Email resmi turnamen yang ditampilkan di halaman bantuan.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    {{-- WA Template --}}
                    <div class="settings-card mb-4">
                        <div class="settings-card-header">
                            <div class="icon-box" style="background: #fef3c7; color: #d97706;"><i class="bi bi-chat-left-text"></i></div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0" style="font-size: 0.95rem;">Template WhatsApp</h6>
                                <span class="text-secondary" style="font-size: 0.75rem;">Pesan otomatis saat status PAID</span>
                            </div>
                        </div>
                        <div class="settings-card-body">
                            <div class="form-field">
                                <label class="field-label">Template Pesan (Status PAID)</label>
                                <div class="field-input" style="align-items: flex-start;">
                                    <i class="bi bi-pencil-square fi-icon" style="padding-top: 12px;"></i>
                                    <textarea name="wa_template_paid" rows="10" style="min-height: 200px;">{{ \App\Models\Setting::getVal('wa_template_paid', "Halo *{nama_tim}*! 🎮\n\nPembayaran pendaftaran turnamen *Yomuda Championship {nama_season}* telah *BERHASIL DIVERIFIKASI* (Lunas) dengan ID Transaksi: {id_transaksi}.\n\n{grup_info}Terima kasih telah bergabung, siapkan squad terbaikmu! 🔥\n\nKalau mau tanya-tanya bisa hubungi admin ke {nomor_admin} yaa.\n\n-- Yomuda Championship --") }}</textarea>
                                </div>
                                <div class="field-hint mt-2">
                                    <strong>Placeholder dinamis:</strong><br>
                                    <code>{nama_tim}</code> Nama tim &nbsp; <code>{nama_season}</code> Nama season &nbsp; <code>{id_transaksi}</code> ID Transaksi<br>
                                    <code>{link_grup}</code> Link grup WA &nbsp; <code>{grup_info}</code> Info grup WA &nbsp; <code>{nomor_admin}</code> No. admin<br>
                                    <code>{harga}</code> Harga tiket
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================ --}}
        {{-- TAB 2: Payment Gateway --}}
        {{-- ============================================ --}}
        <div class="tab-pane" id="tab-gateway">
            <div class="row g-4">
                <div class="col-lg-6">
                    {{-- TriPay --}}
                    <div class="settings-card mb-4">
                        <div class="settings-card-header">
                            <div class="icon-box" style="background: #dbeafe; color: #2563eb;"><i class="bi bi-credit-card-2-front"></i></div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0" style="font-size: 0.95rem;">TriPay Gateway</h6>
                                <span class="text-secondary" style="font-size: 0.75rem;">Kredensial & konfigurasi TriPay</span>
                            </div>
                        </div>
                        <div class="settings-card-body">
                            <div class="form-field">
                                <label class="field-label">Mode Gateway</label>
                                <div class="field-input">
                                    <i class="bi bi-toggles fi-icon"></i>
                                    <select name="tripay_mode" style="padding: 10px 12px 10px 0;">
                                        <option value="sandbox" {{ \App\Models\Setting::getVal('tripay_mode', env('TRIPAY_MODE', 'sandbox')) === 'sandbox' ? 'selected' : '' }}>Sandbox (Testing)</option>
                                        <option value="production" {{ \App\Models\Setting::getVal('tripay_mode', env('TRIPAY_MODE', 'sandbox')) === 'production' ? 'selected' : '' }}>Production (Live)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-field">
                                <label class="field-label">Merchant Code</label>
                                <div class="field-input">
                                    <i class="bi bi-hash fi-icon"></i>
                                    <input type="text" name="tripay_merchant_code" placeholder="T..."
                                        value="{{ \App\Models\Setting::getVal('tripay_merchant_code', env('TRIPAY_MERCHANT_CODE')) }}" required>
                                </div>
                            </div>
                            <div class="form-field">
                                <label class="field-label">API Key</label>
                                <div class="field-input">
                                    <i class="bi bi-key-fill fi-icon"></i>
                                    <input type="text" name="tripay_api_key" placeholder="DEV-..."
                                        value="{{ \App\Models\Setting::getVal('tripay_api_key', env('TRIPAY_API_KEY')) }}" required>
                                </div>
                            </div>
                            <div class="form-field">
                                <label class="field-label">Private Key</label>
                                <div class="field-input">
                                    <i class="bi bi-shield-lock-fill fi-icon"></i>
                                    <input type="text" name="tripay_private_key" placeholder="Kunci privat..."
                                        value="{{ \App\Models\Setting::getVal('tripay_private_key', env('TRIPAY_PRIVATE_KEY')) }}" required>
                                </div>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-8">
                                    <div class="form-field mb-0">
                                        <label class="field-label">Nama QRIS di Checkout</label>
                                        <div class="field-input">
                                            <i class="bi bi-tag fi-icon"></i>
                                            <input type="text" name="tripay_qris_name" placeholder="QRIS"
                                                value="{{ \App\Models\Setting::getVal('tripay_qris_name', 'QRIS') }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-field mb-0">
                                        <label class="field-label">Urutan</label>
                                        <div class="field-input">
                                            <input type="number" name="tripay_sort_order" min="1" placeholder="1" style="padding-left: 12px;"
                                                value="{{ \App\Models\Setting::getVal('tripay_sort_order', '1') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="switch-row">
                                <label for="payment_gateway_tripay"><i class="bi bi-power text-success me-2"></i> Aktifkan TriPay</label>
                                <div class="form-check form-switch m-0 p-0">
                                    <input class="form-check-input shadow-none ms-0" type="checkbox" name="payment_gateway_tripay" id="payment_gateway_tripay" value="on" style="cursor:pointer; width: 2.5em; height: 1.25em;"
                                        {{ \App\Models\Setting::getVal('payment_gateway_tripay', 'on') === 'on' ? 'checked' : '' }}>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Dips Gateway --}}
                    <div class="settings-card">
                        <div class="settings-card-header">
                            <div class="icon-box" style="background: #fef3c7; color: #d97706;"><i class="bi bi-wallet2"></i></div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0" style="font-size: 0.95rem;">Dips Gateway (Manual)</h6>
                                <span class="text-secondary" style="font-size: 0.75rem;">Pembayaran QRIS manual</span>
                            </div>
                        </div>
                        <div class="settings-card-body">
                            <div class="row g-3 mb-3">
                                <div class="col-8">
                                    <div class="form-field mb-0">
                                        <label class="field-label">Nama di Checkout</label>
                                        <div class="field-input">
                                            <i class="bi bi-tag fi-icon"></i>
                                            <input type="text" name="manual_payment_name" placeholder="QRIS (All Payment)"
                                                value="{{ \App\Models\Setting::getVal('manual_payment_name', 'QRIS (All Payment)') }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-field mb-0">
                                        <label class="field-label">Urutan</label>
                                        <div class="field-input">
                                            <input type="number" name="manual_payment_sort_order" min="1" placeholder="3" style="padding-left: 12px;"
                                                value="{{ \App\Models\Setting::getVal('manual_payment_sort_order', '3') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="switch-row">
                                <label for="payment_gateway_gopay_qris"><i class="bi bi-power text-success me-2"></i> Aktifkan Dips Gateway</label>
                                <div class="form-check form-switch m-0 p-0">
                                    <input class="form-check-input shadow-none ms-0" type="checkbox" name="payment_gateway_gopay_qris" id="payment_gateway_gopay_qris" value="on" style="cursor:pointer; width: 2.5em; height: 1.25em;"
                                        {{ \App\Models\Setting::getVal('payment_gateway_gopay_qris', 'on') === 'on' ? 'checked' : '' }}>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    {{-- iPaymu --}}
                    <div class="settings-card">
                        <div class="settings-card-header">
                            <div class="icon-box" style="background: #e0e7ff; color: #4f46e5;"><i class="bi bi-credit-card"></i></div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0" style="font-size: 0.95rem;">iPaymu Gateway</h6>
                                <span class="text-secondary" style="font-size: 0.75rem;">Kredensial & konfigurasi iPaymu</span>
                            </div>
                        </div>
                        <div class="settings-card-body">
                            <div class="form-field">
                                <label class="field-label">Mode Gateway</label>
                                <div class="field-input">
                                    <i class="bi bi-toggles fi-icon"></i>
                                    <select name="ipaymu_mode" style="padding: 10px 12px 10px 0;">
                                        <option value="sandbox" {{ \App\Models\Setting::getVal('ipaymu_mode', 'sandbox') === 'sandbox' ? 'selected' : '' }}>Sandbox (Testing)</option>
                                        <option value="production" {{ \App\Models\Setting::getVal('ipaymu_mode', 'sandbox') === 'production' ? 'selected' : '' }}>Production (Live)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-field">
                                <label class="field-label">Virtual Account / VA</label>
                                <div class="field-input">
                                    <i class="bi bi-hash fi-icon"></i>
                                    <input type="text" name="ipaymu_va" placeholder="Nomor VA iPaymu..."
                                        value="{{ \App\Models\Setting::getVal('ipaymu_va', env('IPAYMU_VA')) }}">
                                </div>
                            </div>
                            <div class="form-field">
                                <label class="field-label">API Key / Secret Key</label>
                                <div class="field-input">
                                    <i class="bi bi-key-fill fi-icon"></i>
                                    <input type="text" name="ipaymu_api_key" placeholder="Secret Key..."
                                        value="{{ \App\Models\Setting::getVal('ipaymu_api_key', env('IPAYMU_API_KEY')) }}">
                                </div>
                            </div>
                            <div class="row g-3 mb-3">
                                <div class="col-8">
                                    <div class="form-field mb-0">
                                        <label class="field-label">Nama QRIS di Checkout</label>
                                        <div class="field-input">
                                            <i class="bi bi-tag fi-icon"></i>
                                            <input type="text" name="ipaymu_qris_name" placeholder="QRIS (iPaymu)"
                                                value="{{ \App\Models\Setting::getVal('ipaymu_qris_name', 'QRIS (iPaymu)') }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-field mb-0">
                                        <label class="field-label">Urutan</label>
                                        <div class="field-input">
                                            <input type="number" name="ipaymu_sort_order" min="1" placeholder="2" style="padding-left: 12px;"
                                                value="{{ \App\Models\Setting::getVal('ipaymu_sort_order', '2') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="switch-row">
                                <label for="payment_gateway_ipaymu"><i class="bi bi-power text-success me-2"></i> Aktifkan iPaymu</label>
                                <div class="form-check form-switch m-0 p-0">
                                    <input class="form-check-input shadow-none ms-0" type="checkbox" name="payment_gateway_ipaymu" id="payment_gateway_ipaymu" value="on" style="cursor:pointer; width: 2.5em; height: 1.25em;"
                                        {{ \App\Models\Setting::getVal('payment_gateway_ipaymu', 'off') === 'on' ? 'checked' : '' }}>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================ --}}
        {{-- TAB 3: Email & SMTP --}}
        {{-- ============================================ --}}
        <div class="tab-pane" id="tab-smtp">
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="settings-card">
                        <div class="settings-card-header">
                            <div class="icon-box" style="background: #fce7f3; color: #db2777;"><i class="bi bi-envelope-paper"></i></div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0" style="font-size: 0.95rem;">SMTP & Penerima Notifikasi</h6>
                                <span class="text-secondary" style="font-size: 0.75rem;">Konfigurasi email transaksional</span>
                            </div>
                        </div>
                        <div class="settings-card-body">
                            <div class="form-field">
                                <label class="field-label">Email Penerima Notifikasi</label>
                                <div class="field-input">
                                    <i class="bi bi-envelope-at-fill fi-icon"></i>
                                    <input type="email" name="admin_notification_email" placeholder="monotp94@gmail.com"
                                        value="{{ \App\Models\Setting::getVal('admin_notification_email', 'monotp94@gmail.com') }}" required>
                                </div>
                                <p class="field-hint">Semua notifikasi registrasi lunas dan token expired akan dikirim ke email ini.</p>
                            </div>
                            <div class="row g-3">
                                <div class="col-8">
                                    <div class="form-field">
                                        <label class="field-label">SMTP Host</label>
                                        <div class="field-input">
                                            <i class="bi bi-server fi-icon"></i>
                                            <input type="text" name="mail_host" placeholder="smtp.gmail.com"
                                                value="{{ \App\Models\Setting::getVal('mail_host') }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-field">
                                        <label class="field-label">Port</label>
                                        <div class="field-input">
                                            <input type="text" name="mail_port" placeholder="587" style="padding-left: 12px;"
                                                value="{{ \App\Models\Setting::getVal('mail_port', '587') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-field">
                                <label class="field-label">SMTP Username</label>
                                <div class="field-input">
                                    <i class="bi bi-person fi-icon"></i>
                                    <input type="text" name="mail_username" placeholder="yomudachampionship@gmail.com"
                                        value="{{ \App\Models\Setting::getVal('mail_username') }}">
                                </div>
                            </div>
                            <div class="form-field">
                                <label class="field-label">SMTP Password</label>
                                <div class="field-input">
                                    <i class="bi bi-lock-fill fi-icon"></i>
                                    <input type="password" name="mail_password" placeholder="Kosongkan jika tidak diubah">
                                </div>
                            </div>
                            <div class="form-field">
                                <label class="field-label">Enkripsi</label>
                                <div class="field-input">
                                    <i class="bi bi-shield-check fi-icon"></i>
                                    <select name="mail_encryption" style="padding: 10px 12px 10px 0;">
                                        <option value="tls" {{ \App\Models\Setting::getVal('mail_encryption', 'tls') === 'tls' ? 'selected' : '' }}>TLS</option>
                                        <option value="ssl" {{ \App\Models\Setting::getVal('mail_encryption') === 'ssl' ? 'selected' : '' }}>SSL</option>
                                        <option value="none" {{ \App\Models\Setting::getVal('mail_encryption') === 'none' ? 'selected' : '' }}>Tanpa Enkripsi</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-field mb-0">
                                        <label class="field-label">Pengirim (Address)</label>
                                        <div class="field-input">
                                            <i class="bi bi-at fi-icon"></i>
                                            <input type="email" name="mail_from_address" placeholder="yomudachampionship@gmail.com"
                                                value="{{ \App\Models\Setting::getVal('mail_from_address', 'yomudachampionship@gmail.com') }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-field mb-0">
                                        <label class="field-label">Nama Pengirim</label>
                                        <div class="field-input">
                                            <i class="bi bi-person-badge fi-icon"></i>
                                            <input type="text" name="mail_from_name" placeholder="Yomuda Championship"
                                                value="{{ \App\Models\Setting::getVal('mail_from_name', 'Yomuda Championship') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    {{-- Gemini AI --}}
                    <div class="settings-card">
                        <div class="settings-card-header">
                            <div class="icon-box" style="background: #d1fae5; color: #059669;"><i class="bi bi-stars"></i></div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0" style="font-size: 0.95rem;">Google Gemini AI</h6>
                                <span class="text-secondary" style="font-size: 0.75rem;">API Key untuk fitur AI Recap & Validasi</span>
                            </div>
                        </div>
                        <div class="settings-card-body">
                            <div class="form-field mb-0">
                                <label class="field-label">Gemini API Key</label>
                                <div class="field-input">
                                    <i class="bi bi-key-fill fi-icon" style="color: #059669;"></i>
                                    <input type="text" name="gemini_api_key" placeholder="AIzaSy..."
                                        value="{{ \App\Models\Setting::getVal('gemini_api_key', env('GEMINI_API_KEY')) }}">
                                </div>
                                <p class="field-hint">Untuk fitur AI Rangkuman Juara & Validasi Laporan. Dapatkan di <a href="https://aistudio.google.com/" target="_blank" class="text-success fw-bold text-decoration-none">Google AI Studio</a>.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================ --}}
        {{-- TAB 4: Sosial Media & Aset --}}
        {{-- ============================================ --}}
        <div class="tab-pane" id="tab-social">
            <div class="row g-4">
                <div class="col-lg-6">
                    {{-- Social Media Links --}}
                    <div class="settings-card mb-4">
                        <div class="settings-card-header">
                            <div class="icon-box" style="background: #e0e7ff; color: #6366f1;"><i class="bi bi-share"></i></div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0" style="font-size: 0.95rem;">Tautan Media Sosial</h6>
                                <span class="text-secondary" style="font-size: 0.75rem;">Link sosmed yang ditampilkan di website</span>
                            </div>
                        </div>
                        <div class="settings-card-body">
                            <div class="form-field">
                                <label class="field-label">Instagram</label>
                                <div class="field-input">
                                    <i class="bi bi-instagram fi-icon"></i>
                                    <input type="url" name="social_instagram" placeholder="https://www.instagram.com/..."
                                        value="{{ \App\Models\Setting::getVal('social_instagram', 'https://www.instagram.com/yomuda.championship/') }}" required>
                                </div>
                            </div>
                            <div class="form-field">
                                <label class="field-label">TikTok</label>
                                <div class="field-input">
                                    <i class="bi bi-tiktok fi-icon"></i>
                                    <input type="url" name="social_tiktok" placeholder="https://www.tiktok.com/..."
                                        value="{{ \App\Models\Setting::getVal('social_tiktok', 'https://www.tiktok.com/@yomudachampionship') }}" required>
                                </div>
                            </div>
                            <div class="form-field">
                                <label class="field-label">YouTube</label>
                                <div class="field-input">
                                    <i class="bi bi-youtube fi-icon"></i>
                                    <input type="url" name="social_youtube" placeholder="https://www.youtube.com/..."
                                        value="{{ \App\Models\Setting::getVal('social_youtube', 'https://www.youtube.com/@ymdchamps/streams') }}" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Tournament Rules --}}
                    <div class="settings-card">
                        <div class="settings-card-header">
                            <div class="icon-box" style="background: #ffedd5; color: #ea580c;"><i class="bi bi-file-earmark-ruled"></i></div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0" style="font-size: 0.95rem;">Rules Turnamen</h6>
                                <span class="text-secondary" style="font-size: 0.75rem;">Upload PDF atau link Google Drive</span>
                            </div>
                        </div>
                        <div class="settings-card-body">
                            <div class="form-field">
                                <label class="field-label">Link Google Drive (Alternatif)</label>
                                <div class="field-input">
                                    <i class="bi bi-link-45deg fi-icon"></i>
                                    <input type="url" name="global_rules_link" placeholder="https://drive.google.com/..."
                                        value="{{ \App\Models\Setting::getVal('global_rules_link') }}">
                                </div>
                            </div>
                            <div class="form-field">
                                <label class="field-label">Upload File PDF Rules (Utama)</label>
                                <input type="file" name="rules_file" class="form-control border-light-subtle rounded-3 shadow-none" accept="application/pdf" style="font-size: 0.85rem;">
                                <p class="field-hint">Jika di-upload, PDF ini akan menggantikan link Google Drive di atas.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    {{-- Website Assets --}}
                    <div class="settings-card">
                        <div class="settings-card-header">
                            <div class="icon-box" style="background: #fef3c7; color: #d97706;"><i class="bi bi-image"></i></div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0" style="font-size: 0.95rem;">Website Assets</h6>
                                <span class="text-secondary" style="font-size: 0.75rem;">Logo dan favicon turnamen</span>
                            </div>
                        </div>
                        <div class="settings-card-body">
                            <div class="form-field">
                                <label class="field-label">Logo Yomuda (PNG)</label>
                                <input type="file" name="logo" class="form-control border-light-subtle rounded-3 shadow-none" accept="image/png" onchange="previewAsset(this, 'logoPreview')" style="font-size: 0.85rem;">
                                <p class="field-hint">PNG transparan untuk logo utama di header & footer.</p>
                                <div class="mt-3 text-center">
                                    <div class="asset-preview d-inline-block">
                                        <img id="logoPreview" src="{{ asset('images/logo-yomuda.png') }}" style="max-height: 56px;">
                                    </div>
                                </div>
                            </div>
                            <div class="form-field">
                                <label class="field-label">Favicon Website (ICO/PNG)</label>
                                <input type="file" name="favicon" class="form-control border-light-subtle rounded-3 shadow-none" accept="image/x-icon, image/png" onchange="previewAsset(this, 'faviconPreview')" style="font-size: 0.85rem;">
                                <p class="field-hint">Ikon kecil yang muncul di tab browser.</p>
                                <div class="mt-3 text-center">
                                    <div class="asset-preview d-inline-block">
                                        <img id="faviconPreview" src="{{ asset('favicon.ico') }}" style="max-height: 32px; max-width: 32px;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================================ --}}
        {{-- TAB 5: Sistem --}}
        {{-- ============================================ --}}
        <div class="tab-pane" id="tab-system">
            <div class="row g-4">
                <div class="col-lg-6">
                    {{-- Log Retention --}}
                    <div class="settings-card mb-4">
                        <div class="settings-card-header">
                            <div class="icon-box" style="background: #e2e8f0; color: #475569;"><i class="bi bi-clock-history"></i></div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0" style="font-size: 0.95rem;">Pembersihan Log Otomatis</h6>
                                <span class="text-secondary" style="font-size: 0.75rem;">Log rotation & retensi data</span>
                            </div>
                        </div>
                        <div class="settings-card-body">
                            <div class="form-field mb-0">
                                <label class="field-label">Batas Penyimpanan Log (Hari)</label>
                                <div class="field-input">
                                    <i class="bi bi-calendar-event-fill fi-icon"></i>
                                    <input type="number" name="log_retention_days" placeholder="15" min="1"
                                        value="{{ \App\Models\Setting::getVal('log_retention_days', '15') }}" required>
                                </div>
                                <p class="field-hint">Log aktivitas admin yang melewati batas ini akan otomatis dibersihkan untuk menghemat kapasitas database.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-6">
                    {{-- Maintenance Mode --}}
                    <div class="settings-card">
                        <div class="settings-card-header">
                            <div class="icon-box" style="background: #fee2e2; color: #dc2626;"><i class="bi bi-tools"></i></div>
                            <div>
                                <h6 class="fw-bold text-dark mb-0" style="font-size: 0.95rem;">Mode Pemeliharaan</h6>
                                <span class="text-secondary" style="font-size: 0.75rem;">Maintenance mode & bypass token</span>
                            </div>
                        </div>
                        <div class="settings-card-body">
                            <div class="switch-row mb-3">
                                <label for="maintenance_mode">
                                    <i class="bi bi-power text-danger me-2"></i> Aktifkan Maintenance
                                </label>
                                <div class="form-check form-switch m-0 p-0">
                                    <input class="form-check-input shadow-none ms-0" type="checkbox" role="switch" id="maintenance_mode" name="maintenance_mode" value="true" style="cursor:pointer; width: 2.5em; height: 1.25em;"
                                        {{ app()->isDownForMaintenance() ? 'checked' : '' }}>
                                </div>
                            </div>
                            <p class="field-hint mt-0 mb-3">Pengunjung biasa akan diarahkan ke halaman maintenance. Anda tetap dapat mengakses web via cookie bypass.</p>

                            <div class="form-field mb-0">
                                <label class="field-label">Bypass Token / Secret URL</label>
                                <div class="field-input">
                                    <i class="bi bi-shield-lock-fill fi-icon"></i>
                                    <input type="text" name="maintenance_secret" placeholder="yomudasecret"
                                        value="{{ \App\Models\Setting::getVal('maintenance_secret', 'yomudasecret') }}" required>
                                </div>
                                @if(app()->isDownForMaintenance())
                                    <div class="mt-3 p-3 rounded-3" style="background: #f0f9ff; border: 1px solid #bae6fd;">
                                        <i class="bi bi-info-circle-fill text-primary me-1" style="font-size: 0.85rem;"></i>
                                        <span style="font-size: 0.8rem; color: #0369a1;">
                                            Bypass: <a href="{{ url('/' . \App\Models\Setting::getVal('maintenance_secret', 'yomudasecret')) }}" target="_blank" class="fw-bold" style="color: #0369a1;">{{ url('/' . \App\Models\Setting::getVal('maintenance_secret', 'yomudasecret')) }}</a>
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Floating Save Button --}}
        <div class="floating-save-bar">
            <button type="submit" class="btn btn-warning fw-bold px-4 py-3 rounded-pill text-dark d-flex align-items-center gap-2" style="font-size: 0.88rem;">
                <i class="bi bi-cloud-arrow-up-fill fs-5"></i> SIMPAN PENGATURAN
            </button>
        </div>
    </form>
</div>

<script>
function previewAsset(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function (e) { preview.src = e.target.result; }
        reader.readAsDataURL(input.files[0]);
    }
}

// Tab Switching
document.querySelectorAll('.settings-tab').forEach(tab => {
    tab.addEventListener('click', function() {
        document.querySelectorAll('.settings-tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
        this.classList.add('active');
        document.getElementById(this.dataset.tab).classList.add('active');
    });
});
</script>
@endsection
