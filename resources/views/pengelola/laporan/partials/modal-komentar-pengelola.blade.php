<div
    id="modal-komentar-pengelola"
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
                    id="komentar-pengelola-tahap"
                    class="mt-1.5 text-sm font-medium text-primary"
                ></p>
            </div>

            <button
                type="button"
                data-tutup-komentar-pengelola
                aria-label="Tutup modal"
                class="rounded-lg p-2 text-subtle transition hover:bg-soft"
            >
                <x-icon name="close" size="20" />
            </button>
        </div>

        {{-- Area diskusi --}}
        <div class="min-h-0 flex-1 overflow-y-auto px-5 py-4">
            <div
                id="daftar-komentar-pengelola"
                class="space-y-4">
                <p class="text-sm text-subtle">
                    Belum ada komentar pada tahap penanganan ini.
                </p>
            </div>
        </div>

        {{-- Footer form --}}
        <div class="shrink-0 border-t border-line px-5 py-4">
            <form
                id="form-balasan-komentar-pengelola"
                method="POST"
                action=""
            >
                @csrf

                <div>
                    <div class="mb-2 flex items-center justify-between gap-3">
                        <label
                            for="isi-balasan-pengelola"
                            class="text-sm font-semibold text-ink"
                        >
                            Tulis Tanggapan Anda:
                        </label>

                        <div class="shrink-0 text-xs text-subtle">
                            <span id="jumlah-karakter-balasan">0</span> / 300
                        </div>
                    </div>

                    <textarea
                        id="isi-balasan-pengelola"
                        name="isi"
                        rows="2"
                        maxlength="300"
                        placeholder="Pilih komentar yang ingin dibalas terlebih dahulu..."
                        class="w-full resize-none rounded-xl border border-line bg-white px-4 py-2 text-sm leading-5 text-ink placeholder:text-sm placeholder:text-placeholder focus:border-primary focus:outline-none focus:ring-1 focus:ring-primary"
                        required
                        disabled
                    ></textarea>
                </div>

                {{-- Tombol --}}
                <div class="mt-3 flex flex-col-reverse justify-end gap-3 sm:flex-row">
                    <button
                        type="button"
                        data-tutup-komentar-pengelola
                        class="rounded-xl bg-soft-blue px-4 py-2 text-sm font-semibold text-ink transition hover:opacity-80"
                    >
                        Batal
                    </button>

                    <x-button
                        type="submit"
                        size="md"
                        icon="send"
                    >
                        Kirim Tanggapan
                    </x-button>
                </div>
            </form>
        </div>
    </div>

{{-- Template komentar berdasarkan riwayat penanganan --}}
@foreach ($laporan->riwayat as $riwayat)
    <template data-komentar-riwayat="{{ $riwayat->id_riwayat }}">
        @forelse ($riwayat->komentar as $komentar)
            <div class="rounded-xl border border-line bg-white p-4">
                {{-- Informasi penulis komentar --}}
                <div class="flex items-start justify-between gap-3">
                    <div class="flex min-w-0 items-center gap-3">
                        <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-soft-blue font-semibold text-ink">
                            {{ strtoupper(substr($komentar->penulis->username ?? 'P', 0, 1)) }}
                        </div>

                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="text-sm font-semibold text-ink">
                                    {{ $komentar->penulis->username ?? 'Pengguna' }}
                                </span>

                                @if ((int) $komentar->id_akun === (int) $laporan->id_akun)
                                    <span class="rounded-full bg-mint/50 px-2 py-0.5 text-xs font-medium text-primary">
                                        Pelapor
                                    </span>
                                @endif
                            </div>

                            <p class="mt-0.5 text-xs text-subtle">
                                {{ $komentar->created_at->timezone('Asia/Makassar')->locale('id')->translatedFormat('j F Y, H:i') }} WITA
                            </p>
                        </div>
                    </div>

                    <span class="shrink-0 text-xs text-subtle">
                        #{{ $komentar->id_komentar }}
                    </span>
                </div>

                {{-- Isi komentar --}}
                <p class="mt-3 whitespace-pre-line text-sm leading-relaxed text-ink">
                    {{ $komentar->isi }}
                </p>

                {{-- Balasan pengelola --}}
                @foreach ($komentar->balasan as $balasan)
                    <div class="mt-4 rounded-lg bg-soft p-3">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex min-w-0 items-center gap-2">
                                <x-icon name="reply" size="17" class="shrink-0 text-primary" />

                                <span class="text-sm font-semibold text-primary">
                                    {{ $balasan->penulis->username ?? 'Pengelola' }}
                                </span>

                                <span class="rounded-full bg-mint/50 px-2 py-0.5 text-xs font-medium text-primary">
                                    Tanggapan Resmi
                                </span>
                            </div>

                            <span class="shrink-0 text-xs text-subtle">
                                {{ $balasan->created_at->timezone('Asia/Makassar')->locale('id')->translatedFormat('j F Y, H:i') }} WITA
                            </span>
                        </div>

                        <p class="mt-2 whitespace-pre-line text-sm leading-relaxed text-ink">
                            {{ $balasan->isi }}
                        </p>

                    </div>
                @endforeach

                {{-- Status atau tombol balas komentar --}}
                <div class="mt-3 flex justify-end">
                    @if ($komentar->balasan->isNotEmpty())
                        <span class="rounded-lg bg-soft px-3 py-2 text-xs font-semibold text-muted">      
                            Sudah Ditanggapi
                        </span>
                    @else
                        <button
                            type="button"
                            data-pilih-komentar
                            data-action="{{ route('pengelola.laporan.komentar.balasan.store', [
                                $laporan->id_laporan,
                                $riwayat->id_riwayat,
                                $komentar->id_komentar,
                            ]) }}"
                            class="rounded-lg bg-primary px-3 py-2 text-xs font-semibold text-white transition hover:bg-primary-hover"
                        >
                            Balas Komentar
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <p class="rounded-xl bg-soft p-4 text-sm text-subtle">
                Belum ada komentar pada tahap penanganan ini.
            </p>
        @endforelse
    </template>
@endforeach
</div>