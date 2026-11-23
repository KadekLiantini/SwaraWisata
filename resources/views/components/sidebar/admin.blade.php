{{--
    Sidebar admin (Figma: DESIGN SYSTEM > Aside gelap).
    Menu memakai nama route di bawah. Bila route belum dibuat, tautan menjadi "#".
--}}
@php
    $menu = [
        ['label' => 'Dashboard Kurasi',    'icon' => 'space_dashboard',      'route' => 'admin.dashboard',       'active' => 'admin.dashboard'],
        ['label' => 'Pengajuan Lokasi',    'icon' => 'add_location_alt',     'route' => 'admin.pengajuan.index', 'active' => 'admin.pengajuan.*'],
        ['label' => 'Kelola Destinasi',    'icon' => 'forest',               'route' => 'admin.destinasi.index', 'active' => 'admin.destinasi.*'],
        ['label' => 'Akun Pengelola',      'icon' => 'shield_person',        'route' => 'admin.pengelola.index', 'active' => 'admin.pengelola.*'],
        ['label' => 'Kategori Pelaporan',  'icon' => 'category',             'route' => 'admin.kategori.index',  'active' => 'admin.kategori.*'],
    ];
@endphp

<aside {{ $attributes->class('sticky top-0 hidden h-screen w-72 shrink-0 flex-col justify-between bg-admin drop-shadow-[0_1px_4px_rgba(0,0,0,0.08)] lg:flex') }}>
    <div>
        <div class="p-6">
            <x-logo variant="white" :href="route('admin.dashboard')" class="p-1" />
        </div>

        <nav class="flex flex-col gap-1 px-4 pt-2">
            @foreach ($menu as $item)
                <x-sidebar.link
                    theme="dark"
                    :href="Route::has($item['route']) ? route($item['route']) : '#'"
                    :icon="$item['icon']"
                    :active="request()->routeIs($item['active'])"
                >{{ $item['label'] }}</x-sidebar.link>
            @endforeach
        </nav>
    </div>

    <form method="POST" action="{{ route('logout') }}" class="p-4">
        @csrf
        <button type="submit" class="flex w-full items-center gap-2 rounded-lg px-4 py-2 font-heading text-xs leading-4 font-semibold text-danger-soft transition hover:bg-white/5">
            <x-icon name="logout" size="18" /> Keluar Sistem
        </button>
    </form>
</aside>
