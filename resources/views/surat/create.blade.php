<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengajuan Surat Pengantar</title>
    <!-- Memanggil Tailwind CSS agar tampilan langsung rapi -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /* Efek bayangan kertas */
        .kertas-a4 {
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body class="bg-gray-100 py-10 px-4 min-h-screen">

    <!-- Container Utama (Bentuk Kertas) -->
    <div class="max-w-3xl mx-auto bg-white p-8 md:p-12 border border-gray-200 kertas-a4 rounded-sm">
        
        <!-- ================= BAGIAN KOP SURAT ================= -->
        <div class="text-center border-b-4 border-double border-black pb-5 mb-8">
            <h1 class="text-2xl font-extrabold uppercase tracking-widest text-gray-900">
                Rukun Tetangga 001 / RW 002
            </h1>
            <h2 class="text-xl font-bold uppercase text-gray-800 mt-1">
                Kelurahan Teluk Pucung, Kecamatan Bekasi Utara, Kota Bekasi, Jawa Barat
            </h2>
            <p class="text-sm text-gray-600 mt-2">
                Sekretariat: Jl. Raya Mawar No. 123, Gedung Serbaguna, Kota Simulasi, 12345
            </p>
            <p class="text-sm text-gray-600">
                Email: pengurus@rt001.com | Telp/WA: {{ $admin_contact_phone ?? '081234567890' }}
            </p>
        </div>
        <!-- =================================================== -->

        <!-- Judul Form -->
        <h3 class="text-lg font-bold text-center underline uppercase mb-8 tracking-wide">
            Formulir Pengajuan Surat Pengantar
        </h3>

        <div class="mb-6 bg-blue-50 border-l-4 border-blue-500 p-4 rounded text-sm text-blue-700">
            Halo, <strong>{{ $user->name }}</strong>. Silakan lengkapi detail pengajuan surat di bawah ini. Pastikan data yang dimasukkan sudah benar.
        </div>

        <!-- Form Input -->
        <form action="{{ route('surat.store') }}" method="POST">
            @csrf
            
            <div class="mb-5">
                <label for="jenis_surat" class="block text-sm font-semibold text-gray-800 mb-2">Jenis Surat yang Diajukan</label>
                <!-- Input ini otomatis terisi dari URL ?jenis=..., saya buat format huruf besar di awal -->
                <input type="text" id="jenis_surat" name="jenis_surat" 
                       value="{{ ucfirst(request()->query('jenis')) }}" 
                       class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50 text-gray-700 font-medium"
                       required>
            </div>

            <div class="mb-8">
                <label for="keperluan" class="block text-sm font-semibold text-gray-800 mb-2">Keperluan (Tujuan Pembuatan Surat)</label>
                <textarea id="keperluan" name="keperluan" rows="5" 
                          class="w-full px-4 py-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                          placeholder="Contoh: Sebagai persyaratan administrasi pembuatan KTP baru..."
                          required></textarea>
            </div>

            <!-- Tombol Aksi -->
            <div class="flex justify-end items-center gap-3 pt-5 border-t border-gray-200 mt-8">
                <a href="{{ route('surat.index') }}" 
                   class="px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded hover:bg-gray-50 transition-colors">
                    Batal
                </a>
                <button type="submit" 
                        class="px-5 py-2.5 text-sm font-medium text-white bg-blue-600 rounded hover:bg-blue-700 focus:ring-4 focus:ring-blue-200 transition-colors">
                    Kirim Pengajuan Surat
                </button>
            </div>
        </form>

    </div>

</body>
</html>