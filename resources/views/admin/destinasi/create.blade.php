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
    title="Tambah Destinasi"
    :breadcrumbs="[$crumbTataKelola, $crumbKelolaDestinasi, 'Tambah Destinasi']"
>
    <div class="mx-auto max-w-5xl space-y-6">

        {{-- Header --}}
        <x-page-header
            title="Tambah Destinasi Wisata"
            description="Lengkapi informasi destinasi wisata untuk ditambahkan ke dalam sistem SwaraWisata."
        />

        <form
            method="POST"
            action="{{ route('admin.destinasi.store') }}"
            enctype="multipart/form-data"
            class="space-y-6"
        >
            @csrf

            {{-- Informasi destinasi --}}
            <x-card>
                <div class="space-y-5">
                    <div>
                        <h2 class="font-heading text-lg font-semibold text-ink">
                            Informasi Destinasi
                        </h2>
                        <p class="mt-1 text-sm text-muted">
                            Masukkan informasi dasar tempat wisata.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <x-form.input
                            name="nama"
                            label="Nama Tempat Wisata"
                            placeholder="Contoh: Pantai Melasti Ungasan"
                            maxlength="150"
                            required
                        />

                        <x-form.select
                            name="id_kategori_wisata"
                            label="Kategori Wisata"
                            :options="$kategoriWisata"
                            placeholder="Pilih kategori wisata"
                            required
                        />

                        <x-form.select
                            name="id_kecamatan"
                            label="Kecamatan / Wilayah"
                            :options="$kecamatan"
                            placeholder="Pilih kecamatan"
                            required
                        />

                        <x-form.input
                            name="link_maps"
                            label="Tautan Lokasi Google Maps"
                            type="url"
                            placeholder="https://maps.google.com/..."
                            maxlength="500"
                        />
                    </div>

                    <x-form.textarea
                        name="deskripsi"
                        label="Deskripsi Destinasi"
                        placeholder="Jelaskan informasi singkat mengenai destinasi wisata..."
                        rows="4"
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
                            Tambahkan foto publikasi resmi destinasi wisata.
                        </p>
                    </div>

                    <div class="rounded-xl border-2 border-dashed border-line bg-soft/50 p-8 text-center">
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-white">
                            <x-icon name="image" class="text-primary" />
                        </div>

                        <p class="mt-3 font-medium text-ink">
                            Pilih foto destinasi
                        </p>
                        <p class="mt-1 text-sm text-muted">
                            Format JPG, JPEG, PNG, atau WebP. Maksimal 2 MB.
                        </p>

                        <input
                            id="foto"
                            type="file"
                            name="foto"
                            accept="image/jpeg,image/png,image/webp"
                            @if ($errors->has('foto')) aria-invalid="true" @endif
                            class="mt-4 block w-full text-sm text-muted
                                   file:mr-3 file:rounded-lg file:border-0
                                   file:bg-primary file:px-4 file:py-2
                                   file:font-medium file:text-white
                                   hover:file:bg-primary-hover"
                        />

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
                    Simpan Destinasi
                </x-button>
            </div>
        </form>

    </div>
</x-layouts.admin>
