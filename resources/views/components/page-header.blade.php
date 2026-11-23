{{--
    Judul halaman + deskripsi.
    Contoh: <x-page-header title="Daftar Akun Wisatawan" description="Buat akun untuk ..." />
    Slot opsional untuk tombol di kanan: <x-page-header title="Destinasi"><x-button ...>Tambah</x-button></x-page-header>
--}}
@props([
    'title',
    'description' => null,
])

<div {{ $attributes->class('flex w-full flex-wrap items-end justify-between gap-4') }}>
    <div class="flex min-w-0 flex-col gap-1">
        <h1 class="font-heading text-[28px] leading-9 font-bold tracking-[-0.8px] text-ink sm:text-[32px] sm:leading-10">{{ $title }}</h1>
        @if ($description)
            <p class="text-[15px] leading-[24.38px] text-muted">{{ $description }}</p>
        @endif
    </div>
    @if ($slot->isNotEmpty())
        <div class="flex shrink-0 items-center gap-2">{{ $slot }}</div>
    @endif
</div>
