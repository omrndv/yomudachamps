@extends('layouts.admin')
@section('title', 'Catatan Admin - Yomuda Championship')

@section('content')
<style>
    .notes-container {
        min-height: calc(100vh - 120px);
    }
    .notes-sidebar {
        border-right: 1px solid rgba(0, 0, 0, 0.06);
    }
    .search-box {
        display: flex;
        align-items: center;
        background: #f1f5f9;
        border: 1px solid transparent;
        border-radius: 12px;
        padding: 2px 12px;
        transition: all 0.2s ease;
    }
    .search-box:focus-within {
        background: #ffffff;
        border-color: #f59e0b;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
    }
    .search-box input {
        border: 0;
        background: transparent;
        font-size: 0.85rem;
        padding: 8px 6px;
        outline: none;
        width: 100%;
        color: #1e293b;
    }
    .search-box i {
        color: #94a3b8;
        font-size: 0.95rem;
    }
    .note-list-scroll {
        max-height: calc(100vh - 270px);
        overflow-y: auto;
        padding-right: 4px;
        scrollbar-width: thin;
        scrollbar-color: rgba(245, 158, 11, 0.3) transparent;
    }
    .note-list-scroll::-webkit-scrollbar {
        width: 4px;
    }
    .note-list-scroll::-webkit-scrollbar-thumb {
        background: rgba(245, 158, 11, 0.3);
        border-radius: 4px;
    }
    .note-card {
        border: 1px solid rgba(0, 0, 0, 0.05);
        border-radius: 14px;
        background: #ffffff;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        margin-bottom: 10px;
        display: block;
        text-decoration: none !important;
        padding: 16px;
    }
    .note-card:hover {
        border-color: rgba(0, 0, 0, 0.08);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
        transform: translateY(-1px);
    }
    .note-card.active {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%) !important;
        border-color: transparent !important;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.15) !important;
    }
    .note-card.active .note-card-title {
        color: #ffffff !important;
    }
    .note-card.active .note-card-excerpt {
        color: #94a3b8 !important;
    }
    .note-card.active .note-card-time {
        color: #f59e0b !important;
    }
    .note-card-title {
        font-size: 0.9rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 4px;
    }
    .note-card-excerpt {
        font-size: 0.78rem;
        color: #64748b;
        margin-bottom: 8px;
        line-height: 1.4;
    }
    .note-card-time {
        font-size: 0.68rem;
        font-weight: 600;
        color: #94a3b8;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    
    /* Editor Area */
    .note-editor-card {
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.06);
        border-radius: 20px;
        min-height: calc(100vh - 160px);
        display: flex;
        flex-direction: column;
    }
    .editor-header {
        padding: 24px 30px;
        border-bottom: 1px solid #f1f5f9;
        flex-shrink: 0;
    }
    .editor-title-input {
        border: 0;
        font-size: 1.6rem;
        font-weight: 800;
        color: #0f172a;
        width: 100%;
        outline: none;
        padding: 0;
        letter-spacing: -0.5px;
    }
    .editor-title-input::placeholder {
        color: #cbd5e1;
    }
    .editor-body {
        flex: 1 1 auto;
        padding: 30px;
        display: flex;
        flex-direction: column;
        min-height: 0;
    }
    .editor-textarea {
        flex: 1;
        border: 0;
        outline: none;
        resize: none;
        font-size: 0.95rem;
        line-height: 1.7;
        color: #334155;
        padding: 0;
        width: 100%;
        min-height: 250px;
    }
    .editor-textarea::placeholder {
        color: #94a3b8;
    }
    .editor-footer {
        padding: 20px 30px;
        border-top: 1px solid #f1f5f9;
        background: #fafafa;
        border-bottom-left-radius: 20px;
        border-bottom-right-radius: 20px;
        flex-shrink: 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .status-badge {
        font-size: 0.75rem;
        font-weight: 600;
        padding: 6px 14px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        border: 1px solid rgba(0, 0, 0, 0.05);
    }
    .btn-action-round {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid rgba(0,0,0,0.06);
        background: #ffffff;
        color: #dc2626;
        transition: all 0.15s ease;
    }
    .btn-action-round:hover {
        background: #fef2f2;
        border-color: #fca5a5;
        transform: scale(1.05);
    }

    @media (max-width: 767.98px) {
        .notes-sidebar {
            border-right: 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.06);
            padding-bottom: 20px;
            margin-bottom: 10px;
        }
        .note-list-scroll {
            max-height: 220px;
        }
        .note-editor-card {
            min-height: auto;
        }
        .editor-header {
            padding: 16px 20px;
        }
        .editor-body {
            padding: 20px;
        }
        .editor-footer {
            padding: 16px 20px;
        }
        .editor-title-input {
            font-size: 1.25rem;
        }
    }
</style>

