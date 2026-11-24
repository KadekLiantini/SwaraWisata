{{--
    Judul section bergaya landing page: label kecil (eyebrow) + judul + deskripsi.
    tone (warna label): mint | peach | plain | plain-gold
    align: center | left
--}}
@props([
    'eyebrow' => null,
    'title',
    'description' => null,
    'tone' => 'plain',
    'align' => 'center',
])

@php
    $tones = [
        'mint'       => 'bg-mint text-primary',
        'peach'      => 'bg-peach text-secondary-ink',
        'plain'      => 'text-primary',
        'plain-gold' => 'text-secondary-ink',
    ];
    $center = $align === 'center';
@endphp

<div {{ $attributes->class(['flex flex-col gap-1', 'mx-auto max-w-3xl items-center text-center' => $center, 'items-start' => ! $center]) }}>
    @if ($eyebrow)
        <span @class(['mb-1 rounded-full px-4 py-1 font-heading text-[15px] leading-[22px] font-semibold tracking-[0.75px] uppercase', $tones[$tone] ?? $tones['plain']])>
            {{ $eyebrow }}
        </span>
    @endif
    <h2 class="font-heading text-[28px] leading-9 font-bold tracking-[-0.8px] text-ink sm:text-[32px] sm:leading-10">{{ $title }}</h2>
    @if ($description)
        <p @class(['text-muted', 'text-[15px] leading-[24.38px]' => $center, 'text-[13px] leading-[18px]' => ! $center])>{{ $description }}</p>
    @endif
</div>