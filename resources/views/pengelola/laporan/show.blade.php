
<x-layouts.pengelola title="Detail Laporan">
    @php
        $jumlahFoto = $laporan->foto->count();
        $riwayatTerurut = $laporan->riwayat->sortBy('created_at');
        $riwayatTerakhir = $laporan->riwayat->sortByDesc('updated_at')->first();
        $tabAktif = old('jenis_tindakan', 'tindak_lanjut');
         $sudahSelesai = $laporan->status === \App\Models\Laporan::STATUS_SELESAI;
    @endphp

    <div class="space-y-6">

        {{-- Header halaman --}}
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <div class="flex items-center gap-2 text-sm">
                    <a
                        href="{{ route('pengelola.laporan.index') }}"
                        class="text-muted transition hover:text-primary"
                    >
                        Daftar Laporan
                    </a>

                    <x-icon name="chevron_right" size="16" class="text-subtle" />

                    <span class="font-semibold text-ink">
                        Detail Laporan
                    </span>
                </div>

                <div class="mt-1 flex flex-wrap items-center gap-3">
                <h1 class="font-heading text-xl font-bold text-ink">
                    Detail Laporan Insiden Sarana Wisata
                </h1>

                @php
                    $statusLaporan = [
                        'belum_dilihat' => [
                            'label' => 'Belum Dilihat',
                            'class' => 'bg-soft-strong text-muted',
                        ],
                        'dilihat' => [
                            'label' => 'Dilihat',
                            'class' => 'bg-soft-blue text-primary',
                        ],
                        'ditindaklanjuti' => [
                            'label' => 'Ditindaklanjuti',
                            'class' => 'bg-peach text-secondary-ink',
                        ],
                        'selesai' => [
                            'label' => 'Selesai',
                            'class' => 'bg-mint text-primary',
                        ],
                    ];

                    $statusAktif = $statusLaporan[$laporan->status] ?? [
                        'label' => ucfirst(str_replace('_', ' ', $laporan->status)),
                        'class' => 'bg-soft-strong text-muted',
                    ];
                @endphp

                <span class="inline-flex items-center gap-2 rounded-full px-3 py-1 text-xs font-bold {{ $statusAktif['class'] }}">
                    <span class="h-2 w-2 rounded-full bg-current"></span>
                    {{ $statusAktif['label'] }}
                </span>
            </div>
            </div>

            <x-button
                :href="route('pengelola.laporan.index')"
                variant="ghost"
                size="sm"
                icon-left="arrow_back"
                class="!text-ink bg-soft hover:bg-soft-strong"
            >
                Kembali ke Daftar Laporan
            </x-button>
        </div>

        {{-- Konten utama --}}
        <div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-5">

            {{-- Kolom kiri: informasi laporan --}}
            <div class="space-y-5 lg:col-span-3">
                <x-card>
                    <div class="space-y-5">

                        {{-- Kategori dan judul --}}
                        <div>
                            <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wide text-primary">
                                <x-icon name="category" size="18" />
                                <span>Kategori:</span>
                                <span>
                                    {{ $laporan->kategoriKerusakan?->nama ?? 'Kategori tidak tersedia' }}
                                </span>
                            </div>

                            <h2 class="mt-2 font-heading text-xl font-bold text-ink">
                                {{ $laporan->judul }}
                            </h2>

                            <p class="mt-1 text-sm text-muted">
                                ID Laporan: #{{ $laporan->id_laporan }}
                            </p>
                        </div>

                        <div class="border-t border-line pt-4">

                            {{-- Judul bukti kerusakan --}}
                            <h3 class="mb-3 flex items-center gap-2 font-heading text-sm font-bold text-ink">
                                 <img
                                    src="{{ asset('images/icon-foto.svg') }}"
                                    alt=""
                                    class="h-5 w-5 shrink-0"
                                >
                                Bukti Kerusakan Terotentikasi
                                <span class="font-normal text-muted">
                                    ({{ $jumlahFoto }} Berkas Foto)
                                </span>
                            </h3>

                            @if ($laporan->foto->isNotEmpty())
                                <div class="grid grid-cols-2 gap-3 sm:grid-cols-3">
                                    @foreach ($laporan->foto as $foto)
                                        <button
                                            type="button"
                                            data-foto-preview="{{ $foto->url }}"
                                            aria-label="Lihat foto kerusakan"
                                            class="group overflow-hidden rounded-xl text-left"
                                        >
                                            <img
                                                src="{{ $foto->url }}"
                                                alt="Foto bukti kerusakan"
                                                class="h-36 w-full rounded-xl object-cover transition group-hover:scale-105"
                                            >
                                        </button>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-sm text-muted">
                                    Belum ada foto kerusakan.
                                </p>
                            @endif
                        </div>

                        {{-- Informasi laporan --}}
                        <div class="grid grid-cols-1 gap-x-5 gap-y-5 rounded-2xl bg-soft p-5 sm:grid-cols-2 xl:grid-cols-3">

                            {{-- Destinasi --}}
                            <div>
                                <p class="text-xs font-semibold text-muted">
                                    Destinasi Terikat
                                </p>
                                <p class="mt-1 flex items-center gap-1.5 text-sm font-semibold text-ink">
                                    <x-icon name="landscape" size="18" class="text-primary" />
                                    {{ $laporan->destinasi?->nama ?? '-' }}
                                </p>
                            </div>

                            {{-- Lokasi --}}
                            <div>
                                <p class="text-xs font-semibold text-muted">
                                    Lokasi Spesifik
                                </p>
                                <p class="mt-1 flex items-center gap-1.5 text-sm font-semibold text-ink">
                                    <x-icon name="location_on" size="18" class="text-primary" />
                                    {{ $laporan->lokasi_spesifik ?? '-' }}
                                </p>
                            </div>

                            {{-- Identitas Pelapor --}}
                            <div>
                                <p class="text-xs font-semibold text-muted">
                                    Identitas Pelapor
                                </p>
                                <p class="mt-1 flex items-center gap-1.5 text-sm font-semibold text-ink">
                                    <x-icon name="account_circle" size="20" class="text-primary" />
                                    @ {{ $laporan->pelapor?->username ?? 'pengguna' }}
                                </p>
                            </div>

                            {{-- Waktu Pengiriman --}}
                            <div>
                                <p class="text-xs font-semibold text-muted">
                                    Waktu Pengiriman
                                </p>
                                <p class="mt-1 text-sm font-semibold text-ink">
                                    {{ $laporan->created_at?->timezone('Asia/Makassar')->format('d M Y, H:i') ?? '-' }}
                                    WITA
                                </p>
                            </div>

                            {{-- Pembaruan Terakhir --}}
                            <div>
                                <p class="text-xs font-semibold text-muted">
                                    Pembaruan Terakhir
                                </p>
                                <p class="mt-1 text-sm font-semibold text-primary">
                                    {{ $riwayatTerakhir?->updated_at?->timezone('Asia/Makassar')->format('d M Y, H:i') ?? '-' }}
                                    @if ($riwayatTerakhir)
                                        WITA
                                    @endif
                                </p>
                            </div></div>

                        
                        {{-- Deskripsi laporan --}}<div>
                            <h3 class="mb-3 flex items-center gap-2 font-heading text-sm font-bold text-ink">
                                <x-icon name="description" size="20" class="text-primary" />
                                Deskripsi Kerusakan Aduan Wisatawan
                            </h3>

                            <blockquote class="rounded-2xl border-l-4 border-primary bg-soft p-4 text-sm italic leading-7 text-ink">
                                "{{ $laporan->deskripsi }}"
                            </blockquote></div>
                    </div>
                </x-card>
            </div>

            {{-- Kolom kanan: aksi dan timeline --}}
            <div class="space-y-5 lg:col-span-2">

                {{-- Kartu aksi pengelola --}}
                <x-card>
                    <div class="space-y-4">
                        <div>
                            <h2 class="font-heading font-bold text-ink">
                                Aksi Tanggap Pengelola
                            </h2>
                            <p class="mt-1 text-xs text-muted">
                                {{ $laporan->destinasi?->nama ?? 'Destinasi Anda' }}
                            </p>
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <button
                                type="button"
                                data-pilih-tab="tindak_lanjut"
                                class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-3 py-3 text-center text-xs font-semibold text-white transition hover:bg-primary-hover"
                            >
                                <x-icon name="edit_note" size="18" />
                                <span>Kirim Tindak Lanjut</span>
                            </button>

                            <button
                                type="button"
                                data-pilih-tab="penyelesaian"
                                class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-3 py-3 text-center text-xs font-semibold text-white transition hover:brightness-95"
                            >
                                <x-icon name="task_alt" size="18" />
                                <span>Kirim Penyelesaian</span>
                            </button>
                        </div>
                    </div>
                </x-card>


            {{-- Timeline penanganan --}}
            <x-card>
                <div class="space-y-5">
                    <h2 class="flex items-center gap-3 border-b border-line pb-4 font-heading text-lg font-bold text-ink">
                        <x-icon name="history" size="25" class="text-primary" />
                        Jejak Penanganan Terverifikasi
                    </h2>

                    <div class="relative space-y-6 pl-6">

                        {{-- Garis timeline --}}
                        <div class="absolute bottom-4 left-[8px] top-3 w-[2px] rounded-full bg-line/50"></div>

                        {{-- Laporan dikirim wisatawan --}}
                        <div class="relative">
                            <div class="absolute -left-6 top-1 z-10 flex h-4 w-4 items-center justify-center rounded-full bg-primary ring-4 ring-white">
                                <span class="h-2 w-2 rounded-full bg-white"></span>
                            </div>

                            <div class="flex flex-wrap items-start justify-between gap-2">
                                <p class="font-heading text-xs font-bold text-ink">
                                    Laporan Dikirimkan Wisatawan
                                </p>
                                <span class="text-[11px] font-bold text-muted">
                                    {{ $laporan->created_at?->timezone('Asia/Makassar')->format('d M, H:i') ?? '-' }} WITA
                                </span>
                            </div>

                            <p class="mt-1 text-[13px] leading-[18px] text-muted">
                                Diserahkan oleh wisatawan
                                <span class="font-semibold">
                                    {{ '@' . ($laporan->pelapor?->username ?? 'wisatawan') }}
                                </span>
                                beserta {{ $jumlahFoto }} lampiran foto.
                            </p>
                        </div>

                        {{-- Laporan dilihat pengelola --}}
                        <div class="relative">
                            <div class="absolute -left-6 top-1 z-10 flex h-4 w-4 items-center justify-center rounded-full {{ $laporan->dilihat_pada ? 'bg-primary' : 'bg-soft-strong' }} ring-4 ring-white">
                                @if ($laporan->dilihat_pada)
                                    <x-icon name="check" size="11" class="text-white" />
                                @else
                                    <span class="h-2 w-2 rounded-full bg-subtle"></span>
                                @endif
                            </div>

                            <div class="flex flex-wrap items-start justify-between gap-2">
                                <p class="font-heading text-xs font-bold text-ink">
                                    {{ $laporan->dilihat_pada ? 'Dilihat oleh Pengelola' : 'Menunggu Dilihat' }}
                                </p>

                                <span class="text-[11px] font-bold text-muted">
                                    {{ $laporan->dilihat_pada?->timezone('Asia/Makassar')->format('d M, H:i') ?? '-' }}
                                    @if ($laporan->dilihat_pada)
                                        WITA
                                    @endif
                                </span>
                            </div>

                            <p class="mt-1 text-[13px] leading-[18px] text-muted">
                                @if ($laporan->dilihat_pada)
                                    Laporan telah dibuka oleh pengelola destinasi.
                                @else
                                    Laporan belum dilihat oleh pengelola.
                                @endif
                            </p>
                        </div>