<div class="container-fluid py-4 notes-container">
    <div class="row g-4">
        
        {{-- KIRI: Sidebar List Catatan --}}
        <div class="col-12 col-md-4 col-xl-3 notes-sidebar">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold text-dark mb-0" style="letter-spacing: -0.5px;">Catatan Staf</h5>
                    <span class="text-secondary small" style="font-size: 0.75rem;">Koordinasi tim internal</span>
                </div>
                <button class="btn btn-warning btn-sm fw-bold rounded-pill px-3 py-1.5 shadow-sm text-dark hover-gold d-flex align-items-center gap-1.5" onclick="createNewNote()" style="font-size: 0.78rem;">
                    <i class="bi bi-plus-lg"></i> Baru
                </button>
            </div>

            {{-- Live Search Input --}}
            <div class="mb-3">
                <div class="search-box">
                    <i class="bi bi-search"></i>
                    <input type="text" id="searchNotes" placeholder="Cari judul catatan...">
                </div>
            </div>

            {{-- Scrollable List --}}
            <div class="note-list-scroll" id="notesList">
                @forelse($all_notes as $n)
                    @php
                        // Get plain text excerpt (first 50 chars) from markdown or rich content
                        $excerpt = strip_tags($n->content);
                        $excerpt = strlen($excerpt) > 50 ? substr($excerpt, 0, 50) . '...' : (empty($excerpt) ? 'Tidak ada konten...' : $excerpt);
                    @endphp
                    <a href="{{ route('admin.notes.index', ['id' => $n->id]) }}" 
                       class="note-card note-item-link {{ isset($current_note) && $current_note->id == $n->id ? 'active' : '' }}"
                       data-title="{{ strtolower($n->title) }}">
                        <div class="note-card-title text-truncate">{{ $n->title ?? 'Tanpa Judul' }}</div>
                        <div class="note-card-excerpt text-truncate-2">{{ $excerpt }}</div>
                        <div class="note-card-time">
                            <i class="bi bi-calendar3"></i>
                            {{ date('d M Y • H:i', strtotime($n->updated_at)) }}
                        </div>
                    </a>
                @empty
                    <div class="text-center py-5 bg-white rounded-4 border border-light-subtle">
                        <i class="bi bi-journal-x fs-1 d-block mb-2 text-secondary opacity-30"></i>
                        <span class="small text-secondary">Belum ada catatan</span>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- KANAN: Editor Panel --}}
        <div class="col-12 col-md-8 col-xl-9">
            @if(isset($current_note))
                <div class="note-editor-card shadow-sm">
                    {{-- Editor Header --}}
                    <div class="editor-header d-flex justify-content-between align-items-center">
                        <div class="flex-grow-1 me-3">
                            <input type="text" id="noteTitle" class="editor-title-input" 
                                   value="{{ $current_note->title }}" placeholder="Judul Catatan...">
                        </div>
                        <button class="btn-action-round shadow-none" onclick="deleteNote({{ $current_note->id }})" title="Hapus Catatan">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </div>

                    {{-- Editor Workspace --}}
                    <div class="editor-body">
                        <textarea id="adminNoteArea" class="editor-textarea" 
                                  placeholder="Tulis detail koordinasi internal, rancangan tournament, atau memo di sini...">{{ $current_note->content }}</textarea>
                    </div>

                    {{-- Editor Footer / Status Bar --}}
                    <div class="editor-footer">
                        <div class="d-flex align-items-center gap-3 flex-wrap">
                            <div id="save-status" class="status-badge bg-white text-secondary">
                                <i class="bi bi-check2-all text-success"></i> Tersimpan
                            </div>
                            <span class="text-secondary" style="font-size: 0.72rem;">
                                <i class="bi bi-clock-history me-1"></i> Update terakhir: <span id="last-updated" class="fw-semibold text-dark">{{ date('d M Y H:i', strtotime($current_note->updated_at)) }}</span>
                            </span>
                        </div>
                        
                        <button id="manualSave" class="btn btn-warning fw-bold px-4 py-2 rounded-pill shadow-sm text-dark hover-gold d-flex align-items-center gap-1.5" style="font-size: 0.8rem;">
                            <i class="bi bi-cloud-arrow-up-fill"></i> Simpan Catatan
                        </button>
                    </div>
                </div>
            @else
                <div class="note-editor-card shadow-sm d-flex flex-column justify-content-center align-items-center p-5 text-center text-muted">
                    <div class="mb-3" style="font-size: 3.5rem; color: #cbd5e1;"><i class="bi bi-sticky"></i></div>
                    <h5 class="fw-bold text-dark mb-1">Catatan Tidak Dipilih</h5>
                    <p class="small text-secondary mb-3" style="max-width: 320px;">Silakan pilih salah satu judul catatan di sebelah kiri atau buat catatan baru untuk memulai koordinasi.</p>
                    <button class="btn btn-warning fw-bold px-4 py-2 rounded-pill text-dark hover-gold" onclick="createNewNote()" style="font-size: 0.8rem;">
                        <i class="bi bi-plus-lg me-1"></i> Buat Catatan Baru
                    </button>
                </div>
            @endif
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Live Search Sidebar Notes
    const searchInput = document.getElementById('searchNotes');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            document.querySelectorAll('.note-item-link').forEach(card => {
                const title = card.getAttribute('data-title');
                if (title.includes(query)) {
                    card.style.setProperty('display', 'block', 'important');
                } else {
                    card.style.setProperty('display', 'none', 'important');
                }
            });
        });
    }

    // Auto-save logic
    const noteArea = document.getElementById('adminNoteArea');
    const noteTitle = document.getElementById('noteTitle');
    const saveStatus = document.getElementById('save-status');
    const lastUpdated = document.getElementById('last-updated');
    let timeout = null;

    function saveNote(withAlert = false) {
        if (!noteArea) return;
        saveStatus.innerHTML = '<span class="spinner-border spinner-border-sm text-warning me-1"></span> Menyimpan...';
        
        fetch("{{ route('admin.notes.update', $current_note->id ?? 0) }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ 
                title: noteTitle.value || 'Tanpa Judul',
                content: noteArea.value 
            })
        })
        .then(response => response.json())
        .then(data => {
            saveStatus.innerHTML = '<i class="bi bi-check2-all text-success"></i> Tersimpan';
            if (data.updated_at) {
                // Parse date formatting to match server view
                const d = new Date(data.updated_at);
                const months = ['Mey', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des']; // local short month representation or general format
                lastUpdated.innerText = data.updated_at;
            }
            // Update active note title on sidebar dynamically
            const activeCard = document.querySelector('.note-card.active .note-card-title');
            const activeCardExcerpt = document.querySelector('.note-card.active .note-card-excerpt');
            if (activeCard) {
                activeCard.innerText = noteTitle.value || 'Tanpa Judul';
                activeCard.closest('.note-card').setAttribute('data-title', (noteTitle.value || 'Tanpa Judul').toLowerCase());
            }
            if (activeCardExcerpt) {
                const plainText = noteArea.value.replace(/<\/?[^>]+(>|$)/g, "");
                activeCardExcerpt.innerText = plainText.length > 50 ? plainText.substring(0, 50) + '...' : (plainText || 'Tidak ada konten...');
            }

            if (withAlert) {
                Swal.fire({ 
                    toast: true, 
                    position: 'top-end', 
                    icon: 'success', 
                    title: 'Catatan disimpan!', 
                    showConfirmButton: false, 
                    timer: 1500,
                    background: '#0f172a',
                    color: '#ffffff'
                });
            }
        })
        .catch(err => {
            saveStatus.innerHTML = '<span class="text-danger"><i class="bi bi-x-circle me-1"></i> Gagal</span>';
        });
    }

    if (noteArea) {
        [noteArea, noteTitle].forEach(el => {
            el.addEventListener('keyup', () => {
                clearTimeout(timeout);
                saveStatus.innerHTML = '<span class="spinner-border spinner-border-sm text-warning me-1"></span> Sedang mengetik...';
                timeout = setTimeout(() => saveNote(false), 2000); 
            });
        });
        document.getElementById('manualSave').addEventListener('click', () => saveNote(true));
    }
});

