<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Umkm; // Memanggil model UMKM
use Illuminate\Support\Facades\Storage; // Wajib ditambahkan untuk mengelola file/foto

class UmkmController extends Controller
{
    // Fungsi untuk menampilkan form edit
    public function edit($id)
    {
        $umkm = Umkm::findOrFail($id);
        
        // Cek keamanan: Hanya admin atau pemilik yang boleh masuk ke halaman ini
        if (auth()->user()->role !== 'admin' && auth()->user()->name !== $umkm->nama_pemilik) {
            abort(403, 'Anda tidak memiliki akses untuk mengedit UMKM ini.');
        }

        return view('umkm.edit', compact('umkm'));
    }

    // Fungsi untuk menyimpan perubahan ke database
    public function update(Request $request, $id)
    {
        $umkm = Umkm::findOrFail($id);
        
        // Cek keamanan lagi saat menyimpan data
        if (auth()->user()->role !== 'admin' && auth()->user()->name !== $umkm->nama_pemilik) {
            abort(403, 'Akses ditolak.');
        }

        // Validasi input form (sekarang ditambahkan validasi untuk file foto)
        $request->validate([
            'nama_umkm'   => 'required|string|max:255',
            'jenis_usaha' => 'required|string|max:255',
            'deskripsi'   => 'nullable|string',
            'foto'        => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Maksimal 2MB
        ]);

        // Ambil data teks saja terlebih dahulu
        $data = $request->only(['nama_umkm', 'jenis_usaha', 'deskripsi']);

        // LOGIKA UPLOAD FOTO
        if ($request->hasFile('foto')) {
            // Hapus foto lama dari penyimpanan (storage) jika ada, agar memori tidak penuh
            if ($umkm->foto && Storage::disk('public')->exists($umkm->foto)) {
                Storage::disk('public')->delete($umkm->foto);
            }
            
            // Simpan foto baru ke folder 'storage/app/public/umkm_photos'
            $data['foto'] = $request->file('foto')->store('umkm_photos', 'public');
        }

        // Simpan semua perubahan (termasuk path foto) ke database
        $umkm->update($data);

        // Kembalikan ke halaman daftar UMKM dengan pesan sukses
        return redirect()->route('umkm.index')
            ->with('success', 'Menu / Data UMKM berhasil diperbarui!');
    }
}