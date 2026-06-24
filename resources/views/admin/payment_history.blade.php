@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4" style="background-color: #f8fafc; min-height: 100vh;">
    {{-- Header & Sync Info --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h2 class="fw-bold text-slate-800 mb-1 d-flex align-items-center gap-2.5" style="font-size: 1.75rem; letter-spacing: -0.5px;">
                TriPay Gateway Dashboard
                @if(($tripayMode ?? 'sandbox') === 'production')
                    <span class="badge bg-success text-white fw-bold px-3 py-1.5 rounded-pill" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                        <i class="bi bi-shield-check-fill me-1"></i> LIVE MODE
                    </span>
                @else
                    <span class="badge bg-warning text-dark fw-bold px-3 py-1.5 rounded-pill" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                        <i class="bi bi-exclamation-triangle-fill me-1"></i> SANDBOX MODE
                    </span>
                @endif
            </h2>
            <p class="text-secondary mb-0" style="font-size: 0.9rem;">
                Pemantauan mutasi, transaksi, dan arus keuangan real-time dari API Payment Gateway TriPay.
            </p>
        </div>
    </div>

    {{-- Alert Success / Error --}}
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center">
            <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    {{-- Filter Card --}}
    <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
        <form action="{{ route('admin.payments') }}" method="GET">
            <div class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label small fw-bold text-secondary text-uppercase mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">Cari Referensi TriPay / Invoice</label>
                    <div class="input-group rounded-3 overflow-hidden border border-light-subtle shadow-none">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-search text-secondary"></i></span>
                        <input type="text" name="search" class="form-control border-0 bg-light shadow-none p-2.5" placeholder="Masukkan Ref (T...) atau Invoice (YMD...)" value="{{ request('search') }}" style="font-size: 0.85rem;">
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold text-secondary text-uppercase mb-1" style="font-size: 0.7rem; letter-spacing: 0.5px;">Status Pembayaran</label>
                    <select name="status" class="form-select border-0 bg-light rounded-3 shadow-none p-2.5" style="font-size: 0.85rem;">
                        <option value="">Semua Status</option>
                        <option value="PAID" {{ request('status') == 'PAID' ? 'selected' : '' }}>PAID (Lunas)</option>
                        <option value="UNPAID" {{ request('status') == 'UNPAID' ? 'selected' : '' }}>UNPAID (Belum Bayar)</option>
                        <option value="EXPIRED" {{ request('status') == 'EXPIRED' ? 'selected' : '' }}>EXPIRED (Kedaluwarsa)</option>
                        <option value="FAILED" {{ request('status') == 'FAILED' ? 'selected' : '' }}>FAILED (Gagal)</option>
                        <option value="REFUND" {{ request('status') == 'REFUND' ? 'selected' : '' }}>REFUND (Pengembalian)</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="d-flex gap-2 w-100">
                        <button type="submit" class="btn btn-dark w-100 fw-bold rounded-3 shadow-sm py-2.5" style="font-size: 0.85rem; letter-spacing: 0.3px;">
                            <i class="bi bi-filter me-1"></i> CARI TRANSAKSI
                        </button>
                        <a href="{{ route('admin.payments') }}" class="btn btn-light rounded-3 shadow-sm border border-light-subtle py-2.5 px-3 d-flex align-items-center justify-content-center text-muted" title="Reset Filter">
                            <i class="bi bi-arrow-clockwise fs-5"></i>
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- Ringkasan Pendapatan Halaman --}}
    <div class="row g-4 mb-4">
        {{-- Total Pendapatan Bersih --}}
        <div class="col-md-4">
            <div class="card card-stats border-0 p-4 bg-white shadow-sm rounded-4 h-100 d-flex flex-column justify-content-between">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div>
                        <p class="text-secondary small fw-bold mb-1 text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Pendapatan Bersih (Halaman Ini)</p>
                        <h3 class="fw-bold text-success mb-0" style="font-size: 1.5rem; letter-spacing: -0.5px;">
                            Rp {{ number_format($total_cuan, 0, ',', '.') }}
                        </h3>
                    </div>
                    <div class="icon-shape text-white shadow-sm" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; border-radius: 12px;">
                        <i class="bi bi-wallet2 fs-5"></i>
                    </div>
                </div>
                <div class="pt-2 border-top border-light text-muted" style="font-size: 0.72rem;">
                    Dana bersih yang siap ditransfer / dikliring ke rekening.
                </div>
            </div>
        </div>

        {{-- Total Potongan Fee --}}
        <div class="col-md-4">
            <div class="card card-stats border-0 p-4 bg-white shadow-sm rounded-4 h-100 d-flex flex-column justify-content-between">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div>
                        <p class="text-secondary small fw-bold mb-1 text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Potongan Biaya TriPay (Halaman Ini)</p>
                        <h3 class="fw-bold text-danger mb-0" style="font-size: 1.5rem; letter-spacing: -0.5px;">
                            Rp {{ number_format($total_fee, 0, ',', '.') }}
                        </h3>
                    </div>
                    <div class="icon-shape text-white shadow-sm" style="background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%); width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; border-radius: 12px;">
                        <i class="bi bi-percent fs-5"></i>
                    </div>
                </div>
                <div class="pt-2 border-top border-light text-muted" style="font-size: 0.72rem;">
                    Akumulasi biaya transaksi (MDR / Flat Fee) e-wallet & QRIS.
                </div>
            </div>
        </div>

        {{-- Data Transaksi Ditemukan --}}
        <div class="col-md-4">
            <div class="card card-stats border-0 p-4 bg-white shadow-sm rounded-4 h-100 d-flex flex-column justify-content-between">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <div>
                        <p class="text-secondary small fw-bold mb-1 text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Total Transaksi Terdaftar</p>
                        <h3 class="fw-bold text-dark mb-0" style="font-size: 1.5rem; letter-spacing: -0.5px;">
                            {{ $total_trx }} <span class="fs-6 text-muted fw-normal">Mutasi</span>
                        </h3>
                    </div>
                    <div class="icon-shape text-white shadow-sm" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%); width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; border-radius: 12px;">
                        <i class="bi bi-list-columns-reverse fs-5"></i>
                    </div>
                </div>
                <div class="pt-2 border-top border-light text-muted" style="font-size: 0.72rem;">
                    Jumlah riwayat keseluruhan transaksi di akun TriPay Anda.
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Log TriPay --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                    <thead class="bg-light">
                        <tr class="fw-bold text-secondary text-uppercase" style="font-size: 0.73rem; border-bottom: 2px solid #f1f5f9;">
                            <th class="ps-4 py-3 border-0" width="60">#</th>
                            <th class="py-3 border-0">Waktu & Referensi</th>
                            <th class="py-3 border-0">Pelanggan / Invoice</th>
                            <th class="py-3 border-0">Metode Pembayaran</th>
                            <th class="py-3 text-center border-0" width="120">Status</th>
                            <th class="py-3 text-end border-0" width="130">Kotor (Gross)</th>
                            <th class="py-3 text-end border-0" width="110">Biaya (Fee)</th>
                            <th class="py-3 text-end pe-4 border-0" width="130">Bersih (Net)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $index => $pay)
                        <tr style="border-bottom: 1px solid #f8fafc;">
                            <td class="ps-4 text-muted fw-semibold">
                                @if($pagination)
                                    {{ ($pagination->current_page - 1) * $pagination->per_page + $loop->iteration }}
                                @else
                                    {{ $loop->iteration }}
                                @endif
                            </td>
                            <td class="py-3">
                                <div class="fw-bold text-dark">
                                    {{ isset($pay->created_at) ? date('d/m/y H:i', $pay->created_at) : '-' }} WIB
                                </div>
                                <div class="text-primary small fw-semibold" style="font-size: 0.75rem;">
                                    Ref: {{ $pay->reference ?? '-' }}
                                </div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark text-uppercase">{{ $pay->customer_name ?? 'UMUM / TANPA NAMA' }}</div>
                                <div class="text-muted small" style="font-size: 0.72rem;">
                                    Inv: <span class="fw-semibold">{{ $pay->merchant_ref ?? '-' }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-1.5">
                                    <span class="badge bg-dark text-white px-2 py-1" style="font-size: 0.65rem; letter-spacing: 0.3px;">
                                        {{ $pay->payment_method ?? 'ONLINE' }}
                                    </span>
                                    <span class="text-secondary small d-block" style="font-size: 0.75rem;">
                                        {{ $pay->payment_name ?? '' }}
                                    </span>
                                </div>
                            </td>
                            <td class="text-center">
                                @php
                                    $statusRaw = strtoupper($pay->status ?? 'UNPAID');
                                    $badgeClass = 'bg-warning text-dark';
                                    if ($statusRaw === 'PAID') {
                                        $badgeClass = 'bg-success-subtle text-success border border-success-subtle';
                                    } elseif ($statusRaw === 'EXPIRED' || $statusRaw === 'FAILED') {
                                        $badgeClass = 'bg-danger-subtle text-danger border border-danger-subtle';
                                    } elseif ($statusRaw === 'REFUND') {
                                        $badgeClass = 'bg-info-subtle text-info border border-info-subtle';
                                    }
                                @endphp
                                <span class="badge {{ $badgeClass }} px-2.5 py-1 rounded-pill fw-bold" style="font-size: 0.68rem; letter-spacing: 0.3px;">
                                    {{ $statusRaw }}
                                </span>
                            </td>
                            <td class="text-end fw-bold text-dark">
                                Rp {{ number_format($pay->amount ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="text-end text-danger fw-medium" style="font-size: 0.8rem;">
                                - Rp {{ number_format($pay->total_fee ?? 0, 0, ',', '.') }}
                            </td>
                            <td class="text-end fw-bold text-success pe-4">
                                Rp {{ number_format($pay->amount_received ?? 0, 0, ',', '.') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <div class="py-4">
                                    <i class="bi bi-search fs-1 d-block mb-3 opacity-25"></i>
                                    <p class="mb-0 fw-bold">Tidak ada data transaksi ditemukan di akun TriPay Anda.</p>
                                    <small>Coba sesuaikan kata kunci pencarian atau ganti filter status pembayaran.</small>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Custom Pagination --}}
        <div class="card-footer bg-white border-0 py-3 d-flex justify-content-center">
            @if($pagination && $pagination->last_page > 1)
            <nav>
                <ul class="pagination mb-0">
                    <!-- Previous Button -->
                    @if($pagination->current_page > 1)
                        <li class="page-item">
                            <a class="page-link rounded-start-3 shadow-none border-light-subtle text-dark" href="{{ route('admin.payments', array_merge(request()->query(), ['page' => $pagination->current_page - 1])) }}">
                                <i class="bi bi-chevron-left"></i>
                            </a>
                        </li>
                    @else
                        <li class="page-item disabled">
                            <span class="page-link rounded-start-3 border-light-subtle text-muted"><i class="bi bi-chevron-left"></i></span>
                        </li>
                    @endif

                    <!-- Current Info -->
                    <li class="page-item disabled">
                        <span class="page-link border-light-subtle text-dark fw-bold px-4" style="font-size: 0.85rem;">
                            Halaman {{ $pagination->current_page }} / {{ $pagination->last_page }}
                        </span>
                    </li>

                    <!-- Next Button -->
                    @if($pagination->current_page < $pagination->last_page)
                        <li class="page-item">
                            <a class="page-link rounded-end-3 shadow-none border-light-subtle text-dark" href="{{ route('admin.payments', array_merge(request()->query(), ['page' => $pagination->current_page + 1])) }}">
                                <i class="bi bi-chevron-right"></i>
                            </a>
                        </li>
                    @else
                        <li class="page-item disabled">
                            <span class="page-link rounded-end-3 border-light-subtle text-muted"><i class="bi bi-chevron-right"></i></span>
                        </li>
                    @endif
                </ul>
            </nav>
            @endif
        </div>
    </div>
</div>

{{-- Styling Khusus --}}
<style>
    .card-stats {
        background: #ffffff;
        border: 1px solid rgba(241, 245, 249, 0.8) !important;
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.01);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .card-stats:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 20px -5px rgba(0, 0, 0, 0.04), 0 8px 8px -5px rgba(0, 0, 0, 0.02);
        border-color: rgba(226, 232, 240, 0.8) !important;
    }
    .pagination .page-link {
        padding: 8px 16px;
    }
</style>
@endsection