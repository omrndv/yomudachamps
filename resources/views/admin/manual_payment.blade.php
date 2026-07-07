@extends('qris.layout')
@section('title', 'Pembayaran Manual')

@section('content')
<div class="space-y-8 pb-12">
    <!-- Page Header -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 dark:text-white flex items-center gap-2">
                <i data-lucide="credit-card" class="w-6 h-6 text-blue-600"></i> Setelan &amp; Transaksi Pembayaran Manual
            </h2>
            <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Kelola QRIS statis, biaya admin, nominal unik, serta saldo masuk dalam satu dashboard terpadu.</p>
        </div>
        <a href="{{ route('qris.verify-payments') }}" class="bg-amber-500 hover:bg-amber-600 text-slate-900 font-extrabold py-2.5 px-5 rounded-2xl flex items-center gap-2 text-xs shadow-sm transition-all">
            <i data-lucide="phone" class="w-4 h-4"></i> Buka Verifikator Mobile (PWA)
        </a>
    </div>

    <!-- Quick Stats & Settings Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Balance Card -->
        <div class="bg-gradient-to-r from-slate-900 to-slate-950 border border-slate-800 rounded-3xl p-6 shadow-sm flex flex-col justify-between h-full">
            <div>
                <div class="flex justify-between items-center mb-6">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Total Pendapatan Terverifikasi</span>
                    <div class="w-8 h-8 bg-blue-500/10 rounded-xl flex items-center justify-center text-blue-500">
                        <i data-lucide="wallet" class="w-4.5 h-4.5"></i>
                    </div>
                </div>
                <h3 class="text-3xl font-black font-mono text-white tracking-tight">
                    Rp {{ number_format($totalBalance, 0, ',', '.') }}
                </h3>
                <p class="text-[10px] text-slate-500 mt-2 font-medium">Akumulasi seluruh transaksi manual lunas (PAID).</p>
            </div>
        </div>

        <!-- Configuration Settings Form -->
        <div class="lg:col-span-2 bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-6 shadow-sm">
            <h4 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider mb-6 flex items-center gap-2">
                <i data-lucide="sliders" class="w-4.5 h-4.5 text-blue-600"></i> Konfigurasi Pembayaran Manual
            </h4>

            <form action="{{ route('admin.manual-payment.settings') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Status Switch -->
                    <div class="flex flex-col justify-center">
                        <label class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2">Status Gerbang Pembayaran</label>
                        <label class="inline-flex items-center cursor-pointer mt-1">
                            <input type="checkbox" name="enabled" class="sr-only peer" {{ $settings['enabled'] ? 'checked' : '' }}>
                            <div class="relative w-11 h-6 bg-slate-200 peer-focus:outline-none dark:bg-slate-800 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all dark:border-slate-600 peer-checked:bg-blue-600"></div>
                            <span class="ms-3 text-xs font-bold text-slate-700 dark:text-slate-300">Aktifkan Manual Payment</span>
                        </label>
                    </div>

                    <!-- Admin Fee -->
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2">Biaya Admin (Rp)</label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-xs font-bold text-slate-400">Rp</span>
                            <input type="number" name="admin_fee" value="{{ $settings['admin_fee'] }}" required min="0"
                                class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white rounded-xl ps-10 pe-4 py-2.5 text-xs focus:outline-none focus:border-blue-500 transition-all font-semibold">
                        </div>
                    </div>

                    <!-- Min Code -->
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2">Kode Unik Min</label>
                        <input type="number" name="unique_min" value="{{ $settings['unique_min'] }}" required min="0"
                            class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white rounded-xl px-4 py-2.5 text-xs focus:outline-none focus:border-blue-500 transition-all font-semibold">
                    </div>

                    <!-- Max Code -->
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2">Kode Unik Max</label>
                        <input type="number" name="unique_max" value="{{ $settings['unique_max'] }}" required min="0"
                            class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white rounded-xl px-4 py-2.5 text-xs focus:outline-none focus:border-blue-500 transition-all font-semibold">
                    </div>
                </div>

                <!-- QRIS Upload & Preview -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-slate-100 dark:border-slate-800">
                    <div>
                        <label class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-2">Unggah Gambar QRIS Statis</label>
                        <input type="file" name="qris_image_file" accept="image/*"
                            class="block w-full text-xs text-slate-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-900/30 dark:file:text-blue-400">
                    </div>

                    @if($settings['qris_image'])
                    <div class="flex items-center gap-4 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 p-3 rounded-2xl">
                        <img src="{{ $settings['qris_image'] }}" alt="QRIS Statis" class="w-12 h-12 object-contain rounded-lg border border-slate-200 dark:border-slate-800 bg-white">
                        <div>
                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200 block">QRIS Statis Aktif</span>
                            <a href="{{ $settings['qris_image'] }}" target="_blank" class="text-[10px] text-blue-600 dark:text-blue-400 font-bold hover:underline flex items-center gap-1 mt-0.5">
                                Lihat Gambar Asli <i data-lucide="external-link" class="w-3 h-3"></i>
                            </a>
                        </div>
                    </div>
                    @endif
                </div>

                <div class="flex justify-end pt-4 border-t border-slate-100 dark:border-slate-800">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2.5 px-6 rounded-xl flex items-center gap-2 text-xs shadow-sm transition-all">
                        <i data-lucide="check" class="w-4 h-4"></i> Simpan Konfigurasi
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Riwayat Transaksi Manual -->
    <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl p-6 shadow-sm">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-6">
            <h4 class="text-sm font-bold text-slate-900 dark:text-white uppercase tracking-wider flex items-center gap-2">
                <i data-lucide="list-filter" class="w-4.5 h-4.5 text-blue-600"></i> Riwayat Pembayaran Manual
            </h4>
            <form action="{{ route('admin.manual-payment') }}" method="GET" class="flex gap-2 w-full md:w-auto">
                <input type="text" name="search" placeholder="Cari TRX ID atau nama tim..." value="{{ request('search') }}"
                    class="w-full md:w-64 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white rounded-xl px-4 py-2 text-xs focus:outline-none focus:border-blue-500 transition-all font-semibold">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold px-4 py-2 rounded-xl text-xs shadow-sm transition-all shrink-0">Cari</button>
            </form>
        </div>

        <div class="overflow-x-auto custom-scroll border border-slate-100 dark:border-slate-800 rounded-2xl">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-950 text-slate-400 dark:text-slate-500 uppercase font-black text-[10px] tracking-wider border-b border-slate-100 dark:border-slate-850">
                        <th class="py-4 px-6">TRX ID</th>
                        <th class="py-4 px-6">Tim</th>
                        <th class="py-4 px-6">Season</th>
                        <th class="py-4 px-6 text-right">Jumlah</th>
                        <th class="py-4 px-6 text-center">Status</th>
                        <th class="py-4 px-6 text-center">Bukti Transfer</th>
                        <th class="py-4 px-6 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                    @forelse($transactions as $tx)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-950/20 transition-all">
                        <td class="py-4 px-6 font-mono font-bold text-slate-500">{{ $tx->trx_id }}</td>
                        <td class="py-4 px-6">
                            <span class="font-bold text-slate-900 dark:text-white block">{{ $tx->team ? $tx->team->name : 'N/A' }}</span>
                            @if($tx->team)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $tx->team->wa_number) }}" target="_blank" class="text-emerald-500 font-bold hover:underline inline-flex items-center gap-1 mt-0.5">
                                <i data-lucide="phone" class="w-3.5 h-3.5"></i> {{ $tx->team->wa_number }}
                            </a>
                            @endif
                        </td>
                        <td class="py-4 px-6 text-slate-400">{{ ($tx->team && $tx->team->season) ? $tx->team->season->name : 'N/A' }}</td>
                        <td class="py-4 px-6 text-right font-mono font-bold text-slate-800 dark:text-slate-200">Rp {{ number_format($tx->amount, 0, ',', '.') }}</td>
                        <td class="py-4 px-6 text-center">
                            @if($tx->status === 'PAID')
                                <span class="bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-extrabold text-[9px] uppercase tracking-wider px-2.5 py-1 rounded-full">PAID</span>
                            @elseif($tx->status === 'CLAIMED')
                                <span class="bg-amber-500/10 text-amber-600 dark:text-amber-400 font-extrabold text-[9px] uppercase tracking-wider px-2.5 py-1 rounded-full">CLAIMED</span>
                            @elseif($tx->status === 'PENDING')
                                <span class="bg-slate-500/10 text-slate-500 font-extrabold text-[9px] uppercase tracking-wider px-2.5 py-1 rounded-full">PENDING</span>
                            @else
                                <span class="bg-rose-500/10 text-rose-500 font-extrabold text-[9px] uppercase tracking-wider px-2.5 py-1 rounded-full">EXPIRED</span>
                            @endif
                        </td>
                        <td class="py-4 px-6 text-center">
                            @if($tx->gopay_reference && str_starts_with($tx->gopay_reference, 'PROOFS/'))
                                @php
                                    $filename = str_replace('PROOFS/', '', $tx->gopay_reference);
                                @endphp
                                <a href="{{ asset('uploads/proofs/' . $filename) }}" target="_blank" class="border border-slate-200 dark:border-slate-800 hover:border-blue-500 dark:hover:border-blue-500 text-slate-600 dark:text-slate-300 font-extrabold px-3 py-1.5 rounded-xl transition-all inline-flex items-center gap-1.5">
                                    <i data-lucide="image" class="w-3.5 h-3.5"></i> Lihat Bukti
                                </a>
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                        </td>
                        <td class="py-4 px-6 text-center">
                            @if($tx->status === 'CLAIMED' || $tx->status === 'PENDING' || $tx->status === 'EXPIRED')
                            <div class="inline-flex gap-1.5">
                                <form action="{{ route('qris.settle', $tx->trx_id) }}" method="POST" onsubmit="return confirm('Setujui pembayaran tim ini?')">
                                    @csrf
                                    <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-white p-1.5 rounded-lg active:scale-95 transition-all" title="Setujui Pembayaran">
                                        <i data-lucide="check" class="w-4 h-4"></i>
                                    </button>
                                </form>
                                @if($tx->status === 'CLAIMED')
                                <form action="{{ route('qris.reject', $tx->trx_id) }}" method="POST" onsubmit="return confirm('Tolak bukti transfer tim ini?')">
                                    @csrf
                                    <button type="submit" class="bg-rose-500 hover:bg-rose-600 text-white p-1.5 rounded-lg active:scale-95 transition-all" title="Tolak Bukti">
                                        <i data-lucide="x" class="w-4 h-4"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                            @else
                                <span class="text-slate-400">-</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-8 text-slate-400">
                            <i data-lucide="inbox" class="w-8 h-8 mx-auto text-slate-300 dark:text-slate-700 mb-2"></i>
                            <p class="mb-0 text-xs font-semibold">Belum ada transaksi pembayaran manual.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6 flex justify-center">
            {{ $transactions->links() }}
        </div>
    </div>
</div>
@endsection
