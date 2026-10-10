@extends('layouts.admin')

@section('content')
<style>
    .card-custom {
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.06) !important;
        border-radius: 16px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02);
    }
    .form-control-custom {
        width: 100%;
        border-radius: 10px;
        border: 1px solid rgba(0, 0, 0, 0.12);
        padding: 8px 12px;
        font-size: 0.85rem;
        transition: all 0.2s ease;
        background-color: #ffffff;
        color: #1e293b;
    }
    .form-control-custom:focus {
        border-color: #f59e0b;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
        outline: none;
    }
    .nav-wizard .nav-link {
        color: #64748b;
        border: 1px solid transparent;
        transition: all 0.2s ease;
        font-weight: 600;
        border-radius: 12px;
        padding: 12px 18px;
    }
    .nav-wizard .nav-link:hover {
        background-color: #f1f5f9;
        color: #1e293b;
    }
    .nav-wizard .nav-link.active {
        background: #ffffff;
        color: #d97706 !important;
        border: 1px solid rgba(245, 158, 11, 0.3) !important;
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.12);
    }
    .nav-wizard .nav-link.active .step-badge {
        background-color: #f59e0b !important;
        color: #ffffff !important;
    }
    .step-badge {
        width: 28px;
        height: 28px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-size: 0.82rem;
        background-color: #e2e8f0;
        color: #475569;
        font-weight: 700;
        transition: all 0.2s ease;
    }
    .folder-card {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        transition: all 0.2s ease;
        background: #ffffff;
    }
    .folder-card:hover {
        border-color: #f59e0b;
        transform: translateY(-2px);
        box-shadow: 0 6px 14px rgba(0, 0, 0, 0.05);
    }
    .folder-card.is-active-target {
        border-color: #10b981;
        background: #f0fdf4;
    }
    .file-item-card {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        transition: all 0.2s ease;
        background: #ffffff;
    }
    .file-item-card:hover {
        border-color: #cbd5e1;
        background: #f8fafc;
    }
    .cursor-pointer {
        cursor: pointer;
    }
