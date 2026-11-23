<x-layouts.public title="Beranda Wisatawan">
    <div class="mx-auto w-full max-w-[1440px] space-y-5 px-4 py-10 sm:px-8">

        {{-- Sapaan dan ringkasan laporan --}}
        <section class="rounded-2xl bg-gradient-to-r from-soft via-soft to-[#d9f3f5] p-6 sm:p-8">
            <h1 class="font-heading text-3xl font-bold text-ink">
                Halo, {{ $akun->username }}!
            </h1>
            <p class="mt-2 max-w-2xl text-sm leading-6 text-muted">
                Cari tempat wisata yang ingin Anda laporkan. Pastikan destinasi sudah terdaftar
                agar laporan kerusakan langsung terhubung ke instansi dan pengelola resmi.
            </p>

            <div class="mt-6 grid grid-cols-2 gap-3 lg:grid-cols-4">
                <x-card padding="md">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-semibold uppercase text-subtle">Total Laporan Saya</p>
                        <span class="rounded-lg bg-soft p-2 text-primary">
                            <x-icon name="folder_open" />
                        </span>
                    </div>
                    <div class="mt-3 flex items-end gap-2">
                        <span class="font-heading text-4xl font-bold text-ink">{{ $totalLaporan }}</span>
                        <span class="mb-1 text-xs text-muted">Laporan</span>
                    </div>
                    <span class="text-xs font-semibold text-primary">Buka riwayat audit →</span>
                </x-card>

                <x-card padding="md">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-semibold uppercase text-subtle">Menunggu / Dilihat</p>
                        <span class="rounded-lg bg-soft-strong p-2 text-ink">
                            <x-icon name="visibility" />
                        </span>
                    </div>
                    <div class="mt-3 flex items-end gap-2">
                        <span class="font-heading text-4xl font-bold text-ink">{{ $menungguLaporan }}</span>
                        <span class="mb-1 text-xs text-muted">Dalam antrean</span>
                    </div>
                </x-card>

                <x-card padding="md">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-semibold uppercase text-secondary-ink">Ditindaklanjuti</p>
                        <span class="rounded-lg bg-secondary/30 p-2 text-secondary-ink">
                            <x-icon name="engineering" />
                        </span>
                    </div>
                    <div class="mt-3 flex items-end gap-2">
                        <span class="font-heading text-4xl font-bold text-secondary-ink">{{ $ditindaklanjuti }}</span>
                        <span class="mb-1 text-xs text-muted">Sedang aksi</span>
                    </div>
                </x-card>

                <x-card padding="md">
                    <div class="flex items-center justify-between">
                        <p class="text-xs font-semibold uppercase text-primary">Selesai Ditangani</p>
                        <span class="rounded-lg bg-mint p-2 text-primary">
                            <x-icon name="verified" />
                        </span>
                    </div>
                    <div class="mt-3 flex items-end gap-2">
                        <span class="font-heading text-4xl font-bold text-primary">{{ $selesai }}</span>
                        <span class="mb-1 text-xs text-muted">Tuntas dipulihkan</span>
                    </div>
                </x-card>
            </div>
        </section>

        {{-- Pencarian destinasi: satu baris penuh --}}
        <section class="rounded-xl bg-white p-5 shadow-sm">
            <form action="{{ route('wisatawan.beranda') }}" method="GET">
                <div class="flex items-center gap-3 rounded-lg border border-soft-strong bg-soft px-3">
                    <x-icon name="search" class="text-primary" />
                    <input
                        type="text"
                        name="cari"
                        value="{{ request('cari') }}"
                        placeholder="Cari nama tempat wisata, cagar budaya, atau pantai..."
                        class="w-full border-0 bg-transparent py-3 text-sm text-ink outline-none focus:ring-0"
                    >
                </div>

                {{-- Filter dan sorting: satu baris di bawah pencarian --}}
                <div class="mt-4 grid grid-cols-1 gap-3 md:grid-cols-3">
                    <div>
                        <label for="kabupaten" class="mb-1 flex items-center gap-1 text-xs font-semibold text-subtle">
                            <x-icon name="location_on" size="15" />
                            Wilayah
                        </label>
                        <select
                            name="kabupaten"
                            id="kabupaten"
                            class="w-full rounded-lg border-0 bg-soft px-3 py-3 text-sm text-ink focus:ring-1 focus:ring-primary"
                        >
                            <option value="">Semua Wilayah Terdaftar</option>
                            @foreach ($kabupaten as $wilayah)
                                <option value="{{ $wilayah->id_kabupaten }}"
                                    @selected(request('kabupaten') == $wilayah->id_kabupaten)>
                                    {{ $wilayah->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="kategori" class="mb-1 flex items-center gap-1 text-xs font-semibold text-subtle">
                            Kategori Wisata
                        </label>
                        <select
                            name="kategori"
                            id="kategori"
                            class="w-full rounded-lg border-0 bg-soft px-3 py-3 text-sm text-ink focus:ring-1 focus:ring-primary"
                        >
                            <option value="">Semua Kategori</option>
                            @foreach ($kategoriWisata as $kategori)
                                <option value="{{ $kategori->id_kategori_wisata }}"
                                    @selected(request('kategori') == $kategori->id_kategori_wisata)>
                                    {{ $kategori->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="urutkan" class="mb-1 flex items-center gap-1 text-xs font-semibold text-subtle">
                            <x-icon name="sort" size="15" />
                            Urutkan Berdasarkan
                        </label>
                        <select
                            name="urutkan"
                            id="urutkan"
                            class="w-full rounded-lg border-0 bg-soft px-3 py-3 text-sm text-ink focus:ring-1 focus:ring-primary"
                        >
                            <option value="terbanyak" @selected($urutkan === 'terbanyak')>Paling Banyak Dilaporkan</option>
                            <option value="nama_asc" @selected($urutkan === 'nama_asc')>Nama A–Z</option>
                            <option value="nama_desc" @selected($urutkan === 'nama_desc')>Nama Z–A</option>
                            <option value="terbaru" @selected($urutkan === 'terbaru')>Destinasi Terbaru</option>
                        </select>
                    </div>
                </div>

                <div class="mt-3 flex flex-wrap gap-2">
                    <x-button type="submit" size="sm" icon-left="search">Terapkan</x-button>
                    <x-button :href="route('wisatawan.beranda')" variant="outline" size="sm">Reset</x-button>
                </div>
            </form>
        </section>

        {{-- Konten utama: destinasi kiri, pembaruan dan etika kanan --}}
        <div class="grid grid-cols-1 items-start gap-5 lg:grid-cols-3">

            {{-- Kolom kiri: daftar destinasi --}}
            <section class="space-y-5 lg:col-span-2">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    @forelse ($destinasi as $item)
                        <x-card padding="md" class="flex flex-col">    
                            {{-- Foto destinasi dan badge kategori --}}
                            <div class="relative">
                                @if ($item->foto)
                                    <img
                                        src="{{ asset('storage/' . $item->foto) }}"
                                        alt="{{ $item->nama }}"
                                        class="h-36 w-full rounded-lg object-cover"
                                    >
                                @else
                                    <div class="flex h-36 items-center justify-center rounded-lg bg-soft text-primary">
                                        <x-icon name="landscape" size="44" />
                                    </div>
                                @endif

                                <span class="absolute left-2 top-2 rounded-full bg-white/95 px-2 py-1 text-xs font-semibold text-primary shadow-sm">
                                    {{ $item->kategoriWisata?->nama ?? 'Destinasi Wisata' }}
                                </span>
                            </div>

                            <div class="mt-3 flex flex-1 flex-col">
                                <p class="mt-2 flex items-center gap-1 text-xs text-subtle">
                                    <x-icon name="location_on" size="14" />
                                    Kec. {{ $item->kecamatan?->nama ?? '-' }},
                                    Kab. {{ $item->kecamatan?->kabupaten?->nama ?? '-' }}
                                </p>

                                <h3 class="mt-1 font-heading text-base font-bold text-ink">
                                    {{ $item->nama }}
                                </h3>

                                <p class="mt-1 line-clamp-2 text-xs leading-5 text-muted">
                                    {{ $item->deskripsi ?: 'Belum ada deskripsi destinasi.' }}
                                </p>

                                <div class="mt-auto flex items-center justify-between gap-2 pt-4">
                                    <span class="flex items-center gap-1 rounded-md bg-soft-strong px-2 py-1 text-xs font-semibold text-ink">
                                        <x-icon name="warning" size="14" />
                                        {{ $item->laporan_count }} Laporan Aktif
                                    </span>

                                    @if (Route::has('laporan.create'))
                                        <a
                                            href="{{ route('laporan.create', ['id_destinasi' => $item->id_destinasi]) }}"
                                            class="inline-flex items-center gap-1 rounded-lg bg-primary px-3 py-2 text-xs font-semibold text-white transition hover:opacity-90"
                                        >
                                            Lihat & Lapor
                                            <x-icon name="arrow_forward" size="15" />
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </x-card>
                    @empty
                        <div class="rounded-xl border border-dashed border-line bg-white p-8 text-center sm:col-span-2">
                            <x-icon name="search_off" size="40" class="mx-auto text-subtle" />
                            <p class="mt-3 font-semibold text-ink">Destinasi tidak ditemukan</p>
                            <p class="mt-1 text-sm text-muted">
                                Coba ubah kata kunci atau filter pencarian.
                            </p>
                        </div>
                    @endforelse
                </div>

                <div>
                    {{ $destinasi->links() }}
                </div>

                {{-- Pengajuan destinasi baru: di bawah card destinasi --}}
                <section class="flex flex-col items-start gap-5 rounded-xl bg-primary p-6 text-white sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-start gap-3">
                        <span class="rounded-lg bg-white/15 p-3">
                            <x-icon name="add_location_alt" size="28" />
                        </span>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-wide text-mint">
                                Aturan Pelaporan Mandiri
                            </p>
                            <h2 class="mt-1 font-heading text-xl font-bold">
                                Tempat Wisata Tujuan Anda Belum Terdaftar?
                            </h2>
                            <p class="mt-2 max-w-lg text-sm leading-6 text-white/80">
                                Jangan biarkan fasilitas rusak terbengkalai. Ajukan pendaftaran
                                destinasi baru agar tim admin dapat memverifikasi pengelola resmi
                                dan membuka kanal aduan warga.
                            </p>
                        </div>
                    </div>

                    @if (Route::has('pengajuan.create'))
                        <x-button :href="route('pengajuan.create')" variant="secondary" size="md" icon-left="add_location_alt" class="shrink-0">
                            Ajukan Destinasi Wisata Baru
                        </x-button>
                    @endif
                </section>
            </section>

            {{-- Kolom kanan --}}
            <aside class="flex flex-col gap-4">

                {{-- Pembaruan laporan saya --}}
                <x-card padding="md">
                    <h2 class="flex items-center gap-2 font-heading text-base font-bold text-ink">
                        <x-icon name="notifications_active" class="text-primary" />
                        Pembaruan Laporan Saya
                    </h2>
                    <p class="mt-2 text-xs leading-5 text-muted">
                        Pemberitahuan resmi pengelola tempat wisata atas laporan Anda.
                    </p>

                    <div class="mt-4 space-y-3">
                        @forelse ($pembaruan as $update)
                            <div class="rounded-lg bg-soft p-3">
                                @if ($update->jenis_tindakan === 'penyelesaian')
                                    <span class="inline-flex rounded-full bg-mint px-2 py-1 text-xs font-semibold text-primary">
                                        ● Selesai
                                    </span>
                                @else
                                    <span class="inline-flex rounded-full bg-secondary/30 px-2 py-1 text-xs font-semibold text-secondary-ink">
                                        ● Ditindaklanjuti
                                    </span>
                                @endif

                                <h3 class="mt-2 text-sm font-bold text-ink">
                                    {{ $update->laporan?->judul ?? 'Laporan' }}
                                </h3>

                                <p class="mt-1 text-xs leading-5 text-muted">
                                    {{ $update->deskripsi ?: 'Pengelola telah memberikan pembaruan pada laporan Anda.' }}
                                </p>

                                <div class="mt-2 flex items-center justify-between gap-2 text-xs text-subtle">
                                    <span class="flex items-center gap-1">
                                        <x-icon name="location_on" size="13" />
                                        {{ $update->laporan?->destinasi?->nama ?? '-' }}
                                    </span>
                                    <span>{{ $update->created_at?->diffForHumans() }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-lg bg-soft p-5 text-center">
                                <x-icon name="notifications_none" size="30" class="text-subtle" />
                                <p class="mt-2 text-sm font-semibold text-ink">Belum ada pembaruan</p>
                                <p class="mt-1 text-xs text-muted">
                                    Pembaruan laporan Anda akan muncul di sini.
                                </p>
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-3">
                        @if (Route::has('laporan.index'))
                            <x-button :href="route('laporan.index')" variant="outline" size="sm" block>
                                Buka Semua Laporan Saya
                                <x-icon name="open_in_new" size="15" />
                            </x-button>
                        @endif
                    </div>
                </x-card>

                {{-- Etika pelaporan --}}
                <x-card padding="md" class="bg-soft">
                    <h2 class="flex items-center gap-2 font-heading text-base font-bold text-ink">
                        <x-icon name="verified_user" class="text-primary" />
                        Etika Pelaporan SwaraWisata
                    </h2>

                    <ul class="mt-3 space-y-2 text-xs leading-5 text-muted">
                        <li class="flex items-start gap-2">
                            <x-icon name="check_circle" size="16" class="text-primary" />
                            Sertakan bukti foto kondisi fasilitas yang jelas dan aktual.
                        </li>
                        <li class="flex items-start gap-2">
                            <x-icon name="check_circle" size="16" class="text-primary" />
                            Gunakan bahasa santun tanpa menyudutkan perseorangan.
                        </li>
                    </ul>
                </x-card>
            </aside>
        </div>

    </div>
</x-layouts.public>
