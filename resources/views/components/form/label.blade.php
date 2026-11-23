{{--
    Label form. Biasanya tidak dipakai langsung karena sudah ada di <x-form.input>.
    Contoh: <x-form.label for="judul" required>Judul Laporan</x-form.label>
--}}
@props([
    'for' => null,
    'required' => false,
])

<label @if ($for) for="{{ $for }}" @endif {{ $attributes->class('inline-flex items-center gap-1 font-heading text-sm leading-5 font-semibold text-ink') }}>
    <span>{{ $slot }}</span>
    @if ($required)
        <span class="text-danger" aria-hidden="true">*</span>
    @endif
</label>
