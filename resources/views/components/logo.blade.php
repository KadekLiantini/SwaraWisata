{{--
    Logo SwaraWisata.
    Contoh: <x-logo /> | <x-logo variant="white" :href="route('admin.dashboard')" />
    File logo: public/images/logo.svg dan public/images/logo-white.svg (ekspor dari Figma).
--}}
@props([
    'variant' => 'default',
    'href' => null,
])

@php
    $white = $variant === 'white';
@endphp

<a href="{{ $href ?? route('home') }}" {{ $attributes->class('inline-flex shrink-0 items-center gap-2') }}>
    <img src="{{ asset($white ? 'images/logo-white.svg' : 'images/logo.svg') }}" alt="" class="size-8" width="32" height="32">
    <span @class([
        'font-heading text-xl leading-7 font-semibold tracking-[-0.5px]',
        'text-white' => $white,
        'text-primary' => ! $white,
    ])>SwaraWisata</span>
</a>
