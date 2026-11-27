@php
    $crumbTataKelola = new class implements \Illuminate\Contracts\Support\Htmlable {
        public function toHtml()
        {
            return '<a href="'.e(route('admin.dashboard')).'" class="transition hover:text-primary">Tata Kelola</a>';
        }
    };
@endphp

<x-layouts.admin title="Dashboard" :breadcrumbs="[$crumbTataKelola, 'Dashboard Kurasi']">
    <x-page-header
        title="Dashboard Kurasi"
        description="Pantau jumlah destinasi, akun pengelola, dan pengajuan lokasi yang menunggu verifikasi."
    />

    @php
        $kartuStatistik = [
            [
                'label' => 'Destinasi Terdaftar',
                'angka' => $statistik['total_destinasi'],
                'keterangan' => 'Titik Wisata',
                'ikon' => 'forest',
                'warna' => 'text-ink',
                'ikonWarna' => 'text-primary',
                'ikonBg' => 'bg-soft-blue/70',
                'aksen' => false,
                'kompak' => false,
            ],
            [
                'label' => null,
                'angka' => $statistik['pengajuan_menunggu'],
                'keterangan' => 'Usulan Wisatawan',
                'ikon' => null,
                'warna' => 'text-secondary-ink',
                'ikonWarna' => 'text-secondary-ink',
                'ikonBg' => 'bg-peach',
                'aksen' => true,
                'kompak' => true,
            ],
            [
                'label' => 'Pengajuan Disetujui',
                'angka' => $statistik['pengajuan_disetujui'],
                'keterangan' => 'Usulan Disetujui',
                'ikon' => 'check_circle',
                'warna' => 'text-primary',
                'ikonWarna' => 'text-primary',
                'ikonBg' => 'bg-soft-blue/70',
                'aksen' => false,
                'kompak' => false,
            ],
            [
                'label' => 'Pengajuan Ditolak',
                'angka' => $statistik['pengajuan_ditolak'],
                'keterangan' => 'Usulan Ditolak',
                'ikon' => 'cancel',
                'warna' => 'text-danger',
                'ikonWarna' => 'text-danger',
                'ikonBg' => 'bg-danger/10',
                'aksen' => false,
                'kompak' => false,
            ],
        ];
    @endphp

    {{-- Empat statistik utama --}}
    <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach ($kartuStatistik as $kartu)
            <x-card
                padding="md"
                @class([
                    'relative flex flex-col justify-between overflow-hidden rounded-2xl border border-line shadow-[0_1px_3px_rgba(15,23,42,0.03)]',
                    'self-start' => $kartu['kompak'],
                    'min-h-[140px]' => ! $kartu['kompak'],
                ])
            >
                @if ($kartu['aksen'])
                    <span class="absolute inset-x-0 top-0 h-1 bg-secondary"></span>
                @endif

                @if ($kartu['label'] || $kartu['ikon'])
                    <div class="flex items-start justify-between gap-3">
                        @if ($kartu['label'])
                            <p class="text-[11px] font-semibold uppercase tracking-[0.08em] text-muted">
                                {{ $kartu['label'] }}
                            </p>
                        @else
                            <span aria-hidden="true"></span>
                        @endif

                        @if ($kartu['ikon'])
                            <span class="flex size-9 shrink-0 items-center justify-center rounded-xl {{ $kartu['ikonBg'] }} {{ $kartu['ikonWarna'] }}">
                                <x-icon :name="$kartu['ikon']" size="17" />
                            </span>
                        @endif
                    </div>
                @endif

                <div @class([
                    'flex flex-wrap items-baseline gap-x-2 gap-y-1',
                    'pt-1' => $kartu['kompak'],
                    'mt-6' => ! $kartu['kompak'],
                ])>
                    <span class="font-heading text-[34px] sm:text-[36px] leading-none font-bold tracking-tight tabular-nums {{ $kartu['warna'] }}">
                        {{ $kartu['angka'] }}
                    </span>
                    <span class="font-heading text-[15px] leading-5 font-semibold {{ $kartu['warna'] }}">
                        {{ $kartu['keterangan'] }}
                    </span>
                </div>
            </x-card>
        @endforeach
    </div>

    {{-- Panel pengelola dan destinasi --}}
    <div class="mt-4 grid grid-cols-1 gap-4 lg:grid-cols-2">
        <x-card padding="md" class="flex items-center gap-4 rounded-2xl border border-line bg-gradient-to-r from-soft-blue/70 via-soft-blue/30 to-white shadow-[0_1px_3px_rgba(15,23,42,0.03)]">
            <span class="flex size-14 shrink-0 items-center justify-center rounded-xl bg-primary text-white">
                <x-icon name="manage_accounts" size="26" />
            </span>

            <div class="min-w-0">
                <p class="font-heading text-[15px] leading-5 font-bold text-ink">
                    Akun Pengelola Aktif
                </p>
                <p class="mt-1.5 flex flex-wrap items-baseline gap-x-2 gap-y-1">
                    <span class="font-heading text-[32px] leading-none font-bold tracking-tight tabular-nums text-primary">
                        {{ $pengelolaAktif }}
                    </span>
                    <span class="text-[12px] leading-4 font-medium text-muted">
                        Akun Resmi Berwenang
                    </span>
                </p>
            </div>
        </x-card>

        <x-card padding="md" class="relative overflow-hidden rounded-2xl border border-[#dbe4f3] bg-gradient-to-r from-soft-blue/50 via-white to-[#f6efe2] shadow-[0_1px_3px_rgba(15,23,42,0.03)]">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div class="flex items-center gap-4">
                    <span class="flex size-14 shrink-0 items-center justify-center rounded-xl bg-[#7a5a24] text-white">
                        <x-icon name="person_alert" size="26" />
                    </span>

                    <div class="min-w-0">
                        <p class="font-heading text-[15px] leading-5 font-bold text-ink">
                            Destinasi Tanpa Akun Pengelola
                        </p>
                        <p class="mt-1.5 flex flex-wrap items-baseline gap-x-2 gap-y-1">
                            <span class="font-heading text-[32px] leading-none font-bold tracking-tight tabular-nums text-[#6b4a12]">
                                {{ $destinasiTanpaPengelola }}
                            </span>
                            <span class="text-[12px] leading-4 font-medium text-muted">
                                Menunggu Pembuatan Akun
                            </span>
                        </p>
                    </div>
                </div>

                <x-button :href="route('admin.pengelola.terbitkan')" variant="outline" size="sm" class="w-full shrink-0 justify-center bg-white! text-[#6b4a12]! ring-[#e2d4bd]! shadow-xs! hover:bg-[#faf5ec]! sm:w-auto">
                    Terbitkan Akun
                </x-button>
            </div>
        </x-card>
    </div>

    {{-- Tabel pengajuan menunggu verifikasi --}}
    <x-card padding="none" class="mt-5 overflow-hidden rounded-2xl border border-line shadow-[0_1px_3px_rgba(15,23,42,0.03)]">
        <div class="flex flex-col gap-3 border-b border-line px-5 py-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">
            <div class="min-w-0">
                <h2 class="font-heading text-[18px] leading-6 font-bold text-ink">
                    Pengajuan Lokasi Menunggu Verifikasi
                </h2>
                <p class="mt-1 text-[12px] leading-4 text-muted">
                    Lima pengajuan terbaru yang belum diverifikasi admin.
                </p>
            </div>

            <a href="{{ route('admin.pengajuan.index') }}" class="inline-flex shrink-0 items-center gap-1 font-heading text-[12px] font-semibold text-primary transition hover:text-primary-hover">
                Lihat Semua Usulan
                <x-icon name="chevron_right" size="16" />
            </a>
        </div>

        @if ($pengajuanMenunggu->isEmpty())
            <div class="flex flex-col items-center justify-center gap-2 px-5 py-12 text-center">
                <span class="flex size-12 items-center justify-center rounded-xl bg-soft text-muted">
                    <x-icon name="inventory_2" size="24" />
                </span>
                <p class="font-heading text-sm font-semibold text-ink">
                    Belum ada pengajuan menunggu verifikasi
                </p>
                <p class="text-[13px] leading-[18px] text-muted">
                    Pengajuan lokasi baru dari wisatawan akan muncul di sini.
                </p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full min-w-[900px] text-left text-sm">
                    <thead class="bg-soft-blue/60 text-[11px] font-semibold uppercase tracking-[0.08em] text-muted">
                        <tr>
                            <th scope="col" class="px-5 py-3.5 sm:pl-6">Nama Lokasi Usulan</th>
                            <th scope="col" class="px-5 py-3.5">Wilayah</th>
                            <th scope="col" class="px-5 py-3.5">Kategori</th>
                            <th scope="col" class="px-5 py-3.5">Diajukan Oleh</th>
                            <th scope="col" class="px-5 py-3.5">Tanggal</th>
                            <th scope="col" class="px-5 py-3.5">Status</th>
                            <th scope="col" class="px-5 py-3.5 text-center sm:pr-6">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-line">
                        @foreach ($pengajuanMenunggu as $pengajuan)
                            <tr class="transition hover:bg-soft-blue/25">
                                <td class="px-5 py-4 sm:pl-6">
                                    <div class="flex items-center gap-3">
                                        <span class="flex size-9 shrink-0 items-center justify-center rounded-xl bg-soft-blue/70 text-primary">
                                            <x-icon name="forest" size="16" />
                                        </span>
                                        <div class="min-w-0">
                                            <p class="font-semibold text-ink">{{ $pengajuan->nama_lokasi }}</p>
                                            <p class="mt-0.5 text-[11px] tabular-nums text-muted">#{{ $pengajuan->id_pengajuan }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <p class="font-medium text-ink">{{ $pengajuan->kecamatan?->kabupaten?->nama ?? '-' }}</p>
                                    @if ($pengajuan->kecamatan)
                                        <p class="mt-0.5 text-[11px] text-subtle">Kec. {{ $pengajuan->kecamatan->nama }}</p>
                                    @endif
                                </td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center rounded-full bg-soft-blue/70 px-2.5 py-1 text-[11px] font-medium text-primary">
                                        {{ $pengajuan->kategoriWisata?->nama ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-2">
                                        <span class="flex size-7 shrink-0 items-center justify-center rounded-full bg-soft-blue font-heading text-[11px] font-bold text-primary">
                                            {{ mb_strtoupper(mb_substr($pengajuan->pengaju?->username ?? '-', 0, 1)) }}
                                        </span>
                                        <span class="text-muted">{{ '@'.($pengajuan->pengaju?->username ?? '-') }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-4 tabular-nums text-muted">
                                    {{ $pengajuan->created_at?->locale('id')->translatedFormat('d M, H:i') ?? '-' }}
                                </td>
                                <td class="px-5 py-4">
                                    <x-status-badge :status="$pengajuan->status" size="sm" />
                                </td>
                                <td class="px-5 py-4 text-center sm:pr-6">
                                    <a
                                        href="{{ route('admin.pengajuan.show', ['pengajuan' => $pengajuan->id_pengajuan]) }}"
                                        class="inline-flex size-8 items-center justify-center rounded-lg bg-primary text-white shadow-xs transition hover:bg-primary-hover"
                                        title="Lihat detail pengajuan"
                                        aria-label="Lihat detail pengajuan {{ $pengajuan->nama_lokasi }}"
                                    >
                                        <x-icon name="visibility" size="16" />
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </x-card>
</x-layouts.admin>