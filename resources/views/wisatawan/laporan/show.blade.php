<x-layouts.public title="Detail Laporan" cta="none">
    <div class="min-h-screen bg-[#f8faff]">
        <div class="mx-auto max-w-7xl px-6 py-8">

            {{-- Breadcrumb dan tombol kembali --}}
            <div class="mb-6 flex flex-wrap items-center justify-between gap-3 pt-6 pb-4">
                <div class="flex flex-wrap items-center gap-2 text-[13px] font-medium text-[#3e4944]">
                    <a href="{{ route('wisatawan.beranda') }}"
                        class="inline-flex items-center gap-1.5 hover:text-[#005f49]">
                        <x-icon name="home" size="16" />
                        Beranda Wisatawan
                    </a>

                    <span class="text-[#6e7a74]">/</span>

                    <a href="{{ route('laporan.create', $laporan->id_destinasi) }}"
                        class="hover:text-[#005f49]">
                        {{ $laporan->destinasi->nama }}
                    </a>

                    <span class="text-[#6e7a74]">/</span>

                    <span class="rounded-md bg-[#dee8ff] px-2 py-0.5 font-bold text-[#111c2d]">
                        #{{ $laporan->id_laporan }}
                    </span>
                </div>

                <a href="{{ route('laporan.create', $laporan->id_destinasi) }}"
                    class="inline-flex shrink-0 items-center gap-1.5 rounded-lg bg-[#e7eeff] px-[14px] py-2 text-xs font-semibold leading-4 text-[#111c2d] transition-colors hover:bg-[#dee8ff]">
                    <x-icon name="arrow_back" size="16" />
                    Kembali ke Detail Destinasi
                </a>
            </div>

            {{-- Identitas pemilik laporan --}}
            <div class="mb-6 rounded-xl border border-[#dee8ff] bg-white px-5 py-4">
                <p class="text-sm text-[#6e7a74]">
                    {{ $laporanSaya ? 'Laporan Milik Anda' : 'Laporan Wisatawan' }}
                </p>
                <p class="mt-1 font-semibold text-[#111c2d]">
                    {{ $laporan->pelapor->username }}
                </p>
            </div>

            {{-- Konten utama: detail dan riwayat --}}
            <div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-[1.62fr_1fr]">

                {{-- KIRI: Detail laporan --}}
                <section class="min-w-0 space-y-6">

                    {{-- Identitas dan status laporan --}}
                    <div class="rounded-2xl border border-[#e5eaf3] bg-white p-6">
                        <div class="mb-4 flex flex-wrap items-center gap-2">
                            @php
                                $statusClass = match ($laporan->status) {
                                    'belum_dilihat' => 'bg-gray-100 text-gray-600',
                                    'dilihat' => 'bg-blue-50 text-blue-700',
                                    'ditindaklanjuti' => 'bg-amber-50 text-amber-700',
                                    'selesai' => 'bg-green-50 text-green-700',
                                    default => 'bg-gray-100 text-gray-600',
                                };

                                $statusLabel = match ($laporan->status) {
                                    'belum_dilihat' => 'Belum Dilihat',
                                    'dilihat' => 'Dilihat',
                                    'ditindaklanjuti' => 'Ditindaklanjuti',
                                    'selesai' => 'Selesai',
                                    default => $laporan->status,
                                };
                            @endphp

                            <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $statusClass }}">
                                {{ $statusLabel }}
                            </span>

                            <span class="rounded-full bg-[#e7eeff] px-3 py-1 text-xs font-semibold text-[#34466b]">
                                {{ $laporan->kategoriKerusakan->nama }}
                            </span>
                        </div>

                        <h1 class="font-heading text-2xl font-bold leading-snug text-[#111c2d]">
                            {{ $laporan->judul }}
                        </h1>
                    </div>

                    {{-- Bukti Fotografi Kerusakan --}}
                    <div class="rounded-2xl border border-[#edf0f5] bg-white p-5 shadow-sm">
                        <h2 class="mb-3 flex items-center gap-2 font-heading text-lg font-bold text-[#111c2d]">
                            <x-icon name="photo_library" size="20" class="text-[#008568]" />
                            Bukti Fotografi Kerusakan
                        </h2>

                        @if ($laporan->foto->isNotEmpty())
                            <div class="grid h-[340px] grid-cols-[2fr_1fr] gap-2.5 overflow-hidden rounded-xl">

                                {{-- Foto utama --}}
                                <div class="group relative min-w-0 overflow-hidden rounded-xl">
                                    <img
                                        src="{{ $laporan->foto->first()->url }}"
                                        alt="Foto bukti utama"
                                        class="h-full w-full object-cover"
                                    >

                                    <div class="absolute left-2.5 top-2.5 rounded-full bg-[#344253]/80 px-2.5 py-1 text-xs font-semibold text-white">
                                        <span class="inline-flex items-center gap-1">
                                            <x-icon name="camera" size="14" />
                                            Foto Bukti Wisatawan (1 dari {{ $laporan->foto->count() }})
                                        </span>
                                    </div>

                                    <button
                                        type="button"
                                        onclick="window.open('{{ $laporan->foto->first()->url }}', '_blank')"
                                        class="absolute bottom-2.5 left-2.5 inline-flex items-center gap-1 rounded-md bg-white/70 px-2 py-1 text-xs font-semibold text-white backdrop-blur-sm transition hover:bg-white/90"
                                    >
                                        <x-icon name="zoom_in" size="14" />
                                        Perbesar
                                    </button>
                                </div>

                                {{-- Foto lainnya --}}
                                <div class="grid min-h-0 grid-rows-{{ min($laporan->foto->count() - 1, 2) }} gap-2.5">
                                    @foreach ($laporan->foto->skip(1) as $index => $foto)
                                        <div class="relative min-h-0 overflow-hidden rounded-xl">
                                            <img
                                                src="{{ $foto->url }}"
                                                alt="Foto bukti {{ $index + 2 }}"
                                                class="h-full w-full object-cover"
                                            >

                                            <span class="absolute left-2.5 top-2.5 rounded-full bg-[#344253]/80 px-2.5 py-1 text-xs font-semibold text-white">
                                                Foto {{ $index + 2 }} dari {{ $laporan->foto->count() }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <div class="rounded-xl bg-[#f0f3ff] p-8 text-center text-sm text-[#6e7a74]">
                                Tidak ada foto laporan.
                            </div>
                        @endif
                    </div>

                    {{-- Rincian Administratif --}}
                    <section class="rounded-2xl border border-[#edf0f5] bg-white p-6 shadow-sm">
                        <h2 class="mb-4 flex items-center gap-2 font-heading text-lg font-semibold text-[#111c2d]">
                            <x-icon name="fact_check" size="18" class="text-[#005f49]" />
                            Rincian Administratif Laporan
                        </h2>

                        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">

                            {{-- Destinasi --}}
                            <div class="rounded-lg bg-[#f0f3ff] p-3">
                                <p class="mb-1 text-[11px] font-bold uppercase tracking-wide text-[#6e7a74]">
                                    Destinasi
                                </p>
                                <div class="flex items-start gap-2 font-heading text-[15px] font-medium text-[#005f49]">
                                    <x-icon name="flag" size="16" class="mt-0.5 shrink-0" />
                                    {{ $laporan->destinasi->nama }}
                                </div>
                            </div>

                            {{-- Lokasi spesifik --}}
                            <div class="rounded-lg bg-[#f0f3ff] p-3">
                                <p class="mb-1 text-[11px] font-bold uppercase tracking-wide text-[#6e7a74]">
                                    Lokasi Spesifik
                                </p>
                                <div class="font-heading text-[15px] font-medium text-[#111c2d]">
                                    {{ $laporan->lokasi_spesifik }}
                                </div>
                            </div>

                            {{-- Kategori kerusakan --}}
                            <div class="rounded-lg bg-[#f0f3ff] p-3">
                                <p class="mb-1 text-[11px] font-bold uppercase tracking-wide text-[#6e7a74]">
                                    Kategori Kerusakan
                                </p>
                                <div class="font-heading text-[15px] font-medium text-[#111c2d]">
                                    {{ $laporan->kategoriKerusakan->nama }}
                                </div>
                            </div>

                            {{-- Pelapor --}}
                            <div class="rounded-lg bg-[#f0f3ff] p-3">
                                <p class="mb-1 text-[11px] font-bold uppercase tracking-wide text-[#6e7a74]">
                                    Dilaporkan Oleh
                                </p>
                                <div class="flex items-start gap-2 font-heading text-[15px] font-medium text-[#111c2d]">
                                    <x-icon name="badge" size="16" class="mt-0.5 shrink-0 text-[#005f49]" />
                                    {{ $laporan->pelapor->username }}
                                </div>
                            </div>

                            {{-- Waktu pembuatan --}}
                            <div class="rounded-lg bg-[#f0f3ff] p-3">
                                <p class="mb-1 text-[11px] font-bold uppercase tracking-wide text-[#6e7a74]">
                                    Waktu Pembuatan
                                </p>
                                <div class="font-body text-[15px] font-normal text-[#111c2d]">
                                    {{ $laporan->created_at?->format('d M Y, H:i') }} WIB
                                </div>
                            </div>

                            {{-- Pembaruan terakhir --}}
                            <div class="rounded-lg bg-[#f0f3ff] p-3">
                                <p class="mb-1 text-[11px] font-bold uppercase tracking-wide text-[#6e7a74]">
                                    Pembaruan Terakhir
                                </p>
                                <div class="font-body text-[15px] font-normal text-[#111c2d]">
                                    @if ($riwayatTerakhir)
                                        {{ $riwayatTerakhir->created_at?->format('d M Y, H:i') }} WIB
                                        oleh {{ $riwayatTerakhir->pengelola?->username ?? 'Pengelola' }}
                                    @else
                                        Belum ada pembaruan
                                    @endif
                                </div>
                            </div>

                        </div>
                    </section>

                    {{-- Deskripsi Pelapor --}}
                    <div class="rounded-2xl border border-[#edf0f5] bg-white p-5 shadow-sm">
                        <h2 class="mb-4 flex items-center gap-2 font-heading text-lg font-bold text-[#111c2d]">
                            <x-icon name="notes" size="20" class="text-[#008568]" />
                            Deskripsi Pelapor
                        </h2>

                        <div class="rounded-xl bg-[#f0f3ff] p-4">
                            <p class="text-sm leading-7 text-[#3e4944]">
                                {{ $laporan->deskripsi }}
                            </p>
                        </div>
                    </div>

                    {{-- Tindakan terhadap Laporan --}}
                    <div class="rounded-2xl border border-[#edf0f5] bg-white p-5 shadow-sm">
                        <div class="mb-3 flex flex-wrap items-center justify-between gap-2">
                            <h2 class="flex items-center gap-2 font-heading text-lg font-bold text-[#111c2d]">
                                <x-icon name="gavel" size="20" class="text-[#008568]" />
                                Tindakan terhadap Laporan
                            </h2>

                            <span class="rounded-md bg-[#dee8ff] px-2.5 py-1 text-xs font-semibold text-[#6e7a74]">
                                Mode:
                                {{ ($laporanSaya && $bolehDiubah) ? 'Dapat Diubah' : 'Terkunci' }}
                            </span>
                        </div>

                        <div class="flex flex-wrap gap-2">
                            <button
                                id="btn-edit-laporan"
                                type="button"
                                @disabled(!($laporanSaya && $bolehDiubah))
                                class="inline-flex items-center gap-1.5 rounded-lg bg-[#005F49] px-3.5 py-2.5 text-sm font-medium text-white transition hover:brightness-95 disabled:cursor-not-allowed disabled:text-[#8d99ac]"
                            >
                                @if ($laporanSaya && $bolehDiubah)
                                    <x-icon name="edit" size="17" />
                                @else
                                    <x-icon name="lock" size="17" />
                                @endif
                                Edit Laporan
                            </button>

                            <form
                                action="{{ route('laporan.destroy', $laporan->id_laporan) }}"
                                method="POST"
                                onsubmit="return confirm('Yakin ingin menghapus laporan ini?')"
                            >
                                @csrf
                                @method('DELETE')

                                <button
                                    type="submit"
                                    @disabled(!($laporanSaya && $bolehDiubah))
                                    class="inline-flex items-center gap-1.5 rounded-lg bg-[#BA1A1A] px-3.5 py-2.5 text-sm font-medium text-white transition-all duration-200 hover:-translate-y-0.5 hover:brightness-90 disabled:cursor-not-allowed disabled:text-[#8d99ac] disabled:hover:translate-y-0 disabled:hover:brightness-100"
                                >
                                    @if ($laporanSaya && $bolehDiubah)
                                        <x-icon name="delete" size="17" />
                                    @else
                                        <x-icon name="lock" size="17" />
                                    @endif
                                    Hapus Laporan
                                </button>
                            </form>

                            <button
                                type="button"
                                onclick="navigator.clipboard.writeText(window.location.href)"
                                class="inline-flex items-center gap-1.5 rounded-lg bg-[#e7eeff] px-3.5 py-2.5 text-sm font-medium text-[#111c2d] transition-all duration-200 hover:-translate-y-0.5 hover:brightness-90"
                            >
                                <x-icon name="share" size="17" />
                                Salin Tautan Publik
                            </button>
                        </div>
                    </div>
                </section>

                {{-- KANAN: Riwayat penanganan --}}
                <section class="min-w-0">
                    <div class="rounded-xl bg-white p-6 shadow-sm">

                        <h2 class="mb-1 font-heading text-2xl font-semibold leading-8 text-[#111c2d]">
                            Riwayat Penanganan
                        </h2>

                        <p class="mb-3 text-sm leading-5 text-[#3e4944]">
                            Pantau perkembangan penanganan laporan secara transparan dan berurutan.
                        </p>

                        {{-- Identitas pengelola --}}
                        <div class="mb-6 flex flex-wrap items-center gap-1.5 rounded-lg bg-[#dee8ff] px-2 py-1 text-[11px] font-semibold leading-[18px] text-[#111c2d]">
                            <x-icon name="business" size="15" />
                            Pengelola:
                            <span class="font-medium text-[#005f49]">
                                {{ $pengelolaDestinasi?->username ?? 'Belum ditetapkan' }}
                            </span>
                        </div>

                        <ol class="relative list-none">

                            {{-- Tahap 1: Laporan dikirim --}}
                            <li class="relative pb-[26px] pl-11
                                before:absolute before:bottom-[-2px] before:left-[14px] before:top-[30px] before:w-0.5 before:bg-[#005f49]">

                                <span class="absolute left-0 top-0 z-10 flex h-[30px] w-[30px] items-center justify-center rounded-full bg-[#005f49] text-white">
                                    <x-icon name="check" size="17" />
                                </span>

                                <div class="flex items-start justify-between gap-2">
                                    <h3 class="font-heading text-[17px] font-medium leading-6 text-[#111c2d]">
                                        1. Laporan Dikirim
                                    </h3>

                                    <div class="flex flex-wrap justify-end gap-1.5">
                                        <span class="rounded-md bg-[#98f4d3] px-2.5 py-0.5 text-[11px] font-semibold leading-4 text-[#00513e]">
                                            Selesai
                                        </span>
                                    </div>
                                </div>

                                <p class="mb-1.5 text-[11px] font-medium leading-4 text-[#6e7a74]">
                                    {{ $laporan->created_at?->format('d M Y, H:i') }}
                                </p>

                                <p class="text-sm leading-5 text-[#3e4944]">
                                    Laporan berhasil masuk ke sistem pengawasan SwaraWisata secara terenkripsi.
                                </p>
                            </li>

                            {{-- Tahap 2: Dilihat pengelola --}}
                            <li class="relative pb-[26px] pl-11
                                before:absolute before:bottom-[-2px] before:left-[14px] before:top-[30px] before:w-0.5
                                {{ $laporan->dilihat_pada ? 'before:bg-[#005f49]' : 'before:border-l-2 before:border-dashed before:border-[#aab4c6]' }}">

                                <span class="absolute left-0 top-0 z-10 flex h-[30px] w-[30px] items-center justify-center rounded-full
                                    {{ $laporan->dilihat_pada ? 'bg-[#005f49] text-white' : 'bg-[#dee8ff] text-[#8c97aa] ring-2 ring-white' }}">
                                    <x-icon name="visibility" size="17" />
                                </span>

                                <div class="flex items-start justify-between gap-2">
                                    <h3 class="font-heading text-[17px] font-medium leading-6 text-[#111c2d]">
                                        2. Dilihat Pengelola
                                    </h3>

                                    <div class="flex flex-wrap justify-end gap-1.5">
                                        <span class="rounded-md px-2.5 py-0.5 text-[11px] font-semibold leading-4
                                            {{ $laporan->dilihat_pada ? 'bg-[#dee8ff] text-[#005f49]' : 'bg-[#dee8ff] text-[#6e7a74]' }}">
                                            {{ $laporan->dilihat_pada ? 'Terverifikasi' : 'Menunggu' }}
                                        </span>
                                    </div>
                                </div>

                                @if ($laporan->dilihat_pada)
                                    <p class="mb-1.5 text-[11px] font-medium leading-4 text-[#6e7a74]">
                                        {{ $laporan->dilihat_pada->format('d M Y, H:i') }}
                                    </p>

                                    <p class="text-sm leading-5 text-[#3e4944]">
                                        Laporan telah dilihat dan ditinjau oleh pengelola
                                        {{ $pengelolaDestinasi?->username ?? 'destinasi ini' }}.
                                    </p>
                                @else
                                    <p class="text-sm leading-5 text-[#6e7a74]">
                                        Menunggu laporan dilihat dan diverifikasi oleh pengelola.
                                    </p>
                                @endif
                            </li>

                            {{-- Tahap 3: Ditindaklanjuti --}}
                            <li class="relative pb-[26px] pl-11
                                before:absolute before:bottom-[-2px] before:left-[14px] before:top-[30px] before:w-0
                                before:border-l-2 before:border-dashed before:border-[#aab4c6]">

                                <span class="absolute left-0 top-0 z-10 flex h-[30px] w-[30px] items-center justify-center rounded-full
                                    {{ $riwayatTindakLanjut->isNotEmpty() && !$riwayatPenyelesaian ? 'bg-[#fba51a] text-white shadow-[0_0_0_4px_#ffeacb]' : ($riwayatPenyelesaian ? 'bg-[#005f49] text-white' : 'bg-[#dee8ff] text-[#8c97aa] ring-2 ring-white') }}">
                                    <x-icon name="build" size="17" />
                                </span>

                                <div class="flex items-start justify-between gap-2">
                                    <h3 class="font-heading text-[17px] font-medium leading-6 text-[#111c2d]">
                                        3. Ditindaklanjuti
                                    </h3>

                                    <div class="flex flex-wrap justify-end gap-1.5">
                                        @if ($jumlahKomentar > 0)
                                            <span class="inline-flex items-center gap-1 rounded-md bg-[#e7eeff] px-2.5 py-0.5 text-[11px] font-semibold leading-4 text-[#005f49]">
                                                <x-icon name="chat_bubble" size="13" />
                                                {{ $jumlahKomentar }} Komentar
                                            </span>
                                        @endif

                                        @if ($riwayatPenyelesaian)
                                            <span class="rounded-md bg-[#98f4d3] px-2.5 py-0.5 text-[11px] font-semibold leading-4 text-[#00513e]">
                                                Selesai
                                            </span>
                                        @elseif ($riwayatTindakLanjut->isNotEmpty())
                                            <span class="rounded-md bg-[#ffddb8] px-2.5 py-0.5 text-[11px] font-semibold leading-4 text-[#653e00]">
                                                Tahap Terkini
                                            </span>
                                        @else
                                            <span class="rounded-md bg-[#dee8ff] px-2.5 py-0.5 text-[11px] font-semibold leading-4 text-[#6e7a74]">
                                                Menunggu
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                @if ($riwayatTindakLanjut->isNotEmpty())
                                    @foreach ($riwayatTindakLanjut as $riwayat)
                                        <p class="{{ $loop->first ? 'mb-1.5' : 'mt-3 mb-1.5' }} text-[11px] font-medium leading-4 text-[#6e7a74]">
                                            {{ $riwayat->created_at?->format('d M Y, H:i') }}
                                        </p>

                                        <p class="text-sm leading-5 text-[#111c2d]">
                                            {{ $riwayat->deskripsi }}
                                        </p>

                                        {{-- Detail tindakan lapangan --}}
                                        <div class="mt-2 overflow-hidden rounded-lg bg-[#f0f3ff]">

                                            <details open class="group">
                                                <summary class="flex cursor-pointer list-none items-center justify-between gap-2 px-2 py-2 font-heading text-xs font-semibold leading-4 text-[#005f49]">
                                                    <span class="flex items-center gap-1.5">
                                                        <x-icon name="groups" size="16" />
                                                        Tindakan di Lapangan #{{ $loop->iteration }}
                                                    </span>

                                                    <x-icon name="expand_more" size="17"
                                                        class="text-[#6e7a74] transition-transform group-open:rotate-180" />
                                                </summary>

                                                <div class="px-2 pb-2">

                                                    <p class="text-[13px] leading-5 text-[#3e4944]">
                                                        {{ $riwayat->deskripsi }}
                                                    </p>

                                                    {{-- Dokumentasi progres --}}
                                                    @if ($riwayat->foto->isNotEmpty())
                                                        <p class="mb-1 mt-2 font-heading text-[11px] font-semibold leading-4 text-[#3e4944]">
                                                            Dokumentasi Progres Lapangan Pengelola:
                                                        </p>

                                                        <div class="grid grid-cols-2 gap-1">
                                                            @foreach ($riwayat->foto as $foto)
                                                                <a href="{{ $foto->url }}"
                                                                    target="_blank"
                                                                    class="block h-[90px] overflow-hidden rounded-md">
                                                                    <img src="{{ $foto->url }}"
                                                                        alt="Dokumentasi progres penanganan"
                                                                        class="h-full w-full object-cover transition hover:scale-105">
                                                                </a>
                                                            @endforeach
                                                        </div>
                                                    @endif

                                                    {{-- Komentar --}}
                                                    @if ($riwayat->komentar->isNotEmpty())
                                                        <div class="mt-3 border-t border-[#dee8ff] pt-2">
                                                            <p class="mb-2 font-heading text-[11px] font-semibold text-[#3e4944]">
                                                                Diskusi Penanganan
                                                            </p>

                                                            <div class="space-y-3">
                                                                @foreach ($riwayat->komentar as $komentar)
                                                                    <div>
                                                                        <p class="text-xs font-semibold text-[#111c2d]">
                                                                            {{ $komentar->penulis?->username ?? 'Pengguna' }}
                                                                        </p>

                                                                        <p class="text-[13px] leading-5 text-[#3e4944]">
                                                                            {{ $komentar->isi }}
                                                                        </p>

                                                                        @if ($komentar->balasan->isNotEmpty())
                                                                            <div class="ml-3 mt-2 space-y-2 border-l-2 border-[#dee8ff] pl-3">
                                                                                @foreach ($komentar->balasan as $balasan)
                                                                                    <div>
                                                                                        <p class="text-xs font-semibold text-[#111c2d]">
                                                                                            {{ $balasan->penulis?->username ?? 'Pengguna' }}
                                                                                        </p>

                                                                                        <p class="text-[13px] leading-5 text-[#3e4944]">
                                                                                            {{ $balasan->isi }}
                                                                                        </p>
                                                                                    </div>
                                                                                @endforeach
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </details>
                                        </div>
                                    @endforeach
                                @else
                                    <p class="text-sm leading-5 text-[#6e7a74]">
                                        Laporan belum memasuki tahap tindak lanjut oleh pengelola.
                                    </p>
                                @endif
                            </li>

                            {{-- Tahap 4: Selesai --}}
                            <li class="relative pl-11">

                                <span class="absolute left-0 top-0 z-10 flex h-[30px] w-[30px] items-center justify-center rounded-full
                                    {{ $riwayatPenyelesaian ? 'bg-[#005f49] text-white' : 'bg-[#dee8ff] text-[#8c97aa] ring-2 ring-white' }}">
                                    <x-icon name="check_circle" size="17" />
                                </span>

                                <div class="flex items-start justify-between gap-2">
                                    <h3 class="font-heading text-[17px] font-medium leading-6
                                        {{ $riwayatPenyelesaian ? 'text-[#111c2d]' : 'text-[#6e7a74]' }}">
                                        4. Selesai
                                    </h3>

                                    <span class="rounded-md px-2.5 py-0.5 text-[11px] font-semibold leading-4
                                        {{ $riwayatPenyelesaian ? 'bg-[#98f4d3] text-[#00513e]' : 'bg-[#dee8ff] text-[#6e7a74]' }}">
                                        {{ $riwayatPenyelesaian ? 'Selesai Ditangani' : 'Menunggu' }}
                                    </span>
                                </div>

                                @if ($riwayatPenyelesaian)
                                    <p class="mb-1.5 text-[11px] font-medium leading-4 text-[#6e7a74]">
                                        {{ $riwayatPenyelesaian->created_at?->format('d M Y, H:i') }}
                                    </p>

                                    <p class="text-sm leading-5 text-[#3e4944]">
                                        {{ $riwayatPenyelesaian->deskripsi }}
                                    </p>
                                @else
                                    <p class="mt-1.5 text-sm leading-5 text-[#6e7a74]">
                                        Menunggu penyelesaian dan uji kelayakan fasilitas oleh pengelola. Pengelola akan mengunggah foto bukti fasilitas yang telah pulih setelah seluruh perbaikan selesai.
                                    </p>
                                @endif
                            </li>
                        </ol>
                    </div>
                </section>
            </div>

            {{-- Laporan Terkait Lainnya --}}
            <section class="mt-10">
                <div class="mb-5">
                    <p class="text-xs font-semibold uppercase tracking-wider text-[#008568]">
                        Pantau Area Sekitar
                    </p>

                    <h2 class="mt-1 font-heading text-xl font-bold text-[#111c2d]">
                        Laporan Terkait Lainnya di {{ $laporan->destinasi->nama }}
                    </h2>
                </div>

                @if ($laporanLain->isNotEmpty())
                    <div class="grid gap-4 md:grid-cols-2">
                        @foreach ($laporanLain as $item)
                            <article class="rounded-xl border border-[#edf0f5] bg-white p-5 shadow-sm">
                                <div class="mb-3 flex items-center gap-2">
                                    <span class="h-2 w-2 rounded-full {{ $item->status_tampilan['dot'] }}"></span>

                                    <span class="rounded-md px-2.5 py-1 text-xs font-semibold {{ $item->status_tampilan['class'] }}">
                                        {{ $item->status_tampilan['label'] }}
                                    </span>
                                </div>

                                <h3 class="font-heading font-bold text-[#111c2d]">
                                    {{ $item->judul }}
                                </h3>

                                <p class="mt-2 text-sm leading-relaxed text-[#6e7a74]">
                                    {{ \Illuminate\Support\Str::limit($item->deskripsi, 120) }}
                                </p>

                                <div class="mt-4 flex flex-wrap items-center justify-between gap-3 border-t border-[#edf0f5] pt-3">
                                    <span class="flex items-center gap-1.5 text-xs text-[#6e7a74]">
                                        <x-icon name="location_on" size="16" />
                                        {{ $item->lokasi_spesifik }}
                                    </span>

                                    <a
                                        href="{{ route('laporan.show', $item) }}"
                                        class="inline-flex items-center gap-1 text-sm font-semibold text-[#008568] transition hover:gap-2"
                                    >
                                        Buka Detail
                                        <x-icon name="arrow_forward" size="16" />
                                    </a>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="rounded-xl border border-dashed border-[#d9e0eb] bg-white px-5 py-8 text-center">
                        <p class="text-sm text-[#6e7a74]">
                            Belum ada laporan lain di destinasi ini.
                        </p>
                    </div>
                @endif
            </section>
        </div>
    </div>

    {{-- Modal Edit Laporan --}}
    <div
        id="modal-edit-laporan"
        class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/50 p-4"
    >
        <div class="relative max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl bg-white p-6 shadow-xl sm:p-8">

            {{-- Header Modal --}}
            <div class="mb-6 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <span class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-primary text-white">
                        <x-icon name="camera_alt" size="24" />
                    </span>

                    <div>
                        <h2 class="font-heading text-xl font-bold text-ink">
                            Edit Laporan
                        </h2>
                        <p class="text-sm text-muted">
                            Perbarui informasi laporan kerusakan
                        </p>
                    </div>
                </div>

                <button
                    id="btn-tutup-modal-edit"
                    type="button"
                    class="flex size-9 items-center justify-center rounded-lg text-muted transition hover:bg-soft hover:text-ink"
                >
                    <x-icon name="close" size="22" />
                </button>
            </div>
            <form
                action="{{ route('laporan.update', $laporan->id_laporan) }}"
                method="POST"
                enctype="multipart/form-data"
                class="space-y-5"
            >
                @csrf
                @method('PUT')

                {{-- Destinasi Terpilih --}}
                <div>
                    <div class="mb-2 flex items-center justify-between">
                        <label class="font-heading text-sm font-medium text-ink">
                            Destinasi Terpilih
                        </label>
                        <span class="flex items-center gap-1 text-xs font-medium text-muted">
                            <x-icon name="lock" size="14" />
                            Terkunci Otomatis
                        </span>
                    </div>

                    <div class="flex items-center gap-2 rounded-xl bg-soft-strong px-4 py-3 font-heading text-sm font-semibold text-ink">
                        <x-icon name="flag" size="18" class="text-primary" />
                        {{ $laporan->destinasi->nama }}
                    </div>
                </div>

                {{-- Judul --}}
                <x-form.input
                    name="judul"
                    label="Judul Laporan Kerusakan"
                    placeholder="Masukkan judul kerusakan"
                    maxlength="80"
                    counter
                    required
                    variant="soft"
                    :value="$laporan->judul"
                />

                {{-- Kategori --}}
                <x-form.select
                    name="id_kategori_kerusakan"
                    label="Kategori Kerusakan"
                    :options="$kategoriKerusakan->pluck('nama', 'id_kategori_kerusakan')"
                    :value="$laporan->id_kategori_kerusakan"
                    placeholder="Pilih kategori"
                    required
                />

                {{-- Lokasi Spesifik --}}
                <x-form.input
                    name="lokasi_spesifik"
                    label="Lokasi Spesifik Titik Kerusakan"
                    placeholder="Sebutkan titik lokasi kerusakan"
                    icon="location_on"
                    required
                    variant="soft"
                    :value="$laporan->lokasi_spesifik"
                />

                {{-- Deskripsi --}}
                <x-form.textarea
                    name="deskripsi"
                    label="Deskripsi Kerusakan Terperinci"
                    placeholder="Jelaskan kondisi kerusakan secara terperinci..."
                    rows="4"
                    maxlength="1000"
                    required
                    class="bg-soft"
                >{{ $laporan->deskripsi }}</x-form.textarea>

                {{-- Bukti Foto --}}
                <div>
                    <label class="mb-2 block font-heading text-sm font-medium text-ink">
                        Bukti Foto Kerusakan
                    </label>

                    <div class="mb-3 flex flex-wrap gap-3">
                        @foreach ($laporan->foto as $foto)
                            <img
                                src="{{ $foto->url }}"
                                alt="Foto laporan"
                                class="size-20 rounded-lg object-cover"
                            >
                        @endforeach
                    </div>

                    <label
                        for="foto-edit"
                        class="flex cursor-pointer flex-col items-center gap-2 rounded-xl border border-dashed border-[#b6c6e3] bg-soft p-6 text-center transition hover:bg-soft-strong"
                    >
                        <x-icon name="cloud_upload" size="24" />
                        <span class="text-sm text-ink">
                            Tambahkan foto baru
                            <span class="font-semibold text-primary underline">Pilih File</span>
                        </span>
                        <span class="text-xs text-muted">Maksimal 3 gambar (JPG/PNG)</span>
                    </label>

                    <input
                        type="file"
                        name="foto[]"
                        id="foto-edit"
                        accept="image/jpeg,image/png,image/webp"
                        multiple
                        data-max-foto-baru="{{ max(0, 3 - $laporan->foto->count()) }}"
                        class="hidden"
                    >
                </div>

                {{-- Tombol --}}
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button
                        type="button"
                        id="btn-batal-edit"
                        class="rounded-xl px-5 py-3 font-heading text-sm font-medium text-ink transition hover:bg-soft"
                    >
                        Batal
                    </button>

                    <x-button type="submit" size="lg" icon="save">
                        Simpan Perubahan
                    </x-button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.public>
