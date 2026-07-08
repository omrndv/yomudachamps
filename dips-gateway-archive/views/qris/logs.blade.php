@extends('qris.layout')
@section('title', 'Audit Log & API Logger')

@section('content')
<div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl shadow-sm p-6 sm:p-8">
    <div class="flex items-center justify-between flex-wrap gap-4 mb-8">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-rose-50 dark:bg-rose-500/10 rounded-2xl flex items-center justify-center text-rose-600">
                <i data-lucide="shield-alert" class="w-6 h-6"></i>
            </div>
            <div>
                <h3 class="text-lg font-black text-slate-900 dark:text-white">Audit Log & API Error Logger</h3>
                <p class="text-xs text-slate-400 dark:text-slate-550 mt-1">Pantau performa koneksi API, log notifikasi, dan anomali sistem secara real-time.</p>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto rounded-2xl border border-slate-100 dark:border-slate-800/80">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 dark:bg-slate-950 border-b border-slate-150 dark:border-slate-850">
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider">Waktu</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider">Jenis Event</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider">Judul</th>
                    <th class="px-6 py-4 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-wider">Detail Pesan / Payload</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-slate-850 text-xs">
                @forelse($logs as $log)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/20 transition-all">
                        <td class="px-6 py-4 text-slate-500 dark:text-slate-450 font-medium whitespace-nowrap font-mono">
                            {{ $log->created_at->setTimezone('Asia/Jakarta')->format('d/m/Y H:i:s') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($log->type === 'API_ERROR' || $log->type === 'ERROR')
                                <span class="px-2.5 py-1 rounded-full text-[9px] font-black bg-red-50 dark:bg-red-500/10 text-red-650 dark:text-red-400 border border-red-100 dark:border-red-500/20 uppercase tracking-wide">
                                    API Error
                                </span>
                            @elseif($log->type === 'TRANSACTION_PAID')
                                <span class="px-2.5 py-1 rounded-full text-[9px] font-black bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-500/20 uppercase tracking-wide">
                                    Paid
                                </span>
                            @elseif($log->type === 'TRANSACTION_CREATED')
                                <span class="px-2.5 py-1 rounded-full text-[9px] font-black bg-blue-50 dark:bg-blue-500/10 text-blue-750 dark:text-blue-400 border border-blue-100 dark:border-blue-500/20 uppercase tracking-wide">
                                    Created
                                </span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-[9px] font-black bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-405 uppercase tracking-wide">
                                    {{ $log->type }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-slate-900 dark:text-white font-extrabold whitespace-nowrap">
                            {{ $log->title }}
                        </td>
                        <td class="px-6 py-4 text-slate-600 dark:text-slate-400 leading-relaxed font-mono text-[11px] max-w-lg break-all">
                            {{ $log->message }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-12 text-slate-400 italic text-xs">
                            Belum ada log aktivitas yang tercatat.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $logs->links() }}
    </div>
</div>
@endsection
