@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4" style="background-color: #f8fafc; min-height: 100vh;">
<style>
    /* Premium Page styling */
    .log-card {
        background: #ffffff;
        border: 1px solid rgba(0,0,0,0.06);
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02);
        overflow: hidden;
    }
    .log-table th {
        font-size: 0.72rem;
        letter-spacing: 0.8px;
        font-weight: 700;
        text-transform: uppercase;
        color: #64748b;
        background-color: #f8fafc;
        border-bottom: 1px solid #edf2f7;
        padding: 14px 16px;
    }
    .log-table td {
        padding: 14px 16px;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.85rem;
    }
    .log-table tr:last-child td {
        border-bottom: 0;
    }
    
    /* Tabs Filter Styling mimicking Settings Page */
    .log-tabs {
        display: flex;
        gap: 4px;
        background: #f1f5f9;
        border-radius: 14px;
        padding: 4px;
        overflow-x: auto;
        scrollbar-width: none;
    }
    .log-tabs::-webkit-scrollbar { display: none; }
    .log-tab {
        padding: 10px 18px;
        border-radius: 10px;
        font-size: 0.82rem;
        font-weight: 600;
        color: #64748b;
        background: transparent;
        border: none;
        white-space: nowrap;
        text-decoration: none !important;
        transition: all 0.15s ease;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .log-tab:hover {
        color: #334155;
        background: rgba(255,255,255,0.6);
    }
    .log-tab.active {
        background: #ffffff;
        color: #0f172a;
        box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    }
    
    /* Subtle Soft Badges */
    .badge-soft {
        font-size: 0.68rem;
        font-weight: 700;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        padding: 6px 12px;
        border-radius: 8px;
        min-width: 75px;
        text-align: center;
        display: inline-block;
    }
    .badge-soft-danger {
        background-color: #fef2f2;
        color: #dc2626;
        border: 1px solid #fee2e2;
    }
    .badge-soft-success {
        background-color: #f0fdf4;
        color: #16a34a;
        border: 1px solid #d1fae5;
    }
    .badge-soft-warning {
        background-color: #fffbeb;
        color: #d97706;
        border: 1px solid #fef3c7;
    }
    .badge-soft-info {
        background-color: #f0fdfa;
        color: #0d9488;
        border: 1px solid #ccfbf1;
    }
    .badge-soft-secondary {
        background-color: #f8fafc;
        color: #475569;
        border: 1px solid #e2e8f0;
    }
</style>

    {{-- Header --}}
    <div class="row align-items-center mb-4">
        <div class="col-12 col-md-9 mb-3 mb-md-0">
            <h2 class="fw-bold text-dark mb-1" style="font-size: 1.6rem; letter-spacing: -0.5px;">
                Log Aktivitas Admin
            </h2>
            <p class="text-secondary mb-0" style="font-size: 0.85rem;">
                Audit log aktivitas, pelacakan IP address, sistem operasi, browser, dan waktu perubahan sistem.
            </p>
        </div>
        <div class="col-12 col-md-3 text-md-end">
            <form action="{{ route('admin.activity-log.clear') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus seluruh log aktivitas secara permanen? Tindakan ini tidak dapat dibatalkan.');" class="d-inline-block w-100">
                @csrf
                <button type="submit" class="btn btn-outline-danger fw-semibold px-4 py-2.5 rounded-pill w-100 d-inline-flex align-items-center justify-content-center gap-2" style="font-size: 0.82rem; border-width: 1.5px;">
                    <i class="bi bi-trash3-fill"></i> Bersihkan Semua Log
                </button>
            </form>
        </div>
    </div>

    {{-- Tabs Filter --}}
    <div class="log-tabs mb-4">
        <a href="{{ route('admin.activity-log', ['type' => 'login']) }}" class="log-tab {{ $type === 'login' ? 'active' : '' }}">
            <i class="bi bi-box-arrow-in-right"></i> Log Login & Logout
        </a>
        <a href="{{ route('admin.activity-log', ['type' => 'tambah']) }}" class="log-tab {{ $type === 'tambah' ? 'active' : '' }}">
            <i class="bi bi-plus-circle"></i> Log Tambah Data
        </a>
        <a href="{{ route('admin.activity-log', ['type' => 'ubah']) }}" class="log-tab {{ $type === 'ubah' ? 'active' : '' }}">
            <i class="bi bi-pencil-square"></i> Log Ubah Data
        </a>
        <a href="{{ route('admin.activity-log', ['type' => 'hapus']) }}" class="log-tab {{ $type === 'hapus' ? 'active' : '' }}">
            <i class="bi bi-trash3"></i> Log Hapus Data
        </a>
        <a href="{{ route('admin.activity-log', ['type' => 'semua']) }}" class="log-tab {{ $type === 'semua' ? 'active' : '' }}">
            <i class="bi bi-list-stars"></i> Semua Aktivitas
        </a>
    </div>

    {{-- Tabel Log --}}
    <div class="log-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 log-table">
                <thead>
                    <tr>
                        <th style="width: 190px;">Waktu</th>
                        <th style="width: 150px;">Pengguna</th>
                        <th>Aktivitas</th>
                        <th style="width: 150px;">IP Address</th>
                        <th style="width: 250px;">Device & Browser</th>
                    </tr>
                </thead>
                <tbody class="text-dark">
                    @forelse($activities as $activity)
                        <tr>
                            <td class="text-secondary">
                                <i class="bi bi-calendar3 me-1.5 text-muted"></i>
                                {{ $activity->created_at->format('d M Y, H:i:s') }}
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border border-light-subtle rounded-pill px-2.5 py-1.5 fw-bold" style="font-size: 0.75rem;">
                                    <i class="bi bi-person-circle text-warning me-1"></i>
                                    {{ $activity->user ? $activity->user->username : 'System/Guest' }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $isDanger = str_contains(strtolower($activity->activity), 'hapus') || str_contains(strtolower($activity->activity), 'gagal');
                                    $isAdd = str_contains(strtolower($activity->activity), 'tambah') || str_contains(strtolower($activity->activity), 'membuat') || str_contains(strtolower($activity->activity), 'store') || str_contains(strtolower($activity->activity), 'create') || str_contains(strtolower($activity->activity), 'import');
                                    $isSuccess = !$isAdd && (str_contains(strtolower($activity->activity), 'berhasil') || str_contains(strtolower($activity->activity), 'sukses'));
                                    $isWarning = str_contains(strtolower($activity->activity), 'ubah') || str_contains(strtolower($activity->activity), 'perbarui') || str_contains(strtolower($activity->activity), 'pengaturan') || str_contains(strtolower($activity->activity), 'edit');
                                @endphp
                                <div class="d-flex align-items-center gap-2.5">
                                    @if($isDanger)
                                        <span class="badge-soft badge-soft-danger">Hapus</span>
                                        <span class="text-dark fw-semibold" style="font-size: 0.88rem;">{{ $activity->activity }}</span>
                                    @elseif($isAdd)
                                        <span class="badge-soft badge-soft-success">Tambah</span>
                                        <span class="text-dark fw-semibold" style="font-size: 0.88rem;">{{ $activity->activity }}</span>
                                    @elseif($isSuccess)
                                        <span class="badge-soft badge-soft-success">Sukses</span>
                                        <span class="text-dark fw-semibold" style="font-size: 0.88rem;">{{ $activity->activity }}</span>
                                    @elseif($isWarning)
                                        <span class="badge-soft badge-soft-warning">Ubah</span>
                                        <span class="text-dark fw-semibold" style="font-size: 0.88rem;">{{ $activity->activity }}</span>
                                    @else
                                        <span class="badge-soft badge-soft-info">Info</span>
                                        <span class="text-dark fw-semibold" style="font-size: 0.88rem;">{{ $activity->activity }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="text-secondary font-monospace" style="font-size: 0.78rem;">
                                <i class="bi bi-geo-alt me-1 text-muted"></i>
                                {{ $activity->ip_address ?? '-' }}
                            </td>
                            <td class="text-secondary" style="font-size: 0.8rem;">
                                @if(str_contains(strtolower($activity->device), 'mac'))
                                    <i class="bi bi-apple text-dark me-1" title="Mac OS"></i>
                                @elseif(str_contains(strtolower($activity->device), 'windows'))
                                    <i class="bi bi-windows text-info me-1" title="Windows OS"></i>
                                @elseif(str_contains(strtolower($activity->device), 'android'))
                                    <i class="bi bi-android text-success me-1" title="Android"></i>
                                @elseif(str_contains(strtolower($activity->device), 'ios') || str_contains(strtolower($activity->device), 'iphone'))
                                    <i class="bi bi-phone-fill text-dark me-1" title="iOS Device"></i>
                                @else
                                    <i class="bi bi-laptop me-1 text-muted"></i>
                                @endif
                                {{ $activity->device ?? 'Unknown Device' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-5 text-center text-secondary">
                                <div class="py-4">
                                    <i class="bi bi-journal-x fs-1 text-muted mb-3 d-block"></i>
                                    Belum ada data log aktivitas terekam.
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        {{-- Pagination --}}
        @if($activities->hasPages())
            <div class="card-footer bg-white border-0 py-3.5 px-4 border-top border-light">
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2">
                    <div class="text-secondary small">
                        Menampilkan {{ $activities->firstItem() }} sampai {{ $activities->lastItem() }} dari {{ $activities->total() }} entri log
                    </div>
                    <div>
                        {{ $activities->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
