<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Iuran;
use Illuminate\Support\Facades\Auth;

class IuranController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
        $this->middleware(['role:admin,bendahara'])->except(['index', 'show']);
    }
    
    public function index()
    {
        $iuran = Iuran::orderBy('created_at', 'desc')->paginate(10);
        return view('iuran.index', compact('iuran'));
    }
    
    public function create()
    {
        return view('iuran.create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'nama_iuran' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'jenis_iuran' => 'required|in:wajib,sukarela,kegiatan',
            'nominal' => 'required|numeric|min:0',
            'tanggal_mulai' => 'required|date',
            'periode' => 'required|in:bulanan,tahunan,sekali',
        ]);
        
        Iuran::create($request->all());
        
        return redirect()->route('iuran.index')
            ->with('success', 'Iuran berhasil ditambahkan!');
    }
    
    public function show(Iuran $iuran)
    {
        $pembayaran = $iuran->pembayaran()->with('user')->paginate(10);
        return view('iuran.show', compact('iuran', 'pembayaran'));
    }
    
    public function edit(Iuran $iuran)
    {
        return view('iuran.edit', compact('iuran'));
    }
    
    public function update(Request $request, Iuran $iuran)
    {
        $request->validate([
            'nama_iuran' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'jenis_iuran' => 'required|in:wajib,sukarela,kegiatan',
            'nominal' => 'required|numeric|min:0',
            'tanggal_mulai' => 'required|date',
            'status' => 'required|in:aktif,nonaktif',
        ]);
        
        $iuran->update($request->all());
        
        return redirect()->route('iuran.index')
            ->with('success', 'Iuran berhasil diupdate!');
    }
    
    public function destroy(Iuran $iuran)
    {
        $iuran->delete();
        return redirect()->route('iuran.index')
            ->with('success', 'Iuran berhasil dihapus!');
    }
}