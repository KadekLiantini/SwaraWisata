{{--
    Kartu pratinjau laporan (Figma: Landing Page > Laporan Terbaru dari Wisatawan).
    Contoh:
      <x-laporan.preview-card :laporan="$laporan" :href="route('laporan.show', $laporan)" />
      <x-laporan.preview-card :laporan="$laporan" :href="..." action="Lihat Detail" action-icon="arrow_forward" />
    Relasi yang perlu di-load: ->with(['destinasi', 'kategoriKerusakan', 'foto'])
--}}
@props([
    'laporan',
    'href' => '#',
    'action' => 'Lihat Selengkapnya (Masuk)',
    'actionIcon' => 'lock',
])

@php
    $foto = $laporan->foto->first();
@endphp

<article {{ $attributes->class('flex flex-col justify-between overflow-hidden rounded-xl bg-soft shadow-[0_1px_2px_rgba(0,0,0,0.05)]') }}>
    <div>
        <div class="relative h-48 overflow-hidden bg-soft-strong">
            <div class="absolute inset-0 flex items-center justify-center text-line">
                <x-icon name="image" size="40" />
            </div>
            @if ($foto)
                <img src="{{ $foto->url }}" alt="Foto laporan: {{ $laporan->judul }}" loading="lazy"
                     class="absolute inset-0 size-full object-cover" onerror="this.remove()">
            @endif

            <x-status-badge :status="$laporan->status" class="absolute top-2 left-2" />

            <span class="absolute right-2 bottom-2 rounded bg-[rgba(38,49,67,0.8)] px-2 py-0.5 font-heading text-[15px] leading-[22px] font-semibold text-[#ecf1ff] backdrop-blur-sm">
                {{ $laporan->created_at->locale('id')->translatedFormat('d M Y') }}
            </span>
        </div>

        <div class="flex flex-col gap-1 p-4">
            @if ($laporan->kategoriKerusakan)
                <p class="flex items-center gap-1 font-heading text-[15px] leading-[22px] font-semibold text-primary">
                    <x-icon :name="$laporan->kategoriKerusakan->icon()" size="16" />
                    <span class="truncate">{{ $laporan->kategoriKerusakan->nama }}</span>
                </p>
            @endif
            <h3 class="line-clamp-2 font-heading text-base leading-6 font-semibold text-ink">{{ $laporan->judul }}</h3>
            <p class="flex items-center gap-1 pt-1 text-[13px] leading-[18px] text-muted">
                <x-icon name="location_on" size="14" />
                <span class="truncate">{{ $laporan->destinasi?->nama }}</span>
            </p>
        </div>
    </div>

    <div class="px-4 pb-4">
        <a href="{{ $href }}" class="flex w-full items-center justify-center gap-1 rounded-lg bg-soft-strong p-2 font-heading text-[15px] leading-[22px] font-semibold text-primary transition hover:bg-soft-blue">
            <x-icon :name="$actionIcon" size="15" />
            {{ $action }}
        </a>
    </div>
</article>