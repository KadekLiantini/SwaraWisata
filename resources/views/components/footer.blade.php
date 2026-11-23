{{-- Footer halaman publik (Figma: DESIGN SYSTEM > Footer). --}}
<footer {{ $attributes->class('bg-soft drop-shadow-[0_-1px_3px_rgba(0,0,0,0.02)]') }}>
    <div class="mx-auto flex w-full max-w-[1280px] flex-col gap-10 px-4 py-10 sm:px-8 lg:flex-row lg:items-center lg:justify-between">
        <div class="flex max-w-md flex-col gap-1">
            <x-logo />
            <p class="text-[13px] leading-[18px] text-muted">
                Sistem Aspirasi &amp; Pemantauan Kualitas Destinasi Wisata Terpadu Republik Indonesia.
                Menjaga ruang publik dan kelestarian alam bersama komunitas.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-x-20 gap-y-6">
            <div class="flex w-[226px] flex-col gap-4">
                <h4 class="font-heading text-base leading-6 font-semibold text-ink">Kontak Layanan</h4>
                <ul class="flex flex-col gap-2 text-[13px] leading-[18px] text-muted">
                    <li class="flex items-start gap-1">
                        <x-icon name="mail" size="16" class="mt-px text-primary" />
                        <a href="mailto:info@swarawisata.id" class="hover:text-primary">info@swarawisata.id</a>
                    </li>
                    <li class="flex items-start gap-1">
                        <x-icon name="call" size="16" class="mt-px text-primary" />
                        <span>WhatsApp: +62 812-3456-7890</span>
                    </li>
                    <li class="flex items-start gap-1">
                        <x-icon name="schedule" size="16" class="mt-px text-primary" />
                        <span>Jam Layanan: Senin - Jumat<br>08.00 - 17.00 WITA</span>
                    </li>
                </ul>
            </div>

            <nav class="flex items-center gap-4 font-heading text-xs leading-4 font-semibold text-muted">
                <a href="#" class="hover:text-primary">Panduan</a>
                <a href="#" class="hover:text-primary">Kebijakan Privasi</a>
                <a href="#" class="hover:text-primary">Syarat &amp; Ketentuan</a>
            </nav>
        </div>
    </div>

    <div class="mx-auto w-full max-w-[1280px] px-4 py-4 sm:px-8">
        <p class="text-[13px] leading-[18px] text-muted">© {{ date('Y') }} SwaraWisata. Seluruh Hak Cipta Dilindungi.</p>
    </div>
</footer>