{{-- Ambil data penyelesaian --}}
@php
    $riwayatPenyelesaian = $laporan->riwayat
        ->firstWhere('jenis_tindakan', 'penyelesaian');

    $riwayatTindakLanjut = $riwayatTerurut
        ->where('jenis_tindakan', 'tindak_lanjut');
@endphp

{{-- Entri tindak lanjut --}}
@forelse ($riwayatTindakLanjut as $riwayat)
    <div class="relative">
        <div class="absolute -left-6 top-2 z-10 flex h-4 w-4 items-center justify-center rounded-full bg-secondary-ink ring-4 ring-white">
            <span class="h-1.5 w-1.5 rounded-full bg-white"></span>
        </div>

        <div class="min-w-0 rounded-xl bg-soft p-3 sm:p-4">
            <div class="flex flex-wrap items-center justify-between gap-2">
                <p class="font-heading text-[11px] font-bold uppercase tracking-[0.55px] text-secondary-ink">
                    ENTRI TINDAK LANJUT #{{ $laporan->id_laporan }}
                </p>

                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        disabled
                        aria-label="Komentar"
                        title="Fitur komentar akan tersedia pada pengembangan berikutnya"
                        data-komentar-placeholder
                        class="inline-flex h-8 w-8 items-center justify-center rounded-md bg-mint/50 text-primary shadow-sm disabled:cursor-not-allowed"
                    >
                        <x-icon name="chat" size="17" />
                    </button>

                    <span class="rounded px-2 py-1 bg-peach text-[11px] font-bold text-secondary-ink">
                        Aktif
                    </span>
                </div>
            </div>

            <p class="mt-2 text-[11px] font-semibold text-muted">
                {{ $riwayat->pengelola?->username ?? 'Pengelola' }}
                ·
                {{ $riwayat->created_at?->timezone('Asia/Makassar')->format('d M Y, H:i') ?? '-' }}
                WITA
            </p>

            <p class="mt-3 whitespace-pre-line text-[13px] leading-[21px] text-ink">
                {{ $riwayat->deskripsi }}
            </p>

            @if ($riwayat->foto->isNotEmpty())
                <div class="mt-4 grid grid-cols-2 gap-2">
                    @foreach ($riwayat->foto as $foto)
                        <button
                            type="button"
                            data-foto-preview="{{ asset('storage/' . $foto->path) }}"
                            aria-label="Lihat foto bukti penanganan"
                            class="overflow-hidden rounded-lg bg-soft-strong"
                        >
                            <img
                                src="{{ asset('storage/' . $foto->path) }}"
                                alt="Foto bukti penanganan"
                                class="h-32 w-full object-cover transition hover:scale-105"
                            />
                        </button>
                    @endforeach
                </div>
            @endif

            <div class="mt-4 flex flex-nowrap justify-end gap-2 border-t border-line/50 pt-3">
                <button
                    type="button"
                    data-toggle-edit="{{ $riwayat->id_riwayat }}"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-primary px-3 py-2 text-[11px] font-bold text-white transition hover:bg-primary-hover"
                >
                    <x-icon name="edit" size="15" class="text-white" />
                    Edit Pembaruan
                </button>

                <form
                    action="{{ route('pengelola.laporan.riwayat.destroy', [$laporan->id_laporan, $riwayat->id_riwayat]) }}"
                    method="POST"
                    onsubmit="return confirm('Yakin ingin menghapus pembaruan ini?')"
                >
                    @csrf
                    @method('DELETE')

                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-danger-soft px-3 py-2 text-[11px] font-bold text-danger transition hover:brightness-95"
                    >
                        <x-icon name="delete" size="15" />
                        Hapus Pembaruan
                    </button>
                </form>
            </div>

            <div
                id="edit-riwayat-{{ $riwayat->id_riwayat }}"
                class="mt-4 hidden border-t border-line pt-4"
            >
                <form
                    action="{{ route('pengelola.laporan.riwayat.update', [$laporan->id_laporan, $riwayat->id_riwayat]) }}"
                    method="POST"
                    enctype="multipart/form-data"
                    class="space-y-3"
                >
                    @csrf
                    @method('PUT')

                    <x-form.textarea
                        name="deskripsi"
                        label="Edit Deskripsi"
                        rows="4"
                        required
                        maxlength="500"
                        :value="$riwayat->deskripsi"
                        placeholder="Jelaskan pembaruan penanganan..."
                    />

                    <x-form.input
                        name="foto[]"
                        type="file"
                        label="Ganti Foto Bukti (Opsional)"
                        multiple
                        accept="image/jpeg,image/png"
                        hint="JPG atau PNG, maksimal 2 MB per foto. Foto lama akan diganti."
                    />

                    <div class="flex flex-wrap gap-2">
                        <x-button type="submit" size="sm" icon-left="save">
                            Simpan Perubahan
                        </x-button>

                        <button
                            type="button"
                            data-toggle-edit="{{ $riwayat->id_riwayat }}"
                            class="rounded-lg px-4 py-2 text-sm font-semibold text-muted hover:bg-soft"
                        >
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@empty
    <div class="relative opacity-60">
        <div class="absolute -left-6 top-2 z-10 flex h-4 w-4 items-center justify-center rounded-full bg-line ring-4 ring-white">
            <span class="h-1.5 w-1.5 rounded-full bg-white"></span>
        </div>

        <div class="rounded-xl border border-line bg-soft p-3 sm:p-4">
            <div class="flex flex-nowrap items-center justify-between gap-2">
                <p class="font-heading text-[11px] font-bold uppercase tracking-[0.55px] text-subtle">
                    ENTRI TINDAK LANJUT #{{ $laporan->id_laporan }}
                </p>

                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        disabled
                        aria-label="Komentar"
                        title="Fitur komentar akan tersedia pada pengembangan berikutnya"
                        data-komentar-placeholder
                        class="inline-flex h-8 w-8 items-center justify-center rounded-md bg-soft-strong text-subtle shadow-sm disabled:cursor-not-allowed"
                    >
                        <x-icon name="chat" size="17" />
                    </button>

                    <span class="text-[11px] font-bold text-subtle">
                        Tahap Berikutnya
                    </span>
                </div>
            </div>

            <p class="mt-2 text-[13px] leading-[18px] text-subtle">
                Belum ada entri tindak lanjut dari pengelola.
                Pembaruan penanganan akan muncul di bagian ini.
            </p>
        </div>
    </div>
