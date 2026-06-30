@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4" style="background-color: #f8fafc; min-height: 100vh;">
    {{-- Header --}}
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <h2 class="fw-bold text-dark mb-1" style="font-size: 1.75rem; letter-spacing: -0.5px;">
                Pengaturan FAQ (Tanya Jawab)
            </h2>
            <p class="text-secondary mb-0" style="font-size: 0.9rem;">
                Kelola daftar pertanyaan umum yang akan ditampilkan di landing page halaman depan secara interaktif.
            </p>
        </div>
        <div class="col-md-4 text-md-end mt-3 mt-md-0">
            <button class="btn btn-warning fw-bold px-4 py-2.5 rounded-pill shadow-sm text-dark" data-bs-toggle="modal" data-bs-target="#addFaqModal">
                <i class="bi bi-plus-lg me-1"></i> Tambah FAQ
            </button>
        </div>
    </div>

    {{-- Success Alert --}}
    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-3 mb-4 d-flex align-items-center">
            <i class="bi bi-check-circle-fill me-2 fs-5 text-success"></i>
            <div class="fw-semibold small text-success">{{ session('success') }}</div>
        </div>
    @endif

    {{-- Bulk Action Bar (Floating / Dynamic Panel) --}}
    <div class="card border-0 shadow-sm rounded-4 bg-white mb-4 p-3 d-none" id="bulkActionPanel">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-warning text-dark rounded-pill px-3 py-2 fw-bold" id="selectedCountBadge" style="font-size: 0.78rem;">0 Terpilih</span>
                <span class="small text-secondary">Pilih tindakan massal untuk FAQ terpilih:</span>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-success btn-sm fw-bold rounded-pill px-3 py-1.5" id="btnBulkActive">
                    <i class="bi bi-eye-fill me-1"></i> Aktifkan
                </button>
                <button type="button" class="btn btn-secondary btn-sm fw-bold rounded-pill px-3 py-1.5" id="btnBulkInactive">
                    <i class="bi bi-eye-slash-fill me-1"></i> Nonaktifkan
                </button>
            </div>
        </div>
    </div>

    {{-- Tabel / List FAQ --}}
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                    <thead class="bg-light text-secondary text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.8px;">
                        <tr>
                            <th class="py-3 px-4" style="width: 50px;">
                                <div class="form-check">
                                    <input type="checkbox" id="selectAllFaq" class="form-check-input cursor-pointer" style="width: 1.15rem; height: 1.15rem;">
                                </div>
                            </th>
                            <th class="py-3 px-2" style="width: 80px;">Urutan</th>
                            <th class="py-3 px-3" style="width: 30%;">Pertanyaan</th>
                            <th class="py-3 px-3">Jawaban</th>
                            <th class="py-3 px-3 text-center" style="width: 130px;">Tampil di Web</th>
                            <th class="py-3 px-4 text-end" style="width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-dark">
                        @forelse($faqs as $faq)
                            <tr id="faq-row-{{ $faq->id }}">
                                <td class="py-3 px-4">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input faq-checkbox cursor-pointer" value="{{ $faq->id }}" style="width: 1.1rem; height: 1.1rem;">
                                    </div>
                                </td>
                                <td class="py-3 px-2 font-monospace">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="badge bg-light text-secondary border border-light-subtle rounded-pill px-2.5 py-1.5 fw-bold">
                                            #{{ $faq->order }}
                                        </span>
                                        <div class="d-flex flex-column gap-0">
                                            <button type="button" class="btn btn-link p-0 text-secondary btn-reorder-faq" data-id="{{ $faq->id }}" data-direction="up" style="line-height: 1; font-size: 0.75rem; text-decoration: none;">
                                                <i class="bi bi-chevron-up"></i>
                                            </button>
                                            <button type="button" class="btn btn-link p-0 text-secondary btn-reorder-faq" data-id="{{ $faq->id }}" data-direction="down" style="line-height: 1; font-size: 0.75rem; text-decoration: none;">
                                                <i class="bi bi-chevron-down"></i>
                                            </button>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-3 fw-bold text-dark">
                                    {{ $faq->question }}
                                </td>
                                <td class="py-3 px-3 text-secondary" style="max-width: 400px; white-space: pre-wrap; word-break: break-word;">
                                    {{ Str::limit($faq->answer, 180) }}
                                </td>
                                <td class="py-3 px-3 text-center">
                                    <div class="d-flex flex-column align-items-center gap-1.5">
                                        <span class="badge rounded-pill px-2.5 py-1.5 status-badge-{{ $faq->id }} {{ $faq->is_active ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-secondary-subtle text-secondary border border-secondary-subtle' }}" style="font-size: 0.72rem;">
                                            {{ $faq->is_active ? 'Aktif' : 'Nonaktif' }}
                                        </span>
                                        <div class="form-check form-switch m-0 d-inline-block">
                                            <input class="form-check-input faq-status-switch cursor-pointer" type="checkbox" data-id="{{ $faq->id }}" {{ $faq->is_active ? 'checked' : '' }} style="width: 2.1rem; height: 1.1rem;">
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-end">
                                    <button class="btn btn-sm btn-outline-secondary rounded-pill me-1 fw-bold" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editFaqModal{{ $faq->id }}">
                                        <i class="bi bi-pencil"></i> Edit
                                    </button>
                                    <a href="{{ route('admin.faqs.delete', $faq->id) }}" 
                                       class="btn btn-sm btn-outline-danger rounded-pill fw-bold" 
                                       onclick="return confirm('Apakah Anda yakin ingin menghapus FAQ ini?');">
                                        <i class="bi bi-trash"></i> Hapus
                                    </a>
                                </td>
                            </tr>

                            {{-- Edit FAQ Modal --}}
                            <div class="modal fade" id="editFaqModal{{ $faq->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content border-0 rounded-4 shadow-lg">
                                        <form action="{{ route('admin.faqs.update', $faq->id) }}" method="POST">
                                            @csrf
                                            <div class="modal-header border-bottom border-light px-4 py-3">
                                                <h5 class="modal-title fw-bold text-dark"><i class="bi bi-pencil-square text-warning me-1"></i> Edit FAQ</h5>
                                                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body p-4">
                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold text-secondary text-uppercase mb-1">Pertanyaan (Question)</label>
                                                    <input type="text" name="question" class="form-control rounded-3 border-light-subtle shadow-none p-2.5" 
                                                           value="{{ $faq->question }}" placeholder="Contoh: Kapan turnamen dimulai?" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-bold text-secondary text-uppercase mb-1">Jawaban (Answer)</label>
                                                    <textarea name="answer" class="form-control rounded-3 border-light-subtle shadow-none p-2.5" 
                                                              rows="5" placeholder="Masukkan jawaban lengkap..." required>{{ $faq->answer }}</textarea>
                                                </div>
                                                <div class="mb-0">
                                                    <label class="form-label small fw-bold text-secondary text-uppercase mb-1">Nomor Urut Tampil (Semakin kecil semakin atas)</label>
                                                    <input type="number" name="order" class="form-control rounded-3 border-light-subtle shadow-none p-2.5" 
                                                           value="{{ $faq->order }}" placeholder="0" required>
                                                </div>
                                            </div>
                                            <div class="modal-footer border-top border-light px-4 py-3">
                                                <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold text-dark">Simpan Perubahan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="6" class="py-5 text-center text-secondary">
                                    <div class="py-4">
                                        <i class="bi bi-question-circle fs-1 text-muted mb-3 d-block"></i>
                                        Belum ada FAQ yang dibuat.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Add FAQ Modal --}}
