<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\KartuKeluarga;
use App\Models\WargaStatusHistory;
use App\Models\KkVerifikasiHistory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class WargaController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin,bendahara,rt,rw,super_admin']);
    }

    /**
     * Tampilan Utama Direktori Data Warga RW 013
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Membangun query dasar warga (termasuk Ketua RT sebagai bagian warga)
        $query = User::withSum(['pembayaran as total_pemasukan' => function ($q) {
                $q->where('status', 'lunas');
            }], 'jumlah_bayar')
            ->withSum('kasRt as total_pengeluaran', 'pengeluaran')
            ->with('kartuKeluarga')
            ->whereIn('role', ['warga', 'rt']);

        // PEMBATASAN AKSES HIERARKI RT & RW:
        // Ketua RT HANYA melihat warga wilayah RT-nya sendiri
        if ($user->isRt()) {
            $rtNum = $user->clean_rt_number;
            $query->where(function ($q) use ($rtNum) {
                $q->where('rt_number', $rtNum)
                  ->orWhere('rt_number', str_pad($rtNum, 3, '0', STR_PAD_LEFT))
                  ->orWhere('rt_number', 'RT ' . str_pad($rtNum, 3, '0', STR_PAD_LEFT))
                  ->orWhere('address', 'like', "%RT 00{$rtNum}%")
                  ->orWhere('address', 'like', "%RT {$rtNum}%");
            });
        } elseif ($request->filled('rt') && $request->rt !== 'all') {
            // Pengurus RW dapat memfilter tampilan per RT
            $filterRt = intval($request->rt);
            $query->where(function ($q) use ($filterRt) {
                $q->where('rt_number', $filterRt)
                  ->orWhere('rt_number', str_pad($filterRt, 3, '0', STR_PAD_LEFT))
                  ->orWhere('rt_number', 'RT ' . str_pad($filterRt, 3, '0', STR_PAD_LEFT))
                  ->orWhere('address', 'like', "%RT 00{$filterRt}%")
                  ->orWhere('address', 'like', "%RT {$filterRt}%");
            });
        }

        // Filter Status Verifikasi
        if ($request->filled('status_verifikasi') && $request->status_verifikasi !== 'all') {
            $query->where('status_verifikasi', $request->status_verifikasi);
        }

        // Filter Status Domisili
        if ($request->filled('status_domisili') && $request->status_domisili !== 'all') {
            $query->where('status_domisili', $request->status_domisili);
        }

        // Filter berdasarkan Kelompok Usia jika dipilih
        if ($request->filled('age_group') && $request->age_group !== 'all') {
            $now = Carbon::now();
            switch ($request->age_group) {
                case 'balita': // 0-5 tahun
                    $query->whereDate('tanggal_lahir', '>=', $now->copy()->subYears(5));
                    break;
                case 'anak': // 6-12 tahun
                    $query->whereDate('tanggal_lahir', '>=', $now->copy()->subYears(12))
                          ->whereDate('tanggal_lahir', '<', $now->copy()->subYears(5));
                    break;
                case 'remaja': // 13-19 tahun
                    $query->whereDate('tanggal_lahir', '>=', $now->copy()->subYears(19))
                          ->whereDate('tanggal_lahir', '<', $now->copy()->subYears(12));
                    break;
                case 'dewasa': // 20-55 tahun
                    $query->whereDate('tanggal_lahir', '>=', $now->copy()->subYears(55))
                          ->whereDate('tanggal_lahir', '<', $now->copy()->subYears(19));
                    break;
                case 'lansia': // 56+ tahun
                    $query->whereDate('tanggal_lahir', '<', $now->copy()->subYears(55));
                    break;
            }
        }

        // Menambahkan logika pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%")
                  ->orWhere('no_kk', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Eksekusi query dengan sorting dan paginasi
        $warga = $query->orderBy('house_number')->paginate(50);
        $warga->appends($request->all());

        // Statistik Ringkas Kependudukan untuk Header
        $statQuery = User::whereIn('role', ['warga', 'rt']);
        if ($user->isRt()) {
            $rtNum = $user->clean_rt_number;
            $statQuery->where(function ($q) use ($rtNum) {
                $q->where('rt_number', $rtNum)
                  ->orWhere('rt_number', str_pad($rtNum, 3, '0', STR_PAD_LEFT))
                  ->orWhere('rt_number', 'RT ' . str_pad($rtNum, 3, '0', STR_PAD_LEFT))
                  ->orWhere('address', 'like', "%RT 00{$rtNum}%")
                  ->orWhere('address', 'like', "%RT {$rtNum}%");
            });
        }

        $stats = [
            'total_warga' => (clone $statQuery)->count(),
            'total_terverifikasi' => (clone $statQuery)->whereIn('status_verifikasi', ['Terverifikasi RT', 'Terverifikasi RW'])->count(),
            'total_menunggu' => (clone $statQuery)->whereIn('status_verifikasi', ['Menunggu Verifikasi RT', 'Menunggu Verifikasi RW', 'Belum Diverifikasi'])->count(),
            'total_perlu_perbaikan' => (clone $statQuery)->whereIn('status_verifikasi', ['Perlu Perbaikan', 'Ditolak'])->count(),
            'total_kk' => (clone $statQuery)->whereNotNull('no_kk')->distinct('no_kk')->count('no_kk'),
        ];

        return view('warga.index', compact('warga', 'stats'));
    }

    /**
     * Halaman Tambah Warga Baru (Stepper Wizard 6 Tahap)
     */
    public function create()
    {
        $this->authorizeAdminOrBendahara();
        $currentUser = auth()->user();

        // Opsi RT: Jika Ketua RT, hanya RT-nya sendiri. Jika RW/Admin, RT 001 - RT 008.
        $availableRts = [];
        if ($currentUser->isRt()) {
            $rtNum = $currentUser->clean_rt_number;
            $availableRts[] = [
                'val' => str_pad($rtNum, 3, '0', STR_PAD_LEFT),
                'label' => 'RT ' . str_pad($rtNum, 3, '0', STR_PAD_LEFT)
            ];
        } else {
            for ($i = 1; $i <= 8; $i++) {
                $availableRts[] = [
                    'val' => str_pad($i, 3, '0', STR_PAD_LEFT),
                    'label' => 'RT ' . str_pad($i, 3, '0', STR_PAD_LEFT)
                ];
            }
        }

        return view('warga.create', compact('availableRts', 'currentUser'));
    }

    /**
     * Proses Simpan Data Warga Baru dengan Transaksi Database & Private File Storage
     */
    public function store(Request $request)
    {
        $this->authorizeAdminOrBendahara();
        $currentUser = auth()->user();

        // Validasi Ketat 16-Digit NIK, No. KK, dan Unik NIK/Email
        $request->validate([
            // Tahap 1: Identitas Pribadi
            'name' => 'required|string|max:255',
            'nik' => 'required|string|size:16|regex:/^[0-9]{16}$/|unique:users,nik',
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'required|date|before_or_equal:today',
            'gender' => 'required|in:Laki-laki,Perempuan',
            'status_perkawinan' => 'required|in:Belum Kawin,Kawin,Cerai Hidup,Cerai Mati',
            'agama' => 'required|in:Islam,Kristen,Katolik,Hindu,Buddha,Konghucu,Penghayat Kepercayaan',
            'kewarganegaraan' => 'required|in:WNI,WNA',
            'foto_profil' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',

            // Tahap 2: Data Keluarga
            'no_kk' => 'required|string|size:16|regex:/^[0-9]{16}$/',
            'hubungan_keluarga' => 'required|in:Kepala Keluarga,Istri,Suami,Anak,Orang Tua,Saudara,Famili Lain',

            // Tahap 3: Alamat & Tempat Tinggal
            'rt_number' => 'required|string',
            'rw_number' => 'nullable|string',
            'house_number' => 'required|string|max:20',
            'blok_rumah' => 'nullable|string|max:20',
            'gang_jalan' => 'nullable|string|max:100',
            'address' => 'required|string',
            'status_rumah' => 'required|in:Milik Sendiri,Kontrak,Kos,Menumpang,Lainnya',
            'status_domisili' => 'required|in:Warga Tetap,Warga Sementara,Pendatang Baru,Akan Pindah,Sudah Pindah,Meninggal Dunia',

            // Tahap 4: Kontak & Pekerjaan
            'email' => 'required|email|max:255|unique:users,email',
            'phone' => 'required|string|min:8|max:20',
            'pekerjaan' => 'nullable|string|max:100',
            'pendidikan_terakhir' => 'nullable|string|max:50',
            'password' => 'nullable|string|min:6',

            // Tahap 5: Dokumen Pendukung (Private Storage)
            'dokumen_ktp' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
            'dokumen_kk' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'dokumen_pendukung' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',

            // Aksi simpan (draft vs simpan langsung)
            'action_type' => 'nullable|in:draft,simpan_verifikasi'
        ], [
            'nik.size' => 'Nomor Induk Kependudukan (NIK) harus tepat 16 digit angka.',
            'nik.regex' => 'NIK hanya boleh berupa deretan 16 digit angka.',
            'nik.unique' => 'NIK sudah terdaftar dalam sistem Warkas Machi RW 013.',
            'no_kk.size' => 'Nomor Kartu Keluarga (KK) harus tepat 16 digit angka.',
            'no_kk.regex' => 'Nomor KK hanya boleh berupa deretan 16 digit angka.',
            'tanggal_lahir.before_or_equal' => 'Tanggal lahir tidak boleh berada di masa depan.',
            'email.unique' => 'Alamat email sudah digunakan oleh akun lain.',
        ]);

        // Pembatasan Otoritas RT: Ketua RT tidak boleh menambahkan warga ke RT lain
        if ($currentUser->isRt()) {
            $rtNumber = str_pad($currentUser->clean_rt_number, 3, '0', STR_PAD_LEFT);
        } else {
            $rtNumber = str_pad(intval($request->rt_number), 3, '0', STR_PAD_LEFT);
        }

        DB::beginTransaction();

        try {
            // 1. Tangani Upload File ke Private Storage (app/private/)
            $dokumenKtpPath = null;
            if ($request->hasFile('dokumen_ktp')) {
                $dokumenKtpPath = $request->file('dokumen_ktp')->store('private/dokumen_ktp', 'local');
            }

            $dokumenPendukungPath = null;
            if ($request->hasFile('dokumen_pendukung')) {
                $dokumenPendukungPath = $request->file('dokumen_pendukung')->store('private/dokumen_pendukung', 'local');
            }

            // Foto Profil (Publik untuk avatar visual)
            $fotoProfilPath = null;
            if ($request->hasFile('foto_profil')) {
                $fotoProfilPath = $request->file('foto_profil')->store('profile_photos', 'public');
            }

            // 2. Cek atau Buat Record Kartu Keluarga
            $noKk = trim($request->no_kk);
            $kartuKeluarga = KartuKeluarga::where('no_kk', $noKk)->first();

            $dokumenKkPath = null;
            $originalKkName = null;
            $fileKkSize = null;
            $fileKkMime = null;
            if ($request->hasFile('dokumen_kk')) {
                $file = $request->file('dokumen_kk');
                $dokumenKkPath = $file->store('private/dokumen_kk', 'local');
                $originalKkName = $file->getClientOriginalName();
                $fileKkSize = $file->getSize();
                $fileKkMime = $file->getMimeType();
            }

            if (!$kartuKeluarga) {
                $kartuKeluarga = KartuKeluarga::create([
                    'no_kk' => $noKk,
                    'nama_kepala_keluarga' => ($request->hubungan_keluarga === 'Kepala Keluarga') ? $request->name : 'Keluarga ' . $request->name,
                    'nik_kepala_keluarga' => ($request->hubungan_keluarga === 'Kepala Keluarga') ? $request->nik : null,
                    'alamat' => $request->address,
                    'rt' => $rtNumber,
                    'rw' => '013',
                    'blok' => $request->blok_rumah,
                    'gang_jalan' => $request->gang_jalan,
                    'nomor_rumah' => $request->house_number,
                    'kelurahan' => 'Sukamiskin',
                    'kecamatan' => 'Arcamanik',
                    'kota' => 'Kota Bandung',
                    'provinsi' => 'Jawa Barat',
                    'kode_pos' => '40293',
                    'status_rumah' => $request->status_rumah,
                    'dokumen_kk_path' => $dokumenKkPath,
                    'original_filename' => $originalKkName,
                    'file_size' => $fileKkSize,
                    'mime_type' => $fileKkMime,
                    'sumber_dokumen' => 'Salinan Resmi Dukcapil',
                    'status_verifikasi_dukcapil' => $dokumenKkPath ? 'Menunggu Verifikasi' : 'Belum Ada Dokumen',
                    'status_dokumen' => 'Aktif',
                    'created_by' => $currentUser->id,
                ]);
            } else {
                // Update file KK jika diunggah baru
                if ($dokumenKkPath) {
                    $kartuKeluarga->update([
                        'dokumen_kk_path' => $dokumenKkPath,
                        'original_filename' => $originalKkName,
                        'file_size' => $fileKkSize,
                        'mime_type' => $fileKkMime,
                        'status_verifikasi_dukcapil' => 'Menunggu Verifikasi',
                    ]);
                }
            }

            // Tentukan Status Verifikasi Awal
            $statusVerifikasi = 'Belum Diverifikasi';
            if ($request->action_type === 'simpan_verifikasi') {
                if ($currentUser->isRw()) {
                    $statusVerifikasi = 'Terverifikasi RW';
                } elseif ($currentUser->isRt()) {
                    $statusVerifikasi = 'Terverifikasi RT';
                } else {
                    $statusVerifikasi = 'Menunggu Verifikasi RT';
                }
            }

            // 3. Buat Akun / Data Warga
            $password = $request->filled('password') ? Hash::make($request->password) : Hash::make('warga12345');

            $warga = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $password,
                'role' => 'warga',
                'rt_number' => $rtNumber,
                'rw_number' => 'RW 013',
                'house_number' => $request->house_number,
                'blok_rumah' => $request->blok_rumah,
                'gang_jalan' => $request->gang_jalan,
                'phone' => $request->phone,
                'address' => $request->address,
                'status_rumah' => $request->status_rumah,
                'status_domisili' => $request->status_domisili,
                'status_verifikasi' => $statusVerifikasi,
                'nik' => $request->nik,
                'no_kk' => $noKk,
                'kartu_keluarga_id' => $kartuKeluarga->id,
                'hubungan_keluarga' => $request->hubungan_keluarga,
                'gender' => $request->gender,
                'tempat_lahir' => $request->tempat_lahir,
                'tanggal_lahir' => $request->tanggal_lahir,
                'agama' => $request->agama,
                'status_perkawinan' => $request->status_perkawinan,
                'pekerjaan' => $request->pekerjaan,
                'pendidikan_terakhir' => $request->pendidikan_terakhir,
                'kewarganegaraan' => $request->kewarganegaraan,
                'dokumen_ktp_path' => $dokumenKtpPath,
                'dokumen_pendukung_path' => $dokumenPendukungPath,
                'foto_profil' => $fotoProfilPath,
                'is_active' => true,
            ]);

            // Jika peran adalah Kepala Keluarga, update kartu_keluarga kepala_keluarga_id
            if ($request->hubungan_keluarga === 'Kepala Keluarga') {
                $kartuKeluarga->update([
                    'kepala_keluarga_id' => $warga->id,
                    'nama_kepala_keluarga' => $warga->name,
                    'nik_kepala_keluarga' => $warga->nik,
                ]);
            }

            // 4. Catat Audit Trail Awal
            WargaStatusHistory::create([
                'user_id' => $warga->id,
                'kartu_keluarga_id' => $kartuKeluarga->id,
                'tipe_perubahan' => 'pendaftaran_awal',
                'status_lama' => null,
                'status_baru' => $statusVerifikasi,
                'petugas_id' => $currentUser->id,
                'petugas_nama' => $currentUser->name,
                'petugas_role' => $currentUser->role,
                'catatan' => 'Pendaftaran data warga baru melalui wizard administrasi kependudukan terpadu RW 013.',
                'metadata' => [
                    'nik' => $warga->masked_nik,
                    'no_kk' => $kartuKeluarga->masked_no_kk,
                    'rt' => $rtNumber,
                    'hubungan' => $request->hubungan_keluarga,
                ],
                'created_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('warga.show', $warga->id)
                ->with('success', "Data warga {$warga->name} (NIK: {$warga->masked_nik}) berhasil disimpan dan terintegrasi dengan Kartu Keluarga!");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal menyimpan data warga: ' . $e->getMessage());
        }
    }

    /**
     * Tampilan Detail Profil Kependudukan Lengkap & Dokumen
     */
    public function show(User $warga)
    {
        $this->authorizeAdminOrBendahara($warga);

        $currentUser = auth()->user();
        $kartuKeluarga = $warga->kartuKeluarga ?? KartuKeluarga::where('no_kk', $warga->no_kk)->first();
        $anggotaKeluarga = $kartuKeluarga ? $kartuKeluarga->anggota : $warga->familyMembers();
        $statusHistories = $warga->statusHistories()->take(20)->get();

        return view('warga.show', compact('warga', 'kartuKeluarga', 'anggotaKeluarga', 'statusHistories', 'currentUser'));
    }

    /**
     * Tampilan Edit Biodata Warga
     */
    public function edit(User $warga)
    {
        $this->authorizeAdminOrBendahara($warga);
        $currentUser = auth()->user();

        $availableRts = [];
        if ($currentUser->isRt()) {
            $rtNum = $currentUser->clean_rt_number;
            $availableRts[] = [
                'val' => str_pad($rtNum, 3, '0', STR_PAD_LEFT),
                'label' => 'RT ' . str_pad($rtNum, 3, '0', STR_PAD_LEFT)
            ];
        } else {
            for ($i = 1; $i <= 8; $i++) {
                $availableRts[] = [
                    'val' => str_pad($i, 3, '0', STR_PAD_LEFT),
                    'label' => 'RT ' . str_pad($i, 3, '0', STR_PAD_LEFT)
                ];
            }
        }

        return view('warga.edit', compact('warga', 'availableRts', 'currentUser'));
    }

    /**
     * Update Biodata Warga dengan Pencatatan Riwayat Perubahan Status
     */
    public function update(Request $request, User $warga)
    {
        $this->authorizeAdminOrBendahara($warga);
        $currentUser = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|min:8|max:20',
            'email' => 'required|email|max:255|unique:users,email,' . $warga->id,
            'nik' => 'required|string|size:16|regex:/^[0-9]{16}$/|unique:users,nik,' . $warga->id,
            'no_kk' => 'required|string|size:16|regex:/^[0-9]{16}$/',
            'gender' => 'required|in:Laki-laki,Perempuan',
            'tanggal_lahir' => 'required|date|before_or_equal:today',
            'tempat_lahir' => 'nullable|string|max:100',
            'status_perkawinan' => 'nullable|string|max:50',
            'agama' => 'nullable|string|max:50',
            'pekerjaan' => 'nullable|string|max:100',
            'pendidikan_terakhir' => 'nullable|string|max:50',
            'kewarganegaraan' => 'nullable|string|max:20',
            'hubungan_keluarga' => 'required|in:Kepala Keluarga,Istri,Suami,Anak,Orang Tua,Saudara,Famili Lain',
            'status_rumah' => 'required|in:Milik Sendiri,Kontrak,Kos,Menumpang,Lainnya',
            'status_domisili' => 'required|in:Warga Tetap,Warga Sementara,Pendatang Baru,Akan Pindah,Sudah Pindah,Meninggal Dunia',
            'house_number' => 'required|string|max:20',
            'blok_rumah' => 'nullable|string|max:20',
            'gang_jalan' => 'nullable|string|max:100',
            'address' => 'required|string',
            'password' => 'nullable|string|min:6',
            'dokumen_ktp' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:4096',
            'dokumen_pendukung' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        DB::beginTransaction();

        try {
            $oldDomisili = $warga->status_domisili;
            $oldHubungan = $warga->hubungan_keluarga;

            $updateData = $request->only([
                'name', 'phone', 'email', 'nik', 'no_kk', 'gender', 'tanggal_lahir',
                'tempat_lahir', 'status_perkawinan', 'agama', 'pekerjaan', 'pendidikan_terakhir',
                'kewarganegaraan', 'hubungan_keluarga', 'status_rumah', 'status_domisili',
                'house_number', 'blok_rumah', 'gang_jalan', 'address'
            ]);

            // RT tidak boleh memindahkan warga ke RT lain
            if (!$currentUser->isRt() && $request->filled('rt_number')) {
                $updateData['rt_number'] = str_pad(intval($request->rt_number), 3, '0', STR_PAD_LEFT);
            }

            // Upload Dokumen KTP jika diperbarui
            if ($request->hasFile('dokumen_ktp')) {
                if ($warga->dokumen_ktp_path && Storage::disk('local')->exists($warga->dokumen_ktp_path)) {
                    Storage::disk('local')->delete($warga->dokumen_ktp_path);
                }
                $updateData['dokumen_ktp_path'] = $request->file('dokumen_ktp')->store('private/dokumen_ktp', 'local');
            }

            // Upload Dokumen Pendukung jika diperbarui
            if ($request->hasFile('dokumen_pendukung')) {
                if ($warga->dokumen_pendukung_path && Storage::disk('local')->exists($warga->dokumen_pendukung_path)) {
                    Storage::disk('local')->delete($warga->dokumen_pendukung_path);
                }
                $updateData['dokumen_pendukung_path'] = $request->file('dokumen_pendukung')->store('private/dokumen_pendukung', 'local');
            }

            if ($request->filled('password')) {
                $updateData['password'] = Hash::make($request->password);
            }

            $warga->update($updateData);

            // Rekam audit jika status domisili berubah
            if ($oldDomisili !== $request->status_domisili) {
                WargaStatusHistory::create([
                    'user_id' => $warga->id,
                    'kartu_keluarga_id' => $warga->kartu_keluarga_id,
                    'tipe_perubahan' => 'status_domisili',
                    'status_lama' => $oldDomisili,
                    'status_baru' => $request->status_domisili,
                    'petugas_id' => $currentUser->id,
                    'petugas_nama' => $currentUser->name,
                    'petugas_role' => $currentUser->role,
                    'catatan' => $request->catatan_perubahan ?? 'Pembaruan status domisili kependudukan oleh petugas.',
                    'created_at' => now(),
                ]);
            }

            DB::commit();

            return redirect()->route('warga.show', $warga->id)
                ->with('success', 'Data kependudukan warga berhasil diperbarui!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal memperbarui data warga: ' . $e->getMessage());
        }
    }

    /**
     * Proses Verifikasi Berjenjang Status Warga (RT & RW Level)
     */
    public function verifikasi(Request $request, User $warga)
    {
        $this->authorizeAdminOrBendahara($warga);
        $currentUser = auth()->user();

        $request->validate([
            'status_verifikasi' => 'required|in:Terverifikasi RT,Terverifikasi RW,Menunggu Verifikasi RW,Perlu Perbaikan,Ditolak',
            'catatan' => 'required|string|max:500',
        ]);

        // Validasi Kewenangan Role
        if ($currentUser->isRt() && in_array($request->status_verifikasi, ['Terverifikasi RW'])) {
            return back()->with('error', 'Ketua RT hanya berwenang memverifikasi hingga tingkat RT.');
        }

        $oldStatus = $warga->status_verifikasi;
        $warga->update([
            'status_verifikasi' => $request->status_verifikasi
        ]);

        // Catat ke Audit History
        WargaStatusHistory::create([
            'user_id' => $warga->id,
            'kartu_keluarga_id' => $warga->kartu_keluarga_id,
            'tipe_perubahan' => 'status_verifikasi',
            'status_lama' => $oldStatus,
            'status_baru' => $request->status_verifikasi,
            'petugas_id' => $currentUser->id,
            'petugas_nama' => $currentUser->name,
            'petugas_role' => $currentUser->role,
            'catatan' => $request->catatan,
            'created_at' => now(),
        ]);

        return back()->with('success', "Status kependudukan warga berhasil diubah menjadi: {$request->status_verifikasi}");
    }

    /**
     * Download / Stream Dokumen Identitas Sensitif dari Private Storage dengan Otorisasi Backend
     */
    public function downloadDokumen(Request $request, $id, $type)
    {
        $currentUser = auth()->user();
        $warga = User::findOrFail($id);

        // Otorisasi Ketat: Hanya RW, RT wilayah bersangkutan, atau Pemilik Data Akun Sendiri
        $isOwner = ($currentUser->id === $warga->id);
        $isRwAdmin = $currentUser->isRw();
        $isSameRt = ($currentUser->isRt() && $currentUser->clean_rt_number === $warga->clean_rt_number);

        if (!$isOwner && !$isRwAdmin && !$isSameRt) {
            abort(403, 'Akses Ditolak: Anda tidak memiliki izin untuk mengunduh dokumen kependudukan ini.');
        }

        $filePath = null;
        $downloadName = '';

        if ($type === 'ktp') {
            $filePath = $warga->dokumen_ktp_path;
            $downloadName = "KTP_{$warga->masked_nik}.pdf";
        } elseif ($type === 'pendukung') {
            $filePath = $warga->dokumen_pendukung_path;
            $downloadName = "Dokumen_Pendukung_{$warga->masked_nik}.pdf";
        } elseif ($type === 'kk') {
            $kk = $warga->kartuKeluarga;
            $filePath = $kk ? $kk->dokumen_kk_path : null;
            $downloadName = "KK_Resmi_{$warga->masked_no_kk}.pdf";
        }

        if (!$filePath || !Storage::disk('local')->exists($filePath)) {
            abort(404, 'Berkas dokumen belum tersedia atau file fisik tidak ditemukan di sistem arsip.');
        }

        $fullPath = Storage::disk('local')->path($filePath);
        $mime = mime_content_type($fullPath) ?: 'application/octet-stream';

        return response()->file($fullPath, [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="' . $downloadName . '"',
            'Cache-Control' => 'no-cache, private'
        ]);
    }

    /**
     * Reset Password Akun Warga secara Aman (Laravel Hash, Tidak Menampilkan Plaintext)
     */
    public function resetPassword(Request $request, User $warga)
    {
        $this->authorizeAdminOrBendahara($warga);
        $currentUser = auth()->user();

        $request->validate([
            'new_password' => 'required|string|min:6|confirmed'
        ]);

        $warga->update([
            'password' => Hash::make($request->new_password)
        ]);

        // Audit log
        WargaStatusHistory::create([
            'user_id' => $warga->id,
            'kartu_keluarga_id' => $warga->kartu_keluarga_id,
            'tipe_perubahan' => 'reset_password',
            'status_lama' => 'Aktif',
            'status_baru' => 'Password Direset',
            'petugas_id' => $currentUser->id,
            'petugas_nama' => $currentUser->name,
            'petugas_role' => $currentUser->role,
            'catatan' => 'Reset password kredensial login akun warga oleh pengurus wilayah.',
            'created_at' => now(),
        ]);

        return back()->with('success', "Password akun warga {$warga->name} berhasil diperbarui secara aman.");
    }

    public function destroy(User $warga)
    {
        $this->authorizeAdminOrBendahara($warga);

        $currentUser = auth()->user();
        WargaStatusHistory::create([
            'user_id' => $warga->id,
            'kartu_keluarga_id' => $warga->kartu_keluarga_id,
            'tipe_perubahan' => 'penghapusan_data',
            'status_lama' => $warga->status_domisili,
            'status_baru' => 'Dihapus dari Sistem',
            'petugas_id' => $currentUser->id,
            'petugas_nama' => $currentUser->name,
            'petugas_role' => $currentUser->role,
            'catatan' => 'Penghapusan data kependudukan warga.',
            'created_at' => now(),
        ]);

        $warga->delete();
        return redirect()->route('warga.index')
            ->with('success', 'Data warga berhasil dihapus dari sistem!');
    }

    public function toggleActive(User $warga)
    {
        $this->authorizeAdminOrBendahara($warga);
        $currentUser = auth()->user();

        $newActive = !$warga->is_active;
        $warga->update(['is_active' => $newActive]);

        WargaStatusHistory::create([
            'user_id' => $warga->id,
            'kartu_keluarga_id' => $warga->kartu_keluarga_id,
            'tipe_perubahan' => 'toggle_akun',
            'status_lama' => $newActive ? 'Nonaktif' : 'Aktif',
            'status_baru' => $newActive ? 'Aktif' : 'Nonaktif',
            'petugas_id' => $currentUser->id,
            'petugas_nama' => $currentUser->name,
            'petugas_role' => $currentUser->role,
            'catatan' => ($newActive ? 'Aktivasi' : 'Penonaktifan') . ' akses akun login warga oleh pengurus.',
            'created_at' => now(),
        ]);

        return redirect()->route('warga.index')
            ->with('success', 'Status aktivasi akun warga berhasil diperbarui!');
    }

    /**
     * API Detail Keluarga untuk Modal (Tetap Kompatibel 100% dengan Fitur Lama)
     */
    public function getKeluargaDetail($no_kk)
    {
        $members = User::where('no_kk', $no_kk)
            ->orderByRaw("
                CASE 
                    WHEN hubungan_keluarga = 'Kepala Keluarga' THEN 1
                    WHEN hubungan_keluarga = 'Istri' THEN 2
                    WHEN hubungan_keluarga = 'Suami' THEN 3
                    WHEN hubungan_keluarga = 'Anak' THEN 4
                    ELSE 5
                END, tanggal_lahir ASC
            ")
            ->get();

        if ($members->isEmpty()) {
            return response()->json(['message' => 'Data keluarga tidak ditemukan'], 404);
        }

        // Cari Kepala Keluarga
        $head = $members->first(fn ($m) => $m->hubungan_keluarga === 'Kepala Keluarga')
            ?? $members->first(fn ($m) => $m->gender === 'Laki-laki' && $m->tanggal_lahir && Carbon::parse($m->tanggal_lahir)->age >= 20)
            ?? $members->first();

        // Cari record KartuKeluarga resmi jika ada
        $kkRecord = KartuKeluarga::where('no_kk', $no_kk)->first();

        $formattedMembers = $members->map(function ($m) use ($head) {
            $age = $m->tanggal_lahir ? Carbon::parse($m->tanggal_lahir)->age : null;
            
            $peran = $m->hubungan_keluarga ?: 'Anggota Keluarga';
            if ($m->id === $head->id) {
                $peran = ($m->role === 'rt') ? 'Kepala Keluarga (Ketua ' . $m->clean_rt_label . ')' : 'Kepala Keluarga';
            }

            return [
                'id' => $m->id,
                'name' => $m->name,
                'avatar_url' => $m->avatar_url,
                'role' => $m->role,
                'nik' => $m->masked_nik,
                'nik_raw' => $m->nik,
                'gender' => $m->gender ?? '-',
                'phone' => $m->phone ?? '-',
                'tanggal_lahir' => $m->tanggal_lahir ? Carbon::parse($m->tanggal_lahir)->translatedFormat('d M Y') : '-',
                'usia' => $age !== null ? $age . ' tahun' : '-',
                'peran' => $peran,
                'status_domisili' => $m->status_domisili ?? 'Warga Tetap',
                'status_verifikasi' => $m->status_verifikasi ?? 'Terverifikasi RT',
                'is_active' => $m->is_active,
            ];
        });

        return response()->json([
            'no_kk' => $no_kk,
            'masked_no_kk' => $kkRecord ? $kkRecord->masked_no_kk : substr($no_kk, 0, 6) . '******' . substr($no_kk, -4),
            'head_name' => $head->name,
            'head_avatar' => $head->avatar_url,
            'head_role' => $head->role === 'rt' ? 'Ketua ' . $head->clean_rt_label : 'Kepala Keluarga',
            'address' => $head->address,
            'status_rumah' => ucfirst(str_replace('_', ' ', $head->status_rumah ?? 'Milik Sendiri')),
            'rt_rw' => 'RT ' . intval($head->rt_number) . ' / RW ' . intval($head->rw_number ?: 13),
            'house_number' => $head->house_number,
            'total_anggota' => $members->count(),
            'status_verifikasi_dukcapil' => $kkRecord ? $kkRecord->status_verifikasi_dukcapil : 'Belum Ada Dokumen',
            'can_print_kk' => $kkRecord ? $kkRecord->isVerifiedDukcapil() : false,
            'kk_id' => $kkRecord ? $kkRecord->id : null,
            'members' => $formattedMembers,
        ]);
    }

    /**
     * Tambah Anggota Keluarga Baru ke dalam KK yang Sudah Ada
     */
    public function storeAnggotaKeluarga(Request $request, $no_kk)
    {
        $this->authorizeAdminOrBendahara();
        $currentUser = auth()->user();

        $sampleFamily = User::where('no_kk', $no_kk)->firstOrFail();
        $kkRecord = KartuKeluarga::where('no_kk', $no_kk)->first();

        $request->validate([
            'name' => 'required|string|max:255',
            'gender' => 'required|in:Laki-laki,Perempuan',
            'tanggal_lahir' => 'required|date|before_or_equal:today',
            'nik' => 'required|string|size:16|regex:/^[0-9]{16}$/|unique:users,nik',
            'hubungan_keluarga' => 'required|in:Istri,Suami,Anak,Orang Tua,Saudara,Famili Lain,Kepala Keluarga',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|unique:users,email',
            'pekerjaan' => 'nullable|string|max:100',
            'agama' => 'nullable|string|max:50',
            'status_perkawinan' => 'nullable|string|max:50',
        ]);

        $email = $request->email;
        if (!$email) {
            $count = User::count() + 1;
            $email = "warga{$count}_" . rand(100, 999) . "@warkasmachi.id";
        }

        DB::beginTransaction();

        try {
            $newMember = User::create([
                'name' => $request->name,
                'email' => $email,
                'password' => Hash::make('warga12345'),
                'role' => 'warga',
                'rt_number' => $sampleFamily->rt_number ?? '001',
                'rw_number' => $sampleFamily->rw_number ?? '013',
                'house_number' => $sampleFamily->house_number ?? '000',
                'blok_rumah' => $sampleFamily->blok_rumah,
                'gang_jalan' => $sampleFamily->gang_jalan,
                'phone' => $request->phone ?? $sampleFamily->phone,
                'address' => $sampleFamily->address,
                'status_rumah' => $sampleFamily->status_rumah ?? 'Milik Sendiri',
                'status_domisili' => 'Warga Tetap',
                'status_verifikasi' => $currentUser->isRw() ? 'Terverifikasi RW' : 'Terverifikasi RT',
                'nik' => $request->nik,
                'no_kk' => $no_kk,
                'kartu_keluarga_id' => $kkRecord ? $kkRecord->id : null,
                'hubungan_keluarga' => $request->hubungan_keluarga,
                'gender' => $request->gender,
                'tanggal_lahir' => $request->tanggal_lahir,
                'pekerjaan' => $request->pekerjaan,
                'agama' => $request->agama ?? 'Islam',
                'status_perkawinan' => $request->status_perkawinan ?? 'Belum Kawin',
                'kewarganegaraan' => 'WNI',
                'is_active' => true,
            ]);

            // Audit log
            WargaStatusHistory::create([
                'user_id' => $newMember->id,
                'kartu_keluarga_id' => $kkRecord ? $kkRecord->id : null,
                'tipe_perubahan' => 'tambah_anggota_keluarga',
                'status_lama' => null,
                'status_baru' => $newMember->status_verifikasi,
                'petugas_id' => $currentUser->id,
                'petugas_nama' => $currentUser->name,
                'petugas_role' => $currentUser->role,
                'catatan' => "Penambahan anggota keluarga ({$request->hubungan_keluarga}) ke dalam KK No. {$no_kk}.",
                'created_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Anggota keluarga baru berhasil ditambahkan ke Kartu Keluarga!',
                'data' => $newMember
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan anggota keluarga: ' . $e->getMessage()
            ], 500);
        }
    }

    protected function authorizeAdminOrBendahara(User $warga = null)
    {
        $currentUser = auth()->user();
        abort_unless($currentUser && (in_array(strtolower($currentUser->role), ['admin', 'bendahara', 'rw', 'super_admin']) || $currentUser->isRt()), 403, 'Akses ditolak.');

        // Keamanan data antar-RT: Ketua RT dilarang mengelola warga dari RT lain
        if ($currentUser->isRt() && $warga) {
            $userRt = $currentUser->clean_rt_number;
            $wargaRt = $warga->clean_rt_number;
            if ($userRt !== $wargaRt) {
                abort(403, "Akses ditolak: Anda hanya memiliki kewenangan mengelola warga di {$currentUser->clean_rt_label}.");
            }
        }
    }
}
