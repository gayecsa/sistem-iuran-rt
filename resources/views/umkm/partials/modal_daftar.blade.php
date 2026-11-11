<div class="modal fade" id="modalDaftarUmkm" tabindex="-1" aria-labelledby="modalDaftarUmkmLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-2xl rounded-3xl overflow-hidden">
            <div class="modal-header bg-gradient-to-r from-blue-700 to-blue-600 text-white p-5 border-0">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-white/20 flex items-center justify-center">
                        <i data-lucide="store" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <h5 class="modal-title font-bold text-lg text-white" id="modalDaftarUmkmLabel">Pendaftaran Usaha UMKM Warga</h5>
                        <p class="text-xs text-blue-100 mb-0">Daftarkan usaha Anda di katalog marketplace RW 013 Sukamiskin</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <form action="{{ route('umkm.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-6 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        {{-- Nama UMKM --}}
                        <div class="col-span-1 sm:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nama Tempat Usaha / Usaha Mandiri <span class="text-rose-500">*</span></label>
                            <input type="text" name="nama_umkm" required placeholder="Contoh: Dapur Sunda Bu Maya" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-blue-600 focus:ring-2 focus:ring-blue-100 outline-none">
                        </div>

                        {{-- Kategori --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Kategori Usaha <span class="text-rose-500">*</span></label>
                            <select name="kategori" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-blue-600 outline-none">
                                <option value="Makanan">Makanan</option>
                                <option value="Minuman">Minuman</option>
                                <option value="Kue & Roti">Kue & Roti</option>
                                <option value="Makanan Ringan">Makanan Ringan</option>
                                <option value="Warung & Sembako">Warung & Sembako</option>
                                <option value="Toko">Toko</option>
                                <option value="Fashion">Fashion</option>
                                <option value="Jasa">Jasa</option>
                                <option value="Kerajinan">Kerajinan</option>
                                <option value="Usaha Rumahan">Usaha Rumahan</option>
                            </select>
                        </div>

                        {{-- Jenis Usaha --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Sub-Jenis Usaha <span class="text-rose-500">*</span></label>
                            <input type="text" name="jenis_usaha" required placeholder="Contoh: Masakan Sunda / Cafe / Jahit" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-blue-600 outline-none">
                        </div>

                        {{-- Nama Pemilik --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nama Pemilik Usaha <span class="text-rose-500">*</span></label>
                            <input type="text" name="nama_pemilik" value="{{ auth()->user()->name }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-blue-600 outline-none">
                        </div>

                        {{-- No. WhatsApp --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Nomor WhatsApp Aktif <span class="text-rose-500">*</span></label>
                            <input type="text" name="no_hp" value="{{ auth()->user()->phone ?? '08' }}" required placeholder="08..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-blue-600 outline-none">
                        </div>

                        {{-- Wilayah RT --}}
                        <div>
                            <label class="block text-xs font-bold text-slate-700 mb-1">Wilayah RT <span class="text-rose-500">*</span></label>
                            <select name="rt_number" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-blue-600 outline-none">
                                @for($i = 1; $i <= 8; $i++)
                                    @php $rtPad = str_pad($i, 3, '0', STR_PAD_LEFT); @endphp
                                    <option value="{{ $rtPad }}" {{ auth()->user()->clean_rt_number == $rtPad ? 'selected' : '' }}>RT {{ $rtPad }}</option>
                                @endfor
                            </select>
                        </div>

                        {{-- Jam Buka & Tutup --}}
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Jam Buka</label>
                                <input type="text" name="jam_buka" value="08:00" placeholder="08:00" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-blue-600 outline-none">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 mb-1">Jam Tutup</label>
                                <input type="text" name="jam_tutup" value="21:00" placeholder="21:00" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:border-blue-600 outline-none">
                            </div>
                        </div>

                        {{-- Alamat Lengkap --}}
                        <div class="col-span-1 sm:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 mb-1">Alamat Tempat Usaha (Sukamiskin, Arcamanik) <span class="text-rose-500">*</span></label>
                            <textarea name="alamat" rows="2" required placeholder="Contoh: Jl. Pacuan Kuda No. 12, RT 001 / RW 013, Kelurahan Sukamiskin" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm focus:border-blue-600 outline-none">{{ auth()->user()->address }}</textarea>
                        </div>

                        {{-- Foto Usaha --}}
                        <div class="col-span-1 sm:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 mb-1">Foto Tempat Usaha / Produk Utama (Maks. 2MB)</label>
                            <input type="file" name="foto" accept="image/jpeg,image/png,image/webp" class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        </div>

                        {{-- Deskripsi Usaha --}}
                        <div class="col-span-1 sm:col-span-2">
                            <label class="block text-xs font-bold text-slate-700 mb-1">Deskripsi Singkat Usaha</label>
                            <textarea name="deskripsi" rows="3" placeholder="Ceritakan produk unggulan atau keistimewaan usaha Anda..." class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-sm focus:border-blue-600 outline-none"></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-slate-50 border-t border-slate-200 px-6 py-4 flex items-center justify-between">
                    <span class="text-xs text-slate-500">* Wajib diisi dengan benar. Akun Anda otomatis tercatat sebagai pemilik sah.</span>
                    <div class="flex items-center gap-2">
                        <button type="button" class="px-4 py-2 rounded-xl bg-slate-200 text-slate-700 text-xs font-semibold" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl bg-blue-600 text-white font-bold text-xs hover:bg-blue-700 shadow-sm">Kirim Pendaftaran Usaha</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
