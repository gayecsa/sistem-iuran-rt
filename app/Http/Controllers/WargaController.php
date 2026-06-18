<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class WargaController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin,bendahara']);
    }

    public function index()
    {
        $warga = User::withSum(['pembayaran as total_pemasukan' => function ($query) {
                $query->where('status', 'lunas');
            }], 'jumlah_bayar')
            ->withSum('kasRt as total_pengeluaran', 'pengeluaran')
            ->where('role', 'warga')
            ->orderBy('house_number')
            ->paginate(100);

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

        // KODE DETEKTIF: Ini untuk mengecek apakah data dari form beneran masuk atau tidak
        dd($request->all());

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
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'warga',
            'rt_number' => '001',
            'house_number' => $request->house_number,
            'phone' => $request->phone,
            'address' => $request->address,
            'status_rumah' => $request->status_rumah,
            'nik' => $request->nik,
            'no_kk' => $request->no_kk,
            'gender' => $request->gender,
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
        ]);

        $warga->update($request->only(['name', 'phone', 'address', 'status_rumah', 'nik', 'no_kk', 'gender']));

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
}