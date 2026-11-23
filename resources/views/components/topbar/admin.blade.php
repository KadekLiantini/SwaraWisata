{{--
    Header atas halaman admin dengan breadcrumb (Figma: DESIGN SYSTEM > Header "Tata Kelola > ...").
    breadcrumbs: array teks, item terakhir ditampilkan hijau. Contoh: ['Tata Kelola', 'Kelola Destinasi']
--}}
@props([
    'breadcrumbs' => ['Tata Kelola', 'SwaraWisata Pusat'],
])

<header {{ $attributes->class('sticky top-0 z-30 flex h-16 items-center justify-between gap-4 bg-page/85 px-4 shadow-[0_1px_8px_rgba(0,0,0,0.04)] backdrop-blur-2xl sm:px-10') }}>
    <nav aria-label="Breadcrumb" class="flex min-w-0 items-center gap-1 font-heading text-[11px] leading-[14px] font-bold">
        @foreach ($breadcrumbs as $crumb)
            @if (! $loop->first)
                <x-icon name="chevron_right" size="14" class="text-subtle" />
            @endif
            <span @class(['truncate', 'text-primary' => $loop->last, 'text-subtle' => ! $loop->last])>{{ $crumb }}</span>
        @endforeach
    </nav>

    <div class="flex shrink-0 items-center gap-2 pl-4">
        <span class="hidden font-heading text-xs leading-4 font-semibold text-ink sm:block">{{ '@'.auth()->user()->username }}</span>
        <span class="flex size-8 items-center justify-center rounded-full bg-primary text-white">
            <x-icon name="person" size="14" />
        </span>
    </div>
</header>
