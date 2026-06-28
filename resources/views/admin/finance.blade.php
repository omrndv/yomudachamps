@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4" style="background-color: #f8fafc; min-height: 100vh;">
    {{-- Validation and Feedback --}}
    @if ($errors->any())
        <div class="alert alert-danger py-2 small border-0 mb-3 rounded-3 shadow-sm">
            @foreach ($errors->all() as $error)
                <li class="list-unstyled"><i class="bi bi-exclamation-triangle-fill me-2"></i>{{ $error }}</li>
            @endforeach
        </div>
    @endif
    @if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                background: '#ffffff',
                color: '#1e293b',
            });
            Toast.fire({
                icon: 'success',
                title: "{{ session('success') }}"
            });
        });
    </script>
    @endif

    {{-- Breadcrumb & Header --}}
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2" style="font-size: 0.85rem;">
                    <li class="breadcrumb-item"><a href="{{ route('admin.seasons') }}" class="text-decoration-none text-warning fw-semibold">Daftar Season</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard', $current_season->id) }}" class="text-decoration-none text-warning fw-semibold">{{ $current_season->name }}</a></li>
                    <li class="breadcrumb-item active text-secondary" aria-current="page">Laporan & Rekap Keuangan</li>
                </ol>
            </nav>
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h2 class="fw-bold text-dark m-0" style="font-size: 1.75rem; letter-spacing: -0.5px;">
                        Rekap Keuangan <span class="text-warning">{{ $current_season->name }}</span>
                    </h2>
                    <p class="text-secondary small mb-0 mt-1">Kelola pemasukan tambahan (sponsor, dll) dan pengeluaran operasional turnamen.</p>
                </div>
                <div>
                    <a href="{{ route('admin.dashboard', $current_season->id) }}" class="btn btn-outline-secondary btn-sm px-3 fw-bold rounded-pill shadow-sm me-2">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
                    </a>
                    <button class="btn btn-warning btn-sm px-3 fw-bold rounded-pill shadow-sm text-dark" data-bs-toggle="modal" data-bs-target="#modalAddFinance">
                        <i class="bi bi-plus-circle-fill me-1"></i> Tambah Transaksi
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="row g-4 mb-4">
        {{-- Card 1: Pendapatan Bersih --}}
        <div class="col-xl-3 col-md-6">
            <div class="card card-custom border-0 p-4 bg-white shadow-sm rounded-4 h-100 d-flex flex-column justify-content-between">
                <div class="d-flex justify-content-between mb-3">
                    <div>
                        <p class="text-secondary small fw-bold mb-1 text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Pendapatan Bersih (Total Akhir)</p>
                        <h3 class="fw-bold {{ $net_income >= 0 ? 'text-success' : 'text-danger' }} mb-0" style="font-size: 1.5rem; letter-spacing: -0.5px;">
                            Rp {{ number_format($net_income, 0, ',', '.') }}
                        </h3>
                    </div>
                    <div class="icon-shape {{ $net_income >= 0 ? 'text-success bg-success-subtle' : 'text-danger bg-danger-subtle' }}" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; border-radius: 12px;">
                        <i class="bi bi-wallet2 fs-5"></i>
                    </div>
                </div>
                <p class="small text-muted mb-0 mt-2" style="font-size: 0.7rem;">
                    Total bersih setelah dikurangi semua pengeluaran.
                </p>
            </div>
        </div>

        {{-- Card 2: Registrasi Gross --}}
        <div class="col-xl-3 col-md-6">
            <div class="card card-custom border-0 p-4 bg-white shadow-sm rounded-4 h-100 d-flex flex-column justify-content-between">
                <div class="d-flex justify-content-between mb-3">
                    <div>
                        <p class="text-secondary small fw-bold mb-1 text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Registrasi Peserta (Gross)</p>
                        <h3 class="fw-bold text-dark mb-0" style="font-size: 1.5rem; letter-spacing: -0.5px;">
                            Rp {{ number_format($total_income, 0, ',', '.') }}
                        </h3>
                    </div>
                    <div class="icon-shape text-primary bg-primary-subtle" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; border-radius: 12px;">
                        <i class="bi bi-people fs-5"></i>
                    </div>
                </div>
                <div class="row text-center mt-2 border-top border-light pt-2" style="font-size: 0.65rem;">
                    <div class="col-4 border-end px-1">
                        <span class="text-muted d-block" style="font-size: 0.62rem;" title="Otomatis via TriPay/Website">TriPay (Web)</span>
                        <strong class="text-dark d-block">Rp {{ number_format($tripay_income, 0, ',', '.') }}</strong>
                    </div>
                    <div class="col-4 border-end px-1">
                        <span class="text-muted d-block" style="font-size: 0.62rem;" title="Manual oleh Admin/Bulk Add">Manual/Bulk</span>
                        <strong class="text-dark d-block">Rp {{ number_format($manual_income, 0, ',', '.') }}</strong>
                    </div>
                    <div class="col-4 px-1">
                        <span class="text-muted d-block" style="font-size: 0.62rem;" title="Peserta Daftar Solo">Solo Player</span>
                        <strong class="text-dark d-block">Rp {{ number_format($solo_income, 0, ',', '.') }}</strong>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 3: Pemasukan Lain --}}
        <div class="col-xl-3 col-md-6">
            <div class="card card-custom border-0 p-4 bg-white shadow-sm rounded-4 h-100 d-flex flex-column justify-content-between">
                <div class="d-flex justify-content-between mb-3">
                    <div>
                        <p class="text-secondary small fw-bold mb-1 text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Pemasukan Tambahan</p>
                        <h3 class="fw-bold text-success mb-0" style="font-size: 1.5rem; letter-spacing: -0.5px;">
                            + Rp {{ number_format($additional_income, 0, ',', '.') }}
                        </h3>
                    </div>
                    <div class="icon-shape text-success bg-success-subtle" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; border-radius: 12px;">
                        <i class="bi bi-graph-up-arrow fs-5"></i>
                    </div>
                </div>
                <div class="row text-center mt-2 border-top border-light pt-2" style="font-size: 0.65rem;">
                    <div class="col-6 border-end px-1">
                        <span class="text-muted d-block" style="font-size: 0.62rem;" title="Jumlah slot YMD cadangan terjual">Slot YMD Terjual</span>
                        <strong class="text-warning d-block">{{ $ymd_slots_count }} Slot</strong>
                    </div>
                    <div class="col-6 px-1">
                        <span class="text-muted d-block" style="font-size: 0.62rem;" title="Total keuntungan dari penjualan slot YMD">Keuntungan YMD</span>
                        <strong class="text-success d-block">Rp {{ number_format($ymd_slots_income, 0, ',', '.') }}</strong>
                    </div>
                </div>
            </div>
        </div>

        {{-- Card 4: Total Pengeluaran --}}
        <div class="col-xl-3 col-md-6">
            <div class="card card-custom border-0 p-4 bg-white shadow-sm rounded-4 h-100 d-flex flex-column justify-content-between">
                <div class="d-flex justify-content-between mb-3">
                    <div>
                        <p class="text-secondary small fw-bold mb-1 text-uppercase" style="font-size: 0.7rem; letter-spacing: 0.5px;">Total Pengeluaran</p>
                        <h3 class="fw-bold text-danger mb-0" style="font-size: 1.5rem; letter-spacing: -0.5px;">
                            - Rp {{ number_format($total_expense, 0, ',', '.') }}
                        </h3>
                    </div>
                    <div class="icon-shape text-danger bg-danger-subtle" style="width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; border-radius: 12px;">
                        <i class="bi bi-graph-down-arrow fs-5"></i>
                    </div>
                </div>
                <p class="small text-muted mb-0 mt-2" style="font-size: 0.7rem;">
                    Biaya hadiah (prize pool), operasional, dan perlengkapan.
                </p>
            </div>
        </div>
    </div>

    {{-- Ledger Table Card --}}
    <div class="card border-0 shadow-sm p-4 rounded-4 bg-white mb-4">
        <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom">
            <div>
                <h5 class="fw-bold text-dark mb-1">
                    <i class="bi bi-journal-text text-warning me-2"></i> Rincian Arus Kas Turnamen
                </h5>
                <p class="text-secondary small mb-0">Daftar semua transaksi yang tercatat secara otomatis dari sistem maupun manual.</p>
            </div>
            <button class="btn btn-warning btn-sm px-3 fw-bold rounded-pill shadow-sm text-dark" data-bs-toggle="modal" data-bs-target="#modalAddFinance">
                <i class="bi bi-plus-circle-fill me-1"></i> Tambah Transaksi
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                <thead class="bg-light">
                    <tr class="text-secondary small fw-bold" style="font-size: 0.75rem;">
                        <th class="py-2.5 border-0">Tanggal</th>
                        <th class="py-2.5 border-0">Keterangan / Transaksi</th>
                        <th class="py-2.5 border-0 text-center">Tipe</th>
                        <th class="py-2.5 border-0 text-end">Jumlah</th>
                        <th class="py-2.5 border-0 text-center" width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Baris otomatis untuk Registrasi Tim & Solo --}}
                    @if($total_income > 0)
                    <tr style="border-bottom: 1px solid #f8fafc; background-color: rgba(25, 135, 84, 0.02);">
                        <td><span class="text-muted">-</span></td>
                        <td><span class="fw-bold text-dark">Total Pemasukan Registrasi (Otomatis)</span></td>
                        <td class="text-center">
                            <span class="badge bg-success-subtle text-success px-2.5 py-1 rounded-pill" style="font-size: 0.7rem;">PEMASUKAN</span>
                        </td>
                        <td class="text-end fw-bold text-success">
                            Rp {{ number_format($total_income, 0, ',', '.') }}
                        </td>
                        <td class="text-center">
                            <span class="text-muted small italic" style="font-size: 0.7rem;">Sistem</span>
                        </td>
                    </tr>
                    @endif

                    @forelse($finances as $finance)
                    <tr style="border-bottom: 1px solid #f8fafc;">
                        <td>
                            <span class="text-dark small fw-semibold">
                                {{ $finance->date ? date('d M Y', strtotime($finance->date)) : date('d M Y', strtotime($finance->created_at)) }}
                            </span>
                        </td>
                        <td>
                            <span class="text-dark fw-bold">{{ $finance->title }}</span>
                        </td>
                        <td class="text-center">
                            @if($finance->type === 'INCOME')
                                <span class="badge bg-success-subtle text-success px-2.5 py-1 rounded-pill" style="font-size: 0.7rem;">PEMASUKAN</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger px-2.5 py-1 rounded-pill" style="font-size: 0.7rem;">PENGELUARAN</span>
                            @endif
                        </td>
                        <td class="text-end fw-bold {{ $finance->type === 'INCOME' ? 'text-success' : 'text-danger' }}">
                            {{ $finance->type === 'INCOME' ? '+' : '-' }} Rp {{ number_format($finance->amount, 0, ',', '.') }}
                        </td>
                        <td class="text-center">
                            <form action="{{ route('admin.season.finance.delete', [$current_season->id, $finance->id]) }}" method="POST" onsubmit="return confirm('Hapus catatan transaksi ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-link text-danger p-0 m-0" style="font-size: 0.75rem; text-decoration: none;">
                                    <i class="bi bi-trash"></i> Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                        @if($total_income == 0)
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted small">
                                <i class="bi bi-wallet-fill d-block fs-3 mb-2 text-secondary opacity-50"></i>
                                Belum ada catatan transaksi keuangan untuk season ini.
                            </td>
                        </tr>
                        @endif
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ADD FINANCE MODAL --}}
<div class="modal fade" id="modalAddFinance" tabindex="-1" aria-hidden="true" style="z-index: 1055;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-bottom border-light p-3">
                <h5 class="fw-bold text-dark mb-0">
                    <i class="bi bi-plus-circle text-warning me-2"></i>Tambah Transaksi Keuangan
                </h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.season.finance.store', $current_season->id) }}" method="POST">
                @csrf
                <div class="modal-body p-4 text-start">
                    <div class="mb-3">
                        <label class="small fw-bold text-secondary mb-1">Tipe Transaksi</label>
                        <select name="type" class="form-select rounded-3 shadow-none border-light-subtle" required>
                            <option value="INCOME">Pemasukan Tambahan (Sponsor, donasi, dll)</option>
                            <option value="EXPENSE">Pengeluaran (Hadiah, operasional, dll)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold text-secondary mb-1">Keterangan / Nama Transaksi</label>
                        <input type="text" name="title" class="form-control rounded-3 shadow-none border-light-subtle" placeholder="Contoh: Sponsor dari Brand X, Beli Trophy" required>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold text-secondary mb-1">Jumlah (Rupiah)</label>
                        <div class="input-group">
                            <span class="input-group-text rounded-start-3 bg-light border-light-subtle text-secondary fw-bold">Rp</span>
                            <input type="number" name="amount" class="form-control rounded-end-3 shadow-none border-light-subtle" placeholder="Contoh: 500000" min="0" required>
                        </div>
                    </div>
                    <div class="mb-0">
                        <label class="small fw-bold text-secondary mb-1">Tanggal</label>
                        <input type="date" name="date" class="form-control rounded-3 shadow-none border-light-subtle" value="{{ date('Y-m-d') }}">
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 pt-0">
                    <button type="button" class="btn btn-light fw-bold px-4" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning fw-bold px-4 text-dark shadow-sm">Simpan Transaksi</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Custom Style --}}
<style>
    .card-custom {
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.01);
    }
</style>
@endsection
