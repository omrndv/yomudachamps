@extends('qris.layout')
@section('title', 'Pengaturan Sistem')

@section('content')
<div class="max-w-3xl">
    <div class="bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 rounded-3xl shadow-sm p-8">
        <div class="flex items-center gap-4 mb-8">
            <div class="w-12 h-12 bg-blue-50 dark:bg-blue-500/10 rounded-2xl flex items-center justify-center text-blue-600">
                <i data-lucide="settings-2" class="w-6 h-6"></i>
            </div>
            <div>
                <h3 class="text-lg font-black text-slate-900 dark:text-white">Konfigurasi Gateway</h3>
                <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Atur parameter integrasi GoPay Merchant Anda di sini.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 text-emerald-700 dark:text-emerald-400 px-4 py-3 rounded-xl text-sm font-bold mb-6 flex items-center gap-2">
                <i data-lucide="check-circle" class="w-4 h-4"></i> {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('qris.config.update') }}" method="POST" class="space-y-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">Merchant ID</label>
                    <input type="text" name="merchant_id" value="{{ $config->merchant_id }}" required
                        class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 transition-all font-mono"
                        placeholder="G572567010">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">GoPay API URL</label>
                    <input type="url" name="api_url" value="{{ $config->api_url }}" required
                        class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 transition-all font-mono"
                        placeholder="https://api.gojekapi.com/v2/transactions">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">Token Otorisasi (GoBiz Bearer Token)</label>
                <textarea name="token" rows="3"
                    class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white rounded-xl px-4 py-3 text-[11px] font-mono focus:outline-none focus:border-blue-500 transition-all placeholder-slate-400 dark:placeholder-slate-655"
                    placeholder="Masukkan token baru">{{ $config->token }}</textarea>
                <p class="text-[10px] text-slate-400 dark:text-slate-550 mt-1.5 leading-normal">Token ditampilkan secara langsung. Kosongkan kolom ini jika Anda tidak ingin menyimpan token.</p>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">String QRIS Statis</label>
                <textarea name="static_qris" rows="5" required
                    class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white rounded-xl px-4 py-3 text-[11px] font-mono focus:outline-none focus:border-blue-500 transition-all"
                    placeholder="00020101021126610014COM.GO-JEK.WWW01189...">{{ $config->static_qris }}</textarea>
                <p class="text-[10px] text-slate-400 dark:text-slate-550 mt-1.5 leading-normal">Salin mentah string QRIS statis dari outlet GoPay Merchant Anda. String ini akan diparse menggunakan library EMVCo buatan kita untuk nominal dinamis.</p>
            </div>

            <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-3.5 px-4 rounded-xl text-sm transition-all shadow-md active:scale-[0.98] mt-4 flex items-center justify-center gap-2">
                <i data-lucide="save" class="w-4 h-4"></i> Simpan Konfigurasi
            </button>
        </form>
    </div>
</div>
@endsection
