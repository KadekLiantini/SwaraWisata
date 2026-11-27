{{--
    Profil pengelola (Figma: MOCKUP > Halaman Profil Pengelola, node 343:12).
    Data dari Pengelola\ProfilController@show:
    $akun, $destinasi, $tab, $statistik, $jumlahRiwayat, $daftarRiwayat, $inisial, $idRegistrasi, $fotoDestinasi
--}}
@php
    $link = fn (string $route, $param = null) => Route::has($route) ? route($route, $param) : '#';
    $tanggal = fn ($waktu) => $waktu?->locale('id')->translatedFormat('d F Y');

    $kabupaten = $destinasi?->kecamatan?->kabupaten?->nama;
    $lokasi = collect([
        $destinasi?->kecamatan ? 'Kec. '.$destinasi->kecamatan->nama : null,
        $kabupaten ? (str_starts_with($kabupaten, 'Kota') ? $kabupaten : 'Kab. '.$kabupaten) : null,
        $destinasi ? 'Bali' : null,
    ])->filter()->implode(', ');

    $kartuStatistik = [
        ['label' => 'Total Laporan',   'angka' => $statistik['total'],           'ket' => 'Di Destinasi '.($destinasi?->nama ?? '-'), 'warna' => 'text-muted',         'angkaWarna' => 'text-ink'],
        ['label' => 'Belum Dilihat',   'angka' => $statistik['belum_dilihat'],   'ket' => 'Menunggu Ditinjau',                         'warna' => 'text-secondary-ink', 'angkaWarna' => 'text-secondary-ink'],
        ['label' => 'Ditindaklanjuti', 'angka' => $statistik['ditindaklanjuti'], 'ket' => 'Pekerjaan Berjalan',                        'warna' => 'text-primary',       'angkaWarna' => 'text-primary'],
        ['label' => 'Selesai Penuh',   'angka' => $statistik['selesai'],         'ket' => 'Tuntas & Terverifikasi',                    'warna' => 'text-muted',         'angkaWarna' => 'text-ink'],
    ];

    $kelasKartu = 'rounded-xl border border-line/30 bg-white drop-shadow-[0_1px_1px_rgba(0,0,0,0.05)]';
    $kelasLabel = 'font-heading text-[11px] leading-[14px] font-bold tracking-[0.55px] text-muted uppercase';
    $kelasSelect = 'appearance-none rounded-lg bg-soft py-2.5 pr-9 pl-3 font-heading text-xs leading-4 font-semibold text-ink focus:ring-2 focus:ring-primary focus:outline-none';
@endphp

