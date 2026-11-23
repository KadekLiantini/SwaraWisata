{{--
    Teks bantuan atau pesan error di bawah input. Dipakai oleh input, select, textarea.
--}}
@props([
    'id',
    'error' => null,
    'hint' => null,
    'icon' => 'info',
])

@if ($error)
    <p id="{{ $id }}-error" class="flex items-start gap-1 pt-1 text-[13px] leading-[18px] text-danger">
        <x-icon name="error" size="16" class="mt-px" />
        <span>{{ $error }}</span>
    </p>
@elseif ($hint)
    <p id="{{ $id }}-hint" class="flex items-start gap-1 pt-1 text-[13px] leading-[18px] text-muted">
        <x-icon :name="$icon" size="16" class="mt-px" />
        <span data-hint-text>{{ $hint }}</span>
    </p>
@endif
