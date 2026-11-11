<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AkunRtSeeder extends Seeder
{
    /**
     * Jalankan seeder akun Ketua RT 001 s/d RT 008 di wilayah RW 013.
     * Bersifat idempotent & aman: tidak menimpa password jika akun sudah ada,
     * serta tidak mengubah akun admin/RW atau warga yang sudah terdaftar.
     */
    public function run(): void
    {
        $rtList = [
            [
                'rt_number' => '001',
                'name' => 'Bambang Suprayitno',
                'email' => 'rt001@warkas.machi.id',
                'phone' => '081298440101',
                'address' => 'Jl. Gandaria No. 12, RT 001 / RW 013',
                'house_number' => '012',
            ],
            [
                'rt_number' => '002',
                'name' => 'H. Agus Mulyadi',
                'email' => 'rt002@warkas.machi.id',
                'phone' => '081388210202',
                'address' => 'Jl. Kenanga No. 8, RT 002 / RW 013',
                'house_number' => '008',
            ],
            [
                'rt_number' => '003',
                'name' => 'Suryanto',
                'email' => 'rt003@warkas.machi.id',
                'phone' => '081577660303',
                'address' => 'Jl. Melati No. 5, RT 003 / RW 013',
                'house_number' => '005',
            ],
            [
                'rt_number' => '004',
                'name' => 'Wahyudi Kusuma',
                'email' => 'rt004@warkas.machi.id',
                'phone' => '081844550404',
                'address' => 'Jl. Anggrek No. 21, RT 004 / RW 013',
                'house_number' => '021',
            ],
            [
                'rt_number' => '005',
                'name' => 'Hendro Purnomo',
                'email' => 'rt005@warkas.machi.id',
                'phone' => '081911220505',
                'address' => 'Jl. Mawar No. 14, RT 005 / RW 013',
                'house_number' => '014',
            ],
            [
                'rt_number' => '006',
                'name' => 'Rahmat Hidayat',
                'email' => 'rt006@warkas.machi.id',
                'phone' => '081233440606',
                'address' => 'Jl. Flamboyan No. 19, RT 006 / RW 013',
                'house_number' => '019',
            ],
            [
                'rt_number' => '007',
                'name' => 'Eko Prasetyo',
                'email' => 'rt007@warkas.machi.id',
                'phone' => '081722330707',
                'address' => 'Jl. Dahlia No. 30, RT 007 / RW 013',
                'house_number' => '030',
            ],
            [
                'rt_number' => '008',
                'name' => 'Ir. Dedi Kurniawan',
                'email' => 'rt008@warkas.machi.id',
                'phone' => '081199880808',
                'address' => 'Jl. Cempaka No. 45, RT 008 / RW 013',
                'house_number' => '045',
            ],
        ];

        foreach ($rtList as $rtData) {
            // Cek apakah akun RT ini sudah ada (berdasarkan email atau kombinasi role='rt' dan rt_number)
            $existing = User::where('email', $rtData['email'])
                ->orWhere(function ($q) use ($rtData) {
                    $q->where('role', 'rt')->where('rt_number', $rtData['rt_number']);
                })
                ->first();

            if (!$existing) {
                User::create([
                    'name' => $rtData['name'],
                    'email' => $rtData['email'],
                    'password' => Hash::make('password123'),
                    'role' => 'rt',
                    'rt_number' => $rtData['rt_number'],
                    'rw_number' => '013',
                    'house_number' => $rtData['house_number'],
                    'phone' => $rtData['phone'],
                    'address' => $rtData['address'],
                    'status_rumah' => 'milik_sendiri',
                    'is_active' => true,
                ]);
            } else {
                // Update atribut tanpa menimpa password yang sudah ada
                $existing->update([
                    'name' => $rtData['name'],
                    'role' => 'rt',
                    'rt_number' => $rtData['rt_number'],
                    'rw_number' => '013',
                    'house_number' => $rtData['house_number'],
                    'phone' => $rtData['phone'],
                    'address' => $rtData['address'],
                    'is_active' => true,
                ]);
            }
        }
    }
}
