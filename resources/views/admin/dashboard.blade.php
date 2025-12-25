@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2">
                    <li class="breadcrumb-item"><a href="{{ route('admin.seasons') }}" class="text-decoration-none text-warning fw-bold">Daftar Season</a></li>
                    <li class="breadcrumb-item active">{{ $current_season->name }}</li>
                </ol>
            </nav>
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <h3 class="fw-bold text-dark m-0">Peserta <span class="text-warning">{{ $current_season->name }}</span></h3>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('admin.team.deleteAll', $current_season->id) }}"
                        class="btn btn-outline-danger btn-sm px-3 fw-bold shadow-sm"
                        onclick="return confirm('PERINGATAN! Semua data tim di season ini akan dihapus permanen. Lanjutkan?')">
                        <i class="bi bi-trash3-fill me-1"></i> Reset Data
                    </a>
                    <button class="btn btn-outline-dark btn-sm px-3 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalBulk">
                        <i class="bi bi-stack me-1"></i> Bulk Add
                    </button>
                    <button type="button" class="btn btn-outline-success btn-sm px-3 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalExportWA">
                        <i class="bi bi-whatsapp me-1"></i> Format WA
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card-custom border-start border-warning border-4 p-4 bg-white shadow-sm rounded-4">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="text-muted small fw-bold mb-0">TOTAL TIM TERDAFTAR</p>
                        <h2 class="fw-bold mb-0">
                            {{ $filtered_teams->count() }}
                            <span class="fs-6 text-secondary fw-normal">/ {{ $current_season->slot }} Slot</span>
                        </h2>
                    </div>
                    <i class="bi bi-people text-warning fs-1 opacity-25"></i>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card-custom border-start border-success border-4 p-4 bg-white shadow-sm rounded-4">
                <div class="d-flex justify-content-between">
                    <div>
                        <p class="text-muted small fw-bold mb-0">ESTIMASI PENDAPATAN (PAID)</p>
                        <h2 class="fw-bold mb-0 text-success">
                            Rp {{ number_format($total_income, 0, ',', '.') }}
                        </h2>
                    </div>
                    <i class="bi bi-wallet2 text-success fs-1 opacity-25"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="card-custom bg-white p-4 shadow-sm rounded-4">
        <div class="row mb-3">
            <div class="col-md-4">
                <div class="input-group shadow-sm rounded-3">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" id="searchTable" class="form-control border-start-0 ps-0" placeholder="Cari nama tim atau ID TRX...">
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="bg-light">
                    <tr class="text-secondary small fw-bold text-uppercase" style="font-size: 0.75rem;">
                        <th class="px-3" width="50">#</th>
                        <th>ID TRX</th>
                        <th>NAMA TIM</th>
                        <th>NOMOR WHATSAPP</th>
                        <th class="text-center">STATUS</th>
                        <th class="text-center" width="150">AKSI</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($filtered_teams as $index => $team)
                    <tr class="team-row">
                        <td class="px-3 text-muted">{{ $index + 1 }}</td>
                        <td class="small fw-bold text-primary">#{{ $team->trx_id }}</td>
                        <td><span class="fw-bold text-dark text-uppercase">{{ $team->name }}</span></td>
                        <td>
                            <code class="text-dark fw-bold me-2">{{ $team->wa_number }}</code>
                            @php
                            $wa = preg_replace('/[^0-9]/', '', $team->wa_number);
                            $wa_link = str_starts_with($wa, '0') ? '62' . substr($wa, 1) : (str_starts_with($wa, '8') ? '62' . $wa : $wa);
                            @endphp
                            <a href="https://wa.me/{{ $wa_link }}" target="_blank" class="btn btn-sm btn-outline-success border-0 py-0">
                                <i class="bi bi-whatsapp"></i>
                            </a>
                        </td>
                        <td class="text-center">
                            <span class="badge {{ $team->status == 'PAID' ? 'bg-success text-white' : 'bg-warning text-dark' }} px-3 py-2 rounded-pill" style="font-size: 0.7rem;">
                                {{ $team->status }}
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="btn-group shadow-sm">
                                <button class="btn btn-sm btn-white border" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $team->id }}">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <a href="{{ route('admin.team.delete', $team->id) }}"
                                    class="btn btn-sm btn-white border text-danger"
                                    onclick="return confirm('Yakin mau hapus tim {{ $team->name }}?')">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>

                    <div class="modal fade" id="modalEdit{{ $team->id }}" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content border-0 shadow">
                                <div class="modal-header border-0">
                                    <h5 class="fw-bold">Edit Tim: {{ $team->name }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form action="{{ route('admin.team.update', $team->id) }}" method="POST">
                                    @csrf
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="small fw-bold text-muted mb-1 text-uppercase">Nama Tim</label>
                                            <input type="text" name="name" class="form-control" value="{{ $team->name }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="small fw-bold text-muted mb-1 text-uppercase">Nomor WhatsApp</label>
                                            <input type="number" name="wa_number" class="form-control" value="{{ $team->wa_number }}" required>
                                        </div>
                                        <div class="mb-0">
                                            <label class="small fw-bold text-muted mb-1 text-uppercase">Status Pembayaran</label>
                                            <select name="status" class="form-select">
                                                <option value="PENDING" {{ $team->status == 'PENDING' ? 'selected' : '' }}>PENDING</option>
                                                <option value="PAID" {{ $team->status == 'PAID' ? 'selected' : '' }}>PAID (Lunas)</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer border-0">
                                        <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Batal</button>
                                        <button type="submit" class="btn btn-warning fw-bold px-4">Simpan Perubahan</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-emoji-frown fs-2 d-block mb-2"></i>
                            Belum ada peserta di season ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="modal fade" id="modalBulk" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header border-0">
                <h5 class="fw-bold">Import Data (Bulk Add)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.bulk.store', $current_season->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info py-2 small border-0 mb-3">
                        <i class="bi bi-info-circle me-1"></i>
                        <strong>Format:</strong> Nama Tim [Spasi/Tab] Nomor WA. Masukkan satu baris per tim.
                    </div>
                    <textarea name="bulk_data" class="form-control" rows="10" placeholder="Contoh:&#10;GARUDA TEAM 081234567890&#10;ELANG ESPORT 089988776655" required></textarea>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light fw-bold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-dark fw-bold px-4">Proses & Tambahkan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modalExportWA" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white">
                <h5 class="fw-bold mb-0"><i class="bi bi-whatsapp me-2 text-success"></i>Format Salin WA</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body bg-light">
                <p class="small text-muted mb-2">Salin teks di bawah untuk dibagikan ke grup:</p>
                <textarea id="textPengumuman" class="form-control border-0 shadow-sm p-3" rows="15" readonly
                    style="font-family: 'Courier New', Courier, monospace; font-size: 13px; resize: none; background: #fff;">
📢 PENGUMUMAN NOMOR WHATSAPP PERWAKILAN TIM

Berikut adalah daftar nomor WhatsApp perwakilan setiap tim.
Tim yang berada di bracket atas wajib membuat room dan mengundang tim lawan.

@forelse($paid_teams as $key => $team)
{{ $team->name }} --- {{ $team->wa_number }}
@if(($key + 1) % 10 == 0)

@endif
@empty
(Belum ada tim dengan status PAID)
@endforelse
</textarea>
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-success fw-bold w-100 py-2" onclick="copyToClipboard()">
                    <i class="bi bi-clipboard-check me-2"></i> SALIN TEKS SEKARANG
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('searchTable').addEventListener('keyup', function() {
        let filter = this.value.toLowerCase();
        let rows = document.querySelectorAll('.team-row');

        rows.forEach(row => {
            let text = row.innerText.toLowerCase();
            if (text.includes(filter)) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        });
    });

    function copyToClipboard() {
        var copyText = document.getElementById("textPengumuman");
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        try {
            navigator.clipboard.writeText(copyText.value);
            alert("✅ Teks berhasil disalin!");
        } catch (err) {
            document.execCommand("copy");
            alert("✅ Teks berhasil disalin!");
        }
    }
</script>
@endsection