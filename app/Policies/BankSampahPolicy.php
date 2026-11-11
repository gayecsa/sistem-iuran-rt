<?php

namespace App\Policies;

use App\Models\User;
use App\Models\BankSampah;
use App\Models\BankSampahTransaksi;
use Illuminate\Auth\Access\HandlesAuthorization;

class BankSampahPolicy
{
    use HandlesAuthorization;

    /**
     * Hak akses melihat detail transaksi:
     * - Warga hanya bisa melihat transaksinya sendiri
     * - Petugas Bank Sampah di RT terkait
     * - Ketua RT di RT terkait
     * - Admin / Ketua RW
     */
    public function viewTransaksi(User $user, BankSampahTransaksi $transaksi): bool
    {
        if ($user->isRw()) {
            return true;
        }

        if ($user->id === $transaksi->user_id) {
            return true;
        }

        $cleanRt = str_pad(preg_replace('/[^0-9]/', '', (string)$transaksi->rt_number), 3, '0', STR_PAD_LEFT);

        if ($user->isPetugasBankSampah($cleanRt)) {
            return true;
        }

        if ($user->isRt()) {
            $userRt = str_pad(preg_replace('/[^0-9]/', '', (string)$user->rt_number), 3, '0', STR_PAD_LEFT);
            return $userRt === $cleanRt;
        }

        return false;
    }

    /**
     * Hak akses memproses alur operasional transaksi (terima, timbang, konfirmasi, tolak):
     * - Petugas Bank Sampah unit RT terkait
     * - Ketua RT unit RT terkait
     * - Admin / Ketua RW
     *
     * CATATAN: Transaksi yang sudah 'selesai' TIDAK BISA diedit sembarangan lewat jalur ini.
     */
    public function manageTransaksi(User $user, BankSampahTransaksi $transaksi): bool
    {
        $cleanRt = str_pad(preg_replace('/[^0-9]/', '', (string)$transaksi->rt_number), 3, '0', STR_PAD_LEFT);

        if ($user->isRw()) {
            return true;
        }

        if ($user->isPetugasBankSampah($cleanRt)) {
            return true;
        }

        if ($user->isRt()) {
            $userRt = str_pad(preg_replace('/[^0-9]/', '', (string)$user->rt_number), 3, '0', STR_PAD_LEFT);
            return $userRt === $cleanRt;
        }

        return false;
    }

    /**
     * Warga membatalkan transaksinya sendiri (hanya bila status masih 'menunggu')
     */
    public function cancelTransaksi(User $user, BankSampahTransaksi $transaksi): bool
    {
        return $user->id === $transaksi->user_id && $transaksi->status === 'menunggu';
    }

    /**
     * Hak akses mengubah tarif harga per kg / liter:
     * Hanya Admin / Pengurus RW.
     */
    public function manageTarif(User $user): bool
    {
        return $user->isRw();
    }

    /**
     * Hak akses menunjuk petugas Bank Sampah unit:
     * Hanya Admin / Pengurus RW.
     */
    public function manageUnit(User $user): bool
    {
        return $user->isRw();
    }

    /**
     * Hak akses koreksi transaksi selesai (Emergency Audit RW):
     * Hanya Admin RW dengan alasan tertulis dan pencatatan ledger kompensasi.
     */
    public function koreksiTransaksi(User $user): bool
    {
        return $user->isRw();
    }
}