@endforelse

{{-- Tahap verifikasi akhir dan penyelesaian --}}
<div class="relative {{ $sudahSelesai ? '' : 'opacity-70' }}">
    {{-- Titik timeline --}}
    <div class="absolute -left-6 top-2 z-10 flex h-4 w-4 items-center justify-center rounded-full {{ $sudahSelesai ? 'bg-primary' : 'bg-line' }} ring-4 ring-white">
        @if ($sudahSelesai)
            <x-icon name="check" size="12" class="text-white" />
        @else
            <span class="h-1.5 w-1.5 rounded-full bg-white"></span>
        @endif
    </div>

    {{-- Kartu penyelesaian --}}
    <div class="min-w-0 rounded-xl {{ $sudahSelesai ? 'bg-soft p-3 sm:p-4' : 'p-3 sm:p-4 border border-line bg-soft' }}">

        {{-- Judul dan tombol komentar --}}
        <div class="flex flex-nowrap items-center justify-between gap-2">
            <p class="font-heading text-[11px] font-bold uppercase tracking-[0.55px] {{ $sudahSelesai ? 'text-ink' : 'text-subtle' }}">
                VERIFIKASI AKHIR & PENYELESAIAN #{{ $laporan->id_laporan }}
            </p>

            <div class="flex items-center gap-2">
                {{-- Tombol komentar --}}
                <button
                    type="button"
                    disabled
                    aria-label="Komentar"
                    title="Fitur komentar akan tersedia pada pengembangan berikutnya"
                    data-komentar-placeholder
                    class="inline-flex h-8 w-8 shrink-0 items-center justify-center rounded-md {{ $sudahSelesai ? 'bg-mint/50 text-primary' : 'bg-soft-strong text-subtle' }} shadow-sm disabled:cursor-not-allowed"
                >
                    <x-icon name="chat" size="17" />
                </button>

                {{-- Status penyelesaian --}}
                @if ($sudahSelesai)
                    <span class="rounded px-2 py-1 text-[11px] font-bold text-primary bg-mint">
                        Selesai
                    </span>
                @else
                    <span class="text-[11px] font-bold text-subtle">
                        Tahap Berikutnya
                    </span>
                @endif
            </div>
        </div>

         @if (! $sudahSelesai)
            <p class="mt-2 text-[13px] leading-[18px] text-subtle">
                Menunggu pelaporan pengerjaan rampung dengan bukti foto hasil perbaikan.
            </p>
        @endif

        @if ($riwayatPenyelesaian)
            {{-- Identitas pengelola dan waktu --}}
            <p class="mt-2 text-[11px] font-semibold text-muted">
                {{ $riwayatPenyelesaian->pengelola?->username ?? 'Pengelola' }}
                ·
                {{ $riwayatPenyelesaian->created_at?->timezone('Asia/Makassar')->format('d M Y, H:i') ?? '-' }}
                WITA
            </p>

            {{-- Deskripsi --}}
            <p class="mt-3 whitespace-pre-line text-[13px] leading-[21px] text-ink">
                {{ $riwayatPenyelesaian->deskripsi }}
            </p>

            {{-- Foto bukti --}}
            @if ($riwayatPenyelesaian->foto->isNotEmpty())
                <div class="mt-4 grid grid-cols-2 gap-2">
                    @foreach ($riwayatPenyelesaian->foto as $foto)
                        <button
                            type="button"
                            data-foto-preview="{{ asset('storage/' . $foto->path) }}"
                            aria-label="Lihat foto bukti penyelesaian"
                            class="overflow-hidden rounded-lg bg-soft-strong"
                        >
                            <img
                                src="{{ asset('storage/' . $foto->path) }}"
                                alt="Foto bukti penyelesaian"
                                class="h-32 w-full object-cover transition hover:scale-105"
                            />
                        </button>
                    @endforeach
                </div>
            @endif

