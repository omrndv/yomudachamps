@extends('layouts.app')
@section('title', 'Detail Pembayaran')

@section('content')
<style>
    .detail-container {
        position: relative;
        padding: 3px;
        background: linear-gradient(45deg, #ffc107, #343a40, #ffc107);
        background-size: 400% 400%;
        animation: gradient-animation 5s ease infinite;
        border-radius: 24px;
        max-width: 450px;
        width: 100%;
    }

    @keyframes gradient-animation {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    .detail-card {
        background: #121417;
        border-radius: 22px;
        padding: 35px 25px;
        color: #fff;
    }

    .qris-container {
        background: #fff;
        padding: 15px;
        border-radius: 15px;
        display: inline-block;
        margin: 20px 0;
        box-shadow: 0 0 20px rgba(255, 193, 7, 0.2);
    }

    #countdown {
        text-shadow: 0 0 10px rgba(220, 53, 69, 0.3);
        background: rgba(220, 53, 69, 0.1);
        display: inline-block;
        padding: 5px 20px;
        border-radius: 12px;
        border: 1px solid rgba(220, 53, 69, 0.2);
    }

    .pay-code {
        font-family: 'Arial Black', sans-serif;
        font-size: 1.8rem;
        color: #ffc107;
        letter-spacing: 2px;
        background: rgba(255, 193, 7, 0.1);
        padding: 10px 20px;
        border-radius: 12px;
        border: 1px dashed #ffc107;
    }

    .instruction-accordion .accordion-item {
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.05);
        margin-bottom: 10px;
        border-radius: 12px !important;
        overflow: hidden;
    }

    .instruction-accordion .accordion-button {
        background: transparent;
        color: #fff;
        font-size: 0.85rem;
        font-weight: bold;
        box-shadow: none;
    }

    .instruction-accordion .accordion-button:not(.collapsed) {
        color: #ffc107;
        background: rgba(255, 193, 7, 0.05);
    }

    .instruction-accordion .accordion-body {
        color: #adb5bd;
        font-size: 0.8rem;
        line-height: 1.6;
    }

    .btn-check-status {
        background: transparent;
        color: #ffc107;
        border: 2px solid #ffc107;
        border-radius: 12px;
        padding: 14px;
        font-weight: 800;
        width: 100%;
        transition: all 0.3s;
    }

    .btn-check-status:hover {
        background: #ffc107;
        color: #000;
    }

    .btn-download-qris {
        background: #28a745;
        color: #fff;
        border: none;
        border-radius: 10px;
        padding: 8px 15px;
        font-size: 0.8rem;
        font-weight: bold;
        margin-top: 10px;
        transition: 0.3s;
        text-decoration: none;
        display: inline-block;
    }

    .btn-download-qris:hover {
        background: #218838;
        color: #fff;
        transform: translateY(-2px);
    }
    
    .payment-alert {
        background: rgba(255, 193, 7, 0.1);
        border: 1px solid #ffc107;
        border-radius: 12px;
        padding: 15px;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 12px;
        animation: pulse-border 2s infinite;
    }

    .payment-alert i {
        font-size: 1.5rem;
        color: #ffc107;
    }

    .payment-alert p {
        margin: 0;
        font-size: 0.85rem;
        color: #fff;
        line-height: 1.4;
        text-align: left;
    }

    .payment-alert b {
        color: #ffc107;
    }

    @keyframes pulse-border {
        0% { box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.4); }
        70% { box-shadow: 0 0 0 10px rgba(255, 193, 7, 0); }
        100% { box-shadow: 0 0 0 0 rgba(255, 193, 7, 0); }
    }
</style>

