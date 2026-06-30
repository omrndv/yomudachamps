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
                        Desain tata letak sertifikat secara visual sekelas Figma. Geser, tambah teks, atau masukkan logo/hero kustom secara interaktif.
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
        {{-- Kolom Kiri: Workspace Editor Figma-Level & Panel Unduh --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                    <h5 class="fw-bold text-dark mb-0"><i class="bi bi-palette text-warning me-2"></i>Workspace Editor</h5>
                    
                    {{-- Element Toolkit --}}
                    @if($layout->template_path)
                        <div class="d-flex gap-2">
                            <button type="button" id="btnAddText" class="btn btn-outline-warning text-dark btn-sm fw-bold rounded-pill px-3">
                                <i class="bi bi-fonts me-1"></i> + Teks Kustom
                            </button>
                            <button type="button" id="btnAddImage" class="btn btn-outline-warning text-dark btn-sm fw-bold rounded-pill px-3">
                                <i class="bi bi-image me-1"></i> + Logo/Gambar
                            </button>
                            <input type="file" id="elementImageLoader" accept="image/*" style="display: none;">
                        </div>
                    @endif
                </div>
                
                {{-- Area Canvas/Pratinjau Sertifikat --}}
                <div class="position-relative border rounded-4 overflow-hidden bg-light shadow-inner d-flex align-items-center justify-content-center" 
                     id="editorWrapper" 
                     style="min-height: 480px; max-width: 100%; user-select: none;">
                    
                    @if($layout->template_path)
                        @php
                            $isPdf = strtolower(pathinfo($layout->template_path, PATHINFO_EXTENSION)) === 'pdf';
                        @endphp
                        
                        <img src="{{ asset($layout->template_path) }}" 
                             id="certTemplateImg" 
                             alt="Template Sertifikat" 
                             class="img-fluid w-100" 
                             style="object-fit: contain; pointer-events: none; user-select: none; {{ $isPdf ? 'display: none;' : '' }}">

                        @if($isPdf)
                            <canvas id="pdfCanvas" class="w-100" style="object-fit: contain; pointer-events: none; user-select: none;"></canvas>
                        @endif

                        {{-- Draggable Elements Container --}}
                        <div id="elementsContainer" class="position-absolute top-0 start-0 w-100 h-100" style="pointer-events: none;"></div>
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
                        <span><i class="bi bi-info-circle me-1"></i> Klik dan geser elemen di atas untuk memposisikan letak cetak secara instan. Gunakan Arrow Keys ← ↑ → ↓ untuk menggeser presisi.</span>
                        <button type="button" id="btnSaveConfig" class="btn btn-warning text-dark btn-sm fw-bold rounded-pill px-4 shadow-sm">
                            <i class="bi bi-cloud-check-fill me-1"></i> Simpan Desain Layout
                        </button>
                    </div>
                @endif
            </div>

            {{-- Row Cetak Massal & Cetak Manual (Ditaruh di Bawah Canvas agar Rapi) --}}
            @if($layout->template_path)
                <div class="row g-4 mb-4">
                    {{-- Cetak Massal Google Drive --}}
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100">
                            <h5 class="fw-bold text-dark mb-3"><i class="bi bi-google text-warning me-2"></i>Cetak ke Google Drive</h5>
                            
                            @if(!$googleConnected)
                                <p class="text-secondary small mb-3">
                                    Hubungkan dengan Google Drive untuk mengunggah seluruh sertifikat secara otomatis ke folder pilihan Anda.
                                </p>
                                <a href="{{ route('admin.certificate.google-login', ['season_id' => $season->id]) }}" class="btn btn-outline-danger w-100 fw-bold rounded-pill py-2 d-flex align-items-center justify-content-center gap-2">
                                    <i class="bi bi-google"></i> Hubungkan Google Drive
                                </a>
                            @else
                                <div class="p-3 border rounded-3 bg-light mb-3">
                                    <div class="small text-secondary mb-1">Terhubung sebagai:</div>
                                    <div class="fw-bold text-dark text-truncate mb-2" style="font-size: 0.85rem;"><i class="bi bi-person-check-fill text-success me-1"></i>{{ $googleUserEmail ?? 'Akun Google Aktif' }}</div>
                                    <a href="{{ route('admin.certificate.google-disconnect') }}" class="btn btn-sm btn-outline-danger w-100 rounded-pill py-1 fw-bold" style="font-size: 0.72rem;">
                                        <i class="bi bi-box-arrow-right me-1"></i> Putuskan & Ganti Akun
                                    </a>
                                </div>

                                <div class="mb-3" id="driveUploadSection">
                                    <label class="form-label small fw-bold text-secondary">Link Folder Google Drive Tujuan</label>
                                    <input type="url" id="googleDriveLink" class="form-control rounded-3" value="{{ $layout->google_drive_link }}" placeholder="https://drive.google.com/drive/folders/..." required>
                                    <div class="form-text text-muted" style="font-size: 0.72rem;">Pastikan folder disetel publik/bisa diakses sebelum mulai generate.</div>
                                </div>

                                <button type="button" id="btnGenerateToDrive" class="btn btn-danger w-100 fw-bold rounded-pill py-2 shadow-sm">
                                    <i class="bi bi-lightning-charge-fill me-1"></i> Generate & Upload ({{ $paidTeamsCount }} Tim)
                                </button>

                                {{-- Progress Bar (Dynamic) --}}
                                <div class="mt-3" id="progressContainer" style="display: none;">
                                    <div class="d-flex justify-content-between align-items-center mb-1 small text-secondary">
                                        <span id="progressText">Memproses sertifikat...</span>
                                        <span id="progressPercent" class="fw-bold">0%</span>
                                    </div>
                                    <div class="progress rounded-pill mb-3" style="height: 8px;">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-danger rounded-pill" id="progressBar" role="progressbar" style="width: 0%;"></div>
                                    </div>

                                    {{-- Terminal Console Log --}}
                                    <div class="bg-dark text-success p-3 rounded-3 font-monospace small overflow-y-auto" 
                                         id="terminalConsole" 
                                         style="max-height: 150px; font-size: 0.72rem; line-height: 1.4; border: 1px solid #334155;">
                                        [SYSTEM] Menunggu pemrosesan...
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Cetak Manual / Download Instan --}}
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm rounded-4 bg-white p-4 h-100">
                            <h5 class="fw-bold text-dark mb-3"><i class="bi bi-file-earmark-pdf text-warning me-2"></i>Cetak / Edit Manual</h5>
                            <p class="text-secondary small mb-3">
                                Unduh sertifikat secara manual dengan mengetik nama secara langsung tanpa menyimpan data di database.
                            </p>

                            <form action="{{ route('admin.certificate.download-single') }}" method="GET" target="_blank" class="mt-auto">
                                <input type="hidden" name="season_id" value="{{ $season->id }}">
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-secondary">Ketik Nama Peserta</label>
                                    <input type="text" name="name" class="form-control rounded-3" placeholder="Contoh: Muhammad Agus" required>
                                </div>
                                <button type="submit" class="btn btn-outline-warning text-dark w-100 fw-bold rounded-pill py-2">
                                    <i class="bi bi-download me-1"></i> Pratinjau & Download
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        {{-- Kolom Kanan: Properties Inspector & Background Settings --}}
        <div class="col-lg-4">
            {{-- Card 1: Element Properties Inspector (Figma-Style Properties Panel) --}}
            @if($layout->template_path)
                <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4" id="propertiesInspector" style="display: none;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold text-dark mb-0"><i class="bi bi-sliders text-warning me-2"></i>Inspektur Elemen</h5>
                        <button type="button" id="btnDeleteElement" class="btn btn-sm btn-outline-danger border-0 rounded-circle" style="padding: 2px 6px;">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                    
                    <div class="border-top pt-3">
                        {{-- Field 1: Konten Teks (Khusus Teks) --}}
                        <div class="mb-3" id="propTextContainer">
                            <label class="form-label small fw-bold text-secondary">Isi Teks</label>
                            <textarea id="propTextContent" class="form-control rounded-3" rows="2" placeholder="Masukkan teks..."></textarea>
                            <div class="form-text text-muted" id="propTextHelp" style="font-size: 0.68rem;">Gunakan tag `< NAMA PESERTA >` untuk nama dinamis. Gunakan `**kata**` untuk menebalkan kata tertentu.</div>
                        </div>

                        {{-- Field 2: Ukuran Font & Warna (Teks Only) --}}
                        <div class="row g-2 mb-3" id="propFontContainer">
                            <div class="col-6">
                                <label class="form-label small fw-bold text-secondary">Ukuran Font</label>
                                <input type="number" id="propFontSize" class="form-control rounded-3" min="10" max="250">
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold text-secondary">Warna Teks</label>
                                <input type="color" id="propFontColor" class="form-control form-control-color w-100 rounded-3 border" style="height: 38px; padding: 2px;">
                            </div>
                        </div>

                        {{-- Field 3: Align & Style (Teks Only) --}}
                        <div class="mb-3" id="propStyleContainer">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="small fw-bold text-secondary">Format & Perataan</span>
                            </div>
                            <div class="btn-group w-100" role="group">
                                <input type="checkbox" class="btn-check" id="propFontBold" autocomplete="off">
                                <label class="btn btn-outline-secondary btn-sm" for="propFontBold"><i class="bi bi-type-bold"></i> Bold</label>

                                <input type="radio" class="btn-check" name="propAlign" id="propAlignLeft" value="left" autocomplete="off">
                                <label class="btn btn-outline-secondary btn-sm" for="propAlignLeft"><i class="bi bi-text-left"></i> Rata Kiri</label>

                                <input type="radio" class="btn-check" name="propAlign" id="propAlignCenter" value="center" autocomplete="off">
                                <label class="btn btn-outline-secondary btn-sm" for="propAlignCenter"><i class="bi bi-text-center"></i> Rata Tengah</label>
                            </div>
                        </div>

                        {{-- Field 4: Dimensi Ukuran Gambar (Gambar Only) --}}
                        <div class="row g-2 mb-3" id="propDimensionContainer" style="display: none;">
                            <div class="col-6">
                                <label class="form-label small fw-bold text-secondary">Lebar (px)</label>
                                <input type="number" id="propImageWidth" class="form-control rounded-3" min="10">
                            </div>
                            <div class="col-6">
                                <label class="form-label small fw-bold text-secondary">Tinggi (px)</label>
                                <input type="number" id="propImageHeight" class="form-control rounded-3" min="10">
                            </div>
                        </div>

                        {{-- Info Koordinat --}}
                        <div class="bg-light p-2.5 rounded-3 d-flex justify-content-between text-secondary style-info" style="font-size: 0.72rem;">
                            <span>Koordinat X: <strong id="propPosXLabel">0</strong>%</span>
                            <span>Koordinat Y: <strong id="propPosYLabel">0</strong>%</span>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Card 2: Pengaturan Background Template --}}
            <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
                <h5 class="fw-bold text-dark mb-3"><i class="bi bi-file-earmark-arrow-up text-warning me-2"></i>Ganti Background</h5>
                
                <form id="layoutForm" action="{{ route('admin.season.certificate.layout', $season->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="pos_x" id="inputPosX" value="{{ $layout->pos_x }}">
                    <input type="hidden" name="pos_y" id="inputPosY" value="{{ $layout->pos_y }}">
                    <input type="hidden" name="font_size" id="inputFontSize" value="{{ $layout->font_size }}">
                    <input type="hidden" name="font_color" id="inputFontColor" value="{{ $layout->font_color }}">
                    <input type="hidden" name="layout_data" id="layoutDataField">

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Upload Background Template (PDF/JPG/PNG)</label>
                        <input type="file" id="certTemplateInput" name="template" class="form-control rounded-3" accept="image/jpeg,image/png,application/pdf">
                        <div class="form-text text-muted" style="font-size: 0.72rem;">Unggah background sertifikat beresolusi tinggi.</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-bold text-secondary">Font Kustom (.ttf)</label>
                        <input type="file" name="font" class="form-control rounded-3" accept=".ttf">
                        <div class="form-text text-muted" style="font-size: 0.72rem;">
                            @if($layout->font_path)
                                <span class="text-success fw-bold"><i class="bi bi-file-earmark-check-fill"></i> Font aktif terpasang</span>
                            @else
                                Menggunakan font bawaan (Arial).
                            @endif
                        </div>
                    </div>

                    <button type="submit" class="btn btn-outline-warning text-dark w-100 fw-bold rounded-pill py-2 shadow-sm">
                        <i class="bi bi-save me-1"></i> Upload & Simpan File
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.min.js"></script>
<script>
pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.worker.min.js';

