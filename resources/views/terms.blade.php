@extends('layouts.app')

@section('title', 'Syarat & Ketentuan (Terms & Conditions) - Yomuda Championship')

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
                        Syarat & Ketentuan (Terms & Conditions)
                    </h2>
                    
                    <p class="text-secondary small mb-4">Terakhir Diperbarui: 2 Juli 2026</p>

                    <div class="lh-lg" style="font-size: 0.95rem; color: rgba(255, 255, 255, 0.8);">
                        <h5 class="fw-bold text-white mt-4 mb-2">1. Definisi dan Layanan</h5>
                        <p class="mb-3">
                            Selamat datang di Yomuda Championship (yomudachamps.com). Dengan mendaftar, mengakses, atau menggunakan layanan kami, Anda menyetujui untuk terikat oleh Syarat dan Ketentuan ini. Kami menyediakan platform pendaftaran, penjadwalan bagan turnamen game, e-sertifikat, dan pelaporan skor pertandingan.
                        </p>

                        <h5 class="fw-bold text-white mt-4 mb-2">2. Pendaftaran dan Akun Tim</h5>
                        <p class="mb-3">
                            Perwakilan tim wajib mengisi data pendaftaran dengan benar, termasuk nama tim, email, dan nomor WhatsApp aktif kapten tim. Penggunaan nama tim yang mengandung unsur SARA, pornografi, atau menyinggung pihak lain dilarang dan dapat didelete sepihak oleh admin.
                        </p>

                        <h5 class="fw-bold text-white mt-4 mb-2">3. Transaksi Pembayaran</h5>
                        <p class="mb-3">
                            Biaya registrasi wajib dibayarkan melalui metode payment gateway otomatis yang disediakan. Pendaftaran baru dinyatakan berhasil setelah status transaksi berubah menjadi lunas (PAID). Seluruh transaksi pendaftaran bersifat final.
                        </p>

                        <h5 class="fw-bold text-white mt-4 mb-2">4. Aturan Turnamen</h5>
                        <p class="mb-3">
                            Peserta menyetujui untuk tunduk pada peraturan resmi turnamen yang berlaku untuk tiap season. Penggunaan cheat, kecurangan, provokasi berlebih, serta manipulasi laporan skor tanding akan ditindak tegas berupa diskualifikasi permanen tanpa pengembalian biaya pendaftaran.
</p>

                        <h5 class="fw-bold text-white mt-4 mb-2">5. Perubahan Ketentuan</h5>
                        <p class="mb-0">
                            Kami berhak mengubah atau memperbarui Syarat & Ketentuan ini sewaktu-waktu tanpa pemberitahuan sebelumnya. Pengguna disarankan untuk membaca halaman ini secara berkala.
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
