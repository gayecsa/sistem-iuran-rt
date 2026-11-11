@extends('layouts.app')

@section('title', 'Jurnal Transaksi Keuangan - Warkas Machi')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-800 tracking-tight">Jurnal Transaksi Keuangan</h1>
            <p class="text-xs text-slate-500 mt-1">
                Catatan mutasi riil seluruh pemasukan, pengeluaran, dan perpindahan dana kas di RW 013
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('keuangan.transaksi.create') }}" class="px-4 py-2 rounded-xl text-xs font-bold bg-[#1456A0] hover:bg-blue-800 text-white shadow flex items-center gap-1.5">
                <i data-lucide="plus-circle" class="w-4 h-4"></i> Catat Transaksi Baru
            </a>
        </div>
    </div>

    <!-- Alert Notifikasi -->
    @if(session('success'))
        <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-xl flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-3 text-emerald-800 text-sm font-medium">
                <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 flex-shrink-0"></i>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-rose-50 border-l-4 border-rose-500 p-4 rounded-xl flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-3 text-rose-800 text-sm font-medium">
                <i data-lucide="alert-triangle" class="w-5 h-5 text-rose-600 flex-shrink-0"></i>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- Filter Bar -->
    <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm">
        <form method="GET" action="{{ route('keuangan.transaksi.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 text-xs">
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Buku Kas</label>
                <select name="buku_kas_id" class="w-full border border-slate-300 rounded-lg px-2.5 py-2 focus:ring-2 focus:ring-[#1456A0] outline-none">
                    <option value="">Semua Buku Kas</option>
                    @foreach($availableBuku as $b)
                        <option value="{{ $b->id }}" {{ request('buku_kas_id') == $b->id ? 'selected' : '' }}>
                            {{ $b->nama_kas }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Jenis Transaksi</label>
                <select name="jenis_transaksi" class="w-full border border-slate-300 rounded-lg px-2.5 py-2 focus:ring-2 focus:ring-[#1456A0] outline-none">
                    <option value="">Semua Jenis</option>
                    <option value="pemasukan" {{ request('jenis_transaksi') === 'pemasukan' ? 'selected' : '' }}>Pemasukan</option>
                    <option value="pengeluaran" {{ request('jenis_transaksi') === 'pengeluaran' ? 'selected' : '' }}>Pengeluaran</option>
                    <option value="transfer_keluar" {{ request('jenis_transaksi') === 'transfer_keluar' ? 'selected' : '' }}>Transfer Keluar</option>
                    <option value="transfer_masuk" {{ request('jenis_transaksi') === 'transfer_masuk' ? 'selected' : '' }}>Transfer Masuk</option>
                </select>
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" 
                       class="w-full border border-slate-300 rounded-lg px-2.5 py-2 focus:ring-2 focus:ring-[#1456A0] outline-none">
            </div>

            <div>
                <label class="block font-semibold text-slate-700 mb-1">Cari Keterangan / Pihak</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="No TRX, uraian, pihak..." 
                       class="w-full border border-slate-300 rounded-lg px-2.5 py-2 focus:ring-2 focus:ring-[#1456A0] outline-none">
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="w-full py-2 bg-[#1456A0] text-white font-bold rounded-lg hover:bg-blue-800">
                    Terapkan
                </button>
                <a href="{{ route('keuangan.transaksi.index') }}" class="py-2 px-3 bg-slate-100 text-slate-600 font-medium rounded-lg hover:bg-slate-200">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Tabel Jurnal Transaksi -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-700 font-bold uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="py-3 px-3">Tanggal</th>
                        <th class="py-3 px-3">No. Transaksi</th>
                        <th class="py-3 px-3">Buku Kas</th>
                        <th class="py-3 px-3">Kategori & Uraian</th>
                        <th class="py-3 px-3">Penyetor / Penerima</th>
                        <th class="py-3 px-3">Metode</th>
                        <th class="py-3 px-3 text-right">Nominal</th>
                        <th class="py-3 px-3 text-center">Petugas</th>
                        <th class="py-3 px-3 text-center">Status</th>
                        <th class="py-3 px-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($transaksis as $trx)
                        <tr class="hover:bg-slate-50/80 transition {{ $trx->status === 'dibatalkan' ? 'opacity-50 line-through bg-slate-50' : '' }}">
                            <td class="py-3 px-3 text-slate-500 whitespace-nowrap">
                                {{ \Carbon\Carbon::parse($trx->tanggal_transaksi)->translatedFormat('d/m/Y') }}
                            </td>
                            <td class="py-3 px-3 font-mono font-bold text-slate-800 whitespace-nowrap">
                                {{ $trx->nomor_transaksi }}
                            </td>
                            <td class="py-3 px-3">
                                <span class="px-2 py-0.5 rounded text-[11px] font-semibold bg-slate-100 text-slate-700">
                                    {{ $trx->bukuKas->nama_kas ?? '-' }}
                                </span>
                            </td>
                            <td class="py-3 px-3">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold border {{ $trx->jenis_badge }} mr-1">
                                    {{ strtoupper(str_replace('_', ' ', $trx->jenis_transaksi)) }}
                                </span>
                                <span class="font-bold text-slate-800">{{ $trx->kategori }}</span>
                                <div class="text-[11px] text-slate-400 max-w-xs truncate">{{ $trx->keterangan }}</div>
                            </td>
                            <td class="py-3 px-3 text-slate-700">
                                {{ $trx->penyetor_penerima ?: '-' }}
                            </td>
                            <td class="py-3 px-3 capitalize">
                                {{ str_replace('_', ' ', $trx->metode_pembayaran) }}
                            </td>
                            <td class="py-3 px-3 text-right font-bold whitespace-nowrap">
                                @if(in_array($trx->jenis_transaksi, ['pemasukan', 'transfer_masuk']))
                                    <span class="text-emerald-600">+Rp {{ number_format($trx->nominal, 0, ',', '.') }}</span>
                                @else
                                    <span class="text-rose-600">-Rp {{ number_format($trx->nominal, 0, ',', '.') }}</span>
                                @endif
                            </td>
                            <td class="py-3 px-3 text-center text-slate-500 text-[11px] whitespace-nowrap">
                                {{ $trx->petugas->name ?? $trx->nama_petugas ?? 'Sistem' }}
                            </td>
                            <td class="py-3 px-3 text-center whitespace-nowrap">
                                @if($trx->status === 'selesai')
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-emerald-100 text-emerald-800">
                                        Selesai
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-slate-200 text-slate-600" title="{{ $trx->alasan_batal }}">
                                        Batal
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-3 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5">
                                    @if($trx->bukti_transaksi_path)
                                        <a href="{{ asset('storage/' . $trx->bukti_transaksi_path) }}" target="_blank" class="text-blue-600 hover:text-blue-800 p-1" title="Lihat Bukti Fisik">
                                            <i data-lucide="paperclip" class="w-3.5 h-3.5"></i>
                                        </a>
                                    @endif
                                    @if($trx->status === 'selesai')
                                        <button onclick="bukaModalBatal('{{ $trx->id }}', '{{ $trx->nomor_transaksi }}', '{{ number_format($trx->nominal, 0, ',', '.') }}')" 
                                                class="text-rose-600 hover:text-rose-800 p-1 hover:bg-rose-50 rounded" title="Batalkan Transaksi">
                                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="py-10 text-center text-slate-400">
                                Tidak ada transaksi keuangan yang sesuai dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $transaksis->links() }}
        </div>
    </div>
