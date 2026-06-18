<?php

namespace App\Http\Controllers;

use App\Models\Pengumuman;
use Illuminate\Http\Request;

class PengumumanController extends Controller
{
    public function index() 
    {
        // Mengambil semua pengumuman tanpa filter tanggal dulu untuk tes
        $semua_pengumuman = Pengumuman::latest()->get();
        
        return view('pengumuman.index', compact('semua_pengumuman'));
    }

    public function show($id)
    {
        $pengumuman = Pengumuman::findOrFail($id);
        return view('pengumuman.show', compact('pengumuman'));
    }

    public function detail($id)
    {
        $pengumuman = Pengumuman::findOrFail($id);
        return response()->json($pengumuman);
    }
}