<div class="modal fade" id="addFaqModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <form action="{{ route('admin.faqs.store') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom border-light px-4 py-3">
                    <h5 class="modal-title fw-bold text-dark"><i class="bi bi-plus-circle-fill text-warning me-1"></i> Tambah FAQ Baru</h5>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary text-uppercase mb-1">Pertanyaan (Question)</label>
                        <input type="text" name="question" class="form-control rounded-3 border-light-subtle shadow-none p-2.5" 
                               placeholder="Contoh: Kapan turnamen dimulai?" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary text-uppercase mb-1">Jawaban (Answer)</label>
                        <textarea name="answer" class="form-control rounded-3 border-light-subtle shadow-none p-2.5" 
                                  rows="5" placeholder="Masukkan jawaban lengkap..." required></textarea>
                    </div>
                    <div class="mb-0">
                        <label class="form-label small fw-bold text-secondary text-uppercase mb-1">Nomor Urut Tampil (Semakin kecil semakin atas)</label>
                        <input type="number" name="order" class="form-control rounded-3 border-light-subtle shadow-none p-2.5" 
                               value="0" placeholder="0" required>
                    </div>
                </div>
                <div class="modal-footer border-top border-light px-4 py-3">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold text-dark">Simpan FAQ</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function() {
    const reorderButtons = document.querySelectorAll(".btn-reorder-faq");
    reorderButtons.forEach(button => {
        button.addEventListener("click", function() {
            const faqId = this.getAttribute("data-id");
            const direction = this.getAttribute("data-direction");
            
            fetch(`/admin/faqs/reorder/${faqId}`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({ direction: direction })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: data.error || 'Gagal mengubah urutan FAQ'
                    });
                }
            })
            .catch(error => {
                console.error("Error reordering FAQ:", error);
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Terjadi kesalahan sistem'
                });
            });
        });
    });

    // -------------------------------------------------------------
    // LOGIKA SELEKSI & AKSI MASSAL (BULK ACTIONS)
    // -------------------------------------------------------------
    const selectAllCheckbox = document.getElementById("selectAllFaq");
    const faqCheckboxes = document.querySelectorAll(".faq-checkbox");
    const bulkActionPanel = document.getElementById("bulkActionPanel");
    const selectedCountBadge = document.getElementById("selectedCountBadge");
    const btnBulkActive = document.getElementById("btnBulkActive");
    const btnBulkInactive = document.getElementById("btnBulkInactive");

    function updateBulkPanelVisibility() {
        const checkedCount = document.querySelectorAll(".faq-checkbox:checked").length;
        if (checkedCount > 0) {
            bulkActionPanel.classList.remove("d-none");
            selectedCountBadge.textContent = `${checkedCount} Terpilih`;
        } else {
            bulkActionPanel.classList.add("d-none");
        }
        // Sync selectAllCheckbox checked status
        selectAllCheckbox.checked = (checkedCount === faqCheckboxes.length && faqCheckboxes.length > 0);
    }

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener("change", function() {
            faqCheckboxes.forEach(cb => {
                cb.checked = this.checked;
            });
            updateBulkPanelVisibility();
        });
    }

    faqCheckboxes.forEach(cb => {
        cb.addEventListener("change", updateBulkPanelVisibility);
    });

    // Jalankan Aksi Massal (Bulk Action Request)
    function executeBulkAction(status) {
        const selectedIds = Array.from(document.querySelectorAll(".faq-checkbox:checked")).map(cb => cb.value);
        if (selectedIds.length === 0) return;

        const actionText = status === 'active' ? 'mengaktifkan' : 'menonaktifkan';
        if (!confirm(`Apakah Anda yakin ingin ${actionText} ${selectedIds.length} FAQ yang dipilih?`)) {
            return;
        }

        fetch("{{ route('admin.faqs.bulk-status') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({
                ids: selectedIds,
                status: status
            })
        })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                // Reload halaman untuk melihat status terbaru
                window.location.reload();
            } else {
                alert('Gagal: ' + res.message);
            }
        })
        .catch(err => {
            console.error(err);
            alert('Terjadi kesalahan jaringan.');
        });
    }

    if (btnBulkActive) {
        btnBulkActive.addEventListener("click", () => executeBulkAction('active'));
    }
    if (btnBulkInactive) {
        btnBulkInactive.addEventListener("click", () => executeBulkAction('inactive'));
    }

    // -------------------------------------------------------------
    // LOGIKA TOGGLE SWITCH INDIVIDUAL (AJAX TOGGLE)
    // -------------------------------------------------------------
    const statusSwitches = document.querySelectorAll(".faq-status-switch");
    statusSwitches.forEach(sw => {
        sw.addEventListener("change", function() {
            const faqId = this.getAttribute("data-id");
            const isChecked = this.checked;
            const badge = document.querySelector(`.status-badge-${faqId}`);

            fetch(`/admin/faqs/toggle/${faqId}`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                }
            })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    // Update badge style & text dynamically
                    if (res.is_active) {
                        badge.className = `badge rounded-pill px-2.5 py-1.5 status-badge-${faqId} bg-success-subtle text-success border border-success-subtle`;
                        badge.textContent = "Aktif";
                    } else {
                        badge.className = `badge rounded-pill px-2.5 py-1.5 status-badge-${faqId} bg-secondary-subtle text-secondary border border-secondary-subtle`;
                        badge.textContent = "Nonaktif";
                    }
                } else {
                    // Revert checkbox state on failure
                    this.checked = !isChecked;
                    alert('Gagal memperbarui status: ' + res.message);
                }
            })
            .catch(err => {
                this.checked = !isChecked;
                console.error(err);
                alert('Terjadi kesalahan koneksi.');
            });
        });
    });
});
</script>
@endpush
