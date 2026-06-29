@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4" style="background-color: #f8fafc; min-height: 100vh;">
    {{-- Breadcrumb & Header --}}
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-2" style="font-size: 0.85rem;">
                    <li class="breadcrumb-item"><a href="{{ route('admin.seasons') }}" class="text-decoration-none text-warning fw-semibold">Daftar Season</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard', $season->id) }}" class="text-decoration-none text-warning fw-semibold">{{ $season->name }}</a></li>
                    <li class="breadcrumb-item active text-secondary" aria-current="page">Generator Sertifikat</li>
                </ol>
            </nav>
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h2 class="fw-bold text-dark m-0" style="font-size: 1.75rem; letter-spacing: -0.5px;">
                        Generator Sertifikat <span class="text-warning">{{ $season->name }}</span>
                    </h2>
                    <p class="text-secondary small mb-0 mt-1">
                        Desain tata letak nama secara visual dengan drag & drop, lalu cetak otomatis ke Google Drive peserta atau download manual.
                    </p>
                </div>
                <div>
                    <a href="{{ route('admin.dashboard', $season->id) }}" class="btn btn-outline-secondary btn-sm px-3 fw-bold rounded-pill shadow-sm">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-3 py-3 mb-4 d-flex align-items-center">
            <i class="bi bi-check-circle-fill text-success me-2 fs-5"></i>
            <div class="fw-semibold small text-success">{{ session('success') }}</div>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger border-0 shadow-sm rounded-3 py-3 mb-4 d-flex align-items-center">
            <i class="bi bi-exclamation-triangle-fill text-danger me-2 fs-5"></i>
            <div class="fw-semibold small text-danger">{{ session('error') }}</div>
        </div>
    @endif

    <div class="row g-4">
        {{-- Kolom Kiri: Editor Visual (Drag & Drop) --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                <h5 class="fw-bold text-dark mb-3"><i class="bi bi-palette text-warning me-2"></i>Desain Letak Nama (Drag & Drop)</h5>
                
                {{-- Area Canvas/Pratinjau Sertifikat --}}
                <div class="position-relative border rounded-4 overflow-hidden bg-light shadow-inner d-flex align-items-center justify-content-center" 
                     id="editorWrapper" 
                     style="min-height: 400px; max-width: 100%;">
                    
                    @if($layout->template_path)
                        <img src="{{ asset($layout->template_path) }}" 
                             id="certTemplateImg" 
                             alt="Template Sertifikat" 
                             class="img-fluid w-100" 
                             style="object-fit: contain; pointer-events: none; user-select: none;">
                        
                        {{-- Elemen Nama Draggable --}}
                        <div id="draggableName" 
                             class="position-absolute cursor-move px-3 py-1 rounded border border-warning shadow fw-bold d-inline-flex align-items-center justify-content-center"
                             style="
                                left: {{ $layout->pos_x }}%; 
                                top: {{ $layout->pos_y }}%; 
                                font-size: calc({{ $layout->font_size }}px * 0.4); 
                                color: {{ $layout->font_color }}; 
                                background: rgba(0, 0, 0, 0.45);
                                backdrop-filter: blur(4px);
                                transform: translate(-50%, -50%);
                                user-select: none;
                                white-space: nowrap;
                             ">
                            {{-- Placeholder teks --}}
                            {{ '< NAMA PESERTA >' }}
                        </div>
                    @else
                        <div class="text-center py-5 text-secondary">
                            <i class="bi bi-file-earmark-image d-block mb-3 text-muted" style="font-size: 4rem;"></i>
                            <h6 class="fw-bold mb-1">Belum Ada Template Sertifikat</h6>
                            <p class="small text-muted mb-0">Silakan unggah gambar template JPG/PNG di panel kanan untuk memulai.</p>
                        </div>
                    @endif
                </div>

                @if($layout->template_path)
                    <div class="mt-3 d-flex justify-content-between align-items-center flex-wrap gap-2 text-secondary small">
                        <span><i class="bi bi-info-circle me-1"></i> Klik dan geser kotak <strong>&lt; NAMA PESERTA &gt;</strong> di atas untuk mengatur koordinat cetak.</span>
                        <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2.5 py-1">
                            X: <span id="posXLabel">{{ $layout->pos_x }}</span>% | Y: <span id="posYLabel">{{ $layout->pos_y }}</span>%
                        </span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Kolom Kanan: Pengaturan & Opsi Cetak --}}
        <div class="col-lg-4">
            {{-- Card 1: Pengaturan Aset & Font --}}
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
                <h5 class="fw-bold text-dark mb-3"><i class="bi bi-sliders text-warning me-2"></i>Pengaturan Aset</h5>
                
                <form action="{{ route('admin.season.certificate.layout', $season->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="pos_x" id="inputPosX" value="{{ $layout->pos_x }}">
                    <input type="hidden" name="pos_y" id="inputPosY" value="{{ $layout->pos_y }}">

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Gambar Template (JPG/PNG)</label>
                        <input type="file" name="template" class="form-control rounded-3" accept="image/jpeg,image/png">
                        <div class="form-text text-muted" style="font-size: 0.72rem;">Unggah background sertifikat beresolusi tinggi (rekomendasi 1920x1080px).</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Font Kustom (.ttf)</label>
                        <input type="file" name="font" class="form-control rounded-3" accept=".ttf">
                        <div class="form-text text-muted" style="font-size: 0.72rem;">
                            @if($layout->font_path)
                                <span class="text-success fw-bold"><i class="bi bi-file-earmark-check-fill"></i> Font aktif terpasang</span>
                            @else
                                Menggunakan font bawaan (Poppins Bold).
                            @endif
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-secondary">Ukuran Font (px)</label>
                            <input type="number" name="font_size" id="inputFontSize" class="form-control rounded-3" value="{{ $layout->font_size }}" min="10" max="200" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-secondary">Warna Font</label>
                            <div class="input-group">
                                <input type="color" name="font_color" id="inputFontColor" class="form-control form-control-color w-100 rounded-3 border" value="{{ $layout->font_color }}" style="height: 38px; padding: 2px;" required>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-warning w-100 fw-bold rounded-pill py-2 shadow-sm">
                        <i class="bi bi-save me-1"></i> Simpan Konfigurasi
                    </button>
                </form>
            </div>

            {{-- Card 2: Cetak Massal (Google Drive API) --}}
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
                <h5 class="fw-bold text-dark mb-3"><i class="bi bi-google text-warning me-2"></i>Cetak ke Google Drive</h5>
                
                @if(!$googleConnected)
                    <p class="text-secondary small mb-3">
                        Hubungkan dengan Google Drive untuk mengunggah seluruh sertifikat secara otomatis ke folder pilihan Anda.
                    </p>
                    <a href="{{ route('admin.certificate.google-login') }}" class="btn btn-outline-danger w-100 fw-bold rounded-pill py-2 d-flex align-items-center justify-content-center gap-2">
                        <i class="bi bi-google"></i> Hubungkan Google Drive
                    </a>
                @else
                    <div class="p-3 border rounded-3 bg-light mb-3">
                        <div class="small text-secondary mb-1">Terhubung sebagai:</div>
                        <div class="fw-bold text-dark text-truncate" style="font-size: 0.85rem;"><i class="bi bi-person-check-fill text-success me-1"></i>{{ $googleUserEmail ?? 'Akun Google Aktif' }}</div>
                    </div>

                    <div class="mb-3" id="driveUploadSection">
                        <label class="form-label small fw-bold text-secondary">Link Folder Google Drive Tujuan</label>
                        <input type="url" id="googleDriveLink" class="form-control rounded-3" placeholder="https://drive.google.com/drive/folders/..." required>
                        <div class="form-text text-muted" style="font-size: 0.72rem;">Pastikan folder disetel publik/bisa diakses sebelum mulai generate.</div>
                    </div>

                    <button type="button" id="btnGenerateToDrive" class="btn btn-danger w-100 fw-bold rounded-pill py-2 shadow-sm" {{ !$layout->template_path ? 'disabled' : '' }}>
                        <i class="bi bi-lightning-charge-fill me-1"></i> Generate & Upload ({{ $paidTeamsCount }} Tim)
                    </button>

                    {{-- Progress Bar (Dynamic) --}}
                    <div class="mt-3" id="progressContainer" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-1 small text-secondary">
                            <span id="progressText">Memproses sertifikat...</span>
                            <span id="progressPercent" class="fw-bold">0%</span>
                        </div>
                        <div class="progress rounded-pill" style="height: 8px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-danger rounded-pill" id="progressBar" role="progressbar" style="width: 0%;"></div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Card 3: Cetak Manual / Download Instan --}}
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
                <h5 class="fw-bold text-dark mb-3"><i class="bi bi-file-earmark-pdf text-warning me-2"></i>Cetak / Edit Manual</h5>
                <p class="text-secondary small mb-3">
                    Unduh sertifikat secara manual dengan mengetik nama secara langsung tanpa menyimpan data di database.
                </p>

                <form action="{{ route('admin.certificate.download-single') }}" method="GET" target="_blank">
                    <input type="hidden" name="season_id" value="{{ $season->id }}">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Ketik Nama Peserta</label>
                        <input type="text" name="name" class="form-control rounded-3" placeholder="Contoh: Muhammad Agus" required>
                    </div>
                    <button type="submit" class="btn btn-outline-warning text-dark w-100 fw-bold rounded-pill py-2" {{ !$layout->template_path ? 'disabled' : '' }}>
                        <i class="bi bi-download me-1"></i> Pratinjau & Download
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // -------------------------------------------------------------
    // LOGIKA DRAG & DROP EDITOR
    // -------------------------------------------------------------
    const draggable = document.getElementById('draggableName');
    const wrapper = document.getElementById('editorWrapper');
    const templateImg = document.getElementById('certTemplateImg');

    const inputPosX = document.getElementById('inputPosX');
    const inputPosY = document.getElementById('inputPosY');
    const posXLabel = document.getElementById('posXLabel');
    const posYLabel = document.getElementById('posYLabel');

    const inputFontSize = document.getElementById('inputFontSize');
    const inputFontColor = document.getElementById('inputFontColor');

    if (draggable && wrapper) {
        let isDragging = false;

        draggable.addEventListener('mousedown', function(e) {
            isDragging = true;
            draggable.style.cursor = 'grabbing';
            e.preventDefault();
        });

        document.addEventListener('mousemove', function(e) {
            if (!isDragging) return;

            const rect = wrapper.getBoundingClientRect();
            
            // Dapatkan koordinat mouse relatif terhadap area pratinjau sertifikat
            let x = e.clientX - rect.left;
            let y = e.clientY - rect.top;

            // Batasi agar tidak melimpah keluar container
            x = Math.max(0, Math.min(x, rect.width));
            y = Math.max(0, Math.min(y, rect.height));

            // Konversi ke persentase
            const percentX = ((x / rect.width) * 100).toFixed(2);
            const percentY = ((y / rect.height) * 100).toFixed(2);

            // Update layout element posisi
            draggable.style.left = percentX + '%';
            draggable.style.top = percentY + '%';

            // Update input tersembunyi & label koordinat
            inputPosX.value = percentX;
            inputPosY.value = percentY;
            
            if (posXLabel) posXLabel.textContent = percentX;
            if (posYLabel) posYLabel.textContent = percentY;
        });

        document.addEventListener('mouseup', function() {
            if (isDragging) {
                isDragging = false;
                draggable.style.cursor = 'grab';
            }
        });

        // Responsif Font Size Live Preview
        if (inputFontSize) {
            inputFontSize.addEventListener('input', function() {
                const size = this.value;
                draggable.style.fontSize = `calc(${size}px * 0.4)`;
            });
        }

        // Responsif Font Color Live Preview
        if (inputFontColor) {
            inputFontColor.addEventListener('input', function() {
                const color = this.value;
                draggable.style.color = color;
            });
        }
    }

    // -------------------------------------------------------------
    // SINKRONISASI & GENERATE MASAL KE GOOGLE DRIVE
    // -------------------------------------------------------------
    const btnGenerateToDrive = document.getElementById('btnGenerateToDrive');
    if (btnGenerateToDrive) {
        btnGenerateToDrive.addEventListener('click', function() {
            const driveLinkInput = document.getElementById('googleDriveLink');
            const driveLink = driveLinkInput.value.trim();

            if (!driveLink) {
                alert('Silakan isi Link Folder Google Drive tujuan terlebih dahulu.');
                return;
            }

            // Show progress state
            const progressContainer = document.getElementById('progressContainer');
            const progressBar = document.getElementById('progressBar');
            const progressPercent = document.getElementById('progressPercent');
            const progressText = document.getElementById('progressText');

            progressContainer.style.display = 'block';
            progressBar.style.width = '10%';
            progressPercent.textContent = '10%';
            progressText.textContent = 'Menghubungkan ke Google Drive API...';
            btnGenerateToDrive.disabled = true;

            const formData = new FormData();
            formData.append('drive_link', driveLink);

            // Ajax request to start background generate and upload
            progressBar.style.width = '30%';
            progressPercent.textContent = '30%';
            progressText.textContent = 'Memulai proses cetak gambar...';

            fetch("{{ route('admin.season.certificate.generate-drive', $season->id) }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(r => r.json())
            .then(res => {
                if (res.success) {
                    progressBar.style.width = '100%';
                    progressPercent.textContent = '100%';
                    progressText.textContent = 'Selesai!';
                    
                    setTimeout(() => {
                        alert(res.message);
                        progressContainer.style.display = 'none';
                        btnGenerateToDrive.disabled = false;
                    }, 500);
                } else {
                    alert('Gagal: ' + res.message);
                    progressContainer.style.display = 'none';
                    btnGenerateToDrive.disabled = false;
                }
            })
            .catch(err => {
                console.error(err);
                alert('Terjadi kesalahan koneksi atau limit memori server terlampaui saat mencetak sertifikat.');
                progressContainer.style.display = 'none';
                btnGenerateToDrive.disabled = false;
            });
        });
    }
});
</script>
@endpush
