{{--
    Kartu putih. padding: lg (40px, seperti kartu Registrasi) | md (24px) | none
    Contoh: <x-card>...</x-card> | <x-card padding="md" class="lg:col-span-7">...</x-card>
--}}
@props([
    'padding' => 'lg',
])

@php
    $paddings = ['lg' => 'p-6 sm:p-10', 'md' => 'p-6', 'none' => ''];
@endphp

<div {{ $attributes->class(['rounded-xl bg-white shadow-md', $paddings[$padding] ?? $paddings['lg']]) }}>
    {{ $slot }}
</div>
