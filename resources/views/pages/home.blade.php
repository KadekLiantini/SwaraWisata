{{--
    Landing page publik (Figma: Landing Page Publik). Hanya untuk pengunjung yang belum login.
    Data dari HomeController: $statistik, $top3, $maksLaporan, $laporanTerbaru, $pembaruanTerakhir
--}}
@php
    $format = fn ($angka) => number_format($angka, 0, ',', '.');
    $urlLapor = route('login');
    $urlDetail = fn ($laporan) => Route::has('laporan.show') ? route('laporan.show', $laporan) : route('login');
    $urlSemua = Route::has('laporan.index') ? route('laporan.index') : route('login');
    $whatsapp = 'https://wa.me/6281234567890';
    $progresStatus = ['belum_dilihat' => 25, 'dilihat' => 50, 'ditindaklanjuti' => 80, 'selesai' => 100];
@endphp

<x-layouts.public cta="landing" footer="landing">
    @push('head')
        <style>html { scroll-behavior: smooth; }</style>
    @endpush

    <x-slot:nav>
        <a href="#tentang">Tentang</a>
        <a href="#cara-lapor">Cara Lapor</a>
        <a href="#destinasi">Destinasi</a>
        <a href="#laporan">Laporan</a>
        <a href="#kontak">Kontak Kami</a>
    </x-slot:nav>

    {{-- ============ HERO ============ --}}
    <section class="relative overflow-hidden bg-page py-16 lg:py-24">
        <div aria-hidden="true" class="pointer-events-none absolute -top-40 -right-40 size-96 rounded-full bg-mint/25 blur-[64px]"></div>
        <div aria-hidden="true" class="pointer-events-none absolute top-[calc(50%+160px)] -left-32 size-80 -translate-y-1/2 rounded-full bg-peach/30 blur-[64px]"></div>

        <div class="relative mx-auto flex w-full max-w-[1280px] flex-col gap-10 px-4 sm:px-8">
            <div class="grid items-center gap-10 lg:grid-cols-12">
                {{-- Teks --}}
                <div class="flex flex-col gap-6 lg:col-span-7">
                    <h1 class="font-heading text-[36px] leading-[44px] font-extrabold tracking-[-1.2px] text-ink sm:text-[48px] sm:leading-[56px]">
                        Suarakan Kerusakan,<br>Wujudkan Wisata<br>
                        yang <span class="text-primary underline decoration-secondary decoration-4 underline-offset-[6px]">Aman &amp; Nyaman</span>
                    </h1>
                    <p class="max-w-[672px] text-lg leading-7 text-muted">
                        Wisatawan dapat melaporkan kerusakan infrastruktur wisata secara langsung dan memantau proses
                        penanganannya oleh pengelola destinasi secara terbuka, transparan, dan akuntabel.
                    </p>
                    <div class="flex flex-wrap items-center gap-4 pt-1">
                        <a href="{{ $urlLapor }}" class="inline-flex items-center gap-1 rounded-lg bg-primary px-6 py-3 font-heading text-[15px] leading-[22px] font-semibold text-white shadow-md transition hover:bg-primary-hover">
                            Laporkan Kerusakan <x-icon name="arrow_forward" size="16" />
                        </a>
                        <a href="#tentang" class="inline-flex items-center gap-1 rounded-lg bg-soft-strong px-6 py-3 font-heading text-[15px] leading-[22px] font-semibold text-ink transition hover:bg-soft-blue">
                            <x-icon name="explore" size="18" /> Pelajari Lebih Lanjut
                        </a>
                    </div>
                </div>

                {{-- Kartu visual --}}
                <div class="relative h-[460px] overflow-hidden rounded-2xl bg-soft-strong shadow-xl lg:col-span-5">
                    @if (file_exists(public_path('images/hero.jpg')))
                        <img src="{{ asset('images/hero.jpg') }}" alt="Jalur kayu di kawasan wisata alam Bali" class="absolute inset-0 size-full object-cover">
                    @endif
                    <div class="absolute inset-0 bg-gradient-to-t from-[rgba(38,49,67,0.9)] via-[rgba(38,49,67,0.3)] to-[rgba(38,49,67,0)]"></div>

                    <span class="absolute top-4 left-4 inline-flex items-center gap-2 rounded-full bg-page/95 px-4 py-1.5 font-heading text-[15px] leading-[22px] font-semibold text-ink shadow-md backdrop-blur-md">
                        <x-icon name="location_on" size="16" class="text-secondary-ink" />
                        {{ $format($statistik['destinasi']) }} Destinasi Wisata Terpantau Aktif
                    </span>

                    @if ($pembaruanTerakhir)
                        <div class="absolute inset-x-4 bottom-4 flex flex-col gap-2 rounded-xl bg-page/95 p-4 shadow-lg backdrop-blur-md">
                            <div class="flex items-center justify-between gap-2">
                                <span class="flex items-center gap-1 font-heading text-[15px] leading-[22px] font-semibold text-muted">
                                    <span class="size-2 rounded-full bg-primary-soft"></span>
                                    Pembaruan Penanganan Terakhir
                                </span>
                                <span class="font-heading text-[15px] leading-[22px] font-bold text-primary">
                                    {{ $pembaruanTerakhir->created_at->locale('id')->diffForHumans() }}
                                </span>
                            </div>
                            <p class="truncate font-heading text-[15px] leading-[22px] font-semibold text-ink">{{ $pembaruanTerakhir->laporan->judul }}</p>
                            <div class="h-1.5 overflow-hidden rounded-full bg-soft-blue">
                                <div class="h-full rounded-full bg-primary" style="width: {{ $progresStatus[$pembaruanTerakhir->laporan->status] ?? 25 }}%"></div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Statistik --}}
            <div class="grid gap-6 md:grid-cols-3">
                @foreach ([
                    ['ikon' => 'assignment',   'kotak' => 'bg-mint text-primary',          'angka' => $statistik['total'],     'label' => 'Total Laporan Masuk',      'warna' => 'text-primary',       'teks' => 'Aspirasi wisatawan domestik dan mancanegara di destinasi wisata Bali.'],
                    ['ikon' => 'check_circle', 'kotak' => 'bg-primary/10 text-primary',    'angka' => $statistik['selesai'],   'label' => 'Laporan Selesai Ditangani', 'warna' => 'text-primary',       'teks' => 'Fasilitas publik kembali berfungsi aman dengan foto bukti perbaikan.'],
                    ['ikon' => 'forest',       'kotak' => 'bg-peach text-secondary-ink',   'angka' => $statistik['destinasi'], 'label' => 'Destinasi Terdaftar',      'warna' => 'text-secondary-ink', 'teks' => 'Mencakup pantai, pura, air terjun, hingga desa wisata di Bali.'],
                ] as $stat)
                    <div class="flex items-start gap-4 rounded-xl bg-soft p-6 drop-shadow-[0_1px_1px_rgba(0,0,0,0.05)]">
                        <span class="flex size-12 shrink-0 items-center justify-center rounded-lg {{ $stat['kotak'] }}">
                            <x-icon :name="$stat['ikon']" size="22" />
                        </span>
                        <div>
                            <p class="font-heading text-[32px] leading-10 font-bold text-ink">{{ $format($stat['angka']) }}</p>
                            <p class="font-heading text-[15px] leading-[22px] font-bold {{ $stat['warna'] }}">{{ $stat['label'] }}</p>
                            <p class="pt-1 text-[15px] leading-[22px] text-muted">{{ $stat['teks'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============ TENTANG ============ --}}
    <section id="tentang" class="scroll-mt-20 bg-white py-16 lg:py-24">
        <div class="mx-auto flex w-full max-w-[1280px] flex-col gap-10 px-4 sm:px-8">
            <x-section-header
                eyebrow="Tentang Platform"
                title="Menjembatani Wisatawan & Pengelola Destinasi"
                description="SwaraWisata hadir sebagai wadah komunikasi dua arah: wisatawan melaporkan fasilitas atau infrastruktur yang rusak, pengelola menerima dan menindaklanjuti secara langsung di lapangan, sementara pelapor dapat mengawal perkembangan perbaikan secara transparan tanpa birokrasi berbelit."
            />

            <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-4">
                @foreach ([
                    ['ikon' => 'add_a_photo',      'kotak' => 'bg-mint text-primary',        'judul' => 'Pelaporan Mudah dengan Bukti Foto',  'teks' => 'Cukup unggah foto temuan kerusakan, tentukan lokasi fasilitas di area wisata, dan kirimkan laporan dalam hitungan menit.', 'catatan' => 'Lokasi spesifik di destinasi', 'warna' => 'text-primary'],
                    ['ikon' => 'visibility',       'kotak' => 'bg-peach text-secondary-ink', 'judul' => 'Transparansi Penanganan Nyata',      'teks' => 'Lihat riwayat tindak lanjut, deskripsi penanganan, hingga dokumentasi foto hasil perbaikan resmi dari tim pengelola.',   'catatan' => 'Audit publik terbuka',          'warna' => 'text-secondary-ink'],
                    ['ikon' => 'trending_up',      'kotak' => 'bg-mint text-primary',        'judul' => 'Status Laporan Otomatis & Terpantau', 'teks' => 'Pantau tahapan status: Dilihat, Ditindaklanjuti, hingga Selesai, langsung dari akun Anda.',                                'catatan' => '3 tahap status otomatis',       'warna' => 'text-primary'],
                    ['ikon' => 'add_location_alt', 'kotak' => 'bg-soft-blue text-primary',   'judul' => 'Inklusif: Ajukan Destinasi Baru',     'teks' => 'Jika destinasi favoritmu belum terdaftar di sistem, ajukan lokasi baru agar fasilitasnya dapat segera dipantau bersama publik.', 'catatan' => 'Seluruh wilayah Bali', 'warna' => 'text-primary'],
                ] as $manfaat)
                    <div class="flex flex-col justify-between gap-6 rounded-xl bg-soft p-6 drop-shadow-[0_1px_1px_rgba(0,0,0,0.05)]">
                        <div class="flex flex-col gap-[3.2px]">
                            <span class="flex size-12 items-center justify-center rounded-lg {{ $manfaat['kotak'] }}">
                                <x-icon :name="$manfaat['ikon']" size="22" />
                            </span>
                            <h3 class="pt-[12.8px] font-heading text-base leading-6 font-semibold text-ink">{{ $manfaat['judul'] }}</h3>
                            <p class="text-[13px] leading-[21.13px] text-muted">{{ $manfaat['teks'] }}</p>
                        </div>
                        <p class="flex items-center gap-1 pt-2 font-heading text-[15px] leading-[22px] font-semibold {{ $manfaat['warna'] }}">
                            {{ $manfaat['catatan'] }} <x-icon name="check" size="16" />
                        </p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- ============ CARA LAPOR ============ --}}
    <section id="cara-lapor" class="scroll-mt-20 bg-page py-16 lg:py-24">
        <div class="mx-auto flex w-full max-w-[1280px] flex-col gap-10 px-4 sm:px-8">
            <x-section-header
                eyebrow="Panduan & Prosedur"
                tone="plain-gold"
                title="5 Langkah Mudah Menyampaikan Aspirasi"
                description="Prosedur pelaporan terstandarisasi untuk memastikan setiap masalah cepat direspons oleh pihak pengelola."
            />

            <ol class="grid gap-4 sm:grid-cols-2 lg:grid-cols-5">
                @foreach ([
                    ['Daftar / Masuk Akun',          'Akses sistem dengan akun terverifikasi untuk memastikan integritas dan akuntabilitas data laporan.'],
                    ['Pilih Destinasi Wisata',       'Cari tempat wisata yang bermasalah atau ajukan penambahan destinasi baru jika lokasi belum terdaftar.'],
                    ['Isi Formulir Laporan',         'Lengkapi judul, kategori (toilet, jalan, rambu), deskripsi, lokasi spesifik, serta unggah bukti foto lapangan.'],
                    ['Pengelola Menindaklanjuti',    'Pengelola destinasi menerima laporan di dasbor dan melakukan aksi perbaikan fisik di lokasi.'],
                    ['Pantau Status Hingga Tuntas',  'Pantau pembaruan status pengerjaan hingga laporan ditandai resmi \'Selesai\' dengan dokumentasi.'],
                ] as [$judul, $teks])
                    <li class="flex min-h-[238px] flex-col gap-1 rounded-xl bg-soft p-6 drop-shadow-[0_1px_1px_rgba(0,0,0,0.05)]">
                        <span class="flex size-8 items-center justify-center rounded-full bg-primary font-heading text-[15px] leading-[22px] font-bold text-white">{{ $loop->iteration }}</span>
                        <h3 class="pt-3 font-heading text-base leading-6 font-semibold text-ink">{{ $judul }}</h3>
                        <p class="text-[13px] leading-[18px] text-muted">{{ $teks }}</p>
                    </li>
                @endforeach
            </ol>
        </div>
    </section>

    {{-- ============ TOP 3 DESTINASI ============ --}}
    <section id="destinasi" class="scroll-mt-20 bg-white py-16 lg:py-24">
        <div class="mx-auto flex w-full max-w-[1280px] flex-col gap-10 px-4 sm:px-8">
            <x-section-header
                eyebrow="Data Infrastruktur"
                tone="mint"
                align="left"
                title="Top 3 Destinasi dengan Laporan Terbanyak"
                description="Berdasarkan seluruh laporan akumulatif"
            />

            @if ($top3->isEmpty())
                <x-alert variant="info">Belum ada laporan yang masuk. Jadilah yang pertama melaporkan kerusakan fasilitas wisata.</x-alert>
            @else
                <div class="grid gap-6 md:grid-cols-3">
                    @foreach ($top3 as $destinasi)
                        @php
                            $kecamatan = $destinasi->kecamatan;
                            $kabupaten = $kecamatan?->kabupaten?->nama;
                            $peringkat = [
                                1 => 'bg-secondary text-[#684000]',
                                2 => 'bg-soft-blue text-primary',
                                3 => 'bg-soft-blue text-muted',
                            ][$loop->iteration];
                        @endphp
                        <article class="flex flex-col justify-between rounded-xl bg-soft p-6 drop-shadow-[0_1px_1px_rgba(0,0,0,0.05)]">
                            <div class="flex flex-col gap-1">
                                <span class="flex size-9 items-center justify-center rounded-full font-heading text-[15px] leading-[22px] font-bold {{ $peringkat }}">#{{ $loop->iteration }}</span>
                                <h3 class="pt-3 font-heading text-xl leading-7 font-semibold text-ink">{{ $destinasi->nama }}</h3>
                                <p class="flex items-center gap-1 text-[13px] leading-[18px] text-muted">
                                    <x-icon name="location_on" size="14" />
                                    {{ $kecamatan?->nama }}{{ $kabupaten ? ', '.$kabupaten : '' }}
                                </p>

                                <p class="flex items-baseline justify-between pt-3 pb-1 font-heading font-semibold">
                                    <span class="text-[15px] leading-[22px] text-muted">Volume Laporan</span>
                                    <span class="text-base leading-6 text-primary">{{ $format($destinasi->laporan_count) }} Laporan</span>
                                </p>
                                <div class="h-3 overflow-hidden rounded-full bg-soft-blue">
                                    <div class="h-full rounded-full bg-primary" style="width: {{ round($destinasi->laporan_count / $maksLaporan * 100) }}%"></div>
                                </div>

                                <div class="grid grid-cols-2 gap-2 pt-3">
                                    <div class="flex flex-col items-center gap-[1.5px] rounded-lg bg-soft-strong p-2">
                                        <span class="font-heading text-[15px] leading-[22px] font-bold text-primary">{{ $format($destinasi->selesai_count) }}</span>
                                        <span class="font-heading text-[11px] leading-[22px] font-semibold text-muted">Selesai</span>
                                    </div>
                                    <div class="flex flex-col items-center gap-[1.5px] rounded-lg bg-soft-strong p-2">
                                        <span class="font-heading text-[15px] leading-[22px] font-bold text-secondary-ink">{{ $format($destinasi->proses_count) }}</span>
                                        <span class="font-heading text-[11px] leading-[22px] font-semibold text-muted">Sedang Proses</span>
                                    </div>
                                </div>
                            </div>

                            <p class="flex items-center justify-between gap-2 pt-8">
                                <span class="text-[15px] leading-[22px] text-muted">Fasilitas Terbanyak:</span>
                                <span class="text-right font-heading text-[15px] leading-[22px] font-semibold text-ink">{{ $destinasi->fasilitas_terbanyak ?? '-' }}</span>
                            </p>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    {{-- ============ LAPORAN TERBARU ============ --}}
    <section id="laporan" class="scroll-mt-20 bg-page py-16 lg:py-24">
        <div class="mx-auto flex w-full max-w-[1280px] flex-col items-center gap-10 px-4 sm:px-8">
            <x-section-header
                eyebrow="Pratinjau Aspirasi"
                tone="mint"
                title="Laporan Terbaru dari Wisatawan"
                description="Pengunjung umum dapat melihat pratinjau ringkasan laporan terbaru. Untuk melihat deskripsi rinci, titik lokasi detail, foto resolusi penuh, dan riwayat penanganan, silakan masuk ke sistem."
            />

            @if ($laporanTerbaru->isEmpty())
                <x-alert variant="info" class="max-w-xl">Belum ada laporan yang bisa ditampilkan.</x-alert>
            @else
                <div class="grid w-full gap-6 sm:grid-cols-2 lg:grid-cols-4">
                    @foreach ($laporanTerbaru as $laporan)
                        <x-laporan.preview-card :laporan="$laporan" :href="$urlDetail($laporan)" />
                    @endforeach
                </div>
            @endif

            <a href="{{ $urlSemua }}" class="inline-flex items-center gap-1 rounded-lg bg-primary px-10 py-3.5 font-heading text-[15px] leading-[22px] font-semibold text-white shadow-md transition hover:bg-primary-hover">
                <x-icon name="vpn_key" size="18" /> Lihat Semua Laporan di Portal Utama
            </a>
        </div>
    </section>

    {{-- ============ KONTAK ============ --}}
    <section id="kontak" class="scroll-mt-20 bg-white py-16 lg:py-24">
        <div class="mx-auto flex w-full max-w-[1280px] flex-col gap-10 px-4 sm:px-8">
            <x-section-header
                eyebrow="Pusat Bantuan"
                tone="peach"
                title="Hubungi Layanan Pengaduan & Kemitraan"
                description="Butuh bantuan teknis atau ingin mendaftarkan tempat wisata Anda sebagai pengelola resmi SwaraWisata? Hubungi tim kami melalui kanal langsung di bawah ini."
            />

            <div class="grid gap-6 md:grid-cols-2">
                <div class="flex flex-col justify-between gap-4 rounded-xl bg-soft p-6 drop-shadow-[0_1px_1px_rgba(0,0,0,0.05)]">
                    <div class="flex flex-col gap-[3.1px]">
                        <span class="flex size-12 items-center justify-center rounded-lg bg-mint text-primary"><x-icon name="chat" size="22" /></span>
                        <h3 class="pt-[12.9px] font-heading text-base leading-6 font-semibold text-ink">Layanan WhatsApp Resmi</h3>
                        <p class="max-w-sm text-[13px] leading-[21.13px] text-muted">Khusus verifikasi akun pengelola destinasi, penyerahan kredensial manual oleh admin, dan bantuan pelaporan darurat.</p>
                    </div>
                    <div class="flex flex-col gap-1 rounded-lg bg-soft-strong px-4 pt-2 pb-4">
                        <span class="font-heading text-[15px] leading-[22px] font-semibold text-muted">WhatsApp Fast Response:</span>
                        <a href="{{ $whatsapp }}" target="_blank" rel="noopener" class="inline-flex items-center gap-1 font-heading text-base leading-6 font-semibold text-primary hover:underline">
                            +62 812-3456-7890 <x-icon name="open_in_new" size="14" />
                        </a>
                    </div>
                </div>

                <div class="flex flex-col justify-between gap-4 rounded-xl bg-soft p-6 drop-shadow-[0_1px_1px_rgba(0,0,0,0.05)]">
                    <div class="flex flex-col gap-[3.1px]">
                        <span class="flex size-12 items-center justify-center rounded-lg bg-peach text-secondary-ink"><x-icon name="mail" size="22" /></span>
                        <h3 class="pt-[12.9px] font-heading text-base leading-6 font-semibold text-ink">Email Dukungan Teknis</h3>
                        <p class="max-w-sm text-[13px] leading-[21.13px] text-muted">Pertanyaan integrasi data sistem, audiensi Dinas Pariwisata Daerah, pengajuan kerja sama kawasan, dan pers.</p>
                    </div>
                    <div class="flex flex-col gap-1 rounded-lg bg-soft-strong px-4 pt-2 pb-4">
                        <span class="font-heading text-[15px] leading-[22px] font-semibold text-muted">Email Resmi:</span>
                        <a href="mailto:bantuan@swarawisata.id" class="inline-flex items-center gap-1 font-heading text-base leading-6 font-semibold text-secondary-ink hover:underline">
                            bantuan@swarawisata.id <x-icon name="send" size="14" />
                        </a>
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-6 rounded-2xl bg-gradient-to-r from-primary to-primary-soft p-6 shadow-lg sm:p-10 lg:flex-row lg:items-center lg:justify-between">
                <div class="flex items-start gap-4">
                    <span class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-white/10 text-peach backdrop-blur-md">
                        <x-icon name="account_balance" size="25" />
                    </span>
                    <div class="flex flex-col gap-[3.2px]">
                        <h3 class="font-heading text-2xl leading-8 font-bold text-white">Anda Pengelola Destinasi Wisata?</h3>
                        <p class="max-w-[672px] text-[15px] leading-[24.38px] text-[#a8ffdf]">
                            Daftarkan obyek wisata Anda untuk mendapatkan akun resmi, akses dasbor pemantauan fasilitas,
                            dan laporan keluhan langsung dari wisatawan secara gratis.
                        </p>
                    </div>
                </div>
                <a href="{{ $whatsapp }}" target="_blank" rel="noopener" class="inline-flex shrink-0 items-center gap-2 self-start rounded-lg bg-secondary px-6 py-3 font-heading text-[15px] leading-[22px] font-semibold text-ink shadow-sm transition hover:brightness-95 lg:self-auto">
                    <x-icon name="verified" size="18" /> Daftar Pengelola Resmi
                </a>
            </div>
        </div>
    </section>
</x-layouts.public>