@extends('layouts.app')
@section('title', 'Media & Sosial - Yomuda Championship')

@push('styles')
<style>
    .glass-card {
        background: rgba(255, 255, 255, 0.03);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
    }
    
    .social-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 12px 24px;
        border-radius: 12px;
        font-weight: 600;
        transition: all 0.3s ease;
        text-decoration: none;
        color: white;
    }
    
    .btn-ig {
        background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%);
    }
    
    .btn-ig:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(220, 39, 67, 0.3);
        color: white;
    }
    
    .btn-tiktok {
        background: #000000;
        border: 1px solid rgba(255,255,255,0.1);
    }
    
    .btn-tiktok:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.4);
        background: #111;
        color: white;
    }
    
    .btn-youtube {
        background: #FF0000;
    }
    
    .btn-youtube:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(255, 0, 0, 0.3);
        color: white;
    }

    .video-container {
        position: relative;
        width: 100%;
        padding-bottom: 56.25%; /* 16:9 Aspect Ratio */
        height: 0;
        overflow: hidden;
        border-radius: 15px;
    }

    .video-container iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border: 0;
    }

    .hero-title {
        font-size: 2.5rem;
        font-weight: 800;
        background: linear-gradient(90deg, #fff, #a5a5a5);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    @media (max-width: 768px) {
        .hero-title {
            font-size: 1.8rem;
        }
    }
</style>
@endpush

@section('content')
@php
    $ig = \App\Models\Setting::getVal('social_instagram', 'https://www.instagram.com/yomuda.championship/');
    $tiktok = \App\Models\Setting::getVal('social_tiktok', 'https://www.tiktok.com/@yomudachampionship');
    $youtube = \App\Models\Setting::getVal('social_youtube', 'https://www.youtube.com/@ymdchamps');
    
    $embedUrl = null;
    if (str_contains($youtube, 'watch?v=')) {
        parse_str(parse_url($youtube, PHP_URL_QUERY), $query);
        if (isset($query['v'])) {
            $embedUrl = "https://www.youtube.com/embed/" . $query['v'];
        }
    } elseif (str_contains($youtube, 'youtu.be/')) {
        $path = parse_url($youtube, PHP_URL_PATH);
        $embedUrl = "https://www.youtube.com/embed" . $path;
    }
@endphp

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10 text-center mb-5">
            <h1 class="hero-title mb-3">Media & Sosial</h1>
            <p class="text-secondary fw-medium">Ikuti terus update terbaru dan tonton live streaming turnamen Yomuda Championship!</p>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8 mb-4">
            <div class="glass-card p-4 p-md-5">
                <div class="d-flex align-items-center mb-4">
                    <i class="bi bi-youtube text-danger fs-2 me-3"></i>
                    <h3 class="fw-bold mb-0">Live Streaming & Videos</h3>
                </div>
                
                @if($embedUrl)
                <div class="video-container shadow-lg mb-4">
                    <iframe src="{{ $embedUrl }}" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen loading="lazy"></iframe>
                </div>
                @else
                <div class="text-center py-5 rounded-4 mb-4" style="background: rgba(0,0,0,0.5); border: 1px dashed rgba(255,255,255,0.1);">
                    <i class="bi bi-camera-video text-secondary mb-3" style="font-size: 3rem;"></i>
                    <h5 class="fw-bold text-white mb-2">Kunjungi Channel YouTube Kami</h5>
                    <p class="text-secondary small mb-0">Tonton live stream dan highlight turnamen seru di channel kami.</p>
                </div>
                @endif
                
                <div class="text-center">
                    <a href="{{ $youtube }}" target="_blank" class="social-btn btn-youtube d-inline-flex w-auto px-5">
                        <i class="bi bi-youtube fs-5"></i>
                        <span>Buka YouTube Channel</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row justify-content-center mt-3">
        <div class="col-lg-8">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="glass-card p-4 h-100 text-center">
                        <div class="mb-3">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/e/e7/Instagram_logo_2016.svg" alt="Instagram" width="40">
                        </div>
                        <h5 class="fw-bold mb-3">Instagram</h5>
                        <p class="text-secondary small mb-4">Info turnamen, bracket, dan pengumuman pemenang.</p>
                        <a href="{{ $ig }}" target="_blank" class="social-btn btn-ig w-100">
                            <i class="bi bi-instagram"></i> Follow @yomuda.championship
                        </a>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="glass-card p-4 h-100 text-center">
                        <div class="mb-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-tiktok text-white" viewBox="0 0 16 16">
                                <path d="M9 0h1.98c.144.715.54 1.617 1.235 2.512C12.895 3.389 13.797 4 15 4v2c-1.753 0-3.07-.814-4-1.829V11a5 5 0 1 1-5-5v2a3 3 0 1 0 3 3V0Z"/>
                            </svg>
                        </div>
                        <h5 class="fw-bold mb-3">TikTok</h5>
                        <p class="text-secondary small mb-4">Momen seru, funny moment, dan highlight turnamen.</p>
                        <a href="{{ $tiktok }}" target="_blank" class="social-btn btn-tiktok w-100">
                            <i class="bi bi-tiktok"></i> Follow @yomudachampionship
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
