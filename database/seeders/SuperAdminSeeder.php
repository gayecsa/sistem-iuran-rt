<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Membuat atau memperbarui akun Super Admin (Idempotent).
     */
    public function run(): void
    {
        User::updateOrCreate(
            [
                'email' => 'annisasfzahra@gmail.com',
            ],
            [
                'name' => 'Super Admin',
                'password' => Hash::make(env('SUPER_ADMIN_PASSWORD', 'exclumsyve13')),
                'role' => 'super_admin',
                'rt_number' => '000',
                'rw_number' => '013',
                'phone' => '081234567800',
                'address' => 'Sekretariat RW 013 Suryatmajan',
                'is_active' => true,
                'status_verifikasi' => 'Terverifikasi RW',
            ]
        );
    }
}
