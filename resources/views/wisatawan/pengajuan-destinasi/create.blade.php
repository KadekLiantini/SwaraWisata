<x-layouts.public title="Ajukan Tempat Wisata Baru" cta="none">
    <div class="min-h-screen bg-[#f8faff]">
        <div class="mx-auto max-w-7xl px-6 py-8">

            {{-- Breadcrumb --}}
            <div class="mb-5 flex items-center gap-1.5 text-xs text-gray-600">
                <x-icon name="home" size="15" />
                    <a href="{{ route('wisatawan.beranda') }}" class="hover:text-primary">
                        Beranda Wisatawan
                    </a>
                <span>›</span>
                <span class="font-semibold text-[#00634d]">Ajukan Tempat Wisata Baru</span>
            </div>

            {{-- Judul halaman --}}
            <div class="mb-8 flex flex-wrap items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-[#172238] sm:text-3xl">
                        Ajukan Tempat Wisata Baru
                    </h1>
                    <p class="mt-1 max-w-2xl text-sm leading-relaxed text-gray-600">
                        Tidak menemukan tempat wisata yang ingin Anda laporkan? Ajukan di sini.
                        Admin akan memeriksa pengajuan Anda sebelum tempat wisata ditambahkan ke sistem resmi.
                    </p>
                </div>

                <div class="flex items-center gap-3 rounded-xl bg-[#eef2ff] px-4 py-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-[#cce5e0] text-[#00634d]">
                        <x-icon name="add_location_alt" class="h-5 w-5" />
                    </div>
                    <div>
                        <p class="text-[10px] font-semibold uppercase tracking-wide text-gray-500">
                            Kuota Pengajuan
                        </p>
                        <p class="text-sm font-bold text-[#172238]">Maks. 10 / Wilayah</p>
                    </div>
                </div>
            </div>

            {{-- Informasi dan prosedur --}}
            <div class="mb-5 grid gap-4 lg:grid-cols-[0.85fr_1.2fr]">

                <div class="relative overflow-hidden rounded-xl bg-[#eef2ff] p-5">
                    <div class="absolute -bottom-12 right-0 h-32 w-32 rounded-full bg-[#f4eae4]"></div>

                    <div class="relative">
                        <div class="mb-3 flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-[#f7e8d1] text-[#93601c]">
                                <x-icon name="manage_search" class="h-5 w-5" />
                            </div>
                            <div>
                                <p class="text-[10px] font-bold uppercase text-[#93601c]">Cek Kembali</p>
                                <h2 class="text-sm font-semibold text-[#172238]">
                                    Cegah Lokasi Wisata Sudah Terdaftar
                                </h2>
                            </div>
                        </div>

                        <p class="mb-8 text-xs leading-relaxed text-gray-600">
                            Sebelum mengajukan, pastikan destinasi belum ada di direktori sistem.
                            Duplikasi pengajuan akan otomatis ditolak saat proses audit kurasi dinas.
                        </p>

                        <a href="{{ route('wisatawan.beranda') }}"
                           class="flex items-center justify-center gap-2 rounded-lg bg-white px-4 py-2.5 text-xs font-semibold text-[#172238] transition hover:bg-gray-50">
                            <x-icon name="search" class="h-4 w-4" />
                            Cari di Beranda Wisata
                        </a>
                    </div>
                </div>

                <div class="rounded-xl bg-white p-5 shadow-sm">
                    <div class="mb-4 flex items-center gap-3">
                        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-[#e8f2ef] text-[#00634d]">
                            <x-icon name="verified" class="h-5 w-5" />
                        </div>
                        <h2 class="text-sm font-semibold text-[#172238]">
                            Prosedur Kurasi Lapangan 3 Langkah
                        </h2>
                    </div>

                    <div class="grid gap-2 sm:grid-cols-3">
                        <div class="rounded-lg bg-[#eef2ff] p-3">
                            <p class="mb-1 text-xs font-bold text-[#172238]">
                                <span class="mr-1 inline-flex h-4 w-4 items-center justify-center rounded-full bg-[#00634d] text-[10px] text-white">1</span>
                                Isi Data Valid
                            </p>
                            <p class="text-xs leading-relaxed text-gray-600">
                                Formulir lokasi akurat dengan tautan resmi Google Maps.
                            </p>
                        </div>

                        <div class="rounded-lg bg-[#eef2ff] p-3">
                            <p class="mb-1 text-xs font-bold text-[#172238]">
                                <span class="mr-1 inline-flex h-4 w-4 items-center justify-center rounded-full bg-amber-500 text-[10px] text-white">2</span>
                                Pengecekan Manual
                            </p>
                            <p class="text-xs leading-relaxed text-gray-600">
                                Admin memverifikasi berkas (Disetujui / Ditolak).
                            </p>
                        </div>

                        <div class="rounded-lg bg-[#eef2ff] p-3">
                            <p class="mb-1 text-xs font-bold text-[#172238]">
                                <span class="mr-1 inline-flex h-4 w-4 items-center justify-center rounded-full bg-[#008c78] text-[10px] text-white">3</span>
                                Pantau Berkala
                            </p>
                            <p class="text-xs leading-relaxed text-gray-600">
                                Cek hasil berkala di tabel riwayat pengajuan akun Anda.
                            </p>
                        </div>
                    </div>

                    <div class="mt-4 flex gap-2 rounded-lg bg-[#e8efff] p-3 text-xs leading-relaxed text-gray-700">
                        <x-icon name="info" class="mt-0.5 h-4 w-4 shrink-0 text-amber-600" />
                        <p>
                            <strong>Catatan:</strong> Persetujuan dilakukan secara manual oleh admin,
                            sehingga <u class=" text-amber-700">tidak tersedia notifikasi email otomatis</u>. Harap pantau perkembangan
                            status verifikasi Anda langsung di halaman ini atau tabel Pengajuan Saya.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Form pengajuan --}}
            <div class="mb-8 rounded-xl bg-white p-4 shadow-sm sm:p-5">
                <div class="mb-6 flex flex-wrap items-center justify-between gap-3 rounded-xl bg-[#eef2ff] px-5 py-4">
                    <div class="flex items-center gap-3">
                        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-[#00634d] text-white">
                            <x-icon name="edit_location_alt" class="h-5 w-5" />
                        </div>
                        <h2 class="text-base font-semibold text-[#172238]">
                            Formulir Pengajuan Tempat Wisata
                        </h2>
                    </div>
                    <span class="rounded-full bg-white px-3 py-1 text-[10px] font-semibold text-gray-600">
                        <span class="text-red-500">*</span> Semua kolom wajib diisi
                    </span>
                </div>

                @if ($errors->any())
                    <div class="mb-5 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                        <p class="mb-1 font-semibold">Periksa kembali isian formulir:</p>
                        <ul class="list-inside list-disc">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('pengajuan.store') }}" method="POST">
                    @csrf

                    {{-- Nama lokasi --}}
                    <div class="mb-5">
                        <div class="mb-2 flex flex-wrap justify-between gap-2">
                            <label for="nama_lokasi" class="text-xs font-semibold text-[#172238]">
                                Nama Lokasi Wisata <span class="text-red-500">*</span>
                            </label>
                            <span class="text-[11px] text-gray-500">
                                Gunakan nama resmi yang dikenal masyarakat
                            </span>
                        </div>

                        <div class="flex items-center gap-3 rounded-lg border border-gray-200 px-3 py-3 focus-within:border-[#00634d]">
                            <x-icon name="pin_drop" size="16" class="!h-4 !w-4 !leading-none text-gray-500" />
                            <input
                                type="text"
                                id="nama_lokasi"
                                name="nama_lokasi"
                                value="{{ old('nama_lokasi') }}"
                                maxlength="150"
                                required
                                placeholder="Contoh: Pantai Melasti, Air Terjun Banyumala"
                                class="w-full border-0 bg-transparent p-0 text-sm text-gray-800 outline-none placeholder:text-gray-300 focus:ring-0"
                            >
                        </div>
                    </div>

                    {{-- Kabupaten, kecamatan, kategori --}}
                    <div class="mb-5 grid gap-4 md:grid-cols-3">
                        <div>
                            <label for="id_kabupaten" class="mb-2 block text-xs font-semibold text-[#172238]">
                                Wilayah Kabupaten / Kota <span class="text-red-500">*</span>
                            </label>
                            <div class="flex items-center gap-2 rounded-lg border border-gray-200 px-3">
                                <x-icon name="location_city" size="16" class="!h-4 !w-4 !leading-none shrink-0 text-gray-500" />
                                <select
                                    id="id_kabupaten"
                                    data-kecamatan-url="{{ route('pengajuan.kecamatan', ['idKabupaten' => '__ID__']) }}"
                                    class="w-full border-0 bg-transparent py-3 text-xs text-gray-700 outline-none focus:ring-0"
                                    required
                                >
                                    <option value="">Pilih Kabupaten/Kota...</option>
                                    @foreach ($kabupaten as $item)
                                        <option value="{{ $item->id_kabupaten }}"
                                            @selected(old('id_kabupaten') == $item->id_kabupaten)>
                                            {{ $item->label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <label for="id_kecamatan" class="mb-2 block text-xs font-semibold text-[#172238]">
                                Kecamatan <span class="text-red-500">*</span>
                            </label>
                            <div class="flex items-center gap-2 rounded-lg border border-gray-200 px-3">
                                <x-icon name="holiday_village" size="16" class="!h-4 !w-4 !leading-none shrink-0 text-gray-500" />
                                <select
                                    id="id_kecamatan"
                                    name="id_kecamatan"
                                    data-old-value="{{ old('id_kecamatan') }}"
                                    required
                                    disabled
                                    class="w-full border-0 bg-transparent py-3 text-xs text-gray-700 outline-none focus:ring-0 disabled:text-gray-400"
                                >
                                    <option value="">Pilih kabupaten terlebih dahulu</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label for="id_kategori_wisata" class="mb-2 block text-xs font-semibold text-[#172238]">
                                Kategori Wisata <span class="text-red-500">*</span>
                            </label>
                            <div class="flex items-center gap-2 rounded-lg border border-gray-200 px-3">
                                <x-icon name="category" size="16" class="!h-4 !w-4 !leading-none shrink-0 text-gray-500" />

                                <select
                                    id="id_kategori_wisata"
                                    name="id_kategori_wisata"
                                    required
                                    class="w-full border-0 bg-transparent py-3 text-xs text-gray-700 outline-none focus:ring-0"
                                >
                                    <option value="">Pilih Kategori Objek...</option>
                                    @foreach ($kategoriWisata as $kategori)
                                        <option value="{{ $kategori->id_kategori_wisata }}"
                                            @selected(old('id_kategori_wisata') == $kategori->id_kategori_wisata)>
                                            {{ $kategori->nama }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- Link Google Maps --}}
                    <div class="mb-1">
                        <label for="link_maps" class="mb-2 block text-xs font-semibold text-[#172238]">
                            Link Google Maps <span class="text-red-500">*</span>
                        </label>
                        <div class="flex items-center gap-3 rounded-lg border border-gray-200 px-3 py-3 focus-within:border-[#00634d]">
                            <x-icon name="map" size="16" class="!h-4 !w-4 !leading-none text-[#00634d]" />
                            <input
                                type="url"
                                id="link_maps"
                                name="link_maps"
                                value="{{ old('link_maps') }}"
                                maxlength="500"
                                required
                                placeholder="https://maps.app.goo.gl/..."
                                class="w-full border-0 bg-transparent p-0 text-sm text-gray-800 outline-none placeholder:text-gray-300 focus:ring-0"
                            >
                        </div>
                        <div class="mt-1 flex items-start gap-1 rounded-lg bg-[#eef2ff] px-3 py-2 text-[11px] text-gray-600">
                            <x-icon name="help" size="14" class="!h-3.5 !w-3.5 !leading-none shrink-0 text-[#00634d]" />
                            <p>
                                Buka Google Maps, cari titik lokasi wisata, tekan Bagikan (Share),
                                lalu salin tautan resmi yang dihasilkan.
                            </p>
                        </div>
                    </div>

                    {{-- Tombol --}}
                    <div class="mt-8 flex flex-wrap justify-end gap-3">
                        <a href="{{ route('wisatawan.beranda') }}"
                           class="inline-flex items-center gap-2 rounded-lg bg-[#e5edff] px-5 py-2.5 text-xs font-semibold text-[#172238] transition hover:bg-[#d8e4ff]">
                            <x-icon name="close" size="16" class="!h-4 !w-4 !leading-none" />
                            Batal & Kembali ke Beranda
                        </a>

                        <button type="submit"
                                class="inline-flex items-center gap-2 rounded-lg bg-[#00634d] px-5 py-2.5 text-xs font-semibold text-white shadow-sm transition hover:bg-[#004e3d]">
                            <x-icon name="send" size="16" class="!h-4 !w-4 !leading-none" />
                            Kirim Pengajuan Tempat Wisata
                        </button>
                    </div>
                </form>
            </div>

            {{-- Riwayat pengajuan --}}
            <section>
                <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-[#00634d] text-white">
                            <x-icon name="assignment" class="h-5 w-5" />
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-[#172238]">
                                Status Riwayat Pengajuan Saya
                            </h2>
                            <p class="text-xs text-gray-600">
                                Daftar usulan tempat wisata yang telah dikirim ke kurator dinas
                            </p>
                        </div>
                    </div>

                    <span class="rounded-full bg-[#00866c] px-4 py-1 text-[10px] font-semibold text-white">
                        {{ $jumlahPengajuan }} Pengajuan Diajukan
                    </span>
                </div>

                <div class="overflow-x-auto rounded-xl bg-white shadow-sm">
                    <table class="w-full min-w-[900px] text-left text-xs">
                        <thead class="bg-[#eef2ff] text-[10px] uppercase tracking-wide text-gray-600">
                            <tr>
                                <th class="px-4 py-3">Nama Tempat Wisata</th>
                                <th class="px-4 py-3">Kabupaten & Kecamatan</th>
                                <th class="px-4 py-3">Kategori</th>
                                <th class="px-4 py-3">Waktu Pengajuan</th>
                                <th class="px-4 py-3">Status Verifikasi</th>
                                <th class="px-4 py-3">Tindakan / Catatan Admin</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-100">
                            @forelse ($riwayatPengajuan as $pengajuan)
                                <tr class="align-top">
                                    <td class="px-4 py-4">
                                        <p class="font-semibold text-[#172238]">
                                            {{ $pengajuan->nama_lokasi }}
                                        </p>
                                        <a href="{{ $pengajuan->link_maps }}"
                                           target="_blank"
                                           rel="noopener noreferrer"
                                           class="mt-1 inline-flex items-center gap-1 text-[10px] text-gray-500 hover:text-[#00634d]">
                                            <x-icon name="share_location" size="12" class="!h-3 !w-3 !leading-none" />
                                            Lihat Titik Koordinat
                                        </a>
                                    </td>

                                    <td class="px-4 py-4 text-gray-700">
                                        <p class="font-semibold">
                                            {{ $pengajuan->kecamatan->kabupaten->label }}
                                        </p>
                                        <p class="text-gray-500">
                                            Kec. {{ $pengajuan->kecamatan->nama }}
                                        </p>
                                    </td>

                                    <td class="px-4 py-4">
                                        <span class="rounded bg-[#e8efff] px-2 py-1 text-[10px] font-semibold text-gray-700">
                                            {{ $pengajuan->kategoriWisata->nama }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-4 text-gray-700">
                                        {{ $pengajuan->created_at->format('d M Y') }}
                                        <p class="mt-1 text-[10px] text-gray-400">
                                            {{ $pengajuan->created_at->format('H:i') }} WITA
                                        </p>
                                    </td>

                                    <td class="px-4 py-4">
                                        <x-status-badge :status="$pengajuan->status" size="sm" />
                                    </td>

                                    <td class="px-4 py-4 text-gray-600">
                                        @if ($pengajuan->status === \App\Models\PengajuanDestinasi::DISETUJUI && $pengajuan->destinasi)
                                            <p>Tempat wisata telah ditambahkan ke sistem.</p>
                                            <a href="{{ route('laporan.create', $pengajuan->destinasi->id_destinasi) }}"
                                               class="mt-1 inline-flex items-center gap-1 font-semibold text-[#00634d] hover:underline">
                                                Lihat Detail Destinasi & Lapor Kerusakan
                                                <x-icon name="arrow-right" class="h-3 w-3" />
                                            </a>
                                        @elseif ($pengajuan->status === \App\Models\PengajuanDestinasi::DITOLAK)
                                            <p class="rounded bg-red-50 p-2 text-[11px] text-red-700">
                                                Pengajuan ditolak. Silakan hubungi admin untuk informasi lebih lanjut.
                                            </p>
                                        @else
                                            <p>Sedang dalam antrean peninjauan oleh tim admin dinas.</p>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-10 text-center text-sm text-gray-500">
                                        Belum ada pengajuan destinasi wisata.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
    </div>
</x-layouts.public>
