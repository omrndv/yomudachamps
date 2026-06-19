@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4" style="background-color: #f8fafc; min-height: 100vh;">
    {{-- Header --}}
    <div class="row mb-4 align-items-center">
        <div class="col-md-12">
            <h2 class="fw-bold text-dark mb-1" style="font-size: 1.75rem; letter-spacing: -0.5px;">
                Log Aktivitas Admin
            </h2>
            <p class="text-secondary mb-0" style="font-size: 0.9rem;">
                Catatan audit aktivitas, pelacakan IP address, sistem operasi, browser, dan waktu perubahan pada sistem.
            </p>
        </div>
    </div>

    {{-- Tabs Filter --}}
    <div class="d-flex flex-wrap gap-2 mb-4 bg-white p-2 rounded-4 shadow-sm border border-light-subtle">
        <a href="{{ route('admin.activity-log', ['type' => 'login']) }}" class="btn btn-sm rounded-3 px-4 py-2.5 fw-bold transition-all {{ $type === 'login' ? 'btn-warning text-dark shadow-sm' : 'btn-light text-secondary border-0 bg-transparent' }}">
            <i class="bi bi-box-arrow-in-right me-1"></i> Log Login & Logout
        </a>
        <a href="{{ route('admin.activity-log', ['type' => 'tambah']) }}" class="btn btn-sm rounded-3 px-4 py-2.5 fw-bold transition-all {{ $type === 'tambah' ? 'btn-warning text-dark shadow-sm' : 'btn-light text-secondary border-0 bg-transparent' }}">
            <i class="bi bi-plus-circle me-1"></i> Log Tambah Data
        </a>
        <a href="{{ route('admin.activity-log', ['type' => 'ubah']) }}" class="btn btn-sm rounded-3 px-4 py-2.5 fw-bold transition-all {{ $type === 'ubah' ? 'btn-warning text-dark shadow-sm' : 'btn-light text-secondary border-0 bg-transparent' }}">
            <i class="bi bi-pencil-square me-1"></i> Log Ubah Data
        </a>
        <a href="{{ route('admin.activity-log', ['type' => 'hapus']) }}" class="btn btn-sm rounded-3 px-4 py-2.5 fw-bold transition-all {{ $type === 'hapus' ? 'btn-warning text-dark shadow-sm' : 'btn-light text-secondary border-0 bg-transparent' }}">
            <i class="bi bi-trash3 me-1"></i> Log Hapus Data
        </a>
        <a href="{{ route('admin.activity-log', ['type' => 'semua']) }}" class="btn btn-sm rounded-3 px-4 py-2.5 fw-bold transition-all {{ $type === 'semua' ? 'btn-warning text-dark shadow-sm' : 'btn-light text-secondary border-0 bg-transparent' }}">
            <i class="bi bi-list-stars me-1"></i> Semua Aktivitas
        </a>
    </div>

    {{-- Tabel Log --}}
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                    <thead class="bg-light text-secondary text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.8px;">
                        <tr>
                            <th class="py-3 px-4" style="width: 180px;">Waktu</th>
                            <th class="py-3 px-3" style="width: 130px;">Pengguna</th>
                            <th class="py-3 px-3">Aktivitas</th>
                            <th class="py-3 px-3" style="width: 150px;">IP Address</th>
                            <th class="py-3 px-4" style="width: 250px;">Device & Browser</th>
                        </tr>
                    </thead>
                    <tbody class="text-dark">
                        @forelse($activities as $activity)
                            <tr>
                                <td class="py-3 px-4 text-secondary">
                                    <i class="bi bi-clock me-1"></i>
                                    {{ $activity->created_at->format('d M Y, H:i:s') }}
                                </td>
                                <td class="py-3 px-3">
                                    <span class="badge bg-light text-dark border border-light-subtle rounded-pill px-2.5 py-1.5 fw-bold" style="font-size: 0.75rem;">
                                        <i class="bi bi-person-circle text-primary me-1"></i>
                                        {{ $activity->user ? $activity->user->username : 'System/Guest' }}
                                    </span>
                                </td>
                                <td class="py-3 px-3">
                                    @php
                                        $isDanger = str_contains(strtolower($activity->activity), 'hapus') || str_contains(strtolower($activity->activity), 'gagal');
                                        $isAdd = str_contains(strtolower($activity->activity), 'tambah') || str_contains(strtolower($activity->activity), 'membuat') || str_contains(strtolower($activity->activity), 'store') || str_contains(strtolower($activity->activity), 'create');
                                        $isSuccess = !$isAdd && (str_contains(strtolower($activity->activity), 'berhasil') || str_contains(strtolower($activity->activity), 'sukses'));
                                        $isWarning = str_contains(strtolower($activity->activity), 'ubah') || str_contains(strtolower($activity->activity), 'perbarui') || str_contains(strtolower($activity->activity), 'pengaturan') || str_contains(strtolower($activity->activity), 'edit');
                                    @endphp
                                    <div class="d-flex align-items-center gap-2">
                                        @if($isDanger)
                                            <span class="badge bg-danger text-white rounded-2 px-2.5 py-1.5 fw-bold text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.5px; min-width: 70px; text-align: center;">Hapus</span>
                                            <span class="text-dark fw-semibold">{{ $activity->activity }}</span>
                                        @elseif($isAdd)
                                            <span class="badge bg-success text-white rounded-2 px-2.5 py-1.5 fw-bold text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.5px; min-width: 70px; text-align: center;">Tambah</span>
                                            <span class="text-dark fw-semibold">{{ $activity->activity }}</span>
                                        @elseif($isSuccess)
                                            <span class="badge bg-success text-white rounded-2 px-2.5 py-1.5 fw-bold text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.5px; min-width: 70px; text-align: center;">Sukses</span>
                                            <span class="text-dark fw-semibold">{{ $activity->activity }}</span>
                                        @elseif($isWarning)
                                            <span class="badge bg-warning text-dark rounded-2 px-2.5 py-1.5 fw-bold text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.5px; min-width: 70px; text-align: center;">Ubah</span>
                                            <span class="text-dark fw-semibold">{{ $activity->activity }}</span>
                                        @else
                                            <span class="badge bg-info text-dark rounded-2 px-2.5 py-1.5 fw-bold text-uppercase" style="font-size: 0.68rem; letter-spacing: 0.5px; min-width: 70px; text-align: center;">Info</span>
                                            <span class="text-dark fw-semibold">{{ $activity->activity }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3 px-3 text-secondary font-monospace" style="font-size: 0.8rem;">
                                    <i class="bi bi-geo-alt me-1 text-muted"></i>
                                    {{ $activity->ip_address ?? '-' }}
                                </td>
                                <td class="py-3 px-4 text-secondary">
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
        </div>
        
        {{-- Pagination --}}
        @if($activities->hasPages())
            <div class="card-footer bg-white border-0 py-3 px-4 border-top border-light">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-secondary small">
                        Showing {{ $activities->firstItem() }} to {{ $activities->lastItem() }} of {{ $activities->total() }} entries
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
