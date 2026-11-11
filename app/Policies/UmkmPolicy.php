<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Umkm;
use Illuminate\Auth\Access\HandlesAuthorization;

class UmkmPolicy
{
    use HandlesAuthorization;

    /**
     * Warga, RT, dan RW dapat melihat katalog publik
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Siapapun dapat melihat detail UMKM publik
     */
    public function view(?User $user, Umkm $umkm): bool
    {
        // Jika UMKM dinonaktifkan, hanya pemilik atau pengurus RT/RW yang boleh melihat
        if (!$umkm->is_active || $umkm->status_verifikasi === 'dinonaktifkan') {
            if (!$user) return false;
            return $user->id === $umkm->user_id || in_array($user->role, ['rw', 'admin', 'super_admin', 'rt']);
        }
        return true;
    }

    /**
     * Warga, RT, dan RW dapat mendaftarkan UMKM baru
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * HANYA PEMILIK USAHA YANG BERHAK MENGUBAH KONTEN/PROFIL UMKM
     * Administrator RW / RT TIDAK MEMILIKI HAK MENGUBAH DATA USAHA MILIK WARGA!
     * (Kecuali jika admin adalah pemilik usaha tersebut: $user->id === $umkm->user_id)
     */
    public function update(User $user, Umkm $umkm): bool
    {
        // Usaha yang belum terhubung tidak dapat diedit sebelum ditautkan ke akun pemilik
        if (!$umkm->user_id) {
            return false;
        }

        return $user->id === $umkm->user_id;
    }

    /**
     * HANYA PEMILIK USAHA YANG BERHAK MENGELOLA MENU / PRODUK (TAMBAH, EDIT, HAPUS, UBAH HARGA)
     * Admin RW / RT DILARANG KERAS MENGUBAH DAFTAR MENU DAN HARGA USAHA WARGA!
     */
    public function manageProduk(User $user, Umkm $umkm): bool
    {
        if (!$umkm->user_id) {
            return false;
        }

        return $user->id === $umkm->user_id;
    }

    /**
     * Menghapus usaha UMKM (Hanya pemilik)
     */
    public function delete(User $user, Umkm $umkm): bool
    {
        if (!$umkm->user_id) {
            return in_array($user->role, ['rw', 'admin', 'super_admin']);
        }

        return $user->id === $umkm->user_id;
    }

    /**
     * HAK MODERASI DAN PENGAWASAN (ADMIN RW & KETUA RT)
     * Mengatur status verifikasi dan publikasi (bukan mengubah konten produk)
     */
    public function moderate(User $user, Umkm $umkm): bool
    {
        if (in_array($user->role, ['rw', 'admin', 'super_admin'])) {
            return true;
        }

        if ($user->role === 'rt') {
            $userRt = str_pad(preg_replace('/[^0-9]/', '', $user->rt_number ?: '1'), 3, '0', STR_PAD_LEFT);
            $umkmRt = str_pad(preg_replace('/[^0-9]/', '', $umkm->rt_number ?: '1'), 3, '0', STR_PAD_LEFT);
            return $userRt === $umkmRt;
        }

        return false;
    }

    /**
     * Hak mengaitkan akun pemilik untuk UMKM yang berstatus 'belum_terhubung'
     */
    public function linkOwner(User $user, Umkm $umkm): bool
    {
        return in_array($user->role, ['rw', 'admin', 'super_admin']);
    }
}
