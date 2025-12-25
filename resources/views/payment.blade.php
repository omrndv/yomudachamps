@extends('layouts.app')
@section('title', 'Pilih Metode Pembayaran')

@section('content')
<style>
    .payment-container {
        position: relative;
        padding: 3px;
        background: linear-gradient(45deg, #ffc107, #343a40, #ffc107);
        background-size: 400% 400%;
        animation: gradient-animation 5s ease infinite;
        border-radius: 24px;
        max-width: 420px;
        width: 100%;
    }

    @keyframes gradient-animation {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    .payment-card {
        background: #121417;
        border-radius: 22px;
        padding: 35px 25px;
        color: #fff;
    }

    .info-box {
        background: rgba(255, 255, 255, 0.03);
        border-left: 4px solid #ffc107;
        border-radius: 12px;
        padding: 15px 20px;
    }

    .price-tag {
        font-family: 'Arial Black', sans-serif;
        font-size: 2.2rem;
        color: #ffc107;
        text-shadow: 0 0 20px rgba(255, 193, 7, 0.3);
    }

    .method-list {
        max-height: 320px;
        overflow-y: auto;
        padding-right: 5px;
    }

    .method-list::-webkit-scrollbar {
        width: 4px;
    }

    .method-list::-webkit-scrollbar-track {
        background: #1b1f23;
    }

    .method-list::-webkit-scrollbar-thumb {
        background: #ffc107;
        border-radius: 10px;
    }

    .payment-option {
        cursor: pointer;
        border-radius: 12px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        background: rgba(255, 255, 255, 0.02);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 15px;
        margin-bottom: 10px;
    }

    .payment-option:hover {
        background: rgba(255, 255, 255, 0.05);
        border-color: rgba(255, 193, 7, 0.5);
    }

    .btn-check:checked+.payment-option {
        background: rgba(255, 193, 7, 0.1) !important;
        border-color: #ffc107 !important;
        box-shadow: 0 0 15px rgba(255, 193, 7, 0.2);
        transform: scale(1.02);
    }

    .btn-check:checked+.payment-option .method-name {
        color: #ffc107 !important;
    }

    .btn-check:checked+.payment-option i {
        color: #ffc107 !important;
        transform: translateX(3px);
    }

    .logo-container {
        background: #fff;
        width: 50px;
        height: 30px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 4px;
        margin-right: 15px;
    }

    .logo-container img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }

    .btn-pay {
        background: #ffc107;
        color: #000;
        border: none;
        border-radius: 12px;
        padding: 16px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 2px;
        width: 100%;
        transition: all 0.4s;
        margin-top: 10px;
    }

    .btn-pay:hover {
        background: #fff;
        box-shadow: 0 0 30px rgba(255, 193, 7, 0.4);
        transform: translateY(-3px);
    }
</style>

<div class="payment-container mx-auto">
    <div class="payment-card">
        <div class="text-center mb-4">
            <div class="badge bg-warning text-dark mb-2 px-3 py-2" style="border-radius: 50px; font-size: 0.6rem; font-weight: 800;">STEP 2: PAYMENT METHOD</div>
            <h3 class="fw-bold mb-0">KONFIRMASI</h3>
        </div>

        <div class="info-box mb-4">
            <div class="d-flex justify-content-between mb-1">
                <span class="small text-secondary fw-bold">TEAM</span>
                <span class="small fw-bold text-white">{{ $team->name }}</span>
            </div>
            <div class="d-flex justify-content-between">
                <span class="small text-secondary fw-bold">TOTAL</span>
                <span class="small fw-bold text-warning">Rp {{ number_format($team->season->price, 0, ',', '.') }}</span>
            </div>
        </div>

        <form action="{{ route('payment.checkout', $team->trx_id) }}" method="POST">
            @csrf
            <label class="label-v2 mb-3 small fw-bold text-secondary text-uppercase" style="letter-spacing: 1px;">Pilih Cara Bayar:</label>

            <div class="method-list mb-4">
                @forelse($channels as $channel)
                @if($channel->active)
                <div class="position-relative">
                    <input type="radio" class="btn-check" name="payment_method" id="method-{{ $channel->code }}" value="{{ $channel->code }}" required>
                    <label class="payment-option" for="method-{{ $channel->code }}">
                        <div class="d-flex align-items-center">
                            <div class="logo-container">
                                <img src="{{ $channel->icon_url }}" alt="{{ $channel->name }}">
                            </div>
                            <span class="method-name small fw-bold text-white">{{ $channel->name }}</span>
                        </div>
                        <i class="bi bi-chevron-right text-secondary small"></i>
                    </label>
                </div>
                @endif
                @empty
                <div class="text-center py-3">
                    <p class="small text-danger">Gagal memuat metode pembayaran.</p>
                </div>
                @endforelse
            </div>

            <button type="submit" class="btn-pay">
                BAYAR SEKARANG <i class="bi bi-arrow-right-short fs-4"></i>
            </button>
        </form>

        <p class="text-center text-secondary mt-4 mb-0" style="font-size: 0.7rem;">
            <i class="bi bi-shield-lock-fill text-warning me-1"></i> Secure Payment by TriPay
        </p>
    </div>
</div>
@endsection