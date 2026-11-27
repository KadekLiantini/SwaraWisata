@php
    $crumbTataKelola = new class implements \Illuminate\Contracts\Support\Htmlable {
        public function toHtml()
        {
            return '<a href="'.e(route('admin.dashboard')).'" class="transition hover:text-primary">Tata Kelola</a>';
        }
    };

    $crumbKategori = new class implements \Illuminate\Contracts\Support\Htmlable {
        public function toHtml()
        {
            return '<a href="'.e(route('admin.kategori.index')).'" class="transition hover:text-primary">Kategori Pelaporan</a>';
        }
    };

    $currentPage = $kategori->currentPage();
    $lastPage = $kategori->lastPage();

    if ($lastPage <= 7) {
        $paginationItems = range(1, max(1, $lastPage));
    } elseif ($currentPage <= 3) {
        $paginationItems = [1, 2, 3, '…', $lastPage];
    } elseif ($currentPage >= $lastPage - 2) {
        $paginationItems = [1, '…', $lastPage - 2, $lastPage - 1, $lastPage];
    } else {
        $paginationItems = [
            1, '…',
            $currentPage - 1,
            $currentPage,
            $currentPage + 1,
            '…',
            $lastPage,
        ];
    }
@endphp

<x-layouts.admin
    title="Kategori Pelaporan"
    :breadcrumbs="[$crumbTataKelola, $crumbKategori]"
