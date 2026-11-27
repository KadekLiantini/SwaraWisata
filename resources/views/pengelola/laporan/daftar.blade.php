
<x-layouts.pengelola title="Daftar Pelaporan">
    <div class="space-y-6">

        {{-- Header halaman --}}
        <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-center">
            <div>
                <h1 class="text-2xl font-bold text-ink">
                    Daftar Laporan Kerusakan Destinasi
                </h1>
                <p class="mt-2 text-sm text-muted">
                    Kelola dan tindak lanjuti seluruh aduan fasilitas wisatawan yang masuk ke Kawasan Wisata Pantai Kuta
                </p>
            </div>

            {{-- Ganti href setelah route panduan tersedia --}}
            <a href="{{ route('pengelola.info') }}"
            class="inline-flex items-center justify-center gap-2 rounded-lg border border-none bg-soft-strong px-4 py-2.5 text-sm font-semibold text-ink hover:bg-soft-blue">
                <img src="{{ asset('images/icon-buku.svg') }}" alt="" aria-hidden="true" class="h-5 w-5">
                Panduan Penanganan
            </a>
        </div>
        
        {{-- Kartu ringkasan laporan --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

            {{-- Perlu Ditinjau Segera --}}
            <div class="rounded-xl bg-white p-5 shadow-md">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm text-muted font-semibold">Perlu Ditinjau Segera</p>
                        <p class="mt-2 text-3xl font-bold text-danger">
                            {{ $perluDitinjau }}
                        </p>
                    </div>
                    <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-danger-soft">
                        <img src="{{ asset('images/icon-lonceng.svg') }}"
                            alt=""
                            class="h-6 w-6">
                    </div>
                </div>
            </div>

            {{-- Dalam Peninjauan --}}
            <div class="rounded-xl bg-white p-5 shadow-md">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm text-muted font-semibold">Dalam Peninjauan</p>
                        <p class="mt-2 text-3xl font-bold text-ink">
                            {{ $dalamPeninjauan }}
                        </p>
                    </div>
                    <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-soft-strong">
                        <img src="{{ asset('images/icon-mata.svg') }}"
                            alt=""
                            class="h-6 w-6">
                    </div>
                </div>
            </div>

            {{-- Sedang Diperbaiki --}}
            <div class="rounded-xl bg-white p-5 shadow-md">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm text-muted font-semibold">Sedang Diperbaiki</p>
                        <p class="mt-2 text-3xl font-bold text-secondary-ink">
                            {{ $sedangDiperbaiki }}
                        </p>
                    </div>
                    <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-peach">
                        <img src="{{ asset('images/icon-tools.svg') }}"
                            alt=""
                            class="h-6 w-6">
                    </div>
                </div>
            </div>

            {{-- Selesai Diverifikasi --}}
            <div class="rounded-xl bg-white p-5 shadow-md">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm text-muted font-semibold">Selesai Diverifikasi</p>
                        <p class="mt-2 text-3xl font-bold text-primary">
                            {{ $selesaiDiverifikasi }}
                        </p>
                    </div>
                    <div class="flex h-11 w-11 items-center justify-center rounded-lg bg-mint/50">
                        <img src="{{ asset('images/icon-selesai-ditangani.svg') }}"
                            alt=""
                            class="h-6 w-6">
                    </div>
                </div>
            </div>

        </div>


{{-- Daftar laporan --}}
<x-card padding="md">

    {{-- Header tabel --}}
    <div class="flex flex-col justify-between gap-2 lg:flex-row lg:items-start">
        <div>
            <h2 class="font-heading text-lg font-bold text-ink">
                Seluruh Laporan
            </h2>
            <p class="mt-1 text-sm text-muted">
                Cari dan kelola laporan kerusakan fasilitas destinasi.
            </p>
        </div>
    </div>

    {{-- Form pencarian dan filter --}}
    {{-- Form pencarian dan filter --}}
<form
    id="filter-laporan"
    action="{{ route('pengelola.laporan.index') }}"
    method="GET"
    class="mt-3 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between"
>
    {{-- Pencarian judul, ID, atau pelapor --}}
    <div class="relative w-full sm:max-w-[360px]">
        <x-icon
            name="search"
            class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted"
        />

        <input
            id="search-laporan"
            type="text"
            name="search"
            value="{{ $search }}"
            placeholder="Cari judul, ID, atau pelapor..."
            class="w-full rounded-xl border border-transparent bg-soft py-2 pl-9 pr-3 text-sm text-ink placeholder:text-muted focus:border-primary/90 focus:outline-none focus:ring-1 focus:ring-primary/90"
        />
    </div>

    {{-- Dropdown filter --}}
    <div class="flex flex-wrap items-center gap-2">

        {{-- Filter status --}}
        <div class="relative min-w-[155px]">
            <select
                name="filter"
                onchange="this.form.submit()"
                class="w-full appearance-none rounded-xl border border-transparent bg-soft px-3 py-2 pr-9 text-sm font-medium text-ink focus:border-primary/90 focus:outline-none focus:ring-1 focus:ring-primary/90"
            >
                <option value="semua" @selected($filter === 'semua')>
                    Semua Status
                </option>
                <option value="dilihat" @selected($filter === 'dilihat')>
                    Dilihat
                </option>
                <option value="ditindaklanjuti" @selected($filter === 'ditindaklanjuti')>
                    Ditindaklanjuti
                </option>
                <option value="selesai" @selected($filter === 'selesai')>
                    Selesai
                </option>
            </select>

            <x-icon
                name="keyboard_arrow_down"
                class="pointer-events-none absolute right-2 top-1/2 h-4 w-4 -translate-y-1/2 text-muted"
            />
        </div>

        {{-- Filter kategori --}}
        <div class="relative min-w-[155px]">
            <select
                name="kategori"
                onchange="this.form.submit()"
                class="w-full appearance-none rounded-xl border border-transparent bg-soft px-3 py-2 pr-9 text-sm font-medium text-ink focus:border-primary/90 focus:outline-none focus:ring-1 focus:ring-primary/90"
            >
                <option value="">Semua Kategori</option>

                @foreach ($daftarKategori as $itemKategori)
                    <option
                        value="{{ $itemKategori->id_kategori_kerusakan }}"
                        @selected((string) $kategori === (string) $itemKategori->id_kategori_kerusakan)
                    >
                        {{ $itemKategori->nama }}
                    </option>
                @endforeach
            </select>

            <x-icon
                name="keyboard_arrow_down"
                class="pointer-events-none absolute right-2 top-1/2 h-4 w-4 -translate-y-1/2 text-muted"
            />
        </div>

        {{-- Pengurutan --}}
        <div class="relative min-w-[115px]">
            <select
                name="sort"
                onchange="this.form.submit()"
                class="w-full appearance-none rounded-xl border border-transparent bg-soft px-3 py-2 pr-9 text-sm font-medium text-ink focus:border-primary/90 focus:outline-none focus:ring-1 focus:ring-primary/90"
            >
                <option value="terbaru" @selected($sort === 'terbaru')>
                    Terbaru
                </option>
                <option value="terlama" @selected($sort === 'terlama')>
                    Terlama
                </option>
            </select>

            <x-icon
                name="sort"
                class="pointer-events-none absolute right-2 top-1/2 h-4 w-4 -translate-y-1/2 text-muted"
            />
        </div>

    </div>
</form>

    {{-- Tabel laporan --}}
    <div class="mt-3 w-full overflow-x-auto">
        <table class="w-full min-w-[1100px] text-left text-sm">

            {{-- Header kolom --}}
            <thead>
                <tr class="border-b border-line bg-soft/50 text-[11px] uppercase tracking-wide text-muted">
                    <th class="px-3 py-3 font-bold">Bukti / ID</th>
                    <th class="px-3 py-3 font-bold">Judul Laporan</th>
                    <th class="px-3 py-3 font-bold">Kategori</th>
                    <th class="px-3 py-3 font-bold">Waktu Masuk</th>
                    <th class="px-3 py-3 font-bold">Pelapor</th>
                    <th class="px-3 py-3 font-bold">Status Penanganan</th>
                    <th class="px-3 py-3 text-right font-bold">Tindakan</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-line">

                @forelse ($laporan as $item)
                    @php
                        $fotoUtama = $item->foto->first();

                        $statusLabel = match ($item->status) {
                            'belum_dilihat' => 'Belum Dilihat',
                            'dilihat' => 'Dilihat',
                            'ditindaklanjuti' => 'Ditindaklanjuti',
                            'selesai' => 'Selesai',
                            default => 'Tidak diketahui',
                        };

                        $statusDot = match ($item->status) {
                            'belum_dilihat' => 'bg-danger',
                            'dilihat' => 'bg-primary',
                            'ditindaklanjuti' => 'bg-secondary-ink',
                            'selesai' => 'bg-primary',
                            default => 'bg-subtle',
                        };

                        $statusBadge = match ($item->status) {
                            'belum_dilihat' => 'bg-danger-soft text-danger',
                            'dilihat' => 'bg-primary/20 text-primary',
                            'ditindaklanjuti' => 'bg-secondary/20 text-secondary-ink',
                            'selesai' => 'bg-mint text-primary',
                            default => 'bg-soft text-muted',
                        };

                        $kategoriIcon = $item->kategoriKerusakan?->icon() ?? 'category';

                        $waktuMasuk = $item->created_at
                            ? $item->created_at->copy()->timezone('Asia/Makassar')
                            : null;

                        $fotoProfil = data_get($item->pelapor, 'foto_profil');

                        $username = $item->pelapor?->username ?? 'Tidak diketahui';

                        $inisial = strtoupper(substr($username, 0, 1));

                        $lokasiDetail = data_get($item, 'lokasi_spesifik');
                    @endphp

                    <tr class="text-sm transition-colors hover:bg-soft/30">

                        {{-- Bukti dan ID --}}
                        <td class="whitespace-nowrap px-3 py-4">
                            <div class="flex items-center gap-3">
                                <span class="font-semibold text-subtle">
                                    #{{ $item->id_laporan }}
                                </span>

                                @if ($fotoUtama)
                                    <img
                                        src="{{ $fotoUtama->url }}"
                                        alt="Bukti foto laporan {{ $item->id_laporan }}"
                                        class="h-12 w-12 shrink-0 rounded-lg border border-line object-cover"
                                        loading="lazy"
                                    >
                                @else
                                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-soft">
                                        <x-icon name="image" class="h-5 w-5 text-muted" />
                                    </div>
                                @endif
                            </div>
                        </td>

                        {{-- Judul laporan dan lokasi detail --}}
                        <td class="max-w-xs px-3 py-4">
                            <p class="font-semibold text-ink">
                                {{ $item->judul }}
                            </p>
                            <p class="mt-1 truncate text-xs text-subtle">
                                {{ $lokasiDetail ?: 'Lokasi detail belum tersedia' }}
                            </p>
                        </td>

                        {{-- Kategori --}}
                        <td class="whitespace-nowrap px-3 py-4">
                            <span class="inline-flex items-center gap-2 rounded-lg bg-soft px-3 py-2 text-xs font-semibold text-muted">
                                <x-icon
                                    :name="$kategoriIcon"
                                    class="h-4 w-4"
                                />
                                {{ $item->kategoriKerusakan?->nama ?? 'Tidak diketahui' }}
                            </span>
                        </td>

                        {{-- Waktu masuk --}}
                        <td class="whitespace-nowrap px-3 py-4">
                            @if ($waktuMasuk)
                                <p class="text-xs font-semibold text-ink">
                                    {{ $waktuMasuk->locale('id')->format('d M Y') }}
                                </p>

                                <p class="mt-1 text-xs text-muted">
                                    {{ $waktuMasuk->format('H:i') }} WITA
                                </p>

                                <p class="mt-1 text-[11px] font-medium text-primary">
                                    {{ $waktuMasuk->locale('id')->diffForHumans() }}
                                </p>
                            @else
                                <span class="text-xs text-muted">-</span>
                            @endif
                        </td>

                        {{-- Pelapor --}}
                        <td class="whitespace-nowrap px-3 py-4">
                            <div class="flex items-center gap-2.5">
                                @if ($fotoProfil)
                                    <img
                                        src="{{ asset('storage/' . $fotoProfil) }}"
                                        alt="Foto profil {{ $username }}"
                                        class="h-9 w-9 shrink-0 rounded-full object-cover"
                                    >
                                @else
                                    <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-soft-strong text-xs font-bold text-muted">
                                        {{ $inisial }}
                                    </div>
                                @endif

                                <span class="text-xs font-semibold text-ink">
                                    {{ '@' . $username }}
                                </span>
                            </div>
                        </td>

                        {{-- Status penanganan --}}
                        <td class="whitespace-nowrap px-3 py-4">
                            <span class="inline-flex items-center gap-2 whitespace-nowrap rounded-full px-3 py-1.5 text-xs font-semibold {{ $statusBadge }}">
                                <span class="h-2 w-2 shrink-0 rounded-full {{ $statusDot }}"></span>
                                {{ $statusLabel }}
                            </span>
                        </td>

                        {{-- Tindakan --}}
                        <td class="whitespace-nowrap px-3 py-4">
                            <div class="flex justify-end">
                                <a
                                    href="{{ route('pengelola.laporan.show', $item->id_laporan) }}"
                                    class="inline-flex items-center gap-2 rounded-lg bg-soft px-3 py-2 text-xs font-semibold text-ink transition hover:bg-soft-blue"
                                >
                                    Lihat Detail
                                    <x-icon name="arrow_forward" class="h-4 w-4" />
                                </a>
                            </div>
                        </td>

                    </tr>

                @empty
                    <tr>
                        <td colspan="7" class="px-3 py-10 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <x-icon name="inbox" class="h-8 w-8 text-subtle" />

                                <p class="text-sm font-semibold text-ink">
                                    Belum ada laporan ditemukan
                                </p>

                                <p class="text-xs text-muted">
                                    Coba ubah kata kunci atau filter pencarian.
                                </p>
                            </div>
                        </td>
                    </tr>
                @endforelse

            </tbody>
        </table>
    </div>

    {{-- Footer jumlah laporan dan pagination --}}
    <div class="flex flex-col justify-between gap-4 px-3 py-5 sm:flex-row sm:items-center">

        <p class="text-sm text-muted">
            Menampilkan
            {{ $laporan->firstItem() ?? 0 }}
            -
            {{ $laporan->lastItem() ?? 0 }}
            dari
            {{ $laporan->total() }}
            laporan kerusakan
        </p>

        {{-- Pagination --}}
        @if ($laporan->lastPage() > 1)
            @php
                $currentPage = $laporan->currentPage();
                $lastPage = $laporan->lastPage();

                $startPage = max(1, $currentPage - 1);
                $endPage = min($lastPage, $currentPage + 2);
            @endphp

            <nav class="flex items-center gap-2" aria-label="Pagination">

                {{-- Halaman sebelumnya --}}
                <a
                    href="{{ $laporan->previousPageUrl() ?? '#' }}"
                    @if ($laporan->onFirstPage()) aria-disabled="true" @endif
                    class="flex h-10 w-10 items-center justify-center rounded-lg text-muted transition
                    {{ $laporan->onFirstPage() ? 'pointer-events-none opacity-40' : 'hover:bg-soft' }}"
                >
                    <x-icon name="arrow_back" class="h-5 w-5" />
                </a>

                {{-- Halaman pertama --}}
                @if ($startPage > 1)
                    <a
                        href="{{ $laporan->url(1) }}"
                        class="flex h-10 min-w-10 items-center justify-center rounded-lg text-sm font-medium text-muted transition hover:bg-soft"
                    >
                        1
                    </a>

                    @if ($startPage > 2)
                        <span class="px-1 text-subtle">...</span>
                    @endif
                @endif

                {{-- Nomor halaman --}}
                @foreach (range($startPage, $endPage) as $page)
                    <a
                        href="{{ $laporan->url($page) }}"
                        @if ($page === $currentPage) aria-current="page" @endif
                        class="flex h-10 min-w-10 items-center justify-center rounded-lg px-3 text-sm font-semibold transition
                        {{ $page === $currentPage
                            ? 'bg-primary text-white shadow-sm'
                            : 'text-ink hover:bg-soft' }}"
                    >
                        {{ $page }}
                    </a>
                @endforeach

                {{-- Halaman terakhir --}}
                @if ($endPage < $lastPage)
                    @if ($endPage < $lastPage - 1)
                        <span class="px-1 text-subtle">...</span>
                    @endif

                    <a
                        href="{{ $laporan->url($lastPage) }}"
                        class="flex h-10 min-w-10 items-center justify-center rounded-lg text-sm font-medium text-muted transition hover:bg-soft"
                    >
                        {{ $lastPage }}
                    </a>
                @endif

                {{-- Halaman selanjutnya --}}
                <a
                    href="{{ $laporan->nextPageUrl() ?? '#' }}"
                    @if (!$laporan->hasMorePages()) aria-disabled="true" @endif
                    class="flex h-10 w-10 items-center justify-center rounded-lg text-muted transition
                    {{ !$laporan->hasMorePages() ? 'pointer-events-none opacity-40' : 'hover:bg-soft' }}"
                >
                    <x-icon name="arrow_forward" class="h-5 w-5" />
                </a>

            </nav>
        @endif

    </div>

</x-card>


    </div>
</x-layouts.pengelola>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('filter-laporan');
        const search = document.getElementById('search-laporan');

        if (!form || !search) return;

        let timeout = null;

        search.addEventListener('input', function () {
            clearTimeout(timeout);

            const keyword = search.value.trim();

            // Jika pencarian dikosongkan, langsung tampilkan semua laporan
            if (keyword.length === 0) {
                form.requestSubmit();
                return;
            }

            // Jangan mencari jika baru 1 karakter
            if (keyword.length < 2) return;

            timeout = setTimeout(function () {
                form.requestSubmit();
            }, 400);
        });
    });
</script>