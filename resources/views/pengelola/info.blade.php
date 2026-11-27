{{--
    Informasi panduan pengelola (Figma: MOCKUP > Halaman Informasi, node 30:1019).
    Data dari Pengelola\InfoController: $destinasi, $contohPembaruan (boleh null), $fotoContoh (boleh null)
    Isi panduan sengaja ditulis di view (statis). Nama destinasi otomatis mengikuti akun yang login.
--}}
@php
    $link = fn (string $route, $param = null) => Route::has($route) ? route($route, $param) : '#';
    $nama = $destinasi?->nama ?? 'destinasi Anda';
    $maksFotoMb = 2; // samakan dengan aturan validasi foto pada formulir penanganan

    $inisialDestinasi = collect(explode(' ', (string) $destinasi?->nama))->filter()->take(2)
        ->map(fn ($kata) => mb_strtoupper(mb_substr($kata, 0, 1)))->implode('') ?: 'SW';

    $tahapan = [
        ['judul' => 'Buka Daftar Laporan', 'ikon' => 'fact_check', 'label' => 'Ruang Lingkup Terisolasi',
         'isi' => "Akses portal Anda dibatasi hanya untuk laporan di {$nama}. Anda tidak memiliki akses ke laporan destinasi lain.",
         'garis' => 'bg-primary', 'nomor' => 'bg-soft-strong text-primary', 'warna' => 'text-primary'],
        ['judul' => 'Buka Detail Laporan', 'ikon' => 'visibility', 'label' => 'Stempel Waktu Tercatat',
         'isi' => 'Status laporan berubah otomatis menjadi <b class="font-semibold text-ink">"Dilihat"</b> tepat saat halaman rincian laporan pertama kali dibuka oleh akun Anda.',
         'garis' => 'bg-secondary-ink', 'nomor' => 'bg-peach text-[#2a1700]', 'warna' => 'text-secondary-ink'],
        ['judul' => 'Aksi Lapangan Nyata', 'ikon' => 'handyman', 'label' => 'Inspeksi Fisik & Material',
         'isi' => "Koordinasikan tim teknis sarana {$nama} ke titik kerusakan yang dilaporkan (mis. pagar pembatas, anak tangga, atau jembatan kayu).",
         'garis' => 'bg-primary-soft', 'nomor' => 'bg-soft-strong text-primary', 'warna' => 'text-primary'],
        ['judul' => 'Formulir Tindak Lanjut', 'ikon' => 'pending_actions', 'label' => 'Proses Pekerjaan Berjalan',
         'isi' => 'Wajib melampirkan foto proses perbaikan dan deskripsi penanganan. Pengiriman sukses mengubah status otomatis menjadi <b class="font-semibold text-ink">"Ditindaklanjuti"</b>.',
         'garis' => 'bg-secondary', 'nomor' => 'bg-peach text-[#2a1700]', 'warna' => 'text-secondary-ink'],
        ['judul' => 'Formulir Penyelesaian', 'ikon' => 'task_alt', 'label' => 'Klarifikasi Akhir Pulih',
         'isi' => 'Kirim bukti foto kondisi tuntas, kokoh, dan bersih bersama ringkasan penuntasan sarana. Status resmi berganti menjadi <b class="font-semibold text-ink">"Selesai"</b>.',
         'garis' => 'bg-primary', 'nomor' => 'bg-primary text-white', 'warna' => 'text-primary'],
        ['judul' => 'Transparansi Publik', 'ikon' => 'public', 'label' => 'Audit Komunitas Real-time',
         'isi' => 'Wisatawan memantau riwayat penanganan langsung di website secara transparan, tanpa perlu notifikasi email manual.',
         'garis' => 'bg-[#005e54]', 'nomor' => 'bg-[#62fae3] text-[#00201c]', 'warna' => 'text-[#005e54]'],
    ];

    $otomatisasi = [
        ['status' => 'dilihat',
         'pemicu' => 'Pengelola membuka detail laporan untuk pertama kali. Sistem mencatat <b class="font-semibold text-ink">stempel waktu audit</b> awal secara transparan.',
         'akibat' => "Wisatawan mengetahui bahwa pengelola {$nama} telah membaca laporan mereka."],
        ['status' => 'ditindaklanjuti',
         'pemicu' => 'Pengelola berhasil mengirim <b class="font-semibold text-ink">Formulir Tindak Lanjut</b> disertai foto bukti proses lapangan dan catatan teknis.',
         'akibat' => 'Status berubah kuning ambar. Foto progres pekerjaan tampil di riwayat laporan, membuktikan perbaikan sedang dikerjakan.'],
        ['status' => 'selesai',
         'pemicu' => 'Pengelola berhasil mengirim <b class="font-semibold text-ink">Formulir Penyelesaian</b> disertai foto hasil akhir sarana fasilitas yang telah pulih.',
         'akibat' => 'Status berubah hijau tuntas. Riwayat pembaruan menampilkan foto kondisi sebelum dan sesudah perbaikan secara terbuka.'],
    ];

    $ketentuan = [
        ['ikon' => 'add_a_photo', 'judul' => 'Deskripsi & Foto Bukti Wajib',
         'isi' => "Sistem menolak formulir yang kosong. Foto wajib berformat JPG/PNG dengan ukuran maksimal {$maksFotoMb} MB."],
        ['ikon' => 'filter_center_focus', 'judul' => 'Foto Sesuai Tahapan Penanganan',
         'isi' => 'Foto perbaikan bertahap untuk formulir tindak lanjut (mis. tukang mengelas pagar); foto hasil bersih & kokoh untuk formulir penyelesaian.'],
        ['ikon' => 'feed', 'judul' => 'Bahasa Faktual & Bertanggung Jawab',
         'isi' => 'Tuliskan estimasi durasi pekerjaan, material yang diganti, serta penanggung jawab teknis tanpa menggunakan singkatan yang rancu.'],
    ];

    $hakAkses = [
        ['boleh' => true,  'isi' => 'Mengedit dan menghapus pembaruan penanganan yang Anda buat sendiri bila terjadi koreksi catatan.'],
        ['boleh' => false, 'isi' => 'Mengubah atau menghapus laporan asli milik wisatawan demi menjaga integritas data.'],
        ['boleh' => false, 'isi' => 'Mengubah pembaruan penanganan milik akun lain.'],
        ['boleh' => false, 'isi' => "Mengakses atau membalas laporan destinasi wisata di luar {$nama}."],
    ];

    $faq = [
        ['tanya' => 'Apa yang terjadi jika saya menghapus formulir penyelesaian?',
         'jawab' => 'Status laporan dihitung ulang dari riwayat yang masih ada. Bila masih ada tindak lanjut, status kembali menjadi "Ditindaklanjuti"; bila tidak ada, status kembali menjadi "Dilihat". Komentar wisatawan pada pembaruan yang dihapus ikut terhapus.'],
        ['tanya' => 'Bisakah saya mengirim formulir tindak lanjut lebih dari satu kali?',
         'jawab' => 'Bisa. Satu laporan boleh memiliki banyak tindak lanjut, misalnya untuk setiap tahap pekerjaan. Setiap kiriman tetap wajib disertai deskripsi dan foto bukti, dan semuanya tampil berurutan di riwayat laporan.'],
        ['tanya' => 'Siapa yang dapat dihubungi jika mengalami kendala akun atau lupa kata sandi?',
         'jawab' => 'Akun pengelola dibuat oleh Administrator Pusat SwaraWisata, jadi hubungi administrator untuk kendala akun atau lupa kata sandi. Bila masih bisa masuk, kata sandi dapat diganti sendiri melalui halaman Profil.'],
    ];

    $kelasKartu = 'rounded-2xl bg-white p-6 shadow-[0_1px_2px_rgba(0,0,0,0.05)] sm:p-10';
    $kelasJudulBagian = 'font-heading text-2xl leading-8 font-bold tracking-[-0.6px] text-ink';
