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
}