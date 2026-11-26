{{--
    Modal Edit Laporan. Dipakai di:
      - wisatawan/laporan/show.blade.php : @include('wisatawan.laporan.partials.modal-edit', ['kembaliKe' => url()->current()])
      - wisatawan/profil.blade.php       : dimuat lewat route laporan.edit saat ikon edit diklik
    Variabel: $laporan (dengan relasi foto & destinasi), $kategoriKerusakan, $kembaliKe (opsional)
    JavaScript: resources/js/modal-edit-laporan.js
--}}
<div
    id="modal-edit-laporan"
    class="fixed inset-0 z-[100] hidden items-center justify-center bg-black/50 p-4"
    @if ($errors->any() && (string) old('_edit_laporan') === (string) $laporan->id_laporan) data-buka-otomatis @endif
>
    <div class="relative max-h-[90vh] w-full max-w-2xl overflow-y-auto rounded-2xl bg-white p-6 shadow-xl sm:p-8">

        {{-- Header Modal --}}
        <div class="mb-6 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="flex size-12 shrink-0 items-center justify-center rounded-xl bg-primary text-white">
                    <x-icon name="camera_alt" size="24" />
                </span>

                <div>
                    <h2 class="font-heading text-xl font-bold text-ink">
                        Edit Laporan
                    </h2>
                    <p class="text-sm text-muted">
                        Perbarui informasi laporan kerusakan
                    </p>
                </div>
            </div>

            <button
                id="btn-tutup-modal-edit"
                type="button"
                class="flex size-9 items-center justify-center rounded-lg text-muted transition hover:bg-soft hover:text-ink"
            >
                <x-icon name="close" size="22" />
            </button>
        </div>
        <form
            action="{{ route('laporan.update', $laporan->id_laporan) }}"
            method="POST"
            enctype="multipart/form-data"
            class="space-y-5"
        >
            @csrf
            @method('PUT')
            <input type="hidden" name="_edit_laporan" value="{{ $laporan->id_laporan }}">
            <input type="hidden" name="kembali" value="{{ $kembaliKe ?? url()->current() }}">

            {{-- Destinasi Terpilih --}}
            <div>
                <div class="mb-2 flex items-center justify-between">
                    <label class="font-heading text-sm font-medium text-ink">
                        Destinasi Terpilih
                    </label>
                    <span class="flex items-center gap-1 text-xs font-medium text-muted">
                        <x-icon name="lock" size="14" />
                        Terkunci Otomatis
                    </span>
                </div>

                <div class="flex items-center gap-2 rounded-xl bg-soft-strong px-4 py-3 font-heading text-sm font-semibold text-ink">
                    <x-icon name="flag" size="18" class="text-primary" />
                    {{ $laporan->destinasi->nama }}
                </div>
            </div>

            {{-- Judul --}}
            <x-form.input
                name="judul"
                label="Judul Laporan Kerusakan"
                placeholder="Masukkan judul kerusakan"
                maxlength="80"
                counter
                required
                variant="soft"
                :value="$laporan->judul"
            />

            {{-- Kategori --}}
            <x-form.select
                name="id_kategori_kerusakan"
                label="Kategori Kerusakan"
                :options="$kategoriKerusakan->pluck('nama', 'id_kategori_kerusakan')"
                :selected="$laporan->id_kategori_kerusakan"
                placeholder="Pilih kategori"
                required
            />

            {{-- Lokasi Spesifik --}}
            <x-form.input
                name="lokasi_spesifik"
                label="Lokasi Spesifik Titik Kerusakan"
                placeholder="Sebutkan titik lokasi kerusakan"
                icon="location_on"
                required
                variant="soft"
                :value="$laporan->lokasi_spesifik"
            />

            {{-- Deskripsi --}}
            <x-form.textarea
                name="deskripsi"
                label="Deskripsi Kerusakan Terperinci"
                placeholder="Jelaskan kondisi kerusakan secara terperinci..."
                rows="4"
                maxlength="1000"
                required
                class="bg-soft"
                :value="$laporan->deskripsi"
            />

            {{-- Bukti Foto --}}
            <div>
                <label class="mb-2 block font-heading text-sm font-medium text-ink">
                    Bukti Foto Kerusakan
                </label>

                {{-- Foto lama dan preview foto baru --}}
                <div
                    id="daftar-foto-edit"
                    class="mb-3 grid grid-cols-2 gap-3 sm:grid-cols-3"
                >
                    @foreach ($laporan->foto as $foto)
                        <div
                            class="relative overflow-hidden rounded-xl border border-black/5 bg-white p-2"
                            data-foto-lama
                        >
                            <img
                                src="{{ $foto->url }}"
                                alt="Foto laporan"
                                class="h-28 w-full rounded-lg object-cover"
                            >

                            <p class="mt-2 truncate text-xs font-medium text-ink">
                                Foto sebelumnya
                            </p>

                            <button
                                type="button"
                                class="btn-hapus-foto-lama absolute right-3 top-3 flex size-6 items-center justify-center rounded-full bg-red-500 text-sm font-bold text-white shadow-md transition hover:bg-red-600"
                                data-id="{{ $foto->id_foto_laporan }}"
                                title="Hapus foto"
                            >
                                ×
                            </button>
                        </div>
                    @endforeach
                </div>

                {{-- ID foto lama yang akan dihapus --}}
                <div id="foto-hapus-container"></div>

                <label
                    for="foto-edit"
                    class="flex cursor-pointer flex-col items-center gap-2 rounded-xl border border-dashed border-[#b6c6e3] bg-soft p-6 text-center transition hover:bg-soft-strong"
                >
                    <x-icon name="cloud_upload" size="24" />
                    <span class="text-sm text-ink">
                        Tambahkan foto baru
                        <span class="font-semibold text-primary underline">Pilih File</span>
                    </span>
                    <span class="text-xs text-muted">
                        Maksimal 3 foto secara keseluruhan (JPG/PNG/WEBP)
                    </span>
                </label>

                <input
                    type="file"
                    name="foto[]"
                    id="foto-edit"
                    accept="image/jpeg,image/png,image/webp"
                    multiple
                    data-jumlah-foto-lama="{{ $laporan->foto->count() }}"
                    class="hidden"
                >

                @error('foto')
                    <p class="mt-2 flex items-center gap-1 text-[13px] leading-[18px] text-danger">
                        <x-icon name="error" size="16" /> {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Tombol --}}
            <div class="flex items-center justify-end gap-3 pt-2">
                <button
                    type="button"
                    id="btn-batal-edit"
                    class="rounded-xl px-5 py-3 font-heading text-sm font-medium text-ink transition hover:bg-soft"
                >
                    Batal
                </button>

                <x-button type="submit" size="lg" icon="save">
                    Simpan Perubahan
                </x-button>
            </div>
        </form>
    </div>
</div>