{{-- Footer halaman publik (Figma: Landing Page Publik > Footer). --}}
@php
    $beranda = route('home');
    $navigasi = [
        'Tentang'     => $beranda.'#tentang',
        'Cara Lapor'  => $beranda.'#cara-lapor',
        'Destinasi'   => $beranda.'#destinasi',
        'Laporan'     => $beranda.'#laporan',
        'Kontak Kami' => $beranda.'#kontak',
    ];
@endphp

<footer {{ $attributes->class('bg-soft pt-10 pb-6') }}>
    <div class="mx-auto flex w-full max-w-[1280px] flex-col gap-10 px-4 sm:px-8">
        <div class="grid gap-10 md:grid-cols-3">
            {{-- Kolom 1: identitas --}}
            <div class="flex flex-col gap-[15.2px]">
                <x-logo />
                <p class="max-w-xs text-[13px] leading-[21.13px] text-muted">
                    Jembatan aspirasi wisatawan dan pengelola untuk mewujudkan infrastruktur wisata Bali
                    yang aman, nyaman, dan berdaya saing.
                </p>
                <div class="flex items-center gap-2">
                    @foreach (['language' => 'Portal Resmi', 'share' => 'Kanal Komunitas', 'description' => 'Layanan Informasi'] as $ikon => $label)
                        <a href="#" aria-label="{{ $label }}" title="{{ $label }}"
                           class="flex size-8 items-center justify-center rounded-full bg-soft-strong text-primary transition hover:bg-soft-blue">
                            <x-icon :name="$ikon" size="16" />
                        </a>
                    @endforeach
                </div>
            </div>

            {{-- Kolom 2: navigasi cepat --}}
            <div class="flex flex-col gap-4">
                <h4 class="font-heading text-base leading-6 font-semibold text-ink">Navigasi Cepat</h4>
                <ul class="flex flex-col gap-2 text-[13px] leading-[18px] text-muted">
                    @foreach ($navigasi as $label => $url)
                        <li><a href="{{ $url }}" class="hover:text-primary">{{ $label }}</a></li>
                    @endforeach
                </ul>
            </div>

            {{-- Kolom 3: kontak --}}
            <div class="flex flex-col gap-4">
                <h4 class="font-heading text-base leading-6 font-semibold text-ink">Kontak Layanan</h4>
                <ul class="flex flex-col gap-2 text-[13px] leading-[18px] text-muted">
                    <li class="flex items-start gap-1">
                        <x-icon name="mail" size="16" class="mt-px text-primary" />
                        <a href="mailto:info@swarawisata.id" class="hover:text-primary">info@swarawisata.id</a>
                    </li>
                    <li class="flex items-start gap-1">
                        <x-icon name="call" size="16" class="mt-px text-primary" />
                        <a href="https://wa.me/6281234567890" target="_blank" rel="noopener" class="hover:text-primary">WhatsApp: +62 812-3456-7890</a>
                    </li>
                    <li class="flex items-start gap-1">
                        <x-icon name="schedule" size="16" class="mt-px text-primary" />
                        <span>Jam Layanan: Senin - Jumat<br>08.00 - 17.00 WITA</span>
                    </li>
                </ul>
            </div>
        </div>

        <div class="flex flex-col gap-3 pt-4 text-[13px] leading-[18px] text-muted sm:flex-row sm:items-center sm:justify-between">
            <p>© {{ date('Y') }} SwaraWisata. Seluruh hak cipta dilindungi.</p>
            <nav class="flex flex-wrap gap-4">
                <a href="#" class="hover:text-primary">Kebijakan Privasi</a>
                <a href="#" class="hover:text-primary">Syarat &amp; Ketentuan</a>
                <a href="#" class="hover:text-primary">Pedoman Komunitas</a>
            </nav>
        </div>
    </div>
</footer>