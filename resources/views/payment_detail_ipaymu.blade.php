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

    .qris-container {
        background: #fff;
        padding: 15px;
        border-radius: 15px;
        display: inline-block;
        box-shadow: 0 0 20px rgba(255, 193, 7, 0.2);
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

    @keyframes pulse-border {
        0% { box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.4); }
        70% { box-shadow: 0 0 0 10px rgba(255, 193, 7, 0); }
        100% { box-shadow: 0 0 0 0 rgba(255, 193, 7, 0); }
    }
</style>

<div class="detail-container mx-auto">
    <div class="detail-card">
        <div class="text-center mb-4">
            <div class="badge bg-warning text-dark mb-3 px-3 py-2" style="border-radius: 50px; font-size: 0.6rem; font-weight: 800;">STATUS: MENUNGGU PEMBAYARAN</div>

            <h4 class="fw-bold mb-0 text-uppercase">Scan QRIS Untuk Bayar</h4>
            <p class="text-secondary small mt-1">Selesaikan pembayaran secara instan di bawah ini</p>
        </div>

        <div class="text-center mb-4">
            <span class="text-secondary small d-block mb-1">Total Pembayaran:</span>
            <h3 class="fw-bold text-warning" style="font-family: 'Arial Black';">Rp {{ number_format($team->season->price, 0, ',', '.') }}</h3>
        </div>

        <div class="text-center mb-4">
            @if($team->payment_method)
            <div class="qris-container">
                <img src="{{ $team->payment_method }}" alt="QRIS" style="width: 220px; height: 220px; object-fit: contain;">
                <br>
                <a href="{{ route('qris.download', ['url' => $team->payment_method]) }}" class="btn-download-qris">
                    <i class="bi bi-download me-1"></i> DOWNLOAD QRIS
                </a>
            </div>
            <p class="small text-info mt-3 mb-0"><i class="bi bi-info-circle me-1"></i> Mendukung DANA, OVO, GoPay, LinkAja, ShopeePay & e-wallet/M-Banking lainnya.</p>
            @endif
        </div>

        <div class="p-3 mb-4" style="background: rgba(255, 255, 255, 0.03); border-radius: 12px; border-left: 4px solid #ffc107;">
            <div class="d-flex justify-content-between mb-2">
                <span class="small text-secondary">Nama Tim:</span>
                <span class="small fw-bold text-white">{{ $team->name }}</span>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span class="small text-secondary">WhatsApp:</span>
                <span class="small fw-bold text-white">{{ $team->wa_number }}</span>
            </div>
            <div class="d-flex justify-content-between mb-2">
                <span class="small text-secondary">Season:</span>
                <span class="small fw-bold text-white">{{ $team->season->name }}</span>
            </div>
            <div class="d-flex justify-content-between">
                <span class="small text-secondary">TRX ID:</span>
                <span class="small fw-bold text-warning">{{ $team->trx_id }}</span>
            </div>
        </div>
        
        <div class="payment-alert">
            <i class="bi bi-info-circle-fill"></i>
            <p>
                <b>PENTING:</b> Setelah transfer/scan berhasil, silakan <b>tunggu 5-10 detik</b>. Halaman ini akan memverifikasi status secara otomatis dan mengarahkan Anda ke grup koordinasi.
            </p>
        </div>

        <div class="mt-4">
            <button onclick="window.location.reload();" class="btn-check-status d-block text-center">
                REFRESH STATUS <i class="bi bi-arrow-repeat ms-1"></i>
            </button>
        </div>
    </div>
</div>

<script>
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
        }, 3000);
    }

    document.addEventListener('DOMContentLoaded', function() {
        autoCheckStatus();
    });
</script>
@endsection
