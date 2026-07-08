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

        <form action="{{ route('qris.config.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
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

            <div class="border-t border-slate-100 dark:border-slate-800/80 pt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">No. WhatsApp Notifikasi Admin</label>
                    <input type="text" name="admin_wa" value="{{ $config->admin_wa }}"
                        class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white rounded-xl px-4 py-3 text-sm focus:outline-none focus:border-blue-500 transition-all"
                        placeholder="6285122616191">
                    <p class="text-[10px] text-slate-400 dark:text-slate-550 mt-1.5">Nomor tujuan untuk info token expired / over-slot.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">Warna Halaman QRIS (Branding)</label>
                    <div class="flex gap-3">
                        <input type="color" name="primary_color" value="{{ $config->primary_color }}"
                            class="h-11 w-16 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl cursor-pointer">
                        <input type="text" readonly value="{{ $config->primary_color }}"
                            class="flex-1 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white rounded-xl px-4 py-3 text-sm font-mono focus:outline-none">
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">Logo Halaman QRIS (Kustom)</label>
                @if($config->custom_logo)
                    <div class="mb-3 flex items-center justify-between bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 p-3 rounded-2xl">
                        <div class="flex items-center gap-3">
                            <img src="{{ $config->custom_logo }}" alt="Custom Logo" class="max-h-8 rounded">
                            <span class="text-xs text-slate-400 font-mono">{{ $config->custom_logo }}</span>
                        </div>
                        <button type="button" onclick="if(confirm('Apakah Anda yakin ingin menghapus logo kustom?')) document.getElementById('delete-logo-form').submit();" class="text-red-500 hover:text-red-650 text-xs font-bold flex items-center gap-1">
                            <i data-lucide="trash-2" class="w-4 h-4"></i> Hapus Logo
                        </button>
                    </div>
                @endif
                <input type="file" name="custom_logo" accept="image/*"
                    class="block w-full text-xs text-slate-500 dark:text-slate-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-900/30 dark:file:text-blue-400">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-2">String QRIS Statis</label>
                
                <!-- QR Scanner Section -->
                <div class="mb-3 p-4 border border-dashed border-slate-300 dark:border-slate-700 rounded-xl bg-slate-50/50 dark:bg-slate-900/50">
                    <label class="block text-xs font-bold text-slate-600 dark:text-slate-400 mb-2">Upload Gambar QRIS (Otomatis ekstrak string)</label>
                    <input type="file" id="qr-input-file" accept="image/*" class="block w-full text-sm text-slate-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-900/30 dark:file:text-blue-400">
                    <div id="qr-scan-result" class="mt-2 text-xs text-emerald-600 dark:text-emerald-400 hidden">
                        <i data-lucide="check-circle" class="w-3 h-3 inline"></i> Berhasil membaca QRIS! String telah diisi otomatis.
                    </div>
                    <div id="qr-scan-error" class="mt-2 text-xs text-rose-600 dark:text-rose-400 hidden">
                        <i data-lucide="alert-circle" class="w-3 h-3 inline"></i> Gagal membaca QR code dari gambar.
                    </div>
                    <div id="reader" style="display:none;"></div>
                </div>

                <textarea name="static_qris" id="static_qris_input" rows="5" required
                    class="w-full bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white rounded-xl px-4 py-3 text-[11px] font-mono focus:outline-none focus:border-blue-500 transition-all"
                    placeholder="00020101021126610014COM.GO-JEK.WWW01189...">{{ $config->static_qris }}</textarea>
                <p class="text-[10px] text-slate-400 dark:text-slate-550 mt-1.5 leading-normal">Salin mentah string QRIS statis dari outlet GoPay Merchant Anda, atau upload gambar QRIS statis pada form di atas agar otomatis terisi.</p>
            </div>

            <button type="submit"
                class="w-full bg-blue-600 hover:bg-blue-500 text-white font-bold py-3.5 px-4 rounded-xl text-sm transition-all shadow-md active:scale-[0.98] mt-4 flex items-center justify-center gap-2">
                <i data-lucide="save" class="w-4 h-4"></i> Simpan Konfigurasi
            </button>
        </form>
    </div>
</div>

<form id="delete-logo-form" action="{{ route('qris.delete-logo') }}" method="POST" style="display:none;">
    @csrf
</form>
@endsection

@push('scripts')
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileinput = document.getElementById('qr-input-file');
    const staticQrisInput = document.getElementById('static_qris_input');
    const scanResult = document.getElementById('qr-scan-result');
    const scanError = document.getElementById('qr-scan-error');

    if (fileinput) {
        fileinput.addEventListener('change', e => {
            if (e.target.files.length == 0) {
                return;
            }
            
            scanResult.classList.add('hidden');
            scanError.classList.add('hidden');
            
            const imageFile = e.target.files[0];
            const html5QrCode = new Html5Qrcode("reader");

            html5QrCode.scanFile(imageFile, true)
            .then(decodedText => {
                staticQrisInput.value = decodedText;
                scanResult.classList.remove('hidden');
            })
            .catch(err => {
                scanError.classList.remove('hidden');
                console.log(`Error scanning file: ${err}`)
            });
        });
    }
});
</script>
@endpush
