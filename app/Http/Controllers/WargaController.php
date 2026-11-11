<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class WargaController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin,bendahara,rt,rw,super_admin']);
    }

    public function index(Request $request)
    {
        $user = auth()->user();

        // Membangun query dasar warga
        $query = User::withSum(['pembayaran as total_pemasukan' => function ($q) {
                $q->where('status', 'lunas');
            }], 'jumlah_bayar')
            ->withSum('kasRt as total_pengeluaran', 'pengeluaran')
            ->where('role', 'warga');

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

        // Menambahkan logika pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            
            // Dibungkus function($q) agar orWhere tidak mengabaikan where('role', 'warga')
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        // Eksekusi query dengan sorting dan paginasi
        $warga = $query->orderBy('house_number')->paginate(100);
        
        // Membawa parameter search & filter ke link paginasi
        $warga->appends($request->all());

        return view('warga.index', compact('warga'));
    }

    public function create()
    {
        $this->authorizeAdminOrBendahara();
        return view('warga.create');
    }

    public function store(Request $request)
    {
        $this->authorizeAdminOrBendahara();
        $currentUser = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'house_number' => 'required|string|unique:users',
            'phone' => 'required|string',
            'address' => 'required|string',
            'status_rumah' => 'required|in:milik_sendiri,kontrak,sewa',
            'nik' => 'nullable|string|max:20|unique:users',
            'no_kk' => 'nullable|string|max:20|unique:users',
            'gender' => 'nullable|in:Laki-laki,Perempuan',
            'rt_number' => 'nullable|string',
            'rw_number' => 'nullable|string',
            'tanggal_lahir' => 'nullable|date',
        ]);

        // Jika Ketua RT yang menambahkan, paksakan rt_number milik RT-nya sendiri
        $rtNumber = $currentUser->isRt() ? $currentUser->clean_rt_label : ($request->rt_number ?? 'RT 001');

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'warga',
            'rt_number' => $rtNumber,
            'rw_number' => $request->rw_number ?? 'RW 013',
            'house_number' => $request->house_number,
            'phone' => $request->phone,
            'address' => $request->address,
            'status_rumah' => $request->status_rumah,
            'nik' => $request->nik,
            'no_kk' => $request->no_kk,
            'gender' => $request->gender,
            'tanggal_lahir' => $request->tanggal_lahir,
        ]);

        return redirect()->route('warga.index')
            ->with('success', 'Warga berhasil ditambahkan!');
    }

    public function edit(User $warga)
    {
        $this->authorizeAdminOrBendahara($warga);
        return view('warga.edit', compact('warga'));
    }

    public function update(Request $request, User $warga)
    {
        $this->authorizeAdminOrBendahara($warga);
        $currentUser = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string',
            'address' => 'required|string',
            'status_rumah' => 'required|in:milik_sendiri,kontrak,sewa',
            'nik' => 'nullable|string|max:20|unique:users,nik,' . $warga->id,
            'no_kk' => 'nullable|string|max:20|unique:users,no_kk,' . $warga->id,
            'gender' => 'nullable|in:Laki-laki,Perempuan',
            'rt_number' => 'nullable|string',
            'rw_number' => 'nullable|string',
            'tanggal_lahir' => 'nullable|date',
        ]);

        $updateData = $request->only(['name', 'phone', 'address', 'status_rumah', 'nik', 'no_kk', 'gender', 'rw_number', 'tanggal_lahir']);
        
        // RT tidak boleh memindahkan warga ke RT lain
        if (!$currentUser->isRt() && $request->filled('rt_number')) {
            $updateData['rt_number'] = $request->rt_number;
        }

        $warga->update($updateData);

        if ($request->filled('password')) {
            $warga->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('warga.index')
            ->with('success', 'Data warga berhasil diupdate!');
    }

    public function destroy(User $warga)
    {
        $this->authorizeAdminOrBendahara($warga);

        $warga->delete();
        return redirect()->route('warga.index')
            ->with('success', 'Warga berhasil dihapus!');
    }

    public function toggleActive(User $warga)
    {
        $this->authorizeAdminOrBendahara($warga);

        $warga->update(['is_active' => ! $warga->is_active]);

        return redirect()->route('warga.index')
            ->with('success', 'Status warga berhasil diperbarui!');
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

    public function getKeluargaDetail($no_kk)
    {
        $members = User::where('no_kk', $no_kk)
            ->orderBy('tanggal_lahir', 'asc')
            ->get();

        if ($members->isEmpty()) {
            return response()->json(['message' => 'Data keluarga tidak ditemukan'], 404);
        }

        // Kepala keluarga = laki-laki dewasa (>=20) tertua; fallback: anggota tertua
        $isAdultMale = fn ($m, $max) => $m->gender === 'Laki-laki' && $m->tanggal_lahir
            && Carbon::parse($m->tanggal_lahir)->age >= 20 && Carbon::parse($m->tanggal_lahir)->age < $max;
        $headMember = $members->first(fn ($m) => $isAdultMale($m, 60))
            ?? $members->first(fn ($m) => $isAdultMale($m, 200))
            ?? $members->first();

        // Urutan: Kepala keluarga -> pasangan (perempuan dewasa) -> lainnya berdasarkan usia
        $members = $members->sortBy(function ($m) use ($headMember) {
            if ($m->id === $headMember->id) return '0';
            $age = $m->tanggal_lahir ? Carbon::parse($m->tanggal_lahir)->age : 0;
            $group = ($m->gender === 'Perempuan' && $age >= 20 && $age < 60) ? '1' : '2';
            return $group . '-' . ($m->tanggal_lahir ?? '9999');
        })->values();

        $head = $headMember;

        $formattedMembers = $members->map(function ($m) use ($headMember) {
            $age = $m->tanggal_lahir ? Carbon::parse($m->tanggal_lahir)->age : null;
            
            $peran = 'Anggota Keluarga';
            if ($m->id === $headMember->id) {
                $peran = 'Kepala Keluarga / Ayah';
            } else if ($age !== null && $age >= 60) {
                $peran = ($m->gender === 'Laki-laki') ? 'Kakek' : 'Nenek';
            } else if ($age !== null && $age >= 20) {
                $peran = ($m->gender === 'Perempuan') ? 'Ibu Rumah Tangga' : 'Anggota Keluarga';
            } else if ($age !== null && $age < 20) {
                $peran = ($age <= 5) ? 'Anak (Balita)' : 'Anak';
            }

            return [
                'id' => $m->id,
                'name' => $m->name,
                'nik' => $m->nik ?? '-',
                'gender' => $m->gender ?? '-',
                'phone' => $m->phone ?? '-',
                'tanggal_lahir' => $m->tanggal_lahir ? Carbon::parse($m->tanggal_lahir)->translatedFormat('d M Y') : '-',
                'usia' => $age !== null ? $age . ' tahun' : '-',
                'peran' => $peran,
                'is_active' => $m->is_active,
            ];
        });

        return response()->json([
            'no_kk' => $no_kk,
            'address' => $head->address,
            'status_rumah' => ucfirst(str_replace('_', ' ', $head->status_rumah ?? 'milik_sendiri')),
            'rt_rw' => 'RT ' . intval($head->rt_number) . ' / RW ' . intval($head->rw_number),
            'house_number' => $head->house_number,
            'total_anggota' => $members->count(),
            'members' => $formattedMembers,
        ]);
    }

    public function storeAnggotaKeluarga(Request $request, $no_kk)
    {
        $this->authorizeAdminOrBendahara();

        $sampleFamily = User::where('no_kk', $no_kk)->firstOrFail();

        $request->validate([
            'name' => 'required|string|max:255',
            'gender' => 'required|in:Laki-laki,Perempuan',
            'tanggal_lahir' => 'required|date',
            'nik' => 'nullable|string|max:20|unique:users,nik',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|unique:users,email',
        ]);

        $email = $request->email;
        if (!$email) {
            $count = User::count() + 1;
            $email = "warga{$count}_" . rand(100, 999) . "@rt001.com";
        }

        $nik = $request->nik;
        if (!$nik) {
            $nik = '32750' . str_pad(rand(1, 99999999991), 11, '0', STR_PAD_LEFT);
        }

        $newMember = User::create([
            'name' => $request->name,
            'email' => $email,
            'password' => Hash::make('password123'),
            'role' => 'warga',
            'rt_number' => $sampleFamily->rt_number ?? '001',
            'rw_number' => $sampleFamily->rw_number ?? '01',
            'house_number' => $sampleFamily->house_number ?? '000',
            'phone' => $request->phone ?? $sampleFamily->phone,
            'address' => $sampleFamily->address,
            'status_rumah' => $sampleFamily->status_rumah ?? 'milik_sendiri',
            'nik' => $nik,
            'no_kk' => $no_kk,
            'gender' => $request->gender,
            'tanggal_lahir' => $request->tanggal_lahir,
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Anggota keluarga baru berhasil ditambahkan!',
            'data' => $newMember
        ]);
    }
}