{{-- Tombol edit dan hapus --}}
<div class="mt-4 flex flex-nowrap justify-end gap-2 border-t border-line/50 pt-3">
    <button
        type="button"
        data-toggle-edit="{{ $riwayatPenyelesaian->id_riwayat }}"
        class="inline-flex min-h-10 min-w-0 flex-1 items-center justify-center gap-2 rounded-lg bg-primary px-2 py-2 text-center text-[11px] font-bold text-white transition hover:bg-primary-hover"
    >
        <x-icon name="edit" size="15" class="shrink-0 text-white" />
        <span>Edit Penyelesaian</span>
    </button>

    <form
        action="{{ route('pengelola.laporan.riwayat.destroy', [$laporan->id_laporan, $riwayatPenyelesaian->id_riwayat]) }}"
        method="POST"
        class="min-w-0 flex-1"
        onsubmit="return confirm('Yakin ingin menghapus bukti penyelesaian ini?')"
    >
        @csrf
        @method('DELETE')

        <button
            type="submit"
            class="inline-flex min-h-10 w-full items-center justify-center gap-2 rounded-lg bg-danger-soft px-2 py-2 text-center text-[11px] font-bold text-danger transition hover:brightness-95"
        >
            <x-icon name="delete" size="15" class="shrink-0" />
            <span>Hapus Penyelesaian</span>
        </button>
    </form>
