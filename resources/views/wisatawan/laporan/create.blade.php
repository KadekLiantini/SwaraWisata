<x-layouts.public title="Detail Destinasi" cta="none">

    <div class="mx-auto w-full max-w-[1280px] px-4 pb-14 sm:px-8">

        {{-- Breadcrumb --}}
        <nav class="flex flex-wrap items-center gap-1.5 py-5 font-heading text-xs font-medium text-muted">
            <x-icon name="home" size="15" />
            <a href="{{ route('wisatawan.beranda') }}" class="hover:text-primary">
                Beranda Wisatawan
            </a>

            <x-icon name="chevron_right" size="14" />
            <a href="{{ route('wisatawan.beranda') }}#pencarian-destinasi" class="hover:text-primary">
                Destinasi
            </a>

            <x-icon name="chevron_right" size="14" />
            <span class="font-semibold text-primary">
                {{ $destinasi->nama }}
            </span>
        </nav>

        {{-- Hero Destinasi --}}
        <section class="relative flex min-h-[420px] items-end overflow-hidden rounded-2xl bg-[#334b4b] text-white shadow-xl">

            {{-- Foto dan overlay --}}
            @if ($destinasi->foto)
                <img src="{{ asset('storage/' . $destinasi->foto) }}" alt="{{ $destinasi->nama }}" class="absolute inset-0 size-full object-cover">
            @endif

            <div class="absolute inset-0 bg-gradient-to-b from-[#111c2d]/5 via-[#111c2d]/30 to-[#111c2d]/90"></div>

            {{-- Kategori --}}
            <span class="absolute left-4 top-4 inline-flex items-center gap-1.5 rounded-full bg-primary px-3 py-1 font-heading text-[11px] font-bold text-white">
                <x-icon name="landscape" size="14" />
                {{ $destinasi->kategoriWisata->nama ?? 'Destinasi Wisata' }}
            </span>

            {{-- Google Maps --}}
            @if ($destinasi->link_maps)
                <a
                    href="{{ $destinasi->link_maps }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="absolute right-4 top-4 inline-flex items-center gap-1.5 rounded-full bg-white/95 px-4 py-2 font-heading text-xs font-semibold text-ink shadow-sm transition hover:bg-white"
                >
                    <x-icon name="explore" size="16" />
                    Lihat di Google Maps
                    <x-icon name="open_in_new" size="14" />
                </a>
            @endif

            {{-- Informasi Destinasi --}}
            <div class="relative flex w-full flex-col items-start justify-between gap-6 p-6 sm:flex-row sm:items-end sm:p-10">
                <div class="max-w-3xl">
                    <p class="mb-1 flex items-center gap-1.5 text-xs font-medium text-white/90">
                        <x-icon name="location_on" size="16" />
                        Kec. {{ $destinasi->kecamatan->nama ?? '-' }},
                        Kab. {{ $destinasi->kecamatan->kabupaten->nama ?? '-' }}
                    </p>

                    <h1 class="mb-3 font-heading text-3xl font-extrabold leading-tight tracking-tight sm:text-4xl lg:text-[42px]">
                        {{ $destinasi->nama }}
                    </h1>

                    <p class="max-w-3xl text-sm leading-6 text-white/85">
                        {{ $destinasi->deskripsi ?: 'Belum ada deskripsi destinasi.' }}
                    </p>
                </div>

                <a
                    href="#form-laporan"
                    class="inline-flex shrink-0 items-center gap-2 rounded-xl bg-[#fba51a] px-5 py-3 font-heading text-sm font-semibold text-[#4a2e00] shadow-md transition hover:brightness-105"
                >
                    <x-icon name="warning" size="18" />
                    Laporkan Kerusakan di Sini
                </a>
            </div>
        </section>


        {{-- Statistik Laporan --}}
        <section class="mt-6 grid grid-cols-2 gap-4 lg:grid-cols-4">

            {{-- Total Aduan --}}
            <div class="rounded-xl border border-black/5 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-muted">Total Aduan</span>
                    <span class="flex size-9 items-center justify-center rounded-lg bg-blue-50 text-blue-600">
                        <x-icon name="description" size="19" />
                    </span>
                </div>
                <p class="mt-3 font-heading text-3xl font-extrabold text-ink">
                    {{ $totalAduan }}
                </p>
                <p class="mt-1 text-xs text-muted">Laporan wisatawan</p>
            </div>

            {{-- Menunggu Tindakan --}}
            <div class="rounded-xl border border-black/5 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-muted">Menunggu Tindakan</span>
                    <span class="flex size-9 items-center justify-center rounded-lg bg-amber-50 text-amber-600">
                        <x-icon name="schedule" size="19" />
                    </span>
                </div>
                <p class="mt-3 font-heading text-3xl font-extrabold text-ink">
                    {{ $menungguTindakan }}
                </p>
                <p class="mt-1 text-xs text-muted">Telah dilihat pengelola</p>
            </div>

            {{-- Sedang Diproses --}}
            <div class="rounded-xl border border-black/5 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-[#855300]">Sedang Diproses</span>
                    <span class="flex size-9 items-center justify-center rounded-lg bg-orange-50 text-orange-600">
                        <x-icon name="engineering" size="19" />
                    </span>
                </div>
                <p class="mt-3 font-heading text-3xl font-extrabold text-[#855300]">
                    {{ $sedangDiproses }}
                </p>
                <p class="mt-1 text-xs text-muted">Tim Teknis dalam perbaikan</p>
            </div>

            {{-- Tuntas Diperbaiki --}}
            <div class="rounded-xl border border-black/5 bg-white p-5 shadow-sm">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-[#005F49]">Tuntas Diperbaiki</span>
                    <span class="flex size-9 items-center justify-center rounded-lg bg-green-50 text-green-600">
                        <x-icon name="check_circle" size="19" />
                    </span>
                </div>
                <p class="mt-3 font-heading text-3xl font-extrabold text-[#005F49]">
                    {{ $tuntasDiperbaiki }}
                </p>
                <p class="mt-1 text-xs text-muted">Kondisi fasilitas pulih</p>
            </div>
        </section>
    
        {{-- Konten Utama --}}
        <div class="grid grid-cols-1 items-start gap-8 mt-8 pb-14 lg:grid-cols-12">

            {{-- Form Laporan --}}
            <section id="form-laporan" class="rounded-2xl bg-white p-6 shadow-sm lg:sticky lg:top-24 lg:col-span-5 sm:p-8">

                {{-- Header Form --}}
                <div class="mb-6 flex items-center gap-3">
                    <span class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-primary text-white">
                        <x-icon name="camera_alt" size="24" />
                    </span>

                    <div>
                        <h2 class="font-heading text-xl font-bold text-ink">
                            Buat Laporan Baru
                        </h2>
                        <p class="text-sm text-muted">
                            Kirim bukti temuan kerusakan fasilitas langsung ke dinas
                        </p>
                    </div>
                </div>

                <form
                    action="{{ route('laporan.store', ['destinasi' => $destinasi->id_destinasi]) }}"
                    method="POST"
                    enctype="multipart/form-data"
                    class="space-y-5"
                >
                    @csrf

                    {{-- Destinasi Terpilih --}}
                    <div>
                        <div class="mb-2 flex items-center justify-between">
                            <label class="font-heading text-sm font-medium text-ink">
                                Destinasi Terpilih
                            </label>
                            <span class="flex items-center gap-1 text-xs font-medium text-muted">
                                <x-icon name="lock" size="14" />
                                Terkunci Otomatis
                            </span>
                        </div>

                        <div class="flex items-center gap-2 rounded-xl bg-soft-strong px-4 py-3 font-heading text-sm font-semibold text-ink">
                            <x-icon name="flag" size="18" class="text-primary" />
                            {{ $destinasi->nama }}
                        </div>
                    </div>

                    {{-- Judul --}}
                    <x-form.input
                        name="judul"
                        label="Judul Laporan Kerusakan"
                        placeholder="Masukkan judul kerusakan"
                        maxlength="80"
                        counter
                        required
                        variant="soft"
                    />

                    {{-- Kategori --}}
                    <x-form.select
                        name="id_kategori_kerusakan"
                        label="Kategori Kerusakan"
                        :options="$kategoriKerusakan->pluck('nama', 'id_kategori_kerusakan')"
                        placeholder="Pilih kategori"
                        required
                    />

                    {{-- Lokasi Spesifik --}}
                    <x-form.input
                        name="lokasi_spesifik"
                        label="Lokasi Spesifik Titik Kerusakan"
                        placeholder="Sebutkan titik lokasi kerusakan"
                        icon="location_on"
                        required
                        variant="soft"
                    />

                    {{-- Deskripsi --}}
                    <x-form.textarea
                        name="deskripsi"
                        label="Deskripsi Kerusakan Terperinci"
                        placeholder="Jelaskan kondisi kerusakan secara terperinci..."
                        rows="4"
                        maxlength="1000"
                        required
                        class="bg-soft"
                    />

                    {{-- Bukti Foto --}}
                    <div>
                        <label for="foto" class="mb-2 block font-heading text-sm font-medium text-ink">
                            Bukti Foto Kerusakan
                        </label>

                        <label id="area-upload-foto" 
                            for="foto"
                            class="flex cursor-pointer flex-col items-center gap-2 rounded-xl border border-dashed border-[#b6c6e3] bg-soft p-6 text-center transition hover:bg-soft-strong"
                        >
                            <x-icon name="cloud_upload" size="24" />
                            <span class="text-sm text-ink">
                                Tarik dan lepaskan foto ke sini, atau
                                <span class="font-semibold text-primary underline">Pilih File</span>
                            </span>
                            <span class="text-xs text-muted">Maksimal 3 gambar (JPG/PNG)</span>
                        </label>

                        <input
                            type="file"
                            name="foto[]"
                            id="foto"
                            accept="image/jpeg,image/png"
                            multiple
                            class="hidden"
                        >

                        <div id="preview-foto" class="mt-3 flex flex-col gap-3"></div>
                    </div>

                    {{-- Tombol --}}
                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button
                            type="reset"
                            class="rounded-xl px-5 py-3 font-heading text-sm font-medium text-ink transition hover:bg-soft"
                        >
                            Batal
                        </button>

                        <x-button type="submit" size="lg" icon="send">
                            Kirim Laporan
                        </x-button>
                    </div>
                </form>
            </section>

            {{-- Kolom Kanan: Laporan Pengunjung --}}
            <aside class="lg:col-span-7">
                <div id="daftar-laporan-container"> 
                {{-- Header Laporan Publik --}}
                <div class="rounded-2xl bg-white p-6 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h2 class="font-heading text-xl font-bold text-ink">
                                Laporan Publik di Lokasi Ini
                            </h2>
                            <p class="mt-2 text-sm leading-6 text-muted">
                                Transparansi pemantauan bersama untuk perlindungan ekowisata.
                            </p>
                        </div>

                        {{-- Pengurutan --}}
                        <form
                            method="GET"
                            action="{{ route('laporan.create', $destinasi->id_destinasi) }}"
                            class="flex shrink-0 items-center gap-2"
                        >
                            @if ($status)
                                <input type="hidden" name="status" value="{{ $status }}">
                            @endif

                            <label for="urutan" class="text-xs font-medium text-muted">
                                Urutkan:
                            </label>

                            <select
                                id="urutan"
                                name="urutan"
                                class="rounded-lg bg-soft px-3 py-2 text-sm text-ink focus:outline-none focus:ring-2 focus:ring-primary/20"
                            >
                                <option value="terbaru" @selected($urutan === 'terbaru')>
                                    Terbaru Diajukan
                                </option>
                                <option value="terlama" @selected($urutan === 'terlama')>
                                    Terlama Diajukan
                                </option>
                            </select>
                        </form>
                    </div>

                    {{-- Filter Status --}}
                    <div class="mt-4 flex flex-wrap gap-2">
                        @php
                            $filters = [
                                ['label' => 'Semua', 'status' => null, 'jumlah' => $totalAduan],
                                ['label' => 'Belum Dilihat', 'status' => 'belum_dilihat', 'jumlah' => $belumDilihat],
                                ['label' => 'Dilihat', 'status' => 'dilihat', 'jumlah' => $menungguTindakan],
                                ['label' => 'Ditindaklanjuti', 'status' => 'ditindaklanjuti', 'jumlah' => $sedangDiproses],
                                ['label' => 'Selesai', 'status' => 'selesai', 'jumlah' => $tuntasDiperbaiki],
                            ];
                        @endphp

                        @foreach ($filters as $filter)
                            <a
                                href="{{ route('laporan.create', array_filter([
                                    'destinasi' => $destinasi->id_destinasi,
                                    'status' => $filter['status'],
                                    'urutan' => $urutan !== 'terbaru' ? $urutan : null,
                                ], fn ($value) => $value !== null)) }}"
                                @class([
                                    'inline-flex items-center gap-2 rounded-full px-4 py-1.5 text-xs font-semibold transition',
                                    'bg-primary text-white' => $status === $filter['status'],
                                    'bg-soft text-ink hover:bg-soft-strong' => $status !== $filter['status'],
                                ])
                            >
                                {{ $filter['label'] }} ({{ $filter['jumlah'] }})
                            </a>
                        @endforeach
                    </div>
                </div>

                {{-- Daftar Laporan --}}
                <div class="mt-5 space-y-4">
                    @forelse ($laporan as $item)
                        <article class="overflow-hidden rounded-2xl border border-black/5 bg-white p-6 shadow-sm {{ $item->laporan_saya ? 'border-t-4 border-t-[#855300]' : '' }}">

                            {{-- Header: Kategori dan Status --}}
                            <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                                <div class="flex flex-wrap gap-1.5">
                                    @if ($item->laporan_saya)
                                        <span class="inline-flex items-center gap-1 rounded-md bg-[#ffddb8] px-2.5 py-1 text-xs font-semibold text-[#653e00]">
                                            <x-icon name="star" size="12" />
                                            Laporan Saya
                                        </span>
                                    @endif

                                    <span class="rounded-md bg-[#dee8ff] px-2.5 py-1 text-xs font-semibold text-ink">
                                        {{ $item->kategoriKerusakan->nama ?? 'Lainnya' }}
                                    </span>
                                </div>

                                <span class="inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold {{ $item->status_class }}">
                                    <span class="size-1.5 rounded-full bg-current"></span>
                                    {{ $item->status_label }}
                                </span>
                            </div>

                            {{-- Isi: Foto dan Informasi --}}
                            <div class="grid grid-cols-1 gap-5 sm:grid-cols-[232px_minmax(0,1fr)]">

                                {{-- Foto --}}
                                <div class="relative h-[190px] overflow-hidden rounded-xl bg-soft">
                                    @if ($item->foto->isNotEmpty())
                                        <img
                                            src="{{ $item->foto->first()->url }}"
                                            alt="Foto {{ $item->judul }}"
                                            class="size-full object-cover"
                                        >

                                        <span class="absolute bottom-2 right-2 rounded-md bg-black/70 px-2 py-1 text-xs font-medium text-white">
                                            {{ $item->foto->count() }} Foto
                                        </span>
                                        @if ($item->status === 'selesai')
                                            <span class="absolute bottom-2 left-2 inline-flex items-center gap-1 rounded-md bg-[#d1fae5] px-2 py-1 text-xs font-semibold text-[#065f46]">
                                                <x-icon name="verified" size="14" />
                                                Terverifikasi
                                            </span>
                                        @endif
                                    @else
                                        <div class="flex size-full items-center justify-center text-muted">
                                            <x-icon name="image" size="3xl" />
                                        </div>
                                    @endif
                                </div>

                                {{-- Informasi --}}
                                <div class="min-w-0">
                                    <h3 class="mb-1 font-heading text-xl font-bold leading-7 text-ink">
                                        {{ $item->judul }}
                                    </h3>

                                    <p class="text-[15px] leading-[22px] text-text">
                                        {{ $item->deskripsi }}
                                    </p>

                                    <div class="mt-2 flex flex-wrap items-center gap-2 text-[13px] text-muted">
                                        <span class="inline-flex items-center gap-1.5 font-semibold text-ink">
                                            <x-icon name="person" size="14" />
                                            {{ $item->pelapor->username ?? 'Pengguna' }}
                                        </span>

                                        <span class="size-1 rounded-full bg-muted"></span>

                                        <span>
                                            {{ $item->created_at->format('d M Y, H:i') }} WIB
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Footer: Progres Penanganan --}}
                            <div class="mt-4 flex items-center justify-between gap-4 text-[13px] font-semibold leading-[18px]
                                {{ $item->status === 'ditindaklanjuti'
                                    ? 'rounded-lg bg-[#f0f3ff] px-3.5 py-2.5'
                                    : '' }}">

                                {{-- Pesan Progres --}}
                                <span class="flex min-w-0 items-center gap-1.5
                                    {{ $item->status === 'ditindaklanjuti'
                                        ? 'text-[#653e00]'
                                        : ($item->status === 'dilihat'
                                            ? 'text-text'
                                            : 'text-[#005f49]') }}">

                                    @if ($item->status === 'ditindaklanjuti')
                                        <x-icon name="build" size="16" />
                                    @elseif ($item->status === 'selesai')
                                        <x-icon name="check_circle" size="16" />
                                    @elseif ($item->status === 'dilihat')
                                        <x-icon name="schedule" size="16" />
                                    @else
                                        <x-icon name="schedule" size="16" />
                                    @endif

                                    {{ $item->pesan_penanganan }}
                                </span>

                                {{-- Link Detail --}}
                                <a href="#"
                                    class="inline-flex shrink-0 items-center gap-1.5 text-[#005f49] transition-transform duration-200 hover:-translate-y-0.5">
                                    Lihat Detail & Riwayat Penanganan
                                    <x-icon name="arrow_forward" size="16" />
                                </a>
                            </div>
                        </article>
                    @empty
                        {{-- Tampilan ketika belum ada laporan --}}
                    @endforelse
                    
                    {{-- Pagination --}}
                    @if ($laporan->hasPages())
                        <div class="mt-6 flex flex-wrap items-center justify-between gap-3 rounded-2xl border border-black/5 bg-white px-6 py-4 shadow-sm">

                            <p class="text-sm text-text">
                                Menampilkan
                                <strong class="font-semibold text-ink">
                                    {{ $laporan->firstItem() }} - {{ $laporan->lastItem() }}
                                </strong>
                                dari
                                <strong class="font-semibold text-ink">{{ $laporan->total() }}</strong>
                                laporan
                            </p>

                            <nav class="flex items-center gap-2" aria-label="Halaman">
                                @if ($laporan->onFirstPage())
                                    <span class="grid size-10 place-items-center rounded-lg bg-soft text-ink opacity-40">&lsaquo;</span>
                                @else
                                    <a href="{{ $laporan->previousPageUrl() }}"
                                        class="grid size-10 place-items-center rounded-lg bg-soft text-ink transition hover:bg-[#dee8ff]">&lsaquo;</a>
                                @endif

                                @foreach ($laporan->getUrlRange(1, $laporan->lastPage()) as $page => $url)
                                    @if ($page == $laporan->currentPage())
                                        <span class="grid size-10 place-items-center rounded-lg bg-[#005f49] text-sm font-semibold text-white">
                                            {{ $page }}
                                        </span>
                                    @else
                                        <a href="{{ $url }}"
                                            class="grid size-10 place-items-center rounded-lg bg-soft text-sm font-semibold text-ink transition hover:bg-[#dee8ff]">
                                            {{ $page }}
                                        </a>
                                    @endif
                                @endforeach

                                @if ($laporan->hasMorePages())
                                    <a href="{{ $laporan->nextPageUrl() }}"
                                        class="grid size-10 place-items-center rounded-lg bg-soft text-ink transition hover:bg-[#dee8ff]">&rsaquo;</a>
                                @else
                                    <span class="grid size-10 place-items-center rounded-lg bg-soft text-ink opacity-40">&rsaquo;</span>
                                @endif
                            </nav>
                        </div>
                    @endif
                </div>
                </div>
            </aside>
        </div>
    </div>
</x-layouts.public>
