<?php

namespace App\Http\Controllers;

use App\Models\Pengumuman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class PengumumanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request) 
    {
        $query = Pengumuman::query();

        // Filter Kategori
        if ($request->filled('kategori') && $request->kategori !== 'Semua') {
            $query->where('kategori', $request->kategori);
        }

        // Search Keyword
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('judul', 'like', "%{$s}%")
                  ->orWhere('isi', 'like', "%{$s}%");
            });
        }

        $semua_pengumuman = $query->orderBy('created_at', 'desc')->paginate(12)->withQueryString();
        $isPengurus = Auth::check() && in_array(Auth::user()->role, ['rw', 'rt', 'admin', 'super_admin']);

        return view('pengumuman.index', compact('semua_pengumuman', 'isPengurus'));
    }

    public function create()
    {
        $this->checkPengurusAccess();
        return view('pengumuman.create');
    }

    public function store(Request $request)
    {
        $this->checkPengurusAccess();

        $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
            'kategori' => 'required|string|max:50',
            'tanggal_aktif' => 'nullable|date',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
        ]);

        $gambarPath = null;
        if ($request->hasFile('gambar')) {
            $gambarPath = $request->file('gambar')->store('pengumuman', 'public');
        }

        Pengumuman::create([
            'judul' => $request->judul,
            'isi' => $request->isi,
            'kategori' => $request->kategori,
            'tanggal_aktif' => $request->tanggal_aktif ? Carbon::parse($request->tanggal_aktif) : Carbon::now(),
            'gambar' => $gambarPath,
        ]);

        return redirect()->route('pengumuman.index')->with('success', 'Pengumuman resmi baru berhasil dipublikasikan!');
    }

    public function show($id)
    {
        $pengumuman = Pengumuman::findOrFail($id);
        $relatedAnnouncements = Pengumuman::where('id', '!=', $id)
            ->latest()
            ->take(4)
            ->get();
        $isPengurus = Auth::check() && in_array(Auth::user()->role, ['rw', 'rt', 'admin', 'super_admin']);

        return view('pengumuman.show', compact('pengumuman', 'relatedAnnouncements', 'isPengurus'));
    }

    public function edit($id)
    {
        $this->checkPengurusAccess();
        $pengumuman = Pengumuman::findOrFail($id);
        return view('pengumuman.edit', compact('pengumuman'));
    }

    public function update(Request $request, $id)
    {
        $this->checkPengurusAccess();
        $pengumuman = Pengumuman::findOrFail($id);

        $request->validate([
            'judul' => 'required|string|max:255',
            'isi' => 'required|string',
            'kategori' => 'required|string|max:50',
            'tanggal_aktif' => 'nullable|date',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:3072',
        ]);

        $gambarPath = $pengumuman->gambar;
        if ($request->hasFile('gambar')) {
            if ($gambarPath && Storage::disk('public')->exists($gambarPath)) {
                Storage::disk('public')->delete($gambarPath);
            }
            $gambarPath = $request->file('gambar')->store('pengumuman', 'public');
        }

        $pengumuman->update([
            'judul' => $request->judul,
            'isi' => $request->isi,
            'kategori' => $request->kategori,
            'tanggal_aktif' => $request->tanggal_aktif ? Carbon::parse($request->tanggal_aktif) : $pengumuman->tanggal_aktif,
            'gambar' => $gambarPath,
        ]);

        return redirect()->route('pengumuman.show', $pengumuman->id)->with('success', 'Pengumuman berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $this->checkPengurusAccess();
        $pengumuman = Pengumuman::findOrFail($id);

        if ($pengumuman->gambar && Storage::disk('public')->exists($pengumuman->gambar)) {
            Storage::disk('public')->delete($pengumuman->gambar);
        }

        $pengumuman->delete();

        return redirect()->route('pengumuman.index')->with('success', 'Pengumuman berhasil dihapus.');
    }

    public function detail($id)
    {
        $pengumuman = Pengumuman::findOrFail($id);
        return response()->json($pengumuman);
    }

    private function checkPengurusAccess()
    {
        $user = Auth::user();
        if (!$user || !in_array($user->role, ['rw', 'rt', 'admin', 'super_admin'])) {
            abort(403, 'Hanya Pengurus RT/RW dan Administrator yang berwenang mengelola pengumuman.');
        }
    }
}
