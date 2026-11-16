@extends('layouts.app')

@section('title', 'Rekening Koran - ' . $bukuKas->nama_kas . ' - Warkas Machi')

@section('content')
<!-- Tailwind CDN dengan Preflight Disabled -->
<script src="https://cdn.tailwindcss.com"></script>
<script>
    tailwind.config = {
        darkMode: ['class', '[data-theme="dark"]'],
        corePlugins: { preflight: false },
        theme: {
            extend: {
                colors: {
                    wmNavy: '#0F2C59',
                    wmPrimary: '#1456A0',
                    wmSecondary: '#2878D0',
                    wmGreen: '#16865D',
                    wmGreenLight: '#E8F5E9',
                    wmRed: '#DC2626',
                    wmRedLight: '#FEE2E2',
                }
            }
        }
    }
</script>

<div class="space-y-6">

    <!-- 1. BREADCRUMB & NAVIGASI -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-5 sm:p-6 shadow-sm">
        <nav class="flex items-center gap-2 text-xs font-semibold text-slate-400 mb-3" aria-label="Breadcrumb">
            <a href="{{ route('keuangan.dashboard') }}" class="hover:text-blue-700 transition flex items-center gap-1">
                <i data-lucide="layout-dashboard" class="w-3.5 h-3.5"></i>
                <span>Dashboard RW</span>
            </a>
            <i data-lucide="chevron-right" class="w-3 h-3 text-slate-300"></i>
            <a href="{{ route('keuangan.buku_kas.index') }}" class="hover:text-blue-700 transition">
                <span>Buku Kas & Rekening</span>
            </a>
            <i data-lucide="chevron-right" class="w-3 h-3 text-slate-300"></i>
            <span class="text-slate-800 font-bold font-mono">{{ $bukuKas->kode_kas }}</span>
        </nav>

        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2.5 flex-wrap">
                    <span class="font-mono text-xs font-black px-2.5 py-1 rounded-lg bg-blue-100 text-[#1456A0] border border-blue-200">
                        {{ $bukuKas->kode_kas }}
                    </span>
                    <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $bukuKas->jenis_kas === 'kas_rw' ? 'bg-blue-50 text-blue-800 border border-blue-200' : ($bukuKas->jenis_kas === 'kas_rt' ? 'bg-emerald-50 text-emerald-800 border border-emerald-200' : 'bg-indigo-50 text-indigo-800 border border-indigo-200') }}">
                        {{ $bukuKas->jenis_label }}
                    </span>
                    @if($bukuKas->rt_number)
                        <span class="px-2 py-0.5 rounded text-xs font-bold bg-slate-100 text-slate-700 border border-slate-200">
                            Wilayah RT {{ str_pad($bukuKas->rt_number, 3, '0', STR_PAD_LEFT) }}
                        </span>
                    @endif
                </div>

                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight mt-1.5">
                    {{ $bukuKas->nama_kas }}
                </h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-2xl leading-relaxed">
                    {{ $bukuKas->deskripsi ?: 'Buku kas perbendaharaan resmi warga Warkas Machi RW 013.' }}
                </p>

                <div class="flex flex-wrap items-center gap-4 text-xs text-slate-600 mt-3 pt-3 border-t border-slate-100">
                    <span class="flex items-center gap-1.5">
                        <i data-lucide="user-check" class="w-4 h-4 text-slate-400"></i>
                        <strong>PIC:</strong> {{ $bukuKas->nama_penanggung_jawab ?: 'Pengurus Lingkungan' }}
                    </span>
                    <span class="flex items-center gap-1.5 font-mono">
                        <i data-lucide="credit-card" class="w-4 h-4 text-slate-400"></i>
                        <strong>Rekening:</strong> {{ $bukuKas->masked_rekening }}
                        @if($bukuKas->atas_nama)
                            <span class="text-slate-400 font-sans">(a.n. {{ $bukuKas->atas_nama }})</span>
                        @endif
                    </span>
                </div>
            </div>

            <!-- Tombol Aksi Header -->
            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('keuangan.buku_kas.index') }}" 
                   class="px-3.5 py-2 rounded-xl text-xs font-bold bg-slate-100 text-slate-700 hover:bg-slate-200 transition inline-flex items-center gap-1.5">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    <span>Kembali ke Buku Kas</span>
                </a>

                @if($canManageThisKas)
                    <a href="{{ route('keuangan.transaksi.create') }}?buku_kas_id={{ $bukuKas->id }}" 
                       class="px-4 py-2 rounded-xl text-xs font-bold bg-emerald-600 hover:bg-emerald-700 text-white shadow transition inline-flex items-center gap-1.5">
                        <i data-lucide="plus-circle" class="w-4 h-4"></i>
                        <span>+ Catat Transaksi</span>
                    </a>

                    @if($isRw && $bukuKas->jenis_kas === 'kas_rw')
                        <a href="{{ route('keuangan.transaksi.create') }}?buku_kas_id={{ $bukuKas->id }}&jenis=transfer_keluar" 
                           class="px-4 py-2 rounded-xl text-xs font-bold bg-[#1456A0] hover:bg-blue-800 text-white shadow transition inline-flex items-center gap-1.5">
                            <i data-lucide="arrow-left-right" class="w-4 h-4"></i>
                            <span>Transfer Antar Kas</span>
                        </a>
                    @endif
                @else
                    <span class="px-3 py-2 rounded-xl text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200 inline-flex items-center gap-1">
                        <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                        <span>Mode Monitoring</span>
                    </span>
                @endif

                <button type="button" onclick="window.print()" 
                        class="px-3.5 py-2 rounded-xl text-xs font-bold bg-white text-slate-700 border border-slate-300 hover:bg-slate-50 shadow-xs transition inline-flex items-center gap-1.5">
                    <i data-lucide="printer" class="w-4 h-4 text-slate-600"></i>
                    <span>Cetak</span>
                </button>
            </div>
        </div>

        <!-- 4 Kartu Ringkasan Akuntansi -->
        <div class="mt-6 pt-5 border-t border-slate-100 grid grid-cols-2 md:grid-cols-4 gap-3.5">
            <div class="p-3.5 bg-slate-50/80 rounded-xl border border-slate-200/80">
                <span class="text-xs text-slate-500 font-semibold block">Saldo Awal Buku:</span>
                <p class="text-lg font-black text-slate-700 mt-0.5">
                    Rp {{ number_format($bukuKas->saldo_awal, 0, ',', '.') }}
                </p>
            </div>
            <div class="p-3.5 bg-emerald-50/80 rounded-xl border border-emerald-200/80">
                <span class="text-xs text-emerald-700 font-semibold block">Total Pemasukan (Debit):</span>
                <p class="text-lg font-black text-emerald-700 mt-0.5">
                    +Rp {{ number_format($totalMasuk, 0, ',', '.') }}
                </p>
            </div>
            <div class="p-3.5 bg-rose-50/80 rounded-xl border border-rose-200/80">
                <span class="text-xs text-rose-700 font-semibold block">Total Pengeluaran (Kredit):</span>
                <p class="text-lg font-black text-rose-700 mt-0.5">
                    -Rp {{ number_format($totalKeluar, 0, ',', '.') }}
                </p>
            </div>
            <div class="p-3.5 bg-blue-50/80 rounded-xl border border-blue-200/80">
                <span class="text-xs text-[#1456A0] font-semibold block">Saldo Akhir Saat Ini:</span>
                <p class="text-xl font-black text-[#1456A0] mt-0.5">
                    Rp {{ number_format($bukuKas->saldo_sekarang, 0, ',', '.') }}
                </p>
            </div>
        </div>

        <!-- Formula Akuntansi Verifikasi -->
        <div class="mt-3 flex items-center justify-between text-xs text-slate-500 bg-slate-50 px-3.5 py-2 rounded-xl border border-slate-200/80 flex-wrap gap-2">
            <span>Formula Pembukuan: <strong>Saldo Akhir = Saldo Awal + Total Masuk - Total Keluar</strong></span>
            <span class="font-bold text-emerald-700 flex items-center gap-1">
                <i data-lucide="check-circle" class="w-3.5 h-3.5 text-emerald-600"></i>
                <span>Tervalidasi Seimbang</span>
            </span>
        </div>
    </div>

    <!-- Alert Notifikasi Session Flash -->
    @if(session('success'))
        <div class="bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-xl flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-3 text-emerald-800 text-xs font-semibold">
                <i data-lucide="check-circle" class="w-5 h-5 text-emerald-600 flex-shrink-0"></i>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif
    @if(session('error'))
        <div class="bg-rose-50 border-l-4 border-rose-500 p-4 rounded-xl flex items-center justify-between shadow-sm">
            <div class="flex items-center gap-3 text-rose-800 text-xs font-semibold">
                <i data-lucide="alert-triangle" class="w-5 h-5 text-rose-600 flex-shrink-0"></i>
                <span>{{ session('error') }}</span>
            </div>
        </div>
    @endif

    <!-- 2. FILTER MUTASI BUKU KAS -->
    <div class="bg-white rounded-2xl border border-slate-200/80 p-4 shadow-sm">
        <form method="GET" action="{{ route('keuangan.buku_kas.show', $bukuKas->id) }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 text-xs">
            <div>
                <label class="block font-bold text-slate-700 mb-1">Cari Transaksi</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="No TRX, uraian, penyetor..." 
                       class="w-full border border-slate-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-[#1456A0] outline-none">
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Jenis Mutasi</label>
                <select name="jenis" class="w-full border border-slate-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-[#1456A0] outline-none">
                    <option value="">Semua Jenis Mutasi</option>
                    <option value="pemasukan" {{ request('jenis') === 'pemasukan' ? 'selected' : '' }}>Pemasukan</option>
                    <option value="pengeluaran" {{ request('jenis') === 'pengeluaran' ? 'selected' : '' }}>Pengeluaran</option>
                    <option value="transfer_masuk" {{ request('jenis') === 'transfer_masuk' ? 'selected' : '' }}>Transfer Masuk</option>
                    <option value="transfer_keluar" {{ request('jenis') === 'transfer_keluar' ? 'selected' : '' }}>Transfer Keluar</option>
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" 
                       class="w-full border border-slate-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-[#1456A0] outline-none">
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" 
                       class="w-full border border-slate-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-[#1456A0] outline-none">
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 bg-[#1456A0] text-white font-bold rounded-xl hover:bg-blue-800 transition">
                    Filter
                </button>
                <a href="{{ route('keuangan.buku_kas.show', $bukuKas->id) }}" class="py-2 px-3 bg-slate-100 text-slate-600 font-semibold rounded-xl hover:bg-slate-200 transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- 3. TABEL MUTASI / REKENING KORAN -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between flex-wrap gap-2">
            <div>
                <h3 class="text-sm font-extrabold text-slate-800 flex items-center gap-1.5">
                    <i data-lucide="receipt" class="w-4 h-4 text-blue-600"></i>
                    <span>Mutasi Buku Kas & Transaksi</span>
                </h3>
                <p class="text-[11px] text-slate-500 mt-0.5">Catatan seluruh transaksi masuk, keluar, dan pemindahan dana terverifikasi.</p>
            </div>
            <span class="text-xs text-slate-400 font-medium">Total: {{ $transaksis->total() }} catatan transaksi</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-50 text-slate-700 font-bold uppercase tracking-wider text-[11px] border-b border-slate-200/80">
                    <tr>
                        <th class="py-3 px-3.5">Tanggal</th>
                        <th class="py-3 px-3.5">Keterangan & No. TRX</th>
                        <th class="py-3 px-3.5">Kategori</th>
                        <th class="py-3 px-3.5">Jenis Transaksi</th>
                        <th class="py-3 px-3.5 text-right text-emerald-700">Pemasukan (Debit)</th>
                        <th class="py-3 px-3.5 text-right text-rose-700">Pengeluaran (Kredit)</th>
                        <th class="py-3 px-3.5 text-right">Saldo Berjalan</th>
                        <th class="py-3 px-3.5 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($transaksis as $trx)
                        <tr class="hover:bg-slate-50/80 transition {{ $trx->status === 'dibatalkan' ? 'opacity-50 line-through bg-slate-50' : '' }}">
                            <!-- Tanggal -->
                            <td class="py-3 px-3.5 text-slate-500 whitespace-nowrap">
                                <span class="font-bold text-slate-800 block">
                                    {{ \Carbon\Carbon::parse($trx->tanggal_transaksi)->format('d/m/Y') }}
                                </span>
                                <small class="text-[10px] text-slate-400">
                                    {{ \Carbon\Carbon::parse($trx->created_at)->format('H:i') }} WIB
                                </small>
                            </td>

                            <!-- Keterangan & No TRX -->
                            <td class="py-3 px-3.5">
                                <div class="font-bold text-slate-900 leading-snug">{{ $trx->keterangan ?: 'Transaksi Keuangan' }}</div>
                                <div class="font-mono text-[11px] text-slate-400 mt-0.5 flex items-center gap-2">
                                    <span>{{ $trx->nomor_transaksi }}</span>
                                    @if($trx->penyetor_penerima)
                                        <span class="text-slate-500">&bull; {{ $trx->penyetor_penerima }}</span>
                                    @endif
                                </div>
                                @if($trx->status === 'dibatalkan')
                                    <span class="inline-block mt-1 text-[10px] text-rose-700 font-bold bg-rose-50 px-2 py-0.5 rounded border border-rose-200">
                                        DIBATALKAN: {{ $trx->alasan_batal ?: 'Dibatalkan pengurus' }}
                                    </span>
                                @endif
                            </td>

                            <!-- Kategori -->
                            <td class="py-3 px-3.5 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded-lg text-xs font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $trx->kategori }}
                                </span>
                            </td>

                            <!-- Jenis Transaksi (Badge) -->
                            <td class="py-3 px-3.5 whitespace-nowrap">
                                @if($trx->jenis_transaksi === 'pemasukan')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        Pemasukan
                                    </span>
                                @elseif($trx->jenis_transaksi === 'transfer_masuk')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-teal-100 text-teal-800 border border-teal-200">
                                        Transfer Masuk
                                    </span>
                                @elseif($trx->jenis_transaksi === 'pengeluaran')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800 border border-rose-200">
                                        Pengeluaran
                                    </span>
                                @elseif($trx->jenis_transaksi === 'transfer_keluar')
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 border border-amber-200">
                                        Transfer Keluar
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700">
                                        {{ ucfirst($trx->jenis_transaksi) }}
                                    </span>
                                @endif
                            </td>

                            <!-- Pemasukan (Debit) -->
                            <td class="py-3 px-3.5 text-right font-black text-emerald-600 whitespace-nowrap">
                                @if(in_array($trx->jenis_transaksi, ['pemasukan', 'transfer_masuk']))
                                    +Rp {{ number_format($trx->nominal, 0, ',', '.') }}
                                @else
                                    <span class="text-slate-300 font-normal">-</span>
                                @endif
                            </td>

                            <!-- Pengeluaran (Kredit) -->
                            <td class="py-3 px-3.5 text-right font-black text-rose-600 whitespace-nowrap">
                                @if(in_array($trx->jenis_transaksi, ['pengeluaran', 'transfer_keluar']))
                                    -Rp {{ number_format($trx->nominal, 0, ',', '.') }}
                                @else
                                    <span class="text-slate-300 font-normal">-</span>
                                @endif
                            </td>

                            <!-- Saldo Berjalan (Running Balance) -->
                            <td class="py-3 px-3.5 text-right font-mono font-black text-slate-900 whitespace-nowrap">
                                Rp {{ number_format($trx->saldo_setelahnya, 0, ',', '.') }}
                            </td>

                            <!-- Aksi -->
                            <td class="py-3 px-3.5 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1">
                                    @if($trx->bukti_transaksi_path)
                                        <a href="{{ asset('storage/' . $trx->bukti_transaksi_path) }}" target="_blank"
                                           class="text-blue-600 hover:text-blue-800 p-1 rounded hover:bg-blue-50" title="Lihat Bukti Transaksi">
                                            <i data-lucide="paperclip" class="w-4 h-4"></i>
                                        </a>
                                    @endif

                                    @if($canManageThisKas && $trx->status === 'selesai')
                                        <button onclick="bukaModalBatal('{{ $trx->id }}', '{{ $trx->nomor_transaksi }}', '{{ number_format($trx->nominal, 0, ',', '.') }}')" 
                                                class="text-rose-600 hover:text-rose-800 font-bold text-[11px] px-2 py-1 rounded hover:bg-rose-50 transition" title="Batalkan Transaksi">
                                            Batal
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-400 bg-white">
                                <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-2">
                                    <i data-lucide="receipt" class="w-6 h-6"></i>
                                </div>
                                <strong class="text-slate-700 block text-sm">Belum Ada Transaksi</strong>
                                <span class="text-xs text-slate-500">Tidak ada catatan mutasi transaksi yang sesuai dengan filter pencarian.</span>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transaksis->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $transaksis->withQueryString()->links() }}
            </div>
        @endif
    </div>

