{{--
    Sidebar pengelola (Figma: DESIGN SYSTEM > Aside putih).
    Menu memakai nama route di bawah. Bila route belum dibuat, tautan menjadi "#".
    Halaman profil pengelola dibuka dari avatar di topbar (route pengelola.profil).
--}}
@php
    $akun = auth()->user();
    $destinasi = $akun->destinasi?->loadMissing('kecamatan.kabupaten');
    $kabupaten = $destinasi?->kecamatan?->kabupaten?->nama;
    $kabupatenLabel = $kabupaten && ! str_starts_with($kabupaten, 'Kota') ? 'Kab. '.$kabupaten : $kabupaten;

    $menu = [
        ['label' => 'Dashboard',         'icon' => 'grid_view', 'route' => 'pengelola.dashboard',     'active' => 'pengelola.dashboard'],
        ['label' => 'Informasi Panduan', 'icon' => 'menu_book', 'route' => 'pengelola.info',          'active' => 'pengelola.info*'],
        ['label' => 'Daftar Laporan',    'icon' => 'flag',      'route' => 'pengelola.laporan.index', 'active' => 'pengelola.laporan.*'],
    ];
@endphp

<aside {{ $attributes->class('sticky top-0 hidden h-screen w-72 shrink-0 flex-col justify-between bg-white p-4 drop-shadow-[0_1px_4px_rgba(0,0,0,0.04)] lg:flex') }}>
    <div class="flex flex-col gap-4">
        <div class="p-1">
            <x-logo :href="route('pengelola.dashboard')" />
        </div>

        @if ($destinasi)
            <div class="flex flex-col gap-2.5 rounded-xl bg-soft p-2 pb-4">
                <p class="flex items-center gap-1 font-heading text-[11px] leading-[14px] font-bold tracking-[0.55px] text-primary uppercase">
                    <x-icon name="verified" size="18" /> Destinasi Terikat
                </p>
                <p class="font-heading text-base leading-6 font-semibold text-ink">{{ $destinasi->nama }}</p>
                @if ($destinasi->kecamatan)
                    <p class="flex items-center gap-1 font-heading text-xs leading-4 font-semibold text-muted">
                        <x-icon name="location_on" size="16" />
                        {{ $destinasi->kecamatan->nama }}{{ $kabupatenLabel ? ', '.$kabupatenLabel : '' }}
                    </p>
                @endif
            </div>
        @endif

        <nav class="flex flex-col gap-1 pt-1">
            @foreach ($menu as $item)
                <x-sidebar.link
                    :href="Route::has($item['route']) ? route($item['route']) : '#'"
                    :icon="$item['icon']"
                    :active="request()->routeIs($item['active'])"
                >{{ $item['label'] }}</x-sidebar.link>
            @endforeach
        </nav>
    </div>

    <form method="POST" action="{{ route('logout') }}" class="border-t border-line/30 pt-2">
        @csrf
        <button type="submit" class="flex w-full items-center gap-2 rounded-lg px-4 py-2 font-heading text-sm leading-5 font-semibold text-danger transition hover:bg-danger-soft/50">
            <x-icon name="logout" size="18" /> Keluar Portal
        </button>
    </form>
</aside>
