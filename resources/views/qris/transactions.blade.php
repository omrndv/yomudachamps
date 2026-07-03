@extends('qris.layout')
@section('title', 'Daftar Transaksi')

@section('content')
<div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm border-collapse">
            <thead>
                <tr class="bg-slate-50 dark:bg-slate-800/40 border-b border-slate-200 dark:border-slate-800 text-slate-450 dark:text-slate-400 font-bold uppercase text-[10px] tracking-wider">
                    <th class="py-4 px-6">ID / Referensi</th>
                    <th class="py-4 px-6">Nama Tim</th>
                    <th class="py-4 px-6">Nominal</th>
                    <th class="py-4 px-6">Batas Pembayaran</th>
                    <th class="py-4 px-6">Status</th>
                    <th class="py-4 px-6 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800" id="transactions-table-body">
                @forelse($transactions as $tx)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-850/50 transition-all cursor-pointer" onclick="openDetailDrawer({{ json_encode($tx) }}, '{{ $tx->team->name ?? 'Tim Terhapus' }}', '{{ $tx->team->email ?? '-' }}', '{{ $tx->team->phone ?? '-' }}', '{{ $tx->created_at->setTimezone('Asia/Jakarta')->format('d M Y, H:i:s') }}', '{{ $tx->expires_at->setTimezone('Asia/Jakarta')->format('d M Y, H:i:s') }}', '{{ $tx->paid_at ? $tx->paid_at->setTimezone('Asia/Jakarta')->format('d M Y, H:i:s') : '-' }}', '{{ $tx->team->season->name ?? '-' }}')">
                        <td class="py-4 px-6 font-mono text-xs text-sky-600 dark:text-sky-400 font-bold">{{ $tx->trx_id }}</td>
                        <td class="py-4 px-6">
                            <div class="font-bold text-slate-900 dark:text-white">{{ $tx->team->name ?? 'Tim Terhapus' }}</div>
                            <span class="text-[10px] text-slate-400 dark:text-slate-555 mt-1.5 block">Season: {{ $tx->team->season->name ?? '-' }}</span>
                        </td>
                        <td class="py-4 px-6">
                            <div class="font-bold text-slate-900 dark:text-white">Rp {{ number_format($tx->amount, 0, ',', '.') }}</div>
                            <span class="text-[10px] text-slate-400 dark:text-slate-555 mt-1.5 block">Kode Unik: +{{ $tx->unique_code }}</span>
                        </td>
                        <td class="py-4 px-6 text-xs font-semibold">
                            @if($tx->status === 'PENDING')
                                <!-- Countdown Timer -->
                                <span class="text-blue-600 dark:text-blue-400 flex items-center gap-1 countdown-timer" data-expires="{{ $tx->expires_at->timestamp }}">
                                    <i data-lucide="clock" class="w-3.5 h-3.5 shrink-0"></i> Menghitung...
                                </span>
                            @else
                                <span class="text-slate-400 dark:text-slate-500">
                                    {{ $tx->expires_at->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }}
                                </span>
                            @endif
                        </td>
                        <td class="py-4 px-6">
                            @if($tx->status === 'PAID')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-500/20">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> PAID
                                </span>
                            @elseif($tx->status === 'CLAIMED')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-blue-50 dark:bg-blue-500/10 text-blue-700 dark:text-blue-400 border border-blue-100 dark:border-blue-500/20 animate-pulse">
                                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span> CLAIMED
                                </span>
                            @elseif($tx->status === 'PENDING')
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-yellow-50 dark:bg-yellow-500/10 text-yellow-700 dark:text-yellow-450 border border-yellow-100 dark:border-yellow-500/20">
                                    <span class="w-1.5 h-1.5 rounded-full bg-yellow-500 animate-pulse"></span> PENDING
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-450 border border-slate-200 dark:border-slate-700">
                                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> EXPIRED
                                </span>
                            @endif
                        </td>
                        <td class="py-4 px-6 text-right space-x-2" onclick="event.stopPropagation()">
                            @if($tx->status === 'PENDING' || $tx->status === 'CLAIMED')
                                <form action="{{ route('qris.settle', $tx->trx_id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menyelesaikan transaksi ini secara manual?');" class="inline-block">
                                    @csrf
                                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold px-3 py-2 rounded-xl transition-all shadow-sm active:scale-[0.98]">
                                        Settle Manual
                                    </button>
                                </form>
                            @endif

                            <!-- Delete Button -->
                            <form action="{{ route('qris.delete', $tx->trx_id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus transaksi ini dari database?');" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-50 text-red-650 hover:bg-red-500 hover:text-white dark:bg-red-950/20 dark:text-red-400 dark:hover:bg-red-650 text-xs font-bold px-3 py-2 rounded-xl transition-all shadow-sm active:scale-[0.98]">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-slate-400 text-sm">
                            Belum ada transaksi QRIS yang tercatat.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-4">
    {{ $transactions->links() }}
