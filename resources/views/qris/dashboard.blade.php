@extends('qris.layout')
@section('title', 'Dashboard')

@section('content')
<!-- Page Header -->
<div class="flex justify-between items-center mb-6">
    <h2 class="text-xl font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
        <i data-lucide="layout-dashboard" class="w-6 h-6 text-blue-600"></i> Dashboard Overview
    </h2>
    <form action="{{ route('qris.sync-pending') }}" method="POST">
        @csrf
        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-xl flex items-center gap-2 text-sm shadow-sm transition-all" onclick="this.innerHTML='<i data-lucide=\'loader\' class=\'w-4 h-4 animate-spin\'></i> Syncing...'; this.form.submit(); this.disabled=true;">
            <i data-lucide="refresh-cw" class="w-4 h-4"></i> Sync Pending
        </button>
    </form>
</div>

<!-- Stats Summary Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
    
    <!-- First Card: Dark Blue Theme Card -->
    <div class="bg-blue-600 dark:bg-blue-700 text-white rounded-3xl p-6 shadow-md relative overflow-hidden group">
        <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-white/5 rounded-full blur-xl"></div>
        <div class="flex justify-between items-start mb-4">
            <div class="text-[10px] font-bold text-blue-100 uppercase tracking-wider">Total Volume Sukses</div>
            <div class="w-7 h-7 bg-white/10 rounded-lg flex items-center justify-center text-white">
                <i data-lucide="wallet" class="w-4 h-4"></i>
            </div>
        </div>
        <div class="text-2xl font-black font-mono">
            Rp {{ number_format($globalStats->total_volume, 0, ',', '.') }}
        </div>
        <div class="text-[10px] text-blue-100 mt-3 font-semibold flex items-center gap-1">
            <i data-lucide="trending-up" class="w-3.5 h-3.5"></i> 100% Settle rate
        </div>
    </div>

    <!-- Card 2: White/Dark Spending Card -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-6 shadow-sm">
        <div class="flex justify-between items-start mb-4">
            <div class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Transaksi Sukses</div>
            <div class="w-7 h-7 bg-slate-100 dark:bg-slate-800 rounded-lg flex items-center justify-center text-blue-600 dark:text-blue-450">
                <i data-lucide="check-circle" class="w-4 h-4"></i>
            </div>
        </div>
        <div class="text-2xl font-black text-slate-900 dark:text-white">
            {{ $globalStats->paid_count }} Trx
        </div>
        <div class="text-[10px] text-slate-450 dark:text-slate-500 mt-3 font-semibold flex items-center gap-1">
            <i data-lucide="trending-up" class="w-3.5 h-3.5"></i> Transaksi terbayar lunas
        </div>
    </div>

    <!-- Card 3: Pending Card -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-6 shadow-sm">
        <div class="flex justify-between items-start mb-4">
            <div class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Transaksi Pending</div>
            <div class="w-7 h-7 bg-yellow-50 dark:bg-yellow-500/10 rounded-lg flex items-center justify-center text-yellow-600 dark:text-yellow-450">
                <i data-lucide="hourglass" class="w-4 h-4"></i>
            </div>
        </div>
        <div class="text-2xl font-black text-slate-900 dark:text-white">
            {{ $globalStats->pending_count }} Trx
        </div>
        <div class="text-[10px] text-yellow-600 dark:text-yellow-450 mt-3 font-semibold flex items-center gap-1">
            <i data-lucide="clock" class="w-3.5 h-3.5"></i> Menunggu pembayaran
        </div>
    </div>

    <!-- Card 4: Expired Card -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-6 shadow-sm">
        <div class="flex justify-between items-start mb-4">
            <div class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">Transaksi Kedaluwarsa</div>
            <div class="w-7 h-7 bg-slate-100 dark:bg-slate-800 rounded-lg flex items-center justify-center text-slate-450">
                <i data-lucide="slash" class="w-4 h-4"></i>
            </div>
        </div>
        <div class="text-2xl font-black text-slate-900 dark:text-white">
            {{ $globalStats->expired_count }} Trx
        </div>
        <div class="text-[10px] text-slate-450 dark:text-slate-550 mt-3 font-semibold flex items-center gap-1">
            <i data-lucide="alert-triangle" class="w-3.5 h-3.5"></i> Melewati batas waktu
        </div>
    </div>
</div>

<!-- Charts Row with Chart.js -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-8">
    <!-- Chart 1: Bar Chart (Monthly Overview) -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-6 shadow-sm">
        <h3 class="text-sm font-extrabold text-slate-900 dark:text-white mb-6">Analisis Transaksi Bulanan</h3>
        <div class="h-56 relative w-full">
            <canvas id="monthlyChart"></canvas>
        </div>
    </div>

    <!-- Chart 2: Line Chart (Weekly Volume) -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-6 shadow-sm">
        <h3 class="text-sm font-extrabold text-slate-900 dark:text-white mb-6">Performa Transaksi Mingguan</h3>
        <div class="h-56 relative w-full">
            <canvas id="weeklyChart"></canvas>
        </div>
    </div>
</div>

