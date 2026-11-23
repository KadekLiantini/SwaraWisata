{{--
    Input teks/password/file dengan label, ikon, teks bantuan, dan pesan error otomatis.
    Nilai lama (old()) dan error validasi ($errors) terisi otomatis berdasarkan name.

    Props:
      name, label, type (text|password|number|url|file|...), icon (nama Material Symbol),
      hint, hint-icon, required, counter (tampilkan 0/maxlength), value
    Atribut lain (placeholder, maxlength, autocomplete, data-match, dll.) diteruskan ke <input>.

    Contoh:
      <x-form.input name="username" label="Username" icon="badge" required counter maxlength="20"
                    placeholder="Masukkan username unik" hint="4-20 karakter." />
      <x-form.input name="password" type="password" label="Password" icon="lock" required />
--}}
@props([
    'name',
    'label' => null,
    'type' => 'text',
    'id' => null,
    'icon' => null,
    'hint' => null,
    'hintIcon' => 'info',
    'required' => false,
    'counter' => false,
    'value' => null,
    'variant' => 'default',
    'requiredText' => null, 
])

@php
    $id = $id ?? str_replace(['[', ']'], ['_', ''], $name);
    $errorKey = str_replace(['[', ']'], ['.', ''], $name);
    $error = $errors->first($errorKey);
    $isPassword = $type === 'password';
    $current = ($isPassword || $type === 'file') ? null : old($errorKey, $value);
    $maxlength = $attributes->get('maxlength');
    $describedBy = $error ? "{$id}-error" : ($hint ? "{$id}-hint" : null);
    $soft = $variant === 'soft';
@endphp

<div class="flex w-full flex-col gap-1">
    @if ($label || ($counter && $maxlength) || $requiredText)
        <div class="flex items-center justify-between gap-2">
            @if ($label)
                <x-form.label :for="$id" :required="$required && ! $requiredText">{{ $label }}</x-form.label>
            @endif
            @if ($counter && $maxlength)
                <span data-counter-for="{{ $id }}" class="font-heading text-[11px] leading-[14px] font-bold text-subtle">{{ mb_strlen((string) $current) }}/{{ $maxlength }}</span>
            @elseif ($requiredText)
                <span class="font-heading text-[11px] leading-[14px] font-bold text-primary">{{ $requiredText }}</span>
            @endif
        </div>
    @endif

    <div class="relative">
        @if ($icon)
            <span @class(['pointer-events-none absolute inset-y-0 left-0 flex items-center text-subtle', 'pl-3.5' => $soft, 'pl-4' => ! $soft])>
                <x-icon :name="$icon" size="20" />
            </span>
        @endif

        <input
            id="{{ $id }}"
            name="{{ $name }}"
            type="{{ $type }}"
            @if (! is_null($current)) value="{{ $current }}" @endif
            @required($required)
            @if ($error) aria-invalid="true" @endif
            @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif
            {{ $attributes->class([
                'block w-full rounded-lg text-[15px] leading-[18px] text-ink shadow-[0_1px_2px_rgba(0,0,0,0.05)] ring-1 ring-inset transition',
                'placeholder:text-placeholder focus:outline-none focus:ring-2 focus:ring-primary',
                'file:mr-3 file:rounded-md file:border-0 file:bg-soft file:px-3 file:py-1 file:font-heading file:text-sm file:font-semibold file:text-primary',
                'bg-soft pt-[13px] pb-[14px]' => $soft,
                'bg-white py-[9px]' => ! $soft,
                'ring-danger' => $error,
                'ring-transparent' => $soft && ! $error,
                'ring-black/5' => ! $soft && ! $error,
                'pl-10' => $icon && $soft,
                'pl-11' => $icon && ! $soft,
                'pl-4' => ! $icon,
                'pr-12' => $isPassword,
                'pr-4' => ! $isPassword,
            ]) }}
        >

        @if ($isPassword)
            <button type="button" data-toggle-password="{{ $id }}" aria-label="Tampilkan password" aria-pressed="false"
                    class="absolute inset-y-0 right-0 flex items-center pr-4 text-subtle hover:text-ink focus-visible:text-primary focus-visible:outline-none">
                <x-icon name="visibility" size="20" />
            </button>
        @endif
    </div>

    <x-form.hint :id="$id" :error="$error" :hint="$hint" :icon="$hintIcon" />
</div>
