{{--
    Header halaman publik & wisatawan (Figma: Header di Registrasi / DESIGN SYSTEM).
    - Tamu: tombol "Masuk" (atau "Daftar" bila cta="register", tanpa tombol bila cta="none").
    - Sudah login: avatar + username + menu dropdown.
    Slot opsional untuk tautan menu di tengah (dipakai landing page):
      <x-navbar.public><a href="#cara-kerja">Cara Kerja</a></x-navbar.public>
--}}
@props([
    'cta' => 'login',
])

@php
    $akun = auth()->user();
    $link = fn (string $route) => Route::has($route) ? route($route) : '#';
@endphp

<header {{ $attributes->class('fixed inset-x-0 top-0 z-40 bg-page/90 shadow-[0_1px_8px_rgba(0,0,0,0.04)] backdrop-blur-xl') }}>
    <div class="mx-auto flex h-20 w-full max-w-[1280px] items-center justify-between gap-6 px-4 sm:px-8">
        <x-logo />

        @if ($slot->isNotEmpty())
            <nav class="hidden items-center gap-8 font-heading text-sm font-semibold text-muted md:flex [&_a:hover]:text-primary">
                {{ $slot }}
            </nav>
        @endif

        @auth
            <details class="group relative">
                <summary class="flex cursor-pointer list-none items-center gap-2 rounded-xl p-1 pr-2 transition hover:bg-soft [&::-webkit-details-marker]:hidden">
                    <span class="flex size-10 items-center justify-center rounded-full bg-primary text-white shadow-sm">
                        <x-icon name="person" size="16" />
                    </span>
                    <span class="hidden max-w-36 truncate font-heading text-sm leading-[17.5px] font-semibold text-ink sm:block">{{ $akun->username }}</span>
                    <x-icon name="expand_more" size="20" class="text-subtle transition group-open:rotate-180" />
                </summary>

                <div class="absolute right-0 mt-2 w-56 rounded-xl bg-white p-2 shadow-lg ring-1 ring-black/5">
                    @if ($akun->isWisatawan())
                        <a href="{{ $link('wisatawan.profil') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 font-heading text-sm font-semibold text-muted hover:bg-soft hover:text-primary">
                            <x-icon name="account_circle" size="18" /> Profil Saya
                        </a>
                    @else
                        <a href="{{ $akun->dashboardRoute() }}" class="flex items-center gap-2 rounded-lg px-3 py-2 font-heading text-sm font-semibold text-muted hover:bg-soft hover:text-primary">
                            <x-icon name="grid_view" size="18" /> Dashboard
                        </a>
                    @endif

                    <form method="POST" action="{{ route('logout') }}" class="mt-1 border-t border-line/30 pt-1">
                        @csrf
                        <button type="submit" class="flex w-full items-center gap-2 rounded-lg px-3 py-2 font-heading text-sm font-semibold text-danger hover:bg-danger-soft/50">
                            <x-icon name="logout" size="18" /> Keluar
                        </button>
                    </form>
                </div>
            </details>
        @else
            @if ($cta === 'landing')
                <div class="flex items-center gap-2">
                    <a href="{{ route('login') }}" class="rounded-lg bg-primary px-4 py-2 font-heading text-sm leading-5 font-semibold text-white transition hover:bg-primary-hover">Masuk</a>
                    <a href="{{ route('register') }}" aria-label="Daftar akun wisatawan" title="Daftar akun"
                       class="ml-1 flex size-8 items-center justify-center rounded-full bg-primary text-white transition hover:bg-primary-hover">
                        <x-icon name="person" size="14" />
                    </a>
                </div>
            @elseif ($cta === 'login')
                <x-button :href="route('login')" size="sm" icon-left="login">Masuk</x-button>
            @endif
        @endauth
    </div>
</header>