</div>

            {{-- Form edit tersembunyi --}}
            <div
                id="edit-riwayat-{{ $riwayatPenyelesaian->id_riwayat }}"
                class="mt-4 hidden border-t border-line pt-4"
            >
                <form
                    action="{{ route('pengelola.laporan.riwayat.update', [$laporan->id_laporan, $riwayatPenyelesaian->id_riwayat]) }}"
                    method="POST"
                    enctype="multipart/form-data"
                    class="space-y-3"
                >
                    @csrf
                    @method('PUT')

                    <x-form.textarea
                        name="deskripsi"
                        label="Edit Deskripsi Penyelesaian"
                        rows="4"
                        required
                        maxlength="500"
                        :value="$riwayatPenyelesaian->deskripsi"
                        placeholder="Jelaskan hasil penyelesaian..."
                    />

                    <x-form.input
                        name="foto[]"
                        type="file"
                        label="Ganti Foto Bukti (Opsional)"
                        multiple
                        accept="image/jpeg,image/png"
                        hint="JPG atau PNG, maksimal 2 MB per foto. Foto lama akan diganti."
                    />

                    <div class="flex flex-wrap gap-2">
                        <x-button type="submit" size="sm" icon-left="save">
                            Simpan Perubahan
                        </x-button>

                        <button
                            type="button"
                            data-toggle-edit="{{ $riwayatPenyelesaian->id_riwayat }}"
                            class="rounded-lg px-4 py-2 text-sm font-semibold text-muted hover:bg-soft"
                        >
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        @endif
    </div>
