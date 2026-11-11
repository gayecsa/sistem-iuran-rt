@extends('layouts.app')

@section('title', 'Rekening Koran - ' . $bukuKas->nama_kas . ' - Warkas Machi')

@section('content')
<div class="space-y-6">
    <!-- Breadcrumb & Navigasi -->
    <div class="flex items-center gap-2 text-xs text-slate-500">
        <a href="{{ route('keuangan.dashboard') }}" class="hover:underline">Dashboard Keuangan</a>
        <span>/</span>
        <a href="{{ route('keuangan.buku_kas.index') }}" class="hover:underline">Buku Kas</a>
        <span>/</span>
        <span class="text-slate-800 font-semibold">{{ $bukuKas->kode_kas }}</span>
    </div>

    <!-- Header Buku Kas -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1.5">
                    <span class="font-mono text-xs font-bold px-2.5 py-0.5 rounded bg-blue-100 text-[#1456A0]">
                        {{ $bukuKas->kode_kas }}
                    </span>
                    <span class="text-xs font-semibold px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-700">
                        {{ strtoupper(str_replace('_', ' ', $bukuKas->jenis_kas)) }}
                    </span>
                </div>
                <h1 class="text-2xl font-black text-slate-800">{{ $bukuKas->nama_kas }}</h1>
                <p class="text-xs text-slate-500 mt-1">{{ $bukuKas->deskripsi }}</p>
                <div class="flex flex-wrap gap-4 text-xs text-slate-600 mt-2 font-medium">
                    <span><strong>PIC:</strong> {{ $bukuKas->nama_penanggung_jawab ?: '-' }}</span>
                    <span><strong>Rekening:</strong> {{ $bukuKas->nama_bank ? $bukuKas->nama_bank . ' - ' . $bukuKas->no_rekening . ' (a.n. ' . $bukuKas->atas_nama . ')' : 'Kas Tunai' }}</span>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('keuangan.transaksi.create') }}?buku_kas_id={{ $bukuKas->id }}" class="px-4 py-2 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow inline-flex items-center gap-1.5">
                    <i data-lucide="plus-circle" class="w-4 h-4"></i> Catat Transaksi
                </a>
                @if($isRw)
                    <a href="{{ route('keuangan.transaksi.create') }}?buku_kas_id={{ $bukuKas->id }}&jenis=transfer_keluar" class="px-4 py-2 rounded-xl text-xs font-bold bg-blue-600 hover:bg-blue-700 text-white shadow inline-flex items-center gap-1.5">
                        <i data-lucide="arrow-left-right" class="w-4 h-4"></i> Transfer Kas
                    </a>
                @endif
            </div>
        </div>

        <!-- Banner Ringkasan Saldo & Audit Persamaan Akuntansi -->
        <div class="mt-6 pt-5 border-t border-slate-100 grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200">
                <span class="text-xs text-slate-500 font-medium">Saldo Awal Buku:</span>
                <p class="text-lg font-extrabold text-slate-700 mt-0.5">
                    Rp {{ number_format($bukuKas->saldo_awal, 0, ',', '.') }}
                </p>
            </div>
            <div class="p-3 bg-emerald-50 rounded-xl border border-emerald-200">
                <span class="text-xs text-emerald-700 font-medium">Total Pemasukan (Debit):</span>
                <p class="text-lg font-extrabold text-emerald-700 mt-0.5">
                    +Rp {{ number_format($totalMasuk, 0, ',', '.') }}
                </p>
            </div>
            <div class="p-3 bg-rose-50 rounded-xl border border-rose-200">
                <span class="text-xs text-rose-700 font-medium">Total Pengeluaran (Kredit):</span>
                <p class="text-lg font-extrabold text-rose-700 mt-0.5">
                    -Rp {{ number_format($totalKeluar, 0, ',', '.') }}
                </p>
            </div>
            <div class="p-3 bg-blue-50 rounded-xl border border-blue-200">
                <span class="text-xs text-[#1456A0] font-medium">Saldo Akhir Buku Kas:</span>
                <p class="text-xl font-black text-[#1456A0] mt-0.5">
                    Rp {{ number_format($bukuKas->saldo_sekarang, 0, ',', '.') }}
                </p>
            </div>
        </div>

        <!-- Badge Formula Pembukuan -->
        <div class="mt-3 flex items-center justify-between text-[11px] text-slate-500 bg-slate-50 px-3 py-1.5 rounded-lg border border-slate-200">
            <span>Rumus: <strong>Saldo Akhir = Saldo Awal + Total Masuk - Total Keluar</strong></span>
            <span class="font-bold text-emerald-600 flex items-center gap-1">
                <i data-lucide="check" class="w-3.5 h-3.5"></i> Terverifikasi Seimbang
            </span>
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

    <!-- Filter Mutasi -->
    <div class="bg-white rounded-2xl border border-slate-200 p-4 shadow-sm">
        <form method="GET" action="{{ route('keuangan.buku_kas.show', $bukuKas->id) }}" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-3 text-xs">
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Cari Transaksi</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="No TRX, uraian, penyetor..." 
                       class="w-full border border-slate-300 rounded-lg px-2.5 py-2 focus:ring-2 focus:ring-[#1456A0] outline-none">
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Jenis Transaksi</label>
                <select name="jenis" class="w-full border border-slate-300 rounded-lg px-2.5 py-2 focus:ring-2 focus:ring-[#1456A0] outline-none">
                    <option value="">Semua Mutasi</option>
                    <option value="pemasukan" {{ request('jenis') === 'pemasukan' ? 'selected' : '' }}>Pemasukan</option>
                    <option value="pengeluaran" {{ request('jenis') === 'pengeluaran' ? 'selected' : '' }}>Pengeluaran</option>
                    <option value="transfer_masuk" {{ request('jenis') === 'transfer_masuk' ? 'selected' : '' }}>Transfer Masuk</option>
                    <option value="transfer_keluar" {{ request('jenis') === 'transfer_keluar' ? 'selected' : '' }}>Transfer Keluar</option>
                </select>
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" 
                       class="w-full border border-slate-300 rounded-lg px-2.5 py-2 focus:ring-2 focus:ring-[#1456A0] outline-none">
            </div>
            <div>
                <label class="block font-semibold text-slate-700 mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" 
                       class="w-full border border-slate-300 rounded-lg px-2.5 py-2 focus:ring-2 focus:ring-[#1456A0] outline-none">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="w-full py-2 bg-[#1456A0] text-white font-bold rounded-lg hover:bg-blue-800">
                    Filter
                </button>
                <a href="{{ route('keuangan.buku_kas.show', $bukuKas->id) }}" class="py-2 px-3 bg-slate-100 text-slate-600 font-medium rounded-lg hover:bg-slate-200">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Tabel Rekening Koran & Mutasi -->
    <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-700 font-bold uppercase tracking-wider text-[11px]">
                    <tr>
                        <th class="py-3 px-3">Tanggal</th>
                        <th class="py-3 px-3">No. Transaksi</th>
                        <th class="py-3 px-3">Kategori & Keterangan</th>
                        <th class="py-3 px-3">Penyetor / Penerima</th>
                        <th class="py-3 px-3 text-right">Debit (Masuk)</th>
                        <th class="py-3 px-3 text-right">Kredit (Keluar)</th>
                        <th class="py-3 px-3 text-right">Saldo Berjalan</th>
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
                                @if($trx->status === 'dibatalkan')
                                    <span class="block text-[9px] text-rose-600 uppercase font-sans font-bold">Dibatalkan</span>
                                @endif
                            </td>
                            <td class="py-3 px-3">
                                <div class="font-bold text-slate-800">{{ $trx->kategori }}</div>
                                <div class="text-[11px] text-slate-400 max-w-xs truncate">{{ $trx->keterangan }}</div>
                            </td>
                            <td class="py-3 px-3 text-slate-700">
                                {{ $trx->penyetor_penerima ?: '-' }}
                            </td>
                            <td class="py-3 px-3 text-right font-bold text-emerald-600 whitespace-nowrap">
                                @if(in_array($trx->jenis_transaksi, ['pemasukan', 'transfer_masuk']))
                                    Rp {{ number_format($trx->nominal, 0, ',', '.') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="py-3 px-3 text-right font-bold text-rose-600 whitespace-nowrap">
                                @if(in_array($trx->jenis_transaksi, ['pengeluaran', 'transfer_keluar']))
                                    Rp {{ number_format($trx->nominal, 0, ',', '.') }}
                                @else
                                    -
                                @endif
                            </td>
                            <td class="py-3 px-3 text-right font-mono font-bold text-slate-900 whitespace-nowrap">
                                Rp {{ number_format($trx->saldo_setelahnya, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-3 text-center whitespace-nowrap">
                                @if($trx->status === 'selesai')
                                    <button onclick="bukaModalBatal('{{ $trx->id }}', '{{ $trx->nomor_transaksi }}', '{{ number_format($trx->nominal, 0, ',', '.') }}')" 
                                            class="text-rose-600 hover:text-rose-800 font-semibold text-[11px] px-2 py-1 rounded hover:bg-rose-50" title="Batalkan Transaksi">
                                        Batal
                                    </button>
                                @else
                                    <span class="text-slate-400 text-[10px] italic">Batal</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-10 text-center text-slate-400">
                                Tidak ada catatan mutasi transaksi pada periode/kriteria ini.
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

<!-- Modal Pembatalan Transaksi dengan Alasan Wajib -->
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
                <p>Anda akan membatalkan transaksi: <strong id="batal-nomor-trx" class="font-mono"></strong></p>
                <p class="mt-1">Nominal: <strong id="batal-nominal-trx"></strong></p>
                <p class="text-[11px] text-rose-700 mt-1">Saldo buku kas akan otomatis disesuaikan secara terbalik dan riwayat pembatalan dicatat di Audit Log.</p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1">Alasan Pembatalan (Wajib Diisi) *</label>
                <textarea name="alasan_batal" required rows="3" placeholder="Contoh: Terjadi salah input nominal oleh petugas / transaksi ganda" 
                          class="w-full text-xs font-medium border border-slate-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-rose-500 outline-none"></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('modal-batal-transaksi').classList.add('hidden')" 
                        class="px-4 py-2 text-xs font-medium text-slate-600 hover:bg-slate-100 rounded-xl">
                    Tutup
                </button>
                <button type="submit" class="px-4 py-2 text-xs font-bold bg-rose-600 hover:bg-rose-700 text-white rounded-xl shadow">
                    Konfirmasi Batalkan
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