<x-layouts.pengelola title="Profil Pengelola">
    <div class="flex flex-col gap-6 py-5">
        {{-- ============ JUDUL HALAMAN + TOMBOL ============ --}}
        <div class="flex flex-wrap items-end justify-between gap-4">
            <div class="flex max-w-3xl min-w-0 flex-1 basis-80 flex-col gap-1">
                <h1 class="font-heading text-[32px] leading-10 font-bold tracking-[-0.8px] text-ink">Profil Pengelola Destinasi</h1>
                <p class="text-[15px] leading-[22px] text-muted">
                    Informasi identitas akun resmi, destinasi binaan terikat, dan riwayat seluruh pembaruan penanganan laporan yang dikirimkan.
                </p>
            </div>

            <div class="flex shrink-0 items-center gap-2">
                <button type="button" onclick="document.getElementById('dialog-password').showModal()"
                        class="flex h-8 items-center gap-2 rounded-lg bg-secondary/55 pr-4 pl-3 font-heading text-xs leading-4 font-semibold text-secondary-ink shadow-[0_1px_2px_rgba(0,0,0,0.05)] transition hover:bg-secondary/70">
                    <x-icon name="lock_open" size="18" /> Ganti Password
                </button>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="flex h-8 items-center gap-2 rounded-lg bg-danger-soft px-4 font-heading text-xs leading-4 font-semibold text-[#93000a] drop-shadow-[0_1px_1px_rgba(0,0,0,0.05)] transition hover:brightness-95">
                        <x-icon name="logout" size="16" /> Keluar Portal
                    </button>
                </form>
            </div>
        </div>

        {{-- ============ KARTU AKUN + KARTU DESTINASI ============ --}}
        <section class="grid gap-3 lg:grid-cols-12">
            {{-- Kartu informasi akun --}}
            <div class="flex gap-4 self-start p-[17px] lg:col-span-4 {{ $kelasKartu }}">
                <div class="relative size-16 shrink-0">
                    <span class="flex size-16 items-center justify-center rounded-full bg-primary font-heading text-xl leading-7 font-extrabold text-white shadow-[0_4px_6px_-1px_rgba(0,0,0,0.1),0_2px_4px_-2px_rgba(0,0,0,0.1)]">{{ $inisial }}</span>
                    <span class="absolute right-0 bottom-0 flex size-5 items-center justify-center rounded-full border-2 border-white bg-mint text-primary">
                        <x-icon name="check" size="12" />
                    </span>
                </div>

                <div class="flex min-w-0 flex-1 flex-col gap-1">
                    <div class="flex flex-col items-start gap-2">
                        <p class="max-w-full truncate font-heading text-sm leading-5 font-bold text-ink">{{ $akun->username }}</p>
                        <span class="rounded-full bg-mint px-2 py-0.5 font-heading text-[11px] leading-[16.5px] font-bold text-[#00513e]">Pengelola</span>
                    </div>
                    <p class="truncate font-mono text-xs leading-4 font-bold text-primary">{{ '@'.$akun->username }}</p>
                    <p class="flex items-center gap-1 pt-1 text-xs leading-[18px] text-muted">
                        <x-icon name="calendar_today" size="14" />
                        Dibuat: <span class="font-medium text-xs text-ink">{{ $tanggal($akun->created_at) ?? '-' }}</span>
                    </p>
                </div>
            </div>

            {{-- Kartu destinasi yang dikelola (hanya baca) --}}
            <div class="flex flex-col gap-4 p-[17px] sm:flex-row lg:col-span-8 lg:min-h-[300px] {{ $kelasKartu }}">
                @if ($destinasi)
                    <div class="relative h-56 shrink-0 overflow-hidden rounded-lg bg-soft-strong sm:h-auto sm:w-56">
                        @if ($fotoDestinasi)
                            <img src="{{ $fotoDestinasi }}" alt="Foto {{ $destinasi->nama }}" class="absolute inset-0 size-full object-cover" onerror="this.remove()">
                        @else
                            <span class="absolute inset-0 flex items-center justify-center text-line"><x-icon name="landscape" size="48" /></span>
                        @endif
                        <span aria-hidden="true" class="absolute inset-0 bg-gradient-to-t from-ink/80 via-ink/0 to-ink/0"></span>
                        @if ($destinasi->kategoriWisata)
                            <span class="absolute bottom-2 left-2 rounded bg-white/90 px-2 py-0.5 font-heading text-[11px] leading-[16.5px] font-bold text-primary backdrop-blur-md">
                                {{ $destinasi->kategoriWisata->nama }}
                            </span>
                        @endif
                    </div>

                    <div class="flex min-w-0 flex-1 flex-col gap-2">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex min-w-0 flex-col gap-0.5">
                                <h2 class="font-heading text-xl leading-7 font-bold text-ink">{{ $destinasi->nama }}</h2>
                                @if ($lokasi)
                                    <p class="flex items-start gap-1 text-[13px] leading-[18px] text-muted">
                                        <x-icon name="location_on" size="14" class="mt-0.5" /> {{ $lokasi }}
                                    </p>
                                @endif
                            </div>

                            @if ($destinasi->link_maps)
                                <a href="{{ $destinasi->link_maps }}" target="_blank" rel="noopener"
                                   class="flex shrink-0 items-center gap-1 rounded-lg border border-line/30 bg-soft px-[11px] py-[7px] font-heading text-[11px] leading-[14px] font-bold text-primary transition hover:bg-soft-strong">
                                    <x-icon name="map" size="14" /> Buka Google Maps <x-icon name="open_in_new" size="12" />
                                </a>
                            @endif
                        </div>

                        <p class="text-[13px] leading-[18px] text-muted">{{ $destinasi->deskripsi ?: 'Belum ada deskripsi destinasi.' }}</p>
                    </div>
                @else
                    <div class="flex flex-1 flex-col items-center justify-center gap-2 py-10 text-center">
                        <x-icon name="location_off" size="32" class="text-line" />
                        <p class="font-heading text-sm font-semibold text-ink">Akun ini belum terhubung ke destinasi.</p>
                        <p class="text-[13px] text-muted">Hubungi administrator pusat untuk mengaitkan destinasi.</p>
                    </div>
                @endif
            </div>
        </section>

        {{-- ============ RINGKASAN ANGKA ============ --}}
        <section class="grid grid-cols-2 gap-2 lg:grid-cols-4">
            @foreach ($kartuStatistik as $kartu)
                <div class="flex flex-col px-[9px] pt-2 pb-[9px] {{ $kelasKartu }}">
                    <p class="font-heading text-[11px] leading-[16.5px] font-semibold tracking-[0.55px] uppercase {{ $kartu['warna'] }}">{{ $kartu['label'] }}</p>
                    <p class="pt-1 font-heading text-2xl leading-8 font-bold {{ $kartu['angkaWarna'] }}">{{ $kartu['angka'] }}</p>
                    <p class="truncate text-[11px] leading-[16.5px] {{ $kartu['warna'] }}">{{ $kartu['ket'] }}</p>
                </div>
            @endforeach
        </section>

        {{-- ============ TAB ============ --}}
        <div id="tab-profil" class="flex scroll-mt-24 flex-col gap-6">
            <nav class="flex gap-2 overflow-x-auto border-b border-line/40 pt-1" aria-label="Tab profil">
                @foreach ([
                    'akun'    => ['label' => 'Informasi Akun',         'ikon' => 'badge'],
                    'riwayat' => ['label' => 'Riwayat Pembaruan Saya', 'ikon' => 'history'],
                ] as $kunci => $item)
                    <a href="{{ route('pengelola.profil', $kunci === 'akun' ? [] : ['tab' => $kunci]) }}#tab-profil"
                       @if ($tab === $kunci) aria-current="page" @endif
                       @class([
                           '-mb-px flex shrink-0 items-center gap-2 border-b-2 px-4 pb-2.5 font-heading text-sm leading-5 whitespace-nowrap transition',
                           'border-primary font-bold text-primary' => $tab === $kunci,
                           'border-transparent font-semibold text-muted hover:text-primary' => $tab !== $kunci,
                       ])>
                        <x-icon :name="$item['ikon']" size="18" /> {{ $item['label'] }}
                        @if ($kunci === 'riwayat')
                            <span class="rounded-full bg-mint px-2 py-0.5 font-heading text-[11px] leading-5 font-bold text-[#00513e]">{{ $jumlahRiwayat }}</span>
                        @endif
                    </a>
                @endforeach
            </nav>

            @if ($tab === 'akun')
                {{-- ============ TAB 1: INFORMASI AKUN ============ --}}
                <div class="flex flex-col gap-4 px-[25px] pt-[25px] pb-7 {{ $kelasKartu }}">
                    <div class="flex items-center justify-between gap-3 border-b border-soft-strong pb-[9px]">
                        <h2 class="flex items-center gap-2 font-heading text-base leading-6 font-semibold text-ink">
                            <x-icon name="account_box" size="20" class="text-muted" /> Rincian Akun Resmi Pengelola
                        </h2>
                        <span class="rounded bg-mint px-2.5 py-1 font-heading text-[11px] leading-[14px] font-bold text-[#00513e]">Tervalidasi Sistem</span>
                    </div>

                    <dl class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <dt class="{{ $kelasLabel }}">Username Akun</dt>
                            <dd class="pt-1 font-mono text-[15px] leading-[22px] font-bold break-all text-primary">{{ '@'.$akun->username }}</dd>
                        </div>
                        <div>
                            <dt class="{{ $kelasLabel }}">Peran / Role</dt>
                            <dd class="pt-1 text-[15px] leading-[22px] font-semibold text-ink">Pengelola Destinasi Resmi</dd>
                        </div>
                        <div>
                            <dt class="{{ $kelasLabel }}">ID Registrasi Akun</dt>
                            <dd class="pt-1 font-mono text-[15px] leading-[22px] font-bold text-ink">{{ $idRegistrasi }}</dd>
                        </div>
                        <div>
                            <dt class="{{ $kelasLabel }}">Tanggal Akun Dibuat</dt>
                            <dd class="pt-1 text-[15px] leading-[22px] font-semibold text-ink">{{ $tanggal($akun->created_at) ?? '-' }}</dd>
                            <dd class="text-[11px] leading-[16.5px] text-muted">Didaftarkan oleh Administrator Pusat</dd>
                        </div>
                        <div class="rounded-lg bg-soft p-2 sm:col-span-2">
                            <dt class="{{ $kelasLabel }}">Destinasi Terikat (Satu Akun Satu Kawasan)</dt>
                            <dd class="pt-1 font-heading text-base leading-6 font-bold text-ink">{{ $destinasi?->nama ?? 'Belum terhubung' }}</dd>
                        </div>
                    </dl>
                </div>
            @else
                {{-- ============ TAB 2: RIWAYAT PEMBARUAN SAYA ============ --}}
                <div class="overflow-hidden {{ $kelasKartu }}">
                    <form method="GET" action="{{ route('pengelola.profil') }}#tab-profil" data-live-search data-live-target="#hasil-riwayat"
                          class="flex flex-wrap items-center gap-2 border-b border-soft-strong p-4">
                        <input type="hidden" name="tab" value="riwayat">

                        <label class="relative min-w-56 flex-1">
                            <span class="sr-only">Cari riwayat</span>
                            <x-icon name="search" size="18" class="pointer-events-none absolute top-1/2 left-3 -translate-y-1/2 text-subtle" />
                            <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari judul laporan, #ID, atau isi pembaruan..."
                                   class="w-full rounded-lg bg-soft py-2.5 pr-3 pl-9 text-[13px] leading-[18px] text-ink placeholder:text-placeholder focus:ring-2 focus:ring-primary focus:outline-none">
                        </label>

                        @foreach ([
                            'jenis' => ['' => 'Semua Jenis', 'tindak_lanjut' => 'Tindak Lanjut', 'penyelesaian' => 'Penyelesaian'],
                            'urut'  => ['terbaru' => 'Terbaru', 'terlama' => 'Terlama'],
                        ] as $nama => $opsi)
                            <label class="relative">
                                <span class="sr-only">{{ $nama === 'jenis' ? 'Jenis tindakan' : 'Urutkan' }}</span>
                                <select name="{{ $nama }}" class="{{ $kelasSelect }}">
                                    @foreach ($opsi as $nilai => $teks)
                                        <option value="{{ $nilai }}" @selected(request($nama, $nama === 'urut' ? 'terbaru' : '') === $nilai)>{{ $teks }}</option>
                                    @endforeach
                                </select>
                                <x-icon name="expand_more" size="18" class="pointer-events-none absolute top-1/2 right-2 -translate-y-1/2 text-muted" />
                            </label>
                        @endforeach
                    </form>

                    <div id="hasil-riwayat">
                        @forelse ($daftarRiwayat as $riwayat)
                            @php
                                $foto = $riwayat->foto->first();
                                $urlFoto = $foto ? ($foto->url ?? asset('storage/'.$foto->path)) : null;
                                $selesai = $riwayat->jenis_tindakan === 'penyelesaian';
                            @endphp
                            <article class="flex gap-4 border-b border-soft-strong p-4 last:border-b-0">
                                <div class="relative size-16 shrink-0 overflow-hidden rounded-lg bg-soft-strong">
                                    <x-icon name="photo_camera" size="20" class="absolute inset-0 m-auto text-line" />
                                    @if ($urlFoto)
                                        <img src="{{ $urlFoto }}" alt="" class="absolute inset-0 size-full object-cover" onerror="this.remove()">
                                    @endif
                                </div>

                                <div class="flex min-w-0 flex-1 flex-col gap-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <span @class([
                                            'inline-flex items-center gap-1 rounded-full px-2 py-0.5 font-heading text-[11px] leading-4 font-bold',
                                            'bg-mint text-[#00513e]' => $selesai,
                                            'bg-peach text-secondary-ink' => ! $selesai,
                                        ])>
                                            <x-icon :name="$selesai ? 'task_alt' : 'construction'" size="12" />
                                            {{ $selesai ? 'Penyelesaian' : 'Tindak Lanjut' }}
                                        </span>
                                        <span class="text-xs leading-4 text-subtle">{{ $riwayat->created_at?->locale('id')->translatedFormat('d F Y, H:i') }}</span>
                                    </div>

                                    <a href="{{ $link('pengelola.laporan.show', $riwayat->laporan) }}"
                                       class="truncate font-heading text-sm leading-5 font-bold text-ink hover:text-primary hover:underline">
                                        <span class="text-primary">{{ $riwayat->laporan->kode }}</span> {{ $riwayat->laporan->judul }}
                                    </a>
                                    <p class="line-clamp-2 text-[13px] leading-[18px] text-muted">{{ $riwayat->deskripsi }}</p>

                                    <p class="flex items-center gap-3 pt-0.5 text-xs leading-4 text-subtle">
                                        <span class="flex items-center gap-1"><x-icon name="image" size="14" /> {{ $riwayat->foto->count() }} foto</span>
                                        <span class="flex items-center gap-1"><x-icon name="chat_bubble" size="14" /> {{ $riwayat->komentar_count }} komentar</span>
                                    </p>
                                </div>

                                <a href="{{ $link('pengelola.laporan.show', $riwayat->laporan) }}" title="Buka laporan" aria-label="Buka laporan"
                                   class="hidden size-9 shrink-0 items-center justify-center self-center rounded-lg bg-soft text-primary transition hover:bg-soft-strong sm:flex">
                                    <x-icon name="chevron_right" size="20" />
                                </a>
                            </article>
                        @empty
                            <div class="flex flex-col items-center gap-2 px-4 py-12 text-center">
                                <span class="flex size-12 items-center justify-center rounded-full bg-soft text-primary"><x-icon name="history" size="24" /></span>
                                <p class="font-heading text-sm font-semibold text-ink">
                                    {{ request()->hasAny(['q', 'jenis']) ? 'Tidak ada pembaruan yang cocok.' : 'Belum ada pembaruan penanganan yang dikirim.' }}
                                </p>
                                <p class="text-[13px] text-muted">Pembaruan tindak lanjut dan penyelesaian yang Anda kirim akan muncul di sini.</p>
                                @unless (request()->hasAny(['q', 'jenis']))
                                    <x-button :href="$link('pengelola.laporan.index')" size="sm" icon-left="flag" class="mt-2">Buka Daftar Laporan</x-button>
                                @endunless
                            </div>
                        @endforelse

                        @if ($daftarRiwayat->total() > 0)
                            <x-pagination :paginator="$daftarRiwayat" label="pembaruan" />
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    <x-dialog.ganti-password :action="route('pengelola.password.update')" />
</x-layouts.pengelola>