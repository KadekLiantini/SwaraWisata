@php
    $crumbTataKelola = new class implements \Illuminate\Contracts\Support\Htmlable {
        public function toHtml()
        {
            return '<a href="'.e(route('admin.dashboard')).'" class="transition hover:text-primary">Tata Kelola</a>';
        }
    };

    $search = $filter['search'] ?? '';
    $idKabupaten = (int) ($filter['id_kabupaten'] ?? 0);
    $status = $filter['status'] ?? 'semua';

    $adaFilter = $search !== '' || $idKabupaten > 0 || $status !== 'semua';

    $bukaModalTambah = (bool) ($bukaTambah ?? false) || (bool) session('buka_tambah_pengelola');
    $editErrorId = (int) session('buka_edit_pengelola_id', 0);

    $warnaAvatar = [
        'bg-emerald-100 text-emerald-700',
        'bg-teal-100 text-teal-700',
        'bg-sky-100 text-sky-700',
        'bg-indigo-100 text-indigo-700',
        'bg-amber-100 text-amber-800',
    ];
@endphp

<x-layouts.admin
    title="Akun Pengelola"
    :breadcrumbs="[$crumbTataKelola, 'Akun Pengelola']"
>
    <div class="space-y-5">
        {{-- Header halaman --}}
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="font-heading text-xl font-bold tracking-tight text-ink sm:text-2xl">
                    Kelola Akun Pengelola Destinasi
                </h1>
                <p class="mt-1 text-xs text-muted">
                    Terbitkan, perbarui, dan kelola kredensial akses pengelola resmi untuk setiap destinasi wisata terdaftar.
                </p>
            </div>

            <button
                type="button"
                id="tombol-tambah-pengelola"
                class="inline-flex shrink-0 items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2.5 text-xs font-semibold text-white shadow-sm transition hover:bg-primary-hover"
            >
                <x-icon name="person_add" size="17" />
                <span>Tambah Akun Pengelola</span>
            </button>
        </div>

        {{-- Panel pencarian dan filter --}}
        <section class="rounded-xl border border-line bg-white p-4">
            <form
                id="form-filter-pengelola"
                method="GET"
                action="{{ route('admin.pengelola.index') }}"
                autocomplete="off"
                class="grid grid-cols-1 gap-3 md:grid-cols-[minmax(0,1.5fr)_minmax(0,0.85fr)_minmax(0,0.85fr)_auto]"
            >
                <div class="relative min-w-0">
                    <label for="search" class="sr-only">Cari username atau nama kawasan wisata</label>
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3.5 text-muted">
                        <x-icon name="search" size="17" />
                    </span>
                    <input
                        id="search"
                        name="search"
                        type="search"
                        value="{{ $search }}"
                        maxlength="100"
                        placeholder="Cari username atau nama kawasan wisata..."
                        class="w-full rounded-lg border border-line bg-white px-3.5 py-2.5 pl-10 text-xs text-ink outline-none transition placeholder:text-muted focus:border-primary focus:ring-2 focus:ring-primary/10"
                    >
                </div>

                <div>
                    <label for="id_kabupaten" class="sr-only">Filter Kabupaten / Kota</label>
                    <select
                        id="id_kabupaten"
                        name="id_kabupaten"
                        class="w-full rounded-lg border border-line bg-white px-3.5 py-2.5 text-xs font-medium text-ink outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/10"
                    >
                        <option value="">Semua Kabupaten / Kota</option>
                        @foreach ($kabupaten as $item)
                            <option value="{{ $item->id_kabupaten }}" @selected($idKabupaten === (int) $item->id_kabupaten)>
                                {{ $item->label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="status" class="sr-only">Filter Status Akun</label>
                    <select
                        id="status"
                        name="status"
                        class="w-full rounded-lg border border-line bg-white px-3.5 py-2.5 text-xs font-medium text-ink outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/10"
                    >
                        <option value="semua" @selected($status === 'semua')>Semua Status Akun</option>
                        <option value="aktif" @selected($status === 'aktif')>Aktif Terverifikasi</option>
                        <option value="nonaktif" @selected($status === 'nonaktif')>Nonaktif / Ditangguhkan</option>
                    </select>
                </div>

                <a
                    href="{{ route('admin.pengelola.index') }}"
                    data-reset-pengelola
                    aria-label="Reset pencarian dan filter"
                    title="Reset pencarian & filter"
                    class="flex h-[38px] w-full items-center justify-center rounded-lg border border-line bg-white text-ink transition hover:border-primary/40 hover:bg-soft hover:text-primary md:w-[38px]"
                >
                    <x-icon name="refresh" size="16" />
                </a>
            </form>
        </section>

        {{-- Tabel akun pengelola --}}
        <section id="tabel-pengelola-wrapper" class="overflow-hidden rounded-xl border border-line bg-white transition-opacity duration-150">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[920px] text-left">
                    <colgroup>
                        <col style="width: 26%">
                        <col style="width: 22%">
                        <col style="width: 14%">
                        <col style="width: 12%">
                        <col style="width: 14%">
                        <col style="width: 12%">
                    </colgroup>

                    <thead class="bg-soft/80">
                        <tr class="text-[10px] font-semibold uppercase tracking-wider text-muted">
                            <th class="px-5 py-3.5">Username</th>
                            <th class="px-3 py-3.5">Destinasi yang<br>Dikelola</th>
                            <th class="px-3 py-3.5">Kabupaten<br>/ Kota</th>
                            <th class="px-3 py-3.5">Tanggal<br>Dibuat</th>
                            <th class="px-3 py-3.5">Status</th>
                            <th class="px-5 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-line">
                        @forelse ($pengelola as $item)
                            @php
                                $destinasi = $item->destinasi;
                                $aktif = $destinasi ? (bool) $destinasi->is_active : false;
                                $inisial = mb_strtoupper(mb_substr(preg_replace('/^pengelola_/', '', $item->username) ?: $item->username, 0, 2));
                                $kelasAvatar = $warnaAvatar[$item->id_akun % count($warnaAvatar)];
                            @endphp
                            <tr class="bg-white transition-colors hover:bg-soft/40">
                                {{-- Kolom 1: Username & keterangan pengelola --}}
                                <td class="px-5 py-4">
                                    <div class="flex min-w-0 items-center gap-3">
                                        <span class="flex size-9 shrink-0 items-center justify-center rounded-full text-xs font-bold {{ $kelasAvatar }}">
                                            {{ $inisial }}
                                        </span>
                                        <div class="min-w-0">
                                            <p class="truncate text-xs font-semibold text-ink">
                                                {{ '@' . $item->username }}
                                            </p>
                                            <p class="mt-0.5 truncate text-[11px] text-muted">
                                                {{ $destinasi ? 'Pengelola ' . $destinasi->nama : 'Belum terhubung destinasi' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Kolom 2: Destinasi yang dikelola --}}
                                <td class="px-3 py-4">
                                    @if ($destinasi)
                                        <div class="flex items-start gap-2">
                                            <span class="mt-0.5 flex size-6 shrink-0 items-center justify-center rounded-md bg-soft-blue text-primary">
                                                <x-icon name="forest" size="14" />
                                            </span>
                                            <div class="min-w-0">
                                                <p class="text-xs font-medium leading-snug text-ink">
                                                    {{ $destinasi->nama }}
                                                </p>
                                                @if ($destinasi->kategoriWisata)
                                                    <p class="mt-0.5 text-[10px] text-muted">
                                                        {{ $destinasi->kategoriWisata->nama }}
                                                    </p>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-xs italic text-muted">-</span>
                                    @endif
                                </td>

                                {{-- Kolom 3: Kabupaten / Kota --}}
                                <td class="px-3 py-4">
                                    <p class="text-xs font-medium text-ink">
                                        {{ $destinasi?->kecamatan?->kabupaten?->label ?? '-' }}
                                    </p>
                                    @if ($destinasi?->kecamatan)
                                        <p class="mt-0.5 text-[10px] text-muted">
                                            Kec. {{ $destinasi->kecamatan->nama }}
                                        </p>
                                    @endif
                                </td>

                                {{-- Kolom 4: Tanggal dibuat --}}
                                <td class="whitespace-nowrap px-3 py-4">
                                    <p class="text-xs text-ink">
                                        {{ $item->created_at?->locale('id')->translatedFormat('d M Y') ?? '-' }}
                                    </p>
                                </td>

                                {{-- Kolom 5: Status --}}
                                <td class="whitespace-nowrap px-3 py-4">
                                    @if ($aktif)
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-mint px-2.5 py-1 text-[11px] font-semibold text-primary">
                                            <span class="size-1.5 rounded-full bg-primary"></span>
                                            <span>Aktif Terverifikasi</span>
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-danger-soft px-2.5 py-1 text-[11px] font-semibold text-danger">
                                            <span class="size-1.5 rounded-full bg-danger"></span>
                                            <span>Nonaktif / Ditangguhkan</span>
                                        </span>
                                    @endif
                                </td>

                                {{-- Kolom 6: Aksi Edit & Hapus --}}
                                <td class="whitespace-nowrap px-5 py-4">
                                    <div class="flex items-center justify-end gap-5">
                                        <button
                                            type="button"
                                            data-buka-edit
                                            data-id="{{ $item->id_akun }}"
                                            data-action="{{ route('admin.pengelola.update', ['pengelola' => $item->id_akun]) }}"
                                            data-username="{{ $item->username }}"
                                            data-destinasi-id="{{ $item->id_destinasi }}"
                                            class="inline-flex items-center px-1 py-1 text-sm font-semibold text-primary transition hover:text-primary-hover hover:underline"
                                        >
                                            Edit
                                        </button>

                                        <button
                                            type="button"
                                            data-buka-hapus
                                            data-action="{{ route('admin.pengelola.destroy', ['pengelola' => $item->id_akun]) }}"
                                            data-username="{{ '@' . $item->username }}"
                                            data-destinasi="{{ $destinasi?->nama ?? 'destinasi terkait' }}"
                                            class="inline-flex items-center justify-center rounded-lg border border-danger px-4 py-2 text-sm font-semibold text-danger transition hover:bg-danger-soft"
                                        >
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-14 text-center">
                                    <div class="mx-auto flex max-w-sm flex-col items-center gap-2 text-muted">
                                        <span class="flex size-12 items-center justify-center rounded-full bg-soft text-primary">
                                            <x-icon name="manage_accounts" size="24" />
                                        </span>
                                        <p class="font-heading text-sm font-bold text-ink">Tidak Ada Akun Pengelola</p>
                                        <p class="text-xs leading-relaxed text-muted">
                                            @if ($adaFilter)
                                                Tidak ada akun pengelola yang sesuai dengan pencarian atau filter yang dipilih.
                                            @else
                                                Belum ada akun pengelola terdaftar. Klik tombol "Tambah Akun Pengelola" untuk menerbitkan akun baru.
                                            @endif
                                        </p>
                                        @if ($adaFilter)
                                            <a href="{{ route('admin.pengelola.index') }}" data-reset-pengelola class="mt-1 inline-flex items-center gap-1 text-xs font-semibold text-primary hover:underline">
                                                <x-icon name="refresh" size="14" />
                                                Reset pencarian &amp; filter
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Footer & pagination --}}
            <div class="flex flex-col gap-3 border-t border-line bg-soft/60 px-5 py-3.5 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-xs text-muted">
                    Menampilkan
                    <span class="font-semibold text-ink">{{ $pengelola->firstItem() ?? 0 }}&ndash;{{ $pengelola->lastItem() ?? 0 }}</span>
                    dari
                    <span class="font-semibold text-ink">{{ $pengelola->total() }}</span>
                    akun pengelola terdaftar
                </p>

                @if ($pengelola->hasPages())
                    <nav aria-label="Navigasi halaman pengelola" class="flex flex-wrap items-center justify-end gap-1.5">
                        @if ($pengelola->onFirstPage())
                            <span aria-disabled="true" class="inline-flex h-8 items-center rounded-md border border-line bg-white/70 px-3 text-xs font-medium text-subtle">
                                Sebelumnya
                            </span>
                        @else
                            <a href="{{ $pengelola->previousPageUrl() }}" class="inline-flex h-8 items-center rounded-md border border-line bg-white px-3 text-xs font-medium text-ink transition hover:bg-soft">
                                Sebelumnya
                            </a>
                        @endif

                        @foreach ($pengelola->getUrlRange(max(1, $pengelola->currentPage() - 2), min($pengelola->lastPage(), $pengelola->currentPage() + 2)) as $halaman => $url)
                            @if ($halaman === $pengelola->currentPage())
                                <span aria-current="page" class="inline-flex size-8 items-center justify-center rounded-md bg-primary text-xs font-semibold text-white">
                                    {{ $halaman }}
                                </span>
                            @else
                                <a href="{{ $url }}" class="inline-flex size-8 items-center justify-center rounded-md border border-line bg-white text-xs font-medium text-ink transition hover:bg-soft">
                                    {{ $halaman }}
                                </a>
                            @endif
                        @endforeach

                        @if ($pengelola->hasMorePages())
                            <a href="{{ $pengelola->nextPageUrl() }}" class="inline-flex h-8 items-center rounded-md border border-line bg-white px-3 text-xs font-medium text-ink transition hover:bg-soft">
                                Berikutnya
                            </a>
                        @else
                            <span aria-disabled="true" class="inline-flex h-8 items-center rounded-md border border-line bg-white/70 px-3 text-xs font-medium text-subtle">
                                Berikutnya
                            </span>
                        @endif
                    </nav>
                @endif
            </div>
        </section>
    </div>

    <style>
        dialog.modal-swara::backdrop {
            background-color: rgba(17, 28, 45, 0.55);
            backdrop-filter: blur(2px);
        }
    </style>

    {{-- MODAL 1: BUAT AKUN PENGELOLA BARU --}}
    <dialog
        id="modal-tambah-pengelola"
        class="modal-swara m-auto max-h-[92vh] w-[calc(100%-24px)] max-w-[540px] overflow-hidden rounded-2xl border border-line bg-white p-0 text-ink shadow-2xl open:flex open:flex-col"
        aria-labelledby="judul-modal-tambah"
        aria-modal="true"
    >
        <div class="flex shrink-0 items-start justify-between gap-3 border-b border-line bg-white px-6 py-4">
            <div class="flex min-w-0 items-center gap-3">
                <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-primary text-white shadow-sm">
                    <x-icon name="badge" size="20" />
                </span>
                <div class="min-w-0">
                    <h2 id="judul-modal-tambah" class="font-heading text-base font-bold text-ink">
                        Buat Akun Pengelola Baru
                    </h2>
                    <p class="text-[11px] text-muted">
                        Penerbitan Kredensial Lapangan Terverifikasi
                    </p>
                </div>
            </div>

            <button
                type="button"
                data-tutup-dialog="modal-tambah-pengelola"
                aria-label="Tutup modal"
                class="flex size-8 shrink-0 items-center justify-center rounded-lg text-muted transition hover:bg-soft hover:text-ink"
            >
                <x-icon name="close" size="18" />
            </button>
        </div>

        <form
            id="form-tambah-pengelola"
            method="POST"
            action="{{ route('admin.pengelola.store') }}"
            class="flex min-h-0 flex-1 flex-col"
        >
            @csrf

            <div class="min-h-0 flex-1 space-y-4 overflow-y-auto px-6 py-5">
                @if ($destinasiTersedia->isEmpty())
                    <div class="rounded-xl border border-amber-200 bg-amber-50/80 p-3.5 text-xs text-amber-900">
                        Seluruh destinasi wisata terdaftar saat ini sudah memiliki akun pengelola terikat. Tambahkan destinasi baru terlebih dahulu jika ingin menerbitkan akun baru.
                    </div>
                @endif

                {{-- Pilih Destinasi Wisata Terikat --}}
                <div>
                    <label for="tambah_id_destinasi" class="mb-1.5 block text-[11px] font-semibold text-ink">
                        Pilih Destinasi Wisata Terikat <span class="text-danger">*</span>
                    </label>
                    <select
                        id="tambah_id_destinasi"
                        name="id_destinasi"
                        required
                        class="w-full rounded-lg border bg-soft px-3.5 py-2.5 text-xs font-medium text-ink outline-none transition focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/10 @if($bukaModalTambah && $errors->has('id_destinasi')) border-danger @else border-transparent @endif"
                    >
                        <option value="">-- Pilih destinasi wisata yang belum memiliki akun --</option>
                        @foreach ($destinasiTersedia as $dest)
                            @php
                                $kec = $dest->kecamatan?->nama;
                                $kab = $dest->kecamatan?->kabupaten?->label;
                                $wilayahText = trim(implode(', ', array_filter([$kec, $kab])));
                            @endphp
                            <option
                                value="{{ $dest->id_destinasi }}"
                                data-nama="{{ $dest->nama }}"
                                data-kabupaten="{{ $kab ?? '' }}"
                                @selected((string) old('id_destinasi') === (string) $dest->id_destinasi)
                            >
                                {{ $dest->nama }}{{ $wilayahText ? ' - ' . $wilayahText : '' }}
                            </option>
                        @endforeach
                    </select>
                    @if ($bukaModalTambah && $errors->has('id_destinasi'))
                        <p class="mt-1 text-[11px] text-danger">{{ $errors->first('id_destinasi') }}</p>
                    @endif
                </div>

                {{-- Username Pengelola --}}
                <div>
                    <label for="tambah_username" class="mb-1.5 block text-[11px] font-semibold text-ink">
                        Username Pengelola <span class="text-danger">*</span>
                    </label>
                    <div class="relative flex items-center rounded-lg border bg-soft transition focus-within:border-primary focus-within:bg-white focus-within:ring-2 focus-within:ring-primary/10 @if($bukaModalTambah && $errors->has('username')) border-danger @else border-transparent @endif">
                        <span class="pl-3.5 text-xs font-semibold text-muted">@</span>
                        <input
                            id="tambah_username"
                            name="username"
                            type="text"
                            value="{{ $bukaModalTambah ? old('username', '') : '' }}"
                            maxlength="50"
                            required
                            placeholder="pengelola_namaobjek"
                            class="w-full bg-transparent px-1.5 py-2.5 pr-9 text-xs font-medium text-ink outline-none placeholder:text-subtle"
                        >
                        <span id="tambah_username_valid" class="pointer-events-none absolute right-3 hidden text-primary">
                            <x-icon name="check_circle" size="16" />
                        </span>
                    </div>
                    <p id="tambah_username_hint" class="mt-1.5 hidden items-center gap-1.5 text-[10px] text-primary">
                        <span class="size-1.5 shrink-0 rounded-full bg-primary"></span>
                        <span>Username unik valid dan memenuhi format standar otoritas (@pengelola_[nama_objek]).</span>
                    </p>
                    @if ($bukaModalTambah && $errors->has('username'))
                        <p class="mt-1 text-[11px] text-danger">{{ $errors->first('username') }}</p>
                    @endif
                </div>

                {{-- Kata Sandi Kredensial --}}
                <div>
                    <div class="mb-1.5 flex items-center justify-between gap-2">
                        <label for="tambah_password" class="block text-[11px] font-semibold text-ink">
                            Kata Sandi Kredensial <span class="text-danger">*</span>
                        </label>
                        <button
                            type="button"
                            id="tombol-generate-tambah"
                            class="inline-flex items-center gap-1 text-[11px] font-semibold text-primary transition hover:underline"
                        >
                            <x-icon name="autorenew" size="14" />
                            <span>Buat Otomatis (Generate)</span>
                        </button>
                    </div>

                    <div class="relative flex items-center rounded-lg border bg-soft transition focus-within:border-primary focus-within:bg-white focus-within:ring-2 focus-within:ring-primary/10 @if($bukaModalTambah && $errors->has('password')) border-danger @else border-transparent @endif">
                        <input
                            id="tambah_password"
                            name="password"
                            type="text"
                            value="{{ $bukaModalTambah ? old('password', '') : '' }}"
                            minlength="8"
                            maxlength="100"
                            required
                            placeholder="Minimal 8 karakter"
                            class="w-full bg-transparent px-3.5 py-2.5 pr-10 font-mono text-xs font-medium text-ink outline-none placeholder:font-sans placeholder:text-subtle"
                        >
                        <button
                            type="button"
                            data-toggle-sandi="tambah_password"
                            aria-label="Tampilkan atau sembunyikan kata sandi"
                            class="absolute right-3 flex items-center justify-center text-muted transition hover:text-ink"
                        >
                            <x-icon name="visibility" size="16" />
                        </button>
                    </div>
                    @if ($bukaModalTambah && $errors->has('password'))
                        <p class="mt-1 text-[11px] text-danger">{{ $errors->first('password') }}</p>
                    @endif
                </div>

                {{-- Kotak Kredensial Siap Disalin --}}
                <div class="rounded-xl border border-primary/20 bg-mint/30 p-3.5">
                    <div class="mb-2 flex items-center justify-between gap-2">
                        <div class="flex items-center gap-1.5 text-xs font-semibold text-primary">
                            <x-icon name="vpn_key" size="15" />
                            <span>Kredensial Siap Disalin</span>
                        </div>

                        <button
                            type="button"
                            id="tombol-salin-tambah"
                            class="inline-flex items-center gap-1.5 rounded-md bg-primary px-2.5 py-1 text-[11px] font-semibold text-white transition hover:bg-primary-hover"
                        >
                            <x-icon name="content_copy" size="13" />
                            <span id="label-salin-tambah">Salin Kredensial</span>
                        </button>
                    </div>

                    <pre id="preview-kredensial-tambah" class="overflow-x-auto rounded-lg border border-line bg-white p-3 font-mono text-[11px] leading-relaxed text-ink">Username: -