</style>

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
                        Workflow Sertifikat <span class="text-warning">{{ $season->name }}</span>
                    </h2>
                    <p class="text-secondary small mb-0 mt-1">
                        Pilih folder Google Drive &rarr; Desain template visual &rarr; Generate sertifikat & pantau hasil langsung di website.
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.dashboard', $season->id) }}" class="btn btn-outline-secondary btn-sm px-3 fw-bold rounded-pill shadow-sm">
                        <i class="bi bi-arrow-left me-1"></i> Dashboard
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

    {{-- Workflow Wizard Steps Navigation --}}
    <div class="card card-custom p-2 mb-4">
        <ul class="nav nav-pills nav-fill nav-wizard gap-2" id="certWorkflowTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active d-flex align-items-center justify-content-center gap-2" id="step1-tab" data-bs-toggle="pill" data-bs-target="#step1-pane" type="button" role="tab">
                    <span class="step-badge">1</span>
                    <span class="d-flex flex-column text-start">
                        <span class="lh-1" style="font-size: 0.88rem;">Folder Google Drive</span>
                        <small class="text-muted fw-normal" style="font-size: 0.7rem;" id="step1Subtitle">
                            {{ $activeFolder ? 'Terpilih: ' . Str::limit($activeFolder['name'], 20) : 'Pilih / buat folder' }}
                        </small>
                    </span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link d-flex align-items-center justify-content-center gap-2" id="step2-tab" data-bs-toggle="pill" data-bs-target="#step2-pane" type="button" role="tab">
                    <span class="step-badge">2</span>
                    <span class="d-flex flex-column text-start">
                        <span class="lh-1" style="font-size: 0.88rem;">Desain Template</span>
                        <small class="text-muted fw-normal" style="font-size: 0.7rem;">
                            {{ $layout->template_path ? 'Template aktif' : 'Upload background' }}
                        </small>
                    </span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link d-flex align-items-center justify-content-center gap-2" id="step3-tab" data-bs-toggle="pill" data-bs-target="#step3-pane" type="button" role="tab">
                    <span class="step-badge">3</span>
                    <span class="d-flex flex-column text-start">
                        <span class="lh-1" style="font-size: 0.88rem;">Generate & Berkas Drive</span>
                        <small class="text-muted fw-normal" style="font-size: 0.7rem;" id="step3Subtitle">
                            {{ $paidTeamsCount }} tim siap cetak
                        </small>
                    </span>
                </button>
            </li>
        </ul>
    </div>

    {{-- Tab Content --}}
    <div class="tab-content" id="certWorkflowTabContent">
        
        {{-- ========================================== --}}
        {{-- STEP 1: GOOGLE DRIVE FOLDER MANAGER --}}
        {{-- ========================================== --}}
        <div class="tab-pane fade show active" id="step1-pane" role="tabpanel">
            <div class="row g-4">
                {{-- Info Status Akun & Folder Target Aktif --}}
                <div class="col-lg-4">
                    {{-- Status Koneksi Akun Google --}}
                    <div class="card card-custom p-4 mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold text-dark mb-0"><i class="bi bi-google text-danger me-2"></i>Akun Google</h5>
                            @if($googleConnected)
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill small">
                                    <i class="bi bi-check-circle-fill me-1"></i> Terhubung
                                </span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1 rounded-pill small">
                                    <i class="bi bi-x-circle-fill me-1"></i> Belum Login
                                </span>
                            @endif
                        </div>

                        @if(!$googleConnected)
                            <p class="text-secondary small mb-3">
                                Masuk dengan akun Google untuk mengelola folder Google Drive dan mengunggah sertifikat secara otomatis.
                            </p>
                            <a href="{{ route('admin.certificate.google-login', ['season_id' => $season->id]) }}" class="btn btn-outline-danger w-100 fw-bold rounded-pill py-2.5 d-flex align-items-center justify-content-center gap-2 shadow-sm">
                                <i class="bi bi-google"></i> Hubungkan Google Drive
                            </a>
                        @else
                            <div class="p-3 border rounded-3 bg-light mb-3">
                                <div class="text-muted small mb-1">Email Google Drive:</div>
                                <div class="fw-bold text-dark text-truncate mb-2" style="font-size: 0.9rem;">
                                    <i class="bi bi-person-check-fill text-success me-1"></i>{{ $googleUserEmail ?? 'Akun Google Aktif' }}
                                </div>
                                <a href="{{ route('admin.certificate.google-disconnect') }}" class="btn btn-sm btn-outline-danger w-100 rounded-pill py-1.5 fw-bold" style="font-size: 0.75rem;">
                                    <i class="bi bi-box-arrow-right me-1"></i> Ganti / Putuskan Akun
                                </a>
                            </div>
                            <div class="small text-muted">
                                <i class="bi bi-shield-check text-success me-1"></i> Akses token disimpan aman dan diperbarui otomatis.
                            </div>
                        @endif
                    </div>

                    {{-- Card Folder Target Terpilih --}}
                    <div class="card card-custom p-4 mb-4" id="activeFolderCard">
                        <h5 class="fw-bold text-dark mb-3"><i class="bi bi-folder-check text-warning me-2"></i>Folder Tujuan Season</h5>
                        <div id="activeFolderDetails">
                            @if($activeFolder)
                                <div class="p-3 border border-success rounded-3 bg-success-subtle mb-3">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <i class="bi bi-folder-fill text-warning fs-3"></i>
                                        <div class="overflow-hidden">
                                            <div class="fw-bold text-dark text-truncate" id="activeFolderName" style="font-size: 1rem;">
                                                {{ $activeFolder['name'] }}
                                            </div>
                                            <div class="text-muted font-monospace small" id="activeFolderId" style="font-size: 0.72rem;">
                                                ID: {{ $activeFolder['id'] }}
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <a href="{{ $activeFolder['webViewLink'] }}" target="_blank" id="activeFolderLink" class="btn btn-sm btn-outline-dark rounded-pill fw-semibold flex-fill" style="font-size: 0.75rem;">
                                            <i class="bi bi-box-arrow-up-right me-1"></i> Buka di Drive
                                        </a>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-warning text-dark w-100 fw-bold rounded-pill py-2 shadow-sm" onclick="goToStep(2)">
                                    Lanjut ke Langkah 2: Desain <i class="bi bi-arrow-right ms-1"></i>
                                </button>
                            @else
                                <div class="text-center py-4 border rounded-3 bg-light mb-3">
                                    <i class="bi bi-folder-x text-muted fs-1 mb-2 d-block"></i>
                                    <div class="fw-bold text-dark small mb-1">Belum Ada Folder Terpilih</div>
                                    <div class="text-muted small px-3">Pilih folder di sebelah kanan atau buat folder baru untuk menyimpan sertifikat Season {{ $season->name }}.</div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Kolom Kanan: Google Drive Folder Explorer --}}
                <div class="col-lg-8">
                    <div class="card card-custom p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                            <div>
                                <h5 class="fw-bold text-dark mb-0"><i class="bi bi-hdd-network text-warning me-2"></i>Google Drive Explorer</h5>
                                <div class="text-muted small">Kelola folder, buat subfolder, atau ubah nama folder langsung di Google Drive Anda.</div>
                            </div>
                            @if($googleConnected)
                                <div class="d-flex gap-2">
                                    <button type="button" class="btn btn-outline-warning text-dark btn-sm fw-bold rounded-pill px-3 shadow-sm" onclick="openCreateFolderModal()">
                                        <i class="bi bi-folder-plus me-1"></i> + Folder Baru
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary btn-sm rounded-circle shadow-sm" title="Refresh Folder" onclick="loadDriveFolders(currentParentId)">
                                        <i class="bi bi-arrow-clockwise"></i>
                                    </button>
                                </div>
                            @endif
                        </div>

                        @if(!$googleConnected)
                            <div class="text-center py-5">
                                <i class="bi bi-google text-muted" style="font-size: 3.5rem;"></i>
                                <h6 class="fw-bold mt-3 mb-1">Akun Google Belum Terhubung</h6>
                                <p class="text-muted small mb-4">Silakan klik tombol di panel kiri untuk menghubungkan Google Drive Anda.</p>
                                <a href="{{ route('admin.certificate.google-login', ['season_id' => $season->id]) }}" class="btn btn-danger fw-bold rounded-pill px-4 shadow-sm">
                                    <i class="bi bi-google me-1"></i> Hubungkan Sekarang
                                </a>
                            </div>
                        @else
                            {{-- Breadcrumb Navigation Drive --}}
                            <div class="d-flex align-items-center justify-content-between bg-light p-2.5 rounded-3 mb-3 border">
                                <div class="d-flex align-items-center gap-1 overflow-hidden" id="folderBreadcrumbContainer">
                                    <span class="badge bg-white text-dark border px-2 py-1 rounded cursor-pointer" onclick="loadDriveFolders('root')">
                                        <i class="bi bi-hdd me-1 text-warning"></i> Beranda Drive
                                    </span>
                                    <span id="breadcrumbTrail" class="d-flex align-items-center gap-1 small text-secondary"></span>
                                </div>
                                <div id="currentFolderActionContainer" style="display: none;">
                                    <button type="button" class="btn btn-sm btn-success rounded-pill fw-bold px-3 shadow-sm" id="btnSelectCurrentAsTarget" onclick="selectCurrentFolderAsTarget()" style="font-size: 0.75rem;">
                                        <i class="bi bi-check2-circle me-1"></i> Pilih Folder Ini
                                    </button>
                                </div>
                            </div>

                            {{-- Folder List Loading Spinner --}}
                            <div id="foldersLoadingSpinner" class="text-center py-5">
                                <div class="spinner-border text-warning" role="status"></div>
                                <div class="small text-muted mt-2">Memuat folder dari Google Drive...</div>
                            </div>

                            {{-- Folder Grid Container --}}
                            <div id="foldersContainer" class="row g-3" style="display: none;"></div>

                            {{-- Folder Empty State --}}
                            <div id="foldersEmptyState" class="text-center py-5" style="display: none;">
                                <i class="bi bi-folder-x text-muted" style="font-size: 3rem;"></i>
                                <h6 class="fw-bold mt-2 mb-1">Tidak Ada Folder</h6>
                                <p class="text-muted small mb-3">Direktori ini belum memiliki subfolder.</p>
                                <button type="button" class="btn btn-outline-warning text-dark btn-sm rounded-pill fw-bold px-3" onclick="openCreateFolderModal()">
                                    <i class="bi bi-folder-plus me-1"></i> Buat Folder di Sini
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- ========================================== --}}
        {{-- STEP 2: DESAIN TEMPLATE & VISUAL EDITOR --}}
        {{-- ========================================== --}}
        <div class="tab-pane fade" id="step2-pane" role="tabpanel">
            <div class="row g-4">
                {{-- Kolom Kiri: Canvas Figma-Level & Toolbar --}}
                <div class="col-lg-8">
                    <div class="card card-custom p-4 mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                            <h5 class="fw-bold text-dark mb-0"><i class="bi bi-palette text-warning me-2"></i>Workspace Desain Layout</h5>
                            
                            {{-- Element Toolkit --}}
                            @if($layout->template_path)
                                <div class="d-flex gap-2">
                                    <button type="button" id="btnAddText" class="btn btn-outline-warning text-dark btn-sm fw-bold rounded-pill px-3 shadow-sm">
                                        <i class="bi bi-fonts me-1"></i> + Teks Kustom
                                    </button>
                                    <button type="button" id="btnAddImage" class="btn btn-outline-warning text-dark btn-sm fw-bold rounded-pill px-3 shadow-sm">
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
                                    <p class="small text-muted mb-3">Silakan unggah gambar template PDF/JPG/PNG di panel kanan untuk memulai.</p>
                                </div>
                            @endif
                        </div>

                        @if($layout->template_path)
                            <div class="mt-3 d-flex justify-content-between align-items-center flex-wrap gap-2 text-secondary small">
                                <span><i class="bi bi-info-circle me-1"></i> Geser elemen di canvas. Gunakan tombol panah ← ↑ → ↓ untuk menggeser presisi.</span>
                                <button type="button" id="btnSaveConfig" class="btn btn-warning text-dark btn-sm fw-bold rounded-pill px-4 shadow-sm">
                                    <i class="bi bi-cloud-check-fill me-1"></i> Simpan Desain Layout
                                </button>
                            </div>
                        @endif
                    </div>

                    {{-- Navigasi Langkah --}}
                    <div class="d-flex justify-content-between align-items-center">
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-4 fw-bold btn-sm shadow-sm" onclick="goToStep(1)">
                            <i class="bi bi-arrow-left me-1"></i> Langkah 1: Folder Drive
                        </button>
                        <button type="button" class="btn btn-warning text-dark rounded-pill px-4 fw-bold btn-sm shadow-sm" onclick="goToStep(3)">
                            Langkah 3: Generate Berkas <i class="bi bi-arrow-right ms-1"></i>
                        </button>
                    </div>
                </div>

                {{-- Kolom Kanan: Upload Template & Inspektur Elemen --}}
                <div class="col-lg-4">
                    {{-- Card Upload Background & Font --}}
                    <div class="card card-custom p-4 mb-4">
                        <h5 class="fw-bold text-dark mb-3"><i class="bi bi-file-earmark-arrow-up text-warning me-2"></i>Upload Template Background</h5>
                        
                        <form id="layoutForm" action="{{ route('admin.season.certificate.layout', $season->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="pos_x" id="inputPosX" value="{{ $layout->pos_x }}">
                            <input type="hidden" name="pos_y" id="inputPosY" value="{{ $layout->pos_y }}">
                            <input type="hidden" name="font_size" id="inputFontSize" value="{{ $layout->font_size }}">
                            <input type="hidden" name="font_color" id="inputFontColor" value="{{ $layout->font_color }}">
                            <input type="hidden" name="layout_data" id="layoutDataField">
                            <input type="hidden" name="google_drive_link" id="layoutDriveLinkField" value="{{ $layout->google_drive_link }}">
                            <input type="hidden" name="is_released" id="layoutFormIsReleased" value="{{ $layout->is_released ? '1' : '0' }}">

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-secondary">File Template (PDF / JPG / PNG)</label>
                                <input type="file" id="certTemplateInput" name="template" class="form-control-custom" accept="image/jpeg,image/png,application/pdf">
                                <div class="form-text text-muted" style="font-size: 0.72rem;">Unggah gambar template sertifikat beresolusi tinggi (maks 30MB).</div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label small fw-bold text-secondary">Font Kustom (.ttf)</label>
                                <input type="file" name="font" class="form-control-custom" accept=".ttf">
                                <div class="form-text text-muted" style="font-size: 0.72rem;">
                                    @if($layout->font_path)
                                        <span class="text-success fw-bold"><i class="bi bi-file-earmark-check-fill"></i> Font aktif terpasang</span>
                                    @else
                                        Font bawaan: Arial
                                    @endif
                                </div>
                            </div>

                            <button type="submit" class="btn btn-outline-warning text-dark w-100 fw-bold rounded-pill py-2 shadow-sm">
                                <i class="bi bi-save me-1"></i> Upload & Simpan Template
                            </button>
                        </form>
                    </div>

                    {{-- Card Inspektur Elemen (Figma Style) --}}
                    @if($layout->template_path)
                        <div class="card card-custom p-4 mb-4" id="propertiesInspector" style="display: none;">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5 class="fw-bold text-dark mb-0"><i class="bi bi-sliders text-warning me-2"></i>Inspektur Elemen</h5>
                                <button type="button" id="btnDeleteElement" class="btn btn-sm btn-outline-danger border-0 rounded-circle" style="padding: 2px 6px;">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                            
                            <div class="border-top pt-3">
                                {{-- Konten Teks --}}
                                <div class="mb-3" id="propTextContainer">
                                    <label class="form-label small fw-bold text-secondary">Isi Teks</label>
                                    <textarea id="propTextContent" class="form-control-custom" rows="2" placeholder="Masukkan teks..."></textarea>
                                    <div class="form-text text-muted" id="propTextHelp" style="font-size: 0.68rem;">
                                        Gunakan tag `&lt; NAMA PESERTA &gt;` untuk nama dinamis. Gunakan `**kata**` untuk tebal.
                                    </div>
                                </div>

                                {{-- Ukuran Font & Warna --}}
                                <div class="row g-2 mb-3" id="propFontContainer">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold text-secondary">Ukuran Font</label>
                                        <input type="number" id="propFontSize" class="form-control-custom" min="10" max="500">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold text-secondary">Warna Teks</label>
                                        <input type="color" id="propFontColor" class="form-control form-control-color w-100 rounded-3 border" style="height: 38px; padding: 2px;">
                                    </div>
                                </div>

                                {{-- Align & Style --}}
                                <div class="mb-3" id="propStyleContainer">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="small fw-bold text-secondary">Format & Perataan</span>
                                    </div>
                                    <div class="btn-group w-100" role="group">
                                        <input type="checkbox" class="btn-check" id="propFontBold" autocomplete="off">
                                        <label class="btn btn-outline-secondary btn-sm" for="propFontBold"><i class="bi bi-type-bold"></i> Bold</label>
         
                                        <input type="radio" class="btn-check" name="propAlign" id="propAlignLeft" value="left" autocomplete="off">
                                        <label class="btn btn-outline-secondary btn-sm" for="propAlignLeft"><i class="bi bi-text-left"></i> Kiri</label>
         
                                        <input type="radio" class="btn-check" name="propAlign" id="propAlignCenter" value="center" autocomplete="off">
                                        <label class="btn btn-outline-secondary btn-sm" for="propAlignCenter"><i class="bi bi-text-center"></i> Tengah</label>
        
                                        <input type="radio" class="btn-check" name="propAlign" id="propAlignRight" value="right" autocomplete="off">
                                        <label class="btn btn-outline-secondary btn-sm" for="propAlignRight"><i class="bi bi-text-right"></i> Kanan</label>
                                    </div>
                                </div>

                                {{-- Dimensi Gambar --}}
                                <div class="row g-2 mb-3" id="propDimensionContainer" style="display: none;">
                                    <div class="col-6">
                                        <label class="form-label small fw-bold text-secondary">Lebar (px)</label>
                                        <input type="number" id="propImageWidth" class="form-control-custom" min="10">
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label small fw-bold text-secondary">Tinggi (px)</label>
                                        <input type="number" id="propImageHeight" class="form-control-custom" min="10">
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

                    {{-- Card Cetak Manual / Test Single PDF --}}
                    <div class="card card-custom p-4 mb-4">
                        <h5 class="fw-bold text-dark mb-3"><i class="bi bi-file-earmark-pdf text-warning me-2"></i>Uji Cetak 1 Peserta</h5>
                        <p class="text-secondary small mb-3">
                            Unduh pratinjau sertifikat PDF langsung untuk memastikan posisi dan ukuran teks pas sebelum cetak masal.
                        </p>
                        <form action="{{ route('admin.certificate.download-single') }}" method="GET" target="_blank">
                            <input type="hidden" name="season_id" value="{{ $season->id }}">
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-secondary">Ketik Contoh Nama Peserta</label>
                                <input type="text" name="name" class="form-control-custom" placeholder="Contoh: Muhammad Budi Pratama" required>
                            </div>
                            <button type="submit" class="btn btn-outline-secondary w-100 fw-bold rounded-pill py-2 shadow-sm">
                                <i class="bi bi-download me-1"></i> Unduh Uji PDF
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- ========================================== --}}
        {{-- STEP 3: GENERATE & LIHAT BERKAS DRIVE --}}
        {{-- ========================================== --}}
        <div class="tab-pane fade" id="step3-pane" role="tabpanel">
            <div class="row g-4">
                {{-- Panel Kontrol Generate & Status --}}
                <div class="col-lg-4">
                    <div class="card card-custom p-4 mb-4">
                        <h5 class="fw-bold text-dark mb-3"><i class="bi bi-broadcast text-warning me-2"></i>Status Publikasi</h5>
                        <div class="mb-0">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="certIsReleased" {{ $layout->is_released ? 'checked' : '' }} style="cursor: pointer; width: 45px; height: 22px;">
                                <label class="form-check-label fw-bold small text-secondary ms-2" for="certIsReleased" style="cursor: pointer; padding-top: 2px;">Rilis Sertifikat ke Publik</label>
                            </div>
                            <div class="form-text text-muted mt-2" style="font-size: 0.68rem; line-height: 1.4;">
                                Jika aktif, peserta dapat mencari & mengunduh link sertifikat via halaman publik.
                            </div>
                        </div>
                    </div>

                    <div class="card card-custom p-4 mb-4">
                        <h5 class="fw-bold text-dark mb-3"><i class="bi bi-lightning-charge text-danger me-2"></i>Generate Massal</h5>

                        {{-- Ringkasan Target --}}
                        <div class="bg-light p-3 rounded-3 mb-3 border">
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                <span class="small text-muted">Folder Target:</span>
                                <span class="small fw-bold text-dark text-truncate ms-2" id="step3TargetFolderName" style="max-width: 160px;">
                                    {{ $activeFolder ? $activeFolder['name'] : 'Belum dipilih' }}
                                </span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                <span class="small text-muted">Template:</span>
                                <span class="small fw-bold {{ $layout->template_path ? 'text-success' : 'text-danger' }}">
                                    {{ $layout->template_path ? 'Siap Digunakan' : 'Belum Ada' }}
                                </span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="small text-muted">Tim Terbayar:</span>
                                <span class="small fw-bold text-primary">{{ $paidTeamsCount }} Tim</span>
                            </div>
                        </div>

                        @if(!$googleConnected)
                            <div class="alert alert-danger py-2 small mb-3">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i> Akun Google Drive belum terhubung.
                            </div>
                            <a href="{{ route('admin.certificate.google-login', ['season_id' => $season->id]) }}" class="btn btn-outline-danger w-100 fw-bold rounded-pill py-2">
                                <i class="bi bi-google me-1"></i> Hubungkan Google Drive
                            </a>
                        @elseif(!$activeFolder)
                            <div class="alert alert-warning py-2 small mb-3">
                                <i class="bi bi-exclamation-circle-fill me-1"></i> Silakan pilih folder di Langkah 1 terlebih dahulu.
                            </div>
                            <button type="button" class="btn btn-warning text-dark w-100 fw-bold rounded-pill py-2" onclick="goToStep(1)">
                                Ke Langkah 1: Pilih Folder
                            </button>
                        @elseif(!$layout->template_path)
                            <div class="alert alert-warning py-2 small mb-3">
                                <i class="bi bi-exclamation-circle-fill me-1"></i> Silakan unggah template background di Langkah 2 terlebih dahulu.
                            </div>
                            <button type="button" class="btn btn-warning text-dark w-100 fw-bold rounded-pill py-2" onclick="goToStep(2)">
                                Ke Langkah 2: Upload Template
                            </button>
                        @else
                            <button type="button" id="btnGenerateToDrive" class="btn btn-danger w-100 fw-bold rounded-pill py-2.5 shadow-sm">
                                <i class="bi bi-lightning-charge-fill me-1"></i> Generate & Upload ({{ $paidTeamsCount }} Tim)
                            </button>
                        @endif

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
                                 style="max-height: 180px; font-size: 0.72rem; line-height: 1.4; border: 1px solid #334155;">
                                [SYSTEM] Menunggu pemrosesan...
                            </div>

                            {{-- Cancel Button --}}
                            <button type="button" id="btnStopGenerate" class="btn btn-sm btn-outline-danger w-100 mt-2 rounded-pill fw-bold" style="font-size: 0.75rem; display: none;">
                                <i class="bi bi-x-circle me-1"></i> Hentikan Proses
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Kolom Kanan: Google Drive File Viewer (Lihat Hasil Langsung di Website) --}}
                <div class="col-lg-8">
                    <div class="card card-custom p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                            <div>
                                <h5 class="fw-bold text-dark mb-0"><i class="bi bi-files text-warning me-2"></i>Berkas di Google Drive</h5>
                                <div class="text-muted small">Pratinjau sertifikat PDF langsung di website tanpa perlu keluar dari aplikasi.</div>
                            </div>
                            <div class="d-flex gap-2 align-items-center">
                                <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3 py-2 fw-semibold" id="fileCountBadge">
                                    0 Berkas
                                </span>
                                <button type="button" class="btn btn-outline-secondary btn-sm rounded-circle shadow-sm" title="Refresh Berkas" onclick="loadDriveFiles()">
                                    <i class="bi bi-arrow-clockwise"></i>
                                </button>
                                @if($activeFolder)
                                    <a href="{{ $activeFolder['webViewLink'] }}" target="_blank" id="step3FolderLink" class="btn btn-outline-warning text-dark btn-sm fw-bold rounded-pill px-3 shadow-sm">
                                        <i class="bi bi-box-arrow-up-right me-1"></i> Buka Folder di Drive
                                    </a>
                                @endif
                            </div>
                        </div>

                        {{-- Files Loading Spinner --}}
                        <div id="filesLoadingSpinner" class="text-center py-5" style="display: none;">
                            <div class="spinner-border text-warning" role="status"></div>
                            <div class="small text-muted mt-2">Mengambil berkas sertifikat dari Google Drive...</div>
                        </div>

                        {{-- File List Container --}}
                        <div id="filesContainer" class="d-flex flex-column gap-2"></div>

                        {{-- File Empty State --}}
                        <div id="filesEmptyState" class="text-center py-5">
                            <i class="bi bi-file-earmark-x text-muted" style="font-size: 3.5rem;"></i>
                            <h6 class="fw-bold mt-2 mb-1">Belum Ada Berkas Sertifikat</h6>
                            <p class="text-muted small mb-0">Klik tombol <strong>"Generate & Upload"</strong> di samping kiri untuk membuat dan mengunggah sertifikat ke folder Google Drive.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

