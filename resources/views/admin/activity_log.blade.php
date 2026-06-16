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
                                <td class="py-3 px-3 fw-semibold">
                                    @php
                                        $isDanger = str_contains(strtolower($activity->activity), 'hapus') || str_contains(strtolower($activity->activity), 'gagal');
                                        $isSuccess = str_contains(strtolower($activity->activity), 'berhasil') || str_contains(strtolower($activity->activity), 'membuat') || str_contains(strtolower($activity->activity), 'tambah');
                                        $isWarning = str_contains(strtolower($activity->activity), 'ubah') || str_contains(strtolower($activity->activity), 'perbarui') || str_contains(strtolower($activity->activity), 'pengaturan');
                                    @endphp
                                    @if($isDanger)
                                        <span class="text-danger"><i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $activity->activity }}</span>
                                    @elseif($isSuccess)
                                        <span class="text-success"><i class="bi bi-check-circle-fill me-1"></i> {{ $activity->activity }}</span>
                                    @elseif($isWarning)
                                        <span class="text-warning-emphasis"><i class="bi bi-pencil-square me-1"></i> {{ $activity->activity }}</span>
                                    @else
                                        <span><i class="bi bi-info-circle-fill text-info me-1"></i> {{ $activity->activity }}</span>
                                    @endif
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
