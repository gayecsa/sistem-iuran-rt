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
    
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'house_number' => 'required|string|max:10',
            'phone' => 'required|string|max:15',
            'address' => 'required|string',
            'nik' => 'nullable|string|max:20|unique:users,nik',
            'no_kk' => 'nullable|string|max:20|unique:users,no_kk',
        ]);
        
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'warga',
            'rt_number' => '001',
            'house_number' => $request->house_number,
            'phone' => $request->phone,
            'address' => $request->address,
            'status_rumah' => $request->status_rumah ?? 'milik_sendiri',
            'nik' => $request->nik,
            'no_kk' => $request->no_kk,
        ]);
        
        Auth::login($user);
        
        return redirect()->route('dashboard')->with('success', 'Pendaftaran berhasil! Selamat datang di RT 001');
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