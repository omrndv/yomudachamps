@extends('layouts.app')

@section('title', 'Daftar FAQ Lengkap - Yomuda Championship')

@push('styles')
<style>
    :root {
        --ymd-yellow: #ffc107;
        --ymd-dark: #16191c;
    }

    .faq-container {
        position: relative;
        overflow: hidden;
        padding: 100px 0 80px;
        background:
            radial-gradient(circle at top, rgba(255, 193, 7, 0.08) 0%, transparent 45%),
            radial-gradient(circle at bottom, rgba(255, 193, 7, 0.03) 0%, transparent 60%);
    }

    .faq-container::before {
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

    .faq-section {
        background: rgba(255, 255, 255, 0.015);
        border-radius: 35px;
        border: 1px solid rgba(255, 255, 255, 0.05);
        padding: 50px 35px;
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
    }
    .faq-accordion .accordion-item {
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 20px !important;
        margin-bottom: 15px;
        overflow: hidden;
        transition: all 0.3s ease;
    }
    .faq-accordion .accordion-item:hover {
        border-color: rgba(255, 193, 7, 0.35);
        background: rgba(255, 193, 7, 0.015);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    }
    .faq-accordion .accordion-button {
        background: transparent;
        color: #ffffff;
        font-weight: 700;
        font-size: 1.05rem;
        padding: 22px 28px;
        box-shadow: none;
        border: none;
    }
    .faq-accordion .accordion-button:not(.collapsed) {
        color: var(--ymd-yellow);
        background: transparent;
    }
    .faq-accordion .accordion-button::after {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23fff'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
        filter: drop-shadow(0 0 4px rgba(255,255,255,0.2));
        transition: transform 0.25s ease;
    }
    .faq-accordion .accordion-button:not(.collapsed)::after {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23ffc107'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
        filter: drop-shadow(0 0 4px rgba(255,193,7,0.4));
    }
    .faq-accordion .accordion-body {
        color: rgba(255, 255, 255, 0.7);
        font-size: 0.95rem;
        line-height: 1.8;
        padding: 0 28px 24px;
    }
</style>
@endpush

@section('content')
<div class="faq-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                
                {{-- Back button --}}
                <div class="mb-4">
                    <a href="{{ route('home') }}" class="text-decoration-none text-white-50 hover-text-warning small transition-all">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Beranda
                    </a>
                </div>

                {{-- FAQ Wrapper --}}
                <div class="faq-section shadow-lg">
                    <div class="text-center mb-5">
                        <h2 class="section-title text-white text-uppercase d-block mb-3 fw-bold" style="letter-spacing: 1px;">Yomuda FAQ Center</h2>
                        <p class="text-white-50 mb-0 px-3">
                            Temukan semua jawaban lengkap mengenai pendaftaran, pelaksanaan turnamen, dan regulasi Yomuda Championship.
                        </p>
                    </div>

                    @if(isset($faqs) && count($faqs) > 0)
                        <div class="accordion faq-accordion" id="faqAccordion">
                            @foreach($faqs as $index => $faq)
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="faqHeading{{ $faq->id }}">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse{{ $faq->id }}" aria-expanded="false" aria-controls="faqCollapse{{ $faq->id }}">
                                            {{ $faq->question }}
                                        </button>
                                    </h2>
                                    <div id="faqCollapse{{ $faq->id }}" class="accordion-collapse collapse" aria-labelledby="faqHeading{{ $faq->id }}" data-bs-parent="#faqAccordion">
                                        <div class="accordion-body">
                                            {!! nl2br(e($faq->answer)) !!}
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-5 text-secondary">
                            <i class="bi bi-question-circle fs-1 text-muted mb-3 d-block"></i>
                            Belum ada pertanyaan umum yang diaktifkan saat ini.
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
