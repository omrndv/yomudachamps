<style>
    @keyframes footer-gradient {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    .animated-footer {
        position: relative;
        padding-top: 3px;
        background: linear-gradient(45deg, #ffc107, #343a40, #ffc107, #ff9800);
        background-size: 400% 400%;
        animation: footer-gradient 5s ease infinite;
    }

    .footer-content {
        background: #121417;
        padding: 50px 20px;
    }

    .footer-link {
        color: #ffc107;
        text-decoration: none;
        transition: all 0.3s ease;
        font-weight: 600;
    }

    .footer-link:hover {
        color: #fff;
        text-shadow: 0 0 10px rgba(255, 193, 7, 0.6);
    }

    .social-divider {
        color: rgba(255, 255, 255, 0.1);
        margin: 0 10px;
    }
</style>

<footer class="animated-footer">
    <div class="footer-content text-center">
        <div class="container" style="max-width: 600px">
            
            <h5 class="text-warning fw-bold mb-3" style="letter-spacing: 1px;">YOMUDA CHAMPIONSHIP</h5>
            <p class="small text-white-50 mb-4 px-3" style="line-height: 1.8;">
                Turnamen e-sports Mobile Legends yang diselenggarakan secara online dengan sistem kompetitif & fair play. Buktikan bahwa timmu adalah yang terkuat!
            </p>

            <div class="mb-4">
                <p class="small text-light mb-1">
                    <i class="bi bi-whatsapp text-warning me-2"></i>
                    <a href="https://wa.me/6281991444084" class="text-decoration-none text-white fw-bold">081991444084</a>
                </p>
                <p class="small">
                    <i class="bi bi-envelope text-warning me-2"></i>
                    <a href="mailto:yomudachampionship@gmail.com" class="text-decoration-none text-white-50">yomudachampionship@gmail.com</a>
                </p>
            </div>

            <div class="mb-4 py-2 px-4 d-inline-block" style="background: rgba(255,255,255,0.03); border-radius: 50px;">
                <a href="https://www.instagram.com/yomuda.championship/" class="footer-link small">Instagram</a>
                <span class="social-divider">|</span>
                <a href="https://www.tiktok.com/@yomudachampionship" class="footer-link small">TikTok</a>
                <span class="social-divider">|</span>
                <a href="https://www.youtube.com/@ymdchamps/streams" class="footer-link small">YouTube</a>
            </div>

            <div class="pt-4 border-top border-secondary" style="border-opacity: 0.1;">
                <p class="small text-secondary mb-0" style="font-size: 0.7rem; letter-spacing: 1px;">
                    © {{ date('Y') }} YOMUDA CHAMPIONSHIP • ALL RIGHTS RESERVED
                </p>
            </div>

        </div>
    </div>
</footer>