{{-- MODAL: BUAT FOLDER BARU --}}
<div class="modal fade" id="modalCreateFolder" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark"><i class="bi bi-folder-plus text-warning me-2"></i>Buat Folder Baru</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-3">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary">Nama Folder</label>
                    <input type="text" id="newFolderNameInput" class="form-control-custom" placeholder="Contoh: Sertifikat Season {{ $season->name }}" required>
                    <div class="form-text text-muted" style="font-size: 0.72rem;">Folder akan otomatis dibuat dengan izin publik agar sertifikat dapat diunduh peserta.</div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-warning text-dark btn-sm rounded-pill fw-bold px-4 shadow-sm" id="btnSubmitCreateFolder" onclick="submitCreateFolder()">
                    <i class="bi bi-plus-circle me-1"></i> Buat Folder
                </button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL: GANTI NAMA FOLDER --}}
<div class="modal fade" id="modalRenameFolder" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark"><i class="bi bi-pencil-square text-warning me-2"></i>Ganti Nama Folder</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body py-3">
                <input type="hidden" id="renameFolderIdInput">
                <div class="mb-3">
                    <label class="form-label small fw-bold text-secondary">Nama Folder Baru</label>
                    <input type="text" id="renameFolderNameInput" class="form-control-custom" required>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-warning text-dark btn-sm rounded-pill fw-bold px-4 shadow-sm" id="btnSubmitRenameFolder" onclick="submitRenameFolder()">
                    <i class="bi bi-check2 me-1"></i> Simpan Nama
                </button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL: PRATINJAU PDF IN-WEBSITE --}}
