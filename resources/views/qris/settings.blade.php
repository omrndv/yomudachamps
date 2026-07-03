@extends('qris.layout')
@section('title', 'Pengaturan')

@section('content')
<div class="bg-white border border-gray-200 rounded-3xl shadow-sm p-6 sm:p-8 max-w-3xl mx-auto">
    <h3 class="text-lg font-extrabold mb-6 text-gray-900">
        Pengaturan GoPay Merchant & API
    </h3>

    <form action="{{ route('qris.config.update') }}" method="POST" class="space-y-6">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Merchant ID</label>
                <input type="text" name="merchant_id" value="{{ $config->merchant_id }}" required placeholder="Contoh: G572567010" 
                    class="w-full bg-gray-50 text-gray-900 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all">
                <p class="text-[10px] text-gray-400 mt-2">Ditemukan di dalam aplikasi GoBiz atau dari String QRIS.</p>
            </div>
            <div>
                <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">API GoPay URL</label>
                <input type="url" name="api_url" value="{{ $config->api_url }}" required placeholder="https://api.gobiz.co.id/v2/transactions" 
                    class="w-full bg-gray-50 text-gray-900 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all">
                <p class="text-[10px] text-gray-400 mt-2">Biarkan default kecuali ada perubahan endpoint dari Gojek.</p>
            </div>
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">String QRIS Statis Murni</label>
            <textarea name="static_qris" rows="4" required placeholder="Paste string text hasil scan QRIS fisik toko Anda di sini..." 
                class="w-full bg-gray-50 text-gray-900 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all font-mono break-all leading-relaxed">{{ $config->static_qris }}</textarea>
            <p class="text-[10px] text-gray-400 mt-2">Gunakan string dari cabang yang valid (seperti "Gami"). String ini akan diproses menjadi QRIS dinamis.</p>
        </div>

        <div>
            <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">GoBiz Bearer Token (Authorization)</label>
            <input type="password" name="token" placeholder="{{ $config->has_token ? '•••••••••••••••• (Kosongkan jika tidak ingin mengubah)' : 'Paste Bearer token dari aplikasi GoBiz...' }}" 
                class="w-full bg-gray-50 text-gray-900 border border-gray-200 rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-all font-mono">
            <p class="text-[10px] text-gray-400 mt-2">Diperlukan agar Poller otomatis mendeteksi pembayaran yang masuk ke saldo GoPay.</p>
        </div>

        <div class="pt-4 flex items-center justify-end">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-xl transition-all shadow-sm active:scale-95 flex items-center gap-2">
                Simpan Konfigurasi
            </button>
        </div>
    </form>
</div>
@endsection
