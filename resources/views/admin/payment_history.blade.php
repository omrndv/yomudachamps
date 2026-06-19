@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4" style="background-color: #f8fafc; min-height: 100vh;">
    {{-- Header & Sync Button --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h2 class="fw-bold text-slate-800 mb-1" style="font-size: 1.75rem; letter-spacing: -0.5px;">
                Riwayat Pembayaran
            </h2>
            <p class="text-secondary mb-0" style="font-size: 0.9rem;">
                Daftar pelunasan pendaftaran yang tersinkronisasi otomatis dengan API Gateway Tripay.
            </p>
        </div>
        <div>
            <a href="{{ route('admin.payments.sync') }}" class="btn btn-warning fw-bold shadow-sm rounded-pill px-4 py-2 d-flex align-items-center gap-2" style="font-size: 0.85rem;">
                <i class="bi bi-arrow-repeat fs-6"></i> SINKRONISASI TRIPAY
            </a>
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
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-secondary text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Season</label>
                    <select name="season_id" class="form-select border-0 bg-light rounded-3 shadow-none p-2.5" style="font-size: 0.85rem;">
                        <option value="">Semua Season</option>
                        @foreach($seasons as $s)
                            <option value="{{ $s->id }}" {{ isset($season_id) && $season_id == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-secondary text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Dari Tanggal</label>
                    <input type="date" name="start_date" class="form-control border-0 bg-light rounded-3 shadow-none p-2.5" value="{{ $start_date }}" style="font-size: 0.85rem;">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-secondary text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Sampai Tanggal</label>
                    <input type="date" name="end_date" class="form-control border-0 bg-light rounded-3 shadow-none p-2.5" value="{{ $end_date }}" style="font-size: 0.85rem;">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-secondary text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Cari Tim / TRX</label>
                    <input type="text" name="search" class="form-control border-0 bg-light rounded-3 shadow-none p-2.5" placeholder="Nama Tim atau ID TRX..." value="{{ request('search') }}" style="font-size: 0.85rem;">
                </div>
                <div class="col-md-2">
                    <div class="d-flex gap-2 w-100">
                        <button type="submit" class="btn btn-dark w-100 fw-bold rounded-3 shadow-sm py-2.5" style="font-size: 0.85rem;">
                            <i class="bi bi-filter me-1"></i> FILTER
                        </button>
                        <a href="{{ route('admin.payments') }}" class="btn btn-light rounded-3 shadow-sm border border-light-subtle py-2.5 px-3 d-flex align-items-center justify-content-center text-muted" title="Reset Filter">
                            <i class="bi bi-arrow-clockwise fs-5"></i>
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- Ringkasan Pendapatan --}}
    <div class="row g-3 mb-4">
        {{-- Total Pendapatan Bersih --}}
        <div class="col-md-6">
            <div class="card card-stats border-0 p-4 bg-white shadow-sm rounded-4 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-secondary small fw-bold mb-1 text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Total Pendapatan Bersih</p>
                        <h3 class="fw-bold text-success mb-0" style="font-size: 1.6rem; letter-spacing: -0.5px;">
                            Rp {{ number_format($total_cuan, 0, ',', '.') }}
                        </h3>
                    </div>
                    <div class="icon-shape text-white shadow-sm" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                        <i class="bi bi-wallet2 fs-5"></i>
                    </div>
                </div>
                <div class="mt-3 pt-2 border-top border-light d-flex align-items-center text-muted" style="font-size: 0.75rem;">
                    Berdasarkan <strong class="text-dark mx-1">{{ $total_trx }}</strong> transaksi berstatus lunas (PAID).
                </div>
            </div>
        </div>

        {{-- Data Tim Ditemukan --}}
        <div class="col-md-6">
            <div class="card card-stats border-0 p-4 bg-white shadow-sm rounded-4 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-secondary small fw-bold mb-1 text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Data Tim Ditemukan</p>
                        <h3 class="fw-bold text-dark mb-0" style="font-size: 1.6rem; letter-spacing: -0.5px;">
                            {{ $total_trx }} <span class="fs-6 text-muted fw-normal">Tim</span>
                        </h3>
                    </div>
                    <div class="icon-shape text-white shadow-sm" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                        <i class="bi bi-people fs-5"></i>
                    </div>
                </div>
                <div class="mt-3 pt-2 border-top border-light d-flex align-items-center text-muted" style="font-size: 0.75rem;">
                    Data terfilter sesuai dengan kriteria pencarian Anda.
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Log --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                    <thead class="bg-light">
                        <tr class="fw-bold text-secondary text-uppercase" style="font-size: 0.75rem; border-bottom: 2px solid #f1f5f9;">
                            <th class="ps-4 py-3 text-center border-0" width="60">#</th>
                            <th class="py-3 border-0">Waktu Lunas & ID</th>
                            <th class="py-3 border-0">Nama Tim & Season</th>
                            <th class="py-3 border-0">Metode</th>
                            <th class="py-3 text-center border-0">Status</th>
                            <th class="py-3 text-end pe-4 border-0" width="180">Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $index => $pay)
                        <tr style="border-bottom: 1px solid #f8fafc;">
                            <td class="ps-4 text-center text-muted fw-semibold">
                                {{ ($payments->currentPage() - 1) * $payments->perPage() + $loop->iteration }}
                            </td>
                            <td class="py-3">
                                <div class="fw-bold text-dark">{{ $pay->updated_at->format('d/m/y H:i') }} WIB</div>
                                <div class="text-muted small" style="font-size: 0.75rem;">TRX: <span class="fw-semibold">{{ $pay->trx_id }}</span></div>
                                <div class="text-primary small" style="font-size: 0.7rem;">Ref: {{ $pay->tripay_reference ?? '-' }}</div>
                            </td>
                            <td>
                                <div class="fw-bold text-dark text-uppercase">{{ $pay->name }}</div>
                                <span class="badge bg-secondary text-white" style="font-size: 0.65rem;">{{ $pay->season->name }}</span>
                            </td>
                            <td>
                                <span class="badge bg-info text-dark text-uppercase fw-bold" style="font-size: 0.65rem;">
                                    {{ $pay->payment_method ?? 'QRIS/VA' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success text-white px-3 py-2 rounded-pill" style="font-size: 0.65rem; font-weight: 600;">
                                    <i class="bi bi-check-circle-fill me-1"></i> PAID
                                </span>
                            </td>
                            {{-- Bug Fix Applied: Show actual dynamic amount paid/fee rather than hardcoded 10.000 --}}
                            <td class="text-end fw-bold text-success pe-4" style="font-size: 0.95rem;">
                                Rp {{ number_format($pay->amount ?? $pay->season->price, 0, ',', '.') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <div class="py-4">
                                    <i class="bi bi-search fs-1 d-block mb-3 opacity-25"></i>
                                    <p class="mb-0 fw-bold">Tidak ada data pembayaran ditemukan.</p>
                                    <small>Coba sesuaikan filter tanggal atau klik tombol sinkronisasi.</small>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        {{-- Pagination --}}
        @if($payments->hasPages())
        <div class="card-footer bg-white border-0 py-3 d-flex justify-content-center">
            {{ $payments->links('pagination::bootstrap-5') }}
        </div>
        @endif
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
    .icon-shape {
        width: 44px;
        height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
    }
</style>
@endsection