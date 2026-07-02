@extends('layouts.app')

@section('title', 'Hubungi Kami (Contact Us) - Yomuda Championship')

@push('styles')
<style>
    :root {
        --ymd-yellow: #ffc107;
        --ymd-dark: #16191c;
    }

    .policy-container {
        position: relative;
        overflow: hidden;
        padding: 100px 0 80px;
        background:
            radial-gradient(circle at top, rgba(255, 193, 7, 0.08) 0%, transparent 45%),
            radial-gradient(circle at bottom, rgba(255, 193, 7, 0.03) 0%, transparent 60%);
    }

    .policy-container::before {
        content: '';
        position: absolute;
        inset: 0;
        background-image:
            linear-gradient(rgba(255, 255, 255, 0.015) 1px, transparent 1px),
            linear-gradient(90deg, rgba(255, 255, 255, 0.015) 1px, transparent 1px);
        background-size: 40px 40px;
        mask-image: linear-gradient(to bottom, black, transparent 90%);
        pointer-events: none;
    }

    .policy-section {
        background: rgba(255, 255, 255, 0.015);
        border-radius: 35px;
        border: 1px solid rgba(255, 255, 255, 0.05);
        padding: 50px 35px;
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
    }
    
    .contact-item {
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 20px;
        padding: 25px;
        transition: all 0.3s ease;
    }
    
    .contact-item:hover {
        border-color: var(--ymd-yellow);
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    }
</style>
@endpush

@section('content')
<div class="policy-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                
                {{-- Back button --}}
                <div class="mb-4">
                    <a href="{{ route('home') }}" class="text-decoration-none text-white-50 hover-text-warning small transition-all">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Beranda
                    </a>
                </div>

                {{-- Policy Wrapper --}}
                <div class="policy-section shadow-lg text-white text-center">
                    <h2 class="fw-bold mb-3 text-warning">Hubungi Kami (Contact Us)</h2>
                    <p class="text-secondary small mb-5">Kami siap membantu Anda kapan saja terkait kendala teknis dan pendaftaran turnamen.</p>

                    <div class="row g-4 text-start justify-content-center">
                        {{-- Email --}}
                        <div class="col-md-6 col-lg-4">
                            <div class="contact-item h-100 d-flex flex-column align-items-center text-center">
                                <div class="fs-1 text-warning mb-3"><i class="bi bi-envelope-fill"></i></div>
                                <h6 class="fw-bold text-white mb-2">E-Mail Resmi</h6>
                                <p class="text-secondary small mb-0">{{ \App\Models\Setting::getVal('admin_email', 'monotp94@gmail.com') }}</p>
                            </div>
                        </div>

                        {{-- Phone / WA --}}
                        <div class="col-md-6 col-lg-4">
                            <div class="contact-item h-100 d-flex flex-column align-items-center text-center">
                                <div class="fs-1 text-warning mb-3"><i class="bi bi-whatsapp"></i></div>
                                <h6 class="fw-bold text-white mb-2">WhatsApp Support</h6>
                                <p class="text-secondary small mb-0">{{ \App\Models\Setting::getVal('admin_wa', '08123456789') }}</p>
                            </div>
                        </div>

                        {{-- Address --}}
                        <div class="col-md-12 col-lg-4">
                            <div class="contact-item h-100 d-flex flex-column align-items-center text-center">
                                <div class="fs-1 text-warning mb-3"><i class="bi bi-geo-alt-fill"></i></div>
                                <h6 class="fw-bold text-white mb-2">Alamat Usaha/Bisnis</h6>
                                <p class="text-secondary small mb-0">Yomuda Championship HQ, Blimbing, Kota Malang, Jawa Timur, Indonesia</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