<div class="detail-container mx-auto">
    <div class="detail-card">
        <div class="text-center mb-4">
            <div class="badge bg-warning text-dark mb-3 px-3 py-2" style="border-radius: 50px; font-size: 0.6rem; font-weight: 800;">STATUS: WAITING PAYMENT</div>

            <div class="countdown-wrapper mb-4">
                <span class="text-secondary d-block small mb-1">Batas Waktu Pembayaran:</span>
                <div id="countdown" class="fw-bold text-danger" style="font-size: 1.6rem; letter-spacing: 2px; font-family: 'Courier New', Courier, monospace;">
                    00:00:00
                </div>
            </div>

            <h4 class="fw-bold mb-0 text-uppercase">Instruksi Bayar</h4>
            <p class="text-secondary small mt-1">Selesaikan pembayaran sebelum waktu habis</p>
        </div>

        <div class="text-center mb-4">
            <span class="text-secondary small d-block mb-2">Total Pembayaran:</span>
            <h3 class="fw-bold text-warning" style="font-family: 'Arial Black';">Rp {{ number_format($detail->amount, 0, ',', '.') }} <br> (+Biaya Admin)</h3>
        </div>
        
        <div class="payment-alert">
            <i class="bi bi-info-circle-fill"></i>
            <p>
                <b>PENTING:</b> Setelah transfer, silakan <b>tunggu 5-10 detik</b> atau <b>refresh halaman</b> ini untuk mendapatkan link grup tournament secara otomatis.
            </p>
        </div>
        
        <div class="text-center mb-4">
            @if($detail->payment_method === 'DANA')
            <p class="text-secondary small mb-3">Klik tombol di bawah untuk bayar pakai DANA:</p>
            <a href="{{ $detail->checkout_url }}" target="_blank" class="btn btn-primary d-inline-block px-4 py-3 mb-3" style="background: #118eea; border: none; border-radius: 12px; font-weight: bold; box-shadow: 0 4px 15px rgba(17, 142, 234, 0.3);">
                <img src="https://upload.wikimedia.org/wikipedia/commons/7/72/Logo_dan_2022.svg" height="20" class="me-2">
                BAYAR SEKARANG
            </a>
            <p class="small text-info"><i class="bi bi-info-circle me-1"></i> Kamu akan diarahkan ke aplikasi/web DANA</p>
            @elseif(Str::contains($detail->payment_method, 'QRIS'))
            <p class="text-secondary small mb-1">Scan QRIS di bawah ini:</p>
            <div class="qris-container">
                @php
                    $qrisUrl = $detail->qr_url ?? 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . $detail->qr_content;
                @endphp
                <img src="{{ $qrisUrl }}" alt="QRIS" style="width: 200px; height: 200px;">
                <br>
                <a href="{{ route('qris.download', ['url' => $qrisUrl]) }}" class="btn-download-qris">
                    <i class="bi bi-download me-1"></i> DOWNLOAD QRIS
                </a>
            </div>
            <p class="small text-info"><i class="bi bi-info-circle me-1"></i> Bisa pakai Dana, OVO, GoPay, ShopeePay, dll.</p>
            @else
            <p class="text-secondary small mb-2">Kode Bayar / Nomor VA:</p>
            <div class="pay-code d-inline-block">{{ $detail->pay_code }}</div>
            <button class="btn btn-sm btn-dark ms-2" onclick="copyToClipboard('{{ $detail->pay_code }}')">
                <i class="bi bi-clipboard"></i>
            </button>
            <p class="small text-secondary mt-2">Salin kode di atas ke aplikasi bank kamu</p>
            @endif
        </div>

        <div class="divider-dashed mb-4" style="border-top: 1px dashed rgba(255,255,255,0.1);"></div>

        <div class="accordion instruction-accordion" id="paymentSteps">
            @foreach($detail->instructions as $index => $group)
            <div class="accordion-item">
                <h2 class="accordion-header">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#step{{ $index }}">
                        {{ $group->title }}
                    </button>
                </h2>
                <div id="step{{ $index }}" class="accordion-collapse collapse" data-bs-parent="#paymentSteps">
                    <div class="accordion-body">
                        <ol class="ps-3 mb-0">
                            @foreach($group->steps as $step)
                            <li class="mb-2">{!! $step !!}</li>
                            @endforeach
                        </ol>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-4">
            <a href="{{ route('payment.success', $team->trx_id) }}" class="btn-check-status d-block text-center text-decoration-none">
                SAYA SUDAH BAYAR <i class="bi bi-arrow-repeat ms-1"></i>
            </a>

            <a href="{{ route('payment.confirm', $team->trx_id) }}" class="btn d-block text-center mt-2" style="color: #6c757d; font-size: 0.75rem; font-weight: bold; text-decoration: underline;">
                Ganti Metode Pembayaran
            </a>

            <div class="text-center mt-3">
                <p class="text-secondary mb-0" style="font-size: 0.75rem; letter-spacing: 1px;">
                    TRX ID: <span class="text-warning fw-bold">{{ $team->trx_id }}</span>
                </p>
                <p class="text-secondary small mb-0" style="font-size: 0.65rem; opacity: 0.6;">
                    REF: {{ $detail->reference }}
                </p>
            </div>
        </div>
    </div>
</div>

<script>
    function startCountdown() {
        const expiredTime = {{ $detail->expired_time ?? 0 }} * 1000;
        if (expiredTime === 0) return;

        const timer = setInterval(function() {
            const now = new Date().getTime();
            const distance = expiredTime - now;

            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((distance % (1000 * 60)) / 1000);

            const display = document.getElementById("countdown");
            if (display) {
                if (distance < 0) {
                    clearInterval(timer);
                    display.innerHTML = "EXPIRED";
                    window.location.href = "{{ route('payment.confirm', $team->trx_id) }}";
                } else {
                    display.innerHTML = (hours < 10 ? "0" + hours : hours) + ":" + (minutes < 10 ? "0" + minutes : minutes) + ":" + (seconds < 10 ? "0" + seconds : seconds);
                }
            }
        }, 1000);
    }

    function autoCheckStatus() {
        const checkInterval = setInterval(function() {
            fetch("{{ route('payment.check.status', $team->trx_id) }}")
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'PAID') {
                        clearInterval(checkInterval);
                        Swal.fire({
                            title: 'PEMBAYARAN BERHASIL!',
                            text: 'Sistem telah memverifikasi pembayaran tim kamu.',
                            icon: 'success',
                            background: '#121417',
                            color: '#fff',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            allowOutsideClick: false,
                            willClose: () => {
                                window.location.href = "{{ route('payment.success', $team->trx_id) }}";
                            }
                        });
                    }
                })
                .catch(error => console.error('Error checking status:', error));
        }, 2000);
    }

    document.addEventListener('DOMContentLoaded', function() {
        startCountdown();
        autoCheckStatus();
    });

    function copyToClipboard(text) {
        navigator.clipboard.writeText(text);
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: 'Berhasil disalin!',
            showConfirmButton: false,
            timer: 1500,
            background: '#1a1d21',
            color: '#ffc107'
        });
    }
</script>
@endsection