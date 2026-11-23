{{--
    Satu item menu sidebar. Biasanya tidak dipanggil langsung oleh halaman.
    theme: light (pengelola) | dark (admin)
--}}
@props([
    'href' => '#',
    'icon',
    'active' => false,
    'theme' => 'light',
])

@php
    $classes = match (true) {
        $theme === 'light' && $active  => 'bg-primary-soft font-sans text-base leading-6 text-white shadow-sm',
        $theme === 'light'             => 'font-heading text-sm leading-5 text-muted hover:bg-soft hover:text-primary',
        $active                        => 'bg-primary font-heading text-[15px] leading-[22px] text-white shadow-sm',
        default                        => 'font-heading text-xs leading-4 text-line hover:bg-white/5 hover:text-white',
    };
@endphp

<a href="{{ $href }}" @if ($active) aria-current="page" @endif
   {{ $attributes->class(['flex w-full items-center gap-2 rounded-lg px-4 py-2 font-semibold transition', $classes]) }}>
    <x-icon :name="$icon" size="18" />
    <span>{{ $slot }}</span>
</a>
