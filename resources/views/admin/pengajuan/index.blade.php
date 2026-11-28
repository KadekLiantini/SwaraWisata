@php
    $crumbTataKelola = new class implements \Illuminate\Contracts\Support\Htmlable {
        public function toHtml()
        {
            return '<a href="'.e(route('admin.dashboard')).'" class="transition hover:text-primary">Tata Kelola</a>';
        }
    };

    $search = $filter['search'] ?? '';
    $status = $filter['status'] ?? 'semua';
    $idKabupaten = (int) ($filter['id_kabupaten'] ?? 0);
    $sort = $filter['sort'] ?? 'terbaru';

    $total = (int) ($ringkasan['total'] ?? 0);
    $jumlahMenunggu = (int) ($ringkasan[\App\Models\PengajuanDestinasi::MENUNGGU] ?? 0);
    $jumlahDisetujui = (int) ($ringkasan[\App\Models\PengajuanDestinasi::DISETUJUI] ?? 0);
    $jumlahDitolak = (int) ($ringkasan[\App\Models\PengajuanDestinasi::DITOLAK] ?? 0);

    $tabStatus = [
        ['key' => 'semua', 'label' => 'Semua', 'jumlah' => $total],
        ['key' => \App\Models\PengajuanDestinasi::MENUNGGU, 'label' => 'Menunggu', 'jumlah' => $jumlahMenunggu],
        ['key' => \App\Models\PengajuanDestinasi::DISETUJUI, 'label' => 'Disetujui', 'jumlah' => $jumlahDisetujui],
        ['key' => \App\Models\PengajuanDestinasi::DITOLAK, 'label' => 'Ditolak', 'jumlah' => $jumlahDitolak],
    ];

    $parameterTab = array_filter([
        'search' => $search,
        'id_kabupaten' => $idKabupaten > 0 ? $idKabupaten : '',
        'sort' => $sort !== 'terbaru' ? $sort : '',
    ], fn ($nilai) => $nilai !== '' && $nilai !== null);

    $adaFilter = $search !== '' || $status !== 'semua' || $idKabupaten > 0 || $sort !== 'terbaru';

    $oldPengajuanId = (int) old('_pengajuan_id', 0);
    $bukaModalVerifikasi = $oldPengajuanId > 0 && (
        session('buka_verifikasi')
        || $errors->hasAny([
            'nama',
            'id_kategori_wisata',
            'id_kabupaten',
            'id_kecamatan',
            'deskripsi',
            'link_maps',
            'foto',
        ])
    );

    $actionVerifikasiAwal = $oldPengajuanId > 0
        ? route('admin.pengajuan.setujui', ['pengajuan' => $oldPengajuanId])
        : '#';
@endphp

<x-layouts.admin
    title="Pengajuan Lokasi"
    :breadcrumbs="[$crumbTataKelola, 'Pengajuan Lokasi']"
