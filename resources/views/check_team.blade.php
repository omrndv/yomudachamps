@extends('layouts.app')

@section('title', 'Cek Status Tim - Yomuda Championship')

@section('content')
<div class="container d-flex justify-content-center align-items-center" style="min-height: 85vh; padding: 20px 0;">
    <div class="card border-0 shadow-lg text-white card-checker"
        style="background: rgba(18, 20, 23, 0.85); border-radius: 28px; max-width: 480px; width: 100%; backdrop-filter: blur(15px); border: 1px solid rgba(255, 193, 7, 0.2) !important; overflow: hidden;">

        <div style="height: 4px; width: 100%; background: linear-gradient(90deg, #ffc107, #ff9800);"></div>

        <div class="card-body p-4 p-md-5">
            <div class="text-center mb-4">
                <div class="mb-3 mx-auto" style="width: 20%;">
                    <img src="/images/logo-yomuda.png" alt="Logo Yomuda Championship" class="img-fluid">
                </div>
                <h4 class="fw-bold text-warning mb-1" style="letter-spacing: 2px;">CARI TEAM KAMU</h4>
                <p class="text-white-50 small">
                    Gunakan nomor WhatsApp perwakilan tim untuk memantau status pendaftaran.
                </p>
            </div>

            @if(session('error'))
            <div class="alert alert-danger border-0 small py-3 mb-4 d-flex align-items-center shadow-sm"
                style="background: rgba(220, 53, 69, 0.1); border-radius: 12px; color: #ea868f;">
                <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
                <div>{{ session('error') }}</div>
            </div>
            @endif

            <form action="{{ route('check.team.search') }}" method="POST" class="mb-2">
                @csrf
                <div class="mb-4 text-start">
                    <label class="small fw-bold text-warning text-uppercase mb-2 d-block"
                        style="font-size: 0.75rem; letter-spacing: 1.5px;">
                        Nomor WhatsApp Perwakilan
                    </label>
                    <div class="input-group-custom">
                        <i class="bi bi-whatsapp icon-input"></i>
                        <input
                            type="tel"
                            name="wa_number"
                            class="form-control-custom"
                            placeholder="08xxxxxxxxxx"
                            autocomplete="off"
                            required>
                    </div>
                </div>

                <button type="submit" class="btn btn-warning w-100 fw-bold py-3 shadow-warning btn-search">
                    <i class="bi bi-search me-2"></i> TEMUKAN TIM SAYA
                </button>
            </form>

            @if(session('teams'))
                <hr class="my-4 border-secondary opacity-25">
                <h6 class="small fw-bold text-white-50 mb-3 text-uppercase">Hasil Pencarian ({{ session('teams')->count() }} Tim):</h6>
                
                <div class="results-wrapper" style="max-height: 350px; overflow-y: auto; padding-right: 5px;">
                    @foreach(session('teams') as $team)
                    <div class="result-box p-3 rounded-4 mb-3"
                        style="background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 193, 7, 0.1);">

                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <span class="text-white-50 text-uppercase fw-bold"
                                    style="font-size: 0.6rem; letter-spacing: 1px;">
                                    Nama Tim
                                </span>
                                <h5 class="fw-bold mb-0 text-white text-uppercase"
                                    style="letter-spacing: 0.5px;">
                                    {{ $team->name }}
                                </h5>
                                <code class="text-warning small" style="font-size: 0.7rem;">
                                    ID: {{ $team->trx_id }}
                                </code>
                            </div>

                            <span class="badge-status {{ $team->status == 'PAID' ? 'paid' : 'pending' }}">
                                <i class="bi {{ $team->status == 'PAID' ? 'bi-check-circle-fill' : 'bi-clock-fill' }} me-1"></i>
                                {{ $team->status }}
                            </span>
                        </div>

                        @if($team->status == 'PAID')
                            @if($team->season->wa_link)
                            <a href="{{ $team->season->wa_link }}" target="_blank"
                                class="btn btn-success w-100 fw-bold py-2 shadow-sm btn-whatsapp" style="font-size: 0.85rem;">
                                <i class="bi bi-whatsapp me-2"></i> GRUP KOORDINASI
                            </a>
                            @else
                            <div class="alert alert-secondary py-2 small mb-0 text-center" style="font-size: 0.7rem; background: rgba(255,255,255,0.05); border: none; color: #adb5bd;">
                                Link grup belum tersedia
                            </div>
                            @endif
                        @else
                            @if($team->tripay_reference)
                                {{-- Sudah pilih metode, arahkan ke instruksi bayar --}}
                                <a href="{{ route('payment.detail', $team->trx_id) }}"
                                    class="btn btn-outline-warning w-100 fw-bold py-2" style="font-size: 0.85rem;">
                                    LANJUTKAN PEMBAYARAN <i class="bi bi-arrow-right ms-1"></i>
                                </a>
                            @else
                                <a href="{{ route('payment.confirm', $team->trx_id) }}"
                                    class="btn btn-warning w-100 fw-bold py-2 shadow-sm text-dark" style="font-size: 0.85rem;">
                                    PILIH METODE BAYAR <i class="bi bi-wallet2 ms-2"></i>
                                </a>
                            @endif
                        @endif
                    </div>
                    @endforeach
                </div>
            @endif

            <div class="mt-5">
                <a href="{{ route('home') }}"
                    class="text-white-50 small text-decoration-none btn-back">
                    <i class="bi bi-chevron-left me-1"></i> Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    .input-group-custom { position: relative; }
    .icon-input {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #ffc107;
        font-size: 1.2rem;
    }
    .form-control-custom {
        width: 100%;
        background: rgba(0, 0, 0, 0.3);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        padding: 14px 15px 14px 45px;
        color: white;
    }
    .form-control-custom:focus {
        outline: none;
        border-color: #ffc107;
        background: rgba(0, 0, 0, 0.5);
    }
    .badge-status {
        font-size: 0.65rem;
        font-weight: 800;
        padding: 5px 12px;
        border-radius: 50px;
        text-transform: uppercase;
    }
    .badge-status.paid { background: #198754; color: white; }
    .badge-status.pending { background: #ffc107; color: #000; }
    .shadow-warning { box-shadow: 0 4px 15px rgba(255, 193, 7, 0.3); }
    .btn-back:hover { color: #ffc107 !important; }
    .results-wrapper::-webkit-scrollbar { width: 4px; }
    .results-wrapper::-webkit-scrollbar-thumb { background: #ffc107; border-radius: 10px; }
    .results-wrapper::-webkit-scrollbar-track { background: rgba(255,255,255,0.05); }
</style>
@endsection