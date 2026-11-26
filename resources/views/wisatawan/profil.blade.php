{{--
    Profil wisatawan (Figma: Profil Wisatawan).
    Data dari Wisatawan\ProfilController@show: $akun, $tab, $statistik, $daftar, $opsiDestinasi
--}}
@php
    $link = fn (string $route, $param = null) => Route::has($route) ? route($route, $param) : '#';
    $inisial = collect(preg_split('/[_\s]+/', $akun->username))->filter()->take(2)
        ->map(fn ($kata) => mb_strtoupper(mb_substr($kata, 0, 1)))->implode('');
    $inisial = mb_strlen($inisial) < 2 ? mb_strtoupper(mb_substr($akun->username, 0, 2)) : $inisial;

    $opsiStatus = $tab === 'laporan'
        ? ['belum_dilihat' => 'Belum Dilihat', 'dilihat' => 'Dilihat', 'ditindaklanjuti' => 'Ditindaklanjuti', 'selesai' => 'Selesai']
        : ['menunggu_verifikasi' => 'Menunggu Verifikasi', 'disetujui' => 'Disetujui', 'ditolak' => 'Ditolak'];

    $kelasSelect = 'appearance-none rounded-lg bg-soft py-2.5 pr-9 pl-3 font-heading text-xs leading-4 font-semibold text-ink focus:ring-2 focus:ring-primary focus:outline-none';
    $tombolAksi = 'flex size-9 items-center justify-center rounded-lg transition';
@endphp