>
    <div class="space-y-5">
        <x-page-header
            title="Pengajuan Lokasi Wisata"
            description="Verifikasi dan validasi usulan tempat wisata baru dari wisatawan terdaftar untuk dimasukkan ke katalog resmi sistem."
        />

        {{-- Panel pencarian dan filter --}}
        <section class="rounded-xl border border-line bg-white p-4 sm:p-5">
            <form
                id="form-filter-pengajuan"
                method="GET"
                action="{{ route('admin.pengajuan.index') }}"
                autocomplete="off"
                class="space-y-3"
            >
                <input type="hidden" id="filter_status" name="status" value="{{ $status }}">

                <div class="grid grid-cols-1 gap-3 xl:grid-cols-[minmax(15rem,1fr)_auto] xl:items-center">
                    <div class="relative min-w-0">
                        <label for="search" class="sr-only">Cari nama tempat wisata atau pengaju</label>
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-muted">
                            <x-icon name="search" size="17" />
                        </span>
                        <input
                            id="search"
                            name="search"
                            type="search"
                            value="{{ $search }}"
                            maxlength="100"
                            placeholder="Cari nama tempat wisata, tautan maps, atau pengaju..."
                            class="w-full rounded-lg border border-transparent bg-soft px-3 py-2.5 pl-10 text-xs text-ink outline-none transition placeholder:text-muted focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/10"
                        >
                    </div>

                    <div id="tab-status-pengajuan" class="max-w-full overflow-x-auto">
                        <div class="flex w-max items-center gap-1 rounded-lg bg-soft p-1">
                            @foreach ($tabStatus as $tab)
                                @php $aktif = $status === $tab['key']; @endphp
                                <a
                                    href="{{ route('admin.pengajuan.index', array_merge($parameterTab, ['status' => $tab['key']])) }}"
                                    data-tab-status="{{ $tab['key'] }}"
                                    @if ($aktif) aria-current="page" @endif
                                    class="inline-flex shrink-0 items-center gap-1.5 rounded-md px-3 py-2 text-[11px] font-semibold transition {{ $aktif ? 'bg-white text-primary shadow-sm' : 'text-ink hover:bg-white/70' }}"
                                >
                                    @if ($tab['key'] === \App\Models\PengajuanDestinasi::MENUNGGU)
                                        <span class="size-1.5 rounded-full bg-secondary"></span>
                                    @endif
                                    <span>{{ $tab['label'] }}</span>
                                    @if ($tab['key'] !== 'semua')
                                        <span class="tabular-nums {{ $aktif ? 'text-primary' : 'text-muted' }}">({{ $tab['jumlah'] }})</span>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-2 sm:grid-cols-[minmax(0,1fr)_minmax(0,1fr)_auto]">
                    <div>
                        <label for="id_kabupaten" class="sr-only">Filter kabupaten</label>
                        <select
                            id="id_kabupaten"
                            name="id_kabupaten"
                            class="w-full rounded-lg border border-transparent bg-soft px-3 py-2.5 text-xs text-ink outline-none transition focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/10"
                        >
                            <option value="">Semua Kabupaten</option>
                            @foreach ($kabupaten as $item)
                                <option value="{{ $item->id_kabupaten }}" @selected($idKabupaten === (int) $item->id_kabupaten)>
                                    {{ $item->label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="sort" class="sr-only">Urutan pengajuan</label>
                        <select
                            id="sort"
                            name="sort"
                            class="w-full rounded-lg border border-transparent bg-soft px-3 py-2.5 text-xs text-ink outline-none transition focus:border-primary focus:bg-white focus:ring-2 focus:ring-primary/10"
                        >
                            <option value="terbaru" @selected($sort === 'terbaru')>Urutan: Terbaru Masuk</option>
                            <option value="terlama" @selected($sort === 'terlama')>Urutan: Terlama Masuk</option>
                        </select>
                    </div>

                    <a
                        href="{{ route('admin.pengajuan.index') }}"
                        id="tombol-reset-filter"
                        aria-label="Reset filter"
                        title="Reset filter"
                        class="flex h-[38px] w-full items-center justify-center rounded-lg bg-soft text-ink transition hover:bg-primary/10 hover:text-primary sm:w-[38px]"
                    >
                        <x-icon name="refresh" size="16" />
                    </a>
                </div>
            </form>
        </section>

        {{-- Tabel pengajuan --}}
        <section id="tabel-pengajuan-wrapper" class="overflow-hidden rounded-xl border border-line bg-white transition-opacity duration-150">
            <div class="overflow-x-auto">
                <table class="w-full min-w-[980px] text-left">
                    <colgroup>
                        <col style="width: 27%">
                        <col style="width: 13%">
                        <col style="width: 9%">
                        <col style="width: 13%">
                        <col style="width: 10%">
                        <col style="width: 14%">
                        <col style="width: 14%">
                    </colgroup>

                    <thead class="bg-soft">
                        <tr class="text-[10px] font-semibold text-muted">
                            <th class="px-5 py-4">Nama Destinasi Usulan</th>
                            <th class="px-3 py-4">Kabupaten &amp;<br>Kecamatan</th>
                            <th class="px-3 py-4">Kategori</th>
                            <th class="px-3 py-4">Diajukan Oleh</th>
                            <th class="px-3 py-4">Tanggal<br>Masuk</th>
                            <th class="px-3 py-4">Status</th>
                            <th class="px-4 py-4 text-center">Aksi Verifikasi</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-line">
                        @forelse ($pengajuan as $item)
                            @php
                                $menunggu = $item->status === \App\Models\PengajuanDestinasi::MENUNGGU
                                    && $item->id_destinasi === null;
                                $ditolak = $item->status === \App\Models\PengajuanDestinasi::DITOLAK;

                                $skemaMaps = strtolower((string) parse_url((string) $item->link_maps, PHP_URL_SCHEME));
                                $linkMapsAman = in_array($skemaMaps, ['http', 'https'], true) ? $item->link_maps : '';

                                $statusLabel = match ($item->status) {
                                    \App\Models\PengajuanDestinasi::MENUNGGU => 'Menunggu Verifikasi',
                                    \App\Models\PengajuanDestinasi::DISETUJUI => 'Disetujui',
                                    \App\Models\PengajuanDestinasi::DITOLAK => 'Ditolak',
                                    default => ucfirst((string) $item->status),
                                };
                            @endphp
                            <tr class="transition-colors {{ $ditolak ? 'bg-red-50/30 hover:bg-red-50/60' : 'bg-white hover:bg-soft/40' }}">
                                <td class="px-5 py-4">
                                    @php
                                        $koordinat = null;
                                        if ($item->link_maps && preg_match('/(?:@|[?&](?:q|ll)=)(-?\d+\.\d+),\s*(-?\d+\.\d+)/', $item->link_maps, $cocok)) {
                                            $koordinat = $cocok[1] . ', ' . $cocok[2];
                                        }
                                    @endphp

                                    <div class="flex min-w-0 items-center gap-3">
                                        <span class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-soft-blue text-primary">
                                            <x-icon name="location_on" size="20" />
                                        </span>
                                        <div class="min-w-0">
                                            <div class="flex items-center gap-2">
                                                <p class="truncate text-xs font-semibold text-ink">{{ $item->nama_lokasi }}</p>

                                                @if ($item->status === \App\Models\PengajuanDestinasi::DISETUJUI)
                                                    <span class="inline-flex shrink-0 items-center rounded bg-mint/70 px-1.5 py-0.5 text-[9px] font-semibold text-primary">
                                                        {{ $item->destinasi && ! $item->destinasi->is_active ? 'Katalog Nonaktif' : 'Katalog Aktif' }}
                                                    </span>
                                                @elseif ($item->status === \App\Models\PengajuanDestinasi::DITOLAK)
                                                    <span class="inline-flex shrink-0 items-center rounded bg-red-100 px-1.5 py-0.5 text-[9px] font-semibold text-red-700">
                                                        Usulan Ditolak
                                                    </span>
                                                @elseif ($item->link_maps)
                                                    <span class="inline-flex shrink-0 items-center gap-1 rounded bg-mint/70 px-1.5 py-0.5 text-[9px] font-semibold text-primary">
                                                        <x-icon name="my_location" size="11" />
                                                        <span>Geo-Tagged</span>
                                                    </span>
                                                @endif
                                            </div>

                                            <div class="mt-1 flex items-center gap-1 truncate text-[10px] text-muted">
                                                <x-icon name="location_on" size="12" class="shrink-0 text-subtle" />
                                                @if ($item->link_maps)
                                                    <a href="{{ $item->link_maps }}" target="_blank" rel="noopener noreferrer" class="truncate hover:text-primary hover:underline">
                                                        {{ $koordinat ?? 'Lokasi terlampir' }}
                                                    </a>
                                                @else
                                                    <span>Lokasi belum ditandai</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-3 py-4">
                                    <p class="text-[11px] font-medium leading-snug text-ink">
                                        {{ $item->kecamatan?->kabupaten?->label ?? '-' }}
                                    </p>
                                    <p class="mt-0.5 text-[11px] leading-snug text-muted">
                                        Kec. {{ $item->kecamatan?->nama ?? '-' }}
                                    </p>
                                </td>

                                <td class="px-3 py-4">
                                    <span class="inline-flex max-w-full items-center rounded-md bg-soft-blue px-2 py-1 text-[10px] font-medium leading-tight text-ink">
                                        {{ $item->kategoriWisata?->nama ?? '-' }}
                                    </span>
                                </td>

                                <td class="px-3 py-4">
                                    <div class="flex min-w-0 items-center gap-2">
                                        <span class="flex size-7 shrink-0 items-center justify-center rounded-full bg-indigo-100 text-[10px] font-semibold text-indigo-700">
                                            {{ mb_strtoupper(mb_substr($item->pengaju?->username ?? '-', 0, 2)) }}
                                        </span>
                                        <div class="min-w-0">
                                            <p class="truncate text-[11px] font-semibold text-ink">
                                                {{ $item->pengaju?->username ? '@'.$item->pengaju->username : '-' }}
                                            </p>
                                            <p class="mt-0.5 truncate text-[10px] text-muted">Wisatawan Terdaftar</p>
                                        </div>
                                    </div>
                                </td>

                                <td class="whitespace-nowrap px-3 py-4">
                                    <p class="text-[10px] font-medium leading-snug text-ink">
                                        {{ $item->created_at?->locale('id')->translatedFormat('d M Y') ?? '-' }}
                                    </p>
                                    <p class="mt-1 text-[10px] leading-snug text-muted">
                                        {{ $item->created_at?->locale('id')->translatedFormat('H:i') ?? '-' }} WITA
                                    </p>
                                </td>

                                <td class="whitespace-nowrap px-3 py-4">
                                    <x-status-badge :status="$item->status" size="sm" />
                                </td>

                                <td class="whitespace-nowrap px-4 py-4">
                                    <div class="flex items-center justify-center gap-1.5">
                                        @if ($menunggu)
                                            {{-- Ikon mata: langsung membuka modal form Verifikasi & Tambah Destinasi --}}
                                            <button
                                                type="button"
                                                title="Periksa & Verifikasi"
                                                aria-label="Periksa dan verifikasi {{ $item->nama_lokasi }}"
                                                data-buka-verifikasi
                                                data-id="{{ $item->id_pengajuan }}"
                                                data-action="{{ route('admin.pengajuan.setujui', ['pengajuan' => $item->id_pengajuan]) }}"
                                                data-nama="{{ $item->nama_lokasi }}"
                                                data-kategori="{{ $item->id_kategori_wisata }}"
                                                data-kabupaten="{{ $item->kecamatan?->id_kabupaten }}"
                                                data-kecamatan="{{ $item->id_kecamatan }}"
                                                data-maps="{{ $linkMapsAman }}"
                                                data-pengaju="{{ $item->pengaju?->username ? '@'.$item->pengaju->username : '' }}"
                                                data-tanggal="{{ $item->created_at?->locale('id')->translatedFormat('d F Y, H:i') ? $item->created_at->locale('id')->translatedFormat('d F Y, H:i').' WITA' : '' }}"
                                                class="inline-flex size-7 items-center justify-center rounded-md bg-soft text-ink transition hover:bg-soft-blue hover:text-primary"
                                            >
                                                <x-icon name="visibility" size="15" />
                                            </button>

                                            {{-- Tombol Setujui --}}
                                            <button
                                                type="button"
                                                data-buka-verifikasi
                                                data-id="{{ $item->id_pengajuan }}"
                                                data-action="{{ route('admin.pengajuan.setujui', ['pengajuan' => $item->id_pengajuan]) }}"
                                                data-nama="{{ $item->nama_lokasi }}"
                                                data-kategori="{{ $item->id_kategori_wisata }}"
                                                data-kabupaten="{{ $item->kecamatan?->id_kabupaten }}"
                                                data-kecamatan="{{ $item->id_kecamatan }}"
                                                data-maps="{{ $linkMapsAman }}"
                                                data-pengaju="{{ $item->pengaju?->username ? '@'.$item->pengaju->username : '' }}"
                                                data-tanggal="{{ $item->created_at?->locale('id')->translatedFormat('d F Y, H:i') ? $item->created_at->locale('id')->translatedFormat('d F Y, H:i').' WITA' : '' }}"
                                                class="inline-flex items-center gap-1 rounded-md bg-primary px-2.5 py-1.5 text-[11px] font-semibold text-white transition hover:bg-primary/90"
                                            >
                                                <x-icon name="check_circle" size="14" />
                                                <span>Setujui</span>
                                            </button>

                                            {{-- Tombol Tolak --}}
                                            <button
                                                type="button"
                                                data-buka-tolak
                                                data-action="{{ route('admin.pengajuan.tolak', ['pengajuan' => $item->id_pengajuan]) }}"
                                                data-nama="{{ $item->nama_lokasi }}"
                                                class="inline-flex items-center gap-1 rounded-md px-2 py-1.5 text-[11px] font-semibold text-danger transition hover:bg-danger-soft"
                                            >
                                                <x-icon name="cancel" size="14" />
                                                <span>Tolak</span>
                                            </button>
                                        @else
                                            {{-- Status Disetujui / Ditolak: buka modal ringkasan read-only --}}
                                            <button
                                                type="button"
                                                title="Lihat ringkasan pengajuan"
                                                aria-label="Lihat ringkasan {{ $item->nama_lokasi }}"
                                                data-buka-info
                                                data-id="{{ $item->id_pengajuan }}"
                                                data-nama="{{ $item->nama_lokasi }}"
                                                data-kabupaten="{{ $item->kecamatan?->kabupaten?->label ?? '-' }}"
                                                data-kecamatan="{{ $item->kecamatan?->nama ?? '-' }}"
                                                data-kategori="{{ $item->kategoriWisata?->nama ?? '-' }}"
                                                data-pengaju="{{ $item->pengaju?->username ? '@'.$item->pengaju->username : '-' }}"
                                                data-tanggal="{{ $item->created_at?->locale('id')->translatedFormat('d F Y, H:i') ?? '-' }} WITA"
                                                data-maps="{{ $linkMapsAman }}"
                                                data-status="{{ $item->status }}"
                                                data-status-label="{{ $statusLabel }}"
                                                data-verifikator="{{ $item->verifikator?->username ? '@'.$item->verifikator->username : '-' }}"
                                                data-diverifikasi-pada="{{ $item->diverifikasi_pada?->locale('id')->translatedFormat('d F Y, H:i') ? $item->diverifikasi_pada->locale('id')->translatedFormat('d F Y, H:i').' WITA' : '-' }}"
                                                data-destinasi-nama="{{ $item->destinasi?->nama ?? '' }}"
                                                data-destinasi-id="{{ $item->destinasi?->id_destinasi ?? '' }}"
                                                class="inline-flex size-7 items-center justify-center rounded-md bg-soft text-ink transition hover:bg-soft-blue hover:text-primary"
                                            >
                                                <x-icon name="visibility" size="15" />
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-14 text-center">
                                    <div class="mx-auto flex max-w-sm flex-col items-center gap-2 text-muted">
                                        <span class="flex size-12 items-center justify-center rounded-full bg-soft text-primary">
                                            <x-icon name="inbox" size="24" />
                                        </span>
                                        <p class="font-heading text-sm font-bold text-ink">Tidak Ada Pengajuan</p>
                                        <p class="text-xs leading-relaxed text-muted">
                                            @if ($adaFilter)
                                                Tidak ada pengajuan yang sesuai dengan pencarian atau filter yang dipilih. Coba ubah kata kunci atau reset filter.
                                            @else
                                                Belum ada usulan tempat wisata baru dari wisatawan. Pengajuan yang masuk akan tampil di sini.
                                            @endif
                                        </p>
                                        @if ($adaFilter)
                                            <a href="{{ route('admin.pengajuan.index') }}" data-reset-pengajuan class="mt-1 inline-flex items-center gap-1 text-xs font-semibold text-primary hover:underline">
                                                <x-icon name="refresh" size="14" />
                                                Reset filter
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Footer dan pagination --}}
            <div class="flex flex-col gap-3 border-t border-line bg-soft px-4 py-3 sm:flex-row sm:items-center sm:justify-between sm:px-5">
                <p class="text-xs text-muted">
                    Menampilkan
                    <span class="font-semibold text-ink">{{ $pengajuan->firstItem() ?? 0 }}&ndash;{{ $pengajuan->lastItem() ?? 0 }}</span>
                    dari
                    <span class="font-semibold text-ink">{{ $pengajuan->total() }}</span>
                    total usulan destinasi
                </p>

                @if ($pengajuan->hasPages())
                    <nav aria-label="Navigasi halaman" class="flex flex-wrap items-center justify-end gap-1.5">
                        @if ($pengajuan->onFirstPage())
                            <span aria-disabled="true" class="flex size-7 items-center justify-center rounded-md bg-white/70 text-muted">
                                <x-icon name="chevron_left" size="16" />
                            </span>
                        @else
                            <a href="{{ $pengajuan->previousPageUrl() }}" aria-label="Halaman sebelumnya" class="flex size-7 items-center justify-center rounded-md bg-white text-primary transition hover:bg-primary/10">
                                <x-icon name="chevron_left" size="16" />
                            </a>
                        @endif

                        @foreach ($pengajuan->getUrlRange(max(1, $pengajuan->currentPage() - 2), min($pengajuan->lastPage(), $pengajuan->currentPage() + 2)) as $halaman => $url)
                            @if ($halaman === $pengajuan->currentPage())
                                <span aria-current="page" class="flex size-7 items-center justify-center rounded-md bg-primary text-xs font-semibold text-white">{{ $halaman }}</span>
                            @else
                                <a href="{{ $url }}" aria-label="Halaman {{ $halaman }}" class="flex size-7 items-center justify-center rounded-md bg-white text-xs font-semibold text-primary transition hover:bg-primary/10">{{ $halaman }}</a>
                            @endif
                        @endforeach

                        @if ($pengajuan->hasMorePages())
                            <a href="{{ $pengajuan->nextPageUrl() }}" aria-label="Halaman berikutnya" class="flex size-7 items-center justify-center rounded-md bg-white text-primary transition hover:bg-primary/10">
                                <x-icon name="chevron_right" size="16" />
                            </a>
                        @else
                            <span aria-disabled="true" class="flex size-7 items-center justify-center rounded-md bg-white/70 text-muted">
                                <x-icon name="chevron_right" size="16" />
                            </span>
                        @endif
                    </nav>
                @endif
            </div>
        </section>
    </div>

    <style>
        dialog.modal-swara::backdrop {
            background-color: rgba(17, 28, 45, 0.6);
        }
    </style>

    {{-- MODAL 1: RINGKASAN INFORMASI PENGAJUAN --}}
    <dialog
        id="modal-info-pengajuan"
        class="modal-swara m-auto max-h-[90vh] w-[calc(100%-24px)] max-w-[640px] overflow-hidden rounded-xl border border-line bg-white p-0 text-ink shadow-2xl open:flex open:flex-col"
        aria-labelledby="judul-modal-info"
        aria-modal="true"
    >
        <div class="flex shrink-0 items-start justify-between gap-3 border-b border-line bg-soft px-5 py-4">
            <div class="flex min-w-0 items-center gap-3">
                <span class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-white text-primary shadow-sm">
                    <x-icon name="location_on" size="20" />
                </span>
                <div class="min-w-0">
                    <p class="text-[10px] font-semibold uppercase tracking-[0.08em] text-muted">
                        Ringkasan Usulan <span id="info-id"></span>
                    </p>
                    <h2 id="judul-modal-info" class="break-words font-heading text-base font-bold leading-snug text-ink">-</h2>
                </div>
            </div>

            <button
                type="button"
                data-tutup-dialog="modal-info-pengajuan"
                aria-label="Tutup ringkasan"
                class="flex size-8 shrink-0 items-center justify-center rounded-lg text-muted transition hover:bg-white hover:text-ink"
            >
                <x-icon name="close" size="18" />
            </button>
        </div>

        <div class="min-h-0 flex-1 space-y-4 overflow-y-auto p-5">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <div>
                    <p class="text-[10px] font-semibold uppercase tracking-[0.06em] text-muted">Kabupaten / Kota</p>
                    <p id="info-kabupaten" class="mt-1 text-sm font-medium text-ink">-</p>
                </div>
                <div>
                    <p class="text-[10px] font-semibold uppercase tracking-[0.06em] text-muted">Kecamatan</p>
                    <p id="info-kecamatan" class="mt-1 text-sm font-medium text-ink">-</p>
                </div>
                <div>
                    <p class="text-[10px] font-semibold uppercase tracking-[0.06em] text-muted">Kategori Wisata</p>
                    <p class="mt-1">
                        <span id="info-kategori" class="inline-flex rounded-md bg-soft-blue px-2.5 py-1 text-[11px] font-semibold text-primary">-</span>
                    </p>
                </div>
                <div>
                    <p class="text-[10px] font-semibold uppercase tracking-[0.06em] text-muted">Status Pengajuan</p>
                    <p class="mt-1">
                        <span id="info-status-badge" class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-semibold">-</span>
                    </p>
                </div>
                <div>
                    <p class="text-[10px] font-semibold uppercase tracking-[0.06em] text-muted">Diajukan Oleh</p>
                    <p id="info-pengaju" class="mt-1 text-sm font-medium text-ink">-</p>
                </div>
                <div>
                    <p class="text-[10px] font-semibold uppercase tracking-[0.06em] text-muted">Tanggal Masuk</p>
                    <p id="info-tanggal" class="mt-1 text-sm font-medium text-ink">-</p>
                </div>
            </div>

            <div>
                <p class="text-[10px] font-semibold uppercase tracking-[0.06em] text-muted">Tautan Google Maps</p>
                <div class="mt-1">
                    <a
                        id="info-maps-link"
                        href="#"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="hidden items-start gap-1.5 break-all text-sm font-medium text-primary hover:underline"
                    >
                        <x-icon name="open_in_new" size="16" class="mt-0.5 shrink-0" />
                        <span id="info-maps-text"></span>
                    </a>
                    <span id="info-maps-kosong" class="text-sm text-muted">Tidak ada tautan valid</span>
                </div>
            </div>

            <div id="info-hasil-verifikasi" class="hidden rounded-lg border border-line bg-soft/60 p-3.5 text-xs">
                <p class="font-semibold text-ink">Informasi Hasil Verifikasi</p>
                <p class="mt-1 text-muted">
                    Diverifikasi oleh <span id="info-verifikator" class="font-semibold text-ink">-</span>
                    pada <span id="info-diverifikasi-pada" class="font-semibold text-ink">-</span>.
                </p>
                <p id="info-destinasi-terkait" class="mt-1 hidden font-medium text-primary"></p>
            </div>
        </div>

        <div class="flex shrink-0 flex-wrap items-center justify-end gap-2 border-t border-line bg-white px-5 py-3.5">
            <x-button
                type="button"
                variant="outline"
                size="sm"
                data-tutup-dialog="modal-info-pengajuan"
            >
                Tutup
            </x-button>

            <x-button
                type="button"
                id="info-tombol-verifikasi"
                variant="primary"
                size="sm"
                class="hidden"
            >
                <x-icon name="verified_user" size="16" />
                Lanjut Verifikasi
            </x-button>
        </div>
    </dialog>

    {{-- MODAL 2: VERIFIKASI & TAMBAH DESTINASI --}}
    <dialog
        id="modal-verifikasi"
        class="modal-swara m-auto max-h-[90vh] w-[calc(100%-24px)] max-w-[800px] overflow-hidden rounded-xl border border-line bg-white p-0 text-ink shadow-2xl open:flex open:flex-col"
        role="dialog"
        aria-labelledby="judul-modal-verifikasi"
        aria-describedby="deskripsi-modal-verifikasi"
        aria-modal="true"
    >
        {{-- Header modal --}}
        <div class="flex shrink-0 items-start justify-between gap-3 border-b border-line bg-soft px-5 py-4">
            <div class="flex min-w-0 items-center gap-3">
                <span class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-primary text-white shadow-sm">
                    <x-icon name="verified_user" size="20" />
                </span>

                <div class="min-w-0">
                    <h2 id="judul-modal-verifikasi" class="break-words font-heading text-sm font-bold leading-5 text-ink sm:text-base">
                        Verifikasi &amp; Tambah Destinasi{{ old('nama') ? ': '.old('nama') : '' }}
                    </h2>
                    <p id="deskripsi-modal-verifikasi" class="mt-0.5 text-[11px] leading-4 text-muted">
                        Lengkapi data destinasi resmi sebelum menyetujui pengajuan wisatawan.
                    </p>
                </div>
            </div>

            <button
                type="button"
                data-tutup-dialog="modal-verifikasi"
                aria-label="Tutup modal verifikasi"
                class="flex size-8 shrink-0 items-center justify-center rounded-lg text-muted transition hover:bg-white hover:text-ink"
            >
                <x-icon name="close" size="18" />
            </button>
        </div>

        <form
            id="form-verifikasi"
            method="POST"
            action="{{ $actionVerifikasiAwal }}"
            enctype="multipart/form-data"
            class="flex min-h-0 flex-1 flex-col"
        >
            @csrf
            <input type="hidden" id="verifikasi_pengajuan_id" name="_pengajuan_id" value="{{ old('_pengajuan_id', '') }}">

            <div class="min-h-0 flex-1 space-y-4 overflow-y-auto p-5">
                @if ($bukaModalVerifikasi && $errors->any())
                    <div class="rounded-lg border border-danger/30 bg-danger-soft/50 px-3.5 py-2.5 text-xs text-danger">
                        Periksa kembali isian formulir verifikasi di bawah ini.
                    </div>
                @endif

                {{-- Baris 1 --}}
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="verifikasi_nama" class="mb-1.5 block text-[11px] font-semibold text-ink">
                            Nama Tempat Wisata <span class="text-danger">*</span>
                        </label>
                        <input
                            id="verifikasi_nama"
                            name="nama"
                            type="text"
                            value="{{ old('nama', '') }}"
                            maxlength="150"
                            required
                            class="w-full rounded-lg border bg-soft/40 px-3 py-2.5 text-sm text-ink outline-none transition placeholder:text-subtle focus:border-primary focus:ring-2 focus:ring-primary/10 @error('nama') border-danger @else border-line @enderror"
                        >
                        @error('nama')
                            <p class="mt-1 text-[11px] text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="verifikasi_id_kategori_wisata" class="mb-1.5 block text-[11px] font-semibold text-ink">
                            Kategori Wisata Resmi <span class="text-danger">*</span>
                        </label>
                        <select
                            id="verifikasi_id_kategori_wisata"
                            name="id_kategori_wisata"
                            required
                            class="w-full rounded-lg border bg-soft/40 px-3 py-2.5 text-sm text-ink outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/10 @error('id_kategori_wisata') border-danger @else border-line @enderror"
                        >
                            <option value="">Pilih kategori</option>
                            @foreach ($kategoriWisata as $kategori)
                                <option
                                    value="{{ $kategori->id_kategori_wisata }}"
                                    @selected((string) old('id_kategori_wisata') === (string) $kategori->id_kategori_wisata)
                                >
                                    {{ $kategori->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_kategori_wisata')
                            <p class="mt-1 text-[11px] text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Baris 2 --}}
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                    <div>
                        <label for="verifikasi_id_kabupaten" class="mb-1.5 block text-[11px] font-semibold text-ink">
                            Kabupaten / Kota <span class="text-danger">*</span>
                        </label>
                        <select
                            id="verifikasi_id_kabupaten"
                            name="id_kabupaten"
                            required
                            class="w-full rounded-lg border bg-soft/40 px-3 py-2.5 text-sm text-ink outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/10 @error('id_kabupaten') border-danger @else border-line @enderror"
                        >
                            <option value="">Pilih kabupaten/kota</option>
                            @foreach ($kabupaten as $item)
                                <option
                                    value="{{ $item->id_kabupaten }}"
                                    @selected((string) old('id_kabupaten') === (string) $item->id_kabupaten)
                                >
                                    {{ $item->label }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_kabupaten')
                            <p class="mt-1 text-[11px] text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="verifikasi_id_kecamatan" class="mb-1.5 block text-[11px] font-semibold text-ink">
                            Kecamatan <span class="text-danger">*</span>
                        </label>
                        <select
                            id="verifikasi_id_kecamatan"
                            name="id_kecamatan"
                            required
                            class="w-full rounded-lg border bg-soft/40 px-3 py-2.5 text-sm text-ink outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/10 @error('id_kecamatan') border-danger @else border-line @enderror"
                        >
                            <option value="">Pilih kecamatan</option>
                            @foreach ($kecamatan as $item)
                                <option
                                    value="{{ $item->id_kecamatan }}"
                                    data-kabupaten="{{ $item->id_kabupaten }}"
                                    @selected((string) old('id_kecamatan') === (string) $item->id_kecamatan)
                                >
                                    {{ $item->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_kecamatan')
                            <p class="mt-1 text-[11px] text-danger">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Baris 3: tautan maps --}}
                <div>
                    <label for="verifikasi_link_maps" class="mb-1.5 block text-[11px] font-semibold text-ink">
                        Tautan Titik Lokasi Google Maps <span class="text-danger">*</span>
                    </label>
                    <div class="flex flex-col gap-2 sm:flex-row">
                        <input
                            id="verifikasi_link_maps"
                            name="link_maps"
                            type="url"
                            value="{{ old('link_maps', '') }}"
                            maxlength="500"
                            required
                            placeholder="https://maps.app.goo.gl/..."
                            class="min-w-0 flex-1 rounded-lg border bg-soft/40 px-3 py-2.5 text-sm text-ink outline-none transition placeholder:text-subtle focus:border-primary focus:ring-2 focus:ring-primary/10 @error('link_maps') border-danger @else border-line @enderror"
                        >
                        <button
                            type="button"
                            id="tombol-buka-maps"
                            class="inline-flex shrink-0 items-center justify-center gap-1.5 rounded-lg border border-line bg-white px-3 py-2.5 text-[11px] font-semibold text-primary transition hover:bg-soft disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            <x-icon name="open_in_new" size="14" />
                            Buka Maps
                        </button>
                    </div>
                    @error('link_maps')
                        <p class="mt-1 text-[11px] text-danger">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Baris 4: deskripsi --}}
                <div>
                    <div class="mb-1.5 flex items-center justify-between gap-2">
                        <label for="verifikasi_deskripsi" class="block text-[11px] font-semibold text-ink">
                            Deskripsi Destinasi Resmi <span class="text-danger">*</span>
                        </label>
                        <span id="hitung-deskripsi" class="text-[10px] font-medium tabular-nums text-subtle">
                            {{ mb_strlen((string) old('deskripsi', '')) }}/500
                        </span>
                    </div>
                    <textarea
                        id="verifikasi_deskripsi"
                        name="deskripsi"
                        rows="4"
                        maxlength="500"
                        required
                        placeholder="Jelaskan daya tarik lokasi, akses menuju lokasi, dan potensi wisata yang ditawarkan."
                        class="w-full rounded-lg border bg-soft/40 px-3 py-2.5 text-sm text-ink outline-none transition placeholder:text-subtle focus:border-primary focus:ring-2 focus:ring-primary/10 @error('deskripsi') border-danger @else border-line @enderror"
                    >{{ old('deskripsi', '') }}</textarea>
                    @error('deskripsi')
                        <p class="mt-1 text-[11px] text-danger">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Baris 5: foto utama --}}
                <div>
                    <div class="mb-1.5 flex flex-wrap items-center justify-between gap-1">
                        <label for="verifikasi_foto" class="block text-[11px] font-semibold text-ink">
                            Foto Potensi Resmi <span class="text-danger">*</span>
                            <span class="font-normal text-subtle">(Maksimal 1 Foto)</span>
                        </label>
                        <span class="text-[10px] text-subtle">JPG/PNG/WebP &middot; maks. 2 MB</span>
                    </div>

                    <input
                        id="verifikasi_foto"
                        name="foto"
                        type="file"
                        accept="image/jpeg,image/png,image/webp"
                        class="sr-only"
                    >

                    <label
                        for="verifikasi_foto"
                        id="area-tambah-foto"
                        class="flex cursor-pointer flex-col items-center justify-center gap-1.5 rounded-lg border border-dashed border-line bg-soft/40 px-4 py-6 text-center transition hover:border-primary hover:bg-soft"
                    >
                        <span class="flex size-9 items-center justify-center rounded-full bg-white text-primary shadow-sm">
                            <x-icon name="add_photo_alternate" size="20" />
                        </span>
                        <span class="text-xs font-semibold text-ink">Tambah Foto</span>
                        <span class="text-[10px] text-subtle">Pilih satu foto terbaik untuk publikasi destinasi.</span>
                    </label>

                    <div id="preview-foto" class="hidden items-center gap-3 rounded-lg border border-line bg-soft/40 p-3">
                        <img
                            id="gambar-preview-foto"
                            src=""
                            alt="Pratinjau foto destinasi"
                            class="h-20 w-28 shrink-0 rounded-md object-cover"
                        >
                        <div class="min-w-0">
                            <p id="nama-file-preview" class="truncate text-xs font-semibold text-ink">Foto siap diunggah</p>
                            <p class="mt-0.5 text-[10px] text-subtle">
                                Foto akan disimpan sebagai foto utama destinasi.
                            </p>
                            <label
                                for="verifikasi_foto"
                                class="mt-2 inline-flex cursor-pointer items-center gap-1 rounded-lg border border-line bg-white px-2.5 py-1.5 text-[10px] font-semibold text-primary transition hover:bg-soft"
                            >
                                <x-icon name="sync" size="13" />
                                Ganti Foto
                            </label>
                        </div>
                    </div>

                    @error('foto')
                        <p class="mt-1 text-[11px] text-danger">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Footer modal --}}
            <div class="flex shrink-0 flex-wrap items-center justify-end gap-2 border-t border-line bg-white px-5 py-3.5">
                <x-button
                    type="button"
                    variant="outline"
                    size="sm"
                    data-tutup-dialog="modal-verifikasi"
                >
                    Batal
                </x-button>

                <x-button
                    type="submit"
                    id="tombol-simpan-verifikasi"
                    variant="primary"
                    size="sm"
                >
                    <x-icon name="check" size="16" />
                    Simpan &amp; Setujui Destinasi
                </x-button>
            </div>
        </form>
    </dialog>

    {{-- MODAL 3: KONFIRMASI TOLAK PENGAJUAN --}}
    <dialog
        id="modal-tolak-pengajuan"
        class="modal-swara m-auto w-[calc(100%-24px)] max-w-[440px] overflow-hidden rounded-xl border border-line bg-white p-0 text-ink shadow-2xl"
        aria-labelledby="judul-modal-tolak"
        aria-modal="true"
    >
        <div class="p-5">
            <div class="flex items-start gap-3">
                <span class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-danger-soft text-danger">
                    <x-icon name="cancel" size="20" />
                </span>
                <div class="min-w-0">
                    <h2 id="judul-modal-tolak" class="font-heading text-base font-bold text-ink">
                        Tolak Pengajuan Lokasi?
                    </h2>
                    <p class="mt-1.5 text-xs leading-relaxed text-muted">
                        Apakah Anda yakin ingin menolak usulan lokasi
                        <span id="tolak-nama-lokasi" class="font-semibold text-ink">-</span>?
                        Tindakan ini akan memperbarui status menjadi ditolak dan tidak dapat dibatalkan.
                    </p>
                </div>
            </div>

            <form id="form-tolak-pengajuan" method="POST" action="#" class="mt-5 flex items-center justify-end gap-2">
                @csrf
                <x-button
                    type="button"
                    variant="outline"
                    size="sm"
                    data-tutup-dialog="modal-tolak-pengajuan"
                >
                    Batal
                </x-button>

                <x-button
                    type="submit"
                    id="tombol-konfirmasi-tolak"
                    variant="danger"
                    size="sm"
                >
                    Ya, Tolak Pengajuan
                </x-button>
            </form>
        </div>
    </dialog>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // --- LIVE SEARCH, FILTER & PAGINATION OTOMATIS ---
            const formFilter = document.getElementById('form-filter-pengajuan');
            const inputSearch = document.getElementById('search');
            const inputStatusHidden = document.getElementById('filter_status');
            const selectFilterKabupaten = document.getElementById('id_kabupaten');
            const selectFilterSort = document.getElementById('sort');
            const wadahTabStatus = document.getElementById('tab-status-pengajuan');
            const wadahTabel = document.getElementById('tabel-pengajuan-wrapper');

            let timerCari = null;
            let controllerFetch = null;

            const bangunUrlFilter = () => {
                if (!formFilter) return window.location.href;
                const url = new URL(formFilter.action, window.location.origin);
                const formData = new FormData(formFilter);

                for (const [kunci, nilai] of formData.entries()) {
                    const teks = String(nilai).trim();
                    if (teks === '' || (kunci === 'status' && teks === 'semua') || (kunci === 'sort' && teks === 'terbaru')) {
                        continue;
                    }
                    url.searchParams.set(kunci, teks);
                }

                return url.toString();
            };

            const muatDaftarPengajuan = async (targetUrl, modeHistory = 'push') => {
                if (!wadahTabel) {
                    window.location.href = targetUrl;
                    return;
                }

                if (controllerFetch) {
                    controllerFetch.abort();
                }
                controllerFetch = new AbortController();

                wadahTabel.classList.add('opacity-60');

                try {
                    const respon = await fetch(targetUrl, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' },
                        signal: controllerFetch.signal,
                    });

                    if (!respon.ok) {
                        throw new Error('Gagal memuat data pengajuan.');
                    }

                    const html = await respon.text();
                    const doc = new DOMParser().parseFromString(html, 'text/html');

                    const tabelBaru = doc.getElementById('tabel-pengajuan-wrapper');
                    const tabBaru = doc.getElementById('tab-status-pengajuan');

                    if (!tabelBaru) {
                        throw new Error('Kontainer tabel tidak ditemukan.');
                    }

                    wadahTabel.innerHTML = tabelBaru.innerHTML;
                    if (wadahTabStatus && tabBaru) {
                        wadahTabStatus.innerHTML = tabBaru.innerHTML;
                    }

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
                formFilter.addEventListener('submit', (event) => {
                    event.preventDefault();
                    clearTimeout(timerCari);
                    muatDaftarPengajuan(bangunUrlFilter(), 'replace');
                });
            }

            if (inputSearch) {
                inputSearch.addEventListener('input', () => {
                    clearTimeout(timerCari);
                    timerCari = setTimeout(() => {
                        muatDaftarPengajuan(bangunUrlFilter(), 'replace');
                    }, 400);
                });
            }

            [selectFilterKabupaten, selectFilterSort].forEach((dropdown) => {
                if (!dropdown) return;
                dropdown.addEventListener('change', () => {
                    clearTimeout(timerCari);
                    muatDaftarPengajuan(bangunUrlFilter(), 'push');
                });
            });

            window.addEventListener('popstate', () => {
                const params = new URLSearchParams(window.location.search);
                if (inputSearch) inputSearch.value = params.get('search') || '';
                if (inputStatusHidden) inputStatusHidden.value = params.get('status') || 'semua';
                if (selectFilterKabupaten) selectFilterKabupaten.value = params.get('id_kabupaten') || '';
                if (selectFilterSort) selectFilterSort.value = params.get('sort') || 'terbaru';
                muatDaftarPengajuan(window.location.href, 'none');
            });

            // --- PENGELOLAAN MODAL DIALOG ---
            const semuaDialog = ['modal-info-pengajuan', 'modal-verifikasi', 'modal-tolak-pengajuan']
                .map((id) => document.getElementById(id))
                .filter(Boolean);

            const perbaruiKunciScroll = () => {
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
                perbaruiKunciScroll();
            };

            const tutupDialog = (dialog) => {
                if (!dialog) return;
                if (typeof dialog.close === 'function') {
                    if (dialog.open) dialog.close();
                } else {
                    dialog.removeAttribute('open');
                }
                perbaruiKunciScroll();
            };

            semuaDialog.forEach((dialog) => {
                dialog.addEventListener('click', (event) => {
                    if (event.target === dialog) {
                        tutupDialog(dialog);
                    }
                });
                dialog.addEventListener('close', perbaruiKunciScroll);
            });

            // Elemen Modal Verifikasi
            const modalVerifikasi = document.getElementById('modal-verifikasi');
            const formVerifikasi = document.getElementById('form-verifikasi');
            const judulVerifikasi = document.getElementById('judul-modal-verifikasi');
            const deskripsiSubModal = document.getElementById('deskripsi-modal-verifikasi');
            const inputPengajuanId = document.getElementById('verifikasi_pengajuan_id');
            const inputNama = document.getElementById('verifikasi_nama');
            const selectKategori = document.getElementById('verifikasi_id_kategori_wisata');
            const selectKabupaten = document.getElementById('verifikasi_id_kabupaten');
            const selectKecamatan = document.getElementById('verifikasi_id_kecamatan');
            const inputLinkMaps = document.getElementById('verifikasi_link_maps');
            const inputDeskripsi = document.getElementById('verifikasi_deskripsi');
            const hitungDeskripsi = document.getElementById('hitung-deskripsi');
            const inputFoto = document.getElementById('verifikasi_foto');
            const areaTambahFoto = document.getElementById('area-tambah-foto');
            const areaPreviewFoto = document.getElementById('preview-foto');
            const gambarPreview = document.getElementById('gambar-preview-foto');
            const namaFilePreview = document.getElementById('nama-file-preview');
            const tombolMaps = document.getElementById('tombol-buka-maps');
            const tombolSimpan = document.getElementById('tombol-simpan-verifikasi');

            const semuaKecamatan = selectKecamatan
                ? Array.from(selectKecamatan.options)
                    .filter((opsi) => opsi.dataset.kabupaten)
                    .map((opsi) => ({
                        value: opsi.value,
                        label: opsi.textContent.trim(),
                        kabupaten: opsi.dataset.kabupaten,
                    }))
                : [];

            const sinkronkanKecamatan = (kecamatanTarget = null) => {
                if (!selectKabupaten || !selectKecamatan) return;
                const idKab = selectKabupaten.value;
                const terpilih = kecamatanTarget !== null ? String(kecamatanTarget) : selectKecamatan.value;

                selectKecamatan.innerHTML = '<option value="">Pilih kecamatan</option>';

                semuaKecamatan
                    .filter((opsi) => opsi.kabupaten === idKab)
                    .forEach((opsi) => {
                        const el = document.createElement('option');
                        el.value = opsi.value;
                        el.textContent = opsi.label;
                        el.dataset.kabupaten = opsi.kabupaten;
                        if (opsi.value === terpilih) {
                            el.selected = true;
                        }
                        selectKecamatan.appendChild(el);
                    });
            };

            if (selectKabupaten) {
                selectKabupaten.addEventListener('change', () => sinkronkanKecamatan(''));
                sinkronkanKecamatan();
            }

            const perbaruiHitungDeskripsi = () => {
                if (inputDeskripsi && hitungDeskripsi) {
                    hitungDeskripsi.textContent = inputDeskripsi.value.length + '/500';
                }
            };

            if (inputDeskripsi) {
                inputDeskripsi.addEventListener('input', perbaruiHitungDeskripsi);
                perbaruiHitungDeskripsi();
            }

            const urlValid = (nilai) => {
                try {
                    const url = new URL(nilai);
                    return ['http:', 'https:'].includes(url.protocol) ? url.href : null;
                } catch (e) {
                    return null;
                }
            };

            const perbaruiTombolMaps = () => {
                if (inputLinkMaps && tombolMaps) {
                    tombolMaps.disabled = urlValid(inputLinkMaps.value) === null;
                }
            };

            if (inputLinkMaps && tombolMaps) {
                tombolMaps.addEventListener('click', () => {
                    const url = urlValid(inputLinkMaps.value);
                    if (url) window.open(url, '_blank', 'noopener,noreferrer');
                });
                inputLinkMaps.addEventListener('input', perbaruiTombolMaps);
                perbaruiTombolMaps();
            }

            let urlPreview = null;
            const resetPreviewFoto = () => {
                if (urlPreview) {
                    URL.revokeObjectURL(urlPreview);
                    urlPreview = null;
                }
                if (inputFoto) inputFoto.value = '';
                if (gambarPreview) gambarPreview.src = '';
                if (areaPreviewFoto) {
                    areaPreviewFoto.classList.add('hidden');
                    areaPreviewFoto.classList.remove('flex');
                }
                if (areaTambahFoto) {
                    areaTambahFoto.classList.remove('hidden');
                }
            };

            if (inputFoto) {
                inputFoto.addEventListener('change', () => {
                    const file = inputFoto.files && inputFoto.files[0];
                    if (!file) return;

                    if (urlPreview) URL.revokeObjectURL(urlPreview);
                    urlPreview = URL.createObjectURL(file);

                    if (gambarPreview) gambarPreview.src = urlPreview;
                    if (namaFilePreview) namaFilePreview.textContent = file.name;
                    if (areaTambahFoto) areaTambahFoto.classList.add('hidden');
                    if (areaPreviewFoto) {
                        areaPreviewFoto.classList.remove('hidden');
                        areaPreviewFoto.classList.add('flex');
                    }
                });
            }

            const bukaModalVerifikasiDariData = (data) => {
                if (!modalVerifikasi || !formVerifikasi) return;

                formVerifikasi.action = data.action || '#';
                if (inputPengajuanId) inputPengajuanId.value = data.id || '';
                if (judulVerifikasi) {
                    judulVerifikasi.textContent = 'Verifikasi & Tambah Destinasi: ' + (data.nama || '');
                }
                if (deskripsiSubModal) {
                    const infoPengaju = data.pengaju ? 'Diusulkan oleh ' + data.pengaju : '';
                    const infoTanggal = data.tanggal ? ' (' + data.tanggal + ')' : '';
                    deskripsiSubModal.textContent = infoPengaju
                        ? infoPengaju + infoTanggal + '. Lengkapi data destinasi resmi sebelum menyetujui.'
                        : 'Lengkapi data destinasi resmi sebelum menyetujui pengajuan.';
                }

                if (inputNama) inputNama.value = data.nama || '';
                if (selectKategori) selectKategori.value = data.kategori || '';
                if (selectKabupaten) selectKabupaten.value = data.kabupaten || '';
                sinkronkanKecamatan(data.kecamatan || '');
                if (inputLinkMaps) inputLinkMaps.value = data.maps || '';
                if (inputDeskripsi) inputDeskripsi.value = '';

                perbaruiHitungDeskripsi();
                perbaruiTombolMaps();
                resetPreviewFoto();
                bukaDialog(modalVerifikasi);
            };

            if (modalVerifikasi && formVerifikasi) {
                modalVerifikasi.addEventListener('cancel', (event) => {
                    if (formVerifikasi.dataset.mengirim === '1') {
                        event.preventDefault();
                    }
                });

                if (tombolSimpan) {
                    formVerifikasi.addEventListener('submit', (event) => {
                        if (formVerifikasi.dataset.mengirim === '1') {
                            event.preventDefault();
                            return;
                        }
                        formVerifikasi.dataset.mengirim = '1';
                        tombolSimpan.disabled = true;
                        tombolSimpan.textContent = 'Menyimpan...';
                        tombolSimpan.setAttribute('aria-busy', 'true');
                    });
                }
            }

            // Elemen Modal Tolak
            const modalTolak = document.getElementById('modal-tolak-pengajuan');
            const formTolak = document.getElementById('form-tolak-pengajuan');
            const tolakNamaLokasi = document.getElementById('tolak-nama-lokasi');
            const tombolKonfirmasiTolak = document.getElementById('tombol-konfirmasi-tolak');

            if (formTolak && tombolKonfirmasiTolak) {
                formTolak.addEventListener('submit', (event) => {
                    if (formTolak.dataset.mengirim === '1') {
                        event.preventDefault();
                        return;
                    }
                    formTolak.dataset.mengirim = '1';
                    tombolKonfirmasiTolak.disabled = true;
                    tombolKonfirmasiTolak.textContent = 'Memproses...';
                });
            }

            // Elemen Modal Info
            const modalInfo = document.getElementById('modal-info-pengajuan');
            const tombolInfoVerifikasi = document.getElementById('info-tombol-verifikasi');
            let idPengajuanInfoAktif = null;

            const bukaModalInfoDariData = (d) => {
                if (!modalInfo) return;
                idPengajuanInfoAktif = d.id || null;

                document.getElementById('info-id').textContent = d.id ? '#' + d.id : '';
                document.getElementById('judul-modal-info').textContent = d.nama || '-';
                document.getElementById('info-kabupaten').textContent = d.kabupaten || '-';
                document.getElementById('info-kecamatan').textContent = d.kecamatan || '-';
                document.getElementById('info-kategori').textContent = d.kategori || '-';
                document.getElementById('info-pengaju').textContent = d.pengaju || '-';
                document.getElementById('info-tanggal').textContent = d.tanggal || '-';

                const badge = document.getElementById('info-status-badge');
                badge.textContent = d.statusLabel || '-';
                badge.className = 'inline-flex items-center rounded-full px-2.5 py-1 text-[11px] font-semibold ';
                if (d.status === 'disetujui') {
                    badge.className += 'bg-mint text-primary';
                } else if (d.status === 'ditolak') {
                    badge.className += 'bg-danger-soft text-danger';
                } else {
                    badge.className += 'bg-peach text-secondary-ink';
                }

                const linkEl = document.getElementById('info-maps-link');
                const textEl = document.getElementById('info-maps-text');
                const kosongEl = document.getElementById('info-maps-kosong');
                if (d.maps) {
                    linkEl.href = d.maps;
                    textEl.textContent = d.maps;
                    linkEl.classList.remove('hidden');
                    linkEl.classList.add('inline-flex');
                    kosongEl.classList.add('hidden');
                } else {
                    linkEl.classList.add('hidden');
                    linkEl.classList.remove('inline-flex');
                    kosongEl.classList.remove('hidden');
                }

                const boxHasil = document.getElementById('info-hasil-verifikasi');
                const teksDestinasi = document.getElementById('info-destinasi-terkait');
                if (d.status === 'disetujui' || d.status === 'ditolak') {
                    document.getElementById('info-verifikator').textContent = d.verifikator || '-';
                    document.getElementById('info-diverifikasi-pada').textContent = d.diverifikasiPada || '-';
                    boxHasil.classList.remove('hidden');

                    if (d.status === 'disetujui' && d.destinasiNama) {
                        teksDestinasi.textContent = 'Destinasi resmi terdaftar: ' + d.destinasiNama + (d.destinasiId ? ' (#' + d.destinasiId + ')' : '');
                        teksDestinasi.classList.remove('hidden');
                    } else {
                        teksDestinasi.classList.add('hidden');
                    }
                } else {
                    boxHasil.classList.add('hidden');
                }

                if (tombolInfoVerifikasi) {
                    if (d.status === 'menunggu_verifikasi') {
                        tombolInfoVerifikasi.classList.remove('hidden');
                    } else {
                        tombolInfoVerifikasi.classList.add('hidden');
                    }
                }

                bukaDialog(modalInfo);
            };

            if (tombolInfoVerifikasi) {
                tombolInfoVerifikasi.addEventListener('click', () => {
                    tutupDialog(modalInfo);
                    if (!idPengajuanInfoAktif) return;
                    const tombolBaris = document.querySelector('[data-buka-verifikasi][data-id="' + idPengajuanInfoAktif + '"]');
                    if (tombolBaris) {
                        bukaModalVerifikasiDariData(tombolBaris.dataset);
                    }
                });
            }

            // Event delegation global agar tetap berfungsi setelah live search / filter AJAX
            document.addEventListener('click', (event) => {
                const tombolTutup = event.target.closest('[data-tutup-dialog]');
                if (tombolTutup) {
                    tutupDialog(document.getElementById(tombolTutup.dataset.tutupDialog));
                    return;
                }

                const tombolVerifikasi = event.target.closest('[data-buka-verifikasi]');
                if (tombolVerifikasi) {
                    bukaModalVerifikasiDariData(tombolVerifikasi.dataset);
                    return;
                }

                const tombolTolak = event.target.closest('[data-buka-tolak]');
                if (tombolTolak && modalTolak && formTolak) {
                    formTolak.action = tombolTolak.dataset.action || '#';
                    if (tolakNamaLokasi) tolakNamaLokasi.textContent = '"' + (tombolTolak.dataset.nama || '') + '"';
                    bukaDialog(modalTolak);
                    return;
                }

                const tombolInfo = event.target.closest('[data-buka-info]');
                if (tombolInfo) {
                    bukaModalInfoDariData(tombolInfo.dataset);
                    return;
                }

                const linkTab = event.target.closest('[data-tab-status]');
                if (linkTab) {
                    event.preventDefault();
                    clearTimeout(timerCari);
                    if (inputStatusHidden) {
                        inputStatusHidden.value = linkTab.dataset.tabStatus || 'semua';
                    }
                    muatDaftarPengajuan(linkTab.href, 'push');
                    return;
                }

                const linkReset = event.target.closest('#tombol-reset-filter, [data-reset-pengajuan]');
                if (linkReset) {
                    event.preventDefault();
                    clearTimeout(timerCari);
                    if (inputSearch) inputSearch.value = '';
                    if (inputStatusHidden) inputStatusHidden.value = 'semua';
                    if (selectFilterKabupaten) selectFilterKabupaten.value = '';
                    if (selectFilterSort) selectFilterSort.value = 'terbaru';
                    muatDaftarPengajuan(linkReset.href, 'push');
                    return;
                }

                const linkPagination = event.target.closest('nav[aria-label="Navigasi halaman"] a');
                if (linkPagination) {
                    event.preventDefault();
                    muatDaftarPengajuan(linkPagination.href, 'push');
                }
            });

            // Buka otomatis modal verifikasi jika validasi server gagal atau dibuka dari dashboard (?verifikasi=ID)
            @if ($bukaModalVerifikasi)
                bukaDialog(modalVerifikasi);
            @elseif ($verifikasiId > 0)
                const tombolTarget = document.querySelector('[data-buka-verifikasi][data-id="{{ $verifikasiId }}"]');
                if (tombolTarget) {
                    bukaModalVerifikasiDariData(tombolTarget.dataset);
                }
            @endif
        });
    </script>
    @endpush
</x-layouts.admin>