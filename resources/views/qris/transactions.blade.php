@extends('qris.layout')
@section('title', 'Daftar Transaksi')

@section('content')
<div class="bg-white border border-gray-200 rounded-3xl shadow-sm overflow-hidden">
    <div class="p-6 border-b border-gray-100 flex items-center justify-between">
        <h3 class="text-lg font-extrabold text-gray-900">Riwayat Transaksi QRIS</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left text-sm border-collapse">
            <thead>
                <tr class="bg-gray-50 border-b border-gray-200 text-gray-500 font-bold uppercase text-[10px] tracking-wider">
                    <th class="py-4 px-6">ID / Referensi</th>
                    <th class="py-4 px-6">Nama Tim</th>
                    <th class="py-4 px-6">Nominal</th>
                    <th class="py-4 px-6">Batas Pembayaran</th>
                    <th class="py-4 px-6">Status</th>
                    <th class="py-4 px-6 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($transactions as $tx)
                    <tr class="hover:bg-gray-50 transition-all">
                        <td class="py-4 px-6 font-mono text-xs text-blue-600 font-bold">{{ $tx->trx_id }}</td>
                        <td class="py-4 px-6">
                            <div class="font-bold text-gray-900">{{ $tx->team->name ?? 'Tim Terhapus' }}</div>
                            <span class="text-[10px] text-gray-400 mt-1 block">Season: {{ $tx->team->season->name ?? '-' }}</span>
                        </td>
                        <td class="py-4 px-6">
                            <div class="font-bold text-gray-900">Rp {{ number_format($tx->amount, 0, ',', '.') }}</div>
                            <span class="text-[10px] text-gray-400 mt-1 block">Kode Unik: +{{ $tx->unique_code }}</span>
                        </td>
                        <td class="py-4 px-6 text-xs font-semibold">
                            @if($tx->status === 'PENDING')
                                <span class="text-blue-600">Menunggu...</span>
                            @else
                                <span class="text-gray-500">{{ $tx->expires_at->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }}</span>
                            @endif
                        </td>
                        <td class="py-4 px-6">
                            @if($tx->status === 'PAID')
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-green-100 text-green-700">PAID</span>
                            @elseif($tx->status === 'CLAIMED')
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700">CLAIMED</span>
                            @elseif($tx->status === 'PENDING')
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-yellow-100 text-yellow-700">PENDING</span>
                            @else
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-gray-100 text-gray-600">EXPIRED</span>
                            @endif
                        </td>
                        <td class="py-4 px-6 text-right space-x-2">
                            @if($tx->status === 'PENDING' || $tx->status === 'CLAIMED')
                                <form action="{{ route('qris.settle', $tx->trx_id) }}" method="POST" onsubmit="return confirm('Selesaikan transaksi ini manual?');" class="inline-block">
                                    @csrf
                                    <button type="submit" class="bg-green-600 hover:bg-green-500 text-white text-xs font-bold px-3 py-2 rounded-lg">Settle</button>
                                </form>
                            @endif
                            <form action="{{ route('qris.delete', $tx->trx_id) }}" method="POST" onsubmit="return confirm('Hapus transaksi ini?');" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-50 text-red-600 hover:bg-red-500 hover:text-white text-xs font-bold px-3 py-2 rounded-lg">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-gray-400 text-sm">Belum ada transaksi QRIS.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="p-4 border-t border-gray-100">
        {{ $transactions->links() }}
    </div>
</div>
@endsection