@endphp

<x-layouts.pengelola title="Informasi Panduan">
    <div class="flex flex-col gap-10 py-5">
        {{-- ============ HERO ============ --}}
        <section class="relative overflow-hidden rounded-2xl bg-[linear-gradient(162deg,#e7eeff_0%,#f0f3ff_50%,#dee8ff_100%)] p-6 shadow-[0_1px_2px_rgba(0,0,0,0.05)] sm:p-10">
            <div aria-hidden="true" class="absolute -top-24 -right-20 size-80 rounded-full bg-primary/10 blur-[64px]"></div>
            <div aria-hidden="true" class="absolute right-32 -bottom-20 size-64 rounded-full bg-secondary/15 blur-[40px]"></div>

            <div class="relative flex max-w-3xl flex-col gap-2">
                <h1 class="max-w-[480px] font-heading text-4xl leading-[1.1] font-extrabold tracking-[-1.2px] text-ink sm:text-5xl">Panduan Menangani Laporan Kerusakan</h1>
                <p class="max-w-[500px] text-[17px] leading-7 text-muted">
                    Pahami tata kelola, alur otomatisasi status, dan ketentuan formulir penanganan laporan wisatawan di {{ $destinasi ? $nama : 'destinasi Anda' }}.
                </p>
            </div>
        </section>

        {{-- ============ SIKLUS 6 TAHAP ============ --}}
        <section class="flex flex-col gap-4">
            <h2 class="{{ $kelasJudulBagian }}">Siklus 6 Tahap Penanganan Kerusakan Fasilitas</h2>

            <ol class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                @foreach ($tahapan as $tahap)
                    <li class="relative flex flex-col justify-between gap-5 overflow-hidden rounded-xl bg-white p-6 shadow-[0_1px_2px_rgba(0,0,0,0.05)]">
                        <span aria-hidden="true" class="absolute inset-y-0 left-0 w-1.5 {{ $tahap['garis'] }}"></span>

                        <div class="flex flex-col gap-1">
                            <span class="flex size-10 items-center justify-center rounded-full font-heading text-base leading-6 font-bold {{ $tahap['nomor'] }}">
                                {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                            </span>
                            <h3 class="pt-3 font-heading text-base leading-6 font-semibold text-ink">{{ $tahap['judul'] }}</h3>
                            <p class="text-[13px] leading-[21px] text-muted">{!! $tahap['isi'] !!}</p>
                        </div>

                        <p class="flex items-center gap-1.5 font-heading text-xs leading-4 font-semibold {{ $tahap['warna'] }}">
                            <x-icon :name="$tahap['ikon']" size="16" /> {{ $tahap['label'] }}
                        </p>
                    </li>
                @endforeach
            </ol>
        </section>

        {{-- ============ TABEL OTOMATISASI STATUS ============ --}}
        <section class="flex flex-col gap-6 {{ $kelasKartu }}">
            <h2 class="{{ $kelasJudulBagian }}">Tabel Otomatisasi Status &amp; Pemicu Sistem</h2>

            <div class="overflow-x-auto">
                <table class="w-full min-w-[640px] text-left text-[13px] leading-[18px]">
                    <thead class="bg-soft font-heading text-xs leading-4 font-bold text-ink">
                        <tr>
                            <th scope="col" class="w-40 rounded-l-lg px-4 py-3.5">Status Laporan</th>
                            <th scope="col" class="px-4 py-3.5">Pemicu Aksi Sistem</th>
                            <th scope="col" class="rounded-r-lg px-4 py-3.5">Konsekuensi di Portal Wisatawan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-soft-strong">
                        @foreach ($otomatisasi as $baris)
                            <tr class="align-top">
                                <td class="px-4 py-4"><x-status-badge :status="$baris['status']" size="sm" /></td>
                                <td class="px-4 py-4 text-muted">{!! $baris['pemicu'] !!}</td>
                                <td class="px-4 py-4 text-ink">{{ $baris['akibat'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        {{-- ============ KETENTUAN FORMULIR + MATRIKS HAK AKSES ============ --}}
        <section class="grid items-start gap-6 lg:grid-cols-2">
            <div class="flex flex-col gap-4 {{ $kelasKartu }}">
                <h3 class="font-heading text-xl leading-7 font-semibold text-ink">Ketentuan Pengisian Formulir Penanganan</h3>

                <ul class="flex flex-col gap-4">
                    @foreach ($ketentuan as $item)
                        <li class="flex gap-2 rounded-xl bg-soft p-2">
                            <x-icon :name="$item['ikon']" size="22" class="text-primary" />
                            <div class="flex flex-col gap-0.5">
                                <p class="font-heading text-sm leading-5 font-semibold text-ink">{{ $item['judul'] }}</p>
                                <p class="text-[13px] leading-[18px] text-muted">{{ $item['isi'] }}</p>
                            </div>
                        </li>
                    @endforeach
                </ul>

                <div class="mt-4 flex flex-wrap items-center justify-between gap-2 rounded-xl bg-soft-strong p-2 pt-3">
                    <p class="text-[13px] leading-[18px] text-muted">Validasi Format Bukti:</p>
                    <span class="rounded-md bg-white px-2.5 py-1 font-heading text-[13px] leading-[18px] font-semibold text-primary">JPG, JPEG, PNG</span>
                </div>
            </div>

            <div class="flex flex-col gap-4 {{ $kelasKartu }}">
                <h3 class="font-heading text-xl leading-7 font-semibold text-ink">Matriks Hak Akses &amp; Batasan Pengelola</h3>

                <ul class="flex flex-col gap-2">
                    @foreach ($hakAkses as $item)
                        <li @class(['flex gap-2 rounded-xl p-2 text-[13px] leading-[18px] text-ink', 'bg-soft' => $item['boleh'], 'bg-soft-blue/60' => ! $item['boleh']])>
                            <x-icon :name="$item['boleh'] ? 'check_circle' : 'cancel'" size="20" @class(['text-primary' => $item['boleh'], 'text-danger' => ! $item['boleh']]) />
                            <p>
                                <span @class(['font-semibold', 'text-primary' => $item['boleh'], 'text-danger' => ! $item['boleh']])>[{{ $item['boleh'] ? 'Boleh' : 'Dilarang' }}]</span>
                                {{ $item['isi'] }}
                            </p>
                        </li>
                    @endforeach
                </ul>
            </div>
        </section>

        {{-- ============ SIMULASI TAMPILAN PUBLIK ============ --}}
        <section class="grid items-center gap-6 lg:grid-cols-2 {{ $kelasKartu }}">
            <div class="flex flex-col gap-2">
                <span class="inline-flex w-fit items-center gap-1.5 rounded-full bg-soft-strong px-3 py-1 font-heading text-[11px] leading-[14px] font-bold text-primary uppercase">
                    <x-icon name="visibility" size="16" /> Simulasi Tampilan Publik
                </span>
                <h3 class="pt-0.5 {{ $kelasJudulBagian }}">Bagaimana Wisatawan Melihat Tindak Lanjut Anda?</h3>
                <p class="text-[15px] leading-6 text-muted">
                    Setiap kali formulir Anda terkirim, riwayat laporan di website langsung diperbarui. Ini menciptakan rasa tenang bagi publik dan menghindarkan destinasi dari isu viral negatif.
                </p>
                <ul class="flex flex-col gap-1 pt-2 text-[13px] leading-[18px] text-ink">
                    <li class="flex items-center gap-2"><x-icon name="done_all" size="18" class="text-primary" /> Foto bukti tampil bersama tanggal dan jam pembaruan</li>
                    <li class="flex items-center gap-2"><x-icon name="done_all" size="18" class="text-primary" /> Identitas pengelola resmi {{ $nama }} tercantum terverifikasi</li>
                </ul>
            </div>

            {{-- Kartu contoh: memakai pembaruan penyelesaian terbaru di destinasi ini bila sudah ada --}}
            <div class="flex flex-col gap-2 rounded-xl bg-soft p-4 drop-shadow-[0_1px_1px_rgba(0,0,0,0.05)] sm:p-6">
                <div class="flex items-center justify-between gap-2">
                    <div class="flex min-w-0 items-center gap-2">
                        <span class="flex size-8 shrink-0 items-center justify-center rounded-full bg-primary text-xs leading-4 font-semibold text-white">{{ $inisialDestinasi }}</span>
                        <div class="min-w-0">
                            <p class="truncate font-heading text-xs leading-4 font-semibold text-ink">{{ $destinasi ? 'Kawasan '.$destinasi->nama : 'Pengelola Destinasi' }}</p>
                            <p class="font-heading text-[11px] leading-[14px] font-bold text-muted">
                                Update resmi • {{ $contohPembaruan?->created_at?->locale('id')->diffForHumans() ?? 'Contoh tampilan' }}
                            </p>
                        </div>
                    </div>
                    <span class="shrink-0 rounded-full bg-mint px-2.5 py-0.5 font-heading text-[11px] leading-[14px] font-bold text-[#002117]">Selesai</span>
                </div>

                <p class="text-[13px] leading-[18px] text-muted italic">
                    "{{ $contohPembaruan ? \Illuminate\Support\Str::limit($contohPembaruan->deskripsi, 140) : 'Pagar pengaman kayu telah diganti dengan balok ulin baru dan dipasang pengikat kawat baja tahan karat.' }}"
                </p>

                <div class="relative h-44 overflow-hidden rounded-lg bg-soft-blue shadow-[0_1px_2px_rgba(0,0,0,0.05)]">
                    <x-icon name="landscape" size="40" class="absolute inset-0 m-auto text-line" />
                    @if ($fotoContoh)
                        <img src="{{ $fotoContoh }}" alt="Contoh foto bukti penyelesaian" class="absolute inset-0 size-full object-cover" onerror="this.remove()">
                    @endif
                    <span class="absolute bottom-2 left-2 flex items-center gap-1 rounded bg-admin/85 px-2 py-1 font-heading text-[11px] leading-[14px] font-semibold text-white backdrop-blur-md">
                        <x-icon name="photo_camera" size="14" /> Foto Bukti Penyelesaian Tuntas
                    </span>
                </div>

                <div class="flex items-center justify-between gap-2 pt-2 font-heading text-[11px] leading-[14px] font-bold text-primary">
                    <span class="flex items-center gap-1"><x-icon name="check_circle" size="14" /> Dilihat</span>
                    <span aria-hidden="true" class="h-0.5 max-w-10 flex-1 bg-primary"></span>
                    <span class="flex items-center gap-1"><x-icon name="check_circle" size="14" /> Ditindaklanjuti</span>
                    <span aria-hidden="true" class="h-0.5 max-w-10 flex-1 bg-primary"></span>
                    <span class="flex items-center gap-1"><x-icon name="check_circle" size="14" /> Selesai</span>
                </div>
            </div>
        </section>

        {{-- ============ FAQ ============ --}}
        <section class="flex flex-col gap-6">
            <div class="flex flex-wrap items-end justify-between gap-2">
                <div>
                    <p class="font-heading text-[11px] leading-[14px] font-bold tracking-[1.1px] text-primary uppercase">Tanya Jawab Operasional</p>
                    <h2 class="{{ $kelasJudulBagian }}">Pertanyaan Umum Pengelola</h2>
                </div>
                <p class="text-[13px] leading-[18px] text-muted">Kendala umum seputar alur dan otoritas penanganan</p>
            </div>

            <div class="flex flex-col gap-2">
                @foreach ($faq as $item)
                    <details class="group rounded-xl bg-white shadow-[0_1px_2px_rgba(0,0,0,0.05)]">
                        <summary class="flex cursor-pointer list-none items-center justify-between gap-4 p-6 font-heading text-base leading-6 font-semibold text-ink [&::-webkit-details-marker]:hidden">
                            {{ $item['tanya'] }}
                            <x-icon name="expand_more" size="22" class="text-primary transition group-open:rotate-180" />
                        </summary>
                        <p class="-mt-2 px-6 pb-6 text-sm leading-6 text-muted">{{ $item['jawab'] }}</p>
                    </details>
                @endforeach
            </div>
        </section>

        {{-- ============ AJAKAN BERTINDAK ============ --}}
        <section class="flex flex-col justify-between gap-6 rounded-2xl bg-primary p-6 shadow-[0_4px_6px_-1px_rgba(0,0,0,0.1),0_2px_4px_-2px_rgba(0,0,0,0.1)] sm:p-10 lg:flex-row lg:items-center">
            <div class="flex flex-col gap-1">
                <span class="inline-flex w-fit items-center gap-1.5 rounded-full bg-primary-soft px-3 py-1 font-heading text-[11px] leading-[14px] font-bold text-[#a8ffdf] uppercase">
                    <x-icon name="flag" size="14" /> Siap Melayani Wisatawan
                </span>
                <h3 class="pt-0.5 font-heading text-2xl leading-8 font-bold tracking-[-0.6px] text-white">Terapkan Prosedur Ini pada Laporan Aktif</h3>
                <p class="max-w-xl text-[15px] leading-[22px] text-[#a8ffdf]">
                    Periksa laporan yang masuk dari wisatawan di {{ $nama }} secara berkala dan pastikan standar keselamatan pengunjung terpenuhi.
                </p>
            </div>

            <div class="flex shrink-0 flex-wrap gap-3">
                <a href="{{ $link('pengelola.laporan.index') }}"
                   class="flex items-center gap-2 rounded-lg bg-white px-6 py-3 font-heading text-sm leading-5 font-semibold text-primary shadow-[0_4px_6px_-1px_rgba(0,0,0,0.1),0_2px_4px_-2px_rgba(0,0,0,0.1)] transition hover:bg-soft">
                    <x-icon name="assignment" size="18" /> Buka Daftar Laporan
                </a>
                <a href="{{ $link('pengelola.dashboard') }}"
                   class="flex items-center gap-2 rounded-lg bg-primary-soft px-4 py-3 font-heading text-sm leading-5 font-semibold text-white transition hover:brightness-110">
                    <x-icon name="grid_view" size="18" /> Kembali ke Dashboard
                </a>
            </div>
        </section>
    </div>
</x-layouts.pengelola>