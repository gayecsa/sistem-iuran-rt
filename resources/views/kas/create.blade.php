@extends('layouts.app')

@section('content')
<style>
    /* Custom Theme: Pink Gradasi Biru */
    .bg-gradient-pink-blue {
        background: linear-gradient(135deg, #fd79a8 0%, #74b9ff 100%) !important;
        color: white !important;
    }
    
    .card-custom {
        border: none;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
        background-color: #ffffff;
    }

    .form-label-custom {
        font-weight: 600;
        color: #555555;
        font-size: 0.9rem;
    }

    .form-control-custom {
        border-radius: 8px;
        border: 1px solid #dee2e6;
        padding: 0.6rem 1rem;
        transition: all 0.2s ease-in-out;
    }

    .form-control-custom:focus {
        border-color: #74b9ff;
        box-shadow: 0 0 0 0.25rem rgba(116, 185, 255, 0.25);
    }

    .btn-gradient-save {
        background: linear-gradient(135deg, #fd79a8 0%, #74b9ff 100%);
        border: none;
        color: white;
        font-weight: 600;
        padding: 0.6rem 1.5rem;
        border-radius: 8px;
        transition: opacity 0.2s;
    }

    .btn-gradient-save:hover {
        opacity: 0.9;
        color: white;
    }

    .btn-outline-pink {
        border: 1.5px solid #fd79a8;
        color: #fd79a8;
        font-weight: 600;
        padding: 0.6rem 1.5rem;
        border-radius: 8px;
        background-color: transparent;
    }

    .btn-outline-pink:hover {
        background-color: rgba(253, 121, 168, 0.05);
        color: #fd79a8;
    }
</style>

<div class="animate-fadeInUp container py-4">
    <div class="card card-custom m-auto" style="max-width: 850px;">
        <div class="card-header bg-gradient-pink-blue py-3" style="border-top-left-radius: 12px; border-top-right-radius: 12px;">
            <h4 class="mb-0 fw-bold"><i class="fas fa-plus-circle me-2"></i>Catat Transaksi Baru</h4>
        </div>
        
        <div class="card-body p-4">
            {{-- Form menembak ke kas-rt.store dengan dukungn multi-part data gambar --}}
            <form action="{{ route('kas-rt.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                {{-- Baris 1: Tipe Transaksi & Tanggal --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label form-label-custom">Tipe Transaksi</label>
                        <select name="tipe" class="form-select form-control-custom @error('tipe') is-invalid @enderror" required>
                            <option value="pemasukan" {{ old('tipe') == 'pemasukan' ? 'selected' : '' }}>Pemasukan (Iuran / Donasi)</option>
                            <option value="pengeluaran" {{ old('tipe') == 'pengeluaran' ? 'selected' : '' }}>Pengeluaran Kas</option>
                        </select>
                        @error('tipe')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label form-label-custom">Tanggal</label>
                        <input type="date" name="tanggal_transaksi" class="form-control form-control-custom @error('tanggal_transaksi') is-invalid @enderror" value="{{ old('tanggal_transaksi', date('Y-m-d')) }}" required>
                        @error('tanggal_transaksi')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                {{-- Baris 2: Dropdown Pilih Warga (Sistem) --}}
                <div class="mb-4">
                    <label class="form-label form-label-custom">Pilih Warga (Sistem)</label>
                    <select name="pembayaran_id" class="form-select form-control-custom @error('pembayaran_id') is-invalid @enderror">
                        <option value="">-- Umum / Non-Warga --</option>
                        @foreach($pembayaran as $p)
                            <option value="{{ $p->id }}" {{ old('pembayaran_id') == $p->id ? 'selected' : '' }}>
                                {{ $p->nama_warga ?? 'Warga - ID '.$p->id }}
                            </option>
                        @endforeach
                    </select>
                    @error('pembayaran_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Baris 3: INPUT MANUAL (Nama, No HP, Bukti Gambar) --}}
                <div class="row mb-4">
                    <div class="col-md-4">
                        <label class="form-label form-label-custom">Nama Warga</label>
                        <input type="text" name="nama_warga" class="form-control form-control-custom @error('nama_warga') is-invalid @enderror" placeholder="Ketik Nama..." value="{{ old('nama_warga') }}">
                        @error('nama_warga')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label form-label-custom">No. HP Warga</label>
                        <input type="text" name="no_hp" class="form-control form-control-custom @error('no_hp') is-invalid @enderror" placeholder="Ketik No HP..." value="{{ old('no_hp') }}">
                        @error('no_hp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label form-label-custom">Bukti Pembayaran</label>
                        <input type="file" name="bukti_pembayaran" class="form-control form-control-custom @error('bukti_pembayaran') is-invalid @enderror" accept="image/*">
                        @error('bukti_pembayaran')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Baris 4: Kategori & Jumlah Nominal --}}
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label form-label-custom">Kategori</label>
                        <select name="kategori" class="form-select form-control-custom @error('kategori') is-invalid @enderror" required>
                            <option value="">-- Pilih Kategori --</option>
                            <option value="Iuran Bulanan" {{ old('kategori') == 'Iuran Bulanan' ? 'selected' : '' }}>Iuran Bulanan</option>
                            <option value="Donasi" {{ old('kategori') == 'Donasi' ? 'selected' : '' }}>Donasi</option>
                            <option value="Sosial" {{ old('kategori') == 'Sosial' ? 'selected' : '' }}>Sosial & Santunan</option>
                            <option value="Operasional" {{ old('kategori') == 'Operasional' ? 'selected' : '' }}>Operasional RT</option>
                            <option value="Lainnya" {{ old('kategori') == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                        </select>
                        @error('kategori')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label form-label-custom">Jumlah (Nominal Rp)</label>
                        <input type="number" name="jumlah" class="form-control form-control-custom @error('jumlah') is-invalid @enderror" placeholder="Contoh: 50000" value="{{ old('jumlah') }}" required>
                        @error('jumlah')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                
                {{-- Baris 5: Keterangan --}}
                <div class="mb-4">
                    <label class="form-label form-label-custom">Keterangan Tambahan</label>
                    <textarea name="keterangan" class="form-control form-control-custom @error('keterangan') is-invalid @enderror" rows="4" placeholder="Tulis detail catatan transaksi di sini...">{{ old('keterangan') }}</textarea>
                    @error('keterangan')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                {{-- Tombol Aksi --}}
                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('kas-rt.index') }}" class="btn btn-outline-pink">
                        <i class="fas fa-times-circle me-2"></i>Batal
                    </a>
                    <button type="submit" class="btn btn-gradient-save">
                        <i class="fas fa-save me-2"></i>Simpan Transaksi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection