@extends('qris.layout')
@section('title', 'Settlement & Payout Tracker')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <!-- Left Column: Record Payout Form -->
    <div class="lg:col-span-1">
        <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-6 shadow-sm">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-rose-50 dark:bg-rose-500/10 rounded-2xl flex items-center justify-center text-rose-600">
                    <i data-lucide="download-cloud" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900 dark:text-white">Catat Payout Baru</h3>
                    <p class="text-[10px] text-slate-450 dark:text-slate-500 mt-0.5">Catat penarikan dana ke rekening penampung.</p>
                </div>
            </div>

            @if(session('success'))
                <div class="bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 text-emerald-700 dark:text-emerald-400 px-4 py-3 rounded-xl text-xs font-bold mb-6 flex items-center gap-2">
                    <i data-lucide="check-circle" class="w-4 h-4"></i> {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('qris.payouts') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">Nama Bank Penerima</label>
                    <input type="text" name="destination_bank" required class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-850 text-slate-900 dark:text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-rose-500 transition-all" placeholder="Contoh: BCA, Mandiri, GoPay">
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">Nomor Rekening / E-Wallet</label>
                    <input type="text" name="destination_account" required class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-850 text-slate-900 dark:text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-rose-500 transition-all font-mono" placeholder="85122616191">
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">Nama Pemilik Rekening</label>
                    <input type="text" name="recipient_name" required class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-850 text-slate-900 dark:text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-rose-500 transition-all" placeholder="Contoh: Nadiv">
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">Nominal Payout (Rp)</label>
                    <input type="number" name="amount" required min="1000" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-850 text-slate-900 dark:text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-rose-500 transition-all font-mono" placeholder="1000000">
                </div>

                <button type="submit" class="w-full bg-rose-600 hover:bg-rose-500 text-white font-bold py-3 px-4 rounded-xl text-xs transition-all shadow-md active:scale-[0.98] flex items-center justify-center gap-2 mt-6">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i> Catat Payout
                </button>
            </form>
        </div>
    </div>

    <!-- Right Column: History & Stats -->
    <div class="lg:col-span-2 space-y-6">
        <!-- Accumulation Stats -->
        <div class="bg-gradient-to-r from-rose-500 to-rose-600 text-white rounded-3xl p-6 shadow-md relative overflow-hidden flex items-center justify-between">
            <div class="absolute -right-6 -bottom-6 w-32 h-32 bg-white/5 rounded-full blur-2xl"></div>
            <div>
                <h4 class="text-xs font-bold text-rose-100 uppercase tracking-wider">Total Akumulasi Payout</h4>
                <div class="text-2xl font-black font-mono mt-1">Rp {{ number_format($totalPayout, 0, ',', '.') }}</div>
            </div>
            <div class="w-12 h-12 bg-white/10 rounded-2xl flex items-center justify-center border border-white/20">
                <i data-lucide="arrow-up-right" class="w-6 h-6"></i>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-6 sm:p-8 shadow-sm">
            <h3 class="text-sm font-extrabold text-slate-900 dark:text-white mb-6">Riwayat Payout & Settlement</h3>

            <div class="overflow-x-auto rounded-2xl border border-slate-100 dark:border-slate-850">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-950 border-b border-slate-150 dark:border-slate-850">
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider">Tujuan Transfer</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider">Nominal</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-850 text-xs">
                        @forelse($payouts as $p)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/20 transition-all">
                                <td class="px-6 py-4 text-slate-500 dark:text-slate-450 font-medium font-mono whitespace-nowrap">
                                    {{ $p->created_at->setTimezone('Asia/Jakarta')->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-extrabold text-slate-850 dark:text-white">{{ $p->recipient_name }}</div>
                                    <div class="text-[10px] font-mono text-slate-400 mt-0.5">{{ $p->destination_bank }} ({{ $p->destination_account }})</div>
                                </td>
                                <td class="px-6 py-4 font-black font-mono text-slate-700 dark:text-slate-200">
                                    Rp {{ number_format($p->amount, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2.5 py-1 rounded-full text-[9px] font-black bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-100 dark:border-rose-500/20 uppercase tracking-wide">
                                        {{ $p->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-12 text-slate-400 italic text-xs">
                                    Belum ada transaksi payout yang tercatat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6 font-semibold">
                {{ $payouts->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
