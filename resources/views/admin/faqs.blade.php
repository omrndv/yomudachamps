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
                Kelola daftar pertanyaan umum yang akan ditampilkan di landing page halaman depan.
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
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    {{-- Tabel / List FAQ --}}
    <div class="card border-0 shadow-sm rounded-4 bg-white overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                    <thead class="bg-light text-secondary text-uppercase fw-bold" style="font-size: 0.72rem; letter-spacing: 0.8px;">
                        <tr>
                            <th class="py-3 px-4" style="width: 80px;">Urutan</th>
                            <th class="py-3 px-3">Pertanyaan</th>
                            <th class="py-3 px-3">Jawaban</th>
                            <th class="py-3 px-4 text-end" style="width: 150px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="text-dark">
                        @forelse($faqs as $faq)
                            <tr>
                                <td class="py-3 px-4 font-monospace">
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
                                <td class="py-3 px-3 fw-bold">
                                    {{ $faq->question }}
                                </td>
                                <td class="py-3 px-3 text-secondary text-truncate" style="max-width: 400px;">
                                    {{ $faq->answer }}
                                </td>
                                <td class="py-3 px-4 text-end">
                                    <button class="btn btn-sm btn-outline-secondary rounded-pill me-1" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editFaqModal{{ $faq->id }}">
                                        <i class="bi bi-pencil"></i> Edit
                                    </button>
                                    <a href="{{ route('admin.faqs.delete', $faq->id) }}" 
                                       class="btn btn-sm btn-outline-danger rounded-pill" 
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
                                <td colspan="4" class="py-5 text-center text-secondary">
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
});
</script>
@endpush
