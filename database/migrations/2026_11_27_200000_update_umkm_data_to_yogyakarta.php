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
        // 1. Update UMKM records that reference Bandung/Sukamiskin/Priangan/Arcamanik
        $replacements = [
            'Batagor & Siomay Bandung Juara Arcamanik' => 'Gudeg & Krecek Yu Djum Suryatmajan',
            'Seblak Jeletot Bandung Rasa' => 'Bakmi Jawa & Wedang Ronde Malioboro',
            'Surabi Kinca & Oncom Tradisional Arcamanik' => 'Kue Kipo & Yangko Tradisional Malioboro',
            'Cireng Crispy & Cimol Bumbu Rujak Bandung' => 'Bakpia & Pastry Khas Jogja Suryatmajan',
            'Bandung Bakery & Brownies Kukus Sukamiskin' => 'Jogja Bakery & Roti Sisir Malioboro',
            'Konveksi Kaos & Sablon Bandung Kreatif' => 'Batik & Kaos Sablon Malioboro Kreatif',
            'Laundry Express Wangi Arcamanik' => 'Laundry Express Wangi Suryatmajan',
            'Mie Kocok Bandung Asli Kang Arcamanik' => 'Soto Kadipiro & Nasi Campur Danurejan',
            'Kedai Kopi Priangan & Roastery' => 'Kedai Kopi Mataram & Roastery',
            'Cuanki & Bakso Aci Bandung Gurih' => 'Angkringan Kopi Jos & Nasi Kucing Malioboro',
            'Pisang Bolen & Pastry Lembang Sukamiskin' => 'Bakpia Kukus & Pastry Tugu Suryatmajan',
        ];

        foreach ($replacements as $oldName => $newName) {
            DB::table('umkm')->where('nama_umkm', $oldName)->update([
                'nama_umkm' => $newName,
            ]);
        }

        // 2. Update address street names and descriptions
        $addressUpdates = [
            'Arcamanik Endah' => 'Sosrowijayan',
            'Cisaranten Kulon' => 'Dagen',
            'Terusan Jakarta' => 'Beskalan',
            'Bina Asih' => 'Pajeksan',
            'Puri Asri' => 'Suryatmajan',
            'Pacuan Kuda' => 'Mataram',
            'Golf Raya' => 'Malioboro',
        ];

        foreach ($addressUpdates as $oldStreet => $newStreet) {
            DB::table('umkm')->where('alamat', 'LIKE', "%{$oldStreet}%")->update([
                'alamat' => DB::raw("REPLACE(alamat, '{$oldStreet}', '{$newStreet}')"),
            ]);
            DB::table('umkm')->where('deskripsi', 'LIKE', "%{$oldStreet}%")->update([
                'deskripsi' => DB::raw("REPLACE(deskripsi, '{$oldStreet}', '{$newStreet}')"),
            ]);
        }

        // 3. Update category and description references to Bandung / Lembang / Sukamiskin / Priangan
        DB::table('umkm')->where('jenis_usaha', 'LIKE', '%Bandung%')->update([
            'jenis_usaha' => DB::raw("REPLACE(jenis_usaha, 'Bandung', 'Jogja')"),
        ]);

        $textUpdates = [
            'Bandung' => 'Jogja',
            'Sukamiskin' => 'Suryatmajan',
            'Arcamanik' => 'Malioboro',
            'Priangan' => 'Mataram',
            'Lembang' => 'Malioboro',
        ];

        foreach ($textUpdates as $oldWord => $newWord) {
            DB::table('umkm')->where('deskripsi', 'LIKE', "%{$oldWord}%")->update([
                'deskripsi' => DB::raw("REPLACE(deskripsi, '{$oldWord}', '{$newWord}')"),
            ]);
            DB::table('umkm')->where('nama_umkm', 'LIKE', "%{$oldWord}%")->update([
                'nama_umkm' => DB::raw("REPLACE(nama_umkm, '{$oldWord}', '{$newWord}')"),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No reverse needed for seeder cleanup
    }
};
