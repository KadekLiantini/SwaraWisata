{{--
    Kotak pemberitahuan.
    variant: notice (kuning, seperti di halaman Registrasi) | info | success | error
    Contoh:
      <x-alert variant="notice" title="Akses khusus wisatawan">Halaman ini khusus wisatawan.</x-alert>
      <x-alert variant="success">Laporan berhasil dikirim.</x-alert>
--}}
@props([
    'variant' => 'info',
    'title' => null,
    'icon' => null,
])

@php
    $styles = [
        'notice'  => ['box' => 'bg-soft',         'accent' => 'text-secondary-ink', 'icon' => 'verified_user'],
        'info'    => ['box' => 'bg-soft',         'accent' => 'text-primary',       'icon' => 'info'],
        'success' => ['box' => 'bg-primary/10',   'accent' => 'text-primary',       'icon' => 'check_circle'],
        'error'   => ['box' => 'bg-danger-soft',  'accent' => 'text-danger',        'icon' => 'error'],
    ];
    $style = $styles[$variant] ?? $styles['info'];
@endphp

<div role="{{ $variant === 'error' ? 'alert' : 'status' }}" {{ $attributes->class(['flex w-full items-start gap-2 rounded-lg p-4', $style['box']]) }}>
    <x-icon :name="$icon ?? $style['icon']" size="18" filled :class="'mt-px '.$style['accent']" />
    <div class="flex min-w-0 flex-col gap-0.5">
        @if ($title)
            <p @class(['font-heading text-[11px] leading-[14px] font-bold tracking-[0.55px] uppercase', $style['accent']])>{{ $title }}</p>
        @endif
        <div class="text-[13px] leading-[18px] text-ink">{{ $slot }}</div>
    </div>
</div>
