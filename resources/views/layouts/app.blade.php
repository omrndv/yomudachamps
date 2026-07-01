<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="index, follow">

    <title>@yield('title', 'Yomuda Championship - Platform Pendaftaran Turnamen E-sports')</title>
    <link rel="canonical" href="{{ url()->current() }}">

    <meta name="description" content="@yield('meta_description', 'Platform pendaftaran turnamen E-sports dengan sistem pembayaran otomatis dan verifikasi real-time. Kelola pendaftaran timmu dengan mudah.')">
    <meta name="keywords" content="@yield('meta_keywords', 'Yomuda Championship, pendaftaran turnamen e-sports, turnamen mobile legends, turnamen online indonesia')">
    <meta name="author" content="Yomuda Championship">

    <meta property="og:title" content="@yield('og_title', 'Yomuda Championship - Turnamen E-sports')">
    <meta property="og:description" content="@yield('meta_description', 'Sistem pendaftaran turnamen e-sports otomatis.')">
    <meta property="og:image" content="@yield('og_image', asset('images/logo-yomuda.png'))">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="@yield('twitter_title', 'Yomuda Championship')">
    <meta name="twitter:description" content="@yield('twitter_description', 'Daftar turnamen e-sports jadi lebih mudah dengan sistem otomatis.')">
    <meta name="twitter:image" content="@yield('twitter_image', asset('images/logo-yomuda.png'))">

    <link rel="icon" type="image/png" href="{{ asset('images/logo-yomuda.png') }}">

    <!-- Performance Optimizations: DNS Prefetch & Preconnect -->
    <link rel="dns-prefetch" href="//fonts.googleapis.com">
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link rel="dns-prefetch" href="//cdn.jsdelivr.net">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link rel="preload" as="image" href="/images/bg-mobile.jpg" media="(max-width: 768px)">
    <link rel="preload" as="image" href="/images/bg-yomuda.jpg" media="(min-width: 769px)">
    <link rel="preload" as="image" href="/images/logo-yomuda.png">

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Defer non-critical scripts -->
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}" defer></script>

    <meta name="google-site-verification" content="ErJnugnESZ9vYec5vVLW1evAKh6SQ5xJTG_jlY2WzOg">

    <style>
        html,
        body {
            min-height: 100%;
            margin: 0;
            padding: 0;
        }

        html {
            scroll-behavior: smooth;
            -webkit-text-size-adjust: 100%;
            text-size-adjust: 100%;
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box;
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
        }

        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: #121417;
            color: #ffffff;
            font-family: 'Poppins', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            overflow-x: hidden;
        }

        button,
        input,
        textarea,
        select {
            font-family: inherit;
        }

        a {
            text-decoration: none;
        }

        main {
            flex: 1;
            width: 100%;
            padding: 100px 20px 50px;
            background-image: url('/images/bg-yomuda.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
        }

        .content-wrapper {
            width: 100%;
            display: flex;
            justify-content: center;
        }

        .floating-check-team {
            position: fixed;
            top: 15px;
            right: 15px;
            z-index: 1050;
            border-radius: 50px;
            font-size: 0.7rem;
            padding: 7px 15px;
            font-weight: 800;
            letter-spacing: 0.2px;
            color: #000;
            background: #ffc107;
            border: none;
            box-shadow: 0 10px 28px rgba(0, 0, 0, 0.18);
            transition: 0.25s ease;
        }

        .floating-check-team:hover {
            background: #ffffff;
            color: #000;
            transform: translateY(-2px);
        }

        footer {
            margin-top: 80px;
            border-top: 1px solid rgba(255,255,255,.05);
        }

        @media (max-width: 768px) {
            main {
                padding: 80px 10px 50px;
                background-image: url('/images/bg-mobile.jpg');
                background-attachment: scroll;
            }

            .floating-check-team {
                top: 12px;
                right: 12px;
                font-size: 0.62rem;
                padding: 6px 12px;
            }
        }
    </style>

    @stack('styles')
</head>

<body>
    <a href="{{ route('check.team') }}" class="floating-check-team d-flex align-items-center justify-content-center">
        <i class="bi bi-search me-1"></i>
        CEK TIM KAMU
    </a>

    <main>
        <div class="content-wrapper">
            @yield('content')
        </div>
    </main>

    @include('components.footer')

    @if(session('success') || session('error') || session('info'))
    <script src="{{ asset('js/sweetalert2.all.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true
            });

            @if(session('success'))
                Toast.fire({
                    icon: 'success',
                    title: @json(session('success'))
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    icon: 'error',
                    title: 'Waduh!',
                    text: @json(session('error')),
                    confirmButtonColor: '#ffc107'
                });
            @endif

            @if(session('info'))
                Swal.fire({
                    icon: 'info',
                    title: 'Pendaftaran Ditemukan',
                    text: @json(session('info')),
                    confirmButtonColor: '#ffc107'
                });
            @endif
        });
    </script>
    @endif

    {{-- FLOATING AI CHAT WIDGET --}}
    <div id="yomuda-ai-chat" class="position-fixed bottom-0 end-0 m-3 m-md-4" style="z-index: 2000; font-family: 'Outfit', sans-serif;">
        <!-- Chat Toggle Button -->
        <button id="ai-chat-toggle" class="btn btn-warning rounded-circle shadow-lg d-flex align-items-center justify-content-center" style="width: 56px; height: 56px; border: 2px solid rgba(255, 255, 255, 0.2); transition: all 0.3s ease;">
            <i class="bi bi-robot fs-3 text-dark" id="ai-icon"></i>
            <i class="bi bi-x-lg fs-4 text-dark d-none" id="ai-close-icon"></i>
        </button>

        <!-- Chat Window -->
        <div id="ai-chat-window" class="card shadow-2xl border-0 rounded-4 d-none" style="width: 380px; height: 500px; position: absolute; bottom: 70px; right: 0; background: rgba(255, 255, 255, 0.98); backdrop-filter: blur(20px); border: 1px solid rgba(226, 232, 240, 0.8) !important; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); transform: scale(0.95); opacity: 0;">
            <!-- Header -->
            <div class="card-header bg-dark text-white rounded-top-4 p-3.5 d-flex align-items-center justify-content-between border-0" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%) !important;">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-warning rounded-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 38px; height: 38px; background: linear-gradient(135deg, #ffc107 0%, #ffca28 100%) !important;">
                        <i class="bi bi-robot text-dark fs-5"></i>
                    </div>
                    <div>
                        <h6 class="fw-bold mb-0 text-white" style="font-size: 0.92rem; letter-spacing: -0.3px;">Yomuda AI Assistant</h6>
                        <span class="text-success small fw-bold d-flex align-items-center gap-1" style="font-size: 0.68rem; opacity: 0.85;">
                            <span class="d-inline-block bg-success rounded-circle" style="width: 6px; height: 6px; animation: pulse 1.5s infinite;"></span> Online 24/7
                        </span>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-1">
                    <button type="button" class="btn btn-link text-white-50 p-1.5 rounded-3 hover-bg-white-10" id="ai-chat-clear-btn" title="Hapus riwayat chat">
                        <i class="bi bi-trash3" style="font-size: 0.9rem;"></i>
                    </button>
                    <button type="button" class="btn-close btn-close-white shadow-none" id="ai-chat-close-btn" style="font-size: 0.8rem;"></button>
                </div>
            </div>
            <!-- Body / Message Area -->
            <div class="card-body p-3.5 overflow-y-auto d-flex flex-column gap-3" id="ai-chat-messages" style="height: 360px; font-size: 0.85rem; background: #fafafb;">
                <!-- Welcome Message -->
                <div class="d-flex gap-2.5 ai-message">
                    <div class="bg-warning rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm" style="width: 30px; height: 30px; background: linear-gradient(135deg, #ffc107 0%, #ffca28 100%) !important;">
                        <i class="bi bi-robot text-dark" style="font-size: 0.8rem;"></i>
                    </div>
                    <div class="bg-white border border-light-subtle p-3 rounded-4 rounded-start-0 text-dark max-w-75 shadow-sm leading-relaxed">
                        Halo Bro/Sist! Saya <strong>Yomuda AI</strong> 🚀<br><br>Ada yang bisa saya bantu terkait jadwal turnamen, biaya pendaftaran, aturan tanding, atau kontak admin?
                        
                        <!-- Quick Prompts inside welcome message -->
                        <div class="d-flex flex-wrap gap-1.5 mt-3" id="ai-quick-prompts">
                            <button type="button" class="btn btn-outline-warning btn-sm text-dark bg-white rounded-pill px-2.5 py-1 fw-bold text-uppercase shadow-xs" style="font-size: 0.68rem; border-color: rgba(255, 193, 7, 0.5);" data-prompt="Info Turnamen Aktif">🏆 Info Turnamen</button>
                            <button type="button" class="btn btn-outline-warning btn-sm text-dark bg-white rounded-pill px-2.5 py-1 fw-bold text-uppercase shadow-xs" style="font-size: 0.68rem; border-color: rgba(255, 193, 7, 0.5);" data-prompt="Aturan Turnamen">📖 Aturan Tanding</button>
                            <button type="button" class="btn btn-outline-warning btn-sm text-dark bg-white rounded-pill px-2.5 py-1 fw-bold text-uppercase shadow-xs" style="font-size: 0.68rem; border-color: rgba(255, 193, 7, 0.5);" data-prompt="Hubungi Admin">📱 Kontak Admin</button>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Footer / Input Form -->
            <div class="card-footer p-2.5 bg-white border-top border-light rounded-bottom-4">
                <form id="ai-chat-form" class="input-group input-group-sm">
                    <input type="text" id="ai-chat-input" class="form-control border-0 bg-light rounded-pill-start ps-3.5 shadow-none text-dark" placeholder="Tulis pertanyaanmu..." style="height: 42px; font-size: 16px !important; outline: none;">
                    <button type="submit" class="btn btn-warning rounded-pill-end px-3.5 shadow-none d-flex align-items-center justify-content-center" style="height: 42px; background: #ffc107; border-color: #ffc107;">
                        <i class="bi bi-send-fill text-dark fs-5" id="ai-send-icon"></i>
                        <span class="spinner-border spinner-border-sm text-dark d-none" id="ai-send-spinner" role="status"></span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <style>
        #ai-chat-toggle {
            position: relative;
            background: linear-gradient(135deg, #ffc107 0%, #ffb300 100%) !important;
            border-color: #ffb300 !important;
        }
        #ai-chat-toggle:hover {
            transform: scale(1.1) rotate(5deg);
        }
        #ai-chat-toggle::after {
            content: '';
            position: absolute;
            width: 100%;
            height: 100%;
            border-radius: 50%;
            border: 4px solid #ffc107;
            animation: ai-pulse 2s infinite;
            opacity: 0;
            top: 0;
            left: 0;
            pointer-events: none;
        }
        @keyframes ai-pulse {
            0% {
                transform: scale(0.95);
                opacity: 0.8;
            }
            100% {
                transform: scale(1.4);
                opacity: 0;
            }
        }
        #ai-chat-messages::-webkit-scrollbar {
            width: 4px;
        }
        #ai-chat-messages::-webkit-scrollbar-thumb {
            background-color: rgba(0,0,0,0.08);
            border-radius: 4px;
        }
        .max-w-75 {
            max-width: 80%;
        }
        #ai-chat-input {
            font-size: 16px !important;
        }
        #ai-chat-input:focus {
            background: #fff !important;
            border: 1px solid rgba(255, 193, 7, 0.4) !important;
        }
        .hover-bg-white-10:hover {
            background: rgba(255, 255, 255, 0.1);
        }
        .shadow-xs {
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }
        @media (max-width: 575.98px) {
            #ai-chat-window {
                width: calc(100vw - 32px) !important;
                right: 0 !important;
                left: auto !important;
                bottom: 70px !important;
                height: 480px !important;
            }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggleBtn = document.getElementById('ai-chat-toggle');
            const closeBtn = document.getElementById('ai-chat-close-btn');
            const clearBtn = document.getElementById('ai-chat-clear-btn');
            const chatWindow = document.getElementById('ai-chat-window');
            const chatMessages = document.getElementById('ai-chat-messages');
            const chatForm = document.getElementById('ai-chat-form');
            const chatInput = document.getElementById('ai-chat-input');
            const aiIcon = document.getElementById('ai-icon');
            const aiCloseIcon = document.getElementById('ai-close-icon');
            const sendIcon = document.getElementById('ai-send-icon');
            const sendSpinner = document.getElementById('ai-send-spinner');

            function toggleChat() {
                if (chatWindow.classList.contains('d-none')) {
                    chatWindow.classList.remove('d-none');
                    setTimeout(() => {
                        chatWindow.style.opacity = '1';
                        chatWindow.style.transform = 'scale(1)';
                    }, 50);
                    aiIcon.classList.add('d-none');
                    aiCloseIcon.classList.remove('d-none');
                    chatInput.focus();
                } else {
                    chatWindow.style.opacity = '0';
                    chatWindow.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        chatWindow.classList.add('d-none');
                    }, 250);
                    aiIcon.classList.remove('d-none');
                    aiCloseIcon.classList.add('d-none');
                }
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }

            if (toggleBtn && closeBtn && chatWindow) {
                toggleBtn.addEventListener('click', toggleChat);
                closeBtn.addEventListener('click', toggleChat);
            }

            function attachPromptHandlers() {
                const promptBtns = document.querySelectorAll('#ai-quick-prompts button');
                promptBtns.forEach(btn => {
                    btn.addEventListener('click', function () {
                        chatInput.value = btn.getAttribute('data-prompt');
                        chatForm.dispatchEvent(new Event('submit'));
                    });
                });
            }

            if (clearBtn) {
                clearBtn.addEventListener('click', function () {
                    if (confirm('Hapus seluruh riwayat chat dengan Yomuda AI?')) {
                        chatMessages.innerHTML = `
                            <div class="d-flex gap-2.5 ai-message">
                                <div class="bg-warning rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm" style="width: 30px; height: 30px; background: linear-gradient(135deg, #ffc107 0%, #ffca28 100%) !important;">
                                    <i class="bi bi-robot text-dark" style="font-size: 0.8rem;"></i>
                                </div>
                                <div class="bg-white border border-light-subtle p-3 rounded-4 rounded-start-0 text-dark max-w-75 shadow-sm leading-relaxed">
                                    Riwayat chat telah di-reset 🔄<br><br>Ada yang bisa saya bantu lagi Bro/Sist?
                                    <div class="d-flex flex-wrap gap-1.5 mt-3" id="ai-quick-prompts">
                                        <button type="button" class="btn btn-outline-warning btn-sm text-dark bg-white rounded-pill px-2.5 py-1 fw-bold text-uppercase shadow-xs" style="font-size: 0.68rem; border-color: rgba(255, 193, 7, 0.5);" data-prompt="Info Turnamen Aktif">🏆 Info Turnamen</button>
                                        <button type="button" class="btn btn-outline-warning btn-sm text-dark bg-white rounded-pill px-2.5 py-1 fw-bold text-uppercase shadow-xs" style="font-size: 0.68rem; border-color: rgba(255, 193, 7, 0.5);" data-prompt="Aturan Turnamen">📖 Aturan Tanding</button>
                                        <button type="button" class="btn btn-outline-warning btn-sm text-dark bg-white rounded-pill px-2.5 py-1 fw-bold text-uppercase shadow-xs" style="font-size: 0.68rem; border-color: rgba(255, 193, 7, 0.5);" data-prompt="Hubungi Admin">📱 Kontak Admin</button>
                                    </div>
                                </div>
                            </div>
                        `;
                        attachPromptHandlers();
                    }
                });
            }

            attachPromptHandlers();

            if (chatForm) {
                chatForm.addEventListener('submit', function (e) {
                    e.preventDefault();
                    const text = chatInput.value.trim();
                    if (!text) return;

                    chatInput.value = '';
                    
                    const qp = document.getElementById('ai-quick-prompts');
                    if (qp) qp.classList.add('d-none');

                    if (sendIcon && sendSpinner) {
                        sendIcon.classList.add('d-none');
                        sendSpinner.classList.remove('d-none');
                    }

                    const userMsg = document.createElement('div');
                    userMsg.className = 'd-flex justify-content-end mb-1';
                    userMsg.innerHTML = `
                        <div class="bg-warning bg-opacity-25 border border-warning border-opacity-10 p-3 rounded-4 rounded-end-0 text-dark max-w-75 shadow-xs leading-relaxed">
                            ${text}
                        </div>
                    `;
                    chatMessages.appendChild(userMsg);
                    chatMessages.scrollTop = chatMessages.scrollHeight;

                    const loader = document.createElement('div');
                    loader.className = 'd-flex gap-2 align-items-center text-muted mb-1';
                    loader.id = 'ai-typing-indicator';
                    loader.innerHTML = `
                        <div class="bg-warning rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm" style="width: 30px; height: 30px; background: linear-gradient(135deg, #ffc107 0%, #ffca28 100%) !important;">
                            <i class="bi bi-robot text-dark" style="font-size: 0.8rem;"></i>
                        </div>
                        <div class="spinner-border spinner-border-sm text-warning" role="status"></div>
                        <span class="small italic" style="font-size: 0.75rem;">Yomuda AI sedang mengetik...</span>
                    `;
                    chatMessages.appendChild(loader);
                    chatMessages.scrollTop = chatMessages.scrollHeight;

                    fetch("{{ route('ai.chat') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ message: text })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (sendIcon && sendSpinner) {
                            sendIcon.classList.remove('d-none');
                            sendSpinner.classList.add('d-none');
                        }

                        const ind = document.getElementById('ai-typing-indicator');
                        if (ind) ind.remove();

                        const aiReply = document.createElement('div');
                        aiReply.className = 'd-flex gap-2.5 mb-1';
                        
                        let formattedText = data.reply || 'Maaf, saya tidak mengerti.';
                        // Convert bold markdown **text** to <strong>text</strong>
                        formattedText = formattedText.replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>');
                        // Convert bullet list * to bullet points •
                        formattedText = formattedText.replace(/^\s*\*\s+(.+)$/gm, '• $1');
                        // Convert Markdown links [text](url) to HTML anchors
                        formattedText = formattedText.replace(/\[([^\]]+)\]\((https?:\/\/[^\s)]+)\)/g, '<a href="$2" target="_blank" class="text-primary fw-semibold">$1</a>');
                        // Auto-link remaining raw URLs without double-linking existing anchor tags
                        formattedText = formattedText.replace(/(<a[^>]*>.*?<\/a>)|(https?:\/\/[^\s<]+)/g, (match, group1, group2) => {
                            if (group1) return group1; // Return existing HTML anchor tags unchanged
                            return `<a href="${group2}" target="_blank" class="text-primary fw-semibold">${group2}</a>`;
                        });

                        aiReply.innerHTML = `
                            <div class="bg-warning rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm" style="width: 30px; height: 30px; background: linear-gradient(135deg, #ffc107 0%, #ffca28 100%) !important;">
                                <i class="bi bi-robot text-dark" style="font-size: 0.8rem;"></i>
                            </div>
                            <div class="bg-white border border-light-subtle p-3 rounded-4 rounded-start-0 text-dark max-w-75 shadow-sm leading-relaxed" style="white-space: pre-line;">
                                ${formattedText}
                            </div>
                        `;
                        chatMessages.appendChild(aiReply);
                        chatMessages.scrollTop = chatMessages.scrollHeight;
                    })
                    .catch(err => {
                        if (sendIcon && sendSpinner) {
                            sendIcon.classList.remove('d-none');
                            sendSpinner.classList.add('d-none');
                        }

                        const ind = document.getElementById('ai-typing-indicator');
                        if (ind) ind.remove();

                        const aiReply = document.createElement('div');
                        aiReply.className = 'd-flex gap-2.5 mb-1';
                        aiReply.innerHTML = `
                            <div class="bg-warning rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 shadow-sm" style="width: 30px; height: 30px; background: linear-gradient(135deg, #ffc107 0%, #ffca28 100%) !important;">
                                <i class="bi bi-robot text-dark" style="font-size: 0.8rem;"></i>
                            </div>
                            <div class="bg-white border border-light-subtle p-3 rounded-4 rounded-start-0 text-dark max-w-75 shadow-sm leading-relaxed">
                                Koneksi terputus. Silakan tanyakan langsung ke admin WhatsApp di <a href="https://wa.me/{{ \App\Models\Setting::getVal('admin_wa', '0851-2261-6191') }}" target="_blank" class="text-primary fw-semibold">WhatsApp Admin</a>.
                            </div>
                        `;
                        chatMessages.appendChild(aiReply);
                        chatMessages.scrollTop = chatMessages.scrollHeight;
                    });
                });
            }
        });
    </script>

    @stack('scripts')
</body>
</html>