</div>

<!-- PAYMENT DETAIL DRAWER (SIDE SLIDE-OVER) -->
<div id="detail-drawer" class="fixed inset-y-0 right-0 w-full sm:w-[600px] bg-[#f8fafc] dark:bg-slate-950 border-l border-slate-200 dark:border-slate-850 shadow-2xl z-50 transform translate-x-full transition-transform duration-300 ease-in-out flex flex-col">
    <!-- Drawer Header -->
    <div class="h-20 bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between px-6 shrink-0">
        <div class="flex items-center gap-2 text-slate-800 dark:text-white">
            <button onclick="closeDetailDrawer()" class="w-8 h-8 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800 flex items-center justify-center">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </button>
            <h3 class="text-md font-extrabold">Detail Transaksi</h3>
        </div>
        <div id="drawer-header-actions">
            <!-- Action Form Button -->
        </div>
    </div>

    <!-- Drawer Content -->
    <div class="flex-1 overflow-y-auto p-6 space-y-6 custom-scroll">
        
        <!-- Summary Card -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-6 shadow-sm grid grid-cols-2 sm:grid-cols-4 gap-6">
            <div>
                <span class="text-[10px] text-slate-400 dark:text-slate-555 font-bold uppercase tracking-wider block">No Referensi</span>
                <div id="drawer-summary-id" class="text-xs font-mono font-bold text-slate-800 dark:text-slate-205 mt-1.5 break-all">-</div>
            </div>
            <div>
                <span class="text-[10px] text-slate-400 dark:text-slate-555 font-bold uppercase tracking-wider block">Jumlah Bayar</span>
                <div id="drawer-summary-amount" class="text-sm font-black text-slate-900 dark:text-white mt-1.5">-</div>
            </div>
            <div>
                <span class="text-[10px] text-slate-400 dark:text-slate-555 font-bold uppercase tracking-wider block">Status</span>
                <div id="drawer-summary-status" class="mt-1.5">
                    <!-- Status Badge -->
                </div>
            </div>
            <div>
                <span class="text-[10px] text-slate-400 dark:text-slate-555 font-bold uppercase tracking-wider block">Channel</span>
                <div class="text-xs font-bold text-slate-800 dark:text-slate-200 mt-1.5">QRIS</div>
            </div>
        </div>

        <!-- Detail Pembayaran Table -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm">
            <h4 class="text-xs font-black text-slate-950 dark:text-white uppercase tracking-wider mb-4">Detail Pembayaran</h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-4 gap-x-6 text-xs">
                <div class="flex justify-between py-2 border-b border-slate-100 dark:border-slate-800">
                    <span class="text-slate-400 font-semibold">Nama Merchant</span>
                    <span class="font-bold text-slate-850 dark:text-slate-200">Yomuda Championship</span>
                </div>
                <div class="flex justify-between py-2 border-b border-slate-100 dark:border-slate-800">
                    <span class="text-slate-400 font-semibold">Jumlah Dibayar</span>
                    <span id="drawer-det-amount" class="font-bold text-slate-850 dark:text-slate-200">-</span>
                </div>
                <div class="flex justify-between py-2 border-b border-slate-100 dark:border-slate-800">
                    <span class="text-slate-400 font-semibold">No. Ref. Merchant</span>
                    <span id="drawer-det-ref" class="font-bold text-slate-850 dark:text-slate-200 font-mono">-</span>
                </div>
                <div class="flex justify-between py-2 border-b border-slate-100 dark:border-slate-800">
                    <span class="text-slate-400 font-semibold">Biaya Merchant</span>
                    <span class="font-bold text-slate-850 dark:text-slate-200">Rp 0</span>
                </div>
                <div class="flex justify-between py-2 border-b border-slate-100 dark:border-slate-800">
                    <span class="text-slate-400 font-semibold">Tanggal Request</span>
                    <span id="drawer-det-created" class="font-bold text-slate-850 dark:text-slate-200">-</span>
                </div>
                <div class="flex justify-between py-2 border-b border-slate-100 dark:border-slate-800">
                    <span class="text-slate-400 font-semibold">Biaya Pelanggan</span>
                    <span id="drawer-det-customer-fee" class="font-bold text-slate-850 dark:text-slate-200">-</span>
                </div>
                <div class="flex justify-between py-2 border-b border-slate-100 dark:border-slate-800">
                    <span class="text-slate-400 font-semibold">Batas Pembayaran</span>
                    <span id="drawer-det-expires" class="font-bold text-slate-850 dark:text-slate-200">-</span>
                </div>
                <div class="flex justify-between py-2 border-b border-slate-100 dark:border-slate-800">
                    <span class="text-slate-400 font-semibold">Total Biaya</span>
                    <span id="drawer-det-total" class="font-bold text-slate-850 dark:text-slate-200">-</span>
                </div>
                <div class="flex justify-between py-2 border-b border-slate-100 dark:border-slate-800">
                    <span class="text-slate-400 font-semibold">Dibayar Pada</span>
                    <span id="drawer-det-paid" class="font-bold text-slate-850 dark:text-slate-200">-</span>
                </div>
                <div class="flex justify-between py-2 border-b border-slate-100 dark:border-slate-800">
                    <span class="text-slate-400 font-semibold">Status Kliring</span>
                    <span id="drawer-det-clearing" class="font-bold">-</span>
                </div>
            </div>
        </div>

        <!-- Informasi Pelanggan -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm">
            <h4 class="text-xs font-black text-slate-950 dark:text-white uppercase tracking-wider mb-4">Informasi Pelanggan</h4>
            <div class="space-y-3 text-xs">
                <div class="grid grid-cols-3 py-2 border-b border-slate-100 dark:border-slate-800">
                    <span class="text-slate-400 font-semibold col-span-1">Nama</span>
                    <span id="drawer-cust-name" class="font-bold text-slate-850 dark:text-slate-200 col-span-2">-</span>
                </div>
                <div class="grid grid-cols-3 py-2 border-b border-slate-100 dark:border-slate-800">
                    <span class="text-slate-400 font-semibold col-span-1">Email</span>
                    <span id="drawer-cust-email" class="font-bold text-slate-850 dark:text-slate-200 col-span-2">-</span>
                </div>
                <div class="grid grid-cols-3 py-2">
                    <span class="text-slate-400 font-semibold col-span-1">Telepon</span>
                    <span id="drawer-cust-phone" class="font-bold text-slate-850 dark:text-slate-200 col-span-2">-</span>
                </div>
            </div>
        </div>

        <!-- Detail Pesanan -->
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 shadow-sm overflow-hidden">
            <h4 class="text-xs font-black text-slate-950 dark:text-white uppercase tracking-wider mb-4">Detail Pesanan</h4>
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/40 border-b border-slate-200 dark:border-slate-800 text-slate-450 dark:text-slate-400 font-bold">
                        <th class="py-2.5 px-4">Nama Produk</th>
                        <th class="py-2.5 px-4 text-center">Jumlah</th>
                        <th class="py-2.5 px-4 text-right">Harga</th>
                        <th class="py-2.5 px-4 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    <tr>
                        <td id="drawer-order-product" class="py-3 px-4 font-bold text-slate-850 dark:text-slate-200">-</td>
                        <td class="py-3 px-4 text-center font-semibold text-slate-800 dark:text-slate-200">1</td>
                        <td id="drawer-order-price" class="py-3 px-4 text-right font-semibold text-slate-800 dark:text-slate-200">-</td>
                        <td id="drawer-order-subtotal" class="py-3 px-4 text-right font-bold text-slate-800 dark:text-slate-200">-</td>
                    </tr>
                    <tr>
                        <td class="py-3 px-4 font-bold text-slate-850 dark:text-slate-200">Biaya Transaksi Pelanggan (Kode Unik)</td>
                        <td class="py-3 px-4 text-center font-semibold text-slate-800 dark:text-slate-200">1</td>
                        <td id="drawer-order-fee-price" class="py-3 px-4 text-right font-semibold text-slate-800 dark:text-slate-200">-</td>
                        <td id="drawer-order-fee-subtotal" class="py-3 px-4 text-right font-bold text-slate-800 dark:text-slate-200">-</td>
                    </tr>
                    <tr class="font-extrabold text-slate-950 dark:text-white bg-slate-50/50 dark:bg-slate-800/20">
                        <td colspan="3" class="py-3 px-4 text-right text-xs">Total Pembayaran</td>
                        <td id="drawer-order-grandtotal" class="py-3 px-4 text-right text-xs font-black">-</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Bukti Transfer Manual Peserta (Jika Ada) -->
        <div id="drawer-proof-section" class="border border-slate-200 dark:border-slate-800 rounded-3xl p-5 flex flex-col bg-white dark:bg-slate-900 shadow-sm hidden">
            <span class="text-[10px] text-slate-400 dark:text-slate-555 font-bold uppercase mb-3 flex items-center gap-1.5">
                <i data-lucide="image" class="w-4 h-4 text-blue-500"></i> Bukti Transfer Peserta
            </span>
            <a id="drawer-proof-link" href="" target="_blank" class="block group relative overflow-hidden rounded-xl border border-slate-200 dark:border-slate-800">
                <img id="drawer-proof-img" src="" alt="Bukti Transfer" class="max-w-full h-auto mx-auto object-contain bg-slate-50 dark:bg-slate-950 p-1">
                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center text-white text-xs font-bold transition-all duration-200">
                    Klik untuk Memperbesar <i data-lucide="external-link" class="w-3.5 h-3.5 ml-1"></i>
                </div>
            </a>
        </div>

        <!-- QRIS Dynamic QR Code Display -->
        <div class="border border-slate-200 dark:border-slate-800 rounded-3xl p-5 flex flex-col items-center justify-center bg-white dark:bg-slate-900 shadow-sm">
            <span class="text-[10px] text-slate-400 dark:text-slate-555 font-bold uppercase mb-3">Dynamic QR Code (Scan to Pay)</span>
            <img id="drawer-qr-img" src="" alt="QR Code QRIS" class="w-44 h-44 object-contain border border-slate-200 dark:border-slate-855 rounded-xl bg-white p-2.5 mb-4">
            <div class="w-full">
                <span class="text-[9px] text-slate-400 dark:text-slate-555 font-bold block mb-1">RAW QRIS STRING</span>
                <textarea id="drawer-qris-string" readonly rows="3" class="w-full bg-slate-50 dark:bg-slate-955 border border-slate-200 dark:border-slate-800 rounded-xl p-2 text-[9px] font-mono text-slate-500 dark:text-slate-400 focus:outline-none"></textarea>
            </div>
        </div>
    </div>