</div>

<!-- Modal Pembatalan Transaksi -->
<div id="modal-batal-transaksi" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm hidden">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl mx-4">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 class="text-base font-bold text-rose-600 flex items-center gap-2">
                <i data-lucide="alert-triangle" class="w-5 h-5 text-rose-600"></i> Batalkan Transaksi Keuangan
            </h3>
            <button onclick="document.getElementById('modal-batal-transaksi').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form id="form-batal-transaksi" method="POST" action="" class="mt-4 space-y-4">
            @csrf
            <div class="p-3 bg-rose-50 border border-rose-200 rounded-xl text-xs text-rose-900">
                <p>Nomor Transaksi: <strong id="batal-nomor-trx" class="font-mono"></strong></p>
                <p class="mt-0.5">Nominal: <strong id="batal-nominal-trx"></strong></p>
                <p class="text-[11px] text-rose-700 mt-1">Saldo buku kas akan otomatis disesuaikan dan transaksi dicatat dibatalkan di audit log.</p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Alasan Pembatalan (Wajib Diisi) *</label>
                <textarea name="alasan_batal" required rows="3" placeholder="Tuliskan alasan pembatalan transaksi ini..." 
                          class="w-full text-xs font-medium border border-slate-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-rose-500 outline-none"></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('modal-batal-transaksi').classList.add('hidden')" 
                        class="px-4 py-2 text-xs font-medium text-slate-600 hover:bg-slate-100 rounded-xl">
                    Tutup
                </button>
                <button type="submit" class="px-4 py-2 text-xs font-bold bg-rose-600 hover:bg-rose-700 text-white rounded-xl shadow">
                    Batalkan Transaksi
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function bukaModalBatal(id, nomor, nominal) {
    document.getElementById('form-batal-transaksi').action = '/keuangan/transaksi/' + id + '/batal';
    document.getElementById('batal-nomor-trx').innerText = nomor;
    document.getElementById('batal-nominal-trx').innerText = 'Rp ' + nominal;
    document.getElementById('modal-batal-transaksi').classList.remove('hidden');
}
</script>
@endsection
