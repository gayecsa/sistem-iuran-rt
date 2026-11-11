<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LetterType;
use App\Models\LetterRequest;
use Illuminate\Support\Facades\Auth;

class LetterMasterController extends Controller
{
    /**
     * Master Katalog Jenis & Template Surat RW 013
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        abort_unless($user->isRw(), 403, 'Akses khusus Ketua RW / Administrator.');

        $query = LetterType::query()->orderBy('urutan');

        if ($request->filled('kategori') && $request->kategori !== 'semua') {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function($q) use ($s) {
                $q->where('nama', 'like', "%{$s}%")
                  ->orWhere('singkatan', 'like', "%{$s}%")
                  ->orWhere('deskripsi', 'like', "%{$s}%")
                  ->orWhere('kode', 'like', "%{$s}%");
            });
        }

        $types = $query->paginate(20)->withQueryString();

        $stats = [
            'total' => LetterType::count(),
            'active' => LetterType::where('is_active', true)->count(),
            'inactive' => LetterType::where('is_active', false)->count(),
            'total_requests' => LetterRequest::count(),
        ];

        return view('surat.master_index', compact('user', 'types', 'stats'));
    }

    /**
     * Form Edit Template, Persyaratan & Alur Surat
     */
    public function edit($id)
    {
        $user = Auth::user();
        abort_unless($user->isRw(), 403, 'Akses khusus Ketua RW / Administrator.');

        $type = LetterType::findOrFail($id);

        return view('surat.master_edit', compact('user', 'type'));
    }

    /**
     * Simpan Perubahan Template & Konfigurasi Surat
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        abort_unless($user->isRw(), 403, 'Akses khusus Ketua RW / Administrator.');

        $type = LetterType::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:150',
            'singkatan' => 'required|string|max:30',
            'deskripsi' => 'required|string|max:1000',
            'tujuan' => 'nullable|string|max:255',
            'alur_persetujuan' => 'required|string|in:rt_then_rw,rt_only,pengantar_kelurahan,internal_rw',
            'biaya' => 'required|string|max:50',
            'estimasi_hari' => 'required|string|max:50',
            'kode_klasifikasi' => 'required|string|max:20',
            'template_body' => 'required|string',
            'persyaratan' => 'nullable|array',
            'is_active' => 'required|boolean',
        ]);

        $persyaratanClean = [];
        if ($request->filled('persyaratan_text')) {
            $lines = explode("\n", str_replace("\r", "", $request->persyaratan_text));
            foreach ($lines as $line) {
                $trimmed = trim($line);
                if (!empty($trimmed)) {
                    $persyaratanClean[] = ltrim($trimmed, "-*0123456789. ");
                }
            }
        } elseif ($request->has('persyaratan')) {
            $persyaratanClean = array_filter($request->persyaratan);
        }

        $type->update([
            'nama' => $request->nama,
            'singkatan' => $request->singkatan,
            'deskripsi' => $request->deskripsi,
            'tujuan' => $request->tujuan,
            'alur_persetujuan' => $request->alur_persetujuan,
            'biaya' => $request->biaya,
            'estimasi_hari' => $request->estimasi_hari,
            'kode_klasifikasi' => $request->kode_klasifikasi,
            'template_body' => $request->template_body,
            'persyaratan' => !empty($persyaratanClean) ? array_values($persyaratanClean) : $type->persyaratan,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('rw.surat_master.index')->with('success', "Konfigurasi layanan \"{$type->nama}\" berhasil diperbarui.");
    }

    /**
     * Toggle Aktif / Nonaktif Surat
     */
    public function toggleActive($id)
    {
        $user = Auth::user();
        abort_unless($user->isRw(), 403, 'Akses khusus Ketua RW / Administrator.');

        $type = LetterType::findOrFail($id);
        $type->update(['is_active' => !$type->is_active]);

        $statusText = $type->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Jenis surat \"{$type->nama}\" berhasil {$statusText}.");
    }
}
