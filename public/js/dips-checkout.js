(function() {
    // Dips Checkout SDK Widget
    document.addEventListener('DOMContentLoaded', initDipsCheckout);

    function initDipsCheckout() {
        const widgetContainer = document.getElementById('dips-checkout-widget');
        if (!widgetContainer) return;

        const trxId = widgetContainer.getAttribute('data-trx-id');
        if (!trxId) {
            widgetContainer.innerHTML = '<div style="color: #ff3333; padding: 10px; font-weight: bold;">Error: data-trx-id attribute is missing.</div>';
            return;
        }

        const origin = widgetContainer.getAttribute('data-origin') || window.location.origin;
        const apiDetailsUrl = `${origin}/payment/manual/api/checkout-details/${trxId}`;
        const apiCheckUrl = `${origin}/payment/manual/check/${trxId}`;

        // Inject Premium Styles
        const style = document.createElement('style');
        style.innerHTML = `
            .dips-widget-card {
                background: #121214;
                border: 1px solid rgba(255, 122, 0, 0.25);
                border-radius: 16px;
                padding: 24px;
                color: #ffffff;
                font-family: 'Outfit', 'Inter', sans-serif;
                max-width: 420px;
                margin: 0 auto;
                text-align: center;
                box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
            }
            .dips-widget-header h5 {
                margin: 0;
                font-size: 1.15rem;
                font-weight: 700;
                color: #ffffff;
                letter-spacing: 0.5px;
            }
            .dips-widget-header p {
                margin: 4px 0 0 0;
                font-size: 0.8rem;
                color: #a1a1aa;
            }
            .dips-qr-container {
                background: #ffffff;
                padding: 12px;
                border-radius: 12px;
                display: inline-block;
                margin: 20px 0;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            }
            .dips-qr-img {
                width: 200px;
                height: 200px;
                display: block;
            }
            .dips-amount-box {
                background: rgba(255, 122, 0, 0.08);
                border: 1px solid rgba(255, 122, 0, 0.3);
                border-radius: 10px;
                padding: 12px;
                margin-bottom: 16px;
            }
            .dips-amount-label {
                font-size: 0.75rem;
                color: #a1a1aa;
                text-transform: uppercase;
                letter-spacing: 0.8px;
            }
            .dips-amount-value {
                font-size: 1.5rem;
                font-weight: 800;
                color: #ff7a00;
                margin-top: 4px;
            }
            .dips-timer {
                font-size: 0.85rem;
                color: #f59e0b;
                font-weight: 600;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 6px;
            }
            .dips-status-badge {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 6px 14px;
                border-radius: 20px;
                font-size: 0.78rem;
                font-weight: 700;
                margin-top: 16px;
                text-transform: uppercase;
            }
            .dips-status-pending {
                background: rgba(245, 158, 11, 0.15);
                color: #f59e0b;
                border: 1px solid rgba(245, 158, 11, 0.3);
            }
            .dips-status-paid {
                background: rgba(16, 185, 129, 0.15);
                color: #10b981;
                border: 1px solid rgba(16, 185, 129, 0.3);
            }
            .dips-loader {
                border: 2px solid rgba(255,255,255,0.1);
                border-radius: 50%;
                border-top: 2px solid #ff7a00;
                width: 16px;
                height: 16px;
                animation: dips-spin 0.8s linear infinite;
                display: inline-block;
            }
            @keyframes dips-spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
        `;
        document.head.appendChild(style);

        // Render Initial Loading
        widgetContainer.innerHTML = `
            <div class="dips-widget-card">
                <div style="padding: 40px 0;">
                    <div class="dips-loader" style="width: 32px; height: 32px; border-width: 3px;"></div>
                    <p style="margin-top: 16px; color: #a1a1aa; font-size: 0.9rem;">Menyiapkan pembayaran aman...</p>
                </div>
            </div>
        `;

        // Fetch Details
        fetch(apiDetailsUrl)
            .then(res => {
                if (!res.ok) throw new Error('Transaction not found or API error.');
                return res.json();
            })
            .then(data => {
                if (!data.success) {
                    widgetContainer.innerHTML = `<div class="dips-widget-card"><p style="color: #ef4444;">${data.message}</p></div>`;
                    return;
                }
                renderWidget(data);
            })
            .catch(err => {
                widgetContainer.innerHTML = `<div class="dips-widget-card"><p style="color: #ef4444;">Gagal memuat detail pembayaran. Silakan segarkan halaman.</p></div>`;
            });

        function renderWidget(tx) {
            if (tx.status === 'PAID') {
                renderSuccess(tx);
                return;
            }

            if (tx.status === 'EXPIRED' || tx.seconds_left <= 0) {
                renderExpired(tx);
                return;
            }

            const qrCodeUrl = `https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=${encodeURIComponent(tx.qris_string)}`;
            
            widgetContainer.innerHTML = `
                <div class="dips-widget-card">
                    <div class="dips-widget-header">
                        <h5>${tx.team_name}</h5>
                        <p>${tx.season_name}</p>
                    </div>

                    <div class="dips-qr-container">
                        <img src="${qrCodeUrl}" class="dips-qr-img" alt="QRIS Code">
                    </div>

                    <div class="dips-amount-box">
                        <div class="dips-amount-label">TOTAL PEMBAYARAN</div>
                        <div class="dips-amount-value">Rp ${formatRupiah(tx.amount)}</div>
                    </div>

                    <div class="dips-timer" id="dips-countdown-timer">
                        <div class="dips-loader"></div>
                        <span>Menunggu Pembayaran... (<span id="dips-time-string">--:--</span>)</span>
                    </div>

                    <div class="dips-status-badge dips-status-pending">
                        <i class="bi bi-clock-history"></i> PENDING
                    </div>
                </div>
            `;

            // Start Countdown
            let secondsLeft = tx.seconds_left;
            const timerText = document.getElementById('dips-time-string');
            
            const countdownInterval = setInterval(() => {
                secondsLeft--;
                if (secondsLeft <= 0) {
                    clearInterval(countdownInterval);
                    clearInterval(pollInterval);
                    renderExpired(tx);
                } else {
                    const mins = Math.floor(secondsLeft / 60);
                    const secs = secondsLeft % 60;
                    if (timerText) {
                        timerText.textContent = `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
                    }
                }
            }, 1000);

            // Start Status Polling
            const pollInterval = setInterval(() => {
                fetch(apiCheckUrl)
                    .then(res => res.json())
                    .then(check => {
                        if (check.status === 'PAID') {
                            clearInterval(countdownInterval);
                            clearInterval(pollInterval);
                            renderSuccess(tx);
                        }
                    })
                    .catch(err => console.error('Error polling status:', err));
            }, 5000);
        }

        function renderSuccess(tx) {
            widgetContainer.innerHTML = `
                <div class="dips-widget-card" style="border-color: rgba(16, 185, 129, 0.4); background: radial-gradient(circle at top, rgba(16, 185, 129, 0.1) 0%, #121214 70%);">
                    <div style="padding: 20px 0;">
                        <div style="font-size: 3rem; color: #10b981; margin-bottom: 12px;">✔️</div>
                        <h5 style="color: #ffffff; font-size: 1.3rem;">Pembayaran Sukses!</h5>
                        <p style="color: #a1a1aa; font-size: 0.85rem; margin-top: 8px;">Pendaftaran tim <strong>${tx.team_name}</strong> telah berhasil diverifikasi.</p>
                        
                        <div class="dips-status-badge dips-status-paid" style="margin-top: 24px;">
                            TERVERIFIKASI LUNAS
                        </div>
                    </div>
                </div>
            `;
        }

        function renderExpired(tx) {
            widgetContainer.innerHTML = `
                <div class="dips-widget-card" style="border-color: rgba(239, 68, 68, 0.3);">
                    <div style="padding: 20px 0;">
                        <div style="font-size: 3rem; color: #ef4444; margin-bottom: 12px;">❌</div>
                        <h5>Pembayaran Kedaluwarsa</h5>
                        <p style="color: #a1a1aa; font-size: 0.85rem; margin-top: 8px;">Waktu pembayaran untuk pendaftaran ini telah habis. Silakan daftarkan kembali tim Anda.</p>
                        
                        <div class="dips-status-badge" style="background: rgba(239, 68, 68, 0.15); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.3);">
                            EXPIRED
                        </div>
                    </div>
                </div>
            `;
        }

        function formatRupiah(num) {
            return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        }
    }
})();
