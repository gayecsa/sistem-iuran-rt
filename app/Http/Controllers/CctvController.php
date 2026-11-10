<?php

namespace App\Http\Controllers;

use App\Models\Cctv;
use Illuminate\Http\Request;

class CctvController extends Controller
{
    public function index()
    {
        $cctvs = Cctv::latest()->get();
        return view('cctv.index', compact('cctvs'));
    }

    public function store(Request $request)
    {
        Cctv::create($this->validated($request));
        return back()->with('success', 'Kamera CCTV berhasil ditambahkan.');
    }

    public function update(Request $request, Cctv $cctv)
    {
        $cctv->update($this->validated($request));
        return back()->with('success', 'Kamera CCTV berhasil diperbarui.');
    }

    public function destroy(Cctv $cctv)
    {
        $cctv->delete();
        return back()->with('success', 'Kamera CCTV berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'nama' => 'required|string|max:100',
            'lokasi' => 'nullable|string|max:150',
            'stream_url' => 'required|url|max:1000',
        ]);
        $data['aktif'] = $request->boolean('aktif');
        return $data;
    }
}