</div>
                    </div>
                </div>
            </x-card>

            </div>
        </div>

        {{-- Form respons pengelola --}}
        <div id="form-respons" class="scroll-mt-6">
            <x-card>
                <div class="space-y-5">

                    {{-- Judul dan pilihan formulir --}}
                    <div class="flex flex-col justify-between gap-4 lg:flex-row lg:items-center">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <x-icon name="edit_note" size="24" class="text-primary" />

                                <h2 class="font-heading text-lg font-bold text-ink">
                                    Formulir Kirim Respons Pengelola
                                </h2>
                            </div>

                            <p class="mt-1 text-sm text-muted">
                                Sampaikan perkembangan penanganan laporan sesuai kondisi destinasi.
                            </p>
                        </div>

                        {{-- Pilihan formulir --}}
                        <div class="flex shrink-0 rounded-xl bg-soft p-1">
                            <button
                                type="button"
                                data-tab-button="tindak_lanjut"
                                class="flex flex-1 items-center justify-center gap-2 rounded-lg bg-primary px-4 py-3 text-sm font-semibold text-white transition lg:flex-none"
                            >
                                <x-icon name="engineering" size="20" />
                                <span>Formulir 1: Tindak Lanjut</span>
                            </button>

                            <button
                                type="button"
                                data-tab-button="penyelesaian"
                                class="flex flex-1 items-center justify-center gap-2 rounded-lg px-4 py-3 text-sm font-semibold text-muted transition lg:flex-none"
                            >
                                <x-icon name="verified" size="20" />
                                <span>Formulir 2: Penyelesaian</span>
                            </button>
                        </div>
                    </div>

                    {{-- Garis pemisah --}}
                    <div class="border-t border-line"></div>

                    {{-- Ringkasan laporan --}}
                    <div class="grid grid-cols-1 gap-4 rounded-xl bg-soft p-4 sm:grid-cols-3">
                        <div>
                            <p class="text-xs font-medium text-muted">Laporan Terkait</p>
                            <p class="mt-1 text-sm font-bold text-ink">
                                #{{ $laporan->id_laporan }} - {{ $laporan->judul }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-medium text-muted">Kategori Kerusakan</p>
                            <p class="mt-1 text-sm font-bold text-ink">
                                {{ $laporan->kategoriKerusakan?->nama ?? '-' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs font-medium text-muted">Lokasi Target Pekerjaan</p>
                            <p class="mt-1 text-sm font-bold text-ink">
                                {{ $laporan->lokasi_spesifik ?? $laporan->destinasi?->nama ?? '-' }}
                            </p>
                        </div>
                    </div>

                    {{-- Header jenis formulir --}}
                    <div class="flex flex-wrap items-center justify-between gap-2 rounded-lg bg-soft-strong px-4 py-3">
                        <div class="flex items-center gap-2">
                            <x-icon
                                name="construction"
                                size="20"
                                class="text-primary"
                            />

                            <h3
                                data-tab-heading
                                class="text-sm font-bold text-ink"
                            >
                                Formulir Tindak Lanjut
                            </h3>
                        </div>

                        <p class="text-xs font-medium text-muted">
                            Isi tindakan yang sudah atau sedang dikerjakan
                        </p>
                    </div>

                    {{-- Form tindak lanjut --}}
                    <form
                        id="tab-tindak_lanjut"
                        action="{{ route('pengelola.laporan.riwayat.store', $laporan->id_laporan) }}"
                        method="POST"
                        enctype="multipart/form-data"
                        class="space-y-5"
                        data-tab-content="tindak_lanjut"
                    >
                        @csrf
                        <input type="hidden" name="jenis_tindakan" value="tindak_lanjut">

                        <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">

                            {{-- Kolom kiri --}}
                            <div class="space-y-4">

                                {{-- Jenis tindakan --}}
                                <div>
                                    <div class="mb-2 flex items-center justify-between gap-2">
                                        <label class="text-sm font-semibold text-ink">
                                            Jenis Tindakan Respons
                                        </label>

                                        <span class="text-xs font-semibold text-primary">
                                            Terkunci Otomatis
                                        </span>
                                    </div>

                                    <div class="flex items-center justify-between rounded-xl bg-soft-strong px-4 py-3">
                                        <span class="text-sm font-medium text-ink">
                                            Tindak Lanjut Lapangan
                                        </span>

                                        <x-icon name="lock" size="20" class="text-primary" />
                                    </div>
                                </div>

                                {{-- Deskripsi --}}
                                <x-form.textarea
                                    name="deskripsi"
                                    label="Deskripsi Tindakan Pengerjaan"
                                    rows="6"
                                    required
                                    counter
                                    maxlength="500"
                                    placeholder="Jelaskan tindakan yang dilakukan, misalnya pemasangan barikade tali kuning, pengukuran dimensi balok, dan penarikan material..."
                                />

                            </div>

                          
                    {{-- Kolom kanan: Upload foto --}}
                    <div class="space-y-3">
                        {{-- Label upload --}}
                        <div class="flex flex-wrap items-center justify-between gap-2">
                            <label for="foto-tindak-lanjut" class="text-sm font-semibold text-ink">
                                Unggah Foto Bukti Proses Penanganan
                                <span class="text-danger">*</span>
                            </label>

                            <span class="text-xs font-semibold text-muted">
                                Min. 1 Foto (JPG/PNG maks. 2MB)
                            </span>
                        </div>

                        {{-- Input file --}}
                        <input
                            type="file"
                            name="foto[]"
                            id="foto-tindak-lanjut"
                            accept="image/jpeg,image/png"
                            multiple
                            required
                            class="sr-only"
                        >

                        {{-- Area pilih dan tarik foto --}}
                        <label
                            for="foto-tindak-lanjut"
                            id="dropzone-tindak-lanjut"
                            class="flex h-[200px] w-full cursor-pointer flex-col items-center justify-center rounded-2xl bg-soft px-5 text-center transition hover:bg-soft-strong"
                        >
                            <span class="mb-3 flex h-[70px] w-[70px] items-center justify-center rounded-full bg-soft-strong">
                                <x-icon name="add_a_photo" size="36" class="text-primary" />
                            </span>

                            <span class="font-heading text-base font-bold text-ink">
                                Pilih Berkas Foto atau Tarik ke Sini
                            </span>

                            <span class="mt-1 text-sm font-medium text-muted">
                                Format didukung: JPG, PNG dengan metadata lokasi
                            </span>
                        </label>

                        {{-- Daftar foto terpilih --}}
                        <div id="daftar-foto-tindak-lanjut" class="space-y-2"></div>

                        {{-- Pesan validasi --}}
                        @error('foto')
                            <p class="text-sm text-danger">{{ $message }}</p>
                        @enderror

                        @error('foto.*')
                            <p class="text-sm text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    </div> {{-- PENUTUP GRID DUA KOLOM --}}

                    {{-- Footer --}}
                    <div class="flex w-full flex-wrap items-center justify-between gap-3 border-t border-line pt-4">
                        <p class="flex items-center gap-2 text-xs font-medium text-muted">
                            <x-icon name="sync" size="17" class="text-primary" />
                            Status laporan otomatis berubah menjadi "Ditindaklanjuti" saat formulir dikirim.
                        </p>

                        <div class="flex gap-2">
                            <button
                                type="reset"
                                class="rounded-lg bg-soft px-4 py-2.5 text-sm font-semibold text-ink transition hover:bg-soft-strong"
                            >
                                Batal
                            </button>

                            <x-button type="submit" icon-left="send">
                                Kirim Tindak Lanjut
                            </x-button>
                        </div>
                    </div>
                    </form>




                {{-- Form penyelesaian --}}
                <form
                    id="tab-penyelesaian"
                    action="{{ route('pengelola.laporan.riwayat.store', $laporan->id_laporan) }}"
                    method="POST"
                    enctype="multipart/form-data"
                    class="hidden space-y-5"
                    data-tab-content="penyelesaian"
                >
                    @csrf
                    <input type="hidden" name="jenis_tindakan" value="penyelesaian">

                    {{-- Grid dua kolom --}}
                    <div class="grid grid-cols-1 gap-5 lg:grid-cols-2">

                        {{-- Kolom kiri --}}
                        <div class="space-y-4">

                            {{-- Jenis tindakan --}}
                            <div>
                                <div class="mb-2 flex items-center justify-between gap-2">
                                    <label class="text-sm font-semibold text-ink">
                                        Jenis Tindakan Respons
                                    </label>

                                    <span class="text-xs font-semibold text-primary">
                                        Terkunci Otomatis
                                    </span>
                                </div>

                                <div class="flex items-center justify-between rounded-xl bg-soft-strong px-4 py-3">
                                    <span class="text-sm font-medium text-ink">
                                        Penyelesaian Kerusakan
                                    </span>

                                    <x-icon name="lock" size="20" class="text-primary" />
                                </div>
                            </div>

                            {{-- Deskripsi --}}
                            <x-form.textarea
                                name="deskripsi"
                                label="Deskripsi Hasil Penyelesaian"
                                rows="6"
                                required
                                counter
                                maxlength="500"
                                placeholder="Jelaskan hasil penyelesaian kerusakan dan kondisi fasilitas setelah diperbaiki..."
                            />

                        </div>

                        {{-- Kolom kanan: Upload foto --}}
                        <div class="space-y-3">

                            {{-- Label upload --}}
                            <div class="flex flex-wrap items-center justify-between gap-2">
                                <label for="foto-penyelesaian" class="text-sm font-semibold text-ink">
                                    Unggah Foto Bukti Hasil Penyelesaian
                                    <span class="text-danger">*</span>
                                </label>

                                <span class="text-xs font-semibold text-muted">
                                    Min. 1 Foto (JPG/PNG maks. 2MB)
                                </span>
                            </div>

                            
                            {{-- Input file --}}
                            <input
                                type="file"
                                name="foto[]"
                                id="foto-penyelesaian"
                                accept="image/jpeg,image/png"
                                multiple
                                required
                                class="sr-only"
                            />

                            {{-- Area pilih dan tarik foto --}}
                            <label
                                for="foto-penyelesaian"
                                id="dropzone-penyelesaian"
                                class="relative flex min-h-[200px] w-full cursor-pointer flex-col items-center justify-center overflow-hidden rounded-2xl bg-soft px-5 py-4 text-center transition hover:bg-soft-strong"
                            >
                                {{-- Tampilan awal --}}
                                <div id="placeholder-foto-penyelesaian" class="flex flex-col items-center">
                                    <span class="mb-3 flex h-[70px] w-[70px] items-center justify-center rounded-full bg-soft-strong">
                                        <x-icon name="add_a_photo" size="36" class="text-primary" />
                                    </span>

                                    <span class="font-heading text-base font-bold text-ink">
                                        Pilih Berkas Foto atau Tarik ke Sini
                                    </span>

                                    <span class="mt-1 text-sm font-medium text-muted">
                                        Format didukung: JPG, PNG dengan metadata lokasi
                                    </span>
                                </div>

                                {{-- Preview foto --}}
                                <div id="preview-foto-penyelesaian" class="hidden w-full">
                                    <div
                                        id="grid-preview-penyelesaian"
                                        class="grid grid-cols-2 gap-3"
                                    ></div>

                                    <p class="mt-3 text-xs font-semibold text-primary">
                                        Klik untuk menambahkan atau mengganti foto
                                    </p>
                                </div>
                            </label>

                            {{-- Daftar foto terpilih --}}
                            <div id="daftar-foto-penyelesaian" class="space-y-2"></div>


                            {{-- Pesan validasi --}}
                            @error('foto')
                                <p class="text-sm text-danger">{{ $message }}</p>
                            @enderror

                            @error('foto.*')
                                <p class="text-sm text-danger">{{ $message }}</p>
                            @enderror

                        </div>

                    </div> {{-- Penutup grid dua kolom --}}

                    {{-- Footer --}}
                    <div class="flex w-full flex-wrap items-center justify-between gap-3 border-t border-line pt-4">
                        <p class="flex items-center gap-2 text-xs font-medium text-muted">
                            <x-icon name="sync" size="17" class="text-primary" />
                            Status laporan otomatis berubah menjadi "Selesai" saat formulir dikirim.
                        </p>

                        <div class="flex gap-2">
                            <button
                                type="reset"
                                class="rounded-lg bg-soft px-4 py-2.5 text-sm font-semibold text-ink transition hover:bg-soft-strong"
                            >
                                Batal
                            </button>

                            <x-button type="submit" icon-left="send">
                                Kirim Penyelesaian
                            </x-button>
                        </div>
                    </div>
                </form>

                </div>
            </x-card>
        </div>
    </div>

    {{-- Modal preview foto --}}
    <dialog
        id="modal-foto-laporan"
        class="m-auto w-[calc(100%-2rem)] max-w-3xl rounded-2xl bg-white p-4 shadow-xl backdrop:bg-ink/70"
    >
        <div class="flex items-center justify-between gap-3">
            <h2 class="font-heading font-bold text-ink">
                Foto Bukti Laporan
            </h2>

            <button
                type="button"
                id="tutup-modal-foto"
                aria-label="Tutup foto"
                class="rounded-lg p-2 text-subtle transition hover:bg-soft hover:text-ink"
            >
                <x-icon name="close" size="22" />
            </button>
        </div>

        <div class="mt-4 flex justify-center">
            <img
                id="gambar-preview-laporan"
                src=""
                alt="Preview foto"
                class="max-h-[75vh] max-w-full rounded-xl object-contain"
            >
        </div>
    </dialog>

    

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Preview foto laporan dan foto riwayat
        const modal = document.getElementById('modal-foto-laporan');
        const gambar = document.getElementById('gambar-preview-laporan');
        const tombolTutup = document.getElementById('tutup-modal-foto');

        if (modal && gambar && tombolTutup) {
            document.querySelectorAll('[data-foto-preview]').forEach(function (tombol) {
                tombol.addEventListener('click', function () {
                    gambar.src = this.dataset.fotoPreview;
                    modal.showModal();
                });
            });

            tombolTutup.addEventListener('click', function () {
                modal.close();
            });

            modal.addEventListener('click', function (event) {
                if (event.target === modal) {
                    modal.close();
                }
            });

            modal.addEventListener('close', function () {
                gambar.src = '';
            });
        }

        // Tab formulir
        const tombolTab = document.querySelectorAll('[data-tab-button]');
        const isiTab = document.querySelectorAll('[data-tab-content]');

        function pilihTab(jenis) {
            tombolTab.forEach(function (tombol) {
                const aktif = tombol.dataset.tabButton === jenis;

                tombol.classList.toggle('bg-primary', aktif);
                tombol.classList.toggle('text-white', aktif);
                tombol.classList.toggle('text-muted', !aktif);
            });

            isiTab.forEach(function (form) {
                form.classList.toggle('hidden', form.dataset.tabContent !== jenis);
            });

            // Perbarui judul formulir
            const judulForm = document.querySelector('[data-tab-heading]');

            if (judulForm) {
                judulForm.textContent = jenis === 'penyelesaian'
                    ? 'Formulir Penyelesaian'
                    : 'Formulir Tindak Lanjut';
            }
        }

        tombolTab.forEach(function (tombol) {
            tombol.addEventListener('click', function () {
                pilihTab(this.dataset.tabButton);
            });
        });

        document.querySelectorAll('[data-pilih-tab]').forEach(function (tombol) {
            tombol.addEventListener('click', function () {
                pilihTab(this.dataset.pilihTab);

                document.getElementById('form-respons').scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            });
        });

        pilihTab(@json($tabAktif));

        // Upload foto: tindak lanjut dan penyelesaian
        
function aktifkanUploadFoto(jenis) {
    const inputFoto = document.getElementById('foto-' + jenis);
    const dropzone = document.getElementById('dropzone-' + jenis);
    const daftarFoto = document.getElementById('daftar-foto-' + jenis);

    if (!inputFoto || !dropzone || !daftarFoto) {
        return;
    }

    let files = [];

    // Simpan tampilan awal area upload
    const tampilanAwal = dropzone.innerHTML;

    function tampilkanFile() {
        daftarFoto.innerHTML = '';

        // Kosongkan area upload
        dropzone.innerHTML = '';

        if (files.length === 0) {
            // Kembalikan tampilan awal jika tidak ada foto
            dropzone.innerHTML = tampilanAwal;
            return;
        }

        // Buat area preview foto
        const container = document.createElement('div');
        container.className = 'grid w-full grid-cols-2 gap-3';

        files.forEach(function (file, index) {
            const reader = new FileReader();

            reader.onload = function (event) {
                const item = document.createElement('div');
                item.className = 'overflow-hidden rounded-xl bg-soft-strong';

                const gambar = document.createElement('img');
                gambar.src = event.target.result;
                gambar.alt = 'Preview foto ' + (index + 1);
                gambar.className = 'h-36 w-full rounded-xl object-cover';

                item.appendChild(gambar);
                container.appendChild(item);
            };

            reader.readAsDataURL(file);

            // Daftar nama file
            const ukuran = (file.size / (1024 * 1024)).toFixed(1);

            const item = document.createElement('div');
            item.className = 'flex items-center justify-between gap-3 rounded-xl bg-soft-strong px-4 py-3';

            const nama = document.createElement('span');
            nama.className = 'min-w-0 truncate text-sm font-medium text-ink';
            nama.textContent = file.name + ' (' + ukuran + ' MB)';

            const hapus = document.createElement('button');
            hapus.type = 'button';
            hapus.className = 'shrink-0 text-danger hover:opacity-70';
            hapus.innerHTML = '<span class="material-symbols-outlined">close</span>';
            hapus.setAttribute('aria-label', 'Hapus ' + file.name);

            hapus.addEventListener('click', function () {
                files.splice(index, 1);
                sinkronkanInput();
            });

            item.append(nama, hapus);
            daftarFoto.appendChild(item);
        });

        // Masukkan preview ke area upload
        dropzone.appendChild(container);

        const keterangan = document.createElement('span');
        keterangan.className = 'mt-3 text-xs font-semibold text-primary';
        keterangan.textContent = 'Klik untuk menambahkan atau mengganti foto';

        dropzone.appendChild(keterangan);
    }

    function sinkronkanInput() {
        const transfer = new DataTransfer();

        files.forEach(function (file) {
            transfer.items.add(file);
        });

        inputFoto.files = transfer.files;
        tampilkanFile();
    }

    function tambahFile(fileBaru) {
        const valid = fileBaru.filter(function (file) {
            return ['image/jpeg', 'image/png'].includes(file.type);
        });

        valid.forEach(function (file) {
            const sudahAda = files.some(function (existing) {
                return existing.name === file.name &&
                    existing.size === file.size &&
                    existing.lastModified === file.lastModified;
            });

            if (!sudahAda) {
                files.push(file);
            }
        });

        sinkronkanInput();
    }

    // Pilih foto melalui input
    inputFoto.addEventListener('change', function () {
        tambahFile(Array.from(inputFoto.files));
    });

    // Drag and drop
    ['dragenter', 'dragover'].forEach(function (event) {
        dropzone.addEventListener(event, function (e) {
            e.preventDefault();
            dropzone.classList.add('ring-2', 'ring-primary');
        });
    });

    ['dragleave', 'drop'].forEach(function (event) {
        dropzone.addEventListener(event, function (e) {
            e.preventDefault();
            dropzone.classList.remove('ring-2', 'ring-primary');
        });
    });

    dropzone.addEventListener('drop', function (e) {
        tambahFile(Array.from(e.dataTransfer.files));
    });

    // Reset formulir
    if (inputFoto.form) {
        inputFoto.form.addEventListener('reset', function () {
            files = [];
            inputFoto.value = '';
            tampilkanFile();
        });
    }
}

        aktifkanUploadFoto('tindak-lanjut');
        aktifkanUploadFoto('penyelesaian');

        // Buka/tutup form edit riwayat
        document.querySelectorAll('[data-toggle-edit]').forEach(function (tombol) {
            tombol.addEventListener('click', function () {
                const id = this.dataset.toggleEdit;
                const formEdit = document.getElementById('edit-riwayat-' + id);

                if (formEdit) {
                    formEdit.classList.toggle('hidden');
                }
            });
        });
    });
</script>


</x-layouts.pengelola>
