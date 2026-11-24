{{--
    Badge status laporan (warna sesuai Figma landing page).
    Contoh: <x-status-badge :status="$laporan->status" />  |  <x-status-badge status="selesai" size="sm" />
    status: belum_dilihat | dilihat | ditindaklanjuti | selesai
--}}
@props([
    'status',
    'size' => 'md',
])

@php
    $styles = [
        'belum_dilihat'   => ['label' => 'Belum Dilihat',   'box' => 'bg-white',     'dot' => 'bg-placeholder', 'text' => 'text-muted'],
        'dilihat'         => ['label' => 'Dilihat',         'box' => 'bg-soft-blue', 'dot' => 'bg-subtle',      'text' => 'text-muted'],
        'ditindaklanjuti' => ['label' => 'Ditindaklanjuti', 'box' => 'bg-peach',     'dot' => 'bg-secondary',   'text' => 'text-[#653e00]'],
        'selesai'         => ['label' => 'Selesai',         'box' => 'bg-mint',      'dot' => 'bg-primary',     'text' => 'text-[#002117]'],
    ];
    $style = $styles[$status] ?? $styles['belum_dilihat'];
@endphp

<span {{ $attributes->class([
    'inline-flex items-center gap-1.5 rounded-full font-heading font-semibold whitespace-nowrap drop-shadow-[0_1px_1px_rgba(0,0,0,0.05)]',
    'px-2.5 py-1 text-[15px] leading-[22px]' => $size === 'md',
    'px-2 py-0.5 text-xs leading-4' => $size === 'sm',
    $style['box'],
    $style['text'],
]) }}>
    <span @class(['size-2 shrink-0 rounded-full', $style['dot']])></span>
    {{ $style['label'] }}
</span>