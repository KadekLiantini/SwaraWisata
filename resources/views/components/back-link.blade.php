{{--
    Tautan kembali dengan panah.
    Contoh: <x-back-link /> | <x-back-link :href="route('pengelola.laporan.index')">Kembali ke daftar</x-back-link>
--}}
@props([
    'href' => null,
])

<a href="{{ $href ?? route('home') }}" {{ $attributes->class('inline-flex items-center gap-1 font-heading text-sm leading-5 font-semibold text-primary hover:underline') }}>
    <x-icon name="arrow_back" size="16" />
    <span>{{ $slot->isEmpty() ? 'Kembali ke Beranda' : $slot }}</span>
</a>
