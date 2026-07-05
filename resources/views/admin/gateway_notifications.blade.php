@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4" style="background-color: #f8fafc; min-height: 100vh;">
    {{-- Header --}}
    <div class="row mb-4 align-items-center">
        <div class="col-md-7">
            <h2 class="fw-bold text-dark mb-1" style="font-size: 1.75rem; letter-spacing: -0.5px;">
                Log Notifikasi & Aktivitas Gateway
            </h2>
            <p class="text-secondary mb-0" style="font-size: 0.9rem;">
                Riwayat aktivitas sistem QRIS Gateway, transaksi lunas, pembuatan QRIS, dan status deteksi API GoPay.
            </p>
        </div>
        <div class="col-md-5 text-md-end mt-3 mt-md-0 d-flex flex-wrap gap-2 justify-content-md-end">
            <form action="{{ route('admin.settings.gateway_notifications.clear') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus semua log notifikasi gateway ini? Tindakan ini tidak dapat dibatalkan.');" class="m-0">
                @csrf
                <button type="submit" class="btn btn-outline-danger d-inline-flex align-items-center gap-2" style="border-radius: 12px; font-weight: 600; padding: 10px 18px; font-size: 0.85rem;">
                    <i class="bi bi-trash3 fs-6"></i> Bersihkan Log
                </button>
            </form>
            <a href="{{ route('admin.settings') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-2" style="border-radius: 12px; font-weight: 600; padding: 10px 18px; font-size: 0.85rem;">
                <i class="bi bi-arrow-left-short fs-5"></i> Kembali ke Pengaturan
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        </div>
    @endif

    {{-- Stats Cards --}}
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4 bg-white rounded-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-danger-subtle text-danger rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-exclamation-triangle-fill fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-secondary mb-1 small fw-bold text-uppercase">Error API Terdeteksi</h6>
                        <h4 class="fw-bold text-dark mb-0">
                            {{ \App\Models\GatewayNotification::where('type', 'API_ERROR')->count() }}
                        </h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4 bg-white rounded-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-success-subtle text-success rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-cash-check fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-secondary mb-1 small fw-bold text-uppercase">Transaksi Terverifikasi</h6>
                        <h4 class="fw-bold text-dark mb-0">
                            {{ \App\Models\GatewayNotification::where('type', 'TRANSACTION_PAID')->count() }}
                        </h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-4 bg-white rounded-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary-subtle text-primary rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-qr-code fs-4"></i>
                    </div>
                    <div>
                        <h6 class="text-secondary mb-1 small fw-bold text-uppercase">Total QRIS Di-generate</h6>
                        <h4 class="fw-bold text-dark mb-0">
                            {{ \App\Models\GatewayNotification::where('type', 'TRANSACTION_CREATED')->count() }}
                        </h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Row --}}
    <div class="d-flex gap-2 mb-4 overflow-x-auto pb-2">
        <a href="{{ route('admin.settings.gateway_notifications') }}" class="btn {{ !request('type') ? 'btn-primary' : 'btn-white border' }} rounded-pill px-4 py-2 text-xs font-bold whitespace-nowrap">
            Semua Log
        </a>
        <a href="{{ route('admin.settings.gateway_notifications', ['type' => 'TRANSACTION_PAID']) }}" class="btn {{ request('type') === 'TRANSACTION_PAID' ? 'btn-success' : 'btn-white border' }} rounded-pill px-4 py-2 text-xs font-bold whitespace-nowrap">
            Bayar Lunas
        </a>
        <a href="{{ route('admin.settings.gateway_notifications', ['type' => 'API_ERROR']) }}" class="btn {{ request('type') === 'API_ERROR' ? 'btn-danger' : 'btn-white border' }} rounded-pill px-4 py-2 text-xs font-bold whitespace-nowrap">
            API Error
        </a>
        <a href="{{ route('admin.settings.gateway_notifications', ['type' => 'TRANSACTION_CREATED']) }}" class="btn {{ request('type') === 'TRANSACTION_CREATED' ? 'btn-primary' : 'btn-white border' }} rounded-pill px-4 py-2 text-xs font-bold whitespace-nowrap">
            QRIS Baru
        </a>
        <a href="{{ route('admin.settings.gateway_notifications', ['type' => 'API_SUCCESS']) }}" class="btn {{ request('type') === 'API_SUCCESS' ? 'btn-success' : 'btn-white border' }} rounded-pill px-4 py-2 text-xs font-bold whitespace-nowrap">
            Koneksi Normal
        </a>
    </div>

    {{-- Main Logs List --}}
    <div class="card border-0 shadow-sm bg-white rounded-4 overflow-hidden">
        <div class="card-header bg-white border-bottom border-light p-4 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                <i class="bi bi-clock-history text-primary"></i> Timeline Notifikasi Sistem
            </h5>
            <span class="badge bg-light text-secondary border border-light-subtle rounded-pill px-3 py-2 fw-medium" style="font-size: 0.8rem;">
                Halaman {{ $notifications->currentPage() }} dari {{ $notifications->lastPage() }}
            </span>
        </div>

        <div class="card-body p-0">
            @if($notifications->isEmpty())
                <div class="text-center py-5">
                    <div class="mb-3 text-secondary">
                        <i class="bi bi-bell-slash fs-1" style="opacity: 0.5;"></i>
                    </div>
                    <h5 class="fw-bold text-dark">Belum Ada Notifikasi</h5>
                    <p class="text-secondary small mb-0">Aktivitas gateway dan transaksi pendaftaran baru akan muncul di sini.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="border-collapse: separate;">
                        <thead>
                            <tr class="bg-light-subtle text-secondary small fw-bold border-bottom" style="background-color: #f8fafc;">
                                <th style="width: 180px; padding: 16px 24px;">Status / Tipe</th>
                                <th style="padding: 16px;">Detail Pesan</th>
                                <th style="width: 180px; padding: 16px 24px;" class="text-end">Waktu Kejadian</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($notifications as $notif)
                                <tr style="border-bottom: 1px solid rgba(0,0,0,0.03);">
                                    <td style="padding: 20px 24px;">
                                        @if($notif->type === 'API_ERROR')
                                            <span class="badge d-inline-flex align-items-center gap-1.5 px-3 py-2 rounded-pill bg-danger-subtle text-danger border border-danger-subtle fw-bold" style="font-size: 0.73rem;">
                                                <i class="bi bi-exclamation-octagon-fill"></i> API ERROR
                                            </span>
                                        @elseif($notif->type === 'API_SUCCESS')
                                            <span class="badge d-inline-flex align-items-center gap-1.5 px-3 py-2 rounded-pill bg-success-subtle text-success border border-success-subtle fw-bold" style="font-size: 0.73rem;">
                                                <i class="bi bi-check-circle-fill"></i> API SEHAT
                                            </span>
                                        @elseif($notif->type === 'TRANSACTION_PAID')
                                            <span class="badge d-inline-flex align-items-center gap-1.5 px-3 py-2 rounded-pill bg-success-subtle text-success border border-success-subtle fw-bold" style="font-size: 0.73rem;">
                                                <i class="bi bi-wallet2"></i> BAYAR LUNAS
                                            </span>
                                        @elseif($notif->type === 'TRANSACTION_CREATED')
                                            <span class="badge d-inline-flex align-items-center gap-1.5 px-3 py-2 rounded-pill bg-primary-subtle text-primary border border-primary-subtle fw-bold" style="font-size: 0.73rem;">
                                                <i class="bi bi-plus-circle-fill"></i> QRIS BARU
                                            </span>
                                        @else
                                            <span class="badge d-inline-flex align-items-center gap-1.5 px-3 py-2 rounded-pill bg-secondary-subtle text-secondary border border-secondary-subtle fw-bold" style="font-size: 0.73rem;">
                                                <i class="bi bi-info-circle-fill"></i> INFO
                                            </span>
                                        @endif
                                    </td>
                                    <td style="padding: 20px 16px;">
                                        <h6 class="fw-bold text-dark mb-1" style="font-size: 0.95rem;">
                                            {{ $notif->title }}
                                        </h6>
                                        <p class="text-secondary mb-0 small" style="line-height: 1.5;">
                                            {{ $notif->message }}
                                        </p>
                                    </td>
                                    <td style="padding: 20px 24px;" class="text-end text-secondary small">
                                        <div class="fw-bold text-dark">{{ $notif->created_at->setTimezone('Asia/Jakarta')->format('H:i:s') }} WIB</div>
                                        <div style="font-size: 0.75rem;">{{ $notif->created_at->setTimezone('Asia/Jakarta')->format('d M Y') }}</div>
                                        <div class="text-muted" style="font-size: 0.7rem;">{{ $notif->created_at->diffForHumans() }}</div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination Links --}}
                <div class="p-4 border-top border-light d-flex justify-content-center">
                    {{ $notifications->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
