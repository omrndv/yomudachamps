@extends('qris.layout')
@section('title', 'Detail Tim - ' . $team->name)

@section('content')
<!-- Page Header -->
<div class="mb-6 flex items-center justify-between">
    <div class="flex items-center gap-3">
        <a href="{{ route('qris.transactions') }}" class="w-10 h-10 bg-slate-100 dark:bg-slate-800 text-slate-650 hover:bg-slate-200 dark:hover:bg-slate-700 rounded-2xl flex items-center justify-center transition-all shadow-sm">
            <i data-lucide="arrow-left" class="w-5 h-5"></i>
        </a>
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 dark:text-white">{{ $team->name }}</h2>
            <span class="text-xs text-slate-400 font-bold block mt-1 uppercase tracking-wider">Season: {{ $team->season->name ?? '-' }}</span>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Team Profile Info Card -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-6 shadow-sm h-fit">
        <h3 class="text-sm font-extrabold text-slate-900 dark:text-white mb-4 uppercase tracking-wider">Profil Pendaftaran Tim</h3>
        
        <div class="space-y-4">
            <div>
                <label class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Email Kontak</label>
                <span class="text-xs font-semibold text-slate-700 dark:text-slate-200 block">{{ $team->email ?? '-' }}</span>
            </div>
            <div>
                <label class="text-[10px] uppercase font-bold text-slate-400 block mb-1">No. WhatsApp / HP</label>
                <span class="text-xs font-semibold text-slate-700 dark:text-slate-200 block">{{ $team->phone ?? '-' }}</span>
            </div>
            <div>
                <label class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Tanggal Daftar</label>
                <span class="text-xs font-semibold text-slate-700 dark:text-slate-200 block">{{ $team->created_at->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB</span>
            </div>
            <div>
                <label class="text-[10px] uppercase font-bold text-slate-400 block mb-1">Status Turnamen saat ini</label>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-extrabold 
                    @if($team->status === 'PAID') bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-100/50 dark:border-emerald-500/20
                    @elseif($team->status === 'PENDING') bg-yellow-50 dark:bg-yellow-500/10 text-yellow-700 dark:text-yellow-450 border border-yellow-100/50 dark:border-yellow-500/20
                    @else bg-red-50 dark:bg-red-500/10 text-red-700 dark:text-red-400 border border-red-100/50 dark:border-red-550/20
                    @endif">
                    {{ $team->status }}
                </span>
            </div>
        </div>
    </div>

    <!-- QRIS Transactions History List -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-6 shadow-sm">
            <h3 class="text-sm font-extrabold text-slate-900 dark:text-white mb-4 uppercase tracking-wider">Riwayat Tagihan & Pembayaran QRIS</h3>
            
            <div class="space-y-4">
                @forelse($transactions as $tx)
                    <div class="border border-slate-100 dark:border-slate-800/80 p-5 rounded-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-2 mb-2 flex-wrap">
                                <span class="text-xs font-mono text-sky-600 dark:text-sky-400 font-bold block">{{ $tx->trx_id }}</span>
                                <span class="text-[10px] font-extrabold px-2.5 py-1 rounded-lg 
                                    @if($tx->status === 'PAID') bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400
                                    @elseif($tx->status === 'PENDING') bg-yellow-50 dark:bg-yellow-500/10 text-yellow-700 dark:text-yellow-450
                                    @else bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-500
                                    @endif">
                                    {{ $tx->status }}
                                </span>
                            </div>
                            <div class="space-y-1">
                                <div class="text-xs font-semibold text-slate-700 dark:text-slate-200">
                                    Tagihan: <strong class="text-slate-900 dark:text-white text-sm">Rp {{ number_format($tx->amount, 0, ',', '.') }}</strong> 
                                    (Kode Unik: +{{ $tx->unique_code }})
                                </div>
                                <div class="text-[10px] text-slate-450 dark:text-slate-500 font-medium">
                                    Dibuat: {{ $tx->created_at->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB | 
                                    Kedaluwarsa: {{ $tx->expires_at->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB
                                </div>
                                @if($tx->gopay_reference)
                                    <div class="text-[10.5px] font-mono text-slate-450 dark:text-slate-500 mt-2">
                                        No. Ref GoPay: <strong class="text-slate-650 dark:text-slate-350">{{ $tx->gopay_reference }}</strong>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Action buttons for specific transaction -->
                        <div class="flex items-center gap-2">
                            @if($tx->status === 'PENDING' || $tx->status === 'CLAIMED' || $tx->status === 'EXPIRED')
                                <form action="{{ route('qris.settle', $tx->trx_id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menyelesaikan transaksi ini secara manual?');" class="inline-block m-0">
                                    @csrf
                                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold px-3.5 py-2 rounded-xl transition-all shadow-sm">
                                        Setujui & Settle
                                    </button>
                                </form>
                            @endif

                            @if($tx->status === 'PAID')
                                <a href="{{ route('qris.invoice', $tx->trx_id) }}" target="_blank" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold px-3.5 py-2.5 rounded-xl transition-all shadow-sm flex items-center gap-1">
                                    <i data-lucide="printer" class="w-3.5 h-3.5"></i> Invoice
                                </a>
                                <button type="button" onclick="showRefundModal('{{ $tx->trx_id }}', '{{ $tx->amount }}', '{{ $team->phone ?? '' }}', '{{ $team->name ?? '' }}')" class="bg-amber-600 hover:bg-amber-500 text-white text-xs font-bold px-3.5 py-2.5 rounded-xl transition-all shadow-sm">
                                    Refund
                                </button>
                                <form id="refund-form-{{ $tx->trx_id }}" action="{{ route('qris.refund', $tx->trx_id) }}" method="POST" style="display:none;">
                                    @csrf
                                </form>
                            @endif

                            <form action="{{ route('qris.delete', $tx->trx_id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus transaksi ini?');" class="inline-block m-0">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-50 text-red-650 hover:bg-red-500 hover:text-white dark:bg-red-950/20 dark:text-red-400 dark:hover:bg-red-650 text-xs font-bold px-3.5 py-2.5 rounded-xl transition-all shadow-sm">
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8 text-slate-400 italic text-xs">
                        Tidak ada transaksi QRIS yang tercatat untuk tim ini.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    window.showRefundModal = function(trxId, amount, phone, teamName) {
        const modal = document.getElementById('refund-helper-modal');
        document.getElementById('refund-target-team').innerText = teamName;
        document.getElementById('refund-amount').innerText = 'Rp ' + parseInt(amount).toLocaleString('id-ID');
        document.getElementById('refund-phone').innerText = phone || '-';
        
        // Buat QR Code berisi teks informasi transfer agar admin bisa scan & salin instan
        const qrData = encodeURIComponent(`Transfer Rp ${amount} ke E-Wallet/HP: ${phone} (Refund Tim ${teamName})`);
        document.getElementById('refund-qr-img').src = `https://api.qrserver.com/v1/create-qr-code/?size=160x160&data=${qrData}`;
        
        // Simpan trxId untuk aksi submit form
        document.getElementById('btn-confirm-refund-submit').onclick = function() {
            if (confirm('Konfirmasi sekali lagi untuk menandai transaksi ' + trxId + ' sebagai REFUNDED di database?')) {
                document.getElementById('refund-form-' + trxId).submit();
            }
        };

        modal.classList.remove('hidden');
    }

    window.closeRefundModal = function() {
        document.getElementById('refund-helper-modal').classList.add('hidden');
    }
</script>
@endpush

<!-- Refund Helper Modal -->
<div id="refund-helper-modal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" aria-hidden="true" onclick="closeRefundModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        <div class="inline-block align-bottom bg-white dark:bg-slate-900 rounded-3xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-slate-200 dark:border-slate-800">
            <div class="bg-white dark:bg-slate-900 p-6">
                <div class="flex items-start gap-4">
                    <div class="mx-auto shrink-0 flex items-center justify-center h-10 w-10 rounded-2xl bg-amber-50 dark:bg-amber-500/10 text-amber-600">
                        <i data-lucide="arrow-left-right" class="w-5 h-5"></i>
                    </div>
                    <div class="text-left w-full">
                        <h3 class="text-md font-extrabold text-slate-900 dark:text-white mb-1">Panduan Pengembalian Dana</h3>
                        <p class="text-xs text-slate-500 mb-4">Scan QR di bawah untuk menyalin nomor tujuan atau salin data transfer secara manual.</p>
                        
                        <!-- QR Code Area -->
                        <div class="flex justify-center p-4 bg-slate-50 dark:bg-slate-950 border border-slate-100 dark:border-slate-800 rounded-2xl mb-4">
                            <img id="refund-qr-img" src="" alt="Scan Refund QR" class="w-40 h-40 border border-slate-200 rounded-xl">
                        </div>

                        <!-- Details Info -->
                        <div class="space-y-3 p-4 bg-slate-50 dark:bg-slate-950 border border-slate-100 dark:border-slate-800 rounded-2xl">
                            <div class="flex justify-between text-xs">
                                <span class="text-slate-400 font-medium">Tim Penerima:</span>
                                <span id="refund-target-team" class="font-extrabold text-slate-850 dark:text-white">-</span>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span class="text-slate-400 font-medium">Nominal Refund:</span>
                                <span id="refund-amount" class="font-extrabold text-amber-600 font-mono">-</span>
                            </div>
                            <div class="flex justify-between text-xs">
                                <span class="text-slate-400 font-medium">No. HP / E-Wallet:</span>
                                <span id="refund-phone" class="font-extrabold text-slate-850 dark:text-white font-mono">-</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-slate-50 dark:bg-slate-800/40 px-6 py-4 flex flex-row-reverse gap-2 rounded-b-3xl">
                <button type="button" id="btn-confirm-refund-submit" class="bg-amber-500 hover:bg-amber-600 text-white font-bold px-4 py-2 text-xs rounded-xl transition-all shadow-sm">
                    Tandai Telah Direfund (VOID)
                </button>
                <button type="button" onclick="closeRefundModal()" class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-semibold px-4 py-2 text-xs rounded-xl transition-all shadow-sm">
                    Batal
                </button>
            </div>
        </div>
    </div>
</div>
