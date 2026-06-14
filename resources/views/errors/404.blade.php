@extends('layouts.app')

@section('title', '404 - Halaman Tidak Ditemukan | Yomuda Championship')

@section('content')
<div class="container d-flex align-items-center justify-content-center" style="min-height: 70vh;">
    <div class="text-center">
        <div class="mb-4">
            <i class="bi bi-exclamation-triangle text-warning" style="font-size: 5rem; filter: drop-shadow(0 0 15px rgba(255, 193, 7, 0.3));"></i>
        </div>
        
        <h1 class="fw-black text-white mb-2" style="font-family: 'Arial Black', sans-serif; font-size: 3rem; letter-spacing: -2px;">
            404 <span class="text-warning">ERROR</span>
        </h1>
        
        <h4 class="text-white fw-bold mb-4">Waduh, Squad Kamu Nyasar!</h4>
        
        <p class="text-white mb-5 mx-auto" style="max-width: 400px;">
            Halaman yang kamu cari nggak ada atau sudah pindah base. Jangan sampai telat masuk lobby, yuk balik ke halaman utama!
        </p>

        <a href="{{ route('home') }}" class="btn btn-warning px-5 py-3 fw-bold shadow-lg" style="border-radius: 50px; transition: 0.3s;">
            <i class="bi bi-house-door-fill me-2"></i> KEMBALI KE HOME
        </a>
    </div>
</div>

<style>
    body {
        background-color: #0d0f12; 
    }
    .btn-warning:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(255, 193, 7, 0.2) !important;
    }
</style>
@endsection