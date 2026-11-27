@php
    $crumbTataKelola = new class implements \Illuminate\Contracts\Support\Htmlable {
        public function toHtml()
        {
            return '<a href="'.e(route('admin.dashboard')).'" class="transition hover:text-primary">Tata Kelola</a>';
        }
    };

    $crumbKelolaDestinasi = new class implements \Illuminate\Contracts\Support\Htmlable {
        public function toHtml()
        {
            return '<a href="'.e(route('admin.destinasi.index')).'" class="transition hover:text-primary">Kelola Destinasi</a>';
        }
    };
@endphp

<x-layouts.admin
    title="Kelola Destinasi"
    :breadcrumbs="[$crumbTataKelola, $crumbKelolaDestinasi]"
>
    @php
        $total = (int) ($ringkasan['total'] ?? 0);
        $terhubung = (int) ($ringkasan['terhubung'] ?? 0);
        $belum = (int) ($ringkasan['belum_terhubung'] ?? 0);

        $persentase = $total > 0 ? ($terhubung / $total) * 100 : 0;
        $persentaseBelum = $total > 0 ? ($belum / $total) * 100 : 0;

        $persentaseLabel = number_format($persentase, 1, ',', '.');
        $progressWidth = number_format($persentase, 2, '.', '');
        $progressBelumWidth = number_format($persentaseBelum, 2, '.', '');

        $currentPage = $destinasi->currentPage();
        $lastPage = $destinasi->lastPage();

        if ($lastPage <= 7) {
            $paginationItems = range(1, max(1, $lastPage));
        } elseif ($currentPage <= 3) {
            $paginationItems = [1, 2, 3, '…', $lastPage];
        } elseif ($currentPage >= $lastPage - 2) {
            $paginationItems = [
                1, '…',
                $lastPage - 2,
                $lastPage - 1,
                $lastPage
            ];
        } else {
            $paginationItems = [
                1, '…',
                $currentPage - 1,
                $currentPage,
                $currentPage + 1,
                '…',
                $lastPage
            ];
        }
    @endphp

    <div class="space-y-5">

        {{-- HEADER HALAMAN --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="min-w-0">
                <h1 class="font-heading text-2xl font-bold leading-tight tracking-tight text-ink sm:text-[28px]">
                    Katalog & Kelola Destinasi Wisata
                </h1>
                <p class="mt-1 max-w-2xl text-sm leading-relaxed text-muted">
                    Kelola basis data tempat wisata resmi, perbarui profil destinasi, dan pantau status keterikatan akun pengelola secara terpusat.
                </p>
            </div>

            <x-button
                href="{{ route('admin.destinasi.create') }}"
                variant="primary"
                size="sm"
                class="shrink-0 self-start whitespace-nowrap sm:self-center"
            >
                <x-icon name="add_circle" size="16" />
                Tambah Destinasi Baru
            </x-button>
        </div>


        {{-- KARTU RINGKASAN --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">

            {{-- Total destinasi --}}
            <div class="flex min-h-[124px] flex-col rounded-xl border border-line bg-white p-4 shadow-sm sm:p-5">
                <div class="flex items-start justify-between gap-3">
                    <p class="text-[10px] font-semibold uppercase tracking-[0.08em] text-muted">
                        Total Destinasi Terdaftar
                    </p>

                    <span class="flex size-8 shrink-0 items-center justify-center rounded-lg bg-soft-blue text-primary">
                        <x-icon name="park" size="18" />
                    </span>
                </div>

                <div class="mt-1 flex flex-wrap items-baseline gap-x-2 gap-y-1">
                    <span class="font-heading text-[30px] font-bold leading-none tracking-tight text-ink">
                        {{ $total }}
                    </span>
                    <span class="text-[11px] font-medium text-primary">
                        Dalam Katalog
                    </span>
                </div>

                <div class="mt-auto pt-3">
                    <div class="h-1.5 w-full overflow-hidden rounded-full bg-soft">
                        <div
                            class="h-full rounded-full bg-primary transition-all"
                            style="width: {{ $progressWidth }}%"
                        ></div>
                    </div>
                </div>
            </div>


            {{-- Destinasi terhubung --}}
            <div class="flex min-h-[124px] flex-col rounded-xl border border-line bg-white p-4 shadow-sm sm:p-5">
                <div class="flex items-start justify-between gap-3">
                    <p class="text-[10px] font-semibold uppercase tracking-[0.08em] text-muted">
                        Terkoneksi Akun Pengelola
                    </p>

                    <span class="flex size-8 shrink-0 items-center justify-center rounded-lg bg-soft-blue text-primary">
                        <x-icon name="verified_user" size="18" />
                    </span>
                </div>

                <div class="mt-1 flex flex-wrap items-baseline gap-x-2 gap-y-1">
                    <span class="font-heading text-[30px] font-bold leading-none tracking-tight text-ink">
                        {{ $terhubung }}
                    </span>
                    <span class="text-[11px] font-medium text-primary">
                        {{ $persentaseLabel }}% Terhubung
                    </span>
                </div>

                <div class="mt-auto pt-3">
                    <div class="h-1.5 w-full overflow-hidden rounded-full bg-soft">
                        <div
                            class="h-full rounded-full bg-primary transition-all"
                            style="width: {{ $progressWidth }}%"
                        ></div>
                    </div>
                </div>
            </div>


            {{-- Belum terhubung --}}
            <div class="flex min-h-[124px] flex-col rounded-xl border border-line bg-white p-4 shadow-sm sm:col-span-2 sm:p-5 xl:col-span-1">
                <div class="flex items-start justify-between gap-3">
                    <p class="text-[10px] font-semibold uppercase tracking-[0.08em] text-secondary-ink">
                        Perlu Penugasan Akun
                    </p>

                    <span class="flex size-8 shrink-0 items-center justify-center rounded-lg bg-peach text-secondary-ink">
                        <x-icon name="warning" size="18" />
                    </span>
                </div>

                <div class="mt-1 flex flex-wrap items-baseline gap-x-2 gap-y-1">
                    <span class="font-heading text-[30px] font-bold leading-none tracking-tight text-secondary-ink">
                        {{ $belum }}
                    </span>
                    <span class="text-[11px] font-medium text-secondary-ink">
                        Belum Ada Pengelola
                    </span>
                </div>

                <div class="mt-auto pt-3">
                    <div class="h-1.5 w-full overflow-hidden rounded-full bg-secondary/20">
                        <div
                            class="h-full rounded-full bg-secondary transition-all"
                            style="width: {{ $progressBelumWidth }}%"
                        ></div>
                    </div>
                </div>
            </div>

        </div>

        {{-- PENCARIAN DAN FILTER --}}
        <div class="rounded-xl border border-line bg-white p-4 shadow-sm sm:p-5">
            <form
                id="form-filter-destinasi"
                method="GET"
                action="{{ route('admin.destinasi.index') }}"
                autocomplete="off"
                class="grid grid-cols-1 gap-x-4 gap-y-3 lg:grid-cols-12 lg:items-start"
            >
                {{-- Pencarian: kiri, posisi tengah secara vertikal --}}
                <div class="lg:col-span-4 lg:row-span-2 lg:self-center">
                    <label for="search" class="sr-only">Cari Destinasi</label>

                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-subtle">
                            <x-icon name="search" size="17" />
                        </span>

                        <input
                            id="search"
                            name="search"
                            type="text"
                            value="{{ request('search', '') }}"
                            placeholder="Cari nama destinasi atau kecamatan..."
                            class="w-full rounded-lg border border-line bg-white py-2.5 pl-10 pr-3 text-sm text-ink outline-none transition placeholder:text-subtle focus:border-primary focus:ring-2 focus:ring-primary/10"
                        >
                    </div>
                </div>

                {{-- Wilayah: tengah atas --}}
                <div class="lg:col-span-3">
                    <label for="id_kabupaten" class="sr-only">Wilayah</label>

                    <select
                        id="id_kabupaten"
                        name="id_kabupaten"
                        class="w-full rounded-lg border border-line bg-white px-3 py-2.5 text-sm text-ink outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/10"
                    >
                        <option value="">Semua Wilayah</option>

                        @foreach ($kabupaten as $id => $nama)
                            <option
                                value="{{ $id }}"
                                @selected((string) request('id_kabupaten', '') === (string) $id)
                            >
                                {{ $nama }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Status pengelola: kanan atas --}}
                <div class="lg:col-span-5">
                    <label for="status" class="sr-only">Status Pengelola</label>

                    <select
                        id="status"
                        name="status"
                        class="w-full rounded-lg border border-line bg-white px-3 py-2.5 text-sm text-ink outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/10"
                    >
                        <option value="">Semua Status Pengelola</option>

                        <option
                            value="terhubung"
                            @selected(request('status') === 'terhubung')
                        >
                            Sudah Memiliki Akun
                        </option>

                        <option
                            value="belum_terhubung"
                            @selected(request('status') === 'belum_terhubung')
                        >
                            Belum Memiliki Akun
                        </option>
                    </select>
                </div>

                {{-- Urutkan: tengah bawah --}}
                <div class="lg:col-span-3">
                    <label for="sort" class="sr-only">Urutkan</label>

                    <select
                        id="sort"
                        name="sort"
                        class="w-full rounded-lg border border-line bg-white px-3 py-2.5 text-sm text-ink outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/10"
                    >
                        <option value="">Terbaru</option>
                        <option
                            value="nama_az"
                            @selected(request('sort') === 'nama_az')
                        >
                            Nama A-Z
                        </option>
                    </select>
                </div>

                {{-- Pencarian dan filter diterapkan otomatis; hanya tombol reset yang tetap terlihat --}}
                <div class="flex items-center gap-2 sm:col-span-1 lg:col-span-4">
                    <span class="sr-only">
                        Daftar diperbarui otomatis saat pencarian diketik atau filter diubah.
                    </span>

                    <a
                        href="{{ route('admin.destinasi.index') }}"
                        aria-label="Reset filter"
                        title="Reset filter"
                        class="flex size-[42px] shrink-0 items-center justify-center rounded-lg border border-line bg-white text-muted transition hover:bg-soft hover:text-primary"
                    >
                        <x-icon name="refresh" size="16" />
                    </a>
                </div>
            </form>
        </div>

        {{-- TABEL DESTINASI --}}
        <div id="daftar-destinasi-container" class="overflow-hidden rounded-xl border border-line bg-white shadow-sm">

            {{-- Isi tabel --}}
            <div class="overflow-x-auto">
                <table class="w-full min-w-[900px] table-fixed text-left">

                    <colgroup>
                        <col style="width: 7%">
                        <col style="width: 21%">
                        <col style="width: 14%">
                        <col style="width: 14%">
                        <col style="width: 16%">
                        <col style="width: 7%">
                        <col style="width: 21%">
                    </colgroup>

                    <thead class="bg-soft">
                        <tr class="text-[10px] font-semibold uppercase tracking-[0.06em] text-muted">
                            <th class="px-3 py-3.5">Foto</th>
                            <th class="px-3 py-3.5">Nama Destinasi</th>
                            <th class="px-3 py-3.5">Wilayah</th>
                            <th class="px-3 py-3.5">Kategori</th>
                            <th class="px-3 py-3.5">Pengelola</th>
                            <th class="px-2 py-3.5 text-center">Audit</th>
                            <th class="px-2 py-3.5 text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-line">

                        @forelse ($destinasi as $item)
                            <tr class="transition-colors hover:bg-soft/40">

                                {{-- Foto --}}
                                <td class="px-3 py-3">
                                    @if ($item->foto)
                                        <img
                                            src="{{ \Illuminate\Support\Facades\Storage::url($item->foto) }}"
                                            alt="Foto {{ $item->nama }}"
                                            class="size-10 rounded-lg object-cover"
                                        >
                                    @else
                                        <div
                                            class="flex size-10 items-center justify-center rounded-lg bg-soft text-subtle"
                                            role="img"
                                            aria-label="Belum ada foto"
                                            title="Belum ada foto"
                                        >
                                            <x-icon name="image" size="16" />
                                        </div>
                                    @endif
                                </td>


                                {{-- Nama destinasi --}}
                                <td class="px-3 py-3">
                                    <p class="truncate text-xs font-semibold text-ink">
                                        {{ $item->nama }}
                                    </p>
                                    <p class="mt-1.5 flex flex-wrap items-center gap-2 text-[11px] text-muted">
                                        <span>#{{ $item->id_destinasi }}</span>

                                        @if ($item->is_active)
                                            <span class="inline-flex items-center gap-1.5 rounded-full border border-primary/15 bg-mint px-2.5 py-1 text-[11px] font-semibold leading-none text-primary">
                                                <span class="size-1.5 rounded-full bg-primary"></span>
                                                Aktif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 rounded-full border border-secondary/25 bg-peach px-2.5 py-1 text-[11px] font-semibold leading-none text-secondary-ink">
                                                <span class="size-1.5 rounded-full bg-secondary"></span>
                                                Nonaktif
                                            </span>
                                        @endif
                                    </p>
                                </td>


                                {{-- Wilayah: digeser ke kiri --}}
                                <td class="px-3 py-3">
                                    <p class="truncate text-xs font-medium text-ink">
                                        {{ $item->kecamatan?->kabupaten?->nama ?? '-' }}
                                    </p>
                                    <p class="mt-1 truncate text-[10px] text-muted">
                                        {{ $item->kecamatan?->nama ?? '-' }}
                                    </p>
                                </td>


                                {{-- Kategori --}}
                                <td class="px-3 py-3">
                                    <span class="inline-flex max-w-full truncate rounded-full bg-soft-blue px-2.5 py-1.5 text-[10px] font-semibold text-primary">
                                        {{ $item->kategoriWisata?->nama ?? '-' }}
                                    </span>
                                </td>


                                {{-- Akun pengelola --}}
                                <td class="px-3 py-3">
                                    @if ($item->pengelola)
                                        <span
                                            class="inline-flex max-w-full items-center gap-1.5 rounded-full bg-mint px-2 py-1 text-[10px] font-semibold text-primary"
                                            title="Akun pengelola: {{ $item->pengelola->username }}"
                                        >
                                            <span class="size-1.5 shrink-0 rounded-full bg-primary"></span>
                                            <span class="truncate">{{ $item->pengelola->username }}</span>
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex max-w-full items-center gap-1.5 rounded-full bg-peach px-2 py-1 text-[10px] font-semibold text-secondary-ink"
                                            title="Belum ada akun pengelola"
                                        >
                                            <span class="size-1.5 shrink-0 rounded-full bg-secondary"></span>
                                            <span class="truncate">Belum Ada</span>
                                        </span>
                                    @endif
                                </td>


                                {{-- Audit: jumlah laporan aktif --}}
                                <td class="px-2 py-3 text-center">
                                    @if ((int) $item->laporan_count > 0)
                                        <span
                                            class="inline-flex items-center justify-center gap-1 rounded-full bg-peach px-2 py-1 text-[10px] font-semibold text-secondary-ink"
                                            title="{{ (int) $item->laporan_count }} laporan aktif"
                                        >
                                            <x-icon name="warning" size="13" />
                                            {{ (int) $item->laporan_count }}
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center justify-center gap-1 rounded-full bg-soft-blue px-2 py-1 text-[10px] font-semibold text-primary"
                                            title="Tidak ada laporan aktif"
                                        >
                                            <x-icon name="check_circle" size="13" />
                                            0
                                        </span>
                                    @endif
                                </td>


                                {{-- Aksi --}}
                                <td class="px-2 py-3">
                                    <div class="flex items-center justify-center gap-2">
                                        <x-button
                                            :href="route('admin.destinasi.edit', ['id_destinasi' => $item->id_destinasi])"
                                            variant="ghost"
                                            size="sm"
                                        >
                                            Edit
                                        </x-button>

                                        <form
                                            method="POST"
                                            action="{{ route('admin.destinasi.status', ['id_destinasi' => $item->id_destinasi]) }}"
                                            data-form-status="1"
                                            data-nama="{{ $item->nama }}"
                                            data-aksi="{{ $item->is_active ? 'menonaktifkan' : 'mengaktifkan' }}"
                                        >
                                            @csrf
                                            @method('PATCH')

                                            <x-button
                                                type="submit"
                                                :variant="$item->is_active ? 'outline' : 'primary'"
                                                size="sm"
                                                class="min-w-[116px] justify-center whitespace-nowrap"
                                            >
                                                {{ $item->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </x-button>
                                        </form>
                                    </div>
                                </td>

                            </tr>

                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-12 text-center">
                                    <div class="flex flex-col items-center gap-2 text-muted">
                                        <x-icon name="search_off" size="26" />
                                        <p class="text-sm">
                                            Tidak ada destinasi yang sesuai dengan pencarian atau filter.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>


            {{-- FOOTER DAN PAGINATION --}}
            <div class="flex flex-col gap-3 border-t border-line px-4 py-3 sm:flex-row sm:items-center sm:justify-between sm:px-5">

                {{-- Informasi jumlah data --}}
                <p class="text-xs text-muted">
                    Menampilkan
                    <span class="font-semibold text-ink">
                        {{ $destinasi->firstItem() ?? 0 }}–{{ $destinasi->lastItem() ?? 0 }}
                    </span>
                    dari
                    <span class="font-semibold text-ink">
                        {{ $destinasi->total() }}
                    </span>
                    destinasi
                </p>


                {{-- Navigasi halaman --}}
                <nav
                    aria-label="Navigasi halaman"
                    class="flex items-center justify-end gap-1.5"
                >

                    {{-- Halaman sebelumnya --}}
                    @if ($destinasi->onFirstPage())
                        <span
                            aria-disabled="true"
                            aria-label="Tidak ada halaman sebelumnya"
                            class="flex size-7 items-center justify-center rounded-md bg-soft-blue text-subtle"
                        >
                            <x-icon name="chevron_left" size="16" />
                        </span>
                    @else
                        <a
                            href="{{ $destinasi->previousPageUrl() }}"
                            aria-label="Halaman sebelumnya"
                            class="flex size-7 items-center justify-center rounded-md bg-soft-blue text-primary transition hover:bg-primary/10"
                        >
                            <x-icon name="chevron_left" size="16" />
                        </a>
                    @endif


                    {{-- Nomor halaman --}}
                    @foreach ($paginationItems as $page)
                        @if ($page === '…')
                            <span class="flex size-7 items-center justify-center text-xs text-muted">
                                …
                            </span>

                        @elseif ($page === $currentPage)
                            <span
                                aria-current="page"
                                class="flex size-7 items-center justify-center rounded-md bg-primary text-xs font-semibold text-white"
                            >
                                {{ $page }}
                            </span>

                        @else
                            <a
                                href="{{ $destinasi->url($page) }}"
                                aria-label="Halaman {{ $page }}"
                                class="flex size-7 items-center justify-center rounded-md bg-soft-blue text-xs font-semibold text-primary transition hover:bg-primary/10"
                            >
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach


                    {{-- Halaman berikutnya --}}
                    @if ($destinasi->hasMorePages())
                        <a
                            href="{{ $destinasi->nextPageUrl() }}"
                            aria-label="Halaman berikutnya"
                            class="flex size-7 items-center justify-center rounded-md bg-soft-blue text-primary transition hover:bg-primary/10"
                        >
                            <x-icon name="chevron_right" size="16" />
                        </a>
                    @else
                        <span
                            aria-disabled="true"
                            aria-label="Tidak ada halaman berikutnya"
                            class="flex size-7 items-center justify-center rounded-md bg-soft-blue text-subtle"
                        >
                            <x-icon name="chevron_right" size="16" />
                        </span>
                    @endif

                </nav>
            </div>

        </div>

        {{-- MODAL KONFIRMASI STATUS --}}
        <div
            id="modal-konfirmasi-status"
            class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/50 p-4"
            role="dialog"
            aria-modal="true"
            aria-labelledby="judul-modal-status"
        >
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-xl sm:p-7">
                <div class="flex items-start gap-4">
                    <span class="flex size-11 shrink-0 items-center justify-center rounded-full bg-soft-blue text-primary">
                        <x-icon name="warning" size="20" />
                    </span>

                    <div class="min-w-0">
                        <h2 id="judul-modal-status" class="font-heading text-lg font-bold text-ink">
                            Ubah status destinasi?
                        </h2>
                        <p class="mt-1 text-sm leading-relaxed text-muted">
                            Destinasi <span id="nama-destinasi-status" class="font-semibold text-ink"></span>
                            akan <span id="aksi-status" class="font-semibold text-ink"></span>. Data destinasi tidak akan dihapus.
                        </p>
                    </div>
                </div>

                <div class="mt-6 flex flex-col-reverse justify-end gap-2 sm:flex-row">
                    <x-button
                        type="button"
                        variant="outline"
                        size="sm"
                        data-tutup-status="1"
                    >
                        Batal
                    </x-button>

                    <x-button
                        id="konfirmasi-status"
                        type="button"
                        variant="primary"
                        size="sm"
                    >
                        Ya, Lanjutkan
                    </x-button>
                </div>
            </div>
        </div>
    </div>
</x-layouts.admin>