document.addEventListener('DOMContentLoaded', function() {
    const wrapper = document.getElementById('editorWrapper');
    const container = document.getElementById('elementsContainer');
    const templateImg = document.getElementById('certTemplateImg');
    const pdfCanvas = document.getElementById('pdfCanvas');
    
    // Properties panel components
    const inspector = document.getElementById('propertiesInspector');
    const propTextContainer = document.getElementById('propTextContainer');
    const propTextContent = document.getElementById('propTextContent');
    const propFontContainer = document.getElementById('propFontContainer');
    const propFontSize = document.getElementById('propFontSize');
    const propFontColor = document.getElementById('propFontColor');
    const propStyleContainer = document.getElementById('propStyleContainer');
    const propFontBold = document.getElementById('propFontBold');
    const propAlignLeft = document.getElementById('propAlignLeft');
    const propAlignCenter = document.getElementById('propAlignCenter');
    const propDimensionContainer = document.getElementById('propDimensionContainer');
    const propImageWidth = document.getElementById('propImageWidth');
    const propImageHeight = document.getElementById('propImageHeight');
    const propPosXLabel = document.getElementById('propPosXLabel');
    const propPosYLabel = document.getElementById('propPosYLabel');
    const btnDeleteElement = document.getElementById('btnDeleteElement');

    // Layout configuration elements
    let elements = @json($layout->layout_data ?? []);
    let selectedElementId = null;

    // Load PDF if template is PDF
    if (pdfCanvas) {
        const url = "{{ asset($layout->template_path) }}";
        pdfjsLib.getDocument(url).promise.then(pdf => {
            pdf.getPage(1).then(page => {
                const viewport = page.getViewport({ scale: 1.5 });
                const context = pdfCanvas.getContext('2d');
                pdfCanvas.height = viewport.height;
                pdfCanvas.width = viewport.width;

                const renderContext = {
                    canvasContext: context,
                    viewport: viewport
                };
                page.render(renderContext).promise.then(() => {
                    renderWorkspace();
                });
            });
        }).catch(err => {
            console.error('Error rendering PDF:', err);
        });
    }

    // Initialize default elements if layout_data is empty
    if (!elements || elements.length === 0) {
        elements = [
            {
                id: 'participant_name',
                type: 'text',
                text: '< NAMA PESERTA >',
                x: parseFloat("{{ $layout->pos_x }}") || 50.0,
                y: parseFloat("{{ $layout->pos_y }}") || 50.0,
                font_size: parseInt("{{ $layout->font_size }}") || 48,
                color: "{{ $layout->font_color }}" || '#ffc107',
                bold: true,
                align: 'center',
                is_dynamic_name: true
            }
        ];
    }

    // Render workspace layer by layer
    function renderWorkspace() {
        if (!container) return;
        container.innerHTML = '';

        let originalWidth = 1920; // fallback scale
        if (templateImg && templateImg.complete && templateImg.naturalWidth) {
            originalWidth = templateImg.naturalWidth;
        } else if (pdfCanvas) {
            originalWidth = pdfCanvas.width || 1920;
        }
        
        const scale = wrapper.clientWidth / originalWidth;

        elements.forEach(el => {
            const div = document.createElement('div');
            div.id = 'el-' + el.id;
            div.className = 'position-absolute cursor-move';
            div.style.left = el.x + '%';
            div.style.top = el.y + '%';
            
            // Set transform horizontal berdasarkan perataan aligment teks agar cocok dengan PDF render engine
            if (el.type === 'text' && el.align === 'left') {
                div.style.transform = 'translate(0%, -50%)';
            } else if (el.type === 'text' && el.align === 'right') {
                div.style.transform = 'translate(-100%, -50%)';
            } else {
                div.style.transform = 'translate(-50%, -50%)';
            }
            
            div.style.pointerEvents = 'auto'; // allow mouse events on dynamic elements
            
            // Selection indicator border
            if (el.id === selectedElementId) {
                div.style.border = '2px dashed #f59e0b';
                div.style.padding = '4px';
                div.style.borderRadius = '4px';
                div.style.zIndex = '999';
            }

            if (el.type === 'text') {
                const escapedText = el.text
                    .replace(/&/g, "&amp;")
                    .replace(/</g, "&lt;")
                    .replace(/>/g, "&gt;")
                    .replace(/"/g, "&quot;")
                    .replace(/'/g, "&#039;");
                
                // Ganti markdown bold (**teks**) menjadi HTML <strong> dan pertahankan spasi dengan white-space pre
                div.innerHTML = escapedText.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
                div.style.fontSize = (el.font_size * scale) + 'px';
                div.style.color = el.color;
                div.style.fontWeight = el.bold ? 'bold' : 'normal';
                div.style.whiteSpace = 'pre'; // Pertahankan spasi agar layaknya span/pre
                div.style.textAlign = el.align || 'center';
            } else if (el.type === 'image') {
                const img = document.createElement('img');
                img.src = el.src;
                img.style.width = (el.width * scale) + 'px';
                img.style.height = (el.height * scale) + 'px';
                img.style.pointerEvents = 'none'; // prevent image tags dragging default behaviour
                img.style.objectFit = 'contain';
                div.appendChild(img);
            }

            // Bind click handler for selection
            div.addEventListener('click', function(e) {
                e.stopPropagation();
                selectElement(el.id);
            });

            // Bind drag handler
            bindDragHandler(div, el);

            container.appendChild(div);
        });

        // Update Hidden Field value for form submits
        const layoutDataField = document.getElementById('layoutDataField');
        if (layoutDataField) {
            layoutDataField.value = JSON.stringify(elements);
        }
    }

    // Drag and Drop core logic (Live dragging using absolute pixel offset to avoid top-left jumps and lag)
    function bindDragHandler(elementDiv, el) {
        let isDragging = false;
        let hasMoved = false;
        let startX, startY;
        let initialX, initialY;

        elementDiv.addEventListener('mousedown', function(e) {
            isDragging = true;
            hasMoved = false;
            elementDiv.style.cursor = 'grabbing';
            
            // Pilih elemen tetapi bypass renderWorkspace secara destruktif untuk mencegah node DOM diganti saat drag dimulai
            selectElementWithoutRedraw(el.id);
            
            const rect = wrapper.getBoundingClientRect();
            startX = e.clientX;
            startY = e.clientY;
            
            // Dapatkan koordinat awal elemen dalam pixel
            initialX = (el.x / 100) * rect.width;
            initialY = (el.y / 100) * rect.height;
            
            e.preventDefault();
            e.stopPropagation();
        });

        document.addEventListener('mousemove', function(e) {
            if (!isDragging) return;
            hasMoved = true;

            const rect = wrapper.getBoundingClientRect();
            const deltaX = e.clientX - startX;
            const deltaY = e.clientY - startY;

            let newX = initialX + deltaX;
            let newY = initialY + deltaY;

            newX = Math.max(0, Math.min(newX, rect.width));
            newY = Math.max(0, Math.min(newY, rect.height));

            // Set posisi langsung dalam pixel untuk pergerakan real-time 60fps tanpa lag
            elementDiv.style.left = newX + 'px';
            elementDiv.style.top = newY + 'px';

            const percentX = parseFloat(((newX / rect.width) * 100).toFixed(2));
            const percentY = parseFloat(((newY / rect.height) * 100).toFixed(2));

            // Update state koordinat internal secara live
            el.x = percentX;
            el.y = percentY;

            if (selectedElementId === el.id) {
                propPosXLabel.textContent = percentX;
                propPosYLabel.textContent = percentY;
            }
        });

        document.addEventListener('mouseup', function() {
            if (isDragging) {
                isDragging = false;
                elementDiv.style.cursor = 'grab';
                
                if (hasMoved) {
                    const rect = wrapper.getBoundingClientRect();
                    const currentPixelLeft = parseFloat(elementDiv.style.left);
                    const currentPixelTop = parseFloat(elementDiv.style.top);
                    
                    // Konversi kembali koordinat pixel akhir ke persentase untuk database
                    el.x = parseFloat(((currentPixelLeft / rect.width) * 100).toFixed(2));
                    el.y = parseFloat(((currentPixelTop / rect.height) * 100).toFixed(2));
                }
                
                renderWorkspace();
            }
        });
    }

    // Select element WITH full redraw (when clicked normally)
    function selectElement(id) {
        selectedElementId = id;
        renderWorkspace();
        loadProperties(id);
    }

    // Select element WITHOUT redraw (called on mousedown to prevent deleting/recreating DOM node which causes jumps)
    function selectElementWithoutRedraw(id) {
        selectedElementId = id;
        
        // Atur border seleksi secara dinamis langsung ke DOM
        elements.forEach(item => {
            const div = document.getElementById('el-' + item.id);
            if (div) {
                if (item.id === id) {
                    div.style.border = '2px dashed #f59e0b';
                    div.style.padding = '4px';
                    div.style.borderRadius = '4px';
                    div.style.zIndex = '999';
                } else {
                    div.style.border = '';
                    div.style.padding = '';
                    div.style.borderRadius = '';
                    div.style.zIndex = '';
                }
            }
        });

        loadProperties(id);
    }

    // Load selected element properties to properties panel
    function loadProperties(id) {
        const el = elements.find(item => item.id === id);
        if (!el) {
            if (inspector) inspector.style.display = 'none';
            return;
        }

        if (inspector) inspector.style.display = 'block';

        propPosXLabel.textContent = el.x;
        propPosYLabel.textContent = el.y;

        if (el.type === 'text') {
            propTextContainer.style.display = 'block';
            propFontContainer.style.display = 'flex';
            propStyleContainer.style.display = 'block';
            propDimensionContainer.style.display = 'none';

            propTextContent.value = el.text;
            propFontSize.value = el.font_size;
            propFontColor.value = el.color;
            propFontBold.checked = el.bold;
            
            if (el.align === 'left') {
                propAlignLeft.checked = true;
            } else {
                propAlignCenter.checked = true;
            }

            if (el.is_dynamic_name) {
                propTextContent.disabled = true;
                propTextHelp.innerText = "Tag nama peserta utama diatur otomatis oleh sistem.";
            } else {
                propTextContent.disabled = false;
                propTextHelp.innerText = "Ketik teks kustom Anda bebas. Gunakan **kata** untuk menebalkan kata tertentu.";
            }
        } else if (el.type === 'image') {
            propTextContainer.style.display = 'none';
            propFontContainer.style.display = 'none';
            propStyleContainer.style.display = 'none';
            propDimensionContainer.style.display = 'flex';

            propImageWidth.value = el.width;
            propImageHeight.value = el.height;
        }
    }

    // Keyboard Arrow Keys navigation (Figma-Style Element Positioning)
    document.addEventListener('keydown', function(e) {
        if (!selectedElementId) return;

        // Skip jika sedang fokus mengetik di input/textarea
        const activeEl = document.activeElement;
        if (activeEl && (activeEl.tagName === 'INPUT' || activeEl.tagName === 'TEXTAREA')) {
            return;
        }

        const el = elements.find(item => item.id === selectedElementId);
        if (!el) return;

        // Shift + Arrow moves 1.0%, normal Arrow moves 0.1%
        const step = e.shiftKey ? 1.0 : 0.1;

        if (e.key === 'ArrowUp') {
            el.y = parseFloat((el.y - step).toFixed(2));
            e.preventDefault();
        } else if (e.key === 'ArrowDown') {
            el.y = parseFloat((el.y + step).toFixed(2));
            e.preventDefault();
        } else if (e.key === 'ArrowLeft') {
            el.x = parseFloat((el.x - step).toFixed(2));
            e.preventDefault();
        } else if (e.key === 'ArrowRight') {
            el.x = parseFloat((el.x + step).toFixed(2));
            e.preventDefault();
        } else {
            return;
        }

        // Terapkan ke DOM secara real-time tanpa menggambar ulang seluruh kontainer
        const div = document.getElementById('el-' + el.id);
        if (div) {
            div.style.left = el.x + '%';
            div.style.top = el.y + '%';
        }

        // Update teks label koordinat di panel kanan
        propPosXLabel.textContent = el.x;
        propPosYLabel.textContent = el.y;

        // Update hidden field data
        const layoutDataField = document.getElementById('layoutDataField');
        if (layoutDataField) {
            layoutDataField.value = JSON.stringify(elements);
        }
    });

    // Deselect elements when clicking wrapper or canvas background
    if (wrapper) {
        wrapper.addEventListener('click', function() {
            selectedElementId = null;
            if (inspector) inspector.style.display = 'none';
            renderWorkspace();
        });
    }

    // Properties event listeners
    if (propTextContent) {
        propTextContent.addEventListener('input', function() {
            if (selectedElementId) {
                const el = elements.find(item => item.id === selectedElementId);
                if (el && el.type === 'text') {
                    el.text = this.value;
                    renderWorkspace();
                }
            }
        });
    }

    if (propFontSize) {
        propFontSize.addEventListener('input', function() {
            if (selectedElementId) {
                const el = elements.find(item => item.id === selectedElementId);
                if (el && el.type === 'text') {
                    el.font_size = parseInt(this.value) || 24;
                    if (el.is_dynamic_name) {
                        document.getElementById('inputFontSize').value = el.font_size;
                    }
                    renderWorkspace();
                }
            }
        });
    }

    if (propFontColor) {
        propFontColor.addEventListener('input', function() {
            if (selectedElementId) {
                const el = elements.find(item => item.id === selectedElementId);
                if (el && el.type === 'text') {
                    el.color = this.value;
                    if (el.is_dynamic_name) {
                        document.getElementById('inputFontColor').value = el.color;
                    }
                    renderWorkspace();
                }
            }
        });
    }

    if (propFontBold) {
        propFontBold.addEventListener('change', function() {
            if (selectedElementId) {
                const el = elements.find(item => item.id === selectedElementId);
                if (el && el.type === 'text') {
                    el.bold = this.checked;
                    renderWorkspace();
                }
            }
        });
    }

    if (propAlignLeft) {
        propAlignLeft.addEventListener('change', function() {
            if (selectedElementId && this.checked) {
                const el = elements.find(item => item.id === selectedElementId);
                if (el) el.align = 'left';
                renderWorkspace();
            }
        });
    }

    if (propAlignCenter) {
        propAlignCenter.addEventListener('change', function() {
            if (selectedElementId && this.checked) {
                const el = elements.find(item => item.id === selectedElementId);
                if (el) el.align = 'center';
                renderWorkspace();
            }
        });
    }

    if (propImageWidth) {
        propImageWidth.addEventListener('input', function() {
            if (selectedElementId) {
                const el = elements.find(item => item.id === selectedElementId);
                if (el && el.type === 'image') {
                    el.width = parseInt(this.value) || 50;
                    renderWorkspace();
                }
            }
        });
    }

    if (propImageHeight) {
        propImageHeight.addEventListener('input', function() {
            if (selectedElementId) {
                const el = elements.find(item => item.id === selectedElementId);
                if (el && el.type === 'image') {
                    el.height = parseInt(this.value) || 50;
                    renderWorkspace();
                }
            }
        });
    }

    // Delete Element
    if (btnDeleteElement) {
        btnDeleteElement.addEventListener('click', function() {
            if (selectedElementId) {
                const el = elements.find(item => item.id === selectedElementId);
                if (el && el.is_dynamic_name) {
                    alert('Tag nama peserta utama tidak dapat dihapus!');
                    return;
                }

                if (confirm('Hapus elemen terpilih ini?')) {
                    elements = elements.filter(item => item.id !== selectedElementId);
                    selectedElementId = null;
                    if (inspector) inspector.style.display = 'none';
                    renderWorkspace();
                }
            }
        });
    }

    // Add Text Element
    const btnAddText = document.getElementById('btnAddText');
    if (btnAddText) {
        btnAddText.addEventListener('click', function() {
            const newTextEl = {
                id: 'text_' + Date.now(),
                type: 'text',
                text: 'Teks Kustom',
                x: 50,
                y: 50,
                font_size: 28,
                color: '#ffffff',
                bold: false,
                align: 'center'
            };
            elements.push(newTextEl);
            selectElement(newTextEl.id);
        });
    }

    // Add Image Element (AJAX element image uploader)
    const btnAddImage = document.getElementById('btnAddImage');
    const imageLoader = document.getElementById('elementImageLoader');

    if (btnAddImage && imageLoader) {
        btnAddImage.addEventListener('click', function() {
            imageLoader.click();
        });

        imageLoader.addEventListener('change', function() {
            if (imageLoader.files && imageLoader.files[0]) {
                const file = imageLoader.files[0];
                const formData = new FormData();
                formData.append('image', file);

                btnAddImage.disabled = true;
                btnAddImage.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Uploading...';

                fetch("{{ route('admin.season.certificate.upload-element', $season->id) }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                })
                .then(r => r.json())
                .then(res => {
                    btnAddImage.disabled = false;
                    btnAddImage.innerHTML = '<i class="bi bi-image me-1"></i> + Logo/Gambar';
                    imageLoader.value = ''; // clear input

                    if (res.success) {
                        const newImgEl = {
                            id: 'image_' + Date.now(),
                            type: 'image',
                            src: res.path,
                            x: 50,
                            y: 50,
                            width: 120,
                            height: 120
                        };
                        elements.push(newImgEl);
                        selectElement(newImgEl.id);
                    } else {
                        alert('Gagal mengunggah aset gambar: ' + res.message);
                    }
                })
                .catch(err => {
                    btnAddImage.disabled = false;
                    btnAddImage.innerHTML = '<i class="bi bi-image me-1"></i> + Logo/Gambar';
                    imageLoader.value = '';
                    console.error(err);
                    alert('Terjadi kesalahan saat mengunggah aset.');
                });
            }
        });
    }

    // AJAX Save Configuration Layout
    const btnSaveConfig = document.getElementById('btnSaveConfig');
    if (btnSaveConfig) {
        btnSaveConfig.addEventListener('click', function() {
            // Sync fallback name tag position
            const mainNameEl = elements.find(item => item.is_dynamic_name);
            if (mainNameEl) {
                document.getElementById('inputPosX').value = mainNameEl.x;
                document.getElementById('inputPosY').value = mainNameEl.y;
                document.getElementById('inputFontSize').value = mainNameEl.font_size;
                document.getElementById('inputFontColor').value = mainNameEl.color;
            }

            const formData = new FormData(document.getElementById('layoutForm'));
            formData.set('layout_data', JSON.stringify(elements));

            const driveLinkEl = document.getElementById('googleDriveLink');
            if (driveLinkEl) {
                formData.set('google_drive_link', driveLinkEl.value.trim());
            }

            btnSaveConfig.disabled = true;
            btnSaveConfig.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';

            fetch("{{ route('admin.season.certificate.layout', $season->id) }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(r => r.json())
            .then(res => {
                btnSaveConfig.disabled = false;
                btnSaveConfig.innerHTML = '<i class="bi bi-cloud-check-fill me-1"></i> Simpan Desain Layout';
                if (res.success) {
                    alert('Konfigurasi tata letak berhasil disimpan!');
                } else {
                    alert('Gagal: ' + res.message);
                }
            })
            .catch(err => {
                btnSaveConfig.disabled = false;
                btnSaveConfig.innerHTML = '<i class="bi bi-cloud-check-fill me-1"></i> Simpan Desain Layout';
                console.error(err);
                alert('Terjadi kesalahan koneksi.');
            });
        });
    }

    // Load initial layout elements
    if (templateImg) {
        if (templateImg.complete) {
            renderWorkspace();
        } else {
            templateImg.addEventListener('load', renderWorkspace);
        }
    }
    window.addEventListener('resize', renderWorkspace);

    // -------------------------------------------------------------
    // SINKRONISASI & GENERATE MASAL KE GOOGLE DRIVE (CONSOLE LOG POLLING)
    // -------------------------------------------------------------
    const btnGenerateToDrive = document.getElementById('btnGenerateToDrive');
    let pollInterval = null;

    function startLogPolling() {
        if (pollInterval) clearInterval(pollInterval);
        
        const progressContainer = document.getElementById('progressContainer');
        const progressBar = document.getElementById('progressBar');
        const progressPercent = document.getElementById('progressPercent');
        const progressText = document.getElementById('progressText');
        const terminalConsole = document.getElementById('terminalConsole');
        
        progressContainer.style.display = 'block';
        if (btnGenerateToDrive) btnGenerateToDrive.disabled = true;

        pollInterval = setInterval(() => {
            fetch("{{ route('admin.season.certificate.logs', $season->id) }}")
            .then(r => r.json())
            .then(res => {
                progressBar.style.width = res.progress + '%';
                progressPercent.textContent = res.progress + '%';
                
                if (res.logs && res.logs.length > 0) {
                    terminalConsole.innerHTML = res.logs.map(log => {
                        let colorClass = 'text-success';
                        if (log.includes('❌') || log.includes('🚨')) colorClass = 'text-danger';
                        if (log.includes('✅') || log.includes('🎉')) colorClass = 'text-info';
                        return `<div class="mb-1 ${colorClass}">${log}</div>`;
                    }).join('');
                    
                    terminalConsole.scrollTop = terminalConsole.scrollHeight;
                }

                if (res.status === 'idle') {
                    clearInterval(pollInterval);
                    pollInterval = null;
                    if (btnGenerateToDrive) btnGenerateToDrive.disabled = false;
                    progressText.textContent = 'Sinkronisasi Selesai!';
                } else {
                    progressText.textContent = 'Sedang mensinkronisasi sertifikat...';
                }
            })
            .catch(err => console.error('Error polling logs:', err));
        }, 300);
    }

    fetch("{{ route('admin.season.certificate.logs', $season->id) }}")
    .then(r => r.json())
    .then(res => {
        if (res.status === 'running') {
            startLogPolling();
        }
    });

    if (btnGenerateToDrive) {
        btnGenerateToDrive.addEventListener('click', function() {
            const driveLinkInput = document.getElementById('googleDriveLink');
            const driveLink = driveLinkInput.value.trim();

            if (!driveLink) {
                alert('Silakan isi Link Folder Google Drive tujuan terlebih dahulu.');
                return;
            }

            const formData = new FormData();
            formData.append('drive_link', driveLink);

            if (btnGenerateToDrive) btnGenerateToDrive.disabled = true;

            startLogPolling();

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
                if (!res.success) {
                    alert('Gagal: ' + res.message);
                    if (btnGenerateToDrive) btnGenerateToDrive.disabled = false;
                }
            })
            .catch(err => {
                console.error(err);
                alert('Gagal memulai proses sinkronisasi.');
                if (btnGenerateToDrive) btnGenerateToDrive.disabled = false;
            });
        });
    }

    // Image compressor for background template
    function compressTemplateImage(file, maxWidth, quality) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.readAsDataURL(file);
            reader.onload = function(event) {
                const img = new Image();
                img.src = event.target.result;
                img.onload = function() {
                    const canvas = document.createElement('canvas');
                    let width = img.width;
                    let height = img.height;

                    if (width > maxWidth) {
                        height = Math.round((height * maxWidth) / width);
                        width = maxWidth;
                    }

                    canvas.width = width;
                    canvas.height = height;

                    const ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, width, height);

                    canvas.toBlob((blob) => {
                        if (blob) {
                            resolve(new File([blob], file.name.replace(/\.[^/.]+$/, "") + ".jpg", {
                                type: 'image/jpeg',
                                lastModified: Date.now()
                            }));
                        } else {
                            reject(new Error("Canvas toBlob failed"));
                        }
                    }, 'image/jpeg', quality);
                };
                img.onerror = (err) => reject(err);
            };
            reader.onerror = (err) => reject(err);
        });
    }

    const layoutForm = document.getElementById('layoutForm');
    const certTemplateInput = document.getElementById('certTemplateInput');
    if (layoutForm && certTemplateInput) {
        layoutForm.addEventListener('submit', function(e) {
            if (certTemplateInput.files && certTemplateInput.files[0]) {
                const file = certTemplateInput.files[0];
                const fileType = file.type;

                if (fileType.startsWith('image/')) {
                    e.preventDefault();

                    const submitBtn = layoutForm.querySelector('button[type="submit"]');
                    submitBtn.disabled = true;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Mengompresi & Menyimpan... 🚀';

                    compressTemplateImage(file, 2400, 0.90)
                    .then(compressedFile => {
                        const dataTransfer = new DataTransfer();
                        dataTransfer.items.add(compressedFile);
                        certTemplateInput.files = dataTransfer.files;
                        layoutForm.submit();
                    })
                    .catch(err => {
                        console.error('Compression error, submitting original file:', err);
                        layoutForm.submit();
                    });
                }
            }
        });
    }
});
</script>
@endpush
