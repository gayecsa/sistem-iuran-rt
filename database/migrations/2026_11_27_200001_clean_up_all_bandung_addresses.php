<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $replacements = [
            'Cisaranten Kulon' => 'Dagen',
            'Cisaranten Baru' => 'Dagen',
            'Cisaranten' => 'Dagen',
            'Terusan Jakarta' => 'Beskalan',
            'Bina Asih' => 'Pajeksan',
            'Puri Asri' => 'Suryatmajan',
            'Kec. Arcamanik, Kota Bandung 40293' => 'Kec. Danurejan, Kota Yogyakarta 55213',
            'Kec. Arcamanik' => 'Kec. Danurejan',
            'Kota Bandung' => 'Kota Yogyakarta',
            'Bandung' => 'Yogyakarta',
            'Arcamanik' => 'Danurejan',
            'Sukamiskin' => 'Suryatmajan',
            '40293' => '55213',
            'Jawa Barat' => 'DI Yogyakarta',
        ];

        // 1. Update kartu_keluargas
        foreach ($replacements as $old => $new) {
            DB::table('kartu_keluargas')->where('alamat', 'LIKE', "%{$old}%")->update([
                'alamat' => DB::raw("REPLACE(alamat, '{$old}', '{$new}')"),
            ]);
        }

        // 2. Update users address
        foreach ($replacements as $old => $new) {
            DB::table('users')->where('address', 'LIKE', "%{$old}%")->update([
                'address' => DB::raw("REPLACE(address, '{$old}', '{$new}')"),
            ]);
        }

        // 3. Update wargas if any address column exists
        if (\Illuminate\Support\Facades\Schema::hasColumn('wargas', 'alamat')) {
            foreach ($replacements as $old => $new) {
                DB::table('wargas')->where('alamat', 'LIKE', "%{$old}%")->update([
                    'alamat' => DB::raw("REPLACE(alamat, '{$old}', '{$new}')"),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
