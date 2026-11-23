<x-layouts.public title="Daftar Akun" cta="login" glow :footer="false">
    <div class="mx-auto w-full max-w-[1280px] px-4 pt-6 pb-10 sm:px-8">
        <div class="py-2">
            <x-back-link />
        </div>

        <div class="mt-6 grid grid-cols-12 gap-6">
            <x-card class="col-span-12 flex flex-col gap-6 lg:col-span-7 lg:col-start-4">
                <x-alert variant="notice" title="Akses khusus wisatawan">
                    Halaman ini khusus wisatawan. Akun pengelola dibuat oleh admin.
                </x-alert>

                <x-page-header
                    title="Daftar Akun Wisatawan"
                    description="Buat akun untuk melaporkan kerusakan infrastruktur wisata dan memantau penanganannya."
                />

                <form method="POST" action="{{ route('register') }}" class="flex flex-col gap-6" novalidate>
                    @csrf

                    <x-form.input
                        name="username"
                        label="Username"
                        icon="badge"
                        required
                        counter
                        maxlength="20"
                        autocomplete="username"
                        autofocus
                        placeholder="Masukkan username unik"
                        hint="Harus unik dan belum dipakai akun lain. 4-20 karakter, huruf, angka, dan underscore (tanpa spasi)."
                    />

                    <x-form.input
                        name="password"
                        type="password"
                        label="Password"
                        icon="lock"
                        required
                        autocomplete="new-password"
                        placeholder="Masukkan password"
                        hint="Minimal 8 karakter. Disimpan dalam bentuk hash kriptografi."
                        hint-icon="shield"
                    />

                    <x-form.input
                        name="password_confirmation"
                        type="password"
                        label="Konfirmasi Password"
                        icon="lock_clock"
                        required
                        autocomplete="new-password"
                        placeholder="Ulangi password"
                        hint="Wajib sama dengan password untuk mencegah kesalahan pengetikan."
                        hint-icon="sync_alt"
                        data-match="password"
                    />

                    <div class="flex flex-col gap-4 pt-2">
                        <x-button type="submit" size="lg" icon="how_to_reg" block>Daftar</x-button>

                        <div class="flex flex-wrap items-center justify-between gap-2 pt-1">
                            <a href="{{ route('login') }}" class="inline-flex items-center gap-1 font-heading text-sm leading-5 font-semibold text-primary hover:underline">
                                Sudah punya akun? Masuk di sini
                                <x-icon name="login" size="16" />
                            </a>
                            <a href="{{ route('home') }}" class="font-heading text-[11px] leading-[14px] font-bold text-muted hover:text-primary">
                                ← Kembali ke Beranda
                            </a>
                        </div>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-layouts.public>
