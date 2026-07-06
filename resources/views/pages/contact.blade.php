@extends('layouts.app')
@section('title', 'Kontak Kami - Yomuda Championship')
@section('content')
@php
    $adminWaLink = '6285122616191';
    $adminEmail = 'yomudachampionship@gmail.com';
@endphp
<div class="container py-5 text-center">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card bg-dark border-secondary p-5" style="border-radius: 25px;">
                <h2 class="text-warning fw-bold mb-3">Kontak Support</h2>
                <p class="text-white-50 mb-5">Butuh bantuan terkait pendaftaran atau pembayaran? Hubungi kami melalui saluran resmi berikut:</p>
                
                <div class="d-grid gap-3">
                    <a href="https://wa.me/{{ $adminWaLink }}" class="btn btn-outline-warning p-3 fw-bold" style="border-radius: 15px;">
                        <i class="bi bi-whatsapp me-2"></i> WHATSAPP SUPPORT
                    </a>
                    <a href="mailto:{{ $adminEmail }}" class="btn btn-outline-light p-3 fw-bold" style="border-radius: 15px;">
                        <i class="bi bi-envelope me-2"></i> EMAIL OFFICIAL
                    </a>
                </div>

                <div class="mt-4 pt-4 border-top border-secondary border-opacity-20 text-start text-white-50" style="font-size: 0.85rem;">
                    <strong class="text-warning d-block mb-1">Alamat Usaha:</strong>
                    <p class="m-0">Dusun 1, Sokaraja Kulon, Sokaraja, Banyumas, Jawa Tengah, 53181.</p>
                </div>
                
                <p class="small text-secondary mt-4 mb-0">Jam Operasional: 09:00 - 22:00 WIB</p>
            </div>
        </div>
    </div>
</div>
@endsection