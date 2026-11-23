{{--
    Area teks dengan gaya yang sama seperti <x-form.input>.
    Contoh: <x-form.textarea name="deskripsi" label="Deskripsi" rows="5" required counter maxlength="1000" />
--}}
@props([
    'name',
    'label' => null,
    'id' => null,
    'hint' => null,
    'hintIcon' => 'info',
    'required' => false,
    'counter' => false,
    'value' => null,
])

@php
    $id = $id ?? $name;
    $error = $errors->first($name);
    $current = old($name, $value);
    $maxlength = $attributes->get('maxlength');
    $describedBy = $error ? "{$id}-error" : ($hint ? "{$id}-hint" : null);
@endphp

<div class="flex w-full flex-col gap-1">
    @if ($label || ($counter && $maxlength))
        <div class="flex items-center justify-between gap-2">
            @if ($label)
                <x-form.label :for="$id" :required="$required">{{ $label }}</x-form.label>
            @endif
            @if ($counter && $maxlength)
                <span data-counter-for="{{ $id }}" class="font-heading text-[11px] leading-[14px] font-bold text-subtle">{{ mb_strlen((string) $current) }}/{{ $maxlength }}</span>
            @endif
        </div>
    @endif

    <textarea
        id="{{ $id }}"
        name="{{ $name }}"
        @required($required)
        @if ($error) aria-invalid="true" @endif
        @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif
        {{ $attributes->merge(['rows' => 4])->class([
            'block w-full rounded-lg bg-white px-4 py-[9px] text-[15px] leading-6 text-ink shadow-[0_1px_2px_rgba(0,0,0,0.05)] ring-1 ring-inset transition',
            'placeholder:text-placeholder focus:outline-none focus:ring-2 focus:ring-primary',
            'ring-danger' => $error,
            'ring-black/5' => ! $error,
        ]) }}
    >{{ $current }}</textarea>

    <x-form.hint :id="$id" :error="$error" :hint="$hint" :icon="$hintIcon" />
</div>
