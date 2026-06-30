@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-0">System Error Logs</h3>
            <p class="text-secondary small mb-0">Memantau aktivitas log & error dari server laravel.log secara real-time</p>
        </div>
        <button onclick="window.location.reload();" class="btn btn-warning text-dark fw-bold rounded-pill px-4">
            <i class="bi bi-arrow-clockwise me-1"></i> Refresh Log
        </button>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-dark text-light">
        <div class="card-header bg-dark border-secondary border-opacity-25 d-flex justify-content-between align-items-center py-3 px-4">
            <div class="d-flex align-items-center gap-2">
                <span class="d-inline-block rounded-circle bg-danger" style="width: 12px; height: 12px;"></span>
                <span class="d-inline-block rounded-circle bg-warning" style="width: 12px; height: 12px;"></span>
                <span class="d-inline-block rounded-circle bg-success" style="width: 12px; height: 12px;"></span>
                <span class="ms-2 text-secondary font-monospace" style="font-size: 0.8rem;">laravel.log (150 Baris Terakhir)</span>
            </div>
            <button onclick="copyLogs()" class="btn btn-sm btn-outline-secondary text-light font-monospace" style="font-size: 0.72rem;">
                <i class="bi bi-clipboard me-1"></i> Copy Log
            </button>
        </div>
        
        <div class="card-body p-0">
            <div class="p-4 font-monospace overflow-auto" id="logTerminal" style="max-height: 600px; font-size: 0.78rem; line-height: 1.6; background-color: #0f172a; color: #cbd5e1;">
                @foreach($logs as $log)
                    @php
                        $colorClass = 'text-light';
                        if (stripos($log, 'ERROR') !== false || stripos($log, 'exception') !== false) {
                            $colorClass = 'text-danger fw-bold';
                        } elseif (stripos($log, 'WARNING') !== false) {
                            $colorClass = 'text-warning';
                        } elseif (stripos($log, 'INFO') !== false) {
                            $colorClass = 'text-info';
                        }
                    @endphp
                    <div class="mb-1 py-1 border-bottom border-secondary border-opacity-10 {{ $colorClass }}">
                        {{ $log }}
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
function copyLogs() {
    const text = document.getElementById('logTerminal').innerText;
    navigator.clipboard.writeText(text).then(() => {
        alert('Log berhasil disalin ke clipboard!');
    }).catch(err => {
        console.error('Gagal menyalin log:', err);
    });
}
</script>
@endsection