<div class="modal fade" id="modalPdfPreview" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" style="height: 90vh;">
        <div class="modal-content rounded-4 border-0 shadow h-100 overflow-hidden">
            <div class="modal-header py-2.5 px-4 bg-dark text-white border-0">
                <div class="d-flex align-items-center gap-2 overflow-hidden">
                    <i class="bi bi-file-earmark-pdf-fill text-danger fs-5"></i>
                    <h6 class="modal-title fw-bold text-truncate m-0" id="previewModalTitle">Pratinjau Sertifikat</h6>
                </div>
                <div class="d-flex gap-2">
                    <a href="#" target="_blank" id="previewExternalLink" class="btn btn-sm btn-outline-light rounded-pill px-3" style="font-size: 0.75rem;">
                        <i class="bi bi-box-arrow-up-right me-1"></i> Buka di Drive
                    </a>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body p-0 position-relative bg-secondary" style="height: calc(100% - 56px);">
                <div id="pdfLoadingNotice" class="position-absolute top-50 start-50 translate-middle text-center text-white">
                    <div class="spinner-border text-warning mb-2" role="status"></div>
                    <div class="small">Memuat pratinjau sertifikat...</div>
                </div>
                <iframe id="pdfPreviewIframe" src="" class="w-100 h-100 border-0" allow="autoplay" onload="document.getElementById('pdfLoadingNotice').style.display='none';"></iframe>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.min.js"></script>
