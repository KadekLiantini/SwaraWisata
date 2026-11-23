<x-layouts.public title="Masuk" cta="register" :footer="false">
    {{-- Bar navigasi kembali --}}
    <div class="bg-soft py-2 drop-shadow-[0_1px_1px_rgba(0,0,0,0.05)]">
        <div class="mx-auto flex w-full max-w-[1280px] flex-wrap items-center justify-between gap-2 px-4 sm:px-8">
            <x-back-link />
            <span class="inline-flex items-center gap-1 rounded-full bg-soft-strong px-2 py-1 font-heading text-[11px] leading-[14px] font-bold text-muted">
                <x-icon name="encrypted" size="14" filled class="text-primary" />
                Akses Terenkripsi &amp; Otentikasi Terpadu
            </span>
        </div>
    </div>

    {{-- Banner: hanya muncul bila pengguna diarahkan dari halaman yang butuh login --}}
    @if (session()->has('url.intended'))
        <div class="mx-auto w-full max-w-[1280px] px-4 pt-6 sm:px-8">
            <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-line/30 bg-[rgba(216,227,251,0.8)] p-[17px] shadow-[0_1px_2px_rgba(0,0,0,0.05)] backdrop-blur-sm">
                <div class="flex items-center gap-2">
                    <span class="flex size-10 shrink-0 items-center justify-center rounded-lg bg-secondary/20 text-secondary-ink">
                        <x-icon name="lock" size="22" filled />
                    </span>
                    <div>
                        <p class="font-heading text-base leading-6 font-semibold text-ink">Silakan masuk terlebih dahulu untuk melanjutkan.</p>
                        <p class="text-[13px] leading-[18px] text-muted">Setelah berhasil masuk, sistem otomatis mengembalikan Anda ke halaman tujuan.</p>
                    </div>
                </div>
                <span class="inline-flex items-center gap-1 rounded-full bg-white px-2 py-1 font-heading text-[11px] leading-[14px] font-bold text-primary drop-shadow-[0_1px_1px_rgba(0,0,0,0.05)]">
                    <x-icon name="verified_user" size="14" />
                    Akses Terproteksi
                </span>
            </div>
        </div>
    @endif

    <div class="mx-auto w-full max-w-[1280px] px-4 py-10 sm:px-8">
        <div class="grid grid-cols-12 gap-10">
            <x-card class="col-span-12 w-full lg:col-span-8 lg:col-start-3 lg:max-w-[729px]">
                {{-- Judul --}}
                <div class="flex flex-col gap-[3.2px] pb-6">
                    <span class="inline-flex w-fit items-center gap-1 rounded-full bg-mint/40 px-2 py-1 font-heading text-[11px] leading-[14px] font-bold text-primary">
                        <x-icon name="hub" size="14" />
                        Satu portal akses untuk Wisatawan, Pengelola Destinasi, dan Administrator
                    </span>
                    <x-page-header
                        class="pt-[4.8px]"
                        title="Masuk ke SwaraWisata"
                        description="Masuk untuk melihat detail laporan, mengirim laporan kerusakan, atau mengelola laporan destinasi Anda."
                    />
                </div>

                {{-- Pesan gagal login --}}
                @if ($errors->has('login'))
                    <x-alert variant="error" class="mb-4">{{ $errors->first('login') }}</x-alert>
                @endif

                {{-- Form --}}
                <form method="POST" action="{{ route('login.store') }}" class="flex flex-col gap-4" novalidate>
                    @csrf

                    <x-form.input
                        name="username"
                        label="Username"
                        icon="account_circle"
                        variant="soft"
                        required
                        required-text="*Wajib diisi"
                        autocomplete="username"
                        autofocus
                        placeholder="Masukkan username"
                    />

                    <x-form.input
                        name="password"
                        type="password"
                        label="Password"
                        icon="lock"
                        variant="soft"
                        required
                        required-text="*Wajib diisi"
                        autocomplete="current-password"
                        placeholder="Masukkan password"
                    />

                    <div class="pt-1">
                        <x-button type="submit" icon="login" block>Masuk</x-button>
                    </div>
                </form>

                {{-- Catatan untuk pengelola --}}
                <div class="pt-6">
                    <div class="flex items-start gap-2 rounded-xl bg-soft-strong p-4 drop-shadow-[0_1px_1px_rgba(0,0,0,0.05)]">
                        <span class="flex size-8 shrink-0 items-center justify-center rounded-full bg-primary/10 text-primary">
                            <x-icon name="vpn_key" size="18" />
                        </span>
                        <div class="flex min-w-0 flex-1 flex-col gap-0.5">
                            <p class="font-heading text-base leading-6 font-semibold text-ink">Catatan Khusus Pengelola Destinasi</p>
                            <p class="text-[15px] leading-[22px] font-semibold text-ink">Pengelola tempat wisata: gunakan username dan password dari admin.</p>
                            <p class="pt-px text-[13px] leading-[21.13px] text-muted">
                                Tidak tersedia pemulihan mandiri via email. Jika pengelola lupa password atau memerlukan
                                pembaruan kredensial, hubungi tim dinas atau administrator via WhatsApp resmi Layanan:
                                <a href="https://wa.me/6281234567890" target="_blank" rel="noopener" class="font-bold hover:text-primary">+62 812-3456-7890</a>.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Tautan bawah --}}
                <div class="flex flex-wrap items-end justify-between gap-2 pt-10">
                    <p class="flex items-end gap-1">
                        <span class="text-[13px] leading-[18px] text-muted">Belum punya akun?</span>
                        <a href="{{ route('register') }}" class="font-heading text-sm leading-5 font-semibold text-primary hover:underline">Daftar sekarang</a>
                    </p>
                    <a href="{{ route('home') }}" class="font-heading text-xs leading-4 font-semibold text-muted hover:text-primary">← Kembali ke Beranda</a>
                </div>
            </x-card>
        </div>
    </div>
</x-layouts.public>