function createNewNote() {
    Swal.fire({
        title: 'Judul Catatan Baru',
        input: 'text',
        inputPlaceholder: 'Misal: Rencana Sponsor Turnamen...',
        showCancelButton: true,
        confirmButtonText: 'Buat Catatan',
        confirmButtonColor: '#f59e0b',
        cancelButtonColor: '#64748b',
        background: '#0f172a',
        color: '#ffffff',
        customClass: {
            confirmButton: 'btn btn-warning rounded-pill px-4 fw-bold text-dark',
            cancelButton: 'btn btn-light rounded-pill px-4 fw-bold text-dark'
        }
    }).then((result) => {
        if (result.isConfirmed && result.value) {
            window.location.href = "{{ route('admin.notes.store') }}?title=" + encodeURIComponent(result.value);
        }
    });
}

function deleteNote(id) {
    Swal.fire({
        title: 'Hapus Catatan Ini?',
        text: "Konten catatan akan hilang secara permanen!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Hapus',
        cancelButtonText: 'Batal',
        background: '#0f172a',
        color: '#ffffff',
        customClass: {
            confirmButton: 'btn btn-danger rounded-pill px-4 fw-bold text-white',
            cancelButton: 'btn btn-light rounded-pill px-4 fw-bold text-dark'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "/admin/notes/delete/" + id;
        }
    });
}
</script>
@endsection