<script>
pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.4.120/pdf.worker.min.js';

// Global variables for Google Drive Explorer
let currentParentId = 'root';
let currentActiveFolderId = "{{ $activeFolder ? $activeFolder['id'] : '' }}";
let isGoogleConnected = {{ $googleConnected ? 'true' : 'false' }};

// Helper: Switch wizard tabs programmatically
function goToStep(stepNumber) {
    const tabEl = document.getElementById('step' + stepNumber + '-tab');
    if (tabEl) {
        const tab = new bootstrap.Tab(tabEl);
        tab.show();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}

// -------------------------------------------------------------
// GOOGLE DRIVE FOLDER EXPLORER (STEP 1)
// -------------------------------------------------------------
function loadDriveFolders(parentId = 'root') {
    if (!isGoogleConnected) return;

    currentParentId = parentId;
    const spinner = document.getElementById('foldersLoadingSpinner');
    const container = document.getElementById('foldersContainer');
    const emptyState = document.getElementById('foldersEmptyState');
    const trailContainer = document.getElementById('breadcrumbTrail');
    const currentActionContainer = document.getElementById('currentFolderActionContainer');

    spinner.style.display = 'block';
    container.style.display = 'none';
    emptyState.style.display = 'none';

    fetch(`{{ route('admin.season.certificate.drive-folders', $season->id) }}?parent_id=${encodeURIComponent(parentId)}`)
    .then(r => r.json())
    .then(res => {
        spinner.style.display = 'none';

        if (!res.success) {
            Swal.fire('Error', res.message || 'Gagal memuat folder Drive.', 'error');
            return;
        }

        // Update Breadcrumb
        if (parentId === 'root') {
            trailContainer.innerHTML = '';
            currentActionContainer.style.display = 'none';
        } else if (res.current_folder) {
            trailContainer.innerHTML = `
                <i class="bi bi-chevron-right text-muted" style="font-size: 0.7rem;"></i>
                <span class="badge bg-warning-subtle text-dark border border-warning-subtle px-2 py-1 rounded fw-bold text-truncate" style="max-width: 250px;">
                    ${res.current_folder.name}
                </span>
            `;
            currentActionContainer.style.display = 'block';
        }

        // Render Folders
        container.innerHTML = '';

        // Add "Up / Back" folder button if not in root
        if (parentId !== 'root') {
            const backCol = document.createElement('div');
            backCol.className = 'col-md-6 col-lg-4';
            backCol.innerHTML = `
                <div class="folder-card p-3 d-flex align-items-center gap-3 cursor-pointer bg-light" onclick="loadDriveFolders('${res.parent_of_current || 'root'}')">
                    <i class="bi bi-arrow-90deg-up text-secondary fs-4"></i>
                    <div>
                        <div class="fw-bold text-dark small">.. Ke Folder Sebelumnya</div>
                        <div class="text-muted" style="font-size: 0.7rem;">Kembali satu tingkat ke atas</div>
                    </div>
                </div>
            `;
            container.appendChild(backCol);
        }

        if (!res.folders || res.folders.length === 0) {
            if (parentId === 'root') {
                emptyState.style.display = 'block';
            } else {
                container.style.display = 'flex'; // show only the back button
            }
            return;
        }

        res.folders.forEach(f => {
            const isTarget = (f.id === currentActiveFolderId);
            const col = document.createElement('div');
            col.className = 'col-md-6 col-lg-4';
            col.innerHTML = `
                <div class="folder-card p-3 h-100 d-flex flex-column justify-content-between ${isTarget ? 'is-active-target' : ''}">
                    <div class="d-flex align-items-start gap-2 mb-2">
                        <i class="bi bi-folder-fill ${isTarget ? 'text-success' : 'text-warning'} fs-2 flex-shrink-0"></i>
                        <div class="overflow-hidden flex-fill">
                            <div class="fw-bold text-dark text-truncate small" title="${f.name}">${f.name}</div>
                            <div class="text-muted" style="font-size: 0.68rem;">Dibuat: ${new Date(f.createdTime).toLocaleDateString('id-ID')}</div>
                            ${isTarget ? '<span class="badge bg-success text-white rounded-pill px-2 py-0 mt-1" style="font-size: 0.65rem;"><i class="bi bi-check2"></i> Target Aktif</span>' : ''}
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-1 pt-2 border-top mt-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill py-1 px-2.5 flex-fill fw-semibold" style="font-size: 0.72rem;" onclick="loadDriveFolders('${f.id}')">
                            <i class="bi bi-folder2-open me-1"></i> Buka
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary rounded-circle" style="width: 28px; height: 28px; padding: 0;" title="Ganti Nama" onclick="openRenameFolderModal('${f.id}', '${f.name.replace(/'/g, "\\'")}')">
                            <i class="bi bi-pencil" style="font-size: 0.75rem;"></i>
                        </button>
                        <button type="button" class="btn btn-sm ${isTarget ? 'btn-success text-white' : 'btn-outline-warning text-dark'} rounded-pill py-1 px-2.5 fw-bold" style="font-size: 0.72rem;" onclick="setTargetFolder('${f.id}', '${f.name.replace(/'/g, "\\'")}')">
                            ${isTarget ? '<i class="bi bi-check-lg"></i> Terpilih' : '<i class="bi bi-pin-angle"></i> Pilih'}
                        </button>
                    </div>
                </div>
            `;
            container.appendChild(col);
        });

        container.style.display = 'flex';
    })
    .catch(err => {
        spinner.style.display = 'none';
        console.error(err);
        Swal.fire('Error', 'Gagal memuat folder dari Google Drive.', 'error');
    });
}

function openCreateFolderModal() {
    document.getElementById('newFolderNameInput').value = `Sertifikat Season {{ $season->name }}`;
    const modal = new bootstrap.Modal(document.getElementById('modalCreateFolder'));
    modal.show();
}

function submitCreateFolder() {
    const input = document.getElementById('newFolderNameInput');
    const name = input.value.trim();
    if (!name) {
        Swal.fire('Peringatan', 'Silakan masukkan nama folder.', 'warning');
        return;
    }

    const btn = document.getElementById('btnSubmitCreateFolder');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Membuat...';

    fetch("{{ route('admin.season.certificate.drive-create-folder', $season->id) }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            name: name,
            parent_id: currentParentId
        })
    })
    .then(r => r.json())
    .then(res => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-plus-circle me-1"></i> Buat Folder';

        if (res.success) {
            bootstrap.Modal.getInstance(document.getElementById('modalCreateFolder')).hide();
            Swal.fire({
                title: 'Berhasil!',
                text: 'Folder baru berhasil dibuat.',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            });
            loadDriveFolders(currentParentId);
        } else {
            Swal.fire('Gagal', res.message || 'Gagal membuat folder.', 'error');
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-plus-circle me-1"></i> Buat Folder';
        console.error(err);
        Swal.fire('Error', 'Terjadi kesalahan sistem.', 'error');
    });
}

