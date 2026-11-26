@php
    $crumbTataKelola = new class implements \Illuminate\Contracts\Support\Htmlable {
        public function toHtml()
        {
            return '<a href="'.e(route('admin.dashboard')).'" class="transition hover:text-primary">Tata Kelola</a>';
        }
    };

    $crumbKelolaDestinasi = new class implements \Illuminate\Contracts\Support\Htmlable {
        public function toHtml()
        {
            return '<a href="'.e(route('admin.destinasi.index')).'" class="transition hover:text-primary">Kelola Destinasi</a>';
        }
    };
@endphp

<x-layouts.admin
    title="Edit Destinasi"
    :breadcrumbs="[$crumbTataKelola, $crumbKelolaDestinasi, 'Edit Destinasi']"
>
    <div class="mx-auto max-w-5xl space-y-6">

        {{-- Header --}}
        <x-page-header
            title="Edit Destinasi Wisata"
            description="Perbarui informasi destinasi wisata yang telah terdaftar di SwaraWisata."
        />

        <form
            method="POST"
            action="{{ route('admin.destinasi.update', ['id_destinasi' => $destinasi->id_destinasi]) }}"
            enctype="multipart/form-data"
            class="space-y-6"
        >
            @csrf
            @method('PUT')

            {{-- Informasi destinasi --}}
            <x-card>
                <div class="space-y-5">
                    <div>
                        <h2 class="font-heading text-lg font-semibold text-ink">
                            Informasi Destinasi
                        </h2>
                        <p class="mt-1 text-sm text-muted">
                            Perbarui informasi dasar tempat wisata.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <x-form.input
                            name="nama"
                            label="Nama Tempat Wisata"
                            value="{{ old('nama', $destinasi->nama) }}"
                            maxlength="150"
                            required
                        />

                        <x-form.select
                            name="id_kategori_wisata"
                            label="Kategori Wisata"
                            :options="$kategoriWisata"
                            :selected="old('id_kategori_wisata', $destinasi->id_kategori_wisata)"
                            required
                        />

                        <x-form.select
                            name="id_kecamatan"
                            label="Kecamatan / Wilayah"
                            :options="$kecamatan"
                            :selected="old('id_kecamatan', $destinasi->id_kecamatan)"
                            required
                        />

                        <x-form.input
                            name="link_maps"
                            label="Tautan Lokasi Google Maps"
                            type="url"
                            placeholder="https://maps.google.com/..."
                            value="{{ old('link_maps', $destinasi->link_maps) }}"
                            maxlength="500"
                        />
                    </div>

                    <x-form.textarea
                        name="deskripsi"
                        label="Deskripsi Destinasi"
                        rows="4"
                        value="{{ old('deskripsi', $destinasi->deskripsi) }}"
                    />
                </div>
            </x-card>

            {{-- Foto destinasi --}}
            <x-card>
                <div class="space-y-4">
                    <div>
                        <h2 class="font-heading text-lg font-semibold text-ink">
                            Foto Destinasi
                        </h2>
                        <p class="mt-1 text-sm text-muted">
                            Perbarui foto publikasi destinasi wisata jika diperlukan.
                        </p>
                    </div>

                    <div class="rounded-xl border border-line bg-soft/50 p-5">
                        <p class="mb-3 text-sm font-medium text-ink">
                            Foto saat ini
                        </p>

                        @if ($destinasi->foto)
                            <img
                                src="{{ \Illuminate\Support\Facades\Storage::url($destinasi->foto) }}"
                                alt="Foto destinasi {{ $destinasi->nama }}"
                                class="h-40 w-full rounded-lg object-cover"
                            >
                        @else
                            <div class="flex h-40 items-center justify-center rounded-lg bg-white text-sm text-muted">
                                <x-icon name="image" class="mr-2 text-primary" />
                                Belum ada foto destinasi
                            </div>
                        @endif
                    </div>

                    <div>
                        <label for="foto" class="mb-2 block text-sm font-medium text-ink">
                            Ganti Foto Destinasi
                        </label>
                        <input
                            id="foto"
                            type="file"
                            name="foto"
                            accept="image/jpeg,image/png,image/webp"
                            @if ($errors->has('foto')) aria-invalid="true" @endif
                            class="block w-full text-sm text-muted file:mr-3 file:rounded-lg file:border-0 file:bg-primary file:px-4 file:py-2 file:font-medium file:text-white hover:file:bg-primary-hover"
                        />
                        <p class="mt-2 text-xs text-muted">
                            Format JPG, JPEG, PNG, atau WebP. Maksimal 2 MB. Kosongkan jika tidak ingin mengganti foto.
                        </p>

                        @error('foto')
                            <p class="pt-2 text-[13px] leading-[18px] text-danger">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>
            </x-card>

            {{-- Tombol aksi --}}
            <div class="flex flex-col-reverse justify-end gap-3 sm:flex-row">
                <x-button
                    href="{{ route('admin.destinasi.index') }}"
                    variant="outline"
                >
                    Batal
                </x-button>

                <x-button type="submit" variant="primary">
                    Simpan Perubahan
                </x-button>
            </div>
        </form>

    </div>
</x-layouts.admin>
