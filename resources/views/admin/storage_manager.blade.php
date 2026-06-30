@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4" style="background-color: #f8fafc; min-height: 100vh;">
    {{-- Header --}}
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="fw-bold text-dark m-0" style="font-size: 1.75rem; letter-spacing: -0.5px;">
                Manajemen Penyimpanan & Media <i class="bi bi-hdd-fill text-warning ms-1"></i>
            </h2>
            <p class="text-secondary small mb-0 mt-1">
                Pantau kapasitas server, bersihkan berkas chat usang, atau hapus bukti tanding terdahulu untuk meringankan beban hosting Anda.
            </p>
        </div>
    </div>

    {{-- Capacity Card --}}
    @php
        function formatBytes($bytes, $precision = 2) {
            $units = ['B', 'KB', 'MB', 'GB', 'TB'];
            $bytes = max($bytes, 0);
            $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
            $pow = min($pow, count($units) - 1);
            $bytes /= pow(1024, $pow);
            return round($bytes, $precision) . ' ' . $units[$pow];
        }
    @endphp
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 text-white" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <span class="text-white-50 text-uppercase fw-bold small" style="font-size: 0.65rem; letter-spacing: 1px;">Total Ruang Digunakan Oleh Media</span>
                            <h1 class="fw-bold text-white mt-1 mb-2" style="font-size: 2.5rem;">
                                {{ formatBytes($totalSystemSize) }}
                            </h1>
                            <p class="text-white-50 small mb-0">
                                Mencakup folder <code class="text-warning">chat_uploads</code>, <code class="text-warning">match_results</code>, dan poster-poster season aktif.
                            </p>
                        </div>
                        <div class="col-md-4 text-md-end mt-3 mt-md-0">
                            <i class="bi bi-cloud-slash text-warning" style="font-size: 4rem; opacity: 0.25;"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Folder Managers Grid --}}
    <div class="row g-4">
        @foreach($storageData as $key => $folder)
            <div class="col-lg-4 col-md-6">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="fw-bold text-dark mb-0">{{ $folder['name'] }}</h5>
                            <code class="text-secondary small" style="font-size: 0.7rem;">/public/{{ $key === 'posters' ? 'storage/posters' : ($key === 'certificates' ? 'uploads/certificates' : $key) }}</code>
                        </div>
                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-2.5 py-1" style="font-size: 0.7rem; font-weight: 700;">
                            {{ $folder['files_count'] }} File
                        </span>
                    </div>

                    <div class="card-body px-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="text-secondary small">Kapasitas Folder:</span>
                            <span class="fw-bold text-dark">{{ formatBytes($folder['total_size']) }}</span>
                        </div>

                        {{-- File List Container --}}
                        <div class="border rounded-4 bg-light p-2 mb-3" style="max-height: 250px; overflow-y: auto;">
                            @if(count($folder['files']) > 0)
                                <div class="list-group list-group-flush" style="font-size: 0.8rem;">
                                    @foreach($folder['files'] as $file)
                                        <div class="list-group-item bg-transparent d-flex justify-content-between align-items-center px-1 py-2 border-bottom-0" id="file-row-{{ md5($file['path']) }}">
                                            <div class="d-flex align-items-center gap-2 overflow-hidden me-2">
                                                {{-- Mini Image Preview --}}
                                                <a href="{{ $file['path'] }}" target="_blank">
                                                    @if(strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)) === 'pdf')
                                                        <div class="rounded bg-danger-subtle text-danger d-flex align-items-center justify-content-center" style="width: 28px; height: 28px;">
                                                            <i class="bi bi-file-earmark-pdf-fill" style="font-size: 0.95rem;"></i>
                                                        </div>
                                                    @else
                                                        <img src="{{ $file['path'] }}" class="rounded" loading="lazy" style="width: 28px; height: 28px; object-fit: cover; background-color: #ddd;">
                                                    @endif
                                                </a>
                                                <div class="overflow-hidden">
                                                    <div class="text-dark fw-semibold text-truncate small" style="max-width: 140px;" title="{{ $file['name'] }}">
                                                        {{ $file['name'] }}
                                                    </div>
                                                    <div class="text-secondary" style="font-size: 0.65rem;">
                                                        {{ formatBytes($file['size']) }}
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="button" class="btn btn-outline-danger btn-sm border-0 rounded-circle btn-delete-file" data-path="{{ $file['path'] }}" data-row-id="file-row-{{ md5($file['path']) }}" style="padding: 2px 6px;">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-5 text-secondary small">
                                    <i class="bi bi-folder-x d-block mb-1 text-muted" style="font-size: 2rem;"></i>
                                    Folder Kosong
                                </div>
                            @endif
                        </div>

                        {{-- Clear Folder Action --}}
                        @if($folder['files_count'] > 0)
                            <form action="{{ route('admin.storage.clear-folder') }}" method="POST" onsubmit="return confirm('PERINGATAN! Anda akan menghapus SEMUA berkas di dalam folder ini. Tindakan ini tidak bisa dibatalkan. Lanjutkan?')">
                                @csrf
                                <input type="hidden" name="folder" value="{{ $key }}">
                                <button type="submit" class="btn btn-outline-danger btn-sm w-100 py-2 fw-bold rounded-3">
                                    <i class="bi bi-trash3 me-1"></i> Kosongkan Folder Ini
                                </button>
                            </form>
                        @else
                            <button class="btn btn-light btn-sm w-100 py-2 fw-bold rounded-3 text-muted" disabled>
                                Folder Bersih
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Individual file delete handler
        document.querySelectorAll('.btn-delete-file').forEach(button => {
            button.addEventListener('click', function() {
                const path = this.getAttribute('data-path');
                const rowId = this.getAttribute('data-row-id');

                if (confirm('Hapus berkas ini secara permanen dari server?')) {
                    this.disabled = true;
                    this.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

                    fetch("{{ route('admin.storage.delete-file') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ file_path: path })
                    })
                    .then(r => r.json())
                    .then(res => {
                        if (res.success) {
                            // Remove row with simple animation
                            const row = document.getElementById(rowId);
                            if (row) {
                                row.style.transition = 'all 0.3s';
                                row.style.opacity = '0';
                                setTimeout(() => {
                                    row.remove();
                                    // Reload page to update counters if needed, or update dynamically
                                    window.location.reload();
                                }, 300);
                            }
                        } else {
                            alert(res.message);
                            this.disabled = false;
                            this.innerHTML = '<i class="bi bi-trash"></i>';
                        }
                    })
                    .catch(err => {
                        alert('Gagal menghapus berkas.');
                        this.disabled = false;
                        this.innerHTML = '<i class="bi bi-trash"></i>';
                        console.error(err);
                    });
                }
            });
        });
    });
</script>
@endpush
