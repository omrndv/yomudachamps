@extends('qris.layout')
@section('title', 'Quick Checkout (QRIS Bebas)')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <!-- Left Column: Generator Form -->
    <div class="lg:col-span-1">
        <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-6 shadow-sm">
            <div class="flex items-center gap-3 mb-6">
                <div class="w-10 h-10 bg-blue-50 dark:bg-blue-500/10 rounded-2xl flex items-center justify-center text-blue-600">
                    <i data-lucide="link" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="text-sm font-extrabold text-slate-900 dark:text-white">Buat QRIS Bebas</h3>
                    <p class="text-[10px] text-slate-450 dark:text-slate-500 mt-0.5">Buat link tagihan/checkout QRIS instan.</p>
                </div>
            </div>

            @if(session('success'))
                <div class="bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 text-emerald-700 dark:text-emerald-400 px-4 py-3 rounded-xl text-xs font-bold mb-6 flex items-center gap-2">
                    <i data-lucide="check-circle" class="w-4 h-4"></i> {{ session('success') }}
                </div>
            @endif

            @if(session('success_link'))
                <div class="bg-blue-50 dark:bg-blue-500/10 border border-blue-200 dark:border-blue-500/20 p-4 rounded-2xl mb-6">
                    <span class="text-[10px] font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider block mb-2">Link QRIS Bebas Berhasil Dibuat:</span>
                    <div class="flex items-center gap-2">
                        <input type="text" id="quick-checkout-url" readonly value="{{ session('success_link') }}" class="w-full bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-850 text-slate-800 dark:text-slate-200 rounded-xl px-3 py-2 text-xs font-mono focus:outline-none">
                        <button type="button" onclick="copyQuickUrl()" class="bg-blue-600 hover:bg-blue-700 text-white p-2.5 rounded-xl transition-all shadow-sm active:scale-95 shrink-0 flex items-center justify-center">
                            <i data-lucide="copy" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 text-red-700 dark:text-red-400 px-4 py-3 rounded-xl text-xs font-bold mb-6 flex items-center gap-2">
                    <i data-lucide="alert-circle" class="w-4 h-4"></i> {{ session('error') }}
                </div>
            @endif

            <form action="{{ route('qris.quick-checkout') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">Deskripsi Pembayaran</label>
                    <input type="text" name="description" required class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-850 text-slate-900 dark:text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 transition-all" placeholder="Contoh: Jersey Yomuda Season 5">
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-555 uppercase tracking-wider mb-2">Nominal Pembayaran (Rp)</label>
                    <input type="number" name="amount" required min="1000" class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-855 text-slate-900 dark:text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 transition-all font-mono" placeholder="150000">
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-3 px-4 rounded-xl text-xs transition-all shadow-md active:scale-[0.98] flex items-center justify-center gap-2 mt-6">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i> Buat Link Pembayaran
                </button>
            </form>
        </div>
    </div>

    <!-- Right Column: History List -->
    <div class="lg:col-span-2">
        <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-6 sm:p-8 shadow-sm">
            <h3 class="text-sm font-extrabold text-slate-900 dark:text-white mb-6">Riwayat QRIS Bebas</h3>

            <div class="overflow-x-auto rounded-2xl border border-slate-100 dark:border-slate-850">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-950 border-b border-slate-150 dark:border-slate-850">
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider">ID / Deskripsi</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider">Nominal</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-850 text-xs">
                        @forelse($quickTx as $tx)
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/20 transition-all">
                                <td class="px-6 py-4 text-slate-500 dark:text-slate-450 font-medium font-mono whitespace-nowrap">
                                    {{ $tx->created_at->setTimezone('Asia/Jakarta')->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-extrabold text-slate-850 dark:text-white">{{ $tx->description }}</div>
                                    <div class="text-[10px] font-mono text-slate-400 mt-0.5">{{ $tx->trx_id }}</div>
                                </td>
                                <td class="px-6 py-4 font-black font-mono text-slate-700 dark:text-slate-200">
                                    Rp {{ number_format($tx->amount, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($tx->status === 'PAID')
                                        <span class="px-2.5 py-1 rounded-full text-[9px] font-black bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-500/20 uppercase tracking-wide">
                                            PAID
                                        </span>
                                    @elseif($tx->status === 'PENDING')
                                        <span class="px-2.5 py-1 rounded-full text-[9px] font-black bg-yellow-50 dark:bg-yellow-500/10 text-yellow-700 dark:text-yellow-450 border border-yellow-100 dark:border-yellow-500/20 uppercase tracking-wide">
                                            PENDING
                                        </span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-[9px] font-black bg-slate-50 dark:bg-slate-800 text-slate-500 dark:text-slate-400 border border-slate-100 dark:border-slate-700 uppercase tracking-wide">
                                            {{ $tx->status }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('qris.pay', $tx->trx_id) }}" target="_blank" class="bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 p-2 rounded-xl transition-all flex items-center justify-center" title="Buka Halaman Bayar">
                                            <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                                        </a>
                                        <button type="button" onclick="copyToClipboard('{{ route('qris.pay', $tx->trx_id) }}')" class="bg-blue-50 hover:bg-blue-100 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 p-2 rounded-xl transition-all flex items-center justify-center" title="Salin Link">
                                            <i data-lucide="copy" class="w-3.5 h-3.5"></i>
                                        </button>
                                        <form action="{{ route('qris.delete', $tx->trx_id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus link checkout ini?');" class="inline m-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-red-50 hover:bg-red-100 text-red-650 p-2 rounded-xl transition-all flex items-center justify-center" title="Hapus Link">
                                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-12 text-slate-400 italic text-xs">
                                    Belum ada transaksi QRIS bebas yang dibuat.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-6 font-semibold">
                {{ $quickTx->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function copyQuickUrl() {
        const copyText = document.getElementById("quick-checkout-url");
        copyText.select();
        copyText.setSelectionRange(0, 99999);
        navigator.clipboard.writeText(copyText.value);
        alert("Link pembayaran berhasil disalin ke clipboard!");
    }

    function copyToClipboard(text) {
        navigator.clipboard.writeText(text);
        alert("Link pembayaran berhasil disalin ke clipboard!");
    }
</script>
@endpush
