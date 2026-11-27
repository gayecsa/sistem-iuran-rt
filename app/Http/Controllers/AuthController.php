<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\LetterRequest;
use App\Models\Pengaduan;
use App\Models\PembayaranIuran;
use App\Models\KartuKeluarga;
use App\Models\ActivityLog;
use App\Models\Cctv;
use App\Models\Pengumuman;
use App\Models\WargaStatusHistory;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function showCover()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        $baseWargaQuery = User::whereIn('role', ['warga', 'rt']);
        $stats = [
            'total_rt' => 8,
            'total_kk' => KartuKeluarga::count(),
            'total_warga' => (clone $baseWargaQuery)->count(),
            'total_laki' => (clone $baseWargaQuery)->where('gender', 'Laki-laki')->count(),
            'total_perempuan' => (clone $baseWargaQuery)->where('gender', 'Perempuan')->count(),
            'total_cctv' => Cctv::where('aktif', true)->count(),
        ];

        $pengumumanTerbaru = Pengumuman::latest()->take(3)->get();

        return view('cover', compact('stats', 'pengumumanTerbaru'));
    }

    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function showLinkRequestForm()
    {
        return view('auth.passwords.email');
    }

    public function sendResetLinkEmail(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetForm($token)
    {
        return view('auth.passwords.reset', ['token' => $token, 'email' => request('email')]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                Auth::login($user);
            }
        );

        return $status === Password::PASSWORD_RESET
            ? redirect()->route('dashboard')->with('success', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }
    
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|min:6',
        ]);
        
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        $credentials = $request->only('email', 'password');
        
        if (Auth::attempt($credentials, $request->remember)) {
            $user = Auth::user();

            if (isset($user->is_active) && !$user->is_active) {
                Auth::logout();
                if ($request->hasSession()) {
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                }
                return back()->withErrors([
                    'email' => 'Akun Anda berstatus nonaktif. Silakan hubungi Sekretariat RW 013 untuk aktivasi kembali.',
                ])->withInput();
            }

            // Catat waktu login terakhir
            try {
                $user->forceFill(['last_login_at' => \Carbon\Carbon::now()])->saveQuietly();
            } catch (\Throwable $e) {
                // Ignore if column error
            }

            // Audit Trail: Catat login pengguna
            ActivityLog::record('auth', 'auth.login', "Pengguna {$user->name} ({$user->role}) berhasil login ke sistem.", [
                'role' => $user->role,
                'email' => $user->email,
            ], $user->id);

            if ($request->hasSession()) {
                $request->session()->regenerate();
            }
            
            if ($user->isSuperAdmin()) {
                return redirect()->route('dashboard.super-admin')->with('success', 'Selamat datang di Control Center Super Admin!');
            } elseif ($user->isRw() || $user->role === 'admin' || $user->role === 'bendahara') {
                return redirect()->route('dashboard.rw')->with('success', 'Selamat datang ' . $user->role_badge_label . '!');
            } elseif ($user->isRt() || $user->role === 'rt') {
                return redirect()->route('dashboard.rt')->with('success', 'Selamat datang ' . $user->role_badge_label . '!');
            } else {
                return redirect()->route('dashboard.warga')->with('success', 'Selamat datang di Layanan Warga RW 013!');
            }
        }
        
        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->withInput();
    }
    
    public function showRegister()
    {
        return view('auth.register');
    }

    /**
     * AJAX Endpoint: Auto-detect NIK dan Nomor KK saat registrasi mandiri
     */
    public function checkNikKk(Request $request)
    {
        $nik = preg_replace('/[^0-9]/', '', (string)$request->input('nik'));
        $noKk = preg_replace('/[^0-9]/', '', (string)$request->input('no_kk'));

        // Cek jika NIK sudah 16 digit
        if (strlen($nik) === 16) {
            $userByNik = User::where('nik', $nik)->first();

            if ($userByNik) {
                // Skenario 1 vs 2: Apakah akun sudah aktif diklaim oleh pemilik pribadi?
                // Akun dianggap belum diklaim mandiri jika last_login_at masih null dan emailnya berakhiran domain sistem internal (@haven.local, @havenportal.id, @rt001.com, @warkas.local)
                $emailDomain = substr(strrchr($userByNik->email, '@') ?: '', 1);
                $isInternalDomain = in_array($emailDomain, ['haven.local', 'havenportal.id', 'rt001.com', 'warkas.local', 'warga.local']);
                $isUnclaimed = ($userByNik->last_login_at === null && $isInternalDomain);

                // Dapatkan nama kepala keluarga dan RT
                $kk = null;
                if ($userByNik->kartu_keluarga_id) {
                    $kk = KartuKeluarga::find($userByNik->kartu_keluarga_id);
                } elseif ($userByNik->no_kk) {
                    $kk = KartuKeluarga::where('no_kk', $userByNik->no_kk)->first();
                }

                $kepalaKeluarga = $kk ? $kk->nama_kepala_keluarga : ($userByNik->name ?: 'Kepala Keluarga');
                $rtLabel = str_pad(intval($userByNik->rt_number ?: ($kk ? $kk->rt : 1)), 3, '0', STR_PAD_LEFT);

                if ($isUnclaimed) {
                    return response()->json([
                        'status' => 'claim_existing',
                        'message' => "Data NIK ditemukan di keluarga {$kepalaKeluarga} • RT {$rtLabel}. Anda dapat melanjutkan aktivasi akun ini.",
                        'user' => [
                            'id' => $userByNik->id,
                            'name' => $userByNik->name,
                            'nik' => $userByNik->nik,
                            'no_kk' => $userByNik->no_kk ?: ($kk ? $kk->no_kk : ''),
                            'gender' => $userByNik->gender,
                            'tempat_lahir' => $userByNik->tempat_lahir,
                            'tanggal_lahir' => $userByNik->tanggal_lahir ? (is_string($userByNik->tanggal_lahir) ? substr($userByNik->tanggal_lahir, 0, 10) : $userByNik->tanggal_lahir->format('Y-m-d')) : null,
                            'agama' => $userByNik->agama,
                            'pekerjaan' => $userByNik->pekerjaan,
                            'phone' => $userByNik->phone,
                            'rt_number' => $rtLabel,
                            'house_number' => $userByNik->house_number ?: ($kk ? $kk->nomor_rumah : ''),
                            'status_rumah' => $userByNik->status_rumah ?: ($kk ? $kk->status_rumah : 'Milik Sendiri'),
                            'address' => $userByNik->address ?: ($kk ? $kk->alamat : ''),
                            'hubungan_keluarga' => $userByNik->hubungan_keluarga ?: 'Anggota Keluarga',
                        ],
                        'family' => [
                            'nama_kepala_keluarga' => $kepalaKeluarga,
                            'rt' => $rtLabel,
                            'rw' => '013',
                        ]
                    ]);
                } else {
                    return response()->json([
                        'status' => 'nik_exists_active',
                        'message' => 'NIK ini sudah terdaftar dan memiliki akun aktif. Silakan masuk menggunakan email terdaftar Anda atau reset password jika lupa.',
                        'user_id' => $userByNik->id,
                    ]);
                }
            }
        }

        // Skenario 3: NIK belum ada di sistem, tapi No. KK sudah ada (16 digit)
        if (strlen($noKk) === 16) {
            $kkRecord = KartuKeluarga::where('no_kk', $noKk)->first();

            // Bisa juga mencari dari user lain yang punya no_kk ini jika record kartu_keluarga belum ada
            if (!$kkRecord) {
                $userWithKk = User::where('no_kk', $noKk)->first();
                if ($userWithKk) {
                    $rtVal = str_pad(intval($userWithKk->rt_number ?: 1), 3, '0', STR_PAD_LEFT);
                    return response()->json([
                        'status' => 'join_kk',
                        'message' => "Terhubung ke Kartu Keluarga {$userWithKk->name} • RT {$rtVal}.",
                        'kk' => [
                            'no_kk' => $noKk,
                            'nama_kepala_keluarga' => $userWithKk->name,
                            'rt' => $rtVal,
                            'rw' => '013',
                            'house_number' => $userWithKk->house_number,
                            'status_rumah' => $userWithKk->status_rumah ?: 'Milik Sendiri',
                            'address' => $userWithKk->address,
                        ]
                    ]);
                }
            } else {
                $rtVal = str_pad(intval($kkRecord->rt ?: 1), 3, '0', STR_PAD_LEFT);
                return response()->json([
                    'status' => 'join_kk',
                    'message' => "Terhubung ke Kartu Keluarga {$kkRecord->nama_kepala_keluarga} • RT {$rtVal}.",
                    'kk' => [
                        'id' => $kkRecord->id,
                        'no_kk' => $kkRecord->no_kk,
                        'nama_kepala_keluarga' => $kkRecord->nama_kepala_keluarga,
                        'rt' => $rtVal,
                        'rw' => '013',
                        'house_number' => $kkRecord->nomor_rumah,
                        'status_rumah' => $kkRecord->status_rumah ?: 'Milik Sendiri',
                        'address' => $kkRecord->alamat,
                    ]
                ]);
            }
        }

        // Skenario 4: NIK & KK baru
        return response()->json([
            'status' => 'new',
            'message' => 'NIK & Nomor KK belum terdata. Anda akan mendaftar sebagai warga / KK baru.',
        ]);
    }
    
    public function register(Request $request)
    {
        $nik = preg_replace('/[^0-9]/', '', (string)$request->input('nik'));
        $noKk = preg_replace('/[^0-9]/', '', (string)$request->input('no_kk'));
        $regMode = $request->input('reg_mode', 'new'); // 'claim_existing', 'join_kk', atau 'new'

        // Cek jika akun existing didata RT diklaim
        $claimUserId = $request->input('claim_user_id');
        $existingUser = null;
        if ($claimUserId) {
            $existingUser = User::find($claimUserId);
        } elseif ($nik && strlen($nik) === 16) {
            $candidate = User::where('nik', $nik)->first();
            if ($candidate) {
                $emailDomain = substr(strrchr($candidate->email, '@') ?: '', 1);
                $isInternalDomain = in_array($emailDomain, ['haven.local', 'havenportal.id', 'rt001.com', 'warkas.local', 'warga.local']);
                if ($candidate->last_login_at === null && $isInternalDomain) {
                    $existingUser = $candidate;
                }
            }
        }

        $emailRule = 'required|string|email|max:255|unique:users,email' . ($existingUser ? (',' . $existingUser->id) : '');
        $nikRule = 'required|string|size:16|regex:/^[0-9]{16}$/|unique:users,nik' . ($existingUser ? (',' . $existingUser->id) : '');

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => $emailRule,
            'password' => 'required|string|min:6|confirmed',
            'nik' => $nikRule,
            'no_kk' => 'required|string|size:16|regex:/^[0-9]{16}$/',
            'gender' => 'nullable|in:Laki-laki,Perempuan',
            'tanggal_lahir' => 'nullable|date|before_or_equal:today',
            'tempat_lahir' => 'nullable|string|max:100',
            'agama' => 'nullable|string|max:50',
            'pekerjaan' => 'nullable|string|max:100',
            'hubungan_keluarga' => 'nullable|string|max:50',
            'rt_number' => 'nullable|string|max:10',
            'house_number' => 'required|string|max:20',
            'phone' => 'required|string|max:20',
            'status_rumah' => 'required|string|max:50',
            'address' => 'required|string',
        ], [
            'nik.required' => 'NIK (16 Digit) wajib diisi sesuai KTP.',
            'nik.size' => 'NIK harus tepat 16 digit angka.',
            'nik.regex' => 'NIK hanya boleh berupa deretan angka 16 digit.',
            'nik.unique' => 'NIK ini sudah terdaftar dan aktif. Silakan masuk di halaman Login.',
            'no_kk.required' => 'Nomor KK (16 Digit) wajib diisi.',
            'no_kk.size' => 'Nomor KK harus tepat 16 digit angka.',
            'no_kk.regex' => 'Nomor KK hanya boleh berupa deretan angka 16 digit.',
            'password.confirmed' => 'Konfirmasi password tidak cocok dengan password.',
            'email.unique' => 'Email ini sudah digunakan oleh akun lain.',
        ]);
        
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $rtNumber = str_pad(intval($request->rt_number ?: 1), 3, '0', STR_PAD_LEFT);

        DB::beginTransaction();
        try {
            // Cek atau hubungkan KartuKeluarga
            $kkRecord = KartuKeluarga::where('no_kk', $noKk)->first();
            if (!$kkRecord) {
                $kkRecord = KartuKeluarga::create([
                    'no_kk' => $noKk,
                    'nama_kepala_keluarga' => ($request->hubungan_keluarga === 'Kepala Keluarga') ? $request->name : 'Keluarga ' . $request->name,
                    'nik_kepala_keluarga' => ($request->hubungan_keluarga === 'Kepala Keluarga') ? $nik : null,
                    'alamat' => $request->address,
                    'rt' => $rtNumber,
                    'rw' => '013',
                    'nomor_rumah' => $request->house_number,
                    'kelurahan' => 'Sukamiskin',
                    'kecamatan' => 'Arcamanik',
                    'kota' => 'Kota Bandung',
                    'provinsi' => 'Jawa Barat',
                    'kode_pos' => '40293',
                    'status_rumah' => $request->status_rumah,
                    'status_verifikasi_dukcapil' => 'Belum Ada Dokumen',
                    'status_dokumen' => 'Aktif',
                ]);
            }

            if ($existingUser) {
                // Skenario 1: Klaim akun yang sudah di-input oleh RT
                $existingUser->update([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'phone' => $request->phone,
                    'house_number' => $request->house_number,
                    'address' => $request->address,
                    'status_rumah' => $request->status_rumah,
                    'rt_number' => $rtNumber,
                    'gender' => $request->gender ?: $existingUser->gender,
                    'tanggal_lahir' => $request->tanggal_lahir ?: $existingUser->tanggal_lahir,
                    'tempat_lahir' => $request->tempat_lahir ?: $existingUser->tempat_lahir,
                    'agama' => $request->agama ?: ($existingUser->agama ?: 'Islam'),
                    'pekerjaan' => $request->pekerjaan ?: $existingUser->pekerjaan,
                    'hubungan_keluarga' => $request->hubungan_keluarga ?: ($existingUser->hubungan_keluarga ?: 'Kepala Keluarga'),
                    'kartu_keluarga_id' => $kkRecord->id,
                    'no_kk' => $noKk,
                    'is_active' => true,
                    'last_login_at' => Carbon::now(),
                ]);

                $user = $existingUser;

                // Catat history
                try {
                    WargaStatusHistory::create([
                        'user_id' => $user->id,
                        'kartu_keluarga_id' => $kkRecord->id,
                        'tipe_perubahan' => 'Aktivasi Akun Mandiri',
                        'status_lama' => 'Didata RT (Belum Aktif)',
                        'status_baru' => 'Akun Aktif Terverifikasi',
                        'petugas_id' => $user->id,
                        'petugas_nama' => $user->name,
                        'petugas_role' => 'warga',
                        'catatan' => 'Warga melakukan aktivasi dan verifikasi akun mandiri via Haven Portal.',
                        'created_at' => now(),
                    ]);
                } catch (\Throwable $e) {}

                DB::commit();
                Auth::login($user);

                return redirect()->route('dashboard.warga')->with('success', 'Selamat, akun Anda berhasil diaktivasi dan terhubung ke data RT ' . $rtNumber . '!');
            } else {
                // Skenario 3 & 4: Buat user baru (Join KK atau KK Baru)
                $user = User::create([
                    'name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'role' => 'warga',
                    'rt_number' => $rtNumber,
                    'rw_number' => '013',
                    'house_number' => $request->house_number,
                    'phone' => $request->phone,
                    'address' => $request->address,
                    'status_rumah' => $request->status_rumah,
                    'nik' => $nik,
                    'no_kk' => $noKk,
                    'kartu_keluarga_id' => $kkRecord->id,
                    'gender' => $request->gender ?: 'Laki-laki',
                    'tanggal_lahir' => $request->tanggal_lahir,
                    'tempat_lahir' => $request->tempat_lahir,
                    'agama' => $request->agama ?: 'Islam',
                    'pekerjaan' => $request->pekerjaan,
                    'hubungan_keluarga' => $request->hubungan_keluarga ?: 'Kepala Keluarga',
                    'status_domisili' => 'Warga Tetap',
                    'status_verifikasi' => 'Terverifikasi RT',
                    'is_active' => true,
                    'last_login_at' => Carbon::now(),
                ]);

                DB::commit();
                Auth::login($user);

                return redirect()->route('dashboard.warga')->with('success', 'Pendaftaran akun berhasil! Selamat datang di Haven Portal RW 013.');
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withErrors(['general' => 'Terjadi kesalahan saat memproses pendaftaran: ' . $e->getMessage()])->withInput();
        }
    }
    
    public function showProfile(Request $request)
    {
        $user = Auth::user();

        $tabMap = [
            '1' => 'biodata',
            '2' => 'akun',
            '3' => 'keamanan',
            '4' => 'notifikasi',
            '5' => 'aktivitas',
            '6' => 'privasi',
            'biodata' => 'biodata',
            'akun' => 'akun',
            'keamanan' => 'keamanan',
            'notifikasi' => 'notifikasi',
            'aktivitas' => 'aktivitas',
            'privasi' => 'privasi',
        ];
        $activeTab = $tabMap[$request->query('tab', 'biodata')] ?? 'biodata';

        // 1. Mini-statistik riil dari database
        $suratCount = LetterRequest::where('user_id', $user->id)->count();
        $pengaduanCount = Pengaduan::where('user_id', $user->id)->count();
        $iuranCount = 0;
        if ($user->kartu_keluarga_id) {
            $iuranCount += PembayaranIuran::where('kartu_keluarga_id', $user->kartu_keluarga_id)->count();
        }
        $iuranCount += DB::table('pembayaran')->where('user_id', $user->id)->count();
        $totalAktivitas = $suratCount + $pengaduanCount + $iuranCount;

        // 2. Data Kartu Keluarga untuk Tab 2 (Informasi Akun)
        $kartuKeluarga = null;
        if ($user->kartu_keluarga_id) {
            $kartuKeluarga = KartuKeluarga::with(['kepalaKeluarga', 'anggota'])->find($user->kartu_keluarga_id);
        } elseif ($user->no_kk) {
            $kartuKeluarga = KartuKeluarga::with(['kepalaKeluarga', 'anggota'])->where('no_kk', $user->no_kk)->first();
        }

        // 3. Tab 5: Riwayat Aktivitas Saya (Timeline riil)
        $surats = LetterRequest::where('user_id', $user->id)
            ->latest()
            ->take(15)
            ->get()
            ->map(function ($s) {
                return [
                    'type' => 'surat',
                    'icon' => 'file-text',
                    'badge_color' => '#1456A0',
                    'badge_bg' => '#EAF3FF',
                    'title' => 'Pengajuan Surat: ' . ($s->judul_surat ?: $s->jenis_surat),
                    'desc' => 'No: ' . ($s->nomor_surat ?: $s->nomor_registrasi ?: '-') . ' • Keperluan: ' . ($s->keperluan ?: '-'),
                    'status' => $s->status,
                    'date' => $s->created_at,
                ];
            });

        $pengaduans = Pengaduan::where('user_id', $user->id)
            ->latest()
            ->take(15)
            ->get()
            ->map(function ($p) {
                return [
                    'type' => 'pengaduan',
                    'icon' => 'message-square-warning',
                    'badge_color' => '#D97706',
                    'badge_bg' => '#FEF3C7',
                    'title' => 'Laporan Pengaduan: ' . ($p->kategori ?: 'Warga'),
                    'desc' => Str::limit($p->deskripsi, 90),
                    'status' => $p->status,
                    'date' => $p->created_at,
                ];
            });

        $iurans = collect();
        if ($user->kartu_keluarga_id) {
            $iurans = PembayaranIuran::where('kartu_keluarga_id', $user->kartu_keluarga_id)
                ->latest('tanggal_bayar')
                ->take(15)
                ->get()
                ->map(function ($i) {
                    return [
                        'type' => 'iuran',
                        'icon' => 'receipt',
                        'badge_color' => '#16865D',
                        'badge_bg' => '#E3F5EC',
                        'title' => 'Pembayaran Iuran: Rp ' . number_format($i->nominal_bayar, 0, ',', '.'),
                        'desc' => 'No. Bayar: ' . $i->nomor_pembayaran . ' • Kanal: ' . ($i->kanal_pembayaran ?: 'Tunai'),
                        'status' => $i->status,
                        'date' => $i->tanggal_bayar ?: $i->created_at,
                    ];
                });
        }

        $activities = $surats->concat($pengaduans)->concat($iurans)->sortByDesc('date')->values();

        // 4. Deteksi Sesi Aktif Saat Ini untuk Tab 3
        $userAgent = $request->userAgent() ?? '';
        $browser = 'Web Browser';
        if (strpos($userAgent, 'Edg') !== false) {
            $browser = 'Microsoft Edge';
        } elseif (strpos($userAgent, 'Chrome') !== false) {
            $browser = 'Google Chrome';
        } elseif (strpos($userAgent, 'Firefox') !== false) {
            $browser = 'Mozilla Firefox';
        } elseif (strpos($userAgent, 'Safari') !== false) {
            $browser = 'Apple Safari';
        }

        $platform = 'Desktop';
        if (strpos($userAgent, 'Windows') !== false) {
            $platform = 'Windows PC';
        } elseif (strpos($userAgent, 'Macintosh') !== false) {
            $platform = 'macOS';
        } elseif (strpos($userAgent, 'Linux') !== false) {
            $platform = 'Linux';
        } elseif (strpos($userAgent, 'Android') !== false) {
            $platform = 'Android Smartphone';
        } elseif (strpos($userAgent, 'iPhone') !== false || strpos($userAgent, 'iPad') !== false) {
            $platform = 'iOS Device';
        }

        $sessionInfo = [
            'ip' => $request->ip() ?: '127.0.0.1',
            'device' => "{$browser} on {$platform}",
            'browser' => $browser,
            'platform' => $platform,
            'last_active' => $user->last_login_at ? $user->last_login_at->translatedFormat('d M Y, H:i') . ' WIB' : now()->translatedFormat('d M Y, H:i') . ' WIB',
        ];

        return view('auth.profile', compact(
            'user',
            'activeTab',
            'suratCount',
            'pengaduanCount',
            'iuranCount',
            'totalAktivitas',
            'kartuKeluarga',
            'activities',
            'sessionInfo'
        ));
    }

    public function updateBiodata(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:25',
            'gender' => 'nullable|string|in:L,P,Laki-laki,Perempuan',
            'tempat_lahir' => 'nullable|string|max:100',
            'tanggal_lahir' => 'nullable|date',
            'agama' => 'nullable|string|max:50',
            'status_perkawinan' => 'nullable|string|max:50',
            'pekerjaan' => 'nullable|string|max:100',
            'blok_rumah' => 'nullable|string|max:50',
            'house_number' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
        ]);

        if (isset($validated['gender'])) {
            if ($validated['gender'] === 'L') $validated['gender'] = 'Laki-laki';
            if ($validated['gender'] === 'P') $validated['gender'] = 'Perempuan';
        }

        $user->update($validated);

        ActivityLog::record('profil', 'profile.biodata_updated', "Pengguna {$user->name} memperbarui data biodata profil.", [
            'changes' => array_keys($validated),
        ], $user->id);

        return redirect()->route('profile.edit', ['tab' => 'biodata'])->with('success', 'Informasi pribadi berhasil diperbarui.');
    }

    public function uploadFoto(Request $request)
    {
        $request->validate([
            'foto_profil' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $user = Auth::user();

        if ($user->foto_profil && Storage::disk('public')->exists($user->foto_profil)) {
            Storage::disk('public')->delete($user->foto_profil);
        }
        if ($user->profile_photo && Storage::disk('public')->exists($user->profile_photo) && $user->profile_photo !== $user->foto_profil) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        $file = $request->file('foto_profil');
        $filename = time() . '_' . $user->id . '.' . $file->extension();
        $path = $file->storeAs('avatars', $filename, 'public');

        $user->update([
            'foto_profil' => $path,
            'profile_photo' => $path,
        ]);

        return redirect()->route('profile.edit', ['tab' => 'biodata'])->with('success', 'Foto profil berhasil diperbarui.');
    }

    public function updateEmail(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'current_password' => 'required|string',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->route('profile.edit', ['tab' => 'akun'])
                ->withErrors(['current_password' => 'Password saat ini yang Anda masukkan salah.'])
                ->withInput();
        }

        $oldEmail = $user->email;
        $user->email = $request->email;
        $user->save();

        ActivityLog::record('profil', 'profile.email_updated', "Pengguna {$user->name} memperbarui alamat email menjadi {$request->email}.", [
            'old_email' => $oldEmail,
            'new_email' => $request->email,
        ], $user->id);

        return redirect()->route('profile.edit', ['tab' => 'akun'])->with('success', 'Email login berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required|string',
            'password' => 'required|string|min:8|confirmed',
        ], [
            'password.min' => 'Password baru minimal harus 8 karakter.',
            'password.confirmed' => 'Konfirmasi password baru tidak cocok.',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->route('profile.edit', ['tab' => 'keamanan'])
                ->withErrors(['current_password' => 'Password saat ini yang Anda masukkan salah.'])
                ->withInput();
        }

        $user->password = Hash::make($request->password);
        $user->save();

        ActivityLog::record('profil', 'profile.password_updated', "Pengguna {$user->name} berhasil mengubah kata sandi akun.", [], $user->id);

        return redirect()->route('profile.edit', ['tab' => 'keamanan'])->with('success', 'Password Anda berhasil diperbarui dengan aman.');
    }

    public function updateNotifications(Request $request)
    {
        $user = Auth::user();

        $prefs = [
            'notif_tagihan' => $request->boolean('notif_tagihan'),
            'notif_layanan' => $request->boolean('notif_layanan'),
            'notif_kegiatan' => $request->boolean('notif_kegiatan'),
        ];

        if ($user->isRt() || $user->isRw() || $user->role === 'super_admin' || $user->role === 'admin' || $user->role === 'bendahara') {
            $prefs['notif_konfirmasi_bayar'] = $request->boolean('notif_konfirmasi_bayar');
            $prefs['notif_verifikasi_surat'] = $request->boolean('notif_verifikasi_surat');
        }

        $user->notification_preferences = $prefs;
        $user->save();

        return redirect()->route('profile.edit', ['tab' => 'notifikasi'])->with('success', 'Preferensi notifikasi berhasil disimpan.');
    }

    public function updatePrivacy(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'phone_visibility' => 'required|in:internal,public',
        ]);

        $settings = [
            'mask_nik_kk' => $request->boolean('mask_nik_kk'),
            'phone_visibility' => $request->phone_visibility,
        ];

        $user->privacy_settings = $settings;
        $user->save();

        return redirect()->route('profile.edit', ['tab' => 'privasi'])->with('success', 'Pengaturan privasi berhasil diperbarui.');
    }

    public function updateProfile(Request $request)
    {
        // Backward-compatible fallback jika form lama mengirim ke route('profile.update')
        $user = Auth::user();

        if ($request->filled('password')) {
            return $this->updatePassword($request);
        }

        if ($request->filled('email') && $request->email !== $user->email) {
            return $this->updateEmail($request);
        }

        if ($request->hasFile('foto_profil') || $request->hasFile('profile_photo')) {
            $file = $request->file('foto_profil') ?? $request->file('profile_photo');
            $request->files->set('foto_profil', $file);
            return $this->uploadFoto($request);
        }

        return $this->updateBiodata($request);
    }

    public function makeAdmin(Request $request)
    {
        $user = Auth::user();
        $user->role = 'admin';
        $user->save();

        return redirect()->route('profile.edit')->with('success', 'Akun Anda sekarang sudah menjadi admin.');
    }
    
    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            ActivityLog::record('auth', 'auth.logout', "Pengguna {$user->name} ({$user->role}) telah logout dari sistem.", [
                'role' => $user->role,
                'email' => $user->email,
            ], $user->id);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('success', 'Anda telah logout');
    }
}