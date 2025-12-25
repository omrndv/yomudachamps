@extends('layouts.app')
@section('title', 'Pendaftaran Berhasil')

@section('content')
<style>
    .success-container {
        position: relative;
        padding: 3px;
        background: linear-gradient(45deg, #28a745, #343a40, #ffc107);
        background-size: 400% 400%;
        animation: gradient-animation 5s ease infinite;
        border-radius: 24px;
        max-width: 420px;
        width: 100%;
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

    .success-card {
        background: #121417;
        border-radius: 22px;
        padding: 40px 30px;
        color: #fff;
    }

    .check-icon {
        font-size: 4rem;
        color: #28a745;
        text-shadow: 0 0 20px rgba(40, 167, 69, 0.4);
        display: block;
        margin-bottom: 15px;
    }

    .status-box {
        background: rgba(40, 167, 69, 0.05);
        border: 1px solid rgba(40, 167, 69, 0.2);
        border-radius: 12px;
        padding: 15px;
        text-align: left;
    }

    .btn-wa {
        background: #25d366;
        color: #fff;
        border: none;
        border-radius: 12px;
        padding: 16px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
        width: 100%;
        transition: all 0.4s;
    }

    .btn-wa:hover {
        background: #20ba5a;
        box-shadow: 0 0 25px rgba(37, 211, 102, 0.4);
        transform: translateY(-3px);
    }
</style>

<div class="success-container mx-auto">
    <div class="success-card text-center">
        <img src="/images/logo-yomuda.png" alt="Logo Yomuda" class="mb-3" style="width: 120px; height: auto; object-fit: contain;">

        <h3 class="fw-bold text-warning mb-1">REGISTRASI BERHASIL</h3>
        <p class="text-secondary small mb-4">Slot tim kamu sudah aman dalam turnamen! (Screenshot halaman ini untuk bukti sukses daftar).</p>

        <div class="status-box mb-4" style="background: rgba(255,255,255,0.05); padding: 15px; border-radius: 12px;">
            <div class="d-flex justify-content-between mb-2">
                <span class="small text-secondary fw-bold">Status</span>
                <span class="small fw-bold text-success">Lunas</span>
            </div>
            <div class="d-flex justify-content-between">
                <span class="small text-secondary fw-bold">Nama Team</span>
                <span class="small fw-bold text-white">{{ $team->name }}</span>
            </div>
        </div>

        <p class="small text-white-50 mb-4">
            Klik tombol di bawah untuk masuk ke grup koordinasi peserta.
        </p>

        @if($team->season->wa_link)
        <a href="{{ $team->season->wa_link }}" target="_blank" class="btn btn-wa text-decoration-none d-block text-center"
            style="background: #25d366; color: #fff; width: 100%; border-radius: 12px; padding: 15px; font-weight: bold; border: none;">
            <i class="bi bi-whatsapp me-2"></i> GABUNG GRUP WHATSAPP
        </a>
        @else
        <div class="alert alert-dark small text-center" style="background: rgba(255,255,255,0.05); border: 1px dashed #6c757d;">
            <i class="bi bi-info-circle me-1"></i> Link grup belum tersedia. Admin akan menghubungimu.
        </div>
        @endif

        <p class="text-secondary mt-4 mb-0" style="font-size: 0.7rem; letter-spacing: 1px;">
            ID PENDAFTARAN: #{{ $team->trx_id }}
        </p>
    </div>
</div>
@endsection