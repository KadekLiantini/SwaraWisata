{{-- Header atas halaman pengelola (Figma: DESIGN SYSTEM > Header "Kawasan Wisata ..."). --}}
@php
    $akun = auth()->user();
    $profil = Route::has('pengelola.profil') ? route('pengelola.profil') : '#';
@endphp

<header {{ $attributes->class('sticky top-0 z-30 flex h-20 items-center justify-between gap-4 bg-page/85 px-4 shadow-[0_1px_8px_rgba(0,0,0,0.04)] backdrop-blur-2xl sm:px-10') }}>
    <div class="flex min-w-0 flex-col gap-0.5">
        <p class="truncate font-heading text-xl leading-7 font-semibold text-ink">{{ $akun->destinasi?->nama ?? 'Portal Pengelola' }}</p>
        <p class="flex items-center gap-1 font-heading text-[11px] leading-[14px] font-bold text-muted">
            <x-icon name="domain" size="14" filled class="text-primary" />
            Sistem Pengawasan &amp; Tata Kelola Destinasi
        </p>
    </div>

    <div class="flex shrink-0 items-center gap-4">
        <a href="{{ $profil }}" class="flex items-center gap-2 pl-4">
            <span class="hidden text-right sm:block">
                <span class="block font-heading text-sm leading-[17.5px] font-semibold text-ink">Pengelola</span>
                <span class="block font-heading text-[11px] leading-[14px] font-bold text-muted">{{ '@'.$akun->username }}</span>
            </span>
            <span class="flex size-8 items-center justify-center rounded-full bg-primary text-white">
                <x-icon name="person" size="14" />
            </span>
        </a>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" aria-label="Keluar" title="Keluar"
                    class="flex size-9 items-center justify-center rounded-lg text-muted transition hover:bg-soft hover:text-danger">
                <x-icon name="logout" size="18" />
            </button>
        </form>
    </div>
</header>
