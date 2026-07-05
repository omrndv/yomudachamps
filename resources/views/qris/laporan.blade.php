@extends('qris.layout')
@section('title', 'Laporan & Export')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-xl font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
        <i data-lucide="bar-chart-3" class="w-6 h-6 text-blue-600"></i> Laporan & Export Transaksi
    </h2>
</div>

<!-- Filter Card -->
<div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-6 shadow-sm mb-6">
    <form action="{{ route('qris.laporan') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
        <div>
            <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-550 uppercase tracking-wider mb-2">Dari Tanggal</label>
            <input type="date" name="dari" value="{{ request('dari') }}" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-250 dark:border-slate-700 rounded-xl px-4 py-2 text-xs font-semibold text-slate-700 dark:text-white focus:outline-none focus:border-blue-600">
        </div>
        <div>
            <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-550 uppercase tracking-wider mb-2">Sampai Tanggal</label>
            <input type="date" name="sampai" value="{{ request('sampai') }}" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-250 dark:border-slate-700 rounded-xl px-4 py-2 text-xs font-semibold text-slate-700 dark:text-white focus:outline-none focus:border-blue-600">
        </div>
        <div>
            <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-550 uppercase tracking-wider mb-2">Filter Season</label>
            <select name="season_id" class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-250 dark:border-slate-700 rounded-xl px-4 py-2 text-xs font-semibold text-slate-700 dark:text-white focus:outline-none focus:border-blue-600">
                <option value="">Semua Season</option>
                @foreach($seasons as $s)
                    <option value="{{ $s->id }}" {{ request('season_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex gap-2">
            <button type="submit" class="flex-1 bg-slate-800 hover:bg-slate-900 text-white font-bold py-2.5 px-4 rounded-xl text-xs shadow-sm transition-all text-center">
                Filter
            </button>
            <a href="{{ route('qris.export-csv', request()->all()) }}" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-4 rounded-xl text-xs shadow-sm transition-all text-center flex items-center justify-center gap-1">
                <i data-lucide="download" class="w-3.5 h-3.5"></i> Export CSV
            </a>
        </div>
    </form>
</div>

<!-- Stats / Total Volume Summary -->
<div class="grid grid-cols-1 sm:grid-cols-2 gap-6 mb-6">
    <div class="bg-blue-600 dark:bg-blue-700 text-white rounded-3xl p-6 shadow-md relative overflow-hidden">
        <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-white/5 rounded-full blur-xl"></div>
        <div class="flex justify-between items-start mb-2">
            <div class="text-[10px] font-bold text-blue-100 uppercase tracking-wider">Total Volume Sukses (Filter Aktif)</div>
            <i data-lucide="wallet" class="w-5 h-5 text-blue-200"></i>
        </div>
        <div class="text-2xl font-black font-mono">
            Rp {{ number_format($totalVolume, 0, ',', '.') }}
        </div>
    </div>
    <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-6 shadow-sm flex justify-between items-center">
        <div>
            <div class="text-[10px] font-bold text-slate-400 dark:text-slate-550 uppercase tracking-wider mb-1">Jumlah Transaksi Terbayar</div>
            <div class="text-2xl font-black text-slate-900 dark:text-white">{{ $transactions->count() }}</div>
        </div>
        <div class="w-12 h-12 bg-emerald-50 dark:bg-emerald-500/10 rounded-2xl flex items-center justify-center text-emerald-600">
            <i data-lucide="check-circle" class="w-6 h-6"></i>
        </div>
    </div>
</div>

<!-- Laporan Table -->
<div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm border-collapse">
            <thead>
                <tr class="bg-slate-50 dark:bg-slate-800/40 border-b border-slate-200 dark:border-slate-800 text-slate-400 dark:text-slate-550 font-bold uppercase text-[10px] tracking-wider">
                    <th class="py-4 px-6">ID / Ref GoPay</th>
                    <th class="py-4 px-6">Nama Tim</th>
                    <th class="py-4 px-6">Season</th>
                    <th class="py-4 px-6">Nominal</th>
                    <th class="py-4 px-6">Waktu Pembayaran</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($transactions as $tx)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-850/50 transition-all">
                        <td class="py-4 px-6">
                            <span class="font-mono text-xs text-sky-600 dark:text-sky-400 font-bold block">{{ $tx->trx_id }}</span>
                            @if($tx->gopay_reference)
                                <span class="text-[10px] text-slate-400 font-mono block mt-1">GoPay: {{ $tx->gopay_reference }}</span>
                            @endif
                        </td>
                        <td class="py-4 px-6 font-bold text-slate-900 dark:text-white">
                            <a href="{{ route('qris.team-detail', $tx->team->id ?? 0) }}" class="hover:underline text-blue-600">{{ $tx->team->name ?? 'Tim Terhapus' }}</a>
                        </td>
                        <td class="py-4 px-6 text-slate-500 dark:text-slate-400 text-xs font-semibold">
                            {{ $tx->team->season->name ?? '-' }}
                        </td>
                        <td class="py-4 px-6 font-bold text-slate-900 dark:text-white">
                            Rp {{ number_format($tx->amount, 0, ',', '.') }}
                        </td>
                        <td class="py-4 px-6 text-xs text-slate-500 dark:text-slate-400">
                            {{ $tx->paid_at ? $tx->paid_at->setTimezone('Asia/Jakarta')->format('d M Y, H:i') : '-' }} WIB
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-slate-400 italic">
                            Belum ada transaksi sukses yang cocok dengan filter pencarian Anda.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
