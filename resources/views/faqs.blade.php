@extends('layouts.app')

@section('title', 'FAQ - Yomuda Championship')

@push('styles')
<style>
    :root {
        --ymd-yellow: #ffc107;
        --ymd-dark: #0e0f11;
        --ymd-card: #141618;
        --ymd-border: rgba(255, 255, 255, 0.06);
    }

    /* ───── PAGE WRAPPER ───── */
    .faq-page {
        min-height: 100vh;
        padding: 60px 0 100px;
    }

    /* ───── HERO HEADER ───── */
    .faq-hero {
        text-align: center;
        padding: 20px 20px 60px;
        position: relative;
    }

    .faq-hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(255, 193, 7, 0.08);
        border: 1px solid rgba(255, 193, 7, 0.22);
        border-radius: 50px;
        padding: 7px 20px;
        font-size: 0.72rem;
        font-weight: 800;
        letter-spacing: 2px;
        text-transform: uppercase;
        color: var(--ymd-yellow);
        margin-bottom: 22px;
    }

    .faq-hero-badge span {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: var(--ymd-yellow);
        animation: pulse-dot 1.8s ease infinite;
    }

    @keyframes pulse-dot {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.4; transform: scale(0.7); }
    }

    .faq-hero h1 {
        font-size: clamp(2rem, 5vw, 3.4rem);
        font-weight: 900;
        letter-spacing: -2px;
        line-height: 1.1;
        margin-bottom: 16px;
        background: linear-gradient(160deg, #ffffff 40%, #ffc107 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .faq-hero p {
        max-width: 520px;
        margin: 0 auto 36px;
        color: rgba(255, 255, 255, 0.5);
        font-size: 1rem;
        line-height: 1.8;
    }

    /* ───── SEARCH BAR ───── */
    .faq-search-wrapper {
        max-width: 520px;
        margin: 0 auto;
        position: relative;
    }

    .faq-search-wrapper i {
        position: absolute;
        left: 20px;
        top: 50%;
        transform: translateY(-50%);
        color: rgba(255, 255, 255, 0.3);
        font-size: 1rem;
        pointer-events: none;
        transition: color 0.3s;
    }

    .faq-search-wrapper:focus-within i {
        color: var(--ymd-yellow);
    }

    #faq-search {
        width: 100%;
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 16px;
        padding: 16px 20px 16px 50px;
        color: #fff;
        font-size: 0.95rem;
        outline: none;
        transition: border-color 0.3s, box-shadow 0.3s, background 0.3s;
    }

    #faq-search::placeholder {
        color: rgba(255, 255, 255, 0.28);
    }

    #faq-search:focus {
        border-color: rgba(255, 193, 7, 0.5);
        background: rgba(255, 193, 7, 0.04);
        box-shadow: 0 0 0 4px rgba(255, 193, 7, 0.07);
    }

    /* ───── CATEGORY TABS ───── */
    .faq-tabs-wrapper {
        display: flex;
        justify-content: center;
        gap: 10px;
        flex-wrap: wrap;
        margin-bottom: 32px;
    }

    .faq-tab-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 22px;
        border-radius: 50px;
        border: 1px solid rgba(255, 255, 255, 0.08);
        background: rgba(255, 255, 255, 0.03);
        color: rgba(255, 255, 255, 0.5);
        font-size: 0.8rem;
        font-weight: 700;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        cursor: pointer;
        transition: all 0.25s ease;
    }

    .faq-tab-btn .tab-count {
        background: rgba(255, 255, 255, 0.08);
        border-radius: 20px;
        padding: 1px 8px;
        font-size: 0.7rem;
        font-weight: 800;
        transition: all 0.25s ease;
    }

    .faq-tab-btn:hover {
        border-color: rgba(255, 193, 7, 0.3);
        color: #fff;
    }

    .faq-tab-btn.active {
        background: var(--ymd-yellow);
        border-color: var(--ymd-yellow);
        color: #000;
        box-shadow: 0 6px 24px rgba(255, 193, 7, 0.28);
    }

    .faq-tab-btn.active .tab-count {
        background: rgba(0, 0, 0, 0.15);
        color: #000;
    }

    /* ───── ACCORDION ───── */
    .faq-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .faq-item {
        background: var(--ymd-card);
        border: 1px solid var(--ymd-border);
        border-radius: 18px;
        overflow: hidden;
        transition: border-color 0.25s, box-shadow 0.25s;
    }

    .faq-item:hover {
        border-color: rgba(255, 193, 7, 0.25);
    }

    .faq-item.active-item {
        border-color: rgba(255, 193, 7, 0.45);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
    }

    .faq-question {
        width: 100%;
        background: transparent;
        border: none;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 22px 26px;
        cursor: pointer;
        text-align: left;
        transition: background 0.2s;
    }

    .faq-question:hover {
        background: rgba(255, 255, 255, 0.02);
    }

    .faq-question-text {
        font-weight: 700;
        font-size: 0.97rem;
        color: #fff;
        line-height: 1.5;
        transition: color 0.25s;
    }

    .faq-item.active-item .faq-question-text {
        color: var(--ymd-yellow);
    }

    .faq-chevron {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.08);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: all 0.3s ease;
    }

    .faq-chevron i {
        font-size: 0.75rem;
        color: rgba(255, 255, 255, 0.5);
        transition: transform 0.3s ease, color 0.3s ease;
    }

    .faq-item.active-item .faq-chevron {
        background: rgba(255, 193, 7, 0.12);
        border-color: rgba(255, 193, 7, 0.3);
    }

    .faq-item.active-item .faq-chevron i {
        transform: rotate(180deg);
        color: var(--ymd-yellow);
    }

    .faq-answer-wrapper {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .faq-answer {
        padding: 0 26px 24px;
        color: rgba(255, 255, 255, 0.62);
        font-size: 0.92rem;
        line-height: 1.85;
        border-top: 1px solid var(--ymd-border);
        padding-top: 18px;
    }

    /* ───── EMPTY / NO RESULTS ───── */
    .faq-empty {
        text-align: center;
        padding: 60px 20px;
        display: none;
    }

    .faq-empty-icon {
        width: 64px;
        height: 64px;
        margin: 0 auto 18px;
        border-radius: 20px;
        background: rgba(255, 193, 7, 0.08);
        border: 1px solid rgba(255, 193, 7, 0.15);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
    }

    .faq-empty h5 {
        color: #fff;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .faq-empty p {
        color: rgba(255, 255, 255, 0.4);
        font-size: 0.9rem;
        margin: 0;
    }

    /* ───── CTA FOOTER CARD ───── */
    .faq-cta {
        margin-top: 40px;
        border-radius: 24px;
        border: 1px solid rgba(255, 193, 7, 0.15);
        background: linear-gradient(135deg, rgba(255, 193, 7, 0.05) 0%, rgba(14, 15, 17, 0.8) 100%);
        padding: 36px 40px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 24px;
        flex-wrap: wrap;
    }

    .faq-cta-text h5 {
        font-weight: 800;
        font-size: 1.1rem;
        color: #fff;
        margin-bottom: 6px;
    }

    .faq-cta-text p {
        color: rgba(255, 255, 255, 0.5);
        font-size: 0.88rem;
        margin: 0;
    }

    .faq-cta-btn {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: #25D366;
        color: #fff;
        font-weight: 800;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        padding: 13px 26px;
        border-radius: 14px;
        text-decoration: none;
        white-space: nowrap;
        transition: all 0.25s ease;
        box-shadow: 0 6px 20px rgba(37, 211, 102, 0.2);
    }

    .faq-cta-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(37, 211, 102, 0.35);
        color: #fff;
    }

    /* ───── BACK LINK ───── */
    .faq-back {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: rgba(255, 255, 255, 0.4);
        font-size: 0.85rem;
        font-weight: 600;
        text-decoration: none;
        margin-bottom: 40px;
        transition: color 0.25s;
    }

    .faq-back:hover {
        color: var(--ymd-yellow);
    }

    /* ───── SEARCH HIGHLIGHT ───── */
    .faq-highlight {
        background: rgba(255, 193, 7, 0.25);
        color: var(--ymd-yellow);
        border-radius: 3px;
        padding: 0 2px;
    }

    /* ───── RESPONSIVE ───── */
    @media (max-width: 768px) {
        .faq-page { padding: 30px 0 80px; }
        .faq-hero { padding: 10px 16px 40px; }
        .faq-hero h1 { letter-spacing: -1px; }
        .faq-cta { flex-direction: column; text-align: center; padding: 28px 24px; }
        .faq-cta-btn { width: 100%; justify-content: center; }
        .faq-question { padding: 18px 20px; }
        .faq-answer { padding: 0 20px 20px; padding-top: 16px; }
    }