</div>

<!-- Background Backdrop for Drawer -->
<div id="drawer-backdrop" onclick="closeDetailDrawer()" class="fixed inset-0 bg-black/45 backdrop-blur-sm z-40 hidden"></div>
@endsection

@push('scripts')
<script>
    // Open Payment Detail Drawer
    function openDetailDrawer(tx, custName, custEmail, custPhone, createdAt, expiresAt, paidAt, seasonName) {
        
        // Format amounts
        const amountFormatted = 'Rp ' + Number(tx.amount).toLocaleString('id-ID');
        const baseAmountFormatted = 'Rp ' + Number(tx.base_amount).toLocaleString('id-ID');
        const uniqueFeeFormatted = 'Rp ' + Number(tx.unique_code).toLocaleString('id-ID');

        // Set Header Settle Action Button
        const actionsContainer = document.getElementById('drawer-header-actions');
        if (tx.status === 'PENDING' || tx.status === 'CLAIMED') {
            actionsContainer.innerHTML = `
                <form action="/qris-gateway/settle/${tx.trx_id}" method="POST" onsubmit="return confirm('Selesaikan transaksi ini secara manual?');">
                    @csrf
                    <button type="submit" class="bg-blue-600 hover:bg-blue-500 text-white font-bold py-2 px-4 rounded-xl text-xs transition-all shadow-md active:scale-[0.98] flex items-center gap-1.5">
                        <i data-lucide="check" class="w-4 h-4"></i> Settle Manual
                    </button>
                </form>
            `;
        } else {
            actionsContainer.innerHTML = '';
        }

        // Fill Summary Card
        document.getElementById('drawer-summary-id').innerText = tx.trx_id;
        document.getElementById('drawer-summary-amount').innerText = amountFormatted;
        
        const summaryStatus = document.getElementById('drawer-summary-status');
        const clearingStatus = document.getElementById('drawer-det-clearing');
        if (tx.status === 'PAID') {
            summaryStatus.innerHTML = `<span class="inline-flex px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-100">Dibayar</span>`;
            clearingStatus.innerHTML = `<span class="inline-flex px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-100">Selesai (${paidAt})</span>`;
        } else if (tx.status === 'CLAIMED') {
            summaryStatus.innerHTML = `<span class="inline-flex px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-blue-50 text-blue-700 border border-blue-100 animate-pulse">Klaim Bukti</span>`;
            clearingStatus.innerHTML = `<span class="text-blue-650 dark:text-blue-400 font-bold flex items-center gap-1"><i data-lucide="image" class="w-3.5 h-3.5"></i> Menunggu Verifikasi Bukti</span>`;
        } else if (tx.status === 'PENDING') {
            summaryStatus.innerHTML = `<span class="inline-flex px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-yellow-50 text-yellow-700 border border-yellow-100">Pending</span>`;
            clearingStatus.innerHTML = `<span class="text-yellow-600 dark:text-yellow-450 font-bold">Menunggu Pembayaran</span>`;
        } else {
            summaryStatus.innerHTML = `<span class="inline-flex px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-slate-100 text-slate-700 border border-slate-200">Kedaluwarsa</span>`;
            clearingStatus.innerHTML = `<span class="text-slate-400 font-bold">Gagal</span>`;
        }

        // Tampilkan Bukti Transfer Peserta jika ada
        const proofSection = document.getElementById('drawer-proof-section');
        if (tx.status === 'CLAIMED' && tx.gopay_reference && tx.gopay_reference.startsWith('PROOFS/')) {
            const filename = tx.gopay_reference.replace('PROOFS/', '');
            const proofUrl = '/uploads/proofs/' + filename;
            document.getElementById('drawer-proof-img').src = proofUrl;
            document.getElementById('drawer-proof-link').href = proofUrl;
            proofSection.classList.remove('hidden');
        } else {
            proofSection.classList.add('hidden');
        }

        // Fill Detail Pembayaran
        document.getElementById('drawer-det-amount').innerText = amountFormatted;
        document.getElementById('drawer-det-ref').innerText = tx.trx_id;
        document.getElementById('drawer-det-created').innerText = createdAt + ' WIB';
        document.getElementById('drawer-det-customer-fee').innerText = uniqueFeeFormatted;
        document.getElementById('drawer-det-expires').innerText = expiresAt + ' WIB';
        document.getElementById('drawer-det-total').innerText = amountFormatted;
        document.getElementById('drawer-det-paid').innerText = paidAt !== '-' ? paidAt + ' WIB' : '-';

        // Fill Customer Info
        document.getElementById('drawer-cust-name').innerText = custName;
        document.getElementById('drawer-cust-email').innerText = custEmail;
        document.getElementById('drawer-cust-phone').innerText = custPhone;

        // Fill Order Details
        document.getElementById('drawer-order-product').innerText = `Registrasi ${seasonName}`;
        document.getElementById('drawer-order-price').innerText = baseAmountFormatted;
        document.getElementById('drawer-order-subtotal').innerText = baseAmountFormatted;
        document.getElementById('drawer-order-fee-price').innerText = uniqueFeeFormatted;
        document.getElementById('drawer-order-fee-subtotal').innerText = uniqueFeeFormatted;
        document.getElementById('drawer-order-grandtotal').innerText = amountFormatted;

        // Fill QR Code
        document.getElementById('drawer-qr-img').src = 'https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=' + encodeURIComponent(tx.qris_string);
        document.getElementById('drawer-qris-string').value = tx.qris_string;

        // Re-render Lucide Icons in drawer
        lucide.createIcons();

        // Slide in Drawer & show Backdrop
        document.getElementById('detail-drawer').classList.remove('translate-x-full');
        document.getElementById('drawer-backdrop').classList.remove('hidden');
    }

    // Close Drawer
    function closeDetailDrawer() {
        document.getElementById('detail-drawer').classList.add('translate-x-full');
        document.getElementById('drawer-backdrop').classList.add('hidden');
    }

    // COUNTDOWN TIMER LOGIC
    document.addEventListener('DOMContentLoaded', () => {
        const timerElements = document.querySelectorAll('.countdown-timer');
        
        function updateCountdown() {
            const now = Math.floor(Date.now() / 1000);
            timerElements.forEach(el => {
                const expiresTimestamp = parseInt(el.getAttribute('data-expires'));
                const diff = expiresTimestamp - now;
                
                if (diff <= 0) {
                    el.innerHTML = `<span class="text-red-500 font-bold uppercase text-[9px]"><i data-lucide="alert-circle" class="w-3.5 h-3.5 inline"></i> Kedaluwarsa</span>`;
                    lucide.createIcons();
                } else {
                    const mins = Math.floor(diff / 60);
                    const secs = diff % 60;
                    el.innerHTML = `<i data-lucide="clock" class="w-3.5 h-3.5 shrink-0 inline"></i> ${mins}m ${secs}s`;
                }
            });
        }
        
        if (timerElements.length > 0) {
            setInterval(updateCountdown, 1000);
            updateCountdown();
        }
    });
</script>
@endpush