Password: -
Destinasi: -</pre>
                </div>
            </div>

            <div class="flex shrink-0 items-center justify-end gap-2.5 border-t border-line bg-white px-6 py-4">
                <button
                    type="button"
                    data-tutup-dialog="modal-tambah-pengelola"
                    class="inline-flex items-center justify-center rounded-lg border border-line bg-soft px-4 py-2 text-xs font-semibold text-ink transition hover:bg-line/50"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    id="tombol-submit-tambah"
                    @disabled($destinasiTersedia->isEmpty())
                    class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-primary px-4 py-2 text-xs font-semibold text-white transition hover:bg-primary-hover disabled:cursor-not-allowed disabled:opacity-50"
                >
                    <x-icon name="person_add" size="15" />
                    <span>Simpan &amp; Terbitkan Akun</span>
                </button>
            </div>
        </form>
    </dialog>

    {{-- MODAL 2: EDIT AKUN PENGELOLA --}}
    <dialog
        id="modal-edit-pengelola"
        class="modal-swara m-auto max-h-[92vh] w-[calc(100%-24px)] max-w-[540px] overflow-hidden rounded-2xl border border-line bg-white p-0 text-ink shadow-2xl open:flex open:flex-col"
        aria-labelledby="judul-modal-edit"
        aria-modal="true"
    >
        <div class="flex shrink-0 items-start justify-between gap-3 border-b border-line bg-white px-6 py-4">
            <div class="flex min-w-0 items-center gap-3">
                <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-soft-blue text-primary">
                    <x-icon name="edit_square" size="20" />
                </span>
                <div class="min-w-0">
                    <h2 id="judul-modal-edit" class="font-heading text-base font-bold text-ink">
                        Edit Akun Pengelola
                    </h2>
                    <p class="text-[11px] text-muted">
                        Perbarui destinasi terikat, username, atau atur ulang kata sandi pengelola.
                    </p>
                </div>
            </div>

            <button
                type="button"
                data-tutup-dialog="modal-edit-pengelola"
                aria-label="Tutup modal edit"
                class="flex size-8 shrink-0 items-center justify-center rounded-lg text-muted transition hover:bg-soft hover:text-ink"
            >
                <x-icon name="close" size="18" />
            </button>
        </div>

        <form
            id="form-edit-pengelola"
            method="POST"
            action="{{ $editErrorId > 0 ? route('admin.pengelola.update', ['pengelola' => $editErrorId]) : '#' }}"
            class="flex min-h-0 flex-1 flex-col"
        >
            @csrf
            @method('PUT')

            <div class="min-h-0 flex-1 space-y-4 overflow-y-auto px-6 py-5">
                @if ($editErrorId > 0 && $errors->any())
                    <div class="rounded-xl border border-danger/30 bg-danger-soft px-3.5 py-2.5 text-xs text-danger">
                        {{ $errors->first() }}
                    </div>
                @endif
                <div>
                    <label for="edit_id_destinasi" class="mb-1.5 block text-[11px] font-semibold text-ink">
                        Destinasi Wisata Terikat <span class="text-danger">*</span>
                    </label>
                    <select
                        id="edit_id_destinasi"
                        name="id_destinasi"
                        required
                        class="w-full rounded-lg border bg-soft px-3.5 py-2.5 text-xs font-medium text-ink outline-none transition focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/10 @if($editErrorId > 0 && $errors->has('id_destinasi')) border-danger @else border-transparent @endif"
                    >
                        <option value="">-- Pilih destinasi wisata --</option>
                        @foreach ($semuaDestinasi as $dest)
                            @php
                                $kec = $dest->kecamatan?->nama;
                                $kab = $dest->kecamatan?->kabupaten?->label;
                                $wilayahText = trim(implode(', ', array_filter([$kec, $kab])));
                                $pemilikId = $dest->pengelola?->id_akun;
                            @endphp
                            <option
                                value="{{ $dest->id_destinasi }}"
                                data-pemilik-id="{{ $pemilikId ?? '' }}"
                                data-nama="{{ $dest->nama }}"
                                data-kabupaten="{{ $kab ?? '' }}"
                                @selected($editErrorId > 0 && (string) old('id_destinasi') === (string) $dest->id_destinasi)
                            >
                                {{ $dest->nama }}{{ $wilayahText ? ' - ' . $wilayahText : '' }}
                            </option>
                        @endforeach
                    </select>
                    @if ($editErrorId > 0 && $errors->has('id_destinasi'))
                        <p class="mt-1 text-[11px] text-danger">{{ $errors->first('id_destinasi') }}</p>
                    @endif
                </div>

                <div>
                    <label for="edit_username" class="mb-1.5 block text-[11px] font-semibold text-ink">
                        Username Pengelola <span class="text-danger">*</span>
                    </label>
                    <div class="relative flex items-center rounded-lg border bg-soft transition focus-within:border-primary focus-within:bg-white focus-within:ring-2 focus-within:ring-primary/10 @if($editErrorId > 0 && $errors->has('username')) border-danger @else border-transparent @endif">
                        <span class="pl-3.5 text-xs font-semibold text-muted">@</span>
                        <input
                            id="edit_username"
                            name="username"
                            type="text"
                            value="{{ $editErrorId > 0 ? old('username', '') : '' }}"
                            maxlength="50"
                            required
                            class="w-full bg-transparent px-1.5 py-2.5 text-xs font-medium text-ink outline-none"
                        >
                    </div>
                    @if ($editErrorId > 0 && $errors->has('username'))
                        <p class="mt-1 text-[11px] text-danger">{{ $errors->first('username') }}</p>
                    @endif
                </div>

                <div>
                    <div class="mb-1.5 flex items-center justify-between gap-2">
                        <label for="edit_password" class="block text-[11px] font-semibold text-ink">
                            Kata Sandi Baru <span class="font-normal text-muted">(Kosongkan jika tidak diubah)</span>
                        </label>
                        <button
                            type="button"
                            id="tombol-generate-edit"
                            class="inline-flex items-center gap-1 text-[11px] font-semibold text-primary transition hover:underline"
                        >
                            <x-icon name="autorenew" size="14" />
                            <span>Buat Otomatis (Generate)</span>
                        </button>
                    </div>

                    <div class="relative flex items-center rounded-lg border bg-soft transition focus-within:border-primary focus-within:bg-white focus-within:ring-2 focus-within:ring-primary/10 @if($editErrorId > 0 && $errors->has('password')) border-danger @else border-transparent @endif">
                        <input
                            id="edit_password"
                            name="password"
                            type="text"
                            value=""
                            minlength="8"
                            maxlength="100"
                            placeholder="Biarkan kosong untuk mempertahankan sandi lama"
                            class="w-full bg-transparent px-3.5 py-2.5 pr-10 font-mono text-xs font-medium text-ink outline-none placeholder:font-sans placeholder:text-subtle"
                        >
                        <button
                            type="button"
                            data-toggle-sandi="edit_password"
                            aria-label="Tampilkan atau sembunyikan kata sandi"
                            class="absolute right-3 flex items-center justify-center text-muted transition hover:text-ink"
                        >
                            <x-icon name="visibility" size="16" />
                        </button>
                    </div>
                    @if ($editErrorId > 0 && $errors->has('password'))
                        <p class="mt-1 text-[11px] text-danger">{{ $errors->first('password') }}</p>
                    @endif
                </div>

                {{-- Kotak Salin Kredensial saat reset password --}}
                <div id="box-kredensial-edit" class="hidden rounded-xl border border-primary/20 bg-mint/30 p-3.5">
                    <div class="mb-2 flex items-center justify-between gap-2">
                        <div class="flex items-center gap-1.5 text-xs font-semibold text-primary">
                            <x-icon name="vpn_key" size="15" />
                            <span>Kredensial Baru Siap Disalin</span>
                        </div>

                        <button
                            type="button"
                            id="tombol-salin-edit"
                            class="inline-flex items-center gap-1.5 rounded-md bg-primary px-2.5 py-1 text-[11px] font-semibold text-white transition hover:bg-primary-hover"
                        >
                            <x-icon name="content_copy" size="13" />
                            <span id="label-salin-edit">Salin Kredensial</span>
                        </button>
                    </div>

                    <pre id="preview-kredensial-edit" class="overflow-x-auto rounded-lg border border-line bg-white p-3 font-mono text-[11px] leading-relaxed text-ink"></pre>
                </div>
            </div>

            <div class="flex shrink-0 items-center justify-end gap-2.5 border-t border-line bg-white px-6 py-4">
                <button
                    type="button"
                    data-tutup-dialog="modal-edit-pengelola"
                    class="inline-flex items-center justify-center rounded-lg border border-line bg-soft px-4 py-2 text-xs font-semibold text-ink transition hover:bg-line/50"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-primary px-4 py-2 text-xs font-semibold text-white transition hover:bg-primary-hover"
                >
                    <x-icon name="check" size="15" />
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </dialog>

    {{-- MODAL 3: KONFIRMASI HAPUS AKUN PENGELOLA --}}
    <dialog
        id="modal-hapus-pengelola"
        class="modal-swara m-auto w-[calc(100%-24px)] max-w-[430px] overflow-hidden rounded-2xl border border-line bg-white p-0 text-ink shadow-2xl"
        aria-labelledby="judul-modal-hapus"
        aria-modal="true"
    >
        <div class="p-6">
            <div class="flex items-start gap-3.5">
                <span class="flex size-10 shrink-0 items-center justify-center rounded-xl bg-danger-soft text-danger">
                    <x-icon name="delete_forever" size="20" />
                </span>
                <div class="min-w-0">
                    <h2 id="judul-modal-hapus" class="font-heading text-base font-bold text-ink">
                        Hapus Akun Pengelola?
                    </h2>
                    <p class="mt-1.5 text-xs leading-relaxed text-muted">
                        Apakah Anda yakin ingin menghapus akun <span id="hapus-username" class="font-semibold text-ink">-</span>
                        untuk destinasi <span id="hapus-destinasi" class="font-semibold text-ink">-</span>?
                        Destinasi tersebut akan kembali tersedia untuk diterbitkan akun pengelola baru.
                    </p>
                </div>
            </div>

            <form id="form-hapus-pengelola" method="POST" action="#" class="mt-5 flex items-center justify-end gap-2">
                @csrf
                @method('DELETE')

                <button
                    type="button"
                    data-tutup-dialog="modal-hapus-pengelola"
                    class="inline-flex items-center justify-center rounded-lg border border-line bg-soft px-4 py-2 text-xs font-semibold text-ink transition hover:bg-line/50"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    class="inline-flex items-center justify-center gap-1.5 rounded-lg bg-danger px-4 py-2 text-xs font-semibold text-white transition hover:bg-danger/90"
                >
                    <x-icon name="delete" size="15" />
                    <span>Ya, Hapus Akun</span>
                </button>
            </form>
        </div>
    </dialog>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // --- LIVE SEARCH, FILTER & PAGINATION ---
            const formFilter = document.getElementById('form-filter-pengelola');
            const inputSearch = document.getElementById('search');
            const selectFilterKabupaten = document.getElementById('id_kabupaten');
            const selectFilterStatus = document.getElementById('status');
            const wadahTabel = document.getElementById('tabel-pengelola-wrapper');

            let timerCari = null;
            let controllerFetch = null;

            const bangunUrlFilter = () => {
                if (!formFilter) return window.location.href;
                const url = new URL(formFilter.action, window.location.origin);
                const formData = new FormData(formFilter);

                for (const [kunci, nilai] of formData.entries()) {
                    const teks = String(nilai).trim();
                    if (teks === '' || (kunci === 'status' && teks === 'semua')) {
                        continue;
                    }
                    url.searchParams.set(kunci, teks);
                }

                return url.toString();
            };

            const muatDaftarPengelola = async (targetUrl, modeHistory = 'push') => {
                if (!wadahTabel) {
                    window.location.href = targetUrl;
                    return;
                }

                if (controllerFetch) controllerFetch.abort();
                controllerFetch = new AbortController();
                wadahTabel.classList.add('opacity-60');

                try {
                    const respon = await fetch(targetUrl, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                        signal: controllerFetch.signal,
                    });

                    if (!respon.ok) throw new Error('Gagal memuat daftar pengelola.');

                    const html = await respon.text();
                    const doc = new DOMParser().parseFromString(html, 'text/html');
                    const tabelBaru = doc.getElementById('tabel-pengelola-wrapper');

                    if (!tabelBaru) throw new Error('Tabel baru tidak ditemukan.');

                    wadahTabel.innerHTML = tabelBaru.innerHTML;

                    if (modeHistory === 'replace') {
                        window.history.replaceState({}, '', targetUrl);
                    } else if (modeHistory === 'push') {
                        window.history.pushState({}, '', targetUrl);
                    }
                } catch (err) {
                    if (err.name !== 'AbortError') {
                        window.location.href = targetUrl;
                    }
                } finally {
                    wadahTabel.classList.remove('opacity-60');
                }
            };

            if (formFilter) {
                formFilter.addEventListener('submit', (e) => {
                    e.preventDefault();
                    clearTimeout(timerCari);
                    muatDaftarPengelola(bangunUrlFilter(), 'replace');
                });
            }

            if (inputSearch) {
                inputSearch.addEventListener('input', () => {
                    clearTimeout(timerCari);
                    timerCari = setTimeout(() => {
                        muatDaftarPengelola(bangunUrlFilter(), 'replace');
                    }, 350);
                });
            }

            [selectFilterKabupaten, selectFilterStatus].forEach((el) => {
                if (!el) return;
                el.addEventListener('change', () => {
                    clearTimeout(timerCari);
                    muatDaftarPengelola(bangunUrlFilter(), 'push');
                });
            });

            window.addEventListener('popstate', () => {
                const params = new URLSearchParams(window.location.search);
                if (inputSearch) inputSearch.value = params.get('search') || '';
                if (selectFilterKabupaten) selectFilterKabupaten.value = params.get('id_kabupaten') || '';
                if (selectFilterStatus) selectFilterStatus.value = params.get('status') || 'semua';
                muatDaftarPengelola(window.location.href, 'none');
            });

            // --- HELPER DIALOG MODAL ---
            const semuaDialog = ['modal-tambah-pengelola', 'modal-edit-pengelola', 'modal-hapus-pengelola']
                .map((id) => document.getElementById(id))
                .filter(Boolean);

            const perbaruiScrollBody = () => {
                const adaTerbuka = semuaDialog.some((dlg) => dlg.open || dlg.hasAttribute('open'));
                document.documentElement.classList.toggle('overflow-hidden', adaTerbuka);
                document.body.classList.toggle('overflow-hidden', adaTerbuka);
            };

            const bukaDialog = (dialog) => {
                if (!dialog) return;
                if (typeof dialog.showModal === 'function') {
                    if (!dialog.open) dialog.showModal();
                } else {
                    dialog.setAttribute('open', '');
                }
                perbaruiScrollBody();
            };

            const tutupDialog = (dialog) => {
                if (!dialog) return;
                if (typeof dialog.close === 'function') {
                    if (dialog.open) dialog.close();
                } else {
                    dialog.removeAttribute('open');
                }
                perbaruiScrollBody();
            };

            semuaDialog.forEach((dlg) => {
                dlg.addEventListener('click', (e) => {
                    if (e.target === dlg) tutupDialog(dlg);
                });
                dlg.addEventListener('close', perbaruiScrollBody);
            });

            // --- GENERATOR USERNAME, PASSWORD & SALIN KREDENSIAL ---
            const buatSlugUsername = (namaDestinasi) => {
                const bersih = String(namaDestinasi || '')
                    .toLowerCase()
                    .replace(/[^a-z0-9\s]/g, '')
                    .trim()
                    .replace(/\s+/g, '');
                if (!bersih) return '';
                return ('pengelola_' + bersih).slice(0, 45);
            };

            const buatSandiAcak = (namaDestinasi = '') => {
                const kata = String(namaDestinasi || '')
                    .replace(/[^a-zA-Z0-9\s]/g, '')
                    .trim()
                    .split(/\s+/)
                    .slice(0, 2)
                    .map((w) => w.charAt(0).toUpperCase() + w.slice(1).toLowerCase())
                    .join('');
                const acak = Math.floor(1000 + Math.random() * 9000);
                const simbol = ['!', '#', '@'][Math.floor(Math.random() * 3)];
                return (kata ? 'SW-' + kata + '#' + acak + simbol : 'SW-Bali#' + acak + simbol);
            };

            const salinTeks = async (teks, labelEl) => {
                try {
                    await navigator.clipboard.writeText(teks);
                } catch (e) {
                    const tmp = document.createElement('textarea');
                    tmp.value = teks;
                    document.body.appendChild(tmp);
                    tmp.select();
                    document.execCommand('copy');
                    tmp.remove();
                }
                if (labelEl) {
                    const asli = labelEl.textContent;
                    labelEl.textContent = 'Tersalin!';
                    setTimeout(() => { labelEl.textContent = asli; }, 1800);
                }
            };

            // Modal Tambah Pengelola
            const modalTambah = document.getElementById('modal-tambah-pengelola');
            const selectTambahDest = document.getElementById('tambah_id_destinasi');
            const inputTambahUser = document.getElementById('tambah_username');
            const ikonTambahUserValid = document.getElementById('tambah_username_valid');
            const inputTambahPass = document.getElementById('tambah_password');
            const previewTambah = document.getElementById('preview-kredensial-tambah');
            const tombolGenTambah = document.getElementById('tombol-generate-tambah');
            const tombolSalinTambah = document.getElementById('tombol-salin-tambah');
            const labelSalinTambah = document.getElementById('label-salin-tambah');

            const perbaruiPreviewTambah = () => {
                if (!previewTambah) return;
                const opt = selectTambahDest?.selectedOptions?.[0];
                const namaDest = opt?.dataset?.nama || '-';
                const kabDest = opt?.dataset?.kabupaten || '';
                const destinasiTeks = namaDest !== '-' && kabDest ? `${namaDest} (${kabDest})` : namaDest;

                const userBersih = (inputTambahUser?.value || '').replace(/^@+/, '').trim();
                const passTeks = (inputTambahPass?.value || '').trim();

                const hintTambahUser = document.getElementById('tambah_username_hint');
                const valid = /^[a-z0-9_]{4,50}$/.test(userBersih);

                if (ikonTambahUserValid) {
                    ikonTambahUserValid.classList.toggle('hidden', !valid);
                }
                if (hintTambahUser) {
                    hintTambahUser.classList.toggle('hidden', !valid);
                    hintTambahUser.classList.toggle('flex', valid);
                }
                previewTambah.textContent =
                    `Username: ${userBersih || '-'}\n` +
                    `Password: ${passTeks || '-'}\n` +
                    `Destinasi: ${destinasiTeks}`;
            };

            if (selectTambahDest) {
                selectTambahDest.addEventListener('change', () => {
                    const opt = selectTambahDest.selectedOptions?.[0];
                    const namaDest = opt?.dataset?.nama || '';
                    if (namaDest && inputTambahUser) {
                        inputTambahUser.value = buatSlugUsername(namaDest);
                    }
                    if (namaDest && inputTambahPass && !inputTambahPass.value.trim()) {
                        inputTambahPass.value = buatSandiAcak(namaDest);
                    }
                    perbaruiPreviewTambah();
                });
            }

            if (inputTambahUser) inputTambahUser.addEventListener('input', perbaruiPreviewTambah);
            if (inputTambahPass) inputTambahPass.addEventListener('input', perbaruiPreviewTambah);

            if (tombolGenTambah) {
                tombolGenTambah.addEventListener('click', () => {
                    const namaDest = selectTambahDest?.selectedOptions?.[0]?.dataset?.nama || '';
                    if (inputTambahPass) {
                        inputTambahPass.value = buatSandiAcak(namaDest);
                        perbaruiPreviewTambah();
                    }
                });
            }

            if (tombolSalinTambah && previewTambah) {
                tombolSalinTambah.addEventListener('click', () => {
                    salinTeks(previewTambah.textContent, labelSalinTambah);
                });
            }

            const tombolBukaTambah = document.getElementById('tombol-tambah-pengelola');
            if (tombolBukaTambah) {
                tombolBukaTambah.addEventListener('click', () => {
                    if (inputTambahPass && !inputTambahPass.value.trim()) {
                        const namaDest = selectTambahDest?.selectedOptions?.[0]?.dataset?.nama || '';
                        inputTambahPass.value = buatSandiAcak(namaDest);
                    }
                    perbaruiPreviewTambah();
                    bukaDialog(modalTambah);
                });
            }

            // Modal Edit Pengelola
            const modalEdit = document.getElementById('modal-edit-pengelola');
            const formEdit = document.getElementById('form-edit-pengelola');
            const selectEditDest = document.getElementById('edit_id_destinasi');
            const inputEditUser = document.getElementById('edit_username');
            const inputEditPass = document.getElementById('edit_password');
            const boxKredensialEdit = document.getElementById('box-kredensial-edit');
            const previewEdit = document.getElementById('preview-kredensial-edit');
            const tombolGenEdit = document.getElementById('tombol-generate-edit');
            const tombolSalinEdit = document.getElementById('tombol-salin-edit');
            const labelSalinEdit = document.getElementById('label-salin-edit');

            const perbaruiPreviewEdit = () => {
                if (!boxKredensialEdit || !previewEdit) return;
                const passBaru = (inputEditPass?.value || '').trim();
                if (!passBaru) {
                    boxKredensialEdit.classList.add('hidden');
                    return;
                }
                const opt = selectEditDest?.selectedOptions?.[0];
                const namaDest = opt?.dataset?.nama || '-';
                const kabDest = opt?.dataset?.kabupaten || '';
                const destinasiTeks = namaDest !== '-' && kabDest ? `${namaDest} (${kabDest})` : namaDest;
                const userBersih = (inputEditUser?.value || '').replace(/^@+/, '').trim();

                previewEdit.textContent =
                    `Username: ${userBersih || '-'}\n` +
                    `Password: ${passBaru}\n` +
                    `Destinasi: ${destinasiTeks}`;
                boxKredensialEdit.classList.remove('hidden');
            };

            if (inputEditUser) inputEditUser.addEventListener('input', perbaruiPreviewEdit);
            if (inputEditPass) inputEditPass.addEventListener('input', perbaruiPreviewEdit);
            if (selectEditDest) selectEditDest.addEventListener('change', perbaruiPreviewEdit);

            if (tombolGenEdit) {
                tombolGenEdit.addEventListener('click', () => {
                    const namaDest = selectEditDest?.selectedOptions?.[0]?.dataset?.nama || '';
                    if (inputEditPass) {
                        inputEditPass.value = buatSandiAcak(namaDest);
                        perbaruiPreviewEdit();
                    }
                });
            }

            if (tombolSalinEdit && previewEdit) {
                tombolSalinEdit.addEventListener('click', () => {
                    salinTeks(previewEdit.textContent, labelSalinEdit);
                });
            }

            const bukaModalEditDariData = (d) => {
                if (!modalEdit || !formEdit) return;
                formEdit.action = d.action || '#';
                if (inputEditUser) inputEditUser.value = d.username || '';
                if (inputEditPass) inputEditPass.value = '';

                if (selectEditDest) {
                    Array.from(selectEditDest.options).forEach((opt) => {
                        if (!opt.value) return;
                        const pemilik = opt.dataset.pemilikId || '';
                        const bolehDipilih = pemilik === '' || pemilik === String(d.id);
                        opt.disabled = !bolehDipilih;
                        opt.hidden = !bolehDipilih;
                    });
                    selectEditDest.value = d.destinasiId || '';
                }

                perbaruiPreviewEdit();
                bukaDialog(modalEdit);
            };

            // Modal Hapus Pengelola
            const modalHapus = document.getElementById('modal-hapus-pengelola');
            const formHapus = document.getElementById('form-hapus-pengelola');
            const teksHapusUser = document.getElementById('hapus-username');
            const teksHapusDest = document.getElementById('hapus-destinasi');

            // Event delegation global
            document.addEventListener('click', (e) => {
                const btnTutup = e.target.closest('[data-tutup-dialog]');
                if (btnTutup) {
                    tutupDialog(document.getElementById(btnTutup.dataset.tutupDialog));
                    return;
                }

                const btnToggleSandi = e.target.closest('[data-toggle-sandi]');
                if (btnToggleSandi) {
                    const targetInput = document.getElementById(btnToggleSandi.dataset.toggleSandi);
                    if (targetInput) {
                        targetInput.type = targetInput.type === 'password' ? 'text' : 'password';
                    }
                    return;
                }

                const btnEdit = e.target.closest('[data-buka-edit]');
                if (btnEdit) {
                    bukaModalEditDariData(btnEdit.dataset);
                    return;
                }

                const btnHapus = e.target.closest('[data-buka-hapus]');
                if (btnHapus && modalHapus && formHapus) {
                    formHapus.action = btnHapus.dataset.action || '#';
                    if (teksHapusUser) teksHapusUser.textContent = btnHapus.dataset.username || '-';
                    if (teksHapusDest) teksHapusDest.textContent = btnHapus.dataset.destinasi || '-';
                    bukaDialog(modalHapus);
                    return;
                }

                const linkReset = e.target.closest('[data-reset-pengelola]');
                if (linkReset) {
                    e.preventDefault();
                    if (inputSearch) inputSearch.value = '';
                    if (selectFilterKabupaten) selectFilterKabupaten.value = '';
                    if (selectFilterStatus) selectFilterStatus.value = 'semua';
                    muatDaftarPengelola(linkReset.href, 'push');
                    return;
                }

                const linkPagination = e.target.closest('nav[aria-label="Navigasi halaman pengelola"] a');
                if (linkPagination) {
                    e.preventDefault();
                    muatDaftarPengelola(linkPagination.href, 'push');
                }
            });

            // Auto-buka modal jika dipicu dari ?terbitkan=1 atau validasi gagal
            @if ($bukaModalTambah)
                if (inputTambahPass && !inputTambahPass.value.trim()) {
                    inputTambahPass.value = buatSandiAcak('');
                }
                perbaruiPreviewTambah();
                bukaDialog(modalTambah);
            @elseif ($editErrorId > 0)
                bukaDialog(modalEdit);
            @endif
        });
    </script>
    @endpush
</x-layouts.admin>