</style>
@endpush

@section('content')
<div class="faq-page">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-9 col-xl-8">

                <a href="{{ route('home') }}" class="faq-back">
                    <i class="bi bi-arrow-left"></i> Kembali ke Beranda
                </a>

                <div class="faq-hero">
                    <div class="faq-hero-badge">
                        <span></span> Pusat Bantuan
                    </div>
                    <h1>Ada Pertanyaan?</h1>
                    <p>Temukan semua jawaban seputar pendaftaran, turnamen, dan pembayaran di Yomuda Championship.</p>

                    <div class="faq-search-wrapper">
                        <i class="bi bi-search"></i>
                        <input type="text" id="faq-search" placeholder="Cari pertanyaan, misal: cara daftar, refund...">
                    </div>
                </div>

                @if(isset($faqs) && count($faqs) > 0)
                    @php
                        $tournamentFaqs = [];
                        $paymentFaqs = [];
                        foreach($faqs as $faq) {
                            $q = strtolower($faq->question);
                            if (str_contains($q, 'bayar') || str_contains($q, 'refund') || str_contains($q, 'dana') || str_contains($q, 'transaksi') || str_contains($q, 'biaya')) {
                                $paymentFaqs[] = $faq;
                            } else {
                                $tournamentFaqs[] = $faq;
                            }
                        }
                    @endphp

                    <div class="faq-tabs-wrapper" id="faq-tabs">
                        <button class="faq-tab-btn active" data-tab="all">
                            ✦ Semua
                            <span class="tab-count">{{ count($faqs) }}</span>
                        </button>
                        <button class="faq-tab-btn" data-tab="tournament">
                            🏆 Turnamen & Main
                            <span class="tab-count">{{ count($tournamentFaqs) }}</span>
                        </button>
                        <button class="faq-tab-btn" data-tab="payment">
                            💳 Pembayaran & Refund
                            <span class="tab-count">{{ count($paymentFaqs) }}</span>
                        </button>
                    </div>

                    <div class="faq-list" id="faq-list">

                        @foreach($tournamentFaqs as $faq)
                        <div class="faq-item" data-tab="tournament" data-id="{{ $faq->id }}">
                            <button class="faq-question" onclick="toggleFaq(this)">
                                <span class="faq-question-text">{{ $faq->question }}</span>
                                <span class="faq-chevron"><i class="bi bi-chevron-down"></i></span>
                            </button>
                            <div class="faq-answer-wrapper">
                                <div class="faq-answer">{!! nl2br(e($faq->answer)) !!}</div>
                            </div>
                        </div>
                        @endforeach

                        @foreach($paymentFaqs as $faq)
                        <div class="faq-item" data-tab="payment" data-id="{{ $faq->id }}">
                            <button class="faq-question" onclick="toggleFaq(this)">
                                <span class="faq-question-text">{{ $faq->question }}</span>
                                <span class="faq-chevron"><i class="bi bi-chevron-down"></i></span>
                            </button>
                            <div class="faq-answer-wrapper">
                                <div class="faq-answer">{!! nl2br(e($faq->answer)) !!}</div>
                            </div>
                        </div>
                        @endforeach

                        {{-- JS search index: safe JSON encoding --}}
                        <script id="faq-data-json" type="application/json">
                        [
                            @foreach($faqs as $faq)
                            {
                                "id": {{ $faq->id }},
                                "tab": "{{ (str_contains(strtolower($faq->question), 'bayar') || str_contains(strtolower($faq->question), 'refund') || str_contains(strtolower($faq->question), 'dana') || str_contains(strtolower($faq->question), 'transaksi') || str_contains(strtolower($faq->question), 'biaya')) ? 'payment' : 'tournament' }}",
                                "q": {{ json_encode(strtolower($faq->question)) }},
                                "a": {{ json_encode(strtolower(strip_tags($faq->answer))) }}
                            }{{ !$loop->last ? ',' : '' }}
                            @endforeach
                        ]
                        </script>

                    </div>

                    <div class="faq-empty" id="faq-empty">
                        <div class="faq-empty-icon">🔍</div>
                        <h5>Tidak Ditemukan</h5>
                        <p>Tidak ada FAQ yang cocok dengan pencarianmu.<br>Coba kata kunci lain atau hubungi admin langsung.</p>
                    </div>

                @else
                    <div class="text-center py-5 text-secondary">
                        <i class="bi bi-question-circle fs-1 text-muted mb-3 d-block"></i>
                        Belum ada pertanyaan umum yang diaktifkan saat ini.
                    </div>
                @endif

                <div class="faq-cta">
                    <div class="faq-cta-text">
                        <h5>Pertanyaanmu belum terjawab?</h5>
                        <p>Tim support kami siap membantu kamu setiap hari pukul 09.00–22.00 WIB.</p>
                    </div>
                    <a href="https://wa.me/6285122616191" target="_blank" rel="noopener" class="faq-cta-btn">
                        <i class="bi bi-whatsapp"></i> Chat Admin
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Build search index from safe JSON blob
    const faqDataRaw = document.getElementById('faq-data-json');
    const faqIndex = faqDataRaw ? JSON.parse(faqDataRaw.textContent) : [];
    // Map id -> search text
    const faqSearchMap = {};
    faqIndex.forEach(f => { faqSearchMap[f.id] = { tab: f.tab, text: f.q + ' ' + f.a }; });

    // Store original question text per element to safely restore after highlight
    const originalTexts = new Map();
    document.querySelectorAll('.faq-question-text').forEach(el => {
        originalTexts.set(el, el.textContent);
    });

    function toggleFaq(btn) {
        const item = btn.closest('.faq-item');
        const wrapper = item.querySelector('.faq-answer-wrapper');
        const answer = item.querySelector('.faq-answer');
        const isOpen = item.classList.contains('active-item');

        document.querySelectorAll('.faq-item.active-item').forEach(openItem => {
            openItem.classList.remove('active-item');
            openItem.querySelector('.faq-answer-wrapper').style.maxHeight = '0';
        });

        if (!isOpen) {
            item.classList.add('active-item');
            wrapper.style.maxHeight = (answer.scrollHeight + 32) + 'px';
        }
    }

    const tabBtns = document.querySelectorAll('.faq-tab-btn');
    const faqItems = document.querySelectorAll('.faq-item');
    const emptyEl = document.getElementById('faq-empty');
    let activeTab = 'all';

    tabBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            tabBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            activeTab = btn.dataset.tab;
            filterFaqs();
        });
    });

    document.getElementById('faq-search').addEventListener('input', filterFaqs);

    function escapeRegex(str) {
        return str.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
    }

    function filterFaqs() {
        const query = document.getElementById('faq-search').value.trim().toLowerCase();
        let visibleCount = 0;

        faqItems.forEach(item => {
            const id = parseInt(item.dataset.id);
            const entry = faqSearchMap[id];
            if (!entry) return;

            const matchesTab = activeTab === 'all' || entry.tab === activeTab;
            const matchesSearch = !query || entry.text.includes(query);

            if (matchesTab && matchesSearch) {
                item.style.display = '';
                visibleCount++;

                // Safe highlight: restore original text first, then wrap matches
                const textEl = item.querySelector('.faq-question-text');
                const original = originalTexts.get(textEl);
                if (query && original) {
                    const regex = new RegExp('(' + escapeRegex(query) + ')', 'gi');
                    // Build highlighted text using DOM, not innerHTML string concat
                    const parts = original.split(regex);
                    textEl.textContent = '';
                    parts.forEach(part => {
                        if (regex.test(part)) {
                            const mark = document.createElement('mark');
                            mark.className = 'faq-highlight';
                            mark.textContent = part;
                            textEl.appendChild(mark);
                        } else if (part) {
                            textEl.appendChild(document.createTextNode(part));
                        }
                        regex.lastIndex = 0;
                    });
                } else if (original) {
                    textEl.textContent = original;
                }
            } else {
                item.style.display = 'none';
                item.classList.remove('active-item');
                item.querySelector('.faq-answer-wrapper').style.maxHeight = '0';
            }
        });

        emptyEl.style.display = visibleCount === 0 ? 'block' : 'none';
    }
</script>
@endpush
