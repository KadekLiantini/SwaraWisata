{{--
    Footer tabel: "Menampilkan X dari Y ..." + tombol halaman.
    Contoh: <x-pagination :paginator="$laporan" label="laporan terdaftar" />
    $paginator dari ->paginate(5)->withQueryString()
--}}
@props([
    'paginator',
    'label' => 'data',
])

<div {{ $attributes->class('flex flex-wrap items-center justify-between gap-3 bg-soft p-4') }}>
    <p class="text-[13px] leading-[18px] text-muted">
        Menampilkan <span class="font-semibold text-ink">{{ $paginator->count() }} dari {{ $paginator->total() }}</span> {{ $label }}
    </p>

    @if ($paginator->hasPages())
        <nav class="flex items-center gap-1 font-heading text-xs leading-4 font-semibold" aria-label="Halaman">
            @if ($paginator->onFirstPage())
                <span class="rounded-lg bg-soft-strong px-3 py-1.5 text-subtle">Sebelumnya</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" data-live-link class="rounded-lg bg-soft-strong px-3 py-1.5 text-ink hover:bg-soft-blue">Sebelumnya</a>
            @endif

            @foreach ($paginator->getUrlRange(max(1, $paginator->currentPage() - 2), min($paginator->lastPage(), $paginator->currentPage() + 2)) as $halaman => $url)
                @if ($halaman === $paginator->currentPage())
                    <span aria-current="page" class="rounded-lg bg-primary px-3 py-1.5 text-white">{{ $halaman }}</span>
                @else
                    <a href="{{ $url }}" data-live-link class="rounded-lg bg-soft-strong px-3 py-1.5 text-ink hover:bg-soft-blue">{{ $halaman }}</a>
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" data-live-link class="rounded-lg bg-soft-strong px-3 py-1.5 text-ink hover:bg-soft-blue">Selanjutnya</a>
            @else
                <span class="rounded-lg bg-soft-strong px-3 py-1.5 text-subtle">Selanjutnya</span>
            @endif
        </nav>
    @endif
</div>