{{--
    Badge status laporan / pengajuan.
    Contoh: <x-status-badge :status="$laporan->status" />  |  <x-status-badge :status="$pengajuan->status" size="sm" />
    status laporan:   belum_dilihat | dilihat | ditindaklanjuti | selesai
    status pengajuan: menunggu_verifikasi | disetujui | ditolak
    size: md (kartu landing page) | sm (tabel)
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

        'menunggu_verifikasi' => ['label' => 'Menunggu Verifikasi', 'box' => 'bg-peach',       'dot' => 'bg-secondary', 'text' => 'text-[#653e00]'],
        'disetujui'           => ['label' => 'Disetujui',           'box' => 'bg-mint',        'dot' => 'bg-primary',   'text' => 'text-[#002117]'],
        'ditolak'             => ['label' => 'Ditolak',             'box' => 'bg-danger-soft', 'dot' => 'bg-danger',    'text' => 'text-danger'],
    ];
    $style = $styles[$status] ?? $styles['belum_dilihat'];
@endphp

<span {{ $attributes->class([
    'inline-flex items-center gap-1.5 rounded-full font-heading whitespace-nowrap drop-shadow-[0_1px_1px_rgba(0,0,0,0.05)]',
    'px-2.5 py-1 text-[15px] leading-[22px] font-semibold' => $size === 'md',
    'px-2 py-1 text-[11px] leading-[14px] font-bold' => $size === 'sm',
    $style['box'],
    $style['text'],
]) }}>
    <span @class(['size-2 shrink-0 rounded-full', $style['dot']])></span>
    {{ $style['label'] }}
</span>