>
    <div class="space-y-5">

        {{-- HEADER --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <h1 class="font-heading text-2xl font-bold leading-tight tracking-tight text-ink sm:text-[28px]">
                Kategori Pelaporan Kerusakan
            </h1>

            <x-button
                href="{{ route('admin.kategori.create') }}"
                variant="primary"
                size="sm"
                class="shrink-0 self-start whitespace-nowrap sm:self-center"
            >
                <x-icon name="add_circle" size="16" />
                Tambah Kategori Baru
            </x-button>
        </div>

        {{-- PENCARIAN DAN FILTER --}}
        <div class="rounded-xl border border-line bg-white p-4 shadow-sm sm:p-5">
            <form
                method="GET"
                action="{{ route('admin.kategori.index') }}"
                autocomplete="off"
                data-live-search
                data-live-target="#hasil-kategori"
                class="grid grid-cols-1 gap-x-4 gap-y-3 lg:grid-cols-12 lg:items-center"
            >
                {{-- Search --}}
                <div class="lg:col-span-5">
                    <label for="search" class="sr-only">Cari kategori</label>

                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-subtle">
                            <x-icon name="search" size="17" />
                        </span>

                        <input
                            id="search"
                            name="search"
                            type="text"
                            value="{{ $search }}"
                            placeholder="Cari nama kategori atau fasilitas..."
                            class="w-full rounded-lg border border-line bg-white py-2.5 pl-10 pr-3 text-sm text-ink outline-none transition placeholder:text-subtle focus:border-primary focus:ring-2 focus:ring-primary/10"
                        >
                    </div>
                </div>

                {{-- Filter status --}}
                <div class="flex items-center justify-end gap-3 lg:col-span-7">
                    <label for="status" class="shrink-0 text-xs font-semibold text-muted">
                        Filter Status:
                    </label>

                    <select
                        id="status"
                        name="status"
                        class="w-[180px] shrink-0 rounded-lg border border-line bg-white px-3 py-2.5 text-sm text-ink outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/10"
                    >
                        <option value="aktif" @selected($status === 'aktif')>Aktif Digunakan</option>
                        <option value="nonaktif" @selected($status === 'nonaktif')>Nonaktif</option>
                        <option value="semua" @selected($status === 'semua')>Semua Status</option>
                    </select>

                    <a
                        href="{{ route('admin.kategori.index') }}"
                        aria-label="Reset filter"
                        title="Reset filter"
                        class="flex size-[42px] shrink-0 items-center justify-center rounded-lg border border-line bg-white text-muted transition hover:bg-soft hover:text-primary"
                    >
                        <x-icon name="refresh" size="16" />
                    </a>
                </div>
            </form>
        </div>

    <div id="hasil-kategori">
        {{-- TABEL KATEGORI --}}
        <div class="overflow-hidden rounded-xl border border-line bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[1100px] table-fixed text-left">
                    <colgroup>
                        <col style="width: 7%">
                        <col style="width: 21%">
                        <col style="width: 29%">
                        <col style="width: 13%">
                        <col style="width: 11%">
                        <col style="width: 19%">
                    </colgroup>

                    <thead class="bg-soft">
                        <tr class="text-[10px] font-semibold uppercase tracking-[0.06em] text-muted">
                            <th class="px-3 py-3.5 text-center">No</th>
                            <th class="px-3 py-3.5">Nama Kategori Kerusakan</th>
                            <th class="px-3 py-3.5">Deskripsi Cakupan Fasilitas</th>
                            <th class="px-2 py-3.5 text-center">Laporan Terkait</th>
                            <th class="px-2 py-3.5 text-center">Status</th>
                            <th class="px-2 py-3.5 text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-line">
                        @forelse ($kategori as $index => $item)
                            <tr class="transition-colors hover:bg-soft/40">

                                {{-- NOMOR --}}
                                <td class="px-3 py-4 text-center text-xs font-medium text-muted">
                                    {{ $kategori->firstItem() + $index }}
                                </td>

                                {{-- NAMA KATEGORI --}}
                                <td class="px-3 py-4">
                                    <p class="text-sm font-semibold leading-snug text-ink">
                                        {{ $item->nama }}
                                    </p>
                                    <p class="mt-1 text-[10px] text-muted">
                                        ID: {{ $item->id_kategori_kerusakan }}
                                    </p>
                                </td>

                                {{-- DESKRIPSI --}}
                                <td class="px-3 py-4">
                                    <p class="text-xs leading-relaxed text-muted">
                                        {{ $item->deskripsi ?: '-' }}
                                    </p>
                                </td>

                                {{-- JUMLAH LAPORAN --}}
                                <td class="px-2 py-4 text-center">
                                    <span class="inline-flex items-center justify-center gap-1.5 whitespace-nowrap rounded-full bg-mint px-3 py-2 text-[11px] font-semibold text-primary">
                                        <x-icon name="confirmation_number" size="14" />
                                        <span>{{ $item->laporan_count }} Laporan</span>
                                    </span>
                                </td>

                                {{-- STATUS --}}
                                <td class="px-2 py-4 text-center">
                                    @if ($item->is_active)
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-soft-blue px-2.5 py-1 text-[10px] font-semibold text-primary">
                                            <span class="size-1.5 rounded-full bg-primary"></span>
                                            Aktif
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-peach px-2.5 py-1 text-[10px] font-semibold text-secondary-ink">
                                            <span class="size-1.5 rounded-full bg-secondary"></span>
                                            Nonaktif
                                        </span>
                                    @endif
                                </td>

                                {{-- AKSI --}}
                                <td class="px-2 py-4">
                                    <div class="flex items-center justify-center gap-2">
                                        <x-button
                                            :href="route('admin.kategori.edit', ['id_kategori_kerusakan' => $item->id_kategori_kerusakan])"
                                            variant="ghost"
                                            size="sm"
                                            class="inline-flex shrink-0 items-center justify-center gap-1.5 whitespace-nowrap"
                                        >
                                            Edit
                                        </x-button>

                                        <form
                                            method="POST"
                                            action="{{ route('admin.kategori.status', ['id_kategori_kerusakan' => $item->id_kategori_kerusakan]) }}"
                                            data-form-status-kategori="1"
                                            data-nama="{{ $item->nama }}"
                                            data-aksi="{{ $item->is_active ? 'dinonaktifkan' : 'diaktifkan' }}"
                                        >
                                            @csrf
                                            @method('PATCH')

                                            <x-button
                                                type="submit"
                                                variant="{{ $item->is_active ? 'outline' : 'primary' }}"
                                                size="sm"
                                                class="min-w-[105px] justify-center whitespace-nowrap"
                                            >
                                                {{ $item->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                            </x-button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-5 py-12 text-center">
                                    <div class="flex flex-col items-center gap-2 text-muted">
                                        <x-icon name="search_off" size="26" />
                                        <p class="text-sm">
                                            Tidak ada kategori yang sesuai dengan pencarian atau filter.
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
                <p class="text-xs text-muted">
                    Menampilkan
                    <span class="font-semibold text-ink">
                        {{ $kategori->firstItem() ?? 0 }}–{{ $kategori->lastItem() ?? 0 }}
                    </span>
                    dari
                    <span class="font-semibold text-ink">
                        {{ $kategori->total() }}
                    </span>
                    kategori
                </p>

                <nav aria-label="Navigasi halaman" class="flex items-center justify-end gap-1.5">
                    @if ($kategori->onFirstPage())
                        <span aria-disabled="true" class="flex size-7 items-center justify-center rounded-md bg-soft-blue text-subtle">
                            <x-icon name="chevron_left" size="16" />
                        </span>
                    @else
                        <a href="{{ $kategori->previousPageUrl() }}" aria-label="Halaman sebelumnya"
                            data-live-link
                            class="flex size-7 items-center justify-center rounded-md bg-soft-blue text-primary transition hover:bg-primary/10">
                            <x-icon name="chevron_left" size="16" />
                        </a>
                    @endif  

                    @foreach ($paginationItems as $page)
                        @if ($page === '…')
                            <span class="flex size-7 items-center justify-center text-xs text-muted">…</span>
                        @elseif ($page === $currentPage)
                            <span aria-current="page" class="flex size-7 items-center justify-center rounded-md bg-primary text-xs font-semibold text-white">
                                {{ $page }}
                            </span>
                        @else
                            <a href="{{ $kategori->url($page) }}" aria-label="Halaman {{ $page }}"
                                data-live-link
                                class="flex size-7 items-center justify-center rounded-md bg-soft-blue text-xs font-semibold text-primary transition hover:bg-primary/10">
                                {{ $page }}
                            </a>
                        @endif
                    @endforeach

                    @if ($kategori->hasMorePages())
                        <a href="{{ $kategori->nextPageUrl() }}" aria-label="Halaman berikutnya"
                            data-live-link
                            class="flex size-7 items-center justify-center rounded-md bg-soft-blue text-primary transition hover:bg-primary/10">
                            <x-icon name="chevron_right" size="16" />
                        </a>
                    @else
                        <span aria-disabled="true" class="flex size-7 items-center justify-center rounded-md bg-soft-blue text-subtle">
                            <x-icon name="chevron_right" size="16" />
                        </span>
                    @endif
                </nav>
            </div>
        </div>
    </div>
    
    {{-- MODAL KONFIRMASI STATUS --}}
        <div
            id="modal-konfirmasi-kategori"
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
                        <h2 id="nama-kategori-status" class="font-heading text-lg font-bold text-ink">
                            Ubah status kategori?
                        </h2>
                        <p class="mt-1 text-sm leading-relaxed text-muted">
                            Kategori <span id="nama-kategori-status" class="font-semibold text-ink"></span>
                            akan <span id="aksi-status-kategori" class="font-semibold text-ink"></span>.
                            Data kategori dan laporan terkait tidak akan dihapus.
                        </p>
                    </div>
                </div>

                <div class="mt-6 flex flex-col-reverse justify-end gap-2 sm:flex-row">
                    <x-button type="button" variant="outline" size="sm" data-tutup-status-kategori="1">
                        Batal
                    </x-button>

                    <x-button id="konfirmasi-status-kategori" type="button" variant="primary" size="sm">
                        Ya, Lanjutkan
                    </x-button>
                </div>
            </div>
        </div>
</x-layouts.admin>
