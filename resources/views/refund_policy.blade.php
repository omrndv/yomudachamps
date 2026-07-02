@extends('layouts.app')

@section('title', 'Kebijakan Pengembalian Dana (Refund Policy) - Yomuda Championship')

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
                <div class="policy-section shadow-lg text-white text-start">
                    <h2 class="fw-bold mb-4 border-bottom border-secondary pb-3 text-warning">
                        Kebijakan Pengembalian Dana (Refund Policy)
                    </h2>
                    
                    <p class="text-secondary small mb-4">Terakhir Diperbarui: 2 Juli 2026</p>

                    <div class="lh-lg" style="font-size: 0.95rem; color: rgba(255, 255, 255, 0.8);">
                        <h5 class="fw-bold text-white mt-4 mb-2">1. Ketentuan Umum Pembayaran</h5>
                        <p class="mb-3">
                            Semua pembayaran biaya pendaftaran turnamen yang dilakukan melalui website Yomuda Championship (yomudachamps.com) menggunakan metode pembayaran otomatis bersifat final dan tidak dapat dibatalkan secara sepihak oleh peserta.
                        </p>

                        <h5 class="fw-bold text-white mt-4 mb-2">2. Pembatalan oleh Peserta</h5>
                        <p class="mb-3">
                            Uang pendaftaran yang telah dibayarkan oleh tim peserta <strong>tidak dapat dikembalikan (non-refundable)</strong> jika terjadi pembatalan keikutsertaan secara sepihak oleh tim peserta karena alasan apa pun (termasuk kelalaian, jadwal bentrok pribadi, atau diskualifikasi akibat pelanggaran aturan).
                        </p>

                        <h5 class="fw-bold text-white mt-4 mb-2">3. Pembatalan atau Penundaan oleh Penyelenggara</h5>
                        <p class="mb-3">
                            Apabila turnamen dibatalkan secara total oleh pihak penyelenggara Yomuda Championship, maka seluruh tim peserta yang telah membayar lunas berhak mendapatkan pengembalian dana 100% dari biaya pendaftaran. Proses pengembalian akan dilakukan maksimal dalam waktu 3 hari kerja ke rekening asal atau melalui kesepakatan transfer.
                        </p>

                        <h5 class="fw-bold text-white mt-4 mb-2">4. Kontak Bantuan</h5>
                        <p class="mb-0">
                            Jika Anda mengalami masalah pembayaran ganda atau ingin mengajukan pertanyaan lebih lanjut, silakan hubungi tim kami melalui email <strong>yomudachampionship@gmail.com</strong> atau WhatsApp di nomor <strong>0851-2261-6191</strong>.
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
