@extends('layouts.app')

@section('title', 'Pendaftaran Turnamen Mobile Legends - Yomuda Championship')
@section('meta_description', 'Ikuti ' . $active_season->name . '. Prize Pool ' . ($active_season->prize_pool ?? 'Menarik') . '. Daftarkan timmu sekarang, slot terbatas!')
@section('meta_keywords', 'pendaftaran turnamen mlbb, turnamen mobile legends nasional, yomuda championship')
@section('og_title', 'Registrasi Turnamen ' . $active_season->name)
@section('og_image', asset('storage/posters/' . $active_season->poster))

@push('styles')
<style>
    .register-wrapper {
        width: 100%;
        max-width: 1200px;
        margin: 0 auto;
        color: #ffffff;
        padding: 0 16px;
    }

    .register-grid {
        display: flex;
        flex-direction: column-reverse;
        gap: 32px;
        align-items: center;
        justify-content: center;
        width: 100%;
    }

    .poster-section {
        width: 100%;
        max-width: 520px;
        overflow: hidden;
        background: #121417;
        border: 1px solid #2d3238;
        border-radius: 26px;
        box-shadow: 0 18px 50px rgba(0, 0, 0, 0.28);
    }

    .poster-img-wrapper {
        position: relative;
        width: 100%;
        aspect-ratio: 3 / 4;
        min-height: 520px;
        overflow: hidden;
        background: #050505;
    }

    .poster-img {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center top;
    }

    .poster-placeholder {
        width: 100%;
        height: 100%;
        min-height: 520px;
        display: flex;
        align-items: center;
        justify-content: center;
        background:
            radial-gradient(circle at top, rgba(255, 193, 7, 0.18), transparent 50%),
            linear-gradient(135deg, #111418, #050505);
        color: rgba(255, 255, 255, 0.4);
        font-size: 3rem;
    }

    .tournament-info {
        padding: 22px;
    }

    .tournament-title {
        font-weight: 800;
        color: #ffffff;
        margin-bottom: 6px;
        letter-spacing: -0.4px;
    }

    .sub-text {
        color: rgba(255, 255, 255, 0.55);
        font-size: 0.86rem;
        line-height: 1.7;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
        margin-top: 18px;
    }

    .info-item {
        background: rgba(255, 255, 255, 0.035);
        padding: 13px 14px;
        border-radius: 14px;
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-left: 3px solid #ffc107;
    }

    .info-item label {
        display: block;
        margin-bottom: 4px;
        font-size: 0.62rem;
        font-weight: 600;
        color: rgba(255, 255, 255, 0.45);
        text-transform: uppercase;
        letter-spacing: 0.8px;
    }

    .info-item span {
        display: block;
        font-weight: 600;
        color: #ffc107;
        font-size: 0.86rem;
        line-height: 1.35;
    }

    .form-shell {
        width: 100%;
        max-width: 460px;
        padding: 3px;
        border-radius: 26px;
        background: linear-gradient(45deg, #ffc107, #343a40, #ffc107);
        background-size: 400% 400%;
        animation: gradient-animation 5s ease infinite;
        box-shadow: 0 22px 60px rgba(0, 0, 0, 0.34);
    }

    @keyframes gradient-animation {
        0% {
            background-position: 0% 50%;
        }

        50% {
            background-position: 100% 50%;
        }

        100% {
            background-position: 0% 50%;
        }
    }

    .form-card {
        background:
            radial-gradient(circle at top, rgba(255, 193, 7, 0.08), transparent 42%),
            #121417;
        border-radius: 24px;
        padding: 40px 30px;
    }

    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        border-radius: 999px;
        padding: 8px 14px;
        font-size: 0.66rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.7px;
    }

    .status-pill.open {
        background: #ffc107;
        color: #000000;
    }

    .status-pill.closed {
        background: rgba(220, 53, 69, 0.14);
        color: #ff6b7a;
        border: 1px solid rgba(255, 107, 122, 0.25);
    }

    .form-heading {
        color: #ffffff;
        font-weight: 800;
        letter-spacing: -1px;
        margin-bottom: 0;
        line-height: 1.05;
        text-transform: uppercase;
    }

    .form-heading-sub {
        font-size: 0.88em;
        letter-spacing: 0.5px;
    }

    .limited-alert {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 9px;
        padding: 10px 14px;
        margin-bottom: 24px;
        border-radius: 14px;
        background: rgba(220, 53, 69, 0.12);
        border: 1px solid rgba(220, 53, 69, 0.32);
        color: #ff5d5d;
        font-size: 0.8rem;
        font-weight: 800;
        text-align: center;
    }

    .limited-dot {
        width: 9px;
        height: 9px;
        border-radius: 50%;
        background: #ff4d4d;
        box-shadow: 0 0 14px rgba(255, 77, 77, 0.75);
        flex: 0 0 auto;
    }

    .form-group-custom {
        margin-bottom: 22px;
    }

    .label-v2 {
        display: block;
        margin-bottom: 8px;
        font-size: 0.75rem;
        font-weight: 800;
        color: #ffc107;
        text-transform: uppercase;
        letter-spacing: 0.6px;
    }

    .form-control-v2 {
        width: 100%;
        background: #1b1f23;
        border: 2px solid #2d3238;
        border-radius: 14px;
        color: #ffffff;
        padding: 15px 18px;
        font-size: 0.95rem;
        outline: none;
        transition: 0.25s ease;
    }

    .form-control-v2::placeholder {
        color: rgba(255, 255, 255, 0.32);
    }

    .form-control-v2:focus {
        background: #1d2227;
        border-color: #ffc107;
        box-shadow: 0 0 0 4px rgba(255, 193, 7, 0.12);
    }

    .price-text {
        color: #ffc107;
        font-size: 0.86rem;
    }

    .btn-ultra {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 9px;
        background: #ffc107;
        color: #000000;
        border: none;
        border-radius: 14px;
        padding: 16px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: 0.25s ease;
    }

    .btn-ultra:hover:not(:disabled) {
        background: #ffffff;
        color: #000000;
        transform: translateY(-2px);
    }

    .btn-ultra:disabled {
        background: #6c757d !important;
        color: #ffffff;
        cursor: not-allowed;
        opacity: 0.7;
    }

    .spinner-registration {
        display: none;
    }

    .btn-ultra.loading .spinner-registration {
        display: inline-flex;
    }

    .closed-button {
        width: 100%;
        border-radius: 14px;
        padding: 16px;
        font-weight: 800;
        opacity: 0.55;
        cursor: not-allowed;
    }

    .divider-dashed {
        margin-top: 34px;
        padding-top: 18px;
        border-top: 1px dashed rgba(255, 255, 255, 0.18);
    }

    .empty-season-card {
        display: inline-block;
        background: #121417;
        border: 1px solid #2d3238;
        border-radius: 26px;
        padding: 45px 34px;
        box-shadow: 0 18px 50px rgba(0, 0, 0, 0.25);
    }

    @media (min-width: 768px) {
        .register-grid {
            flex-direction: row;
            align-items: flex-start;
        }
    }

    @media (max-width: 576px) {
        .register-wrapper {
            padding: 0 8px;
        }

        .register-grid {
            gap: 24px;
        }

        .poster-img-wrapper,
        .poster-placeholder {
            min-height: 390px;
        }

        .form-card {
            padding: 32px 22px;
        }

        .info-grid {
            grid-template-columns: 1fr;
        }

        .form-heading {
            font-size: 1.65rem;
        }

        /* Mencegah iOS Safari auto zoom-in pada input field */
        input[type="text"],
        input[type="tel"],
        input[type="number"],
        input[type="email"],
        select,
        textarea,
        .form-control-v2 {
            font-size: 16px !important;
        }
    }
</style>
@endpush

@section('content')
<div class="register-wrapper">
    @if($active_season)
        @php
            $remaining = $active_season->slot - $active_season->teams_count;
            $isAvailable = $active_season->is_open && $remaining > 0;

            $formattedPrizePool = trim($active_season->prize_pool ?? '') !== ''
                ? $active_season->prize_pool
                : 'TBA';
        @endphp

        <div class="register-grid">
            <div class="poster-section">
                <div class="poster-img-wrapper">
                    @if($active_season->poster)
                        <img
                            src="{{ asset('storage/posters/' . $active_season->poster) }}"
                            alt="Poster {{ $active_season->name }}"
                            class="poster-img"
                        >
                    @else
                        <div class="poster-placeholder">
                            <i class="bi bi-trophy"></i>
                        </div>
                    @endif
                </div>

                <div class="tournament-info">
                    <h4 class="tournament-title">
                        Yomuda Championship
                        <span class="text-warning">{{ $active_season->name }}</span>
                    </h4>

                    <p class="sub-text mb-0">
                        Platform pendaftaran turnamen Mobile Legends tingkat Nasional.
                    </p>

                    <div class="info-grid">
                        <div class="info-item">
                            <label>Prize Pool</label>
                            <span>{{ $formattedPrizePool }}</span>
                        </div>

                        <div class="info-item">
                            <label>Jadwal Main</label>
                            <span>{{ $active_season->date_info ?? 'Segera Diumumkan' }}</span>
                        </div>

                        <div class="info-item">
                            <label>Mode Turnamen</label>
                            <span>Custom Draft Pick</span>
                        </div>

                        <div class="info-item">
                            <label>Lokasi</label>
                            <span>Online</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-shell">
                <div class="form-card">
                    @if($active_season->is_open && $remaining <= 5 && $remaining > 0)
                        <div class="limited-alert">
                            <span class="limited-dot"></span>
                            <span>BURUAN! Slot hampir penuh.</span>
                        </div>
                    @endif

                    <div class="text-center mb-5">
                        @if($active_season->is_open)
                            <span class="status-pill open mb-3">
                                <i class="bi bi-lightning-charge-fill"></i>
                                Registration Open
                            </span>
                        @else
                            <span class="status-pill closed mb-3">
                                <i class="bi bi-x-circle-fill"></i>
                                Registration Closed
                            </span>
                        @endif

                        <h2 class="form-heading">
                            <span class="d-block">YOMUDA</span>
                            <span class="d-block text-warning form-heading-sub">CHAMPIONSHIP</span>
                        </h2>

                        <p class="sub-text mt-2 mb-0">
                            Daftarkan skuad terbaikmu dan jadilah juara!
                        </p>
                    </div>

                    @if($errors->any())
                        <div class="alert alert-danger border-0 shadow-sm rounded-3 py-3 mb-4" style="background: rgba(220, 53, 69, 0.12); border: 1px solid rgba(220, 53, 69, 0.32);">
                            <ul class="mb-0 small fw-bold text-danger ps-3">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form id="regForm" action="{{ route('register.store') }}" method="POST">
                        @csrf

                        <input type="hidden" name="season_id" value="{{ $active_season->id }}">

                         <div class="form-group-custom">
                            <label class="label-v2" for="teamName">Nama Team</label>
                            <input
                                type="text"
                                id="teamName"
                                name="name"
                                class="form-control-v2"
                                placeholder="Masukkan nama tim..."
                                value="{{ old('name') }}"
                                required
                            >
                            @error('name')
                                <span class="text-danger small fw-bold mt-1.5 d-block" style="font-size: 0.75rem;">
                                    <i class="bi bi-exclamation-circle-fill me-1"></i> {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="form-group-custom">
                            <label class="label-v2" for="waNumber">Nomor WA Perwakilan</label>
                            <input
                                type="tel"
                                id="waNumber"
                                name="wa_number"
                                class="form-control-v2"
                                placeholder="08xxxxxxxxxx"
                                value="{{ old('wa_number') }}"
                                required
                            >
                            @error('wa_number')
                                <span class="text-danger small fw-bold mt-1.5 d-block" style="font-size: 0.75rem;">
                                    <i class="bi bi-exclamation-circle-fill me-1"></i> {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <div class="text-center mb-3">
                            @if($isAvailable)
                                <p class="price-text mb-0">
                                    Biaya Pendaftaran:
                                    <strong>Rp {{ number_format($active_season->price, 0, ',', '.') }}/team</strong>
                                </p>

                                <button type="submit" id="submitBtn" class="btn-ultra mt-3">
                                    <span class="spinner-registration">
                                        <i class="bi bi-arrow-repeat"></i>
                                    </span>
                                    <span class="btn-text">Daftar Tournament</span>
                                </button>
                            @else
                                <p class="text-danger small fw-bold mb-3">
                                    {{ !$active_season->is_open ? 'PENDAFTARAN SUDAH DITUTUP' : 'SLOT PENDAFTARAN SUDAH PENUH' }}
                                </p>

                                <button type="button" class="btn btn-secondary closed-button" disabled>
                                    SLOT PENUH / TUTUP
                                </button>
                            @endif
                        </div>
                    </form>

                    <div class="divider-dashed"></div>

                    <p class="text-center sub-text mt-3 mb-0">
                        Sistem akan mengarahkanmu ke pembayaran otomatis.
                    </p>
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-5">
            <div class="empty-season-card">
                <i class="bi bi-trophy-fill text-warning mb-3" style="font-size: 3rem;"></i>

                <h3 class="fw-bold text-white">
                    Belum Ada Season Aktif
                </h3>

                <p class="sub-text">
                    Nantikan info turnamen selanjutnya di sosial media kami!
                </p>

                <a href="{{ route('home') }}" class="btn btn-warning mt-3 px-4 fw-bold">
                    Kembali ke Home
                </a>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('regForm');

        if (!form) {
            return;
        }

        form.addEventListener('submit', function (e) {
            if (form.getAttribute('data-submitting') === 'true') {
                e.preventDefault();
                return false;
            }
            form.setAttribute('data-submitting', 'true');

            const btn = document.getElementById('submitBtn');
            if (btn) {
                btn.disabled = true;
                btn.classList.add('loading');
                const btnText = btn.querySelector('.btn-text');
                if (btnText) {
                    btnText.innerText = 'Sedang Memproses...';
                }
            }
            return true;
        });
    });
</script>
@endpush