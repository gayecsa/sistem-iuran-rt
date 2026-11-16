<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\PosyanduKegiatan;
use App\Models\PosyanduKader;
use App\Models\PosyanduPeserta;
use App\Models\PosyanduJadwal;
use App\Models\PosyanduKehadiran;
use App\Models\PosyanduPengukuran;
use App\Models\PosyanduLaporan;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PosyanduSystemSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Sinkronisasi Kader dari PosyanduKegiatan
        $units = PosyanduKegiatan::all();
        foreach ($units as $u) {
            $rt = str_pad($u->rt_number, 3, '0', STR_PAD_LEFT);
            if ($u->kaderUtama) {
                PosyanduKader::firstOrCreate(
                    [
                        'rt_number' => $rt,
                        'user_id' => $u->kader_utama_id,
                    ],
                    [
                        'nama_kader' => $u->kaderUtama->name,
                        'no_telepon' => $u->kaderUtama->phone ?? '0812' . rand(10000000, 99999999),
                        'jabatan' => 'Ketua Kader Posyandu RT ' . $rt,
                        'is_aktif' => true,
                        'catatan' => 'Kader utama penanggung jawab operasional RT ' . $rt,
                    ]
                );
            }
        }

        // 2. Hubungkan Warga Balita, Ibu Hamil & Lansia ke posyandu_pesertas
        $wargas = User::where('role', 'warga')->get();
        foreach ($wargas as $w) {
            $rt = str_pad(preg_replace('/[^0-9]/', '', (string)$w->rt_number) ?: '001', 3, '0', STR_PAD_LEFT);
            $dob = $w->tanggal_lahir ? Carbon::parse($w->tanggal_lahir) : null;
            if (!$dob) continue;

            $ageYears = $dob->age;
            $kategori = null;

            if ($ageYears <= 1) {
                $kategori = 'bayi';
            } elseif ($ageYears <= 5) {
                $kategori = 'balita';
            } elseif ($ageYears >= 60) {
                $kategori = 'lansia';
            } elseif ($w->gender === 'Perempuan' && $ageYears >= 21 && $ageYears <= 38 && $w->id % 7 === 0) {
                $kategori = 'ibu_hamil';
            }

            if ($kategori) {
                PosyanduPeserta::firstOrCreate(
                    [
                        'user_id' => $w->id,
                    ],
                    [
                        'kartu_keluarga_id' => $w->kartu_keluarga_id,
                        'rt_number' => $rt,
                        'kategori' => $kategori,
                        'status_aktif' => true,
                        'golongan_darah' => $w->id % 4 === 0 ? 'O' : ($w->id % 4 === 1 ? 'A' : ($w->id % 4 === 2 ? 'B' : 'AB')),
                        'tanggal_bergabung' => Carbon::now()->subMonths(rand(3, 12))->toDateString(),
                    ]
                );
            }
        }

        // 3. Buat Jadwal Rutin Realistis untuk 8 RT
        $layananStandar = [
            'Penimbangan balita & deteksi tumbuh kembang',
            'Pengukuran panjang/tinggi badan & lingkar kepala',
            'Edukasi gizi seimbang & pembagian PMT biskuit sehat',
            'Pemeriksaan berat badan & konsultasi ibu hamil',
            'Pemeriksaan tekanan darah & skrining kesehatan lansia',
            'Penyuluhan kesehatan keluarga & rujukan Puskesmas'
        ];

        for ($rtNum = 1; $rtNum <= 8; $rtNum++) {
            $rt = str_pad($rtNum, 3, '0', STR_PAD_LEFT);
            $unit = PosyanduKegiatan::where('rt_number', $rt)->orWhere('rt_number', (string)$rtNum)->first();
            $kader = PosyanduKader::where('rt_number', $rt)->first();
            $rtUser = User::where('role', 'rt')->where(function($q) use ($rt, $rtNum) {
                $q->where('rt_number', $rt)->orWhere('rt_number', (string)$rtNum);
            })->first();

            // A. Kegiatan Bulan Lalu (Status Selesai)
            $tglLalu = Carbon::now()->subDays(rand(14, 28));
            $jadwalLalu = PosyanduJadwal::firstOrCreate(
                [
                    'rt_number' => $rt,
                    'tanggal' => $tglLalu->toDateString(),
                ],
                [
                    'posyandu_unit_id' => $unit ? $unit->id : null,
                    'judul_kegiatan' => 'Posyandu Rutin Bulanan RT ' . $rt . ' - Penimbangan & Skrining',
                    'jam_mulai' => '08:30',
                    'jam_selesai' => '11:30',
                    'lokasi' => $unit ? $unit->lokasi : 'Balai Warga RT ' . $rt,
                    'jenis_layanan_list' => $layananStandar,
                    'mitra_nakes' => 'Bidan Nina & Tim UPTD Puskesmas Arcamanik',
                    'status' => 'selesai',
                    'created_by' => $rtUser ? $rtUser->id : null,
                ]
            );

            // B. Catat Kehadiran & Pengukuran untuk Kegiatan Lalu
            $pesertaRt = PosyanduPeserta::where('rt_number', $rt)->get();
            $hadirCount = 0;
            $bayiCount = 0;
            $bumilCount = 0;
            $lansiaCount = 0;

            foreach ($pesertaRt as $p) {
                $isHadir = rand(1, 10) <= 9; // 90% hadir
                PosyanduKehadiran::firstOrCreate(
                    [
                        'jadwal_id' => $jadwalLalu->id,
                        'peserta_id' => $p->id,
                    ],
                    [
                        'rt_number' => $rt,
                        'status_kehadiran' => $isHadir ? 'hadir' : 'tidak_hadir',
                        'jam_datang' => $isHadir ? '08:' . rand(35, 55) : null,
                        'petugas_id' => $kader ? $kader->user_id : null,
                        'catatan_kunjungan' => $isHadir ? 'Kondisi umum baik, aktif, kooperatif saat penimbangan.' : 'Izin ada keperluan keluarga di luar kota.',
                    ]
                );

                if ($isHadir) {
                    $hadirCount++;
                    if (in_array($p->kategori, ['bayi', 'balita', 'anak'])) {
                        $bayiCount++;
                        $bb = rand(85, 160) / 10.0;
                        $tb = rand(70, 105);
                        $lk = rand(44, 49);
                        PosyanduPengukuran::firstOrCreate(
                            [
                                'peserta_id' => $p->id,
                                'jadwal_id' => $jadwalLalu->id,
                            ],
                            [
                                'rt_number' => $rt,
                                'tanggal_pemeriksaan' => $tglLalu->toDateString(),
                                'berat_badan' => $bb,
                                'tinggi_badan' => $tb,
                                'lingkar_kepala' => $lk,
                                'status_pertumbuhan' => 'Pertumbuhan Baik Sesuai Kurva KMS (Nakes)',
                                'tindakan_layanan' => 'Pemberian PMT Biskuit Balita & Edukasi Menu 4 Bintang',
                                'catatan_petugas' => 'Pertumbuhan stabil, nafsu makan baik.',
                                'petugas_pencatat_id' => $kader ? $kader->user_id : null,
                            ]
                        );
                    } elseif ($p->kategori === 'ibu_hamil') {
                        $bumilCount++;
                        PosyanduPengukuran::firstOrCreate(
                            [
                                'peserta_id' => $p->id,
                                'jadwal_id' => $jadwalLalu->id,
                            ],
                            [
                                'rt_number' => $rt,
                                'tanggal_pemeriksaan' => $tglLalu->toDateString(),
                                'berat_badan' => rand(55, 68),
                                'usia_kehamilan_minggu' => rand(14, 28),
                                'tekanan_darah_sistol' => 110,
                                'tekanan_darah_diastol' => 75,
                                'status_pertumbuhan' => 'Kondisi Ibu & Janin Terpantau Baik',
                                'tindakan_layanan' => 'Konsultasi Nutrisi Kehamilan & Tablet Tambah Darah',
                                'catatan_petugas' => 'Tekanan darah normal, edukasi istirahat cukup.',
                                'petugas_pencatat_id' => $kader ? $kader->user_id : null,
                            ]
                        );
                    } elseif ($p->kategori === 'lansia') {
                        $lansiaCount++;
                        PosyanduPengukuran::firstOrCreate(
                            [
                                'peserta_id' => $p->id,
                                'jadwal_id' => $jadwalLalu->id,
                            ],
                            [
                                'rt_number' => $rt,
                                'tanggal_pemeriksaan' => $tglLalu->toDateString(),
                                'berat_badan' => rand(50, 72),
                                'tekanan_darah_sistol' => rand(120, 138),
                                'tekanan_darah_diastol' => rand(80, 88),
                                'status_pertumbuhan' => 'Tanda Vital Stabil (Skrining Rutin)',
                                'tindakan_layanan' => 'Pemeriksaan Tensi Darah & Edukasi Senam Lansia',
                                'catatan_petugas' => 'Keluhan pegal ringan, disarankan rutin gerak pagi.',
                                'petugas_pencatat_id' => $kader ? $kader->user_id : null,
                            ]
                        );
                    }
                }
            }

            // C. Laporan Kegiatan Selesai
            if ($rtNum <= 6) { // 6 RT sudah mengirim laporan, 2 RT (007 & 008) belum/draft
                PosyanduLaporan::firstOrCreate(
                    [
                        'jadwal_id' => $jadwalLalu->id,
                    ],
                    [
                        'rt_number' => $rt,
                        'nomor_laporan' => 'LAP-POS-' . $rt . '-' . $tglLalu->format('Ym') . '-01',
                        'tanggal_laporan' => $tglLalu->copy()->addDay()->toDateString(),
                        'total_hadir' => $hadirCount,
                        'total_bayi_balita' => $bayiCount,
                        'total_ibu_hamil' => $bumilCount,
                        'total_lansia' => $lansiaCount,
                        'ringkasan_layanan' => 'Pelaksanaan penimbangan balita, pembagian PMT gizi sehat, konsultasi ibu hamil, dan pemeriksaan tekanan darah lansia berjalan lancar dibantu Bidan Puskesmas Arcamanik.',
                        'kendala' => 'Timbangan dacin cadangan perlu kalibrasi ulang.',
                        'catatan_evaluasi_rt' => 'Kehadiran warga sangat antusias. Perlu tambahan kursi tunggu di balai RT.',
                        'penanggung_jawab_id' => $rtUser ? $rtUser->id : 1,
                        'status_laporan' => $rtNum <= 4 ? 'disetujui_rw' : 'dikirim_ke_rw',
                        'catatan_rw' => $rtNum <= 4 ? 'Laporan lengkap dan akuntabel. Tingkat kehadiran sangat baik, pertahankan sinergi dengan nakes Puskesmas.' : null,
                        'tanggal_evaluasi_rw' => $rtNum <= 4 ? Carbon::now()->subDays(2) : null,
                        'evaluator_rw_id' => $rtNum <= 4 ? 1 : null,
                    ]
                );
            }

            // D. Jadwal Berikutnya (Akan Datang)
            $tglDepan = Carbon::now()->addDays(rand(5, 18));
            PosyanduJadwal::firstOrCreate(
                [
                    'rt_number' => $rt,
                    'tanggal' => $tglDepan->toDateString(),
                ],
                [
                    'posyandu_unit_id' => $unit ? $unit->id : null,
                    'judul_kegiatan' => 'Layanan Terpadu Posyandu Balita & Lansia RT ' . $rt,
                    'jam_mulai' => '08:00',
                    'jam_selesai' => '11:00',
                    'lokasi' => $unit ? $unit->lokasi : 'Balai Warga RT ' . $rt,
                    'jenis_layanan_list' => $layananStandar,
                    'mitra_nakes' => 'UPTD Puskesmas Arcamanik',
                    'status' => 'akan_datang',
                    'created_by' => $rtUser ? $rtUser->id : null,
                    'keterangan' => 'Harap membawa buku KIA / KMS bagi balita dan ibu hamil.',
                ]
            );
        }
    }
}