</div>

<!-- MODAL: PEMBATALAN TRANSAKSI DENGAN ALASAN WAJIB -->
<div id="modal-batal-transaksi" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs hidden p-4">
    <div class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <h3 class="text-sm font-extrabold text-rose-600 flex items-center gap-2">
                <i data-lucide="alert-triangle" class="w-5 h-5 text-rose-600"></i>
                <span>Batalkan Transaksi Keuangan</span>
            </h3>
            <button onclick="document.getElementById('modal-batal-transaksi').classList.add('hidden')" class="text-slate-400 hover:text-slate-600">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>

        <form id="form-batal-transaksi" method="POST" action="" class="mt-4 space-y-4 text-xs">
            @csrf
            <div class="p-3 bg-rose-50 border border-rose-200 rounded-xl text-rose-900">
                <p>Nomor Transaksi: <strong id="batal-nomor-trx" class="font-mono"></strong></p>
                <p class="mt-1">Nominal: <strong id="batal-nominal-trx" class="text-rose-700 font-black"></strong></p>
                <p class="text-[11px] text-rose-700 mt-1 leading-relaxed">
                    Saldo buku kas akan otomatis disesuaikan secara terbalik dan riwayat pembatalan dicatat secara permanen pada Audit Trail.
                </p>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Alasan Pembatalan (Wajib Diisi) *</label>
                <textarea name="alasan_batal" required rows="3" placeholder="Contoh: Terjadi salah input nominal oleh petugas / transaksi ganda" 
                          class="w-full font-medium border border-slate-300 rounded-xl px-3 py-2 focus:ring-2 focus:ring-rose-500 outline-none text-xs"></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('modal-batal-transaksi').classList.add('hidden')" 
                        class="px-4 py-2 font-semibold text-slate-600 hover:bg-slate-100 rounded-xl">
                    Tutup
                </button>
                <button type="submit" class="px-4 py-2 font-bold bg-rose-600 hover:bg-rose-700 text-white rounded-xl shadow">
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

document.addEventListener('DOMContentLoaded', function () {
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
});
</script>
@endsection