<!-- STATUS KONEKSI & HEALTH CHECK (Nomor 5) -->
<div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl shadow-sm p-6 mt-8">
    <h3 class="text-sm font-extrabold mb-5 flex items-center gap-2 text-slate-900 dark:text-white">
        <i data-lucide="heart-pulse" class="w-5 h-5 text-emerald-500"></i> Status Sistem & API Health Check
    </h3>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- GoPay Merchant API -->
        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-750 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center text-blue-600">
                    <i data-lucide="store" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-900 dark:text-white leading-none">GoPay API</p>
                    <span class="text-[9px] text-slate-400 dark:text-slate-500 mt-1 block">Merchant Sync</span>
                </div>
            </div>
            <span class="inline-flex px-2.5 py-1 rounded-full text-[9px] font-black bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-500/20">Sehat</span>
        </div>

        <!-- Fonnte WhatsApp API -->
        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-750 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center text-emerald-600">
                    <i data-lucide="message-square-text" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-900 dark:text-white leading-none">WhatsApp API</p>
                    <span class="text-[9px] text-slate-400 dark:text-slate-500 mt-1 block">Fonnte Gateway</span>
                </div>
            </div>
            <span class="inline-flex px-2.5 py-1 rounded-full text-[9px] font-black bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-500/20">Aktif</span>
        </div>

        <!-- Database Gateway Status -->
        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-750 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-purple-50 dark:bg-purple-500/10 flex items-center justify-center text-purple-600">
                    <i data-lucide="database" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-900 dark:text-white leading-none">Database</p>
                    <span class="text-[9px] text-slate-400 dark:text-slate-500 mt-1 block">MariaDB/MySQL</span>
                </div>
            </div>
            <span class="inline-flex px-2.5 py-1 rounded-full text-[9px] font-black bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-500/20">Optimal</span>
        </div>

        <!-- Auto-Sync Poller Status -->
        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-750 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-orange-50 dark:bg-orange-500/10 flex items-center justify-center text-orange-600">
                    <i data-lucide="refresh-cw" class="w-5 h-5"></i>
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-900 dark:text-white leading-none">Auto-Sync</p>
                    <span class="text-[9px] text-slate-400 dark:text-slate-500 mt-1 block">Poller Scheduler</span>
                </div>
            </div>
            <span class="inline-flex px-2.5 py-1 rounded-full text-[9px] font-black bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-500/20">Aktif</span>
        </div>
    </div>
</div>

<!-- Recent Activities Table list -->
<div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl shadow-sm p-6 mt-8">
    <h3 class="text-sm font-extrabold mb-5 flex items-center gap-2 text-slate-900 dark:text-white">
        <i data-lucide="activity" class="w-4 h-4 text-blue-600"></i> Mutasi Transaksi Terbaru (Live Stream)
    </h3>
    <div class="divide-y divide-slate-100 dark:divide-slate-800/80">
        @forelse(isset($transactions) ? $transactions->take(5) : $recentTransactions->take(5) as $tx)
            <div class="py-4 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-500 dark:text-slate-400 font-bold text-xs uppercase">
                        {{ substr($tx->team->name ?? 'T', 0, 2) }}
                    </div>
                    <div>
                        <div class="font-bold text-slate-900 dark:text-white text-sm leading-none">{{ $tx->team->name ?? 'Tim Terhapus' }}</div>
                        <span class="text-[10px] font-mono text-slate-400 dark:text-slate-500 mt-1.5 block">{{ $tx->trx_id }}</span>
                    </div>
                </div>
                <div class="text-right">
                    <div class="font-black text-sm text-slate-900 dark:text-white">Rp {{ number_format($tx->amount, 0, ',', '.') }}</div>
                    <span class="text-[10px] text-slate-400 dark:text-slate-550 mt-1 block">{{ $tx->created_at->diffForHumans() }}</span>
                </div>
            </div>
        @empty
            <p class="text-center text-slate-400 py-6 text-sm">Belum ada riwayat transaksi.</p>
        @endforelse
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
    const isDark = document.documentElement.classList.contains('dark');
    const labelColor = isDark ? '#94a3b8' : '#64748b';
    const gridColor = isDark ? '#334155' : '#f1f5f9';

    // 1. Bar Chart (Monthly)
    const ctxMonthly = document.getElementById('monthlyChart');
    if (ctxMonthly) {
        new Chart(ctxMonthly.getContext('2d'), {
            type: 'bar',
            data: {
                labels: @json($monthlyLabels ?? []),
                datasets: [{
                    label: 'Transaksi',
                    data: @json($monthlyCounts ?? []),
                    backgroundColor: '#2563eb', // Blue Navy/Royal
                    borderRadius: 8,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 1000,
                    easing: 'easeOutQuart'
                },
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        grid: { color: gridColor },
                        ticks: { color: labelColor, font: { family: 'Plus Jakarta Sans', size: 9 } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: labelColor, font: { family: 'Plus Jakarta Sans', size: 9 } }
                    }
                }
            }
        });
    }

    // 2. Line Chart (Weekly)
    const ctxWeekly = document.getElementById('weeklyChart');
    if(ctxWeekly) {
        new Chart(ctxWeekly.getContext('2d'), {
            type: 'line',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'Settle',
                    data: @json($weeklyCounts ?? []),
                    borderColor: '#2563eb',
                    borderWidth: 3,
                    pointBackgroundColor: '#2563eb',
                    pointHoverRadius: 6,
                    tension: 0.35,
                    fill: true,
                    backgroundColor: 'rgba(37, 99, 235, 0.08)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    duration: 1200,
                    easing: 'easeOutBack'
                },
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: {
                        grid: { color: gridColor },
                        ticks: { color: labelColor, font: { family: 'Plus Jakarta Sans', size: 9 } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { color: labelColor, font: { family: 'Plus Jakarta Sans', size: 9 } }
                    }
                }
            }
        });
    }
});
</script>
@endpush
