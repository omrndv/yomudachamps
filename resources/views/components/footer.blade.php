<style>
    @keyframes footer-gradient {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    .animated-footer {
        background: linear-gradient(45deg, #ffc107, #343a40, #ffc107, #ff9800);
        background-size: 400% 400%;
        animation: footer-gradient 5s ease infinite;
        margin: 0;
    }

    .footer-content {
        background: #121417;
        padding: 50px 20px;
    }

    .footer-logo {
        width: 80px;
        margin-bottom: 15px;
        filter: drop-shadow(0 0 12px rgba(255, 193, 7, .35));
    }

    .nav-pills-custom {
        background: rgba(255, 255, 255, 0.03);
        border-radius: 50px;
        border: 1px solid rgba(255, 255, 255, 0.05);
        padding: 8px 20px;
        display: inline-flex;
        gap: 15px;
        flex-wrap: wrap;
        justify-content: center;
    }

    .footer-link {
        color: #ffc107;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.8rem;
        transition: .3s;
    }

    .footer-link:hover {
        color: #fff;
        text-shadow: 0 0 10px rgba(255,193,7,.6);
        transform: translateY(-2px);
    }

    .social-box {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 20px;
        margin-top: 25px;
    }

    .social-item {
        color: #fff;
        text-decoration: none;
        font-size: 0.7rem;
        font-weight: 800;
        display: flex;
        align-items: center;
        gap: 8px;
        opacity: .7;
        transition: .3s;
        letter-spacing: 1px;
    }

    .social-item:hover {
        opacity: 1;
        color: #ffc107;
        transform: translateY(-2px);
    }

    .social-item i {
        font-size: 1.1rem;
    }
</style>

<footer class="animated-footer">
    <div class="footer-content text-center">
        <div class="container" style="max-width: 700px">

            <img src="{{ asset('images/logo-yomuda.png') }}" class="footer-logo" alt="Yomuda Logo">

            <a href="{{ url('/') }}" class="text-decoration-none">
                <h5 class="text-warning fw-bold mb-3" style="letter-spacing: 1px;">
                    YOMUDA CHAMPIONSHIP
                </h5>
            </a>

            <p class="small text-white-50 mb-3 px-3" style="line-height: 1.8;">
                Platform pendaftaran turnamen E-sports otomatis dengan sistem pembayaran digital terintegrasi.
            </p>

            <p class="small text-white-50 mb-4 px-3" style="line-height: 1.8; font-size: 0.75rem;">
                <i class="bi bi-geo-alt-fill text-warning me-1"></i> G7VR+227, Dusun I, Sokaraja Kulon, Kec. Sokaraja, Kabupaten Banyumas, Jawa Tengah 53181
            </p>

            <div class="nav-pills-custom mb-3">
                <a href="{{ route('privacy') }}" class="footer-link">Kebijakan Privasi</a>
                <span class="text-white-50">|</span>
                <a href="{{ route('terms') }}" class="footer-link">Ketentuan Layanan</a>
                <span class="text-white-50">|</span>
                <a href="{{ route('contact') }}" class="footer-link">Kontak Support</a>
            </div>

            <div class="social-box mb-4">
                <a href="{{ \App\Models\Setting::getVal('social_instagram', 'https://www.instagram.com/yomuda.championship/') }}" target="_blank" class="social-item">
                    <i class="bi bi-instagram text-warning"></i> INSTAGRAM
                </a>
                <a href="{{ \App\Models\Setting::getVal('social_tiktok', 'https://www.tiktok.com/@yomudachampionship') }}" target="_blank" class="social-item">
                    <i class="bi bi-tiktok text-warning"></i> TIKTOK
                </a>
                <a href="{{ \App\Models\Setting::getVal('social_youtube', 'https://www.youtube.com/@ymdchamps/streams') }}" target="_blank" class="social-item">
                    <i class="bi bi-youtube text-warning"></i> YOUTUBE
                </a>
            </div>

            <div class="border-top border-secondary pt-4" style="border-opacity: .1;">
                <p class="small text-secondary mb-0" style="font-size: .7rem; letter-spacing: 1px;">
                    © {{ date('Y') }} YOMUDA CHAMPIONSHIP • ALL RIGHTS RESERVED
                </p>
            </div>

        </div>
    </div>
</footer>