<x-layouts.public title="Profil Saya" glow>
    <div class="mx-auto flex w-full max-w-[1280px] flex-col gap-4 px-4 pt-4 pb-10 sm:px-8">
        {{-- Breadcrumb --}}
        <nav aria-label="Breadcrumb" class="flex items-center gap-1">
            <a href="{{ $akun->dashboardRoute() }}" class="flex items-center gap-1 text-[13px] leading-[18px] text-muted hover:text-primary">
                <x-icon name="home" size="16" /> Beranda Wisatawan
            </a>
            <x-icon name="chevron_right" size="16" class="text-muted" />
            <span class="rounded-full bg-soft-strong px-2 py-0.5 font-heading text-xs leading-4 font-semibold text-primary">Profil Saya</span>
        </nav>

        {{-- ============ PROFIL & RINGKASAN ============ --}}
        <section class="grid gap-4 lg:grid-cols-12">
            {{-- Kartu profil --}}
            <div class="relative flex flex-col overflow-hidden rounded-xl bg-white p-6 shadow-[0_1px_2px_rgba(0,0,0,0.05)] lg:col-span-4">
                <div aria-hidden="true" class="absolute top-0 right-0 size-32 rounded-bl-[80px] bg-primary/5"></div>

                <div class="relative flex flex-col gap-4 pb-4">
                    <div class="flex items-start justify-between">
                        <div class="relative">
                            <span class="flex size-20 items-center justify-center rounded-full bg-primary font-heading text-2xl leading-8 font-bold tracking-[1.2px] text-white shadow-md">{{ $inisial }}</span>
                            <span class="absolute -right-1 -bottom-1 flex size-6 items-center justify-center rounded-full bg-white p-0.5">
                                <span class="flex size-full items-center justify-center rounded-full bg-primary-soft text-white">
                                    <x-icon name="verified" size="14" />
                                </span>
                            </span>
                        </div>
                        <span class="inline-flex items-center gap-1.5 rounded-full bg-primary/10 px-2 py-1 font-heading text-[11px] leading-[14px] font-bold text-primary">
                            <x-icon name="verified_user" size="14" /> Wisatawan
                        </span>
                    </div>

                    <div class="flex flex-col gap-1">
                        <h1 class="font-heading text-2xl leading-8 font-bold tracking-[-0.6px] break-all text-ink">{{ $akun->username }}</h1>
                        <p class="flex items-center gap-1.5 text-[13px] leading-[18px] text-muted">
                            <x-icon name="badge" size="16" /> Username Akun: {{ '@'.$akun->username }}
                        </p>
                    </div>

                    <p class="flex items-center gap-2 rounded-lg bg-soft p-2 text-[13px] leading-[18px] text-muted">
                        <x-icon name="calendar_month" size="16" class="text-primary" />
                        Bergabung sejak:
                        <span class="font-heading text-[11px] leading-[14px] font-bold text-ink">{{ $akun->created_at?->locale('id')->translatedFormat('d F Y') }}</span>
                    </p>
                </div>

                <div class="relative flex flex-col gap-2 pt-2">
                    <button type="button" onclick="document.getElementById('dialog-password').showModal()"
                            class="flex w-full items-center justify-center gap-2 rounded-lg bg-[#febe5f] px-4 py-2 font-heading text-sm leading-5 font-semibold text-black transition hover:brightness-95">
                        <x-icon name="lock_open" size="20" /> Ganti Password
                    </button>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="flex w-full items-center justify-center gap-2 rounded-lg bg-danger px-4 py-2 font-heading text-sm leading-5 font-semibold text-white transition hover:brightness-110">
                            <x-icon name="logout" size="16" /> Keluar (Logout)
                        </button>
                    </form>
                </div>
            </div>

            {{-- 4 kartu statistik --}}
            <div class="grid gap-4 sm:grid-cols-2 lg:col-span-8">
                @foreach ([
                    ['label' => 'Total Laporan Kerusakan', 'angka' => $statistik['total'],    'warna' => 'text-primary',   'titik' => 'bg-primary',   'ikon' => 'assignment_late',  'ket' => 'Semua aduan terkirim',          'catatan' => '100% Tercatat'],
                    ['label' => 'Sedang Diproses',         'angka' => $statistik['dilihat'] + $statistik['ditindaklanjuti'], 'warna' => 'text-secondary', 'titik' => 'bg-secondary', 'ikon' => 'pending_actions', 'ket' => $statistik['dilihat'].' Dilihat + '.$statistik['ditindaklanjuti'].' Ditindaklanjuti', 'catatan' => 'Dalam Antrean'],
                    ['label' => 'Laporan Selesai',         'angka' => $statistik['selesai'],  'warna' => 'text-primary',   'titik' => 'bg-primary',   'ikon' => 'task_alt',         'ket' => 'Fasilitas telah tuntas diperbaiki', 'catatan' => 'Solutif & Teruji'],
                    ['label' => 'Usulan Destinasi Baru',   'angka' => $statistik['pengajuan'], 'warna' => 'text-[#005e54]', 'titik' => 'bg-[#005e54]', 'ikon' => 'add_location_alt', 'ket' => 'Tempat wisata yang diusulkan', 'catatan' => $statistik['disetujui'].' Disetujui'],
                ] as $kartu)
                    <div class="flex flex-col gap-[3px] rounded-xl bg-white p-6 drop-shadow-[0_1px_1px_rgba(0,0,0,0.05)]">
                        <div class="flex items-start justify-between">
                            <div class="flex flex-col gap-1.5 pt-1.5">
                                <p class="font-heading text-xs leading-4 font-semibold tracking-[0.6px] text-subtle uppercase">{{ $kartu['label'] }}</p>
                                <p class="font-heading text-5xl leading-[56px] font-bold tracking-[-1.2px] {{ $kartu['warna'] }}">{{ $kartu['angka'] }}</p>
                            </div>
                            <span class="flex size-12 items-center justify-center rounded-xl bg-soft-strong {{ $kartu['warna'] }}">
                                <x-icon :name="$kartu['ikon']" size="24" />
                            </span>
                        </div>
                        <div class="flex items-center justify-between gap-2 pt-4">
                            <p class="flex items-center gap-1 text-[13px] leading-[18px] text-muted">
                                <span class="size-2 shrink-0 rounded-full {{ $kartu['titik'] }}"></span> {{ $kartu['ket'] }}
                            </p>
                            <p class="font-heading text-[11px] leading-[14px] font-bold whitespace-nowrap {{ $kartu['warna'] }}">{{ $kartu['catatan'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- ============ TAB ============ --}}
        <div id="riwayat" class="scroll-mt-24 pt-6">
            <div class="inline-flex flex-wrap gap-2 rounded-xl bg-[#dee8ff] p-1.5 shadow-[inset_0_2px_4px_rgba(0,0,0,0.05)]">
                @foreach ([
                    'laporan'   => ['label' => 'Riwayat Pelaporan Kerusakan', 'ikon' => 'assignment_turned_in', 'jumlah' => $statistik['total']],
                    'pengajuan' => ['label' => 'Riwayat Pengajuan Destinasi', 'ikon' => 'explore',              'jumlah' => $statistik['pengajuan']],
                ] as $kunci => $item)
                    <a href="{{ route('wisatawan.profil', ['tab' => $kunci]) }}#riwayat" @if ($tab === $kunci) aria-current="page" @endif
                       @class([
                           'flex items-center gap-2 rounded-lg px-4 py-2 font-heading text-sm leading-5 font-semibold transition',
                           'bg-primary text-white shadow-[0_4px_12px_-2px_rgba(0,95,73,0.25)]' => $tab === $kunci,
                           'text-muted hover:bg-white/60' => $tab !== $kunci,
                       ])>
                        <x-icon :name="$item['ikon']" size="18" />
                        {{ $item['label'] }}
                        <span @class([
                            'ml-1 rounded-full px-2 py-0.5 text-[11px] leading-[14px] font-bold',
                            'bg-white/20 text-white' => $tab === $kunci,
                            'bg-soft-strong text-ink' => $tab !== $kunci,
                        ])>{{ $item['jumlah'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>

        {{-- ============ FILTER ============ --}}
        <form method="GET" action="{{ route('wisatawan.profil') }}#riwayat" data-live-search data-live-target="#hasil-tabel" class="mt-2 flex flex-wrap items-center justify-between gap-3 rounded-xl bg-white p-4 drop-shadow-[0_1px_1px_rgba(0,0,0,0.05)]">
            <input type="hidden" name="tab" value="{{ $tab }}">

            <label class="relative w-full sm:w-80">
                <span class="sr-only">Cari</span>
                <x-icon name="search" size="18" class="pointer-events-none absolute top-1/2 left-3 -translate-y-1/2 text-muted" />
                <input type="search" name="q" value="{{ request('q') }}"
                       placeholder="{{ $tab === 'laporan' ? 'Cari judul atau ID laporan (#16)...' : 'Cari nama lokasi yang diajukan...' }}"
                       class="w-full rounded-lg bg-soft py-[11px] pr-4 pl-10 text-[13px] text-ink placeholder:text-subtle focus:ring-2 focus:ring-primary focus:outline-none">
            </label>

            <div class="flex flex-wrap items-center gap-2">
                <label class="relative">
                    <span class="sr-only">Status</span>
                    <select name="status" class="{{ $kelasSelect }}">
                        <option value="">Semua Status</option>
                        @foreach ($opsiStatus as $nilai => $teks)
                            <option value="{{ $nilai }}" @selected(request('status') === $nilai)>{{ $teks }}</option>
                        @endforeach
                    </select>
                    <x-icon name="expand_more" size="18" class="pointer-events-none absolute top-1/2 right-2.5 -translate-y-1/2 text-muted" />
                </label>

                @if ($tab === 'laporan')
                    <label class="relative">
                        <span class="sr-only">Destinasi</span>
                        <select name="destinasi" onchange="this.form.submit()" class="{{ $kelasSelect }} min-w-48">
                            <option value="">Semua Destinasi</option>
                            @foreach ($opsiDestinasi as $id => $nama)
                                <option value="{{ $id }}" @selected((string) request('destinasi') === (string) $id)>{{ $nama }}</option>
                            @endforeach
                        </select>
                        <x-icon name="expand_more" size="18" class="pointer-events-none absolute top-1/2 right-2.5 -translate-y-1/2 text-muted" />
                    </label>
                @endif

                <label class="relative">
                    <span class="sr-only">Urutkan</span>
                    <select name="urut" onchange="this.form.submit()" class="{{ $kelasSelect }}">
                        <option value="terbaru">Terbaru</option>
                        <option value="terlama" @selected(request('urut') === 'terlama')>Terlama</option>
                    </select>
                    <x-icon name="sort" size="18" class="pointer-events-none absolute top-1/2 right-2.5 -translate-y-1/2 text-muted" />
                </label>
            </div>
        </form>

        {{-- ============ TABEL ============ --}}
        <div id="hasil-tabel" class="mt-2 overflow-hidden rounded-xl bg-white shadow-[0_1px_2px_rgba(0,0,0,0.05)]">
            <div class="overflow-x-auto">
                @if ($tab === 'laporan')
                    <table class="w-full min-w-[1100px] text-left">
                        <thead class="bg-[#dee8ff] font-heading text-xs leading-4 font-bold tracking-[0.6px] text-ink uppercase">
                            <tr>
                                <th class="w-28 px-4 py-6">Bukti Foto</th>
                                <th class="px-4 py-6">ID &amp; Judul Laporan</th>
                                <th class="w-52 px-4 py-6">Destinasi</th>
                                <th class="w-40 px-4 py-4">Kategori<br>Kerusakan</th>
                                <th class="w-40 px-4 py-4">Status<br>Penanganan</th>
                                <th class="w-32 px-4 py-4">Waktu<br>Lapor</th>
                                <th class="w-36 px-4 py-6 text-right">Tindakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($daftar as $laporan)
                                @php
                                    $foto = $laporan->foto->first();
                                    $kabupaten = $laporan->destinasi?->kecamatan?->kabupaten;
                                @endphp
                                <tr class="border-t border-soft align-middle">
                                    <td class="px-4 py-5">
                                        <div class="relative flex h-14 w-20 items-center justify-center overflow-hidden rounded-lg bg-soft-strong text-line">
                                            <x-icon name="image" size="24" />
                                            @if ($foto)
                                                <img src="{{ $foto->url }}" alt="" class="absolute inset-0 size-full object-cover" onerror="this.remove()">
                                            @endif
                                        </div>
                                    </td>
                                    <td class="max-w-80 px-4 py-5">
                                        <p class="font-mono text-[11px] leading-[14px] font-bold tracking-[0.55px] text-primary">{{ $laporan->kode }}</p>
                                        <a href="{{ $link('laporan.show', $laporan) }}" class="line-clamp-3 font-heading text-base leading-[22px] font-semibold text-ink hover:text-primary">{{ $laporan->judul }}</a>
                                        <p class="line-clamp-1 text-[13px] leading-[18px] text-muted">Lokasi: {{ $laporan->lokasi_spesifik }}</p>
                                    </td>
                                    <td class="px-4 py-5">
                                        <p class="flex items-start gap-1 font-heading text-xs leading-4 font-semibold text-primary">
                                            <x-icon name="location_on" size="14" /> {{ $laporan->destinasi?->nama }}
                                        </p>
                                        <p class="text-xs leading-[18px] text-subtle">{{ $kabupaten?->label }}</p>
                                    </td>
                                    <td class="px-4 py-5">
                                        <span class="inline-flex rounded-md bg-soft-strong px-2.5 py-1 font-heading text-[11px] leading-[14px] font-bold text-muted">{{ $laporan->kategoriKerusakan?->nama }}</span>
                                    </td>
                                    <td class="px-4 py-5">
                                        <x-status-badge :status="$laporan->status" size="sm" />
                                    </td>
                                    <td class="px-4 py-5">
                                        <p class="text-[13px] leading-[18px] text-muted">{{ $laporan->created_at->locale('id')->translatedFormat('d M Y') }}</p>
                                        <p class="text-xs leading-[18px] text-subtle">{{ $laporan->created_at->format('H:i') }} WITA</p>
                                    </td>
                                    <td class="px-4 py-5">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <a href="{{ $link('laporan.show', $laporan) }}" title="Lihat detail" aria-label="Lihat detail"
                                               @class([$tombolAksi, 'bg-primary/10 text-primary hover:bg-primary/20' => ! $laporan->bisaDiubah(), 'bg-soft-strong text-ink hover:bg-soft-blue' => $laporan->bisaDiubah()])>
                                                <x-icon :name="$laporan->status === 'selesai' ? 'verified' : 'visibility'" size="18" />
                                            </a>

                                            @if ($laporan->bisaDiubah())
                                                <a href="{{ $link('laporan.edit', $laporan) }}" data-buka-modal-edit title="Edit laporan" aria-label="Edit laporan"
                                                   class="{{ $tombolAksi }} bg-primary/10 text-primary hover:bg-primary/20">
                                                    <x-icon name="edit" size="18" />
                                                </a>
                                                <form method="POST" action="{{ $link('laporan.destroy', $laporan) }}"
                                                      onsubmit="return confirm('Hapus laporan “{{ addslashes($laporan->judul) }}”? Tindakan ini tidak dapat dibatalkan.')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <input type="hidden" name="kembali" value="{{ url()->full() }}#riwayat"> 
                                                    <button type="submit" title="Hapus laporan" aria-label="Hapus laporan" @disabled(! Route::has('laporan.destroy'))
                                                            class="{{ $tombolAksi }} bg-danger-soft text-danger hover:brightness-95 disabled:cursor-not-allowed disabled:opacity-60">
                                                        <x-icon name="delete" size="18" />
                                                    </button>
                                                </form>
                                            @else
                                                @foreach (['Edit', 'Hapus'] as $aksi)
                                                    <span title="{{ $aksi }} tidak tersedia: laporan sudah ditindaklanjuti pengelola" class="{{ $tombolAksi }} cursor-not-allowed bg-soft-strong text-subtle opacity-60">
                                                        <x-icon name="lock" size="16" />
                                                    </span>
                                                @endforeach
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-16 text-center">
                                        <x-icon name="assignment" size="40" class="text-line" />
                                        <p class="mt-2 font-heading text-base font-semibold text-ink">
                                            {{ request()->hasAny(['q', 'status', 'destinasi']) ? 'Tidak ada laporan yang cocok dengan filter.' : 'Anda belum pernah mengirim laporan.' }}
                                        </p>
                                        @unless (request()->hasAny(['q', 'status', 'destinasi']))
                                            <x-button :href="$link('laporan.create')" size="sm" icon-left="add" class="mt-4">Laporkan Kerusakan</x-button>
                                        @endunless
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                @else
                    <table class="w-full min-w-[900px] text-left">
                        <thead class="bg-[#dee8ff] font-heading text-xs leading-4 font-bold tracking-[0.6px] text-ink uppercase">
                            <tr>
                                <th class="px-4 py-6">Nama Lokasi Diajukan</th>
                                <th class="px-4 py-6">Wilayah</th>
                                <th class="px-4 py-6">Kategori Wisata</th>
                                <th class="px-4 py-6">Status</th>
                                <th class="px-4 py-4">Tanggal<br>Pengajuan</th>
                                <th class="px-4 py-6 text-right">Peta</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($daftar as $pengajuan)
                                <tr class="border-t border-soft align-middle">
                                    <td class="px-4 py-5">
                                        <p class="font-heading text-base leading-[22px] font-semibold text-ink">{{ $pengajuan->nama_lokasi }}</p>
                                        @if ($pengajuan->diverifikasi_pada)
                                            <p class="text-xs leading-[18px] text-subtle">Diverifikasi {{ $pengajuan->diverifikasi_pada->locale('id')->translatedFormat('d M Y') }}</p>
                                        @endif
                                    </td>
                                    <td class="px-4 py-5">
                                        <p class="font-heading text-xs leading-4 font-semibold text-primary">{{ $pengajuan->kecamatan?->nama }}</p>
                                        <p class="text-xs leading-[18px] text-subtle">{{ $pengajuan->kecamatan?->kabupaten?->label }}</p>
                                    </td>
                                    <td class="px-4 py-5">
                                        <span class="inline-flex rounded-md bg-soft-strong px-2.5 py-1 font-heading text-[11px] leading-[14px] font-bold text-muted">{{ $pengajuan->kategoriWisata?->nama }}</span>
                                    </td>
                                    <td class="px-4 py-5"><x-status-badge :status="$pengajuan->status" size="sm" /></td>
                                    <td class="px-4 py-5">
                                        <p class="text-[13px] leading-[18px] text-muted">{{ $pengajuan->created_at->locale('id')->translatedFormat('d M Y') }}</p>
                                        <p class="text-xs leading-[18px] text-subtle">{{ $pengajuan->created_at->format('H:i') }} WITA</p>
                                    </td>
                                    <td class="px-4 py-5">
                                        <div class="flex justify-end">
                                            <a href="{{ $pengajuan->link_maps }}" target="_blank" rel="noopener" title="Buka Google Maps" aria-label="Buka Google Maps"
                                               class="{{ $tombolAksi }} bg-primary/10 text-primary hover:bg-primary/20">
                                                <x-icon name="map" size="18" />
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-4 py-16 text-center">
                                        <x-icon name="explore" size="40" class="text-line" />
                                        <p class="mt-2 font-heading text-base font-semibold text-ink">
                                            {{ request()->hasAny(['q', 'status']) ? 'Tidak ada pengajuan yang cocok dengan filter.' : 'Anda belum pernah mengajukan destinasi baru.' }}
                                        </p>
                                        @unless (request()->hasAny(['q', 'status']))
                                            <x-button :href="$link('pengajuan.create')" size="sm" icon-left="add_location_alt" class="mt-4">Ajukan Destinasi</x-button>
                                        @endunless
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                @endif
            </div>

            <x-pagination :paginator="$daftar" :label="$tab === 'laporan' ? 'laporan terdaftar' : 'pengajuan destinasi'" />
        </div>
    </div>

    {{-- ============ MODAL EDIT LAPORAN (hanya dirender saat simpan edit gagal validasi) ============ --}}
    @if ($laporanEdit)
        @include('wisatawan.laporan.partials.modal-edit', ['laporan' => $laporanEdit, 'kembaliKe' => url()->full().'#riwayat'])
    @endif

    {{-- ============ DIALOG GANTI PASSWORD ============ --}}
    <dialog id="dialog-password" class="m-auto w-[calc(100%-2rem)] max-w-md rounded-xl bg-white p-0 shadow-xl backdrop:bg-ink/40">
        <form method="POST" action="{{ route('wisatawan.password.update') }}" class="flex flex-col gap-4 p-6">
            @csrf
            @method('PUT')

            <div class="flex items-start justify-between gap-4">
                <div>
                    <h2 class="font-heading text-xl leading-7 font-bold text-ink">Ganti Password</h2>
                    <p class="text-[13px] leading-[18px] text-muted">Masukkan password lama, lalu buat password baru.</p>
                </div>
                <button type="button" onclick="this.closest('dialog').close()" aria-label="Tutup" class="rounded-lg p-1 text-muted hover:bg-soft">
                    <x-icon name="close" size="20" />
                </button>
            </div>

            <x-form.input name="password_lama" type="password" label="Password Lama" icon="lock" required autocomplete="current-password" />
            <x-form.input name="password_baru" type="password" label="Password Baru" icon="lock_reset" required autocomplete="new-password"
                          hint="Minimal 8 karakter dan berbeda dari password lama." hint-icon="shield" />
            <x-form.input name="password_baru_confirmation" type="password" label="Konfirmasi Password Baru" icon="lock_clock" required autocomplete="new-password"
                          data-match="password_baru" hint="Ulangi password baru." hint-icon="sync_alt" />

            <div class="flex justify-end gap-2 pt-2">
                <x-button variant="ghost" onclick="this.closest('dialog').close()">Batal</x-button>
                <x-button type="submit" icon="check">Simpan Password</x-button>
            </div>
        </form>
    </dialog>

    @if ($errors->hasAny(['password_lama', 'password_baru']))
        @push('scripts')
            <script>document.getElementById('dialog-password').showModal();</script>
        @endpush
    @endif
</x-layouts.public>