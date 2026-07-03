@extends('qris.layout')
@section('title', 'Dashboard')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="space-y-8">
    <!-- Stats Summary Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Card 1 -->
        <div class="bg-blue-600 text-white rounded-3xl p-6 shadow-md relative overflow-hidden">
            <div class="absolute -right-6 -bottom-6 w-24 h-24 bg-white/10 rounded-full blur-xl"></div>
            <div class="flex justify-between items-start mb-4">
                <div class="text-[10px] font-bold text-blue-100 uppercase tracking-wider">Total Volume Sukses</div>
            </div>
            <div class="text-2xl font-black font-mono">
                Rp {{ number_format($globalStats->total_volume, 0, ',', '.') }}
            </div>
        </div>

        <!-- Card 2 -->
        <div class="bg-white border border-gray-200 rounded-3xl p-6 shadow-sm">
            <div class="flex justify-between items-start mb-4">
                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Transaksi Sukses</div>
            </div>
            <div class="text-2xl font-black text-gray-900">
                {{ $globalStats->paid_count }} Trx
            </div>
        </div>

        <!-- Card 3 -->
        <div class="bg-white border border-gray-200 rounded-3xl p-6 shadow-sm">
            <div class="flex justify-between items-start mb-4">
                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Transaksi Pending</div>
            </div>
            <div class="text-2xl font-black text-gray-900">
                {{ $globalStats->pending_count }} Trx
            </div>
        </div>

        <!-- Card 4 -->
        <div class="bg-white border border-gray-200 rounded-3xl p-6 shadow-sm">
            <div class="flex justify-between items-start mb-4">
                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Transaksi Kedaluwarsa</div>
            </div>
            <div class="text-2xl font-black text-gray-900">
                {{ $globalStats->expired_count }} Trx
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Chart 1 -->
        <div class="bg-white border border-gray-200 rounded-3xl p-6 shadow-sm">
            <h3 class="text-sm font-extrabold text-gray-900 mb-6">Analisis Transaksi Bulanan</h3>
            <div class="h-56 relative w-full">
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>

        <!-- Chart 2 -->
        <div class="bg-white border border-gray-200 rounded-3xl p-6 shadow-sm">
            <h3 class="text-sm font-extrabold text-gray-900 mb-6">Performa Transaksi Mingguan</h3>
            <div class="h-56 relative w-full">
                <canvas id="weeklyChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Activities Table list -->
    <div class="bg-white border border-gray-200 rounded-3xl shadow-sm p-6">
        <h3 class="text-sm font-extrabold mb-5 flex items-center gap-2 text-gray-900">
            Mutasi Transaksi Terbaru
        </h3>
        <div class="divide-y divide-gray-100">
            @forelse($recentTransactions as $tx)
                <div class="py-4 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-gray-100 flex items-center justify-center text-gray-500 font-bold text-xs uppercase">
                            {{ substr($tx->team->name ?? 'T', 0, 2) }}
                        </div>
                        <div>
                            <div class="font-bold text-gray-900 text-sm leading-none">{{ $tx->team->name ?? 'Tim Terhapus' }}</div>
                            <span class="text-[10px] font-mono text-gray-400 mt-1.5 block">{{ $tx->trx_id }}</span>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="font-black text-sm text-gray-900">Rp {{ number_format($tx->amount, 0, ',', '.') }}</div>
                        <span class="text-[10px] text-gray-400 mt-1 block">{{ $tx->created_at->diffForHumans() }}</span>
                    </div>
                </div>
            @empty
                <p class="text-center text-gray-400 py-6 text-sm">Belum ada riwayat transaksi.</p>
            @endforelse
        </div>
    </div>
</div>

<script>
    // Inisialisasi Chart.js
    const ctxMonthly = document.getElementById('monthlyChart').getContext('2d');
    new Chart(ctxMonthly, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_reverse($monthlyLabels)) !!},
            datasets: [{
                label: 'Transaksi Sukses',
                data: {!! json_encode(array_reverse($monthlyCounts)) !!},
                backgroundColor: '#2563eb',
                borderRadius: 4
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });

    const ctxWeekly = document.getElementById('weeklyChart').getContext('2d');
    new Chart(ctxWeekly, {
        type: 'line',
        data: {
            labels: ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'],
            datasets: [{
                label: 'Transaksi Sukses',
                data: {!! json_encode($weeklyCounts) !!},
                borderColor: '#10b981',
                tension: 0.4,
                fill: false
            }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });
</script>
@endsection
