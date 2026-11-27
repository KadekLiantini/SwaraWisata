<x-layouts.pengelola title="Dashboard">

 {{-- Header dashboard dan sapaan pengelola --}}
    <x-page-header
    :title="'Selamat datang kembali, ' . $pengelola->username . '!'"
    :description="'Berikut ringkasan pemantauan dan status laporan kerusakan fasilitas di Kawasan Wisata ' . ($pengelola->destinasi?->nama ?? 'destinasi Anda') . '.'"
    class="mb-6 rounded-xl bg-gradient-to-r from-[#dee8ff] via-[#e7eeff] to-white p-6 shadow-sm"
/>

    <div class="space-y-6">


        {{-- Empat kartu ringkasan laporan --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">

            {{-- Kartu Total Laporan Masuk --}}
            <x-card padding="md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-muted text-lg font-semibold">
                            Total Laporan Masuk
                        </p>

                        <div class="mt-2 flex items-baseline gap-2">
                            <p class="font-heading text-3xl font-bold text-ink">
                                {{ $totalLaporan }}
                            </p>
                            <span class="text-muted text-sm font-semibold">
                                Laporan
                            </span>
                        </div>
                    </div>

                    {{-- Icon --}}
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-soft">
                        <img
                            src="{{ asset('images/Icon-file.svg') }}"
                            alt="Icon Laporan"
                            class="h-8 w-8"
                        />
                    </div>
                </div>
            </x-card>

            {{-- Kartu Belum Dilihat --}}
            <x-card padding="md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-[#855300] text-lg font-semibold">
                            Belum Dilihat <span class="text-[#855300]">●</span>
                        </p>

                        <div class="mt-2 flex items-baseline gap-2">
                            <p class="font-heading text-3xl font-bold text-[#855300]">
                                {{ $belumDilihat }}
                            </p>
                            <span class="text-[#855300] text-sm font-semibold">
                                Perlu Respon
                            </span>
                        </div>
                    </div>

                    {{-- Icon --}}
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-peach">
                        <img
                            src="{{ asset('images/Icon-blm-diliat.svg') }}"
                            alt="Icon Laporan"
                            class="h-8 w-8"
                        />
                    </div>
                </div>
            </x-card>


            {{-- Kartu Ditindaklanjuti --}}
            <x-card padding="md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-muted text-lg font-semibold">
                            Sedang Ditindaklanjuti
                        </p>

                        <div class="mt-2 flex items-baseline gap-2">
                            <p class="font-heading text-3xl font-bold text-ink">
                                {{ $ditindaklanjuti }}
                            </p>
                            <span class="text-muted text-sm font-semibold">
                                Laporan Aktif
                            </span>
                        </div>
                    </div>

                    {{-- Icon --}}
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-soft">
                        <img
                            src="{{ asset('images/icon-ditindaklanjuti.svg') }}"
                            alt="Icon Laporan"
                            class="h-8 w-8"
                        />
                    </div>
                </div>
            </x-card>


            {{-- Kartu Selesai Ditangani --}}
            <x-card padding="md">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-primary text-sm font-semibold">
                            Selesai Ditangani
                        </p>

                        <div class="mt-2 flex items-baseline gap-2">
                            <p class="font-heading text-3xl font-bold text-primary">
                                {{ $selesai }}
                            </p>
                            <span class="text-primary text-sm font-semibold">
                                Tuntas
                            </span>
                        </div>
                    </div>

                    {{-- Icon --}}
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-xl bg-mint/40">
                        <img
                            src="{{ asset('images/icon-selesai-ditangani.svg') }}"
                            alt="Icon Laporan"
                            class="h-8 w-8"
                        />
                    </div>
                </div>
            </x-card>

        </div>

        {{-- Visualisasi status dan distribusi kategori --}}
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-12">

            {{-- Visualisasi respon dan status laporan --}}
            <div class="lg:col-span-7">
                <x-card padding="md">
                    <div class="flex flex-wrap items-start justify-between gap-3">
                        <div>
                            <h3 class="font-heading text-lg font-bold text-ink">
                                Visualisasi Respon & Status Laporan
                            </h3>
                        </div>
                        <span class="rounded-lg bg-soft px-3 py-1 text-xs font-semibold text-muted">
                            {{ $totalLaporan }} Total Insiden
                        </span>
                    </div>

                    <div class="mt-7 flex w-full items-center justify-between gap-4">
                    {{-- Teks sebelah kiri --}}
                    <p class="text-[11px] leading-[14px] font-bold text-muted">
                        Tingkat Penyelesaian Keseluruhan
                    </p>

                    {{-- Teks sebelah kanan --}}
                    <p class="text-right text-[11px] leading-[14px] font-bold text-primary">
                        {{ number_format($persentaseSelesai, 1) }}% Selesai Ditangani
                    </p>
                    </div>

                    {{-- Bar komposisi status laporan --}}
                    <div class="mt-2 flex h-4 w-full overflow-hidden rounded-full bg-soft">
                        @if ($totalLaporan > 0)
                            <div
                                class="h-full bg-primary"
                                style="width: {{ $persentaseSelesai }}%"
                                title="Selesai"
                            ></div>
                            <div
                                class="h-full bg-secondary"
                                style="width: {{ $persentaseProses }}%"
                                title="Dalam proses"
                            ></div>
                            <div
                                class="h-full bg-danger"
                                style="width: {{ $persentaseBelumDilihat }}%"
                                title="Belum dilihat"
                            ></div>
                        @endif
                    </div>

                    {{-- Keterangan warna grafik --}}
                    <div class="mt-4 flex w-full -translate-x-2 items-start justify-center gap-2 pt-1">

                        {{-- Selesai --}}
                        <div class="flex min-w-0 flex-1 items-center justify-center gap-2">
                            <span class="h-2.5 w-2.5 shrink-0 rounded-full bg-primary"></span>
                            <p class="text-center text-[11px] leading-[14px] font-semibold text-ink whitespace-nowrap">
                                {{ $selesai }} Selesai ({{ number_format($persentaseSelesai, 1) }}%)
                            </p>
                        </div>

                        {{-- Dalam Proses --}}
                        <div class="flex min-w-0 flex-1 items-center justify-center gap-2">
                            <span class="h-2.5 w-2.5 shrink-0 rounded-full bg-secondary"></span>
                            <p class="text-center text-[11px] leading-[14px] font-semibold text-ink whitespace-nowrap">
                                {{ $jumlahProses }} Proses ({{ number_format($persentaseProses, 1) }}%)
                            </p>
                        </div>

                        {{-- Belum Dilihat --}}
                        <div class="flex min-w-0 flex-1 items-center justify-center gap-2">
                            <span class="h-2.5 w-2.5 shrink-0 rounded-full bg-danger"></span>
                            <p class="text-center text-[11px] leading-[14px] font-semibold text-ink whitespace-nowrap">
                                {{ $belumDilihat }} Belum Dilihat ({{ number_format($persentaseBelumDilihat, 1) }}%)
                            </p>
                        </div>

                    </div>
                </x-card>
            </div>

            {{-- Distribusi kategori kerusakan --}}
            <div class="lg:col-span-5">
                <x-card padding="md">
                    {{-- Judul panel --}}
                    <div class="flex items-center justify-between pb-2">
                        <div class="flex flex-col gap-1">
                            <h3 class="font-heading text-[11px] leading-[14px] font-bold tracking-[0.55px] text-primary uppercase">
                                Distribusi Infrastruktur
                            </h3>

                            <p class="font-heading text-base leading-6 font-bold text-ink">
                                Kategori Kerusakan Teratas
                            </p>
                        </div>

                        <img src="{{ asset('images/Icon-sttistik.svg') }}" alt=""
                            class="h-3.5 w-3.5"/>
                    </div>

        {{-- Daftar kategori --}}
        <div class="flex flex-col gap-3">
           @forelse ($kategoriTeratas as $index => $kategori)
            
            @php
                if ($index === 0) {
                    $warnaKategori = 'bg-primary';
                    $warnaTeks = 'text-primary';
                } elseif ($index <= 2) {
                    $warnaKategori = 'bg-[#855300]';
                    $warnaTeks = 'text-[#855300]';
                } else {
                    $warnaKategori = 'bg-gray-400';
                    $warnaTeks = 'text-gray-500';
                }
            @endphp
    

            <div>
                <div class="mb-2 flex items-center justify-between gap-3">
                    <div class="flex items-center gap-2">
                        <x-icon
                            :name="$kategori->icon()"
                            class="h-5 w-5 {{ $warnaTeks }}"
                        />

                        <p class="text-sm font-medium text-ink">
                            {{ $kategori->nama }}
                        </p>
                    </div>

                    <span class="text-sm font-semibold {{ $warnaTeks }}">
                        {{ $kategori->laporan_count }}
                    </span>
                </div>

                <div class="h-2.5 overflow-hidden rounded-full bg-soft">
                    <div
                        class="h-full rounded-full {{ $warnaKategori }}"
                        style="width: {{ $jumlahKategoriTerbesar > 0 ? ($kategori->laporan_count / $jumlahKategoriTerbesar) * 100 : 0 }}%"
                    ></div>
                </div>
            </div>
        @empty
            <p class="py-8 text-center text-sm text-muted">
                Belum ada data kategori kerusakan.
            </p>
        @endforelse
        </div>
    </x-card>
</div>
        </div>

        
{{-- Daftar laporan terbaru yang perlu ditangani --}}
    <x-card padding="md">
        @php
            $jumlahPerluDitangani = $belumDilihat + $dilihat + $ditindaklanjuti;
        @endphp

        {{-- Header panel --}}
        <div class="flex flex-col justify-between gap-2 lg:flex-row lg:items-start">
            <div>
                <h3 class="font-heading text-lg font-bold text-ink">
                    Laporan Terbaru yang Perlu Ditangani
                </h3>
                <p class="mt-1 text-sm text-muted">
                    5 laporan terbaru yang belum dilihat atau sedang dalam tindak lanjut pengelola.
                </p>
            </div>

            {{-- Tombol lihat semua --}}
            <a
            href="{{ route('pengelola.laporan.index', ['filter' => 'semua']) }}"
            class="inline-flex shrink-0 items-center justify-center gap-2 rounded-lg bg-primary px-4 py-2 text-sm font-semibold text-white hover:bg-primary-hover"
            >
            Lihat Semua
            <x-icon name="arrow_forward" />
            </a>
        </div>

        
{{-- Filter pencarian, status, dan pengurutan --}}
<form
    id="filter-laporan"
    action="{{ route('pengelola.dashboard') }}"
    method="GET"
    class="mt-3 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between"
>
    {{-- Pencarian judul atau ID laporan --}}
    <div class="relative w-full sm:max-w-[360px]">
        <x-icon
            name="search"
            class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-muted"
        />

        <input
            id="search-laporan"
            type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="Cari judul atau ID laporan (#16)..."
            class="w-full rounded-xl border border-transparent bg-[#F0F3FF] py-2 pl-9 pr-3 text-sm text-ink placeholder:text-muted focus:border-primary/90 focus:outline-none focus:ring-1 focus:ring-primary/90"
        />
    </div>

    {{-- Dropdown filter --}}
    <div class="flex flex-wrap items-center gap-2">

        {{-- Filter status --}}
        <div class="relative min-w-[155px]">
            <select
                name="status"
                onchange="this.form.submit()"
                class="w-full appearance-none rounded-xl border border-transparent bg-[#F0F3FF] px-3 py-2 pr-9 text-sm font-medium text-ink focus:border-primary/90 focus:outline-none focus:ring-1 focus:ring-primary/90"
            >
                <option value="">Semua Status</option>
                <option value="dilihat" @selected(request('status') === 'dilihat')>
                    Dilihat
                </option>
                <option value="ditindaklanjuti" @selected(request('status') === 'ditindaklanjuti')>
                    Ditindaklanjuti
                </option>
                <option value="selesai" @selected(request('status') === 'selesai')>
                    Selesai
                </option>
            </select>

            <x-icon
                name="keyboard_arrow_down"
                class="pointer-events-none absolute right-2 top-1/2 h-4 w-4 -translate-y-1/2 text-muted"
            />
        </div>

        {{-- Pengurutan laporan --}}
        <div class="relative min-w-[115px]">
            <select
                name="sort"
                onchange="this.form.submit()"
                class="w-full appearance-none rounded-xl border border-transparent bg-[#F0F3FF] px-3 py-2 pr-9 text-sm font-medium text-ink focus:border-primary/90 focus:outline-none focus:ring-1 focus:ring-primary/90"
            >
                <option value="terbaru" @selected(request('sort', 'terbaru') === 'terbaru')>
                    Terbaru
                </option>
                <option value="terlama" @selected(request('sort') === 'terlama')>
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
            <table class="w-full min-w-[1328px] text-left">
                <thead>
                    <tr class="border-b border-line bg-soft/50 text-[11px] uppercase tracking-wide text-muted">
                        <th class="px-3 py-3 font-bold">Bukti / ID</th>
                        <th class="px-3 py-3 font-bold">Judul Laporan & Lokasi Detail</th>
                        <th class="px-3 py-3 font-bold">Kategori Kerusakan</th>
                        <th class="px-3 py-3 font-bold">Waktu Masuk</th>
                        <th class="px-3 py-3 font-bold">Status Lapor</th>
                        <th class="px-3 py-3 font-bold">Pelapor</th>
                        <th class="px-3 py-3 text-right font-bold">Aksi Tindak Lanjut</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-line">
                    @forelse ($laporanTerbaru as $item)
                        @php
                            $statusTampilan = match ($item->status) {
                                'belum_dilihat' => [
                                    'label' => 'Belum Dilihat',
                                    'class' => 'bg-danger-soft text-danger',
                                    'dot' => 'bg-danger',
                                ],
                                'dilihat' => [
                                    'label' => 'Dilihat',
                                    'class' => 'bg-primary/20 text-primary',
                                    'dot' => 'bg-primary',
                                ],
                                'ditindaklanjuti' => [
                                    'label' => 'Ditindaklanjuti',
                                    'class' => 'bg-secondary/20 text-secondary-ink',
                                    'dot' => 'bg-secondary-ink',
                                ],
                                'selesai' => [
                                    'label' => 'Selesai',
                                    'class' => 'bg-mint text-primary',
                                    'dot' => 'bg-primary',
                                ],
                                default => [
                                    'label' => 'Tidak diketahui',
                                    'class' => 'bg-soft text-muted',
                                    'dot' => 'bg-muted',
                                ],
                            };

                            $waktuMasuk = $item->created_at
                                ? $item->created_at->copy()->timezone('Asia/Makassar')
                                : null;
                        @endphp

                        <tr class="text-sm transition-colors hover:bg-soft/30">
                            
                        {{-- ID laporan dan bukti foto --}}
                        <td class="whitespace-nowrap px-3 py-4">
                            @php
                                $fotoUtama = $item->foto->first();
                            @endphp

                            <div class="flex items-center gap-3">
                                {{-- ID laporan di sebelah kiri --}}
                                <span class="font-semibold text-subtle">
                                    #{{ $item->id_laporan }}
                                </span>

                                {{-- Bukti foto di sebelah kanan --}}
                                @if ($fotoUtama)
                                    <img
                                        src="{{ $fotoUtama->url }}"
                                        alt="Bukti foto laporan {{ $item->id_laporan }}"
                                        class="h-12 w-12 shrink-0 rounded-lg border border-line object-cover"
                                        loading="lazy"
                                    />
                                @else
                                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-soft">
                                        <x-icon name="image" class="h-5 w-5 text-muted" />
                                    </div>
                                @endif
                            </div>
                        </td>


                            {{-- Judul dan lokasi --}}
                            <td class="min-w-[190px] px-3 py-4">
                                <p class="font-semibold text-ink">
                                    {{ $item->judul }}
                                </p>
                                <p class="mt-1 text-xs text-muted">
                                    {{ $item->lokasi_spesifik ?: 'Lokasi tidak dicantumkan' }}
                                </p>
                            </td>

                            {{-- Kategori kerusakan --}}
                            <td class="whitespace-nowrap px-3 py-4">
                                <span class="inline-flex items-center gap-2 rounded-lg bg-soft px-3 py-2 text-xs font-semibold text-muted">
                                    <x-icon
                                        :name="$item->kategoriKerusakan?->icon() ?? 'construction'"
                                        class="h-4 w-4"
                                    />
                                    {{ $item->kategoriKerusakan?->nama ?? 'Tidak diketahui' }}
                                </span>
                            </td>

                            {{-- Waktu masuk --}}
                            <td class="whitespace-nowrap px-3 py-4">
                                @if ($waktuMasuk)
                                    <p class="text-xs font-semibold text-ink">
                                        {{ $waktuMasuk->format('d M Y') }}
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

                            {{-- Status laporan --}}
                            <td class="whitespace-nowrap px-3 py-4">
                                <span class="inline-flex items-center gap-2 whitespace-nowrap rounded-full px-3 py-1 text-xs font-semibold {{ $statusTampilan['class'] }}">
                                <span class="h-2 w-2 rounded-full {{ $statusTampilan['dot'] }}"></span>
                                {{ $statusTampilan['label'] }}
                            </span>
                            </td>

                            {{-- Pelapor --}}
                            <td class="whitespace-nowrap px-3 py-4">
                                <div class="flex items-center gap-2">
                                    {{-- Foto profil sementara menggunakan inisial --}}
                                    <div class="flex h-7 w-7 shrink-0 items-center justify-center rounded-full bg-soft-strong text-xs font-bold text-muted">
                                        {{ strtoupper(substr($item->pelapor?->username ?? 'P', 0, 1)) }}
                                    </div>

                                    <span class="text-xs font-semibold text-ink">
                                        @if ($item ->pelapor?->username)
                                            {{ '@' . $item->pelapor->username }}
                                        @else
                                            Pengguna tidak diketahui
                                        @endif
                                    </span>
                                </div>
                            </td>
                            {{-- Aksi tindak lanjut --}}
                            <td class="whitespace-nowrap px-3 py-4">
                                <div class="flex justify-end">
                                    <a
                                        href="{{ route('pengelola.laporan.show', $item->id_laporan) }}"
                                        class="inline-flex items-center gap-2 rounded-lg bg-soft px-3 py-2 text-xs font-semibold text-muted transition hover:bg-muted/10"
                                    >
                                        Lihat Detail & Tangani
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
                                        Belum ada laporan yang perlu ditangani.
                                    </p>
                                    <p class="text-xs text-muted">
                                        Laporan terbaru akan muncul di bagian ini.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
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

            timeout = setTimeout(function () {
                form.requestSubmit();
            }, 400);
        });
    });
</script>