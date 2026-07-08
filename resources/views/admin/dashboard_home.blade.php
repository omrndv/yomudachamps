@extends('layouts.admin')

@section('content')
<!-- Local ApexCharts -->
<script src="{{ asset('js/apexcharts.min.js') }}"></script>

@if(session('welcome_alert'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true,
            background: '#ffffff',
            color: '#1e293b',
        });
        Toast.fire({
            icon: 'success',
            title: "{{ session('welcome_alert') }}"
        });
    });
</script>
@endif

<div class="container-fluid py-4" style="background-color: #f8fafc; min-height: 100vh;">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-slate-800 mb-1" style="font-size: 1.75rem; letter-spacing: -0.5px;">
                Dashboard Overview
            </h2>
            <p class="text-secondary mb-0" style="font-size: 0.9rem;">
                Analisis data pendaftaran turnamen Yomuda Championship dan performa keuangan.
            </p>
        </div>
        <div class="d-none d-md-flex gap-2">
            <span class="badge bg-white text-dark border border-light-subtle shadow-sm px-3 py-2 rounded-pill d-flex align-items-center gap-2" style="font-size: 0.8rem;">
                <span class="bg-success rounded-circle" style="width: 8px; height: 8px; display: inline-block;"></span>
                Sistem Aktif
            </span>
            <span class="badge bg-dark text-white shadow-sm px-3 py-2 rounded-pill" style="font-size: 0.8rem;">
                {{ now()->translatedFormat('l, d F Y') }}
            </span>
        </div>
    </div>

    {{-- Date Filter Bar --}}
    <div class="card border-0 shadow-sm rounded-4 p-3 mb-4 bg-white" style="border: 1px solid rgba(0, 0, 0, 0.06) !important;">
        <form action="{{ route('admin.dashboard.home') }}" method="GET" class="row g-3 align-items-center">
            <div class="col-12 col-lg-auto text-center text-lg-start">
                <span class="fw-bold text-secondary text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                    <i class="bi bi-calendar3 me-1 text-warning"></i> Rentang Waktu:
                </span>
            </div>
            <div class="col-6 col-sm-6 col-md-3 col-lg-2">
                <div class="input-group input-group-sm rounded-3 overflow-hidden border border-light-subtle bg-light">
                    <span class="input-group-text bg-light border-0 text-muted small" style="font-size: 0.7rem; padding: 0 8px;">Mulai</span>
                    <input type="date" name="start_date" class="form-control border-0 bg-light text-dark shadow-none" value="{{ $start_date->format('Y-m-d') }}" style="font-size: 0.8rem; height: 34px; padding-left: 4px;">
                </div>
            </div>
            <div class="col-6 col-sm-6 col-md-3 col-lg-2">
                <div class="input-group input-group-sm rounded-3 overflow-hidden border border-light-subtle bg-light">
                    <span class="input-group-text bg-light border-0 text-muted small" style="font-size: 0.7rem; padding: 0 8px;">Selesai</span>
                    <input type="date" name="end_date" class="form-control border-0 bg-light text-dark shadow-none" value="{{ $end_date->format('Y-m-d') }}" style="font-size: 0.8rem; height: 34px; padding-left: 4px;">
                </div>
            </div>
            <div class="col-12 col-md-6 col-lg-auto d-flex gap-2 justify-content-center mt-2 mt-md-0">
                <button type="submit" class="btn btn-warning btn-sm fw-bold text-dark rounded-pill px-4 shadow-sm hover-gold" style="height: 34px; font-size: 0.8rem; white-space: nowrap;">
                    Filter Harian
                </button>
                <a href="{{ route('admin.dashboard.home') }}" class="btn btn-outline-secondary btn-sm fw-bold rounded-pill px-4 d-flex align-items-center justify-content-center" style="height: 34px; font-size: 0.8rem; white-space: nowrap;">
                    Reset (7 Hari)
                </a>
            </div>
            <div class="col-12 col-md-6 col-lg-auto ms-lg-auto d-flex justify-content-center mt-2 mt-lg-0">
                <button type="button" class="btn btn-success btn-sm fw-bold text-white rounded-pill px-4 shadow-sm d-flex align-items-center justify-content-center gap-1.5 w-100 w-sm-auto hover-emerald" style="height: 34px; font-size: 0.8rem; white-space: nowrap;" data-bs-toggle="modal" data-bs-target="#modalAiRecapWinners">
                    <i class="bi bi-stars"></i> AI Rekap Pemenang
                </button>
            </div>
        </form>
    </div>

    {{-- Stats Cards Grid --}}
    <div class="row g-3 mb-4">
        {{-- Card 1: Total Pendapatan --}}
        <div class="col-md-6 col-lg-4">
            <div class="card card-stats border-0 p-3 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-uppercase text-secondary fw-bold mb-1" style="font-size: 0.7rem; letter-spacing: 0.7px;">Pendapatan Rentang Tanggal</p>
                        <h3 class="fw-bold text-success mb-0" style="font-size: 1.5rem; letter-spacing: -0.5px;">
                            Rp {{ number_format($total_income, 0, ',', '.') }}
                        </h3>
                    </div>
                    <div class="icon-shape text-white shadow-sm" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
                        <i class="bi bi-wallet2 fs-5"></i>
                    </div>
                </div>
                <div class="mt-3 pt-2 border-top border-light d-flex align-items-center justify-content-between" style="font-size: 0.75rem;">
                    <span class="text-muted"><i class="bi bi-check-circle-fill text-success me-1"></i>Pembayaran Lunas</span>
                    <span class="text-secondary fw-bold">Global: Rp {{ number_format($global_income, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        {{-- Card 2: Tim Lunas --}}
        <div class="col-md-6 col-lg-4">
            <div class="card card-stats border-0 p-3 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-uppercase text-secondary fw-bold mb-1" style="font-size: 0.7rem; letter-spacing: 0.7px;">Tim Lunas Rentang Tanggal</p>
                        <h3 class="fw-bold text-dark mb-0" style="font-size: 1.5rem; letter-spacing: -0.5px;">
                            {{ $total_paid_teams }} <span class="fs-6 fw-normal text-muted">Tim</span>
                        </h3>
                    </div>
                    <div class="icon-shape text-white shadow-sm" style="background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);">
                        <i class="bi bi-shield-check fs-5"></i>
                    </div>
                </div>
                <div class="mt-3 pt-2 border-top border-light d-flex align-items-center justify-content-between" style="font-size: 0.75rem;">
                    <span class="text-muted"><i class="bi bi-info-circle text-primary me-1"></i>Status PAID</span>
                    <span class="text-secondary fw-bold">Global: {{ $global_paid_teams }} / {{ $global_registered_teams }} Tim</span>
                </div>
            </div>
        </div>

        {{-- Card 4: Turnamen Aktif --}}
        <div class="col-md-6 col-lg-4">
            <div class="card card-stats border-0 p-3 h-100">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="text-uppercase text-secondary fw-bold mb-1" style="font-size: 0.7rem; letter-spacing: 0.7px;">Turnamen Aktif Saat Ini</p>
                        <h3 class="fw-bold text-dark mb-0" style="font-size: 1.5rem; letter-spacing: -0.5px;">
                            {{ $total_active_seasons }} <span class="fs-6 fw-normal text-muted">Season</span>
                        </h3>
                    </div>
                    <div class="icon-shape text-dark shadow-sm" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
                        <i class="bi bi-trophy fs-5 text-white"></i>
                    </div>
                </div>
                <div class="mt-3 pt-2 border-top border-light d-flex align-items-center" style="font-size: 0.75rem;">
                    <span class="text-warning fw-bold me-1"><i class="bi bi-lightning-fill"></i></span>
                    <span class="text-muted">Turnamen sedang berjalan</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Row: Charts --}}
    <div class="row g-4 mb-4">
        {{-- Tren Registrasi & Pembayaran (Area Chart) --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm p-4 rounded-4 bg-white h-100" style="border: 1px solid rgba(0, 0, 0, 0.06) !important;">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <div>
                        <h5 class="fw-bold text-dark mb-1" id="chartTitle">Tren Aktivitas Turnamen</h5>
                        <p class="text-muted mb-0" style="font-size: 0.8rem;" id="chartSubTitle">Statistik registrasi tim dan pelunasan pembayaran (7 hari terakhir).</p>
                    </div>
                    {{-- Chart Toggle Buttons --}}
                    <div class="btn-group btn-group-sm bg-light p-1 rounded-pill" role="group">
                        <button type="button" class="btn btn-warning rounded-pill px-3 py-1 fw-bold text-dark shadow-sm transition-all" id="btnShowActivity" onclick="switchChart('activity')">
                            Aktivitas Tim
                        </button>
                        <button type="button" class="btn text-secondary rounded-pill px-3 py-1 fw-bold transition-all" id="btnShowIncome" onclick="switchChart('income')">
                            Pendapatan (Rp)
                        </button>
                    </div>
                </div>
                <div id="trendChart" style="min-height: 320px;"></div>
            </div>
        </div>

        {{-- Status Pembayaran (Doughnut Chart) --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm p-4 rounded-4 bg-white h-100" style="border: 1px solid rgba(0, 0, 0, 0.06) !important;">
                <h5 class="fw-bold text-dark mb-1">Rasio Pembayaran</h5>
                <p class="text-muted mb-3" style="font-size: 0.8rem;">Komparasi status Lunas (PAID) vs Pending.</p>
                
                @if($total_registered_teams > 0)
                    <div class="d-flex justify-content-center align-items-center" style="min-height: 250px;">
                        <div id="statusChart" class="w-100"></div>
                    </div>
                @else
                    <div class="d-flex flex-column justify-content-center align-items-center py-5 text-muted" style="min-height: 250px;">
                        <i class="bi bi-pie-chart fs-1 mb-2 opacity-50"></i>
                        <span style="font-size: 0.85rem;">Belum ada data pendaftar</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Layout Utama: Keterisian & Pembayaran Baru --}}
    <div class="row g-4">
        {{-- Kiri: Perkembangan Slot Turnamen --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4" style="border: 1px solid rgba(0, 0, 0, 0.06) !important;">
                <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom border-light">
                    <h5 class="fw-bold text-dark mb-0">
                        Keterisian Slot Turnamen
                    </h5>
                    <a href="{{ route('admin.seasons') }}" class="btn btn-link text-warning text-decoration-none fw-bold p-0" style="font-size: 0.8rem;">
                        Semua Season <i class="bi bi-arrow-right"></i>
                    </a>
                </div>

                {{-- Filters Bar --}}
                <div class="row g-2 mb-4 align-items-center">
                    <div class="col-md-5">
                        <div class="search-box-season">
                            <i class="bi bi-search"></i>
                            <input type="text" id="searchSeasonHome" placeholder="Cari nama season...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <select id="filterStatusHome" class="form-select form-select-sm rounded-3 border-light-subtle shadow-none bg-white" style="font-size: 0.85rem; height: 38px;">
                            <option value="ACTIVE" selected>Status: Aktif</option>
                            <option value="FINISHED">Status: Selesai</option>
                            <option value="ALL">Status: Semua</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select id="filterSelectSeasonHome" class="form-select form-select-sm rounded-3 border-light-subtle shadow-none bg-white" style="font-size: 0.85rem; height: 38px;">
                            <option value="ALL" selected>Pilih Season: Semua</option>
                            @foreach($seasons as $s)
                            <option value="{{ $s->id }}">{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;">
                        <thead class="bg-light">
                            <tr class="fw-bold text-secondary text-uppercase" style="font-size: 0.75rem; border-bottom: 2px solid #f1f5f9;">
                                <th class="ps-3 py-3 border-0">Nama Season</th>
                                <th class="py-3 border-0">Status</th>
                                <th class="py-3 border-0">Harga</th>
                                <th class="py-3 border-0" style="min-width: 180px;">Progress Slot</th>
                                <th class="py-3 text-center border-0" width="100">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($seasons as $season)
                            <tr class="season-row" data-id="{{ $season->id }}" data-status="{{ $season->status }}" data-name="{{ strtolower($season->name) }}" style="border-bottom: 1px solid #f8fafc;">
                                <td class="ps-3 fw-bold text-dark py-3">
                                    {{ $season->name }}
                                </td>
                                <td>
                                    <span class="badge {{ $season->status == 'ACTIVE' ? 'bg-success-subtle text-success border border-success-subtle' : 'bg-secondary-subtle text-secondary border border-secondary-subtle' }} px-3 py-2 rounded-pill text-uppercase" style="font-size: 0.65rem;">
                                        {{ $season->status }}
                                    </span>
                                </td>
                                <td>
                                    <span class="fw-bold text-slate-700">Rp {{ number_format($season->price, 0, ',', '.') }}</span>
                                </td>
                                <td>
                                    @php
                                        $percent = $season->slot > 0 ? round(($season->teams_count / $season->slot) * 100) : 0;
                                        $barColor = 'bg-primary';
                                        if ($percent >= 90) $barColor = 'bg-danger';
                                        elseif ($percent >= 60) $barColor = 'bg-warning';
                                    @endphp
                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                        <span class="fw-bold text-muted" style="font-size: 0.75rem;">{{ $season->teams_count }} / {{ $season->slot }} Slot</span>
                                        <span class="fw-bold text-dark" style="font-size: 0.75rem;">{{ $percent }}%</span>
                                    </div>
                                    <div class="progress rounded-pill bg-light shadow-none" style="height: 6px; background-color: #f1f5f9 !important;">
                                        <div class="progress-bar rounded-pill {{ $barColor }}" role="progressbar" style="width: {{ $percent }}%" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <a href="{{ route('admin.dashboard', $season->id) }}" class="btn btn-warning btn-sm fw-bold rounded-pill text-dark px-3 shadow-sm" style="font-size: 0.75rem; letter-spacing: 0.3px;">
                                        KELOLA
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Belum ada data season dibuat.</td>
                            </tr>
                            @endforelse
                            <tr id="noSearchResultHome" class="d-none">
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="bi bi-search fs-3 d-block mb-2 text-warning opacity-75"></i>
                                    <span class="fw-bold">Tidak ada season yang cocok</span>
                                    <p class="text-secondary mb-0 small mt-1">Silakan sesuaikan kata kunci pencarian atau pilihan filter Anda.</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Kanan: Pembayaran Terbaru & Pintasan Cepat --}}
        <div class="col-lg-4">
            {{-- Pembayaran Terbaru --}}
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
                <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom border-light">
                    <i class="bi bi-clock-history text-success me-2"></i> Pembayaran Terbaru
                </h5>

                <div class="list-group list-group-flush">
                    @forelse($recent_payments as $pay)
                    <div class="list-group-item px-0 py-3 border-light bg-transparent">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="text-truncate me-2">
                                <span class="fw-bold d-block text-dark text-uppercase text-truncate" style="max-width: 180px; font-size: 0.85rem;">{{ $pay->name }}</span>
                                <small class="text-muted text-uppercase" style="font-size: 0.65rem;">{{ $pay->season->name }} • {{ $pay->payment_method ?? 'QRIS/VA' }}</small>
                            </div>
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill" style="font-size: 0.6rem;">
                                PAID
                            </span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <span class="small text-muted" style="font-size: 0.7rem;">{{ $pay->updated_at->diffForHumans() }}</span>
                            <span class="fw-bold text-success" style="font-size: 0.8rem;">Rp {{ number_format($pay->season->price, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-4 text-muted bg-transparent" style="font-size: 0.8rem;">
                        Belum ada aktivitas pembayaran terbaru.
                    </div>
                    @endforelse
                </div>
            </div>

            {{-- Pintasan Cepat --}}
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
                <h5 class="fw-bold text-dark mb-3 pb-2 border-bottom border-light">
                    <i class="bi bi-lightning-fill text-warning me-2"></i> Pintasan Cepat
                </h5>

                <div class="d-grid gap-2">
                    <a href="{{ route('admin.seasons') }}" class="btn btn-light text-start p-3 border-0 bg-light rounded-3 text-dark fw-bold hover-bg d-flex align-items-center justify-content-between" style="font-size: 0.85rem;">
                        <span><i class="bi bi-trophy text-warning me-2"></i> Kelola Season & Buat Baru</span>
                        <i class="bi bi-chevron-right text-muted fs-7"></i>
                    </a>
                    <a href="{{ route('admin.notes.index') }}" class="btn btn-light text-start p-3 border-0 bg-light rounded-3 text-dark fw-bold hover-bg d-flex align-items-center justify-content-between" style="font-size: 0.85rem;">
                        <span><i class="bi bi-sticky text-info me-2"></i> Tulis Catatan Admin</span>
                        <i class="bi bi-chevron-right text-muted fs-7"></i>
                    </a>
                    <a href="{{ route('admin.settings') }}" class="btn btn-light text-start p-3 border-0 bg-light rounded-3 text-dark fw-bold hover-bg d-flex align-items-center justify-content-between" style="font-size: 0.85rem;">
                        <span><i class="bi bi-gear text-secondary me-2"></i> Pengaturan Sistem</span>
                        <i class="bi bi-chevron-right text-muted fs-7"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Styling Khusus --}}
<style>
    .card-stats {
        background: #ffffff;
        border: 1px solid rgba(241, 245, 249, 0.8) !important;
        border-radius: 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.01);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .card-stats:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 20px -5px rgba(0, 0, 0, 0.04), 0 8px 8px -5px rgba(0, 0, 0, 0.02);
        border-color: rgba(226, 232, 240, 0.8) !important;
    }
    .icon-shape {
        width: 44px;
        height: 44px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
    }
    .hover-bg {
        transition: all 0.2s ease;
    }
    .hover-bg:hover {
        background-color: #f1f5f9 !important;
        transform: translateX(4px);
    }
    .search-box-season {
        display: flex;
        align-items: center;
        background: #f1f5f9;
        border: 1px solid transparent;
        border-radius: 10px;
        padding: 2px 12px;
        transition: all 0.2s ease;
    }
    .search-box-season:focus-within {
        background: #ffffff;
        border-color: #f59e0b;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.1);
    }
    .search-box-season input {
        border: 0;
        background: transparent;
        font-size: 0.85rem;
        padding: 8px 6px;
        outline: none;
        width: 100%;
        color: #1e293b;
    }
    .search-box-season i {
        color: #94a3b8;
    }
</style>

{{-- Script ApexCharts --}}
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Area Chart: Tren Pendaftaran & Pembayaran
        var trendOptions = {
            series: [{
                name: 'Pendaftar (Baru)',
                data: @json($chart_registered)
            }, {
                name: 'Lunas (PAID)',
                data: @json($chart_paid)
            }],
            chart: {
                type: 'area',
                height: 320,
                fontFamily: 'Plus Jakarta Sans, sans-serif',
                toolbar: { show: false },
                zoom: { enabled: false }
            },
            colors: ['#3b82f6', '#10b981'], // Blue, Emerald
            dataLabels: { enabled: false },
            stroke: {
                curve: 'smooth',
                width: 2.5
            },
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.35,
                    opacityTo: 0.05,
                    stops: [0, 90, 100]
                }
            },
            xaxis: {
                categories: @json($chart_labels),
                axisBorder: { show: false },
                axisTicks: { show: false },
                labels: {
                    style: {
                        colors: '#64748b',
                        fontSize: '11px'
                    }
                }
            },
            yaxis: {
                labels: {
                    style: {
                        colors: '#64748b',
                        fontSize: '11px'
                    },
                    formatter: function(val) {
                        return Math.round(val) + ' Tim';
                    }
                }
            },
            grid: {
                borderColor: '#f1f5f9',
                strokeDashArray: 4,
                padding: {
                    right: 20,
                    left: 10
                }
            },
            legend: {
                position: 'top',
                horizontalAlign: 'right',
                fontWeight: 600,
                fontSize: '12px',
                labels: { colors: '#334155' },
                markers: {
                    radius: 12,
                    offsetY: 1
                },
                itemMargin: {
                    horizontal: 12
                }
            },
            tooltip: {
                theme: 'light',
                x: { show: true },
                y: {
                    formatter: function(val) {
                        return val + ' Tim';
                    }
                }
            }
        };

        var trendChart = new ApexCharts(document.querySelector("#trendChart"), trendOptions);
        trendChart.render();

        // Switch chart logic
        let chartMode = 'activity';
        const dataActivity = [
            {
                name: 'Pendaftar (Baru)',
                data: @json($chart_registered)
            },
            {
                name: 'Lunas (PAID)',
                data: @json($chart_paid)
            }
        ];
        const dataIncome = [
            {
                name: 'Pendapatan (Rp)',
                data: @json($chart_income)
            }
        ];

        window.switchChart = function(mode) {
            if (mode === chartMode) return;
            chartMode = mode;

            const btnActivity = document.getElementById('btnShowActivity');
            const btnIncome = document.getElementById('btnShowIncome');
            const title = document.getElementById('chartTitle');
            const subTitle = document.getElementById('chartSubTitle');

            if (mode === 'activity') {
                btnActivity.className = "btn btn-warning rounded-pill px-3 py-1 fw-bold text-dark shadow-sm transition-all";
                btnIncome.className = "btn text-secondary rounded-pill px-3 py-1 fw-bold transition-all";
                title.innerText = "Tren Aktivitas Turnamen";
                subTitle.innerText = "Statistik registrasi tim dan pelunasan pembayaran (7 hari terakhir).";
                
                trendChart.updateOptions({
                    colors: ['#3b82f6', '#10b981'], // Blue, Emerald
                    yaxis: {
                        labels: {
                            formatter: function(val) {
                                return Math.round(val) + ' Tim';
                            }
                        }
                    },
                    tooltip: {
                        y: {
                            formatter: function(val) {
                                return val + ' Tim';
                            }
                        }
                    }
                });
                trendChart.updateSeries(dataActivity);
            } else {
                btnIncome.className = "btn btn-warning rounded-pill px-3 py-1 fw-bold text-dark shadow-sm transition-all";
                btnActivity.className = "btn text-secondary rounded-pill px-3 py-1 fw-bold transition-all";
                title.innerText = "Tren Pendapatan Harian";
                subTitle.innerText = "Grafik total nominal pembayaran lunas yang masuk per hari (7 hari terakhir).";

                trendChart.updateOptions({
                    colors: ['#10b981'], // Emerald
                    yaxis: {
                        labels: {
                            formatter: function(val) {
                                return 'Rp ' + val.toLocaleString('id-ID');
                            }
                        }
                    },
                    tooltip: {
                        y: {
                            formatter: function(val) {
                                return 'Rp ' + val.toLocaleString('id-ID');
                            }
                        }
                    }
                });
                trendChart.updateSeries(dataIncome);
            }
        };

        // Doughnut Chart: Rasio Status Pembayaran
        @if($total_registered_teams > 0)
        var statusOptions = {
            series: [{{ $total_paid_teams }}, {{ $total_registered_teams - $total_paid_teams }}],
            chart: {
                type: 'donut',
                height: 280,
                fontFamily: 'Plus Jakarta Sans, sans-serif'
            },
            labels: ['Lunas (PAID)', 'Pending (PENDING)'],
            colors: ['#10b981', '#f59e0b'], // Emerald, Amber
            legend: {
                position: 'bottom',
                fontSize: '12px',
                fontWeight: 500,
                labels: { colors: '#475569' },
                markers: { radius: 12 },
                itemMargin: {
                    vertical: 4
                }
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: '72%',
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: 'Total Pendaftar',
                                color: '#64748b',
                                fontSize: '12px',
                                fontWeight: 500,
                                formatter: function (w) {
                                    return w.globals.seriesTotals.reduce((a, b) => a + b, 0) + ' Tim';
                                }
                            },
                            value: {
                                color: '#1e293b',
                                fontSize: '20px',
                                fontWeight: 700,
                                offsetY: 4
                            }
                        }
                    }
                }
            },
            dataLabels: { enabled: false },
            stroke: { width: 0 }
        };

        var statusChart = new ApexCharts(document.querySelector("#statusChart"), statusOptions);
        statusChart.render();
        @endif

        // Client-Side Season Filtering for Dashboard Home Table
        function filterSeasonsHome() {
            let searchQuery = document.getElementById('searchSeasonHome').value.toLowerCase();
            let selectedStatus = document.getElementById('filterStatusHome').value;
            let selectedSeasonId = document.getElementById('filterSelectSeasonHome').value;
            let seasonRows = document.querySelectorAll('.season-row');
            let visibleCount = 0;

            seasonRows.forEach(row => {
                let name = row.getAttribute('data-name');
                let status = row.getAttribute('data-status');
                let id = row.getAttribute('data-id');

                let matchesSearch = name.includes(searchQuery);
                let matchesStatus = (selectedStatus === 'ALL' || status === selectedStatus);
                let matchesSeason = (selectedSeasonId === 'ALL' || id === selectedSeasonId);

                if (matchesSearch && matchesStatus && matchesSeason) {
                    row.style.display = "";
                    visibleCount++;
                } else {
                    row.style.display = "none";
                }
            });

            let noResultRow = document.getElementById('noSearchResultHome');
            if (noResultRow) {
                if (visibleCount === 0) {
                    noResultRow.classList.remove('d-none');
                } else {
                    noResultRow.classList.add('d-none');
                }
            }
        }

        // Attach event listeners for dashboard home
        const searchInput = document.getElementById('searchSeasonHome');
        const statusSelect = document.getElementById('filterStatusHome');
        const seasonSelect = document.getElementById('filterSelectSeasonHome');

        if (searchInput && statusSelect && seasonSelect) {
            searchInput.addEventListener('keyup', filterSeasonsHome);
            statusSelect.addEventListener('change', filterSeasonsHome);
            seasonSelect.addEventListener('change', filterSeasonsHome);
            
            // Run filter on initial page load to only show ACTIVE seasons by default
            filterSeasonsHome();
        }
    });
