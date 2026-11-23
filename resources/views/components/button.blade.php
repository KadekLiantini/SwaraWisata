{{--
    Tombol. Menjadi <a> bila diberi href, selain itu <button>.
    variant: primary | secondary | outline | ghost | danger
    size:    sm | md | lg
    Contoh:
      <x-button type="submit" size="lg" icon="how_to_reg" block>Daftar</x-button>
      <x-button :href="route('login')" size="sm" icon-left="login">Masuk</x-button>
--}}
@props([
    'variant' => 'primary',
    'size' => 'md',
    'href' => null,
    'type' => 'button',
    'icon' => null,
    'iconLeft' => null,
    'block' => false,
])

@php
    $variants = [
        'primary'   => 'bg-primary text-white shadow-md hover:bg-primary-hover',
        'secondary' => 'bg-secondary text-ink shadow-md hover:brightness-95',
        'outline'   => 'bg-white text-primary ring-1 ring-inset ring-primary hover:bg-soft',
        'ghost'     => 'text-primary hover:bg-soft',
        'danger'    => 'bg-danger text-white shadow-md hover:brightness-110',
    ];

    $sizes = [
        'sm' => 'gap-1 px-4 py-2 text-sm leading-5 font-bold',
        'md' => 'gap-2 px-5 py-3 text-sm leading-5 font-semibold',
        'lg' => 'gap-2 px-6 py-4 text-sm leading-5 font-semibold',
    ];

    $iconSize = $size === 'sm' ? 15 : 20;

    $classes = implode(' ', [
        'inline-flex items-center justify-center rounded-lg font-heading transition',
        'focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary',
        'disabled:cursor-not-allowed disabled:opacity-60',
        $variants[$variant] ?? $variants['primary'],
        $sizes[$size] ?? $sizes['md'],
        $block ? 'w-full' : '',
    ]);
@endphp

@if ($href)
    <a href="{{ $href }}" {{ $attributes->class($classes) }}>
        @if ($iconLeft) <x-icon :name="$iconLeft" :size="$iconSize" /> @endif
        <span>{{ $slot }}</span>
        @if ($icon) <x-icon :name="$icon" :size="$iconSize" /> @endif
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->class($classes) }}>
        @if ($iconLeft) <x-icon :name="$iconLeft" :size="$iconSize" /> @endif
        <span>{{ $slot }}</span>
        @if ($icon) <x-icon :name="$icon" :size="$iconSize" /> @endif
    </button>
@endif
