@extends('layouts.app')

@section('title', 'Catat Transaksi Keuangan - Warkas Machi')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-xs text-slate-500">
        <a href="{{ route('keuangan.dashboard') }}" class="hover:underline">Dashboard Keuangan</a>
        <span>/</span>
        <a href="{{ route('keuangan.transaksi.index') }}" class="hover:underline">Jurnal Transaksi</a>
        <span>/</span>
        <span class="text-slate-800 font-semibold">Catat Transaksi Baru</span>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 md:p-8">
        <div class="pb-4 border-b border-slate-100 mb-6">
            <h1 class="text-xl font-extrabold text-slate-800 flex items-center gap-2">
                <i data-lucide="edit-3" class="w-6 h-6 text-[#1456A0]"></i> Catat Transaksi Keuangan
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Pencatatan mutasi kas (Pemasukan, Pengeluaran, atau Transfer Antar Kas) dengan nomor transaksi otomatis dan jejak audit.
            </p>
        </div>

        @if($errors->any())
            <div class="bg-rose-50 border-l-4 border-rose-500 p-4 rounded-xl mb-6">
                <div class="flex items-center gap-2 text-rose-800 text-xs font-bold mb-1">
                    <i data-lucide="alert-circle" class="w-4 h-4"></i> Mohon periksa kembali formulir Anda:
                </div>
                <ul class="list-disc list-inside text-xs text-rose-700 space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('keuangan.transaksi.store') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <!-- Buku Kas & Jenis Transaksi -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Pilih Buku Kas *</label>
                    <select name="buku_kas_id" id="buku_kas_id" required class="w-full text-xs font-medium border border-slate-300 rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-[#1456A0] outline-none">
                        @foreach($bukuKasList as $buku)
                            <option value="{{ $buku->id }}" {{ (old('buku_kas_id', request('buku_kas_id')) == $buku->id) ? 'selected' : '' }}>
                                [{{ $buku->kode_kas }}] {{ $buku->nama_kas }} (Saldo: Rp {{ number_format($buku->saldo_sekarang, 0, ',', '.') }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Jenis Transaksi *</label>
                    <select name="jenis_transaksi" id="jenis_transaksi" onchange="toggleTransferFields()" required class="w-full text-xs font-medium border border-slate-300 rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-[#1456A0] outline-none">
                        <option value="pemasukan" {{ old('jenis_transaksi', request('jenis')) === 'pemasukan' ? 'selected' : '' }}>Pemasukan (Uang Masuk / Debit)</option>
                        <option value="pengeluaran" {{ old('jenis_transaksi', request('jenis')) === 'pengeluaran' ? 'selected' : '' }}>Pengeluaran (Uang Keluar / Kredit)</option>
                        @if($isRw)
                            <option value="transfer_keluar" {{ old('jenis_transaksi', request('jenis')) === 'transfer_keluar' ? 'selected' : '' }}>Transfer Antar Kas (Pindah Dana)</option>
                        @endif
                    </select>
                </div>
            </div>

            <!-- Target Kas Transfer (Hanya Tampil jika Transfer Antar Kas) -->
            <div id="transfer_field" class="p-4 bg-blue-50 border border-blue-200 rounded-xl {{ old('jenis_transaksi', request('jenis')) === 'transfer_keluar' ? '' : 'hidden' }}">
                <label class="block text-xs font-bold text-blue-900 mb-1">Kas Tujuan Transfer *</label>
                <select name="target_buku_kas_id" id="target_buku_kas_id" class="w-full text-xs font-medium border border-blue-300 bg-white rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-[#1456A0] outline-none">
                    <option value="">-- Pilih Buku Kas Penerima --</option>
                    @foreach($bukuKasList as $target)
                        <option value="{{ $target->id }}" {{ old('target_buku_kas_id') == $target->id ? 'selected' : '' }}>
                            [{{ $target->kode_kas }}] {{ $target->nama_kas }}
                        </option>
                    @endforeach
                </select>
                <p class="text-[11px] text-blue-700 mt-1">Sistem otomatis mencatat pengeluaran di kas asal dan pemasukan di kas penerima dengan nomor referensi transfer yang sama.</p>
            </div>

            <!-- Tanggal & Nominal -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Transaksi *</label>
                    <input type="date" name="tanggal_transaksi" value="{{ old('tanggal_transaksi', date('Y-m-d')) }}" required 
                           class="w-full text-xs font-medium border border-slate-300 rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-[#1456A0] outline-none">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nominal (Rp) *</label>
                    <input type="number" name="nominal" id="nominal" value="{{ old('nominal') }}" min="1000" step="100" placeholder="Contoh: 150000" required 
                           class="w-full text-xs font-medium border border-slate-300 rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-[#1456A0] outline-none">
                </div>
            </div>

            <!-- Kategori & Metode Pembayaran -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Kategori Transaksi *</label>
                    <input type="text" name="kategori" id="kategori" list="kategori_suggestions" value="{{ old('kategori') }}" placeholder="Ketik atau pilih kategori..." required 
                           class="w-full text-xs font-medium border border-slate-300 rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-[#1456A0] outline-none">
                    <datalist id="kategori_suggestions">
                        @foreach($kategoris as $kat)
                            <option value="{{ $kat->nama_kategori }}">
                        @endforeach
                    </datalist>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Metode Pembayaran *</label>
                    <select name="metode_pembayaran" required class="w-full text-xs font-medium border border-slate-300 rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-[#1456A0] outline-none">
                        <option value="tunai" {{ old('metode_pembayaran') === 'tunai' ? 'selected' : '' }}>Tunai (Cash)</option>
                        <option value="transfer_bank" {{ old('metode_pembayaran') === 'transfer_bank' ? 'selected' : '' }}>Transfer Bank</option>
                        <option value="qris" {{ old('metode_pembayaran') === 'qris' ? 'selected' : '' }}>QRIS</option>
                    </select>
                </div>
            </div>

            <!-- Penyetor / Penerima -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Penyetor / Penerima / Pihak Terkait</label>
                <input type="text" name="penyetor_penerima" value="{{ old('penyetor_penerima') }}" placeholder="Contoh: Toko Bangunan Berkah / Bpk. Alexander Hayes / Danru Satpam" 
                       class="w-full text-xs font-medium border border-slate-300 rounded-xl px-3 py-2.5 focus:ring-2 focus:ring-[#1456A0] outline-none">
            </div>

            <!-- Keterangan / Uraian -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Uraian / Keterangan Transaksi</label>
                <textarea name="keterangan" rows="2" placeholder="Rincian peruntukan dana atau nomor nota fisik..." 
                          class="w-full text-xs font-medium border border-slate-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-[#1456A0] outline-none">{{ old('keterangan') }}</textarea>
            </div>

            <!-- Upload Nota / Bukti Fisik -->
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Lampiran Struk / Nota / Bukti Transfer (Opsional)</label>
                <input type="file" name="bukti_transaksi" accept="image/*,application/pdf" 
                       class="w-full text-xs text-slate-600 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-slate-100 file:text-slate-700 hover:file:bg-slate-200">
                <p class="text-[11px] text-slate-400 mt-1">Maksimal 3 MB (JPG, PNG, atau PDF).</p>
            </div>

            <!-- Tombol Simpan -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('keuangan.transaksi.index') }}" class="px-5 py-2.5 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl text-xs font-bold bg-[#1456A0] hover:bg-blue-800 text-white shadow-md inline-flex items-center gap-1.5">
                    <i data-lucide="save" class="w-4 h-4"></i> Simpan Transaksi Kas
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleTransferFields() {
    const jenis = document.getElementById('jenis_transaksi').value;
    const transferField = document.getElementById('transfer_field');
    const targetSelect = document.getElementById('target_buku_kas_id');
    const katInput = document.getElementById('kategori');

    if (jenis === 'transfer_keluar') {
        transferField.classList.remove('hidden');
        targetSelect.setAttribute('required', 'required');
        katInput.value = 'Transfer Antar Kas';
    } else {
        transferField.classList.add('hidden');
        targetSelect.removeAttribute('required');
    }
}
</script>
@endsection
