@extends('layouts.app')
@section('title', 'Pendaftaran Tim')

@section('content')
<style>
    .card-container {
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

    .gaming-card-v2 {
        background: #121417;
        border-radius: 22px;
        padding: 40px 30px;
        color: #fff;
    }

    .form-group-custom {
        position: relative;
        margin-bottom: 25px;
    }

    .form-control-v2 {
        background: #1b1f23;
        border: 2px solid #2d3238;
        border-radius: 12px;
        color: #fff !important;
        padding: 14px 18px;
        font-size: 0.95rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .form-control-v2:focus {
        background: #1b1f23;
        border-color: #ffc107;
        box-shadow: 0 0 15px rgba(255, 193, 7, 0.15);
        outline: none;
        transform: translateY(-2px);
    }

    .label-v2 {
        display: block;
        font-size: 0.75rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        color: #ffc107;
        margin-bottom: 8px;
        margin-left: 4px;
    }

    .btn-ultra {
        background: #ffc107;
        color: #000;
        border: none;
        border-radius: 12px;
        padding: 16px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 2px;
        width: 100%;
        position: relative;
        overflow: hidden;
        transition: all 0.4s;
    }

    .btn-ultra:hover {
        background: #fff;
        box-shadow: 0 0 30px rgba(255, 193, 7, 0.5);
        transform: scale(1.02);
    }

    .btn-ultra::after {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.4), transparent);
        transition: 0.5s;
    }

    .btn-ultra:hover::after {
        left: 100%;
    }

    .sub-text {
        color: #6c757d;
        font-size: 0.8rem;
        line-height: 1.5;
    }
</style>

<div class="card-container mx-auto">
    <div class="gaming-card-v2">
        <div class="text-center mb-5">
            <span class="badge bg-warning text-dark mb-2 px-3 py-2" style="border-radius: 50px; font-size: 0.65rem; font-weight: 800;">REGISTRATION OPEN</span>
            <h2 class="fw-black mb-0" style="font-family: 'Arial Black', sans-serif; letter-spacing: -1px;">
                YOMUDA <span class="text-warning">CHAMPIONSHIP</span>
            </h2>
            <p class="sub-text mt-2">Daftarkan skuad terbaikmu dan jadilah juara!</p>
        </div>

        @if($active_season)
        <form action="{{ route('register.store') }}" method="POST">
            @csrf
            <input type="hidden" name="season_id" value="{{ $active_season->id }}">

            <div class="form-group-custom">
                <label class="label-v2">Nama Team</label>
                <input type="text" name="name" class="form-control-v2 w-100" placeholder="Masukkan nama tim..." required>
            </div>

            <div class="form-group-custom">
                <label class="label-v2">Nomor Perwakilan Team (WA)</label>
                <input type="tel" name="wa_number" class="form-control-v2 w-100" placeholder="08xxxxxxxxxx" required>
            </div>

            <p class="text-center small mb-3" style="color: #ffc107;">
                @if($active_season->is_open)
                Biaya Pendaftaran: <strong>Rp {{ number_format($active_season->price, 0, ',', '.') }}</strong><br>
                <!-- Slot Tersisa: <strong>{{ $active_season->slot - $active_season->teams_count }} / {{ $active_season->slot }}</strong> -->
                @else
                <span class="text-danger">PENDAFTARAN SUDAH DITUTUP</span>
                @endif
            </p>

            @if($active_season->is_open && ($active_season->slot - $active_season->teams_count) > 0)
            <button type="submit" class="btn-ultra mt-2">Daftar Tournament</button>
            @else
            <button type="button" class="btn btn-secondary w-100 p-3" disabled>SLOT PENUH / TUTUP</button>
            @endif
        </form>
        @else
        <div class="alert alert-danger text-center small fw-bold">BELUM ADA SEASON AKTIF</div>
        @endif

        <div class="mt-5 pt-3 border-top border-secondary" style="border-style: dashed !important; opacity: 0.3;"></div>
        <p class="text-center sub-text mt-3 mb-0">Sistem akan mengarahkanmu ke pembayaran.</p>
    </div>
</div>
@endsection