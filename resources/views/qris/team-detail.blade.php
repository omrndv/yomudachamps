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

                            <form action="{{ route('qris.delete', $tx->trx_id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus transaksi ini?');" class="inline-block m-0">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-50 text-red-650 hover:bg-red-500 hover:text-white dark:bg-red-950/20 dark:text-red-400 dark:hover:bg-red-650 text-xs font-bold px-3.5 py-2 rounded-xl transition-all shadow-sm">
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
