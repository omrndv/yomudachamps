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
        const grad = ctx.createLinearGradient(0, 0, 0, 1920);
        grad.addColorStop(0, '#0f1115');
        grad.addColorStop(0.5, '#161920');
        grad.addColorStop(1, '#0b0c0e');
        ctx.fillStyle = grad;
        ctx.fillRect(0, 0, 1080, 1920);

        // 2. Draw Esport Sci-Fi Grid/Patterns
        ctx.strokeStyle = 'rgba(255, 193, 7, 0.04)';
        ctx.lineWidth = 2;
        const gridSpacing = 80;
        for (let x = 0; x < 1080; x += gridSpacing) {
            ctx.beginPath();
            ctx.moveTo(x, 0);
            ctx.lineTo(x, 1920);
            ctx.stroke();
        }
        for (let y = 0; y < 1920; y += gridSpacing) {
            ctx.beginPath();
            ctx.moveTo(0, y);
            ctx.lineTo(1080, y);
            ctx.stroke();
        }

        // Draw futuristic corner borders/frames
        ctx.strokeStyle = '#ffc107';
        ctx.lineWidth = 8;
        const frameOffset = 60;
        const frameLength = 120;
        
        // Top-Left corner frame
        ctx.beginPath();
        ctx.moveTo(frameOffset + frameLength, frameOffset);
        ctx.lineTo(frameOffset, frameOffset);
        ctx.lineTo(frameOffset, frameOffset + frameLength);
        ctx.stroke();

        // Top-Right corner frame
        ctx.beginPath();
        ctx.moveTo(1080 - frameOffset - frameLength, frameOffset);
        ctx.lineTo(1080 - frameOffset, frameOffset);
        ctx.lineTo(1080 - frameOffset, frameOffset + frameLength);
        ctx.stroke();

        // Bottom-Left corner frame
        ctx.beginPath();
        ctx.moveTo(frameOffset + frameLength, 1920 - frameOffset);
        ctx.lineTo(frameOffset, 1920 - frameOffset);
        ctx.lineTo(frameOffset, 1920 - frameOffset - frameLength);
        ctx.stroke();

        // Bottom-Right corner frame
        ctx.beginPath();
        ctx.moveTo(1080 - frameOffset - frameLength, 1920 - frameOffset);
        ctx.lineTo(1080 - frameOffset, 1920 - frameOffset);
        ctx.lineTo(1080 - frameOffset, 1920 - frameOffset - frameLength);
        ctx.stroke();

        // Neon Glow effect for texts
        ctx.shadowBlur = 20;
        ctx.shadowColor = '#ffc107';

        // 3. Load & Draw Yomuda Logo
        const logo = new Image();
        logo.src = '/images/logo-yomuda.png';
        logo.onload = function() {
            // Draw logo at top center
            const logoW = 280;
            const logoH = 280;
            const logoX = (1080 - logoW) / 2;
            const logoY = 220;
            ctx.drawImage(logo, logoX, logoY, logoW, logoH);

            // 4. Draw Header Text
            ctx.shadowBlur = 0; // reset shadow for small text
            ctx.fillStyle = '#94a3b8';
            ctx.font = 'bold 36px "Plus Jakarta Sans", sans-serif';
            ctx.textAlign = 'center';
            ctx.fillText('OFFICIAL REGISTRATION', 1080 / 2, 590);

            // 5. Draw Title Status (Glow)
            ctx.shadowBlur = 25;
            ctx.shadowColor = '#28a745';
            ctx.fillStyle = '#28a745';
            ctx.font = 'black 90px "Plus Jakarta Sans", sans-serif';
            ctx.fillText('SIAP BERTANDING', 1080 / 2, 700);

            // Draw Divider Line
            ctx.shadowBlur = 0;
            ctx.fillStyle = 'rgba(255, 255, 255, 0.1)';
            ctx.fillRect(140, 780, 800, 4);

            // 6. Draw Team Name Section
            ctx.fillStyle = '#94a3b8';
            ctx.font = 'semibold 36px "Plus Jakarta Sans", sans-serif';
            ctx.fillText('NAMA SQUAD / TIM', 1080 / 2, 880);

            ctx.shadowBlur = 30;
            ctx.shadowColor = '#ffc107';
            ctx.fillStyle = '#ffc107';
            ctx.font = 'black 110px "Plus Jakarta Sans", sans-serif';
            ctx.fillText("{{ strtoupper($team->name) }}", 1080 / 2, 1020);

            // 7. Draw Tournament Name Section
            ctx.shadowBlur = 0;
            ctx.fillStyle = '#94a3b8';
            ctx.font = 'semibold 36px "Plus Jakarta Sans", sans-serif';
            ctx.fillText('TURNAMEN', 1080 / 2, 1180);

            ctx.fillStyle = '#ffffff';
            ctx.font = 'bold 64px "Plus Jakarta Sans", sans-serif';
            ctx.fillText("{{ strtoupper($team->season->name) }}", 1080 / 2, 1290);

            // 8. Draw Footer Info (ID pendaftaran & Brand)
            ctx.fillStyle = 'rgba(255, 255, 255, 0.15)';
            ctx.fillRect(140, 1420, 800, 4);

            ctx.fillStyle = '#64748b';
            ctx.font = 'mono 32px "Courier New", monospace';
            ctx.fillText('REG_ID: #{{ $team->trx_id }}', 1080 / 2, 1520);

            ctx.fillStyle = '#ffc107';
            ctx.font = 'bold 36px "Plus Jakarta Sans", sans-serif';
            ctx.fillText('YOMUDA CHAMPIONSHIP', 1080 / 2, 1680);

            ctx.fillStyle = '#64748b';
            ctx.font = '500 28px "Plus Jakarta Sans", sans-serif';
            ctx.fillText('yomudachampionship.com', 1080 / 2, 1740);

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
            ctx.shadowBlur = 0;
            ctx.fillStyle = '#94a3b8';
            ctx.font = 'bold 36px "Plus Jakarta Sans", sans-serif';
            ctx.textAlign = 'center';
            ctx.fillText('OFFICIAL REGISTRATION', 1080 / 2, 590);

            ctx.shadowBlur = 25;
            ctx.shadowColor = '#28a745';
            ctx.fillStyle = '#28a745';
            ctx.font = 'black 90px "Plus Jakarta Sans", sans-serif';
            ctx.fillText('SIAP BERTANDING', 1080 / 2, 700);

            ctx.shadowBlur = 0;
            ctx.fillStyle = 'rgba(255, 255, 255, 0.1)';
            ctx.fillRect(140, 780, 800, 4);

            ctx.fillStyle = '#94a3b8';
            ctx.font = 'semibold 36px "Plus Jakarta Sans", sans-serif';
            ctx.fillText('NAMA SQUAD / TIM', 1080 / 2, 880);

            ctx.shadowBlur = 30;
            ctx.shadowColor = '#ffc107';
            ctx.fillStyle = '#ffc107';
            ctx.font = 'black 110px "Plus Jakarta Sans", sans-serif';
            ctx.fillText("{{ strtoupper($team->name) }}", 1080 / 2, 1020);

            ctx.shadowBlur = 0;
            ctx.fillStyle = '#94a3b8';
            ctx.font = 'semibold 36px "Plus Jakarta Sans", sans-serif';
            ctx.fillText('TURNAMEN', 1080 / 2, 1180);

            ctx.fillStyle = '#ffffff';
            ctx.font = 'bold 64px "Plus Jakarta Sans", sans-serif';
            ctx.fillText("{{ strtoupper($team->season->name) }}", 1080 / 2, 1290);

            ctx.fillStyle = 'rgba(255, 255, 255, 0.15)';
            ctx.fillRect(140, 1420, 800, 4);

            ctx.fillStyle = '#64748b';
            ctx.font = 'mono 32px "Courier New", monospace';
            ctx.fillText('REG_ID: #{{ $team->trx_id }}', 1080 / 2, 1520);

            ctx.fillStyle = '#ffc107';
            ctx.font = 'bold 36px "Plus Jakarta Sans", sans-serif';
            ctx.fillText('YOMUDA CHAMPIONSHIP', 1080 / 2, 1680);

            ctx.fillStyle = '#64748b';
            ctx.font = '500 28px "Plus Jakarta Sans", sans-serif';
            ctx.fillText('yomudachampionship.com', 1080 / 2, 1740);

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