function openRenameFolderModal(folderId, currentName) {
    document.getElementById('renameFolderIdInput').value = folderId;
    document.getElementById('renameFolderNameInput').value = currentName;
    const modal = new bootstrap.Modal(document.getElementById('modalRenameFolder'));
    modal.show();
}

function submitRenameFolder() {
    const folderId = document.getElementById('renameFolderIdInput').value;
    const name = document.getElementById('renameFolderNameInput').value.trim();
    if (!name) {
        Swal.fire('Peringatan', 'Nama folder tidak boleh kosong.', 'warning');
        return;
    }

    const btn = document.getElementById('btnSubmitRenameFolder');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';

    fetch("{{ route('admin.season.certificate.drive-rename-folder', $season->id) }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            folder_id: folderId,
            name: name
        })
    })
    .then(r => r.json())
    .then(res => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-check2 me-1"></i> Simpan Nama';

        if (res.success) {
            bootstrap.Modal.getInstance(document.getElementById('modalRenameFolder')).hide();
            Swal.fire({
                title: 'Berhasil!',
                text: 'Nama folder berhasil diperbarui.',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false
            });

            // Update UI jika folder yang di-rename adalah target aktif
            if (folderId === currentActiveFolderId) {
                const nameEl = document.getElementById('activeFolderName');
                if (nameEl) nameEl.textContent = name;
                const step3Name = document.getElementById('step3TargetFolderName');
                if (step3Name) step3Name.textContent = name;
            }

            loadDriveFolders(currentParentId);
        } else {
            Swal.fire('Gagal', res.message || 'Gagal mengubah nama folder.', 'error');
        }
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-check2 me-1"></i> Simpan Nama';
        console.error(err);
        Swal.fire('Error', 'Terjadi kesalahan sistem.', 'error');
    });
}

function setTargetFolder(folderId, folderName) {
    Swal.fire({
        title: 'Memproses...',
        text: 'Mengatur folder tujuan sertifikat...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    fetch("{{ route('admin.season.certificate.set-drive-folder', $season->id) }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            folder_id: folderId,
            folder_name: folderName
        })
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            currentActiveFolderId = folderId;

            // Update Active Folder Card
            const detailsCard = document.getElementById('activeFolderDetails');
            detailsCard.innerHTML = `
                <div class="p-3 border border-success rounded-3 bg-success-subtle mb-3">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <i class="bi bi-folder-fill text-warning fs-3"></i>
                        <div class="overflow-hidden">
                            <div class="fw-bold text-dark text-truncate" id="activeFolderName" style="font-size: 1rem;">
                                ${res.folder_name}
                            </div>
                            <div class="text-muted font-monospace small" id="activeFolderId" style="font-size: 0.72rem;">
                                ID: ${res.folder_id}
                            </div>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="${res.google_drive_link}" target="_blank" id="activeFolderLink" class="btn btn-sm btn-outline-dark rounded-pill fw-semibold flex-fill" style="font-size: 0.75rem;">
                            <i class="bi bi-box-arrow-up-right me-1"></i> Buka di Drive
                        </a>
                    </div>
                </div>
                <button type="button" class="btn btn-warning text-dark w-100 fw-bold rounded-pill py-2 shadow-sm" onclick="goToStep(2)">
                    Lanjut ke Langkah 2: Desain <i class="bi bi-arrow-right ms-1"></i>
                </button>
            `;

            // Update sub title & step 3 target
            document.getElementById('step1Subtitle').textContent = 'Terpilih: ' + (res.folder_name.length > 20 ? res.folder_name.substring(0, 18) + '...' : res.folder_name);
            const step3Name = document.getElementById('step3TargetFolderName');
            if (step3Name) step3Name.textContent = res.folder_name;

            const layoutDrive = document.getElementById('layoutDriveLinkField');
            if (layoutDrive) layoutDrive.value = res.google_drive_link;

            Swal.fire({
                title: 'Folder Dipilih!',
                text: `Folder "${res.folder_name}" berhasil disetel sebagai tempat penyimpanan sertifikat.`,
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            });

            loadDriveFolders(currentParentId);
        } else {
            Swal.fire('Gagal', res.message || 'Gagal mengatur target folder.', 'error');
        }
    })
    .catch(err => {
        console.error(err);
        Swal.fire('Error', 'Terjadi kesalahan server.', 'error');
    });
}

function selectCurrentFolderAsTarget() {
    if (currentParentId && currentParentId !== 'root') {
        const trailBadge = document.querySelector('#breadcrumbTrail .badge');
        const folderName = trailBadge ? trailBadge.textContent.trim() : 'Folder Terpilih';
        setTargetFolder(currentParentId, folderName);
    }
}

