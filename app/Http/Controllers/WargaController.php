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
        $this->middleware(['auth', 'role:admin,bendahara']);
    }

    public function index(Request $request)
    {
        // Membangun query dasar sesuai aslinya
        $query = User::withSum(['pembayaran as total_pemasukan' => function ($q) {
                $q->where('status', 'lunas');
            }], 'jumlah_bayar')
            ->withSum('kasRt as total_pengeluaran', 'pengeluaran')
            ->where('role', 'warga');

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
        
        // Membawa parameter search ke link paginasi agar tidak reset saat pindah halaman
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

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'warga',
            'rt_number' => $request->rt_number ?? 'RT 001',
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
        $this->authorizeAdminOrBendahara();
        return view('warga.edit', compact('warga'));
    }

    public function update(Request $request, User $warga)
    {
        $this->authorizeAdminOrBendahara();

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

        $warga->update($request->only(['name', 'phone', 'address', 'status_rumah', 'nik', 'no_kk', 'gender', 'rt_number', 'rw_number', 'tanggal_lahir']));

        if ($request->filled('password')) {
            $warga->update(['password' => Hash::make($request->password)]);
        }

        return redirect()->route('warga.index')
            ->with('success', 'Data warga berhasil diupdate!');
    }

    public function destroy(User $warga)
    {
        $this->authorizeAdminOrBendahara();

        $warga->delete();
        return redirect()->route('warga.index')
            ->with('success', 'Warga berhasil dihapus!');
    }

    public function toggleActive(User $warga)
    {
        $this->authorizeAdminOrBendahara();

        $warga->update(['is_active' => ! $warga->is_active]);

        return redirect()->route('warga.index')
            ->with('success', 'Status warga berhasil diperbarui!');
    }

    protected function authorizeAdminOrBendahara()
    {
        abort_unless(in_array(auth()->user()->role, ['admin', 'bendahara']), 403);
    }

    public function getKeluargaDetail($no_kk)
    {
        $members = User::where('no_kk', $no_kk)
            ->orderBy('tanggal_lahir', 'asc')
            ->get();

        if ($members->isEmpty()) {
            return response()->json(['message' => 'Data keluarga tidak ditemukan'], 404);
        }

        $head = $members->first();

        $formattedMembers = $members->map(function ($m) {
            $age = $m->tanggal_lahir ? Carbon::parse($m->tanggal_lahir)->age : null;
            
            $peran = 'Anggota Keluarga';
            if ($age !== null && $age >= 20) {
                $peran = ($m->gender === 'Laki-laki') ? 'Kepala Keluarga / Ayah' : 'Ibu Rumah Tangga';
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