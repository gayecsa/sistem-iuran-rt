@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h3 class="mb-1">Daftar Warga RT 001</h3>
            <p class="text-muted mb-0">Menampilkan identitas kependudukan, kontak, dan status warga.</p>
        </div>
        @if(auth()->user()->role === 'admin')
            <a href="{{ route('warga.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Tambah Warga
            </a>
        @endif
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card p-3 shadow-sm">
        <div class="mb-3">
            <span class="badge bg-info text-white">Total Warga: {{ $warga->total() }}</span>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>No. HP</th>
                        <th>No. KK</th>
                        <th>NIK</th>
                        <th>Gender</th>
                        <th>Alamat Rumah</th>
                        <th>Status</th>
                        @if(auth()->user()->role === 'admin')
                            <th>Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($warga as $item)
                        <tr>
                            <td>{{ $loop->iteration + ($warga->currentPage() - 1) * $warga->perPage() }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->email }}</td>
                            
                            <td>
                                @if($item->phone)
                                    <span class="text-dark">{{ $item->phone }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            <td class="font-monospace text-secondary" title="No. KK: {{ $item->no_kk }}">{{ $item->no_kk ?? '-' }}</td>
                            <td class="font-monospace text-secondary" title="NIK: {{ $item->nik }}">{{ $item->nik ?? '-' }}</td>
                            <td>
                                @if($item->gender === 'Laki-laki')
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle">Laki-laki</span>
                                @elseif($item->gender === 'Perempuan')
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle">Perempuan</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>

                            <td>{{ $item->address }}</td>
                            
                            <td>
                                <span class="badge {{ $item->is_active ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $item->is_active ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            @if(auth()->user()->role === 'admin')
                                <td>
                                    <div class="d-flex gap-1 flex-wrap">
                                        <a href="{{ route('warga.edit', $item) }}" class="btn btn-sm btn-outline-primary" title="Edit Warga">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('warga.destroy', $item) }}" method="POST" class="m-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Hapus warga ini?');" title="Hapus Warga">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('warga.toggleActive', $item) }}" method="POST" class="m-0">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-outline-secondary" title="Ubah Status Aktif">
                                                <i class="fas fa-power-off"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            {{-- SINKRONISASI: Kolom colspan disesuaikan menjadi 10 untuk Admin dan 9 untuk Umum --}}
                            <td colspan="{{ auth()->user()->role === 'admin' ? 10 : 9 }}" class="text-center text-muted">Belum ada data warga.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $warga->links() }}
        </div>
    </div>
</div>
@endsection