// -------------------------------------------------------------
// GOOGLE DRIVE FILE VIEWER (STEP 3)
// -------------------------------------------------------------
function loadDriveFiles() {
    if (!isGoogleConnected) return;

    const spinner = document.getElementById('filesLoadingSpinner');
    const container = document.getElementById('filesContainer');
    const emptyState = document.getElementById('filesEmptyState');
    const badge = document.getElementById('fileCountBadge');

    spinner.style.display = 'block';
    container.style.display = 'none';
    emptyState.style.display = 'none';

    fetch("{{ route('admin.season.certificate.drive-files', $season->id) }}")
    .then(r => r.json())
    .then(res => {
        spinner.style.display = 'none';

        if (!res.success) {
            emptyState.style.display = 'block';
            badge.textContent = '0 Berkas';
            return;
        }

        const files = res.files || [];
        badge.textContent = files.length + ' Berkas';

        if (files.length === 0) {
            emptyState.style.display = 'block';
            return;
        }

        container.innerHTML = '';
        files.forEach(file => {
            const sizeInKb = Math.round(file.size / 1024);
            const sizeDisplay = sizeInKb > 1024 ? (sizeInKb / 1024).toFixed(1) + ' MB' : sizeInKb + ' KB';
            const dateDisplay = file.createdTime ? new Date(file.createdTime).toLocaleString('id-ID') : '-';

            const item = document.createElement('div');
            item.className = 'file-item-card p-3 d-flex align-items-center justify-content-between flex-wrap gap-2';
            item.innerHTML = `
                <div class="d-flex align-items-center gap-3 overflow-hidden flex-fill">
                    <i class="bi bi-file-earmark-pdf-fill text-danger fs-3 flex-shrink-0"></i>
                    <div class="overflow-hidden">
                        <div class="fw-bold text-dark text-truncate small" title="${file.name}">${file.name}</div>
                        <div class="text-muted d-flex gap-2 align-items-center" style="font-size: 0.72rem;">
                            <span><i class="bi bi-hdd me-1"></i>${sizeDisplay}</span>
                            <span>&bull;</span>
                            <span><i class="bi bi-clock me-1"></i>${dateDisplay}</span>
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-semibold shadow-sm" style="font-size: 0.75rem;" onclick="openPdfPreviewModal('${file.id}', '${file.name.replace(/'/g, "\\'")}', '${file.webViewLink}')">
                        <i class="bi bi-eye-fill me-1"></i> Pratinjau PDF
                    </button>
                    <a href="${file.webViewLink}" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 shadow-sm" title="Buka di Google Drive" style="font-size: 0.75rem;">
                        <i class="bi bi-box-arrow-up-right"></i>
                    </a>
                    ${file.webContentLink ? `
                        <a href="${file.webContentLink}" class="btn btn-sm btn-outline-secondary rounded-pill px-2.5 shadow-sm" title="Unduh Berkas" style="font-size: 0.75rem;">
                            <i class="bi bi-download"></i>
                        </a>
                    ` : ''}
                </div>
            `;
            container.appendChild(item);
        });

        container.style.display = 'flex';
    })
    .catch(err => {
        spinner.style.display = 'none';
        console.error(err);
        emptyState.style.display = 'block';
    });
}

function openPdfPreviewModal(fileId, fileName, webViewLink) {
    const modal = new bootstrap.Modal(document.getElementById('modalPdfPreview'));
    document.getElementById('previewModalTitle').textContent = fileName;
    document.getElementById('previewExternalLink').href = webViewLink;

    const iframe = document.getElementById('pdfPreviewIframe');
    document.getElementById('pdfLoadingNotice').style.display = 'block';
    iframe.src = `https://drive.google.com/file/d/${fileId}/preview`;

    modal.show();
}

// -------------------------------------------------------------
// WORKSPACE FIGMA-STYLE EDITOR (STEP 2)
// -------------------------------------------------------------
document.addEventListener('DOMContentLoaded', function() {
    // Inisialisasi awal Google Drive Folder jika terhubung
    if (isGoogleConnected) {
        loadDriveFolders('root');
    }

    // Auto-load Drive Files saat Tab Langkah 3 diklik
    const step3Tab = document.getElementById('step3-tab');
    if (step3Tab) {
        step3Tab.addEventListener('shown.bs.tab', function() {
            loadDriveFiles();
        });
    }

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
    const propAlignRight = document.getElementById('propAlignRight');
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
                
                div.innerHTML = escapedText.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');
                div.style.fontSize = (el.font_size * scale) + 'px';
                div.style.color = el.color;
                div.style.fontWeight = el.bold ? 'bold' : 'normal';
                div.style.whiteSpace = 'pre';
                div.style.textAlign = el.align || 'center';
            } else if (el.type === 'image') {
                const img = document.createElement('img');
                img.src = el.src;
                img.style.width = (el.width * scale) + 'px';
                img.style.height = (el.height * scale) + 'px';
                img.style.pointerEvents = 'none';
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

    // Drag and Drop core logic
    function bindDragHandler(elementDiv, el) {
        let isDragging = false;
        let hasMoved = false;
        let startX, startY;
        let initialX, initialY;

        elementDiv.addEventListener('mousedown', function(e) {
            isDragging = true;
            hasMoved = false;
            elementDiv.style.cursor = 'grabbing';
            
            selectElementWithoutRedraw(el.id);
            
            const rect = wrapper.getBoundingClientRect();
            startX = e.clientX;
            startY = e.clientY;
            
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

            elementDiv.style.left = newX + 'px';
            elementDiv.style.top = newY + 'px';

            const percentX = parseFloat(((newX / rect.width) * 100).toFixed(2));
            const percentY = parseFloat(((newY / rect.height) * 100).toFixed(2));

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
                    
                    el.x = parseFloat(((currentPixelLeft / rect.width) * 100).toFixed(2));
                    el.y = parseFloat(((currentPixelTop / rect.height) * 100).toFixed(2));
                }
                
                renderWorkspace();
            }
        });
    }

    function selectElement(id) {
        selectedElementId = id;
        renderWorkspace();
        loadProperties(id);
    }

    function selectElementWithoutRedraw(id) {
        selectedElementId = id;
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
            } else if (el.align === 'right') {
                propAlignRight.checked = true;
            } else {
                propAlignCenter.checked = true;
            }

            if (el.is_dynamic_name) {
                propTextContent.disabled = false;
                propTextHelp.innerText = "Gunakan tag < NAMA PESERTA > untuk nama dinamis. Anda bebas mengubah atau mengosongkannya.";
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

    // Keyboard Arrow Keys navigation
    document.addEventListener('keydown', function(e) {
        if (!selectedElementId) return;

        const activeEl = document.activeElement;
        if (activeEl && (activeEl.tagName === 'INPUT' || activeEl.tagName === 'TEXTAREA')) {
            return;
        }

        const el = elements.find(item => item.id === selectedElementId);
        if (!el) return;

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

        const div = document.getElementById('el-' + el.id);
        if (div) {
            div.style.left = el.x + '%';
            div.style.top = el.y + '%';
        }

        propPosXLabel.textContent = el.x;
        propPosYLabel.textContent = el.y;

        const layoutDataField = document.getElementById('layoutDataField');
        if (layoutDataField) {
            layoutDataField.value = JSON.stringify(elements);
        }
    });

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

    if (propAlignRight) {
        propAlignRight.addEventListener('change', function() {
            if (selectedElementId && this.checked) {
                const el = elements.find(item => item.id === selectedElementId);
                if (el) el.align = 'right';
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

    // Add Image Element
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
                    imageLoader.value = '';

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
                        Swal.fire('Gagal', res.message || 'Gagal mengunggah gambar.', 'error');
                    }
                })
                .catch(err => {
                    btnAddImage.disabled = false;
                    btnAddImage.innerHTML = '<i class="bi bi-image me-1"></i> + Logo/Gambar';
                    imageLoader.value = '';
                    console.error(err);
                    Swal.fire('Error', 'Terjadi kesalahan saat mengunggah aset.', 'error');
                });
            }
        });
    }

    // AJAX Save Configuration Layout
    const btnSaveConfig = document.getElementById('btnSaveConfig');
    if (btnSaveConfig) {
        btnSaveConfig.addEventListener('click', function() {
            let mainNameEl = elements.find(item => item.is_dynamic_name);
            if (!mainNameEl) {
                mainNameEl = elements.find(item => item.type === 'text' && /<\s*nama\s+peserta\s*>/i.test(item.text));
            }
            if (!mainNameEl) {
                mainNameEl = elements.find(item => item.type === 'text');
            }
            
            if (mainNameEl) {
                document.getElementById('inputPosX').value = mainNameEl.x;
                document.getElementById('inputPosY').value = mainNameEl.y;
                document.getElementById('inputFontSize').value = mainNameEl.font_size;
                document.getElementById('inputFontColor').value = mainNameEl.color;
            }

            const formData = new FormData(document.getElementById('layoutForm'));
            formData.set('layout_data', JSON.stringify(elements));

            const isReleasedEl = document.getElementById('certIsReleased');
            if (isReleasedEl) {
                formData.set('is_released', isReleasedEl.checked ? '1' : '0');
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
                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'Konfigurasi desain tata letak berhasil disimpan.',
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire('Gagal', res.message || 'Gagal menyimpan.', 'error');
                }
            })
            .catch(err => {
                btnSaveConfig.disabled = false;
                btnSaveConfig.innerHTML = '<i class="bi bi-cloud-check-fill me-1"></i> Simpan Desain Layout';
                console.error(err);
                Swal.fire('Error', 'Terjadi kesalahan jaringan.', 'error');
            });
        });
    }

    if (templateImg) {
        if (templateImg.complete) {
            renderWorkspace();
        } else {
            templateImg.addEventListener('load', renderWorkspace);
        }
    }
    window.addEventListener('resize', renderWorkspace);

    // -------------------------------------------------------------
    // GENERATE MASAL KE GOOGLE DRIVE (STEP 3)
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
        const btnStopGenerate = document.getElementById('btnStopGenerate');
        
        progressContainer.style.display = 'block';
        if (btnGenerateToDrive) btnGenerateToDrive.disabled = true;
        if (btnStopGenerate) {
            btnStopGenerate.style.display = 'block';
            btnStopGenerate.disabled = false;
            btnStopGenerate.innerHTML = '<i class="bi bi-x-circle me-1"></i> Hentikan Proses';
        }

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
                        if (log.includes('✅') || log.includes('🎉') || log.includes('🛑')) colorClass = 'text-info';
                        return `<div class="mb-1 ${colorClass}">${log}</div>`;
                    }).join('');
                    
                    terminalConsole.scrollTop = terminalConsole.scrollHeight;
                }

                if (res.status === 'idle') {
                    clearInterval(pollInterval);
                    pollInterval = null;
                    if (btnGenerateToDrive) btnGenerateToDrive.disabled = false;
                    if (btnStopGenerate) btnStopGenerate.style.display = 'none';
                    progressText.textContent = 'Sinkronisasi Selesai!';

                    // Muat ulang daftar berkas setelah generate selesai
                    loadDriveFiles();
                } else {
                    progressText.textContent = 'Sedang mensinkronisasi sertifikat...';
                }
            })
            .catch(err => console.error('Error polling logs:', err));
        }, 400);
    }

    const btnStopGenerate = document.getElementById('btnStopGenerate');
    if (btnStopGenerate) {
        btnStopGenerate.addEventListener('click', function() {
            if (!confirm('Apakah Anda yakin ingin menghentikan proses generate sertifikat?')) {
                return;
            }
            btnStopGenerate.disabled = true;
            btnStopGenerate.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Menghentikan...';
            
            fetch("{{ route('admin.season.certificate.stop-generate', $season->id) }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(r => r.json())
            .then(res => {
                if (!res.success) {
                    Swal.fire('Gagal', res.message || 'Gagal menghentikan.', 'error');
                    btnStopGenerate.disabled = false;
                    btnStopGenerate.innerHTML = '<i class="bi bi-x-circle me-1"></i> Hentikan Proses';
                }
            })
            .catch(err => {
                console.error(err);
                btnStopGenerate.disabled = false;
                btnStopGenerate.innerHTML = '<i class="bi bi-x-circle me-1"></i> Hentikan Proses';
            });
        });
    }

    // Check ongoing process on page load
    fetch("{{ route('admin.season.certificate.logs', $season->id) }}")
    .then(r => r.json())
    .then(res => {
        if (res.status === 'running') {
            startLogPolling();
        }
    });

    if (btnGenerateToDrive) {
        btnGenerateToDrive.addEventListener('click', function() {
            if (btnGenerateToDrive) btnGenerateToDrive.disabled = true;

            startLogPolling();

            fetch("{{ route('admin.season.certificate.generate-drive', $season->id) }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(r => r.json())
            .then(res => {
                if (!res.success) {
                    Swal.fire('Gagal', res.message || 'Gagal memulai sinkronisasi.', 'error');
                    if (btnGenerateToDrive) btnGenerateToDrive.disabled = false;
                }
            })
            .catch(err => {
                console.error(err);
                Swal.fire('Error', 'Gagal memulai proses sinkronisasi.', 'error');
                if (btnGenerateToDrive) btnGenerateToDrive.disabled = false;
            });
        });
    }

    // Toggle Publikasi (Rilis Sertifikat)
    const certIsReleasedToggle = document.getElementById('certIsReleased');
    if (certIsReleasedToggle) {
        certIsReleasedToggle.addEventListener('change', function () {
            const isChecked = this.checked ? '1' : '0';
            const layoutFormIsReleased = document.getElementById('layoutFormIsReleased');
            if (layoutFormIsReleased) {
                layoutFormIsReleased.value = isChecked;
            }
            
            Swal.fire({
                title: 'Memproses...',
                text: 'Memperbarui status publikasi sertifikat...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            const formData = new FormData();
            formData.append('is_released', isChecked);

            fetch("{{ route('admin.season.certificate.toggle-release', $season->id) }}", {
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
                    Swal.fire({
                        title: isChecked === '1' ? '🟢 Sertifikat Dirilis!' : '🔴 Rilis Ditutup',
                        text: isChecked === '1' ? 'Peserta sekarang dapat mencari dan mengunduh sertifikat mereka.' : 'Halaman pencarian sertifikat publik dinonaktifkan.',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire('Gagal', 'Gagal mengubah status: ' + (res.message || 'Terjadi kesalahan.'), 'error');
                    this.checked = !this.checked;
                    if (layoutFormIsReleased) layoutFormIsReleased.value = this.checked ? '1' : '0';
                }
            })
            .catch(err => {
                console.error("AJAX Error:", err);
                Swal.fire('Error', 'Terjadi kesalahan jaringan atau server.', 'error');
                this.checked = !this.checked;
                if (layoutFormIsReleased) layoutFormIsReleased.value = this.checked ? '1' : '0';
            });
        });
    }

    // Image compressor for background template upload
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
