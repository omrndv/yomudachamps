@extends('layouts.admin')

@section('content')
<div class="container-fluid px-4 py-4">
    <!-- Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <h4 class="fw-bold mb-1 text-slate-800">Setelan &amp; Transaksi Pembayaran Manual</h4>
            <p class="text-secondary small mb-0">Kelola QRIS statis, biaya admin, nominal unik, serta saldo masuk dalam satu dashboard terpadu.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('qris.verify-payments') }}" class="btn btn-warning fw-bold d-flex align-items-center gap-2 shadow-sm" style="border-radius: 12px; padding: 10px 18px;">
                <i class="bi bi-phone-fill fs-5"></i> Buka Verifikator Mobile (PWA)
            </a>
        </div>
    </div>

    <!-- Stats Bar & Quick Config -->
    <div class="row g-4 mb-4">
        <!-- Balance Card -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); border-radius: 20px;">
                <div class="card-body p-4 text-white d-flex flex-column justify-content-between">
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="small text-white-50 fw-semibold text-uppercase tracking-wider">Total Pendapatan Terverifikasi</span>
                            <div class="p-2 bg-warning bg-opacity-10 rounded-3 text-warning">
                                <i class="bi bi-wallet2 fs-4"></i>
                            </div>
                        </div>
                        <h2 class="fw-black mb-1" style="font-size: 2.2rem; letter-spacing: -1px;">
                            Rp {{ number_format($totalBalance, 0, ',', '.') }}
                        </h2>
                        <p class="text-white-50 small mb-0">Dari total transaksi manual berstatus lunas (PAID).</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Configuration Settings Form -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm" style="border-radius: 20px;">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3 d-flex align-items-center gap-2">
                        <i class="bi bi-gear-fill text-warning"></i> Pengaturan Pembayaran Manual
                    </h5>
                    
                    <form action="{{ route('admin.manual-payment.settings') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row g-3">
                            <!-- Toggle Manual Payment -->
                            <div class="col-12 col-md-6 d-flex align-items-center">
                                <div class="form-check form-switch p-0">
                                    <label class="form-check-label fw-bold d-block mb-1" for="enabled">Status Gerbang Pembayaran</label>
                                    <input class="form-check-input ms-0" type="checkbox" name="enabled" id="enabled" style="width: 50px; height: 26px;" {{ $settings['enabled'] ? 'checked' : '' }}>
                                    <span class="small text-secondary d-block mt-1">Aktifkan untuk menampilkan QRIS statis / transfer manual kepada peserta.</span>
                                </div>
                            </div>

                            <!-- Admin Fee -->
                            <div class="col-12 col-md-6">
                                <label for="admin_fee" class="form-label fw-bold mb-1">Biaya Admin (Rp)</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">Rp</span>
                                    <input type="number" class="form-control" id="admin_fee" name="admin_fee" value="{{ $settings['admin_fee'] }}" required min="0">
                                </div>
                                <span class="small text-secondary d-block mt-1">Biaya admin tambahan per pendaftaran.</span>
                            </div>

                            <!-- Unique Code Min -->
                            <div class="col-12 col-md-6 col-lg-3">
                                <label for="unique_min" class="form-label fw-bold mb-1">Kode Unik Min</label>
                                <input type="number" class="form-control" id="unique_min" name="unique_min" value="{{ $settings['unique_min'] }}" required min="0">
                            </div>

                            <!-- Unique Code Max -->
                            <div class="col-12 col-md-6 col-lg-3">
                                <label for="unique_max" class="form-label fw-bold mb-1">Kode Unik Max</label>
                                <input type="number" class="form-control" id="unique_max" name="unique_max" value="{{ $settings['unique_max'] }}" required min="0">
                            </div>

                            <!-- QRIS Statis File Upload -->
                            <div class="col-12 col-md-6 col-lg-6">
                                <label for="qris_image_file" class="form-label fw-bold mb-1">Unggah Gambar QRIS Statis</label>
                                <input type="file" class="form-control" id="qris_image_file" name="qris_image_file" accept="image/*">
                            </div>
                        </div>

                        <!-- Current QRIS Preview -->
                        @if($settings['qris_image'])
                        <div class="mt-3 p-3 bg-light rounded-4 d-flex align-items-center gap-3">
                            <img src="{{ $settings['qris_image'] }}" alt="QRIS Statis" style="width: 50px; height: 50px; object-fit: contain; border-radius: 8px; border: 1px solid rgba(0,0,0,0.1);">
                            <div>
                                <span class="small fw-bold d-block">QRIS Statis Aktif</span>
                                <a href="{{ $settings['qris_image'] }}" target="_blank" class="small text-warning fw-bold text-decoration-none">Lihat Gambar Asli <i class="bi bi-box-arrow-up-right"></i></a>
                            </div>
                        </div>
                        @endif

                        <div class="text-end mt-4">
                            <button type="submit" class="btn btn-warning fw-bold px-4" style="border-radius: 10px;">
                                <i class="bi bi-save2-fill"></i> Simpan Konfigurasi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Riwayat Transaksi Manual -->
    <div class="card border-0 shadow-sm" style="border-radius: 20px;">
        <div class="card-body p-4">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
                <h5 class="fw-bold mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-list-stars text-warning"></i> Riwayat Pembayaran Manual
                </h5>
                <form action="{{ route('admin.manual-payment') }}" method="GET" class="d-flex gap-2 w-100 w-md-auto">
                    <input type="text" name="search" class="form-control form-control-sm px-3 py-2" placeholder="Cari TRX ID / nama tim..." value="{{ request('search') }}" style="border-radius: 10px; width: 100%; min-width: 240px;">
                    <button type="submit" class="btn btn-warning btn-sm fw-bold px-3" style="border-radius: 10px;">Cari</button>
                </form>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="py-3 px-4">TRX ID</th>
                            <th class="py-3">Tim</th>
                            <th class="py-3">Season</th>
                            <th class="py-3 text-end">Jumlah</th>
                            <th class="py-3 text-center">Status</th>
                            <th class="py-3 text-center">Bukti Transfer</th>
                            <th class="py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $tx)
                        <tr>
                            <td class="py-3 px-4 font-monospace small fw-bold">{{ $tx->trx_id }}</td>
                            <td class="py-3">
                                <span class="fw-semibold d-block text-slate-800">{{ $tx->team ? $tx->team->name : 'N/A' }}</span>
                                @if($tx->team)
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $tx->team->wa_number) }}" target="_blank" class="small text-success text-decoration-none fw-semibold">
                                    <i class="bi bi-whatsapp"></i> {{ $tx->team->wa_number }}
                                </a>
                                @endif
                            </td>
                            <td class="py-3 small text-secondary">{{ ($tx->team && $tx->team->season) ? $tx->team->season->name : 'N/A' }}</td>
                            <td class="py-3 text-end font-monospace fw-bold">Rp {{ number_format($tx->amount, 0, ',', '.') }}</td>
                            <td class="py-3 text-center">
                                @if($tx->status === 'PAID')
                                    <span class="badge bg-success bg-opacity-10 text-success px-3 py-1.5 rounded-pill fw-bold">PAID</span>
                                @elseif($tx->status === 'CLAIMED')
                                    <span class="badge bg-warning bg-opacity-10 text-warning px-3 py-1.5 rounded-pill fw-bold">CLAIMED</span>
                                @elseif($tx->status === 'PENDING')
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary px-3 py-1.5 rounded-pill fw-bold">PENDING</span>
                                @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-1.5 rounded-pill fw-bold">EXPIRED</span>
                                @endif
                            </td>
                            <td class="py-3 text-center">
                                @if($tx->gopay_reference && str_starts_with($tx->gopay_reference, 'PROOFS/'))
                                    @php
                                        $filename = str_replace('PROOFS/', '', $tx->gopay_reference);
                                    @endphp
                                    <a href="{{ asset('uploads/proofs/' . $filename) }}" target="_blank" class="btn btn-sm btn-outline-warning fw-bold px-3 py-1" style="border-radius: 8px;">
                                        <i class="bi bi-image"></i> Lihat Bukti
                                    </a>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td class="py-3 text-center">
                                @if($tx->status === 'CLAIMED' || $tx->status === 'PENDING' || $tx->status === 'EXPIRED')
                                <div class="d-inline-flex gap-1">
                                    <form action="{{ route('qris.settle', $tx->trx_id) }}" method="POST" onsubmit="return confirm('Setujui pembayaran tim ini?')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success px-2.5 py-1" title="Setujui Pembayaran" style="border-radius: 8px;">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                    </form>
                                    @if($tx->status === 'CLAIMED')
                                    <form action="{{ route('qris.reject', $tx->trx_id) }}" method="POST" onsubmit="return confirm('Tolak bukti transfer tim ini?')">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-danger px-2.5 py-1" title="Tolak Bukti" style="border-radius: 8px;">
                                            <i class="bi bi-x-lg"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-secondary">
                                <div class="fs-1 text-muted mb-2">📥</div>
                                <p class="mb-0 small">Belum ada transaksi pembayaran manual.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4 d-flex justify-content-center">
                {{ $transactions->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
