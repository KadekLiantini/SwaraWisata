
<div
    id="modal-komentar"
    class="fixed inset-0 z-[110] hidden items-center justify-center bg-black/50 p-4"
>
    <div class="flex h-[684px] max-h-[90vh] w-full max-w-[672px] flex-col overflow-hidden rounded-2xl bg-white shadow-xl">

        {{-- Header --}}
        <div class="flex shrink-0 items-start justify-between gap-4 border-b border-line px-5 py-4">
            <div>
                <h2 class="flex items-center gap-2 font-heading text-lg font-bold text-ink">
                    <x-icon name="forum" size="22" class="shrink-0 text-primary" />
                    Ruang Tanggapan & Komentar Penanganan
                </h2>

                <p
                    id="komentar-tahap"
                    class="mt-1.5 text-sm font-medium text-primary"
                ></p>
            </div>

            <button
                type="button"
                data-tutup-komentar
                aria-label="Tutup modal"
                class="rounded-lg p-2 text-subtle transition hover:bg-soft"
            >
                <x-icon name="close" size="20" />
            </button>
        </div>

        {{-- Area diskusi --}}
        <div
            id="daftar-komentar-wrapper"
            class="min-h-0 flex-1 overflow-y-auto px-5 py-4"
        >
            <div
                id="daftar-komentar"
                class="space-y-4"
            >
                <p class="text-sm text-subtle">
                    Belum ada komentar pada tahap penanganan ini.
                </p>
            </div>
        </div>

        {{-- Footer form --}}
        <div class="shrink-0 border-t border-line px-5 py-4">
            <form id="form-komentar" method="POST" action="">
                @csrf

                <div>
                    <div class="mb-2 flex items-center justify-between gap-3">
                        <label
                            for="isi-komentar"
                            class="text-sm font-semibold text-ink"
                        >
                            Tulis Tanggapan / Masukan Anda:
                        </label>

                        <div class="shrink-0 text-xs text-subtle">
                            <span id="jumlah-karakter">0</span> / 300
                        </div>
                    </div>

                    <textarea
                        id="isi-komentar"
                        name="isi"
                        rows="2"
                        maxlength="300"
                        placeholder="Tulis komentar, masukan, atau konfirmasi terkait progres penanganan ini..."
                        class="w-full resize-none rounded-xl border border-line bg-white px-4 py-2 text-sm leading-5 text-ink placeholder:text-sm placeholder:text-placeholder focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                        required
                    >{{ old('isi') }}</textarea>
                </div>

                {{-- Tombol --}}
                <div class="mt-3 flex flex-col-reverse justify-end gap-3 sm:flex-row">
                    <button
                        type="button"
                        data-tutup-komentar
                        class="rounded-xl bg-soft-blue px-4 py-2 text-sm font-semibold text-ink transition hover:opacity-80"
                    >
                        Batal
                    </button>

                    <x-button type="submit" size="md" icon="send">
                        Kirim Komentar
                    </x-button>
                </div>
            </form>
        </div>
    </div>
</div>
