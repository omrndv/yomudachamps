@extends('layouts.app')
@section('title', 'Pendaftaran Berhasil')

@section('content')
<style>
    .success-container {
        position: relative;
        padding: 3px;
        background: linear-gradient(45deg, #28a745, #343a40, #ffc107);
        background-size: 400% 400%;
        animation: gradient-animation 5s ease infinite;
        border-radius: 24px;
        max-width: 420px;
        width: 100%;
    }

    @keyframes gradient-animation {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    .success-card {
        background: #121417;
        border-radius: 22px;
        padding: 40px 30px;
        color: #fff;
    }

    .status-box {
        background: rgba(40, 167, 69, 0.05);
        border: 1px solid rgba(40, 167, 69, 0.2);
        border-radius: 12px;
        padding: 15px;
        text-align: left;
    }

    .btn-wa {
        background: #25d366;
        color: #fff;
        border: none;
        border-radius: 12px;
        padding: 16px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 1px;
        width: 100%;
        transition: all 0.4s;
    }

    .btn-wa:hover {
        background: #20ba5a;
        box-shadow: 0 0 25px rgba(37, 211, 102, 0.4);
        transform: translateY(-3px);
    }
</style>

<div class="success-container mx-auto">
    <div class="success-card text-center">
        <img src="/images/logo-yomuda.png" alt="Logo Yomuda" class="mb-3" style="width: 120px; height: auto; object-fit: contain;">

        <h3 class="fw-bold text-warning mb-1">REGISTRASI BERHASIL</h3>
        <p class="text-secondary small mb-4">Slot tim kamu sudah aman dalam turnamen! (Screenshot halaman ini untuk bukti sukses daftar).</p>

        <div class="status-box mb-4" style="background: rgba(255,255,255,0.05); padding: 15px; border-radius: 12px;">
            <div class="d-flex justify-content-between mb-2">
                <span class="small text-secondary fw-bold">Status</span>
                <span class="small fw-bold text-success">Lunas</span>
            </div>
            <div class="d-flex justify-content-between">
                <span class="small text-secondary fw-bold">Nama Team</span>
                <span class="small fw-bold text-white">{{ $team->name }}</span>
            </div>
        </div>

        <p class="small text-white-50 mb-4">
            Klik tombol di bawah untuk masuk ke grup koordinasi peserta.
        </p>

        @if($team->season->wa_link)
        <a href="{{ $team->season->wa_link }}" target="_blank" class="btn btn-wa text-decoration-none d-block text-center mb-3">
            <i class="bi bi-whatsapp me-2"></i> GABUNG GRUP WHATSAPP
        </a>
        @else
        <div class="alert alert-dark small text-center mb-3" style="background: rgba(255,255,255,0.05); border: 1px dashed #6c757d;">
            <i class="bi bi-info-circle me-1"></i> Link grup belum tersedia. Admin akan menghubungimu.
        </div>
        @endif

        {{-- Share Card Button --}}
        <button id="btnDownloadCard" class="btn d-block w-100 text-center text-dark fw-bold mb-4 py-3" style="background: linear-gradient(45deg, #ffc107, #ff9800); border: none; border-radius: 12px; font-size: 0.9rem; letter-spacing: 0.5px; box-shadow: 0 4px 15px rgba(255, 193, 7, 0.2);">
            <i class="bi bi-image-fill me-2"></i> DOWNLOAD KARTU TIM (IG STORY)
        </button>

        <!-- Hidden Canvas for card generation -->
        <canvas id="shareCardCanvas" width="1080" height="1920" style="display: none;"></canvas>

        <div class="mt-4">
            <p class="text-secondary mb-1" style="font-size: 0.7rem; letter-spacing: 1px;">
                ID PENDAFTARAN: #{{ $team->trx_id }}
            </p>
            <button onclick="daftarLagi()" class="btn btn-sm btn-outline-secondary border-0" style="font-size: 0.65rem; opacity: 0.6;">
                <i class="bi bi-plus-circle me-1"></i> Daftar Tim Lainnya
            </button>
        </div>
    </div>
</div>

<script>
    function daftarLagi() {
        window.location.href = "{{ route('register.form') }}";
    }

    document.addEventListener('DOMContentLoaded', () => {
        const btnDownload = document.getElementById('btnDownloadCard');
        if (btnDownload) {
            btnDownload.addEventListener('click', generateAndDownloadCard);
        }
    });

    function generateAndDownloadCard() {
        const btn = document.getElementById('btnDownloadCard');
        const oldHtml = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = `<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span> MEMBUAT KARTU...`;

        const canvas = document.getElementById('shareCardCanvas');
        const ctx = canvas.getContext('2d');

        // 1. Draw Background Esport Gradient
        const grad = ctx.createLinearGradient(0, 0, 1080, 1920);
        grad.addColorStop(0, '#0a0d14');
        grad.addColorStop(0.3, '#111622');
        grad.addColorStop(0.7, '#1b2234');
        grad.addColorStop(1, '#070a0f');
        ctx.fillStyle = grad;
        ctx.fillRect(0, 0, 1080, 1920);

        // 2. Draw Diagonal Esport Stripes Background
        ctx.fillStyle = 'rgba(255, 193, 7, 0.015)';
        for (let i = -1000; i < 2000; i += 120) {
            ctx.beginPath();
            ctx.moveTo(i, 0);
            ctx.lineTo(i + 400, 1920);
            ctx.lineTo(i + 500, 1920);
            ctx.lineTo(i + 100, 0);
            ctx.closePath();
            ctx.fill();
        }

        // Draw Subtle Grid
        ctx.strokeStyle = 'rgba(255, 255, 255, 0.02)';
        ctx.lineWidth = 1;
        const gridSpacing = 100;
        for (let x = 0; x < 1080; x += gridSpacing) {
            ctx.beginPath(); ctx.moveTo(x, 0); ctx.lineTo(x, 1920); ctx.stroke();
        }
        for (let y = 0; y < 1920; y += gridSpacing) {
            ctx.beginPath(); ctx.moveTo(0, y); ctx.lineTo(1080, y); ctx.stroke();
        }

        // Draw Big Esport Hexagon/Crest in Background Center
        ctx.strokeStyle = 'rgba(255, 193, 7, 0.05)';
        ctx.lineWidth = 4;
        ctx.beginPath();
        ctx.moveTo(540, 450);
        ctx.lineTo(840, 600);
        ctx.lineTo(840, 1100);
        ctx.lineTo(540, 1250);
        ctx.lineTo(240, 1100);
        ctx.lineTo(240, 600);
        ctx.closePath();
        ctx.stroke();

        // 3. Draw Outer Neon Border/Frame
        ctx.strokeStyle = '#ffc107';
        ctx.lineWidth = 6;
        ctx.shadowBlur = 20;
        ctx.shadowColor = '#ffc107';
        
        const offset = 50;
        // Draw double frame with cutouts
        ctx.strokeRect(offset, offset, 1080 - (offset * 2), 1920 - (offset * 2));
        
        ctx.strokeStyle = '#ffffff';
        ctx.lineWidth = 2;
        ctx.strokeRect(offset + 12, offset + 12, 1080 - ((offset + 12) * 2), 1920 - ((offset + 12) * 2));

        // Draw esport bracket corners
        ctx.fillStyle = '#ffc107';
        ctx.shadowBlur = 0;
        
        // Top-Left corner block
        ctx.fillRect(offset, offset, 80, 20);
        ctx.fillRect(offset, offset, 20, 80);
        
        // Top-Right corner block
        ctx.fillRect(1080 - offset - 80, offset, 80, 20);
        ctx.fillRect(1080 - offset - 20, offset, 20, 80);

        // Bottom-Left corner block
        ctx.fillRect(offset, 1920 - offset - 20, 80, 20);
        ctx.fillRect(offset, 1920 - offset - 80, 20, 80);

        // Bottom-Right corner block
        ctx.fillRect(1080 - offset - 80, 1920 - offset - 20, 80, 20);
        ctx.fillRect(1080 - offset - 20, 1920 - offset - 80, 20, 80);

        // Load & Draw Yomuda Logo
        const logo = new Image();
        logo.src = '/images/logo-yomuda.png';
        logo.onload = function() {
            // Draw logo at top center
            const logoW = 320;
            const logoH = 320;
            const logoX = (1080 - logoW) / 2;
            const logoY = 200;
            ctx.drawImage(logo, logoX, logoY, logoW, logoH);

            // 4. Draw Header Text
            ctx.fillStyle = '#ffffff';
            ctx.font = 'bold 32px "Plus Jakarta Sans", sans-serif';
            ctx.textAlign = 'center';
            ctx.letterSpacing = '3px';
            ctx.fillText('YOMUDA CHAMPIONSHIP', 1080 / 2, 580);

            // 5. Draw Title Status (Glow)
            ctx.shadowBlur = 30;
            ctx.shadowColor = '#28a745';
            ctx.fillStyle = '#28a745';
            ctx.font = '900 80px "Plus Jakarta Sans", sans-serif';
            ctx.fillText('OFFICIALLY REGISTERED', 1080 / 2, 700);

            // Slanted Ribbon/Banner for Team Name
            ctx.shadowBlur = 0;
            ctx.fillStyle = 'rgba(255, 193, 7, 0.1)';
            ctx.strokeStyle = '#ffc107';
            ctx.lineWidth = 3;
            ctx.beginPath();
            ctx.moveTo(120, 900);
            ctx.lineTo(960, 850);
            ctx.lineTo(960, 1070);
            ctx.lineTo(120, 1120);
            ctx.closePath();
            ctx.fill();
            ctx.stroke();

            // 6. Draw Team Name Section
            ctx.fillStyle = '#ffffff';
            ctx.font = 'bold italic 34px "Plus Jakarta Sans", sans-serif';
            ctx.fillText('TEAM SQUAD', 1080 / 2, 915);

            ctx.shadowBlur = 25;
            ctx.shadowColor = '#ffc107';
            ctx.fillStyle = '#ffc107';
            ctx.font = '900 italic 100px "Plus Jakarta Sans", sans-serif';
            ctx.fillText("{{ strtoupper($team->name) }}", 1080 / 2, 1030);

            // 7. Draw Tournament Name Section
            ctx.shadowBlur = 0;
            ctx.fillStyle = '#94a3b8';
            ctx.font = 'bold 32px "Plus Jakarta Sans", sans-serif';
            ctx.fillText('SEASONS & TOURNAMENT', 1080 / 2, 1220);

            ctx.fillStyle = '#ffffff';
            ctx.font = '900 60px "Plus Jakarta Sans", sans-serif';
            ctx.fillText("{{ strtoupper($team->season->name) }}", 1080 / 2, 1310);

            // 8. Draw Footer Info (ID pendaftaran & Brand)
            ctx.fillStyle = 'rgba(255, 193, 7, 0.2)';
            ctx.fillRect(200, 1440, 680, 2);

            ctx.fillStyle = '#8e9bb0';
            ctx.font = 'bold 36px "Courier New", monospace';
            ctx.fillText('REG ID: #{{ $team->trx_id }}', 1080 / 2, 1530);

            // Draw a green glowing verified badge or shield at bottom
            ctx.shadowBlur = 15;
            ctx.shadowColor = '#28a745';
            ctx.fillStyle = '#28a745';
            ctx.beginPath();
            ctx.arc(540, 1660, 45, 0, 2 * Math.PI);
            ctx.fill();

            // Draw checkmark inside badge
            ctx.shadowBlur = 0;
            ctx.strokeStyle = '#ffffff';
            ctx.lineWidth = 8;
            ctx.beginPath();
            ctx.moveTo(520, 1660);
            ctx.lineTo(535, 1675);
            ctx.lineTo(565, 1640);
            ctx.stroke();

            ctx.fillStyle = '#ffc107';
            ctx.font = '900 32px "Plus Jakarta Sans", sans-serif';
            ctx.fillText('VERIFIED PARTICIPANT', 1080 / 2, 1770);

            // Trigger Download
            const link = document.createElement('a');
            link.download = 'Yomuda_StoryCard_' + "{{ $team->name }}" + '.png';
            link.href = canvas.toDataURL('image/png');
            link.click();

            // Restore button
            btn.disabled = false;
            btn.innerHTML = oldHtml;
        };

        // Fallback if logo fails to load (still generates card)
        logo.onerror = function() {
            ctx.fillStyle = '#ffffff';
            ctx.font = 'bold 32px "Plus Jakarta Sans", sans-serif';
            ctx.textAlign = 'center';
            ctx.fillText('YOMUDA CHAMPIONSHIP', 1080 / 2, 580);

            ctx.shadowBlur = 30;
            ctx.shadowColor = '#28a745';
            ctx.fillStyle = '#28a745';
            ctx.font = '900 80px "Plus Jakarta Sans", sans-serif';
            ctx.fillText('OFFICIALLY REGISTERED', 1080 / 2, 700);

            ctx.shadowBlur = 0;
            ctx.fillStyle = 'rgba(255, 193, 7, 0.1)';
            ctx.strokeStyle = '#ffc107';
            ctx.lineWidth = 3;
            ctx.beginPath();
            ctx.moveTo(120, 900);
            ctx.lineTo(960, 850);
            ctx.lineTo(960, 1070);
            ctx.lineTo(120, 1120);
            ctx.closePath();
            ctx.fill();
            ctx.stroke();

            ctx.fillStyle = '#ffffff';
            ctx.font = 'bold italic 34px "Plus Jakarta Sans", sans-serif';
            ctx.fillText('TEAM SQUAD', 1080 / 2, 915);

            ctx.shadowBlur = 25;
            ctx.shadowColor = '#ffc107';
            ctx.fillStyle = '#ffc107';
            ctx.font = '900 italic 100px "Plus Jakarta Sans", sans-serif';
            ctx.fillText("{{ strtoupper($team->name) }}", 1080 / 2, 1030);

            ctx.shadowBlur = 0;
            ctx.fillStyle = '#94a3b8';
            ctx.font = 'bold 32px "Plus Jakarta Sans", sans-serif';
            ctx.fillText('SEASONS & TOURNAMENT', 1080 / 2, 1220);

            ctx.fillStyle = '#ffffff';
            ctx.font = '900 60px "Plus Jakarta Sans", sans-serif';
            ctx.fillText("{{ strtoupper($team->season->name) }}", 1080 / 2, 1310);

            ctx.fillStyle = 'rgba(255, 193, 7, 0.2)';
            ctx.fillRect(200, 1440, 680, 2);

            ctx.fillStyle = '#8e9bb0';
            ctx.font = 'bold 36px "Courier New", monospace';
            ctx.fillText('REG ID: #{{ $team->trx_id }}', 1080 / 2, 1530);

            ctx.shadowBlur = 15;
            ctx.shadowColor = '#28a745';
            ctx.fillStyle = '#28a745';
            ctx.beginPath();
            ctx.arc(540, 1660, 45, 0, 2 * Math.PI);
            ctx.fill();

            ctx.shadowBlur = 0;
            ctx.strokeStyle = '#ffffff';
            ctx.lineWidth = 8;
            ctx.beginPath();
            ctx.moveTo(520, 1660);
            ctx.lineTo(535, 1675);
            ctx.lineTo(565, 1640);
            ctx.stroke();

            ctx.fillStyle = '#ffc107';
            ctx.font = '900 32px "Plus Jakarta Sans", sans-serif';
            ctx.fillText('VERIFIED PARTICIPANT', 1080 / 2, 1770);

            const link = document.createElement('a');
            link.download = 'Yomuda_StoryCard_' + "{{ $team->name }}" + '.png';
            link.href = canvas.toDataURL('image/png');
            link.click();

            btn.disabled = false;
            btn.innerHTML = oldHtml;
        };
    }
</script>
@endsection