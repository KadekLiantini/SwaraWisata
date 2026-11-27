{{--
    Dialog ganti password, bisa dipakai semua role.
    Contoh:
      <button type="button" onclick="document.getElementById('dialog-password').showModal()">Ganti Password</button>
      <x-dialog.ganti-password :action="route('pengelola.password.update')" />
    Dialog terbuka otomatis lagi bila validasi gagal.
--}}
@props([
    'action',
    'id' => 'dialog-password',
])

<dialog id="{{ $id }}" {{ $attributes->class('m-auto w-[calc(100%-2rem)] max-w-md rounded-xl bg-white p-0 shadow-xl backdrop:bg-ink/40') }}>
    <form method="POST" action="{{ $action }}" class="flex flex-col gap-4 p-6">
        @csrf
        @method('PUT')

        <div class="flex items-start justify-between gap-4">
            <div>
                <h2 class="font-heading text-xl leading-7 font-bold text-ink">Ganti Password</h2>
                <p class="text-[13px] leading-[18px] text-muted">Masukkan password lama, lalu buat password baru.</p>
            </div>
            <button type="button" onclick="this.closest('dialog').close()" aria-label="Tutup" class="rounded-lg p-1 text-muted hover:bg-soft">
                <x-icon name="close" size="20" />
            </button>
        </div>

        <x-form.input name="password_lama" type="password" label="Password Lama" icon="lock" required autocomplete="current-password" />
        <x-form.input name="password_baru" type="password" label="Password Baru" icon="lock_reset" required autocomplete="new-password"
                      hint="Minimal 8 karakter dan berbeda dari password lama." hint-icon="shield" />
        <x-form.input name="password_baru_confirmation" type="password" label="Konfirmasi Password Baru" icon="lock_clock" required autocomplete="new-password"
                      data-match="password_baru" hint="Ulangi password baru." hint-icon="sync_alt" />

        <div class="flex justify-end gap-2 pt-2">
            <x-button variant="ghost" onclick="this.closest('dialog').close()">Batal</x-button>
            <x-button type="submit" icon="check">Simpan Password</x-button>
        </div>
    </form>
</dialog>

@if ($errors->hasAny(['password_lama', 'password_baru']))
    @push('scripts')
        <script>document.getElementById(@js($id)).showModal();</script>
    @endpush
@endif