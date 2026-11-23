{{--
    Ikon Material Symbols Outlined (sama dengan ikon di Figma).
    Daftar nama ikon: https://fonts.google.com/icons
    Contoh: <x-icon name="lock" /> | <x-icon name="verified_user" filled size="18" class="text-primary" />
--}}
@props([
    'name',
    'size' => 20,
    'filled' => false,
])

<span
    {{ $attributes->class('material-symbols-outlined shrink-0 select-none') }}
    style="font-size: {{ $size }}px; font-variation-settings: 'FILL' {{ $filled ? 1 : 0 }}, 'wght' 400, 'GRAD' 0, 'opsz' {{ max(20, min(48, (int) $size)) }};"
    aria-hidden="true"
>{{ $name }}</span>
