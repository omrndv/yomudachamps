@extends('qris.layout')
@section('title', 'Rekonsiliasi GoPay')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-xl font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
        <i data-lucide="arrow-left-right" class="w-6 h-6 text-blue-600"></i> Rekonsiliasi GoPay
    </h2>
    <div class="text-xs bg-slate-100 dark:bg-slate-800 text-slate-500 px-3 py-1.5 rounded-xl font-semibold">
        Menampilkan 50 Mutasi GoPay Terkini
    </div>
</div>

<div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm border-collapse">
            <thead>
                <tr class="bg-slate-50 dark:bg-slate-800/40 border-b border-slate-200 dark:border-slate-800 text-slate-400 dark:text-slate-550 font-bold uppercase text-[10px] tracking-wider">
                    <th class="py-4 px-6">Mutasi GoPay (API)</th>
                    <th class="py-4 px-6">Nominal</th>
                    <th class="py-4 px-6">Status API</th>
                    <th class="py-4 px-6">Kecocokan Database (Website)</th>
                    <th class="py-4 px-6 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse($rows as $r)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-850/50 transition-all">
                        <!-- GoPay Info -->
                        <td class="py-4 px-6">
                            <span class="font-mono text-xs text-slate-700 dark:text-slate-300 font-semibold block">Ref: {{ $r['ref_id'] }}</span>
                            <span class="text-[10px] text-slate-400 block mt-1">
                                {{ $r['time'] ? \Carbon\Carbon::parse($r['time'])->setTimezone('Asia/Jakarta')->format('d M Y, H:i') : '-' }} WIB
                                | Issuer: {{ $r['issuer'] }}
                            </span>
                        </td>
                        <!-- Amount -->
                        <td class="py-4 px-6 font-bold text-slate-900 dark:text-white">
                            Rp {{ number_format($r['amount'], 0, ',', '.') }}
                        </td>
                        <!-- GoPay Status -->
                        <td class="py-4 px-6">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-100/50 dark:border-emerald-500/20">
                                {{ $r['gopay_status'] }}
                            </span>
                        </td>
                        <!-- DB Match Status -->
                        <td class="py-4 px-6">
                            @if($r['is_matched'])
                                <div class="flex flex-col">
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-black bg-green-500/10 text-green-600 border border-green-500/20 w-fit">
                                        <i data-lucide="check" class="w-3 h-3"></i> TERVERIFIKASI
                                    </span>
                                    @if($r['db_tx'] && $r['db_tx']->team)
                                        <span class="text-[10.5px] text-slate-500 mt-1 font-semibold">
                                            Tim: <a href="{{ route('qris.team-detail', $r['db_tx']->team->id) }}" class="text-blue-600 hover:underline font-bold">{{ $r['db_tx']->team->name }}</a>
                                        </span>
                                    @endif
                                </div>
                            @else
                                @if($r['db_tx'])
                                    <!-- Suspect pending/expired -->
                                    <div class="flex flex-col gap-1">
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-black bg-amber-500/10 text-amber-600 border border-amber-500/20 w-fit animate-pulse">
                                            <i data-lucide="help-circle" class="w-3 h-3"></i> BUTUH VERIFIKASI
                                        </span>
                                        <span class="text-[10.5px] text-slate-500 font-medium">
                                            Terduga: <a href="{{ route('qris.team-detail', $r['db_tx']->team->id) }}" class="text-blue-600 hover:underline font-bold">{{ $r['db_tx']->team->name }}</a> ({{ $r['db_tx']->status }})
                                        </span>
                                    </div>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-400 dark:text-slate-550 border border-slate-200 dark:border-slate-700 w-fit">
                                        TIDAK ADA DATA TURNAMEN
                                    </span>
                                @endif
                            @endif
                        </td>
                        <!-- Actions -->
                        <td class="py-4 px-6 text-right">
                            @if(!$r['is_matched'] && $r['db_tx'])
                                <form action="{{ route('qris.settle', $r['db_tx']->trx_id) }}" method="POST" class="inline" onsubmit="return confirm('Sahkan transaksi tim {{ $r['db_tx']->team->name }} menggunakan referensi GoPay ini?');">
                                    @csrf
                                    <input type="hidden" name="gopay_ref" value="{{ $r['ref_id'] }}">
                                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-[10.5px] font-extrabold px-3 py-1.5 rounded-xl transition-all shadow-sm">
                                        Selesaikan Pembayaran
                                    </button>
                                </form>
                            @else
                                <span class="text-xs text-slate-350 dark:text-slate-650 font-semibold italic">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-12 text-center text-slate-400 italic">
                            Tidak ada mutasi GoPay yang terdeteksi.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
