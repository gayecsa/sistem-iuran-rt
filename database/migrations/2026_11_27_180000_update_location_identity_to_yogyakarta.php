<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Update users table addresses
        if (Schema::hasTable('users')) {
            DB::statement("
                UPDATE users 
                SET address = REPLACE(
                    REPLACE(
                        REPLACE(
                            REPLACE(
                                REPLACE(
                                    REPLACE(
                                        REPLACE(address, 'Sukamiskin', 'Suryatmajan'),
                                        'Arcamanik Endah', 'Sosrowijayan'
                                    ),
                                    'Arcamanik', 'Danurejan'
                                ),
                                'Pacuan Kuda', 'Malioboro'
                            ),
                            'Golf Raya', 'Mataram'
                        ),
                        'Kota Bandung', 'Kota Yogyakarta'
                    ),
                    '40293', '55213'
                )
                WHERE address LIKE '%Sukamiskin%' 
                   OR address LIKE '%Arcamanik%' 
                   OR address LIKE '%Bandung%' 
                   OR address LIKE '%40293%'
                   OR address LIKE '%Pacuan Kuda%'
                   OR address LIKE '%Golf Raya%'
            ");

            // Update any remaining "Bandung" in address
            DB::statement("UPDATE users SET address = REPLACE(address, 'Bandung', 'Kota Yogyakarta') WHERE address LIKE '%Bandung%'");
        }

        // 2. Update kartu_keluargas table
        if (Schema::hasTable('kartu_keluargas')) {
            DB::table('kartu_keluargas')->update([
                'kelurahan' => 'Suryatmajan',
                'kecamatan' => 'Danurejan',
                'kota' => 'Kota Yogyakarta',
                'kode_pos' => '55213',
            ]);

            DB::statement("
                UPDATE kartu_keluargas 
                SET alamat = REPLACE(
                    REPLACE(
                        REPLACE(
                            REPLACE(alamat, 'Pacuan Kuda', 'Malioboro'),
                            'Arcamanik Endah', 'Sosrowijayan'
                        ),
                        'Golf Raya', 'Mataram'
                    ),
                    'Sukamiskin', 'Suryatmajan'
                )
                WHERE alamat LIKE '%Pacuan Kuda%' 
                   OR alamat LIKE '%Arcamanik Endah%' 
                   OR alamat LIKE '%Golf Raya%' 
                   OR alamat LIKE '%Sukamiskin%'
            ");

            if (Schema::hasColumn('kartu_keluargas', 'catatan_verifikasi_dukcapil')) {
                DB::statement("
                    UPDATE kartu_keluargas 
                    SET catatan_verifikasi_dukcapil = REPLACE(catatan_verifikasi_dukcapil, 'Kota Bandung', 'Kota Yogyakarta')
                    WHERE catatan_verifikasi_dukcapil LIKE '%Kota Bandung%'
                ");
                DB::statement("
                    UPDATE kartu_keluargas 
                    SET catatan_verifikasi_dukcapil = REPLACE(catatan_verifikasi_dukcapil, 'Bandung', 'Kota Yogyakarta')
                    WHERE catatan_verifikasi_dukcapil LIKE '%Bandung%'
                ");
            }
        }

        // 3. Update letter_requests table
        if (Schema::hasTable('letter_requests')) {
            DB::statement("
                UPDATE letter_requests 
                SET kelurahan = 'Suryatmajan',
                    kecamatan = 'Danurejan',
                    kota = 'Kota Yogyakarta',
                    provinsi = 'DI Yogyakarta',
                    kode_pos = '55213'
            ");

            if (Schema::hasColumn('letter_requests', 'alamat_jalan')) {
                DB::statement("
                    UPDATE letter_requests 
                    SET alamat_jalan = REPLACE(
                        REPLACE(
                            REPLACE(
                                REPLACE(alamat_jalan, 'Pacuan Kuda', 'Malioboro'),
                                'Arcamanik Endah', 'Sosrowijayan'
                            ),
                            'Golf Raya', 'Mataram'
                        ),
                        'Sukamiskin', 'Suryatmajan'
                    )
                    WHERE alamat_jalan LIKE '%Pacuan Kuda%' 
                       OR alamat_jalan LIKE '%Arcamanik Endah%' 
                       OR alamat_jalan LIKE '%Golf Raya%' 
                       OR alamat_jalan LIKE '%Sukamiskin%'
                ");
            }

            if (Schema::hasColumn('letter_requests', 'rw_notes')) {
                DB::statement("
                    UPDATE letter_requests 
                    SET rw_notes = REPLACE(rw_notes, 'Bandung', 'Kota Yogyakarta')
                    WHERE rw_notes LIKE '%Bandung%'
                ");
            }
        }

        // 4. Update letter_types table
        if (Schema::hasTable('letter_types')) {
            DB::statement("
                UPDATE letter_types 
                SET template_body = REPLACE(
                    REPLACE(
                        REPLACE(template_body, 'Sukamiskin', 'Suryatmajan'),
                        'Arcamanik', 'Danurejan'
                    ),
                    'Kota Bandung', 'Kota Yogyakarta'
                )
                WHERE template_body LIKE '%Sukamiskin%' OR template_body LIKE '%Arcamanik%' OR template_body LIKE '%Kota Bandung%'
            ");
        }

        // 5. Update posyandu tables
        if (Schema::hasTable('posyandu_jadwals')) {
            DB::statement("
                UPDATE posyandu_jadwals 
                SET lokasi = REPLACE(lokasi, 'Sukamiskin', 'Suryatmajan')
                WHERE lokasi LIKE '%Sukamiskin%'
            ");
            if (Schema::hasColumn('posyandu_jadwals', 'mitra_nakes')) {
                DB::statement("
                    UPDATE posyandu_jadwals 
                    SET mitra_nakes = REPLACE(
                        REPLACE(mitra_nakes, 'Arcamanik', 'Danurejan'),
                        'Bandung', 'Yogyakarta'
                    )
                    WHERE mitra_nakes LIKE '%Arcamanik%' OR mitra_nakes LIKE '%Bandung%'
                ");
            }
        }

        if (Schema::hasTable('posyandu_kegiatans')) {
            DB::statement("
                UPDATE posyandu_kegiatans 
                SET lokasi = REPLACE(
                    REPLACE(
                        REPLACE(lokasi, 'Pacuan Kuda', 'Malioboro'),
                        'Golf Raya', 'Mataram'
                    ),
                    'Sukamiskin', 'Suryatmajan'
                )
                WHERE lokasi LIKE '%Pacuan Kuda%' OR lokasi LIKE '%Golf Raya%' OR lokasi LIKE '%Sukamiskin%'
            ");
            if (Schema::hasColumn('posyandu_kegiatans', 'mitra_kesehatan')) {
                DB::statement("
                    UPDATE posyandu_kegiatans 
                    SET mitra_kesehatan = REPLACE(
                        REPLACE(mitra_kesehatan, 'Arcamanik', 'Danurejan'),
                        'Bandung', 'Yogyakarta'
                    )
                    WHERE mitra_kesehatan LIKE '%Arcamanik%' OR mitra_kesehatan LIKE '%Bandung%'
                ");
            }
        }

        if (Schema::hasTable('posyandu_laporans')) {
            if (Schema::hasColumn('posyandu_laporans', 'catatan_evaluasi_rt')) {
                DB::statement("
                    UPDATE posyandu_laporans 
                    SET catatan_evaluasi_rt = REPLACE(catatan_evaluasi_rt, 'Puskesmas Arcamanik', 'Puskesmas Danurejan')
                    WHERE catatan_evaluasi_rt LIKE '%Puskesmas Arcamanik%'
                ");
            }
        }

        // 6. Update cctvs table
        if (Schema::hasTable('cctvs')) {
            DB::statement("
                UPDATE cctvs 
                SET lokasi = REPLACE(
                    REPLACE(
                        REPLACE(lokasi, 'Pacuan Kuda', 'Malioboro'),
                        'Golf Raya', 'Mataram'
                    ),
                    'Sukamiskin', 'Suryatmajan'
                )
                WHERE lokasi LIKE '%Pacuan Kuda%' OR lokasi LIKE '%Golf Raya%' OR lokasi LIKE '%Sukamiskin%'
            ");
        }

        // 7. Update jadwal_rondas table
        if (Schema::hasTable('jadwal_rondas')) {
            DB::statement("
                UPDATE jadwal_rondas 
                SET area_patroli = REPLACE(
                    REPLACE(
                        REPLACE(area_patroli, 'Pacuan Kuda', 'Malioboro'),
                        'Golf Raya', 'Mataram'
                    ),
                    'Sukamiskin', 'Suryatmajan'
                )
                WHERE area_patroli LIKE '%Pacuan Kuda%' OR area_patroli LIKE '%Golf Raya%' OR area_patroli LIKE '%Sukamiskin%'
            ");
        }

        // 8. Update bank_sampahs table
        if (Schema::hasTable('bank_sampahs')) {
            DB::statement("
                UPDATE bank_sampahs 
                SET lokasi = REPLACE(
                    REPLACE(
                        REPLACE(lokasi, 'Golf Raya', 'Mataram'),
                        'Pacuan Kuda', 'Malioboro'
                    ),
                    'Sukamiskin', 'Suryatmajan'
                )
                WHERE lokasi LIKE '%Golf Raya%' OR lokasi LIKE '%Pacuan Kuda%' OR lokasi LIKE '%Sukamiskin%'
            ");
        }

        // 9. Update umkm table
        if (Schema::hasTable('umkm')) {
            DB::statement("
                UPDATE umkm 
                SET alamat = REPLACE(
                    REPLACE(
                        REPLACE(alamat, 'Sukamiskin', 'Suryatmajan'),
                        'Pacuan Kuda', 'Malioboro'
                    ),
                    'Golf Raya', 'Mataram'
                )
                WHERE alamat LIKE '%Sukamiskin%' OR alamat LIKE '%Pacuan Kuda%' OR alamat LIKE '%Golf Raya%'
            ");
        }

        // 10. Update buku_kas table
        if (Schema::hasTable('buku_kas')) {
            DB::statement("
                UPDATE buku_kas 
                SET nama_kas = REPLACE(nama_kas, 'Sukamiskin', 'Suryatmajan')
                WHERE nama_kas LIKE '%Sukamiskin%'
            ");
        }

        // 11. Update wilayah_administratifs coordinates to Yogyakarta
        if (Schema::hasTable('wilayah_administratifs')) {
            DB::statement("
                UPDATE wilayah_administratifs 
                SET latitude = -7.792600 + (latitude - (-6.915000)) * 0.5,
                    longitude = 110.365800 + (longitude - 107.679000) * 0.5
                WHERE latitude BETWEEN -7.0 AND -6.8
            ");

            DB::statement("
                UPDATE wilayah_administratifs 
                SET keterangan = REPLACE(
                    REPLACE(
                        REPLACE(
                            REPLACE(keterangan, 'Sukamiskin', 'Suryatmajan'),
                            'Arcamanik', 'Danurejan'
                        ),
                        'Pacuan Kuda', 'Malioboro'
                    ),
                    'Golf Raya', 'Mataram'
                )
                WHERE keterangan LIKE '%Sukamiskin%' 
                   OR keterangan LIKE '%Arcamanik%' 
                   OR keterangan LIKE '%Pacuan Kuda%' 
                   OR keterangan LIKE '%Golf Raya%'
            ");
        }

        // 12. Update iot_devices coordinates for GATE-01
        if (Schema::hasTable('iot_devices')) {
            DB::statement("
                UPDATE iot_devices 
                SET gate_latitude = -7.792600,
                    gate_longitude = 110.365800,
                    name = REPLACE(name, 'Sukamiskin', 'Suryatmajan')
                WHERE device_code = 'GATE-01' OR name LIKE '%Sukamiskin%'
            ");
        }

        // 13. Update activity_logs table
        if (Schema::hasTable('activity_logs')) {
            DB::statement("
                UPDATE activity_logs 
                SET description = REPLACE(
                    REPLACE(description, 'Sukamiskin', 'Suryatmajan'),
                    'Arcamanik', 'Danurejan'
                )
                WHERE description LIKE '%Sukamiskin%' OR description LIKE '%Arcamanik%'
            ");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back if needed
    }
};