</script>

{{-- MODAL AI RECAP WINNERS --}}
<div class="modal fade" id="modalAiRecapWinners" tabindex="-1" aria-hidden="true" style="z-index: 1055;">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 text-dark">
            <div class="modal-header border-bottom border-light p-3">
                <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                    <i class="bi bi-stars text-success"></i> AI Rangkuman & Analisis Juara Season
                </h5>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" style="max-height: 70vh; overflow-y: auto;">
                <div id="aiRecapContent" class="lh-base">
                    <div class="text-center py-5">
                        <div class="spinner-border text-success mb-3" role="status" style="width: 2.5rem; height: 2.5rem;"></div>
                        <p class="text-secondary fw-semibold mb-0">AI sedang mengumpulkan data pemenang dan menyusun laporan analisis...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top border-light p-3">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-semibold small" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script>
    document.getElementById('modalAiRecapWinners').addEventListener('show.bs.modal', function () {
        const contentEl = document.getElementById('aiRecapContent');
        
        contentEl.innerHTML = `
            <div class="text-center py-5">
                <div class="spinner-border text-success mb-3" role="status" style="width: 2.5rem; height: 2.5rem;"></div>
                <p class="text-secondary fw-semibold mb-0">AI sedang menganalisis data bagan turnamen dan merekap sejarah pemenang...</p>
            </div>
        `;
        
        fetch("{{ route('admin.ai.recap_winners') }}")
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    if (data.markdown) {
                        contentEl.innerHTML = `<div class="markdown-body text-slate-800" style="font-size: 0.9rem;">${marked.parse(data.markdown)}</div>`;
                    } else if (data.html) {
                        contentEl.innerHTML = data.html;
                    }
                } else {
                    contentEl.innerHTML = data.html || `<div class="alert alert-danger border-0 rounded-3 mb-0"><i class="bi bi-exclamation-triangle-fill me-2"></i>Gagal memuat rekap pemenang AI.</div>`;
                }
            })
            .catch(err => {
                console.error(err);
                contentEl.innerHTML = `<div class="alert alert-danger border-0 rounded-3 mb-0"><i class="bi bi-exclamation-triangle-fill me-2"></i>Terjadi kesalahan koneksi saat memanggil AI.</div>`;
            });
    });
</script>
@endsection
