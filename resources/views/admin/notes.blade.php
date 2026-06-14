@extends('layouts.admin')
@section('title', 'Admin Notes Manager')

@section('content')
<style>
    /* Professional sidebar and editor styles */
    .note-sidebar-item {
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        border-radius: 12px !important;
        margin-bottom: 8px;
        border: 1px solid rgba(241, 245, 249, 0.8) !important;
        background-color: #fff;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02);
    }
    .note-sidebar-item.active {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
        color: #ffffff !important;
        border-color: transparent !important;
        box-shadow: 0 4px 14px rgba(245, 158, 11, 0.3) !important;
    }
    .note-sidebar-item.active .text-muted {
        color: rgba(255, 255, 255, 0.75) !important;
    }
    .note-sidebar-item:hover:not(.active) {
        background-color: #f8fafc;
        transform: translateX(-4px);
        border-color: rgba(226, 232, 240, 0.8) !important;
    }
    .editor-card {
        border: 1px solid rgba(241, 245, 249, 0.8) !important;
        border-radius: 20px;
        background: #fff;
        min-height: 78vh;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.01);
    }
    #adminNoteArea {
        font-family: 'Plus Jakarta Sans', -apple-system, sans-serif;
        font-size: 1rem;
        color: #334155;
        line-height: 1.6;
        scrollbar-width: thin;
        scrollbar-color: #f59e0b #f1f5f9;
    }
    #adminNoteArea::-webkit-scrollbar { width: 6px; }
    #adminNoteArea::-webkit-scrollbar-thumb { background: #f59e0b; border-radius: 10px; }
    #noteTitle:focus { outline: none; }
</style>

