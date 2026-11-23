{{--
    Dropdown dengan gaya yang sama seperti <x-form.input>.

    Props: name, label, options (array [value => teks] atau Collection), selected, placeholder,
           icon, hint, hint-icon, required

    Contoh (form laporan):
      <x-form.select name="id_kategori_kerusakan" label="Kategori Kerusakan" icon="category" required
                     :options="$kategori->pluck('nama', 'id_kategori_kerusakan')"
                     placeholder="Pilih kategori" />
--}}
@props([
    'name',
    'label' => null,
    'id' => null,
    'options' => [],
    'selected' => null,
    'placeholder' => null,
    'icon' => null,
    'hint' => null,
    'hintIcon' => 'info',
    'required' => false,
])

@php
    $id = $id ?? $name;
    $error = $errors->first($name);
    $current = (string) old($name, $selected);
    $describedBy = $error ? "{$id}-error" : ($hint ? "{$id}-hint" : null);
@endphp

<div class="flex w-full flex-col gap-1">
    @if ($label)
        <x-form.label :for="$id" :required="$required">{{ $label }}</x-form.label>
    @endif

    <div class="relative">
        @if ($icon)
            <span @class(['pointer-events-none absolute inset-y-0 left-0 flex items-center text-subtle', 'pl-3.5' => $soft, 'pl-4' => ! $soft])>
                <x-icon :name="$icon" size="20" />
            </span>
        @endif

        <select
            id="{{ $id }}"
            name="{{ $name }}"
            @required($required)
            @if ($error) aria-invalid="true" @endif
            @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif
            {{ $attributes->class([
                'block w-full appearance-none rounded-lg bg-white py-[9px] pr-11 text-[15px] leading-[18px] text-ink shadow-[0_1px_2px_rgba(0,0,0,0.05)] ring-1 ring-inset transition',
                'focus:outline-none focus:ring-2 focus:ring-primary',
                'ring-danger' => $error,
                'ring-black/5' => ! $error,
                'pl-11' => $icon,
                'pl-4' => ! $icon,
            ]) }}
        >
            @if ($placeholder)
                <option value="" disabled @selected($current === '')>{{ $placeholder }}</option>
            @endif
            @foreach ($options as $value => $text)
                <option value="{{ $value }}" @selected($current === (string) $value)>{{ $text }}</option>
            @endforeach
        </select>

        <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-4 text-subtle">
            <x-icon name="expand_more" size="20" />
        </span>
    </div>

    <x-form.hint :id="$id" :error="$error" :hint="$hint" :icon="$hintIcon" />
</div>