<div class="container-fluid py-4" style="background-color: #f8fafc; min-height: 100vh;">
    {{-- Layout: Mobile = Sidebar di Atas, Desktop = Sidebar di Kanan --}}
    <div class="row g-4 d-flex flex-column-reverse flex-md-row">
        
        {{-- AREA EDITOR (Kiri di Desktop, Bawah di Mobile) --}}
        <div class="col-md-8 col-lg-9 order-md-1">
            @if(isset($current_note))
            <div class="card editor-card shadow-sm p-4 p-lg-5 border-0">
                <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom border-light">
                    <div class="flex-grow-1">
                        <input type="text" id="noteTitle" class="form-control fw-bold border-0 bg-transparent fs-2 p-0 text-slate-800" 
                                value="{{ $current_note->title }}" placeholder="Judul Catatan..." style="box-shadow: none; letter-spacing: -0.5px;">
                        <div class="d-flex align-items-center mt-2">
                            <span id="save-status" class="badge bg-light text-secondary fw-normal rounded-pill px-3 py-1.5 border border-light-subtle">
                                <i class="bi bi-check2-all me-1"></i> Tersimpan
                            </span>
                        </div>
                    </div>
                    <div class="ms-3">
                        <button class="btn btn-outline-danger rounded-circle p-2 border border-danger-subtle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;" onclick="deleteNote({{ $current_note->id }})" title="Hapus Catatan">
                            <i class="bi bi-trash3 fs-5"></i>
                        </button>
                    </div>
                </div>

                <textarea id="adminNoteArea" class="form-control border-0 bg-transparent p-0 shadow-none" rows="18" 
                    style="resize: none; box-shadow: none;" 
                    placeholder="Tulis detail catatanmu di sini, Nadiv...">{{ $current_note->content }}</textarea>
                
                <div class="d-flex justify-content-between align-items-center mt-5 pt-3 border-top border-light-subtle">
                    <div class="text-secondary small">
                        <i class="bi bi-clock-history me-1 text-muted"></i> Update: <span id="last-updated" class="fw-semibold text-dark">{{ $current_note->updated_at }}</span>
                    </div>
                    <button id="manualSave" class="btn btn-warning fw-bold px-5 py-2.5 rounded-pill shadow text-dark" style="letter-spacing: 0.3px;">
                        SIMPAN CATATAN
                    </button>
                </div>
            </div>
            @else
            <div class="card editor-card shadow-sm border-0 d-flex flex-column justify-content-center align-items-center text-muted opacity-75">
                <i class="bi bi-sticky-fill display-2 mb-3 text-secondary opacity-30"></i>
                <h5 class="fw-bold text-dark mb-1">Pilih catatan atau buat baru</h5>
                <p class="small text-secondary mb-0">Semua ide turnamenmu aman di sini.</p>
            </div>
            @endif
        </div>

        {{-- SIDEBAR NOTES (Kanan di Desktop, Atas di Mobile) --}}
        <div class="col-md-4 col-lg-3 order-md-2">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold text-dark m-0" style="letter-spacing: -0.5px;"><i class="bi bi-sticky-fill text-warning me-2"></i>Catatan</h4>
                <button class="btn btn-warning btn-sm rounded-pill px-3 fw-bold text-dark shadow-sm" onclick="createNewNote()">
                    <i class="bi bi-plus-lg me-1"></i> BARU
                </button>
            </div>
            
            <div class="list-group list-group-flush" id="notesList" style="max-height: 75vh; overflow-y: auto; padding-right: 5px;">
                @forelse($all_notes as $n)
                <a href="{{ route('admin.notes.index', ['id' => $n->id]) }}" 
                   class="list-group-item list-group-item-action note-sidebar-item p-3 shadow-none {{ isset($current_note) && $current_note->id == $n->id ? 'active' : '' }}">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="text-truncate w-100">
                            <span class="fw-bold d-block text-truncate mb-1" style="font-size: 0.9rem;">{{ $n->title ?? 'Tanpa Judul' }}</span>
                            <small class="{{ isset($current_note) && $current_note->id == $n->id ? 'text-white-50' : 'text-muted' }} d-block" style="font-size: 0.65rem;">
                                <i class="bi bi-clock me-1"></i>{{ date('d M Y • H:i', strtotime($n->updated_at)) }}
                            </small>
                        </div>
                    </div>
                </a>
                @empty
                <div class="text-center py-5 opacity-75 bg-white rounded-4 shadow-sm border border-light-subtle">
                    <i class="bi bi-journal-x fs-1 d-block mb-2 text-secondary opacity-30"></i>
                    <p class="small text-secondary m-0">Belum ada catatan</p>
                </div>
                @endforelse
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
    const noteArea = document.getElementById('adminNoteArea');
    const noteTitle = document.getElementById('noteTitle');
    const saveStatus = document.getElementById('save-status');
    const lastUpdated = document.getElementById('last-updated');
    let timeout = null;

    function saveNote(withAlert = false) {
        if(!noteArea) return;
        saveStatus.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';
        
        fetch("{{ route('admin.notes.update', $current_note->id ?? 0) }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ 
                title: noteTitle.value,
                content: noteArea.value 
            })
        })
        .then(response => response.json())
        .then(data => {
            saveStatus.innerHTML = '<i class="bi bi-check2-all me-1"></i> Tersimpan';
            if(data.updated_at) lastUpdated.innerText = data.updated_at;
            if (withAlert) {
                Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: 'Tersimpan!', showConfirmButton: false, timer: 1500 });
            }
        })
        .catch(err => {
            saveStatus.innerHTML = '<span class="text-danger"><i class="bi bi-x-circle me-1"></i> Gagal</span>';
        });
    }

    if(noteArea) {
        [noteArea, noteTitle].forEach(el => {
            el.addEventListener('keyup', () => {
                clearTimeout(timeout);
                saveStatus.innerHTML = '<i class="bi bi-pencil me-1"></i> Sedang mengetik...';
                timeout = setTimeout(() => saveNote(false), 2000); 
            });
        });
        document.getElementById('manualSave').addEventListener('click', () => saveNote(true));
    }

    function createNewNote() {
        Swal.fire({
            title: 'Judul Catatan Baru',
            input: 'text',
            inputPlaceholder: 'Misal: Kontak Sponsor...',
            showCancelButton: true,
            confirmButtonText: 'Buat Sekarang',
            confirmButtonColor: '#f59e0b',
            cancelButtonColor: '#64748b',
            customClass: {
                confirmButton: 'btn btn-warning rounded-pill px-4 fw-bold text-dark',
                cancelButton: 'btn btn-light rounded-pill px-4 fw-bold'
            }
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                window.location.href = "{{ route('admin.notes.store') }}?title=" + encodeURIComponent(result.value);
            }
        });
    }

    function deleteNote(id) {
        Swal.fire({
            title: 'Hapus Catatan?',
            text: "Data ini akan hilang selamanya!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#64748b',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal',
            customClass: {
                confirmButton: 'btn btn-danger rounded-pill px-4 fw-bold',
                cancelButton: 'btn btn-light rounded-pill px-4 fw-bold'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "/admin/notes/delete/" + id;
            }
        });
    }
</script>
@endpush